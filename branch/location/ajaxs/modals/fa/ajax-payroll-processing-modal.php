<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/common/templates/template-manage-journal.php");

$headerData = array('Content-Type: application/json');

$dbObj = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $proceedId = $_GET['processedId'];
    $sql_list = "SELECT * FROM `erp_payroll_processing_log` WHERE `process_id`=$proceedId";
    $sqlMainQryObj = queryGet($sql_list,true);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $payrollsql_list = queryGet("SELECT * FROM erp_payroll_processing   WHERE 1 " . $cond . "AND`process_id`=$proceedId AND company_id = $company_id AND location_id = $location_id AND pay_type='".$_GET['act']."' AND status !='deleted'")['data'];


    $dynamic_data = [];

    if ($num_list > 0) {
        foreach($data as $onedata){
        $dynamic_data []= [
            "posting_date" => $onedata['posting_date'],
            "amount" => $onedata['amount'],
            "created_at" => $onedata['created_at'],
            "created_by" => getCreatedByUser($onedata['created_by']),
            "status"=>$onedata['status']

        ];
    }
        $res = [
            "status" => true,
            "msg" => "Success",
            "data" => $dynamic_data,
            "payrollsql_list"=>$payrollsql_list
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sqlMainQryObj
        ];
    }
    echo json_encode($res);
}
