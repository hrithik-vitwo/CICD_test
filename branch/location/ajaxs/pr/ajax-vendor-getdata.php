<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-SendEmailToRFQvendor.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];

    $get_vendor_query = "SELECT * FROM erp_vendor_response WHERE erp_v_id = '$id'";

    $dataset = queryGet($get_vendor_query, false);

    echo json_encode($dataset['data']);

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

}

?>