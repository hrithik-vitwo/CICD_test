<?php
require_once("../../../app/v1/connection-branch-admin.php");
$pageName = basename($_SERVER['PHP_SELF'], '.php');
//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
  echo "Session Timeout";
  exit;
}
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");

// Add Functions
require_once("../../../app/v1/functions/branch/func-customers.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");


if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}



?>


<style>
  .chartContainer {
    width: 100%;
    height: 500px;
    margin-top: 6em;
  }

  .content-wrapper table tr:nth-child(2n+1) td {
    background: #b5c5d3;
  }

  tfoot.individual-search tr th {
    padding: 5px !important;
    border-right: 1px solid #fff !important;
  }

  .vertical-align {
    vertical-align: middle;
  }

  /* .green-text {
    color: #14ca14 !important;
    font-weight: 600;
  }

  .red-text {
    color: red !important;
    font-weight: 600;
  } */

  .dataTables_scrollHeadInner tr th {
    position: sticky;
    top: -1px;
  }

  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row {
    display: flex !important;
    align-items: center;
    justify-content: end;
  }

  /* div.dataTables_wrapper {
    overflow: hidden;
  } */

  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(1),
  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(3) {
    padding: 10px 20px;
  }

  div.dataTables_wrapper div.dataTables_length select {
    width: 60% !important;
    appearance: none !important;
    -webkit-appearance: none;
    -moz-appearance: none;
  }

  .dataTables_scroll {
    position: relative;
    margin-bottom: 10px;
  }

  .dataTables_scroll::-webkit-scrollbar {
    visibility: hidden;
  }

  .dataTables_scrollBody tfoot th {
    background: none !important;
  }

  .dataTables_scrollHead {
    margin-bottom: 40px;
  }

  .dataTables_scrollBody {
    max-height: 75vh !important;
    height: 75% !important;
    overflow: scroll !important;
  }

  .dataTables_scrollFoot {
    position: absolute;
    top: 37px;
    height: 50px;
    overflow-y: scroll;
  }

  div.dataTables_wrapper div.dataTables_filter input {
    margin-left: 10px;
  }

  div.dataTables_scrollFoot>.dataTables_scrollFootInner th {
    border: 0;
  }

  .dataTables_filter {
    padding-right: 0 !important;
  }

  div.dataTables_wrapper div.dataTables_paginate ul.pagination {
    padding: 0;
    border: 0;
  }

  .dt-top-container {
    display: flex;
    align-items: center;
    padding: 0 20px;
    gap: 0;
  }

  .transactional-book-table tr td {
    white-space: pre-line !important;
  }

  .dataTables_length {
    margin-left: 50px;
  }

  a.btn.add-col.setting-menu.waves-effect.waves-light {
    position: absolute !important;
    display: flex;
    justify-content: space-between;
    top: 10px !important;
  }

  div.dataTables_wrapper div.dataTables_length label {
    margin-bottom: 0;
  }

  div.dataTables_wrapper div.dataTables_info {
    padding-left: 20px;
    position: relative;
    top: 0;
  }

  .dataTables_paginate {
    position: relative;
    right: 20px;
    bottom: 20px;
    margin-top: -15px;
  }

  .dt-center-in-div {
    display: block;
    /* order: 3; */
    margin-left: auto;
  }

  .dt-buttons.btn-group.flex-wrap button {
    background-color: #003060 !important;
    border-color: #003060 !important;
    border-radius: 7px !important;
  }

  /* .setting-row .col .btn.setting-menu {
    position: absolute !important;
    right: 255px;
    top: 10px;
  } */

  .dt-buttons.btn-group.flex-wrap {
    gap: 10px;
  }


  table.dataTable>thead .sorting:before,
  table.dataTable>thead .sorting:after,
  table.dataTable>thead .sorting_asc:before,
  table.dataTable>thead .sorting_asc:after,
  table.dataTable>thead .sorting_desc:before,
  table.dataTable>thead .sorting_desc:after,
  table.dataTable>thead .sorting_asc_disabled:before,
  table.dataTable>thead .sorting_asc_disabled:after,
  table.dataTable>thead .sorting_desc_disabled:before,
  table.dataTable>thead .sorting_desc_disabled:after {

    display: block !important;

  }

  .dataTable thead tr th,
  .dataTable tfoot.individual-search tr th {
    padding-right: 30px !important;
    border-right: 0 !important;
  }

  select.fy-dropdown {
    max-width: 100px;
  }

  .report-wrapper .daybook-filter-list.filter-list {
    display: flex;
    gap: 6px;
    justify-content: flex-start;
    position: relative;
    top: 45px;
    left: 255px;
    float: right;
  }

  .daybook-filter-list.filter-list a.active {
    background-color: #003060;
    color: #fff;
  }

  .date-range-input {
    gap: 7px;
  }

  .date-range-input .form-input {
    width: 100%;
  }

  .report-wrapper table tr td {
    background: #e7ebef;
  }

  .reports-card .filter-list a {
    background: #dedede;
    color: #003060;
    z-index: 9;
  }

  .report-wrapper .reports-card {
    background: #fff;
  }

  .report-wrapper table tr:nth-child(2n+1) td {
    background: #ffffff;
  }

  .label-select {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  @media (max-width: 769px) {
    .dt-buttons.btn-group.flex-wrap {
      gap: 10px;
      position: absolute;
      top: -39px;
      right: 60px;
    }

    .dt-buttons.btn-group.flex-wrap button {
      max-width: 60px;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
      margin-top: -10px;
    }


  }

  @media (max-width :575px) {
    .dataTables_scrollFoot {
      position: absolute;
      top: 28px;
    }

    .dt-top-container {
      display: flex;
      align-items: baseline;
      padding: 0 20px;
      gap: 20px;
      flex-direction: column-reverse;
      flex-wrap: nowrap;
    }

    .dataTables_length {
      margin-left: 0;
      margin-bottom: 1em;
    }



    div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    }

    .dt-center-in-div {
      margin: 3px auto;
    }

    div.dataTables_filter {
      right: 0;
      margin-top: 0;
      position: relative;
      right: -43px;
    }

    .dt-buttons.btn-group.flex-wrap {
      gap: 10px;
      position: relative;
      top: 0;
      right: 0;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
      margin-top: 40px;
    }

    .dataTables_length label {
      font-size: 0;
    }
  }

  @media (max-width: 376px) {
    div.dataTables_wrapper div.dataTables_filter {
      margin-top: 0;
      padding-left: 0 !important;
    }



    div.dataTables_wrapper div.dataTables_filter input {
      max-width: 150px;
    }

    select.fy-dropdown {
      max-width: 100px;
    }



    /* div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    } */
  }
</style>

<link rel="stylesheet" href="../../../public/assets/listing.css">
<link rel="stylesheet" href="../../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<!-- Resources -->
<script src="../../../public/assets/core.js"></script>
<script src="../../../public/assets/charts.js"></script>
<script src="../../../public/assets/animated.js"></script>
<script src="../../../public/assets/forceDirected.js"></script>
<script src="../../../public/assets/sunburst.js"></script>


<?php
$variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

if (isset($_POST['from_date'])) {
  $f_date = $_POST['from_date'];
  $to_date = $_POST['to_date'];
  //echo 1;


} else {

  $start = explode('-', $variant_sql['data'][0]['year_start']);
  $end = explode('-', $variant_sql['data'][0]['year_end']);
  $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
  $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
  $_POST['from_date'] = $f_date;
  $_POST['to_date'] = $to_date;
  $_POST['drop_val'] = 'fYDropdown';
  $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
}


$customer_gl = "SELECT
SUM(summary1.amount) AS gl_total_amount
FROM
(
SELECT
    table1.jid AS jid,
    table1.company_id AS company_id,
    table1.branch_id AS branch_id,
    table1.location_id AS location_id,
    table1.jv_no AS jv_no,
    table1.party_code AS party_code,
    table1.party_name AS party_name,
    table1.refarenceCode AS referenceCode,
    table1.parent_id AS parent_id,
    table1.parent_slug AS parent_slug,
    table1.journal_entry_ref AS journal_entry_ref,
    table1.documentNo AS documentNo,
    table1.order_no AS Order_num,
    table1.documentDate AS document_date,
    table1.postingDate AS postingDate,
    table1.remark AS remark,
    table1.glId AS glId,
    coa.gl_code AS gl_code,
    coa.gl_label AS gl_label,
    coa.typeAcc AS typeAcc,
    table1.Amount AS Amount,
    table1.Type AS TYPE,
    table1.journal_created_at AS journal_created_at,
    table1.journal_created_by AS journal_created_by,
    table1.journal_updated_at AS journal_updated_at,
    table1.journal_updated_by AS journal_updated_by
FROM
    (
        (
        SELECT
            *,
            CASE WHEN parent_slug = 'PGI' THEN(
            SELECT
                so_number
            FROM
                erp_branch_sales_order_delivery_pgi
            WHERE
                so_delivery_pgi_id = main_report.parent_id
            LIMIT 1
        ) WHEN parent_slug = 'SOInvoicing' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_invoices
        WHERE
            so_invoice_id = main_report.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'grn' THEN(
    SELECT
        grnPoNumber
    FROM
        erp_grn
    WHERE
        grnId = main_report.parent_id
    LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = main_report.parent_id
LIMIT 1
)
    END AS Order_no
FROM
    (
    SELECT
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
        journal.journalEntryReference AS journal_entry_ref,
        journal.documentNo AS documentNo,
        journal.documentDate AS documentDate,
        journal.postingDate AS postingDate,
        journal.remark AS remark,
        journal.journal_status AS journal_status,
        debit.glId AS glId,
        debit.debit_amount AS Amount,
        'DR' AS TYPE,
        journal.journal_created_at AS journal_created_at,
        journal.journal_created_by AS journal_created_by,
        journal.journal_updated_at AS journal_updated_at,
        journal.journal_updated_by AS journal_updated_by
    FROM
        `erp_acc_journal` AS journal
    INNER JOIN(
        SELECT
            journal_id,
            glId,
            SUM(debit_amount) AS debit_amount
        FROM
            `erp_acc_debit`
        GROUP BY
            journal_id,
            glId
    ) AS debit
ON
    debit.journal_id = journal.id
WHERE
    journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS main_report
    )
UNION
    (
    SELECT
        *,
        CASE WHEN parent_slug = 'PGI' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_delivery_pgi
        WHERE
            so_delivery_pgi_id = mainReport.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'SOInvoicing' THEN(
    SELECT
        so_number
    FROM
        erp_branch_sales_order_invoices
    WHERE
        so_invoice_id = mainReport.parent_id
    LIMIT 1
) WHEN parent_slug = 'grn' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
)
END AS Order_no
FROM
(
SELECT
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
    journal.journalEntryReference AS journal_entry_ref,
    journal.documentNo AS documentNo,
    journal.documentDate AS documentDate,
    journal.postingDate AS postingDate,
    journal.remark AS remark,
    journal.journal_status AS journal_status,
    credit.glId AS glId,
    credit.credit_amount *(-1) AS Amount,
    'CR' AS TYPE,
    journal.journal_created_at AS journal_created_at,
    journal.journal_created_by AS journal_created_by,
    journal.journal_updated_at AS journal_updated_at,
    journal.journal_updated_by AS journal_updated_by
FROM
    `erp_acc_journal` AS journal
INNER JOIN(
    SELECT
        journal_id,
        glId,
        SUM(credit_amount) AS credit_amount
    FROM
        `erp_acc_credit`
    GROUP BY
        journal_id,
        glId
) AS credit
ON
credit.journal_id = journal.id
WHERE
journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS mainReport
)
) AS table1
INNER JOIN `erp_acc_coa_1_table` AS coa
ON
table1.glId = coa.id
ORDER BY
table1.jid
DESC
) AS summary1
WHERE summary1.parent_slug='SOInvoicing' AND summary1.glId=(SELECT customer_gl FROM erp_acc_gl_mapping WHERE company_id=$company_id);";
$customer_gl_queryset = queryGet($customer_gl);
// console($customer_gl_queryset['data']);


$customer_sl = "SELECT SUM(invoices.all_total_amt) FROM erp_branch_sales_order_invoices AS invoices INNER JOIN erp_customer AS customer ON invoices.customer_id=customer.customer_id WHERE invoices.invoice_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND invoices.status='active' AND invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id;";
$customer_sl_queryset = queryGet($customer_sl);
// console($customer_sl_queryset['data']);


$vendor_gl = "SELECT
SUM(summary1.amount) AS gl_total_amount
FROM
(
SELECT
    table1.jid AS jid,
    table1.company_id AS company_id,
    table1.branch_id AS branch_id,
    table1.location_id AS location_id,
    table1.jv_no AS jv_no,
    table1.party_code AS party_code,
    table1.party_name AS party_name,
    table1.refarenceCode AS referenceCode,
    table1.parent_id AS parent_id,
    table1.parent_slug AS parent_slug,
    table1.journal_entry_ref AS journal_entry_ref,
    table1.documentNo AS documentNo,
    table1.order_no AS Order_num,
    table1.documentDate AS document_date,
    table1.postingDate AS postingDate,
    table1.remark AS remark,
    table1.glId AS glId,
    coa.gl_code AS gl_code,
    coa.gl_label AS gl_label,
    coa.typeAcc AS typeAcc,
    table1.Amount AS Amount,
    table1.Type AS TYPE,
    table1.journal_created_at AS journal_created_at,
    table1.journal_created_by AS journal_created_by,
    table1.journal_updated_at AS journal_updated_at,
    table1.journal_updated_by AS journal_updated_by
FROM
    (
        (
        SELECT
            *,
            CASE WHEN parent_slug = 'PGI' THEN(
            SELECT
                so_number
            FROM
                erp_branch_sales_order_delivery_pgi
            WHERE
                so_delivery_pgi_id = main_report.parent_id
            LIMIT 1
        ) WHEN parent_slug = 'SOInvoicing' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_invoices
        WHERE
            so_invoice_id = main_report.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'grn' THEN(
    SELECT
        grnPoNumber
    FROM
        erp_grn
    WHERE
        grnId = main_report.parent_id
    LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = main_report.parent_id
LIMIT 1
)
    END AS Order_no
FROM
    (
    SELECT
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
        journal.journalEntryReference AS journal_entry_ref,
        journal.documentNo AS documentNo,
        journal.documentDate AS documentDate,
        journal.postingDate AS postingDate,
        journal.remark AS remark,
        journal.journal_status AS journal_status,
        debit.glId AS glId,
        debit.debit_amount AS Amount,
        'DR' AS TYPE,
        journal.journal_created_at AS journal_created_at,
        journal.journal_created_by AS journal_created_by,
        journal.journal_updated_at AS journal_updated_at,
        journal.journal_updated_by AS journal_updated_by
    FROM
        `erp_acc_journal` AS journal
    INNER JOIN(
        SELECT
            journal_id,
            glId,
            SUM(debit_amount) AS debit_amount
        FROM
            `erp_acc_debit`
        GROUP BY
            journal_id,
            glId
    ) AS debit
ON
    debit.journal_id = journal.id
WHERE
    journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS main_report
    )
UNION
    (
    SELECT
        *,
        CASE WHEN parent_slug = 'PGI' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_delivery_pgi
        WHERE
            so_delivery_pgi_id = mainReport.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'SOInvoicing' THEN(
    SELECT
        so_number
    FROM
        erp_branch_sales_order_invoices
    WHERE
        so_invoice_id = mainReport.parent_id
    LIMIT 1
) WHEN parent_slug = 'grn' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
)
END AS Order_no
FROM
(
SELECT
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
    journal.journalEntryReference AS journal_entry_ref,
    journal.documentNo AS documentNo,
    journal.documentDate AS documentDate,
    journal.postingDate AS postingDate,
    journal.remark AS remark,
    journal.journal_status AS journal_status,
    credit.glId AS glId,
    credit.credit_amount *(-1) AS Amount,
    'CR' AS TYPE,
    journal.journal_created_at AS journal_created_at,
    journal.journal_created_by AS journal_created_by,
    journal.journal_updated_at AS journal_updated_at,
    journal.journal_updated_by AS journal_updated_by
FROM
    `erp_acc_journal` AS journal
INNER JOIN(
    SELECT
        journal_id,
        glId,
        SUM(credit_amount) AS credit_amount
    FROM
        `erp_acc_credit`
    GROUP BY
        journal_id,
        glId
) AS credit
ON
credit.journal_id = journal.id
WHERE
journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS mainReport
)
) AS table1
INNER JOIN `erp_acc_coa_1_table` AS coa
ON
table1.glId = coa.id
ORDER BY
table1.jid
DESC
) AS summary1
WHERE summary1.parent_slug='grniv' AND summary1.glId=(SELECT vendor_gl FROM erp_acc_gl_mapping WHERE company_id=$company_id);";
$vendor_gl_queryset = queryGet($vendor_gl);
// console($vendor_gl_queryset['data']);


$vendor_sl = "SELECT SUM(grnTotalAmount) FROM erp_grninvoice WHERE companyId=$company_id AND branchId=$branch_id AND locationId=$location_id AND postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "';";
$vendor_sl_queryset = queryGet($vendor_sl);
// console($vendor_sl_queryset['data']);


$fg_inventory_gl = "SELECT
summary1.amount AS gl_total_amount
FROM
(
SELECT
    table1.jid AS jid,
    table1.company_id AS company_id,
    table1.branch_id AS branch_id,
    table1.location_id AS location_id,
    table1.jv_no AS jv_no,
    table1.party_code AS party_code,
    table1.party_name AS party_name,
    table1.refarenceCode AS referenceCode,
    table1.parent_id AS parent_id,
    table1.parent_slug AS parent_slug,
    table1.journal_entry_ref AS journal_entry_ref,
    table1.documentNo AS documentNo,
    table1.order_no AS Order_num,
    table1.documentDate AS document_date,
    table1.postingDate AS postingDate,
    table1.remark AS remark,
    table1.glId AS glId,
    coa.gl_code AS gl_code,
    coa.gl_label AS gl_label,
    coa.typeAcc AS typeAcc,
    table1.Amount AS Amount,
    table1.Type AS TYPE,
    table1.journal_created_at AS journal_created_at,
    table1.journal_created_by AS journal_created_by,
    table1.journal_updated_at AS journal_updated_at,
    table1.journal_updated_by AS journal_updated_by
FROM
    (
        (
        SELECT
            *,
            CASE WHEN parent_slug = 'PGI' THEN(
            SELECT
                so_number
            FROM
                erp_branch_sales_order_delivery_pgi
            WHERE
                so_delivery_pgi_id = main_report.parent_id
            LIMIT 1
        ) WHEN parent_slug = 'SOInvoicing' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_invoices
        WHERE
            so_invoice_id = main_report.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'grn' THEN(
    SELECT
        grnPoNumber
    FROM
        erp_grn
    WHERE
        grnId = main_report.parent_id
    LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = main_report.parent_id
LIMIT 1
)
    END AS Order_no
FROM
    (
    SELECT
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
        journal.journalEntryReference AS journal_entry_ref,
        journal.documentNo AS documentNo,
        journal.documentDate AS documentDate,
        journal.postingDate AS postingDate,
        journal.remark AS remark,
        journal.journal_status AS journal_status,
        debit.glId AS glId,
        debit.debit_amount AS Amount,
        'DR' AS TYPE,
        journal.journal_created_at AS journal_created_at,
        journal.journal_created_by AS journal_created_by,
        journal.journal_updated_at AS journal_updated_at,
        journal.journal_updated_by AS journal_updated_by
    FROM
        `erp_acc_journal` AS journal
    INNER JOIN(
        SELECT
            journal_id,
            glId,
            SUM(debit_amount) AS debit_amount
        FROM
            `erp_acc_debit`
        GROUP BY
            journal_id,
            glId
    ) AS debit
ON
    debit.journal_id = journal.id
WHERE
    journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS main_report
    )
UNION
    (
    SELECT
        *,
        CASE WHEN parent_slug = 'PGI' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_delivery_pgi
        WHERE
            so_delivery_pgi_id = mainReport.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'SOInvoicing' THEN(
    SELECT
        so_number
    FROM
        erp_branch_sales_order_invoices
    WHERE
        so_invoice_id = mainReport.parent_id
    LIMIT 1
) WHEN parent_slug = 'grn' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
)
END AS Order_no
FROM
(
SELECT
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
    journal.journalEntryReference AS journal_entry_ref,
    journal.documentNo AS documentNo,
    journal.documentDate AS documentDate,
    journal.postingDate AS postingDate,
    journal.remark AS remark,
    journal.journal_status AS journal_status,
    credit.glId AS glId,
    credit.credit_amount *(-1) AS Amount,
    'CR' AS TYPE,
    journal.journal_created_at AS journal_created_at,
    journal.journal_created_by AS journal_created_by,
    journal.journal_updated_at AS journal_updated_at,
    journal.journal_updated_by AS journal_updated_by
FROM
    `erp_acc_journal` AS journal
INNER JOIN(
    SELECT
        journal_id,
        glId,
        SUM(credit_amount) AS credit_amount
    FROM
        `erp_acc_credit`
    GROUP BY
        journal_id,
        glId
) AS credit
ON
credit.journal_id = journal.id
WHERE
journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS mainReport
)
) AS table1
INNER JOIN `erp_acc_coa_1_table` AS coa
ON
table1.glId = coa.id
ORDER BY
table1.jid
DESC
) AS summary1
WHERE summary1.glId=(SELECT itemsFG_gl FROM erp_acc_gl_mapping WHERE company_id=$company_id);";
$fg_inventory_gl_queryset = queryGet($fg_inventory_gl);
// console($fg_inventory_gl_queryset['data']);


$fg_inventory_sl = "SELECT SUM(ABS(log.itemQty)*log.itemPrice) AS fg_gl_total_amt FROM erp_inventory_stocks_log AS log INNER JOIN erp_inventory_items AS items ON log.itemId=items.itemId WHERE log.companyId=$company_id AND log.branchId=$branch_id AND items.location_id=$location_id AND log.createdAt BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND items.goodsType=3;";
$fg_inventory_sl_queryset = queryGet($fg_inventory_sl);
// console($fg_inventory_sl_queryset['data']);


$sfg_inventory_gl = "SELECT
summary1.amount AS gl_total_amount
FROM
(
SELECT
    table1.jid AS jid,
    table1.company_id AS company_id,
    table1.branch_id AS branch_id,
    table1.location_id AS location_id,
    table1.jv_no AS jv_no,
    table1.party_code AS party_code,
    table1.party_name AS party_name,
    table1.refarenceCode AS referenceCode,
    table1.parent_id AS parent_id,
    table1.parent_slug AS parent_slug,
    table1.journal_entry_ref AS journal_entry_ref,
    table1.documentNo AS documentNo,
    table1.order_no AS Order_num,
    table1.documentDate AS document_date,
    table1.postingDate AS postingDate,
    table1.remark AS remark,
    table1.glId AS glId,
    coa.gl_code AS gl_code,
    coa.gl_label AS gl_label,
    coa.typeAcc AS typeAcc,
    table1.Amount AS Amount,
    table1.Type AS TYPE,
    table1.journal_created_at AS journal_created_at,
    table1.journal_created_by AS journal_created_by,
    table1.journal_updated_at AS journal_updated_at,
    table1.journal_updated_by AS journal_updated_by
FROM
    (
        (
        SELECT
            *,
            CASE WHEN parent_slug = 'PGI' THEN(
            SELECT
                so_number
            FROM
                erp_branch_sales_order_delivery_pgi
            WHERE
                so_delivery_pgi_id = main_report.parent_id
            LIMIT 1
        ) WHEN parent_slug = 'SOInvoicing' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_invoices
        WHERE
            so_invoice_id = main_report.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'grn' THEN(
    SELECT
        grnPoNumber
    FROM
        erp_grn
    WHERE
        grnId = main_report.parent_id
    LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = main_report.parent_id
LIMIT 1
)
    END AS Order_no
FROM
    (
    SELECT
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
        journal.journalEntryReference AS journal_entry_ref,
        journal.documentNo AS documentNo,
        journal.documentDate AS documentDate,
        journal.postingDate AS postingDate,
        journal.remark AS remark,
        journal.journal_status AS journal_status,
        debit.glId AS glId,
        debit.debit_amount AS Amount,
        'DR' AS TYPE,
        journal.journal_created_at AS journal_created_at,
        journal.journal_created_by AS journal_created_by,
        journal.journal_updated_at AS journal_updated_at,
        journal.journal_updated_by AS journal_updated_by
    FROM
        `erp_acc_journal` AS journal
    INNER JOIN(
        SELECT
            journal_id,
            glId,
            SUM(debit_amount) AS debit_amount
        FROM
            `erp_acc_debit`
        GROUP BY
            journal_id,
            glId
    ) AS debit
ON
    debit.journal_id = journal.id
WHERE
    journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS main_report
    )
UNION
    (
    SELECT
        *,
        CASE WHEN parent_slug = 'PGI' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_delivery_pgi
        WHERE
            so_delivery_pgi_id = mainReport.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'SOInvoicing' THEN(
    SELECT
        so_number
    FROM
        erp_branch_sales_order_invoices
    WHERE
        so_invoice_id = mainReport.parent_id
    LIMIT 1
) WHEN parent_slug = 'grn' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
)
END AS Order_no
FROM
(
SELECT
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
    journal.journalEntryReference AS journal_entry_ref,
    journal.documentNo AS documentNo,
    journal.documentDate AS documentDate,
    journal.postingDate AS postingDate,
    journal.remark AS remark,
    journal.journal_status AS journal_status,
    credit.glId AS glId,
    credit.credit_amount *(-1) AS Amount,
    'CR' AS TYPE,
    journal.journal_created_at AS journal_created_at,
    journal.journal_created_by AS journal_created_by,
    journal.journal_updated_at AS journal_updated_at,
    journal.journal_updated_by AS journal_updated_by
FROM
    `erp_acc_journal` AS journal
INNER JOIN(
    SELECT
        journal_id,
        glId,
        SUM(credit_amount) AS credit_amount
    FROM
        `erp_acc_credit`
    GROUP BY
        journal_id,
        glId
) AS credit
ON
credit.journal_id = journal.id
WHERE
journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS mainReport
)
) AS table1
INNER JOIN `erp_acc_coa_1_table` AS coa
ON
table1.glId = coa.id
ORDER BY
table1.jid
DESC
) AS summary1
WHERE summary1.glId=(SELECT itemsSFG_gl FROM erp_acc_gl_mapping WHERE company_id=$company_id);";
$sfg_inventory_gl_queryset = queryGet($sfg_inventory_gl);
// console($sfg_inventory_gl_queryset['data']);


$sfg_inventory_sl = "SELECT SUM(ABS(log.itemQty)*log.itemPrice) AS sfg_gl_total_amt FROM erp_inventory_stocks_log AS log INNER JOIN erp_inventory_items AS items ON log.itemId=items.itemId WHERE log.companyId=$company_id AND log.branchId=$branch_id AND items.location_id=$location_id AND log.createdAt BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND items.goodsType=2;";
$sfg_inventory_sl_queryset = queryGet($sfg_inventory_sl);
// console($sfg_inventory_sl_queryset['data']);


$rm_inventory_gl = "SELECT
summary1.amount AS gl_total_amount
FROM
(
SELECT
    table1.jid AS jid,
    table1.company_id AS company_id,
    table1.branch_id AS branch_id,
    table1.location_id AS location_id,
    table1.jv_no AS jv_no,
    table1.party_code AS party_code,
    table1.party_name AS party_name,
    table1.refarenceCode AS referenceCode,
    table1.parent_id AS parent_id,
    table1.parent_slug AS parent_slug,
    table1.journal_entry_ref AS journal_entry_ref,
    table1.documentNo AS documentNo,
    table1.order_no AS Order_num,
    table1.documentDate AS document_date,
    table1.postingDate AS postingDate,
    table1.remark AS remark,
    table1.glId AS glId,
    coa.gl_code AS gl_code,
    coa.gl_label AS gl_label,
    coa.typeAcc AS typeAcc,
    table1.Amount AS Amount,
    table1.Type AS TYPE,
    table1.journal_created_at AS journal_created_at,
    table1.journal_created_by AS journal_created_by,
    table1.journal_updated_at AS journal_updated_at,
    table1.journal_updated_by AS journal_updated_by
FROM
    (
        (
        SELECT
            *,
            CASE WHEN parent_slug = 'PGI' THEN(
            SELECT
                so_number
            FROM
                erp_branch_sales_order_delivery_pgi
            WHERE
                so_delivery_pgi_id = main_report.parent_id
            LIMIT 1
        ) WHEN parent_slug = 'SOInvoicing' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_invoices
        WHERE
            so_invoice_id = main_report.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'grn' THEN(
    SELECT
        grnPoNumber
    FROM
        erp_grn
    WHERE
        grnId = main_report.parent_id
    LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = main_report.parent_id
LIMIT 1
)
    END AS Order_no
FROM
    (
    SELECT
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
        journal.journalEntryReference AS journal_entry_ref,
        journal.documentNo AS documentNo,
        journal.documentDate AS documentDate,
        journal.postingDate AS postingDate,
        journal.remark AS remark,
        journal.journal_status AS journal_status,
        debit.glId AS glId,
        debit.debit_amount AS Amount,
        'DR' AS TYPE,
        journal.journal_created_at AS journal_created_at,
        journal.journal_created_by AS journal_created_by,
        journal.journal_updated_at AS journal_updated_at,
        journal.journal_updated_by AS journal_updated_by
    FROM
        `erp_acc_journal` AS journal
    INNER JOIN(
        SELECT
            journal_id,
            glId,
            SUM(debit_amount) AS debit_amount
        FROM
            `erp_acc_debit`
        GROUP BY
            journal_id,
            glId
    ) AS debit
ON
    debit.journal_id = journal.id
WHERE
    journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS main_report
    )
UNION
    (
    SELECT
        *,
        CASE WHEN parent_slug = 'PGI' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_delivery_pgi
        WHERE
            so_delivery_pgi_id = mainReport.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'SOInvoicing' THEN(
    SELECT
        so_number
    FROM
        erp_branch_sales_order_invoices
    WHERE
        so_invoice_id = mainReport.parent_id
    LIMIT 1
) WHEN parent_slug = 'grn' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
)
END AS Order_no
FROM
(
SELECT
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
    journal.journalEntryReference AS journal_entry_ref,
    journal.documentNo AS documentNo,
    journal.documentDate AS documentDate,
    journal.postingDate AS postingDate,
    journal.remark AS remark,
    journal.journal_status AS journal_status,
    credit.glId AS glId,
    credit.credit_amount *(-1) AS Amount,
    'CR' AS TYPE,
    journal.journal_created_at AS journal_created_at,
    journal.journal_created_by AS journal_created_by,
    journal.journal_updated_at AS journal_updated_at,
    journal.journal_updated_by AS journal_updated_by
FROM
    `erp_acc_journal` AS journal
INNER JOIN(
    SELECT
        journal_id,
        glId,
        SUM(credit_amount) AS credit_amount
    FROM
        `erp_acc_credit`
    GROUP BY
        journal_id,
        glId
) AS credit
ON
credit.journal_id = journal.id
WHERE
journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS mainReport
)
) AS table1
INNER JOIN `erp_acc_coa_1_table` AS coa
ON
table1.glId = coa.id
ORDER BY
table1.jid
DESC
) AS summary1
WHERE summary1.glId=(SELECT itemsRM_gl FROM erp_acc_gl_mapping WHERE company_id=$company_id);";
$rm_inventory_gl_queryset = queryGet($rm_inventory_gl);
// console($rm_inventory_gl_queryset['data']);


$rm_inventory_sl = "SELECT SUM(ABS(log.itemQty)*log.itemPrice) AS rm_gl_total_amt FROM erp_inventory_stocks_log AS log INNER JOIN erp_inventory_items AS items ON log.itemId=items.itemId WHERE log.companyId=$company_id AND log.branchId=$branch_id AND items.location_id=$location_id AND log.createdAt BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND items.goodsType=1;";
$rm_inventory_sl_queryset = queryGet($rm_inventory_sl);
// console($rm_inventory_sl_queryset['data']);


$assets_gl = "SELECT
summary1.amount AS gl_total_amount
FROM
(
SELECT
    table1.jid AS jid,
    table1.company_id AS company_id,
    table1.branch_id AS branch_id,
    table1.location_id AS location_id,
    table1.jv_no AS jv_no,
    table1.party_code AS party_code,
    table1.party_name AS party_name,
    table1.refarenceCode AS referenceCode,
    table1.parent_id AS parent_id,
    table1.parent_slug AS parent_slug,
    table1.journal_entry_ref AS journal_entry_ref,
    table1.documentNo AS documentNo,
    table1.order_no AS Order_num,
    table1.documentDate AS document_date,
    table1.postingDate AS postingDate,
    table1.remark AS remark,
    table1.glId AS glId,
    coa.gl_code AS gl_code,
    coa.gl_label AS gl_label,
    coa.typeAcc AS typeAcc,
    table1.Amount AS Amount,
    table1.Type AS TYPE,
    table1.journal_created_at AS journal_created_at,
    table1.journal_created_by AS journal_created_by,
    table1.journal_updated_at AS journal_updated_at,
    table1.journal_updated_by AS journal_updated_by
FROM
    (
        (
        SELECT
            *,
            CASE WHEN parent_slug = 'PGI' THEN(
            SELECT
                so_number
            FROM
                erp_branch_sales_order_delivery_pgi
            WHERE
                so_delivery_pgi_id = main_report.parent_id
            LIMIT 1
        ) WHEN parent_slug = 'SOInvoicing' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_invoices
        WHERE
            so_invoice_id = main_report.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'grn' THEN(
    SELECT
        grnPoNumber
    FROM
        erp_grn
    WHERE
        grnId = main_report.parent_id
    LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = main_report.parent_id
LIMIT 1
)
    END AS Order_no
FROM
    (
    SELECT
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
        journal.journalEntryReference AS journal_entry_ref,
        journal.documentNo AS documentNo,
        journal.documentDate AS documentDate,
        journal.postingDate AS postingDate,
        journal.remark AS remark,
        journal.journal_status AS journal_status,
        debit.glId AS glId,
        debit.debit_amount AS Amount,
        'DR' AS TYPE,
        journal.journal_created_at AS journal_created_at,
        journal.journal_created_by AS journal_created_by,
        journal.journal_updated_at AS journal_updated_at,
        journal.journal_updated_by AS journal_updated_by
    FROM
        `erp_acc_journal` AS journal
    INNER JOIN(
        SELECT
            journal_id,
            glId,
            SUM(debit_amount) AS debit_amount
        FROM
            `erp_acc_debit`
        GROUP BY
            journal_id,
            glId
    ) AS debit
ON
    debit.journal_id = journal.id
WHERE
    journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS main_report
    )
UNION
    (
    SELECT
        *,
        CASE WHEN parent_slug = 'PGI' THEN(
        SELECT
            so_number
        FROM
            erp_branch_sales_order_delivery_pgi
        WHERE
            so_delivery_pgi_id = mainReport.parent_id
        LIMIT 1
    ) WHEN parent_slug = 'SOInvoicing' THEN(
    SELECT
        so_number
    FROM
        erp_branch_sales_order_invoices
    WHERE
        so_invoice_id = mainReport.parent_id
    LIMIT 1
) WHEN parent_slug = 'grn' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
) WHEN parent_slug = 'grniv' THEN(
SELECT
    grnPoNumber
FROM
    erp_grn
WHERE
    grnId = mainReport.parent_id
LIMIT 1
)
END AS Order_no
FROM
(
SELECT
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
    journal.journalEntryReference AS journal_entry_ref,
    journal.documentNo AS documentNo,
    journal.documentDate AS documentDate,
    journal.postingDate AS postingDate,
    journal.remark AS remark,
    journal.journal_status AS journal_status,
    credit.glId AS glId,
    credit.credit_amount *(-1) AS Amount,
    'CR' AS TYPE,
    journal.journal_created_at AS journal_created_at,
    journal.journal_created_by AS journal_created_by,
    journal.journal_updated_at AS journal_updated_at,
    journal.journal_updated_by AS journal_updated_by
FROM
    `erp_acc_journal` AS journal
INNER JOIN(
    SELECT
        journal_id,
        glId,
        SUM(credit_amount) AS credit_amount
    FROM
        `erp_acc_credit`
    GROUP BY
        journal_id,
        glId
) AS credit
ON
credit.journal_id = journal.id
WHERE
journal.journal_status = 'active' AND journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $f_date . "' AND '" . $to_date . "'
) AS mainReport
)
) AS table1
INNER JOIN `erp_acc_coa_1_table` AS coa
ON
table1.glId = coa.id
ORDER BY
table1.jid
DESC
) AS summary1
WHERE summary1.gl_code IN('10101%','10102%');";
$assets_gl_queryset = queryGet($assets_gl);
// console($assets_gl_queryset['data']);


$assets_sl = "SELECT SUM(ABS(log.itemQty)*log.itemPrice) AS fg_gl_total_amt FROM erp_inventory_stocks_log AS log INNER JOIN erp_inventory_items AS items ON log.itemId=items.itemId WHERE log.companyId=$company_id AND log.branchId=$branch_id AND items.location_id=$location_id AND log.createdAt BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND items.goodsType=9;";
$assets_sl_queryset = queryGet($assets_sl);
//console($assets_sl_queryset['data']);


// $sales_gl = "";
// $sales_gl_queryset = queryGet($sales_gl);
// console($sales_gl_queryset);


// $sales_sl = "";
// $sales_sl_queryset = queryGet($sales_sl);
// console($sales_sl_queryset);


$format_array = [];
$format_array[] = array(
  0 => array(
    "account" => "Customer",
    "gl_amount" => $customer_gl_queryset['data']['gl_total_amount'] ?? 0,
    "sl_amount" => $customer_sl_queryset['data']['SUM(invoices.all_total_amt)'] ?? 0
  ),
  1 => array(
    "account" => "Vendor",
    "gl_amount" => $vendor_gl_queryset['data']['gl_total_amount'] ?? 0,
    "sl_amount" => $vendor_sl_queryset['data']['SUM(grnTotalAmount)'] ?? 0
  ),
  2 => array(
    "account" => "FG Inventory",
    "gl_amount" => $fg_inventory_gl_queryset['data']['gl_total_amount'] ?? 0,
    "sl_amount" => $fg_inventory_sl_queryset['data']['fg_gl_total_amt'] ?? 0
  ),
  3 => array(
    "account" => "SFG Inventory",
    "gl_amount" => $sfg_inventory_gl_queryset['data']['gl_total_amount'] ?? 0,
    "sl_amount" => $sfg_inventory_sl_queryset['data']['sfg_gl_total_amt'] ?? 0
  ),
  4 => array(
    "account" => "RM Inventory",
    "gl_amount" => $rm_inventory_gl_queryset['data']['gl_total_amount'] ?? 0,
    "sl_amount" => $rm_inventory_sl_queryset['data']['rm_gl_total_amt'] ?? 0
  ),
  5 => array(
    "account" => "Assets",
    "gl_amount" => $assets_gl_queryset['data']['gl_total_amount'] ?? 0,
    "sl_amount" => $assets_sl_queryset['data']['fg_gl_total_amt'] ?? 0
  ),
  6 => array(
    "account" => "Sales",
    "gl_amount" => 0,
    "sl_amount" => 0
  )
);

// console($format_array);




if (isset($_GET['detailed-view'])) {
  ?>
        <!-- Content Wrapper detailed-view -->
        <div class="content-wrapper report-wrapper">
          <!-- Content Header (Page header) -->

          <!-- Main content -->
          <section class="content">
            <div class="container-fluid">

              <!-- row -->
              <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                  <div class="card card-tabs reports-card">
                    <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                      <!---------------------- Search START -->
                      <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                        <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                          <div class="label-select">
                            <h3 class="card-title mb-0">SL Vs GL Report</h3>
                          </div>

                          <div class="fy-custom-section">
                            <div class="fy-dropdown-section">
                              <?php
                              $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                              if (isset($_POST['from_date'])) {
                                $f_date = $_POST['from_date'];
                                $to_date = $_POST['to_date'];
                                //echo 1;
                            

                              } else {

                                $start = explode('-', $variant_sql['data'][0]['year_start']);
                                $end = explode('-', $variant_sql['data'][0]['year_end']);
                                $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                                $_POST['from_date'] = $f_date;
                                $_POST['to_date'] = $to_date;
                                $_POST['drop_val'] = 'fYDropdown';
                                $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                              }

                              ?>
                              <h6 class="text-xs font-bold">Financial Year</h6>
                              <div class="dropdown-fyear">
                                <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                                  <option value="">--Select FY--</option>
                                  <?php
                                  foreach ($variant_sql['data'] as $key => $data) {
                                    $start = explode('-', $data['year_start']);
                                    $end = explode('-', $data['year_end']);
                                    $startDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                    $endDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));

                                    ?>
                                          <option value="<?= $data['year_variant_id'] ?>" data-start="<?= $startDate ?>" data-end="<?= $endDate ?>" <?php if (($_POST['drop_val'] == 'fYDropdown' && $_POST['drop_id'] == $data['year_variant_id'])) {
                                                  echo "selected";
                                                } ?>><?= $data['year_variant_name'] ?></option>
                                        <?php
                                  }
                                  ?>

                                  <option value="customrange" <?php if ($_POST['drop_id'] == '') {
                                    echo "selected";
                                  } ?>>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                                  </option>
                                </select>

                                <label class="mb-0" for="">OR</label>


                                <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                                  <option value="">--Select One--</option>
                                  <option value="0" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 0) {
                                    echo "selected";
                                  } ?>>Today Report</option>
                                  <option value="6" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 6) {
                                    echo "selected";
                                  } ?>>Last 7 Days</option>
                                  <option value="14" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 14) {
                                    echo "selected";
                                  } ?>>Last 15 Days</option>
                                  <option value="29" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 29) {
                                    echo "selected";
                                  } ?>>Last 30 Days</option>
                                  <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                    echo "selected";
                                  } ?>>Last 45 Days</option>
                                  <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                    echo "selected";
                                  } ?>>Last 60 Days</option>
                                </select>
                              </div>
                              <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                            </div>

                            <div class="customrange-section">
                              <h6 class="text-xs font-bold">Custom Range</h6>
                              <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                                <div class="date-range-input d-flex">
                                  <div class="form-input">
                                    <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                                  </div>
                                  <div class="form-input">
                                    <label class="mb-0" for="">TO</label>
                                    <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                                  </div>
                                </div>
                                <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                              </form>
                              <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                            </div>

                            <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                          </div>

                        </li>
                      </ul>
                      <!---------------------- Search END -->
                    </div>
                    <div class="daybook-filter-list filter-list">
                      <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Visual Representation</a>
                      <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2 "></i>Concised View</a>
                      <!-- <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Detailed View</a> -->
                    </div>
                    <div class="card card-tabs mb-0" style="border-radius: 20px;">

                      <div class="tab-content" id="custom-tabs-two-tabContent">
                        <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                          <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>



                          <?php
                          $cond = '';

                          $sql_list = "SELECT invoices.invoice_date AS invoice_date,invoices.invoice_no AS invoice_num,func.functionalities_name AS profit_center,customer.customer_code AS customer_code,customer.trade_name AS customer_name,items.itemCode AS item_code,groups.goodGroupName AS item_group,items.itemName AS item_name, items.qty AS total_qty,items.uom AS uom,items.unitPrice AS rate ,(items.basePrice - items.totalDiscountAmt) AS taxable_amount,items.totalPrice,invoices.created_at,invoices.created_by,invoices.updated_at,invoices.updated_by FROM erp_branch_sales_order_invoices AS invoices LEFT JOIN erp_customer AS customer ON invoices.customer_id=customer.customer_id LEFT JOIN erp_branch_sales_order_invoice_items AS items ON invoices.so_invoice_id = items.so_invoice_id LEFT JOIN erp_inventory_items AS inventory ON items.itemCode=inventory.itemCode LEFT JOIN erp_inventory_mstr_good_groups AS groups ON inventory.goodsGroup=groups.goodGroupId LEFT JOIN erp_company_functionalities AS func ON invoices.profit_center=func.functionalities_id WHERE invoices.company_id=1 AND invoices.branch_id=$branch_id AND invoices.location_id = $location_id AND groups.companyId = $company_id AND invoices.invoice_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND invoices.status='active'";

                          $queryset = queryGet($sql_list, true);
                          // console($queryset);
                          $num_list = $queryset['numRows'];

                          $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_REPORT_DETAILED_VIEW_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
                          $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                          $settingsCheckbox = unserialize($settingsCh);
                          //console($settingsCheckbox);
                        

                          if ($num_list > 0) {
                            $i = 1;
                            ?>
                                  <table id="dataTable" class="table table-hover transactional-book-table" style="width: 100%; position: relative;">

                                    <thead>
                                      <tr>
                                        <?php if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>SL NO.</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Invoice No.</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Invoice Date</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Profit Center</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Customer Code</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Customer Name</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Product Code</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Product Name</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Product Group</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Total Quantity</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>UOM</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Rate</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Base Amount</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Total Amount</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Created By</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Created At</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Updated By</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                <th>Updated At</th>
                                        <?php } ?>
                                      </tr>
                                    </thead>

                                    <tbody>
                                      <?php
                                      $datas = $queryset['data'];
                                      $sl = 0;
                                      foreach ($datas as $data) {
                                        $i = 1;
                                        // console($data);
                                        $sl++;
                                        ?>
                                              <tr>
                                                <?php if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td> <?php echo $sl; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td><?php echo ($data['invoice_num']); ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td><?php echo formatDateORDateTime($data['invoice_date']); ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td><?php echo $data['profit_center']; ?></td>
                                                <?php }

                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td> <?php echo $data['customer_code']; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td> <?php echo $data['customer_name']; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td> <?php echo $data['item_code']; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td> <?php echo $data['item_name']; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td> <?php echo $data['item_group']; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td> <?php echo $data['total_qty']; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td> <?php echo $data['uom']; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td> <?php echo $data['rate']; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td><?php echo ($data['taxable_amount']); ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td><?php echo ($data['totalPrice']); ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td><?php echo getCreatedByUser($data['created_by']); ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td><?php echo formatDateORDateTime($data['created_at']); ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td><?php echo getCreatedByUser($data['updated_by']); ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox)) { ?>
                                                        <td><?php echo formatDateORDateTime($data['updated_at']); ?></td>
                                                <?php } ?>
                                              </tr>
                                            <?php
                                      }
                                      ?>
                                    </tbody>
                                    <?php $j = 1; ?>
                                    <tfoot class="individual-search">
                                      <tr>
                                        <?php if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>SL NO.</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Invoice Number</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Invoice Date</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Profit Center</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Customer Code</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Customer Name</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Product Code</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Product Name</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Product Group</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Total Quantity</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>UOM</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Rate</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Taxable Amount</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Total Amount</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Created By</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Created At</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Updated By</th>
                                        <?php }
                                        $j++;
                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                <th>Updated At</th>
                                        <?php } ?>
                                      </tr>
                                    </tfoot>

                                  </table>
                          <?php } else { ?>
                                  <table id="mytable" class="table defaultDataTable table-hover">
                                    <thead>
                                      <tr>
                                        <td>

                                        </td>
                                      </tr>
                                    </thead>
                                  </table>
                              </div>
                      <?php } ?>
                      </div>

                      <!---------------------------------Detailed View  Table settings Model Start--------------------------------->

                <div class="modal" id="myModal2">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title">Detailed View Column Settings</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                        <div class="modal-body" style="max-height: 450px;">
                          <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                          <input type="hidden" name="pageTableName" value="ERP_REPORT_DETAILED_VIEW_<?= $pageName ?>" />
                          <div class="modal-body">
                            <div id="dropdownframe"></div>
                            <div id="main2">
                              <?php $p = 1; ?>
                              <table>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    SL NO.</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Invoice Date</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Invoice Number</td>
                                </tr>

                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Profit Center</td>
                                </tr>


                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Customer Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Customer Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Product Code</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Product Name</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Product Group</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Total Quantity</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    UOM</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Rate</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Taxable Amount</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Total Amount</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Created By</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Created At</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Updated By</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                    Updated At</td>
                                </tr>
                              </table>
                            </div>
                          </div>
                        </div>

                        <div class="modal-footer">
                          <button type="submit" name="add-table-settings" class="btn btn-success">Save</button>
                          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!---------------------------------Table Model End--------------------------------->

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
        </div>
        <!-- /.Content Wrapper detailed-view-->
      <?php
} else if (isset($_GET['concised-view'])) {
  ?>
              <!-- Content Wrapper concised-view -->
              <div class="content-wrapper report-wrapper">
                <!-- Content Header (Page header) -->

                <!-- Main content -->
                <section class="content">
                  <div class="container-fluid">

                    <!-- row -->
                    <div class="row p-0 m-0">
                      <div class="col-12 mt-2 p-0">
                        <div class="card card-tabs reports-card">
                          <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                            <!---------------------- Search START -->
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                              <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                <div class="label-select">
                                  <h3 class="card-title mb-0">SL Vs GL Report</h3>
                                </div>

                                <div class="fy-custom-section">
                                  <div class="fy-dropdown-section">
                                    <?php
                                    $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                                    // if (isset($_POST['from_date'])) {
                                    //   $f_date = $_POST['from_date'];
                                    //   $to_date = $_POST['to_date'];
                                    //   //echo 1;
                                  

                                    // } else {
                                  
                                    //   $start = explode('-', $variant_sql['data'][0]['year_start']);
                                    //   $end = explode('-', $variant_sql['data'][0]['year_end']);
                                    //   $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                    //   $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                                    //   $_POST['from_date'] = $f_date;
                                    //   $_POST['to_date'] = $to_date;
                                    //   $_POST['drop_val'] = 'fYDropdown';
                                    //   $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                                    // }
                                  
                                    ?>
                                    <h6 class="text-xs font-bold">Financial Year</h6>
                                    <div class="dropdown-fyear">
                                      <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                                        <option value="">--Select FY--</option>
                                        <?php
                                        foreach ($variant_sql['data'] as $key => $data) {
                                          $start = explode('-', $data['year_start']);
                                          $end = explode('-', $data['year_end']);
                                          $startDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                          $endDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));

                                          ?>
                                                <option value="<?= $data['year_variant_id'] ?>" data-start="<?= $startDate ?>" data-end="<?= $endDate ?>" <?php if (($_POST['drop_val'] == 'fYDropdown' && $_POST['drop_id'] == $data['year_variant_id'])) {
                                                        echo "selected";
                                                      } ?>><?= $data['year_variant_name'] ?></option>
                                        <?php
                                        }
                                        ?>

                                        <option value="customrange" <?php if ($_POST['drop_id'] == '') {
                                          echo "selected";
                                        } ?>>
                                          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                                        </option>
                                      </select>

                                      <label class="mb-0" for="">OR</label>


                                      <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                                        <option value="">--Select One--</option>
                                        <option value="0" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 0) {
                                          echo "selected";
                                        } ?>>Today Report</option>
                                        <option value="6" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 6) {
                                          echo "selected";
                                        } ?>>Last 7 Days</option>
                                        <option value="14" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 14) {
                                          echo "selected";
                                        } ?>>Last 15 Days</option>
                                        <option value="29" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 29) {
                                          echo "selected";
                                        } ?>>Last 30 Days</option>
                                        <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                          echo "selected";
                                        } ?>>Last 45 Days</option>
                                        <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                          echo "selected";
                                        } ?>>Last 60 Days</option>
                                      </select>
                                    </div>
                                    <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                                  </div>

                                  <div class="customrange-section">
                                    <h6 class="text-xs font-bold">Custom Range</h6>
                                    <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                      <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                      <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                                      <div class="date-range-input d-flex">
                                        <div class="form-input">
                                          <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                                        </div>
                                        <div class="form-input">
                                          <label class="mb-0" for="">TO</label>
                                          <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                                        </div>
                                      </div>
                                      <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                                    </form>
                                    <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                                  </div>

                                  <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                                </div>

                              </li>
                            </ul>
                            <!---------------------- Search END -->
                          </div>
                          <div class="daybook-filter-list filter-list">
                            <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Visual Representation</a>
                            <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn active waves-effect waves-light"><i class="fa fa-clock mr-2  active"></i>Concised View</a>
                            <!-- <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a> -->
                          </div>
                          <div class="card card-tabs mb-0" style="border-radius: 20px;">

                            <div class="tab-content" id="custom-tabs-two-tabContent">
                              <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                <?php
                                $cond = '';

                                // $sql_list = "SELECT DATE_FORMAT(invoices.invoice_date,'%M, %y') AS month,f_area.functionalities_name AS functional_area_name,SUM(invoices.sub_total_amt - invoices.totalDiscount) AS total_base_amount,SUM(invoices.all_total_amt) AS total_amount,SUM(invoices.due_amount) AS due_amount FROM erp_branch_sales_order_invoices AS invoices LEFT JOIN erp_company_functionalities AS f_area ON invoices.profit_center=f_area.functionalities_id WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id AND invoices.invoice_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND invoices.status='active' GROUP BY month,functional_area_name;";
                              
                                // $queryset = queryGet($sql_list, true);
                                // console($queryset);
                                // $num_list = $queryset['numRows'];
                              
                                $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_REPORT_CONCISED_VIEW_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                $settingsCheckbox_concised_view = unserialize($settingsCh);
                                //console($settingsCheckbox_concised_view);
                                // console('$format_array');
                                // console($format_array[0]);
                                $num_list = count($format_array[0]);
                                if ($num_list > 0) {
                                  $i = 1;
                                  ?>
                                        <!-- <table class="table defaultDataTable table-hover" data-paging="true" data-responsive="false"> -->
                                        <table id="dataTable" class="table table-hover transactional-book-table" style="width: 100%; position: relative;">

                                          <thead>
                                            <tr>
                                        <?php if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                      <th>SL NO.</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                      <th>Account</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                      <th>GL Amount</th>
                                        <?php }
                                        $i++;
                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                      <th>SL Amount</th>
                                        <?php } ?>
                                            </tr>
                                          </thead>

                                          <tbody class="">
                                            <?php
                                            $datas = $format_array[0];
                                            $sl = 0;
                                            foreach ($datas as $data) {
                                              $i = 1;
                                              // console($data);
                                              $sl++;
                                              ?>
                                                    <tr>
                                                <?php if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                              <td> <?php echo $sl; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                              <td><?php echo $data['account']; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                              <td><?php echo $data['gl_amount']; ?></td>
                                                <?php }
                                                $i++;
                                                if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                              <td><?php echo $data['sl_amount']; ?></td>
                                                <?php } ?>
                                                    </tr>
                                            <?php
                                            }
                                            ?>
                                          </tbody>
                                    <?php $j = 1; ?>
                                          <tfoot class="individual-search">
                                            <tr>
                                        <?php if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                      <th>SL NO.</th>
                                        <?php }
                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                      <th>Account</th>
                                        <?php }
                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                      <th>GL Amount</th>
                                        <?php }
                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                      <th>SL Amount</th>
                                        <?php } ?>
                                            </tr>
                                          </tfoot>

                                        </table>
                          <?php } else { ?>
                                        <table id="mytable" class="table defaultDataTable table-hover">
                                          <thead>
                                            <tr>
                                              <td>

                                              </td>
                                            </tr>
                                          </thead>
                                        </table>
                                    </div>
                      <?php } ?>
                            </div>

                            <!---------------------------------Concised View Table settings Model Start--------------------------------->

                <div class="modal" id="myModal2">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title">Concised View Column Settings</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                      </div>
                      <form name="table_settings_concised_view" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings_concised_view();">
                        <div class="modal-body" style="max-height: 450px;">
                          <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                          <input type="hidden" name="pageTableName" value="ERP_REPORT_CONCISED_VIEW_<?= $pageName ?>" />
                          <div class="modal-body">
                            <div id="dropdownframe"></div>
                            <div id="main2">
                              <?php $p = 1; ?>
                              <table>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    SL NO.</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    Account</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    GL Amount</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                  echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                    SL Amount</td>
                                </tr>
                              </table>
                            </div>
                          </div>
                        </div>

                        <div class="modal-footer">
                          <button type="submit" name="add-table-settings" class="btn btn-success">Save</button>
                          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                          </form>
                        </div>
                      </div>
                    </div>

                    <!---------------------------------Table Model End--------------------------------->

              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
              </div>
              <!-- /.Content Wrapper concised-view -->
      <?php
} else {

  ?>
              <!-- Content Wrapper. Graph View -->
              <div class="content-wrapper report-wrapper">
                <!-- Content Header (Page header) -->

                <!-- Main content -->
                <section class="content">
                  <div class="container-fluid">

                    <!-- row -->
                    <div class="row p-0 m-0">
                      <div class="col-12 mt-2 p-0">
                        <div class="card card-tabs reports-card">
                          <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                            <!---------------------- Search START -->
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                              <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                <div class="label-select">
                                  <h3 class="card-title mb-0">SL Vs GL Report</h3>
                                </div>

                                <div class="fy-custom-section">
                                  <div class="fy-dropdown-section">
                                    <?php
                                    $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                                    if (isset($_POST['from_date'])) {
                                      $f_date = $_POST['from_date'];
                                      $to_date = $_POST['to_date'];
                                      //echo 1;
                                  

                                    } else {

                                      $start = explode('-', $variant_sql['data'][0]['year_start']);
                                      $end = explode('-', $variant_sql['data'][0]['year_end']);
                                      $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                      $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                                      $_POST['from_date'] = $f_date;
                                      $_POST['to_date'] = $to_date;
                                      $_POST['drop_val'] = 'fYDropdown';
                                      $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                                    }

                                    ?>
                                    <h6 class="text-xs font-bold">Financial Year</h6>
                                    <div class="dropdown-fyear">
                                      <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                                        <option value="">--Select FY--</option>
                                        <?php
                                        foreach ($variant_sql['data'] as $key => $data) {
                                          $start = explode('-', $data['year_start']);
                                          $end = explode('-', $data['year_end']);
                                          $startDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                          $endDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));

                                          ?>
                                                <option value="<?= $data['year_variant_id'] ?>" data-start="<?= $startDate ?>" data-end="<?= $endDate ?>" <?php if (($_POST['drop_val'] == 'fYDropdown' && $_POST['drop_id'] == $data['year_variant_id'])) {
                                                        echo "selected";
                                                      } ?>><?= $data['year_variant_name'] ?></option>
                                        <?php
                                        }
                                        ?>

                                        <option value="customrange" <?php if ($_POST['drop_id'] == '') {
                                          echo "selected";
                                        } ?>>
                                          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                                        </option>
                                      </select>

                                      <label class="mb-0" for="">OR</label>


                                      <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                                        <option value="">--Select One--</option>
                                        <option value="0" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 0) {
                                          echo "selected";
                                        } ?>>Today Report</option>
                                        <option value="6" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 6) {
                                          echo "selected";
                                        } ?>>Last 7 Days</option>
                                        <option value="14" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 14) {
                                          echo "selected";
                                        } ?>>Last 15 Days</option>
                                        <option value="29" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 29) {
                                          echo "selected";
                                        } ?>>Last 30 Days</option>
                                        <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                          echo "selected";
                                        } ?>>Last 45 Days</option>
                                        <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                          echo "selected";
                                        } ?>>Last 60 Days</option>
                                      </select>
                                    </div>
                                    <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                                  </div>

                                  <div class="customrange-section">
                                    <h6 class="text-xs font-bold">Custom Range</h6>
                                    <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                      <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                      <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                                      <div class="date-range-input d-flex">
                                        <div class="form-input">
                                          <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                                        </div>
                                        <div class="form-input">
                                          <label class="mb-0" for="">TO</label>
                                          <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                                        </div>
                                      </div>
                                      <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                                    </form>
                                    <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                                  </div>

                                  <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                                </div>

                              </li>
                            </ul>
                            <!---------------------- Search END -->
                          </div>
                          <div class="daybook-filter-list filter-list">
                            <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn active waves-effect waves-light"><i class="fa fa-stream mr-2  active"></i>Visual Representation</a>
                            <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>Concised View</a>
                            <!-- <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a> -->
                          </div>

                          <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">

                              <?php
                              //Graph View SQL
                            
                              $sql_list = "SELECT DATE_FORMAT(invoices.invoice_date,'%M, %y') AS month,f_area.functionalities_name AS functional_area_name,SUM(invoices.sub_total_amt - invoices.totalDiscount) AS total_base_amount,SUM(invoices.all_total_amt) AS total_amount,SUM(invoices.due_amount) AS due_amount FROM erp_branch_sales_order_invoices AS invoices LEFT JOIN erp_company_functionalities AS f_area ON invoices.profit_center=f_area.functionalities_id WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id AND invoices.invoice_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND invoices.status='active' GROUP BY month,functional_area_name;";

                              $queryset = queryGet($sql_list, true);
                              // console($queryset);
                              $chartData = json_encode($format_array[0], true);

                              // $num_list = $queryset['numRows'];
                              $num_list = count($format_array[0]);

                              if ($num_list > 0) {
                                $i = 1;
                                ?>

                                      <div class="container-fluid mt-10">

                                        <div class="row">
                                          <div class="col-md-12 col-sm-12 d-flex">
                                            <div class="card flex-fill reports-card">
                                              <div class="card-body">
                                                <div id="chartDivSLVsGL" class="chartContainer"></div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>

                                      </div>

                        <?php } else { ?>
                                      <p>No data Found</p>
                        <?php } ?>
                            </div>


                          </div>
                        </div>
                      </div>
                    </div>
                </section>
                <!-- /.content -->
              </div>
              <!-- /.Content Wrapper. Graph View -->


      <?php
}
require_once("../../common/footer.php");
?>

<script>
  function table_settings_concised_view() {
    var favorite = [];
    $.each($("input[name='settingsCheckbox[]']:checked"), function() {
      favorite.push($(this).val());
    });
    var check = favorite.length;
    if (check < 5) {
      alert("Please Check Atlast 5");
      return false;
    }

  }

  function table_settings() {
    var favorite = [];
    $.each($("input[name='settingsCheckbox[]']:checked"), function() {
      favorite.push($(this).val());
    });
    var check = favorite.length;
    if (check < 5) {
      alert("Please Check Atlast 5");
      return false;
    }

  }


  $(document).ready(function() {



    $('.select2')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal3">
    Add New
  </a></div>`);
      });
    //**************************************************************
    $('.select4')
      .select4()
      .on('select4:open', () => {
        $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
    Add New
  </a></div>`);
      });
  });
</script>

<script>
  function leaveInput(el) {
    if (el.value.length > 0) {
      if (!el.classList.contains('active')) {
        el.classList.add('active');
      }
    } else {
      if (el.classList.contains('active')) {
        el.classList.remove('active');
      }
    }
  }

  var inputs = document.getElementsByClassName("m-input");
  for (var i = 0; i < inputs.length; i++) {
    var el = inputs[i];
    el.addEventListener("blur", function() {
      leaveInput(this);
    });
  }



  // *** autocomplite select *** //
  wow = new WOW({
    boxClass: 'wow', // default
    animateClass: 'animated', // default
    offset: 0, // default
    mobile: true, // default
    live: true // default
  })
  wow.init();
</script>

<script>
  $(document).ready(function() {

    $("#dataTable tfoot th").each(function() {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
    });

    // DataTable
    var columnSl = 0;
    var table = $("#dataTable").DataTable({
      dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
      buttons: ['copy', 'csv', 'excel', 'print'],
      "lengthMenu": [
        [1000, 5000, 10000, -1],
        [1000, 5000, 10000, 'All'],
      ],
      "scrollY": 200,
      "scrollX": true,
      "ordering": false,


      initComplete: function() {
        this.api()
          .columns()
          .every(function() {
            columnSl++;
            console.log(`columnSl=${columnSl}`);
            if (columnSl == 8 || columnSl == 10) {
              //For Dropdown column search
              /*var column = this;
              var select = $('<select class="form-control p-0"><option value="">All</option></select>')
                .appendTo($(column.footer()).empty())
                .on('change', function() {
                  var val = $.fn.dataTable.util.escapeRegex($(this).val());
                  console.log(val);
                  column.search(val ? '^' + val + '$' : '', true, false).draw();
                });

              column
                .data()
                .unique()
                .sort()
                .each(function(d, j) {
                  select.append('<option value="' + d + '">' + d + '</option>');
                });*/
            }
            if (columnSl == 4 || columnSl == 5) {
              var column = this;
              var select = $('<input type="text" class="form-control" placeholder="dd-mm-yyyy">')
                .appendTo($(column.footer()).empty());
            }
          });
      },
    });
    // Apply the search
    columnSl2 = 0;
    table.columns().every(function() {
      columnSl2++;
      if (columnSl2 == 4 || columnSl2 == 5) {
        var that = this;
        $('input', this.footer()).on('keyup change', function() {
          let searchVal = `${(this.value).split("-")[2]}-${(this.value).split("-")[1]}-${(this.value).split("-")[0]}`;
          that.search(searchVal).draw();
        });
      } else {
        var that = this;
        $('input', this.footer()).on('keyup change', function() {
          that.search(this.value).draw();
        });
      }
    });

  });
</script>

<script>
  var elem = document.getElementById("listTabPan");

  function openFullscreen() {
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) {
      /* Safari */
      elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
      /* IE11 */
      elem.msRequestFullscreen();
    }
  }
</script>

<script>
  $(function() {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left'
      },
      function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
      });
  });
</script>

<!-- CHANGES -->
<script>
  $(function() {
    $('input[name="daterange"]').daterangepicker({
      opens: 'left'
    }, function(start, end, label) {
      console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
  });
  $('#fYDropdown').change(function() {
    var title = $(this).val();
    if (title == "customrange") {
      $("#drop_val").val('customrange');
      $("#from_date").val('');
      $("#to_date").val('');
      $("#from_date").focus();
    } else {
      let start = $(this).find(':selected').data('start');
      let end = $(this).find(':selected').data('end');
      //alert(start);
      $("#from_date").val(start);
      $("#to_date").val(end);
      $("#drop_val").val('fYDropdown');
      $("#drop_id").val(title);
      $('#date_form').submit();
    }
  });

  $('#quickDropdown').change(function() {
    var days = $(this).val();
    var today = new Date();
    var seven_days_ago = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));

    var end = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
    var start = seven_days_ago.getFullYear() + '-' + ('0' + (seven_days_ago.getMonth() + 1)).slice(-2) + '-' + ('0' + seven_days_ago.getDate()).slice(-2);

    // alert(start);
    // alert(end);
    $("#from_date").val(start);
    $("#to_date").val(end);
    $("#drop_val").val('quickDrop');
    $("#drop_id").val(days);

    $('#date_form').submit();
  });

  function compare_date() {
    let fromDate = $("#from_date").val();
    let toDate = $("#to_date").val();

    const date1 = new Date(fromDate);
    const date2 = new Date(toDate);
    const diffTime = Math.abs(date2 - date1);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));


    if (fromDate && toDate) {
      if (diffDays > 366) {
        document.getElementById("rangeid").disabled = true;
        $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">Date Range can not be greater than 1 year</p>`);
      } else {
        $(".customRangeCla").html('');
        document.getElementById("rangeid").disabled = false;

        if (toDate < fromDate) {
          $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">From Date can not be greater than To Date</p>`);
          document.getElementById("rangeid").disabled = true;

        } else {
          $(".customRangeCla").html('');
          document.getElementById("rangeid").disabled = false;
        }
      }
    }
  }

  $("#to_date").keyup(function() {
    compare_date();
  });

  $("#from_date").change(function() {
    compare_date();
  });

  $("#to_date").change(function() {
    compare_date();
  });
</script>
<!-- CHANGES -->


<!-- CHART FUNCTION -->
<script>
  var chartData = <?php echo $chartData; ?>;

  am4core.ready(function() {

    // Themes
    am4core.useTheme(am4themes_animated);

    // Create chart instance
    var chart = am4core.create("chartDivSLVsGL", am4charts.XYChart);

    chart.logo.disabled = true;
    chart.colors.step = 2;

    chart.legend = new am4charts.Legend()
    chart.legend.position = 'top'
    chart.legend.paddingBottom = 20
    chart.legend.labels.template.maxWidth = 95

    var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
    xAxis.dataFields.category = 'account'
    xAxis.renderer.cellStartLocation = 0.1
    xAxis.renderer.cellEndLocation = 0.9
    xAxis.renderer.grid.template.location = 0;

    var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
    yAxis.min = 0;

    chart.cursor = new am4charts.XYCursor();
    chart.cursor.lineX.disabled = true;
    chart.cursor.lineY.disabled = true;

    function createSeries(value, name) {
        var series = chart.series.push(new am4charts.ColumnSeries())
        series.dataFields.valueY = value
        series.dataFields.categoryX = 'account'
        series.name = name
        series.tooltipText = "{name} : [bold]{valueY}";

        series.events.on("hidden", arrangeColumns);
        series.events.on("shown", arrangeColumns);

        // var bullet = series.bullets.push(new am4charts.LabelBullet())
        // bullet.interactionsEnabled = false
        // bullet.dy = 30;
        // bullet.label.text = '{valueY}'
        // bullet.label.fill = am4core.color('#ffffff')

        return series;
    }

    chart.data = chartData

    createSeries('gl_amount', 'GL Amount');
    createSeries('sl_amount', 'SL Amount');

    function arrangeColumns() {

        var series = chart.series.getIndex(0);

        var w = 1 - xAxis.renderer.cellStartLocation - (1 - xAxis.renderer.cellEndLocation);
        if (series.dataItems.length > 1) {
            var x0 = xAxis.getX(series.dataItems.getIndex(0), "categoryX");
            var x1 = xAxis.getX(series.dataItems.getIndex(1), "categoryX");
            var delta = ((x1 - x0) / chart.series.length) * w;
            if (am4core.isNumber(delta)) {
                var middle = chart.series.length / 2;

                var newIndex = 0;
                chart.series.each(function(series) {
                    if (!series.isHidden && !series.isHiding) {
                        series.dummyData = newIndex;
                        newIndex++;
                    }
                    else {
                        series.dummyData = chart.series.indexOf(series);
                    }
                })
                var visibleCount = newIndex;
                var newMiddle = visibleCount / 2;

                chart.series.each(function(series) {
                    var trueIndex = chart.series.indexOf(series);
                    var newIndex = series.dummyData;

                    var dx = (newIndex - trueIndex + middle - newMiddle) * delta

                    series.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
                    series.bulletsContainer.animate({ property: "dx", to: dx }, series.interpolationDuration, series.interpolationEasing);
                })
            }
        }
    }
  });
</script>