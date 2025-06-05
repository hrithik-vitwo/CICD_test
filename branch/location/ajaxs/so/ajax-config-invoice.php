<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_GET['act'] === "handleConfigSave") {
    $configEmail = $_GET['configEmail'];
    $configPhone = $_GET['configPhone'];
    $configName = $_GET['configName'];


    $configSql = "INSERT INTO `erp_config_invoices` 
        SET 
            `company_id` = '$company_id', 
            `branch_id` = '$branch_id', 
            `location_id` = '$location_id', 
            `name` = '$configName', 
            `email` = '$configEmail', 
            `phone` = '$configPhone',
            `created_by` = '$created_by', 
            `updated_by` = '$updated_by'
            ";
    $configObj = queryInsert($configSql);

    echo json_encode($configObj);
?>

<?php
} elseif ($_GET['act'] === "getContact") {
    $configListObj = "SELECT * FROM `erp_config_invoices` WHERE `company_id` = $company_id AND `branch_id` = $branch_id AND `location_id` = $location_id";
    $configList = queryGet($configListObj, true);
    echo json_encode($configList);
} elseif ($_GET['act'] === "ss") {
    $price = 20;
    $qty = 5;
    echo $qty * $price;
}  else {
    echo "Something wrong, try again!";
}
?>