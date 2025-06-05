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
require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');
// global $location_id;

$goodsController = new GoodsController();

$goodsBomController = new GoodsBomController();

// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'items') {
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

        $flagSeeano = false;
        $implodeFrom = implode('', array_map(function ($slag, $data) use (&$flagSeeano) {
            $conds = "";
            global $decimalValue;
            if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            } else if ($slag === 'goods.createdBy') {
                

                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
            }
            else if($slag === 'stock.movingWeightedPrice' || $slag === 'stock.itemPrice')
            {
                $flagSeeano = true;
                $cleanedValue = str_replace(',', '', $data['value']);
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } 
            else if ($slag == 'goods.isBomRequired') {
                $statusmap = [
                    "required" => 1,
                    "not required" => 0
                ];

                $inputValue = strtolower(trim($data['value']));
                $value = $statusmap[$inputValue] ?? 0;
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $value . "%'";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sts = " AND goods.status !='deleted'";


        // $sql_list = "SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `erp_inventory_items` as goods ON stock.itemId=goods.itemId WHERE 1 " . $cond . " AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";

        $sql_list = "SELECT goods.itemId, goods.itemCode, goods.itemName,goods.isBomRequired ,UOM.uomName, groups.goodGroupName, stock.movingWeightedPrice, stock.priceType AS valuation_class, stock.itemPrice AS target_price, type.goodTypeName, stock.bomStatus AS bomStatus, stock.status, goods.createdBy FROM erp_inventory_stocks_summary as stock LEFT JOIN erp_inventory_items as goods ON stock.itemId=goods.itemId LEFT JOIN erp_inventory_mstr_uom AS UOM ON goods.baseUnitMeasure = UOM.uomId LEFT JOIN erp_inventory_mstr_good_groups AS groups ON goods.goodsGroup = groups.goodGroupId LEFT JOIN erp_inventory_mstr_good_types AS type ON goods.goodsType = type.goodTypeId LEFT JOIN erp_status_master AS status_mstr ON stock.bomStatus = status_mstr.status_id WHERE 1 " . $cond . " AND stock.location_id=$location_id AND goods.itemId != ''  " . $sts . " ORDER BY stock.stockSummaryId DESC";


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

                $itemCode = $data['itemCode'];

                $itemName = $data['itemName'];

                $netWeight = $data['netWeight'];

                $volume = $data['volume'];

                $goodsType = $data['goodsType'];

                $grossWeight = $data['grossWeight'];

                $buom_id = $data['baseUnitMeasure'];

                // $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                // $buom = $buom_sql['data']['uomName'];

                $buom = $data['uomName'];

                $goodTypeId = $data['goodsType'];

                // $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                // $type_name = $type_sql['data']['goodTypeName'];

                $type_name = $data['goodTypeName'];

                $goodGroupId = $data['goodsGroup'];

                // $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                // $group_name = $group_sql['data']['goodGroupName'];

                $group_name = $data['goodGroupName'];

                // $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                $type_name = $type_sql['data']['goodTypeName'] ? $type_sql['data']['goodTypeName'] : '-';
                $type_name = $data['goodTypeName'];
                $bom_status = "";
                $b_status = "";
                if ($data['isBomRequired'] == 1) {
                    $bom_status .= '<span class="bom-status bom-required">Required</span>';
                    $b_status = 'Required';
                } else {
                    $bom_status .= '<span class="bom-status bom-notrequired">Not Required</span>';
                    $b_status = 'Not Required';
                }

                $formObj = '<form action="/branch/location/manage-location-items-p.php" method="POST">
                                <input type="hidden" name="id" value="' . $data['itemId'] . '">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button ';

                if ($data['status'] == "draft") {
                    $formObj .= 'type="button" style="cursor: inherit; border:none"';
                } else {
                    $formObj .= 'type="submit" onclick="return confirm(\'Are you sure to change item status?\')"';
                }

                $formObj .= ' class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="' . $data['status'] . '">';

                if ($data['status'] == "active") {
                    $formObj .= '<div class="status">' . ucfirst($data['status']) . '</div>';
                } elseif ($data['status'] == "inactive") {
                    $formObj .= '<p class="status-danger">' . ucfirst($data['status']) . '</p>';
                } elseif ($data['status'] == "draft") {
                    $formObj .= '<p class="status-warning">' . ucfirst($data['status']) . '</p>';
                }

                $formObj .= '</button>
                            </form>';

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "itemId" => $data['itemId'],
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "uomName" => $buom,
                    "groups.goodGroupName" => $group_name,
                    "stock.movingWeightedPrice" => decimalValuePreview($data['movingWeightedPrice']),
                    "stock.priceType" => $data['valuation_class'],
                    "stock.itemPrice" => decimalValuePreview($data['target_price']),
                    "type.goodTypeName" => $type_name,
                    "goods.createdBy" => getCreatedByUser($data['createdBy']),
                    "isBomRequired" => $bom_status,
                    "goods.isBomRequired" => $b_status,
                    "status" => $formObj,
                    "stock.status" => $data['status']
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
                "sqlMain" => $sqlMainQryObj,
                "flag" => $flagSeeano,
                "conds" => $cond

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

if ($_POST['act'] == 'alldata') {
    $formObj = $_POST['formDatas'];
    $cond = "";
    // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";

    $flagSeeano = false;
    
    $implodeFrom = implode('', array_map(function ($slag, $data) use (&$flagSeeano) {
        $conds = "";
        global $decimalValue;
        if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
            if ($data['operatorName'] === 'BETWEEN') {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
            } else {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
            }
        } else if ($slag === 'goods.createdBy') {
            $flagSeeano = true;

            $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
        }
        else if($slag === 'stock.movingWeightedPrice' || $slag === 'stock.itemPrice')
            {
                $flagSee = true;
                $cleanedValue = str_replace(',', '', $data['value']);
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
            } 
        else if ($slag == 'goods.isBomRequired') {
            $statusmap = [
                "required" => 1,
                "not required" => 0
            ];

            $inputValue = strtolower(trim($data['value']));
            $value = $statusmap[$inputValue] ?? 0;
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $value . "%'";
        } else {
            $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
        }

        return !empty($data['value']) ? " AND " . $conds : "";
    }, array_keys($formObj), $formObj));

    if (!empty($implodeFrom)) {
        $cond .= $implodeFrom;
    }

    $sts = " AND goods.status !='deleted'";


    // $sql_list = "SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `erp_inventory_items` as goods ON stock.itemId=goods.itemId WHERE 1 " . $cond . " AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc";

    $sql_list = "SELECT goods.itemId, goods.itemCode, goods.itemName,goods.isBomRequired ,UOM.uomName, groups.goodGroupName, stock.movingWeightedPrice, stock.priceType AS valuation_class, stock.itemPrice AS target_price, type.goodTypeName, stock.bomStatus AS bomStatus, stock.status, goods.createdBy FROM erp_inventory_stocks_summary as stock LEFT JOIN erp_inventory_items as goods ON stock.itemId=goods.itemId LEFT JOIN erp_inventory_mstr_uom AS UOM ON goods.baseUnitMeasure = UOM.uomId LEFT JOIN erp_inventory_mstr_good_groups AS groups ON goods.goodsGroup = groups.goodGroupId LEFT JOIN erp_inventory_mstr_good_types AS type ON goods.goodsType = type.goodTypeId LEFT JOIN erp_status_master AS status_mstr ON stock.bomStatus = status_mstr.status_id WHERE 1 " . $cond . " AND stock.location_id=$location_id AND goods.itemId != ''  " . $sts . " ORDER BY stock.stockSummaryId DESC";
    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {

            if ($data['isBomRequired'] == 1) {
                $bom_status .= '<span class="bom-status bom-required">Required</span>';
                $b_status = 'Required';
            } else {
                $bom_status .= '<span class="bom-status bom-notrequired">Not Required</span>';
                $b_status = 'Not Required';
            }

            $dynamic_data_all[] = [
                "sl_no" => $sl,
                "itemId" => $data['itemId'],
                "itemCode" => $data['itemCode'],
                "itemName" => $data['itemName'],
                "uomName" => $data['uomName'],
                "groups.goodGroupName" => $data['goodGroupName'],
                "stock.movingWeightedPrice" => decimalValuePreview($data['movingWeightedPrice']),
                "stock.priceType" => $data['valuation_class'],
                "stock.itemPrice" => decimalValuePreview($data['target_price']),
                "type.goodTypeName" => $data['goodTypeName'],
                "goods.createdBy" => getCreatedByUser($data['createdBy']),
                "goods.isBomRequired" => $b_status,
                "stock.status" => $data['status']
            ];
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
