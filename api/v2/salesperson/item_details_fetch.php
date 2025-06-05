<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $requestBody = requestBody();
    $itemId = $requestBody['itemId'];

    $sql_list = "SELECT summary.*,items.*,hsn.taxPercentage, hsn.hsnDescription
    FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as summary
    INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId=items.itemId
    RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode
    WHERE summary.company_id='$company_id' AND summary.itemId='$itemId'";
    $iv_sql = queryGet($sql_list);
    $uom = $iv_sql['data']['baseUnitMeasure'];
    $uomName = queryGet("SELECT uomName FROM `".ERP_INVENTORY_MSTR_UOM."` WHERE uomId='".$uom."'")['data']['uomName'];

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];
        $iv_data['uomName'] = $uomName;
        // unset($iv_data['baseUnitMeasure']);

        // console($data_array);
        sendApiResponse([
            "status" => $iv_sql['status'],
            "message" => $iv_sql['message'],
            "data" =>$iv_data

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No not found",
            "data" => []

        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
