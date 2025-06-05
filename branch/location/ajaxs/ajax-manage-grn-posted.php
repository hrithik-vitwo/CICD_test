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

// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'grnPosted') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

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
            global $decimalValue;
            $conds = "";
            if ($slag === 'vendorDocumentDate' || $slag==='postingDate') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else if ($slag === 'grnCreatedBy' || $slag === 'created_by') {
                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
            }elseif ($slag === "grnTotalAmount") {
                $cleanedValue = str_replace(',', '', $data['value']);

                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

      

        // $sql_list = "SELECT * FROM erp_grn WHERE 1 ".$cond." AND companyId =".$company_id." AND branchId=".$branch_id." AND locationId=".$location_id." AND iv_status = 1 ". $sts . " ORDER BY grnId DESC";

        // $sql_list="SELECT grn.*, grnIv.grnIvId, grnIv.grnIvCode,grnIv.grnStatus as grnIvStatus FROM erp_grn as grn LEFT JOIN erp_grninvoice as grnIv ON grn.grnId=grnIv.grnId WHERE 1 ".$cond." AND grn.companyId = $company_id AND grn.branchId = $branch_id AND grn.locationId = $location_id AND grn.iv_status = 1 AND grn.grnStatus != 'deleted' ORDER BY grn.grnId DESC";

        $sql_list="SELECT grnIv.* FROM `erp_grninvoice` as grnIv WHERE 1 ".$cond." AND grnIv.companyId=$company_id AND grnIv.branchId=$branch_id AND grnIv.locationId=$location_id  ORDER BY `grnIvId` DESC";
                                             

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
                    "grnId"=>$data['grnId'],
                    "grnIvId"=>$data['grnIvId'],
                    "grnIvCode"=>$data['grnIvCode'],
                    "grnPostingJournalId"=>$data['grnPostingJournalId'],
                    "grnCode"=>$data['grnCode'],
                    "grnType"=>$data['grnType'],
                    "grnPoNumber"=>$data['grnPoNumber'],
                    "vendorName"=>$data['vendorName'],
                    "vendorCode"=>$data['vendorCode'],
                    "vendorDocumentNo"=>$data['vendorDocumentNo'],
                    "vendorDocumentDate"=>formatDateWeb($data['vendorDocumentDate']),
                    "postingDate"=>formatDateWeb($data['postingDate']),
                    "grnTotalAmount"=>decimalValuePreview($data['grnTotalAmount']),
                    "grnCreatedBy" => getCreatedByUser($data['grnCreatedBy']),
                    "grnApprovedStatus"=>$data['grnApprovedStatus'],
                    "grnStatus"=>$data['grnStatus'],
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
                // "sqlMain" => $sqlMainQryObj,
                // "sql" => $sql_list,

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

if ($_POST['act'] == 'alldata') {
    $formObj = $_POST['formDatas'];
    $cond = "";
    $implodeFrom = implode('', array_map(function ($slag, $data) {
        $conds = "";
        global $decimalValue;
        if ($slag === 'vendorDocumentDate' || $slag==='postingDate') {
            if ($data['operatorName'] === 'BETWEEN') {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
            } else {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
            }
        } else if ($slag === 'grnCreatedBy' || $slag === 'created_by') {

            $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
        }
        elseif ($slag === "grnTotalAmount") {
            $cleanedValue = str_replace(',', '', $data['value']);

            $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

            $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
        } 
        else {
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }

        return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));


    if (!empty($implodeFrom)) {
        $cond .= $implodeFrom;
    }

    $sts = " AND `grnStatus` !='deleted'";
    $sql_list="SELECT grnIv.* FROM `erp_grninvoice` as grnIv WHERE 1 ".$cond." AND grnIv.companyId=$company_id AND grnIv.branchId=$branch_id AND grnIv.locationId=$location_id  ORDER BY `grnIvId` DESC";
    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {



            $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "grnId"=>$data['grnId'],
                    "grnIvId"=>$data['grnIvId'],
                    "grnIvCode"=>$data['grnIvCode'],
                    "grnPostingJournalId"=>$data['grnPostingJournalId'],
                    "grnCode"=>$data['grnCode'],
                    "grnType"=>$data['grnType'],
                    "grnPoNumber"=>$data['grnPoNumber'],
                    "vendorName"=>$data['vendorName'],
                    "vendorCode"=>$data['vendorCode'],
                    "vendorDocumentNo"=>$data['vendorDocumentNo'],
                    "vendorDocumentDate"=>formatDateWeb($data['vendorDocumentDate']),
                    "postingDate"=>formatDateWeb($data['postingDate']),
                    "grnTotalAmount"=>decimalValuePreview($data['grnTotalAmount']),
                    "grnCreatedBy" => getCreatedByUser($data['grnCreatedBy']),
                    "grnApprovedStatus"=>$data['grnApprovedStatus'],
                    "grnStatus"=>$data['grnStatus'],
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

