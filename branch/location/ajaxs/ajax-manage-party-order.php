<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");
require_once("../../common/exportexcel-new.php");
// require_once("../../common/exportexcel.php");
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

    $page_no = isset($_POST['pageNo']) ? (int) $_POST['pageNo'] : 1;
    $page_no = max(1, $page_no);

    $offset = ($page_no - 1) * $limit_per_Page;
    $maxPagesl = $page_no * $limit_per_Page;
    $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
    $formObj = $_POST['formDatas'];
    $cond = "";
    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";

      if ($slag === 'so.created_at' || $slag === 'created_at' || $slag === 'delivery_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= " DATE(" . $slag . ") " . $data['operatorName'] . " '" . ($data['value']['fromDate']) . "' AND '" . ($data['value']['toDate']) . "' ";
        } else {
          $conds .= "DATE_FORMAT(" . $slag . ",'%d-%m-%Y')". $data['operatorName'] ."'".formatDateWeb($data['value'])."'";
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "totalAmount") {
        $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
      }  else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }


    $sts = " AND `status` !='deleted'";
    $sql_list = "SELECT so.*, cust.trade_name AS customer_name , cust.customer_code as customer_code FROM `" . ERP_PARTY_ORDER . "` AS so LEFT JOIN erp_customer AS cust ON so.customer_id = cust.customer_id WHERE 1 " . $cond . "  AND so.company_id='" . $company_id . "'  AND so.branch_id='" . $branch_id . "'  AND so.location_id='" . $location_id . "' " . $sts . "  ORDER BY so.id desc";

    // $sql_Mainqry = $sql_list . "  ORDER BY so.so_id DESC LIMIT " . $offset . "," . $limit_per_Page . ";";
    $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
    $sqlMainQryObj = queryGet($sql_Mainqry, true);

    $dynamic_data = [];
    $num_list = $sqlMainQryObj['numRows'];
    $sql_data = $sqlMainQryObj['data'];
    $output = "";
    $limitText = "";
    $sl = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

    if ($num_list > 0) {
      foreach ($sql_data as $data) {

        $goodsType = "";
        if ($data['goodsType'] === "material") {
          $goodsType .= '<p class="goods-type type-goods">GOODS</p>';
        } elseif ($data['goodsType'] === "service") {
          $goodsType .= '<p class="goods-type type-service">SERVICE</p>';
        } elseif ($data['goodsType'] === "both") {
          $goodsType .= '<p class="goods-type type-goods">BOTH</p>';
        } elseif ($data['goodsType'] === "project") {
          $goodsType .= '<p class="goods-type type-project">PROJECT</p>';
        }

        if ($data['label'] == "open") {
          $approvalStatus = '<div class="status-bg status-open">Open</div>';
        } elseif ($data['label'] == "pending") {
          $approvalStatus = '<div class="status-bg status-pending">Pending</div>';
        } elseif ($data['label'] == "exceptional") {
          $approvalStatus = '<div class="status-bg status-exceptional">Exceptional</div>';
        } elseif ($data['label'] == "closed") {
          $approvalStatus = '<div class="status-bg status-closed">Closed</div>';
        }



        $dynamic_data[] = [
          "sl_no" => $sl,
          "party_order_id" => $data['id'],
          "so.order_code" => $data['order_code'],
          "cust.customer_code" => $data['customer_code'],
          "cust.trade_name" => $data['customer_name'],
          "so.order_type" => $data['order_type'],
          "so.created_at" => formatDateORDateTime($data['created_at']),
          "so.created_by" => $data['created_by'],
          "so.status" => $data['status']
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
        "formObj"=>$formObj
        // "csvContent" => $csvContent,
        // "csvContentBypagination" => $csvContentBypagination,
        // "sql" => $sql_list
      ];
    } else {
      $res = [
        "status" => false,
        "msg" => "Error!",
        "sql" => $sql_list,
        "formObj"=>$formObj
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

      if ($slag === 'so.created_at' || $slag === 'created_at' || $slag === 'delivery_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "totalAmount") {
        $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
      }
       else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }


    $sts = " AND `status` !='deleted'";
    $sql_list = "SELECT so.*, cust.trade_name AS customer_name , cust.customer_code as customer_code FROM `" . ERP_PARTY_ORDER . "` AS so LEFT JOIN erp_customer AS cust ON so.customer_id = cust.customer_id WHERE 1 " . $cond . "  AND so.company_id='" . $company_id . "'  AND so.branch_id='" . $branch_id . "'  AND so.location_id='" . $location_id . "' " . $sts . "  ORDER BY so.id desc";

    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list = $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
      foreach ($sql_data_all as $data) {

       


        $dynamic_data_all[] = [
          "sl_no" => $sl,
          "party_order_id" => $data['id'],
          "so.order_code" => $data['order_code'],
          "cust.customer_code" => $data['customer_code'],
          "cust.trade_name" => $data['customer_name'],
          "so.order_type" => $data['order_type'],
          "so.created_at" => formatDateORDateTime($data['created_at']),
          "so.created_by" => $data['created_by'],
          "so.status" => $data['status']
        ];
        $sl++;
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

}

