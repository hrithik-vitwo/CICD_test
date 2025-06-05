<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");
$headerData = array('Content-Type: application/json');

// require_once("../../common/exportexcel.php");
require_once("../../common/exportexcel-new.php");
session_start();
$_SESSION['columnMapping'] = $_POST['columnMapping'];
if (isset($_SESSION['columnMapping'])) {
  $columnMapping = $_SESSION['columnMapping'];
}


// echo json_encode("Hii");

// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'sopgi') {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

    $page_no = isset($_POST['pageNo']) ? (int)$_POST['pageNo'] : 1;
    $page_no = max(1, $page_no);

    $offset = ($page_no - 1) * $limit_per_Page;
    $maxPagesl = $page_no * $limit_per_Page;
    $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
    $formObj = $_POST['formDatas'];
    $cond = "";
    // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


    $implodeFrom = implode('', array_map(function ($slag, $data){
      $conds="";
      global $decimalValue;
      global $decimalQuantity;
      if ($slag === 'so.pgiDate') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(".$slag.")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(".$slag.")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      }else if($slag==="so.totalItems"){

        $cleanedValue = str_replace(',', '', $data['value']);


        // Single value case
        $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;   
      } else if($slag === 'so.created_by' || $slag==='created_by'){

        $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";

    }else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND `status` !='deleted'";
                   
    $sql_list = "SELECT so.*, cust.trade_name AS customer_name FROM `erp_branch_sales_order_delivery_pgi` AS so LEFT JOIN erp_customer AS cust ON so.customer_id =cust.customer_id WHERE 1  " . $cond . " AND so.company_id='" . $company_id . "'  AND so.branch_id='" . $branch_id . "' AND so.location_id='" . $location_id . "' AND so.delivery_no IS NOT NULL   " . $sts . " ORDER BY so_delivery_pgi_id desc";
                   
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
          "so.pgi_no"=>$data['pgi_no'],
          "so.customer_po_no"=>$data['customer_po_no'],
          "so.pgiDate"=>formatDateWeb($data['pgiDate']),
          "cust.trade_name"=>$data['customer_name'],
          "so.pgiStatus"=>$data['pgiStatus'],
          "so.totalItems"=>decimalQuantityPreview($data['totalItems']) ,
          "so_delivery_pgi_id"=>$data['so_delivery_pgi_id'] ,
          "so.created_by" => getCreatedByUser($data['created_by']),
          "status"=>$data['status']       
        ];
        $sl++;
      }
      $output .= "</table>";
      $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
      $queryset = queryGet($sqlRowCount);
      $totalRows = $queryset['data']['row_count'];
      $total_page = ceil($totalRows / $limit_per_Page);
      // $output .= '<div class="active" id="pagination">';

      // $output .= '<div class="active" id="pagination">';


      // if ($page_no > 1) {
      //   $output .= "<a id='" . ($page_no - 1) . "' href='?page=" . ($page_no - 1) . "'>Previous</a>";
      // }

      // for ($i = 1; $i <= $total_page; $i++) {
      //   if ($i <= 5 || $i >= $total_page - 1 || ($i >= $page_no - 2 && $i <= $page_no + 2)) {
      //     $output .= "<a id='{$i}' href='?page={$i}'>{$i}</a>";
      //   }
      // }


      // if ($page_no < $total_page) {
      //   $output .= "<a id='" . ($page_no + 1) . "' href='?page=" . ($page_no + 1) . "'>Next</a>";
      //   $output .= "<a id='" . $total_page . "' href='?page=" . ($page_no + 1) . "'>Last</a>";
      // }

      // $output .= '</div>';
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
        // "sqlMain" => $sqlMainQryObj

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
  // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


  $implodeFrom = implode('', array_map(function ($slag, $data){
    $conds="";
    global $decimalQuantity;
    if ($slag === 'so.pgiDate') {
      if ($data['operatorName'] === 'BETWEEN') {
        $conds .= "DATE(".$slag.")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
      } else {
        $conds .= "DATE(".$slag.")" . $data['operatorName'] . ' "' . $data['value'] . '"';
      }
    }else if($slag==="so.totalItems"){

      $cleanedValue = str_replace(',', '', $data['value']);


      // Single value case
      $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
      $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $roundedValue;   
    } else if($slag === 'so.created_by' || $slag==='created_by'){

      $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";

  }else {
      $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
    }

    return !empty($data['value']) ? " AND " . $conds : "";
  }, array_keys($formObj), $formObj));

  if (!empty($implodeFrom)) {
    $cond .= $implodeFrom;
  }

  $sts = " AND `status` !='deleted'";
                 
  $sql_list = "SELECT so.*, cust.trade_name AS customer_name FROM `erp_branch_sales_order_delivery_pgi` AS so LEFT JOIN erp_customer AS cust ON so.customer_id =cust.customer_id WHERE 1  " . $cond . " AND so.company_id='" . $company_id . "'  AND so.branch_id='" . $branch_id . "' AND so.location_id='" . $location_id . "'   " . $sts . " ORDER BY so_delivery_pgi_id desc";
   $dynamic_data_all = [];
   $sqlMainQryObjall = queryGet($sql_list, true);
   $sql_data_all = $sqlMainQryObjall['data'];
   $num_list =  $sqlMainQryObjall['numRows'];
   if ($num_list > 0) {
   foreach ($sql_data_all as $data) {
    $dynamic_data_all[]= [
      "sl_no" => $sl,
          "so.pgi_no"=>$data['pgi_no'],
          "so.customer_po_no"=>$data['customer_po_no'],
          "so.pgiDate"=>formatDateWeb($data['pgiDate']),
          "cust.trade_name"=>$data['customer_name'],
          "so.pgiStatus"=>$data['pgiStatus'],
          "so.totalItems"=>decimalQuantityPreview($data['totalItems']) ,
          "so_delivery_pgi_id"=>$data['so_delivery_pgi_id'] ,
          "so.created_by" => getCreatedByUser($data['created_by']),
          "status"=>$data['status'] 
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