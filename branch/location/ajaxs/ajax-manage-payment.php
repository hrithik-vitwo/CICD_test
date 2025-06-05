<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");
// require_once("../../common/exportexcel.php");
require_once("../../common/exportexcel-new.php");

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
    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";
      global $decimalValue;

      if ($slag === 'pay.postingDate' || $slag === 'pay.created_at') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ") " . $data['operatorName'] . " '" . $data['value']['fromDate'] . "' AND '" . $data['value']['toDate'] . "'";
        } else {
          $conds .= "DATE(" . $slag . ") " . $data['operatorName'] . " '" . $data['value'] . "'";
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "pay.collect_payment") {
        $cleanedValue = str_replace(',', '', $data['value']);

        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
      } elseif ($slag === "pay.status") {
        if ($data['value'] === "Paid" || $data['value'] === "paid") {
          $conds .= $slag . " " . $data['operatorName'] . " 'active'";
        } else {
          $conds .= $slag . " " . $data['operatorName'] . " '" . $data['value'] . "'";
        }
      } elseif ($slag === "party_code") {
        // Handle party_code condition with CASE WHEN
        $conds .= "(CASE 
                          WHEN pay.type = 'vendor' THEN vendor.vendor_code 
                          ELSE cust.customer_code 
                     END) " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "party_name") {
        // Handle party_name condition with CASE WHEN
        $conds .= "(CASE 
                          WHEN pay.type = 'vendor' THEN vendor.trade_name 
                          ELSE cust.trade_name 
                     END) " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND pay.status !='deleted'";
    // $sql_list = "SELECT
    //                   pay.*,
    //                   bank.bank_name,
    //                   bank.account_no,
    //                   vendor.vendor_code,
    //                   vendor.trade_name
    //               FROM
    //                   erp_acc_bank_cash_accounts AS bank
    //               LEFT JOIN erp_grn_payments AS pay
    //               ON
    //                   bank.id = pay.bank_id
    //                   LEFT JOIN `erp_vendor_details` as vendor
    //                   ON pay.vendor_id=vendor.vendor_id
    //              WHERE pay.company_id = '" . $company_id . "' AND pay.branch_id ='" . $branch_id . "' AND pay.location_id ='" . $location_id . "' " . $cond . " " . $sts . " ORDER BY pay.payment_id DESC";


    $sql_list = "SELECT
                  pay.*,
                  bank.bank_name,
                  bank.account_no,
                  CASE 
                      WHEN pay.type = 'vendor' THEN vendor.vendor_code 
                      ELSE cust.customer_code 
                  END AS party_code,
                  CASE 
                      WHEN pay.type = 'vendor' THEN vendor.trade_name 
                      ELSE cust.trade_name 
                  END AS party_name
              FROM
                  erp_acc_bank_cash_accounts AS bank
              LEFT JOIN erp_grn_payments AS pay
                  ON bank.id = pay.bank_id
              LEFT JOIN erp_vendor_details AS vendor
                  ON pay.vendor_id = vendor.vendor_id
              LEFT JOIN erp_customer AS cust
                  ON pay.customer_id = cust.customer_id
              WHERE 
                  pay.company_id = '" . $company_id . "' 
                  AND pay.branch_id = '" . $branch_id . "' 
                  AND pay.location_id = '" . $location_id . "' 
                  " . $cond . " " . $sts . "
              ORDER BY pay.payment_id DESC";

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

        // $keyPrefix_code = ($data['type'] === 'vendor') ? 'vendor.vendor_code' : 'cust.customer_code';
        // $keyPrefix_name = ($data['type'] === 'vendor') ? 'vendor.trade_name' : 'cust.trade_name';


        $dynamic_data[] = [
          "sl_no" => $sl,
          "payment_id" => base64_encode($data['payment_id']),
          "pay.transactionId" => $data['transactionId'],
          "party_code" => $data['party_code'],
          "party_name" => $data['party_name'],
          "pay.postingDate" => formatDateWeb($data['postingDate']),
          "bank.bank_name" => $data['bank_name'],
          "pay.collect_payment" => decimalValuePreview($data['collect_payment']),
          "pay.status" => $data['status']
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

      // $csvContent = exportToExcelAll($sql_list, json_encode($columnMapping));
      // $csvContentBypagination = exportToExcelByPagin($sql_Mainqry, json_encode($columnMapping));

      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        "limit_per_Page" => $limit_per_Page,
        // "csvContent" => $csvContent,
        // "csvContentBypagination" => $csvContentBypagination,
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
if ($_POST['act'] == 'alldata') {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $formObj = $_POST['formDatas'];
    $cond = "";

    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";
      global $decimalValue;

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
        $cleanedValue = str_replace(',', '', $data['value']);

        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
      } elseif ($slag === "party_code") {
        // Handle party_code condition with CASE WHEN
        $conds .= "(CASE 
                          WHEN pay.type = 'vendor' THEN vendor.vendor_code 
                          ELSE cust.customer_code 
                     END) " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "party_name") {
        // Handle party_name condition with CASE WHEN
        $conds .= "(CASE 
                          WHEN pay.type = 'vendor' THEN vendor.trade_name 
                          ELSE cust.trade_name 
                     END) " . $data['operatorName'] . " '%" . $data['value'] . "%'";
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

    $sql_list = "SELECT
      pay.postingDate as postingDate,pay.transactionId as transactionId , pay.collect_payment as collect_payment , pay.status as status , 
      bank.bank_name as bank_name,
      bank.account_no,
      CASE 
          WHEN pay.type = 'vendor' THEN vendor.vendor_code 
          ELSE cust.customer_code 
      END AS party_code,
      CASE 
          WHEN pay.type = 'vendor' THEN vendor.trade_name 
          ELSE cust.trade_name 
      END AS party_name
  FROM
      erp_acc_bank_cash_accounts AS bank
  LEFT JOIN erp_grn_payments AS pay
      ON bank.id = pay.bank_id
  LEFT JOIN erp_vendor_details AS vendor
      ON pay.vendor_id = vendor.vendor_id
  LEFT JOIN erp_customer AS cust
      ON pay.customer_id = cust.customer_id
  WHERE 
      pay.company_id = '" . $company_id . "' 
      AND pay.branch_id = '" . $branch_id . "' 
      AND pay.location_id = '" . $location_id . "' 
      " . $cond . " " . $sts . "
  ORDER BY pay.payment_id DESC";



    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list = $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
      $sl = 1;
      foreach ($sql_data_all as $data) {


        $dynamic_data_all[] = [
          "sl_no" => $sl,
          "pay.transactionId" => $data['transactionId'],
          "party_code" => $data['party_code'],
          "party_name" => $data['party_name'],
          "pay.postingDate" => formatDateWeb($data['postingDate']),
          "bank.bank_name" => $data['bank_name'],
          "pay.collect_payment" => decimalValuePreview($data['collect_payment']),
          "pay.status" => $data['status']
        ];
        $sl++;
      }

      $dynamic_data_all = json_encode($dynamic_data_all);
      $exportToExcelAll = exportToExcelAll($dynamic_data_all, $_POST['coloum'], $_POST['sql_data_checkbox']);
      $res = [
        "status" => true,
        "msg" => "CSV all generated",
        'csvContentall' => $exportToExcelAll,
        "sql" => $sql_list,
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