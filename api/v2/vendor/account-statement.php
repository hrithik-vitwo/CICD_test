<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    // $vendor_id = 1;
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];

    $cond = "";

    if (isset($_POST['formDate']) && $_POST['formDate'] != '' && isset($_POST['toDate']) && $_POST['toDate'] != '') {
        $cond .= " AND `created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
        $from_date = $_POST['formDate'];
        $to_date = $_POST['toDate'];
    }

    // #################################################################################
    $party_code = $authVendor['vendor_code'];
    // $party_code = '62400047';
    $opening_balance = 0;
    $dateObject = new DateTime($from_date);

    // Get the day of the month
    $dayOfMonth = $dateObject->format('d');

    if ($dayOfMonth === '01') {
        $opening_query = queryGet("SELECT SUM(opening_val) AS opening FROM erp_opening_closing_balance WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND DATE_FORMAT(date,'%Y-%m')=DATE_FORMAT('" . $from_date . "','%Y-%m') AND subgl=$party_code");
        $opening = $opening_query['data']['opening'];

        $opening_balance += $opening;
    } else {
        // Get the first day of the month
        $firstDayOfMonth = date("Y-m-01", strtotime($from_date));

        $prev_day_timestamp = strtotime("-1 day", strtotime($from_date));

        // Use date() to format the timestamp into the desired date format
        $prev_day = date("Y-m-d", $prev_day_timestamp);


        $opening_query = queryGet("SELECT SUM(opening_val) AS opening FROM erp_opening_closing_balance WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND DATE_FORMAT(date,'%Y-%m')=DATE_FORMAT('" . $firstDayOfMonth . "','%Y-%m') AND subgl=$party_code");

        $opening = $opening_query['data']['opening'];

        $transaction_first_sql = queryGet("SELECT SUM(temp_table.amount) - SUM(temp_table.payment) AS transaction_open FROM  (SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,debit.debit_amount AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='Payment' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
                   UNION ALL
                   SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,credit.credit_amount AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug = 'grniv' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
                   UNION ALL
                   SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,credit.credit_amount AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug = 'srniv' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
                   UNION ALL
                     SELECT parent_slug AS type, journal.remark AS remark, dn.postingDate, dn.total AS debit, 0 AS credit FROM erp_debit_note AS dn LEFT JOIN erp_acc_journal AS journal ON dn.journal_id = journal.id WHERE journal.parent_slug='VendorDN' AND dn.company_id=$company_id AND dn.branch_id=$branch_id AND dn.location_id=$location_id AND dn.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' AND dn.status = 'active' AND journal.party_code = $party_code 
                   UNION ALL
                   SELECT parent_slug AS type, journal.remark AS remark, cn.postingDate, 0 AS debit, cn.total AS credit FROM erp_credit_note AS cn LEFT JOIN erp_acc_journal AS journal ON cn.journal_id = journal.id WHERE journal.parent_slug='VendorCN' AND cn.company_id=$company_id AND cn.branch_id=$branch_id AND cn.location_id=$location_id AND cn.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' AND cn.status = 'active' AND journal.party_code = $party_code
                   UNION ALL
                   SELECT 'Rev-Payment' AS type,journal.remark,journal.postingDate,0 AS amount,credit.credit_amount AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug = 'Payment' AND journal.remark LIKE 'REV-%' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
                   UNION ALL
                   SELECT 'Rev-GRN' AS type,journal.remark,journal.postingDate,debit.debit_amount AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='grniv' AND journal.remark LIKE 'REV-%' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
                   UNION ALL
                    SELECT 'Rev-SRN' AS type,journal.remark,journal.postingDate,debit.debit_amount AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='srniv' AND journal.remark LIKE 'REV-%' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
                         ) AS temp_table ORDER BY postingDate");
        // console($transaction_first_sql);
        $transaction_first = $transaction_first_sql['data']['transaction_open'];
        $opening_balance += $opening + $transaction_first;
    }

// **************************************************
    $sum_sql = queryGet("SELECT SUM(temp_table.amount) as amount, SUM(temp_table.payment) as payment, SUM(temp_table.amount) - SUM(temp_table.payment) AS transaction_open FROM  (SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,debit.debit_amount AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='Payment' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
UNION ALL
SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,credit.credit_amount AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug = 'grniv' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
UNION ALL
SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,credit.credit_amount AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug = 'srniv' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
UNION ALL
  SELECT parent_slug AS type, journal.remark AS remark, dn.postingDate, dn.total AS debit, 0 AS credit FROM erp_debit_note AS dn LEFT JOIN erp_acc_journal AS journal ON dn.journal_id = journal.id WHERE journal.parent_slug='VendorDN' AND dn.company_id=$company_id AND dn.branch_id=$branch_id AND dn.location_id=$location_id AND dn.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND dn.status = 'active' AND journal.party_code = $party_code 
UNION ALL
SELECT parent_slug AS type, journal.remark AS remark, cn.postingDate, 0 AS debit, cn.total AS credit FROM erp_credit_note AS cn LEFT JOIN erp_acc_journal AS journal ON cn.journal_id = journal.id WHERE journal.parent_slug='VendorCN' AND cn.company_id=$company_id AND cn.branch_id=$branch_id AND cn.location_id=$location_id AND cn.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND cn.status = 'active' AND journal.party_code = $party_code
UNION ALL
SELECT 'Rev-Payment' AS type,journal.remark,journal.postingDate,0 AS amount,credit.credit_amount AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug = 'Payment' AND journal.remark LIKE 'REV-%' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
UNION ALL
SELECT 'Rev-GRN' AS type,journal.remark,journal.postingDate,debit.debit_amount AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='grniv' AND journal.remark LIKE 'REV-%' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
UNION ALL
 SELECT 'Rev-SRN' AS type,journal.remark,journal.postingDate,debit.debit_amount AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='srniv' AND journal.remark LIKE 'REV-%' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
      ) AS temp_table ORDER BY postingDate", true);

    $billedAmount = $sum_sql['data'][0]['payment'];
    $amountPaid = $sum_sql['data'][0]['amount'];
    $amountDue = round(($opening_balance + $amountPaid) - $billedAmount, 2);
// ************************************************

$statement_sql = queryGet("SELECT * FROM (SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,debit.debit_amount AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='Payment' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
UNION ALL
SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,credit.credit_amount AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug = 'grniv' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
UNION ALL
SELECT journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,credit.credit_amount AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug = 'srniv' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
UNION ALL
  SELECT parent_slug AS type, journal.remark AS remark, dn.postingDate, dn.total AS debit, 0 AS credit FROM erp_debit_note AS dn LEFT JOIN erp_acc_journal AS journal ON dn.journal_id = journal.id WHERE journal.parent_slug='VendorDN' AND dn.company_id=$company_id AND dn.branch_id=$branch_id AND dn.location_id=$location_id AND dn.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND dn.status = 'active' AND journal.party_code = $party_code 
UNION ALL
SELECT parent_slug AS type, journal.remark AS remark, cn.postingDate, 0 AS debit, cn.total AS credit FROM erp_credit_note AS cn LEFT JOIN erp_acc_journal AS journal ON cn.journal_id = journal.id WHERE journal.parent_slug='VendorCN' AND cn.company_id=$company_id AND cn.branch_id=$branch_id AND cn.location_id=$location_id AND cn.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND cn.status = 'active' AND journal.party_code = $party_code
UNION ALL
SELECT 'Rev-Payment' AS type,journal.remark,journal.postingDate,0 AS amount,credit.credit_amount AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.parent_slug = 'Payment' AND journal.remark LIKE 'REV-%' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
UNION ALL
SELECT 'Rev-GRN' AS type,journal.remark,journal.postingDate,debit.debit_amount AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='grniv' AND journal.remark LIKE 'REV-%' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
UNION ALL
 SELECT 'Rev-SRN' AS type,journal.remark,journal.postingDate,debit.debit_amount AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.parent_slug='srniv' AND journal.remark LIKE 'REV-%' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code 
      ) AS subquery ORDER BY postingDate", true);

$data = $statement_sql['data'];

    // #################################################################################

    sendApiResponse([
        "status" => "success",
        "message" => "Data found successfully",
        "data" => [
            "accountSummary" => [
                "openingBalance" => $opening_balance ?? 0,
                "billedAmount" => $billedAmount,
                "amountPaid" => $amountPaid,
                "amountDue" => $amountDue
            ],
            "statement" => $data
        ]
    ]);
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => ""
    ], 405);
}
