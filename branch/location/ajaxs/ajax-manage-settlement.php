<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");
require_once("../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');
session_start();


if ($_POST['act'] == 'managePr') {
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
      if ($slag === 'pr.expectedDate') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND `pLog.status` !='deleted'";

    // $sql_list = "SELECT pr.*,admin.fldAdminName,stat.label FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr LEFT JOIN tbl_branch_admin_details as admin on pr.created_by=admin.fldAdminKey   LEFT JOIN `erp_status_master` AS stat
    // ON pr.pr_status = stat.status_id WHERE 1 " . $cond . "  AND pr.company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' " . $sts . "  ORDER BY purchaseRequestId desc";

    $sql_list = "SELECT log.*,payment.documentDate,payment.transactionId FROM (SELECT payment_id, sum(payment_amt) as advancedAmt,cust.customer_code,pLog.status as status,cust.trade_name as customer_name FROM `erp_branch_sales_order_payments_log` as pLog LEFT JOIN `erp_customer` as cust ON pLog.customer_id = cust.customer_id WHERE payment_type = 'advanced' AND pLog.location_id = $location_id GROUP BY payment_id) as log INNER JOIN `erp_branch_sales_order_payments` as payment ON log.payment_id = payment.payment_id WHERE 1 $cond";

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

        if ($data['label'] == "open") {
          $status = '<div class="status-bg status-open">Open</div>';
        } elseif ($data['label'] == "closed") {
          $status = '<div class="status-bg status-closed">Closed</div>';
        }

        $dynamic_data[] = [
          "sl_no" => $sl,
          "customerId"=>$data['customer_code'],
          "customerName" => $data['customer_name'],
          "transactionNo" => $data['transactionId'],
          "transactionDate" =>formatDateORDateTime($data['documentDate']),
          "transactionAmount" => $data['advancedAmt']

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
        "csvContent" => $csvContent,
        "csvContentBypagination" => $csvContentBypagination

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
