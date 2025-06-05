<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");
$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];
$currency_name = $companyCurrencyData['currency_name'];
if (isset($_GET['gl'])) {
    //echo 1;
    $warning[] = '';
    $gl = $_GET['gl'];
    // $check_gl = queryGet("SELECT * FROM `erp_opening_closing_balance` WHERE `gl`=$gl AND `subgl`='' AND  `company_id`=$company_id AND `location_id`=$location_id AND `branch_id`=$branch_id");
    $check_gl = queryGet('SELECT SUM(CASE WHEN `subgl` = "" THEN `opening_val` ELSE 0 END) AS totalUndefinedBalance, SUM(`opening_val`) AS totalOpeningBalance FROM `erp_opening_closing_balance` WHERE `gl`='.$gl.' AND `company_id`='.$company_id.' AND `location_id`='.$location_id.' AND `branch_id`='.$branch_id.' AND DATE_FORMAT(`date`, "%Y-%m") = DATE_FORMAT("'.$compOpeningDate.'", "%Y-%m")');
    //  console($check_gl); 
    if ($check_gl['numRows'] > 0) {
        $warning['text'] .= "Total opening balace: ".$currency_name." " . decimalValuePreview($check_gl['data']['totalOpeningBalance'])." & total difference: ".$currency_name." " . decimalValuePreview($check_gl['data']['totalUndefinedBalance']);
        $warning['amount'] = $check_gl['data']['totalOpeningBalance'];
    } else {
        $warning['text'] .= "".$currency_name." " . decimalValuePreview(0)." is available in opening";
        $warning['amount'] = '';
    }

    // echo json_encode($check_gl);
    echo json_encode($warning);

} else if (isset($_GET['subgl'])) {

    $warning[] = '';
    $subgl = $_GET['subgl'];


    $check_subgl = queryGet("SELECT * FROM `erp_opening_closing_balance` WHERE `subgl`='".$subgl."'  AND  `company_id`=$company_id  AND `location_id`=$location_id AND `branch_id`=$branch_id");
    // console($check_subgl);
    $gl = $check_subgl['data']['gl'];
    $substr =  substr($subgl, 0, 2);
    if ($substr == 11 || $substr == 22  || $substr == 32  || $substr == 44  || $substr == 19 || $substr == 12) {

        $mwp_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary`as sum LEFT JOIN `erp_inventory_items` as items ON items.`itemId` = sum.`itemId` WHERE items.`itemCode`='".$subgl."'  AND  sum.`company_id`=$company_id  AND sum.`location_id`=$location_id AND sum.`branch_id`=$branch_id");
        $mwp = $mwp_sql['data']['movingWeightedPrice'];
        $qty = $mwp_sql['data']['itemTotalQty'];

        //    exit(); 
        $warning['mwp'] = $mwp * $qty;
        $warning['mwp_status'] = 1;
        $warning['qty'] = $qty;
    } else {

        $warning['mwp'] = 0;
        $warning['mwp_status'] = 0;
    }

    $check_gl = queryGet("SELECT * FROM `erp_opening_closing_balance` WHERE `gl`=$gl AND `subgl`='' AND  `company_id`=$company_id");

    if ($check_gl['numRows'] > 0) {
        $warning['gl_amount'] = $check_gl['data']['gl'];
    } else {
        $warning['gl_amount'] = 0;
    }

    if ($check_subgl['numRows'] > 0) {
        $warning['text'] .= ' '.$currency_name.' ' . decimalValuePreview($check_subgl['data']['opening_val']) . ' is available in opening';
        $warning['amount'] .= $check_subgl['data']['opening_val'];
    } else {
        $warning['text'] .= ' '.$currency_name.' ' . decimalValuePreview(0).' is available in opening';
        $warning['amount'] .= "";
    }
    echo json_encode($warning);
} else {
}
