<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");

require_once("../../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');

session_start();

if ($_POST['act'] == 'cdata') {
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
        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";

            if ($slag === 'customer.customer_created_at' || $slag === 'customer.customer_updated_at' || $slag === 'posting_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === "totalPrice" || $slag === "total_qty" || $slag === "invoice.base_amt" || $slag === "invoice.cgst" || $slag === "invoice.sgst" || $slag === "invoice.igst" || $slag === "invoice.total_amt" || $slag === "credit_notes.cgst" || $slag === "credit_notes.sgst" || $slag === "credit_notes.igst" || $slag === "credit_notes.cn_amount" || $slag === "debit_notes.cgst" || $slag === "debit_notes.sgst" || $slag === "debit_notes.igst" || $slag === "debit_notes.dn_amount") {
                $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
            } 
            else if($slag === 'so.created_by' || $slag==='created_by'|| $slag==='customer.customer_created_by'){

                $resultList = getAdminUserIdByName($data['value']);                        
                $conds .= $slag . " IN  " . " (" . $resultList. ")";
        
            } 
            else if($slag==='updated_by' || $slag==='customer.customer_updated_by'){

                $resultList = getAdminUserIdByName($data['value']);                        
                $conds .= $slag . " IN  " . " (" . $resultList. ")";
        
            } 
            else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        // $sts = " AND `status` !='deleted'";
        $sql_list = "SELECT customer.customer_code, customer.trade_name AS customer_name, COALESCE(invoice.base_amt, 0) AS base_amount, COALESCE(invoice.igst, 0) AS igst, COALESCE(invoice.sgst, 0) AS sgst, COALESCE(invoice.cgst, 0) AS cgst, COALESCE(invoice.total_amt, 0) AS total_amount, COALESCE(credit_notes.cn_amount, 0) - (COALESCE(credit_notes.cgst, 0) + COALESCE(credit_notes.sgst, 0) + COALESCE(credit_notes.igst, 0)) AS cn_base_amount, COALESCE(credit_notes.cgst, 0) AS cn_cgst, COALESCE(credit_notes.sgst, 0) AS cn_sgst, COALESCE(credit_notes.igst, 0) AS cn_igst, COALESCE(credit_notes.cn_amount, 0) AS credit_note_amount, COALESCE(debit_notes.dn_amount, 0) - (COALESCE(debit_notes.cgst, 0) + COALESCE(debit_notes.sgst, 0) + COALESCE(debit_notes.igst, 0)) AS dn_base_amount, COALESCE(debit_notes.cgst, 0) AS dn_cgst, COALESCE(debit_notes.sgst, 0) AS dn_sgst, COALESCE(debit_notes.igst, 0) AS dn_igst, COALESCE(debit_notes.dn_amount, 0) AS debit_note_amount, COALESCE((invoice.total_amt - credit_notes.cn_amount + debit_notes.dn_amount), 0) AS net_sales_amount, customer.customer_created_at, customer.customer_created_by, customer.customer_updated_at, customer.customer_updated_by FROM erp_customer AS customer LEFT JOIN (SELECT inv.customer_id, SUM(inv.sub_total_amt - inv.totalDiscount) AS base_amt, SUM(inv.igst) AS igst, SUM(inv.sgst) AS sgst, SUM(inv.cgst) AS cgst, SUM(inv.all_total_amt) AS total_amt FROM erp_branch_sales_order_invoices AS inv WHERE inv.company_id=$company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id GROUP BY inv.customer_id) AS invoice ON invoice.customer_id = customer.customer_id LEFT JOIN (SELECT cn.party_id, SUM(cn.cgst) AS cgst, SUM(cn.sgst) AS sgst, SUM(cn.igst) AS igst, SUM(cn.total) AS cn_amount FROM erp_credit_note AS cn WHERE cn.company_id=$company_id AND cn.branch_id=$branch_id AND cn.location_id=$location_id AND cn.creditors_type = 'customer' GROUP BY cn.party_id) AS credit_notes ON credit_notes.party_id = customer.customer_id LEFT JOIN (SELECT dn.party_id, SUM(dn.cgst) AS cgst, SUM(dn.sgst) AS sgst, SUM(dn.igst) AS igst, SUM(dn.total) AS dn_amount FROM erp_debit_note AS dn WHERE dn.company_id=$company_id AND dn.branch_id=$branch_id AND dn.location_id=$location_id AND dn.debitor_type = 'customer' GROUP BY dn.party_id) AS debit_notes ON debit_notes.party_id = customer.customer_id WHERE 1 $cond AND customer.company_id=$company_id ORDER BY customer.customer_id ";

        // $sql_Mainqry = $sql_list . "  ORDER BY so.so_id DESC LIMIT " . $offset . "," . $limit_per_Page . ";";
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
                    "customer_code" => $data['customer_code'],
                    "customer_name" => $data['customer_name'],
                    "base_amount" => $data['base_amount'],
                    "cgst" => $data['cgst'],
                    "sgst" => $data['sgst'],
                    "igst" => $data['igst'],
                    "total_amount" => $data['total_amount'],
                    "cn_base_amount" => $data['cn_base_amount'],
                    "cn_cgst" => $data['cn_cgst'],
                    "cn_sgst" => $data['cn_sgst'],
                    "cn_igst" => $data['cn_igst'],
                    "credit_note_amount" => $data['credit_note_amount'],
                    "dn_base_amount" => $data['dn_base_amount'],
                    "dn_cgst" => $data['dn_cgst'],
                    "dn_sgst" => $data['dn_sgst'],
                    "dn_igst" => $data['dn_igst'],
                    "debit_note_amount" => $data['debit_note_amount'],
                    "net_sales_amount" => $data['net_sales_amount'],
                    "created_by" => getCreatedByUser($data['customer_created_by']),
                    "created_at" => $data['customer_created_at'],
                    "updated_by" => getCreatedByUser($data['customer_updated_by']),
                    "updated_at" => $data['customer_updated_at'],
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
                "limit_per_Page" => $limit_per_Page,
                "csvContent" => $csvContent,
                "csvContentBypagination" => $csvContentBypagination,
                "sql" => $sql_list


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
