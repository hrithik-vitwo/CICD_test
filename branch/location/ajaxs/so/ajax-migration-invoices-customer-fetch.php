<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
// so controller

//for database related 
$dbObj = new Database();

// check advance on a specific customerId
function checkAdvance($customerId)
{
    global $company_id;
    global $branch_id;
    global $location_id;
    // check advance sql from a specific customer
    $sql = "SELECT SUM(`payment_amt`) AS `totalAdvanceAmt` FROM `erp_branch_sales_order_payments_log` as collectionLog WHERE collectionLog.company_id=$company_id AND collectionLog.branch_id=$branch_id AND collectionLog.location_id=$location_id AND `payment_type` = 'advanced' AND collectionLog.customer_id=$customerId";
    $res = queryGet($sql);
    if ($res['status'] == 'success') {
        return $res['data']['totalAdvanceAmt'];
    } else {
        return 0;
    }
}

// for any get requests
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if ($_GET['act'] == 'customerInvoiceData') {
        $custCode = $_GET['custCode'];
        if ($custCode == null) {
            echo json_encode(['status' => 'error', 'message' => 'No Customer Id Found']);
            exit();
        }

        //customer details sql 

        $customerDetailsSql = $dbObj->queryGet("SELECT * FROM `erp_customer` WHERE company_id = $company_id  AND company_branch_id = $branch_id AND customer_code =$custCode ");
        $customerId = $customerDetailsSql['data']['customer_id'];
        // off set pagination
        $limit_per_Page = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page_no = max(1, $page_no);
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        // customer invoice data by using customerId
        $custInvSql = "SELECT * FROM `erp_branch_sales_order_invoices` as inv WHERE inv.company_id=$company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id  AND inv.customer_id=$customerId AND inv.type = 'migration'   AND inv.status = 'active'";

        $sql_Mainqry = $custInvSql . " LIMIT " . $offset . "," . $limit_per_Page . ";";

        $custInvRes = $dbObj->queryGet($custInvSql, true);
        // if invoice found on this customer
        if ($custInvRes['numRows'] > 0) {
            $invoiceData = [];
            $custInvData = $custInvRes['data'];

            // $fetchInvoiceAmtDetails = [];
            // $fetchInvoiceAmtRes = $soObj->totalInvoiceAmountDetailsByCustomer($custId);
            // if ($fetchInvoiceAmtRes['status'] == 'success') {
            //     $fetchInvoiceAmtDetails = $fetchInvoiceAmtRes['data'];
            // }

            // compnay currrency data
            $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
            $companyCurrencyData = $companyCurrencyObj["data"];

            // main array building
            // foreach ($custInvData as $data) {
            //     // all inv related data  will be prepared here
            //     $invId = $data['so_invoice_id'];
            //     $dueAmount = $data['due_amount'];
            //     $totalCreditNoteAmount = 0;
            //     $cnData = [];

            //     $cnSql = "SELECT *  FROM erp_credit_note WHERE creditNoteReference='" . $invId . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id ";

            //     $cnRes = $dbObj->queryGet($cnSql, true);
            //     if ($cnRes['numRows'] > 0) {
            //         $cnData = $cnRes['data'];

            //         $totalCreditNoteAmount = $dbObj->queryGet("SELECT SUM(total) AS totalCreditNoteAmount FROM erp_credit_note WHERE creditNoteReference='" . $invId . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id GROUP BY creditNoteReference;")['data']['totalCreditNoteAmount'];
            //     }

            //     $dueAmount = $dueAmount - $totalCreditNoteAmount;

            //     // status data ready
            //     $statusLabel = fetchStatusMasterByCode($data['invoiceStatus'])['data']['label'];
            //     $inputPreviousCurrencyRate = $data['conversion_rate'];

            //     // convertion rate part

            //     if ($companyCurrencyData["currency_name"] != $data["currency_name"]) {
            //         $currencyConverstionObj = currency_conversion($companyCurrencyData["currency_name"], $data["currency_name"]);
            //         $currentConverstionRate = $currencyConverstionObj["quotes"][$companyCurrencyData["currency_name"] . $data["currency_name"]] ?? $data["conversion_rate"];
            //     } else {
            //         $currentConverstionRate = $data["conversion_rate"];
            //     }

            // }

            // main response bulding
            $res = [
                "status" => "success",
                "message" => "Invoice Data Fetched",
                "sql" => $sql_Mainqry,
                "data" => $custInvData,
                "numRows" => $custInvRes['numRows']
            ];

            echo json_encode($res);
            exit();
        } else {
            echo json_encode([
                'status' => 'warning',
                'message' => 'No customer invoice data found',
                'sql' => $sql_Mainqry['sql']
            ]);
            exit();
        }
    } else if ($_GET['act'] == 'oneInvoiceData') {
        $inv_id = $_GET['inv_id'];
        if ($inv_id == null) {
            echo json_encode(['status' => 'error', 'message' => 'No Customer Id Found']);
            exit();
        }

        // customer invoice data by using customerId
        $custInvSql = "SELECT * FROM `erp_branch_sales_order_invoices` as inv WHERE inv.company_id=$company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id  AND inv.type = 'migration'   AND inv.status = 'active' AND inv.so_invoice_id=$inv_id";


        $custInvRes = $dbObj->queryGet($custInvSql);
        // if invoice found on this customer
        if ($custInvRes['numRows'] > 0) {
            $invoiceData = [];
            $custInvData = $custInvRes['data'];


            $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
            $companyCurrencyData = $companyCurrencyObj["data"];

            // main response bulding
            $res = [
                "status" => "success",
                "message" => "Invoice Data Fetched",
                "sql" => $sql_Mainqry,
                "data" => $custInvData,
                "numRows" => $custInvRes['numRows']
            ];

            echo json_encode($res);
            exit();
        } else {
            echo json_encode([
                'status' => 'warning',
                'message' => 'No customer invoice data found',
                'sql' => $custInvRes
            ]);
            exit();
        }
    }
}
