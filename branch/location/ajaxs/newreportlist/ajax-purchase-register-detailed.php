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
    grn.vendorCode AS vendor_code,
    grn.vendorName AS vendor_name,
    goods.goodCode AS item_code,
    items.itemName AS item_name,
    SUM(goods.goodQty) AS total_ordered_qty,
    SUM(goods.receivedQty) AS total_received_qty,
    goods.itemUOM AS uom,
    goods.`goodQty`,
    goods.`receivedQty`,
    goods.`unitPrice`,
    goods.`cgst`,
    goods.`sgst`,
    goods.`igst`,
    goods.`tds`,
    SUM(goods.totalAmount) AS total_amount
FROM
    erp_grninvoice AS grn
INNER JOIN erp_grninvoice_goods AS goods
ON
    grn.grnIvId = goods.grnIvId
LEFT JOIN erp_inventory_items AS items
ON
    goods.goodCode = items.itemCode
WHERE
    grn.companyId = 1 AND grn.branchId = 1 AND grn.locationId = 1 AND grn.postingDate BETWEEN '2023-04-01' AND '2024-06-21' AND grn.grnStatus = 'active' AND items.company_id = 1
GROUP BY
    vendor_code,
    vendor_name,
    item_code,
    item_name,
    uom,
    goodQty,
    receivedQty,
    unitPrice,
    cgst,
    sgst,
    igst,
    tds";

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
                    "sl_no" => $sl++,
                    "vendor_code" => $data['vendor_code'],
                    "vendor_name" => $data['vendor_name'],
                    "item_code" => $data['item_code'],
                    "item_name" => $data['item_name'],
                    "total_ordered-qty" => $data['total_ordered-qty'],
                    "total_received_qty" => $data['total_received_qty'],
                    "uom" => $data['uom'],
                    "goodQty" => $data['goodQty'],
                    "receivedQty" => $data['receivedQty'],
                    "unitPrice" => $data['unitPrice'],
                    "cgst" => $data['cgst'],
                    "sgst" => $data['sgst'],
                    "igst" => $data['igst'],
                    "tds" => $data['tds'],
                    "total_amount" => $data['total_amount']
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
