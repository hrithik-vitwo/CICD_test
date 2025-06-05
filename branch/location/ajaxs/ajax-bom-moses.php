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

// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

include_once("../bom/controller/bom.controller.php");
$bomControllerObj = new BomController();
$bomListObj = $bomControllerObj->getBomList();

if ($_POST['act'] == 'bom') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

        $page_no = isset($_POST['pageNo']) ? (int) $_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $formObj = $_POST['formDatas'];
        $cond = "";
        // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


        $implodeFrom = implode('', array_map(function ($slag, $data) {
            global $decimalValue;
            $conds = "";
            if ($slag === 'preparedDate' || $slag === 'created_at' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } 
            elseif ($slag === "cogm_m" || $slag === "cogm_a" || $slag === "cogm" || $slag === "cogs" || $slag === "msp") {

                $cleanedValue = str_replace(',', '', $data['value']);
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $cleanedValue;
            }
            else if ($slag === 'bomStatus') {
                if ($data['operatorName'] === 'LIKE' || $data['operatorName'] === 'NOT LIKE') {
                    $opr = $data['operatorName'] === 'LIKE' ? 'IN' : 'NOT IN';
                    $conds .= "bomDetails." . $slag . " " . $opr . " (" . "'" . $data['value'] . "'" . ") ";
                }

            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        // console($implodeFrom);
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }



        $sts = " AND `so`.status !='deleted'";


        $sql_list = "SELECT itemSummary.itemId,itemSummary.location_id, itemDetails.itemCode, itemDetails.itemName, itemSummary.bomStatus AS bomCreateStatus, bomDetails.preparedDate, bomDetails.cogm, bomDetails.cogm_m, bomDetails.cogm_a, bomDetails.cogs, bomDetails.msp, bomDetails.bomProgressStatus, bomDetails.createdAt, bomDetails.createdBy, bomDetails.updatedAt, bomDetails.updatedBy, bomDetails.bomStatus, bomDetails.bomId  FROM `erp_inventory_stocks_summary` AS itemSummary LEFT JOIN `erp_inventory_items` AS itemDetails ON itemSummary.itemId = itemDetails.itemId LEFT JOIN `erp_bom` AS bomDetails ON itemSummary.itemId = bomDetails.itemId WHERE 1 " . $cond . " AND itemSummary.location_id = " . $location_id . " AND itemDetails.goodsType IN (2,3) ORDER BY itemSummary.stockSummaryId DESC";

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

                if ($data['bomStatus'] == "active") {
                    $approvalStatus = '<div class="status-bg status-open">Active</div>';
                } elseif ($data['bomStatus'] == "inactive") {
                    $approvalStatus = '<div class="status-bg status-closed">Inactive</div>';
                } else {
                    $approvalStatus = "";
                }
                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "itemCode" => $data['itemCode'] ?? "-",
                    "itemName" => $data['itemName'],
                    "preparedDate" => $data['preparedDate'],
                    "cogm_m" => $data['cogm_m'] ?? "-",
                    "cogm_a" => $data['cogm_a'] ?? "-",
                    "cogm" => $data['cogm'] ?? "-",
                    "cogs" => $data['cogs'] ?? "-",
                    "msp" => $data['msp'] ?? "-",
                    "bomStatus" => $approvalStatus,
                    "bomId" => $data['bomId'] ?? "-",
                    "itemId" => $data['itemId'] ?? "-",
                    "bomCreateStatus" => $data['bomCreateStatus'] ?? "-",
                    "bomId" => $data['bomId'] ?? "-"
                    // "bomProgressStatus" => $data['bomProgressStatus'] ?? "-"
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



            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list,
                "sqlMain" => $sqlMainQryObj,
                // "conds" => console($cond)
            ];
        }

        echo json_encode($res);
    }
}

if ($_POST['act'] == 'alldata') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $formObj = $_POST['formDatas'];
        $cond = "";
        // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


        $implodeFrom = implode('', array_map(function ($slag, $data) {
            global $decimalValue;
            $conds = "";
            if ($slag === 'preparedDate' || $slag === 'created_at' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } 
            elseif ($slag === "cogm_m" || $slag === "cogm_a" || $slag === "cogm" || $slag === "cogs" || $slag === "msp") {
                $cleanedValue = str_replace(',', '', $data['value']);
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $cleanedValue;
            }
            else if ($slag === 'bomStatus') {
                if ($data['operatorName'] === 'LIKE' || $data['operatorName'] === 'NOT LIKE') {
                    $opr = $data['operatorName'] === 'LIKE' ? 'IN' : 'NOT IN';
                    $conds .= "bomDetails." . $slag . " " . $opr . " (" . "'" . $data['value'] . "'" . ") ";
                }

            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        // console($implodeFrom);
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        $sql_list = "SELECT itemSummary.itemId,itemSummary.location_id, itemDetails.itemCode, itemDetails.itemName, itemSummary.bomStatus AS bomCreateStatus, bomDetails.preparedDate, bomDetails.cogm, bomDetails.cogm_m, bomDetails.cogm_a, bomDetails.cogs, bomDetails.msp, bomDetails.bomProgressStatus, bomDetails.createdAt, bomDetails.createdBy, bomDetails.updatedAt, bomDetails.updatedBy, bomDetails.bomStatus, bomDetails.bomId  FROM `erp_inventory_stocks_summary` AS itemSummary LEFT JOIN `erp_inventory_items` AS itemDetails ON itemSummary.itemId = itemDetails.itemId LEFT JOIN `erp_bom` AS bomDetails ON itemSummary.itemId = bomDetails.itemId WHERE 1 " . $cond . " AND itemSummary.location_id = " . $location_id . " AND itemDetails.goodsType IN (2,3) ORDER BY itemSummary.stockSummaryId DESC";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            $sl = 1;
            foreach ($sql_data_all as $data) {
                if ($data['bomStatus'] == "active") {
                    $approvalStatus = '<div class="status-bg status-open">Active</div>';
                } elseif ($data['bomStatus'] == "inactive") {
                    $approvalStatus = '<div class="status-bg status-closed">Inactive</div>';
                } else {
                    $approvalStatus = "";
                }
                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "itemCode" => $data['itemCode'] ?? "-",
                    "itemName" => $data['itemName'],
                    "preparedDate" => $data['preparedDate'],
                    "cogm_m" => decimalValuePreview($data['cogm_m']) ?? "-",
                    "cogm_a" => decimalValuePreview($data['cogm_a']) ?? "-",
                    "cogm" => decimalValuePreview($data['cogm']) ?? "-",
                    "cogs" => decimalValuePreview($data['cogs']) ?? "-",
                    "msp" => decimalValuePreview($data['msp']) ?? "-",
                    "bomStatus" => $data['bomStatus']
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
