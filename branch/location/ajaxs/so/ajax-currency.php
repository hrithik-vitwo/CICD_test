<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_GET['act'] === "currencyPage") {  
    $currency = $_GET['currencyName'];
    $companyCurrencyName = $_GET['currency'];
    // echo json_encode(currency_conversion($companyCurrencyName, $currency));
    $currencyObj = currency_conversion($companyCurrencyName, $currency);
    $currencyRate = 0;
    foreach($currencyObj["quotes"]??[] as $rate){
        $currencyRate = $rate;
    }

    echo json_encode([
        "status"=>"success",
        "message"=> "success",
        "data" =>[
            "rate" => $currencyRate
        ]
    ]);
?>
<?php
} else {
    echo "Something wrong, try again!";
}
?>