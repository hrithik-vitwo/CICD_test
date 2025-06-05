<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../pagination/common-pagination.php");
require_once("../../../common/exportexcel-new.php");
$headerData = array('Content-Type: application/json');



if ($_POST['act'] == 'detailedTransactional') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $limit_per_Page = isset($_POST['limit']) && $_POST['limit'] != '' ? $_POST['limit'] : 25;

        $page_no = isset($_POST['pageNo']) ? (int) $_POST['pageNo'] : 1;
        $page_no = max(1, $page_no);

        $offset = ($page_no - 1) * $limit_per_Page;
        $maxPagesl = $page_no * $limit_per_Page;
        $startPageSL = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;
        $formObj = $_POST['formDatas'];
        $cond = "";
        $dateFormArr = [];
        $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);
        if (!isset($_POST['from_date']) || empty($_POST['from_date'])) {
            $_POST['from_date'] = date('Y-m-d', strtotime('-1 day'));
            $_POST['to_date'] = date('Y-m-d');

            $start_date = $_POST['from_date'];
            $end_date = $_POST['to_date'];
        } else {
            //echo 1;
            if (isset($_POST['from_date']) || (count($_SESSION["reportFilter"] ?? []) > 0)) {
                $start_date = $_POST['from_date'] ?? $_SESSION["reportFilter"]["from_date"];
                $end_date = $_POST['to_date'] ?? $_SESSION["reportFilter"]["to_date"];
                $dateFormArr['from_date'] = $start_date;
                $dateFormArr['to_date'] = $end_date;
                $_SESSION["reportFilter"] = $dateFormArr;
            } 
            // else {
            //     $start = explode('-', $variant_sql['data'][0]['year_start']);
            //     $end = explode('-', $variant_sql['data'][0]['year_end']);
            //     $start_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
            //     $end_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
            //     if (isset($_GET["to_date"]) && $_GET["to_date"] != "") {
            //         $end_date = $_GET["to_date"];
            //     }
            //     $_POST['from_date'] = $start_date;
            //     $_POST['to_date'] = $end_date;
            //     $_POST['drop_val'] = 'fYDropdown';
            //     $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
            // }
        }
        // $cond = "AND DATE(so_date) BETWEEN '" . $previousDate . "' AND '" . $currentDate . "'";


        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalQuantity;
            global $decimalValue;
            // Handle date fields correctly
            if (in_array($slag, ['updated_at', 'created_at', 'journal_created_at' , 'postingDate' ,'summary1.document_date'  ])) {
                // $new_slag = 'varient.' . $slag;
                
                if ($data['operatorName'] === 'BETWEEN' && is_array($data['value'])) {
                    $conds .= " DATE($slag) BETWEEN '" . $data['value']['fromDate'] . "' AND '" . $data['value']['toDate'] . "' ";
                } else {
                    $conds .= " DATE($slag) " . $data['operatorName'] . " '" . $data['value'] . "' ";
                }
            }
            // Handle 'created_by' and 'updated_by' conditions

            else if ($slag === 'journal_created_by') {
                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
            }
        
            // Handle minimum_valueQuantity condition       
            // General fallback condition, EXCLUDING date fields
            else if (!in_array($slag, ['updated_at', 'created_at', 'journal_created_at' , 'postingDate' ,'summary1.document_date'])) {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }
        
            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));
        
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }
        



        // $sts = " AND `so`.status !='deleted'";


        $sql_list = "SELECT summary1.*,
                    CASE
                        
                        WHEN Order_num LIKE 'PO%' THEN (SELECT po_date FROM erp_branch_purchase_order WHERE po_number = summary1.Order_num AND company_id=$company_id)
                            WHEN Order_num LIKE 'SO%' THEN (SELECT so_date FROM erp_branch_sales_order WHERE so_number = summary1.Order_num AND company_id=$company_id)
                        END as order_date
                    FROM
                        (SELECT
                            table1.jid as jid,
                            table1.company_id as company_id,
                            table1.branch_id as branch_id,
                            table1.location_id as location_id,
                            table1.jv_no as jv_no,
                            table1.party_code AS party_code,
                            table1.party_name AS party_name,
                            table1.refarenceCode as referenceCode,
                            table1.parent_id AS parent_id,
                            table1.parent_slug AS parent_slug,
                            table1.journal_entry_ref as journal_entry_ref,
                            table1.documentNo as documentNo,
                            table1.order_no as Order_num,
                            table1.documentDate as document_date,
                            table1.postingDate as postingDate,
                            table1.remark as remark,
                            table1.glId as glId,
                            coa.gl_code as gl_code,
                            coa.gl_label as gl_label,
                            table1.sub_gl_code,
                            table1.sub_gl_name,
                            coa.typeAcc as typeAcc,
                            table1.Amount as Amount,
                            table1.Type as type,
                            table1.journal_created_at as journal_created_at,
                            table1.journal_created_by as journal_created_by,
                            table1.journal_updated_at as journal_updated_at,
                            table1.journal_updated_by as journal_updated_by
                        FROM ( 
                          (SELECT *,
                            CASE
                                WHEN parent_slug ='PGI' THEN (SELECT so_number FROM erp_branch_sales_order_delivery_pgi WHERE so_delivery_pgi_id = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'SOInvoicing' THEN (SELECT so_number FROM erp_branch_sales_order_invoices WHERE so_invoice_id = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'grn' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'grniv' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = main_report.parent_id LIMIT 1)
                            END as Order_no
                        FROM
                        (SELECT
                                journal.id AS jid,
                                journal.company_id AS company_id,
                                journal.branch_id AS branch_id,
                                journal.location_id AS location_id,
                                journal.jv_no AS jv_no,
                                journal.party_code AS party_code,
                                journal.party_name AS party_name,
                                journal.refarenceCode AS refarenceCode,
                                journal.parent_id AS parent_id,
                                journal.parent_slug AS parent_slug,
                                journal.journalEntryReference as journal_entry_ref,
                                journal.documentNo AS documentNo,
                                journal.documentDate AS documentDate,
                                journal.postingDate AS postingDate,
                                journal.remark AS remark,
                                journal.journal_status AS journal_status,
                                debit.glId AS glId, 
                                debit.subGlCode AS sub_gl_code,
                                debit.subGlName AS sub_gl_name,
                                debit.debit_amount AS Amount,
                                'DR' AS Type,
                                journal.journal_created_at as journal_created_at,
                                journal.journal_created_by as journal_created_by,
                                journal.journal_updated_at as journal_updated_at,
                                journal.journal_updated_by as journal_updated_by
                            FROM
                                `" . ERP_ACC_JOURNAL . "` AS journal
                            INNER JOIN
                                (SELECT journal_id,glId,debit_amount,subGlCode,subGlName FROM `" . ERP_ACC_DEBIT . "`) AS debit
                                ON
                                    debit.journal_id = journal.id
                            WHERE
                                journal.journal_status='active' AND
                                journal.company_id=$company_id AND
                                journal.branch_id=$branch_id AND
                                journal.location_id=$location_id AND
                                journal.postingDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' " . $drcond . ") AS main_report)
                            UNION ALL
                            (SELECT *,
                                CASE
                                    WHEN parent_slug ='PGI' THEN (SELECT so_number FROM erp_branch_sales_order_delivery_pgi WHERE so_delivery_pgi_id = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'SOInvoicing' THEN (SELECT so_number FROM erp_branch_sales_order_invoices WHERE so_invoice_id = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'grn' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'grniv' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = mainReport.parent_id LIMIT 1)
                                END as Order_no
                            FROM
                            (SELECT
                                journal.id AS jid,
                                journal.company_id AS company_id,
                                journal.branch_id AS branch_id,
                                journal.location_id AS location_id,
                                journal.jv_no AS jv_no,
                                journal.party_code AS party_code,
                                journal.party_name AS party_name,
                                journal.refarenceCode AS refarenceCode,
                                journal.parent_id AS parent_id,
                                journal.parent_slug AS parent_slug,
                                journal.journalEntryReference as journal_entry_ref,
                                journal.documentNo AS documentNo,
                                journal.documentDate AS documentDate,
                                journal.postingDate AS postingDate,
                                journal.remark AS remark,
                                journal.journal_status AS journal_status,
                                credit.glId AS glId,
                                credit.subGlCode AS sub_gl_code,
                                credit.subGlName AS sub_gl_name,
                                credit.credit_amount*(-1) AS Amount,
                                'CR' AS Type,
                                journal.journal_created_at as journal_created_at,
                                journal.journal_created_by as journal_created_by,
                                journal.journal_updated_at as journal_updated_at,
                                journal.journal_updated_by as journal_updated_by
                            FROM
                                `" . ERP_ACC_JOURNAL . "` AS journal
                            INNER JOIN
                                (SELECT journal_id,glId,credit_amount,subGlCode,subGlName FROM `" . ERP_ACC_CREDIT . "`) AS credit
                                ON
                                    credit.journal_id = journal.id
                            WHERE
                                journal.journal_status='active' AND
                                journal.company_id=$company_id AND
                                journal.branch_id=$branch_id AND
                                journal.location_id=$location_id AND
                                journal.postingDate BETWEEN '" . $start_date . "' AND '" . $end_date . "'  " . $crcond . ") as mainReport)) as table1
                            INNER JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` as coa
                            ON table1.glId = coa.id
                            ) AS summary1 WHERE 1 ".$cond." ORDER BY summary1.jid DESC";

        $sql_Mainqry = $sql_list . " LIMIT " . $offset . "," . $limit_per_Page . ";";
        $sqlMainQryObj = queryGet($sql_Mainqry, true);

        $dynamic_data = [];
        $num_list = $sqlMainQryObj['numRows'];
        $sql_data = $sqlMainQryObj['data'];
        $output = "";
        $limitText = "";
        $sl = ($page_no == 1) ? 1 : ($maxPagesl - $limit_per_Page) + 1;

        // console($sql_data);
        // console($sql_data);

        if ($num_list > 0) {
            foreach ($sql_data as $data) {
                // console($data);
                $ClearingDocumentNo = '-';
                $ClearingDocumentDate = '-';
                $ClearedBy = '-';

                // echo $data['minimumDetails'];


                $dynamic_data[] = [
                    "sl_no" => $sl,                                    // your row counter
                    "branchName" => $branchNameNav,                         // from your nav var
                    "locationName" => $locationNameNav,                       // same
                    "jv_no" => $data['jv_no'],
                    "documentNo" => $data['documentNo'],
                    "referenceCode" => $data['referenceCode'],                // alias referenceCode
                    "postingDate" => formatDateORDateTime($data['postingDate']),
                    "journal_created_at" => formatDateORDateTime($data['journal_created_at']),
                    "journal_created_by" => getCreatedByUser($data['journal_created_by']),
                    "Order_num" => ($data['Order_num'] ?? '-'),
                    "summary1.document_date" => formatDateORDateTime($data['document_date']),
                    "party_code" => ($data['party_code']   ?? '-'),
                    "party_name" => ($data['party_name']   ?? '-'),
                    "gl_code" => $data['gl_code'],                       // from joined coa table
                    "gl_label" => $data['gl_label'],
                    "sub_gl_code" => $data['sub_gl_code'],
                    "sub_gl_name" => $data['sub_gl_name'],
                    "journal_entry_ref" => $data['journal_entry_ref'],
                    "remark" =>WordLimiter($data['remark'] , 5),
                    "type" => $data['type'],                          // case-sensitive alias
                    "journal.amount" => $data['Amount'],
                    "journal.ClearingDocumentNo" => $ClearingDocumentNo,                    // your computed vars
                    "journal.ClearingDocumentDate" => $ClearingDocumentDate,
                    "journal.ClearedBy" => $ClearedBy,
                ];

                $sl++;
            }
            $output .= "</table>";
            $sqlRowCount = "SELECT COUNT(*) as row_count FROM (" . $sql_list . ") AS subquery;";

            // $sqlRowCount = ""
            $queryset = queryGet($sqlRowCount);
            $totalRows = $queryset['data']['row_count'];
            $total_page = ceil($totalRows / $limit_per_Page);
            $output .= pagiNation($page_no, $total_page);

            $limitText .= '<a class="active" id="limitText">Showing ' . $startPageSL . ' to ' . $maxPagesl . ' of ' . $totalRows . ' entries </a>';

            // console($sqlRowCount);

            $res = [
                "status" => true,
                "msg" => "Success",
                "data" => $dynamic_data,
                "pagination" => $output,
                "limitTxt" => $limitText,
                "sqlMain" => $sql_data,
                "the_query" => $sqlMainQryObj['sql'],
                "row_count" => $queryset['sql'],
                "formObj"=>$formObj,
                "totalRows"=>$totalRows

            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list,
                "sqlMain" => $sqlMainQryObj,
                // "conds" => console($cond)
            ];
        }

        echo json_encode($res);
    }
}

if ($_POST['act'] == 'alldata') {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $formObj = $_POST['formDatas'];
        $cond = "";


        $dateFormArr = [];
        $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);
        if (!isset($_POST['from_date']) || empty($_POST['from_date'])) {
            $_POST['from_date'] = date('Y-m-d', strtotime('-1 day'));
            $_POST['to_date'] = date('Y-m-d');

            $start_date = $_POST['from_date'];
            $end_date = $_POST['to_date'];
        } else {
            //echo 1;
            if (isset($_POST['from_date']) || (count($_SESSION["reportFilter"] ?? []) > 0)) {
                $start_date = $_POST['from_date'] ?? $_SESSION["reportFilter"]["from_date"];
                $end_date = $_POST['to_date'] ?? $_SESSION["reportFilter"]["to_date"];
                $dateFormArr['from_date'] = $start_date;
                $dateFormArr['to_date'] = $end_date;
                $_SESSION["reportFilter"] = $dateFormArr;
            } 
            // else {
            //     $start = explode('-', $variant_sql['data'][0]['year_start']);
            //     $end = explode('-', $variant_sql['data'][0]['year_end']);
            //     $start_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
            //     $end_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
            //     if (isset($_GET["to_date"]) && $_GET["to_date"] != "") {
            //         $end_date = $_GET["to_date"];
            //     }
            //     $_POST['from_date'] = $start_date;
            //     $_POST['to_date'] = $end_date;
            //     $_POST['drop_val'] = 'fYDropdown';
            //     $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
            // }
        }

        $implodeFrom = implode('', array_map(function ($slag, $data) {
            $conds = "";
            global $decimalQuantity;
            global $decimalValue;
            // Handle date fields correctly
            if (in_array($slag, ['updated_at', 'created_at', 'journal_created_at' , 'postingDate' ,'summary1.document_date'  ])) {
                // $new_slag = 'varient.' . $slag;
                
                if ($data['operatorName'] === 'BETWEEN' && is_array($data['value'])) {
                    $conds .= " DATE($slag) BETWEEN '" . $data['value']['fromDate'] . "' AND '" . $data['value']['toDate'] . "' ";
                } else {
                    $conds .= " DATE($slag) " . $data['operatorName'] . " '" . $data['value'] . "' ";
                }
            }
            // Handle 'created_by' and 'updated_by' conditions

            else if ($slag === 'journal_created_by') {
                $resultList = getAdminUserIdByName($data['value']);
                if(empty($resultList)){
                    $resultList = 0;
                }
                $operator = ($data['operatorName'] == "LIKE") ? "IN" : "NOT IN";
                $conds .= $slag . " " . $operator . " (" . $resultList . ")";
            }
        
            // Handle minimum_valueQuantity condition       
            // General fallback condition, EXCLUDING date fields
            else if (!in_array($slag, ['updated_at', 'created_at', 'journal_created_at' , 'postingDate' ,'summary1.document_date'])) {
                $conds .= $slag . " " . $data['operatorName'] . " '%" . $data['value'] . "%'";
            }
        
            return !empty($data['value']) ? " AND " . $conds : "";
        }, array_keys($formObj), $formObj));
        
        if (!empty($implodeFrom)) {
            $cond .= $implodeFrom;
        }

        $sql_list = "SELECT summary1.*,
                    CASE
                        
                        WHEN Order_num LIKE 'PO%' THEN (SELECT po_date FROM erp_branch_purchase_order WHERE po_number = summary1.Order_num AND company_id=$company_id)
                            WHEN Order_num LIKE 'SO%' THEN (SELECT so_date FROM erp_branch_sales_order WHERE so_number = summary1.Order_num AND company_id=$company_id)
                        END as order_date
                    FROM
                        (SELECT
                            table1.jid as jid,
                            table1.company_id as company_id,
                            table1.branch_id as branch_id,
                            table1.location_id as location_id,
                            table1.jv_no as jv_no,
                            table1.party_code AS party_code,
                            table1.party_name AS party_name,
                            table1.refarenceCode as referenceCode,
                            table1.parent_id AS parent_id,
                            table1.parent_slug AS parent_slug,
                            table1.journal_entry_ref as journal_entry_ref,
                            table1.documentNo as documentNo,
                            table1.order_no as Order_num,
                            table1.documentDate as document_date,
                            table1.postingDate as postingDate,
                            table1.remark as remark,
                            table1.glId as glId,
                            coa.gl_code as gl_code,
                            coa.gl_label as gl_label,
                            table1.sub_gl_code,
                            table1.sub_gl_name,
                            coa.typeAcc as typeAcc,
                            table1.Amount as Amount,
                            table1.Type as type,
                            table1.journal_created_at as journal_created_at,
                            table1.journal_created_by as journal_created_by,
                            table1.journal_updated_at as journal_updated_at,
                            table1.journal_updated_by as journal_updated_by
                        FROM ( 
                          (SELECT *,
                            CASE
                                WHEN parent_slug ='PGI' THEN (SELECT so_number FROM erp_branch_sales_order_delivery_pgi WHERE so_delivery_pgi_id = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'SOInvoicing' THEN (SELECT so_number FROM erp_branch_sales_order_invoices WHERE so_invoice_id = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'grn' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = main_report.parent_id LIMIT 1)
                                WHEN parent_slug = 'grniv' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = main_report.parent_id LIMIT 1)
                            END as Order_no
                        FROM
                        (SELECT
                                journal.id AS jid,
                                journal.company_id AS company_id,
                                journal.branch_id AS branch_id,
                                journal.location_id AS location_id,
                                journal.jv_no AS jv_no,
                                journal.party_code AS party_code,
                                journal.party_name AS party_name,
                                journal.refarenceCode AS refarenceCode,
                                journal.parent_id AS parent_id,
                                journal.parent_slug AS parent_slug,
                                journal.journalEntryReference as journal_entry_ref,
                                journal.documentNo AS documentNo,
                                journal.documentDate AS documentDate,
                                journal.postingDate AS postingDate,
                                journal.remark AS remark,
                                journal.journal_status AS journal_status,
                                debit.glId AS glId, 
                                debit.subGlCode AS sub_gl_code,
                                debit.subGlName AS sub_gl_name,
                                debit.debit_amount AS Amount,
                                'DR' AS Type,
                                journal.journal_created_at as journal_created_at,
                                journal.journal_created_by as journal_created_by,
                                journal.journal_updated_at as journal_updated_at,
                                journal.journal_updated_by as journal_updated_by
                            FROM
                                `" . ERP_ACC_JOURNAL . "` AS journal
                            INNER JOIN
                                (SELECT journal_id,glId,debit_amount,subGlCode,subGlName FROM `" . ERP_ACC_DEBIT . "`) AS debit
                                ON
                                    debit.journal_id = journal.id
                            WHERE
                                journal.journal_status='active' AND
                                journal.company_id=$company_id AND
                                journal.branch_id=$branch_id AND
                                journal.location_id=$location_id AND
                                journal.postingDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' " . $drcond . ") AS main_report)
                            UNION ALL
                            (SELECT *,
                                CASE
                                    WHEN parent_slug ='PGI' THEN (SELECT so_number FROM erp_branch_sales_order_delivery_pgi WHERE so_delivery_pgi_id = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'SOInvoicing' THEN (SELECT so_number FROM erp_branch_sales_order_invoices WHERE so_invoice_id = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'grn' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = mainReport.parent_id LIMIT 1)
                                    WHEN parent_slug = 'grniv' THEN (SELECT grnPoNumber FROM erp_grn WHERE grnId = mainReport.parent_id LIMIT 1)
                                END as Order_no
                            FROM
                            (SELECT
                                journal.id AS jid,
                                journal.company_id AS company_id,
                                journal.branch_id AS branch_id,
                                journal.location_id AS location_id,
                                journal.jv_no AS jv_no,
                                journal.party_code AS party_code,
                                journal.party_name AS party_name,
                                journal.refarenceCode AS refarenceCode,
                                journal.parent_id AS parent_id,
                                journal.parent_slug AS parent_slug,
                                journal.journalEntryReference as journal_entry_ref,
                                journal.documentNo AS documentNo,
                                journal.documentDate AS documentDate,
                                journal.postingDate AS postingDate,
                                journal.remark AS remark,
                                journal.journal_status AS journal_status,
                                credit.glId AS glId,
                                credit.subGlCode AS sub_gl_code,
                                credit.subGlName AS sub_gl_name,
                                credit.credit_amount*(-1) AS Amount,
                                'CR' AS Type,
                                journal.journal_created_at as journal_created_at,
                                journal.journal_created_by as journal_created_by,
                                journal.journal_updated_at as journal_updated_at,
                                journal.journal_updated_by as journal_updated_by
                            FROM
                                `" . ERP_ACC_JOURNAL . "` AS journal
                            INNER JOIN
                                (SELECT journal_id,glId,credit_amount,subGlCode,subGlName FROM `" . ERP_ACC_CREDIT . "`) AS credit
                                ON
                                    credit.journal_id = journal.id
                            WHERE
                                journal.journal_status='active' AND
                                journal.company_id=$company_id AND
                                journal.branch_id=$branch_id AND
                                journal.location_id=$location_id AND
                                journal.postingDate BETWEEN '" . $start_date . "' AND '" . $end_date . "'  " . $crcond . ") as mainReport)) as table1
                            INNER JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` as coa
                            ON table1.glId = coa.id
                            ) AS summary1 WHERE 1 ".$cond." ORDER BY summary1.jid DESC LIMIT  0,10000";

        $dynamic_data_all = [];
        $sqlMainQryObjall = queryGet($sql_list, true);
        $sql_data_all = $sqlMainQryObjall['data'];
        $num_list = $sqlMainQryObjall['numRows'];
        if ($num_list > 0) {
            $sl = 1;
            foreach ($sql_data_all as $data) {


                $ClearingDocumentNo = '-';
                $ClearingDocumentDate = '-';
                $ClearedBy = '-';



                $dynamic_data_all[] = [
                    "sl_no" => $sl,                                    // your row counter
                    "branchName" => $branchNameNav,                         // from your nav var
                    "locationName" => $locationNameNav,                       // same
                    "jv_no" => $data['jv_no'],
                    "documentNo" => $data['documentNo'],
                    "referenceCode" => $data['referenceCode'],                // alias referenceCode
                    "postingDate" => formatDateORDateTime($data['postingDate']),
                    "journal_created_at" => formatDateORDateTime($data['journal_created_at']),
                    "journal_created_by" => getCreatedByUser($data['journal_created_by']),
                    "Order_num" => ($data['Order_num'] ?? '-'),
                    "summary1.document_date" => formatDateORDateTime($data['document_date']),
                    "party_code" => ($data['party_code']   ?? '-'),
                    "party_name" => ($data['party_name']   ?? '-'),
                    "gl_code" => $data['gl_code'],                       // from joined coa table
                    "gl_label" => $data['gl_label'],
                    "sub_gl_code" => $data['sub_gl_code'],
                    "sub_gl_name" => $data['sub_gl_name'],
                    "journal_entry_ref" => $data['journal_entry_ref'],
                    "remark" =>WordLimiter($data['remark'] , 5),
                    "type" => $data['type'],                          // case-sensitive alias
                    "journal.amount" => $data['Amount'],
                    "journal.ClearingDocumentNo" => $ClearingDocumentNo,                    // your computed vars
                    "journal.ClearingDocumentDate" => $ClearingDocumentDate,
                    "journal.ClearedBy" => $ClearedBy,
                ];
                $sl++;
            }

            $dynamic_data_all=json_encode($dynamic_data_all);
            $exportToExcelAll =exportToExcelAll($dynamic_data_all,$_POST['coloum'],$_POST['sql_data_checkbox']);
            $res = [
                "status" => true,
                "msg" => "CSV all generated",
                'csvContentall' => $exportToExcelAll,
                "sql" => $sql_list,
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
}