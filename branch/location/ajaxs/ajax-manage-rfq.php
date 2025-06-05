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

// echo json_encode("Hii");

// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);


if ($_POST['act'] == 'rfq') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
       // console($_POST);

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
            if ($slag === 'pr.expectedDate' || $slag === 'rfq.closing_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else if ($slag === 'rfq.created_by') {
                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
            } else if ($slag === 'daysLeft') {
                $days = "-" . $data['value'] . "days";
                $closingDate = date('Y-m-d', strtotime($days));
                $conds .= "rfq.closing_date " . $data['operatorName'] . "'" . $closingDate . "'";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND rfq.status !='deleted'";

        //prCodndition 
        $prId = $_POST['prId'];
        $prCondition = "";
        if ($prId != 0) {
            $prCondition = " AND rfq.prId=$prId ";
        }


 $sql_list = "SELECT rfq.rfqId,rfq.rfqCode,pr.prCode,pr.refNo,pr.expectedDate,rfq.created_by,rfq.closing_date FROM `" . ERP_RFQ_LIST . "` as rfq LEFT JOIN `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr ON rfq.prId = pr.purchaseRequestId  WHERE 1 " . $prCondition . "  " . $cond . "  AND rfq.company_id='" . $company_id . "' AND rfq.branch_id = '$branch_id' AND rfq.location_id = '$location_id' " . $sts . " ORDER BY rfq.rfqId desc";


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
                $date1 = date_create($data['closing_date']);
                $date2 = date_create(date('Y-m-d'));
                $diff = date_diff($date1, $date2);


                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "rfqId" => $data['rfqId'],
                    "rfq.rfqCode" => $data['rfqCode'],
                    "rfq.prCode" => $data['prCode'],
                    "pr.refNo" => $data['refNo'],
                    "pr.expectedDate" => $data['expectedDate'],
                    "rfq.created_by" => getCreatedByUser($data['created_by']),
                    "rfq.closing_date" => $data['closing_date'],
                    "daysLeft" => $diff->format("%R%a days")
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
                // "sqlMain" => $sqlMainQryObj,
                "total_page" => $queryset,
                "formObj" => $formObj

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list,
                "formObj" => $formObj

            ];
        }

        echo json_encode($res);
    }
}
if ($_POST['act'] == 'alldata') {
    $formObj = $_POST['formDatas'];
    $cond = "";
    // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


    $implodeFrom = implode('', array_map(function ($slag, $data) {
        $conds = "";
        if ($slag === 'pr.expectedDate' || $slag === 'rfq.closing_date') {
            if ($data['operatorName'] === 'BETWEEN') {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
            } else {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
            }
        } else if ($slag === 'rfq.created_by') {
            $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
        } else if ($slag === 'daysLeft') {
            $days = "-" . $data['value'] . "days";
            $closingDate = date('Y-m-d', strtotime($days));
            $conds .= "rfq.closing_date " . $data['operatorName'] . "'" . $closingDate . "'";
        } else {
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }

        return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
        $cond .= $implodeFrom;
    }

    $sts = " AND rfq.status !='deleted'";

    //prCodndition 
    $prId = $_POST['prId'];
    $prCondition = "";
    if ($prId != 0) {
        $prCondition = " AND rfq.prId=$prId ";
    }


    $sql_list = "SELECT rfq.rfqId,rfq.rfqCode,pr.prCode,pr.refNo,pr.expectedDate,rfq.created_by,rfq.closing_date FROM `" . ERP_RFQ_LIST . "` as rfq LEFT JOIN `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr ON rfq.prId = pr.purchaseRequestId  WHERE 1 " . $prCondition . "  " . $cond . "  AND rfq.company_id='" . $company_id . "' AND rfq.branch_id = '$branch_id' AND rfq.location_id = '$location_id' " . $sts . " ORDER BY rfq.rfqId desc";
    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {

            $date1 = date_create($data['closing_date']);
            $date2 = date_create(date('Y-m-d'));
            $diff = date_diff($date1, $date2);
            $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "rfqId" => $data['rfqId'],
                    "rfq.rfqCode" => $data['rfqCode'],
                    "rfq.prCode" => $data['prCode'],
                    "pr.refNo" => $data['refNo'],
                    "pr.expectedDate" => $data['expectedDate'],
                    "rfq.created_by" => getCreatedByUser($data['created_by']),
                    "rfq.closing_date" => $data['closing_date'],
                    "daysLeft" => $diff->format("%R%a days")
            ];
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
