<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='$company_id' ORDER BY so_id DESC LIMIT 1";
    $lastSoNo = queryGet($sql);

    if (isset($lastSoNo['data'])) {
        $lastSoNo = $lastSoNo['data']['so_number'] ?? 0;
    } else {
        $lastSoNo = '';
    }
    $returnSoNo = getSoSerialNumber($lastSoNo);

    $data = requestBody();

    $soDetails = $data['soDetails'];

    $insSo = queryInsert("INSERT INTO `" . ERP_BRANCH_SALES_ORDER . "` SET 
        `so_number`='$returnSoNo',
        `customer_id`='$customer_id',
        `company_id`='$company_id',
        `branch_id`='$branch_id',
        `location_id`='$location_id',
        `so_date`='" . $soDetails['so_date'] . "',
        `soPostingTime`='" . $soDetails['so_posting_time'] . "',
        `delivery_date`='" . $soDetails['delivery_date'] . "',
        `billingAddress`='" . $soDetails['billing_address'] . "',
        `shippingAddress`='" . $soDetails['shipping_address'] . "',
        `profit_center`='" . $soDetails['profit_center'] . "',
        `credit_period`='" . $soDetails['credit_period'] . "',
        `kamId`='" . $soDetails['kamId'] . "',
        `goodsType`='" . $soDetails['goodsType'] . "',
        `approvalStatus`='" . $soDetails['approvalStatus'] . "',
        `customer_po_no`='" . $soDetails['customer_po_no'] . "',
        `totalItems`='" . $soDetails['totalItems'] . "',
        `totalDiscount`='" . $soDetails['totalDiscount'] . "',
        `totalAmount`='" . $soDetails['totalAmount'] . "',
        `created_by`=0,
        `updated_by`=0,
        `soStatus`='open'");

    if ($insSo['status'] == "success") {
        $data_array = [];
        $items = $data['itemDetails'];

        foreach ($items as $key => $item) {
            $sqll = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` SET 
                `so_id`='" . $insSo['insertedId'] . "',
                `lineNo`='" . $item['lineNo'] . "',
                `inventory_item_id`='" . $item['inventory_item_id'] . "',
                `goodsType`='" . $item['goodsType'] . "',
                `itemCode`='" . $item['itemCode'] . "',
                `itemName`='" . $item['itemName'] . "',
                `itemDesc`='" . $item['itemDesc'] . "',
                `hsnCode`='" . $item['hsnCode'] . "',
                `unitPrice`='" . $item['unitPrice'] . "',
                `totalDiscount`='" . $item['totalDiscount'] . "',
                `itemTotalDiscount`='" . $item['itemTotalDiscount1'] . "',
                `tax`='" . $item['tax'] . "',
                `totalTax`='" . $item['itemTotalTax1'] . "',
                `totalPrice`='" . $item['totalPrice'] . "',
                `tolerance`='" . $item['tolerance'] . "',
                `qty`='" . $item['qty'] . "',
                `uom`='" . $item['uom'] . "'";
            $insItems = queryInsert($sqll);

            $scheduleSql = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` SET 
                `so_item_id`='" . $insItems['insertedId'] . "',
                `delivery_date`='" . $soDetails['delivery_date'] . "',
                `deliveryStatus`='open',
                `qty`='" . $item['qty'] . "'";
            $schedule = queryInsert($scheduleSql);
        }

        sendApiResponse([
            "status" => "success",
            "message" => "Inserted Successfully",
            "data" => $data_array,
            "sql" => $insSo,
            "sqlItem" => $insItems,
            "sqllItem" => $sqll,
            "items" => $_POST,
        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Something went wrong!",
            "data2" => $insSo,
            "sqlItem" => $insItems,
            "sqlItema" => $items,
            "data" => []
        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
