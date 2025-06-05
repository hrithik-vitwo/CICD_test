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
            }  else if($slag === 'rack.created_by' || $slag==='created_by'){


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

        $sts = " AND rack.`status` !='deleted'";


        // $sql_list = "SELECT rack.* , erpStorage.storage_location_name FROM `erp_rack` as storage  LEFT JOIN erp_storage_location as erpStorage ON erpStorage.storage_location_id = storage.storage_location_id WHERE 1 AND  rack.`company_id`=$company_id AND rack.`branch_id`=$branch_id AND rack.`location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY rack.rack_id desc";
           $sql_list = "SELECT rack.*, erpStorage.storage_location_name FROM `erp_rack` AS rack LEFT JOIN erp_storage_location AS erpStorage ON erpStorage.storage_location_id = rack.storage_location_id WHERE 1 AND rack.`company_id` = $company_id AND rack.`branch_id` =$branch_id  AND rack.`location_id` = $location_id ". $cond . " ORDER BY rack.rack_id DESC";
        // $sql_list = "SELECT rack.* , storageLocation.storage_location_name FROM `erp_rack` as rack  LEFT JOIN erp_storage_location as storageLocation ON storageLocation.storage_location_id =rack.storage_location_id WHERE 1 AND  rack.`company_id`=$company_id AND rack.`branch_id`=$branch_id AND rack.`location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY rack.rack_id desc";
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
                    "rack.rack_id"=>$data['rack_id'],
                    "rack.rack_name" => $data['rack_name'],
                    "erpStorage.storage_location_name" => $data['storage_location_name'],
                    "rack.rack_description" => $data['rack_description'],
                    // "storage_control" => $data['storage_control'],
                    // "temp_control" => $data['temp_control'],
                    "rack.created_by" => getCreatedByUser($data['created_by']),
                    // "status" => $formObj
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
            }  else if($slag === 'rack.created_by' || $slag==='created_by'){


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

        $sts = " AND rack.`status` !='deleted'";


        // $sql_list = "SELECT rack.* , erpStorage.storage_location_name FROM `erp_rack` as storage  LEFT JOIN erp_storage_location as erpStorage ON erpStorage.storage_location_id = storage.storage_location_id WHERE 1 AND  rack.`company_id`=$company_id AND rack.`branch_id`=$branch_id AND rack.`location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY rack.rack_id desc";
           $sql_list = "SELECT rack.*, erpStorage.storage_location_name FROM `erp_rack` AS rack LEFT JOIN erp_storage_location AS erpStorage ON erpStorage.storage_location_id = rack.storage_location_id WHERE 1 AND rack.`company_id` = $company_id AND rack.`branch_id` =$branch_id  AND rack.`location_id` = $location_id ". $cond . " ORDER BY rack.rack_id DESC";
        // $sql_list = "SELECT rack.* , storageLocation.storage_location_name FROM `erp_rack` as rack  LEFT JOIN erp_storage_location as storageLocation ON storageLocation.storage_location_id =rack.storage_location_id WHERE 1 AND  rack.`company_id`=$company_id AND rack.`branch_id`=$branch_id AND rack.`location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY rack.rack_id desc";
        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            foreach ($sql_data_all as $data) {
               
                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "rack.rack_id"=>$data['rack_id'],
                    "rack.rack_name" => $data['rack_name'],
                    "erpStorage.storage_location_name" => $data['storage_location_name'],
                    "rack.rack_description" => $data['rack_description'],
                    // "storage_control" => $data['storage_control'],
                    // "temp_control" => $data['temp_control'],
                    "rack.created_by" => getCreatedByUser($data['created_by']),
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