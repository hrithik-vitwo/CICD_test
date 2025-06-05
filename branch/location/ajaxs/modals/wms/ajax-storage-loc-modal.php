<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-sales-order.controller.php");
$headerData = array('Content-Type: application/json');

$BranchSoObj = new BranchSo();
$templateSalesOrderControllerObj = new TemplateSalesOrderController();
$companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];

$companyCurrency = $companyDetailsObj['company_currency'];
$companyCurrencyName = $BranchSoObj->fetchCurrencyIcon($companyCurrency)['data']['currency_name'];


if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $storage_location_id  = $_GET['storage_location_id'];
    $cond = "AND storage_location_id  ='" . $storage_location_id  . "'";
    $sts = " AND `status` !='deleted'";

    $sql_list = "SELECT sl.*, erpWarehouse.warehouse_name FROM `erp_storage_location` AS sl LEFT JOIN erp_storage_warehouse AS erpWarehouse ON erpWarehouse.warehouse_id = sl.warehouse_id WHERE 1 AND sl.`company_id` = $company_id AND sl.`branch_id` =$branch_id  AND sl.`location_id` = $location_id ". $cond . " ORDER BY sl.storage_location_id DESC";
    $sqlMainQryObj = queryGet($sql_list);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];
        $dynamic_data = [
            "storage_location_id" => $data['storage_location_id'],
            "storage_location_name" => $data['storage_location_name'],
            "storage_location_code" => $data['storage_location_code'],
            "warehouse_name" => $data['warehouse_name'],
            "storage_control" => $data['storage_control'],
            "temp_control" => $data['temp_control'],
            "created_at" => formatDateORDateTime($data['created_at']),
            "updated_at" => formatDateORDateTime($data['updated_at']),
            "createdBy" => getCreatedByUser($data['created_by']),
            "updated_by" => getCreatedByUser($data['updated_by'])

        ];


        $res = [
            "status" => true,
            "msg" => "Success",
            // "data" => $dynamic_data,
            "data" => $dynamic_data
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sqlMainQryObj
        ];
    }

    echo json_encode($res);
}
