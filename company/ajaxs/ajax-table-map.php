<?php
include_once("../../app/v1/connection-company-admin.php");
$headerData = array('Content-Type: application/json');
//echo "okay";
if(isset($_GET['wc_id'])){
     $wc_id = $_GET['wc_id'];
     $table = queryGet("SELECT erp_table_master.table_id, erp_table_master.table_name,erp_table_master.table_code,erp_table_master.table_description FROM erp_table_master LEFT JOIN erp_table_wc_mapping ON erp_table_master.table_id = erp_table_wc_mapping.table_id AND erp_table_wc_mapping.wc_id = $wc_id WHERE erp_table_wc_mapping.table_id IS NULL AND erp_table_master.`company_id`= $company_id",true);
//   console($table);
//   exit();
echo '<option>Select Table</option>';
foreach($table['data'] as $table){
   echo '<option value="'.$table['table_id'].'">'.$table['table_name'].'</option>';
}


}

?>