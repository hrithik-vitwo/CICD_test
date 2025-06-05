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

    $warehouse_id  = $_GET['warehouse_id'];
    $cond = "AND warehouse_id  ='" . $warehouse_id  . "'";
    $sts = " AND `status` !='deleted'";
              

    $sql_list = "SELECT * FROM " . ERP_WAREHOUSE . " WHERE 1 AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY warehouse_id desc";
                 
    $sqlMainQryObj = queryGet($sql_list);
    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];
            $dynamic_data = [
                "warehouse_name"=>$data['warehouse_name'],
                "warehouse_code"=>$data['warehouse_code'],
                "warehouse_address"=>$data['warehouse_address'],
                "warehouse_description"=>$data['warehouse_description'],
                "warehouse_lat"=>$data['warehouse_lat'],
                "warehouse_lng"=>$data['warehouse_lng'],
                "created_at"=>formatDateORDateTime($data['created_at']),
                "updated_at"=>formatDateORDateTime($data['updated_at']),
                "createdBy"=>getCreatedByUser($data['created_by']),
                "updated_by"=>getCreatedByUser($data['updated_by'])

            ];
        

        $res = [
            "status" => true,
            "msg" => "Success",
            // "data" => $dynamic_data,
            "data"=>$dynamic_data
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
