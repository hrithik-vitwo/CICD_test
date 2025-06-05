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

    $grnIvId = $_GET['grnIvId'];

    $cond="";
    $sts = " AND grniv.`grnStatus`!='deleted'";
    $sql_list = "SELECT grniv.*, grn.`grnCreatedAt` AS grnDate, grn.`po_date` AS poDate, vendorDetail.vendor_code, vendorDetail.trade_name, vendorDetail.vendor_gstin, vendorDetail.vendor_authorised_person_email, vendorDetail.vendor_authorised_person_phone,vendorDetail.vendor_pan, func.functionalities_name ,(SELECT label FROM `erp_status_master` WHERE status_id=grniv.paymentStatus ) as paymentStatusName FROM `" . ERP_GRNINVOICE . "` AS grniv LEFT JOIN `erp_grn` AS grn ON grn.`grnId` = grniv.`grnId` LEFT JOIN `erp_vendor_details` AS vendorDetail ON vendorDetail.vendor_id = grniv.vendorId LEFT JOIN erp_company_functionalities as func ON grn.functional_area = func.functionalities_id WHERE 1 " . $cond . " AND grniv.grnIvId=$grnIvId AND grniv.`companyId`='$company_id' AND grniv.`branchId`='$branch_id' AND grniv.`locationId`='$location_id' " . $sts . "";

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


        $itemSql="SELECT * FROM `erp_grninvoice_goods` WHERE grnIvId=$grnIvId;";
        $itemQuery=$dbObj->queryGet($itemSql,true);
        $itemData=[];

        if($itemQuery['numRows']>0){
            $itemData=$itemQuery['data'];
        }


        $dynamic_data = [
            "dataObj" => $data,
            "items" =>$itemData,
            "currecyNameWords" => number_to_words_indian_rupees($data['grnTotalAmount']),
            "created_by" => getCreatedByUser($data['grnCreatedBy']),
            "created_at" => formatDateORDateTime($data['grnCreatedAt']),
            "updated_by" => getCreatedByUser($data['grnUpdatedBy']),
            "updated_at" => formatDateORDateTime($data['grnUpdatedAt']),
            "companyCurrency" => $curName,
            // "countryCode" => $_SESSION['logedBranchAdminInfo']['companyCountry']
            "countryCode" => $companyCountry,
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