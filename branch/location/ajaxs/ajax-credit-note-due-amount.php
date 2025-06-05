<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['act'] == 'creditNote-dueAmt') {

        $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
        $companyCurrencyData = $companyCurrencyObj["data"];
        $currency_name = $companyCurrencyData['currency_name'];

        $BranchSoObj = new BranchSo();

        $customerSelect = $_POST['customerSelect'];

        $customerSelect = $_POST['id'];
        $fetchInvoiceAmtDetails = $BranchSoObj->totalInvoiceAmountDetailsByCustomer($customerSelect)['data'];

        $sql_list = "SELECT * FROM `erp_credit_note` WHERE `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . " AND `party_id`=$customerSelect AND `creditors_type`= 'customer' AND `creditNoteReference` = '0' AND `status`='active' ORDER BY cr_note_id desc";

        $sqlMainQryObj = queryGet($sql_list, true);
        $numRows = $sqlMainQryObj['numRows'];
        $sqldata = $sqlMainQryObj['data'];

        $total_outstanding_amount = $fetchInvoiceAmtDetails['total_outstanding_amount'];

        if ($numRows > 0) {
            $total_amount = 0;
            foreach ($sqldata as $row) {
                $total_amount += $row['total'];
            }

            $res = [
                "status" => "success",
                "total_outstanding_amount" => inputValue($total_outstanding_amount),
                "total_overdue_amount" => inputValue($total_outstanding_amount - $total_amount)
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
}
