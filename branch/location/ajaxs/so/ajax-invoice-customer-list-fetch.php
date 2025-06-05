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
        $custId = $_GET['id'];
        if ($custId == null) {
            echo json_encode(['status' => 'error', 'message' => 'No Customer Id Found']);
            exit();
        }

        // off set pagination
        $limit_per_Page = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page_no = max(1, $page_no);
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        // customer invoice data by using customerId
        $custInvSql = "SELECT * FROM `erp_branch_sales_order_invoices` as inv WHERE inv.company_id=$company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id  AND inv.customer_id=$custId AND inv.invoiceStatus != '4' AND inv.status = 'active'";

        $sql_Mainqry = $custInvSql . " LIMIT " . $offset . "," . $limit_per_Page . ";";

        $custInvRes = $dbObj->queryGet($sql_Mainqry, true);
        // if invoice found on this customer
        if ($custInvRes['numRows'] > 0) {
            $invoiceData = [];
            $custInvData = $custInvRes['data'];
            // compnay currrency data
            $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
            $companyCurrencyData = $companyCurrencyObj["data"];

            // main array building
            foreach ($custInvData as $data) {
                // all inv related data  will be prepared here
                $invId = $data['so_invoice_id'];
                $dueAmount = $data['due_amount'];
                $totalCreditNoteAmount = 0;
                $cnData = [];

                $cnSql = "SELECT *  FROM erp_credit_note WHERE creditNoteReference='" . $invId . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND `creditors_type`='customer' AND status='active'";

                $cnRes = $dbObj->queryGet($cnSql, true);
                if ($cnRes['numRows'] > 0) {
                    $cnData = $cnRes['data'];

                    $totalCreditNoteAmount = $dbObj->queryGet("SELECT SUM(total) AS totalCreditNoteAmount FROM erp_credit_note WHERE creditNoteReference='" . $invId . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND status='active' GROUP BY creditNoteReference;")['data']['totalCreditNoteAmount'];
                }

                $dueAmount = $dueAmount - $totalCreditNoteAmount;

                // status data ready
                $statusLabel = fetchStatusMasterByCode($data['invoiceStatus'])['data']['label'];
                $inputPreviousCurrencyRate = $data['conversion_rate'];

                // convertion rate part

                if ($companyCurrencyData["currency_name"] != $data["currency_name"]) {
                    $currencyConverstionObj = currency_conversion($companyCurrencyData["currency_name"], $data["currency_name"]);
                    $currentConverstionRate = $currencyConverstionObj["quotes"][$companyCurrencyData["currency_name"] . $data["currency_name"]] ?? $data["conversion_rate"];
                } else {
                    $currentConverstionRate = $data["conversion_rate"];
                }


                // due calculated 


                $invoiceData[] = [
                    "dataObj" => $data,
                    "dueAmount" => $dueAmount,
                    "statuslabel" => $statusLabel,
                    "inputPreviousCurrencyRate" => $inputPreviousCurrencyRate,
                    "inputPreviousCurrentRate" => $currentConverstionRate,
                    "inputInvoiceCurrencyName" => $data['currency_name'],
                    "inputCompanyCurrencyName" => $companyCurrencyData["currency_name"],
                    "cnData" => $cnData,
                ];
            }

            // main response bulding
            $res = [
                "status" => "success",
                "message" => "Invoice Data Fetched",
                "sql" => $sql_Mainqry,
                "data" => [
                    // "fetchInvoiceAmtDetails" => $fetchInvoiceAmtDetails,
                    "invoiceData" => $invoiceData,
                ],
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
    }

    if ($_GET['act'] == "custInvAmountDetail") {
        $custId = $_GET['id'];
        if ($custId == null) {
            echo json_encode(['status' => 'error', 'message' => 'No Customer Id Found']);
            exit();
        }
        $soObj = new BranchSo();
        $fetchInvoiceAmtRes = $soObj->totalInvoiceAmountDetailsByCustomer($custId);
        $totalAdvanceAmt = $soObj->fetchAdvanceAmt($custId)['data']['totalAdvanceAmt'];
        $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
        $companyCurrencyData = $companyCurrencyObj["data"];
        if ($fetchInvoiceAmtRes['status'] == 'success') {
            echo json_encode([
                "status" => true,
                "message" => "Invoice Amount Details Fetched",
                "data" => [
                    "dataObj" => $fetchInvoiceAmtRes['data'],
                    "companyCurrency" => $companyCurrencyData['currency_name'],
                    "totalAdvanceAmt" => $totalAdvanceAmt
                ],
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Something went wrong",
                "customerId" => $custId
            ]);
        }
    }

    if ($_GET['act'] == "debitNoteData") {
        $custId = $_GET['id'];
        if ($custId == null) {
            echo json_encode(['status' => 'error', 'message' => 'No Customer Id Found']);
            exit();
        }
        // off set pagination
        $limit_per_Page = isset($_GET['limit']) ? (int)$_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page_no = max(1, $page_no);
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        // debit note data by using customerId
        $dnSql = "SELECT * FROM erp_debit_note AS dn WHERE dn.company_id=$company_id AND dn.branch_id=$branch_id AND dn.location_id=$location_id AND dn.debitor_type = 'customer' AND dn.party_id=$custId AND dn.status='active'";
        $sql_Mainqry = $dnSql . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $dnRes = $dbObj->queryGet($sql_Mainqry, true);
        // if invoice found on this customer
        if ($dnRes['numRows'] > 0) {
            $invoiceData = [];
            $dnData = $dnRes['data'];
            // main response bulding
            $res = [
                "status" => "success",
                "message" => "Debit Note Data Fetched",
                "sql" => $sql_Mainqry,
                "data" => $dnData,
                "numRows" => $dnRes['numRows']
            ];
            echo json_encode($res);
            exit();
        } else {
            echo json_encode([
                'status' => 'warning',
                'message' => 'No customer Debit Note data found',
                'sql' => $sql_Mainqry['sql'],
                'dnSql' => $dnSql
            ]);
            exit();
        }
    }

    if ($_GET['act'] == "cnrefInvoiceData") {
        $custId = $_GET['id'];
        if ($custId == null) {
            echo json_encode(['status' => 'error', 'message' => 'No Customer Id Found']);
            exit();
        }
        $custInvSql = "SELECT * FROM `erp_branch_sales_order_invoices` as inv WHERE inv.company_id=$company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id  AND inv.customer_id=$custId AND inv.invoiceStatus != '4' AND inv.due_amount>0 AND inv.status = 'active'";
        $custInvRes = $dbObj->queryGet($custInvSql, true);


        foreach ($custInvRes as $data) {
            // all inv related data  will be prepared here
            $invId = $data['so_invoice_id'];
            $dueAmount = $data['due_amount'];
            $totalCreditNoteAmount = 0;

            $cnSql = "SELECT *  FROM erp_credit_note WHERE creditNoteReference='" . $invId . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND `creditors_type`='customer'";

            $cnRes = $dbObj->queryGet($cnSql, true);
            if ($cnRes['numRows'] > 0) {
                $cnData = $cnRes['data'];

                $totalCreditNoteAmount = $dbObj->queryGet("SELECT SUM(total) AS totalCreditNoteAmount FROM erp_credit_note WHERE creditNoteReference='" . $invId . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id GROUP BY creditNoteReference;")['data']['totalCreditNoteAmount'];
            }

            $dueAmount = $dueAmount - $totalCreditNoteAmount;
        }

        if ($custInvRes['numRows'] > 0) {
            $res = [
                "status" => "success",
                "message" => "Invoice Data Fetched",
                "data" => $custInvRes['data'],
                "invdueAmount" => $dueAmount,
                "cnData" => $cnRes

            ];
            echo json_encode($res);
            exit();
        } else {
            echo json_encode([
                'status' => 'warning',
                'message' => 'No customer Invoice Note data found',
                'sql' => $custInvRes['sql'],
            ]);
            exit();
        }
    }
}
