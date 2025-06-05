<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once("../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../app/v1/functions/branch/func-bom-controller.php");
require_once("pagination/common-pagination.php");

require_once("../../common/exportexcel.php");

$headerData = array('Content-Type: application/json');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['act'] == 'unrefcreditnote') {

        $party_id = $_POST['id'];


        $sql_list = "SELECT * FROM `erp_credit_note` WHERE  `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . " AND `party_id`=$party_id AND `creditors_type`= 'customer' AND `creditNoteReference` = '0' AND `status`='active' ORDER BY cr_note_id desc ";

        $sqlMainQryObj = queryGet($sql_list, true);

        if ($sqlMainQryObj['numRows'] > 0) {
            $res = [
                "status" => true,
                "msg" => "success",
                "data" => $sqlMainQryObj['data'],

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "error!",
                "sql" => $sql_list
            ];
        }

        echo json_encode($res);
    }

    if ($_POST['act'] == 'settleInvoice') {

        $invoice_id = $_POST['invoiceId'];
        $customer_id = $_POST['custId'];
        $crnoteId = $_POST['crnoteId'];

        $updateUnrefCnSql = ("UPDATE `erp_credit_note` SET `creditNoteReference` = $invoice_id WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `cr_note_id`=$crnoteId ");
        $updateUnrefCn = queryUpdate($updateUnrefCnSql);
        if ($updateUnrefCn['status'] == 'success') {
            $res = [
                "status" => "success",
                "message" => "Credit Note Settle successfully",
                "sql" => $updateUnrefCnSql
            ];
        } else {
            $res = [
                "status" => "error!",
                "message" => "Credit Note Settle failed",
                "sql" => $updateUnrefCn['query']
            ];
        }
        echo json_encode($res);
    }
}
