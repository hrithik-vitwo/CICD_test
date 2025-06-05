<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");
require_once("../../common/exportexcel.php");
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
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND `so`.status !='deleted'";

                                
             $sql_list = "SELECT itemSummary.itemId,itemSummary.location_id, itemDetails.itemCode, itemDetails.itemName, itemSummary.bomStatus AS bomCreateStatus, bomDetails.preparedDate, bomDetails.cogm, bomDetails.cogm_m, bomDetails.cogm_a, bomDetails.cogs, bomDetails.msp, bomDetails.bomProgressStatus, bomDetails.createdAt, bomDetails.createdBy, bomDetails.updatedAt, bomDetails.updatedBy, bomDetails.bomStatus, bomDetails.bomId  FROM `erp_inventory_stocks_summary` AS itemSummary LEFT JOIN `erp_inventory_items` AS itemDetails ON itemSummary.itemId = itemDetails.itemId LEFT JOIN `erp_bom` AS bomDetails ON itemSummary.itemId = bomDetails.itemId WHERE 1 ".$cond." AND itemSummary.location_id = ".$location_id ." AND itemDetails.goodsType IN (2,3) ORDER BY itemSummary.stockSummaryId DESC";                  

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

                if ($data['bomStatus'] == "active") {
                    $approvalStatus = '<div class="status-bg status-open">Active</div>';
                  } elseif ($data['bomStatus'] == "inactive") {
                    $approvalStatus = '<div class="status-bg status-closed">Inactive</div>';
                  } else{
                    $approvalStatus="";
                  }
                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "preparedDate" => $data['preparedDate'],
                    "cogm_m" => $data['cogm_m'],
                    "cogm_a" => $data['cogm_a'],
                    "cogm" => $data['cogm'],
                    "cogs" => $data['cogs'],
                    "msp" => $data['msp'],
                    "bomStatus" => $approvalStatus
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
                "limitTxt" => $limitText,
                "sqlMain" => $sql_data

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list,
                "sqlMain" => $sqlMainQryObj
            ];
        }

        echo json_encode($res);
    }
}
