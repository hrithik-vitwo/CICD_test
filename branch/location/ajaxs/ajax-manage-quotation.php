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
// require_once("../../common/exportexcel.php");

$headerData = array('Content-Type: application/json');
session_start();
$_SESSION['columnMapping'] = $_POST['columnMapping'];
if (isset($_SESSION['columnMapping'])) {
    $columnMapping = $_SESSION['columnMapping'];
}

$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'soquotation') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

        $page_no = isset($_POST['pageNo']) ? (int) $_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $formObj = $_POST['formDatas'];
        $cond = "";

        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalValue;
            if ($slag === 'so.posting_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === 'so.totalAmount') {
                $cleanedValue = str_replace(',', '', $data['value']);
            
                    $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                    $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } else if ($slag === 'so.created_by') {
                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
            }else if($slag === 'stat.label' &&  $data['value']=='Expired'){
                $conds .= $slag . " " . $data['operatorName'] . " '%approved%' AND validityperiod< '".date('Y-m-d')."'";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }


            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND `status` !='deleted'";

        $sql_list = "SELECT so.*, cust.trade_name AS customer_name , stat.label FROM `" . ERP_BRANCH_QUOTATIONS . "` AS so LEFT JOIN erp_customer AS cust ON so.customer_id =cust.customer_id 
                                LEFT JOIN `erp_status_master` as stat ON so.approvalStatus=stat.status_id
                                 WHERE 1   " . $cond . "  AND so.company_id='" . $company_id . "'   AND so.branch_id='" . $branch_id . "'  AND so.location_id='" . $location_id . "' " . $sts . "   ORDER BY so.quotation_id  desc";

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
                $approval='';
                if ($data['approvalStatus'] == "14") {
                    $approval ='PENDING';
        
                } else if ($data['approvalStatus'] == "16") {
                    $approval = 'ACCEPTED';
                } else if ($data['approvalStatus'] == "17") {
                    $approval = 'REJECTED';
                } else if ($data['approvalStatus'] == "10") {
                    $approval = 'CLOSED';
                } else if ($data['approvalStatus'] == "11" && $currentDate > $data['validityperiod']) {
                    $approval = 'Expired';
                } else if ($data['approvalStatus'] == "11" && $currentDate <= $data['validityperiod']) {
                    $approval = 'APPROVED';
                } else if ($data['approvalStatus'] == "19") {
                    $approval = 'EXPIRED';
                }
                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "quotation_id" => $data['quotation_id'],
                    "so.quotation_no" => $data['quotation_no'],
                    "approvalStatus" => $data['approvalStatus'],
                    "validityperiod" => $data['validityperiod'],
                    "so.posting_date" => $data['posting_date'],
                    "cust.trade_name" => $data['customer_name'],
                    "so.totalAmount" => decimalValuePreview($data['totalAmount']),
                    "so.created_by" => getCreatedByUser($data['created_by']),
                    "stat.label" => $approval
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
                // "csvContent" => $csvContent,
                // "csvContentBypagination" => $csvContentBypagination,
                // "sqlMain" => $sqlMainQryObj

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sqlMain" => $sqlMainQryObj
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
            global $decimalValue;
            if ($slag === 'so.posting_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === 'so.totalAmount') {
                $cleanedValue = str_replace(',', '', $data['value']);
            
                    $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                    $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } else if ($slag === 'so.created_by') {
    

                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
            }else if($slag === 'stat.label' &&  $data['value']=='Expired'){
                $conds .= $slag . " " . $data['operatorName'] . " '%approved%' AND validityperiod< '".date('Y-m-d')."'";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }


            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND `status` !='deleted'";

        $sql_list = "SELECT so.*, cust.trade_name AS customer_name , stat.label FROM `" . ERP_BRANCH_QUOTATIONS . "` AS so LEFT JOIN erp_customer AS cust ON so.customer_id =cust.customer_id 
                                LEFT JOIN `erp_status_master` as stat ON so.approvalStatus=stat.status_id
                                 WHERE 1   " . $cond . "  AND so.company_id='" . $company_id . "'   AND so.branch_id='" . $branch_id . "'  AND so.location_id='" . $location_id . "' " . $sts . "   ORDER BY so.quotation_id  desc";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            foreach ($sql_data_all as $data) {

                $approval='';
                if ($data['approvalStatus'] == "14") {
                    $approval ='PENDING';
        
                } else if ($data['approvalStatus'] == "16") {
                    $approval = 'ACCEPTED';
                } else if ($data['approvalStatus'] == "17") {
                    $approval = 'REJECTED';
                } else if ($data['approvalStatus'] == "10") {
                    $approval = 'CLOSED';
                } else if ($data['approvalStatus'] == "11" && $currentDate > $data['validityperiod']) {
                    $approval = 'Expired';
                } else if ($data['approvalStatus'] == "11" && $currentDate <= $data['validityperiod']) {
                    $approval = 'APPROVED';
                } else if ($data['approvalStatus'] == "19") {
                    $approval = 'EXPIRED';
                }

                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "quotation_id" => $data['quotation_id'],
                    "so.quotation_no" => $data['quotation_no'],
                    "approvalStatus" => $data['approvalStatus'],
                    "validityperiod" => $data['validityperiod'],
                    "so.posting_date" => $data['posting_date'],
                    "cust.trade_name" => $data['customer_name'],
                    "so.totalAmount" => decimalValuePreview($data['totalAmount']),
                    "so.created_by" => getCreatedByUser($data['created_by']),
                    "stat.label" => $approval
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
}
