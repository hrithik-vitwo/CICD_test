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
$headerData = array('Content-Type: application/json');


$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'assetsUnder') {
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

        $sql_list = "SELECT *,`items`.`createdAt` as `created`  FROM `erp_inventory_stocks_summary` as `stock` LEFT JOIN `erp_inventory_items` as `items` ON `stock`.`itemId` = `items`.`itemId` WHERE 1".$cond." AND `items`.`goodsType`=9 AND `stock`.`location_id`=$location_id ORDER BY `stock`.`stockSummaryId` desc";
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

                $goodTypeId = $data['goodsType'];
                $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                $type_name = $type_sql['data']['goodTypeName'];

                $goodGroupId = $data['goodsGroup'];
                $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                $group_name = $group_sql['data']['goodGroupName']??"";

                $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
                $mwp = $summary_sql['data']['movingWeightedPrice'];
                $val_class = $summary_sql['data']['priceType'];

                $bom_status="";
                if ($data['bomStatus'] == 1) {

                    if ($goodsBomController->isBomCreated($data['itemId'])) {

                        $bom_status .= '<span class="status">Created</span>';
                    } else {

                        $bom_status .=  '<span class="status-warning">Not Created</span>';
                    }
                } else {

                    $bom_status .=  '<span class="status-danger">Not Required</span>';
                }

                $button = "";

                $item_id = $data['itemId'];
                $check_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `location_id`=$location_id AND `itemId`=$item_id ", true);
                
                if ($check_sql['status'] == "success") {
                    $button .= '<button class="btn btn-success" type="button">Added</button>';
                } else {
                    $button .= '<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addToLocation">Add</button>';
                }
                

                $formObj = '<form action="/branch/location/goods-type-items-p.php" method="POST">
                                <input type="hidden" name="id" value="' . $data['itemId'] . '">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="' . $data['status'] . '"';
                if ($data['status'] == "draft") {
                    $formObj .= ' style="cursor: inherit; border:none"';
                } else {
                    $formObj .= ' onclick="return confirm(\'Are you sure to change status?\')"';
                }
                $formObj .= '>';

                if ($data['status'] == "active") {
                    $formObj .= '<p class="status text-xs">' . ucfirst($data['status']) . '</p>';
                } elseif ($data['status'] == "inactive" || $data['status'] == "guest") {
                    $formObj .= '<p class="status-danger text-xs">' . ucfirst($data['status']) . '</p>';
                } elseif ($data['status'] == "draft") {
                    $formObj .= '<p class="status-warning text-xs">' . ucfirst($data['status']) . '</p>';
                }

                $formObj .= '</button>
                            </form>';


                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "uom"=>$buom,
                    "group_name"=>$group_name,
                    "type_name"=>$type_name,
                    "mwp"=>$mwp,
                    "val_class"=>$val_class,
                    "itemprice"=>$summary_sql['data']['itemPrice'],
                    "status"=>$formObj,
                    "button"=>$button
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
                "sqlMain" => $sqlMainQryObj,


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
