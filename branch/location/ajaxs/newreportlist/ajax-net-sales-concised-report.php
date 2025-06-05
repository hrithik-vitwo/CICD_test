<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../pagination/common-pagination.php");

require_once("../../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');

// // print_r($_POST);
// $currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);
$dbObj = new Database();
$BranchSoObj = new BranchSo();
session_start();

if ($_POST['act'] == 'tdata') {
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

            if ($slag === 'doc_date' || $slag === 'due_date' || $slag === 'created_at' || $slag === 'updated_at') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif (strcasecmp($data['value'], 'Goods') === 0) {
                $data['value'] = 'material';
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            } elseif ($slag === "totalAmount") {
                $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));


        if (!empty($implodeFrom)) {
            $cond .= "WHERE 1" . $implodeFrom;
        }

        // $sts = " AND `status` !='deleted'";
        $sql_list = "SELECT 
            customer.customer_id,
            customer.company_id,
            customer.company_branch_id, 
            customer.location_id,
            customer.parentGlId, 
            customer.customer_code,
            customer.customer_pan,
            customer.customer_gstin,
            customer.trade_name,
            customer.legal_name,
            customer.constitution_of_business,
            customer.customer_opening_balance,
            customer.customer_currency,
            customer.customer_visible_to_all,
            customer.customer_website,
            customer.customer_credit_period,
            customer.customer_picture,
            customer.customer_authorised_person_name,
            customer.customer_authorised_person_email,
            customer.customer_authorised_alt_email,
            customer.customer_authorised_person_phone,
            customer.customer_authorised_alt_phone,
            customer.customer_authorised_person_designation,
            customer.customer_discount_group,
            customer.customer_mrp_group,
            customer.customer_profile,
            customer.customer_status,
            customer.customer_created_at, 
            customer.customer_created_by,
            customer.customer_updated_at,
            customer.customer_updated_by,
            customer.customer_active_flag,
            customer.mail_send_status,
            COALESCE(invoice_counts.invoice_count, 0) AS invoice_count,
            COALESCE(credit_note_counts.credit_note_count, 0) AS credit_note_count,
            COALESCE(invoice_totals.total_invoice_amt, 0) - COALESCE(credit_note_totals.total_credit_note_amt, 0) AS totalPrice
        FROM
            erp_customer AS customer
        LEFT JOIN
            (SELECT 
                invoices.customer_id,
                COUNT(*) AS invoice_count
             FROM
                erp_branch_sales_order_invoices AS invoices
             WHERE
                invoices.status = 'active' 
                AND invoices.company_id = 1 
                AND invoices.branch_id = 1 
                AND invoices.location_id = 1 
                AND invoices.invoice_date BETWEEN '2024-04-01' AND '2025-03-31'
             GROUP BY
                invoices.customer_id) AS invoice_counts
        ON
            customer.customer_id = invoice_counts.customer_id
        LEFT JOIN
            (SELECT 
                credit_notes.party_id AS customer_id,
                COUNT(*) AS credit_note_count
             FROM
                erp_credit_note AS credit_notes
             WHERE
                credit_notes.status = 'active' 
                AND credit_notes.company_id = 1 
                AND credit_notes.branch_id = 1 
                AND credit_notes.location_id = 1 
                AND credit_notes.postingDate BETWEEN '2024-04-01' AND '2025-03-31'
             GROUP BY
                credit_notes.party_id) AS credit_note_counts
        ON
            customer.customer_id = credit_note_counts.customer_id
        LEFT JOIN
            (SELECT 
                invoices.customer_id,
                SUM(invoices.all_total_amt) AS total_invoice_amt
             FROM
                erp_branch_sales_order_invoices AS invoices
             WHERE
                invoices.status = 'active' 
                AND invoices.company_id = 1 
                AND invoices.branch_id = 1 
                AND invoices.location_id = 1 
                AND invoices.invoice_date BETWEEN '2024-04-01' AND '2025-03-31'
             GROUP BY
                invoices.customer_id) AS invoice_totals
        ON
            customer.customer_id = invoice_totals.customer_id
        LEFT JOIN
            (SELECT 
                credit_notes.party_id AS customer_id,
                SUM(credit_notes.total) AS total_credit_note_amt
             FROM
                erp_credit_note AS credit_notes
             WHERE
                credit_notes.status = 'active' 
                AND credit_notes.company_id = 1 
                AND credit_notes.branch_id = 1 
                AND credit_notes.location_id = 1 
                AND credit_notes.postingDate BETWEEN '2024-04-01' AND '2025-03-31'
             GROUP BY
                credit_notes.party_id) AS credit_note_totals
        ON
            customer.customer_id = credit_note_totals.customer_id
        WHERE
            customer.company_id = 1
            AND customer.company_branch_id = 1
            AND customer.location_id = 1
        GROUP BY customer.customer_id";

$dbObj->queryUpdate("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))", true);
    
        // $sql_Mainqry = $sql_list . "  ORDER BY so.so_id DESC LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = $dbObj->queryGet($sql_Mainqry, true);


        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        if ($num_list > 0) {
            foreach ($sql_data as $data) {

                // $goodsType = "";
                // if ($data['goodsType'] === "material") {
                //   $goodsType .= '<p class="goods-type type-goods">GOODS</p>';
                // } elseif ($data['goodsType'] === "service") {
                //   $goodsType .= '<p class="goods-type type-service">SERVICE</p>';
                // } elseif ($data['goodsType'] === "both") {
                //   $goodsType .= '<p class="goods-type type-goods">BOTH</p>';
                // } elseif ($data['goodsType'] === "project") {
                //   $goodsType .= '<p class="goods-type type-project">PROJECT</p>';
                // }

                // if ($data['label'] == "open") {
                //   $approvalStatus = '<div class="status-bg status-open">Open</div>';
                // } elseif ($data['label'] == "pending") {
                //   $approvalStatus = '<div class="status-bg status-pending">Pending</div>';
                // } elseif ($data['label'] == "exceptional") {
                //   $approvalStatus = '<div class="status-bg status-exceptional">Exceptional</div>';
                // } elseif ($data['label'] == "closed") {
                //   $approvalStatus = '<div class="status-bg status-closed">Closed</div>';
                // }


                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "customer_code" => $data['customer_code'],
                    "customer_pan" => $data['customer_pan'],
                    "customer_gstin" => $data['customer_gstin'],
                    "trade_name" => $data['trade_name'],
                    "legal_name" => $data['legal_name'],
                    "constitution_of_business" => $data['constitution_of_business'],
                    "customer_opening_balance" => $data['customer_opening_balance'],
                    "customer_currency" => $data['customer_currency'],
                    "customer_visible_to_all" => $data['customer_visible_to_all'],
                    "customer_website" => $data['customer_website'],
                    "customer_credit_period" => $data['customer_credit_period'],
                    "customer_picture" => $data['customer_picture'],
                    "customer_authorised_person_name" => $data['customer_authorised_person_name'],
                    "customer_authorised_person_email" => $data['customer_authorised_person_email'],
                    "customer_authorised_alt_email" => $data['customer_authorised_alt_email'],
                    "customer_authorised_person_phone" => $data['customer_authorised_person_phone'],
                    "customer_authorised_alt_phone" => $data['customer_authorised_alt_phone'],
                    "customer_authorised_person_designation" => $data['customer_authorised_person_designation'],
                    "customer_discount_group" => $data['customer_discount_group'],
                    "customer_mrp_group" => $data['customer_mrp_group'],
                    "customer_profile" => $data['customer_profile'],
                    "customer_status" => $data['customer_status'],
                    "created_at" => formatDateORDateTime($data['customer_created_at']),
                    "created_by" => getCreatedByUser($data['customer_created_by']),  
                    "updated_at" => formatDateORDateTime($data['customer_updated_at']),
                    "updated_by" => getCreatedByUser($data['customer_updated_by']),
                    "customer_active_flag" => $data['customer_active_flag'],
                    "invoice_count" => $data['invoice_count'],
                    "mail_send_status" => $data['imail_send_status'],
                    "credit_note_count" => $data['credit_note_count'],
                    "totalPrice" => $data['totalPrice'],                   
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
                


            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sqlMainQryObj
            ];
        }

        echo json_encode($res);
    }
}
