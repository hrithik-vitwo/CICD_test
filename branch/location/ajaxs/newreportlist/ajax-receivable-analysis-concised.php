<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../pagination/common-pagination.php");
require_once("../../../common/exportexcel-new.php");
// require_once("../../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');

$BranchSoObj = new BranchSo();
session_start();

if ($_POST['act'] == 'cdata') {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION['columnMapping'] = $_POST['columnMapping'];
    if (isset($_SESSION['columnMapping'])) {
      $columnMapping = $_SESSION['columnMapping'];
    }

    $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

    $page_no = isset($_POST['pageNo']) ? (int)$_POST['pageNo'] : 1;
    $page_no = max(1, $page_no);

    $offset = ($page_no - 1) * $limit_per_Page;
    $maxPagesl = $page_no * $limit_per_Page;
    $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
    $formObj = $_POST['formDatas'];
    $cond = "";
    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";

      if ($slag === 'posting_date' || $slag === 'due_date' || $slag === 'created_at' || $slag === 'updated_at') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "total_due" || $slag === "0-30_days_due" || $slag === "31-60_days_due" || $slag === "61-90_days_due" || $slag === "91-180_days_due" || $slag === "more_than_180_days_due") {
        $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
      } elseif ($slag === "created_by" || $slag === "updated_by") {

        $resultList = getAdminUserIdByName($data['value']);
        $conds .= $slag . " IN  " . " (" . $resultList . ")";
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }
    $asOnDate = $_POST['asOnDate'];


    // $sql_list = "SELECT subGlCode AS customer_code, subGlName AS customer_name, SUM(amount) as total_due, SUM(`0-30`) as '0-30_days_due', SUM(`31-60`) as '31-60_days_due', SUM(`61-90`) as '61-90_days_due', SUM(`91-180`) as '91-180_days_due', SUM(`180+`) as 'more_than_180_days_due' FROM (( SELECT d.journal_id, j.postingDate, d.glId, d.subGlCode, d.subGlName, j.refarenceCode, COALESCE(d.debit_amount,0) AS 'amount', 'dr' AS 'type', IF(datediff('$asOnDate', j.postingDate) BETWEEN 0 AND 30, COALESCE(d.debit_amount,0), 0) as '0-30', IF(datediff('$asOnDate', j.postingDate) BETWEEN 31 AND 60, COALESCE(d.debit_amount,0), 0) as '31-60', IF(datediff('$asOnDate', j.postingDate) BETWEEN 61 AND 90, COALESCE(d.debit_amount,0), 0) as '61-90', IF(datediff('$asOnDate', j.postingDate) BETWEEN 91 AND 180, COALESCE(d.debit_amount,0), 0) as '91-180', IF(datediff('$asOnDate', j.postingDate)>180, COALESCE(d.debit_amount,0), 0) as '180+' FROM erp_acc_debit AS d LEFT JOIN erp_acc_journal AS j ON d.journal_id = j.id WHERE d.glId = 88 AND j.location_id = $location_id ) UNION ALL ( SELECT c.journal_id, j.postingDate, c.glId, c.subGlCode, c.subGlName, j.refarenceCode, COALESCE(c.credit_amount, 0)*(-1) AS 'amount', 'cr' AS 'type', IF(datediff('$asOnDate', j.postingDate) BETWEEN 0 AND 30, COALESCE(c.credit_amount,0), 0)*(-1) as '0-30', IF(datediff('$asOnDate', j.postingDate) BETWEEN 31 AND 60, COALESCE(c.credit_amount,0), 0)*(-1) as '31-60', IF(datediff('$asOnDate', j.postingDate) BETWEEN 61 AND 90, COALESCE(c.credit_amount,0), 0)*(-1) as '61-90', IF(datediff('$asOnDate', j.postingDate) BETWEEN 91 AND 180, COALESCE(c.credit_amount,0), 0)*(-1) as '91-180', IF(datediff('$asOnDate', j.postingDate)>180, COALESCE(c.credit_amount,0), 0)*(-1) as '180+' FROM erp_acc_credit AS c LEFT JOIN erp_acc_journal AS j ON c.journal_id = j.id WHERE c.glId = 88 AND j.location_id = $location_id )) AS dr_cr WHERE 1 " . $cond . " GROUP BY subGlCode";


    $sql_list = "SELECT customer_code, customer_name, SUM(CASE WHEN transaction_type = 'INVOICE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' THEN total_amount ELSE 0 END) AS net_due, SUM(CASE WHEN transaction_type = 'INVOICE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' THEN total_amount ELSE 0 END) AS total_due, SUM(CASE WHEN transaction_type = 'INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) AS `0-30_days_due`, SUM(CASE WHEN transaction_type = 'INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) AS `31-60_days_due`, SUM(CASE WHEN transaction_type = 'INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) AS `61-90_days_due`, SUM(CASE WHEN transaction_type = 'INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) AS `91-180_days_due`, SUM(CASE WHEN transaction_type = 'INVOICE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) AS `more_than_180_days_due`, SUM(CASE WHEN transaction_type = 'COLLECTION' THEN (-1)*total_amount ELSE 0 END) AS total_onaccount, SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN (-1)*total_amount ELSE 0 END) AS '0-30_days_onaccount', SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN (-1)*total_amount ELSE 0 END) AS '31-60_days_onaccount', SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN (-1)*total_amount ELSE 0 END) AS '61-90_days_onaccount', SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN (-1)*total_amount ELSE 0 END) AS '91-180_days_onaccount', SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN (-1)*total_amount ELSE 0 END) AS 'more_than_180_days_onaccount' FROM ( SELECT cust.customer_code, cust.trade_name AS customer_name, inv.invoice_no AS document_no, inv.invoice_date AS posting_date, CASE WHEN inv.pgi_id != 0 AND inv.so_id = '' THEN pgi.pgi_no WHEN inv.pgi_id = 0 AND inv.so_id != '' THEN so.so_number ELSE '-' END AS reference_no, 'INVOICE' AS transaction_type, COALESCE(inv.due_amount, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_branch_sales_order_invoices AS inv ON cust.customer_id = inv.customer_id LEFT JOIN erp_branch_sales_order AS so ON inv.so_id = so.so_id LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi ON inv.pgi_id = pgi.so_delivery_pgi_id WHERE inv.company_id=$company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id AND inv.status = 'active' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, inv.invoice_no AS document_no, inv.invoice_date AS posting_date, CASE WHEN inv.pgi_id != 0 AND inv.so_id = '' THEN pgi.pgi_no WHEN inv.pgi_id = 0 AND inv.so_id != '' THEN so.so_number ELSE '-' END AS reference_no, 'REV INVOICE' AS transaction_type, COALESCE(inv.due_amount, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_branch_sales_order_invoices AS inv ON cust.customer_id = inv.customer_id LEFT JOIN erp_branch_sales_order AS so ON inv.so_id = so.so_id LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi ON inv.pgi_id = pgi.so_delivery_pgi_id WHERE inv.company_id=$company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id AND inv.status = 'reverse' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, cn.credit_note_no AS document_no, cn.postingDate AS posting_date, inv.invoice_no AS reference_no, 'CREDIT NOTE' AS transaction_type, COALESCE(cn.total, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_credit_note AS cn ON cust.customer_id = cn.party_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON cn.creditNoteReference = inv.so_invoice_id WHERE inv.invoice_no IS NULL AND cn.company_id=$company_id AND cn.branch_id=$branch_id AND cn.location_id=$location_id AND cn.status = 'active' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, cn.credit_note_no AS document_no, cn.postingDate AS posting_date, inv.invoice_no AS reference_no, 'REV CREDIT NOTE' AS transaction_type, COALESCE(cn.total, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_credit_note AS cn ON cust.customer_id = cn.party_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON cn.creditNoteReference = inv.so_invoice_id WHERE inv.invoice_no IS NULL AND cn.company_id=$company_id AND cn.branch_id=$branch_id AND cn.location_id=$location_id AND cn.status = 'reverse' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, dn.debit_note_no AS document_no, dn.postingDate AS posting_date, inv.invoice_no AS reference_no, 'DEBIT NOTE' AS transaction_type, COALESCE(dn.total, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_debit_note AS dn ON cust.customer_id = dn.party_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON dn.debitNoteReference = inv.so_invoice_id WHERE inv.invoice_no IS NULL AND dn.company_id=$company_id AND dn.branch_id=$branch_id AND dn.location_id=$location_id AND dn.status = 'active' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, dn.debit_note_no AS document_no, dn.postingDate AS posting_date, inv.invoice_no AS reference_no, 'REV DEBIT NOTE' AS transaction_type, COALESCE(dn.total, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_debit_note AS dn ON cust.customer_id = dn.party_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON dn.debitNoteReference = inv.so_invoice_id WHERE inv.invoice_no IS NULL AND dn.company_id=$company_id AND dn.branch_id=$branch_id AND dn.location_id=$location_id AND dn.status = 'reverse' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, col.collectionCode AS document_no, col.postingDate AS posting_date, inv.invoice_no AS reference_no, 'COLLECTION' AS transaction_type, COALESCE(log.payment_amt, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_branch_sales_order_payments AS col ON cust.customer_id = col.customer_id LEFT JOIN erp_branch_sales_order_payments_log AS log ON col.payment_id = log.payment_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON log.invoice_id = inv.so_invoice_id WHERE log.invoice_id = 0 AND inv.invoice_no IS NULL AND col.company_id=$company_id AND col.branch_id=$branch_id AND col.location_id=$location_id) AS subquery GROUP BY customer_code";


    $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";

    $sqlMainQryObj = queryGet($sql_Mainqry, true);

    $dynamic_data = [];
    $num_list = $sqlMainQryObj['numRows'];
    $sql_data = $sqlMainQryObj['data'];
    $output = "";
    $limitText = "";
    $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

    if ($num_list > 0) {

      $companyOpeningDate = new DateTime($compOpeningDate);
      $reportAsOnDate = new DateTime($asOnDate);
      $compOpeningMonthYear = substr($compOpeningDate, 0, 7); //2024-01
      $diffDays = $companyOpeningDate->diff($reportAsOnDate)->days;

      foreach ($sql_data as $data) {

        $customerCode = $data['customer_code'];

        $opening = queryGet("SELECT opening_val FROM erp_opening_closing_balance WHERE location_id=$location_id AND gl=88 AND subgl = '$customerCode' AND `date` LIKE '$compOpeningMonthYear%'");

        $opening_balance = $opening['data']['opening_val'] ?? 0;

        $data['total_due'] += $opening_balance;
        $data['total_onaccount'] += $opening_balance;
        if ($diffDays <= 30) {
          $data['0-30_days_due'] += $opening_balance;
        } elseif ($diffDays <= 60) {
          $data['31-60_days_due'] += $opening_balance;
        } elseif ($diffDays <= 90) {
          $data['61-90_days_due'] += $opening_balance;
        } elseif ($diffDays <= 120) {
          $data['91-180_days_due'] += $opening_balance;
        } else {
          $data['more_than_180_days_due'] += $opening_balance;
          $data['more_than_180_days_onaccount'] += $opening_balance;
        }

        // $goodsType = "";
        // if ($data['goodsType'] === "material") {
        //   $goodsType .= '<p class="goods-type type-goods">GOODS</p>';
        // } elseif ($data['goodsType'] === "service") {
        //   $goodsType .= '<p class="goods-type type-service">SERVICE</p>';
        // } elseif ($data['goodsType'] === "both") {
        //   $goodsType .= '<p class="goods-type type-goods">BOTH</p>';
        // } elseif ($data['goodsType'] === "project") {
        //   $goodsType .= '<p class="goods-type type-project">PROJECT</p>';
        // }

        // if ($data['label'] == "open") {
        //   $approvalStatus = '<div class="status-bg status-open">Open</div>';
        // } elseif ($data['label'] == "pending") {
        //   $approvalStatus = '<div class="status-bg status-pending">Pending</div>';
        // } elseif ($data['label'] == "exceptional") {
        //   $approvalStatus = '<div class="status-bg status-exceptional">Exceptional</div>';
        // } elseif ($data['label'] == "closed") {
        //   $approvalStatus = '<div class="status-bg status-closed">Closed</div>';
        // }


        $dynamic_data[] = [
          "sl_no" => $sl,
          "customer_code" => $data['customer_code'],
          "customer_name" => $data['customer_name'],
          "net_due" => decimalValuePreview($data['net_due']),
          "total_due" => decimalValuePreview($data['total_due']),
          "zero_thirty_days_due" => decimalValuePreview($data['0-30_days_due']),
          "thirtyone_sixty_days_due" => decimalValuePreview($data['31-60_days_due']),
          "sixtyone_ninety_days_due" => decimalValuePreview($data['61-90_days_due']),
          "ninetyone_oneeighty_days_due" => decimalValuePreview($data['91-180_days_due']),
          "more_than_oneeighty_days_due" => decimalValuePreview($data['more_than_180_days_due']),
          "total_onaccount" => decimalValuePreview($data['total_onaccount']),
          "zero_thirty_days_onaccount" => decimalValuePreview($data['0-30_days_onaccount']),
          "thirtyone_sixty_days_onaccount" => decimalValuePreview($data['31-60_days_onaccount']),
          "sixtyone_ninety_days_onaccount" => decimalValuePreview($data['61-90_days_onaccount']),
          "ninetyone_oneeighty_days_onaccount" => decimalValuePreview($data['91-180_days_onaccount']),
          "more_than_oneeighty_days_onaccount" => decimalValuePreview($data['more_than_180_days_onaccount']),
          //   "created_at" => $data['created_at'],
          //   "created_by" => getCreatedByUser($data['created_by']),
          //   "updated_at" => $data['updated_at'],
          //   "updated_by" => getCreatedByUser($data['updated_by']),
        ];
        $sl++;
      }
      $output .= "</table>";
      $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
      $queryset = queryGet($sqlRowCount);
      $totalRows = $queryset['data']['row_count'];
      $total_page = ceil($totalRows / $limit_per_Page);

      $output .= pagiNation($page_no, $total_page);

      $limitText .= '<a class="active" id="limitText">Showing' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';

      // $csvContent = exportToExcelAll($sql_list, json_encode($columnMapping));
      // $csvContentBypagination = exportToExcelByPagin($sql_Mainqry, json_encode($columnMapping));

      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        "limit_per_Page" => $limit_per_Page,
      //   "csvContent" => $csvContent,
      //   "csvContentBypagination" => $csvContentBypagination,
      //   "sql" => $sql_list
      ];
    } else {
      $res = [
        "status" => false,
        "msg" => "Error!",
        "sql" => $sql_list
      ];
    }

    echo json_encode($res);
  }
}

if ($_POST['act'] == 'alldata') {
  $formObj = $_POST['formDatas'];
  $cond = "";
  $implodeFrom = implode('', array_map(function ($slag, $data) {
    $conds = "";

    if ($slag === 'posting_date' || $slag === 'due_date' || $slag === 'created_at' || $slag === 'updated_at') {
      if ($data['operatorName'] === 'BETWEEN') {
        $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
      } else {
        $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
      }
    } elseif (strcasecmp($data['value'], 'Goods') === 0) {
      $data['value'] = 'material';
      $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
    } elseif ($slag === "total_due" || $slag === "0-30_days_due" || $slag === "31-60_days_due" || $slag === "61-90_days_due" || $slag === "91-180_days_due" || $slag === "more_than_180_days_due") {
      $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
    } elseif ($slag === "created_by" || $slag === "updated_by") {

      $resultList = getAdminUserIdByName($data['value']);
      $conds .= $slag . " IN  " . " (" . $resultList . ")";
    } else {
      $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
    }

    return !empty($data['value']) ? " AND " . $conds : "";
  }, array_keys($formObj), $formObj));


  if (!empty($implodeFrom)) {
    $cond .= $implodeFrom;
  }
  $asOnDate = $_POST['asOnDate'];
  $sql_list = "SELECT customer_code, customer_name, SUM(CASE WHEN transaction_type = 'INVOICE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' THEN total_amount ELSE 0 END) AS net_due, SUM(CASE WHEN transaction_type = 'INVOICE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' THEN total_amount ELSE 0 END) AS total_due, SUM(CASE WHEN transaction_type = 'INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN total_amount ELSE 0 END) AS `0-30_days_due`, SUM(CASE WHEN transaction_type = 'INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN total_amount ELSE 0 END) AS `31-60_days_due`, SUM(CASE WHEN transaction_type = 'INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN total_amount ELSE 0 END) AS `61-90_days_due`, SUM(CASE WHEN transaction_type = 'INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN total_amount ELSE 0 END) AS `91-180_days_due`, SUM(CASE WHEN transaction_type = 'INVOICE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV INVOICE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'REV CREDIT NOTE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) + SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'REV DEBIT NOTE' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) - SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN total_amount ELSE 0 END) AS `more_than_180_days_due`, SUM(CASE WHEN transaction_type = 'COLLECTION' THEN (-1)*total_amount ELSE 0 END) AS total_onaccount, SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) THEN (-1)*total_amount ELSE 0 END) AS '0-30_days_onaccount', SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 30 DAY THEN (-1)*total_amount ELSE 0 END) AS '31-60_days_onaccount', SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 60 DAY THEN (-1)*total_amount ELSE 0 END) AS '61-90_days_onaccount', SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date > CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 90 DAY THEN (-1)*total_amount ELSE 0 END) AS '91-180_days_onaccount', SUM(CASE WHEN transaction_type = 'COLLECTION' AND posting_date <= CAST('" . $asOnDate . "' AS DATE) - INTERVAL 180 DAY THEN (-1)*total_amount ELSE 0 END) AS 'more_than_180_days_onaccount' FROM ( SELECT cust.customer_code, cust.trade_name AS customer_name, inv.invoice_no AS document_no, inv.invoice_date AS posting_date, CASE WHEN inv.pgi_id != 0 AND inv.so_id = '' THEN pgi.pgi_no WHEN inv.pgi_id = 0 AND inv.so_id != '' THEN so.so_number ELSE '-' END AS reference_no, 'INVOICE' AS transaction_type, COALESCE(inv.due_amount, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_branch_sales_order_invoices AS inv ON cust.customer_id = inv.customer_id LEFT JOIN erp_branch_sales_order AS so ON inv.so_id = so.so_id LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi ON inv.pgi_id = pgi.so_delivery_pgi_id WHERE inv.company_id=$company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id AND inv.status = 'active' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, inv.invoice_no AS document_no, inv.invoice_date AS posting_date, CASE WHEN inv.pgi_id != 0 AND inv.so_id = '' THEN pgi.pgi_no WHEN inv.pgi_id = 0 AND inv.so_id != '' THEN so.so_number ELSE '-' END AS reference_no, 'REV INVOICE' AS transaction_type, COALESCE(inv.due_amount, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_branch_sales_order_invoices AS inv ON cust.customer_id = inv.customer_id LEFT JOIN erp_branch_sales_order AS so ON inv.so_id = so.so_id LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi ON inv.pgi_id = pgi.so_delivery_pgi_id WHERE inv.company_id=$company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id AND inv.status = 'reverse' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, cn.credit_note_no AS document_no, cn.postingDate AS posting_date, inv.invoice_no AS reference_no, 'CREDIT NOTE' AS transaction_type, COALESCE(cn.total, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_credit_note AS cn ON cust.customer_id = cn.party_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON cn.creditNoteReference = inv.so_invoice_id WHERE inv.invoice_no IS NULL AND cn.company_id=$company_id AND cn.branch_id=$branch_id AND cn.location_id=$location_id AND cn.status = 'active' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, cn.credit_note_no AS document_no, cn.postingDate AS posting_date, inv.invoice_no AS reference_no, 'REV CREDIT NOTE' AS transaction_type, COALESCE(cn.total, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_credit_note AS cn ON cust.customer_id = cn.party_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON cn.creditNoteReference = inv.so_invoice_id WHERE inv.invoice_no IS NULL AND cn.company_id=$company_id AND cn.branch_id=$branch_id AND cn.location_id=$location_id AND cn.status = 'reverse' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, dn.debit_note_no AS document_no, dn.postingDate AS posting_date, inv.invoice_no AS reference_no, 'DEBIT NOTE' AS transaction_type, COALESCE(dn.total, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_debit_note AS dn ON cust.customer_id = dn.party_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON dn.debitNoteReference = inv.so_invoice_id WHERE inv.invoice_no IS NULL AND dn.company_id=$company_id AND dn.branch_id=$branch_id AND dn.location_id=$location_id AND dn.status = 'active' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, dn.debit_note_no AS document_no, dn.postingDate AS posting_date, inv.invoice_no AS reference_no, 'REV DEBIT NOTE' AS transaction_type, COALESCE(dn.total, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_debit_note AS dn ON cust.customer_id = dn.party_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON dn.debitNoteReference = inv.so_invoice_id WHERE inv.invoice_no IS NULL AND dn.company_id=$company_id AND dn.branch_id=$branch_id AND dn.location_id=$location_id AND dn.status = 'reverse' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, col.collectionCode AS document_no, col.postingDate AS posting_date, inv.invoice_no AS reference_no, 'COLLECTION' AS transaction_type, COALESCE(log.payment_amt, 0) AS total_amount FROM erp_customer AS cust LEFT JOIN erp_branch_sales_order_payments AS col ON cust.customer_id = col.customer_id LEFT JOIN erp_branch_sales_order_payments_log AS log ON col.payment_id = log.payment_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON log.invoice_id = inv.so_invoice_id WHERE log.invoice_id = 0 AND inv.invoice_no IS NULL AND col.company_id=$company_id AND col.branch_id=$branch_id AND col.location_id=$location_id) AS subquery GROUP BY customer_code";

  $dynamic_data_all = [];
  $sqlMainQryObjall = queryGet($sql_list, true);
  $sql_data_all = $sqlMainQryObjall['data'];
  $num_list =  $sqlMainQryObjall['numRows'];
  if ($num_list > 0) {
    foreach ($sql_data_all as $data) {
      $customerCode = $data['customer_code'];

      $opening = queryGet("SELECT opening_val FROM erp_opening_closing_balance WHERE location_id=$location_id AND gl=88 AND subgl = '$customerCode' AND `date` LIKE '$compOpeningMonthYear%'");

      $opening_balance = $opening['data']['opening_val'] ?? 0;

      $data['total_due'] += $opening_balance;
      $data['total_onaccount'] += $opening_balance;
      if ($diffDays <= 30) {
        $data['0-30_days_due'] += $opening_balance;
      } elseif ($diffDays <= 60) {
        $data['31-60_days_due'] += $opening_balance;
      } elseif ($diffDays <= 90) {
        $data['61-90_days_due'] += $opening_balance;
      } elseif ($diffDays <= 120) {
        $data['91-180_days_due'] += $opening_balance;
      } else {
        $data['more_than_180_days_due'] += $opening_balance;
        $data['more_than_180_days_onaccount'] += $opening_balance;
      }
      $dynamic_data_all[] = [
        "sl_no" => $sl,
        "customer_code" => $data['customer_code'],
        "customer_name" => $data['customer_name'],
        "net_due" => $data['net_due'],
        "total_due" => $data['total_due'],
        "zero_thirty_days_due" => $data['0-30_days_due'],
        "thirtyone_sixty_days_due" => $data['31-60_days_due'],
        "sixtyone_ninety_days_due" => $data['61-90_days_due'],
        "ninetyone_oneeighty_days_due" => $data['91-180_days_due'],
        "more_than_oneeighty_days_due" => $data['more_than_180_days_due'],
        "total_onaccount" => $data['total_onaccount'],
        "zero_thirty_days_onaccount" => $data['0-30_days_onaccount'],
        "thirtyone_sixty_days_onaccount" => $data['31-60_days_onaccount'],
        "sixtyone_ninety_days_onaccount" => $data['61-90_days_onaccount'],
        "ninetyone_oneeighty_days_onaccount" => $data['91-180_days_onaccount'],
        "more_than_oneeighty_days_onaccount" => $data['more_than_180_days_onaccount'],
      ];
      $sl++;
    }
    $dynamic_data_all = json_encode($dynamic_data_all);
    $exportToExcelAll = exportToExcelAll($dynamic_data_all, $_POST['coloum'], $_POST['sql_data_checkbox']);
    $res = [
      "status" => true,
      "msg" => "alldataSuccess",
      "all_data" => $dynamic_data_all,
      "sql" => $sql_list,
    ];
  } else {
    $res = [
      "status" => false,
      "msg" => "Error!",
      "sql" => $sql_list
    ];
  }

  echo json_encode([
    'status' => 'success',
    'message' => 'CSV allgenerated',
    'csvContentall' => $exportToExcelAll // Encoding CSV content to handle safely in JSON
  ]);
}
