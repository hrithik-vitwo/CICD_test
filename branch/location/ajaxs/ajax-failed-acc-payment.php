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
    $type = $_POST['invoicetype'];
    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";

      if ($slag === 'pay.postingDate' || $slag === 'pay.created_at') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "pay.collect_payment") {
        $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
      } elseif ($slag === "pay.status") {
        if ($data['value'] === "Paid" || $data['value'] === "paid") {
          $conds .= $slag . " " . $data['operatorName'] . "'active'";
        } else {
          $conds .= $slag . " " . $data['operatorName'] . " '" . $data['value'] . "'";
        }
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    //$sts = " AND pay.status !='deleted'";
    if ($type == 'active') {
    $sql_list = "SELECT pay.*, bank.bank_name, bank.account_no, vendor.vendor_code, vendor.trade_name FROM erp_acc_bank_cash_accounts AS bank LEFT JOIN erp_grn_payments AS pay ON bank.id = pay.bank_id LEFT JOIN erp_vendor_details AS vendor ON pay.vendor_id = vendor.vendor_id WHERE pay.company_id = '" . $company_id . "' AND pay.branch_id = '" . $branch_id . "' AND pay.location_id = '" . $location_id . "' " . $cond . " AND pay.status = 'active' AND (pay.journal_id = 0 OR pay.journal_id IS NULL) ORDER BY pay.payment_id DESC";

    }else{
      $sql_list = "SELECT pay.*, bank.bank_name, bank.account_no, vendor.vendor_code, vendor.trade_name FROM erp_acc_bank_cash_accounts AS bank LEFT JOIN erp_grn_payments AS pay ON bank.id = pay.bank_id LEFT JOIN erp_vendor_details AS vendor ON pay.vendor_id = vendor.vendor_id WHERE pay.company_id = '" . $company_id . "' AND pay.branch_id = '" . $branch_id . "' AND pay.location_id = '" . $location_id . "' " . $cond . " AND pay.status = 'reverse' AND (pay.journal_id != 0 AND pay.journal_id IS NOT NULL) AND (pay.reverse_journal_id = 0 OR pay.reverse_journal_id IS NULL) AND pay.created_at>'2025-05-20'  ORDER BY pay.payment_id DESC";

    }

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


        $dynamic_data[] = [
          "sl_no" => $sl,
          "payment_id" => base64_encode($data['payment_id']),
          "postingDate" => $data['postingDate'],
          "transactionId" => $data['transactionId'],
          "bank_name" => $data['bank_name'],
          "collect_payment" => $data['collect_payment'],
          "vendorcode" => $data['vendor_code'],
          "vendorname" => $data['trade_name'],
          "status" => $data['status']
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
        "sql" => $sql_list,
        "type"=>$type
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
