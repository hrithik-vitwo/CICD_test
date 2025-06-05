<?php
include_once("../../../../app/v1/connection-branch-admin.php");

$responseData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $reason= $_POST['reversalReason'];
    $sql = "UPDATE `erp_compliance_gstr2b_documents` SET `status`='reversal', `reverse_reason`='".$reason."' WHERE `company_id`=" . $company_id . " AND `branch_id`=".$branch_id ." AND `id`=".$id."";
    $reconObj = queryUpdate($sql);

    if ($reconObj['status'] == 'success') {

        $responseData = [
            "status" => "success",
            "message" => "Invoices Permanently reversed",
            "sql"=>$reconObj,
            "reason"=>$reason

        ];
    } else {
        $responseData = [
            "status" => "warning",
            "message" => "Failed Invoices Permanently reversed",
            "sql"=>$reconObj
        ];
    }

    echo json_encode($responseData);
}
