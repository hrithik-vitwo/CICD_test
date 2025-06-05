<?php
require_once("api-common-func.php");



// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];
    $vendor_code = $authVendor['vendor_code'];
    $vendor_gst = $authVendor['vendor_gstin'];
    $vendor_pan = $authVendor['vendor_pan'];
    $vendor_tradename = $authVendor['trade_name'];
    $vendor_constofbusiness = $authVendor['constitution_of_business'];
    // console($authVendor);
    // exit();
    $vendor_details = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=$vendor_id ORDER BY vendor_business_id ASC LIMIT 1 ");
    $vendor_sql = queryGet("SELECT * FROM `tbl_vendor_admin_details` WHERE `fldAdminVendorId`=$vendor_id ORDER BY fldAdminKey ASC LIMIT 1 ");

    $vendor_flatno =  $vendor_details['data']['vendor_business_flat_no'];
    $vendor_buildno = $vendor_details['data']['vendor_business_building_no'];
    $vendor_streetname = $vendor_details['data']['vendor_business_street_name'];
    $vendor_location = $vendor_details['data']['vendor_business_location'];

    $vendor_name = $vendor_sql['data']['fldAdminName'];
    $vendor_email = $vendor_sql['data']['fldAdminEmail'];
    $vendor_phone = $vendor_sql['data']['fldAdminPhone'];

    $vendor_city = $vendor_details['data']['vendor_business_city'];
    $vendor_district = $vendor_details['data']['vendor_business_district'];
    $vendor_state = $vendor_details['data']['vendor_business_state'];
    $vendor_pin = $vendor_details['data']['vendor_business_pin_code'];
    $vendor_description = " ";




    $rfq_code = $_POST['rfq_code'];
    $rfq_id = $_POST['rfq_id'];
    $pr_id = $_POST['prId'];
    $rfq_items_id = $_POST['rfq_items'];

    $rand = rand(1000000, 10000000);

    $sql_insert = "INSERT INTO erp_vendor_response 
    SET 
    `vendor_code`='$vendor_code',
    `vendor_id`='$vendor_id',
    `rfq_code`='$rfq_code',
    `vendor_gst`='$vendor_gst',
    `rfqId`='$rfq_id',
    `vendor_pan`='$vendor_pan',
    `vendor_tradename`='$vendor_tradename',
    `vendor_constofbusiness`='$vendor_constofbusiness',
    `vendor_flatno`='$vendor_flatno',
    `vendor_buildno`='$vendor_buildno',
    `vendor_streetname`='$vendor_streetname',
    `vendor_location`='$vendor_location',
    `vendor_name`='$vendor_name',
    `vendor_email`='$vendor_email',
    `vendor_phone`='$vendor_phone',  
    `vendor_city`='$vendor_city',
    `vendor_district`='$vendor_district',
    `vendor_state`='$vendor_state',
    `vendor_pin`='$vendor_pin',
    `vendor_description`='$vendor_description',
    `unique_no`='$rand'";

    $insert =  queryInsert($sql_insert);
    $last_v_id = $insert['insertedId'];

    $r_id = json_decode($rfq_items_id);
    $quan = json_decode($_POST['moq']);
    $value = json_decode($_POST['price']);
    $gst_post = json_decode($_POST['gst']);
    $discount_post = json_decode($_POST['discount']);
    $total_post = json_decode($_POST['total']);
    $del_post = json_decode($_POST['mode']);
    //$date_post = json_decode($_POST['date']);
    $lead_post = json_decode($_POST['lead_time']);

    if ($insert['status'] == "success") {



        foreach ($r_id as $key => $val) {
            $qty = $quan[$key];
            $price = $value[$key];
            $discount = $discount_post[$key];
            $gst =  $gst_post[$key];
            $total = $total_post[$key];
            $del_mode = $del_post[$key];
            $lead_time = $lead_post[$key];

            // $del_date = $date_post[$key];

            // $from_date = date_create(date("Y/m/d")); // Input your date here e.g. strtotime("2014-01-02")
            // $to_date = date_create(date("Y/m/d", strtotime($del_date)));
            // $lead_time = date_diff($from_date, $to_date)->format("%a");

            // console($lead_time);
            // exit();
            // echo $val;
            $sql = queryGet("SELECT * FROM `erp_rfq_items` WHERE `rfqItemId`=$val");
            $item_id = $sql['data']['ItemId'];

            $pr_sql = queryGet("SELECT * FROM `erp_branch_purchase_request_items` WHERE `itemId`=$item_id");
            $item_rq = $pr_sql['data']['remainingQty'];

            $item_sql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemId`=$item_id");
            $item_name = $item_sql['data']['itemName'];
            $item_code = $item_sql['data']['itemCode'];
            $item_desc =  $item_sql['data']['itemDesc'];
            $net_weight =  $item_sql['data']['netWeight'];
            $gross_weight =  $item_sql['data']['grossWeight'];
            $unit =  $item_sql['data']['measuring_unit'];
            $purchase_value = $item_sql['data']['purchasingValueKey'];
            $volume =  $item_sql['data']['volume'];
            $volumeCubeCm =  $item_sql['data']['volumeCubeCm'];
            $height =  $item_sql['data']['height'];
            $width =  $item_sql['data']['width'];
            $length =  $item_sql['data']['length'];
            $goodsType =  $item_sql['data']['goodsType'];
            $goodsGroup =  $item_sql['data']['goodsGroup'];
            $purchaseGroup =  $item_sql['data']['purchaseGroup'];
            $branch =  $item_sql['data']['branch'];
            $availabilityCheck =  $item_sql['data']['availabilityCheck'];
            $issueUnitMeasure =  $item_sql['data']['issueUnitMeasure'];
            $buom =  $item_sql['data']['baseUnitMeasure'];
            $uomRel =  $item_sql['data']['uomRel'];


            if ($item_rq >= $$qty) {
                $moq_diff_value = 1;
            } elseif ($item_rq < $qty) {
                $moq_diff_value = 2;
            }



            $sql1 = queryInsert("INSERT INTO `erp_vendor_item`
            SET 
            `item_id`='$item_id',
            `item_code`='$item_code',
            `item_name`='$item_name',
            `item_desc`='$item_desc',
            `rq`='$item_rq',
            `net_weight`='$net_weight',
            `gross_weight`='$gross_weight',
            `unit`='$unit',
            `volume`='$volume',
            `volumeCubeCm`='$volumeCubeCm',
            `height`='$height',
            `width`='$width',
            `length`='$length',
            `goodsType`='$goodsType',
            `goodsGroup`='$goodsGroup',
            `purchaseGroup`='$purchaseGroup',
            `branch`='$branch',
            `availabilityCheck`='$availabilityCheck',
            `issueUnitMeasure`='$issueUnitMeasure',
            `uomRel`='$uomRel',
            `purchasingValueKey`='$purchasingValueKey',
            `moq`='$qty',
            `price`='$price',
            `discount`='$discount',
            `total`='$total',
            `delivery_mode`= $del_mode,
            `lead_time`='$lead_time',
            `gst`='$gst',
            `moq_diff_value`='$moq_diff_value',
            `erp_v_id`=$last_v_id");
          
           
        }
        $update = queryUpdate("UPDATE `erp_rfq_list` SET `rfq_status`=1 WHERE `rfqId`=$rfq_id");
        // console($update);
        // exit();
        sendApiResponse([
            "status" => "success",
            "message" => "Quotation Added Successfully",
            "data" => []
        ], 200);
    } else {
        sendApiResponse([
            "status" => "error",
            "message" => "Something Went Wrong",
            "data" => []
        ], 405);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
//echo "ok";