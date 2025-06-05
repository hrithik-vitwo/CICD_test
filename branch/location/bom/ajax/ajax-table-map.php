<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
$headerData = array('Content-Type: application/json');


if($_SERVER["REQUEST_METHOD"] == "GET"){
    $map = $_GET['map_array'];
    $wc_id = $_GET['wc_id'];
//$delete = "DELETE FROM `erp_table_wc_mapping` WHERE `wc_id` = $wc_id ";
    foreach($map as $maps){
        
    } 


}

?>