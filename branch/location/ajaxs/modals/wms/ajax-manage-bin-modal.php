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

    $bin_id  = $_GET['bin_id'];
    $cond = "AND bin_id  ='" . $bin_id  . "'";
    $sts = " AND `status` !='deleted'";


    // $sql_list = "SELECT rack.*, erpStorage.storage_location_name FROM `erp_rack` AS rack LEFT JOIN erp_storage_location AS erpStorage ON erpStorage.storage_location_id = rack.storage_location_id WHERE 1 AND rack.`company_id` = $company_id AND rack.`branch_id` =$branch_id  AND rack.`location_id` = $location_id ". $cond . " ORDER BY rack.rack_id DESC";
    // $sql_list = "SELECT bin.*, erpLayer.layer_name FROM `erp_storage_bin` AS bin LEFT JOIN erp_layer AS erpLayer ON erpLayer.layer_id = bin.layer_id WHERE 1 AND bin.`company_id` = $company_id AND bin.`branch_id` =$branch_id  AND bin.`location_id` = $location_id ". $cond . " ORDER BY bin.bin_id DESC";
    $sql_list = "SELECT
    bin.*,
    layer.layer_id AS layer_id,
    layer.layer_name AS layer_name,
    rack.rack_id AS rack_id,
    rack.rack_name AS rack_name,
    STORAGE.storage_location_id AS storage_location_id,
    STORAGE.storage_location_name AS storage_location_name,
    warehouse.warehouse_id AS warehouse_id,
    warehouse.warehouse_name AS warehouse_name
FROM
    erp_storage_bin AS bin
LEFT JOIN erp_layer AS layer
    ON bin.layer_id = layer.layer_id
LEFT JOIN erp_rack AS rack
    ON layer.rack_id = rack.rack_id
LEFT JOIN erp_storage_location AS STORAGE
    ON rack.storage_location_id = STORAGE.storage_location_id
LEFT JOIN erp_storage_warehouse AS warehouse
    ON STORAGE.warehouse_id = warehouse.warehouse_id
WHERE
    1 $cond
    AND layer.company_id = $company_id
    AND layer.branch_id = $branch_id
    AND layer.location_id = $location_id
ORDER BY
    bin.bin_id DESC
";
  $sqlMainQryObj = queryGet($sql_list);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];
        $dynamic_data = [
        "bin_id"=>$data['bin_id'],
        "bin_name" => $data['bin_name'],
        "bin_code" => $data['bin_code'],
        "layer_name" => $data['layer_name'],
        "max_temperature" => $data['max_temperature'],
        "min_temperature" => $data['min_temperature'],
        "warehouse_name" => $data['warehouse_name'],
        "storage_location_name" => $data['storage_location_name'],
        "rack_name" => $data['rack_name'],
        "layer_name" => $data['layer_name'],
        "createdBy" => getCreatedByUser($data['created_by']),
        "updated_by" => getCreatedByUser($data['updated_by']),
        "created_at"=> formatDateORDateTime($data['created_at']),
        "updated_at"=> formatDateORDateTime($data['updated_at'])
        // "updated_by" => getCreatedByUser($data['updated_by'])

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
