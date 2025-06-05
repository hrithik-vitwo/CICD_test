<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$return = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $customer_group = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = '" . $_GET['customer_id'] . "'");

    $item_group = queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemId` = '" . $_GET['item_id'] . "'");
    
    $return['customer_group'] = $customer_group['data']['customer_discount_group'];
    $return['item_group'] = $item_group['data']['discountGroup'];
    $item_group = json_decode($return['item_group']);
    $return['days'] = $_GET['days'];
    $return['today'] = date('Y-m-d');
    $return['qty'] = $_GET['qty'];
    $return['value'] = $_GET['value'];
    $company_id = 1;
    $location_id = 1;

    $item_group_values = "'" . implode("', '", $item_group) . "'";

    $check_discount = queryGet("SELECT * FROM `erp_discount_variant_master` WHERE (`term_of_payment` >= '" . $return['days'] . "' OR `term_of_payment` = 0) AND `valid_from` <= '" . $return['today'] . "' AND `valid_upto` >= '" . $return['today'] . "' AND `customer_discount_group_id` = '" . $return['customer_group'] . "' AND `item_discount_group_id` IN ($item_group_values)  AND `company_id` = " . $company_id . " AND `location_id` = " . $location_id, true);

    $arr = [];
    foreach ($check_discount['data'] as $data) {
        if ($data['minimum_value'] != 0 && $data['minimum_qty'] != 0) {
            if ($data['condition'] == 'AND') {
                if ($return['qty'] >= $data['minimum_qty'] && $return['value'] >= $data['minimum_value']) {

                    $arr[] = $data;
                }
            } else {

                if ($return['qty'] >= $data['minimum_qty'] || $return['value'] >= $data['minimum_value']) {
                    $arr[] = $data;
                }
                
            }
        } elseif ($data['minimum_value'] != 0 && $data['minimum_qty'] == 0) {

            $arr[] = $data;
        } elseif ($data['minimum_value'] == 0 && $data['minimum_qty'] != 0) {

            $arr[] = $data;
        } elseif ($data['minimum_value'] == 0 && $data['minimum_qty'] == 0) {

            $arr[] = $data;
        }
    }

    echo json_encode($arr);
}
