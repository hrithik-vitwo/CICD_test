<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
$headerData = array('Content-Type: application/json');


function sendJsonResponse($data=[], $responseCode = 200){
    http_response_code($responseCode);
    echo json_encode($data, true);
    exit();
}

if(!(isset($company_id) && $company_id>0)){
    sendJsonResponse([
        "status" => "warning",
        "message" => "Unauthorized"
    ], 401);
}

$dbObj = new Database();

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $isValidateObj = validate($_POST, [
        "frmOtherHeadName" => "required",
        "frmOtherHeadCode" => "required",
        "frmOtherHeadRate" => "required",
        "frmOtherHeadUom" => "required",
    ]);


    if($isValidateObj["status"]!="success"){
        sendJsonResponse([
            "status" => "warning",
            "message" => "Validation failed",
            "error" => $isValidateObj
        ], 403);
    }


    $head_name = $_POST["frmOtherHeadName"] ?? "";
    $head_code = $_POST["frmOtherHeadCode"] ?? "";
    $head_gl = $_POST["frmOtherHeadGl"] ?? "";
    $head_rate = $_POST["frmOtherHeadRate"] ?? "";
    $head_uom = $_POST["frmOtherHeadUom"] ?? "";
    $head_type = $_POST["frmOtherHeadType"] ?? 1;

    // $prevHeadCodeObj = $dbObj->queryGet("SELECT * FROM `erp_master_expense_other_head` WHERE `company_id`=".$company_id." AND `branch_id`=".$branch_id." AND `location_id`=".$location_id." AND `status`='active' AND `head_type`=".$head_type." ORDER BY `head_id` DESC LIMIT 1");
    // $newHeadCode = "";
    // if(isset($prevHeadCodeObj["data"]["head_code"])){
    //     $prevHeadCodeObj["data"]["head_code"]
    // }

    $getObj = $dbObj->queryGet("SELECT * FROM `erp_master_expense_other_head` WHERE `company_id`=".$company_id." AND `branch_id`=".$branch_id." AND `location_id`=".$location_id." AND `status`='active' AND `head_type`=".$head_type." AND (`head_name`='".$head_name."' OR `head_code`='".$head_code."')", true);
    if($getObj["status"]=="success"){
        sendJsonResponse([
            "status" => "warning",
            "message" => "Head code or name is already exists, please try with different",
        ], 200);
    }


    if($head_gl!=""){
        $sql = "INSERT INTO `erp_master_expense_other_head` SET `company_id`=".$company_id.",`branch_id`=".$branch_id.",`location_id`=".$location_id.",`head_name`='".$head_name."',`head_code`='".$head_code."',`head_gl`=".$head_gl.",`head_rate`=".$head_rate.",`head_uom`='".$head_uom."', `head_type`= ".$head_type.", `created_by`='".$created_by."', `updated_by`='".$updated_by."'";
    }else{
        $sql = "INSERT INTO `erp_master_expense_other_head` SET `company_id`=".$company_id.",`branch_id`=".$branch_id.",`location_id`=".$location_id.",`head_name`='".$head_name."',`head_code`='".$head_code."', `head_rate`=".$head_rate.",`head_uom`='".$head_uom."', `head_type`= ".$head_type.", `created_by`='".$created_by."', `updated_by`='".$updated_by."'";
    }

    
    $saveObj = $dbObj->queryInsert($sql);

    if($saveObj["status"] == "success"){
        $newGetObj = $dbObj->queryGet("SELECT * FROM `erp_master_expense_other_head` WHERE `head_id`=".$saveObj["insertedId"]);
        sendJsonResponse([
            "status" => "success",
            "message" => "Other expense head created successfully",
            "data" => $newGetObj["data"]
        ], 201);
    }else{
        sendJsonResponse([
            "status" => "error",
            "message" => "Other expense head created failed",
            "data" => []
        ], 200);
    }
}else{
    $head_type = $_GET["head_type"] ?? 1;
    $sql = "SELECT * FROM `erp_master_expense_other_head` WHERE `company_id`=".$company_id." AND `branch_id`=".$branch_id." AND `location_id`=".$location_id." AND `status`='active' AND `head_type`=".$head_type;
    $getObj = $dbObj->queryGet($sql, true);
    unset($getObj["query"]);
    sendJsonResponse($getObj, 200);
}
