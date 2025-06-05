<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");

require_once("../../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');

session_start();

if ($_POST['act'] == 'pdata') {
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

            if ($slag === 'items.createdAt' || $slag === 'items.updatedAt' || $slag === 'posting_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } elseif ($slag === "totalPrice" || $slag === "invoice.qnty" || $slag === "invoice.base_amount" || $slag === "invoice.cgst" || $slag === "invoice.sgst" || $slag === "invoice.igst" || $slag === "invoice.total_amount" || $slag === "credit_notes.cgst" || $slag === "credit_notes.sgst" || $slag === "credit_notes.igst" || $slag === "credit_notes.cn_amount" || $slag === "debit_notes.cgst" || $slag === "debit_notes.sgst" || $slag === "debit_notes.igst" || $slag === "debit_notes.dn_amount") {
                $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
            } 
            else if($slag === 'so.created_by' || $slag==='created_by'|| $slag==='items.createdBy'){

                $resultList = getAdminUserIdByName($data['value']);                        
                $conds .= $slag . " IN  " . " (" . $resultList. ")";
        
            } 
            else if($slag==='updated_by' || $slag==='items.updatedBy'){

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
        $sql_list = "SELECT items.itemCode, items.itemName, grp.goodGroupName AS itemGroup, COALESCE(invoice.qnty, 0) AS quantity, COALESCE(invoice.base_amount, 0) AS base_amount, COALESCE(invoice.cgst, 0) AS cgst, COALESCE(invoice.sgst, 0) AS sgst, COALESCE(invoice.igst, 0) AS igst, COALESCE(invoice.total_amount, 0) AS total_amount, COALESCE(credit_notes.cn_amount, 0) - (COALESCE(credit_notes.cgst, 0)+COALESCE(credit_notes.sgst, 0)+COALESCE(credit_notes.igst, 0)) AS cn_base_amount, COALESCE(credit_notes.cgst, 0) AS cn_cgst, COALESCE(credit_notes.sgst, 0) AS cn_sgst, COALESCE(credit_notes.igst, 0) AS cn_igst, COALESCE(credit_notes.cn_amount, 0) AS credit_note_amount, COALESCE(debit_notes.dn_amount, 0) - (COALESCE(debit_notes.cgst, 0)+COALESCE(debit_notes.sgst, 0)+COALESCE(debit_notes.igst, 0)) AS dn_base_amount, COALESCE(debit_notes.cgst, 0) AS dn_cgst, COALESCE(debit_notes.sgst, 0) AS dn_sgst, COALESCE(debit_notes.igst, 0) AS dn_igst, COALESCE(debit_notes.dn_amount, 0) AS debit_note_amount, COALESCE(invoice.total_amount, 0) - COALESCE(credit_notes.cn_amount, 0) + COALESCE(debit_notes.dn_amount, 0) AS net_sales_amount, items.createdAt, items.createdBy, items.updatedAt, items.updatedBy FROM erp_inventory_items AS items LEFT JOIN (SELECT inv_item.inventory_item_id, SUM(inv_item.qty) AS qnty, SUM(inv_item.basePrice - inv_item.totalDiscountAmt) AS base_amount, SUM(CASE WHEN inv.igst = 0 AND inv.so_invoice_id = inv_item.so_invoice_id THEN (inv_item.totalTax/2) ELSE 0 END) AS cgst, SUM(CASE WHEN inv.igst = 0 AND inv.so_invoice_id = inv_item.so_invoice_id THEN (inv_item.totalTax/2) ELSE 0 END) AS sgst, SUM(CASE WHEN inv.cgst = 0 AND inv.sgst = 0 AND inv.so_invoice_id = inv_item.so_invoice_id THEN (inv_item.totalTax) ELSE 0 END) AS igst, SUM(inv_item.totalPrice) AS total_amount FROM erp_branch_sales_order_invoice_items AS inv_item LEFT JOIN erp_branch_sales_order_invoices AS inv ON inv_item.so_invoice_id = inv.so_invoice_id WHERE inv.company_id=$company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id GROUP BY inv_item.inventory_item_id) AS invoice ON invoice.inventory_item_id = items.itemId LEFT JOIN (SELECT cn_item.item_id, SUM(cn_item.cgst) AS cgst, SUM(cn_item.sgst) AS sgst, SUM(cn_item.igst) AS igst, SUM(cn_item.item_amount) AS cn_amount FROM credit_note_item AS cn_item LEFT JOIN erp_credit_note AS cn ON cn_item.credit_note_id = cn.cr_note_id WHERE cn.company_id=$company_id AND cn.branch_id=$branch_id AND cn.location_id=$location_id GROUP BY cn_item.item_id) AS credit_notes ON credit_notes.item_id = items.itemId LEFT JOIN (SELECT dn_item.item_id, SUM(dn_item.cgst) AS cgst, SUM(dn_item.sgst) AS sgst, SUM(dn_item.igst) AS igst, SUM(dn_item.item_amount) AS dn_amount FROM debit_note_item AS dn_item LEFT JOIN erp_debit_note AS dn ON dn_item.debit_note_id = dn.dr_note_id WHERE dn.company_id=$company_id AND dn.branch_id=$branch_id AND dn.location_id=$location_id GROUP BY dn_item.item_id) AS debit_notes ON debit_notes.item_id = items.itemId LEFT JOIN erp_inventory_mstr_good_groups AS grp ON items.goodsGroup = grp.goodGroupId WHERE 1 $cond AND items.company_id=$company_id ORDER BY items.itemId ";

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
                    "item_code" => $data['itemCode'],
                    "item_name" => $data['itemName'],
                    "item_group" => $data['itemGroup'],
                    "quantity" => $data['quantity'],
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
                    "created_by" => getCreatedByUser($data['createdBy']),
                    "created_at" => $data['createdAt'],
                    "updated_by" => getCreatedByUser($data['updatedBy']),
                    "updated_at" => $data['updatedAt'],
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
