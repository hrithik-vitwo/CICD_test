<?php
require_once("api-common-func.php");

require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
$branchObj = new BranchSo();

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'] ?? 0;
    $company_id = $authCustomer['company_id'] ?? 0;
    $branch_id = $authCustomer['branch_id'] ?? 0;
    $location_id = $authCustomer['location_id'] ?? 0;

    define("BASE_URL", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/");
    define("BUCKET_URL", BASE_URL);
    define("COMP_STORAGE_URL", BUCKET_URL."uploads/$company_id");

    $itemId = $_POST['itemId'] ?? 0;

    // $sql_list = "SELECT `itemId`, `company_id`, `branch`, `location_id`, `parentGlId`, `itemCode`, `item_sell_type`, `itemName`, `itemDesc`, `goodsType`, `goodsGroup`, `purchaseGroup`, `service_group`, `availabilityCheck`, `discountGroup`, `itemOpenStocks`, `itemBlockStocks`, `itemMovingAvgWeightedPrice`, `hsnCode`, `rcm_enabled`, `tds`, `cost_center`, `asset_classes`, `dep_key`, `isBomRequired`, `createdAt`, `createdBy`, `updatedAt`, `updatedBy`, `status` FROM `erp_inventory_items` WHERE `itemId`=$itemId AND `company_id` = $company_id AND `location_id` = $location_id ORDER BY `itemId` desc";

    $sql_list = "SELECT summary.*,items.*,hsn.taxPercentage, hsn.hsnDescription FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as summary INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId=items.itemId RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode WHERE summary.company_id='$company_id' AND summary.branch_id='$branch_id' AND summary.location_id='$location_id' AND summary.itemId=$itemId";

    $iv_sql = queryGet($sql_list, false);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        // discounts 📗📗📗📗📗📗📗📗📗📗📗📗📗📗
        $customer_group_sql = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = 82");

        $item_group_sql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemId` = '" . $itemId . "'");

        $customer_group = $customer_group_sql['data']['customer_discount_group'];
        $itemDiscountGroup = $item_group_sql['data']['discountGroup'];
        $item_group = json_decode($itemDiscountGroup);

        $days = $_POST['days'];
        $today = date('Y-m-d');
        $qty = $_POST['qty'];
        $basePrice = $_POST['basePrice'];

        $item_group_values = "'" . implode("', '", $item_group) . "'";

        // $check_discount = queryGet("SELECT * FROM `erp_discount_variant_master` WHERE (`term_of_payment` >= '" . $days . "' OR `term_of_payment` = 0) AND `valid_from` <= '" . $today . "' AND `valid_upto` >= '" . $today . "' AND `customer_discount_group_id` = '" . $customer_group . "' AND `item_discount_group_id` IN ($item_group_values) AND `coupon` IS NULL AND `company_id` = " . $company_id . " AND `location_id` = " . $location_id, true);

        // $check_discount = queryGet("SELECT * FROM `erp_discount_variant_master` WHERE (`term_of_payment` >= '" . $_POST['days'] . "' OR `term_of_payment` = 0) AND `valid_from` <= '" . date('Y-m-d') . "' AND `valid_upto` >= '" . date('Y-m-d') . "' AND `customer_discount_group_id` = '" . $customer_group . "' AND `item_discount_group_id` IN ($item_group_values)  AND `company_id` = " . $company_id . " AND `location_id` = " . $location_id, true);
        
        $check_discount = "SELECT * FROM `erp_discount_variant_master` WHERE (`term_of_payment` >= '" . $days . "' OR `term_of_payment` = 0) AND `valid_from` <= '" . date('Y-m-d') . "' AND `valid_upto` >= '" . date('Y-m-d') . "' AND `customer_discount_group_id` = '" . $customer_group . "' AND `item_discount_group_id` IN ($item_group_values)  AND `company_id` = " . $company_id . " AND `location_id` = " . $location_id;         
        $product_discounts = queryGet($check_discount, true);

        $iv_data['stock'] = $branchObj->itemQtyStockCheck($itemId, "'rmWhOpen', 'fgWhOpen'")['sumOfBatches'];

        $iv_data['all_discounts'] = $check_discount['data'];
        $iv_data['discounts'] = [];
        $iv_data['mrp_value'] = (int)$iv_data['itemPrice'];

        foreach ($check_discount['data'] as $data) {
            if ($data['minimum_value'] != 0 && $data['minimum_qty'] != 0) {

                if ($data['condition'] == 'AND') {

                    if ($qty >= $data['minimum_qty'] && $basePrice >= $data['minimum_value']) {

                        $iv_data['discounts'] = [
                            'discount_type' => $data['discount_type'],
                            'discount_percentage' => $data['discount_percentage'] ?? '',
                            'discount_value' => $data['discount_value'] ?? '',
                            'discount_max_value' => $data['discount_max_value'] ?? '',
                            'valid_from' => $data['valid_from'],
                            'valid_upto' => $data['valid_upto'],
                            'term_of_payment' => $data['term_of_payment'],
                            'minimum_qty' => $data['minimum_qty'],
                            'condition' => $data['condition'],
                            'minimum_value' => $data['minimum_value']
                        ];
                        // $arr[] = [
                        //     'discount_type' => $data['discount_type'],
                        //     'discount_percentage' => $data['discount_percentage'] ?? '',
                        //     'discount_value' => $data['discount_value'] ?? '',
                        //     'discount_max_value' => $data['discount_max_value'] ?? '',
                        //     'valid_from' => $data['valid_from'],
                        //     'valid_upto' => $data['valid_upto'],
                        //     'term_of_payment' => $data['term_of_payment'],
                        //     'minimum_qty' => $data['minimum_qty'],
                        //     'condition' => $data['condition'],
                        //     'minimum_value' => $data['minimum_value']

                        // ];
                    }
                } else {
                    if ($qty >= $data['minimum_qty'] || $basePrice >= $data['minimum_value']) {

                        $iv_data['discounts'] = [
                            'discount_type' => $data['discount_type'],
                            'discount_percentage' => $data['discount_percentage'] ?? '',
                            'discount_value' => $data['discount_value'] ?? '',
                            'discount_max_value' => $data['discount_max_value'] ?? '',
                            'valid_from' => $data['valid_from'],
                            'valid_upto' => $data['valid_upto'],
                            'term_of_payment' => $data['term_of_payment'],
                            'minimum_qty' => $data['minimum_qty'],
                            'condition' => $data['condition'],
                            'minimum_value' => $data['minimum_value']

                        ];
                    }
                }
            } elseif ($data['minimum_value'] != 0 && $data['minimum_qty'] == 0) {

                $iv_data['discounts'] = [
                    'discount_type' => $data['discount_type'],
                    'discount_percentage' => $data['discount_percentage'] ?? '',
                    'discount_value' => $data['discount_value'] ?? '',
                    'discount_max_value' => $data['discount_max_value'] ?? '',
                    'valid_from' => $data['valid_from'],
                    'valid_upto' => $data['valid_upto'],
                    'term_of_payment' => $data['term_of_payment'],
                    'minimum_qty' => $data['minimum_qty'],
                    'condition' => $data['condition'],
                    'minimum_value' => $data['minimum_value']
                ];
            } elseif ($data['minimum_value'] == 0 && $data['minimum_qty'] != 0) {

                $iv_data['discounts'] = [
                    'discount_type' => $data['discount_type'],
                    'discount_percentage' => $data['discount_percentage'] ?? '',
                    'discount_value' => $data['discount_value'] ?? '',
                    'discount_max_value' => $data['discount_max_value'] ?? '',
                    'valid_from' => $data['valid_from'],
                    'valid_upto' => $data['valid_upto'],
                    'term_of_payment' => $data['term_of_payment'],
                    'minimum_qty' => $data['minimum_qty'],
                    'condition' => $data['condition'],
                    'minimum_value' => $data['minimum_value']
                ];
            } elseif ($data['minimum_value'] == 0 && $data['minimum_qty'] == 0) {

                $iv_data['discounts'] = [
                    'discount_type' => $data['discount_type'],
                    'discount_percentage' => $data['discount_percentage'] ?? '',
                    'discount_value' => $data['discount_value'] ?? '',
                    'discount_max_value' => $data['discount_max_value'] ?? '',
                    'valid_from' => $data['valid_from'],
                    'valid_upto' => $data['valid_upto'],
                    'term_of_payment' => $data['term_of_payment'],
                    'minimum_qty' => $data['minimum_qty'],
                    'condition' => $data['condition'],
                    'minimum_value' => $data['minimum_value']
                ];
            }
        }
        // discounts 📗📗📗📗📗📗📗

        // mrp 💵💵💵💵💵💵💵💵💵
        $customerSql = queryGet("SELECT * FROM `erp_customer` as cus LEFT JOIN `erp_customer_address` as caddress ON cus.customer_id = caddress.customer_id WHERE cus.`customer_id` = 82 AND caddress.customer_address_primary_flag = 1");

        // console($sql);
        $customer_state = $customerSql['data']['customer_address_state_code'];

        $terr = queryGet("SELECT * FROM erp_mrp_territory WHERE JSON_SEARCH(state_codes, 'one', '$customer_state') IS NOT NULL");
        $territory = $terr['data']['territory_id'];
        $mrp_group = $customerSql['data']['customer_mrp_group'];

        //let us assume 
        $comapny_mrp_priority = 'territory';

        $mrpSql = queryGet("SELECT count(*) as count FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $itemId AND (varient.customer_group = $mrp_group OR varient.territory = $territory) AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id");
        
        $count = (int)$mrpSql['data']['count'];
        if ($count > 0) {
            if ($count > 1) {
                if ($comapny_mrp_priority == 'territory') {
                    $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $itemId AND varient.territory = $territory AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id");

                    $iv_data['mrp_value'] = $mrp_sql['data']['mrp'];
                } else {
                    $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $itemId AND varient.customer_group = $mrp_group  AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id");

                    $iv_data['mrp_value'] = $mrp_sql['data']['mrp'];
                }
            } else {
                $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $itemId AND (varient.customer_group = $mrp_group OR varient.territory = $territory) AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id");

                $iv_data['mrp_value'] = $mrp_sql['data']['mrp'];
            }
        } 
        // mrp 💵💵💵💵💵💵💵💵💵💵

        // add product details
        $iv_data['productDetails'] = [
            "netWeight" => $iv_data['netWeight'] ?? 0,
            "grossWeight" => $iv_data['grossWeight'] ?? 0,
            "volume" => $iv_data['volume'] ?? 0,
            "height" => $iv_data['height'] ?? 0,
            "width" => $iv_data['width'] ?? 0,
            "length" => $iv_data['length'] ?? 0,
            "baseUnitMeasure" => $iv_data['baseUnitMeasure'] ?? 0,
            "issueUnitMeasure" => $iv_data['issueUnitMeasure'] ?? 0,
            "uomRel" => $iv_data['uomRel'] ?? 0,
            "service_unit" => $iv_data['service_unit'] ?? 0,
            "weight_unit" => $iv_data['weight_unit'] ?? 0,
            "measuring_unit" => $iv_data['measuring_unit'] ?? 0,
            "purchasingValueKey" => $iv_data['purchasingValueKey'] ?? 0,
        ];

        // inventoryItemImages
        $sqlInventoryItemImages = $branchObj->inventoryItemImages($iv_data['itemId']);
        $iv_data['images'] = [];
        foreach ($sqlInventoryItemImages['data'] as $key => $value) {
            $iv_data['images'][$key] = COMP_STORAGE_URL . '/others/'. $value["image_name"];
        }
        
        // itemSpecification
        $sqlItemSpecification = $branchObj->itemSpecification($iv_data['itemId']); 
        $iv_data['specifications'] = [];
        foreach ($sqlItemSpecification['data'] as $key => $value) {
            $iv_data['specifications'][$key] = $value;
        }

        unset(
            $iv_data['netWeight'],
            $iv_data['grossWeight'],
            $iv_data['volume'],
            $iv_data['height'],
            $iv_data['width'],
            $iv_data['length'],
            $iv_data['baseUnitMeasure'],
            $iv_data['issueUnitMeasure'],
            $iv_data['uomRel'],
            $iv_data['service_unit'],
            $iv_data['weight_unit'],
            $iv_data['measuring_unit'],
            $iv_data['purchasingValueKey']
        );

        sendApiResponse([
            "status" => $iv_sql['status'],
            "message" => $iv_sql['message'],
            "numRows" => $iv_sql['numRows'],
            "data" => $iv_data,

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
