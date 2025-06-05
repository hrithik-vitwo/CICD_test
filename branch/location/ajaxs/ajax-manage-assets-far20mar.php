<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../app/v1/functions/branch/func-bom-controller.php");
require_once("pagination/common-pagination.php");
require_once("../../common/exportexcel-new.php");

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
            $clauseType = "HAVING ";
            if ($slag === 'grn_postingDate' || $slag === 'po_date' || $slag === 'use_date' || $slag === 'posting_date') {
                if ($data['operatorName'] === 'BETWEEN') {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value']['fromDate'] . '" AND "' . $data['value']['toDate'] . '"';
                } else {
                    $conds .= "DATE(" . $slag . ")" . $data['operatorName'] . ' "' . $data['value'] . '"';
                }
            }else if($slag=="total_value" || $slag== "dep_rate" || $slag== "asset_life" || $slag== "grnSubTotal" || $slag== "depreciation_value" || $slag== "depreciation_on_value" || $slag== "total_accu")
            {
                $conds .= $slag . " " . $data['operatorName'] . " " . $data['value'] . "";        
                 $clauseType="HAVING ";
            }
             else {
                $clauseType = "HAVING ";
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }
            

            return !empty($data['value']) ? $clauseType  . $conds : "";
        }, array_keys($formObj), $formObj));

        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }


        $sql_list = " 
SELECT 
    items.itemCode AS itemCode,
    items.itemName AS itemName,
    items.parentGlId,
    gl.gl_label AS gl_name,
    gl.gl_code AS gl_code,
    items.itemDesc AS itemDesc,
    items.itemId,
    str_loc.storage_location_name AS storage_location,
    LOG.logRef AS logRef,
    LOG.refNumber,
    UOM.uomName AS uom,
    LOG.refActivityName AS movement_type,
    LOG.itemQty AS qty,
    str_loc.storage_location_name AS storage_location_name,
    LOG.postingDate AS postingDate,
    LOG.itemPrice * LOG.itemQty AS VALUE,
    LOG.itemPrice AS rate,
    equip.equip_no AS equip_no,
    equip.batch_no,
    equip.puttouse_id,
    LOG.stockLogId AS sort_order,
    equip.equip_no AS equip_sort_order,
    uses.use_date AS use_date,
    uses.qty AS use_qty,
    COALESCE(uses.total_value / NULLIF(uses.qty, 0), 0) AS total_value,
    items.dep_key AS dep_key,
    uses.use_asset_id AS use_asset_id,
    uses.cost_center_id AS cost_center_id,
    cost.CostCenter_code AS CostCenter_code,
    cost.CostCenter_desc AS CostCenter_desc,
    grn.taxComponents,
    grn.vendorDocumentNo AS vendorDocumentNo,
    grn.postingDate AS grn_postingDate,
    grn.grnPoNumber AS grnPoNumber,
    grn.po_date AS po_date,
    grn.vendorName AS vendorName,
    grn.vendorGstin AS vendorGstin,
    grn.vendorGstinStateName AS vendorGstinStateName,
    grn.grnSubTotal AS grnSubTotal,
    COALESCE(total_log.total_qty, 0) AS total_qty,
    COALESCE((depreciation.asset_value-depreciation.depreciation_value) / NULLIF(uses.qty, 0), 0) AS total_accu,
    depreciation.method,
    COALESCE(depreciation.depreciation_value / NULLIF(uses.qty, 0), 0) AS depreciation_value,
    COALESCE(depreciation.depreciation_on_value / NULLIF(uses.qty, 0), 0) AS depreciation_on_value,
    depreciation.posting_date AS posting_date,
    dep_table.wdv AS wdv_value,
    dep_table.slm AS slm_value,
    dep_table.asset_life AS asset_life,
    CASE 
        WHEN depreciation.method = 'WDV' THEN dep_table.wdv
        WHEN depreciation.method = 'SLM' THEN dep_table.slm
        ELSE 0
    END AS dep_rate
FROM erp_inventory_stocks_log AS LOG
LEFT JOIN erp_inventory_items AS items ON LOG.itemId = items.itemId
LEFT JOIN erp_inventory_mstr_uom AS UOM ON LOG.itemUom = UOM.uomId
LEFT JOIN erp_storage_location AS str_loc ON LOG.storageLocationId = str_loc.storage_location_id
LEFT JOIN " . ERP_ACC_CHART_OF_ACCOUNTS . " AS gl ON items.parentGlId = gl.id AND gl.company_id = $company_id
LEFT JOIN erp_asset_use AS uses ON uses.asset_id = LOG.itemId 
    AND uses.use_asset_id = LOG.refNumber
    AND items.goodsType = 9
    AND items.company_id = $company_id
    AND items.branch = $branch_id
    AND items.location_id = $location_id
LEFT JOIN erp_cost_center AS cost ON cost.CostCenter_id = uses.cost_center_id
    AND cost.company_id = $company_id
    AND cost.branch_id = $branch_id
    AND cost.location_id = $location_id
LEFT JOIN erp_grn AS grn ON LOG.logRef = grn.grnCode
LEFT JOIN (
    SELECT refNumber, SUM(itemQty) AS total_qty
    FROM erp_inventory_stocks_log
    WHERE companyId = $company_id
        AND branchId = $branch_id
        AND locationId =$location_id
        AND refActivityName = 'GRN'
    GROUP BY refNumber
) AS total_log ON LOG.refNumber = total_log.refNumber
LEFT JOIN (
    SELECT d1.asset_id, 
           d1.asset_use_id, 
           d1.method, 
           d1.depreciation_value, 
           d1.depreciation_on_value, 
           d1.posting_date,
           d1.asset_value,
           d1.depreciated_value AS total_depreciated_value
    FROM erp_asset_depreciation d1
    INNER JOIN (
        SELECT asset_id, asset_use_id, MAX(asset_depreciation_id) AS latest_id
        FROM erp_asset_depreciation
        WHERE company_id = $company_id
            AND branch_id = $branch_id
            AND location_id =$location_id
        GROUP BY asset_id, asset_use_id
    ) d2 
    ON d1.asset_id = d2.asset_id 
    AND d1.asset_use_id = d2.asset_use_id 
    AND d1.asset_depreciation_id = d2.latest_id
) AS depreciation ON LOG.itemId = depreciation.asset_id 
    AND LOG.refNumber = depreciation.asset_use_id
LEFT JOIN erp_depreciation_table AS dep_table ON dep_table.company_id = $company_id
    AND dep_table.desp_key = items.dep_key
LEFT JOIN erp_equip_details AS equip ON LOG.refNumber = equip.puttouse_id 
    AND LOG.refActivityName = 'Put to Use'
WHERE LOG.companyId = $company_id AND LOG.branchId = $branch_id AND LOG.locationId= $location_id 
    AND items.goodsType = 9  $cond
ORDER BY sort_order DESC, equip_sort_order DESC";


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

                $totalTax = array_sum(array_column(json_decode($data['taxComponents'], true), 'taxAmount'));
                $dynamic_data[] = [
                    "sl_no" => $sl ?: "-",
                    "gl_code" => $data['gl_code'] ?: "-",
                    "gl_name" => $data['gl_name'] ?: "-",
                    "itemCode" => $data['itemCode'] ?: "-",
                    "itemName" => $data['itemName'] ?: "-",
                    "itemDesc" => $data['itemDesc'] ?: "-",
                    "logRef" => $data['logRef'] ?: "-",
                    "equip_no" => $data['equip_no'] ?: "-",
                    'storage_location_name' => $data['storage_location_name'] ?: "-",
                    "CostCenter_desc" => isset($data['CostCenter_desc']) ? ($data['CostCenter_desc'] . '(' . $data['CostCenter_code'] . ')') : "-",
                    "vendorDocumentNo" => $data['vendorDocumentNo'] ?: "-",
                    "grn_postingDate" => $data['grn_postingDate'] ?: "-",
                    "grnPoNumber" => $data['grnPoNumber'] ?: "-",
                    "po_date" => $data['po_date'],
                    "vendorName" => $data['vendorName'] ?: "-",
                    "vendorGstin" => $data['vendorGstin'] ?: "-",
                    "vendorGstinStateName" => $data['vendorGstinStateName'] ?: "-",
                    "total_qty" => helperQuantity($data['total_qty']) ?: "-",
                    "uom" => $data['uom'] ?: "-",
                    "grnSubTotal" => helperAmount($data['grnSubTotal']) ?: "-",
                    "total_gst" => helperAmount($totalTax) ?: "-",
                    "total_with_gst" => helperAmount($data['grnSubTotal'] + $totalTax) ?: "-",
                    "total_value" => helperAmount($data['total_value']) ?: "-",
                    "use_date" => $data['use_date'],
                    "asset_life" => $data['asset_life'] ?: "-",
                    "dep_rate" => $data['dep_rate'] ?: "-",
                    "method" => strtoupper($data['method']) ?: "-",
                    "depreciation_value" => helperAmount($data['depreciation_value']) ?: "-",
                    "depreciation_on_value" => helperAmount($data['depreciation_on_value']) ?: "-",
                    "posting_date" => $data['posting_date'],
                    "total_accu" => helperAmount($data['total_accu']) ?: "-"
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
                "sqllist"=>$sql_list,
               
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
    $fromDate = $_POST['fromDate'];
    $toDate = $_POST['toDate'];
    $sql_list = $_POST['sql'];
    $dynamic_data_all = [];
    $sqlMainQryObjall = queryGet($sql_list, true);
    $sql_data_all = $sqlMainQryObjall['data'];
    $num_list =  $sqlMainQryObjall['numRows'];
    if ($num_list > 0) {
        
        foreach ($sql_data_all as $data) {

            $totalTax = array_sum(array_column(json_decode($data['taxComponents'], true), 'taxAmount'));
            $dynamic_data_all[] = [
                "sl_no" => $sl ?: "-",
                "gl_code" => $data['gl_code'] ?: "-",
                "gl_name" => $data['gl_name'] ?: "-",
                "itemCode" => $data['itemCode'] ?: "-",
                "itemName" => $data['itemName'] ?: "-",
                "itemDesc" => $data['itemDesc'] ?: "-",
                "logRef" => $data['logRef'] ?: "-",
                "equip_no" => $data['equip_no'] ?: "-",
                'storage_location_name' => $data['storage_location_name'] ?: "-",
                "CostCenter_desc" => isset($data['CostCenter_desc']) ? ($data['CostCenter_desc'] . '(' . $data['CostCenter_code'] . ')') : "-",
                "vendorDocumentNo" => $data['vendorDocumentNo'] ?: "-",
                "grn_postingDate" => $data['grn_postingDate'] ?: "-",
                "grnPoNumber" => $data['grnPoNumber'] ?: "-",
                "po_date" => $data['po_date'] ?: "-",
                "vendorName" => $data['vendorName'] ?: "-",
                "vendorGstin" => $data['vendorGstin'] ?: "-",
                "vendorGstinStateName" => $data['vendorGstinStateName'] ?: "-",
                "total_qty" => helperQuantity($data['total_qty']) ?: "-",
                "uom" => $data['uom'] ?: "-",
                "grnSubTotal" => helperAmount($data['grnSubTotal']) ?: "-",
                "total_gst" => helperAmount($totalTax) ?: "-",
                "total_with_gst" => helperAmount($data['grnSubTotal'] + $totalTax) ?: "-",
                "total_value" => helperAmount($data['total_value']) ?: "-",
                "use_date" => $data['use_date'] ?: "-",
                "asset_life" => $data['asset_life'] ?: "-",
                "dep_rate" => $data['dep_rate'] ?: "-",
                "method" => strtoupper($data['method']) ?: "-",
                "depreciation_value" => helperAmount($data['depreciation_value']) ?: "-",
                "depreciation_on_value" => helperAmount($data['depreciation_on_value']) ?: "-",
                "posting_date" => $data['posting_date'] ?: "-",
                "total_accu" => helperAmount($data['total_accu']) ?: "-"
            ];
            $sl++;
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
        'csvContentall' => $exportToExcelAll
         // Encoding CSV content to handle safely in JSON
    ]);
}
