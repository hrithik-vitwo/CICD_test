<?php
require_once("api-common-func.php");

require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
$branchObj = new BranchSo();

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $order_type = $_POST['order_type'];
    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND `created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`order_code` like '%" . $_POST['keyword'] . "%' OR `created_at` like '%" . $_POST['keyword'] . "%')";
    }

    $sql_list = "SELECT * FROM `erp_party_order` WHERE `customer_id` = '" . $customer_id . "' AND order_type='$order_type' " . $cond . " ORDER BY `id` DESC LIMIT 0, 10";

    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        $data_array = [];
        foreach ($iv_data as $key => $data) {
            $sql_item_list = "SELECT `erp_inventory_items`.*, `erp_party_order_item`.`quantity`, true AS 'isClicked', summary.* FROM `erp_party_order_item` LEFT JOIN `erp_inventory_items` ON `erp_party_order_item`.`item_id` = `erp_inventory_items`.`itemId` LEFT JOIN `erp_inventory_stocks_summary` AS summary ON `erp_inventory_items`.`itemId` = summary.`itemId` WHERE `erp_party_order_item`.`order_id` = '" . $data['id'] . "' ORDER BY `erp_party_order_item`.`id` DESC";
            $qry_item = queryGet($sql_item_list, true);

            $data_array[$key] = array("ordermain" => $data);
            $data_array[$key]['ordermain']['items'] = $qry_item['data'];


            // mrp**************************************
            $customerSql = queryGet("SELECT * FROM `erp_customer` as cus LEFT JOIN `erp_customer_address` as caddress ON cus.customer_id = caddress.customer_id WHERE cus.`customer_id` = 79 AND caddress.customer_address_primary_flag = 1");
            $customer_state = $customerSql['data']['customer_address_state_code'] ?? 0;

            $terr = queryGet("SELECT * FROM erp_mrp_territory WHERE JSON_SEARCH(state_codes, 'one', '$customer_state') IS NOT NULL");
            $territory = $terr['data']['territory_id'];
            $mrp_group = $customerSql['data']['customer_mrp_group'];
            // mrp**************************************

            $customer_group = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = 79");
            $customerGroup = $customer_group['data']['customer_discount_group'];

            foreach ($data_array[$key]['ordermain']['items'] as &$product) {

                $dynamic_item_id = $product['itemId'];
                $itemDiscountGroupSql = "SELECT `discountGroup` FROM `erp_inventory_items` WHERE `itemId` = $dynamic_item_id";
                $itemDiscountGroupObj = queryGet($itemDiscountGroupSql);

                // uom **************
                $sqlUom = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId` = " . $product['baseUnitMeasure']);
                $product['uom'] = $sqlUom['data']['uomName'];
                // uom **************

                $itemGroup = json_decode($itemDiscountGroupObj['data']['discountGroup']);
                $itemGroupValues = "'" . implode("', '", $itemGroup) . "'";

                // $discount_query = "SELECT * FROM `erp_discount_variant_master` WHERE `valid_from` <= '" . date('Y-m-d') . "' AND `valid_upto` >= '" . date('Y-m-d') . "' AND `customer_discount_group_id` = '" . $customerGroup . "' AND `item_discount_group_id` IN (" . $itemGroupValues . ") AND `coupon` IS NULL AND `company_id` = " . $company_id . " AND `location_id` = " . $location_id;
                // $product_discounts = queryGet($discount_query, true);

                $check_discount = "SELECT * FROM `erp_discount_variant_master` WHERE (`term_of_payment` >= '" . $days . "' OR `term_of_payment` = 0) AND `valid_from` <= '" . date('Y-m-d') . "' AND `valid_upto` >= '" . date('Y-m-d') . "' AND `customer_discount_group_id` = '" . $customerGroup . "' AND `item_discount_group_id` IN ($itemGroupValues)  AND `company_id` = " . $company_id . " AND `location_id` = " . $location_id;
                $product_discounts = queryGet($check_discount, true);

                $product['stock'] = (int)$branchObj->itemQtyStockCheck($dynamic_item_id, "'rmWhOpen', 'fgWhOpen'")['sumOfBatches'];

                $product['discounts'] = $product_discounts['data'];

                // inventoryItemImages
                $sqlInventoryItemImages = $branchObj->inventoryItemImages($product['itemId']);
                $product['images'] = [];
                foreach ($sqlInventoryItemImages['data'] as $key => $value) {
                    $product['images'][$key] = COMP_STORAGE_URL . '/others/' . $value["image_name"];
                }

                // itemSpecification
                $sqlItemSpecification = $branchObj->itemSpecification($product['itemId']);
                $product['specifications'] = [];
                foreach ($sqlItemSpecification['data'] as $key => $value) {
                    $product['specifications'][$key] = $value;
                }

                // mrp ***************************
                $company_mrp_priority = 'territory';
                $mrp = 0;

                $dynamic_item_id = $product['itemId'];

                $discount_sql = queryGet("SELECT count(*) as count FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $dynamic_item_id AND (varient.customer_group = $mrp_group OR varient.territory = $territory) AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id");

                $count = (int)$discount_sql['data']['count'];

                if ($count > 0) {
                    if ($count > 1) {
                        if ($company_mrp_priority == 'territory') {
                            $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $dynamic_item_id AND  varient.territory = $territory AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id");

                            $product["mrp_value"] = (int)$mrp_sql['data']['mrp'];
                        } else {
                            $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $dynamic_item_id AND varient.customer_group = $mrp_group  AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id");

                            $product["mrp_value"] = (int)$mrp_sql['data']['mrp'];
                        }
                    } else {
                        $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $dynamic_item_id AND (varient.customer_group = $mrp_group OR varient.territory = $territory) AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id");

                        $product["mrp_value"] = (int)$mrp_sql['data']['mrp'];
                    }
                } else {
                    $product["mrp_value"] = (int)$product['itemPrice'];
                }
                unset($product['baseUnitMeasure']);
            }
        }

        sendApiResponse([
            "status" => "success",
            "message" => count($data_array) . " data found",
            "data" => $data_array,
            "customer_detail" => $authCustomer

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No not found",
            "data" => []

        ], 200);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
