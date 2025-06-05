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

if ($_POST['act'] == 'payroll') {

    // echo json_encode("Hii");
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
            $conds = "";
            global $decimalValue;
            if ($slag === 'expectedDate') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === "sum_gross" || $slag === "sum_pf_employee" || $slag === "sum_pf_employeer" || $slag === "sum_esi_employee" || $slag === 'sum_esi_employeer' || $slag === 'sum_ptax' || $slag === "sum_pf_admin" || $slag === "sum_tds") {
                $cleanedValue = str_replace(',', '', $data['value']);

                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } 
            elseif($slag === 'month_year')
            {
                
                $timestamp = strtotime($data['value']);
                $month = date('n', $timestamp); // 1-12
                $year = date('Y', $timestamp);
                if($data['operatorName']=='LIKE'){
                    $conds .= " payroll_month = $month AND payroll_year = $year";
                }else{
                    $conds .= " payroll_month != $month OR payroll_year != $year";
                }

                
            }
            else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $mode = "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))";
        queryGet($mode);

        $sql_list = "SELECT * FROM `erp_payroll_main` WHERE 1 " . $cond . " AND `company_id`=$company_id AND `location_id`=$location_id AND `branch_id`=$branch_id ORDER BY `payroll_year`, `payroll_month`";


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
                $monthName = date("F", mktime(0, 0, 0, $data['payroll_month'], 10));
                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "payroll_main_id" => $data['payroll_main_id'],
                    "month_year" => $monthName . ' ' . $data['payroll_year'],
                    "sum_gross" => decimalValuePreview($data['sum_gross']),
                    "payroll_year" => $data['payroll_year'],
                    "payroll_code" => $data['payroll_code'],
                    "sum_pf_employee" => decimalValuePreview($data['sum_pf_employee']),
                    "sum_pf_employeer" => decimalValuePreview($data['sum_pf_employeer']),
                    "sum_pf_admin" => decimalValuePreview($data['sum_pf_admin']),
                    "sum_esi_employee" => decimalValuePreview($data['sum_esi_employee']),
                    "sum_esi_employeer" => decimalValuePreview($data['sum_esi_employeer']),
                    "sum_ptax" => decimalValuePreview($data['sum_ptax']),
                    "sum_tds" => decimalValuePreview($data['sum_tds']),
                    "payroll_month" => $data['payroll_month'],
                    "status" => $data['acconting_status']

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
                "sqlMain" => $sqlMainQryObj

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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $formObj = $_POST['formDatas'];
        $cond = "";
        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalValue;
            if ($slag === 'expectedDate') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === "sum_gross" || $slag === "sum_pf_employee" || $slag === "sum_pf_employeer" || $slag === "sum_esi_employee" || $slag === 'sum_esi_employeer' || $slag === 'sum_ptax' || $slag === "sum_pf_admin" || $slag === "sum_tds") {
                $cleanedValue = str_replace(',', '', $data['value']);

                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');

                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            }
            elseif($slag === 'month_year')
            {
                $timestamp = strtotime($data['value']);
                $month = date('n', $timestamp); // 1-12
                $year = date('Y', $timestamp);

                $conds .= " payroll_month = $month AND payroll_year = $year";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }


        $sql_list = "SELECT * FROM `erp_payroll_main` WHERE 1 " . $cond . " AND `company_id`=$company_id AND `location_id`=$location_id AND `branch_id`=$branch_id ORDER BY `payroll_year`, `payroll_month`";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];

        if ($num_list > 0) {
            foreach ($sql_data_all as $data) {
                $monthName = date("F", mktime(0, 0, 0, $data['payroll_month'], 10));
                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "payroll_main_id" => $data['payroll_main_id'],
                    "month_year" => $monthName . ' ' . $data['payroll_year'],
                    "sum_gross" => decimalValuePreview($data['sum_gross']),
                    "payroll_year" => $data['payroll_year'],
                    "payroll_code" => $data['payroll_code'],
                    "sum_pf_employee" => decimalValuePreview($data['sum_pf_employee']),
                    "sum_pf_employeer" => decimalValuePreview($data['sum_pf_employeer']),
                    "sum_pf_admin" => decimalValuePreview($data['sum_pf_admin']),
                    "sum_esi_employee" => decimalValuePreview($data['sum_esi_employee']),
                    "sum_esi_employeer" => decimalValuePreview($data['sum_esi_employeer']),
                    "sum_ptax" => decimalValuePreview($data['sum_ptax']),
                    "sum_tds" => decimalValuePreview($data['sum_tds']),
                    "payroll_month" => $data['payroll_month'],
                    "status" => $data['acconting_status']

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