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
session_start();
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);
if ($_POST['act'] == 'sodelivery') {
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
      global $decimalQuantity;
      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'del.delivery_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Reversed') === 0) {
        $slag = 'del.status';
        $data['value'] = 'reverse';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "del.totalAmount") {

        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
      } 
      else if($slag === "del.totalItems")
      {
        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;    
      }
      else if ($slag === 'del.created_by' || $slag === 'created_by') {
        if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
          $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
          $resultList = getAdminUserIdByName($data['value']);
          // $new_slag = 'varient.' . $slag;

          if (strpos($resultList, ',') !== false) {
              $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
              $conds .= $slag . " $opr (" . $resultList . ")";
          } else {
              $conds .= $slag . " $opr '%" . $resultList . "%'";
          }
      }
      } else if ($slag === 'del.status') {
        $val = ($data['value'] == "open") ? "active" : $data['value'];
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $val . "%'";
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }
      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }
    
    $sts = " AND `status` !='deleted'";
    $sql_list = "SELECT del.*,cust.trade_name AS customer_name  FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "`AS del LEFT JOIN `erp_customer` AS cust ON del.customer_id = cust.customer_id  WHERE 1 AND del.company_id='" . $company_id . "' AND del.branch_id='" . $branch_id . "' AND del.location_id='" . $location_id . "' " . $cond . " " . $sts . " ORDER BY del.so_delivery_id DESC";
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
        $del_status = '';
        if ($data['status'] === 'reverse') {
          $del_status .= 'Reversed';
        } else {
          $del_status .= $data['deliveryStatus'];
        }
        $dynamic_data[] = [
          "sl_no" => $sl,
          "del.delivery_no" => $data['delivery_no'],
          "del.so_number" => $data['so_number'],
          "del.delivery_date" => formatDateWeb($data['delivery_date']),
          "cust.trade_name" => $data['customer_name'],
          "del.totalAmount" => decimalValuePreview($data['totalAmount']),
          "del.totalItems" => decimalQuantityPreview($data['totalItems']),
          "created_by" => getCreatedByUser($data['created_by']),
          "del_status" => $del_status,
          "del.status" => $data['status'],
          "so_del_id" => $data['so_delivery_id']
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
        // "csvContent" => $csvContent,
        // "csvContentBypagination" => $csvContentBypagination,
        // "sql" => $sql_list
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
      global $decimalValue;
    global $decimalQuantity;
      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'del.delivery_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Reversed') === 0) {
        $slag = 'del.status';
        $data['value'] = 'reverse';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "del.totalAmount") {

        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
      } 
      else if($slag === "del.totalItems")
      {
        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;    
      }
      else if ($slag === 'del.created_by' || $slag === 'created_by') {
        if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
          $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
          $resultList = getAdminUserIdByName($data['value']);
          // $new_slag = 'varient.' . $slag;

          if (strpos($resultList, ',') !== false) {
              $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
              $conds .= $slag . " $opr (" . $resultList . ")";
          } else {
              $conds .= $slag . " $opr '%" . $resultList . "%'";
          }
      }
      } else if ($slag === 'del.status') {
        $val = ($data['value'] == "open") ? "active" : $data['value'];
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $val . "%'";
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }
      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }
    
    $sts = " AND `status` !='deleted'";
    $sql_list = "SELECT del.*,cust.trade_name AS customer_name  FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "`AS del LEFT JOIN `erp_customer` AS cust ON del.customer_id = cust.customer_id  WHERE 1 AND del.company_id='" . $company_id . "' AND del.branch_id='" . $branch_id . "' AND del.location_id='" . $location_id . "' " . $cond . " " . $sts . " ORDER BY del.so_delivery_id DESC";
   $dynamic_data_all = [];
   $sqlMainQryObjall = queryGet($sql_list, true);
   $sql_data_all = $sqlMainQryObjall['data'];
   $num_list =  $sqlMainQryObjall['numRows'];
   if ($num_list > 0) {
   foreach ($sql_data_all as $data) {
   
    $dynamic_data_all[]= [
      "sl_no" => $sl,
          "del.delivery_no" => $data['delivery_no'],
          "del.so_number" => $data['so_number'],
          "del.delivery_date" => formatDateWeb($data['delivery_date']),
          "cust.trade_name" => $data['customer_name'],
          "del.totalAmount" => decimalValuePreview($data['totalAmount']),
          "del.totalItems" => decimalQuantityPreview($data['totalItems']),
          "created_by" => getCreatedByUser($data['created_by']),
          "del.status" => $data['status'],
          "so_del_id" => $data['so_delivery_id']
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
