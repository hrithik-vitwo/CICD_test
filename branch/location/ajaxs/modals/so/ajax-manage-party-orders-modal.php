<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");

$headerData = array('Content-Type: application/json');

$dbObj = new Database();
$branchSoObj=new BranchSo();
$ItemsObj = new ItemsController();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($_GET["act"] == "modalData") {
        $party_order_id = $_GET['id'];

        $sql_list = "SELECT partyOrder.*, cust.trade_name, cust.customer_code, cust.customer_gstin,cust.customer_pan,cust.customer_currency, cust.customer_status, custAddress.customer_address_building_no, custAddress.customer_address_flat_no, custAddress.customer_address_street_name, custAddress.customer_address_pin_code, custAddress.customer_address_location, custAddress.customer_address_district, custAddress.customer_address_state, cust.customer_authorised_person_email, cust.customer_authorised_person_phone FROM `erp_party_order` AS partyOrder LEFT JOIN erp_customer AS cust ON partyOrder.customer_id = cust.customer_id LEFT JOIN `erp_customer_address` AS custAddress ON partyOrder.customer_id = custAddress.customer_address_id  WHERE id=$party_order_id AND partyOrder.company_id=$company_id AND partyOrder.branch_id=$branch_id  AND partyOrder.location_id=$location_id";

        $query = $dbObj->queryGet($sql_list);
        if ($query['numRows'] > 0) {

            $data = $query['data'];
            $itemDetails = $branchSoObj->fetchParyOrderItems($data['id'])['data'];

            $sql_party_item = "SELECT * FROM `erp_party_order_item` WHERE order_id ='" . $data['id'] . "';";
            $sql_party = $dbObj->queryGet($sql_party_item, true);

            $items = [];
            foreach ($sql_party['data'] as $oneOrderItem) {

                $itemId = $oneOrderItem['item_id'];
                $itemData = $ItemsObj->getItemById($itemId)['data'];

                $items[] = [
                    "itemCode" => $itemData['itemCode'],
                    "itemName" => $itemData['itemName'],
                    "qty" => decimalQuantityPreview($oneOrderItem['quantity']),
                    "hsnCode" => $itemData['hsnCode']
                ];
            }



            $currencyQuery = $dbObj->queryGet("SELECT currency_name FROM `erp_currency_type` WHERE currency_id='" . $data['customer_currency'] . "'");
            if ($currencyQuery['numRows'] > 0) {
                $curName = $currencyQuery['data']['currency_name'];
            } else {
                $curName = "N/A";
            }

            $customerAddress = $data['customer_address_building_no'] . ', ' . $data['customer_address_flat_no'] . ', ' . $data['customer_address_street_name'] . ', ' . $data['customer_address_pin_code'] . ', ' . $data['customer_address_location'] . ', ' . $data['customer_address_district'] . ', ' . $data['customer_address_state'];


            $dynamic_data = [
                "dataObj" => $data,
                "companyCurrencyName" => $curName,
                "customerAddress" => $customerAddress,
                "items" => $items,
            ];

            $res = [
                "status" => true,
                "msg" => "success",
                "data" => $dynamic_data,
                "sql" => $sql_list
            ];
        } else {
            $res = [
                "status" => false,
                "msg" => "Error!",
                "sql" => $sql_list
            ];
        }
        echo json_encode($res);
    }
}
