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

if ($_POST['act'] == 'strageloc') {
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
            elseif($slag === 'storage.storage_control' || $slag === 'storage.temp_control' )
            {
                $conds .= $slag . " " . $data['operatorName'] . " " . $data['value'];
            } 
            else if($slag === 'storage.created_by'){


                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";

            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND storage.`status` !='deleted'";


        // $sql_list = "SELECT sl.storage_location_name,sl.storage_location_code,warehouse.warehouse_name,sl.storage_control,sl.temp_control,sl.status,sl.created_by,sl.created_at,sl.updated_by,sl.updated_at,sl.storage_location_id  FROM erp_storage_location as sl ,erp_storage_warehouse as warehouse  WHERE 1 " . $cond . " AND sl.warehouse_id=warehouse.warehouse_id AND sl.`visibility`='show' AND sl.`company_id`=$company_id AND sl.`branch_id`=$branch_id AND sl.`location_id`=$location_id  ORDER BY sl.warehouse_id desc";

        $sql_list = "SELECT storage.* , erpWarehouse.warehouse_name FROM `erp_storage_location` as storage  LEFT JOIN erp_storage_warehouse as erpWarehouse ON erpWarehouse.warehouse_id = storage.warehouse_id WHERE 1 AND  storage.`company_id`=$company_id AND storage.`branch_id`=$branch_id AND storage.`location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY storage.storage_location_id desc";

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
            //     $formObj = '<form action="' . $_POST['pageName'] . '" method="POST">
            //     <input type="hidden" name="id" value="' . $data['storage_location_id'] . '">
            //     <input type="hidden" name="changeStatus" value="active_inactive">
            //     <button class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="' . $data['status'] . '"';
            //     if ($data['status'] == "draft") {
            //         $formObj .= ' style="cursor: inherit; border:none"';
            //     } else {
            //         $formObj .= ' onclick="return confirm(\'Are you sure to change status?\')"';
            //     }
            //     $formObj .= '>';

            //     if ($data['status'] == "active") {
            //         $formObj .= '<p class="status-bg status-open">' . ucfirst($data['status']) . '</p>';
            //     } elseif ($data['status'] == "inactive") {
            //         $formObj .= '<p class="status-bg status-closed">' . ucfirst($data['status']) . '</p>';
            //     } elseif ($data['status'] == "draft") {
            //         $formObj .= '<p class="status-warning text-xs">' . ucfirst($data['status']) . '</p>';
            //     }

            //     $formObj .= '</button>
            // </form>';

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "storage_location_id"=>$data['storage_location_id'],
                    "storage.storage_location_name" => $data['storage_location_name'],
                    "storage.storage_location_code" => $data['storage_location_code'],
                    "erpWarehouse.warehouse_name" => $data['warehouse_name'],
                    "storage.storage_control" => $data['storage_control'],
                    "storage.temp_control" => $data['temp_control'],
                    "storage.created_by" => getCreatedByUser($data['created_by']),
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
            // $csvContentBypagination = exportToExcelByPagin(  $sql_Mainqry, json_encode($columnMapping));

            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sqlMain" => $queryset,
                "csvContent"=>$csvContent,
                "csvContentBypagination"=>$csvContentBypagination,
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
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else if($slag === 'storage.created_by'){


                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";

            }
            elseif($slag === 'storage.storage_control' || $slag === 'storage.temp_control' )
            {
                $conds .= $slag . " " . $data['operatorName'] . " " . $data['value'];
            }
            else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND storage.`status` !='deleted'";


        // $sql_list = "SELECT sl.storage_location_name,sl.storage_location_code,warehouse.warehouse_name,sl.storage_control,sl.temp_control,sl.status,sl.created_by,sl.created_at,sl.updated_by,sl.updated_at,sl.storage_location_id  FROM erp_storage_location as sl ,erp_storage_warehouse as warehouse  WHERE 1 " . $cond . " AND sl.warehouse_id=warehouse.warehouse_id AND sl.`visibility`='show' AND sl.`company_id`=$company_id AND sl.`branch_id`=$branch_id AND sl.`location_id`=$location_id  ORDER BY sl.warehouse_id desc";

        $sql_list = "SELECT storage.* , erpWarehouse.warehouse_name FROM `erp_storage_location` as storage  LEFT JOIN erp_storage_warehouse as erpWarehouse ON erpWarehouse.warehouse_id = storage.warehouse_id WHERE 1 AND  storage.`company_id`=$company_id AND storage.`branch_id`=$branch_id AND storage.`location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY storage.storage_location_id desc";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];

        if ($num_list > 0) {
            foreach ($sql_data_all as $data) {

                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "storage_location_id"=>$data['storage_location_id'],
                    "storage.storage_location_name" => $data['storage_location_name'],
                    "storage.storage_location_code" => $data['storage_location_code'],
                    "erpWarehouse.warehouse_name" => $data['warehouse_name'],
                    "storage.storage_control" => $data['storage_control'],
                    "storage.temp_control" => $data['temp_control'],
                    "storage.created_by" => getCreatedByUser($data['created_by']),
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