<?php
require_once("api-common-func.php");
// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $company_id = $_GET['company_id'];

    if(isset($company_id) && $company_id != "")
    {
        $table = "erp_acc_coa_".$company_id."_table";
        $check_query = queryGet('SELECT `gl_code`,`gl_label`,`remark`,CASE `typeAcc` WHEN "1" THEN "Asset" ELSE "Liability" END AS `status_label` FROM `'.$table.'` WHERE `company_id`=' . $company_id . ' AND `status`= "active" AND `glStType` = "account" AND (`typeAcc`= 1 OR `typeAcc`= 2) ORDER BY `gl_code` ASC',true);

        if($check_query["numRows"] == 0)
        {
            sendApiResponse([
                "status" => "error",
                "message" => "NO List"
    
            ], 405);
        }
        else
        {
            sendApiResponse([
                "status" => "success",
                "message" => $check_query["data"]
    
            ], 200);
        }

    }
    else
    {
        sendApiResponse([
            "status" => "error",
            "message" => "Something went Wrong"

        ], 405);
    }
    
    
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed"

    ], 405);
}
