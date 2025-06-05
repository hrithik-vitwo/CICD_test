<?php
require_once "api-goods-function.php";
require_once "../../../app/v1/functions/branch/func-opening-closing-balance-controller.php";

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $isValidate = validate($_GET, [
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
   

  

    $company_id = $_GET["company_id"];

    $branch_id = $_GET["branch_id"];
    $location_id = $_GET["location_id"];
    $user_id = $_GET["user_id"];
    $created_by = $_GET["user_id"]."|company";
    $updated_by = $_GET["user_id"]."|company";

    

    $array = [];
   
      $get_gl_query = queryGet("WITH MinDates AS (
    SELECT 
        subgl,
        MIN(date) AS min_date
    FROM 
        erp_opening_closing_balance
    WHERE 
        company_id = $company_id AND branch_id = $branch_id AND location_id = $location_id
    GROUP BY 
        subgl
)
SELECT 
    ct.customer_id,
    ct.customer_code,
    ct.trade_name AS customer_name,
    op.opening_val AS first_opening_val
FROM 
    erp_customer AS ct
JOIN MinDates md 
    ON ct.customer_code = md.subgl
JOIN erp_opening_closing_balance AS op 
    ON ct.customer_code = op.subgl 
    AND op.date = md.min_date
WHERE 
    ct.company_id = $company_id AND op.company_id = $company_id AND op.branch_id = $branch_id AND op.location_id = $location_id
ORDER BY 
    ct.customer_id",true);

    //   console($get_gl_query);

     
        sendApiResponse([
            "status" => "success",
            "message" => $get_gl_query["data"]

        ], 200);
    
    

}
else
{
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed"

    ], 405);
}
