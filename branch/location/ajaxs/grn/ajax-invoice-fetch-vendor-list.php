<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-grn-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$dbObj = new Database();

$grnObj = new GrnController();
// $fetchInvoiceByCustomer = $grnObj->fetchGRNByVendorId($vendorId)['data'];
// $fetchInvoiceByCustomer = $grnObj->fetchGRNInvoiceByVendorId($vendorId)['data'];
// $fetchAdvanceAmt = $grnObj->fetchGrnAdvanceAmt($vendorId)['data']['totalAdvanceAmt'];

if ($_SERVER["REQUEST_METHOD"] == "GET") {


    if ($_GET['act'] == 'vendorGrn') {
        $vendorId = $_GET['id'];

        if ($vendorId == null) {
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
        $custInvSql = "SELECT * FROM `" . ERP_GRNINVOICE . "` WHERE companyId= $company_id AND branchId=  $branch_id AND vendorId='$vendorId' AND paymentStatus != 4 AND grnStatus = 'active'";

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

                // status data ready
                $statusLabel = fetchStatusMasterByCode($data['paymentStatus'])['data']['label'];
                $inputPreviousCurrencyRate = $data['conversion_rate'];

                // convertion rate part

                if ($companyCurrencyData["currency_name"] != $data["currency_name"]) {
                    $currencyConverstionObj = currency_conversion($companyCurrencyData["currency_name"], $data["currency_name"]);
                    $currentConverstionRate = $currencyConverstionObj["quotes"][$companyCurrencyData["currency_name"] . $data["currency_name"]] ?? $data["conversion_rate"];
                } else {
                    $currentConverstionRate = $data["conversion_rate"];
                }


                $invoiceData[] = [
                    "dataObj" => $data,
                    "statuslabel" => $statusLabel,
                    "inputPreviousCurrencyRate" => $inputPreviousCurrencyRate,
                    "inputPreviousCurrentRate" => $currentConverstionRate,
                    "inputInvoiceCurrencyName" => $data['currency_name'],
                    "inputCompanyCurrencyName" => $companyCurrencyData["currency_name"],

                ];
            }

            // main response bulding
            $res = [
                "status" => "success",
                "message" => "Invoice Data Fetched",
                "sql" => $sql_Mainqry,
                "data" => [

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
                'sql' => $sql_Mainqry
            ]);
            exit();
        }
    }
}
