<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("pagination/common-pagination.php");

// require_once("../../common/exportexcel.php");
require_once("../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');
$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Item Code',
        'slag' => ' item.itemCode',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Item Name',
        'slag' => 'item.itemName',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Type',
        'slag' => ' TYPES.goodTypeName',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Total Qty',
        'slag' => 'last_closing_quantity',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'UOM',
        'slag' => 'UOM.uomName',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Valuation Class',
        'slag' => 'summary.priceType',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Price(MW)',
        'slag' => 'summary.movingWeightedPrice',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Total Value',
        'slag' => 'ROUND(SUM(last_closing_quantity) * summary.movingWeightedPrice, 2)',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ]

];


$currentDate = date('Y-m-d');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['act'] == 'inventory') {

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
            global $decimalValue;
            $conds = "";
            if ($slag === 'report_date') {
                $date =  $data['value'];
                $ddate = $date;
                $conds .= "";
            } elseif ($slag === 'last_closing_quantity' || $slag === 'summary.movingWeightedPrice') {
                $cleanedValue = str_replace(',', '', $data['value']);
            
                $roundedValue = number_format(round((float)$cleanedValue, $decimalValue), $decimalValue, '.', '');
                $conds .= "TRUNCATE(" . $slag . ", " . $decimalValue . ") " . $data['operatorName'] . " " . $roundedValue;
                
            } else if ($slag === 'item.createdBy') {

                $resultList = getAdminUserIdByName($data['value']);
                if($data['operatorName']=='LIKE'){
                    $resultList = (!empty($resultList)) ? $resultList : '0';
                    $conds .= $slag . " IN  " . " (" . $resultList . ")";
                }else{
                    $resultList = (!empty($resultList)) ? $resultList : '0';
                    $conds .= $slag . " NOT IN  " . " (" . $resultList . ")";
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

        // $sql_list = "SELECT 
        //                         item.itemId,
        //                         item.itemCode,
        //                         item.itemName,
        //                         types.goodTypeName AS material_type,
        //                         SUM(last_closing_quantity) AS total_quantity,
        //                         UOM.uomName AS uom,
        //                         summary.priceType AS valuation_class,
        //                         summary.movingWeightedPrice AS price,
        //                         ROUND(SUM(last_closing_quantity) * summary.movingWeightedPrice, 2) AS total_value
        //                         -- summary.updatedAt AS last_received_on
        //                     FROM (
        //                         SELECT 
        //                             report.item_id,
        //                             report.storage_id,
        //                             SUM(report.total_closing_qty) AS last_closing_quantity
        //                         FROM 
        //                             erp_inventory_stocks_log_report AS report
        //                         INNER JOIN (
        //                             SELECT 
        //                                 item_id,
        //                                 storage_id,
        //                                 MAX(report_date) AS max_date
        //                             FROM 
        //                                 erp_inventory_stocks_log_report
        //                             WHERE
        //                                 report_date <= '" . $dateAson . "' AND company_id = $company_id AND branch_id = $branch_id AND location_id = $location_id 
        //                             GROUP BY 
        //                                 item_id, storage_id
        //                         ) AS max_dates
        //                         ON 
        //                             report.item_id = max_dates.item_id
        //                             AND report.storage_id = max_dates.storage_id
        //                             AND report.report_date = max_dates.max_date
        //                         GROUP BY 
        //                             report.item_id, report.storage_id
        //                     ) AS last_closing_quantities
        //                     LEFT JOIN erp_inventory_items AS item ON last_closing_quantities.item_id = item.itemId
        //                     LEFT JOIN erp_inventory_mstr_good_types AS types ON item.goodsType = types.goodTypeId
        //                     LEFT JOIN erp_inventory_stocks_summary AS summary ON item.itemId = summary.itemId 
        //                     LEFT JOIN erp_inventory_mstr_uom AS UOM ON item.baseUnitMeasure = UOM.uomId
        //                     WHERE
        //                         item.company_id = $company_id  " . $cond . "
        //                     GROUP BY 
        //                         item_id";

        $goodsType = $_POST['goodsType'];
        $goodCond = '';

        if ($goodsType == 1) {

            $goodCond = "TYPES.goodTypeName LIKE '%FG%' OR TYPES.goodTypeName = 'Finished Good'";
        } elseif ($goodsType == 2) {
            $goodCond = "TYPES.goodTypeName LIKE '%SFG%' OR TYPES.goodTypeName = 'Semi Finished Good'";
        } elseif ($goodsType == 3) {
            $goodCond = "TYPES.goodTypeName LIKE '%RM%' OR TYPES.goodTypeName = 'Raw Material'";
        }

        $goodsCondition = ($goodsType > 0) ? "AND ($goodCond)" : "";

       $sql_list = "SELECT item.itemId, item.itemCode, item.itemName, TYPES.goodTypeName AS material_type, COALESCE(SUM(TRUNCATE(LOG.itemQty, $decimalQuantity)), 0) AS total_quantity, UOM.uomName AS uom, summary.priceType AS valuation_class, summary.movingWeightedPrice AS price,COALESCE(SUM(TRUNCATE(LOG.itemQty,$decimalValue)), 0) * summary.movingWeightedPrice AS total_value, item.createdBy FROM erp_inventory_items AS item LEFT JOIN erp_inventory_stocks_log AS LOG ON LOG.itemId = item.itemId AND LOG.postingDate <= '" . $dateAson . "' LEFT JOIN erp_inventory_mstr_good_types AS TYPES ON item.goodsType = TYPES.goodTypeId AND item.goodsType != 5 LEFT JOIN erp_inventory_stocks_summary AS summary ON item.itemId = summary.itemId LEFT JOIN erp_inventory_mstr_uom AS UOM ON item.baseUnitMeasure = UOM.uomId WHERE item.company_id = $company_id AND summary.company_id = $company_id AND item.goodsType != 5 AND LOG.companyId = $company_id" . $cond . $goodsCondtion . " GROUP BY item.itemId";



        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $dbObj = new Database();

        $dbObj->queryUpdate("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))", true);
        $sqlMainQryObj = $dbObj->queryGet($sql_Mainqry, true);

        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        if ($num_list > 0) {
            foreach ($sql_data as $data) {
                $itemId = $data['itemId'];
                // $total_qty_sql = queryGet("SELECT * FROM `erp_inventory_stocks_log_report` WHERE  `item_Id` = $itemId AND `location_id` = $location_id AND `branch_id` = $branch_id AND `company_id` = $company_id AND DATE_FORMAT(report_date, '%Y %m %d') <= DATE_FORMAT('" . $date . "', '%Y %m %d') ORDER BY `report_id` DESC");
                // $total_qty = $total_qty_sql['data']['total_closing_qty'] ?? 0;

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "item.itemCode" => $data['itemCode'],
                    "itemId" => $data['itemId'],
                    "item.itemName" => $data['itemName'],
                    "TYPES.goodTypeName" => $data['material_type'],
                    "total_quantity" => decimalQuantityPreview($data['total_quantity']),
                    "UOM.uomName" => $data['uom'],
                    "summary.priceType" => $data['valuation_class'],
                    "summary.movingWeightedPrice" => decimalValuePreview($data['price']),
                    "total_value" => decimalValuePreview($data['total_value']),
                    "item.createdBy" => getCreatedByUser($data['createdBy']),
                    // "last_received_on" => $data['last_received_on']

                ];
                $sl++;
            }
            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";

            $queryset = $dbObj->queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);
            $output .= pagiNation($page_no, $total_page);

            $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';

            // $csvContentall = exportToExcelAll("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY','')); " . $sql_list . "", json_encode($columnMapping));
            // $csvContentByPagination = exportToExcelByPagin("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY','')); " . $sql_Mainqry . "", json_encode($columnMapping));



            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                // "csvContent" => $csvContentall,
                // "csvContentByPagination" => $csvContentByPagination,
                "sqlMain" => $sql_list,
                "goodsType" => $goodsCondition

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sqlMainQryObj,

            ];
        }

        echo json_encode($res);
    }
}
if ($_POST['act'] == 'alldata') {
    $formObj = $_POST['formDatas'];
        $cond = "";
        $ddate = "";

        $implodeFrom = implode('', array_map(function ($slag, $data) use (&$ddate) {
            $conds = "";
            if ($slag === 'report_date') {
                $date =  $data['value'];
                $ddate = $date;
                $conds .= "";
            } elseif ($slag === 'last_closing_quantity' || $slag === 'summary.movingWeightedPrice') {
                $conds .= $slag . " " . $data['operatorName'] . "" . $data['value'] . "";
            } else if ($slag === 'item.createdBy') {

                $resultList = getAdminUserIdByName($data['value']);
                $conds .= $slag . " IN  " . " (" . $resultList . ")";
            } else {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }

            return (!empty($data['value']) && $conds != "") ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        $dateAson = ($ddate != "") ? $ddate : $currentDate;
        $goodsType = $_POST['goodsType'];
        $goodCond = '';

        if ($goodsType == 1) {

            $goodCond = "TYPES.goodTypeName LIKE '%FG%' OR TYPES.goodTypeName = 'Finished Good'";
        } elseif ($goodsType == 2) {
            $goodCond = "TYPES.goodTypeName LIKE '%SFG%' OR TYPES.goodTypeName = 'Semi Finished Good'";
        } elseif ($goodsType == 3) {
            $goodCond = "TYPES.goodTypeName LIKE '%RM%' OR TYPES.goodTypeName = 'Raw Material'";
        }

        $goodsCondition = ($goodsType > 0) ? "AND ($goodCond)" : "";

         $sql_list = "SELECT item.itemId, item.itemCode, item.itemName, TYPES.goodTypeName AS material_type, COALESCE(SUM(TRUNCATE(LOG.itemQty, $decimalQuantity)), 0) AS total_quantity, UOM.uomName AS uom, summary.priceType AS valuation_class, summary.movingWeightedPrice AS price,(COALESCE(SUM(TRUNCATE(LOG.itemQty,$decimalValue)), 0) * summary.movingWeightedPrice AS total_value, item.createdBy FROM erp_inventory_items AS item LEFT JOIN erp_inventory_stocks_log AS LOG ON LOG.itemId = item.itemId AND LOG.postingDate <= '" . $dateAson . "' LEFT JOIN erp_inventory_mstr_good_types AS TYPES ON item.goodsType = TYPES.goodTypeId AND item.goodsType != 5 LEFT JOIN erp_inventory_stocks_summary AS summary ON item.itemId = summary.itemId LEFT JOIN erp_inventory_mstr_uom AS UOM ON item.baseUnitMeasure = UOM.uomId WHERE item.company_id = $company_id AND summary.company_id = $company_id AND item.goodsType != 5 AND LOG.companyId = $company_id" . $cond . $goodsCondtion . " GROUP BY item.itemId";
    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        foreach ($sql_data_all as $data) {
            $dynamic_data_all[] = [
                "sl_no" => $sl,
                "item.itemCode" => $data['itemCode'],
                "itemId" => $data['itemId'],
                "item.itemName" => $data['itemName'],
                "TYPES.goodTypeName" => $data['material_type'],
                "total_quantity" => decimalQuantityPreview($data['total_quantity']),
                "UOM.uomName" => $data['uom'],
                "summary.priceType" => $data['valuation_class'],
                "summary.movingWeightedPrice" => decimalValuePreview($data['price']),
                "total_value" => decimalValuePreview($data['total_value']),
                "item.createdBy" => getCreatedByUser($data['createdBy']),
                // "last_received_on" => $data['last_received_on']
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
