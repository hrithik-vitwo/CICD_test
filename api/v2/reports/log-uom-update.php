<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization,  X-Requested-With");
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/lib/jwt/autoload.php");
require_once("../../../app/v1/lib/validator/autoload.php");
//$company_id = 5;
$returnData = [];
//echo $company_id;

 $check = queryGet("SELECT * FROM erp_inventory_stocks_log WHERE companyId = $company_id AND itemUom = ' ' OR itemUom IS NULL");

 console($check);





return $returnData;

?>