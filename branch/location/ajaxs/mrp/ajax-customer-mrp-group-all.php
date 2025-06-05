<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");
// require_once("../../../common/exportexcel.php");
require_once("../../../common/exportexcel-new.php");
require_once("../../../../app/v1/functions/branch/func-mrp-controller.php");
$headerData = array('Content-Type: application/json');

$mrpControllerObj = new MrpController();

if ($_POST['act'] == 'mrpGroupTable') {
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
            $conds = "";
            
            // Handle date fields correctly
            if (in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto'])) {
                // $new_slag = 'varient.' . $slag;
                
                if ($data['operatorName'] === 'BETWEEN' && is_array($data['value'])) {
                    $conds .= " DATE($slag) BETWEEN '" . $data['value']['fromDate'] . "' AND '" . $data['value']['toDate'] . "' ";
                } else {
                    $conds .= " DATE($slag) " . $data['operatorName'] . " '" . $data['value'] . "' ";
                }
            }
            
            // Handle 'created_by' and 'updated_by' conditions
            else if ($slag === 'created_by' || $slag === 'updated_by') {
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
            }
        
            // General fallback condition, EXCLUDING date fields
            else if (!in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto'])) {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }
        
           return $data['value'] !== '' ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));
        
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        



        // $sts = " AND `so`.status !='deleted'";


        $sql_list = "SELECT * FROM `erp_customer_mrp_group` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' AND location_id = '" . $location_id . "' ORDER BY customer_mrp_group_id desc ";

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
                $dynamic_data[] = [
                   "sl_no" => $sl,
                    "customer_mrp_group" => $data['customer_mrp_group'] ?? "-",
                    "created_at" => formatDateWeb($data['created_at']),
                    "created_by" => getCreatedByUser($data['created_by']),
                    "updated_at" => formatDateWeb($data['updated_at']) ?? "-",
                    "updated_by" => getCreatedByUser($data['updated_by']) ?? "-",
                    "customer_mrp_group_id" => $data['customer_mrp_group_id'] ?? "-"
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
                "sqlMain" => $sql_data,
                "the_query" => $sqlMainQryObj['sql']

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
            $conds = "";
            
            // Handle date fields correctly
            if (in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto'])) {
                // $new_slag = 'varient.' . $slag;
                
                if ($data['operatorName'] === 'BETWEEN' && is_array($data['value'])) {
                    $conds .= " DATE($slag) BETWEEN '" . $data['value']['fromDate'] . "' AND '" . $data['value']['toDate'] . "' ";
                } else {
                    $conds .= " DATE($slag) " . $data['operatorName'] . " '" . $data['value'] . "' ";
                }
            }
            
            // Handle 'created_by' and 'updated_by' conditions
            else if ($slag === 'created_by' || $slag === 'updated_by') {
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
            }
        
            // General fallback condition, EXCLUDING date fields
            else if (!in_array($slag, ['updated_at', 'created_at', 'valid_from', 'valid_upto'])) {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }
        
            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));
        
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        $sql_list = "SELECT * FROM `erp_customer_mrp_group` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' AND location_id = '" . $location_id . "' ORDER BY customer_mrp_group_id desc ";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            $sl = 1;
            foreach ($sql_data_all as $data) {

                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "customer_mrp_group" => $data['customer_mrp_group'] ?? "-",
                    "created_at" => formatDateWeb($data['created_at']),
                    "created_by" => getCreatedByUser($data['created_by']),
                    "updated_at" => formatDateWeb($data['updated_at']) ?? "-",
                    "updated_by" => getCreatedByUser($data['updated_by']) ?? "-"
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

if ($_GET["act"] == "updateMrp") {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $mrpGrpid = $_GET["mrpid"];
        $mrpGroupName = $_GET["mrpGroupName"] ?? '';

        $postData = [
            "mrpGroupName" => $mrpGroupName,
            "id" => $mrpGrpid
        ];

        $update = $mrpControllerObj->editMrpGroup($postData);

        $res = [];
        if ($update['status'] == 'success') {
            $res = ["status" => "success", "message" => "MRP Group updated successfully"];
        } else {
            $res = ["status" => "error", "message" => "Something went wrong", "sql" => $update['query']];
        }

        echo json_encode($res);
    }
}


if ($_POST["act"] == "addMrp") {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mrpGroupName = $_POST["mrpGroupName"] ?? '';

        $postData = [
            "mrpGroupName" => $mrpGroupName,
        ];

        $insert = $mrpControllerObj->addMrpGroup($postData);

        $res = [];
        if ($insert['status'] == 'success') {
            $res = ["status" => "success", "message" => "MRP Group added successfully"];
        } else {
            $res = ["status" => "error", "message" => "Something went wrong", "sql" => $insert['query']];
        }

        echo json_encode($res);
    }
}