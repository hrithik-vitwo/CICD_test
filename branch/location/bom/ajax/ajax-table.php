<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
$headerData = array('Content-Type: application/json');


if($_SERVER["REQUEST_METHOD"] == "GET"){
   
    $wc_id = $_GET['wc_id'];
    $select_wc = queryGet("SELECT * FROM `erp_table_master` as table_master LEFT JOIN `erp_table_wc_mapping` as table_mapping ON table_master.table_id = table_mapping.table_id  WHERE table_mapping.`wc_id` = $wc_id AND table_mapping.`company_id`=$company_id AND table_master.`location_id` = $location_id",true);
    
    foreach($select_wc['data'] as $data){
        echo '<option value="'.$data['table_id'].'">'.$data['table_name'].'('.$data['table_code'].')</option>';

    }
       
}
?>