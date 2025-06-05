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
$ItemsObj = new ItemsController();
$dbObj=new Database();
$templateSalesOrderControllerObj = new TemplateSalesOrderController();

if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "modalData") {

    $grnMulId = $_GET['grnMulId'];

    $sql_list = "SELECT * FROM `erp_grn_multiple` WHERE `company_id`='" . $company_id . "' AND `branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND status = '0' AND grn_mul_id='".$grnMulId."' ";
    $sqlMainQryObj = $dbObj->queryGet($sql_list);

    $data = $sqlMainQryObj['data'];
    $num_list = $sqlMainQryObj['numRows'];

    $dynamic_data = [];

    if ($num_list > 0) {
        $dynamic_data = [];


        $curName="";
        $currencyQuery = $dbObj->queryGet("SELECT currency_name FROM `erp_currency_type` WHERE currency_id='" . $company_currency . "'");
        if ($currencyQuery['numRows'] > 0) {
            $curName = $currencyQuery['data']['currency_name'];
        } else {
            $curName = "N/A";
        }

        $grn=unserialize($data['grn_read_json']);
        $grnData=[];
        if($grn['status']=='success'){
            $grnData=$grn['data'];
        }
        // if($grnData==null)
        // exit();

        $dynamic_data = [
            "dataObj" => $data,
            "currecyNameWords" => number_to_words_indian_rupees($data['total_amt']),
            "created_by" => getCreatedByUser($data['created_by']),
            "created_at" => formatDateORDateTime($data['created_at']),
            "updated_by" => getCreatedByUser($data['updated_by']),
            "updated_at" => formatDateORDateTime($data['updated_at']),
            "companyCurrency" => $curName,
            "grnData"=>$grnData
        ];

        $res = [
            "status" => true,
            "msg" => "Success",
            "sql" => $sqlMainQryObj['query'],
            "data" => $dynamic_data
        ];
    } else {
        $res = [
            "status" => false,
            "msg" => "Error!",
            "sql" => $sqlMainQryObj['query']
        ];
    }

    echo json_encode($res);
} 
if($_SERVER["REQUEST_METHOD"] == "GET" && $_GET["act"]=="changeStatus") {    
    $id = $_GET['grnMulId'];
    $res = $dbObj->queryUpdate('UPDATE `erp_grn_multiple` SET `grn_active_status`="deactive" WHERE `grn_mul_id`="' . $id . '"');
    echo json_encode($res);
}
