<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];


if ($_GET['act'] === "address") {

    $bill_id = $_GET['bill_id'];
    $attr = $_GET['attr'];
    if ($attr == 'inv') {
        $inv_sql = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id` = $bill_id");
        $inv = queryGet("SELECT * FROM `erp_branch_sales_order_invoice_items` WHERE `so_invoice_id` = $bill_id", true);
        // console($inv_sql);
        // $total = 0;
        $responseData['bill_address_id'] = $inv_sql['data']['billing_address_id'];

        $responseData['shipping_address_id'] = $inv_sql['data']['shipping_address_id'];

        $responseData['bill_address'] = $inv_sql['data']['customer_billing_address'];

        $responseData['shipping_address'] = $inv_sql['data']['customer_shipping_address'];

        $responseData['bill_type'] = $inv_sql['data']['type'];
        $responseData['posting_date'] = $inv_sql['data']['invoice_date'];


        echo json_encode($responseData);

?>
 
        

     
    <?php
    } else {
        $inv_sql = queryGet("SELECT * FROM `erp_grninvoice` WHERE `grnIvId` = $bill_id");
        $location_address = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id` = $location_id ");

        $address = $location_address['data']['othersLocation_name'] . ',' . $location_address['data']['othersLocation_building_no'] . ',' . $location_address['data']['othersLocation_flat_no'] . ',' . $location_address['data']['othersLocation_street_name'] . ',' . $location_address['data']['othersLocation_pin_code'] . ',' . $location_address['data']['othersLocation_location'] . ',' . $location_address['data']['othersLocation_city'] . ',' . $location_address['data']['othersLocation_district'] . ',' . $location_address['data']['othersLocation_state'];


        $responseData['bill_address_id'] = $location_id;

        $responseData['shipping_address_id'] =  $location_id;

        $responseData['bill_address'] = $address;

        $responseData['shipping_address'] = $address;

        $responseData['bill_type'] = $inv_sql['data']['grnType'];
        $responseData['posting_date'] = $inv_sql['data']['postingDate'];

        echo json_encode($responseData);
    }
}
    ?>