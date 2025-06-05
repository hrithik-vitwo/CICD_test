<?php

require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');

if ($_GET['act'] === "prClose") {
    global $updated_by;
  $pr_id = $_GET['pr_id'];
  $upd = "UPDATE `" . ERP_BRANCH_PURCHASE_REQUEST . "` SET `pr_status`=10 WHERE `purchaseRequestId`=$pr_id";
  $updateObj = queryUpdate($upd);

  if($updateObj['status']== 'success')
  {
        $pr_Sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE `purchaseRequestId`=$pr_id AND `company_id` = $company_id";
        $prData = queryGet($pr_Sql)['data'];
        $pr_code = $prData['prCode'];

        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
        $auditTrail = array();
        $auditTrail['basicDetail']['trail_type'] = 'REJECT';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
        $auditTrail['basicDetail']['table_name'] = ERP_BRANCH_PURCHASE_REQUEST;
        $auditTrail['basicDetail']['column_name'] = 'purchaseRequestId'; // Primary key column
        $auditTrail['basicDetail']['document_id'] = $pr_id;  // primary key
        $auditTrail['basicDetail']['document_number'] = $pr_code;
        $auditTrail['basicDetail']['party_id'] = 0;
        $auditTrail['basicDetail']['action_code'] = $action_code;
        $auditTrail['basicDetail']['action_referance'] = '';
        $auditTrail['basicDetail']['action_title'] = ' PR Closed';  //Action comment
        $auditTrail['basicDetail']['action_name'] = 'Update';     //	Add/Update/Deleted
        $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
        $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
        $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
        $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($upd);
        $auditTrail['basicDetail']['others'] = '';
        $auditTrail['basicDetail']['remark'] = '';

        $auditTrail['action_data']['Purchase Request Details']['PR_Code']       = $prData['prCode'];
        $auditTrail['action_data']['Purchase Request Details']['Expected Date'] = formatDateORDateTime($prData['expectedDate']);
        $auditTrail['action_data']['Purchase Request Details']['PR_Type']       = $prData['pr_type'];
        $auditTrail['action_data']['Purchase Request Details']['Created_By']    = getCreatedByUser($prData['created_by']);
        $auditTrail['action_data']['Purchase Request Details']['Updated_By']    = getCreatedByUser($updated_by);

        $auditTrailreturn = generateAuditTrail($auditTrail);
    }
    echo json_encode($updateObj);

}