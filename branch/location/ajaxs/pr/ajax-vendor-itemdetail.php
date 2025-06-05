<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-SendEmailToRFQvendor.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // echo json_encode($_POST['id']);
    $id = $_POST['id'];

    $details = "SELECT * FROM erp_inventory_items WHERE itemId = '$id'";
    $dataset=queryGet($details, false);

    echo json_encode($dataset['data']);
}


?>