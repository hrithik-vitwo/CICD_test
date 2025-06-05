<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authVendor = authVendorApiRequest();
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];
    $vendor_id = $authVendor['vendor_id'];
    $vendor_code = $authVendor['vendor_code'];
    $vendor_name = $authVendor['trade_name'];
    $vendor_email = $authVendor['email'];
    $vendor_phone = $authVendor['phone'];
    $vendor_gst = $authVendor['vendor_gstin'];
    $vendor_pan = $authVendor['vendor_pan'];
    $constitution_of_business = $authVendor['constitution_of_business'];

    $data = json_decode(file_get_contents("php://input"), true);

    $description = $data['description'];

    $vendorDetailsObj = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE vendor_id=$vendor_id", false);
    $vendorDetails = $vendorDetailsObj['data'];

    $flat_no = $vendorDetails['vendor_business_flat_no'];
    $building_no = $vendorDetails['vendor_business_building_no'];
    $street_name = $vendorDetails['vendor_business_street_name'];
    $location = $vendorDetails['vendor_business_location'];
    $city = $vendorDetails['vendor_business_city'];
    $district = $vendorDetails['vendor_business_district'];
    $state = $vendorDetails['vendor_business_state'];
    $pin_code = $vendorDetails['vendor_business_pin_code'];
    $unique_no = $rfqId . uniqid();

    $rfqListId = $data['rfqId'];
    $rfqListObj = queryGet("SELECT * FROM `erp_rfq_list` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND rfqId=$rfqListId", false);
    $rfqList = $rfqListObj['data'];

    $rfqId = $rfqList['rfqId'] ?? 0;
    $rfq_code = $rfqList['rfqCode'] ?? '';

    $insertQuery = queryInsert("INSERT INTO `erp_vendor_response`
        SET
            `vendor_id` =$vendor_id,
            `vendor_code` ='$vendor_code',
            `rfq_code` ='$rfq_code',
            `rfqId` =$rfqId,
            `vendor_name` ='$vendor_name',
            `vendor_email` ='$vendor_email',
            `vendor_gst` ='$vendor_gst',
            `vendor_pan` ='$vendor_pan',
            `vendor_tradename` ='$vendor_name',
            `vendor_constofbusiness` ='$constitution_of_business',
            `vendor_flatno` ='$flat_no',
            `vendor_buildno` ='$building_no',
            `vendor_streetname` ='$street_name',
            `vendor_location` ='$location',
            `vendor_phone` ='$vendor_phone',
            `vendor_city` ='$city',
            `vendor_district` ='$district',
            `vendor_state` ='$state',
            `vendor_pin` ='$pin_code',
            `vendor_description` ='$description',
            `unique_no` ='$unique_no'
        ");
    $insertedId = $insertQuery['insertedId'];

    $vendorItems = $data['vendorItems'];
    if ($insertQuery['status'] == "success") {

        foreach ($vendorItems as $key => $item) {
            $item_id = $item['item_id'] ?? 0;
            $item_code = $item['item_code'] ?? "";
            $item_name = $item['item_name'] ?? "";
            $item_desc = $item['item_desc'] ?? "";
            $rq = $item['rq'] ?? 0;
            $net_weight = $item['net_weight'] ?? 0;
            $gross_weight = $item['gross_weight'] ?? 0;
            $unit = $item['unit'] ?? 0;
            $purchasingValueKey = $item['purchasingValueKey'] ?? 0;
            $volume = $item['volume'] ?? 0;
            $volumeCubeCm = $item['volumeCubeCm'] ?? 0;
            $height = $item['height'] ?? 0;
            $width = $item['width'] ?? 0;
            $length = $item['length'] ?? 0;
            $goodsType = $item['goodsType'] ?? 0;
            $goodsGroup = $item['goodsGroup'] ?? 0;
            $purchaseGroup = $item['purchaseGroup'] ?? 0;
            $branch = $item['branch'] ?? 0;
            $availabilityCheck = $item['availabilityCheck'] ?? "";
            $issueUnitMeasure = $item['issueUnitMeasure'] ?? 0;
            $uomRel = $item['uomRel'] ?? 0;
            $storageBin = $item['storageBin'];
            $pickingArea = $item['pickingArea'];
            $tempControl = $item['tempControl'];
            $storageControl = $item['storageControl'];
            $maxStoragePeriod = $item['maxStoragePeriod'];
            $maxStoragePeriodTimeUnit = $item['maxStoragePeriodTimeUnit'];
            $minRemainSelfLife = $item['minRemainSelfLife'];
            $minRemainSelfLifeTimeUnit = $item['minRemainSelfLifeTimeUnit'];
            $moq = $item['moq'] ?? 0;
            $price = $item['price'] ?? 0;
            $discount = $item['discount'] ?? 0;
            $total = $item['total'] ?? 0;
            $gst = $item['gst'] ?? 0;
            $lead_time = $item['lead_time'] ?? 0;
            $delivery_mode = $item['delivery_mode'] ?? 0;
            $moq_diff_value = $item['moq_diff_value'] ?? 0;

            // #####################
            if ($rq >= $moq) {
                $moq_diff_value = 1;
            } elseif ($rq < $moq) {
                $moq_diff_value = 2;
            }

            $itemSql = "INSERT INTO `erp_vendor_item` 
                SET
                    `erp_v_id` = $insertedId,
                    `item_id` = $item_id,
                    `item_code` = '$item_code',
                    `item_name` = '$item_name',
                    `item_desc` = '$item_desc',
                    `rq` = '$rq',
                    `net_weight` = '$net_weight',
                    `gross_weight` = '$gross_weight',
                    `unit` = '$unit',
                    `purchasingValueKey` = '$purchasingValueKey',
                    `volume` = '$volume',
                    `volumeCubeCm` = '$volumeCubeCm',
                    `height` = '$height',
                    `width` = '$width',
                    `length` = '$length',
                    `goodsType` = '$goodsType',
                    `goodsGroup` = '$goodsGroup',
                    `purchaseGroup` = '$purchaseGroup',
                    `branch` = '$branch',
                    `availabilityCheck` = '$availabilityCheck',
                    `issueUnitMeasure` = '$issueUnitMeasure',
                    `uomRel` = '$uomRel',
                    `storageBin` = '$storageBin',
                    `pickingArea` = '$pickingArea',
                    `tempControl` = '$tempControl',
                    `storageControl` = '$storageControl',
                    `maxStoragePeriod` = '$maxStoragePeriod',
                    `maxStoragePeriodTimeUnit` = '$maxStoragePeriodTimeUnit',
                    `minRemainSelfLife` = '$minRemainSelfLife',
                    `minRemainSelfLifeTimeUnit` = '$minRemainSelfLifeTimeUnit',
                    `moq` = '$moq',
                    `price` = '$price',
                    `discount` = '$discount',
                    `total` = '$total',
                    `gst` = '$gst',
                    `lead_time` = '$lead_time',
                    `delivery_mode` = '$delivery_mode',
                    `moq_diff_value` = '$moq_diff_value'
            ";
            $insertVendorItems = queryInsert($itemSql);
        }

        if ($insertVendorItems['status'] == "success") {

            $updateRfqObj = "UPDATE `erp_rfq_list` SET rfq_status=10 WHERE rfqId=$rfqId";
            $updateRfq = queryUpdate($updateRfqObj);

            sendApiResponse([
                "status" => "success",
                "message" => "Inserted Successfully"
            ], 200);
        } else {
            sendApiResponse([
                "status" => "warning",
                "itemSql" => $itemSql,
                "message" => "Somthing went wrong!"
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
