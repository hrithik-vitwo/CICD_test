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

if ($_POST['act'] == 'poItems') {

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
            global $decimalValue;
            global $decimalQuantity;
            $conds = "";
            if ($slag === 'po_date' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else if ($slag == 'po_items.qty' || $slag == 'po_items.remainingQty') {
                $cleanedValue = str_replace(',', '', $data['value']);
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $cleanedValue;
            } else if ($slag == 'po_items.unitPrice' || $slag == 'po_items.total_price') {
                $cleanedValue = str_replace(',', '', $data['value']);
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $cleanedValue;
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }


        $sts = " AND po.status!='deleted'";


        $sql_list = "SELECT po_items.itemCode, po_items.itemName, po_items.unitPrice, po_items.qty, po_items.remainingQty, po_items.total_price, po_items.uom, po.po_number, po.delivery_date, po.po_date, (unitPrice*remainingQty) AS remaining_price FROM erp_branch_purchase_order_items as po_items LEFT JOIN erp_branch_purchase_order as po ON po_items.po_id = po.po_id WHERE 1 " . $cond . " AND po.branch_id=$branch_id AND po.location_id=$location_id AND po.company_id=$company_id "  . $sts . " ORDER BY po_items.po_id DESC";


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
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "po_number" => $data['po_number'],
                    "po_date" => $data['po_date'],
                    "delivery_date" => $data['delivery_date'],
                    "po_items.qty" => decimalQuantityPreview($data['qty']),
                    "po_items.remainingQty" => decimalQuantityPreview($data['remainingQty']),
                    "uom" => $data['uom'],
                    "po_items.unitPrice" => decimalValuePreview($data['unitPrice']),
                    "po_items.total_price" => decimalValuePreview($data['total_price']),
                    "remaining_price" => decimalValuePreview($data['remaining_price'])
                ];
                $sl++;
            }
            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);
            $output .= '<div class="active" id="pagination">';

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
                // "sqlMain" => $sqlMainQryObj

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
    // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";

    $implodeFrom = implode('', array_map(function ($slag, $data) {
        global $decimalValue;
        global $decimalQuantity;
        $conds = "";
        if ($slag === 'po_date' || $slag === 'delivery_date') {
            if ($data['operatorName'] === 'BETWEEN') {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
            } else {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
            }
        } else if ($slag == 'po_items.qty' || $slag == 'po_items.remainingQty') {
            $cleanedValue = str_replace(',', '', $data['value']);
            $conds .= "TRUNCATE(" . $slag . ", " . $decimalQuantity . ") " . $data['operatorName'] . " " . $cleanedValue;
        } else if ($slag == 'po_items.unitPrice' || $slag == 'po_items.total_price') {
            $cleanedValue = str_replace(',', '', $data['value']);
            $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $cleanedValue;
        } else {
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }

        return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
        $cond .= $implodeFrom;
    }


    $sts = " AND po.status!='deleted'";


    $sql_list = "SELECT po_items.itemCode, po_items.itemName, po_items.unitPrice, po_items.qty, po_items.remainingQty, po_items.total_price, po_items.uom, po.po_number, po.delivery_date, po.po_date, (unitPrice*remainingQty) AS remaining_price FROM erp_branch_purchase_order_items as po_items LEFT JOIN erp_branch_purchase_order as po ON po_items.po_id = po.po_id WHERE 1 " . $cond . " AND po.branch_id=$branch_id AND po.location_id=$location_id AND po.company_id=$company_id "  . $sts . " ORDER BY po_items.po_id DESC";

    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {
            $dynamic_data_all[] = [
                    "sl_no" => $sl,
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "po_number" => $data['po_number'],
                    "po_date" => $data['po_date'],
                    "delivery_date" => $data['delivery_date'],
                    "po_items.qty" => decimalQuantityPreview($data['qty']),
                    "po_items.remainingQty" => decimalQuantityPreview($data['remainingQty']),
                    "uom" => $data['uom'],
                    "po_items.unitPrice" => decimalValuePreview($data['unitPrice']),
                    "po_items.total_price" => decimalValuePreview($data['total_price']),
                    "remaining_price" => decimalValuePreview($data['remaining_price'])
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
