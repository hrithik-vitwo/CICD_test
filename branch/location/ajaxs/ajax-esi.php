<?php
require_once("../../../app/v1/connection-branch-admin.php");
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

if ($_POST['act'] == 'esi') {

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

        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalValue;
            if ($slag === 'expectedDate') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            }
            elseif ($slag === "proceed_amount") {
                $cleanedValue = str_replace(',', '', $data['value']);
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
            
                $conds .= "(TRUNCATE(amount, $decimalValue) - TRUNCATE(due_amount, $decimalValue)) " . $data['operatorName'] . " " . $roundedValue;
            }
            elseif ($slag === "sum_gross" || $slag === "amount" || $slag === "due_amount") {
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
            }
             else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }


        $sql_list = "SELECT * FROM erp_payroll_processing   WHERE 1 " . $cond . " AND company_id = $company_id AND location_id = $location_id AND pay_type='esi' AND status !='deleted'";


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

                $action = "";
                if ($data['status'] == 'posted') {
                    if ($data['due_amount'] > 0) {
                        $action .= '<form action="" method="POST" class="btn btn-sm">
                                    <input type="hidden" name="accpost" value="">
                                    <input type="hidden" name="payroll_main_id" value="' . $data['payroll_main_id'] . '">
                                    <input type="hidden" name="documentNo" value="' . $data['payroll_code'] . '">
                                    <input type="hidden" name="payroll_month" value="' . $data['payroll_month'] . '">
                                    <input type="hidden" name="payroll_year" value="' . $data['payroll_year'] . '">
                                    <input type="hidden" name="sum_gross" value="' . $data['sum_gross'] . '">
                                    <button title="Post to accounting" type="submit" onclick="return confirm(\'Are you sure to Post?\')" class="p-0 btn btn-sm" style="cursor: pointer; border:none; background: none;">
                                        <i class="fa fa-book po-list-icon" aria-hidden="true"></i>
                                    </button>
                                </form>';
                    } else {
                        $action .= '<a title="Accounting Posted" class="btn btn-sm"><i class="fa fa-check po-list-icon" aria-hidden="true"></i></a>';
                    }
                } else {
                    $action .= 'Payroll pending';
                }

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "doc_no" => $data['doc_no'],
                    "process_id" => $data['process_id'],
                    "month_year" => $monthName . ' ' . $data['payroll_year'],
                    "sum_gross" => $data['sum_gross'],
                    "amount" => decimalValuePreview($data['amount']),
                    "proceed_amount" => $data['amount'] - $data['due_amount'],
                    "due_amount" => decimalValuePreview($data['due_amount']),
                    "status" => $data['status'],
                    "remaining_days" => "-",
                    "acconting_status" =>  $action,

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
            }
            elseif ($slag === "proceed_amount") {
                $cleanedValue = str_replace(',', '', $data['value']);
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
            
                $conds .= "(TRUNCATE(amount, $decimalValue) - TRUNCATE(due_amount, $decimalValue)) " . $data['operatorName'] . " " . $roundedValue;
            }
            elseif ($slag === "sum_gross" || $slag === "amount" || $slag === "due_amount") {
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
            }
             else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sql_list = "SELECT * FROM erp_payroll_processing   WHERE 1 " . $cond . " AND company_id = $company_id AND location_id = $location_id AND pay_type='tds' AND status !='deleted'";
        
        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];

        if ($num_list > 0) {
            foreach ($sql_data_all as $data) {
                $monthName = date("F", mktime(0, 0, 0, $data['payroll_month'], 10));

                $action = "";
                if ($data['status'] == 'posted') {
                    if ($data['due_amount'] > 0) {
                        $action .= '<form action="" method="POST" class="btn btn-sm">
                                    <input type="hidden" name="accpost" value="">
                                    <input type="hidden" name="payroll_main_id" value="' . $data['payroll_main_id'] . '">
                                    <input type="hidden" name="documentNo" value="' . $data['payroll_code'] . '">
                                    <input type="hidden" name="payroll_month" value="' . $data['payroll_month'] . '">
                                    <input type="hidden" name="payroll_year" value="' . $data['payroll_year'] . '">
                                    <input type="hidden" name="sum_gross" value="' . $data['sum_gross'] . '">
                                    <button title="Post to accounting" type="submit" onclick="return confirm(\'Are you sure to Post?\')" class="p-0 btn btn-sm" style="cursor: pointer; border:none; background: none;">
                                        <i class="fa fa-book po-list-icon" aria-hidden="true"></i>
                                    </button>
                                </form>';
                    } else {
                        $action .= '<a title="Accounting Posted" class="btn btn-sm"><i class="fa fa-check po-list-icon" aria-hidden="true"></i></a>';
                    }
                } else {
                    $action .= 'Payroll pending';
                }

                $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "doc_no" => $data['doc_no'],
                    "process_id" => $data['process_id'],
                    "month_year" => $monthName . ' ' . $data['payroll_year'],
                    "sum_gross" => $data['sum_gross'],
                    "amount" => decimalValuePreview($data['amount']),
                    "proceed_amount" => $data['amount'] - $data['due_amount'],
                    "due_amount" => decimalValuePreview($data['due_amount']),
                    "status" => $data['status'],
                    "remaining_days" => "-",
                    "acconting_status" =>  $action,

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
