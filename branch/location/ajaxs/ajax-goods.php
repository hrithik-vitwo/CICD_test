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

// echo json_encode("Hii");

// print_r($_POST);
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'goods') {
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
            } else if ($slag === 'item.createdBy') {
                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
            } else if ($slag == 'isBomRequired') {
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

        $sql_list = "";
        $locItemId = $_POST['locItemId'];
        if ($locItemId == 0) {
            $sts = "AND item.status !='deleted'";
            $sql_list = "SELECT item.*,itemtype.goodTypeName FROM `" . ERP_INVENTORY_ITEMS . "` as item LEFT JOIN `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` as itemtype ON item.goodsType =itemtype.goodTypeId   WHERE 1 " . $cond . "  AND `company_id`=$company_id " . $sts . "  ORDER BY itemId desc";
        } else {
            $sts = " AND goods.status !='deleted'";

            $sql_list = "SELECT goods.goodsType,goods.itemId, goods.itemCode, goods.itemName, UOM.uomName, groups.goodGroupName, stock.movingWeightedPrice, stock.priceType AS valuation_class, stock.itemPrice AS target_price, type.goodTypeName, stock.bomStatus AS bomStatus, stock.status FROM erp_inventory_stocks_summary as stock LEFT JOIN erp_inventory_items as goods ON stock.itemId=goods.itemId LEFT JOIN erp_inventory_mstr_uom AS UOM ON goods.baseUnitMeasure = UOM.uomId LEFT JOIN erp_inventory_mstr_good_groups AS groups ON goods.goodsGroup = groups.goodGroupId LEFT JOIN erp_inventory_mstr_good_types AS type ON goods.goodsType = type.goodTypeId LEFT JOIN erp_status_master AS status_mstr ON stock.bomStatus = status_mstr.status_id WHERE 1 " . $cond . " AND stock.company_id=$company_id AND stock.location_id!=$location_id AND goods.itemId != ''  " . $sts . " ORDER BY stock.stockSummaryId DESC";
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

                $grossWeight = $data[' '];

                $buom_id = $data['baseUnitMeasure'];
                $auom_id = $data['issueUnitMeasure'];

                $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                $buom = $buom_sql['data']['uomName'];

                $service_unit_sql =  queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $data['service_unit'] . "' ");
                //  console($buom);
                $auom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$auom_id ");
                $auom = $buom_sql['data']['uomName'];


                $goodTypeId = $data['goodsType'];
                $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                $type_name = $type_sql['data']['goodTypeName'] ? $type_sql['data']['goodTypeName'] : '-';



                $goodGroupId = $data['goodsGroup'];
                $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                $group_name = $group_sql['data']['goodGroupName'] ? $group_sql['data']['goodGroupName'] : '-';

                $purchaseGroupId = $data['purchaseGroup'];
                $purchase_group_sql = queryGet("SELECT * FROM `erp_inventory_mstr_purchase_groups` WHERE `purchaseGroupId` = $purchaseGroupId ");
                $purchase_group = isset($purchase_group_sql['data']['purchaseGroupName']) ? $purchase_group_sql['data']['purchaseGroupName'] : '-';


                $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
                $mwp = $summary_sql['data']['movingWeightedPrice'];
                $val_class = $summary_sql['data']['priceType'] ? $summary_sql['data']['priceType'] : '-';
                $min_stock = $summary_sql['data']['min_stock'] ? $summary_sql['data']['min_stock'] : '-';
                $max_stock = $summary_sql['data']['max_stock'] ? $summary_sql['data']['max_stock'] : '-';
                $uom;

                if ($row['goodsType'] == 5 || $row['goodsType'] == 7 || $row['goodsType'] == 10) {
                    $uom = $service_unit_sql['data']['uomName'];
                } else {
                    $uom = $buom;
                }

                $bom_status = "";
                $b_status = "";
                if ($data['isBomRequired'] == 1) {
                    $bom_status .= '<span class="bom-status bom-required">Required</span>';
                    $b_status = "Required";
                } else {
                    $bom_status .= '<span class="bom-status bom-notrequired">Not Required</span>';
                    $b_status = "Not Required";
                }



                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "itemId" => $data['itemId'],
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "uom" => $uom,
                    "group_name" => $group_name,
                    "itemtype.goodTypeName" => $data['goodTypeName'],
                    "mwp" => decimalValuePreview($mwp),
                    "val_class" => $val_class,
                    "target_price" => decimalValuePreview($summary_sql['data']['itemPrice']),
                    "item.createdBy" => getCreatedByUser($data['createdBy']),
                    "bom_status" =>  $bom_status,
                    "isBomRequired" => $b_status,
                    "status" => $data['status'],
                    "locItemId" => $locItemId,
                    "goodsType" => $data['goodsType']


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
                "sqlMain" => $sql_list,
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


    $implodeFrom = implode('', array_map(function ($slag, $data) {
        $conds = "";
        if ($slag === 'so_date' || $slag === 'created_at' || $slag === 'delivery_date') {
            if ($data['operatorName'] === 'BETWEEN') {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
            } else {
                $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
            }
        } else if ($slag === 'item.createdBy') {


            $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
        } else if ($slag == 'isBomRequired') {
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
    $sts = "AND item.status !='deleted'";
    $sql_list = "SELECT item.*,itemtype.goodTypeName FROM `" . ERP_INVENTORY_ITEMS . "` as item LEFT JOIN `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` as itemtype ON item.goodsType =itemtype.goodTypeId   WHERE 1 " . $cond . "  AND `company_id`=$company_id " . $sts . "  ORDER BY itemId desc";
    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {

            $itemId = $data['itemId'];
            $itemCode = $data['itemCode'];

            $itemName = $data['itemName'];

            $netWeight = $data['netWeight'];

            $volume = $data['volume'];

            $goodsType = $data['goodsType'];

            $grossWeight = $data[' '];

            $buom_id = $data['baseUnitMeasure'];
            $auom_id = $data['issueUnitMeasure'];

            $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
            $buom = $buom_sql['data']['uomName'];

            $service_unit_sql =  queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $data['service_unit'] . "' ");
            //  console($buom);
            $auom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$auom_id ");
            $auom = $buom_sql['data']['uomName'];


            $goodTypeId = $data['goodsType'];
            $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
            $type_name = $type_sql['data']['goodTypeName'] ? $type_sql['data']['goodTypeName'] : '-';



            $goodGroupId = $data['goodsGroup'];
            $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
            $group_name = $group_sql['data']['goodGroupName'] ? $group_sql['data']['goodGroupName'] : '-';

            $purchaseGroupId = $data['purchaseGroup'];
            $purchase_group_sql = queryGet("SELECT * FROM `erp_inventory_mstr_purchase_groups` WHERE `purchaseGroupId` = $purchaseGroupId ");
            $purchase_group = isset($purchase_group_sql['data']['purchaseGroupName']) ? $purchase_group_sql['data']['purchaseGroupName'] : '-';


            $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
            $mwp = $summary_sql['data']['movingWeightedPrice'];
            $val_class = $summary_sql['data']['priceType'] ? $summary_sql['data']['priceType'] : '-';
            $min_stock = $summary_sql['data']['min_stock'] ? $summary_sql['data']['min_stock'] : '-';
            $max_stock = $summary_sql['data']['max_stock'] ? $summary_sql['data']['max_stock'] : '-';
            $uom;

            if ($row['goodsType'] == 5 || $row['goodsType'] == 7 || $row['goodsType'] == 10) {
                $uom = $service_unit_sql['data']['uomName'];
            } else {
                $uom = $buom;
            }

            $bom_status = "";
            $b_status = "";
            if ($data['isBomRequired'] == 1) {
                $bom_status .= '<span class="bom-status bom-required">Required</span>';
                $b_status = "Required";
            } else {
                $bom_status .= '<span class="bom-status bom-notrequired">Not Required</span>';
                $b_status = "Not Required";
            }

            $dynamic_data_all[] = [
                "sl_no" => $sl,
                "itemId" => $data['itemId'],
                "itemCode" => $data['itemCode'],
                "itemName" => $data['itemName'],
                "uom" => $uom,
                "group_name" => $group_name,
                "itemtype.goodTypeName" => $data['goodTypeName'],
                "mwp" => decimalValuePreview($mwp),
                "val_class" => $val_class,
                "target_price" => decimalValuePreview($summary_sql['data']['itemPrice']),
                "item.createdBy" => getCreatedByUser($data['createdBy']),
                "bom_status" =>  $bom_status,
                "isBomRequired" => $b_status,
                "status" => $data['status'],
                "locItemId" => $locItemId,
                "goodsType" => $data['goodsType']
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
