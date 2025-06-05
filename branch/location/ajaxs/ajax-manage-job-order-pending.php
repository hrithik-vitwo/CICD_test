<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("pagination/common-pagination.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
$headerData = array('Content-Type: application/json');
// require_once("../../common/exportexcel.php");
require_once("../../common/exportexcel-new.php");
// print_r($_POST);
// $currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);
session_start();

if ($_POST['act'] == 'detailed_view') {
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
      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      }
      else if($slag==="so.totalAmount"){

        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
      }
      else if($slag==="so.totalItems")
      {
        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;   
      } else if ($slag === "so.jobOrderApprovalStatus") {
        $data['value'] = strtolower($data['value']);
        if ($data['value'] == 'pending') {
          $data['value'] = 14;

        } else if ($data['value'] == 'approved') {
          $data['value'] = 11;

        } else {
          $data['value'] = ($data['value'] == 'open') ? 9 : '';
        }

        if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
          $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
          // $resultList = getAdminUserIdByName($data['value']);
          // $new_slag = 'varient.' . $slag;

          if (strpos($data['value'], ',') !== false) {
            $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
            $conds .= $slag . " $opr (" . $data['value'] . ")";
          } else {
            $conds .= $slag . " $opr '%" . $data['value'] . "%'";
          }
        }
      }
      else if($slag==="approvalStatus" && strtoupper($data['value'])=='OPEN'){
        $data['value']=9;
        $conds .= $slag . " = '" . $data['value'] . "'";
      }else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND `status` !='deleted'";

    $sql_list = "SELECT so.*,cust.customer_code,cust.trade_name AS customer_name FROM `erp_branch_sales_order` AS so LEFT JOIN erp_customer AS cust ON so.customer_id = cust.customer_id WHERE 1 " . $cond . " AND so.company_id = '" . $company_id . "' AND so.branch_id = '" . $branch_id . "' AND so.location_id = '" . $location_id . "' AND so.goodsType = 'project' AND so.approvalStatus = 9 AND so.jobOrderApprovalStatus IN (14,9,11) " . $sts . " ORDER BY so.so_id DESC";

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

        $goodsType = "";
        if ($data['goodsType'] === "project") {
          $goodsType_page = '<p class="goods-type type-project">PROJECT</p>';
        }

  
        $jobOrderApprovalStatus = '';
        if ($data['jobOrderApprovalStatus'] == 14) {
            $jobOrderApprovalStatus_page = '<div class="status-bg status-pending">PENDING</div>';
            $jobOrderApprovalStatus='PENDING';
        } else if ($data['jobOrderApprovalStatus'] == 11) {
            $jobOrderApprovalStatus_page = '<div class="status-bg status-closed">APPROVED</div>';
            $jobOrderApprovalStatus='APPROVED';
        } else if ($data['jobOrderApprovalStatus'] == 9) {
            $jobOrderApprovalStatus_page = '<div class="status-bg status-open">OPEN</div>';
            $jobOrderApprovalStatus='OPEN';
        }


        $dynamic_data[] = [
          "sl_no" => $sl,
          "soId"=>$data['so_id'],
          "so_number" => $data['so_number'],
          "customer_po_no" => $data['customer_po_no'],
          "so_date" => $data['so_date'],
          "created_at" => explode(" ", $data['created_at'])[0],
          "delivery_date" => formatDateWeb($data['delivery_date']),
          "cust.trade_name" =>  $data['customer_name'],
          "cust.customer_code"=>$data['customer_code'],          
          "so.totalAmount"=>decimalValuePreview($data['totalAmount']),
          "so.totalItems"=>decimalQuantityPreview($data['totalItems']),
          "goodsType" => $data['goodsType'], 
          "goodsType_page" =>$goodsType_page,         
          "jobOrderApprovalStatus" => $jobOrderApprovalStatus,
          "jobOrderApprovalStatus_page"=>$jobOrderApprovalStatus_page,
          "approvalStatus"=>fetchStatusMasterByCode($data['approvalStatus'])['data']['label']
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
      // $csvContent=exportToExcelAll($sql_list,json_encode($columnMapping));
      // $csvContentBypagination=exportToExcelByPagin($sql_Mainqry,json_encode($columnMapping));
      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        // "csvContent"=>$csvContent,
        // "csvContentBypagination"=>$csvContentBypagination,
        "sql" => $sql_list
      ];
    } else {
      $res = [
        "status" => false,
        "msg" => "Error!",
        "sql" => $sql_list,
        "cond" => $cond,
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
      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      }
      else if($slag==="so.totalAmount"){

        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
      }
      else if($slag==="so.totalItems")
      {
        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;   
      }else if ($slag === "so.jobOrderApprovalStatus") {
        $data['value'] = strtolower($data['value']);
        if ($data['value'] == 'pending') {
          $data['value'] = 14;

        } else if ($data['value'] == 'approved') {
          $data['value'] = 11;

        } else {
          $data['value'] = ($data['value'] == 'open') ? 9 : '';
        }

        if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
          $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
          // $resultList = getAdminUserIdByName($data['value']);
          // $new_slag = 'varient.' . $slag;

          if (strpos($data['value'], ',') !== false) {
            $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
            $conds .= $slag . " $opr (" . $data['value'] . ")";
          } else {
            $conds .= $slag . " $opr '%" . $data['value'] . "%'";
          }
        }
      }else if($slag==="approvalStatus" && strtoupper($data['value'])=='OPEN'){
        $data['value']=9;
        $conds .= $slag . " = '" . $data['value'] . "'";
      }else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND `status` !='deleted'";

    $sql_list = "SELECT so.*,cust.customer_code,cust.trade_name AS customer_name FROM `erp_branch_sales_order` AS so LEFT JOIN erp_customer AS cust ON so.customer_id = cust.customer_id WHERE 1 " . $cond . " AND so.company_id = '" . $company_id . "' AND so.branch_id = '" . $branch_id . "' AND so.location_id = '" . $location_id . "' AND so.goodsType = 'project' AND so.approvalStatus = 9 AND so.jobOrderApprovalStatus IN (14) " . $sts . " ORDER BY so.so_id DESC";

   $dynamic_data_all = [];
   $sqlMainQryObjall = queryGet($sql_list, true);
   $sql_data_all = $sqlMainQryObjall['data'];
   $num_list =  $sqlMainQryObjall['numRows'];
   if ($num_list > 0) {
   foreach ($sql_data_all as $data) {

    $goodsType = "";
    if ($data['goodsType'] === "project") {
      $goodsType_page = '<p class="goods-type type-project">PROJECT</p>';
    }


    $jobOrderApprovalStatus = '';
    if ($data['jobOrderApprovalStatus'] == 14) {
        $jobOrderApprovalStatus_page = '<div class="status-bg status-pending">PENDING</div>';
        $jobOrderApprovalStatus='PENDING';
    } else if ($data['jobOrderApprovalStatus'] == 11) {
        $jobOrderApprovalStatus_page = '<div class="status-bg status-closed">APPROVED</div>';
        $jobOrderApprovalStatus='APPROVED';
    } else if ($data['jobOrderApprovalStatus'] == 9) {
        $jobOrderApprovalStatus_page = '<div class="status-bg status-open">OPEN</div>';
        $jobOrderApprovalStatus='OPEN';
    }

        
    $dynamic_data_all[]= [
          "sl_no" => $sl,
          "soId"=>$data['so_id'],
          "so_number" => $data['so_number'],
          "customer_po_no" => $data['customer_po_no'],
          "so_date" => $data['so_date'],
          "created_at" => explode(" ", $data['created_at'])[0],
          "delivery_date" => formatDateWeb($data['delivery_date']),
          "cust.trade_name" =>  $data['customer_name'],
          "cust.customer_code"=>$data['customer_code'],          
          "so.totalAmount"=>decimalValuePreview($data['totalAmount']),
          "so.totalItems"=>decimalQuantityPreview($data['totalItems']),
          "goodsType" => $data['goodsType'], 
          "goodsType_page" =>$goodsType_page,         
          "jobOrderApprovalStatus" => $jobOrderApprovalStatus,
          "jobOrderApprovalStatus_page"=>$jobOrderApprovalStatus_page,
          "approvalStatus"=>fetchStatusMasterByCode($data['approvalStatus'])['data']['label']
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
