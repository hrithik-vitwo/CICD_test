<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
$company_data= getCompanyDataDetails($_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]);
//if($_POST["p_id"] !=0){
    $coa_lastrow_details=getChartOfAccountsDataDetails_byparent($_POST["p_id"],$_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]);
        if($coa_lastrow_details["status"]=="success"){
            $coa_lastrow_details=$coa_lastrow_details['data']['gl_code'];
        } else{
            $coa_lastrow_details='';
        }
//}else{
   // $coa_lastrow_details='';
//}
//if($_POST["p_id"] !=0){
    $parent_details=getChartOfAccountsDataDetails($_POST["p_id"]);
        if($parent_details["status"]=="success"){
            $pdata=$parent_details['data']['p_gl_code'].$parent_details['data']['gl_code'];
        } else{
            $pdata=null;
        }
//}else{
   // $pdata=null;
//}
$data= length_calculater($pdata,$company_data['data']['gl_account_length'],$company_data['data']['gl_length_bkup'],$coa_lastrow_details);
echo json_encode($data);
?>