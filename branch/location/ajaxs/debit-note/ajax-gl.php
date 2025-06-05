<?php

require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_GET['act'] === "gl") {
    $val = $_GET['val'];
    $itemarry = explode('_', $val);
    if (count($itemarry) < 2) {

        $gl_sql = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND glStType='account'  AND `status`!='deleted' ORDER BY gl_code", true);

        $responseData['glhtml'] = "<option value='' selected >Select Account</option>";

        foreach ($gl_sql['data'] as $data) {
            $responseData['glhtml'] .= "<option value=" . $data['id'] . " >" . $data['gl_code'] . "|" . $data['gl_label'] . "</option>";
        }

        $responseData['taxPercentage'] = 0;
    } else {

        $item_id = $itemarry[0];


        // $gl_sql = queryGet("SELECT * FROM `erp_inventory_items` as item , `" . ERP_ACC_CHART_OF_ACCOUNTS . "` as account WHERE item.`parentGlId` = account.`id` AND  item.`itemId` = $item_id",true);

        $gl_sql = queryGet("SELECT item.*, COALESCE(hsn.taxPercentage, 0) AS taxPercentage,account.gl_code,account.gl_label,account.id
    FROM erp_inventory_items AS item
    INNER JOIN " . ERP_ACC_CHART_OF_ACCOUNTS . " AS account ON item.parentGlId = account.id
    LEFT JOIN erp_hsn_code AS hsn ON item.hsnCode = hsn.hsnCode
    WHERE item.itemId = $item_id", true);

        // console($gl_sql);  
        foreach ($gl_sql['data'] as $data) {
            $responseData['glhtml'] .= "<option value=" . $data['id'] . " >" . $data['gl_code'] . "|" . $data['gl_label'] . "</option>";
            $responseData['taxPercentage'] = $data["taxPercentage"];
        }
    }
}

echo json_encode($responseData);
