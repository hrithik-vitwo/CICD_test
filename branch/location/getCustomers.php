<?php 
require_once("../../app/v1/connection-branch-admin.php");

$response = [];


$select_customer = queryGet("SELECT * FROM `erp_customer` WHERE `company_id` = $company_id AND `location_id` = $location_id");
console($select_customer['data']);
exit();


if($select_customer['status'] == 'success'){
    $response['status'] = 'success';
    $response['customers'] = $select_customer['data'];
}
else{
    $response['status'] = 'warning';
    $response['customers'] = '';

}

echo json_encode($response);

 
?>