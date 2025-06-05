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
      if ($slag === 'so_date' || $slag === 'pOrder.created_at' || $slag === 'pOrder.expectedDate') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "pOrder.prodQty" || $slag == "pOrder.remainQty") {
        $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
      } else if ($slag === 'pOrder.created_by' || $slag === 'created_by') {
       if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
                    $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
                    $resultList = getAdminUserIdByName($data['value']);
                    // $new_slag = 'varient.' . $slag;
        
                    if (strpos($resultList, ',') !== false) {
                        $resultList = (!empty($resultList)) ? $resultList : '0';
                        $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
                        $conds .= $slag . " $opr (" . $resultList . ")";
                    } else {
                        $resultList = (!empty($resultList)) ? $resultList : '0';
                        $conds .= $slag . " $opr '%" . $resultList . "%'";
                    }
                }
      } else if ($slag === 'pOrder.status' || $slag === 'status') {

        $masterStatus = [
          "open" => 9,
          "close" => 10,
          "closed" => 10,
          "release" => 13,
        ];
        $statusKey = strtolower($data['value']);
        $stVal = array_key_exists($statusKey, $masterStatus) ? $masterStatus[$statusKey] : $data['value'];

        $conds .= $slag . " " . $data['operatorName'] . " '%" . $stVal . "%'";
      } else if ($slag == 'pOrder.prodQty' || $slag == 'pOrder.remainQty') {
        $cleanedValue = str_replace(',', '', $data['value']);
        $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $cleanedValue;
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = " AND pOrder.status !='deleted'";

    $sql_list = "SELECT pOrder.prod_id, pOrder.sub_prod_id, pOrder.prodCode, pOrder.expectedDate, pOrder.remainQty, pOrder.prodQty, pOrder.mrp_code, pOrder.mrp_status, pOrder.status, pOrder.created_at, pOrder.created_by, pOrder.subProdCode, table_master.table_name, wc.work_center_name, items.itemId, items.itemName, items.itemCode, items.itemDesc, items.goodsType, items.itemOpenStocks, items.itemBlockStocks, goodTypes.goodTypeName, goodTypes.type AS goodTypeShortName, items.baseUnitMeasure AS itemUom FROM `erp_production_order_sub` AS pOrder LEFT JOIN `erp_inventory_items` AS items ON pOrder.`itemId` = items.`itemId` LEFT JOIN `erp_inventory_mstr_good_types` AS goodTypes ON items.goodsType = goodTypes.goodTypeId LEFT JOIN `erp_table_master` AS table_master ON pOrder.table_id = table_master.table_id LEFT JOIN `erp_work_center` AS wc ON wc.work_center_id = pOrder.wc_id WHERE 1 " . $cond . " AND pOrder.`location_id` = $location_id AND pOrder.branch_id=$branch_id AND pOrder.company_id=$company_id ORDER BY sub_prod_id DESC";

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
        $masterStatus = [
          9 => "open",
          10 => "close",
          13 => "release",
        ];
        $statusKey = $data['status'];
        $stVal = array_key_exists($data['status'], $masterStatus) ? $masterStatus[$statusKey] : $data['status'];
        $mrpStatus = '';
        if ($data['mrp_status'] == 'Created') {
          $mrpStatus = 'yes';
        } else {
          $mrpStatus = 'no';
        }

        $dynamic_data[] = [
          "sl_no" => $sl,
          "subProdId" => $data['sub_prod_id'],
          "prodId" => $data['prod_id'],
          "pOrder.subProdCode" => $data['subProdCode'],
          "goodTypes.goodTypeName" => $data['goodTypeName'],
          "items.itemCode" => $data['itemCode'],
          "items.itemName" => $data['itemName'],
          "pOrder.prodCode" => $data['prodCode'],
          "pOrder.mrp_code" => $data['mrp_code'],
          "pOrder.prodQty" => $data['prodQty'],
          "pOrder.remainQty" => $data['remainQty'],
          "pOrder.expectedDate" => $data['expectedDate'],
          "wc.work_center_name" => $data['work_center_name'],
          "table_master.table_name" => $data['table_name'],
          "pOrder.created_at" => $data['created_at'],
          "mrpStatus" => $data['mrp_status'],
          "pOrder.mrp_status" => $mrpStatus,
          "status" => $data['status'],
          "pOrder.status" => $stVal,
          "pOrder.created_by" => getCreatedByUser($data['created_by'])

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
  $cond = "";
  $implodeFrom = implode('', array_map(function ($slag, $data) {
    $conds = "";
    global $decimalValue;
    if ($slag === 'so_date' || $slag === 'pOrder.created_at' || $slag === 'pOrder.expectedDate') {
      if ($data['operatorName'] === 'BETWEEN') {
        $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
      } else {
        $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
      }
    } elseif (strcasecmp($data['value'], 'Goods') === 0) {
      $data['value'] = 'material';
      $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
    } elseif ($slag === "pOrder.prodQty" || $slag == "pOrder.remainQty") {
      $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
    } else if ($slag === 'pOrder.created_by' || $slag === 'created_by') {
      $resultList = getAdminUserIdByName($data['value']);
      $conds .= $slag . " IN  " . " (" . $resultList . ")";
    } else if ($slag === 'pOrder.status' || $slag === 'status') {

      $masterStatus = [
        "open" => 9,
        "close" => 10,
        "closed" => 10,
        "release" => 13,
      ];
      $statusKey = strtolower($data['value']);
      $stVal = array_key_exists($statusKey, $masterStatus) ? $masterStatus[$statusKey] : $data['value'];

      $conds .= $slag . " " . $data['operatorName'] . " '%" . $stVal . "%'";
    } else if ($slag == 'pOrder.prodQty' || $slag == 'pOrder.remainQty') {
      $cleanedValue = str_replace(',', '', $data['value']);
      $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $cleanedValue;
    } else {
      $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
    }

    return !empty($data['value']) ? " AND " . $conds : "";
  }, array_keys($formObj), $formObj));


  if (!empty($implodeFrom)) {
    $cond .= $implodeFrom;
  }

  $sts = " AND pOrder.status !='deleted'";

  $sql_list = "SELECT pOrder.prod_id, pOrder.sub_prod_id, pOrder.prodCode, pOrder.expectedDate, pOrder.remainQty, pOrder.prodQty, pOrder.mrp_code, pOrder.mrp_status, pOrder.status, pOrder.created_at, pOrder.created_by, pOrder.subProdCode, table_master.table_name, wc.work_center_name, items.itemId, items.itemName, items.itemCode, items.itemDesc, items.goodsType, items.itemOpenStocks, items.itemBlockStocks, goodTypes.goodTypeName, goodTypes.type AS goodTypeShortName, items.baseUnitMeasure AS itemUom FROM `erp_production_order_sub` AS pOrder LEFT JOIN `erp_inventory_items` AS items ON pOrder.`itemId` = items.`itemId` LEFT JOIN `erp_inventory_mstr_good_types` AS goodTypes ON items.goodsType = goodTypes.goodTypeId LEFT JOIN `erp_table_master` AS table_master ON pOrder.table_id = table_master.table_id LEFT JOIN `erp_work_center` AS wc ON wc.work_center_id = pOrder.wc_id WHERE 1 " . $cond . " AND pOrder.`location_id` = $location_id AND pOrder.branch_id=$branch_id AND pOrder.company_id=$company_id ORDER BY sub_prod_id DESC";

  $dynamic_data_all = [];
  $sqlMainQryObjall = queryGet($sql_list, true);
  $sql_data_all = $sqlMainQryObjall['data'];
  $num_list =  $sqlMainQryObjall['numRows'];
  if ($num_list > 0) {
    foreach ($sql_data_all as $data) {
        $masterStatus = [
          9 => "open",
          10 => "close",
          13 => "release",
        ];
        $statusKey = $data['status'];
        $stVal = array_key_exists($data['status'], $masterStatus) ? $masterStatus[$statusKey] : $data['status'];
        $mrpStatus = '';
        if ($data['mrp_status'] == 'Created') {
          $mrpStatus = 'yes';
        } else {
          $mrpStatus = 'no';
        }

      $dynamic_data_all[] = [
        "sl_no" => $sl,
        "subProdId" => $data['sub_prod_id'],
        "prodId" => $data['prod_id'],
        "pOrder.subProdCode" => $data['subProdCode'],
        "goodTypes.goodTypeName" => $data['goodTypeName'],
        "items.itemCode" => $data['itemCode'],
        "items.itemName" => $data['itemName'],
        "pOrder.prodCode" => $data['prodCode'],
        "pOrder.mrp_code" => $data['mrp_code'],
        "pOrder.prodQty" => $data['prodQty'],
        "pOrder.remainQty" => $data['remainQty'],
        "pOrder.expectedDate" => $data['expectedDate'],
        "wc.work_center_name" => $data['work_center_name'],
        "table_master.table_name" => $data['table_name'],
        "pOrder.created_at" => $data['created_at'],
        "mrpStatus" => $data['mrp_status'],
        "pOrder.mrp_status" => $mrpStatus,
        "status" => $data['status'],
        "pOrder.status" => $stVal,
        "pOrder.created_by" => getCreatedByUser($data['created_by'])

      ];
      $sl++;
    }
    $dynamic_data_all = json_encode($dynamic_data_all);
        $exportToExcelAll = exportToExcelAll($dynamic_data_all, $_POST['coloum'], $_POST['sql_data_checkbox']);
        $res = [
            "status" => true,
            "msg" => "alldataSuccess",
            "all_data" => $dynamic_data_all,
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
