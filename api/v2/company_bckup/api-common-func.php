<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization,  X-Requested-With");
//header("Access-Control-Allow-Headers: Authorization");
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/lib/jwt/autoload.php");

function requestBody(){
    return json_decode(file_get_contents("php://input"), true);
}
function requestPost(){
    return $_POST;
}
function requestGet(){
    return $_GET;
}
function requestFiles(){
    return $_FILES;
}

function sendApiResponse($responseArray = [], $statusCode = 200){
    http_response_code($statusCode);
    echo json_encode($responseArray, true);
    exit();
}

function getAllfetchAccountingMappingTbl($company_id)
        {
        $returnData = [];
        $sql = "SELECT * FROM `" . ERP_ACC_MAPPING . "` WHERE `company_id`='" . $company_id . "' and map_status='active' ORDER BY `map_id` DESC limit 1";

        $res = queryGet($sql,true);

        if ($res["status"] == "success") {
            if ($res["numRows"] > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = $res["data"];
            } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
            $returnData['data'] = [];
        }
        return $returnData;
    }

    function getChartOfAccountsDataDetails($key = null,$company_id)
        {
        $returnData = [];

        $table = "erp_acc_coa_".$company_id."_table";

        $sql = "SELECT * FROM `" . $table . "` WHERE `id`=" . $key . "";

        $res = queryGet($sql);

        if ($res["status"] == "success") {
            if ($res["numRows"] > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = $res["data"];
            } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
            $returnData['data'] = [];
        }
        return $returnData;
        }

    function getChartOfAccountsDataDetailsByCode($code = null,$company_id)
        {
        $returnData = [];

        $table = "erp_acc_coa_".$company_id."_table";

        $sql = "SELECT * FROM `" . $table . "` WHERE `gl_code`=" . $code . "";

        $res = queryGet($sql);

        if ($res["status"] == "success") {
            if ($res["numRows"] > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = $res["data"];
            } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
            }
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Somthing went wrong";
            $returnData['data'] = [];
        }
        return $returnData;
        }



// function authCustomerApiRequest(){
//     $bearerToken = apache_request_headers()["authorization"] ?? (apache_request_headers()["Authorization"] ?? "");
//     if($bearerToken != ""){
//         $tokenType=substr($bearerToken, 0,6);
//         if($tokenType == "Bearer"){
//             $token = substr($bearerToken,7);

//             $jwtObj = new JwtToken();
//             $jwtVerifyObj=$jwtObj->verifyToken($token);
//             if($jwtVerifyObj["status"]=="success"){
//                 $jwtData = $jwtVerifyObj["data"];
//                 $customerId = $jwtData["customer_id"] ?? "";
//                 $customerCode = $jwtData["customer_code"] ?? "";

//                 $customerDetailsObj = queryGet('SELECT `customer_id`, `company_id`, `company_branch_id` as branch_id, `location_id`, `parentGlId`, `customer_code`, `customer_pan`, `customer_gstin`, `trade_name`, `constitution_of_business`, `customer_opening_balance`, `customer_currency`, `customer_visible_to_all`, `customer_credit_period`, `customer_status` FROM `erp_customer` WHERE `customer_id`='.$customerId);
//                 if($customerDetailsObj["status"]=="success"){
//                     if($customerDetailsObj["data"]["customer_status"]=="active"){
//                         return $customerDetailsObj["data"];
//                     }else{
//                         sendApiResponse([
//                             "status" => "warning",
//                             "message" => "Your account is inactive, contact us as soon as possible!",
//                             "data" => []
//                         ],401);
//                     }
//                 }else{
//                     sendApiResponse([
//                         "status" => "warning",
//                         "message" => "Authorization failed",
//                         "sql" => $customerDetailsObj,
//                         "data" => []
//                     ],401);
//                 }
//             }else{
//                 sendApiResponse([
//                     "status" => "warning",
//                     "message" => "Invalid authorization token1",
//                     "data" => []
//                 ],401);
//             }
//         }else{
//             sendApiResponse([
//                 "status" => "warning",
//                 "message" => "Invalid authorization token2",
//                 "data" => []
//             ],401);
//         }
//     }else{
//         sendApiResponse([
//             "status" => "warning",
//             "message" => "Missing authorization token",
//             "data" => []
//         ],401);
//     }
//     exit();
// }