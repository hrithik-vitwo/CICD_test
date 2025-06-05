<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");
require_once("../../../../app/v1/functions/branch/bankReconciliationStatement.controller.php");


if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if ($_GET['act'] == 'bankTrans') {
        $returnResponse = [];
        $limit_per_Page = isset($_GET['maxlimit']) && $_GET['maxlimit'] != '' ? $_GET['maxlimit'] : 25;
        $page_no = isset($_GET['page_id']) ? (int)$_GET['page_id'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        $output = "";
        $limitText = "";

        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $amount = $_GET['amount'] ? $_GET['amount'] : 0;

        $query = "SELECT s.so_invoice_id,customer.customer_id, customer.trade_name, customer.customer_code, s.due_amount, s.invoice_date FROM `erp_branch_sales_order_invoices` AS s LEFT JOIN `erp_status_master` AS status_master ON status_master.`code` = s.`invoiceStatus` LEFT JOIN `erp_customer` AS customer ON customer.`customer_id` = s.`customer_id` WHERE s.`company_id` = $company_id AND s.`branch_id` = $branch_id AND s.`location_id` = $location_id AND s.`invoiceStatus` != 4 AND s.`due_amount` > 0 AND s.`due_amount` <= $amount ORDER BY s.`due_amount` DESC";

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);
        $output .= "</table>";
        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery;";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];
        $total_page = ceil($totalRows / $limit_per_Page);
        $numRows = $sqlMainQryObj['numRows'];
        $output .= pagiNationinnerTable($page_no, $total_page);

        $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';
        foreach ($sqlMainQryObj['data'] as $data) {
            $dynamic_data[] = [
                "sl_no" => $sl,
                "so_invoice_id" => $data['so_invoice_id'],
                "customer_id" => $data['customer_id'],
                "trade_name" => $data['trade_name'],
                "customer_code" => $data['customer_code'],
                "due_amount" => $data['due_amount'],
                "invoice_date" => $data['invoice_date'],
            ];
            $sl++;
        }

        if ($numRows > 0) {

            $returnResponse = [
                "status" => "success",
                "message" => "data found",
                "numRows" => $query['numRows'],
                "data" => $dynamic_data,
                "limit_per_Page" => $limit_per_Page,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sql" => $query,
                "sql_format" => $sql_Mainqry
            ];
        } else {
            $returnResponse = [
                "status" => "warring",
                "message" => "no data found",
                "sql" => $sqlMainQryObj
            ];
        }
        echo json_encode($returnResponse);
    }

    if ($_GET['act'] == 'bankTransVendor') {
        $returnResponse = [];
        $limit_per_Page = isset($_GET['maxlimit']) && $_GET['maxlimit'] != '' ? $_GET['maxlimit'] : 25;
        $page_no = isset($_GET['page_id']) ? (int)$_GET['page_id'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        $output = "";
        $limitText = "";

        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        $amount = $_GET['amount'] ? $_GET['amount'] : 0;

        $query = "SELECT grniv.grnIvId, grniv.vendorId, grniv.vendorCode, grniv.vendorName, grniv.dueAmt, grniv.postingDate FROM `erp_grninvoice` AS grniv LEFT JOIN `erp_status_master` AS status_master ON status_master.`code` = grniv.`paymentStatus` LEFT JOIN `erp_vendor_details` AS vendor ON vendor.`vendor_id` = grniv.`vendorId` WHERE grniv.`companyId` = $company_id AND grniv.`branchId` = $branch_id AND grniv.`locationId` = $location_id AND grniv.`paymentStatus` != 4 AND grniv.`dueAmt` > 0 AND grniv.`dueAmt` <= $amount ORDER BY grniv.`dueAmt` DESC";

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);
        $output .= "</table>";
        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery;";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];
        $total_page = ceil($totalRows / $limit_per_Page);
        $numRows = $sqlMainQryObj['numRows'];
        $output .= pagiNationinnerTable2($page_no, $total_page);

        $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';
        foreach ($sqlMainQryObj['data'] as $data) {
            $dynamic_data[] = [
                "sl_no" => $sl,
                "grnIvId" => $data['grnIvId'],
                "vendorId" => $data['vendorId'],
                "vendorCode" => $data['vendorCode'],
                "vendorName" => $data['vendorName'],
                "dueAmt" => $data['dueAmt'],
                "postingDate" => $data['postingDate'],
            ];
            $sl++;
        }

        if ($numRows > 0) {

            $returnResponse = [
                "status" => "success",
                "message" => "data found",
                "numRows" => $query['numRows'],
                "data" => $dynamic_data,
                "limit_per_Page" => $limit_per_Page,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sql" => $query,
                "sql_format" => $sql_Mainqry
            ];
        } else {
            $returnResponse = [
                "status" => "warring",
                "message" => "no data found",
                "sql" => $sqlMainQryObj,
                "data" => [],
            ];
        }
        echo json_encode($returnResponse);
    }

    if ($_GET['act'] == 'bankTransNonAccCustomer') {
        $returnResponse = [];
        $limit_per_Page = isset($_GET['maxlimit']) && $_GET['maxlimit'] != '' ? $_GET['maxlimit'] : 25;
        $page_no = isset($_GET['page_id']) ? (int)$_GET['page_id'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        $output = "";
        $limitText = "";

        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        $query = "SELECT j.postingDate, 'customer' AS party, 'collection' AS type, inv.so_invoice_id AS invoice_id, inv.invoice_date AS invoice_date, j.party_code AS customer_code, j.party_name AS customer_name, c.credit_amount AS amount, 'credit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_branch_sales_order_invoices inv ON inv.so_invoice_id = j.parent_id JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId = 90 JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Collection' AND inv.location_id=1 AND inv.due_amount > 0 AND inv.status != 'deleted' UNION ALL SELECT j.postingDate, 'customer' AS party, 'collection' AS type, inv.so_invoice_id AS invoice_id, inv.invoice_date AS invoice_date, j.party_code AS customer_code, j.party_name AS customer_name, d.debit_amount AS amount, 'debit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_branch_sales_order_invoices inv ON inv.so_invoice_id = j.parent_id JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId = 90 JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Collection' AND inv.location_id=$location_id AND inv.due_amount > 0 AND inv.status != 'deleted' AND inv.status !=4 ";

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);
        $output .= "</table>";
        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery;";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];
        $total_page = ceil($totalRows / $limit_per_Page);
        $numRows = $sqlMainQryObj['numRows'];
        $output .= pagiNationinnerTable3($page_no, $total_page);

        $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';
        foreach ($sqlMainQryObj['data'] as $data) {
            $dynamic_data[] = [
                "sl_no" => $sl,
                "postingDate" => $data['postingDate'],
                "party" => $data['party'],
                "invoice_id" => $data['invoice_id'],
                "type" => $data['type'],
                "amount" => $data['amount'],
                "postingDate" => $data['postingDate'],
                "invoice_date" => $data['invoice_date'],
                "customer_code" => $data['customer_code'],
                "customer_name" => $data['customer_name'],
                "transaction_type" => $data['transaction_type'],
            ];
            $sl++;
        }

        if ($numRows > 0) {

            $returnResponse = [
                "status" => "success",
                "message" => "data found",
                "numRows" => $query['numRows'],
                "data" => $dynamic_data,
                "limit_per_Page" => $limit_per_Page,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sql" => $query,
                "sql_format" => $sql_Mainqry
            ];
        } else {
            $returnResponse = [
                "status" => "warring",
                "message" => "no data found",
                "sql" => $sqlMainQryObj
            ];
        }
        echo json_encode($returnResponse);
    }
    
    if ($_GET['act'] == 'bankTransNonAccVendor') {
        $returnResponse = [];
        $limit_per_Page = isset($_GET['maxlimit']) && $_GET['maxlimit'] != '' ? $_GET['maxlimit'] : 25;
        $page_no = isset($_GET['page_id']) ? (int)$_GET['page_id'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        $output = "";
        $limitText = "";

        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        $query = "SELECT j.postingDate, 'vendor' AS party, 'payment' AS TYPE, grn_iv.grnIvId AS grnId, grn_iv.postingDate AS posting_date, j.party_code AS vendor_code, j.party_name AS vendor_name, d.debit_amount AS amount, 'debit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_grninvoice grn_iv ON grn_iv.grnIvId = j.parent_id JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId = 90 JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Payment' AND grn_iv.locationId = $location_id AND grn_iv.dueAmt > 0 AND grn_iv.grnStatus != 'deleted' UNION ALL SELECT j.postingDate, 'vendor' AS party, 'payment' AS TYPE, grn_iv.grnIvId AS grnId, grn_iv.postingDate AS posting_date, j.party_code AS vendor_code, j.party_name AS vendor_name, c.credit_amount AS amount, 'credit' AS transaction_type FROM erp_acc_journal AS j JOIN erp_grninvoice grn_iv ON grn_iv.grnIvId = j.parent_id JOIN erp_acc_debit d ON j.id = d.journal_id AND d.glId = 90 JOIN erp_acc_credit c ON j.id = c.journal_id AND c.glId != 90 WHERE j.company_id = $company_id AND j.branch_id = $branch_id AND j.location_id = $location_id AND j.parent_slug = 'Payment' AND grn_iv.locationId = $location_id AND grn_iv.dueAmt > 0 AND grn_iv.grnStatus != 'deleted' AND grn_iv.grnStatus != 4";

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);
        $output .= "</table>";
        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery;";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];
        $total_page = ceil($totalRows / $limit_per_Page);
        $numRows = $sqlMainQryObj['numRows'];
        $output .= pagiNationinnerTable4($page_no, $total_page);

        $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';
        foreach ($sqlMainQryObj['data'] as $data) {
            $dynamic_data[] = [
                "sl_no" => $sl,
                "party" => $data['party'],
                "grnId" => $data['grnId'],
                "type" => $data['TYPE'],
                "amount" => $data['amount'],
                "postingDate" => $data['postingDate'],
                "posting_date" => $data['posting_date'],
                "vendor_code" => $data['vendor_code'],
                "vendor_name" => $data['vendor_name'],
                "transaction_type" => $data['transaction_type'],
            ];
            $sl++;
        }

        if ($numRows > 0) {

            $returnResponse = [
                "status" => "success",
                "message" => "data found",
                "numRows" => $query['numRows'],
                "data" => $dynamic_data,
                "limit_per_Page" => $limit_per_Page,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sql" => $query,
                "sql_format" => $sql_Mainqry
            ];
        } else {
            $returnResponse = [
                "status" => "warring",
                "message" => "no data found",
                "sql" => $sqlMainQryObj
            ];
        }
        echo json_encode($returnResponse);
    }

    if($_GET['act']=='vendorManualSelect'){
        $bankId=$_GET['bankId'];
        $tnxType=$_GET['tnxType'];
        $brsObj = new BankReconciliationStatement($bankId, $tnxType);
        $vendorListObj = $brsObj->getVendorList();
        echo json_encode($vendorListObj);
    }

    if($_GET['act']=='customerManualSelect'){
        $bankId=$_GET['bankId'];
        $tnxType=$_GET['tnxType'];
        $brsObj = new BankReconciliationStatement($bankId, $tnxType);
        $customerListObj = $brsObj->getCustomerList();
        echo json_encode($customerListObj);
    }
            
} else {
    echo json_encode(["status" => "Error","message" => "Something went wrong try again!"]);
}
