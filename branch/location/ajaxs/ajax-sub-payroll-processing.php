<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/branch/func-payroll-controller.php");
$return=[];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if($_POST['act']=="salary"){
        $return = salaryPayrollAccounting($_POST);
    } else if($_POST['act']=="pf"){
        $return = pfPayrollAccounting($_POST);
    } else if($_POST['act']=="esi"){
        $return = esiPayrollAccounting($_POST);
    } else if($_POST['act']=="pt"){
        $return = ptPayrollAccounting($_POST);
    } else if($_POST['act']=="tds"){
        $return = tdsPayrollAccounting($_POST);
    } else if($_POST['act']=="payroll"){
        $return = payrollAccounting($_POST);
    } else{
        $return['status']="warning";
        $return['message']="Somthing went wrong!";
    }
}

echo json_encode($return);