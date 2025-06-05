<?php
require_once("api-common-func.php");

require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
$branchObj = new BranchSo();

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'] ?? 0;
    $company_id = $authCustomer['company_id'] ?? 0;
    $branch_id = $authCustomer['branch_id'] ?? 0;
    $location_id = $authCustomer['location_id'] ?? 0;

    define("BASE_URL", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/");
    define("BUCKET_URL", BASE_URL);
    define("COMP_STORAGE_URL", BUCKET_URL . "uploads/$company_id");

    // Continue with the existing code for listing groups and associated products
    $pageNo = $_POST['pageNo'] ?? 0;
    $show = $_POST['limit'] ?? 10;
    $goodGroupId = $_POST['goodGroupId'] ?? 0;
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND p.`createdAt` BETWEEN '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (p.`itemCode` LIKE '%" . $_POST['keyword'] . "%' OR p.`itemName` LIKE '%" . $_POST['keyword'] . "%')";
    }

    $checkSql = "SELECT goodGroup.*, items.* FROM `erp_inventory_mstr_good_groups` goodGroup, `erp_inventory_items` items
    WHERE goodGroup.`goodGroupId` = items.`goodsGroup` 
    AND goodGroup.`companyId`= $company_id
    AND goodGroup.`groupParentId`= $goodGroupId";
    $check = queryGet($checkSql, true);
    $group = $check['data'];

    if (empty($group)) {
        $sql_list = "SELECT
                summary.*,
                items.*,
                hsn.taxPercentage,
                hsn.hsnDescription,
                good_groups.*
            FROM
                `erp_inventory_stocks_summary` AS summary
            INNER JOIN
                `erp_inventory_items` AS items ON summary.itemId = items.itemId
            LEFT JOIN
                `erp_inventory_mstr_good_groups` AS good_groups ON items.goodsGroup = good_groups.goodGroupId
            RIGHT JOIN
                `erp_hsn_code` AS hsn ON items.hsnCode = hsn.hsnCode
            WHERE
                summary.company_id = $company_id
                AND good_groups.goodGroupId = items.goodsGroup
                AND summary.branch_id = $branch_id
                AND summary.location_id = $location_id
                AND items.goodsGroup = $goodGroupId
            ORDER BY
                items.itemId DESC
            LIMIT
                $start, $end";
    } else {
        $sql_list = "SELECT
                summary.*,
                items.*,
                hsn.taxPercentage,
                hsn.hsnDescription,
                good_groups.*
            FROM
                `erp_inventory_stocks_summary` AS summary
            INNER JOIN
                `erp_inventory_items` AS items ON summary.itemId = items.itemId
            LEFT JOIN
                `erp_inventory_mstr_good_groups` AS good_groups ON items.goodsGroup = good_groups.goodGroupId
            RIGHT JOIN
                `erp_hsn_code` AS hsn ON items.hsnCode = hsn.hsnCode
            WHERE
                summary.company_id = $company_id
                AND good_groups.goodGroupId = items.goodsGroup
                AND summary.branch_id = $branch_id
                AND summary.location_id = $location_id
                AND good_groups.groupParentId = $goodGroupId
            ORDER BY
                items.itemId DESC
            LIMIT
                $start, $end";
    }

    $iv_sql = queryGet($sql_list, true);

    // mrp**************************************
    // $customerSql = queryGet("SELECT * FROM `erp_customer` as cus LEFT JOIN `erp_customer_address` as caddress ON cus.customer_id = caddress.customer_id WHERE cus.`customer_id` = 82 AND caddress.customer_address_primary_flag = 1");
    // $customer_state = $customerSql['data']['customer_address_state_code'] ?? 0;

    // $terr = queryGet("SELECT * FROM erp_mrp_territory WHERE JSON_SEARCH(state_codes, 'one', '$customer_state') IS NOT NULL");
    // $territory = $terr['data']['territory_id'];
    // $mrp_group = $customerSql['data']['customer_mrp_group'];

    $sql = queryGet("SELECT * FROM `erp_customer` as cus LEFT JOIN `erp_customer_address` as caddress ON cus.customer_id = caddress.customer_id WHERE cus.`customer_id` = 86 AND caddress.customer_address_primary_flag = 1");

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

    $customer_group = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = 86");
    $customerGroup = $customer_group['data']['customer_discount_group'];

    if ($iv_sql['status'] == "success") {
        $iv_data = $iv_sql["data"];

        // Organize the data into groups with associated products
        $groupsWithProducts = [];
        foreach ($iv_data as $row) {

            $dynamic_item_id = $row['itemId'];
            $itemDiscountGroupSql = "SELECT `discountGroup` FROM `erp_inventory_items` WHERE `itemId` = $dynamic_item_id";
            $itemDiscountGroupObj = queryGet($itemDiscountGroupSql);

            $itemGroup = json_decode($itemDiscountGroupObj['data']['discountGroup']);
            $itemGroupValues = "'" . implode("', '", $itemGroup) . "'";

            $check_discount = "SELECT * FROM `erp_discount_variant_master` WHERE (`term_of_payment` >= '" . $_POST['days'] . "' OR `term_of_payment` = 0) AND `valid_from` <= '" . date('Y-m-d') . "' AND `valid_upto` >= '" . date('Y-m-d') . "' AND `customer_discount_group_id` = '" . $customerGroup . "' AND `item_discount_group_id` IN ($itemGroupValues)  AND `company_id` = " . $company_id . " AND `location_id` = " . $location_id;
            $product_discounts = queryGet($check_discount, true);


            // $groupsWithProducts[$groupId]["items"]['discounts'] = $product_discounts['data'];

            $groupId = $row['goodGroupId'];
            if (!isset($groupsWithProducts[$groupId])) {
                $group = [
                    "companyId" => $row["companyId"],
                    "goodGroupId" => $groupId,
                    "goodGroupName" => $row["goodGroupName"],
                    "goodGroupDesc" => $row["goodGroupDesc"],
                    "groupParentId" => $row["groupParentId"],
                    "goodType" => $row["goodType"],
                    "createdAt" => $row["goodGroupCreatedAt"],
                    "createdBy" => $row["goodGroupCreatedBy"],
                    "status" => $row["goodGroupStatus"],
                    "items" => []
                ];
                $groupsWithProducts[$groupId] = $group;
            }

            // Add product information to the products array within the group            
            $product = [
                "itemId" => $row["itemId"],
                "company_id" => $row["company_id"],
                "branch" => $row["branch"],
                "location_id" => $row["location_id"],
                "parentGlId" => $row["parentGlId"],
                "itemCode" => $row["itemCode"],
                "itemPrice" => $row["itemPrice"],
                "item_sell_type" => $row["item_sell_type"],
                "itemName" => $row["itemName"],
                "itemDesc" => $row["itemDesc"],
                "netWeight" => $row["netWeight"],
                "grossWeight" => $row["grossWeight"],
                "volume" => $row["volume"],
                "volumeCubeCm" => $row["volumeCubeCm"],
                "height" => $row["height"],
                "width" => $row["width"],
                "length" => $row["length"],
                "goodsType" => $row["goodsType"],
                "goodsGroup" => $row["goodsGroup"],
                "purchaseGroup" => $row["purchaseGroup"],
                "service_group" => $row["service_group"],
                "availabilityCheck" => $row["availabilityCheck"],
                "baseUnitMeasure" => $row["baseUnitMeasure"],
                "issueUnitMeasure" => $row["issueUnitMeasure"],
                "uomRel" => $row["uomRel"],
                "service_unit" => $row["service_unit"],
                "weight_unit" => $row["weight_unit"],
                "measuring_unit" => $row["measuring_unit"],
                "purchasingValueKey" => $row["purchasingValueKey"],
                "itemOpenStocks" => $row["itemOpenStocks"],
                "itemBlockStocks" => $row["itemBlockStocks"],
                "itemMovingAvgWeightedPrice" => $row["itemMovingAvgWeightedPrice"],
                "hsnCode" => $row["hsnCode"],
                "rcm_enabled" => $row["rcm_enabled"],
                "tds" => $row["tds"],
                "cost_center" => $row["cost_center"],
                "asset_classes" => $row["asset_classes"],
                "dep_key" => $row["dep_key"],
                "isBomRequired" => $row["isBomRequired"],
                "status" => $row["status"],
                "stock" => $branchObj->itemQtyStockCheck($dynamic_item_id, "'rmWhOpen', 'fgWhOpen'")['sumOfBatches'],
                "discounts" => $product_discounts['data']
            ];

            // mrp ***********************
            if ($territory == 0 && $mrp_group == 0) {
                $product['mrp_value'] = (int)$row['itemPrice'];
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
                    $product["mrp_value"] = (int)$row['itemPrice'];
                }
            }
            // mrp ***********************

            // inventoryItemImages
            $sqlInventoryItemImages = $branchObj->inventoryItemImages($row['itemId']);
            $product['images'] = [];
            foreach ($sqlInventoryItemImages['data'] as $key => $value) {
                $product['images'][$key] = COMP_STORAGE_URL . '/others/' . $value["image_name"];
            }

            // itemSpecification
            $sqlItemSpecification = $branchObj->itemSpecification($row['itemId']);
            $product['specifications'] = [];
            foreach ($sqlItemSpecification['data'] as $key => $value) {
                $product['specifications'][$key] = $value;
            }

            $groupsWithProducts[$groupId]["items"][] = $product;
        }

        sendApiResponse([
            "status" => "success",
            "message" => $iv_sql['message'],
            "numRows" => count($iv_data),
            "data" => array_values($groupsWithProducts)
        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "sql" => $sql_list,
            "message" => "Data not found",
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
