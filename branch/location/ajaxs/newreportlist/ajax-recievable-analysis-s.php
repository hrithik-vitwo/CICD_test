<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../pagination/common-pagination.php");

require_once("../../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');

// // print_r($_POST);
// $currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);
$BranchSoObj = new BranchSo();
session_start();

if ($_POST['act'] == 'tdata') {
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
      } elseif ($slag === "base_amount"||$slag === "igst"||$slag === "sgst"||$slag === "cgst"||$slag === "total_amount") {
        $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
      } elseif ($slag === "created_by" || $slag === "updated_by"){

        $resultList = getAdminUserIdByName($data['value']);                        
        $conds .= $slag . " IN  " . " (" . $resultList. ")";

    } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    // $sts = " AND `status` !='deleted'";
    $sql_list = "SELECT customer_code, customer_name, document_no, posting_date, reference_no, transaction_type, base_amount, igst, sgst, cgst, total_amount, created_at, created_by, updated_at, updated_by FROM( SELECT cust.customer_code, cust.trade_name AS customer_name, inv.invoice_no AS document_no, inv.invoice_date AS posting_date, inv.credit_period AS credit_period, CASE WHEN inv.pgi_id != 0 AND inv.so_id = '' THEN pgi.pgi_no WHEN inv.pgi_id = 0 AND inv.so_id != '' THEN so.so_number ELSE '-' END AS reference_no, 'INVOICE' AS transaction_type, ( inv.sub_total_amt - inv.totalDiscount ) AS base_amount, inv.igst, inv.sgst, inv.cgst, inv.all_total_amt AS total_amount, inv.created_at, inv.created_by, inv.updated_at, inv.updated_by FROM erp_customer AS cust LEFT JOIN erp_branch_sales_order_invoices AS inv ON cust.customer_id = inv.customer_id LEFT JOIN erp_branch_sales_order AS so ON inv.so_id = so.so_id LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi ON inv.pgi_id = pgi.so_delivery_pgi_id WHERE inv.company_id = $company_id AND inv.branch_id = $branch_id AND inv.location_id = $location_id AND inv.status = 'active' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, inv.invoice_no AS document_no, inv.invoice_date AS posting_date, inv.credit_period AS credit_period, CASE WHEN inv.pgi_id != 0 AND inv.so_id = '' THEN pgi.pgi_no WHEN inv.pgi_id = 0 AND inv.so_id != '' THEN so.so_number ELSE '-' END AS reference_no, 'REV INVOICE' AS transaction_type, ( inv.sub_total_amt - inv.totalDiscount ) AS base_amount, inv.igst, inv.sgst, inv.cgst, inv.all_total_amt AS total_amount, inv.created_at, inv.created_by, inv.updated_at, inv.updated_by FROM erp_customer AS cust LEFT JOIN erp_branch_sales_order_invoices AS inv ON cust.customer_id = inv.customer_id LEFT JOIN erp_branch_sales_order AS so ON inv.so_id = so.so_id LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi ON inv.pgi_id = pgi.so_delivery_pgi_id WHERE inv.company_id = $company_id AND inv.branch_id = $branch_id AND inv.location_id = $location_id AND inv.status = 'reverse' UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, cn.credit_note_no AS document_no, cn.postingDate AS posting_date, NULL AS credit_period, inv.invoice_no AS reference_no, 'CREDIT NOTE' AS transaction_type, 0.00 AS base_amount, 0.00 AS igst, 0.00 AS sgst, 0.00 AS cgst, cn.total AS total_amount, cn.created_at, cn.created_by, cn.updated_at, cn.updated_by FROM erp_customer AS cust LEFT JOIN erp_credit_note AS cn ON cust.customer_id = cn.party_id AND cn.creditors_type = 'customer' LEFT JOIN erp_branch_sales_order_invoices AS inv ON cn.creditNoteReference = inv.so_invoice_id WHERE cn.company_id = $company_id AND cn.branch_id = $branch_id AND cn.location_id = $location_id UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, dn.debit_note_no AS document_no, dn.postingDate AS posting_date, NULL AS credit_period, inv.invoice_no AS reference_no, 'DEBIT NOTE' AS transaction_type, 0.00 AS base_amount, 0.00 AS igst, 0.00 AS sgst, 0.00 AS cgst, dn.total AS total_amount, dn.created_at, dn.created_by, dn.updated_at, dn.updated_by FROM erp_customer AS cust LEFT JOIN erp_debit_note AS dn ON cust.customer_id = dn.party_id AND dn.debitor_type = 'customer' LEFT JOIN erp_branch_sales_order_invoices AS inv ON dn.debitNoteReference = inv.so_invoice_id WHERE dn.company_id = $company_id AND dn.branch_id = $branch_id AND dn.location_id = $location_id UNION ALL SELECT cust.customer_code, cust.trade_name AS customer_name, col.collectionCode AS document_no, col.postingDate AS posting_date, NULL AS credit_period, inv.invoice_no AS reference_no, 'COLLECTION' AS transaction_type, 0.00 AS base_amount, 0.00 AS igst, 0.00 AS sgst, 0.00 AS cgst, LOG.payment_amt AS total_amount, col.created_at, col.created_by, col.updated_at, col.updated_by FROM erp_customer AS cust LEFT JOIN erp_branch_sales_order_payments AS col ON cust.customer_id = col.customer_id LEFT JOIN erp_branch_sales_order_payments_log AS LOG ON col.payment_id = LOG.payment_id LEFT JOIN erp_branch_sales_order_invoices AS inv ON LOG.invoice_id = inv.so_invoice_id WHERE col.company_id = $company_id AND col.branch_id = $branch_id AND col.location_id = $location_id) AS subquery WHERE 1 ".$cond."";

    // $sql_Mainqry = $sql_list . "  ORDER BY so.so_id DESC LIMIT " . $offset . "," . $limit_per_Page . ";";
    $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
    $sqlMainQryObj = queryGet($sql_Mainqry, true);

    $dynamic_data = [];
    $num_list = $sqlMainQryObj['numRows'];
    $sql_data = $sqlMainQryObj['data'];
    $output = "";
    $limitText = "";
    $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

    if ($num_list > 0) {
      foreach ($sql_data as $data) {

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
          "document_date" => $data['posting_date'],
          "document_no" => $data['document_no'],
          "reference_no" => $data['reference_no'],
          "transaction_type" => $data['transaction_type'],
          "customer_code" => $data['customer_code'],
          "customer_name" => $data['customer_name'],
          "base_amount" => $data['base_amount'],
          "cust_igst" => $data['igst'],
          "cust_sgst" => $data['sgst'],
          "cust_cgst" => $data['cgst'],
          "total_amount" => $data['total_amount'],
          "created_at" => $data['created_at'],
          "created_by" => getCreatedByUser($data['created_by']),
          "updated_at" => $data['updated_at'],
          "updated_by" => getCreatedByUser($data['updated_by']),
        ];
        $sl++;
      }
      $output .= "</table>";
      $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
      $queryset = queryGet($sqlRowCount);
      $totalRows = $queryset['data']['row_count'];
      $total_page = ceil($totalRows / $limit_per_Page);

      $output .= pagiNation($page_no, $total_page);

      $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';

      $csvContent = exportToExcelAll($sql_list, json_encode($columnMapping));
      $csvContentBypagination = exportToExcelByPagin($sql_Mainqry, json_encode($columnMapping));

      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        "limit_per_Page" => $limit_per_Page,
        "csvContent" => $csvContent,
        "csvContentBypagination" => $csvContentBypagination,
        "sql" => $sql_list


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
