<?php
// 0 0 1 * * php /home/devalpha/public_html/app/v1/cron/opening-balance-update.php
// 0 0 1 * * php /home/alpha/public_html/app/v1/cron/opening-balance-update.php
// 0 0 * * * php /var/www/console.claimz.in/public_html/api/artisan schedule:run >> /dev/null 2>&1
require_once dirname(__DIR__)."/connection-branch-admin.php";
// require_once dirname(__DIR__)."/functions/branch/func-bom-controller.php";

echo "Working:";

if(date("d")==1){
    // $curMonthFirstDate = "2023-07-01";
    $curMonthFirstDate = date("Y-m-01");
    $curMonthLastDate = date("Y-m-t", strtotime($curMonthFirstDate));
    $prevMonthFirstDate = date("Y-m-d", strtotime($curMonthFirstDate . " -1 month"));
    $prevMonthLastDate = date("Y-m-t", strtotime($prevMonthFirstDate));

    // echo "<br>curMonthFirstDate :".$curMonthFirstDate;
    // echo "<br>curMonthLastDate :".$curMonthLastDate;
    // echo "<br>prevMonthFirstDate :".$prevMonthFirstDate;
    // echo "<br>prevMonthLastDate :".$prevMonthLastDate;

    $dbObj = new Database(true);

    // $openingBalanceTable = "erp_opening_closing_balance_test";
    $openingBalanceTable = "erp_opening_closing_balance";

    $selectObj=$dbObj->queryGet('SELECT * FROM `'.$openingBalanceTable.'` WHERE `date` BETWEEN "'.$prevMonthFirstDate.'" AND "'.$prevMonthLastDate.'" ', true);

    echo "<pre>";

    // print_r($selectObj);

    foreach($selectObj["data"] as $prevMonthOpening){
        print_r("\n:::::::::::Previous Month opening details::::::::::::::::::::::::\n");
        print_r($prevMonthOpening);
        $debitDetailsObj = $dbObj->queryGet(
            'SELECT
                `erp_acc_journal`.`id`,
                `erp_acc_journal`.`postingDate`,
                `erp_acc_debit`.`glId`,
                `erp_acc_debit`.`subGlCode`,
                `erp_acc_debit`.`debit_amount`
            FROM
                `erp_acc_journal`
            LEFT JOIN `erp_acc_debit` ON `erp_acc_journal`.`id` = `erp_acc_debit`.`journal_id`
            WHERE
                `erp_acc_journal`.`company_id` = '.$prevMonthOpening["company_id"].'
                 AND `erp_acc_journal`.`branch_id` = '.$prevMonthOpening["branch_id"].' 
                 AND `erp_acc_journal`.`location_id` = '.$prevMonthOpening["location_id"].' 
                 AND `erp_acc_debit`.`glId` = '.$prevMonthOpening["gl"].' 
                 AND `erp_acc_debit`.`subGlCode` = "'.$prevMonthOpening["subgl"].'" 
                 AND `erp_acc_journal`.`postingDate` BETWEEN "'.$prevMonthFirstDate.'" AND "'.$prevMonthLastDate.'"', true);

        $totalDebitAmount = array_sum(array_column($debitDetailsObj["data"] ?? [], 'debit_amount'));
        
        $creditDetailsObj = $dbObj->queryGet(
            'SELECT
                `erp_acc_journal`.`id`,
                `erp_acc_journal`.`postingDate`,
                `erp_acc_credit`.`glId`,
                `erp_acc_credit`.`subGlCode`,
                `erp_acc_credit`.`credit_amount`
            FROM
                `erp_acc_journal`
            LEFT JOIN `erp_acc_credit` ON `erp_acc_journal`.`id` = `erp_acc_credit`.`journal_id`
            WHERE
                `erp_acc_journal`.`company_id` = '.$prevMonthOpening["company_id"].'
                 AND `erp_acc_journal`.`branch_id` = '.$prevMonthOpening["branch_id"].' 
                 AND `erp_acc_journal`.`location_id` = '.$prevMonthOpening["location_id"].' 
                 AND `erp_acc_credit`.`glId` = '.$prevMonthOpening["gl"].' 
                 AND `erp_acc_credit`.`subGlCode` = "'.$prevMonthOpening["subgl"].'" 
                 AND `erp_acc_journal`.`postingDate` BETWEEN "'.$prevMonthFirstDate.'" AND "'.$prevMonthLastDate.'"', true);

        $totalCreditAmount = array_sum(array_column($creditDetailsObj["data"] ?? [], 'credit_amount'));

        // if($prevMonthOpening["subgl"]!=""){
        //     echo "SubGl lavel opening closing details<br>";
        // }else{
        //     echo "Only Gl lavel opening closing details<br>";
        // }
        // echo "totalDebitAmount :". $totalDebitAmount."<br>";
        // echo "totalCreditAmount :". $totalCreditAmount."<br>";
        // echo "================================<br>";

        $totalClosingAmount = $totalDebitAmount-$totalCreditAmount+$prevMonthOpening["opening_val"];

        //update previous month closing details
        $closingSql = 'UPDATE
                `'.$openingBalanceTable.'`
            SET
                `updated_by` = "Auto",
                `closing_val` = '.$totalClosingAmount.'
            WHERE
                `id` ='.$prevMonthOpening["id"];
        
        $openingSql = 'INSERT INTO
                `'.$openingBalanceTable.'`
            SET
                `company_id` = '.$prevMonthOpening["company_id"].',
                `branch_id` = '.$prevMonthOpening["branch_id"].',
                `location_id` = '.$prevMonthOpening["location_id"].',
                `created_by` = "Auto",
                `updated_by` = "Auto",
                `date` = "'.$curMonthFirstDate.'",
                `gl` = '.$prevMonthOpening["gl"].',
                `subgl` = "'.$prevMonthOpening["subgl"].'",
                `opening_val` = '.$totalClosingAmount;

        // print_r("\n::::::::::::::Opening:::::::::::\n");
        // print_r($closingSql);
        // print_r("\n::::::::::::::Closing:::::::::::\n");
        // print_r($openingSql);
        
        $updateObj = $dbObj->queryUpdate($closingSql);
        // print_r($updateObj);
        $openingObj = $dbObj->queryInsert($openingSql);
        // print_r($openingObj);
        
    }
    $actionObj = $dbObj->queryFinish();
    print_r([
        "status" => $actionObj["status"],
        "message" => $curMonthFirstDate." => ".$actionObj["message"]
    ]);
}else{
    print_r([
        "status" => "warning",
        "message" => "Today is not a valid day"
    ]);
}
echo "</pre>";

// $dbObj = new Database();

// $insObj=$dbObj->queryInsert("INSERT INTO `test` SET `name`='Opening Cron',`age`=25");

// print_r($insObj);
