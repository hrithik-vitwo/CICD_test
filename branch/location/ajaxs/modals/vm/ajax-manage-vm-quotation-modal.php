<?php

require_once("../../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$dbObj = new Database();

if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET['act'] == 'quotDel') {
    $id = $_GET['id'];
    $sql = "UPDATE `erp_rfq_list` SET status='deleted' WHERE rfqId=$id";
    $res = $dbObj->queryUpdate($sql);
    echo json_encode($res);
}
