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
session_start();

// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'failedAcc') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $_SESSION['columnMapping'] = $_POST['columnMapping'];
        if (isset($_SESSION['columnMapping'])) {
            $columnMapping = $_SESSION['columnMapping'];
        }

        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

        $page_no = isset($_POST['pageNo']) ? (int)$_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);
        $type = $_POST['invoicetype'];
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $formObj = $_POST['formDatas'];
        $cond = "";
        // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            if ($slag === 'expectedDate') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else if ($slag === 'grnCreatedBy' || $slag === 'created_by') {

                $resultList = getAdminUserIdByName($data['value']);
                $conds .= $slag . " IN  " . " (" . $resultList . ")";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        //$sts = " AND `grnStatus` !='deleted'";  
        if ($type == 'active') {
            $sql_list = "SELECT * FROM `erp_grninvoice` WHERE 1 AND `grnStatus`  IN ('active') AND (ivPostingJournalId = 0 OR ivPostingJournalId IS NULL)   AND companyId =" . $company_id . " AND branchId=" . $branch_id . " AND locationId=" . $location_id . "" . $sts . " ORDER BY grnIvId DESC";
        }else{
            $sql_list = "SELECT * FROM `erp_grninvoice` WHERE 1 AND `grnStatus` IN ('reverse') AND (ivPostingJournalId != 0 AND ivPostingJournalId IS NOT NULL) AND (reverse_ivPostingJournalId = 0 OR reverse_ivPostingJournalId IS NULL)  AND  `postingDate`>='2025-05-08' AND companyId =" . $company_id . " AND branchId=" . $branch_id . " AND locationId=" . $location_id . "" . $sts . " ORDER BY grnIvId DESC";
        }


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
                    "grnId" => $data['grnId'],
                    "grnIvId" => $data['grnIvId'],
                    "grnIvCode" => $data['grnIvCode'],
                    "grnCode" => $data['grnCode'],
                    "grnPoNumber" => $data['grnPoNumber'],
                    "grnType" => $data['grnType'],
                    "vendorName" => $data['vendorName'],
                    "vendorCode" => $data['vendorCode'],
                    "vendorDocumentNo" => $data['vendorDocumentNo'],
                    "vendorDocumentDate" => $data['vendorDocumentDate'],
                    "postingDate" => $data['postingDate'],
                    "grnTotalAmount" => $data['grnTotalAmount'],
                    "created_by" => getCreatedByUser($data['grnCreatedBy']),
                    "grnApprovedStatus" => $data['grnApprovedStatus'],
                    "grnStatus" => $data['grnStatus']
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
            $csvContent = exportToExcelAll($sql_list, json_encode($columnMapping));
            $csvContentBypagination = exportToExcelByPagin($sql_Mainqry, json_encode($columnMapping));


            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sqlMain" => $sqlMainQryObj,
                "csvContent" => $csvContent,
                "csvContentBypagination" => $csvContentBypagination,
                "type" =>$type
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
