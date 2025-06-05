<?php

require_once("../company/api-common-func.php");
require_once("../../../app/v1/connection-branch-admin.php");

$dbObj = new Database();

$check_select_sql = "SELECT * FROM erp_grn_payments WHERE company_id = $company_id AND type IS NULL";

$check_select_result = $dbObj->queryGet($check_select_sql, true)['data'];

$count_customer = 0;
$count_vendor = 0;

foreach ($check_select_result as $data) {
    if ($data['customer_id'] != '' && $data['customer_id'] != 0 && $data['customer_id'] != NULL) {
        $customer_id = $data['customer_id'];
        $payment_id = $data['payment_id'];

        $count_customer++;
        // $customer_type_res = $dbObj->queryUpdate("UPDATE `erp_grn_payments` 
        //                       SET `type` = 'customer'  
        //                       WHERE payment_id=$payment_id");

        // if ($customer_type_res['status'] != "success") {
        //     $dbObj->queryRollBack();
        // }
    }

    if ($data['vendor_id'] != '' && $data['vendor_id'] != 0 && $data['vendor_id']  != NULL) {
        $vendor_id = $data['vendor_id'];
        $payment_id = $data['payment_id'];
        $count_vendor++;
        // $vendor_type_res = queryUpdate("UPDATE `erp_grn_payments` 
        //                     SET `type` = 'vendor'  
        //                     WHERE payment_id=$payment_id");
        // if ($vendor_type_res['status'] != "success") {
        //     $dbObj->queryRollBack();
        // }
    }
}


echo "<br>";

echo"Customer count is : " . $count_customer;

echo "<br>";

echo"Vendor count is : " . $count_vendor;