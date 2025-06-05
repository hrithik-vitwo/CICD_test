<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-collect-payment.php");
$headerData = array('Content-Type: application/json');

$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();
$controller = new TemplateCollectPaymentController();
global $company_id;
global $branch_id;
global $location_id;

if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "modalData") {
    $paymentId = $_GET["paymentId"];
    $paymentSql = queryGet("SELECT type FROM erp_branch_sales_order_payments where payment_id = $paymentId");
    $paymentCollectortype = $paymentSql['data']['type'];

    if ($paymentCollectortype == 'customer') {
        $sql_payment = "SELECT sopayment.*,cust.customer_code as customer_code, cust.trade_name as customer_name ,cust.customer_currency,cust.customer_authorised_person_email as customer_email,custAddress.customer_address_building_no, custAddress.customer_address_flat_no, custAddress.customer_address_street_name, custAddress.customer_address_pin_code, custAddress.customer_address_location, custAddress.customer_address_district, custAddress.customer_address_state FROM `erp_branch_sales_order_payments` as sopayment LEFT JOIN erp_customer as cust on cust.customer_id=sopayment.customer_id LEFT JOIN `erp_customer_address` as custAddress ON sopayment.customer_id = custAddress.customer_address_id WHERE sopayment.payment_id=$paymentId AND sopayment.company_id='" . $company_id . "'  AND sopayment.branch_id='" . $branch_id . "'   AND sopayment.location_id='" . $location_id . "' " . $sts . " ORDER BY sopayment.payment_id DESC ";

        $sql_data = queryGet($sql_payment)['data'];

        $res = [
            "status" => true,
            "msg" => "Success",
            "data" => $sql_data,
            "name"=>$sql_data['customer_name'],
            "currencyName" => getSingleCurrencyType($sql_data['customer_currency']),
            "address" => getCustomerPrimaryAddressById($sql_data['customer_id']),
            "created_by" => getCreatedByUser($sql_data['created_by']),
            "created_at" => formatDateORDateTime($sql_data['created_at']),
            "updated_at" => formatDateORDateTime($sql_data['updated_at']),
            "updated_by" => getCreatedByUser($sql_data['updated_by']),
        ];
    }else{
        $sql_payment = "SELECT sopayment.*, 
        vend.vendor_id, 
        vend.trade_name AS vendor_name, 
        vend.vendor_code, 
        vend.vendor_authorised_person_email 
        FROM `erp_branch_sales_order_payments` AS sopayment 
        LEFT JOIN `erp_vendor_details` AS vend ON vend.vendor_id = sopayment.vendor_id 
        WHERE sopayment.payment_id=$paymentId  AND sopayment.company_id='" . $company_id . "' AND sopayment.branch_id='" . $branch_id . "'   
        AND sopayment.location_id='" . $location_id . "' " . $sts . " 
        ORDER BY sopayment.payment_id DESC ";
    $sql_data =queryGet($sql_payment)['data'];

   $res = [
            "status" => true,
            "msg" => "Success",
            "data" => $sql_data,
            "name"=>$sql_data['vendor_name'],
            "currencyName" => getSingleCurrencyType($sql_data['vendor_currency']),
            "address" => getVendorAddressById($sql_data['vendor_id']),
            "created_by" => getCreatedByUser($sql_data['created_by']),
            "created_at" => formatDateORDateTime($sql_data['created_at']),
            "updated_at" => formatDateORDateTime($sql_data['updated_at']),
            "updated_by" => getCreatedByUser($sql_data['updated_by']),
        ];
    }

    echo json_encode($res);
}
if ($_SERVER["REQUEST_METHOD"] == "GET" &&  $_GET["act"] == "classicView") {
    $paymentId = $_GET["paymentId"];
    $controller->printcollectpayment($paymentId);
    // echo $paymentId;
}
