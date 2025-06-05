<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authCustomer = authCustomerApiRequest();
    $company_id = $authCustomer['company_id'];

    $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE company_id='$company_id' ORDER BY so_id DESC LIMIT 1";
    $lastSoNo = queryGet($sql);

    if (isset($lastSoNo['data'])) {
        $lastSoNo = $lastSoNo['data']['so_number'] ?? 0;
    } else {
        $lastSoNo = '';
    }

    $returnSoNo = getSoSerialNumber($lastSoNo);
    $requestBody = requestBody();
    $soDetails = $requestBody['soDetails'];
    $customer_id = $soDetails['customer_id'];
    $branch_id = $soDetails['branch_id'];
    $location_id = $soDetails['location_id'];

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
        `claimz_id`='" . $soDetails['user_id'] . "',
        `goodsType`='" . $soDetails['goodsType'] . "',
        `approvalStatus`='" . $soDetails['approvalStatus'] . "',
        `customer_po_no`='" . $soDetails['customer_po_no'] . "',
        `totalItems`='" . $soDetails['totalItems'] . "',
        `totalDiscount`='" . $soDetails['totalDiscount'] . "',
        `totalAmount`='" . $soDetails['totalAmount'] . "',
        `created_by`='" . $soDetails['created_by'] . "',
        `updated_by`='" . $soDetails['updated_by'] . "',
        `soStatus`='open'");

    if ($insSo['status'] == "success") {
        $data_array = [];
        $items = $requestBody['itemDetails'];
        $soLastId = $insSo['insertedId'];

        $totalDiscount = 0;
        $totalAmount = 0;
        $totalItems = count($items);

        foreach ($items as $key => $item) {
            $sqll = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` SET 
                `so_id`='" . $soLastId . "',
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
            $itemLastId = $insItems['insertedId'];

            $tot = (($item['unitPrice'] * $item['qty']) - $item['itemTotalDiscount1']) + $item['itemTotalTax1'];
            $totalDiscount += str_replace(',', '', $item['itemTotalDiscount1']);
            $totalAmount = str_replace(',', '', $totalAmount) + str_replace(',', '', $tot);

            if ($insItems['status'] == "success") {
                $scheduleSql = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_DELIVERY_SCHEDULE . "` 
                    SET 
                        `so_item_id`='" . $itemLastId . "',
                        `delivery_date`='" . $soDetails['delivery_date'] . "',
                        `deliveryStatus`='open',
                        `qty`='" . $item['qty'] . "',
                        `remainingQty`='" . $item['qty'] . "'
                ";
                $schedule = queryInsert($scheduleSql);
            }
        }

        // update sales order
        $updateSalesOrder = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` 
                SET 
                    `totalItems`='" . $totalItems . "',
                    `totalDiscount`='" . $totalDiscount . "',
                    `totalAmount`='" . $totalAmount . "' WHERE so_id=" . $soLastId . "
            ";
        queryUpdate($updateSalesOrder);

        sendApiResponse([
            "status" => $insSo['status'],
            "message" => $insSo['message'],
            "lastInsertedId" => $insSo['insertedId']
        ], 200);
    } else {
        sendApiResponse([
            "status" => $insSo['status'],
            "message" => $insSo['message']
        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
