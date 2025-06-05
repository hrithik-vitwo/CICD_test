<?php
require_once("../../../../../app/v1/connection-branch-admin.php"); // app/v1/connection-branch-admin.php
require_once("../../../../../app/v1/functions/branch/func-vendors-controller.php");
require_once("../../pagination/common-pagination.php");
$headerData = array('Content-Type: application/json');
$dbObj = new Database();
$vendorDetailsObj = new VendorController();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $vendorId = $_GET['vendorId'];
    // $vendorId = $_GET['id'];
    if ($_GET['act'] == "modalData") {
        $sts = " AND `vendor_status` !='deleted'";
        $cond = "AND vendor_id=$vendorId";
        $sql_list = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' AND company_branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "'   " . $sts . "  ";
        $sqlObject = $dbObj->queryGet($sql_list);
        $num_list = $sqlObject['numRows'];
        if ($num_list > 0) {
            $data = $sqlObject['data'];

            $navBtn='';
            $mailmsg ='Vendor mail is verified.';

            if ($data['isMailValid'] == 'no') {
                $navBtn = '<div class="action-btns display-flex-gap create-delivery-btn-sales" id="action-navbar"><a class="nav-link approve-po btn btn-warning text-white float-right p-2" id="sendVerificationMail" data-id="' . base64_encode($data['vendor_id']) . '" role="" aria-controls="profile" aria-selected="false"><ion-icon name="send-outline"></ion-icon>Send Verification Mail</a></div>';
                $mailmsg = 'Vendor mail is not verified. Click the button to send verification mail:';
              }
      
            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $data,
                "sql" => $sql_list,
                "navbar" => $navBtn,
                "mailmsg" => $mailmsg
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list
            ];
        }
        echo json_encode($res);
    }


    // inner quotations ajax json send
    if ($_GET['act'] == "vendQuot") {
        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        // Base SQL query
        $query = "SELECT rfq_code , item_name , item_code , moq , price , discount , total , gst , lead_time FROM `erp_vendor_item` as item LEFT JOIN `erp_vendor_response` as response ON response.erp_v_id = item.erp_v_id WHERE response.vendor_id =$vendorId";
        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];

        if ($totalRows <= 0) {
            echo json_encode([
                "status" => "error",
                "message" => "no data found",
                "sql" => $query
            ]);
            exit();
        }

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . "";
        $sqlMainQryObj = $dbObj->queryGet($sql_Mainqry, true);
        $sqldata = $sqlMainQryObj['data'];
        $res = [];
        $dynamic_data = [];

        $numRows = $sqlMainQryObj['numRows'];

        foreach($sqldata as $data)
        {
            $dynamic_data[]= [
                "rfq_code"=> $data["rfq_code"],
                "item_name" => $data["item_name"],
                "item_code"=> $data["item_code"],
                "moq" => decimalQuantityPreview($data["moq"]),
                "price" => decimalValuePreview($data["price"]),
                "discount"  => decimalValuePreview($data["discount"]),
                "total" => decimalValuePreview($data["total"]),
                "gst" => decimalQuantityPreview($data["gst"]),
                "lead_time" => $data["lead_time"],
            ];
        }

        if ($numRows > 0) {
            $res = [
                "status" => "success",
                "message" => "Data found",
                "data" => $dynamic_data,
                "numRows" => $numRows,
                "sql" => $query
            ];
        } else {
            $res = [
                "status" => "warning",
                "message" => "No Invoice Found",
                "data" => [],
                "sql" => $sqlMainQryObj['sql']
            ];
        }
        echo json_encode($res);
    }

    // inner purchase order ajax json send
    if ($_GET['act'] == "vendPurchsOrdr") {
        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        // Base SQL query
        $query = "SELECT * FROM `erp_branch_purchase_order` WHERE company_id=" . $company_id . " AND `vendor_id` ='".$vendorId."'";
        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        // Fetch main query results
        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];

        if ($totalRows <= 0) {
            echo json_encode([
                "status" => "error",
                "message" => "no data found",
                "sql" => $query
            ]);
            exit();
        }

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . "";
        $sqlMainQryObj = $dbObj->queryGet($sql_Mainqry, true);
        $res = [];
        $numRows = $sqlMainQryObj['numRows'];
        if ($numRows > 0) {
            $res = [
                "status" => "success",
                "message" => "Data found",
                "data" => $sqlMainQryObj['data'],
                "numRows" => $numRows,
                "sql" => $query
            ];
        } else {
            $res = [
                "status" => "warning",
                "message" => "No Invoice Found",
                "data" => [],
                "sql" => $sqlMainQryObj['sql']
            ];
        }
        echo json_encode($res);
    }

    // inner vendor mails ajax json send
    if ($_GET['act'] == "vendMails") {
        $code = $_GET['code'];
        $sql_list = "SELECT * FROM `erp_globalmail` WHERE `partyCode` = '" . $code . "' AND `status`='active' ORDER BY `email_id` DESC LIMIT 50";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }

    // inner bills ajax json send   
    if ($_GET['act'] == "vendorBills") {
        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        // Base SQL query
        $query = "SELECT grniv.*, grn.`grnCreatedAt` AS grnDate, grn.`po_date` AS poDate FROM `" . ERP_GRNINVOICE . "` AS grniv, `erp_grn` AS grn WHERE grniv.`companyId`='$company_id' AND grniv.`grnId` = grn.`grnId` AND grniv.`branchId`='$branch_id' AND grniv.`locationId`='$location_id' AND grniv.`grnStatus`!='deleted' AND grniv.vendorId = $vendorId ORDER BY grniv.`grnIvId` DESC"; 
        $res = [];
        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];

        if ($totalRows <= 0) {
            echo json_encode([
                "status" => "error",
                "message" => "no data found",
                "sql" => $query,
                "totalrows" => $totalRows
            ]);
            exit();
        }

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . "";
        $sqlMainQryObj = $dbObj->queryGet($sql_Mainqry, true);
        $numRows = $sqlMainQryObj['numRows'];

        foreach ($sqlMainQryObj['data'] as $inv) {
            $days = $inv['credit_period'];
            $invoice_date = date_create($inv['invoice_date']);
            date_add($invoice_date, date_interval_create_from_date_string($days . " days"));
            $creditPeriod = date_format($invoice_date, "Y-m-d");

            // Determine the status label and class
            $statusLabel = fetchStatusMasterByCode($inv['paymentStatus'])['data']['label'];
            switch (strtolower($statusLabel)) {
                case "paid":
                    $statusClass = "status";
                    break;
                case "pending":
                    $statusClass = "status-warning";
                    break;
                case "partial paid":
                    $statusClass = "status-secondary";
                    break;
                case "payment initiated":
                    $statusClass = "status-info";
                    break;
                default:
                    $statusClass = "status-danger";
                    break;
            }

            // Handle grnStatus condition
            if ($inv['grnStatus'] == 'reverse') {
                $statusLabel = 'Reversed';
                $statusClass = "status-warning";
            }

            // Calculate the due percentage
            $due_amt = $inv['dueAmt'];
            $inv_amt = $inv['grnTotalAmount'];
            $duePercentage = ($due_amt / $inv_amt) * 100;

            // Build the processed data structure
            $data[] = [
                "vendorDocumentNo" => $inv['vendorDocumentNo'],
                "vendorDocumentDate" => formatDateORDateTime($inv['vendorDocumentDate']),
                "grnCode" => $inv['grnCode'],
                "grnDate" => formatDateORDateTime($inv['grnDate']),
                "grnPoNumber" => $inv['grnPoNumber'],
                "poDate" => formatDateORDateTime($inv['poDate']),
                "grnIvCode" => $inv['grnIvCode'],
                "postingDate" => formatDateORDateTime($inv['postingDate']),
                "dueDate" => formatDateORDateTime($inv['dueDate']),
                "grnSubTotal" => decimalValuePreview($inv['grnSubTotal']),
                "taxTotal" => decimalValuePreview($inv['grnTotalCgst'] + $inv['grnTotalSgst'] + $inv['grnTotalIgst']),
                "grnTotalTds" => decimalValuePreview($inv['grnTotalTds']),
                "grnTotalAmount" => decimalValuePreview($inv['grnTotalAmount']),
                "paidAmount" => decimalValuePreview($inv['grnTotalAmount'] - $inv['dueAmt']),
                "dueAmt" => decimalValuePreview($inv['dueAmt']),
                "duePercentage" => decimalQuantityPreview($duePercentage),
                "statusLabel" => $statusLabel,
                "statusClass" => $statusClass,
                "grnStatus" => $inv['grnStatus'] == 'reverse' ? 'Reversed' : '-'
            ];
        }

        if ($numRows > 0) {
            $res = [
                "status" => "success",
                "message" => "Data found",
                "data" => $data,
                "numRows" => $numRows,
                "sql" => $query
            ];
        } else {
            $res = [
                "status" => "warning",
                "message" => "No Invoice Found",
                "data" => [],
                "sql" => $sqlMainQryObj['sql']
            ];
        }
        echo json_encode($res);
    }


    // inner payments ajax json send
    if ($_GET['act'] == "vendPymnts") {
        // Default pagination variables
        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);
        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        // Base SQL query
        $query = "SELECT * FROM " . ERP_GRN_PAYMENTS . " WHERE company_id='" . $company_id . "' AND `vendor_id` = '" . $vendorId . "' ORDER BY payment_id DESC";
        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];

        if ($totalRows <= 0) {
            echo json_encode([
                "status" => "error",
                "message" => "No data found",
                "sql" => $query
            ]);
            exit();
        }

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page;
        $sqlMainQryObj = $dbObj->queryGet($sql_Mainqry, true);

        $res = [];
        $numRows = $sqlMainQryObj['numRows'];

        if ($numRows > 0) {
            $data = $sqlMainQryObj['data'];
            $processedData = [];

            foreach ($data as $row) {
                $payment_id = $row['payment_id'];

                $logQuery = "SELECT payment_type, payment_amt FROM erp_grn_payments_log WHERE payment_id = '" . $payment_id . "'";
                $logResults = $dbObj->queryGet($logQuery , true);
                $logRows = $logResults['numRows'];
                $advancedPayments = [];
                foreach ($logResults['data'] as $logRow) {
                    if ($logRow['payment_type'] === 'advanced') {
                        $advancedPayments[] = $logRow['payment_amt'];
                    }
                }

                $paymentStatus = 'Against Invoice';

                if (count($advancedPayments) == 2) {
                    $difference = $advancedPayments[0] + $advancedPayments[1];
                    $paymentStatus = ($difference > 0) ? "Advance Payment" : "Against Invoice";
                }

                $processedData[] = [
                    'transactionId' => $row['transactionId'],
                    'paymentType' => $paymentStatus, 
                    'collect_payment' => $row['collect_payment'], 
                    'status' => $row['status'],
                    'postingDate' => $row['postingDate'], 
                ];
            }

            $res = [
                "status" => "success",
                "message" => "Data found",
                "data" => $processedData,
            ];
        } else {
            $res = [
                "status" => "warning",
                "message" => "No Invoice Found",
                "data" => [],
                "sql" => $sqlMainQryObj['sql']
            ];
        }

        echo json_encode($res);
    }


    // inner journals ajax json send 
    if ($_GET['act'] == "vendorJournal") {
        $code = $_GET['code'];
        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        // Base SQL query
        $query = "SELECT * FROM `erp_acc_journal` WHERE `party_code`='" . $code . "' AND `company_id`=$company_id AND `location_id`=$location_id AND `parent_slug`='journal'";
        // Fetch main query results
        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];

        if ($totalRows <= 0) {
            echo json_encode([
                "status" => "error",
                "message" => "no data found",
                "sql" => $query
            ]);
            exit();
        }

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . "";
        // console($sql_Mainqry);
        $sqlMainQryObj = $dbObj->queryGet($sql_Mainqry, true);
        $res = [];

        $numRows = $sqlMainQryObj['numRows'];
        if ($numRows > 0) {
            $res = [
                "status" => "success",
                "message" => "Data found",
                "data" => $sqlMainQryObj['data'],
                "numRows" => $numRows,
                "sql" => $query
            ];
        } else {
            $res = [
                "status" => "warning",
                "message" => "No Invoice Found",
                "data" => [],
                "sql" => $sqlMainQryObj['sql']
            ];
        }
        echo json_encode($res);
    }

    // inner debit note ajax json send
    if ($_GET['act'] == "debit-note") {
        $creditorsType = $_GET['creditorsType'];
        $id = $_GET['id'];
        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        // Base SQL query
        $query = "SELECT DISTINCT dn.*, vend_inv.grnIvCode as invoice_code FROM erp_debit_note AS dn LEFT JOIN debit_note_item AS dn_item ON dn.dr_note_id = dn_item.debit_note_id LEFT JOIN erp_grninvoice AS vend_inv ON dn.debitor_type = '" . $creditorsType . "' AND dn_item.invoice_id = vend_inv.grnIvId WHERE dn.debitor_type = '" . $creditorsType . "' AND dn.party_id = '" . $id . "' AND dn.company_id='" . $company_id . "' AND dn.branch_id='" . $branch_id . "' AND dn.location_id='" . $location_id . "'";

        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];

        if ($totalRows <= 0) {
            echo json_encode([
                "status" => "error",
                "message" => "no data found",
                "sql" => $query
            ]);
            exit();
        }

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . "";
        $sqlMainQryObj = $dbObj->queryGet($sql_Mainqry, true);
        $res = [];
        $numRows = $sqlMainQryObj['numRows'];
        if ($numRows > 0) {
            $res = [
                "status" => "success",
                "message" => "Data found",
                "data" => $sqlMainQryObj['data'],
                "numRows" => $numRows,
                "sql" => $query
            ];
        } else {
            $res = [
                "status" => "warning",
                "message" => "No Invoice Found",
                "data" => [],
                "sql" => $sqlMainQryObj['sql']
            ];
        }
        echo json_encode($res);

    }

    // inner credt note ajax json send
    if ($_GET['act'] == "credit-note") {
        $creditorsType = $_GET['creditorsType'];
        $id = $_GET['id'];
        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        // Base SQL query
        $query = "SELECT DISTINCT cn.*, cust_inv.invoice_no AS invoice_code FROM erp_credit_note AS cn LEFT JOIN credit_note_item AS cn_item ON cn.cr_note_id = cn_item.credit_note_id LEFT JOIN erp_branch_sales_order_invoices AS cust_inv ON cn.creditors_type = '" . $creditorsType . "' AND cn_item.invoice_id = cust_inv.so_invoice_id WHERE cn.creditors_type = '" . $creditorsType . "' AND cn.party_id = '" . $id . "' AND cn.company_id='" . $company_id . "' AND cn.branch_id='" . $branch_id . "' AND cn.location_id='".$location_id."'";

        $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery";
        $queryset = queryGet($sqlRowCount);
        $totalRows = $queryset['data']['row_count'];

        if ($totalRows <= 0) {
            echo json_encode([
                "status" => "error",
                "message" => "no data found",
                "sql" => $query
            ]);
            exit();
        }

        $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . "";
        $sqlMainQryObj = $dbObj->queryGet($sql_Mainqry, true);
        $res = [];

        $numRows = $sqlMainQryObj['numRows'];

        if ($numRows > 0) {
            $res = [
                "status" => "success",
                "message" => "Data found",
                "data" => $sqlMainQryObj['data'],
                "numRows" => $numRows,
                "sql" => $query
            ];
        } else {
            $res = [
                "status" => "warning",
                "message" => "No Invoice Found",
                "data" => [],
                "sql" => $sqlMainQryObj['sql']
            ];
        }
        echo json_encode($res);
    }
    
    // Overview Tab All Json send starts here 
    if ($_GET['act'] == "chartMenuOptions") {
        $sql_list = "SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }

    if ($_GET['act'] == "pieMenuOptions") {
        $sql_list = "SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }


    if ($_GET['act'] == "basicDetails") {
        $sql_list = "SELECT * FROM `erp_vendor_details` WHERE vendor_id=$vendorId";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }

    if ($_GET['act'] == "address") {
        // $code=$_GET['code'];
        $sql_list = "SELECT * FROM erp_vendor_bussiness_places WHERE vendor_id=" . $vendorId . " AND  vendor_business_primary_flag=1";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }

    if ($_GET['act'] == "accounting") {
        // $code=$_GET['code'];
        $sql_list = "SELECT * FROM erp_vendor_bank_details WHERE vendor_id='$vendorId'";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }

    if ($_GET['act'] == "other_business_places") {
        $sql_list = "SELECT * FROM erp_vendor_bussiness_places WHERE vendor_business_primary_flag=0 AND vendor_id='$vendorId'";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }

    if ($_GET['act'] == "audit-head-section") {
        $vendId = $_GET['id'];
        $sts = " AND `vendor_status` !='deleted'";
        $cond = "AND vendor_id=$vendId";
        $sql_list = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' AND company_branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "'   " . $sts . "  ";
        $sqlObject = $dbObj->queryGet($sql_list);
        $num_list = $sqlObject['numRows'];
        $res = [];
        if ($num_list > 0) {
            $data = $sqlObject['data'];
            $values = [
                "vendor_created_at" => formatDateORDateTime($data['vendor_created_at']),
                "vendor_updated_at" => formatDateORDateTime($data["vendor_updated_at"]),
                "vendor_created_by" => getCreatedByUser($data["vendor_created_by"]),
                "vendor_updated_by" => getCreatedByUser($data["vendor_updated_by"])
            ];
            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $values,
                "sql" => $sql_list
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list
            ];
        }
        echo json_encode($res);
    }

    if ($_GET['act'] == "auditTrailBodyContent") {

        $ccode = str_replace('-', '/', $_GET['ccode']);
        $auditQuery = "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))";
        // $resultaudit = queryGet($auditQuery);
        $auditQuery = "SELECT * FROM `" . ERP_AUDIT_TRAIL . "` WHERE `party_type`='vendor' AND `document_number` = '" . $ccode . "' OR party_id=" . $_GET['id'] . " AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `status`='active' GROUP BY `action_code`  ORDER BY `id` DESC";
        $resultaudit = $dbObj->queryGet($auditQuery, true);

        if ($resultaudit['status'] == 'success' && count($resultaudit['data']) > 0) {
            $data = [];
            foreach ($resultaudit['data'] as $row) {
                $data[] = [
                    "id" => $row['id'],
                    "document_number" => $row['document_number'],
                    "icon_url" => BASE_URL . "public/storage/audittrail/" . $row['trail_type'] . ".png",
                    "created_by" => getCreatedByUser($row['created_by']),
                    "created_at_formatted" => formatDateORDateTime($row['created_at']),
                    "action_title" => $row['action_title']
                ];
            }

            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $data,
                "sql" => $auditQuery
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $auditQuery
            ];
        }

        echo json_encode($res);
    }


    if ($_GET['act'] == "auditTrailBodyContentLine") {
        // $code=$_GET['code'];
        // Fetch audit trail data if available
        $ccode = str_replace('-', '/', $_GET['doc_code']);
        $currentAuditQuery = "SELECT * FROM `" . ERP_AUDIT_TRAIL . "` WHERE `id`=" . $_GET['doc_id'] . " AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `status`='active'";
        $previousAuditQuery = "SELECT * FROM `" . ERP_AUDIT_TRAIL . "` WHERE id < (SELECT `id` FROM `" . ERP_AUDIT_TRAIL . "` WHERE `id`=" . $_GET['doc_id'] . "  AND `document_number` = '" . $ccode . "' AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id  AND `status`='active' LIMIT 1)  AND `document_number` = '" . $ccode . "' AND (`trail_type`='ADD' OR `trail_type`='EDIT') AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `status`='active' ORDER BY id DESC LIMIT 1";

        $currentResultaudit = $dbObj->queryGet($currentAuditQuery);
        $previousResultaudit = $dbObj->queryGet($previousAuditQuery);

        // Process audit trail if available
        if ($currentResultaudit['status'] == 'success') {
            // Solution 1: Extract and compare action data
            $data2_serialized = $currentResultaudit['data']['action_data'];
            $dataP2 = unserialize($data2_serialized);
            $data2 = convertArraysToStrings($dataP2);

            $changes = [];
            if ($previousResultaudit['status'] == 'success') {
                $data1_serialized = $previousResultaudit['data']['action_data'];
                $dataP1 = unserialize($data1_serialized);
                $data1 = convertArraysToStrings($dataP1);

                $changes = compareArrays($data1, $data2);
            }

            // Add audit trail data to response
            // $res['auditTrail'] = [
            //     'currentData' => $data2,
            //     'previousData' => $data1 ?? null,
            //     'changes' => $changes
            // ];
            $res = [
                "status" => true,
                "msg" => "Success",
                'currentData' => $data2,
                'previousData' => $data1 ?? null,
                'changes' => $changes,
                'created_by' => getCreatedByUser($currentResultaudit['data']['created_by']),
                'created_at' => formatDateORDateTime($currentResultaudit['data']['created_at']),
                "sqlPrevious" => $previousAuditQuery
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sqlCurrent" => $currentAuditQuery,
                'currentData' => null,
                'previousData' => null,
                'changes' => null,
                'created_by' => null,
                'created_at' => null
            ];
        }

        // Return the final response as JSON
        echo json_encode($res);
    }

    if ($_GET['act'] == "sendVerificationMail") {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $companyNameNav;
        $dbObj = new Database();

        $vendor_Id = $_GET["id"];

        $encode_vendor_Id=base64_encode($vendor_Id);
        $encode_company_id=base64_encode($company_id);

        $sql_list = "SELECT * FROM erp_vendor_details WHERE vendor_id=$vendor_Id";

        $sqlMainQryObj = $dbObj->queryGet($sql_list);
        $sql_data = $sqlMainQryObj['data'];


        $trade_name = $sql_data['trade_name'];
        $vendor_code = $sql_data['vendor_code'];
        $mailValidity = $sql_data['isMailValid'];
        $vendor_authorised_person_email = $sql_data['vendor_authorised_person_email'];
        

        if ($mailValidity == 'no') {
            $to = $vendor_authorised_person_email;

            $sub = "Verification For Mail";


            $msg = "Dear $trade_name,<br><br>

                    This is a Verification Mail<br><br>

                    <b>Company Code:</b> $companyCodeNav<br>
                    <b>Vendor Code:</b> $vendor_code<br><br>
                    To validate your mail, <a href='" . BASE_URL . "branch/location/mailVerification_vendor.php?id=$encode_vendor_Id&c_id=$encode_company_id'>Click Here</a><br><br>

                    Best regards,<br>
                    $companyNameNav";

            $mail =  SendMailByMySMTPmailTemplate($vendor_authorised_person_email, $sub, $msg, null, $vendor_code, 'customerAdd', $vendor_Id, $vendor_code);

            if ($mail == true) {
                $updateSql = "UPDATE `erp_vendor_details` SET `mail_send_status`='1' WHERE `vendor_id`=$vendor_Id";
                $update = $dbObj->queryUpdate($updateSql);
                $res = [
                    "status" => "success",
                    "message" => "Email sent successfully"
                ];
            } else {
                $res = [
                    "status" => "error",
                    "message" => "Email does not sent"
                ];
            }
        }

        else {


            $res = [
                "status" => "error",
                "message" => "Email Not Approved"
            ];
        }

        echo json_encode($res);
    }
}
