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
session_start();

$currentDate = date('Y-m-d');
// $timestampPreviousDay = strtotime($fromd . ' -1 day');
// $previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'goodsStock') {
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
        $ddate = "";

        $implodeFrom = implode('', array_map(function ($slag, $data) use (&$ddate) {
            $conds = "";
            if ($slag === 'report_date') {
                $date =  $data['value'];
                $ddate = $date;
                $conds .= "";
            } else if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return (!empty($data['value']) && $conds != "") ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $dateAson = ($ddate != "") ? $ddate : $currentDate;
        $condstock .= " AND DATE_FORMAT(bornDate, '%Y %m %d') <= DATE_FORMAT('" . $dateAson . "', '%Y %m %d')";
                        
        $sts = " AND `status` !='deleted'";

        $sql_list = "SELECT invSummary.*, goodTypes.`goodTypeName` AS goodType,  invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType`, goodUoms.`uomName`, goodUoms.`uomDesc` FROM `erp_inventory_stocks_summary` AS invSummary, `erp_inventory_items` AS invItems, `erp_inventory_mstr_good_types` AS goodTypes, `erp_inventory_mstr_uom` AS goodUoms WHERE invSummary.itemId = invItems.itemId AND invItems.goodsType  IN (1, 2, 3,4)  AND invSummary.`company_id` ='" . $company_id . " ' AND invSummary.`branch_id` ='" . $branch_id . " ' AND invSummary.`location_id` =' " . $location_id . "' AND invItems.`goodsType` = goodTypes.`goodTypeId`  AND invItems.`baseUnitMeasure`=goodUoms.`uomId` " . $cond . " ORDER BY invSummary.`updatedAt` DESC";

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

                $itemId = $data['itemId'];

                $stock_log_sql = queryGet("SELECT
        MAX(bornDate) AS bornDate,
        SUM(itemQty) AS qty,
        SUM(CASE WHEN `storageType` = 'rmWhOpen' THEN itemQty ELSE 0 END) AS rmWhOpen_qty,
        SUM(CASE WHEN `storageType` = 'rmWhReserve' THEN itemQty ELSE 0 END) AS rmWhReserve_qty,
        SUM(CASE WHEN `storageType` = 'rmProdOpen' THEN itemQty ELSE 0 END) AS rmProdOpen_qty,
        SUM(CASE WHEN `storageType` = 'rmProdReserve' THEN itemQty ELSE 0 END) AS rmProdReserve_qty,
        SUM(CASE WHEN `storageType` = 'sfgStockOpen' THEN itemQty ELSE 0 END) AS sfgStockOpen_qty,
        SUM(CASE WHEN `storageType` = 'sfgStockReserve' THEN itemQty ELSE 0 END) AS sfgStockReserve_qty,
        SUM(CASE WHEN `storageType` = 'fgWhOpen' THEN itemQty ELSE 0 END) AS fgWhOpen_qty,
        SUM(CASE WHEN `storageType` = 'fgWhReserve' THEN itemQty ELSE 0 END) AS fgWhReserve_qty,
        SUM(CASE WHEN `storageType` = 'fgMktOpen' THEN itemQty ELSE 0 END) AS fgMktOpen_qty,
        SUM(CASE WHEN `storageType` = 'fgMktReserve' THEN itemQty ELSE 0 END) AS fgMktReserve_qty,
        SUM(CASE WHEN storageType = 'QaLocation' THEN itemQty ELSE 0 END) AS QaLocation_qty
    FROM
        `erp_inventory_stocks_log`
    WHERE
        `itemId` = $itemId AND `locationId` = $location_id AND `branchId` = $branch_id AND `companyId` = $company_id
        " . $condstock . ";
    ");
                //console($stock_log_sql);
                $total_qty_sql = queryGet("SELECT * FROM `erp_inventory_stocks_log_report` WHERE  `item_Id` = $itemId AND `location_id` = $location_id AND `branch_id` = $branch_id AND `company_id` = $company_id AND DATE_FORMAT(report_date, '%Y %m %d') <= DATE_FORMAT('" . $dateAson . "', '%Y %m %d') ORDER BY `report_id` DESC");

                // console($stock_log_sql);
                $total_qty = $total_qty_sql['data']['total_closing_qty'] ?? 0;
                if ($total_qty == 0) {
                    $born_date = '-';
                } else {
                    $born_date = formatDateORDateTime($stock_log_sql['data']['bornDate']) ?? '-';
                }


                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "itemId" => $data['itemId'],
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "goodType" => $data['goodType'],
                    "total_qty" => $total_qty,
                    "uomName" => $data['uomName'],
                    "priceType" => $data['priceType'],
                    "movingWeightedPrice" => number_format($data["movingWeightedPrice"], 2),
                    "targetPrice" => $data['itemPrice'],
                    "value" => number_format($data["movingWeightedPrice"] * $total_qty, 2),
                    "borndate" => ($born_date==='-')?' -':formatDateORDateTime($born_date),
                    "condstock"=>$condstock
                    // "csvContent" => $csvContent,
                    // "csvContentBypagination" => $csvContentBypagination


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
                "sqlMain" => $sqlMainQryObj

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
