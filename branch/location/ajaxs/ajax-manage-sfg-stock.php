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

if ($_POST['act'] == 'sfg') {
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
            if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
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

        $sts = " AND `so`.status !='deleted'";

                                
             $sql_list = "SELECT invSummary.*, goodTypes.`goodTypeName` AS goodType,  invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType`, goodUoms.`uomName`, goodUoms.`uomDesc` FROM `erp_inventory_stocks_summary` AS invSummary, `erp_inventory_items` AS invItems, `erp_inventory_mstr_good_types` AS goodTypes, `erp_inventory_mstr_uom` AS goodUoms WHERE invSummary.itemId = invItems.itemId AND invSummary.`company_id` ='" . $company_id . "' AND invSummary.`branch_id` =' ". $branch_id . "' AND invSummary.`location_id` ='" . $location_id ." ' AND invItems.`goodsType` = goodTypes.`goodTypeId` AND invItems.goodsType = 2 AND invItems.`baseUnitMeasure`=goodUoms.`uomId` " . $cond . "  ORDER BY invSummary.`updatedAt` DESC";
                                             

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

                $itemId = $oneInvItem['itemId'];
                // $total_qty = $oneInvItem['rmWhOpen']+$oneInvItem['rmWhReserve']+$oneInvItem['rmProdOpen']+$oneInvItem['rmProdReserve']+$oneInvItem['sfgStockOpen']+$oneInvItem['sfgStockReserve']+$oneInvItem['fgWhOpen']+$oneInvItem['fgWhReserve']+$oneInvItem['fgMktOpen']+$oneInvItem['fgMktReserve'];
                // console($oneInvItem);

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
              //  console($stock_log_sql);
                $total_qty_sql = queryGet("SELECT * FROM `erp_inventory_stocks_log_report` WHERE  `item_Id` = $itemId AND `location_id` = $location_id AND `branch_id` = $branch_id AND `company_id` = $company_id AND DATE_FORMAT(report_date, '%Y %m %d') <= DATE_FORMAT('" . $date . "', '%Y %m %d') ORDER BY `report_id` DESC");
                

                // console($stock_log_sql);
                $total_qty = $total_qty_sql['data']['total_closing_qty'] ?? 0;
                if ($total_qty == 0) {
                    $born_date = '-';
                } else {
                    $born_date = formatDateORDateTime($stock_log_sql['data']['bornDate']) ?? '-';
                }

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "itemCode"=>$data['itemCode'],
                    "itemName"=>$data['itemName'],
                    "goodType"=>$data['goodType'],
                    "total_qty"=>$total_qty,
                    "uomName"=>$data['uomName'],
                    "priceType"=>$data['priceType'],
                    "movingWeightedPrice"=>number_format($data['movingWeightedPrice']),
                    "targetPrice"=>round($total_qty * $data["movingWeightedPrice"], 2),
                    "itemPrice"=>$data['itemPrice'],
                    "born_date"=>$born_date

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
