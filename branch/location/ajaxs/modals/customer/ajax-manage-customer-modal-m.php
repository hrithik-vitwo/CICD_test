<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-customers-controller.php");
require_once("../../pagination/common-pagination.php");
$headerData = array('Content-Type: application/json');
$dbObj = new Database();
$customerDetailsObj = new CustomersController();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $custId = $_GET['custId'];
    if ($_GET['act'] == "modalData") {
        $sts = " AND `customer_status` !='deleted'";
        $cond = "AND customer_id=$custId";
        $sql_list = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' AND company_branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "'   " . $sts . "  ";
        $sqlObject = $dbObj->queryGet($sql_list);
        $num_list = $sqlObject['numRows'];
        if ($num_list > 0) {
            $data = $sqlObject['data'];
            $dynamic_data = [];
            $dynamic_data = [
                "dataObj" => $data,
                "created_by" => getCreatedByUser($data['customer_created_by']),
                "created_at" => formatDateORDateTime($data['customer_created_at']),
                "updated_by" => getCreatedByUser($data['customer_updated_by']),
                "updated_at" => formatDateORDateTime($data['customer_updated_at']),
                "companyCurrency" => getSingleCurrencyType($company_currency),
                "customerCurrency" => getSingleCurrencyType($data['customer_currency']),
            ];
            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
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

    // inner invcoices ajax json send



    if ($_GET['act'] == "custTransInv") {
        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        // $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        // Base SQL query
        $query = "SELECT * FROM `erp_branch_sales_order_invoices` as salesInv WHERE salesInv.customer_id = $custId AND salesInv.company_id = $company_id AND salesInv.branch_id = $branch_id AND salesInv.location_id = $location_id ORDER BY so_invoice_id DESC"; // Add LIMIT for pagination


        $res = [];
        // $output = "</table>";
        // Count total rows for pagination
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


        foreach ($sqlMainQryObj['data'] as $invoice) {
            $temDueDate = date_create($invoice["invoice_date"]);
            $todayDate = new DateTime(date("Y-m-d"));
            $oneInvDueDays = $todayDate->diff(new DateTime(date_format($temDueDate, "Y-m-d")))->format("%r%a");

            $customerDtls = $customerDetailsObj->getDataCustomerDetails($invoice['customer_id'])['data'][0];
            $customerPic = $customerDtls['customer_picture'];
            $customerName = $customerDtls['trade_name'];
            $customerPicture = '';
            $customer_name = mb_substr($customerName, 0, 1);

            if ($customerPic != '') {
                $customerPicture = '<img src="' . BASE_URL . 'public/storage/avatar/' . $customerPic . '" class="img-fluid avatar rounded-circle" alt="">';
            } else {
                $customerPicture = '<div class="img-fluid avatar rounded-circle d-flex justify-content-center align-items-center" style="border: 1px solid grey;">' . $customer_name . '</div>';
            }

            $statusLabel = $invoice['mailStatus'] == 1 ? 'SENT' : ($invoice['mailStatus'] == 2 ? 'VIEW' : '-');
            $statusIcon = $invoice['mailStatus'] == 1 ? 'checkmark-sharp' : ($invoice['mailStatus'] == 2 ? 'checkmark-done-sharp' : '');

            $data[] = [
                "customerPicture" => $customerPicture,
                "invoiceNo" => $invoice['invoice_no'],
                "totalAmount" => $invoice['all_total_amt'],
                "invoiceDate" => formatDateORDateTime($invoice['invoice_date']),
                "dueDays" => $oneInvDueDays,
                "status" => '<div class="status-custom w-75 text-secondary">' . $statusLabel . '<div class="round"><ion-icon name="' . $statusIcon . '"></ion-icon></div></div>',
                "statusDate" => formatDateORDateTime($invoice['updated_at'])
            ];
        }

        // Generate pagination output
        // $output .= pagiNationinnerTable($page_no, $total_page);

        // Generate limit text
        // $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . ($startPageSL + $numRows - 1) . ' of ' . $totalRows . ' entries </a>';
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
        // console($sqlMainQryObj);

    }

    // inner collection ajax json send

    // if ($_GET['act'] == "custTransCollection") {



    //     $limit_per_Page = isset($_GET['maxlimit']) && $_GET['maxlimit'] != '' ? $_GET['maxlimit'] : 25;
    //     $page_no = isset($_GET['page_id']) ? (int) $_GET['page_id'] : 1;
    //     $page_no = max(1, $page_no);

    //     $offset = ($page_no - 1) * $limit_per_Page;
    //     $maxPagesl = $page_no * $limit_per_Page;
    //     $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

    //     $output = "";
    //     $limitText = "";
    //     $query = "SELECT LOG.payment_type, payment.payment_advice, payment.transactionId, LOG.payment_amt, LOG.created_at FROM `erp_branch_sales_order_payments_log` AS LOG LEFT JOIN `erp_branch_sales_order_payments` AS payment ON LOG.payment_id = payment.payment_id WHERE LOG.`customer_id` = $custId AND LOG.company_id = $company_id AND LOG.branch_id = $branch_id AND LOG.location_id = $location_id";

    //     $sql_Mainqry = $query . " LIMIT " . $offset . "," . $limit_per_Page . "";
    //     // console($sql_Mainqry);
    //     $sqlMainQryObj = $dbObj->queryGet($sql_Mainqry, true);
    //     $res = [];
    //     $output = "</table>";
    //     // Count total rows for pagination
    //     $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $query . ") AS subquery";
    //     $queryset = queryGet($sqlRowCount);
    //     $totalRows = $queryset['data']['row_count'];
    //     $total_page = ceil($totalRows / $limit_per_Page);
    //     $numRows = $sqlMainQryObj['numRows'];

    //     // Generate pagination output
    //     $output .= pagiNationinnerTable2($page_no, $total_page);

    //     // Generate limit text
    //     $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . ($startPageSL + $numRows - 1) . ' of ' . $totalRows . ' entries </a>';
    //     $res = [];
    //     if ($numRows > 0) {
    //         $res = [
    //             "status" => "success",
    //             "message" => "Data found",
    //             "data" => $sqlMainQryObj['data'],
    //             "limit_per_Page" => $limit_per_Page,
    //             "pagination" => $output,
    //             "limitTxt" => $limitText,
    //             "totalRows" => $totalRows
    //         ];
    //     } else {
    //         $res = [
    //             "status" => "warning",
    //             "message" => "No Invoice Found",
    //             "data" => [],
    //             "sql" => $sql_Mainqry
    //         ];
    //     }
    //     echo json_encode($res);
    // }



    if ($_GET['act'] == "custTransCollection") {



        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        $query = "SELECT LOG.payment_type, payment.payment_advice, payment.transactionId, LOG.payment_amt, LOG.created_at FROM `erp_branch_sales_order_payments_log` AS LOG LEFT JOIN `erp_branch_sales_order_payments` AS payment ON LOG.payment_id = payment.payment_id WHERE LOG.`customer_id` = $custId AND LOG.company_id = $company_id AND LOG.branch_id = $branch_id AND LOG.location_id = $location_id";


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

        // Generate pagination output

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

    // inner estimnates ajax json send

    if ($_GET['act'] == "custTransEstimate") {
        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        $query = "SELECT * FROM `erp_branch_quotations` as quot WHERE quot.customer_id=$custId AND quot.company_id=$company_id AND quot.branch_id=$branch_id AND quot.location_id=$location_id ORDER by quotation_id DESC";
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

        // Generate pagination output

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

    // inner sales order ajax json send
    if ($_GET['act'] == "custTransSo") {

        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $query = "SELECT * FROM `erp_branch_sales_order` as so WHERE so.customer_id=$custId AND so.company_id=$company_id AND so.branch_id=$branch_id AND so.location_id=$location_id ORDER BY so.so_id DESC";
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
        $sqldata = $sqlMainQryObj['data']; 
        $res = [];

        $numRows = $sqlMainQryObj['numRows'];
        $dynamic_data = [];

        // Generate pagination output

        foreach($sqldata as $data) {
            $dynamic_data[] = [
                "so_number" => $data["so_number"],
                "customer_po_no" => $data["customer_po_no"],
                "delivery_date" => $data["delivery_date"],
                "totalItems" => decimalQuantityPreview($data["totalItems"]),
                "approvalStatus"=> $data["approvalStatus"]
            ];
            
        }
        if($numRows > 0) {
            $res = [
                "status" => "success",
                "message" => "Data found",
                "data" => $dynamic_data,
                "numRows" => $numRows,
                "sql" => $query
            ];
    }
    else{
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
    if ($_GET['act'] == "custTransJournal") {
        $code = $_GET['code'];
        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $query = "SELECT * FROM `erp_acc_journal` as journal WHERE journal.party_code=$code AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.parent_slug='journal' ";
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

        // Generate pagination output

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
        $query = "SELECT DISTINCT
                        dn.*,vend_inv.grnIvCode as invoice_code
                    FROM
                        erp_debit_note AS dn 
                    LEFT JOIN debit_note_item AS dn_item ON dn.dr_note_id = dn_item.debit_note_id
                    LEFT JOIN erp_grninvoice AS vend_inv ON dn.debitor_type = '" . $creditorsType . "' AND dn_item.invoice_id = vend_inv.grnIvId
                    WHERE
                        dn.debitor_type = '" . $creditorsType . "' AND  dn.party_id = '" . $id . "' AND dn.company_id='" . $company_id . "' AND dn.branch_id='" . $branch_id . "' AND dn.location_id='" . $location_id . "'";


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

        // Generate pagination output

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

    // inner credit note ajax json send


    if ($_GET['act'] == "credit-note") {
        $creditorsType = $_GET['creditorsType'];
        $id = $_GET['id'];
        $limit_per_Page = isset($_GET['limit']) ? (int) $_GET['limit'] : 25;
        $page_no = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;

        // Base SQL query
        $query = "SELECT DISTINCT
                                cn.*,
                                cust_inv.invoice_no AS invoice_code
                            FROM
                                erp_credit_note AS cn
                            LEFT JOIN credit_note_item AS cn_item
                            ON
                                cn.cr_note_id = cn_item.credit_note_id
                            LEFT JOIN erp_branch_sales_order_invoices AS cust_inv
                            ON
                                cn.creditors_type = '" . $creditorsType . "' AND cn_item.invoice_id = cust_inv.so_invoice_id
                            WHERE
                                cn.creditors_type = '" . $creditorsType . "' AND
                            cn.party_id = '" . $id . "' AND cn.company_id='" . $company_id . "' AND cn.branch_id='" . $branch_id . "' AND cn.location_id='" . $location_id . "'
                        ";


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

        // Generate pagination output

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

    // overview chart header 



    if ($_GET['act'] == "chatheader") {
        $sql_list = "SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC ";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }

    // overview address details 


    if ($_GET['act'] == "chatdetails") {
        $sql_list = "SELECT * FROM " . ERP_CUSTOMER_ADDRESS . " WHERE customer_id=$custId AND customer_address_primary_flag=1 ";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }

    // overview 


    if ($_GET['act'] == "AgeingHead") {
        $sql_list = "SELECT * FROM " . ERP_CUSTOMER_ADDRESS . " WHERE customer_id=$custId AND customer_address_primary_flag=1 ";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }

    // mail tab ajax json send

    if ($_GET['act'] == "Mail") {
        $ccode = $_GET['ccode'];
        $sql_list = "SELECT * FROM `erp_globalmail` WHERE `partyCode` = '" . $ccode . "' AND `status`='active' ORDER BY `email_id` DESC LIMIT 50";
        $sqlObject = $dbObj->queryGet($sql_list, true);
        $res = [];
        if ($sqlObject['numRows'] > 0) {
            $res = ["status" => "success", "message" => "Data found", "data" => $sqlObject['data']];
        } else {
            $res = ["status" => "warning", "message" => "Data not found", "sql" => $sql_list];
        }
        echo json_encode($res);
    }

    // mail tab submit 

    if ($_GET['act'] == "Mailsubmit") {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $customer_id = $_GET['id'];
        $shootingDays = $_GET['shootingDays'];
        $operator = $_GET['operator'];

        $sql_list = "INSERT INTO `" . ERP_SETTINGS_EMAIL_CUSTOMER_INVOICE . "` 
                            SET
                                `company_id`='$company_id',
                                `branch_id`='$branch_id',
                                `location_id`='$location_id',
                                `customer_id`='$customer_id',
                                `days`='$shootingDays',
                                `operators`='$operator',
                                `created_by`='$created_by',
                                `updated_by`='$updated_by'
                ";
        $sqlObject = $dbObj->queryInsert($sql_list);
        $res = [];
        if ($sqlObject['status'] == "success") {
            $res = ["message" => $sqlObject['message']];
        } else {
            $res = ["message" => $sqlObject['message']];
        }
        echo json_encode($res);
    }

    if ($_GET['act'] == "Mailsent") {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $companyCodeNav;
        global $companyNameNav;
        $customer_id = $_GET['id'];
        $encode_customer_id=base64_encode($customer_id);
        $encode_company_id=base64_encode($company_id);
        $details=queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = $customer_id AND `company_id` = $company_id")['data'];
        $customer_code=$details['customer_code'];
        $customer_authorised_person_email=$details['customer_authorised_person_email'];
        $trade_name=$details['trade_name'];
        $mailStatus=$details['isMailValid'];
        if($mailStatus=='no'){
        $sub = "Mail Verification";
        $msg = "Dear $trade_name,<br>			
        This is verification mail.<br>
        <b>Company Code: </b>" . $companyCodeNav . "<br>
        <b>Customer Code: </b>" . $customer_code . "<br>          
        To validate your mail, <a href='" . BASE_URL . "branch/location/mailVarification_customer.php?id=$encode_customer_id&c_id=$encode_company_id'>Click Here</a><br><br>
    
        Best regards,  $companyNameNav";
        $mail = SendMailByMySMTPmailTemplate($customer_authorised_person_email, $sub, $msg, null, $customer_code, 'customerAdd', $customer_id, $customer_code);
        if ($mail) {
            echo json_encode(["status" => "success", "message" => "Mail sent successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to send email"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Mail already verified"]);
    }
    }
}
