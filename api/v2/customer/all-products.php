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

    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $return = [];

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND `createdAt` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`itemCode` like '%" . $_POST['keyword'] . "%' OR `itemName` like '%" . $_POST['keyword'] . "%')";
    }

    // $sql_list = "SELECT * FROM `erp_inventory_items` WHERE `company_id` = $company_id AND `location_id` = $location_id $cond ORDER BY `itemId` DESC LIMIT $start, $end";

    $sql_list = "SELECT summary.*,items.*,hsn.taxPercentage, hsn.hsnDescription FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` as summary INNER JOIN  `" . ERP_INVENTORY_ITEMS . "` as items ON summary.itemId=items.itemId RIGHT JOIN `" . ERP_HSN_CODE . "` as hsn ON items.hsnCode = hsn.hsnCode WHERE summary.company_id='$company_id' AND summary.branch_id='$branch_id' AND summary.location_id='$location_id' AND summary.itemId=items.itemId AND items.goodsType IN (3,4,5) $cond ORDER BY summary.itemId DESC LIMIT $start, $end";

    $iv_sql = queryGet($sql_list, true);

    $sql = queryGet("SELECT * FROM `erp_customer` as cus LEFT JOIN `erp_customer_address` as caddress ON cus.customer_id = caddress.customer_id WHERE cus.`customer_id` = $customer_id AND caddress.customer_address_primary_flag = 1");

    $customer_state = $sql['data']['customer_address_state_code'];

    $query = "SELECT * FROM erp_mrp_territory WHERE `location_id` = $location_id";
    $result = queryGet($query, true);
    $matching_rows = [];

    foreach ($result['data'] as $row) {
        $state_codes = unserialize($row['state_codes']);
        if (in_array($customer_state, $state_codes)) {
            $matching_rows[] = $row;
        }
    }
    $territory = !empty($matching_rows[0]['territory_id']) ? $matching_rows[0]['territory_id'] : 0;
    $mrp_group =  !empty($sql['data']['customer_mrp_group']) ? $sql['data']['customer_mrp_group'] : 0;

    $company_sql = queryGet("SELECT * FROM `erp_companies` WHERE `company_id` = $company_id");
    $comapny_mrp_priority = $company_sql['data']['mrpPriority'];
    $today = date('Y-m-d');
    // mrp**************************************

    $customer_group = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = $customer_id");
    $customerGroup = $customer_group['data']['customer_discount_group'];

    foreach ($iv_sql["data"] as &$product) {

        $dynamic_item_id = $product['itemId'];
        $itemDiscountGroupSql = "SELECT `discountGroup` FROM `erp_inventory_items` WHERE `itemId` = $dynamic_item_id";
        $itemDiscountGroupObj = queryGet($itemDiscountGroupSql);

        // inventoryItemImages
        $sqlInventoryItemImages = $branchObj->inventoryItemImages($dynamic_item_id);
        $product['images'] = [];
        foreach ($sqlInventoryItemImages['data'] as $key => $value) {
            $product['images'][$key] = COMP_STORAGE_URL . '/others/'. $value["image_name"];
        }

        // itemSpecification
        $sqlItemSpecification = $branchObj->itemSpecification($dynamic_item_id); 
        $product['specifications'] = [];
        foreach ($sqlItemSpecification['data'] as $key => $value) {
            $product['specifications'][$key] = $value;
        }

        // uom **************
        $sqlUom = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId` = " . $product['baseUnitMeasure']);
        $product['uom'] = $sqlUom['data']['uomName'];
        // uom **************

        $itemGroup = json_decode($itemDiscountGroupObj['data']['discountGroup']);
        $itemGroupValues = "'" . implode("', '", $itemGroup) . "'";

        $check_discount = "SELECT * FROM `erp_discount_variant_master` WHERE (`term_of_payment` >= '" . $_POST['days'] . "' OR `term_of_payment` = 0) AND `valid_from` <= '" . date('Y-m-d') . "' AND `valid_upto` >= '" . date('Y-m-d') . "' AND `customer_discount_group_id` = '" . $customerGroup . "' AND `item_discount_group_id` IN ($itemGroupValues)  AND `company_id` = " . $company_id . " AND `location_id` = " . $location_id;         
        $product_discounts = queryGet($check_discount, true);

        $product['discounts'] = $product_discounts['data'];

        $product['stock'] = (int)$branchObj->itemQtyStockCheck($dynamic_item_id, "'rmWhOpen', 'fgWhOpen'")['sumOfBatches'];

        // mrp ***********************
        if ($territory == 0 && $mrp_group == 0) {
            $product['mrp_value'] = (int)$product['itemPrice'];
        } else {
            $sql_count = queryGet("SELECT count(*) as count FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $dynamic_item_id AND (varient.customer_group = $mrp_group OR varient.territory = $territory) AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");

            $count = (int)$sql_count['data']['count'];
            if ($count > 0) {
                if ($count > 1) {
                    if ($comapny_mrp_priority == 'territory') {
                        $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $dynamic_item_id AND  varient.territory = $territory AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");

                        $product["mrp_value"] = (int)$mrp_sql['data']['mrp'];
                    } else {
                        $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $dynamic_item_id AND varient.customer_group = $mrp_group  AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");

                        $product["mrp_value"] = (int)$mrp_sql['data']['mrp'];
                    }
                } else {
                    $mrp_sql =  queryGet("SELECT * FROM `erp_mrp_variant_items` as items LEFT JOIN `erp_mrp_variant` as varient ON varient.mrp_id = items.mrp_id WHERE items.item_id = $dynamic_item_id AND (varient.customer_group = $mrp_group OR varient.territory = $territory) AND varient.`company_id` = $company_id AND varient.`location_id` = $location_id AND items.`status` = 'active' AND varient.`valid_from` <= '" . $today . "' AND varient.`valid_till` >= '" . $today . "'");

                    $product["mrp_value"] = (int)$mrp_sql['data']['mrp'];
                }
                $product["mrp_value"] = (int)$mrp_sql['data']['mrp'];
            } else {
                $product["mrp_value"] = (int)$product['itemPrice'];
            }
        }
        // mrp ***********************
        
        unset($product['baseUnitMeasure']);
    }
    
    sendApiResponse([
        "status" => "success",
        "message" => "Data fetched successfully",
        "data" => $iv_sql["data"]
    ]);
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
