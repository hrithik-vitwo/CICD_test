<?php
require_once("app/v1/connection-branch-admin.php");
require_once("app/v1/functions/company/func-ChartOfAccounts.php");
require_once("app/v1/functions/branch/func-journal.php");

for ($i = 1; $i <= 1; $i++) {
    $tableName = "erp_acc_coa_default";

    // $selectUsedCoa = queryGet("SELECT DISTINCT(temp_table.gl_id) FROM
    //          (SELECT debit.glId AS gl_id FROM erp_acc_journal as journal INNER JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.company_id=1
    //          UNION
    //          SELECT credit.glId AS gl_id FROM erp_acc_journal as journal INNER JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.company_id=1) AS temp_table LEFT JOIN erp_acc_coa_1_table AS coa ON temp_table.gl_id=coa.id", true);

    // // console($selectUsedCoa);
    // foreach ($selectUsedCoa['data'] as $selectUsedCoarow) {
    //     $selectCoaTxn=queryUpdate("UPDATE ".$tableName." SET `txn_status` = 1 WHERE `id` = ".$selectUsedCoarow['gl_id']."");
    //     console($selectCoaTxn);
    // }

    $selectCoa = queryGet("SELECT * FROM " . $tableName . "", true);

    console($selectCoa);
    $gl_code1='';
    $gl_code2='';
    $gl_code3='';
    $gl_code4='';
    foreach ($selectCoa['data'] as $coarow) {
        console($coarow);
        $selectCoa=queryUpdate("UPDATE ".$tableName." SET sp_id=p_id WHERE `id` = ".$coarow['id']."");
        if ($coarow['glStType'] == 'group') {
            $selectCoa=queryUpdate("UPDATE ".$tableName." SET `gl_code` ='' WHERE `id` = ".$coarow['id']."");
            console($selectCoa);
        } else {
             
            $typeAcc = $coarow['typeAcc'];
            if ($typeAcc == 1) {
                echo $gl_code1 = getCOASerialNumber($gl_code1, $typeAcc);
                $selectCoa=queryUpdate("UPDATE ".$tableName." SET `gl_code` =$gl_code1  WHERE `id` = ".$coarow['id']."");
                console($selectCoa);
            }
            if ($typeAcc == 2) {
                echo $gl_code2 = getCOASerialNumber($gl_code2, $typeAcc);
                $selectCoa=queryUpdate("UPDATE ".$tableName." SET `gl_code` =$gl_code2  WHERE `id` = ".$coarow['id']."");
                console($selectCoa);
            }
            if ($typeAcc == 3) {
                echo $gl_code3 = getCOASerialNumber($gl_code3, $typeAcc);
                $selectCoa=queryUpdate("UPDATE ".$tableName." SET `gl_code` =$gl_code3  WHERE `id` = ".$coarow['id']."");
                console($selectCoa);
            }
            if ($typeAcc == 4) {
               echo $gl_code4 = getCOASerialNumber($gl_code4, $typeAcc);
                $selectCoa=queryUpdate("UPDATE ".$tableName." SET `gl_code` =$gl_code4  WHERE `id` = ".$coarow['id']."");
                console($selectCoa);
            }
        }
    }
}
