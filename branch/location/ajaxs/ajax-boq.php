<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("pagination/common-pagination.php");
require_once("../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');
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

      if ($slag === 'boqDetails.preparedDate' || $slag === 'created_at' || $slag === 'delivery_date') {
        if ($data['operatorName'] === 'BETWEEN') {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
        } else {
          $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
        }
      } elseif (strcasecmp($data['value'], 'Goods') === 0) {
        $data['value'] = 'material';
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      } elseif ($slag === "boqDetails.cogm" || $slag === "boqDetails.cosp_m" || $slag === "boqDetails.cosp_a" || $slag === "boqDetails.cosp_i" || $slag === "boqDetails.cogs" || $slag === "msp") {
        $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
      } else if ($slag === 'so.created_by' || $slag === 'created_by') {

        $resultList = getAdminUserIdByName($data['value']);
        $conds .= $slag . " IN  " . " (" . $resultList . ")";
      } else {
        $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
      }

      return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
      $cond .= $implodeFrom;
    }

    $sts = "AND boqDetails.boqStatus !='deleted'";

    $sql_list = "SELECT  boqDetails.boqId,itemSummary.itemId, itemDetails.itemCode, itemDetails.itemName, itemSummary.bomStatus AS boqCreateStatus, boqDetails.preparedDate, boqDetails.cogm, boqDetails.cogs, boqDetails.msp, boqDetails.boqProgressStatus, boqDetails.createdAt, boqDetails.createdBy, boqDetails.updatedAt, boqDetails.updatedBy, boqDetails.boqStatus FROM `erp_inventory_stocks_summary` AS itemSummary LEFT JOIN `erp_inventory_items` AS itemDetails ON itemSummary.itemId = itemDetails.itemId LEFT JOIN `erp_boq` AS boqDetails ON itemSummary.itemId = boqDetails.itemId AND itemSummary.location_id=boqDetails.locationId WHERE 1 " . $cond . " AND itemSummary.company_id=$company_id AND itemSummary.branch_id=$branch_id AND itemSummary.location_id=" . $location_id . " AND itemDetails.goodsType=5 " . $sts . " ORDER BY boqDetails.boqId DESC";

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
          "boqId" => $data['boqId'],
          "itemId" => $data['itemId'],
          "itemCode" => $data['itemCode'],
          "itemName" => $data['itemName'],
          "preparedDate" => $data['preparedDate'],
          "cogm" => $data['cogm'],
          "cosp_m" => $data['cosp_m'],
          "cosp_a" => $data['cosp_a'],
          "cosp_i" => $data['cosp_i'],
          "cogs" => $data['cogs'],
          "msp" => $data['msp'],
          "boqStatus" => $data['boqStatus']
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
