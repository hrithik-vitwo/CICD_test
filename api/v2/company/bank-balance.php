<?php
require_once "api-goods-function.php";
require_once "../../../app/v1/functions/branch/func-opening-closing-balance-controller.php";

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $isValidate = validate($_POST, [
        "data" => "required",
        "company_id" => "required",
        "branch_id" => "required",
        "location_id" => "required",
        "user_id" => "required"

    ]);
    if ($isValidate["status"] != "success") {
        sendApiResponse([
            "status" => "error",
            "message" => "Invalid inputs"

        ], 200);
    }

    $data_set = json_decode($_POST['data'],true);

    $company_id = $_POST["company_id"];
    $branch_id = $_POST["branch_id"];
    $location_id = $_POST["location_id"]; 
    $user_id = $_POST["user_id"];
    $created_by = $_POST["user_id"]."|company";
    $updated_by = $_POST["user_id"]."|company";

    // print_r($data_set);

    $company_opening_query = queryGet('SELECT `opening_date` FROM `erp_companies` WHERE `company_id`=' . $company_id);

    $compOpeningDate = $company_opening_query["data"]["opening_date"];

    $array = [];
    foreach($data_set as $key => $data)
    {
        $subglcode = $data["subgl"];
        // $get_gl_query = queryGet('SELECT `acc_code`,`parent_gl` FROM `erp_acc_bank_cash_accounts` WHERE `company_id`=' . $company_id . ' AND `acc_code`="'.$subglcode.'" AND `status`= "active"');
        $get_gl_query = queryGet('SELECT `acc_code`,`parent_gl` FROM `erp_acc_bank_cash_accounts` WHERE `company_id`=' . $company_id . ' AND `acc_code` LIKE "%'.$subglcode.'" AND `status`= "active"');

        $data["gl"] = $get_gl_query["data"]["parent_gl"];

        $array[] = $data;

    }

    // sendApiResponse($array, 200);


    $openingClosingBalanceObj = new OpeningClosingBalance();
    $resultObj = $openingClosingBalanceObj->saveOpeningBalance($array);
    // $resultObj["data"] = [];
    $declaration = 0;
    if($declaration == 0)
        {
            $declaration_value = 'unlock';
        }
        else
        {
            $declaration_value = 'lock';
        }
    $insvalidation = "INSERT INTO `erp_migration_validation`
                    SET 
                        `company_id`='$company_id',
                        `branch_id`='$branch_id',
                        `location_id`='$location_id',
                        `user_id`='$user_id',
                        `migration_type`='cashSubGL',
                        `declaration`='$declaration_value',
                        `created_by`='$created_by',
                        `updated_by`='$created_by' 
                        ";
                    queryInsert($insvalidation);
    sendApiResponse($resultObj, 200);

}
else
{
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed"

    ], 405);
}
