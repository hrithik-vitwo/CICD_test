<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/common/templates/template-manage-journal.php");

$headerData = array('Content-Type: application/json');

$dbObj = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $payrollmainId = $_GET['payrollId'];
    $sql_list = "SELECT * FROM `erp_payroll` WHERE `payroll_main_id`=$payrollmainId";
    $sqlMainQryObj = queryGet($sql_list, true);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $sqlPayrollObj = queryGet("SELECT * FROM `erp_payroll_main` WHERE `payroll_main_id`=$payrollmainId")['data'];
    $myr = $sqlPayrollObj['payroll_month'] . '-' . $sqlPayrollObj['payroll_year'];
    $dateObj = DateTime::createFromFormat('m-Y', $myr);
    $datemyr = $dateObj->format('F Y');
    $dynamic_data = [];

    if ($num_list > 0) {
        foreach ($data as $onedata) {
            $cost_center = queryGet("SELECT * FROM `erp_cost_center` WHERE `CostCenter_id` = '" . $onedata['alpha_costcenter_id'] . "' AND `location_id`=$location_id");
           
            $dynamic_data[] = [
                "costCenter_code" => $cost_center['data']['CostCenter_code'],
                "gross" => $onedata['gross'],
                "pf_employee" => $onedata['pf_employee'],
                "pf_employeer" => $onedata['pf_employeer'],
                "pf_admin" => $onedata['pf_admin'],
                "esi_employee" => $onedata['esi_employee'],
                "esi_employeer" => $onedata['esi_employeer'],
                "ptax" => $onedata['ptax'],
                "tds" => $onedata['tds']
            ];
        }
        $res = [
            "status" => true,
            "msg" => "Success",
            "data" => $dynamic_data,
            "sqlPayrollObj" => $sqlPayrollObj,
            "datemyr"=>$datemyr
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
