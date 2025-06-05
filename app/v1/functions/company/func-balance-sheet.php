<?php
//*************************************/INSERT/******************************************//
function multiSearch(array $array, array $pairs)
{
    $found = array();
    foreach ($array as $aKey => $aVal) {
        $coincidences = 0;
        foreach ($pairs as $pKey => $pVal) {
            if (array_key_exists($pKey, $aVal) && $aVal[$pKey] == $pVal) {
                $coincidences++;
            }
        }
        if ($coincidences == count($pairs)) {
            $found[$aKey] = $aVal;
        }
    }

    return $found;
}

function fetchBalanceSheet()
{
    global $dbCon;
    $returnData = [];

    $sql = "SELECT gl_code,gl_label,posting_month,SUM(amount) AS total_amount FROM
    (SELECT
        gl_code,
        gl_label,
        posting_month,
        SUM(amount) AS amount,
        type
    FROM
    ((/* Retrieve all credit transactions */
    SELECT
        credit_coa.gl_code AS gl_code,
        credit_coa.gl_label AS gl_label,
        date_format(journal.postingDate,'%M,%Y') AS posting_month,
        credit.credit_amount * (-1) AS amount,
        credit_coa.typeAcc AS account_type,
        'Cr' AS type
    FROM
        erp_acc_journal AS journal
    INNER JOIN
        erp_acc_credit AS credit
      ON
        journal.id = credit.journal_id
    INNER JOIN
        erp_acc_coa_14_table AS credit_coa
        ON
        credit.glId = credit_coa.id
    WHERE
        credit_coa.typeAcc IN (1,2)
        AND journal.postingDate BETWEEN '2022-04-01' AND '2023-03-31')
       
    UNION
    (/* Retrieve all debit transactions */    
    SELECT
        debit_coa.gl_code AS gl_code,
        debit_coa.gl_label AS gl_label,
        date_format(journal.postingDate,'%M,%Y') AS posting_month,
        debit.debit_amount AS amount,
        debit_coa.typeAcc AS account_type,
        'Dr' AS type
    FROM
        erp_acc_journal AS journal
    INNER JOIN
        erp_acc_debit AS debit
      ON
        journal.id = debit.journal_id
    INNER JOIN
        erp_acc_coa_14_table AS debit_coa
        ON
        debit.glId = debit_coa.id
    WHERE
        debit_coa.typeAcc IN (1,2)
        AND journal.postingDate BETWEEN '2022-04-01' AND '2023-03-31')) AS transaction
    GROUP BY
        gl_code, gl_label,posting_month, type) AS BS_summary
    GROUP BY
        gl_code,gl_label,posting_month
    ORDER BY
        STR_TO_DATE(CONCAT('0001 ', posting_month, ' 01'), '%Y %M %d')";

    $balanceSheetObj = queryGet($sql, true);
    $balanceSheetData = $balanceSheetObj["data"];
    $uniqueGlCodes = array_unique(array_column($balanceSheetData, 'gl_code'));

    $list = [];
    foreach ($uniqueGlCodes as $oneGlCode) {
        $list[$oneGlCode] = multiSearch($balanceSheetData, array('gl_code' => $oneGlCode));
    }

    return $list;

    // if ($res = mysqli_query($dbCon, $sql)) {
    //     $row = $res->fetch_all(MYSQLI_ASSOC);
    //     $returnData['status'] = "success";
    //     $returnData['data'] = $row;
    // }
    // return $returnData;
}
