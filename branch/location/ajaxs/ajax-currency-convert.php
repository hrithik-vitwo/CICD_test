<?php
include_once("../../../app/v1/connection-branch-admin.php");
include_once("../../../app/v1/functions/common/func-common.php");

if(isset($_GET["company_currency"]) && isset($_GET["selected_currency"]) && $_GET["company_currency"] != "" && $_GET["selected_currency"] != "")
{
    $company_currency = $_GET["company_currency"];
    $selected_currency = $_GET["selected_currency"];
    
    $currencyConverstionObj = currency_conversion($company_currency,$selected_currency);

    $currentConverstionRate = $currencyConverstionObj["quotes"][$company_currency . $selected_currency] ?? 1;

    echo json_encode($currentConverstionRate);
}

?>