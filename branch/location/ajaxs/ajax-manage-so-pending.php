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

// print_r($_POST);
// $currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);

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
    // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


    $implodeFrom = implode('', array_map(function ($slag, $data) {
      $conds = "";

      if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      }
      //  elseif (strcasecmp($data['value'], 'Goods') === 0) {
      //   $data['value'] = 'material';
      //   $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      // }
       else if ($slag === "totalAmount") {
        $conds .= $slag . " " . $data['operatorName'] . "" . floatval($data['value']) . "";
      } else if ($slag === 'so.created_by' || $slag === 'created_by') {


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
      }else if($slag=='goods'){
        if(strtoupper($data['value'])=='GOODS'){
          $data['value']='material';
          $conds .= "goodsType " . $data['operatorName'] . " '%". $data['value'] ."%'";
        }else{
          $data['value']=strtolower(str_replace(' ', '', $data['value']));
          $conds .= "goodsType " . $data['operatorName'] . " '%". $data['value'] ."%'";
        }
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%". $data['value'] ."%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND `status` !='deleted'";

    $sql_list = "SELECT so.*,cust.customer_code,cust.trade_name AS customer_name,stat.label  FROM `" . ERP_BRANCH_SALES_ORDER . "` AS so LEFT JOIN erp_customer AS cust ON so.customer_id = cust.customer_id 
               LEFT JOIN `erp_status_master` AS stat  ON
                 so.approvalStatus = stat.status_id
               WHERE 1 " . $cond . "  AND approvalStatus=14 AND so.company_id='" . $company_id . "'  AND so.branch_id='" . $branch_id . "'   AND so.location_id='" . $location_id . "' " . $sts . "  
                  ORDER BY so.so_id DESC";

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
        if ($data['goodsType'] === "material") {
          $goodsType .= '<p class="goods-type type-goods">GOODS</p>';
          $goods='GOODS';
        } elseif ($data['goodsType'] === "service") {
          $goodsType .= '<p class="goods-type type-service">SERVICE</p>';
          $goods='SERVICE';
        } elseif ($data['goodsType'] === "both") {
          $goodsType .= '<p class="goods-type type-goods">BOTH</p>';
          $goods='BOTH';
          
        } elseif ($data['goodsType'] === "project") {
          $goodsType .= '<p class="goods-type type-project">PROJECT</p>';
          $goods='PROJECT';
        }

        if ($data['label'] == "open") {
          $approvalStatus = '<div class="status-bg status-open">Open</div>';
        } elseif ($data['label'] == "pending") {
          $approvalStatus = '<div class="status-bg status-pending">Pending</div>';
        } elseif ($data['label'] == "exceptional") {
          $approvalStatus = '<div class="status-bg status-exceptional">Exceptional</div>';
        } elseif ($data['label'] == "closed") {
          $approvalStatus = '<div class="status-bg status-closed">Closed</div>';
        } elseif ($data['label'] == "rejected") {
          $approvalStatus = '<div class="status-bg status-closed">Rejected</div>';
        }

        $dynamic_data[] = [
         "sl_no" => $sl,
          "so_number" => $data['so_number'],
          "so_id" => $data['so_id'],
          "customer_po_no" => $data['customer_po_no'],
          "so_date" => $data['so_date'],
          "created_at" => explode(" ", $data['created_at'])[0],
          "delivery_date" => $data['delivery_date'],
          "trade_name" =>  $data['customer_name'],
          "goodType" => $goodsType,
          "goods"=>$goods,
          "approvalStatus" => $approvalStatus,
          "soStatus" => $data['approvalStatus'],
          "totalAmount" => decimalValuePreview($data['totalAmount']),
          "customer_code" => $data['customer_code'],
          "storage_location_code" => $data['storage_location_code'],
          "created_by" => getCreatedByUser($data['created_by']),
          "label"=>$data['label'] 
        ];
        $sl++;
      }
      $output .= "</table>";
      $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
      $queryset = queryGet($sqlRowCount);
      $totalRows = $queryset['data']['row_count'];
      $total_page = ceil($totalRows / $limit_per_Page);

      $output .= pagiNation($page_no, $total_page);
      // $csvContent = exportToExcelAll($sql_list, json_encode($columnMapping));
      // $csvContentBypagination = exportToExcelByPagin($sql_Mainqry, json_encode($columnMapping));


      $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';

      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        // "csvContent" => $csvContent,
        // "csvContentBypagination" => $csvContentBypagination
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
  $fromDate=$_POST['fromDate'];
  $toDate=$_POST['toDate'];
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
    }
    //  elseif (strcasecmp($data['value'], 'Goods') === 0) {
    //   $data['value'] = 'material';
    //   $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
    // }
     else if ($slag === "totalAmount") {
      $conds .= $slag . " " . $data['operatorName'] . "" . floatval($data['value']) . "";
    } else if ($slag === 'so.created_by' || $slag === 'created_by') {

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
    }else if($slag=='goods'){
      if(strtoupper($data['value'])=='GOODS'){
        $data['value']='material';
        $conds .= "goodsType " . $data['operatorName'] . " '%". $data['value'] ."%'";
      }else{
        $data['value']=strtolower(str_replace(' ', '', $data['value']));
        $conds .= "goodsType " . $data['operatorName'] . " '%". $data['value'] ."%'";
      }
    } else {
      $conds .= $slag . " " . $data['operatorName'] . " '%". $data['value'] ."%'";
    }

    return !empty($data['value']) ? " AND " . $conds : "";
  }, array_keys($formObj), $formObj));


  if (!empty($implodeFrom)) {
    $cond .= $implodeFrom;
  }

  $sts = " AND `status` !='deleted'";
  $sql_list = "SELECT so.*,cust.customer_code,cust.trade_name AS customer_name,stat.label  FROM `" . ERP_BRANCH_SALES_ORDER . "` AS so LEFT JOIN erp_customer AS cust ON so.customer_id = cust.customer_id 
  LEFT JOIN `erp_status_master` AS stat  ON
    so.approvalStatus = stat.status_id
  WHERE 1 " . $cond . "  AND approvalStatus=14 AND so.company_id='" . $company_id . "'  AND so.branch_id='" . $branch_id . "'   AND so.location_id='" . $location_id . "' " . $sts . "  
     ORDER BY so.so_id DESC";
   $dynamic_data_all = [];
   $sqlMainQryObjall = queryGet($sql_list, true);
   $sql_data_all = $sqlMainQryObjall['data'];
   $num_list =  $sqlMainQryObjall['numRows'];
   if ($num_list > 0) {
   foreach ($sql_data_all as $data) {

    $goodsType = "";
        if ($data['goodsType'] === "material") {
          $goodsType .= '<p class="goods-type type-goods">GOODS</p>';
          $goods='GOODS';
        } elseif ($data['goodsType'] === "service") {
          $goodsType .= '<p class="goods-type type-service">SERVICE</p>';
          $goods='SERVICE';
        } elseif ($data['goodsType'] === "both") {
          $goodsType .= '<p class="goods-type type-goods">BOTH</p>';
          $goods='BOTH';
          
        } elseif ($data['goodsType'] === "project") {
          $goodsType .= '<p class="goods-type type-project">PROJECT</p>';
          $goods='PROJECT';
        }

        if ($data['label'] == "open") {
          $approvalStatus = '<div class="status-bg status-open">Open</div>';
          $approve='Open';
        } elseif ($data['label'] == "pending") {
          $approvalStatus = '<div class="status-bg status-pending">Pending</div>';
          $approve='Pending';
        } elseif ($data['label'] == "exceptional") {
          $approvalStatus = '<div class="status-bg status-exceptional">Exceptional</div>';
          $approve='Exceptional';
        } elseif ($data['label'] == "closed") {
          $approvalStatus = '<div class="status-bg status-closed">Closed</div>';
          $approve='Closed';
        } elseif ($data['label'] == "rejected") {
          $approvalStatus = '<div class="status-bg status-closed">Rejected</div>';
          $approve='Rejected';
        }
        
    $dynamic_data_all[]= [
      "sl_no" => $sl,
      "so_number" => $data['so_number'],
      "so_id" => $data['so_id'],
      "customer_po_no" => $data['customer_po_no'],
      "so_date" => $data['so_date'],
      "created_at" => explode(" ", $data['created_at'])[0],
      "delivery_date" => $data['delivery_date'],
      "trade_name" =>  $data['customer_name'],
      "goodType" => $goodsType,
      "goods"=>$goods,
      "approvalStatus" => $approvalStatus,
      "soStatus" => $data['approvalStatus'],
      "totalAmount" => decimalValuePreview($data['totalAmount']),
      "customer_code" => $data['customer_code'],
      "storage_location_code" => $data['storage_location_code'],
      "created_by" => getCreatedByUser($data['created_by']),
      "label"=>$data['label'] 
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