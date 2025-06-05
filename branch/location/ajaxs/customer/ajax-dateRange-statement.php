<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // console($_POST);
  // exit();
  $from_date = $_POST['from_date'];
  $to_date = $_POST['to_date'];
  $party_code = $_POST['customer_code'];


  $party_gl = 88;
  $opening_balance = 0;
  $dateObject = new DateTime($from_date);

  // Get the day of the month
  $dayOfMonth = $dateObject->format('d');

  // Check if the day of the month is 1
  if ($dayOfMonth === '01') {
    $opening_query = queryGet("SELECT SUM(opening_val) AS opening FROM erp_opening_closing_balance WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND DATE_FORMAT(date,'%Y-%m')=DATE_FORMAT('" . $from_date . "','%Y-%m') AND subgl=$party_code");
    $opening = $opening_query['data']['opening'];
    $opening_balance += $opening;

    //  console($rest_transaction_sql);

  } else {


    // Get the first day of the month
    $firstDayOfMonth = date("Y-m-01", strtotime($from_date));

    $prev_day_timestamp = strtotime("-1 day", strtotime($from_date));

    // Use date() to format the timestamp into the desired date format
    $prev_day = date("Y-m-d", $prev_day_timestamp);


    $opening_query = queryGet("SELECT SUM(opening_val) AS opening FROM erp_opening_closing_balance WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND DATE_FORMAT(date,'%Y-%m')=DATE_FORMAT('" . $firstDayOfMonth . "','%Y-%m') AND subgl=$party_code");

    $opening = $opening_query['data']['opening'];
    $transaction_first_sql = queryGet("SELECT SUM(temp_table.amount) - SUM(temp_table.payment) AS transaction_open FROM  (SELECT 
                                      ej.parent_slug AS type,
                                      ej.remark,
                                      ej.postingDate,
                                      COALESCE(ed.amount, 0) AS amount,
                                      COALESCE(ec.payment, 0) AS payment
                                  FROM 
                                      erp_acc_journal AS ej
                                  LEFT JOIN 
                                      (SELECT journal_id, SUM(debit_amount) AS amount
                                      FROM erp_acc_debit
                                      WHERE glId = $party_gl AND subGlCode=$party_code
                                      GROUP BY journal_id) AS ed ON ej.id = ed.journal_id
                                  LEFT JOIN 
                                      (SELECT journal_id, SUM(credit_amount) AS payment
                                      FROM erp_acc_credit
                                      WHERE glId = $party_gl AND subGlCode=$party_code
                                      GROUP BY journal_id) AS ec ON ej.id = ec.journal_id
                                  WHERE 
                                      ej.party_code = $party_code
                                      AND ej.parent_slug IN ('SOInvoicing','Collection','CustomerCN','CustomerDN','Journal') -- Specify the desired parent_slugs
                                      AND ej.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' -- Specify the date range (replace 'start_date' and 'end_date' with actual dates)
                                      AND ej.company_id = $company_id -- Filter by company_id
                                      AND ej.branch_id = $branch_id -- Filter by branch_id
                                      AND ej.location_id = $location_id -- Filter by location_id
                                      AND ej.journal_status='active' -- Filter by status
                                  ORDER BY 
                                      ej.postingDate ASC
                              ) AS temp_table ORDER BY postingDate");
    // console($transaction_first_sql);

    $transaction_first = $transaction_first_sql['data']['transaction_open'];



    $opening_balance += $opening + $transaction_first;
  }



  $fullgroup = queryGet("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

   $hdjjd="SELECT 
  ej.parent_slug AS type,
  ej.remark,
  ej.postingDate,
  ej.remark,ej.`jv_no`,
  ej.refarenceCode,
  ej.id,
  ej.documentNo,
  COALESCE(ed.amount, 0) AS amount,
  COALESCE(ec.payment, 0) AS payment
FROM 
  erp_acc_journal AS ej
LEFT JOIN 
  (SELECT journal_id, SUM(debit_amount) AS amount
  FROM erp_acc_debit
  WHERE glId = $party_gl AND subGlCode=$party_code
  GROUP BY journal_id) AS ed ON ej.id = ed.journal_id
LEFT JOIN 
  (SELECT journal_id, SUM(credit_amount) AS payment
  FROM erp_acc_credit
  WHERE glId = $party_gl AND subGlCode=$party_code
  GROUP BY journal_id) AS ec ON ej.id = ec.journal_id
WHERE 
  ej.party_code = $party_code
  AND ej.parent_slug IN ('SOInvoicing','Collection','CustomerCN','CustomerDN','Journal') -- Specify the desired parent_slugs
  AND ej.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' -- Specify the date range (replace 'start_date' and 'end_date' with actual dates)
  AND ej.company_id = $company_id -- Filter by company_id
  AND ej.branch_id = $branch_id -- Filter by branch_id
  AND ej.location_id = $location_id -- Filter by location_id
  AND ej.journal_status='active' -- Filter by status
ORDER BY 
  ej.postingDate ASC";

  $statement_sql = queryGet("SELECT 
                                ej.parent_slug AS type,
                                ej.remark,
                                ej.postingDate,
                                ej.remark,ej.`jv_no`,
                                ej.refarenceCode,
                                ej.id,
                                ej.documentNo,
                                COALESCE(ed.amount, 0) AS amount,
                                COALESCE(ec.payment, 0) AS payment
                              FROM 
                                erp_acc_journal AS ej
                              LEFT JOIN 
                                (SELECT journal_id, SUM(debit_amount) AS amount
                                FROM erp_acc_debit
                                WHERE glId = $party_gl AND subGlCode=$party_code
                                GROUP BY journal_id) AS ed ON ej.id = ed.journal_id
                              LEFT JOIN 
                                (SELECT journal_id, SUM(credit_amount) AS payment
                                FROM erp_acc_credit
                                WHERE glId = $party_gl AND subGlCode=$party_code
                                GROUP BY journal_id) AS ec ON ej.id = ec.journal_id
                              WHERE 
                                ej.party_code = $party_code
                                AND ej.parent_slug IN ('SOInvoicing','Collection','CustomerCN','CustomerDN','Journal') -- Specify the desired parent_slugs
                                AND ej.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' -- Specify the date range (replace 'start_date' and 'end_date' with actual dates)
                                AND ej.company_id = $company_id -- Filter by company_id
                                AND ej.branch_id = $branch_id -- Filter by branch_id
                                AND ej.location_id = $location_id -- Filter by location_id
                                AND ej.journal_status='active' -- Filter by status
                              ORDER BY 
                                ej.postingDate ASC", true);

  // $sql = queryGet("SELECT *
  // FROM (
  //     (SELECT j.*, d.journal_id, d.glId, d.subGlCode, d.subGlName, d.debit_amount AS amount, d.debit_remark AS remarks, 'dr' AS dr_cr
  //     FROM erp_acc_journal AS j
  //     LEFT JOIN erp_acc_debit AS d ON j.id = d.journal_id)

  //     UNION

  //     (SELECT j.*, c.journal_id, c.glId, c.subGlCode, c.subGlName, c.credit_amount AS amount, c.credit_remark AS remarks, 'cr' AS dr_cr
  //     FROM erp_acc_journal AS j
  //     LEFT JOIN erp_acc_credit AS c ON j.id = c.journal_id)
  // ) AS tbl WHERE party_code=$party_code AND  company_id = $company_id AND branch_id = $branch_id AND location_id =  $location_id AND postingDate BETWEEN '".$from_date."' AND '".$to_date."' AND subGlCode!='' ORDER BY id DESC",true);

  // console($statement_sql);
  // exit();
  $data = $statement_sql['data'];
  //console($statement_sql);
  $balance =   $opening_balance ?? 0;
  //exit();

  //  $balance = 0.00;
  $sum_sql = queryGet("SELECT SUM(temp_table.amount) as amount, SUM(temp_table.payment) as payment, SUM(temp_table.amount) - SUM(temp_table.payment) AS transaction_open FROM  (SELECT 
                              ej.parent_slug AS type,
                              ej.remark,
                              ej.postingDate,
                              COALESCE(ed.amount, 0) AS amount,
                              COALESCE(ec.payment, 0) AS payment
                            FROM 
                              erp_acc_journal AS ej
                            LEFT JOIN 
                              (SELECT journal_id, SUM(debit_amount) AS amount
                              FROM erp_acc_debit
                              WHERE glId = $party_gl AND subGlCode=$party_code
                              GROUP BY journal_id) AS ed ON ej.id = ed.journal_id
                            LEFT JOIN 
                              (SELECT journal_id, SUM(credit_amount) AS payment
                              FROM erp_acc_credit
                              WHERE glId = $party_gl AND subGlCode=$party_code
                              GROUP BY journal_id) AS ec ON ej.id = ec.journal_id
                            WHERE 
                              ej.party_code = $party_code
                              AND ej.parent_slug IN ('SOInvoicing','Collection','CustomerCN','CustomerDN','Journal') -- Specify the desired parent_slugs
                              AND ej.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' -- Specify the date range (replace 'start_date' and 'end_date' with actual dates)
                              AND ej.company_id = $company_id -- Filter by company_id
                              AND ej.branch_id = $branch_id -- Filter by branch_id
                              AND ej.location_id = $location_id -- Filter by location_id
                              AND ej.journal_status='active' -- Filter by status
                            ORDER BY 
                              ej.postingDate ASC
      ) AS temp_table ORDER BY postingDate", true);
  // console($sum_sql);
  //   exit();
  // echo "ok";
  // echo $opening_balance;
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
      <th>JV No</th>
      <th>Document No</th>
      <th>Details</th>
      <th class="text-right">Debit</th>
      <th class="text-right">Credit</th>
      <th class="text-right">Balance</th>
      </tr>
    </thead>
    <tbody>
    <tr>
      <td>' . $from_date . '</td>
      <td>Opening Balance</td>
      <td></td>
      <td></td>
      <td></td>
      <td class="text-right"></td>
      <td class="text-right"></td>
      <td class="text-right">' . round($opening_balance, 2) . '</td>
    </tr>';


  $balance = $opening_balance ?? 0;
  foreach ($data as $rows) {
    // console($rows);
    $refarenceCode = $rows['refarenceCode'];
    $documentNo = $rows['documentNo'];
    $id = $rows['id'];
    $jv_no = $rows['jv_no'];
    $date = $rows['postingDate'];

    $type = $rows['type'];
    $transaction = $rows['type'];
    $details = $rows['remark'];

    if ($rows['payment'] == 0) {

      $amount = $rows['amount'];

      $balance = $balance + $amount;
    } elseif ($rows['amount'] == 0) {
      $amount = $rows['payment'];
      $balance = $balance - $amount;
    }



    $html .= '<tr>
        <td>' . $date . '</td>
        <td>' . $transaction . '</td>
        <td>' . $jv_no . '</td>
        <td>' . $documentNo . '</td>
        <td><p class="pre-normal">' . $details . '</p></td>
        <td class="text-right">';




    if ($rows['payment'] == 0) {
      $so_amount = round($amount, 2);
    } else {
      $so_amount = 0;
    }

    $html .=  $so_amount . '</td>
        <td class="text-right">';
    if ($rows['amount'] == 0) {
      $c_amount = round($amount, 2);
    } else {
      $c_amount = 0;
    }

    $html .=  $c_amount . '</td>
        <td class="text-right">' . round($balance, 2) . '</td>
      </tr>';
  }



  $total = array_reduce($data, function ($carry, $rows) {
    if ($rows['payment'] == 0) {
      $carry += $rows['amount'];
    } elseif ($rows['amount'] == 0) {
      $carry -= $rows['payment'];
    }
    return $carry;
  }, 0);


  $html .= '<tr>
      <td class="font-bold">Balance Due</td>
      <td colspan="7" class="text-right font-bold">' . round($opening_balance + $total, 2) . '</td>
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






  $returndata['hdjjd'] = $hdjjd;
  $returndata['print_html'] = $print_html;
  $returndata['html'] = $html;

  echo json_encode($returndata);
}
