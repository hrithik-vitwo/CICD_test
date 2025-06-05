<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
$headerData = array('Content-Type: application/json');

$dbObj=new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "modalData") {

    $id = $_GET['prodId'];
    $cond="AND  pOrder.so_por_id=$id";
    $sts = " AND pOrder.status !='deleted'";
    $sql_list = "SELECT pOrder.so_por_id, pOrder.porCode, pOrder.refNo, pOrder.mrp_code, pOrder.qty, pOrder.remainQty, pOrder.expectedDate, pOrder.created_at, pOrder.created_by,pOrder.updated_by,pOrder.updated_at,pOrder.status,pOrder.mrp_status,pOrder.validityperiod,items.itemId,items.itemName,items.itemCode,items.itemDesc,items.goodsType,items.itemOpenStocks,items.itemBlockStocks,goodTypes.goodTypeName, goodTypes.type AS goodTypeShortName,bomName.uom FROM `erp_production_order` AS pOrder LEFT JOIN `erp_inventory_items` AS items ON pOrder.`itemId`=items.`itemId` LEFT JOIN `erp_inventory_mstr_good_types` AS goodTypes ON items.goodsType=goodTypes.goodTypeId LEFT JOIN `erp_bom_item_material` AS bomName ON items.`itemId`=bomName.`item_id` WHERE 1 " . $cond . "  AND pOrder.company_id='" . $company_id . "'  AND pOrder.branch_id='" . $branch_id . "'   AND pOrder.location_id='" . $location_id . "' " . $sts . " ORDER BY pOrder.so_por_id DESC";
    $sqlMainQryObj = $dbObj->queryGet($sql_list);

    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];

        $itemQtyStockCheckingObj = itemQtyTotalStockChecking($data["itemId"], '"rmWhOpen","fgWhOpen","sfgStockOpen"');
        $itemStockQty = $itemQtyStockCheckingObj["data"]["itemQty"] ?? 0;

        
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
            "itemStockQty"=>$itemStockQty,
            "max"=>$max,
            "min"=>$min,
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
}