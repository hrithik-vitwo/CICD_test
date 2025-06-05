<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // console($_POST);
  // exit();
  $currentDate = date("Y-m-d"); // Get the current date

  // Get the start date of the current month
  $from_date = date("Y-m-01", strtotime($currentDate));

  // Get the end date of the current month
  $to_date = date("Y-m-t", strtotime($currentDate));
  $party_code = $_POST['customer_code'];

  $dateObject = new DateTime($from_date);

  // Get the day of the month
  $dayOfMonth = $dateObject->format('d');

  // Check if the day of the month is 1
  if ($dayOfMonth === '01') {
    //echo 1;

    $opening_query = queryGet("SELECT SUM(opening_val) AS opening FROM erp_opening_closing_balance WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND DATE_FORMAT(date,'%Y-%m')=DATE_FORMAT('" . $from_date . "','%Y-%m') AND subgl=$party_code");

    $opening = $opening_query['data']['opening'];



    $opening_balance = $opening;
  } else {


    // Get the first day of the month
    $firstDayOfMonth = date("Y-m-01", strtotime($from_date));

    $prev_day = date($from_date, strtotime("-1 day"));


    $opening_query = queryGet("SELECT SUM(opening_val) AS opening FROM erp_opening_closing_balance WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND DATE_FORMAT(date,'%Y-%m')=DATE_FORMAT('" . $from_date . "','%Y-%m') AND subgl=$party_code");
    // console($opening_query);
    $opening = $opening_query['data']['opening'];

    $transaction_first_sql = queryGet("SELECT SUM(temp_table.debit) - SUM(temp_table.credit) AS transaction_open FROM (
                 SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS debit,credit.credit_amount AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug='Collection' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND journal.party_code=$party_code 
  UNION ALL
  SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,CASE WHEN journal.remark LIKE 'REV%' THEN 0 ELSE debit.debit_amount END AS debit,CASE WHEN journal.remark LIKE 'REV%' THEN debit.debit_amount ELSE 0 END AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='SOInvoicing' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND journal.party_code=$party_code 
  UNION ALL
  SELECT journal.journalEntryReference AS type,journal.remark AS remark,journal.postingDate,0 AS debit,credit.credit_amount AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug='journal' AND journal.journalEntryReference='Collection' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND credit.subGlCode=$party_code 
  UNION ALL
  SELECT 'SOInvoicing' AS type,journal.remark AS remark,journal.postingDate,debit.debit_amount AS debit,0 AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='journal' AND journal.journalEntryReference='Sales' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND debit.subGlCode=$party_code 
   UNION ALL
 SELECT parent_slug AS type, journal.remark AS remark, cn.postingDate, 0 AS debit, cn.total AS credit FROM erp_credit_note AS cn LEFT JOIN erp_acc_journal AS journal ON cn.journal_id = journal.id WHERE journal.parent_slug='CustomerCN' AND cn.company_id = $company_id AND cn.branch_id = $branch_id AND cn.location_id = $location_id AND cn.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND cn.status = 'active' AND journal.party_code = $party_code 
 UNION ALL
 SELECT parent_slug AS type, journal.remark AS remark, dn.postingDate, dn.total AS debit, 0 AS credit FROM erp_debit_note AS dn LEFT JOIN erp_acc_journal AS journal ON dn.journal_id = journal.id WHERE journal.parent_slug='CustomerCN' AND dn.company_id = $company_id AND dn.branch_id = $branch_id AND dn.location_id = $location_id AND dn.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND dn.status = 'active' AND journal.party_code = $party_code) AS temp_table
                  ORDER BY temp_table.postingDate desc;");
    // console($transaction_first_sql);
    $transaction_first = $transaction_first_sql['data']['transaction_open'];




    $opening_balance = $opening + $transaction_first;
  }
  // echo $opening_balance;
  $statement_sql = queryGet("SELECT type, remark, postingDate, debit, credit FROM(SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS debit,credit.credit_amount AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug='Collection' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND journal.party_code=$party_code 
  UNION ALL
  SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,CASE WHEN journal.remark LIKE 'REV%' THEN 0 ELSE debit.debit_amount END AS debit,CASE WHEN journal.remark LIKE 'REV%' THEN debit.debit_amount ELSE 0 END AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='SOInvoicing' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND journal.party_code=$party_code 
  UNION ALL
  SELECT journal.journalEntryReference AS type,journal.remark AS remark,journal.postingDate,0 AS debit,credit.credit_amount AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug='journal' AND journal.journalEntryReference='Collection' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND credit.subGlCode=$party_code 
  UNION ALL
  SELECT 'SOInvoicing' AS type,journal.remark AS remark,journal.postingDate,debit.debit_amount AS debit,0 AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='journal' AND journal.journalEntryReference='Sales' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND debit.subGlCode=$party_code 
   UNION ALL
 SELECT parent_slug AS type, journal.remark AS remark, cn.postingDate, 0 AS debit, cn.total AS credit FROM erp_credit_note AS cn LEFT JOIN erp_acc_journal AS journal ON cn.journal_id = journal.id WHERE journal.parent_slug='CustomerCN' AND cn.company_id = $company_id AND cn.branch_id = $branch_id AND cn.location_id = $location_id AND cn.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND cn.status = 'active' AND journal.party_code = $party_code 
 UNION ALL
 SELECT parent_slug AS type, journal.remark AS remark, dn.postingDate, dn.total AS debit, 0 AS credit FROM erp_debit_note AS dn LEFT JOIN erp_acc_journal AS journal ON dn.journal_id = journal.id WHERE journal.parent_slug='CustomerCN' AND dn.company_id = $company_id AND dn.branch_id = $branch_id AND dn.location_id = $location_id AND dn.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND dn.status = 'active' AND journal.party_code = $party_code) AS subquery
 ORDER BY postingDate", true);

  // console($statement_sql);



  $data = $statement_sql['data'];
  //console($statement_sql);
  $balance =   $opening_balance ?? 0;
  // exit();

  //  $balance = 0.00;
  $sum_sql = queryGet("SELECT SUM(temp_table.debit) as amount, SUM(temp_table.credit) as payment, SUM(temp_table.debit) - SUM(temp_table.credit) AS transaction_open FROM (SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS debit,credit.credit_amount AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug='Collection' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND journal.party_code=$party_code 
  UNION ALL
  SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,CASE WHEN journal.remark LIKE 'REV%' THEN 0 ELSE debit.debit_amount END AS debit,CASE WHEN journal.remark LIKE 'REV%' THEN debit.debit_amount ELSE 0 END AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='SOInvoicing' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND journal.party_code=$party_code 
  UNION ALL
  SELECT journal.journalEntryReference AS type,journal.remark AS remark,journal.postingDate,0 AS debit,credit.credit_amount AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug='journal' AND journal.journalEntryReference='Collection' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND credit.subGlCode=$party_code 
  UNION ALL
  SELECT 'SOInvoicing' AS type,journal.remark AS remark,journal.postingDate,debit.debit_amount AS debit,0 AS credit FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='journal' AND journal.journalEntryReference='Sales' AND journal.company_id = $company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND journal.journal_status='active' AND debit.subGlCode=$party_code 
   UNION ALL
 SELECT parent_slug AS type, journal.remark AS remark, cn.postingDate, 0 AS debit, cn.total AS credit FROM erp_credit_note AS cn LEFT JOIN erp_acc_journal AS journal ON cn.journal_id = journal.id WHERE journal.parent_slug='CustomerCN' AND cn.company_id = $company_id AND cn.branch_id = $branch_id AND cn.location_id = $location_id AND cn.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND cn.status = 'active' AND journal.party_code = $party_code 
 UNION ALL
 SELECT parent_slug AS type, journal.remark AS remark, dn.postingDate, dn.total AS debit, 0 AS credit FROM erp_debit_note AS dn LEFT JOIN erp_acc_journal AS journal ON dn.journal_id = journal.id WHERE journal.parent_slug='CustomerCN' AND dn.company_id = $company_id AND dn.branch_id = $branch_id AND dn.location_id = $location_id AND dn.postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND dn.status = 'active' AND journal.party_code = $party_code) AS temp_table
  ORDER BY temp_table.postingDate desc;", true);


  $html = '';
  $print_html = '';



  $html .= '<div class="row state-head">
  <div class="col-lg-6 col-md-6 col-sm-12">
    <div class="intro-head">
      <h2>STATEMENT OF ACCOUNTS</h2>
      <p>' . $from_date . ' To ' . $to_date . '</p>
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
              <p class="text-xs">Rs' . round($opening_balance, 2) . '</p>
            </div>
            <div class="display-flex-space-between">
              <p class="text-xs">Debit</p>
              <p class="text-xs">Rs ' . round($sum_sql['data'][0]['amount'], 2) . '</p>
            </div>
            <div class="display-flex-space-between">
              <p class="text-xs">Credit</p>
              <p class="text-xs">Rs ' . round($sum_sql['data'][0]['payment'], 2) . '</p>
            </div>
            <hr>
            <div class="display-flex-space-between">
              <p class="text-xs">Balance Due</p>
              <p class="text-xs">Rs' . round($opening_balance + $sum_sql['data'][0]['transaction_open'], 2) . '</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>';

  $html .= '<div class="row state-table">
  <table class="statement-table">
    <thead>
      <tr>
      <th>Date</th>
      <th>Transaction</th>
      <th>Details</th>
      <th class="text-right">Debit</th>
      <th class="text-right">Credit</th>
      <th class="text-right">Balance</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td>' . $from_date . ' To ' . $to_date . '</td>
      <td>Opening Balance</td>
      <td></td>
      <td class="text-right"></td>
      <td class="text-right"></td>
      <td class="text-right">' . round($opening_balance, 2) . '</td>
    </tr>';


  $balance = $opening_balance ?? 0;
  foreach ($data as $rows) {
    // console($rows);
    $date = $rows['postingDate'];

    $type = $rows['type'];
    $transaction = $rows['type'];
    $details = $rows['remark'];

    if ($rows['credit'] == 0) {

      $amount = $rows['debit'];

      $balance = $balance + $amount;
    } elseif ($rows['debit'] == 0) {
      $amount = $rows['credit'];
      $balance = $balance - $amount;
    }



    $html .= '<tr>
        <td>' . $date . '</div>
        <td>' . $transaction . '</td>
        <td>' . $details . '</td>
        <td class="text-right">';




    if ($rows['credit'] == 0) {
      $so_amount = round($amount, 2);
    } else {
      $so_amount = 0;
    }

    $html .=  $so_amount . '</td>
        <td class="text-right">';
    if ($rows['debit'] == 0) {
      $c_amount = round($amount, 2);
    } else {
      $c_amount = 0;
    }

    $html .=  $c_amount . '</td>
        <td class="text-right">' . round($balance, 2) . '</td>
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
      <td class="font-bold">Balance Due</td>
      <td colspan="5" class="text-right font-bold">' . round($opening_balance + $total, 2) . '</td>
      </tr>
    </tbody>
  </table>
</div>';








  $print_html .= '   <table class="table defaultDataTable table-nowrap" id="printable-content">
<tbody>
  <tr>
    <td><img src="../../public/assets/img/logo/vitwo-logo.png" alt="Logo-Company" style="max-width: 150px;"></td>
    <td></td>
    <td></td>
    <td colspan="3">
      <p class="text-lg mb-0 text-bold">ABC Pvt. Ltd.</p>
      <p class="text-lg mb-0">AIC DSU Innovation Foundation</p>
      <p class="text-lg mb-0">Kolkata</p>
      <p class="text-lg mb-0">Kolkata 111111 India</p>
      <p class="text-lg mb-0">+91 1234567897</p>
      <p class="text-lg mb-0">test@test.com</p>
      <p class="text-lg mb-0">www.test.com</p> 
      <p class="text-lg mb-0">GSTIN: 1111111111</p>
    </td>
  </tr>
</tbody>
<tbody>
  <tr>
    <td>
      <p class="text-lg text-bold mb-0">To,</p>
      <p class="text-lg text-bold mb-0">AIC DSU Innovation Foundation</p>
      <p class="text-lg mb-0">Kolkata</p>
      <p class="text-lg mb-0">Kolkata 111111 India</p>
      <p class="text-lg mb-0">+91 1234567897</p>
      <p class="text-lg mb-0">test@test.com</p>
      <p class="text-lg mb-0">www.test.com</p>
      <p class="text-lg mb-0">GSTIN: 1111111111</p>
    </td>
    <td></td>
    <td></td>
    <td colspan="3">
      <p class="text-xl text-bold border-bottom mb-0">STATEMENT OF ACCOUNTS</p>
      <p class="text-lg border-bottom mb-1 mt-1 text-right">01/04/2023 To 31/03/2024</p>
      <p class="text-lg text-bold bg-grey border-0 text-center mb-2 mt-1" style="background-color: #ccc;">Account Summary</p>
      <table width="100%">
        <tr>
          <td class="border-0">
            <p class="text-lg mb-1">Opening Balance</p>
          </td>
          <td class="text-right border-0" style="text-align: right;">
            <p class="text-lg mb-1">Rs 43,365.28</p>
          </td>
        </tr>
        <tr>
          <td class="border-0">
            <p class="text-lg mb-1">Debit</p>
          </td>
          <td class="text-right border-0">
            <p class="text-lg mb-1">Rs 43,365.28</p>
          </td>
        </tr>
        <tr>
          <td class="border-0">
            <p class="text-lg mb-1">Credit</p>
          </td>
          <td class="text-right border-0">
            <p class="text-lg mb-1">Rs 43,365.28</p>
          </td>
        </tr>
        <tr>
          <td class="border-0">
            <p class="text-lg mb-1">Balance Due</p>
          </td>
          <td class="text-right border-0">
            <p class="text-lg mb-1">Rs 43,365.28</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</tbody>
<tbody class="pt-3">
  <tr>
    <th class="text-lg">Date</th>
    <th class="text-lg">Transaction</th>
    <th class="text-lg">Details</th>
    <th class="text-right text-lg">Amount</th>
    <th class="text-right text-lg">Payments</th>
    <th class="text-right text-lg">Balance</th>
  </tr>
  <tr>
    <td class="text-lg">03/06/2023</td>
    <td class="text-lg">Bill</td>
    <td class="text-lg">FY2023-24-00045 - due on 23/05/2023</td>
    <td class="text-right text-lg">43,365.25</td>
    <td class="text-right text-lg"></td>
    <td class="text-right text-lg">43,675.00</td>
  </tr>
  <tr>
    <td class="text-lg">03/06/2023</td>
    <td class="text-lg">Bill</td>
    <td class="text-lg">FY2023-24-00045 - due on 23/05/2023</td>
    <td class="text-right text-lg"></td>
    <td class="text-right text-lg">-43,365.25</td>
    <td class="text-right text-lg">0</td>
  </tr>
  <tr>
    <td class="text-lg"></td>
    <td class="text-lg"></td>
    <td class="text-lg"></td>
    <td colspan="2" class="text-right text-lg text-bold">Balance Due</td>
    <td class="text-right text-lg text-bold">-43,365.25</td>
  </tr>
</tbody>
</table>';






  $returndata['print_html'] = $print_html;
  $returndata['html'] = $html;

  echo json_encode($returndata);
}
