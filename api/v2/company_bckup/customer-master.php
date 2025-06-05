<?php
require_once "api-customer-function.php";

// API CODE
if($_SERVER["REQUEST_METHOD"] == "POST"){

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
            "message" => "Invalid inputs",
            "data" => $_POST,
        ], 405);
    }

    $data_set = json_decode($_POST['data'],true);


        $customer = new CustomersController();

        $create = $customer->createCustomer($data_set,$_POST['company_id'],$_POST['branch_id'],$_POST['location_id'],$_POST['user_id']);


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

}else{
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []

    ], 405);
}

?>