<?php
require_once("api-common-func.php");
// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $company_id = $_GET['company_id'];
    $branch_id = $_GET['branch_id'];
    $location_id = $_GET['location_id'];

    $today = date("Y-m-d");
    $dateObject = new DateTime($today);
    // Get the day of the month
    $dayOfMonth = $dateObject->format('d');
    //find customer gl 
    $gl = queryGet("SELECT `bank_gl` FROM `erp_acc_gl_mapping` WHERE `company_id` = $company_id");
    //  console($gl['data']);
    $gl_id = $gl['data']['bank_gl'];
    // $cash_gl_id = $gl['data']['cash_gl'];
    // Check if the day of the month is 1
    $opening_balance = 0;
    $cash_opening_balance = 0;
    if ($dayOfMonth === '01') {
        $opening_query = queryGet("SELECT SUM(opening_val) AS opening FROM erp_opening_closing_balance WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND DATE_FORMAT(date,'%Y-%m')=DATE_FORMAT('" . $today . "','%Y-%m') AND gl=$gl_id");
        $opening = $opening_query['data']['opening'];
        $opening_balance += $opening;

        //  console($rest_transaction_sql);

        // $cash_opening_query = queryGet("SELECT SUM(opening_val) AS opening FROM erp_opening_closing_balance WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND DATE_FORMAT(date,'%Y-%m')=DATE_FORMAT('" . $today . "','%Y-%m') AND gl=$cash_gl_id");
        // $cash_opening = $cash_opening_query['data']['opening'];
        // $cash_opening_balance += $cash_opening;

    } else {
        // Get the first day of the month
        $firstDayOfMonth = date("Y-m-01", strtotime($today));

        $prev_day_timestamp = strtotime("-1 day", strtotime($from_date));

        // Use date() to format the timestamp into the desired date format
        $prev_day = date("Y-m-d", $prev_day_timestamp);


        $opening_query = queryGet("SELECT SUM(opening_val) AS opening FROM erp_opening_closing_balance WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND DATE_FORMAT(date,'%Y-%m')=DATE_FORMAT('" . $firstDayOfMonth . "','%Y-%m') AND gl=$gl_id");

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
                                      WHERE glId = $gl_id 
                                      GROUP BY journal_id) AS ed ON ej.id = ed.journal_id
                                  LEFT JOIN 
                                      (SELECT journal_id, SUM(credit_amount) AS payment
                                      FROM erp_acc_credit
                                      WHERE glId = $gl_id 
                                      GROUP BY journal_id) AS ec ON ej.id = ec.journal_id
                                  WHERE 
                                      ej.glId=$gl_id
                                      AND ej.parent_slug IN ('SOInvoicing','Collection','CustomerCN','CustomerDN','Journal') -- Specify the desired parent_slugs
                                      AND ej.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' -- Specify the date range (replace 'start_date' and 'end_date' with actual dates)
                                      AND ej.company_id = $company_id 
                                      AND ej.branch_id = $branch_id 
                                      AND ej.location_id = $location_id 
                                      AND ej.journal_status='active' 
                                  ORDER BY 
                                      ej.postingDate ASC
                              ) AS temp_table ORDER BY postingDate");
        // console($transaction_first_sql);

        $transaction_first = $transaction_first_sql['data']['transaction_open'];



        $opening_balance += $opening + $transaction_first;

        //cash 


        // $cash_opening_query = queryGet("SELECT SUM(opening_val) AS opening FROM erp_opening_closing_balance WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND DATE_FORMAT(date,'%Y-%m')=DATE_FORMAT('" . $firstDayOfMonth . "','%Y-%m') AND gl=$cash_gl_id");

        // $cash_opening = $opening_query['data']['opening'];

        // $cash_transaction_first_sql = queryGet("SELECT SUM(temp_table.amount) - SUM(temp_table.payment) AS transaction_open FROM  (SELECT 
        //                                   ej.parent_slug AS type,
        //                                   ej.remark,
        //                                   ej.postingDate,
        //                                   COALESCE(ed.amount, 0) AS amount,
        //                                   COALESCE(ec.payment, 0) AS payment
        //                               FROM 
        //                                   erp_acc_journal AS ej
        //                               LEFT JOIN 
        //                                   (SELECT journal_id, SUM(debit_amount) AS amount
        //                                   FROM erp_acc_debit
        //                                   WHERE glId = $cash_gl_id 
        //                                   GROUP BY journal_id) AS ed ON ej.id = ed.journal_id
        //                               LEFT JOIN 
        //                                   (SELECT journal_id, SUM(credit_amount) AS payment
        //                                   FROM erp_acc_credit
        //                                   WHERE glId = $cash_gl_id 
        //                                   GROUP BY journal_id) AS ec ON ej.id = ec.journal_id
        //                               WHERE 
        //                                   ej.glId=$cash_gl_id
        //                                   AND ej.parent_slug IN ('SOInvoicing','Collection','CustomerCN','CustomerDN','Journal') -- Specify the desired parent_slugs
        //                                   AND ej.postingDate BETWEEN '" . $firstDayOfMonth . "' AND '" . $prev_day . "' -- Specify the date range (replace 'start_date' and 'end_date' with actual dates)
        //                                   AND ej.company_id = $company_id 
        //                                   AND ej.branch_id = $branch_id 
        //                                   AND ej.location_id = $location_id 
        //                                   AND ej.journal_status='active' 
        //                               ORDER BY 
        //                                   ej.postingDate ASC
        //                           ) AS temp_table ORDER BY postingDate");
        // // console($transaction_first_sql);

        // $cash_transaction_first = $cash_transaction_first_sql['data']['transaction_open'];



        // $cash_opening_balance += $cash_opening + $cash_transaction_first;

    }



    if (isset($company_id) && $company_id != "") {
        $check_query = queryGet('SELECT `acc_code`,`type_of_account`,`bank_name` FROM `erp_acc_bank_cash_accounts` WHERE `company_id`=' . $company_id . ' AND `status`= "active" AND `type_of_account` = "bank" ORDER BY `acc_code` ASC', true);

        if ($check_query["numRows"] == 0) {
            sendApiResponse([
                "status" => "error",
                "message" => "NO List"

            ], 405);
        } else {
            sendApiResponse([
                "status" => "success",
                "message" => $check_query["data"],
                "parentBankGl" => $opening_balance,

            ], 200);
        }
    } else {
        sendApiResponse([
            "status" => "error",
            "message" => "Something went Wrong"

        ], 405);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed"

    ], 405);
}
