<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
$headerData = array('Content-Type: application/json');

$companyCurrency=getSingleCurrencyType($company_currency);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // echo 0;
  $gl_id = $_POST['parentgl'];
  $from_date = $_POST['start_date'];
  $to_date = $_POST['to_date'];
  $code = $_POST['code'];
  //echo $gl_id;

  //  $currentYear = date("Y"); // Get the current year

  //  // Get the start date of the current year
  //  $from_date = $currentYear . "-01-01";

  //  // Get the end date of the current year
  //  $to_date = $currentYear . "-12-31";

  $sql = queryGet("SELECT * FROM(
    SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,journal.postingDate AS pdate,0 AS credit, debit.debit_amount AS debit,coa.gl_code,coa.gl_label,debit.subGlCode,debit.subGlName,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON debit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "'  AND journal.journal_status='active' AND debit.glId= $gl_id AND debit.subGlCode = '" . $code . "' 
       UNION ALL
       SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,journal.postingDate AS pdate, credit.credit_amount AS credit,0 AS debit,coa.gl_code,coa.gl_label,credit.subGlCode,credit.subGlName,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON credit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "'  AND journal.journal_status='active' AND credit.glId= $gl_id AND credit.subGlCode = '" . $code . "') AS subquery
       ORDER BY pdate", true);

 
  $data = $sql['data'];

  $debit_sum = 0;
  $credit_sum = 0;
  foreach ($data as $rows) {
    if ($rows['debit'] != '0') {
      $debit_sum += $rows['debit'];
    } elseif ($rows['credit'] != '0') {
      $credit_sum += $rows['credit'];
    }
  }

  $opening_balance = 0;
  $dateObject = new DateTime($from_date);

  // Get the day of the month
  $dayOfMonth = $dateObject->format('d');

  // Check if the day of the month is 1
  //   if ($dayOfMonth === '01') {



  //     $opening = queryGet("SELECT 
  //     coa.gl_code,
  //     coa.gl_label,
  //     subgl_code,
  //     subgl_name,
  //     coa.typeAcc,
  //     bal.closing_val AS opening_balance
  // FROM 
  //     erp_opening_closing_balance AS bal
  // LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON coa.id = bal.gl 
  // LEFT JOIN (
  //     SELECT db.glId, db.subGlCode AS subgl_code, db.subGlName AS subgl_name FROM erp_acc_debit AS db LEFT JOIN erp_acc_journal AS jn ON db.journal_id = jn.id WHERE jn.company_id = $company_id AND jn.branch_id = $branch_id AND jn.location_id = $location_id
  //     UNION ALL
  //     SELECT cr.glId, cr.subGlCode AS subgl_code, cr.subGlName AS subgl_name FROM erp_acc_credit AS cr LEFT JOIN erp_acc_journal AS jn ON cr.journal_id = jn.id WHERE jn.company_id = $company_id AND jn.branch_id = $branch_id AND jn.location_id = $location_id
  // ) AS subgl_details
  // ON
  //     bal.gl = subgl_details.glId
  // AND
  //     bal.subgl = subgl_details.subgl_code
  // WHERE
  //     bal.company_id = $company_id 
  //     AND bal.branch_id = $branch_id 
  //     AND bal.location_id = $location_id 
  //     AND bal.gl = $gl_id 
  //     AND bal.subgl = '" . $code . "'
  //     AND bal.date = (
  //         SELECT 
  //             MAX(date)
  //         FROM 
  //             erp_opening_closing_balance
  //         WHERE 
  //             company_id = bal.company_id
  //             AND branch_id = bal.branch_id
  //             AND location_id = bal.location_id
  //             AND gl = bal.gl
  //             AND subgl = bal.subgl
  //             AND date < '$from_date'
  //     )
  // GROUP BY 
  //     coa.gl_code,
  //     coa.gl_label,
  //     subgl_code,
  //     subgl_name,
  //     coa.typeAcc,
  //     opening_balance");
  //     //console($opening);

  //     $opening = round($opening['data']['opening'], 2) ?? 0;



  //     $opening_balance += $opening;
  //   } else {


  //     $firstDayOfMonth = date("Y-m-01", strtotime($from_date));

  //     $prev_day = date('Y-m-d', strtotime($from_date . ' -1 day'));

  //     $opening = queryGet("SELECT 
  //     coa.gl_code,
  //     coa.gl_label,
  //     subgl_code,
  //     subgl_name,
  //     coa.typeAcc,
  //     bal.closing_val AS opening_balance
  // FROM 
  //     erp_opening_closing_balance AS bal
  // LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON coa.id = bal.gl 
  // LEFT JOIN (
  //     SELECT db.glId, db.subGlCode AS subgl_code, db.subGlName AS subgl_name FROM erp_acc_debit AS db LEFT JOIN erp_acc_journal AS jn ON db.journal_id = jn.id WHERE jn.company_id = $company_id AND jn.branch_id = $branch_id AND jn.location_id = $location_id
  //     UNION ALL
  //     SELECT cr.glId, cr.subGlCode AS subgl_code, cr.subGlName AS subgl_name FROM erp_acc_credit AS cr LEFT JOIN erp_acc_journal AS jn ON cr.journal_id = jn.id WHERE jn.company_id = $company_id AND jn.branch_id = $branch_id AND jn.location_id = $location_id
  // ) AS subgl_details
  // ON
  //     bal.gl = subgl_details.glId
  // AND
  //     bal.subgl = subgl_details.subgl_code
  // WHERE
  //     bal.company_id = $company_id 
  //     AND bal.branch_id = $branch_id 
  //     AND bal.location_id = $location_id 
  //     AND bal.gl = $gl_id 
  //     AND bal.subgl = '" . $code . "'
  //     AND bal.date = (
  //         SELECT 
  //             MAX(date)
  //         FROM 
  //             erp_opening_closing_balance
  //         WHERE 
  //             company_id = bal.company_id
  //             AND branch_id = bal.branch_id
  //             AND location_id = bal.location_id
  //             AND gl = bal.gl
  //             AND subgl = bal.subgl
  //             AND date < '$from_date'
  //     )
  // GROUP BY 
  //     coa.gl_code,
  //     coa.gl_label,
  //     subgl_code,
  //     subgl_name,
  //     coa.typeAcc,
  //     opening_balance");




  //     $rest_transaction_first = queryGet("SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,DATE_FORMAT(journal.postingDate, '%b %y') AS pdate,0 AS credit,SUM(debit.debit_amount) AS debit,coa.gl_code,coa.gl_label,debit.subGlCode,debit.subGlName,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON debit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' AND journal.journal_status='active' AND debit.glId= $gl_id AND debit.subGlCode = '" . $code . "' GROUP BY debit.subGlCode,debit.subGlName,coa.gl_code,coa.gl_label,journal.party_code,journal.party_name,journal.parent_slug,journal.remark,journal.postingDate,coa.typeAcc
  //             UNION
  //             SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,DATE_FORMAT(journal.postingDate, '%b %y') AS pdate,SUM(credit.credit_amount) AS credit,0 AS debit,coa.gl_code,coa.gl_label,credit.subGlCode,credit.subGlName,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON credit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' AND journal.journal_status='active' AND credit.glId= $gl_id AND credit.subGlCode = '" . $code . "' GROUP BY credit.subGlCode,credit.subGlName,coa.gl_code,coa.gl_label,journal.party_code,journal.party_name,journal.parent_slug,journal.remark,journal.postingDate,coa.typeAcc");



  //     $opening = round($opening['data']['opening'], 2) ?? 0;

  //     $transaction_first = $rest_transaction_first['data']['debit_sum'] - $rest_transaction_first['data']['credit_sum'];

  //     $opening_balance += $opening + $transaction_first;
  //   }


  // $opening = queryGet("SELECT (SELECT COALESCE(prevOpenClose.opening_val,0) as openingBalance FROM erp_opening_closing_balance AS prevOpenClose WHERE prevOpenClose.company_id = $company_id AND prevOpenClose.branch_id = $branch_id AND prevOpenClose.location_id = $location_id AND prevOpenClose.gl = $gl_id AND prevOpenClose.subgl = '$code' AND prevOpenClose.date = DATE_FORMAT('$from_date', '%Y-%m-01') ORDER BY prevOpenClose.date LIMIT 1) - (SELECT (SELECT COALESCE(SUM(dr.debit_amount),0) AS totalDebitAmount FROM erp_acc_debit AS dr LEFT JOIN erp_acc_journal AS jn ON dr.journal_id = jn.id WHERE jn.company_id = $company_id AND jn.branch_id = $branch_id AND jn.location_id = $location_id AND dr.glId = $gl_id AND dr.subGlCode = '$code' AND (jn.postingDate>= DATE_FORMAT('$from_date', '%Y-%m-01') AND jn.postingDate<'$from_date')) - (SELECT COALESCE(SUM(cr.credit_amount),0) AS totalCreditAmount FROM erp_acc_credit AS cr LEFT JOIN erp_acc_journal AS jn ON cr.journal_id = jn.id WHERE jn.company_id = $company_id AND jn.branch_id = $branch_id AND jn.location_id = $location_id AND cr.glId = $gl_id AND cr.subGlCode = '$code' AND (jn.postingDate>= DATE_FORMAT('$from_date', '%Y-%m-01') AND jn.postingDate<'$from_date'))) AS totalOpeningBalance");


  $opening = queryGet("
  WITH OpeningBalance AS (
      SELECT  
          gl, 
          subgl, 
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
              FROM erp_opening_closing_balance AS inner_eocb
              WHERE gl = eocb.gl 
                AND subgl = eocb.subgl
                AND company_id = $company_id 
                AND branch_id = $branch_id 
                AND location_id = $location_id
              LIMIT 1
          )
      GROUP BY 
          gl, subgl
  ),
  Debits AS (
      SELECT 
          ed.glId AS gl, 
          ed.subGlCode AS subGlCode, 
          ed.subGlName AS subGlName,
          SUM(ed.debit_amount) AS total_debit_value
      FROM 
          erp_acc_debit AS ed
      JOIN 
          erp_acc_journal AS ej ON ed.journal_id = ej.id
      WHERE 
          ej.postingDate >= (
              SELECT MIN(first_date) 
              FROM OpeningBalance 
              WHERE gl = ed.glId AND subGlCode = ed.subGlCode
          ) 
          AND ej.postingDate < '" . $from_date . "' 
          AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id AND ej.journal_status = 'active'
      GROUP BY 
          ed.glId, ed.subGlCode
  ),
  Credits AS (
      SELECT 
          ec.glId AS gl, 
          ec.subGlCode AS subGlCode, 
          ec.subGlName AS subGlName,
          SUM(ec.credit_amount) AS total_credit_value
      FROM 
          erp_acc_credit AS ec
      JOIN 
          erp_acc_journal AS ej ON ec.journal_id = ej.id
      WHERE 
          ej.postingDate >= (
              SELECT MIN(first_date) 
              FROM OpeningBalance 
              WHERE gl = ec.glId AND subGlCode = ec.subGlCode
          ) 
          AND ej.postingDate < '" . $from_date . "' 
          AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id AND ej.journal_status = 'active'
      GROUP BY 
          ec.glId, ec.subGlCode
  ),
  RangeDebits AS (
      SELECT 
          ed.glId AS gl, 
          ed.subGlCode AS subGlCode, 
          ed.subGlName AS subGlName,
          SUM(ed.debit_amount) AS final_debit_value
      FROM 
          erp_acc_debit AS ed
      JOIN 
          erp_acc_journal AS ej ON ed.journal_id = ej.id
      WHERE 
          ej.postingDate >= '" . $from_date . "' 
          AND ej.postingDate <= '" . $to_date . "' 
          AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id AND ej.journal_status = 'active'
      GROUP BY 
          ed.glId, ed.subGlCode
  ),
  RangeCredits AS (
      SELECT 
          ec.glId AS gl, 
          ec.subGlCode AS subGlCode, 
          ec.subGlName AS subGlName,
          SUM(ec.credit_amount) AS final_credit_value
      FROM 
          erp_acc_credit AS ec
      JOIN 
          erp_acc_journal AS ej ON ec.journal_id = ej.id
      WHERE 
          ej.postingDate >= '" . $from_date . "' 
          AND ej.postingDate <= '" . $to_date . "' 
          AND ej.company_id = $company_id AND ej.branch_id = $branch_id AND ej.location_id = $location_id AND ej.journal_status = 'active'
      GROUP BY 
          ec.glId, ec.subGlCode
  )
  SELECT  
      ob.gl, 
      coa.gl_code,
      coa.gl_label,
      ob.subgl,
     (ob.total_opening_value + COALESCE(d.total_debit_value, 0) - COALESCE(c.total_credit_value, 0)) AS from_opening_value,
      COALESCE(rd.final_debit_value, 0) AS final_debit,
      COALESCE(rc.final_credit_value, 0) AS final_credit,
      ((ob.total_opening_value + COALESCE(d.total_debit_value, 0) - COALESCE(c.total_credit_value, 0)) + COALESCE(rd.final_debit_value, 0) - COALESCE(rc.final_credit_value, 0)) AS to_closing_value
  FROM 
      OpeningBalance AS ob
  LEFT JOIN 
      Debits AS d ON ob.gl = d.gl AND ob.subgl = d.subGlCode
  LEFT JOIN 
      Credits AS c ON ob.gl = c.gl AND ob.subgl = c.subGlCode
  LEFT JOIN 
      RangeDebits AS rd ON ob.gl = rd.gl AND ob.subgl = rd.subGlCode
  LEFT JOIN 
      RangeCredits AS rc ON ob.gl = rc.gl AND ob.subgl = rc.subGlCode
  LEFT JOIN 
      `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON ob.gl = coa.id 
  WHERE ob.subgl = '$code'
  ORDER BY 
      ob.gl, ob.subgl");
  

   //  console($opening);
    // exit();
 
  $opening_balance = $opening['data']['from_opening_value'];


  $sum_sql = queryGet("SELECT SUM(temp_table.debit) as debit_sum,SUM(temp_table.credit) as credit_sum FROM (SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,DATE_FORMAT(journal.postingDate, '%b %y') AS pdate,0 AS credit,SUM(debit.debit_amount) AS debit,coa.gl_code,coa.gl_label,debit.subGlCode,debit.subGlName,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON debit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND debit.glId= $gl_id AND debit.subGlCode = '" . $code . "' GROUP BY debit.subGlCode,debit.subGlName,coa.gl_code,coa.gl_label,journal.party_code,journal.party_name,journal.parent_slug,journal.remark,journal.postingDate,coa.typeAcc
   UNION
   SELECT journal.party_code,journal.party_name,journal.parent_slug AS type,journal.remark,DATE_FORMAT(journal.postingDate, '%b %y') AS pdate,SUM(credit.credit_amount) AS credit,0 AS debit,coa.gl_code,coa.gl_label,credit.subGlCode,credit.subGlName,coa.typeAcc FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON credit.glId=coa.id WHERE journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND credit.glId= $gl_id AND credit.subGlCode = '" . $code . "' GROUP BY credit.subGlCode,credit.subGlName,coa.gl_code,coa.gl_label,journal.party_code,journal.party_name,journal.parent_slug,journal.remark,journal.postingDate,coa.typeAcc) AS temp_table");

  //  console($sum_sql);
  //  exit();


 


  $html = '';
  // console($opening['data']);


  // $debit_sum = round($sum_sql['data']['debit_sum'], 2) ?? 0;
  // $credit_sum = round($sum_sql['data']['credit_sum'], 2) ?? 0;
  $balance_due = $opening_balance + ($debit_sum - $credit_sum);


  //mom sql 



  $mom_sql = queryGet("SELECT DATE_FORMAT(postingDate, '%m %Y') AS month_year,
   SUM(credit) AS total_credit, 
   SUM(debit) AS total_debit 
FROM (
SELECT journal.parent_slug AS type,
       journal.remark,
       journal.postingDate,
       0 AS credit,
       SUM(debit.debit_amount) AS debit,
       coa.gl_code,
       coa.gl_label,
       coa.typeAcc,
       debit.subGlCode,
       debit.subGlName
FROM erp_acc_journal AS journal 
LEFT JOIN erp_acc_debit AS debit ON journal.id = debit.journal_id 
LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON debit.glId = coa.id 
WHERE journal.company_id = $company_id 
  AND journal.branch_id = $branch_id
  AND journal.location_id = $location_id
  AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
  AND journal.journal_status = 'active' 
  AND debit.glId = $gl_id AND debit.subGlCode = '" . $code . "'
 GROUP BY journal.parent_slug,
         journal.remark,
         YEAR(journal.postingDate),
         MONTH(journal.postingDate),
         journal.postingDate,
         coa.gl_code,
         coa.gl_label,
         coa.typeAcc,
         debit.subGlCode,
         debit.subGlName
UNION
SELECT journal.parent_slug AS type,
       journal.remark,
       journal.postingDate,
       SUM(credit.credit_amount) AS credit,
       0 AS debit,
       coa.gl_code,
       coa.gl_label,
       coa.typeAcc,
       credit.subGlCode,
       credit.subGlName
FROM erp_acc_journal AS journal 
LEFT JOIN erp_acc_credit AS credit ON journal.id = credit.journal_id 
LEFT JOIN `" . ERP_ACC_CHART_OF_ACCOUNTS . "` AS coa ON credit.glId = coa.id 
WHERE journal.company_id = $company_id
  AND journal.branch_id = $branch_id
  AND journal.location_id = $location_id
  AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' 
  AND journal.journal_status = 'active' 
  AND credit.glId = $gl_id AND credit.subGlCode = '" . $code . "'
GROUP BY journal.parent_slug,
         journal.remark,
         YEAR(journal.postingDate),
         MONTH(journal.postingDate),
         journal.postingDate,
         coa.gl_code,
         coa.gl_label,
         coa.typeAcc,
         credit.subGlCode,
            credit.subGlName
) AS subquery
GROUP BY YEAR(postingDate), MONTH(postingDate), DATE_FORMAT(postingDate, '%m %Y')
ORDER BY YEAR(postingDate), MONTH(postingDate)", true);
  //  console($mom_sql);


  $start_date = new DateTime($from_date);
  $end_date = new DateTime($to_date);



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

<ul class="nav nav-pills mb-3 ledger-tab" id="pills-tab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active text-xs" id="pills-detailLedger-tab" data-bs-toggle="pill" data-bs-target="#pills-detailLedger" type="button" role="tab" aria-controls="pills-detailLedger" aria-selected="true">Detailed View</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link text-xs" id="pills-conciseLedger-tab" data-bs-toggle="pill" data-bs-target="#pills-conciseLedger" type="button" role="tab" aria-controls="pills-conciseLedger" aria-selected="false">Month on Month View</button>
  </li>
</ul>

';

  $html .= '
<div class="tab-content" id="pills-tabContent">
<div class="tab-pane fade show ledger-list-view active" id="pills-detailLedger" role="tabpanel" aria-labelledby="pills-detailLedger-tab" tabindex="0">
<button class="btn btn-primary float-right ml-3 waves-effect waves-light mb-2" id="exportButton" onclick="exportToExcel()">Export to Excel</button>
  <table class="ledger-view-table">
        <thead>
          <tr>
            <th>Date</th>';



  $html .=  '<th>Party Code</th>
              <th>Party Name</th>
              <th>Transaction</th>
            <th>Details</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
            <th class="text-right">Balance</th>
          </tr>
        </thead>
        <tbody>';


  $html .= '
      <tr>
        <td></td>';


  $html .=  '<td></td>
          <td></td>
          <td></td>
        <td>Opening Balance</td>
        <td></td>
        <td></td>
        <td  class="text-right">' . decimalValuePreview($opening_balance) . '</td>
        </tr>';


  $balance = $opening_balance;

  foreach ($data as $rows) {
    //console($rows['pdate']);
    $date = $rows['pDate'];

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
        <td>' . formatDateORDateTime($rows['pdate']) . '</td>
        <td>' . $rows['party_code'] . '</td>
          <td>' . $rows['party_name'] . '</td>
          <td>' . $transaction . '</td>
          <td><p class="pre-normal">' . $details . '</p></td>';

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




  //    $dataByMonth = [];
  // foreach ($mom_sql['data'] as $item) {
  //  $month_year = $item['month_year'];
  //   $month = explode(" ",$month_year)[0];

  //      $dataByMonth[$item[$month]] = $item;
  // }
  //console($dataByMonth);
  // Create an HTML table
  $html .= '<div class="tab-pane fade" id="pills-conciseLedger" role="tabpanel" aria-labelledby="pills-conciseLedger-tab" tabindex="0">
<button class="btn btn-primary float-right ml-3 waves-effect waves-light mb-2" id="exportButtonMonth" onclick="exportToExcelMonth()">Export to Excel</button>
<table class="ledger-view-table-month">
<tr><th>Month</th><th>Opening</th><th>Debit</th><th>Credit</th><th>Closing</th></tr>
<tr>
<td></td>
<td class="text-right font-bold">'
. decimalValuePreview($opening_balance) .'</td>
<td></td>
<td></td>
<td class="text-right font-bold">' . decimalValuePreview($opening_balance) . '</td>
</tr>

';
  // Initialize the closing balance array
  $closing_balances = [];

  // Loop through each month in the SQL result
  // foreach ($mom_sql['data'] as $entry) {
  //   $month_year = $entry['month_year'];
  //   $total_debit = $entry['total_debit'];
  //   $total_credit = $entry['total_credit'];

  //   // Calculate the closing balance
  //   $closing_balance = $opening_balance + $total_debit - $total_credit;

  //   // Store the closing balance for the month
  //   $closing_balances[$month_year] = $closing_balance;

  //   // Update the opening balance for the next month
  //   $opening_balance = $closing_balance;
  // }


  // foreach ($closing_balances as $month_year => $closing_balance) {
  //   $index = array_search($month_year, array_column($mom_sql['data'], 'month_year'));
  //   $total_debit = decimalValuePreview($mom_sql['data'][$index]['total_debit']);
  //   $total_credit = decimalValuePreview($mom_sql['data'][$index]['total_credit']);
  //   $closingBalance = decimalValuePreview($closing_balance);
  //   $html .= "<tbody><tr>
  //               <td>$month_year</td>
  //               <td>$total_debit</td>
  //               <td>$total_credit</td>
  //               <td>$closingBalance</td>
  //             </tr></tbody>";
  // }

  foreach ($mom_sql['data'] as $entry) {
    $month_year = $entry['month_year'];
    $total_debit = $entry['total_debit'];
    $total_credit = $entry['total_credit'];

    // Calculate the closing balance
    $closing_balance = $opening_balance + $total_debit - $total_credit;

    // Store the closing balance for the month
    $closing_balances[$month_year] = $closing_balance;

    // Store the opening balance for the month
    $opening_balances[$month_year] = $opening_balance;

    // Update the opening balance for the next month
    $opening_balance = $closing_balance;
}

foreach ($closing_balances as $month_year => $closing_balance) {
    $index = array_search($month_year, array_column($mom_sql['data'], 'month_year'));
    $opening_balance = decimalValuePreview($opening_balances[$month_year]);
    $total_debit = decimalValuePreview($mom_sql['data'][$index]['total_debit']);
    $total_credit = decimalValuePreview($mom_sql['data'][$index]['total_credit']);
    $closingBalance = decimalValuePreview($closing_balance);
    $date = DateTime::createFromFormat('m Y', "$month_year");
    $formattedDate = $date->format('M-Y');
    $html .= "<tbody><tr>
                <td class='font-bold'>$formattedDate</td>
                <td class='text-right font-bold'>$opening_balance</td>
                <td class='text-right font-bold'>$total_debit</td>
                <td class='text-right font-bold'>$total_credit</td>
                <td class='text-right font-bold'>$closingBalance</td>
              </tr></tbody>";
}
  $html .=  "</tbody></table>";

  echo $html;
}
