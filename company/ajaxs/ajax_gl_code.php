<?php
include_once("../../app/v1/connection-company-admin.php");
include("../../app/v1/functions/company/func-ChartOfAccounts.php");
include("../../app/v1/functions/admin/func-company.php");
// $company_data= getCompanyDataDetails($company_id);
// //if($_POST["p_id"] !=0){
//     $coa_lastrow_details=getChartOfAccountsDataDetails_byparentCOA($_POST["p_id"],$company_id);
//         if($coa_lastrow_details["status"]=="success"){
//             $coa_lastrow_details=$coa_lastrow_details['data']['gl_code'];
//         } else{
//             $coa_lastrow_details='';
//         }
// //}else{
//    // $coa_lastrow_details='';
// //}
// //if($_POST["p_id"] !=0){
//     $parent_details=getChartOfAccountsDataDetailsCOA($_POST["p_id"]);
//         if($parent_details["status"]=="success"){
//             $pdata=$parent_details['data']['gl_code'];
//             $lvl=$parent_details['data']['lvl'];
//             $typeAcc=$parent_details['data']['typeAcc'];
//         } else{
//             $pdata=null;
//             $lvl=null;
//             $typeAcc=null;
//         }
// //}else{
//    // $pdata=null;
// //}
$parent_details=getChartOfAccountsDataDetailsCOA($_POST["p_id"]);
if($parent_details["status"]=="success"){
    $lvl=$parent_details['data']['lvl'];
    $typeAcc=$parent_details['data']['typeAcc'];
} else{
    $lvl=null;
    $typeAcc=null;
}

$data= length_new_coa($typeAcc,$lvl);
echo json_encode($data);
?>