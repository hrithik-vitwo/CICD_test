<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $company_id = $authCustomer['company_id'];
    $requestPost = requestBody();
    $branch_id = $requestPost['branch_id'];
    $location_id = $requestPost['location_id'];

    $funcSql = "SELECT * FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE othersLocation_id=$location_id AND company_id=$company_id AND branch_id=$branch_id";
    $funcObj = queryGet($funcSql);
//   console($funcObj['data']['companyFunctionalities']);
      $companyFunctionalities = $funcObj['data']['companyFunctionalities'];
      
      //  console($companyFunctionalities);
    $sql_list = "SELECT * FROM `" . ERP_COMPANY_FUNCTIONALITIES . "` WHERE company_id='" . $company_id . "' AND functionalities_id IN($companyFunctionalities) AND functionalities_status='active'";
    // $sql_list = "SELECT * FROM `" . ERP_COMPANY_FUNCTIONALITIES . "` WHERE company_id='" . $company_id . "' AND functionalities_status='active'";
    $iv_sql = queryGet($sql_list, true);
    // console($iv_sql);
    // exit();

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