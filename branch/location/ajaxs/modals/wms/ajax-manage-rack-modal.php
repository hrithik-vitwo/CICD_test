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

    $rack_id  = $_GET['rack_id'];
    $cond = "AND rack_id  ='" . $rack_id  . "'";
    $sts = " AND `status` !='deleted'";


    // $sql_list = "SELECT rack.*, STORAGE.storage_location_name, warehouse.warehouse_name FROM `erp_rack` AS rack LEFT JOIN `erp_storage_location` AS STORAGE ON STORAGE .storage_location_id = rack.storage_location_id LEFT JOIN `erp_storage_warehouse` AS warehouse ON warehouse.warehouse_id = STORAGE.warehouse_id WHERE rack.`company_id` = $company_id AND rack.`branch_id` = $branch_id AND rack.`location_id` = $location_id ORDER BY rack.rack_id DESC";
   $sql_list = "SELECT rack.*, STORAGE.storage_location_id, STORAGE.storage_location_name, warehouse.warehouse_id, warehouse.warehouse_name FROM erp_rack AS rack LEFT JOIN erp_storage_location AS STORAGE ON STORAGE .storage_location_id = rack.storage_location_id LEFT JOIN erp_storage_warehouse AS warehouse ON warehouse.warehouse_id = STORAGE.warehouse_id WHERE rack.company_id = $company_id AND rack.branch_id = $branch_id AND rack.location_id = $location_id AND rack.rack_id = $rack_id ORDER BY rack.rack_id DESC";
    $sqlMainQryObj = queryGet($sql_list);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];
        $dynamic_data = [
            "rack_id" => $data['rack_id'],
            "rack_name" => $data['rack_name'],
            "storage_location_name" => $data['storage_location_name'],
            "rack_description" => $data['rack_description'],
            "warehouse_name" => $data['warehouse_name'],
            // "storage_control" => $data['storage_control'],
            // "temp_control" => $data['temp_control'],
            "created_at" => formatDateORDateTime($data['created_at']),
            "updated_at" => formatDateORDateTime($data['updated_at']),
            "createdBy" => getCreatedByUser($data['created_by']),
            "updated_by" => getCreatedByUser($data['updated_by']),
            "rack_code" =>$data['rack_code']
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
