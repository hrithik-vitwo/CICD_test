<?php
include_once("../../app/v1/connection-service.php");
$returnData = array('Content-Type: application/json');
// console($_POST);

if ($_POST['act'] == 'change') {
    // console($_POST);
    $id = $_POST['id'];
    $status = $_POST['status'];

    $sql = queryUpdate("UPDATE `erp_bug_list` SET `status` = '$status' WHERE `id` = $id");
    if ($status == 'solved') {
        $performer = queryGet("SELECT * FROM `erp_bug_list` WHERE `id` = $id")['data'];
        $assign_to = $performer['assign_to'];
        $bug_user = queryGet("SELECT * FROM `erp_bug_user_details` WHERE `fldAdminKey` = $assign_to")['data'];
        $open_bug_count = $bug_user['open_bug_count'];
        $new_count=$open_bug_count-1;
        $sql1 = queryUpdate("UPDATE `erp_bug_user_details` SET `open_bug_count` = '$new_count' WHERE `fldAdminKey` = $assign_to");

    }else if($status == 'reopen'){
        $performer = queryGet("SELECT * FROM `erp_bug_list` WHERE `id` = $id")['data'];
        $assign_to = $performer['assign_to'];
        $bug_user = queryGet("SELECT * FROM `erp_bug_user_details` WHERE `fldAdminKey` = $assign_to")['data'];
        $open_bug_count = $bug_user['open_bug_count'];
        $new_count=$open_bug_count+1;
        $sql1 = queryUpdate("UPDATE `erp_bug_user_details` SET `open_bug_count` = '$new_count' WHERE `fldAdminKey` = $assign_to");
    }
    // console($sql);

    if ($sql['status'] == 'success') {
        $returnData['status'] = 'Success';
        $returnData['message'] = 'Status Changed !';
        $returnData['changes_status'] = $status;
    } else {
        $returnData['status'] = 'warning';
        $returnData['message'] = 'Failed !';
    }
    echo json_encode($returnData);
}
