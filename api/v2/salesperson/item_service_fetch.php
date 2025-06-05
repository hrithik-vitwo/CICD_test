<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $company_id = $authCustomer['company_id'];
    
    $requestBody = requestBody();
    $branch_id = $requestBody['branch_id'];
    $location_id = $requestBody['location_id'];

    $sql_list = "SELECT summary.*,items.*,hsn.taxPercentage
    FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as summary
    INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId=items.itemId
    RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode
    WHERE summary.company_id='$company_id' AND summary.branch_id = '$branch_id' AND summary.location_id = '$location_id' AND items.goodsType=5 AND summary.status = 'active' AND items.status = 'active'";
    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];
        
        $data_array = [];
        foreach ($iv_data as $data) {
            
            $data_array[] = array("items" => $data);
        }
        // console($data_array);
        sendApiResponse([
            "status" => $iv_sql['status'],
            "message" => $iv_sql['message'],
            "data" => $iv_sql['data']

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No not found",
            "data" => [],
            "sql" => $sql_list

        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
//echo "ok";