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

session_start();

$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'raclog') {
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
            // if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            }  else if($slag === 'layer.created_by' || $slag==='created_by'){


        if (in_array($data['operatorName'], ['LIKE', 'NOT LIKE'])) {
            $opr = ($data['operatorName'] === 'LIKE') ? 'LIKE' : 'NOT LIKE';
            $resultList = getAdminUserIdByName($data['value']);
            // $new_slag = 'varient.' . $slag;
  
            if (strpos($resultList, ',') !== false) {
                $opr = ($opr === 'LIKE') ? 'IN' : 'NOT IN';
                $resultList = (!empty($resultList)) ? $resultList : '0';
                $conds .= $slag . " $opr (" . $resultList . ")";
            } else {
                $resultList = (!empty($resultList)) ? $resultList : '0';
                $conds .= $slag . " $opr '%" . $resultList . "%'";
            }
        }

    }  else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND layer.`status` !='deleted'";


        $sql_list = "SELECT layer.*, erpRack.rack_name FROM `erp_layer` AS layer LEFT JOIN erp_rack AS erpRack ON erpRack.rack_id = layer.rack_id WHERE 1 AND layer.`company_id` = $company_id AND layer.`branch_id` =$branch_id  AND layer.`location_id` = $location_id ". $cond . " ORDER BY layer.layer_id DESC";

        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);

        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $layer =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        if ($num_list > 0) {
            foreach ($sql_data as $data) {
                $dynamic_data[] = [
                    "sl_no" => $layer,
                    "layer.layer_id"=>$data['layer_id'],
                    "layer.layer_name" => $data['layer_name'],
                    "layer.layer_desc" => $data['layer_desc'],
                    "erpRack.rack_name" => $data['rack_name'],
                    "layer.created_by" => getCreatedByUser($data['created_by']),
                    // "status" => $formObj
                ];
                $layer++;
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
                "sqlMain" => $queryset,
                // "csvContent"=>$csvContent,
                // "csvContentBypagination"=>$csvContentBypagination,
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
if ($_POST['act'] == 'alldata') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $formObj = $_POST['formDatas'];
        $cond = "";
        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
            // if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            }  else if($slag === 'layer.created_by' || $slag==='created_by'){


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

    }  else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND layer.`status` !='deleted'";


        $sql_list = "SELECT layer.*, erpRack.rack_name FROM `erp_layer` AS layer LEFT JOIN erp_rack AS erpRack ON erpRack.rack_id = layer.rack_id WHERE 1 AND layer.`company_id` = $company_id AND layer.`branch_id` =$branch_id  AND layer.`location_id` = $location_id ". $cond . " ORDER BY layer.layer_id DESC";
        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            foreach ($sql_data_all as $data) {
               
                $dynamic_data_all[] = [
                   "sl_no" => $layer,
                    "layer.layer_id"=>$data['layer_id'],
                    "layer.layer_name" => $data['layer_name'],
                    "layer.layer_desc" => $data['layer_desc'],
                    "erpRack.rack_name" => $data['rack_name'],
                    "layer.created_by" => getCreatedByUser($data['created_by']),
                    // "status" => $formObj
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