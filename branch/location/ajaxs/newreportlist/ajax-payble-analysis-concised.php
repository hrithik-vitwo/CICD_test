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
  
      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
          if ($data['operatorName'] === 'BETWEEN') {
              $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
          } else {
              $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
          }
      } elseif (strcasecmp($data['value'], 'Goods') === 0)  {
          $data['value'] = 'material';
          $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }elseif($slag==="totalAmount"){
        $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
      } else {
          $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }
  
      return !empty($data['value']) ? " AND " . $conds : "";
  }, array_keys($formObj), $formObj));
  

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    // $sts = " AND `status` !='deleted'";
    $sql_list = "SELECT 
    vendor_code, 
    vendor_name, 
    SUM(CASE WHEN transaction_type = 'INVOICE' THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'CREDIT NOTE' THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'DEBIT NOTE' THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS NOT null THEN total_amount ELSE 0 END) AS total_due,
    SUM(CASE WHEN transaction_type = 'INVOICE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 0 AND 30 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 0 AND 30 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 0 AND 30 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS NOT null AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 0 AND 30 THEN total_amount ELSE 0 END) AS '0_30_days_due',
    SUM(CASE WHEN transaction_type = 'INVOICE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 31 AND 60 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 31 AND 60 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 31 AND 60 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS NOT null AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 31 AND 60 THEN total_amount ELSE 0 END) AS '31_60_days_due',
    SUM(CASE WHEN transaction_type = 'INVOICE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 61 AND 90 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 61 AND 90 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 61 AND 90 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS NOT null AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 61 AND 90 THEN total_amount ELSE 0 END) AS '61_90_days_due',
    SUM(CASE WHEN transaction_type = 'INVOICE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 91 AND 180 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 91 AND 180 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 91 AND 180 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS NOT null AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 91 AND 180 THEN total_amount ELSE 0 END) AS '91_180_days_due',
    SUM(CASE WHEN transaction_type = 'INVOICE' AND DATEDIFF(CURRENT_DATE, posting_date) > 180 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'CREDIT NOTE' AND DATEDIFF(CURRENT_DATE, posting_date) > 180 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'DEBIT NOTE' AND DATEDIFF(CURRENT_DATE, posting_date) > 180 THEN total_amount ELSE 0 END) +
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS NOT null AND DATEDIFF(CURRENT_DATE, posting_date) > 180 THEN total_amount ELSE 0 END) AS 'more_than_180_days_due',
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS null THEN total_amount ELSE 0 END) AS total_unaccount,
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS null AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 0 AND 30 THEN total_amount ELSE 0 END) AS '0_30_days_unaccount',
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS null AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 31 AND 60 THEN total_amount ELSE 0 END) AS '31_60_days_unaccount',
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS null AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 61 AND 90 THEN total_amount ELSE 0 END) AS '61_90_days_unaccount',
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS null AND DATEDIFF(CURRENT_DATE, posting_date) BETWEEN 91 AND 180 THEN total_amount ELSE 0 END) AS '91_180_days_unaccount',
    SUM(CASE WHEN transaction_type = 'PAYMENT' AND reference_no IS null AND DATEDIFF(CURRENT_DATE, posting_date) > 180 THEN total_amount ELSE 0 END) AS 'more_than_180_days_unaccount'
FROM 
    (SELECT vend.vendor_code, vend.trade_name AS vendor_name, grn.grnIvCode AS document_no, grn.postingDate AS posting_date, grn.grnPoNumber AS reference_no, 'INVOICE' AS transaction_type, grn.grnSubTotal AS base_amount, grn.grnTotalCgst AS cgst, grn.grnTotalSgst AS sgst, grn.grnTotalIgst AS igst, grn.grnTotalAmount AS total_amount FROM erp_vendor_details AS vend LEFT JOIN erp_grninvoice AS grn ON vend.vendor_id = grn.vendorId WHERE grn.companyId=1 AND grn.branchId=1 AND grn.locationId=1 AND grn.postingDate BETWEEN '2024-04-01' AND '2025-03-31'
UNION ALL
SELECT vend.vendor_code, vend.trade_name AS vendor_name, cn.credit_note_no AS document_no, cn.postingDate AS posting_date, grn.grnIvCode AS reference_no, 'CREDIT NOTE' AS transaction_type, 0.00 AS base_amount, 0.00 AS igst, 0.00 AS sgst, 0.00 AS cgst, -cn.total AS total_amount FROM erp_vendor_details AS vend LEFT JOIN erp_credit_note AS cn ON vend.vendor_id = cn.party_id AND cn.creditors_type = 'vendor' LEFT JOIN erp_grninvoice AS grn ON cn.creditNoteReference = grn.grnIvId WHERE cn.company_id=1 AND cn.branch_id=1 AND cn.location_id=1 AND cn.postingDate BETWEEN '2024-04-01' AND '2025-03-31'
UNION ALL
SELECT vend.vendor_code, vend.trade_name AS vendor_name, dn.debit_note_no AS document_no, dn.postingDate AS posting_date, grn.grnIvCode AS reference_no, 'DEBIT NOTE' AS transaction_type, 0.00 AS base_amount, 0.00 AS igst, 0.00 AS sgst, 0.00 AS cgst, dn.total AS total_amount FROM erp_vendor_details AS vend LEFT JOIN erp_debit_note AS dn ON vend.vendor_id = dn.party_id AND dn.debitor_type = 'vendor' LEFT JOIN erp_grninvoice AS grn ON dn.debitNoteReference = grn.grnIvId WHERE dn.company_id=1 AND dn.branch_id=1 AND dn.location_id=1 AND dn.postingDate BETWEEN '2024-04-01' AND '2025-03-31'
UNION ALL
SELECT vend.vendor_code, vend.trade_name AS vendor_name, pay.paymentCode AS document_no, pay.postingDate AS posting_date, grn.grnIvCode AS reference_no, 'PAYMENT' AS transaction_type, 0.00 AS base_amount, 0.00 AS igst, 0.00 AS sgst, 0.00 AS cgst, -log.payment_amt AS total_amount FROM erp_vendor_details AS vend LEFT JOIN erp_grn_payments AS pay ON vend.vendor_id = pay.vendor_id LEFT JOIN erp_grn_payments_log AS log ON pay.payment_id = log.payment_id LEFT JOIN erp_grninvoice AS grn ON vend.vendor_id = grn.vendorId WHERE pay.company_id=1 AND pay.branch_id=1 AND pay.location_id=1 AND log.payment_type = 'pay' AND pay.postingDate BETWEEN '2024-04-01' AND '2025-03-31') AS transactions
     GROUP BY vendor_code, vendor_name";

    // $sql_Mainqry = $sql_list . "  ORDER BY so.so_id DESC LIMIT " . $offset . "," . $limit_per_Page . ";";
    $sql_Mainqry = $sql_list." LIMIT ". $offset . "," . $limit_per_Page . ";";
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
          "vendor_code" => $data['vendor_code'],
          "vendor_name" => $data['vendor_name'],
          "total_due" => $data['total_due'],
          "zero_thirty_days_due" => $data['0_30_days_due'],
          "thirtyone_sixty_days_due" => $data['31_60_days_due'],
          "sixtyone_ninety_days_due" => $data['61_90_days_due'],
          "ninetyone_oneeighty_days_due" => $data['91_180_days_due'],
          "more_than_oneeighty_days_due" => $data['more_than_180_days_due'],
          "total_unaccount" => $data['total_unaccount'],
          "zero_thirty_days_unaccount" => $data['0_30_days_unaccount'],
          "thirtyone_sixty_days_unaccount" => $data['31_60_days_unaccount'],
          "sixtyone_ninety_days_unaccount" => $data['61_90_days_unaccount'],
          "ninetyone_oneeighty_days_unaccount" => $data['91_180_days_unaccount'],
          "more_than_oneeighty_days_unaccount" => $data['more_than_180_days_unaccount'],
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
      
      $csvContent=exportToExcelAll($sql_list,json_encode($columnMapping));
      $csvContentBypagination=exportToExcelByPagin($sql_Mainqry,json_encode($columnMapping));

      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        "limit_per_Page" => $limit_per_Page,
        "csvContent"=>$csvContent,
        "csvContentBypagination"=>$csvContentBypagination,
        "sql" => $sql_list


      ];
    } else {
      $res = [
        "status" => false,
        "msg" => "Error!",
        "sql" => $sqlMainQryObj
      ];
    }

    echo json_encode($res);
  }
}
