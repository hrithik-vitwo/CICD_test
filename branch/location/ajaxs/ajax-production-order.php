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
$headerData = array('Content-Type: application/json');

// // print_r($_POST);
// $currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);
$dbObj=new Database();
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
      global $decimalQuantity;
  
      if ($slag === 'pOrder.expectedDate' || $slag === 'pOrder.created_at') {
          if ($data['operatorName'] === 'BETWEEN') {
              $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
          } else {
              $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
          }
      } elseif (strcasecmp($data['value'], 'Goods') === 0)  {
          $data['value'] = 'material';
          $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }else if ($slag === "pOrder.qty" || $slag == "pOrder.remainQty") {
        $cleanedValue = str_replace(',', '', $data['value']);

        $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
    
        $conds .= "TRUNCATE($slag, $decimalQuantity) {$data['operatorName']} $roundedValue";
      } else if($slag === 'pOrder.created_by' || $slag==='created_by'){

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
    } elseif ($slag === "pOrder.status") {
      $statusMap = [
          "Open" => "open",
          "Release" => "Released Order",
          "Closed" => "closed"
      ];
  
      // Convert UI label to DB status value
      $statusValue = $statusMap[$data['value']] ?? $data['value'];
  
      // Fetch status_id from the database
      $statusObj = queryGet("SELECT status_id FROM `erp_status_master` WHERE label='" . $statusValue . "'");
      
      if ($statusObj['numRows'] > 0) {
          $statusId = $statusObj['data']['status_id'];
          $conds .= $slag . " " . $data['operatorName'] . " '%" . $statusId . "%'";
      }
  }
   else {
          $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }
  
      return !empty($data['value']) ? " AND " . $conds : "";
  }, array_keys($formObj), $formObj));
  

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND pOrder.status !='deleted'";
    $sql_list = "SELECT pOrder.so_por_id, pOrder.porCode, pOrder.refNo, pOrder.mrp_code, pOrder.qty, pOrder.remainQty, pOrder.expectedDate, pOrder.created_at, pOrder.created_by, pOrder.status,pOrder.mrp_status,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.goodsType, items.itemOpenStocks, items.itemBlockStocks, goodTypes.goodTypeName, goodTypes.type AS goodTypeShortName FROM `erp_production_order` AS pOrder LEFT JOIN `erp_inventory_items` AS items ON pOrder.`itemId`=items.`itemId` LEFT JOIN `erp_inventory_mstr_good_types` AS goodTypes ON items.goodsType=goodTypes.goodTypeId WHERE 1 " . $cond . "  AND pOrder.company_id='" . $company_id . "'  AND pOrder.branch_id='" . $branch_id . "'   AND pOrder.location_id='" . $location_id . "' " . $sts . " ORDER BY pOrder.so_por_id DESC";

    // $sql_Mainqry = $sql_list . "  ORDER BY so.so_id DESC LIMIT " . $offset . "," . $limit_per_Page . ";";
    $sql_Mainqry = $sql_list." LIMIT ". $offset . "," . $limit_per_Page . ";";
    $sqlMainQryObj = $dbObj->queryGet($sql_Mainqry, true);

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
          "prodId"=>$data['so_por_id'],
          "pOrder.porCode"=>$data['porCode'],
          "goodTypes.goodTypeName"=>$data['goodTypeName'],
          "items.itemCode"=>$data['itemCode'],
          "items.itemName"=>$data['itemName'],
          "pOrder.refNo"=>$data['refNo'],
          "pOrder.mrp_code"=>$data['mrp_code'],
          "pOrder.qty"=>decimalQuantityPreview($data['qty']),
          "pOrder.remainQty"=>decimalQuantityPreview($data['remainQty']),
          "pOrder.expectedDate"=>formatDateWeb($data['expectedDate']),
          "pOrder.created_at"=>formatDateWeb($data['created_at']),
          "pOrder.created_by"=>getCreatedByUser($data['created_by']),
          "pOrder.status"=>fetchStatusMasterByCode($data['status'])['data']['label'],
          "pOrder.mrp_status"=>$data['mrp_status']
        ];
        $sl++;
      }
      $output .= "</table>";
      $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
      $queryset = $dbObj->queryGet($sqlRowCount);
      $totalRows = $queryset['data']['row_count'];
      $total_page = ceil($totalRows / $limit_per_Page);

      $output .= pagiNation($page_no, $total_page);

      $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';
      

      $res = [
        "status" => true,
        "msg" => "Success",
        "data" => $dynamic_data,
        "pagination" => $output,
        "limitTxt" => $limitText,
        "limit_per_Page" => $limit_per_Page,
        "sql" => $sql_list,
        "sqlRowCount" => $sqlRowCount
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
      global $decimalQuantity;

      if ($slag === 'pOrder.expectedDate' || $slag === 'pOrder.created_at') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } else if ($slag === "pOrder.qty" || $slag == "pOrder.remainQty") {
        $cleanedValue = str_replace(',', '', $data['value']);

        $roundedValue = number_format(round((float)$cleanedValue, $decimalQuantity), $decimalQuantity, '.', '');
    
        $conds .= "TRUNCATE($slag, $decimalQuantity) {$data['operatorName']} $roundedValue";
      } else if ($slag === 'pOrder.created_by' || $slag === 'created_by') {

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
      } elseif ($slag === "pOrder.status") {
        $statusMap = [
            "Open" => "open",
            "Release" => "Released Order",
            "Closed" => "closed"
        ];
    
        // Convert UI label to DB status value
        $statusValue = $statusMap[$data['value']] ?? $data['value'];
    
        // Fetch status_id from the database
        $statusObj = queryGet("SELECT status_id FROM `erp_status_master` WHERE label='" . $statusValue . "'");
        
        if ($statusObj['numRows'] > 0) {
            $statusId = $statusObj['data']['status_id'];
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $statusId . "%'";
        }
    } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }


    $sts = " AND pOrder.status !='deleted'";
    $sql_list = "SELECT pOrder.so_por_id, pOrder.porCode, pOrder.refNo, pOrder.mrp_code, pOrder.qty, pOrder.remainQty, pOrder.expectedDate, pOrder.created_at, pOrder.created_by, pOrder.status,pOrder.mrp_status,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.goodsType, items.itemOpenStocks, items.itemBlockStocks, goodTypes.goodTypeName, goodTypes.type AS goodTypeShortName FROM `erp_production_order` AS pOrder LEFT JOIN `erp_inventory_items` AS items ON pOrder.`itemId`=items.`itemId` LEFT JOIN `erp_inventory_mstr_good_types` AS goodTypes ON items.goodsType=goodTypes.goodTypeId WHERE 1 " . $cond . "  AND pOrder.company_id='" . $company_id . "'  AND pOrder.branch_id='" . $branch_id . "'   AND pOrder.location_id='" . $location_id . "' " . $sts . " ORDER BY pOrder.so_por_id DESC";
    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list = $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        $sl = 1;
        foreach ($sql_data_all as $data) {



        $dynamic_data_all[] = [
          "sl_no" => $sl,
          "pOrder.porCode" => $data['porCode'],
          "goodTypes.goodTypeName" => $data['goodTypeName'],
          "items.itemCode" => $data['itemCode'],
          "items.itemName" => $data['itemName'],
          "pOrder.refNo" => $data['refNo'],
          "pOrder.mrp_code" => $data['mrp_code'],
          "pOrder.qty" => decimalQuantityPreview($data['qty']),
          "pOrder.remainQty" => decimalQuantityPreview($data['remainQty']),
          "pOrder.expectedDate" => formatDateWeb($data['expectedDate']),
          "pOrder.created_at" => formatDateWeb($data['created_at']),
          "pOrder.created_by" => getCreatedByUser($data['created_by']),
          "pOrder.status"=>fetchStatusMasterByCode($data['status'])['data']['label'],
          "pOrder.mrp_status" => $data['mrp_status']
        ];
            $sl++;
        }

        $dynamic_data_all=json_encode($dynamic_data_all);
        $exportToExcelAll =exportToExcelAll($dynamic_data_all,$_POST['coloum'],$_POST['sql_data_checkbox']);
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