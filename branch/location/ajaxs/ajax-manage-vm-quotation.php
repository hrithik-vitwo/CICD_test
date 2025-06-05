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

// echo json_encode("Hii");

// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);
session_start();


if ($_POST['act'] == 'quotation') {
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
            if ($slag === 'pr.expectedDate') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else if ($slag === 'rfq.created_by' || $slag === 'created_by') {
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

        $sts = " AND rfq.status !='deleted'";


        $sql_list = "SELECT rfq.*,pr.expectedDate FROM `" . ERP_RFQ_LIST . "` as rfq LEFT JOIN `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr ON rfq.prId = pr.purchaseRequestId  WHERE 1 " . $cond . "  AND rfq.company_id='" . $company_id . "' AND rfq.branch_id = '$branch_id' AND rfq.location_id = '$location_id' 
        " . $sts . " ORDER BY rfq.rfqId desc ";


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
                $rfq_code = $data['rfqCode'];
                $rfq_id = $data['rfqId'];
                $vendor_sql = "SELECT * FROM `" . ERP_RFQ_VENDOR_LIST . "` WHERE `rfqCode`='" . $rfq_code . "'";
                $vendor_get = queryGet($vendor_sql, true);
                $getvendordata = $vendor_get['data'];
                $vendor_count = count($getvendordata);
                $vendor_response_sql = "SELECT * FROM `erp_vendor_response` WHERE `rfq_code`='" . $rfq_code . "'";
                $vendor_response_get = queryGet($vendor_response_sql, true);
                $getvendor_response = $vendor_response_get['data'];
                $vendor_response_count = count($getvendor_response);

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "rfqId" => $data['rfqId'],
                    "rfq.rfqCode" => $data['rfqCode'],
                    "vendoracpratio" => $vendor_response_count . "/" . $vendor_count,
                    "vendoracpratio1" => "\"".$vendor_response_count . "/" . $vendor_count."\"",
                    "pr.expectedDate" => $data['expectedDate'],
                    "rfq.created_by" => getCreatedByUser($data['created_by'])
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
                // "sqlMain" => $sqlMainQryObj,
                "total_page" => $queryset,
                // "csvContent" => $csvContent,
                // "csvContentBypagination" => $csvContentBypagination

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
    $formObj = $_POST['formDatas'];
    $cond = "";
    $implodeFrom = implode('', array_map(function ($slag, $data) {
        $conds = "";
        if ($slag === 'pr.expectedDate') {
            if ($data['operatorName'] === 'BETWEEN') {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
            } else {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
            }
        } else if ($slag === 'rfq.created_by' || $slag === 'created_by') {
            $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
        }  else {
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }

        return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
        $cond .= $implodeFrom;
    }

    $sts = " AND rfq.status !='deleted'";


    $sql_list = "SELECT rfq.*,pr.expectedDate FROM `" . ERP_RFQ_LIST . "` as rfq LEFT JOIN `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr ON rfq.prId = pr.purchaseRequestId  WHERE 1 " . $cond . "  AND rfq.company_id='" . $company_id . "' AND rfq.branch_id = '$branch_id' AND rfq.location_id = '$location_id' 
        " . $sts . " ORDER BY rfq.rfqId desc ";
    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {
 $rfq_code = $data['rfqCode'];
                $rfq_id = $data['rfqId'];
                $vendor_sql = "SELECT * FROM `" . ERP_RFQ_VENDOR_LIST . "` WHERE `rfqCode`='" . $rfq_code . "'";
                $vendor_get = queryGet($vendor_sql, true);
                $getvendordata = $vendor_get['data'];
                $vendor_count = count($getvendordata);
                $vendor_response_sql = "SELECT * FROM `erp_vendor_response` WHERE `rfq_code`='" . $rfq_code . "'";
                $vendor_response_get = queryGet($vendor_response_sql, true);
                $getvendor_response = $vendor_response_get['data'];
                $vendor_response_count = count($getvendor_response);

            $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "rfqId" => $data['rfqId'],
                    "rfq.rfqCode" => $data['rfqCode'],
                   "vendoracpratio1" => "\"".$vendor_response_count . "/" . $vendor_count."\"",
                    "pr.expectedDate" => $data['expectedDate'],
                    "rfq.created_by" => getCreatedByUser($data['created_by'])
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
