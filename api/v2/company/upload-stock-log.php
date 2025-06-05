<?php
require_once "upload-stock-log-function.php";

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

    $company_id = $_POST["company_id"];
    $branch_id = $_POST["branch_id"];
    $location_id = $_POST["location_id"];
    $user_id = $_POST["user_id"];
    $created_by = $_POST["user_id"]."|company";
    $updated_by = $_POST["user_id"]."|company";

    // print_r($data_set);

    $company_opening_query = queryGet('SELECT `opening_date` FROM `erp_companies` WHERE `company_id`=' . $company_id);

    $compOpeningDate = $company_opening_query["data"]["opening_date"];

    $data_set = json_decode($_POST['data'],true);

        $upload = new StockLogUpload();

        $create = $upload->uploadStockLog($data_set,$_POST['company_id'],$_POST['branch_id'],$_POST['location_id'],$_POST['user_id']);

        if($create["error_flag"] > 0)
        {
            sendApiResponse([
                "status" => "warning",
                "message" => "Data partially submitted",
                "data" => $create["flag"]
            ], 405);
        }
        else
        {
            sendApiResponse([
                "status" => "success",
                "message" => "Data successfuly added",
                "data" => $create["flag"]
            ], 200);
        }

}
else
{
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}

?>