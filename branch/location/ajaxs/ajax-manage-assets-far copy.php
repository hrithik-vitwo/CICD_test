<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../app/v1/functions/branch/func-bom-controller.php");
require_once("pagination/common-pagination.php");
require_once("../../common/exportexcel.php");

function exportToExcelAllA($dynamic_data, $column)
{
    $columnMapping = json_decode($column, true);
    $slags = [];
    $sqlcond = "";
    $dbObj = new Database();

    // Mapping the columns from the JSON configuration
    foreach ($columnMapping as $col) {
        if ($col['slag'] !== 'sl_no') {
            $slags[] = $col['slag'];
        }
    }

    // Prepare the CSV headers
    ob_start();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="data_export.csv"');

    $output = fopen('php://output', 'w');

    // Writing the header row for CSV
    $headerRow = [];
    foreach ($columnMapping as $column) {
        $headerRow[] = $column['name'];
    }
    fputcsv($output, $headerRow);

    // Adding the data rows to CSV
    $sl_no = 0;
    foreach ($dynamic_data as $data) {
        $sl_no++;
        // Prepending the serial number (sl_no) to the row
        array_unshift($data, $sl_no);
        fputcsv($output, $data);
    }

    fclose($output);
    $csvContent = ob_get_clean();

    return $csvContent;
}

$headerData = array('Content-Type: application/json');

$goodsController = new GoodsController();
$goodsBomController = new GoodsBomController();
$currentDate = date('Y-m-d');
$timestampPreviousDay = strtotime($fromd . ' -1 day');
$previousDate = date('Y-m-d', $timestampPreviousDay);

if ($_POST['act'] == 'tdata') {
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

        // $sql_list = "SELECT * FROM  `erp_inventory_items` AS `items`  WHERE `goodsType` = 9 AND `company_id` = $company_id ORDER BY itemId DESC";
        // $sql_list = "SELECT * FROM `erp_asset_use` AS `uses` LEFT JOIN `erp_inventory_items` AS `items` ON items.itemId = uses.asset_id WHERE 1 AND items.`goodsType` = 9 AND items.`company_id` = $company_id ORDER BY use_asset_id DESC";
        $sql_list = "
    SELECT 
        items.itemCode,
        items.itemName,
        items.parentGlId,
        items.itemDesc,
        items.itemId,
        str_loc.storage_location_name AS storage_location,
        LOG.logRef,
        LOG.refNumber,
        UOM.uomName AS uom,
        LOG.refActivityName AS movement_type,
        LOG.itemQty AS qty,
        str_loc.storage_location_name,
        LOG.postingDate AS postingDate,
        LOG.itemPrice * LOG.itemQty AS VALUE,
        LOG.itemPrice AS rate,
        gl.gl_label AS gl_name,
        gl.gl_code,
        cc.CostCenter_desc AS cost_center_desc,
        cc.CostCenter_code AS cost_center_code,
        grn.vendorDocumentNo AS grn_doc_no,
        grn.postingDate AS grn_date,
        grn.grnPoNumber AS inv_no,
        grn.po_date AS inv_date,
        grn.vendorName AS vendor_name,
        grn.vendorGstin AS vendor_gst,
        grn.vendorGstinStateName AS vendor_address,
        total_qty.itemQty AS total_qty,
        dep.asset_life AS usefule_life,
        dep.dep_rate,
        dep.dep_method,
        dep.depreciation_on_value AS wdv,
        dep.last_depreciation_date AS lst_run_dep,
        (dep.asset_value - dep.depreciation_date) AS accumulated
    FROM
        erp_inventory_stocks_log AS LOG
    LEFT JOIN erp_inventory_items AS items
        ON LOG.itemId = items.itemId
    LEFT JOIN erp_inventory_mstr_uom AS UOM
        ON LOG.itemUom = UOM.uomId
    LEFT JOIN erp_storage_location AS str_loc
        ON LOG.storageLocationId = str_loc.storage_location_id
    LEFT JOIN erp_branch_otherslocation AS loc 
        ON LOG.locationId = loc.othersLocation_id
    LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS gl
        ON items.parentGlId = gl.id
    LEFT JOIN erp_cost_center AS cc
        ON cc.CostCenter_id = items.cost_center_id
    LEFT JOIN erp_grn AS grn
        ON LOG.logRef = grn.grnCode
    LEFT JOIN erp_asset_use AS uses
        ON uses.asset_id = items.itemId
    LEFT JOIN erp_asset_depreciation AS dep
        ON dep.asset_id = items.itemId AND dep.asset_use_id = uses.use_asset_id
    LEFT JOIN (
        SELECT logRef, SUM(itemQty) AS itemQty 
        FROM erp_inventory_stocks_log 
        WHERE companyId = $company_id
        GROUP BY logRef
    ) AS total_qty
        ON LOG.logRef = total_qty.logRef
    WHERE
        LOG.companyId = $company_id
        AND items.goodsType = 9
    ORDER BY
        LOG.stockLogId DESC
    LIMIT $offset, $limit_per_Page;
";


        // Execute the main query to fetch paginated data
        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);
        console($sqlMainQryObj);

        // Execute the full query without pagination to get total number of rows
        $sqlMainQryObj1 = queryGet($sql_list, true);

        // Extract the query results
        $sql_data1 = $sqlMainQryObj1['data'];
        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];

        $output = "";
        $limitText = "";
        $sl = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        if ($num_list > 0) {
            foreach ($sql_data as $data) {
                // You already have all the necessary data here, no need for additional queries

                $dynamic_data[] = [
                    "sl_no" => $sl,
                    "gl_name" => $data['gl_name'],
                    "gl_code" => $data['gl_code'],
                    "itemCode" => $data['itemCode'],
                    "itemName" => $data['itemName'],
                    "itemDesc" => $data['itemDesc'],
                    "batch_no" => $data['logRef'],
                    'storage_loc' => $data['storage_location'],
                    "cost_center" => isset($data['cost_center_desc'])
                        ? $data['cost_center_desc'] . '(' . $data['cost_center_code'] . ')'
                        : '',
                    "grn_doc_no" => $data['grn_doc_no'],
                    "grn_date" => $data['grn_date'],
                    "inv_no" => $data['inv_no'],
                    "inv_date" => $data['inv_date'],
                    "vendor_name" => $data['vendor_name'],
                    "vendor_gst" => $data['vendor_gst'],
                    "vendor_address" => $data['vendor_address'],
                    "qty" => helperQuantity($data['total_qty']),
                    "uom" => $data['uom'],
                    "basic_value" => helperAmount($data['grnSubTotal']),
                    "total_gst" => helperAmount($data['totalTax']),
                    "total_with_gst" => helperAmount($data['grnSubTotal'] + $data['totalTax']),
                    "created_at" => $data['use_date'],
                    "rate" => $data['total_value'],
                    "usefule_life" => $data['usefule_life'],
                    "dep_rate" => $data['dep_rate'],
                    "dep_method" => strtoupper($data['dep_method']),
                    "wdv" => $data['wdv'],
                    "lst_wdv" => $data['wdv'],
                    "lst_run_dep" => $data['lst_run_dep'],
                    "accumulated" => $data['accumulated'],
                ];
                $sl++;
            }
            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            
            $total_page = ceil($totalRows / $limit_per_Page);
      
            $output .= pagiNation($page_no, $total_page);


            // foreach ($sql_data1 as $data) {
            //     $itemId = $data['itemId'];
            //     $useid = $data['refNumber'];
            //     $sql_list1 = queryGet("SELECT * FROM `erp_asset_use` AS `uses` LEFT JOIN `erp_inventory_items` AS `items` ON items.itemId = uses.asset_id WHERE uses.asset_id='" . $itemId . "' AND items.`goodsType` = 9 AND items.`company_id` = $company_id AND uses.use_asset_id=$useid")['data'];


            //     $itemCode = $data['itemCode'];
            //     $itemName = $data['itemName'];
            //     $netWeight = $data['netWeight'];
            //     $volume = $data['volume'];
            //     $goodsType = $data['goodsType'];
            //     $grossWeight = $data['grossWeight'];
            //     $buom_id = $data['baseUnitMeasure'];
            //     $prentGl = $data['parentGlId'];

            //     $gldetails = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND id=$prentGl");
            //     $gl_name = $gldetails['data']['gl_label'];
            //     $gl_code = $gldetails['data']['gl_code'];
            //     $costcenter_id = $sql_list1['cost_center_id'];
            //     $costcenter = queryGet("SELECT `CostCenter_code`,`CostCenter_desc` FROM `erp_cost_center` WHERE `CostCenter_id`=$costcenter_id AND `company_id`=$company_id AND `branch_id`=$branch_id");

            //     $batch_no = $data['logRef'];
            //     $assetused_id = $sql_list1['use_asset_id'];
            //     $grn_details = queryGet("SELECT * FROM erp_grn WHERE grnCode='" . $batch_no . "'");

            //     $totalTax = array_sum(array_column(json_decode($grn_details['data']['taxComponents'], true), 'taxAmount'));

            //     $depr = queryGet("SELECT * from erp_asset_depreciation WHERE asset_id='" . $itemId . "' AND asset_use_id='" . $assetused_id . "' ORDER BY asset_depreciation_id DESC LIMIT 1")['data'];

            //     $dep_key = $sql_list1['dep_key'];
            //     $depkey = queryGet("SELECT * from erp_depreciation_table where company_id='" . $company_id . "' AND desp_key='" . $dep_key . "'")['data'];
            //     $method = $depr['method'];

            //     $totalqty = queryGet("SELECT `itemQty` from `erp_inventory_stocks_log` WHERE companyId='" . $company_id . "'  AND `branchId`=$branch_id  AND `locationId`=$location_id AND logRef='" . $batch_no . "' AND refActivityName='GRN'");

            //     $dynamic_data1[] = [
            //         "sl_no" => $sl,
            //         "gl_name" => $gl_name,
            //         "gl_code" => $gl_code,
            //         "itemCode" => $data['itemCode'],
            //         "itemName" => $data['itemName'],
            //         "itemDesc" => $data['itemDesc'],
            //         "batch_no" => $data['logRef'],
            //         'storage_loc' => $data['storage_location_name'],
            //         "cost_center" => isset($costcenter['data']['CostCenter_desc'])
            //             ? $costcenter['data']['CostCenter_desc'] . '(' . $costcenter['data']['CostCenter_code'] . ')'
            //             : '',
            //         "grn_doc_no" => $grn_details['data']['vendorDocumentNo'],
            //         "grn_date" => $grn_details['data']['postingDate'],
            //         "inv_no" => $grn_details['data']['grnPoNumber'],
            //         "inv_date" => $grn_details['data']['po_date'],
            //         "vendor_name" => $grn_details['data']['vendorName'],
            //         "vendor_gst" => $grn_details['data']['vendorGstin'],
            //         "vendor_address" => $grn_details['data']['vendorGstinStateName'],
            //         "qty" => helperQuantity($totalqty['data']['itemQty']),
            //         "uom" => $data['uom'],
            //         "basic_value" => helperAmount($grn_details['data']['grnSubTotal']),
            //         "total_gst" => helperAmount($totalTax),
            //         "total_with_gst" => helperAmount($grn_details['data']['grnSubTotal'] + $totalTax),
            //         "created_at" => $sql_list1['use_date'],
            //         "rate" => $sql_list1['total_value'],
            //         "usefule_life" => $depkey['asset_life'],
            //         "dep_rate" => $depkey[$method],
            //         "dep_method" => strtoupper($depr['method']),
            //         "wdv" => $depr['depreciation_on_value'],
            //         "lst_wdv" => $depr['depreciation_on_value'],
            //         "lst_run_dep" => $depr['depreciation_date'],
            //         "accumulated" => $depr['asset_value'] - $depr['depreciation_date'],


            //     ];
            //     $sl++;
            // }
            $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';
            // $csvContent = exportToExcelAllA($dynamic_data1, json_encode($columnMapping));
            // $csvContentBypagination = exportToExcelAllA($dynamic_data, json_encode($columnMapping));

            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sqlMain" => $sqlMainQryObj,
                // "csvContent" => $csvContent,
                // "csvContentBypagination" => $csvContentBypagination


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
