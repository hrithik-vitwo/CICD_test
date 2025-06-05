<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $trade_name = $authCustomer['trade_name'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];
    $orederNo = 'ORDER' . time();

    $data = requestBody();

    $sql_list = "SELECT * FROM `erp_party_order` WHERE `order_code`='" . $orederNo . "'";
    $qry_list = queryGet($sql_list);
    if ($qry_list['status'] != "success") {

        $sqll = "INSERT INTO `erp_party_order` SET 
            `company_id`='" . $company_id . "', 
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `customer_id`='" . $customer_id . "',
            `order_code`='" . $orederNo . "',
            `order_type`='" . $data['order_type'] . "',
            `created_by`='" . $trade_name . "',
            `updated_by`='" . $trade_name . "'";
        $insOrder = queryInsert($sqll);

        if ($insOrder['status'] == "success") {
            $order_id = $insOrder['insertedId'];
            $items = $data['itemDetails'];

            $currentDate = date("Y-m-d");
            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = 'erp_party_order';
            $auditTrail['basicDetail']['column_name'] = 'id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $order_id;  // primary key
            $auditTrail['basicDetail']['party_type'] = 'customer';
            $auditTrail['basicDetail']['party_id'] = $customer_id;
            $auditTrail['basicDetail']['document_number'] = $orederNo;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Customer order';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($sqll);
            $auditTrail['basicDetail']['company_id'] = $company_id;
            $auditTrail['basicDetail']['branch_id'] = $branch_id;
            $auditTrail['basicDetail']['location_id'] =$location_id;
            $auditTrail['basicDetail']['updated_by'] = $trade_name;
            $auditTrail['basicDetail']['created_by'] = $trade_name;
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = '';

            $auditTrail['action_data']['Order Details']['Order No'] = $orederNo;
            $auditTrail['action_data']['Order Details']['Order Type'] = $data['order_type'];
            $auditTrail['action_data']['Order Details']['Order Date'] = formatDateWeb($currentDate);

            foreach ($items as $key => $item) {
                $itemId=$item['item_id'];
                $itemDetails=queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemId` = $itemId AND `company_id` = $company_id")['data'];
                $itemCode=$itemDetails['itemCode'];
                $sqlitem = "INSERT INTO `erp_party_order_item` SET 
                `order_id`='" . $order_id . "',
                `item_id`='" . $item['item_id'] . "',
                `created_by`='" . $trade_name . "',
                `updated_by`='" . $trade_name . "',
                `quantity`='" . $item['quantity'] . "'";
                $insItems = queryInsert($sqlitem);

                $auditTrail['action_data']['Order Item Details'][$itemCode]['ItemCode'] = $itemDetails['itemCode'];
                $auditTrail['action_data']['Order Item Details'][$itemCode]['itemName'] = $itemDetails['itemName'];
                $auditTrail['action_data']['Order Item Details'][$itemCode]['HsnCode'] = $itemDetails['hsnCode'];
                $auditTrail['action_data']['Order Item Details'][$itemCode]['Quantity'] = decimalQuantityPreview($item['quantity']);
            }
            $auditTrailreturn = generateAuditTrailByMail($auditTrail);
            sendApiResponse([
                "status" => "success",
                "message" => "Inserted Successfully",
                "insItems" => $insItems
            ], 200);
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Something went wrong!",
                "insItems" => $insItems
            ], 400);
        }
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
