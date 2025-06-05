<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
$headerData = array('Content-Type: application/json');

$dbObj = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "modalData") {

    $id = $_GET['sub_prod_id'];
    $cond = "AND  pOrder.sub_prod_id=$id";
    $sts = " AND pOrder.status !='deleted'";

    $sql_list = "SELECT pOrder.prod_id, pOrder.sub_prod_id, pOrder.prodCode, pOrder.expectedDate, pOrder.remainQty, pOrder.prodQty, pOrder.mrp_code, pOrder.mrp_status, pOrder.status, pOrder.created_at, pOrder.created_by,pOrder.updated_at,pOrder.updated_by,pOrder.subProdCode, table_master.table_name, wc.work_center_name, items.itemId, items.itemName, items.itemCode, items.itemDesc, items.goodsType, items.itemOpenStocks, items.itemBlockStocks, goodTypes.goodTypeName, goodTypes.type AS goodTypeShortName, items.baseUnitMeasure AS itemUom FROM `erp_production_order_sub` AS pOrder LEFT JOIN `erp_inventory_items` AS items ON pOrder.`itemId` = items.`itemId` LEFT JOIN `erp_inventory_mstr_good_types` AS goodTypes ON items.goodsType = goodTypes.goodTypeId LEFT JOIN `erp_table_master` AS table_master ON pOrder.table_id = table_master.table_id LEFT JOIN `erp_work_center` AS wc ON wc.work_center_id = pOrder.wc_id WHERE 1  " . $cond . " AND pOrder.`location_id` = $location_id AND pOrder.branch_id=$branch_id AND pOrder.company_id=$company_id ORDER BY sub_prod_id DESC";

    $sqlMainQryObj = $dbObj->queryGet($sql_list);

    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];

        $masterCheckStockLocations = [
            1 => "rmWhOpen",
            2 => "sfgStockOpen",
            "other" => "fgWhOpen"
        ];
        $statusOpenVal = 9;
        $statusReleaseVal = 13;
        $statusCloseVal = 10;
        $stockLocation = $masterCheckStockLocations[$data["goodsType"]] ?? $masterCheckStockLocations["other"];
        $itemQtyStockCheckingObj = itemQtyTotalStockChecking($data["itemId"], "'$stockLocation'");
        $itemStockQty = $itemQtyStockCheckingObj["data"]["itemQty"] ?? 0;
        $releaseStatusName = ($data["status"] == $statusOpenVal) ? "Open" : (($data["status"] == $statusReleaseVal) ? "Release" : "Close");

        $defaultStorageLoc = $dbObj->queryGet("SELECT `default_storage_location` FROM `erp_inventory_stocks_summary` WHERE `itemId` ='" . $data['itemId'] . "' ")['data']['default_storage_location'];

        $variant = $_SESSION['visitBranchAdminInfo']['flAdminVariant'];
        $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
        // console($check_var_sql);
        $check_var_data = $check_var_sql['data'];

        $max = $check_var_data['month_end'];
        $min = $check_var_data['month_start'];

        $dynamic_data = [
            "dataObj" => $data,
            // "currecyNameWords" => number_to_words_indian_rupees($data['total_amt']),
            "created_by" => getCreatedByUser($data['created_by']),
            "created_at" => formatDateORDateTime($data['created_at']),
            "updated_by" => getCreatedByUser($data['updated_by']),
            "updated_at" => formatDateORDateTime($data['updated_at']),
            "companyCurrency" => getSingleCurrencyType($company_currency),
            "itemStockQty" => $itemStockQty,
            "releaseStatusName" => $releaseStatusName,
            "defaultStorageLocId" => $defaultStorageLoc ?? 0,
            "min"=>$min,
            "max"=>$max
        ];

        $res = [
            "status" => true,
            "msg" => "Success",
            "sql" => $sqlMainQryObj['query'],
            "data" => $dynamic_data
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sqlMainQryObj['query']
        ];
    }

    echo json_encode($res);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['act'] == "declarationList") {

    $id = $_GET['sub_prod_id'];
    $sql = "SELECT * FROM `erp_production_declarations` WHERE sub_prod_id=" . $id . " AND location_id=$location_id ORDER BY id DESC";
    $declarationsListObj = $dbObj->queryGet($sql, true);
    $res = [];
    if ($declarationsListObj['numRows'] > 0) {
        $res = [
            "status" => true,
            "msg" => "Success",
            "numRows" => $declarationsListObj['numRows'],
            "data" => $declarationsListObj['data']
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Something went wrong",
        ];
    }
    echo json_encode($res);
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['act'] == "storageLocation") {

    $storageLocationObj = queryGet("SELECT MIN(storage_location_id) as storage_location_id, storageLocationTypeSlug, storage_location_name FROM erp_storage_location WHERE company_id = $company_id AND branch_id = $branch_id AND location_id = $location_id GROUP BY storageLocationTypeSlug, storage_location_name ORDER BY storage_location_id", true);

    echo json_encode($storageLocationObj);
}
