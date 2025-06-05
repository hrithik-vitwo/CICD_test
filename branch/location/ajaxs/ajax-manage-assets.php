<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../app/v1/functions/branch/func-bom-controller.php");
require_once("pagination/common-pagination.php");

$headerData = array('Content-Type: application/json');


$goodsController = new GoodsController();
$goodsBomController = new GoodsBomController();


$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'assets') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

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
        $sql_list = "";
        if ($_POST['assetFlagVal'] == "assetC") {
            $sql_list = "SELECT stock.*, items.* FROM `erp_inventory_stocks_summary` AS stock LEFT JOIN `erp_inventory_items` AS items ON `stock`.`itemId` = `items`.`itemId` WHERE 1 " . $cond . "  AND `items`.`goodsType` = 9 AND `stock`.`location_id` = $location_id AND items.status!='deleted' ORDER BY `stock`.`stockSummaryId` DESC";
        } else {
            $sql_list = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . " AND  `goodsType`=9 AND `company_id`=$company_id  ORDER BY itemId DESC";
        }

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
                $itemCode = $data['itemCode'];
                $itemName = $data['itemName'];
                $netWeight = $data['netWeight'];
                $volume = $data['volume'];
                $goodsType = $data['goodsType'];
                $grossWeight = $data['grossWeight'];
                $buom_id = $data['baseUnitMeasure'];

                $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                $buom = $buom_sql['data']['uomName'];
                //  console($buom);
                $goodTypeId = $data['goodsType'];
                $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                $type_name = $type_sql['data']['goodTypeName'];



                $goodGroupId = $data['goodsGroup'];
                $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                $group_name = $group_sql['data']['goodGroupName'] ?? "";


                $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
                $mwp = $summary_sql['data']['movingWeightedPrice'];
                $val_class = $summary_sql['data']['priceType'];

                $bom_status = "";
                if ($data['bomStatus'] == 1) {

                    if ($goodsBomController->isBomCreated($data['itemId'])) {

                        $bom_status .= '<span class="status">Created</span>';
                    } else {

                        $bom_status .=  '<span class="status-warning">Not Created</span>';
                    }
                } else {

                    $bom_status .=  '<span class="status-danger">Not Required</span>';
                }

                $item_id = $data['itemId'];
                $checkRes = [];
                $dDate=date('Y-m-d');
                $check_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `location_id`=$location_id AND `itemId`=$item_id ", true);
                $checkRes = ["flag" => "allList", "status" => $check_sql['status'], "numRows" => $check_sql['numRows']];
                if ($_POST['assetFlagVal'] == "assetC") {
                    // $check_asset = queryGet("SELECT * FROM `erp_asset_use` WHERE `asset_id`=$item_id AND `company_id`=$company_id AND `location_id`=$location_id");
                    
                        $check_asset = queryGet("
                            SELECT 
                                COALESCE(SUM(LOG.itemQty), 0) AS total_quantity
                            FROM 
                                erp_inventory_items AS item
                            LEFT JOIN 
                                erp_inventory_stocks_log AS LOG 
                            ON 
                                LOG.itemId = item.itemId AND LOG.postingDate <= '" . $dDate . "'
                            WHERE 
                                item.itemId = " . $item_id . " 
                                AND item.company_id = " . $company_id . "
                            GROUP BY 
                                item.itemId
                        ");
                    $checkRes = ["flag" => "assetC", "status" => $check_asset['status'], "numRows" =>$check_asset['data']['total_quantity']];
                }

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "itemId" => $data['itemId'],
                    "uom" => $buom,
                    "group_name" => $group_name,
                    "type_name" => $type_name,
                    "mwp" => $mwp,
                    "val_class" => $val_class,
                    "bom_status" => $bom_status,
                    "status" => $data['status'],
                    "checkRes" => $checkRes,
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
            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sql" => $sql_list,
                "assetFlag" => $_POST['assetFlagVal']
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
