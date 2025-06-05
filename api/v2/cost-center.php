<?php 
// API CODE

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization,  X-Requested-With");
require_once("../../app/v1/connection-branch-admin.php");

function sendApiResponse($responseArray = [], $statusCode = 200){
    http_response_code($statusCode);
    echo json_encode($responseArray, true);
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
  

    $unique_id = $_POST['unique_id'];
    $location_sql = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `emp_api_key`='".$unique_id."'");
    $branch_id = $location_sql['data']['branch_id'];
    $company_id = $location_sql['data']['company_id'];
     $costcenter = queryGet("SELECT * FROM `erp_cost_center` WHERE CostCenter_status='active' AND branch_id = $branch_id AND company_id = $company_id", true);
    if($costcenter['status'] == "success")
   {
     
    $data = $costcenter['data'];
        sendApiResponse([
            "status" => "success",
            "message" => "success",
            "data" => $data
        ], 200);

    }
    
    else{
        sendApiResponse([
            "status" => "error",
            "message" => "Something Went Wrong",
            "data" => []
        ], 405);
    }
    }
 
    



else {
  
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
//echo "ok";



?>