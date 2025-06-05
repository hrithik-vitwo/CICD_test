<?php

require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../pagination/common-pagination.php");
$currentDate = date('Y-m-d');
$returnResponse = [];

if (isset($_GET['act']) && $_GET['act'] == 'stock-detail') {
    $itemId = $_GET['itemId'];
    $dateAson = $_GET['dDate'];

    $condstock = " AND DATE_FORMAT(bornDate, '%Y %m %d') <= DATE_FORMAT('" . $dateAson . "', '%Y %m %d')";

    $stock_log_sql = "SELECT
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
        " . $condstock . ";";


    $query = queryGet($stock_log_sql);

    if ($query['status'] == 'success') {
        $returnResponse = $query['data'];
    } else {
        $returnResponse = [
            "status" => "error",
            "message" => "Failed to fetch stock details",
            "data" => []
        ];
    }
} else if (isset($_GET['act']) && $_GET['act'] == 'stocklog') {

    $itemId = $_GET['itemId'];

    $returnResponse = [];
    $limit_per_Page = isset($_GET['maxlimit']) && $_GET['maxlimit'] != '' ? $_GET['maxlimit'] : 25;
    $page_no = isset($_GET['page_id']) ? (int)$_GET['page_id'] : 1;
    $page_no = max(1, $page_no);

    $offset = ($page_no - 1) * $limit_per_Page;
    $maxPagesl = $page_no * $limit_per_Page;
    $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

    $output = "";
    $limitText = "";

    $sl =   ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
    $query = "SELECT * FROM `erp_inventory_stocks_log` WHERE `itemId` ='" . $itemId . "'";

    $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . ";";
    $sqlMainQryObj = queryGet($sql_Mainqry, true);
    $output .= "</table>";
    $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery;";
    $queryset = queryGet($sqlRowCount);
    $totalRows = $queryset['data']['row_count'];
    $total_page = ceil($totalRows / $limit_per_Page);
    $numRows = $sqlMainQryObj['numRows'];

    $output .= pagiNationinnerTable($page_no, $total_page);

    $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';
 
    foreach ($sqlMainQryObj['data'] as $data) {
        $dynamic_data[] = [
            "storageType" => $data['storageType'],
            "itemQty" => $data['itemQty'],
            "itemUom" => $data['itemUom'],
            "itemPrice" => $data['itemPrice'],
            "logRef" => $data['logRef'],
            "min_stock" => $data['min_stock'],
            "max_stock" => $data['max_stock'],
            "createdBy" => getCreatedByUser($data['createdBy']),
            "createdAt" => formatDateORDateTime($data['createdAt'])
        ];
    }

    if ($numRows > 0) {

        $returnResponse = [
            "status" => "success",
            "message" => "data found",
            "data" => $dynamic_data,
            "limit_per_Page" => $limit_per_Page,
            "pagination" => $output,
            "limitTxt" => $limitText
        ];
    } else {
        $returnResponse = [
            "status" => "warring",
            "message" => "no data found",
            "sql" => $sqlMainQryObj,
            "data" => [],
        ];
    }
} else {
    $returnResponse = [
        "status" => "error",
        "message" => "Invalid request",
        "data" => []
    ];
}

echo json_encode($returnResponse);
