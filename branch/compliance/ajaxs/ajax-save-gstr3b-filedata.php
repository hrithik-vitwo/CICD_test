<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../../location/ajaxs/pagination/common-pagination.php");
require_once("../../common/exportexcel.php");
$headerData = array('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $queryParams = json_decode($_POST['queryParams']);
    $tableName = $_POST['act'];

    // Fetch existing record
    $chekUpdatestaussql = queryGet("SELECT `json_data` FROM `erp_compliance_gstr3b_docs` WHERE `company_id`='" . $company_id . "'AND `branch_id`='" . $branch_id . "' AND `period`='" . $queryParams->period . "' AND `table_name`= '" . $tableName . "'");
    $res = "";
    $sql = "";

    // Insert new record if no existing data found
    if ($chekUpdatestaussql['data'] == null) {
        // Ensure encodedData is an array or object and is encoded into JSON correctly
        $encodedData = isset($_POST['encodedData']) ? $_POST['encodedData'] : [];

        $sql = queryInsert("INSERT INTO  `erp_compliance_gstr3b_docs` SET `company_id`='" . $company_id . "',`branch_id`='" . $branch_id . "',`table_name`='" . $tableName . "',`json_data`='" . json_encode($encodedData) . "',`period`='" . $queryParams->period . "',`created_by`='" . $created_by . "',`updated_by`='" . $created_by . "'");

        if ($sql['status'] == 'success') {
            $res = [
                "status" => true,
                "msg" => "success",
                "data" => $sql,
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "error",
                "sql" => $sql
            ];
        }
    } else {
        // Update existing record with new json_data
        $encodedData = isset($_POST['encodedData']) ? $_POST['encodedData'] : [];

        $sql = queryUpdate("UPDATE `erp_compliance_gstr3b_docs` 
        SET `json_data` = '" . json_encode($encodedData) . "' 
        WHERE `company_id` = '" . $company_id . "' 
        AND `branch_id` = '" . $branch_id . "' 
        AND `period` = '" . $queryParams->period . "'
        AND `table_name`= '" . $tableName . "' ");

        if ($sql['status'] == 'success') {
            $res = [
                "status" => "success",
                "msg" => "updated successfully",
                "data" => $sql,
            ];
        } else {
            $res = [
                "status" => "error",
                "msg" => "failed to update",
                "sql" => $sql
            ];
        }
    }
    echo json_encode($res);
}
