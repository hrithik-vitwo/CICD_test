<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("pagination/common-pagination.php");



if (isset($_GET['act']) && $_GET['act'] == 'stocklog') {
    $itemId = $_GET['itemId'];
    $ddate = $_GET['ddate'];
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

    $query = " SELECT               
                                                loc.othersLocation_name AS location,
                                                LOG.refNumber AS document_no,
                                                items.itemCode,
                                                items.itemName,
                                                grp.goodGroupName AS itemGroup,
                                                str_loc.storage_location_name AS storage_location,
                                                LOG.logRef,
                                                UOM.uomName AS uom,
                                                LOG.refActivityName AS movement_type,
                                                LOG.itemQty AS qty,
                                                LOG.itemPrice AS rate,
                                                LOG.postingDate as postingDate,
                                                LOG.itemPrice * LOG.itemQty AS
                                            VALUE
                                            FROM
                                                erp_inventory_stocks_log AS LOG
                                            LEFT JOIN erp_inventory_items AS items
                                            ON
                                                LOG.itemId = items.itemId
                                            LEFT JOIN erp_inventory_mstr_uom AS UOM
                                            ON
                                                LOG.itemUom = UOM.uomId
                                            LEFT JOIN erp_storage_location AS str_loc
                                            ON
                                                LOG.storageLocationId = str_loc.storage_location_id
                                           LEFT JOIN erp_branch_otherslocation AS loc 
                                           ON LOG.locationId = loc.othersLocation_id
                                            LEFT JOIN erp_inventory_mstr_good_groups AS grp
                                            ON
                                                items.goodsGroup = grp.goodGroupId
                                            WHERE
                                                items.itemId = '" . $itemId . "' AND LOG.companyId = $company_id AND LOG.branchId = $branch_id AND LOG.locationId = $location_id AND DATE(LOG.postingDate) <= '" . $ddate . "'
                                            ORDER BY
                                                LOG.stockLogId
                                            DESC";

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
    $sl=1;
    foreach ($sqlMainQryObj['data'] as $data) {
        $dynamic_data[] = [
            "sl_no" => $sl,
            "location" => $data['location'],
            "postingdate" => $data['postingDate'],
            "document_no" => $data['document_no'],
            "itemGroup" => $data['itemGroup'],
            "itemCode" =>  $data['itemCode'],
            "itemName" => $data['itemName'],
            "storage_location" =>  $data['storage_location'],
            "party_code" => $data['party_code']??"-",
            "party_name"=>$data['party_name']??"-",
            "batchNo"=>$data['logRef'],
            "uom"=>$data['uom'],
            "movement_type"=>$data['movement_type'],
            "qty"=>$data['qty'],
            "VALUE"=>$data['VALUE'],
            "currency"=>getSingleCurrencyType($company_currency)
          ];
          $sl++;

    }

    if ($numRows > 0) {
        
        $returnResponse = [
            "status" => "success",
            "message" => "data found",
            "numRows" => $query['numRows'],
            "data" => $dynamic_data,
            "limit_per_Page" => $limit_per_Page,
            "pagination" => $output,
            "limitTxt" => $limitText,
            "sql" => $query
        ];
    } else {
        $returnResponse = [
            "status" => "warring",
            "message" => "no data found",
            "sql" => $sqlMainQryObj,
            "data" => [],
        ];
    }
    echo json_encode($returnResponse);
} else {
    echo json_encode([
        "status" => "Error",
        "message" => "Something went wrong try again!"
    ]);
}
