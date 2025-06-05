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
$dbObj = new Database();
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['act'] == 'tdata') {


        $_SESSION['columnMapping'] = $_POST['columnMapping'];
        if (isset($_SESSION['columnMapping'])) {
            $columnMapping = $_SESSION['columnMapping'];
        }
        $type = $_POST['invoicetype'];
        $sts = " AND `status` !='deleted'";
        $cond = "";
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
            if ($slag === 'so_inv.invoice_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === "so_inv.all_total_amt") {
                $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        $sql_list='';
        if ($type == 'active') {
            $sql_list = "SELECT so_inv.invoice_no, so_inv.invoice_date, so_inv.all_total_amt, so_inv.so_invoice_id, so_inv.status, cust.trade_name, cust.customer_code FROM `erp_branch_sales_order_invoices` AS so_inv LEFT JOIN `erp_e_invoices` ON so_inv.so_invoice_id = `erp_e_invoices`.invoice_id LEFT JOIN `erp_customer` as cust ON so_inv.customer_id=cust.customer_id WHERE 1 " . $cond . " AND (so_inv.journal_id=0 OR so_inv.journal_id IS NULL) AND so_inv.company_id='" . $company_id . "' AND so_inv.branch_id='" . $branch_id . "' AND so_inv.location_id='" . $location_id . "' AND so_inv.invoice_date >='" . $compOpeningDate . "' AND so_inv.`status` IN ('active') ORDER BY so_inv.invoice_date DESC,so_inv.so_invoice_id DESC";
        } else {
            $sql_list = "SELECT so_inv.invoice_no, so_inv.invoice_date, so_inv.all_total_amt, so_inv.so_invoice_id, so_inv.status, cust.trade_name, cust.customer_code FROM `erp_branch_sales_order_invoices` AS so_inv LEFT JOIN `erp_e_invoices` ON so_inv.so_invoice_id = `erp_e_invoices`.invoice_id LEFT JOIN `erp_customer` as cust ON so_inv.customer_id=cust.customer_id WHERE 1 " . $cond . " AND (so_inv.journal_id !=0 AND so_inv.journal_id IS  NOT NULL) AND (so_inv.rev_inv_journal_id =0 OR so_inv.rev_inv_journal_id IS  NULL) AND so_inv.company_id='" . $company_id . "' AND so_inv.branch_id='" . $branch_id . "' AND so_inv.location_id='" . $location_id . "' AND so_inv.invoice_date >='" . $compOpeningDate . "' AND so_inv.`status` IN ('reverse')  AND so_inv.created_at > '2025-05-01' ORDER BY so_inv.invoice_date DESC,so_inv.so_invoice_id DESC";
        }

        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj =  $dbObj->queryGet($sql_Mainqry, true);
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $dynamic_data;

        if ($num_list > 0) {
            $sql_data = $sqlMainQryObj['data'];
            foreach ($sql_data as $data) {
                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "invNo" => $data['invoice_no'],
                    "invoice_date" => $data['invoice_date'],
                    "so_invoice_id" => base64_encode($data['so_invoice_id']),
                    "customerName" => $data['trade_name'],
                    "customercode" => $data['customer_code'],
                    "totalAmt" => decimalValuePreview($data['all_total_amt']),
                    "status" => $data['status']
                ];
                $sl++;
            }

            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);

            $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';

            $output .= pagiNation($page_no, $total_page);

            $csvContent = exportToExcelAll($sql_list, json_encode($columnMapping));
            $csvContentBypagination = exportToExcelByPagin($sql_Mainqry, json_encode($columnMapping));

            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "limit_per_Page" => $limit_per_Page,
                "sql"=>$sql_Mainqry,
                "type" =>$type,
                "csvContent" => $csvContent,
                "csvContentBypagination" => $csvContentBypagination

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list
            ];
        }

        echo json_encode($res);
    } elseif ($_POST['act'] == 'postInv') {


        $cond = "AND so_inv.so_invoice_id=" . $_POST['invId'] . "";

        $sql_Mainqry = "SELECT
                            so_inv.so_invoice_id,
                            so_inv.invoice_no,
                            so_inv.invoice_date,
                            so_inv.sgst,
                            so_inv.cgst,
                            so_inv.igst,
                            so_inv.all_total_amt,
                            so_inv.remarks,
                            cust.trade_name,
                            cust.customer_code,
                            cust.parentGlId
                        FROM
                            `erp_branch_sales_order_invoices` AS so_inv
                        LEFT JOIN `erp_e_invoices` ON so_inv.so_invoice_id = `erp_e_invoices`.invoice_id
                        LEFT JOIN `erp_customer` as cust 
                        ON so_inv.customer_id=cust.customer_id  WHERE 1 " . $cond . " AND (so_inv.journal_id=0 OR so_inv.journal_id IS NULL) AND  so_inv.company_id='" . $company_id . "'  AND so_inv.branch_id='" . $branch_id . "'  AND so_inv.location_id='" . $location_id . "'  AND so_inv.invoice_date >='" . $compOpeningDate . "' AND so_inv.`status` ='active' ORDER BY so_inv.invoice_date DESC,so_inv.invoice_no ASC";

        $sqlMainQryObj =  $dbObj->queryGet($sql_Mainqry);
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];

        echo json_encode($sql_data);
    }
}
