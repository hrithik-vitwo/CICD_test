<?php
// --------------------Others Table 
$tablePrefix="erp_";
//if(!defined("ERP_ACC_CHART_OF_ACCOUNTS")) 	        define("ERP_ACC_CHART_OF_ACCOUNTS",$tablePrefix."acc_chart_of_accounts");

if(!defined("ERP_ACC_CHART_OF_ACCOUNTS")) 	        define("ERP_ACC_CHART_OF_ACCOUNTS",$tablePrefix."acc_coa_".$company_id."_table");
//echo ERP_ACC_CHART_OF_ACCOUNTS;

if(!defined("ERP_AUDIT_TRAIL")) 	                define("ERP_AUDIT_TRAIL",$tablePrefix."audit_trail_".$company_id."_table");
//echo ERP_ACC_CHART_OF_ACCOUNTS;

?>