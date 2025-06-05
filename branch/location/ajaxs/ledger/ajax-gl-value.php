<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
$headerData = array('Content-Type: application/json');
$companyCurrency=getSingleCurrencyType($company_currency);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // echo 0;
  $gl_id = $_POST['gl'];
  $from_date = $_POST['start_date'];
  $to_date = $_POST['to_date'];
  //echo $gl_id;

  //  $currentYear = date("Y"); // Get the current year

  //  // Get the start date of the current year
  //  $from_date = $currentYear . "-01-01";

  //  // Get the end date of the current year
  //  $to_date = $currentYear . "-12-31";
  
  // $sql = queryGet("SELECT * FROM ( SELECT journal.id, journal.party_code, journal.party_name, journal.parent_slug AS type, journal.remark, journal.postingDate, 0 AS credit, sum(debit.debit_amount) AS debit, coa.gl_code, coa.gl_label, coa.typeAcc, debit.subGlCode, debit.subGlName, CASE WHEN journal.party_code = cust.customer_code THEN inv.so_invoice_id WHEN journal.party_code = vend.vendor_code THEN grniv.grnIvId ELSE 0 END AS document_id, CASE WHEN journal.party_code = cust.customer_code THEN inv.invoice_no WHEN journal.party_code = vend.vendor_code THEN grniv.grnIvCode ELSE 0 END AS document_no FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id = debit.journal_id LEFT JOIN " . ERP_ACC_CHART_OF_ACCOUNTS . " AS coa ON debit.glId = coa.id LEFT JOIN erp_customer AS cust ON cust.customer_code = journal.party_code LEFT JOIN erp_vendor_details AS vend ON vend.vendor_code = journal.party_code LEFT JOIN erp_branch_sales_order_invoices AS inv ON inv.customer_id = cust.customer_id LEFT JOIN erp_grninvoice AS grniv ON grniv.vendorId = vend.vendor_id WHERE journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status = 'active' AND debit.glId = $gl_id GROUP BY journal.id, journal.party_code, journal.parent_slug, journal.postingDate, coa.gl_code UNION SELECT journal.id, journal.party_code, journal.party_name, journal.parent_slug AS type, journal.remark, journal.postingDate, sum(credit.credit_amount) AS credit, 0 AS debit, coa.gl_code, coa.gl_label, coa.typeAcc, credit.subGlCode, credit.subGlName, CASE WHEN journal.party_code = cust.customer_code THEN inv.so_invoice_id WHEN journal.party_code = vend.vendor_code THEN grniv.grnIvId ELSE 0 END AS document_id, CASE WHEN journal.party_code = cust.customer_code THEN inv.invoice_no WHEN journal.party_code = vend.vendor_code THEN grniv.grnIvCode ELSE 0 END AS document_no FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id = credit.journal_id LEFT JOIN " . ERP_ACC_CHART_OF_ACCOUNTS . " AS coa ON credit.glId = coa.id LEFT JOIN erp_customer AS cust ON cust.customer_code = journal.party_code LEFT JOIN erp_vendor_details AS vend ON vend.vendor_code = journal.party_code LEFT JOIN erp_branch_sales_order_invoices AS inv ON inv.customer_id = cust.customer_id LEFT JOIN erp_grninvoice AS grniv ON grniv.vendorId = vend.vendor_id WHERE journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status = 'active' AND credit.glId = $gl_id GROUP BY journal.id, journal.party_code, journal.parent_slug, journal.postingDate, coa.gl_code ) AS subquery ORDER BY subquery.postingDate", true);

  // echo $gl_id;
  $sqlQueryy="SELECT * FROM ( SELECT journal.id, journal.party_code, journal.party_name, journal.parent_slug AS type, journal.remark, journal.postingDate, 0 AS credit, debit.debit_amount AS debit, coa.gl_code, coa.gl_label, coa.typeAcc, debit.subGlCode, debit.subGlName FROM erp_acc_debit AS debit LEFT JOIN erp_acc_journal AS journal ON journal.id = debit.journal_id LEFT JOIN " . ERP_ACC_CHART_OF_ACCOUNTS . " AS coa ON debit.glId = coa.id WHERE journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status = 'active' AND debit.glId = $gl_id UNION ALL SELECT journal.id, journal.party_code, journal.party_name, journal.parent_slug AS type, journal.remark, journal.postingDate, credit.credit_amount AS credit, 0 AS debit, coa.gl_code, coa.gl_label, coa.typeAcc, credit.subGlCode, credit.subGlName FROM erp_acc_credit AS credit LEFT JOIN erp_acc_journal AS journal ON journal.id = credit.journal_id LEFT JOIN erp_acc_coa_11_table AS coa ON credit.glId = coa.id WHERE journal.company_id = $company_id AND journal.branch_id = $branch_id AND journal.location_id = $location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status = 'active' AND credit.glId = $gl_id ) AS subquery ORDER BY subquery.postingDate";
  
  $sql=queryGet($sqlQueryy,true);
  $data = $sql['data'];
  $opening_balance = 0;
  $dateObject = new DateTime($from_date);
  // Get the day of the month
  $dayOfMonth = $dateObject->format('d');
  // Check if the day of the month is 1
  // if ($dayOfMonth === '01') {
  //   $opening = queryGet("SELECT (SELECT COALESCE(prevOpenClose.opening_val,0) as openingBalance FROM erp_opening_closing_balance AS prevOpenClose WHERE prevOpenClose.company_id = $company_id AND prevOpenClose.branch_id = $branch_id AND prevOpenClose.location_id = $location_id AND prevOpenClose.gl = 88 AND prevOpenClose.date = DATE_FORMAT('2024-04-05', '%Y-%m-01') ORDER BY prevOpenClose.date LIMIT 1) - (SELECT (SELECT COALESCE(SUM(dr.debit_amount),0) AS totalDebitAmount FROM erp_acc_debit AS dr LEFT JOIN erp_acc_journal AS jn ON dr.journal_id = jn.id WHERE jn.company_id = $company_id AND jn.branch_id = $branch_id AND jn.location_id = $location_id AND dr.glId = 88 AND (jn.postingDate>= DATE_FORMAT('2024-04-05', '%Y-%m-01') AND jn.postingDate<'2024-04-05')) - (SELECT COALESCE(SUM(cr.credit_amount),0) AS totalCreditAmount FROM erp_acc_credit AS cr LEFT JOIN erp_acc_journal AS jn ON cr.journal_id = jn.id WHERE jn.company_id = $company_id AND jn.branch_id = $branch_id AND jn.location_id = $location_id AND cr.glId = 88 AND (jn.postingDate>= DATE_FORMAT('2024-04-05', '%Y-%m-01') AND jn.postingDate<'2024-04-05'))) AS totalOpeningBalance;");
  //   //console($opening);
  //   $opening = round($opening['data']['opening'], 2) ?? 0;
  //   $rest_transaction_sql = queryGet("SELECT SUM(temp_table.debit) - SUM(temp_table.credit) AS transaction_open FROM(SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS credit,SUM(debit.debit_amount) AS debit,coa.gl_code,coa.gl_label,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id LEFT JOIN erp_acc_coa_" . $company_id . "_table AS coa ON debit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '$from_date' AND '$to_date' AND journal.journal_status='active' AND debit.glId= $gl_id GROUP BY journal.party_code,journal.party_name,journal.parent_slug,journal.remark,journal.postingDate,coa.gl_code,coa.gl_label,coa.typeAcc
  //   UNION
  //   SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,journal.postingDate,SUM(credit.credit_amount) AS credit,0 AS debit,coa.gl_code,coa.gl_label,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id LEFT JOIN erp_acc_coa_" . $company_id . "_table AS coa ON credit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '$from_date' AND '$to_date' AND journal.journal_status='active' AND credit.glId= $gl_id  GROUP BY journal.party_code,journal.party_name,journal.parent_slug,journal.remark,journal.postingDate,coa.gl_code,coa.gl_label,coa.typeAcc)AS temp_table
  //   ORDER BY temp_table.postingDate desc;");
  //   // console($rest_transaction_sql);
  //   $rest_transaction = $rest_transaction_sql['data']['transaction_open'];
  //   $opening_balance += $opening;
  // } else {
  //   $firstDayOfMonth = date("Y-m-01", strtotime($from_date));
  //   $prev_day = date('Y-m-d', strtotime($from_date . ' -1 day'));
  //   $opening = queryGet("SELECT coa.gl_code,coa.gl_label,coa.typeAcc,SUM(bal.opening_val) AS opening FROM erp_opening_closing_balance AS bal LEFT JOIN erp_acc_coa_" . $company_id . "_table AS coa ON bal.gl=coa.id WHERE bal.company_id=$company_id AND bal.branch_id=$branch_id AND bal.location_id=$location_id AND bal.gl=$gl_id AND DATE_FORMAT(date,'%Y-%m')=DATE_FORMAT('$firstDayOfMonth','%Y-%m') GROUP BY coa.gl_code,coa.gl_label,coa.typeAcc");
  //   $rest_transaction_first = queryGet("SELECT SUM(temp_table.debit) as debit_sum,SUM(temp_table.credit) as credit_sum FROM (SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS credit,SUM(debit.debit_amount) AS debit,coa.gl_code,coa.gl_label,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id LEFT JOIN erp_acc_coa_" . $company_id . "_table AS coa ON debit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '$firstDayOfMonth' AND '$prev_day' AND journal.journal_status='active' AND debit.glId= $gl_id GROUP BY journal.parent_slug,journal.remark,journal.postingDate,coa.gl_code,coa.gl_label,coa.typeAcc
  //           UNION
  //           SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,SUM(credit.credit_amount) AS credit,0 AS debit,coa.gl_code,coa.gl_label,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id LEFT JOIN erp_acc_coa_" . $company_id . "_table AS coa ON credit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '$firstDayOfMonth' AND '$prev_day' AND journal.journal_status='active' AND credit.glId= $gl_id  GROUP BY journal.parent_slug,journal.remark,journal.postingDate,coa.gl_code,coa.gl_label,coa.typeAcc) AS temp_table");

  //   $rest_transaction_sql = queryGet("SELECT SUM(temp_table.debit),SUM(temp_table.credit),SUM(temp_table.debit) - SUM(temp_table.credit) AS transaction_open FROM(SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS credit,SUM(debit.debit_amount) AS debit,coa.gl_code,coa.gl_label,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id LEFT JOIN erp_acc_coa_" . $company_id . "_table AS coa ON debit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '$firstDayOfMonth' AND '$prev_day' AND journal.journal_status='active' AND debit.glId= $gl_id GROUP BY journal.party_code,journal.party_name,journal.parent_slug,journal.remark,journal.postingDate,coa.gl_code,coa.gl_label,coa.typeAcc
  //           UNION
  //           SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,journal.postingDate,SUM(credit.credit_amount) AS credit,0 AS debit,coa.gl_code,coa.gl_label,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id LEFT JOIN erp_acc_coa_" . $company_id . "_table AS coa ON credit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '$firstDayOfMonth' AND '$prev_day' AND journal.journal_status='active' AND credit.glId= $gl_id  GROUP BY journal.party_code,journal.party_name,journal.parent_slug,journal.remark,journal.postingDate,coa.gl_code,coa.gl_label,coa.typeAcc)AS temp_table
  //           ORDER BY temp_table.postingDate desc;");
  //   //console($rest_transaction_first);
  //   //  console($opening);
  //   $opening = round($opening['data']['opening'], 2) ?? 0;
  //   $rest_transaction = $rest_transaction_sql['data']['transaction_open'];
  //   $transaction_first = $rest_transaction_first['data']['debit_sum'] - $rest_transaction_first['data']['credit_sum'];
  //   $opening_balance += $opening + $transaction_first;
  // }

  // $opening = queryGet("SELECT (SELECT COALESCE(SUM(prevOpenClose.opening_val),0) as openingBalance FROM erp_opening_closing_balance AS prevOpenClose WHERE prevOpenClose.company_id = $company_id AND prevOpenClose.branch_id = $branch_id AND prevOpenClose.location_id = $location_id AND prevOpenClose.gl = $gl_id AND prevOpenClose.date = DATE_FORMAT('$from_date', '%Y-%m-01') ORDER BY prevOpenClose.date LIMIT 1) + (SELECT (SELECT COALESCE(SUM(dr.debit_amount),0) AS totalDebitAmount FROM erp_acc_debit AS dr LEFT JOIN erp_acc_journal AS jn ON dr.journal_id = jn.id WHERE jn.company_id = $company_id AND jn.branch_id = $branch_id AND jn.location_id = $location_id AND dr.glId = $gl_id AND (jn.postingDate>= DATE_FORMAT('$from_date', '%Y-%m-01') AND jn.postingDate<'$from_date')) - (SELECT COALESCE(SUM(cr.credit_amount),0) AS totalCreditAmount FROM erp_acc_credit AS cr LEFT JOIN erp_acc_journal AS jn ON cr.journal_id = jn.id WHERE jn.company_id = $company_id AND jn.branch_id = $branch_id AND jn.location_id = $location_id AND cr.glId = $gl_id AND (jn.postingDate>= DATE_FORMAT('$from_date', '%Y-%m-01') AND jn.postingDate<'$from_date'))) AS totalOpeningBalance");

  $opening = queryGet("WITH OpeningBalance AS (
    SELECT  
        gl, 
        MIN(date) AS first_date, 
        SUM(opening_val) AS total_opening_value
    FROM 
        erp_opening_closing_balance AS eocb
    WHERE 
        company_id = $company_id 
        AND branch_id = $branch_id 
        AND location_id = $location_id 
        AND date = (
            SELECT MIN(date) 
            FROM erp_opening_closing_balance 
            WHERE gl = eocb.gl 
              AND company_id = $company_id 
              AND branch_id = $branch_id 
              AND location_id = $location_id
        )
    GROUP BY 
        gl
),
Debits AS (
    SELECT 
        ed.glId AS gl, 
        SUM(ed.debit_amount) AS total_debit_value
    FROM 
        erp_acc_debit AS ed
    JOIN 
        erp_acc_journal AS ej ON ed.journal_id = ej.id
    WHERE 
        ej.postingDate >= (SELECT first_date FROM OpeningBalance WHERE gl = ed.glId) 
        AND ej.postingDate < '" . $from_date . "' 
        AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id
    GROUP BY 
        ed.glId
),
Credits AS (
    SELECT 
        ec.glId AS gl, 
        SUM(ec.credit_amount) AS total_credit_value
    FROM 
        erp_acc_credit AS ec
    JOIN 
        erp_acc_journal AS ej ON ec.journal_id = ej.id
    WHERE 
        ej.postingDate >= (SELECT first_date FROM OpeningBalance WHERE gl = ec.glId) 
        AND ej.postingDate < '" . $from_date . "' 
        AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id
    GROUP BY 
        ec.glId
),
RangeDebits AS (
    SELECT 
        ed.glId AS gl, 
        SUM(ed.debit_amount) AS final_debit_value
    FROM 
        erp_acc_debit AS ed
    JOIN 
        erp_acc_journal AS ej ON ed.journal_id = ej.id
    WHERE 
        ej.postingDate >= '" . $from_date . "' 
        AND ej.postingDate <= '" . $to_date . "'
        AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id
    GROUP BY 
        ed.glId
),
RangeCredits AS (
    SELECT 
        ec.glId AS gl, 
        SUM(ec.credit_amount) AS final_credit_value
    FROM 
        erp_acc_credit AS ec
    JOIN 
        erp_acc_journal AS ej ON ec.journal_id = ej.id
    WHERE 
        ej.postingDate >= '" . $from_date . "' 
        AND ej.postingDate <= '" . $to_date . "'
     AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id
    GROUP BY 
        ec.glId
)
SELECT 
    ob.gl, 
    coa.gl_code,
    coa.gl_label,
    (ob.total_opening_value + COALESCE(d.total_debit_value, 0) - COALESCE(c.total_credit_value, 0)) AS from_opening_value,
    COALESCE(rd.final_debit_value, 0) AS final_debit,
    COALESCE(rc.final_credit_value, 0) AS final_credit,
    ((ob.total_opening_value + COALESCE(d.total_debit_value, 0) - COALESCE(c.total_credit_value, 0)) + COALESCE(rd.final_debit_value, 0) - COALESCE(rc.final_credit_value, 0)) AS to_closing_value
FROM 
    OpeningBalance AS ob
LEFT JOIN 
    Debits AS d ON ob.gl = d.gl
LEFT JOIN 
    Credits AS c ON ob.gl = c.gl
LEFT JOIN 
    RangeDebits AS rd ON ob.gl = rd.gl
LEFT JOIN 
    RangeCredits AS rc ON ob.gl = rc.gl
LEFT JOIN 
    `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON ob.gl = coa.id
    WHERE ob.gl = $gl_id
ORDER BY 
    ob.gl");



  $opening_balance = $opening['data']['from_opening_value'];

  

  $sum_sql = queryGet("SELECT SUM(temp_table.debit) as debit_sum,SUM(temp_table.credit) as credit_sum FROM (SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS credit,SUM(debit.debit_amount) AS debit,coa.gl_code,coa.gl_label,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id LEFT JOIN erp_acc_coa_" . $company_id . "_table AS coa ON debit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '$from_date' AND '$to_date' AND journal.journal_status='active' AND debit.glId= $gl_id GROUP BY journal.parent_slug,journal.remark,journal.postingDate,coa.gl_code,coa.gl_label,coa.typeAcc
   UNION
   SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,SUM(credit.credit_amount) AS credit,0 AS debit,coa.gl_code,coa.gl_label,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id LEFT JOIN erp_acc_coa_" . $company_id . "_table AS coa ON credit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '$from_date' AND '$to_date' AND journal.journal_status='active' AND credit.glId= $gl_id  GROUP BY journal.parent_slug,journal.remark,journal.postingDate,coa.gl_code,coa.gl_label,coa.typeAcc) AS temp_table");

  //  console($sum_sql);

  $html = '';
  // console($opening['data']);
  $debit_sum = round($sum_sql['data']['debit_sum'], 2) ?? 0;
  $credit_sum = round($sum_sql['data']['credit_sum'], 2) ?? 0;
  $balance_due = $opening_balance + ($debit_sum - $credit_sum);
  //mom sql 
  $mom_sql = queryGet("SELECT EXTRACT(MONTH FROM postingDate) AS month, SUM(credit) AS
   total_credit, SUM(debit) AS total_debit FROM (
   SELECT journal.parent_slug AS
   type,journal.remark,journal.postingDate,0 AS
   credit,SUM(debit.debit_amount) AS
   debit,coa.gl_code,coa.gl_label,coa.typeAcc FROM erp_acc_journal AS
   journal LEFT JOIN erp_acc_debit AS debit ON
   journal.id=debit.journal_id LEFT JOIN erp_acc_coa_" . $company_id . "_table AS coa ON
   debit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id
   AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "'
   AND '" . $to_date . "' AND journal.journal_status='active' AND debit.glId=
   $gl_id GROUP BY journal.parent_slug,journal.remark,journal.postingDate,coa.gl_code,coa.gl_label,coa.typeAcc
      UNION
      SELECT journal.parent_slug AS
   type,journal.remark,journal.postingDate,SUM(credit.credit_amount) AS
   credit,0 AS debit,coa.gl_code,coa.gl_label,coa.typeAcc FROM
   erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON
   journal.id=credit.journal_id LEFT JOIN erp_acc_coa_" . $company_id . "_table AS coa ON
   credit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id
   AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "'
   AND '" . $to_date . "' AND journal.journal_status='active' AND credit.glId=
   $gl_id  GROUP BY journal.parent_slug,journal.remark,journal.postingDate,coa.gl_code,coa.gl_label,coa.typeAcc)
   AS subquery
   GROUP BY EXTRACT(MONTH FROM postingDate);
    ", true);
  //console($mom_sql);


  $start_date = new DateTime($from_date);
  $end_date = new DateTime($to_date);

  // Initialize an empty array to store the months with years
  $monthsArray = [];

  // Loop through the months and years
  while ($start_date <= $end_date) {
    $monthsArray[] = [
      'month' => $start_date->format('n'),
      'year' => $start_date->format('Y')
  ];
    $start_date->modify('+1 month'); // Increment the month by 1
  }
  //console($monthsArray);

  $html .= '<div class="row state-head">
 <div class="col-lg-6 col-md-6 col-sm-12">
   <div class="intro-head">
     <h2 class="text-lg font-bold border-bottom pb-2">Ledger Report</h2>
     <p class="text-sm">' . formatDateORDateTime($from_date) . ' To ' . formatDateORDateTime($to_date) . '</p>
   </div>
 </div>
 <div class="col-lg-6 col-md-6 col-sm-12">
   <div class="acc-summary">
     <div class="table">
       <div class="row">
         <div class="col-12">
           <p>Account Summary</p>
         </div>
         <div class="col-lg-12 col-md-12 col-sm-12">
           <div class="display-flex-space-between">
             <p class="text-xs">Opening Balance</p>
             <p class="text-xs">'.$companyCurrency.' ' . decimalValuePreview($opening_balance) . '</p>
           </div>
           <div class="display-flex-space-between">
             <p class="text-xs">Debit</p>
             <p class="text-xs"> ' . decimalValuePreview($debit_sum) . '</p>
           </div>
           <div class="display-flex-space-between">
             <p class="text-xs">Credit</p>
             <p class="text-xs"> ' . decimalValuePreview($credit_sum) . '</p>
           </div>
           <hr>
           <div class="display-flex-space-between">
             <p class="text-xs">Closing Balance</p>
             <p class="text-xs">'.$companyCurrency.' ' . decimalValuePreview($balance_due) . '</p>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>
</div>

<div class="d-flex justify-content-between align-items-center">
  <ul class="nav nav-pills ledger-tab" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active text-xs" id="pills-detailLedger-tab" data-bs-toggle="pill" data-bs-target="#pills-detailLedger" type="button" role="tab" aria-controls="pills-detailLedger" aria-selected="true">Detailed View</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link text-xs" id="pills-conciseLedger-tab" data-bs-toggle="pill" data-bs-target="#pills-conciseLedger" type="button" role="tab" aria-controls="pills-conciseLedger" aria-selected="false">Month on Month View</button>
    </li>
  </ul>
  <div>

  </div>
</div>
';

  $html .= '
<div class="tab-content" id="pills-tabContent">
<div class="tab-pane fade show ledger-list-view active" id="pills-detailLedger" role="tabpanel" aria-labelledby="pills-detailLedger-tab" tabindex="0">
<button class="btn btn-primary float-right ml-3 waves-effect waves-light mb-2" id="exportButton" onclick="exportToExcel()">Export to Excel</button>
  <table class="ledger-view-table">
  <div class = "ledger-view-table-span"></div>
        <thead>
          <tr>
            <th>Date</th>';


    $html .=  '<th>Sub Ledger Code</th>
              <th>Sub Ledger Name</th>';
 

  $html .= '<th>Transaction</th>
            <th>Details</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
            <th class="text-right">Balance</th>
          </tr>
        </thead>
        <tbody>';


  $html .= '
      <tr>';


    $html .=  '<td></td>';

  $html .= '<td></td>
        <td>Opening Balance</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td  class="text-right">' . decimalValuePreview($opening_balance) . '</td>
        </tr>';


  $balance = $opening_balance;

  foreach ($data as $rows) {
    //  console($rows);
    $date = $rows['postingDate'];

    $type = $rows['type'];
    $transaction = $rows['type'];
    $details = $rows['remark'];

    if ($rows['debit'] != '0') {

      $amount = $rows['debit'];

      $balance = $balance + $amount;
    } elseif ($rows['credit'] != '0') {
      $amount = $rows['credit'];
      $balance = $balance - $amount;
    }
    $html .= '
      <tr>
        <td>' . formatDateORDateTime($date). '</td>';

    if ($gl_id == 66 || $gl_id == 150) {

      $html .=  '<td>' . $rows['party_code'] . '</td>
          <td>' . $rows['party_name'] . '</td>';
    }
    else{
      $html .=  '<td>'. $rows['subGlCode'].'</td>
      <td>'.$rows['subGlName'] .'</td>';
    }
    if ($gl_id == 88) {

      $html .= '<td>' . $transaction . '</td>
        <td><p class="pre-normal"><a class="soModal" href="#" data-id="' . $rows['document_id'] . '" >' . $details . '</a></p></td>';
    } elseif ($gl_id == 150) {
      $html .= '<td>' . $transaction . '</td>
      <td><p class="pre-normal"><a class="grnModal" href="#" data-id="' . $rows['document_id'] . '" >' . $details . '</a></p></td>';
    } else {
      $html .= '<td>' . $transaction . '</td>
      <td><p class="pre-normal"><a class="journalModal" href="#" data-id="' . $rows['id'] . '" >' . $details . '</a></p></td>';
    }
    if ($rows['debit'] != 0) {

      $html .= '<td class="text-right">' . decimalValuePreview($amount) . '</td>
         <td class="text-right">0</td>';
    } else {
      $html .= '<td class="text-right">0</td>
         <td class="text-right">' . decimalValuePreview($amount) . '</td>';
    }


    $html .= '<td class="text-right">' . decimalValuePreview($balance) . '</td>
       </tr>';
  }


  $total = array_reduce($data, function ($carry, $rows) {
    if ($rows['credit'] == 0) {
      $carry += $rows['debit'];
    } elseif ($rows['debit'] == 0) {
      $carry -= $rows['credit'];
    }
    return $carry;
  }, 0);


  $html .= '<tr>
           <td colspan="4" class="text-right font-bold">closing balance</td>
           <td colspan="2" class="text-right font-bold">'.$companyCurrency.' ' . decimalValuePreview($balance_due) . '</td>
         </tr>
         </tbody>
         </table>
         </div>';




  $dataByMonth = [];
  foreach ($mom_sql['data'] as $item) {
    $dataByMonth[$item['month']] = $item;
  }

  // Create an HTML table
  $html .= '<div class="tab-pane fade" id="pills-conciseLedger" role="tabpanel" aria-labelledby="pills-conciseLedger-tab" tabindex="0">
  <button class="btn btn-primary float-right ml-3 waves-effect waves-light mb-2" id="exportButtonMonth" onclick="exportToExcelMonth()">Export to Excel</button>
<table class="ledger-view-table-month">
<tr><th>Month</th><th>Opening</th><th>Debit</th><th>Credit</th><th>Closing</th></tr>
<tr>
<td></td>
<td class="text-right font-bold">' . decimalValuePreview($opening_balance) . '</td>
<td></td>
<td></td>
<td class="text-right font-bold">' . decimalValuePreview($opening_balance) . '</td>
</tr>

';



  foreach ($monthsArray as $months) {
    $month = $months['month'];
    $year = $months['year'];   

    $month_name = date('F', mktime(0, 0, 0, $month, 1));
    $total_debit = $dataByMonth[$month]['total_debit'];
    $total_credit = $dataByMonth[$month]['total_credit'];
    $closing_balance = $opening_balance + $total_debit - $total_credit;
    $html .= '<tr>
    <td class="font-bold">' . $month_name . ' - '.$year.'</td>
    <td class="text-right font-bold">' . decimalValuePreview($opening_balance) . '</td>';
    if (isset($dataByMonth[$month])) {

      $html .= '<td class="text-right font-bold">' . decimalValuePreview($total_debit) . '</td>
        <td class="text-right font-bold">' . decimalValuePreview($total_credit) . '</td>';
    } else {
      $html .=
        '<td class="text-right font-bold">0</td>
        <td class="text-right font-bold">0</td>';
    }
    $html .=

      '<td class="text-right font-bold">' . decimalValuePreview($closing_balance) . '</td>
     </tr>';

    $opening_balance = $closing_balance;
  }

  $html .= '</table>
<div>
</div>
';

  echo $html;
}
