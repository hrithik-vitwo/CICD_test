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

$dbobj = new Database();
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
      if ($slag === 'postingDate' || $slag === 'created_at') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif ($slag === "collect_payment") {

        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
      } elseif ($slag === "status") {
        // if($data['value']==="Collected" || $data['value']==="collected"){
        // $conds .= $slag . " " . $data['operatorName'] . "active";
        // }else{
        //   $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
        // }

        if ($data['value'] === "Collected" || $data['value'] === "collected") {
          $conds .= $slag . " " . $data['operatorName'] . "'active'";
        } else {
          $conds .= $slag . " " . $data['operatorName'] . " '" . $data['value'] . "'";
        }
      } else if ($slag == "party_code") {
        $conds .= "vendor.vendor_code " . $data['operatorName'] . " '%" . $data['value'] . "%' OR cust.customer_code " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } else if ($slag == "party_name") {
        $conds .= "vendor.trade_name " . $data['operatorName'] . " '%" . $data['value'] . "%' OR cust.trade_name " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }else if ($slag === 'sopayment.created_by' || $slag === 'created_by') {
        $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND sopayment.status IN ('active', 'reverse')";
    $sql_list = "SELECT sopayment.*, CASE WHEN sopayment.type = 'vendor' THEN vendor.vendor_code ELSE cust.customer_code END AS party_code, CASE WHEN sopayment.type = 'vendor' THEN vendor.trade_name ELSE cust.trade_name END AS party_name FROM erp_branch_sales_order_payments AS sopayment LEFT JOIN erp_customer AS cust ON cust.customer_id = sopayment.customer_id LEFT JOIN erp_vendor_details AS vendor ON vendor.vendor_id = sopayment.vendor_id WHERE 1 " . $cond . " AND sopayment.company_id = '" . $company_id . "' AND sopayment.branch_id = '" . $branch_id . "' AND sopayment.location_id = '" . $location_id . "' " . $sts . " ORDER BY sopayment.payment_id DESC";
    $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
    $sqlMainQryObj = $dbobj->queryGet($sql_Mainqry, true);

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
          "paymentId" => $data['payment_id'],
          "postingDate" => $data['postingDate'],
          "transactionId" => $data['transactionId'],
          "paymentCollectType" => $data['paymentCollectType'],
          "collect_payment" => decimalValuePreview($data['collect_payment']),
          "created_at" => $data['created_at'],
          "party_code" => $data['party_code'],
          "party_name" => $data['party_name'],
          "sopayment.created_by" => getCreatedByUser($data['created_by']),
          "status" => $data['status']
        ];
        $sl++;
      }
      $output .= "</table>";
      $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
      $queryset = $dbobj->queryGet($sqlRowCount);
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
        // "sqlMainQryObj" => $sqlMainQryObj

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
if ($_POST['act'] == 'alldata') {
  $formObj = $_POST['formDatas'];
    $cond = "";
    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";
      global $decimalValue;

      if ($slag === 'postingDate' || $slag === 'created_at') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif ($slag === "collect_payment") {

        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
      } elseif ($slag === "status") {
        // if($data['value']==="Collected" || $data['value']==="collected"){
        // $conds .= $slag . " " . $data['operatorName'] . "active";
        // }else{
        //   $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
        // }

        if ($data['value'] === "Collected" || $data['value'] === "collected") {
          $conds .= $slag . " " . $data['operatorName'] . "'active'";
        } else {
          $conds .= $slag . " " . $data['operatorName'] . " '" . $data['value'] . "'";
        }
      } else if ($slag == "party_code") {
        $conds .= "vendor.vendor_code " . $data['operatorName'] . " '%" . $data['value'] . "%' OR cust.customer_code " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } else if ($slag == "party_name") {
        $conds .= "vendor.trade_name " . $data['operatorName'] . " '%" . $data['value'] . "%' OR cust.trade_name " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }else if ($slag === 'sopayment.created_by' || $slag === 'created_by') {
        $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND sopayment.status IN ('active', 'reverse')";
    $sql_list = "SELECT sopayment.*, CASE WHEN sopayment.type = 'vendor' THEN vendor.vendor_code ELSE cust.customer_code END AS party_code, CASE WHEN sopayment.type = 'vendor' THEN vendor.trade_name ELSE cust.trade_name END AS party_name FROM erp_branch_sales_order_payments AS sopayment LEFT JOIN erp_customer AS cust ON cust.customer_id = sopayment.customer_id LEFT JOIN erp_vendor_details AS vendor ON vendor.vendor_id = sopayment.vendor_id WHERE 1 " . $cond . " AND sopayment.company_id = '" . $company_id . "' AND sopayment.branch_id = '" . $branch_id . "' AND sopayment.location_id = '" . $location_id . "' " . $sts . " ORDER BY sopayment.payment_id DESC";
   $dynamic_data_all = [];
   $sqlMainQryObjall = queryGet($sql_list, true);
   $sql_data_all = $sqlMainQryObjall['data'];
   $num_list =  $sqlMainQryObjall['numRows'];
   if ($num_list > 0) {
   foreach ($sql_data_all as $data) {
        
    $dynamic_data_all[]= [
          "sl_no" => $sl,
          "paymentId" => $data['payment_id'],
          "postingDate" => $data['postingDate'],
          "transactionId" => $data['transactionId'],
          "paymentCollectType" => $data['paymentCollectType'],
          "collect_payment" => decimalValuePreview($data['collect_payment']),
          "created_at" => $data['created_at'],
          "party_code" => $data['party_code'],
          "party_name" => $data['party_name'],
          "sopayment.created_by" => getCreatedByUser($data['created_by']),
          "status" => $data['status']
    ];
  }
  $dynamic_data_all=json_encode($dynamic_data_all);
  $exportToExcelAll =exportToExcelAll($dynamic_data_all,$_POST['coloum'],$_POST['sql_data_checkbox']);
  $res = [
    "status" => true,
    "msg" => "alldataSuccess",
    "all_data"=>$dynamic_data_all,
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
