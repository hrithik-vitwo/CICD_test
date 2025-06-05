<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");
require_once("../../../common/exportexcel-new.php");



$headerData = array('Content-Type: application/json');



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['act'] == 'TransactionList') {
        // Pagination parameters
        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? (int)$_POST['limit'] : 25;
        $page_no = isset($_POST['pageNo']) ? (int)$_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        // Get bankId and tnxType from GET parameters
        $bankId = isset($_POST["bank"]) && !empty($_POST["bank"]) ? ($_POST["bank"]) : 0;
        $tnxType = isset($_POST["typetnx"]) ? $_POST["typetnx"] : 'all';

        
        // Map tnxType to conditions
        $condition = "";
        if ($bankId > 0) {
            $condition .= " AND s.bank_id = '$bankId'";
        }
        if ($tnxType == 'unrecognised') {
            $condition .= ' AND s.reconciled_status="pending" AND s.remaining_amt >= 0';
        } elseif ($tnxType == 'recognised') {
            $condition .= ' AND s.reconciled_status="reconciled" AND s.reconciled_location_id=' . $location_id;
        }

        $_SESSION['tnx_condition_val'] = $condition; // Store tnxType in session for later use

        // Handle additional form filters (similar to formObj in original code)
        $formObj = isset($_POST['formDatas']) ? $_POST['formDatas'] : [];
        $cond = "";
        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalValue;
            // Handle date fields
            if ($slag === 's.tnx_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            }
            else if($slag === 's.deposit_amt' || $slag === 's.withdrawal_amt' || $slag === 's.remaining_amt') 
            {
                $cleanedValue = str_replace(',', '', $data['value']);
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } 
            // General text fields
            else {
                $conds .= " $slag " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }
            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $countt=queryGet('SELECT * FROM `erp_bank_statements` WHERE `company_id`=' . $company_id . ' AND `bank_id`=' . $bankId . ' AND `status`="active" AND `reconciled_status`="pending" AND `remaining_amt` > 0', true)['numRows'];

        // Define company_id (hardcoded for example, adjust as needed)
        // $company_id = 1;

        // Main SQL query
        // $sql_list = "SELECT s.*, b.bank_name, b.account_no, 
        //             agg.unrecognizedAmount, agg.recognizedAmount, agg.lastFeedDate
        //             FROM `erp_bank_statements` AS s
        //             LEFT JOIN `erp_acc_bank_cash_accounts` AS b ON s.bank_id = b.id
        //             CROSS JOIN (
        //             SELECT SUM(CASE WHEN reconciled_status = 'pending' THEN withdrawal_amt + deposit_amt ELSE 0 END) AS unrecognizedAmount,
        //                 SUM(CASE WHEN reconciled_status = 'reconciled' THEN withdrawal_amt + deposit_amt ELSE 0 END) AS recognizedAmount,
        //                 MAX(tnx_date) AS lastFeedDate
        //             FROM `erp_bank_statements` AS s2
        //             WHERE s2.company_id = " . $company_id . " " . $condition . "
        //         ) AS agg
        //         WHERE s.company_id = " . $company_id . " " . $condition . "
        //         ORDER BY s.id DESC";

        $sql_list = "SELECT s.*, b.bank_name , b.account_no FROM `erp_bank_statements` AS s LEFT JOIN `erp_acc_bank_cash_accounts` AS b ON s.bank_id = b.id WHERE 1 $cond $condition AND s.company_id = $company_id AND b.company_id=$company_id ORDER BY s.id DESC";

        $sql_Mainqry = $sql_list . " LIMIT $offset, $limit_per_Page";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);

        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        if ($num_list > 0) {
            foreach ($sql_data as $one) {
                $bank_ac_val = $one['bank_name'] . " (" . $one['account_no'] . ")";
                $dynamic_data[] = [
                    // "sl_no" => $sl,
                    "s.tnx_date" => formatDateWeb($one['tnx_date'] ?? ""),
                    "s.particular" => $one['particular'] ?? "",
                    "bank_ac_val" => $bank_ac_val,
                    // "b.account_no" =>  ?? "",
                    "s.deposit_amt" => $one['deposit_amt'] > 0 ? $one['deposit_amt'] : "",
                    "s.withdrawal_amt" => $one['withdrawal_amt'] > 0 ? $one['withdrawal_amt'] : "",
                    "s.remaining_amt" => $one['remaining_amt'] > 0 ? $one['remaining_amt'] : "",
                    "s.reconciled_status" => $one['reconciled_status'] ?? "",
                    "s.id" => $one['id'],
                    "bankObj" => $one
                ];
                $sl++;
            }

            $output .= "</table>";

            // Pagination output (simplified, adjust based on your pagiNation function)
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM ($sql_list) AS subquery";
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);
            // $output .= "<div class='pagination'>Pages: $total_page</div>";
            $output .= pagiNation($page_no, $total_page);
            $limitText .= "<a class='active' id='limitText'>Showing $startPageSL to $maxPagesl of $totalRows entries</a>";

            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "cond" => $cond,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sqlCount" => $queryset,
                "the_query" => $sqlMainQryObj['sql'],
                "formObj" => $formObj,
                "unrecon"=>$countt
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list,
                "cond" => $cond,
                "sqlMain" => $sqlMainQryObj
            ];
        }

        // Output JSON response
        echo json_encode($res);
    }


    if ($_POST['act'] == 'alldata') {
        $formObj = isset($_POST['formDatas']) ? $_POST['formDatas'] : [];
        $cond = "";

        $condition  = isset($_SESSION['tnx_condition_val']) ? $_SESSION['tnx_condition_val'] : '';
        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalValue;
            // Handle date fields
            if ($slag === 's.tnx_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            }
            else if($slag === 's.deposit_amt' || $slag === 's.withdrawal_amt' || $slag === 's.remaining_amt') 
            {
                $cleanedValue = str_replace(',', '', $data['value']);
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } 
            // General text fields
            else {
                $conds .= " $slag " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }
            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }


        $sql_list = "SELECT s.*, b.bank_name , b.account_no FROM `erp_bank_statements` AS s LEFT JOIN `erp_acc_bank_cash_accounts` AS b ON s.bank_id = b.id WHERE 1 $cond $condition AND s.company_id = $company_id AND b.company_id=$company_id ORDER BY s.id DESC";
        

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {

            foreach ($sql_data_all as $one) {
                $bank_ac_val = $one['bank_name'] . " (" . $one['account_no'] . ")";
                $dynamic_data_all[] = [
                    // "sl_no" => $sl,
                    "s.tnx_date" => formatDateWeb($one['tnx_date'] ?? ""),
                    "s.particular" => $one['particular'] ?? "",
                    "bank_ac_val" => $bank_ac_val,
                    // "b.account_no" =>  ?? "",
                    "s.deposit_amt" => $one['deposit_amt'] > 0 ? $one['deposit_amt'] : "",
                    "s.withdrawal_amt" => $one['withdrawal_amt'] > 0 ? $one['withdrawal_amt'] : "",
                    "s.remaining_amt" => $one['remaining_amt'] > 0 ? $one['remaining_amt'] : "",
                    "s.reconciled_status" => $one['reconciled_status'] ?? "",
                    "s.id" => $one['id'],
                    "bankObj" => $one
                ];
                $sl++;
            }
            $dynamic_data_all = json_encode($dynamic_data_all);
            $exportToExcelAll = exportToExcelAll($dynamic_data_all, $_POST['coloum'], $_POST['sql_data_checkbox']);
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
