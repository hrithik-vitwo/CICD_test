<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");
// require_once("../../../common/exportexcel.php");
require_once("../../../../app/v1/functions/branch/func-discount-controller.php");
require_once("../../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');

$CustomerDiscountControllerObj = new CustomerDiscountGroupController();

if ($_POST['act'] == 'itemdiscountGroupTable') {
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
            if ($slag === 'updated_at' || $slag === 'created_at') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else if ($slag === 'created_by' || $slag === 'updated_by') {
                if ($data['operatorName'] === 'LIKE' || $data['operatorName'] === 'NOT LIKE') {
                    $opr = $data['operatorName'] === 'LIKE' ? 'IN' : 'NOT IN';
                    $resultList = getAdminUserIdByName($data['value']);
                    $resultList = (!empty($resultList)) ? $resultList : '0';
                    $conds .= $slag . " $opr  " . " (" . $resultList . ")";
                }
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return $data['value'] !== '' ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        // console($implodeFrom);
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }



        // $sts = " AND `so`.status !='deleted'";


        $sql_list = "SELECT * FROM `erp_item_discount_group` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' ORDER BY item_discount_group_id desc ";

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
                    "item_discount_group" => $data['item_discount_group'] ?? "-",
                    "created_at" => $data['created_at'],
                    "created_by" => getCreatedByUser($data['created_by']),
                    "updated_at" => $data['updated_at'] ?? "-",
                    "updated_by" => ($result = getCreatedByUser($data['updated_by'])) !== null && $result !== "NULL" ? $result : "-",
                    "item_discount_group_id" => $data['item_discount_group_id'] ?? "-"
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

        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            if ($slag === 'updated_at' || $slag === 'created_at') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else if ($slag === 'created_by' || $slag === 'updated_by') {
                if ($data['operatorName'] === 'LIKE' || $data['operatorName'] === 'NOT LIKE') {
                    $opr = $data['operatorName'] === 'LIKE' ? 'IN' : 'NOT IN';
                    $resultList = getAdminUserIdByName($data['value']);
                    $conds .= $slag . " $opr  " . " (" . $resultList . ")";
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

        $sql_list = "SELECT * FROM `erp_item_discount_group` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' ORDER BY item_discount_group_id desc ";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            $sl = 1;
            foreach ($sql_data_all as $data) {

                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "item_discount_group" => $data['item_discount_group'] ?? "-",
                    "created_at" => $data['created_at'],
                    "created_by" => getCreatedByUser($data['created_by']),
                    "updated_at" => $data['updated_at'] ?? "-",
                    "updated_by" => ($result = getCreatedByUser($data['updated_by'])) !== null && $result !== "NULL" ? $result : "-",
                    "item_discount_group_id" => $data['item_discount_group_id'] ?? "-"
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

if ($_GET["act"] == "updateitemDisGrp") {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $itemdisgrpid = $_GET["itemdisgrpid"] ?? '';
        $itemdisgrpname = $_GET["itemdisgrpname"] ?? '';

        $postData = [
            "id" => $itemdisgrpid,
            "name" => $itemdisgrpname,
        ];

        $update = $CustomerDiscountControllerObj->edit_item_discount_group($postData, $company_id, $created_by);


        $res = [];
        if ($update['status'] == 'success') {
            $res = ["status" => "success", "message" => "Discount group updated successfully"];
        } else {
            $res = ["status" => "error", "message" => "Something went wrong"];
        }

        echo json_encode($res);
    }
}


if ($_POST["act"] == "additemDisGrp") {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $itemdisgrpname = $_POST["itemdisgrpname"] ?? '';

        $postData = [
            "name" => $itemdisgrpname,
        ];

        $insert = $CustomerDiscountControllerObj->create_item_discount_group($postData, $company_id, $created_by);

        $res = [];
        if ($insert['status'] == 'success') {
            $res = ["status" => "success", "message" => "Discount group created successfully"];
        } else {
            $res = ["status" => "error", "message" => "Something went wrong"];
        }

        echo json_encode($res);
    }
}