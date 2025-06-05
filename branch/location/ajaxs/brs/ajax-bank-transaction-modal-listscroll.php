<?php
require_once("../../../../app/v1/connection-branch-admin.php");
// require_once("../../../../app/v1/functions/branch/bankReconciliationStatement.controller.php");

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    // Customer Non ACC List
    if ($_GET['act'] == 'bankTransCustomerNonAcc') {
        $returnResponse = [];
        $limit_per_Page = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page_no = max(1, $page_no);
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $amount = $_GET['amount'] ? $_GET['amount'] : 0;

        $toggel = $_GET['toggel'];
        $query = "";
        if ($toggel == 1) {
            $query = "SELECT s.so_invoice_id,s.invoice_no,customer.customer_id, customer.trade_name, customer.customer_code,s.total_tax_amt, s.due_amount, s.invoice_date,status_master.label FROM `erp_branch_sales_order_invoices` AS s LEFT JOIN `erp_status_master` AS status_master ON status_master.`code` = s.`invoiceStatus` LEFT JOIN `erp_customer` AS customer ON customer.`customer_id` = s.`customer_id` WHERE s.`company_id` = $company_id AND s.`branch_id` = $branch_id AND s.`location_id` = $location_id AND s.`invoiceStatus` != 4 AND s.`due_amount` > 0 AND s.`due_amount` <= $amount  ORDER BY s.`due_amount` DESC";
        } else {
            $query = "SELECT s.so_invoice_id,s.invoice_no,customer.customer_id, customer.trade_name, customer.customer_code,s.total_tax_amt, s.due_amount, s.invoice_date,status_master.label FROM `erp_branch_sales_order_invoices` AS s LEFT JOIN `erp_status_master` AS status_master ON status_master.`code` = s.`invoiceStatus` LEFT JOIN `erp_customer` AS customer ON customer.`customer_id` = s.`customer_id` WHERE s.`company_id` = $company_id AND s.`branch_id` = $branch_id AND s.`location_id` = $location_id AND s.`invoiceStatus` != 4 AND s.`due_amount` > 0 AND s.`due_amount` >= $amount ORDER BY s.`due_amount` DESC";
        }

        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery;";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];

        if($totalRows <= 0){
            echo json_encode([
                "status" => "error",
                "message" => "no data found",
                "sql" => $query
            ]);
            exit();            
        }


        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);
        $numRows = $sqlMainQryObj['numRows'];

        foreach ($sqlMainQryObj['data'] as $data) {
            $dynamic_data[] = [
                "sl_no" => $sl,
                "customer_code" => $data['customer_code'] ?? "-",
                "trade_name" => $data['trade_name'] ?? "-",
                "invoice_no" => $data['invoice_no'],
                "invoice_date" => $data['invoice_date'],
                "status"=>$data['label'],
                "invoice_amount"=>$data['total_tax_amt'],
                "due_amount" => $data['due_amount'],
                "so_invoice_id" => $data['so_invoice_id'],
                "customer_id" => $data['customer_id'],
            ];
            $sl++;
        }

        if ($numRows > 0) {

            $returnResponse = [
                "status" => "success",
                "message" => "Customer Data fetched successfully",
                "numRows" => $numRows,
                "data" => $dynamic_data,
                "sql" => $query
            ];
        } else {
            $returnResponse = [
                "status" => "warring",
                "message" => "no data found",
                "sql" => $sqlMainQryObj['sql']
            ];
        }
        echo json_encode($returnResponse);
    }

    // Vendor Non ACC List
    if ($_GET['act'] == 'bankTransVendorNonAcc') {
        $returnResponse = [];
        $limit_per_Page = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page_no = max(1, $page_no);
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $amount = $_GET['amount'] ? $_GET['amount'] : 0;

        $toggel = $_GET['toggel'];
        $query = "";
        if ($toggel == 1) {
            $query = "SELECT grniv.grnIvId,grniv.vendorDocumentNo,grniv.grnTotalAmount,grniv.grnIvCode, grniv.vendorId, grniv.vendorCode, grniv.vendorName, grniv.dueAmt, grniv.postingDate,status_master.label FROM `erp_grninvoice` AS grniv LEFT JOIN `erp_status_master` AS status_master ON status_master.`code` = grniv.`paymentStatus` LEFT JOIN `erp_vendor_details` AS vendor ON vendor.`vendor_id` = grniv.`vendorId` WHERE grniv.`companyId` = $company_id AND grniv.`branchId` = $branch_id AND grniv.`locationId` = $location_id AND grniv.`paymentStatus` != 4 AND grniv.`dueAmt` > 0 AND grniv.grnStatus= 'active' AND grniv.`dueAmt` <= $amount ORDER BY grniv.`dueAmt` DESC";
        } else {
            $query = "SELECT grniv.grnIvId,grniv.vendorDocumentNo,grniv.grnTotalAmount,grniv.grnIvCode, grniv.vendorId, grniv.vendorCode, grniv.vendorName, grniv.dueAmt, grniv.postingDate,status_master.label FROM `erp_grninvoice` AS grniv LEFT JOIN `erp_status_master` AS status_master ON status_master.`code` = grniv.`paymentStatus` LEFT JOIN `erp_vendor_details` AS vendor ON vendor.`vendor_id` = grniv.`vendorId` WHERE grniv.`companyId` = $company_id AND grniv.`branchId` = $branch_id AND grniv.`locationId` = $location_id AND grniv.`paymentStatus` != 4 AND grniv.grnStatus= 'active' AND grniv.`dueAmt` > 0 AND grniv.`dueAmt` >= $amount ORDER BY grniv.`dueAmt` DESC";
        }

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);
        $numRows = $sqlMainQryObj['numRows'];

        foreach ($sqlMainQryObj['data'] as $data) {
            $dynamic_data[] = [
                "sl_no" => $sl,
                "grnIvId" => $data['grnIvId'],
                "vendorId" => $data['vendorId'],
                "invoice_no"=>$data['grnIvCode'],
                "vendorCode" => $data['vendorCode'],
                "vendorName" => $data['vendorName'],
                "inv_amount"=>$data['grnTotalAmount'],
                "vendorDocumentNo" => $data['vendorDocumentNo'],
                "dueAmt" => $data['dueAmt'],
                "status" =>$data['label'],
                "postingDate" => $data['postingDate'],
            ];
            $sl++;
        }

        if ($numRows > 0) {

            $returnResponse = [
                "status" => "success",
                "message" => "Vendor Data fetched successfully",
                "numRows" => $numRows,
                "data" => $dynamic_data,
                "sql" => $query
            ];
        } else {
            $returnResponse = [
                "status" => "warring",
                "message" => "no data found",
                "sql" => $sqlMainQryObj['sql']
            ];
        }
        echo json_encode($returnResponse);
    }

    // Customer  ACC List
    if ($_GET['act'] == 'bankTransCustomerAcc') {
        $returnResponse = [];
        $limit_per_Page = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page_no = max(1, $page_no);
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $amount = $_GET['amount'] ? $_GET['amount'] : 0;

        $bank_id=$_GET['bankId'];
        $toggel = $_GET['toggel'];
        $query = "";
        if ($toggel == 1) {
            
            $query = "SELECT 
                p.payment_id,
                p.transactionId,
                cus.customer_code,
                cus.trade_name,
                p.postingDate,
                p.documentDate,
                p.collect_payment,
                p.reconciled_amount,
                (p.collect_payment - p.reconciled_amount) AS unreconciled_amount
            FROM erp_branch_sales_order_payments AS p
            JOIN erp_customer AS cus ON p.customer_id = cus.customer_id
            WHERE 
                p.location_id = $location_id  AND 
                p.company_id =  $company_id AND 
                p.branch_id = $branch_id AND 
                (p.collect_through IS NULL OR TRIM(p.collect_through) = '') AND
                p.reconciled_amount!=p.collect_payment AND
                p.bank_id=$bank_id AND
                p.status='active'
                
            ";
            // $query = "SELECT j.postingDate, 'customer' AS party, 'collection' AS type, inv.so_invoice_id AS invoice_id, inv.invoice_date AS invoice_date, j.party_code AS customer_code, j.party_name AS customer_name, c.credit_amount AS amount, 'credit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_branch_sales_order_invoices inv ON inv.so_invoice_id = j.parent_id JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId = 90 JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Collection' AND inv.location_id=$location_id AND inv.due_amount > 0 AND inv.status != 'deleted' UNION ALL SELECT j.postingDate, 'customer' AS party, 'collection' AS type, inv.so_invoice_id AS invoice_id, inv.invoice_date AS invoice_date, j.party_code AS customer_code, j.party_name AS customer_name, d.debit_amount AS amount, 'debit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_branch_sales_order_invoices inv ON inv.so_invoice_id = j.parent_id JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId = 90 JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Collection' AND inv.location_id=$location_id AND inv.due_amount > 0 AND inv.status != 'deleted' AND inv.status !=4 ";
        } else {

            $query = "SELECT 
                p.payment_id,
                p.transactionId,
                cus.customer_code,
                cus.trade_name,
                p.postingDate,
                p.documentDate,
                p.collect_payment,
                p.reconciled_amount,
                (p.collect_payment - p.reconciled_amount) AS unreconciled_amount
            FROM erp_branch_sales_order_payments AS p
            JOIN erp_customer AS cus ON p.customer_id = cus.customer_id
            WHERE 
                p.location_id = $location_id  AND 
                p.company_id =  $company_id AND 
                p.branch_id = $branch_id AND 
                (p.collect_through IS NULL OR TRIM(p.collect_through) = '') AND
                p.reconciled_amount!=p.collect_payment AND
                p.bank_id=$bank_id AND
                p.status='active'
                
            ";
            // $query = "SELECT j.postingDate, 'customer' AS party, 'collection' AS type, inv.so_invoice_id AS invoice_id, inv.invoice_date AS invoice_date, j.party_code AS customer_code, j.party_name AS customer_name, c.credit_amount AS amount, 'credit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_branch_sales_order_invoices inv ON inv.so_invoice_id = j.parent_id JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId = 90 JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Collection' AND inv.location_id=$location_id AND inv.due_amount > 0 AND inv.status != 'deleted' UNION ALL SELECT j.postingDate, 'customer' AS party, 'collection' AS type, inv.so_invoice_id AS invoice_id, inv.invoice_date AS invoice_date, j.party_code AS customer_code, j.party_name AS customer_name, d.debit_amount AS amount, 'debit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_branch_sales_order_invoices inv ON inv.so_invoice_id = j.parent_id JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId = 90 JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Collection' AND inv.location_id=$location_id AND inv.due_amount > 0  AND inv.status != 'deleted' AND inv.status !=4 ";
        }

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);

        $numRows = $sqlMainQryObj['numRows'];

        foreach ($sqlMainQryObj['data'] as $data) {
            $dynamic_data[] = [
                "sl_no" => $sl,
                "vendor_name"=>$data['trade_name'],
                "vendor_code" => $data['customer_code'],
                "transactionId"=>$data['transactionId'],
                "payment_id"=>$data['payment_id'],
                "collect_payment"=>$data['collect_payment'],
                "reconciled_amount"=>$data['reconciled_amount'],
                "unreconciled_amount"=>$data['unreconciled_amount'],
                "documentDate"=>$data['documentDate'],
                "postingDate"=>$data['postingDate']
            ];
            $sl++;
        }

        if ($numRows > 0) {

            $returnResponse = [
                "status" => "success",
                "message" => "Customer Data fetched successfully",
                "numRows" => $numRows,
                "data" => $dynamic_data,
                "sql" => $query
            ];
        } else {
            $returnResponse = [
                "status" => "warring",
                "message" => "no data found",
                "sql" =>$query
            ];
        }
        echo json_encode($returnResponse);
    }

    // Vendor Acc List
    if ($_GET['act'] == 'bankTransVendorAcc') {
        $returnResponse = [];
        $limit_per_Page = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page_no = max(1, $page_no);
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $amount = $_GET['amount'] ? $_GET['amount'] : 0;

        $toggel = $_GET['toggel'];
        $bank_id=$_GET['bankId'];
        $query = "";
        if ($toggel == 1) {
            $query = "SELECT 
                p.payment_id,
                p.transactionId,
                ven.vendor_code,
                ven.trade_name,
                p.postingDate,
                p.documentDate,
                p.collect_payment,
                p.reconciled_amount,
                (p.collect_payment - p.reconciled_amount) AS unreconciled_amount
            FROM erp_grn_payments AS p
            JOIN erp_vendor_details AS ven ON p.vendor_id = ven.vendor_id
            WHERE 
                p.location_id = $location_id  AND 
                p.company_id =  $company_id AND 
                p.branch_id = $branch_id AND 
                p.payment_through = '' AND 
                p.reconciled_amount!=p.collect_payment AND
                p.bank_id=$bank_id AND
                p.status='active'
                
            ";
            // $query = "SELECT j.postingDate, 'vendor' AS party, 'payment' AS TYPE, grn_iv.grnIvId AS grnId, grn_iv.postingDate AS posting_date, j.party_code AS vendor_code, j.party_name AS vendor_name, d.debit_amount AS amount, 'debit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_grninvoice grn_iv ON grn_iv.grnIvId = j.parent_id JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId = 90 JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Payment' AND grn_iv.locationId = $location_id AND grn_iv.dueAmt > 0 AND grn_iv.grnStatus != 'deleted' UNION ALL SELECT j.postingDate, 'vendor' AS party, 'payment' AS TYPE, grn_iv.grnIvId AS grnId, grn_iv.postingDate AS posting_date, j.party_code AS vendor_code, j.party_name AS vendor_name, c.credit_amount AS amount, 'credit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_grninvoice grn_iv ON grn_iv.grnIvId = j.parent_id JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId = 90 JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Payment' AND grn_iv.locationId = $location_id AND grn_iv.dueAmt > 0 AND grn_iv.grnStatus != 'deleted' AND grn_iv.grnStatus != 4";;
        } else {
            $query = "SELECT 
                p.payment_id,
                p.transactionId,
                ven.vendor_code,
                ven.trade_name,
                p.postingDate,
                p.documentDate,
                p.collect_payment,
                p.reconciled_amount,
                (p.collect_payment - p.reconciled_amount) AS unreconciled_amount
            FROM erp_grn_payments AS p
            JOIN erp_vendor_details AS ven ON p.vendor_id = ven.vendor_id
            WHERE 
                p.location_id = $location_id  AND 
                p.company_id =  $company_id AND 
                p.branch_id = $branch_id AND 
                p.payment_through = '' AND 
                p.reconciled_amount!=p.collect_payment AND
                p.bank_id=$bank_id AND
                p.status='active'
                
            ";
            // $query = "SELECT j.postingDate, 'vendor' AS party, 'payment' AS TYPE, grn_iv.grnIvId AS grnId, grn_iv.postingDate AS posting_date, j.party_code AS vendor_code, j.party_name AS vendor_name, d.debit_amount AS amount, 'debit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_grninvoice grn_iv ON grn_iv.grnIvId = j.parent_id JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId = 90 JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Payment' AND grn_iv.locationId = $location_id AND grn_iv.dueAmt > 0 AND grn_iv.grnStatus != 'deleted' UNION ALL SELECT j.postingDate, 'vendor' AS party, 'payment' AS TYPE, grn_iv.grnIvId AS grnId, grn_iv.postingDate AS posting_date, j.party_code AS vendor_code, j.party_name AS vendor_name, c.credit_amount AS amount, 'credit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_grninvoice grn_iv ON grn_iv.grnIvId = j.parent_id JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId = 90 JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Payment' AND grn_iv.locationId = $location_id AND grn_iv.dueAmt > 0 AND grn_iv.grnStatus != 'deleted' AND grn_iv.grnStatus != 4";
        }

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);

        $numRows = $sqlMainQryObj['numRows'];

        foreach ($sqlMainQryObj['data'] as $data) {
            $dynamic_data[] = [
                "sl_no" => $sl,
                "vendor_name"=>$data['trade_name'],
                "vendor_code" => $data['vendor_code'],
                "transactionId"=>$data['transactionId'],
                "payment_id"=>$data['payment_id'],
                "collect_payment"=>$data['collect_payment'],
                "reconciled_amount"=>$data['reconciled_amount'],
                "unreconciled_amount"=>$data['unreconciled_amount'],
                "documentDate"=>$data['documentDate'],
                "postingDate"=>$data['postingDate']
            ];
            $sl++;
        }

        if ($numRows > 0) {

            $returnResponse = [
                "status" => "success",
                "message" => "Vendor Data fetched successfully",
                "numRows" => $numRows,
                "data" => $dynamic_data,
                "sql" => $query
            ];
        } else {
            $returnResponse = [
                "status" => "warring",
                "message" => "no data found",
                "sql" => $query
            ];
        }
        echo json_encode($returnResponse);
    }
} else {
    echo json_encode(["status" => "Error", "message" => "Wrong Request Method"]);
}
