<?php
include_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $type_id = $_GET['typeId'];

    $type_sql = queryGet("SELECT * FROM `erp_inventory_mstr_good_types` WHERE `goodTypeId`=$type_id");
    $type = $type_sql['data']['goodTypeName'];
    

    $responseData['type_name']=$type;
    $responseData['type_id']=$type_id;
   
   echo json_encode($responseData);

}
else{
    
}


?>