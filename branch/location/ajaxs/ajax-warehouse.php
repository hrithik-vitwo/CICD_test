<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../app/v1/functions/branch/func-bom-controller.php");
require_once("pagination/common-pagination.php");
require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');
session_start();


$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'warehouse') {
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
        $sts = " AND `status` !='deleted'";

        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else if($slag === 'created_by'){

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

        $sql_list = "SELECT * FROM " . ERP_WAREHOUSE . " WHERE 1 AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY warehouse_id desc";
                  
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
                

                $formObj = '<form action="/branch/location/warehouse-p.php" method="POST">
                                <input type="hidden" name="id" value="' . $data['warehouse_id'] . '">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="' . $data['status'] . '"';
                if ($data['status'] == "draft") {
                    $formObj .= ' style="cursor: inherit; border:none"';
                } else {
                    $formObj .= ' onclick="return confirm(\'Are you sure to change status?\')"';
                }
                $formObj .= '>';

                if ($data['status'] == "active") {
                    $formObj .= '<p class="status-bg status-open">' . ucfirst($data['status']) . '</p>';
                } elseif ($data['status'] == "inactive" || $data['status'] == "guest") {
                    $formObj .= '<p class="status-bg status-delete">' . ucfirst($data['status']) . '</p>';
                } elseif ($data['status'] == "draft") {
                    $formObj .= '<p class="status-warning text-xs">' . ucfirst($data['status']) . '</p>';
                }

                $formObj .= '</button>
                            </form>';


                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "warehouse_id"=>$data['warehouse_id'],
                    "warehouse_name"=>$data['warehouse_name'],
                    "warehouse_code"=>$data['warehouse_code'],
                    "warehouse_address"=>$data['warehouse_address'],
                    "warehouse_description"=>$data['warehouse_description'],
                    "warehouse_lat"=>$data['warehouse_lat'],
                    "warehouse_lng"=>$data['warehouse_lng'],
                    "created_by" => getCreatedByUser($data['created_by']),
                    "status"=>$data['status']
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
            
            // $csvContent=exportToExcelAll($sql_list,json_encode($columnMapping));
            // $csvContentBypagination=exportToExcelByPagin($sql_Mainqry,json_encode($columnMapping));
            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sqlMain" => $sqlMainQryObj,
                // "csvContent"=>$csvContent,
                // "csvContentBypagination"=>$csvContentBypagination

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
            } else if($slag === 'created_by'){
                
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
        $sts = " AND `status` !='deleted'";

        $sql_list = "SELECT * FROM " . ERP_WAREHOUSE . " WHERE 1 AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY warehouse_id desc";
        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            foreach ($sql_data_all as $data) {
                

                $formObj = '<form action="/branch/location/warehouse-p.php" method="POST">
                                <input type="hidden" name="id" value="' . $data['warehouse_id'] . '">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="' . $data['status'] . '"';
                if ($data['status'] == "draft") {
                    $formObj .= ' style="cursor: inherit; border:none"';
                } else {
                    $formObj .= ' onclick="return confirm(\'Are you sure to change status?\')"';
                }
                $formObj .= '>';

                if ($data['status'] == "active") {
                    $formObj .= '<p class="status-bg status-open">' . ucfirst($data['status']) . '</p>';
                } elseif ($data['status'] == "inactive" || $data['status'] == "guest") {
                    $formObj .= '<p class="status-bg status-delete">' . ucfirst($data['status']) . '</p>';
                } elseif ($data['status'] == "draft") {
                    $formObj .= '<p class="status-warning text-xs">' . ucfirst($data['status']) . '</p>';
                }

                $formObj .= '</button>
                            </form>';


                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "warehouse_id"=>$data['warehouse_id'],
                    "warehouse_name"=>$data['warehouse_name'],
                    "warehouse_code"=>$data['warehouse_code'],
                    "warehouse_address"=>$data['warehouse_address'],
                    "warehouse_description"=>$data['warehouse_description'],
                    "warehouse_lat"=>$data['warehouse_lat'],
                    "warehouse_lng"=>$data['warehouse_lng'],
                    "created_by" => getCreatedByUser($data['created_by']),
                    "status"=>$data['status']
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