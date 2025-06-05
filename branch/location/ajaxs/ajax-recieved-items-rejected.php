<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
$headerData = array('Content-Type: application/json');

// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'poRecievedItemsRejected') {

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
            if ($slag === 'expectedDate') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $mode = "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))";
        queryGet($mode);

        $sql_list = "SELECT
        qa.`qa_log_Id`,
        qa.`companyId`,
        qa.`branchId`,
        qa.`locationId`,
        qa.`stock_log_id`,
        qa.`status`,
        qa.`passed`,
        SUM(qa.`rejected`) AS rejected,
        SUM(stocklog.itemQty) AS itemQty,
        qa.`qa_file`,
        qa.`qaCreatedAt`,
        qa.`qaCreatedBy`,
        qa.`qaUpdatedAt`,
        qa.`qaUpdatedBy`,
        qa.`uomStatus`,
        item.itemName,
        grn.grnPoNumber,
        grn.vendorCode,
        grn.vendorName,
        grn.vendorDocumentNo,
        stocklog.logRef,
        stocklog.bornDate
    FROM
        `erp_qa_log` AS qa
    LEFT JOIN `erp_inventory_stocks_log` AS stocklog
    ON
        stocklog.`stockLogId` = qa.`stock_log_id`
    LEFT JOIN `erp_storage_location` AS stloc
    ON
        stloc.`storage_location_id` = stocklog.`storageLocationId`
    LEFT JOIN `erp_grn` AS grn
    ON
        grn.`grnCode` = stocklog.`logRef`
    LEFT JOIN `erp_inventory_items` AS item
    ON
        item.`itemId` = stocklog.`itemId`
    LEFT JOIN `erp_inventory_mstr_uom` AS uom
    ON
        uom.`uomId` = item.`baseUnitMeasure`
    WHERE
        rejected > 0 AND qa.`companyId` = '$company_id' AND qa.`branchId` = '$branch_id' AND qa.`locationId` = '$location_id'
    GROUP BY
        `stock_log_id`
    DESC";


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
                    "itemName" => $data['itemName'],
                    "logRef" => $data['logRef'],
                    "rejected" => $data['rejected'],
                    "bornDate" => $data['bornDate'],
                    "grnPoNumber" => $data['grnPoNumber'],
                    "vendorDocumentNo" => $data['vendorDocumentNo'],
                    "vendorName" => $data['vendorName'],
                    "vendorCode"=> $data['vendorCode']
                ];
                $sl++;
            }
            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);
            $output .= '<div class="active" id="pagination">';

            $output .= '<div class="active" id="pagination">';


            if ($page_no > 1) {
                $output .= "<a id='" . ($page_no - 1) . "' href='?page=" . ($page_no - 1) . "'>Previous</a>";
            }

            for ($i = 1; $i <= $total_page; $i++) {
                if ($i <= 5 || $i >= $total_page - 1 || ($i >= $page_no - 2 && $i <= $page_no + 2)) {
                    $output .= "<a id='{$i}' href='?page={$i}'>{$i}</a>";
                }
            }


            if ($page_no < $total_page) {
                $output .= "<a id='" . ($page_no + 1) . "' href='?page=" . ($page_no + 1) . "'>Next</a>";
                $output .= "<a id='" . $total_page . "' href='?page=" . ($page_no + 1) . "'>Last</a>";
            }

            $output .= '</div>';

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
