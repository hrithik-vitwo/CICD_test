<?php

include_once("../../../../app/v1/connection-branch-admin.php");


if (isset($_GET["grn"]) && !empty($_GET["grn"])) {
    $grnCode = addslashes($_GET['grn']);
    $grn_item = queryGet("SELECT goodId FROM `erp_grn_goods` WHERE `grnCode` = '{$grnCode}'", true);
    $status = "false";

    if ($grn_item['status'] === "success" && $grn_item['numRows'] > 0) {
        foreach ($grn_item['data'] as $item) {
            if (checkStockLog($item['goodId'], $grnCode) === "false") {
                $status = "true";
                break;
            }
        }
    }

    echo $status;
    exit;
}

function checkStockLog($itemId, $grnCode)
{
    global $company_id, $branch_id, $location_id;

    $itemId = intval($itemId); 
    $grnCodeSafe = addslashes($grnCode);

    $subQuery = "
        SELECT MIN(`stockLogId`) FROM `erp_inventory_stocks_log`
        WHERE `refActivityName` = 'GRN'
          AND `itemId` = {$itemId}
          AND `refNumber` = '{$grnCodeSafe}'
          AND `companyId` = {$company_id}
          AND `locationId` = {$location_id}
          AND `branchId` = {$branch_id}
    ";

    $query = "
        SELECT COUNT(*) AS total FROM `erp_inventory_stocks_log`
        WHERE `itemId` = {$itemId}
          AND `stockLogId` > ({$subQuery})
          AND `companyId` = {$company_id}
          AND `locationId` = {$location_id}
          AND `branchId` = {$branch_id}
    ";
    // console($query);
    $result = queryGet($query);

    return ($result['status'] === "success" && $result['data']['total'] == 0) ? "true" : "false";
}
