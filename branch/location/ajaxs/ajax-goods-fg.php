<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
$headerData = array('Content-Type: application/json');

// echo json_encode("Hii");

// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'goodsFg') {
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


        $sql_list = "SELECT items.itemCode, items.itemName, items.isBomRequired, items.status, UOM.uomName, groups.goodGroupName, types.goodTypeName, summary.movingWeightedPrice, summary.priceType, summary.itemPrice FROM erp_inventory_items AS items LEFT JOIN erp_inventory_mstr_uom AS UOM ON items.baseUnitMeasure = UOM.uomId LEFT JOIN erp_inventory_mstr_good_groups AS groups ON items.goodsGroup = groups.goodGroupId LEFT JOIN erp_inventory_mstr_good_types AS types ON items.goodsType = types.goodTypeId LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId WHERE items.goodsType=3 AND summary.company_id=$company_id AND summary.branch_id=$branch_id AND summary.location_id=$location_id ORDER BY items.itemId DESC";


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
                // $itemId = $data['itemId'];
                // $itemCode = $data['itemCode'];

                // $itemName = $data['itemName'];

                // $netWeight = $data['netWeight'];

                // $volume = $data['volume'];

                // $goodsType = $data['goodsType'];

                // $grossWeight = $data[' '];

                // $buom_id = $data['baseUnitMeasure'];
                // $auom_id = $data['issueUnitMeasure'];

                // $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                // $buom = $buom_sql['data']['uomName'];

                // $service_unit_sql =  queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $data['service_unit'] . "' ");
                // //  console($buom);
                // $auom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$auom_id ");
                // $auom = $buom_sql['data']['uomName'];


                // $goodTypeId = $data['goodsType'];
                // $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                // $type_name = $type_sql['data']['goodTypeName'] ? $type_sql['data']['goodTypeName'] : '-';



                // $goodGroupId = $data['goodsGroup'];
                // $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                // $group_name = $group_sql['data']['goodGroupName'] ? $group_sql['data']['goodGroupName'] : '-';

                // $purchaseGroupId = $data['purchaseGroup'];
                // $purchase_group_sql = queryGet("SELECT * FROM `erp_inventory_mstr_purchase_groups` WHERE `purchaseGroupId` = $purchaseGroupId ");
                // $purchase_group = isset($purchase_group_sql['data']['purchaseGroupName']) ? $purchase_group_sql['data']['purchaseGroupName'] : '-';


                // $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
                // $mwp = $summary_sql['data']['movingWeightedPrice'];
                // $val_class = $summary_sql['data']['priceType'] ? $summary_sql['data']['priceType'] : '-';
                // $min_stock = $summary_sql['data']['min_stock'] ? $summary_sql['data']['min_stock'] : '-';
                // $max_stock = $summary_sql['data']['max_stock'] ? $summary_sql['data']['max_stock'] : '-';
                // $uom;

                // if ($row['goodsType'] == 5 || $row['goodsType'] == 7 || $row['goodsType'] == 10) {
                //     $uom = $service_unit_sql['data']['uomName'];
                // } else {
                //     $uom = $buom;
                // }

                $bom_status = "";
                if ($data['isBomRequired'] == 1) {
                    $bom_status .= '<span class="status">Required</span>';
                } else {
                    $bom_status .= '<span class="status-danger">Not Required</span>';
                }

                $formObj = '<form action="/branch/location/goods-p.php" method="POST">
                                <input type="hidden" name="id" value="'. $data['itemId'] .'">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button class="p-0 m-0 ml-2 border-0" data-toggle="tooltip" data-placement="top" title="'. $data['status'] .'"';
                
                if ($data['status'] == "draft") {
                    $formObj .= ' style="cursor: inherit; border:none"';
                } else {
                    $formObj .= ' onclick="return confirm(\'Are you sure to change status?\')"';
                }
                
                $formObj .= '>';
     
                if ($data['status'] == "active" ) {
                    $formObj .= '<span class="status">' . ucfirst($data['status']).  '</span>';

                }elseif($data['status'] == "inactive"){
                    $formObj .= '<span class="status-danger">' . ucfirst($data['status']) . '</span>';

                }elseif($data['status'] == "draft"){
                    $formObj .= '<span class="status-warning">' . ucfirst($data['status']) . '</span>';

                }
               
                $formObj .= '</button>
                            </form>';


                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "uomName" => $data['uomName']??'OTH',
                    "goodGroupName" => $data['goodGroupName'],
                    "goodTypeName" => $data['goodTypeName'],
                    "movingWeightedPrice" => round($data['movingWeightedPrice'], 2),
                    "priceType" => $data['priceType'],
                    "itemPrice" => round($data['itemPrice'], 2),
                    "bom_status" =>  $bom_status,
                    "status"=>$formObj

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
