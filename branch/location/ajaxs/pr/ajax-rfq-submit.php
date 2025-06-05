<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-SendEmailToRFQvendor.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
global $created_by;
// $ItemsObj = new ItemsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //POST REQUEST
    $rfq_code = $_POST["rfq_code"];
    $rfq_item_list_id = $_POST["rfq_item_list_id"];
    $closing_date = $_POST["closing_date"];

    foreach ($_POST['data'] as $data) {
        $each = explode("|", $data);
        $vendor_id = $each[0];
        $vendor_code = $each[1];
        $vendor_name = $each[2];
        $vendor_email = $each[3];
        $vendor_type = $each[4];

        if ($vendor_id == "null") {
            $vendor_id = null;
        }
        if ($vendor_code == "null") {
            $vendor_code = null;
        }

        $sql = "INSERT INTO " . ERP_RFQ_VENDOR_LIST . " 
                SET 
                `rfqCode`='$rfq_code',
                `vendorId`='$vendor_id',
                `rfqItemListId`='$rfq_item_list_id',
                `vendorCode`='$vendor_code',
                `vendor_type`='$vendor_type',
                `vendor_name`='$vendor_name',
                `vendor_email`='$vendor_email'";

        queryInsert($sql);
    }

    $update = "UPDATE `erp_rfq_list` SET `closing_date`='" . $closing_date . "' WHERE `rfqId`='" . $rfq_item_list_id . "'";
    $updateObj = queryUpdate($update);
    // if($goodStockInsertObj["status"] != "success"){
    //     $errorsItemStockSummaryUpdate++;
    //    // console($goodStockInserSql);
    // }


    $query = "SELECT * FROM " . ERP_RFQ_VENDOR_LIST . " WHERE `rfqItemListId`='" . $rfq_item_list_id . "' AND emailSendStatus='0';";
    $dataset = queryGet($query, true);
    //  print_r($dataset['data']);
    if (isset($dataset['data'])) {
        SendEmailToRFQvendor($dataset['data']);
    }
    $currentTime = date("Y-m-d H:i:s");
    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
    $auditTrail = array();
    $auditTrail['basicDetail']['trail_type'] = 'MAILSEND';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
    $auditTrail['basicDetail']['table_name'] = ERP_RFQ_LIST;
    $auditTrail['basicDetail']['column_name'] = 'rfqId'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $rfq_item_list_id;  // primary key
    $auditTrail['basicDetail']['document_number'] = $rfq_code;
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['party_id'] = 0;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = ' RFQ Mails';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    $auditTrail['basicDetail']['action_sqlQuery'] = '';
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = '';

    foreach ($_POST['data'] as $data) {
        $each = explode("|", $data);
        $vendor_code = $each[1];
        $vendor_name = $each[2];
        $vendor_email = $each[3];
        $vendor_type = $each[4];
        $auditTrail['action_data']['vendors'][$vendor_code]['vendor_name'] = $vendor_name;
        $auditTrail['action_data']['vendors'][$vendor_code]['vendor_code'] = $vendor_code;
        $auditTrail['action_data']['vendors'][$vendor_code]['vendor_email'] = $vendor_email;
        $auditTrail['action_data']['vendors'][$vendor_code]['vendor_type'] = $vendor_type;
    }

 $auditTrail['action_data']['Send Mail']['Send By'] = getCreatedByUser($created_by);
 $auditTrail['action_data']['Send Mail']['Send At'] = formatDateTime($currentTime);
 $auditTrailreturn = generateAuditTrail($auditTrail);
    $returnData = [
        "status" => "success",
        "message" => "Data saved successfully"
    ];
    echo json_encode($returnData);

}
//   else {
//     $returnData = [
//         "status" => "failed",
//         "message" => "Data saved failed, try again later"
//     ];
// }
