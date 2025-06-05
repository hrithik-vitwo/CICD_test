<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];

    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND created_at between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
    $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];

    $companyDetails = array_merge($companyDetailsObj, $companyAdminDetailsObj);

    $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];

    $sql_list = "SELECT * FROM `erp_vendor_response` WHERE vendor_id = $vendor_id " . $cond . " ORDER BY erp_v_id DESC LIMIT $start,$end";
    $rfq_sql = queryGet($sql_list, true);

    if ($rfq_sql['status'] == "success" && $rfq_sql['numRows'] > 0) {
        $rfq_data = $rfq_sql["data"];
        $data_array = [];
        foreach ($rfq_data as $key => $data) {
            $totalAmount = 0;
            $totalDiscountAmount = 0;
            $totalGSTAmount = 0;
            $rfqId = $data["rfqId"];

            $rfqItemSql = "SELECT 
            vendor_item.`erp_vi_id`, 
            vendor_item.`erp_v_id`, 
            vendor_item.`item_id`, 
            vendor_item.`item_code`, 
            vendor_item.`item_name`, 
            vendor_item.`item_desc`, 
            vendor_item.`rq`, 
            vendor_item.`net_weight`, 
            vendor_item.`gross_weight`, 
            vendor_item.`unit`, 
            vendor_item.`purchasingValueKey`, 
            vendor_item.`volume`, 
            vendor_item.`volumeCubeCm`, 
            vendor_item.`height`, 
            vendor_item.`width`, 
            vendor_item.`length`, 
            vendor_item.`goodsType`, 
            vendor_item.`goodsGroup`, 
            vendor_item.`purchaseGroup`, 
            vendor_item.`branch`, 
            vendor_item.`availabilityCheck`, 
            vendor_item.`issueUnitMeasure`, 
            vendor_item.`uomRel`, 
            vendor_item.`storageBin`, 
            vendor_item.`pickingArea`, 
            vendor_item.`tempControl`, 
            vendor_item.`storageControl`, 
            vendor_item.`maxStoragePeriod`, 
            vendor_item.`maxStoragePeriodTimeUnit`, 
            vendor_item.`minRemainSelfLife`, 
            vendor_item.`minRemainSelfLifeTimeUnit`, 
            vendor_item.`moq`, 
            vendor_item.`price`, 
            vendor_item.`discount`, 
            vendor_item.`total`, 
            vendor_item.`gst`, 
            vendor_item.`lead_time`, 
            vendor_item.`delivery_mode`, 
            vendor_item.`moq_diff_value`,
            (vendor_item.`price` * (vendor_item.`discount` / 100) * vendor_item.`moq`) AS `discount_amount`,
            (vendor_item.`price` * vendor_item.`moq` - (vendor_item.`price` * (vendor_item.`discount` / 100) * vendor_item.`moq`)) AS `price_after_discount`,
            ((vendor_item.`price` * vendor_item.`moq` - (vendor_item.`price` * (vendor_item.`discount` / 100) * vendor_item.`moq`)) * (vendor_item.`gst` / 100)) AS `gst_amount`,
            ((vendor_item.`price` * vendor_item.`moq` - (vendor_item.`price` * (vendor_item.`discount` / 100) * vendor_item.`moq`)) + ((vendor_item.`price` * vendor_item.`moq` - (vendor_item.`price` * (vendor_item.`discount` / 100) * vendor_item.`moq`)) * (vendor_item.`gst` / 100))) AS `total_price_including_gst`,
            item.*
             FROM `erp_vendor_item` as vendor_item, `" . ERP_INVENTORY_ITEMS . "` as item WHERE vendor_item.item_id=item.itemId AND vendor_item.`erp_v_id`=" . $data['erp_v_id'] . "";
            $rfq_items = queryGet($rfqItemSql, true);

            $rfq_item_data = $rfq_items["data"];

            foreach ($rfq_item_data as $key => $value) {
                $totalAmount += $value["total"];
                $totalDiscountAmount += $value["discount_amount"];
                $totalGSTAmount += $value["gst_amount"];
            }

            $data["totalDiscountAmount"] = intval($totalDiscountAmount);
            $data["totalGSTAmount"] = intval($totalGSTAmount);
            $data["totalAmount"] = intval($totalAmount);

            $data["quotationDownloadLink"] = BASE_URL . "vendor/quotation-download.php?rfqId=" . $data["rfqId"];
            // add location details
            $data["shippingDetails"] = $locationDetailsObj;

            // add company details
            $data["companyDetails"] = $companyDetails;

            $data_array[] = array("rfq" => $data, "rfq_item" => $rfq_item_data);
        }
        sendApiResponse([
            "status" => "success",
            "count" => count($data_array),
            "message" => "Quotation fetched successfully",
            "data" => $data_array

        ], 200);
    } 
    else if ($rfq_sql['message'] == "Record not found" && $rfq_sql['numRows'] == 0) {
        sendApiResponse([
            "status" => "warning",
            "sql" => $sql_list,
            "message" => "No quotation found",
            "data" => []

        ], 200);
    }
    else {
        sendApiResponse([
            "status" => "warning",
            "sql" => $sql_list,
            "message" => "No quotation found",
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
