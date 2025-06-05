<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-SendEmailToRFQvendor.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $rand = $_POST['rfqId'].uniqid();
    $vendor_id = $_POST['v_id'];
    $vendor_primary_id = $_POST["vendor_primary_id"]??"";
    $vendor_code = $_POST["v_code"]??"";
    $rfq_code = $_POST['rfq_code'];
    $rfq_id = $_POST['rfqId'];
    $vendor_gst = $_POST['vendor_gst'];
    $vendor_pan = $_POST['vendor_pan'];
    $vendor_tradename = $_POST['vendor_tradename'];
    $vendor_constofbusiness = $_POST['vendor_constofbusiness'];
    $vendor_flatno = $_POST['vendor_flatno'];
    $vendor_buildno = $_POST['vendor_buildno'];
    $vendor_streetname = $_POST['vendor_streetname'];
    $vendor_location = $_POST['vendor_location'];
    $vendor_name = $_POST["v_name"];
    $vendor_email = $_POST["v_email"];
    $vendor_phone = $_POST["v_phone"];
    $vendor_city = $_POST["v_city"];
    $vendor_state = $_POST["v_state"];
    $vendor_district = $_POST["v_district"];
    $vendor_pin = $_POST["v_pin"];
    $vendor_description = $_POST["v_description"];


    $check_db = "SELECT * FROM erp_vendor_response WHERE rfqId = '$rfq_id' AND vendor_email = '$vendor_email'";
    $dataset=queryGet($check_db, true);

    $count = $dataset['numRows'];

    if($count == 0)
    {
 //Check if the total and lead_time
 $check = true;
 foreach($_POST['v_detail'] as $data_value) 
 {
     $each_value = explode("|",$data_value);
     if ($each_value['32'] <= 0 || $each_value['34'] <= 0) {
         $check = false;
         break;
     }
     else{
         $check = true;
     }
 }


 if($check == true)
 {
    //POST REQUEST
    $sql="INSERT INTO erp_vendor_response 
    SET 
    `vendor_code`='$vendor_code',
    `vendor_id`='$vendor_primary_id',
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

    queryInsert($sql);
   
    // print_r("success");

    //get ID
    $query="SELECT * FROM erp_vendor_response WHERE `unique_no`='$rand'";
    $dataset=queryGet($query, false);

    $id = $dataset['data']['erp_v_id'];

    // print_r($_POST['v_detail']);

    // $sql1='';
    foreach($_POST['v_detail'] as $data) 
    {
        $each = explode("|",$data);
        // print_r($each['1']);
        $item_id = $each['0'];
        $item_code = $each['1'];
        $item_name = $each['2'];
        $item_desc = $each['3'];
        $rq = $each['4'];
        $net_weight = $each['5'];
        $gross_weight = $each['6'];
        $unit = $each['7'];
        $volume = $each['8'];
        $volumeCubeCm = $each['9'];
        $height = $each['10'];
        $width = $each['11'];
        $length = $each['12'];
        $goodsType = $each['13'];
        $goodsGroup = $each['14'];
        $purchaseGroup = $each['15'];
        $branch = $each['16'];
        $availabilityCheck = $each['17'];
        $issueUnitMeasure = $each['18'];
        $uomRel = $each['19'];
        $storageBin = $each['20'];
        $pickingArea = $each['21'];
        $tempControl = $each['22'];
        $storageControl = $each['23'];
        $maxStoragePeriod = $each['24'];
        $maxStoragePeriodTimeUnit = $each['25'];
        $minRemainSelfLife = $each['26'];
        $minRemainSelfLifeTimeUnit = $each['27'];
        $purchasingValueKey = $each['28'];
        $quantity = $each['29'];
        $price = $each['30'];
        $discount = $each['31'];
        $total = $each['32'];
        $delivery_mode = $each['33'];
        $lead_time = $each['34'];
        $gst = $each['35'];
        $erp_v_id = $id;
        // $moq_diff_value = 0;

        if($rq >= $quantity)
        {
            $moq_diff_value = 1;
        }
        elseif($rq < $quantity)
        {
            $moq_diff_value = 2;
        }
                
        $sql1 = "INSERT INTO erp_vendor_item 
        SET 
        `item_id`='$item_id',
        `item_code`='$item_code',
        `item_name`='$item_name',
        `item_desc`='$item_desc',
        `rq`='$rq',
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
        `storageBin`='$storageBin',
        `pickingArea`='$pickingArea',
        `tempControl`='$tempControl',
        `storageControl`='$storageControl',
        `maxStoragePeriod`='$maxStoragePeriod',
        `maxStoragePeriodTimeUnit`='$maxStoragePeriodTimeUnit',
        `minRemainSelfLife`='$minRemainSelfLife',
        `minRemainSelfLifeTimeUnit`='$minRemainSelfLifeTimeUnit',
        `purchasingValueKey`='$purchasingValueKey',
        `moq`='$quantity',
        `price`='$price',
        `discount`='$discount',
        `total`='$total',
        `delivery_mode`='$delivery_mode',
        `lead_time`='$lead_time',
        `gst`='$gst',
        `moq_diff_value`='$moq_diff_value',
        `erp_v_id`='$erp_v_id'";

        $result = queryInsert($sql1);
    }
    $currentTime = date("Y-m-d H:i:s");
    $countrysql=queryGet("SELECT evd.*, ec.company_country FROM erp_vendor_details evd JOIN erp_companies ec ON evd.company_id = ec.company_id WHERE evd.vendor_id =$vendor_primary_id ")['data'];

    $companyid=$countrysql['company_id'];
    $locationid=$countrysql['location_id'];
    $company_branch_id=$countrysql['company_branch_id'];

    $country=$countrysql['company_country'];
    $componentsjsn = json_decode(getLebels($country)['data'], true);
    $businessTaxID = $componentsjsn['fields']['businessTaxID'];
    $taxNumber = $componentsjsn['fields']['taxNumber'];
    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
    $auditTrail = array();
    $auditTrail['basicDetail']['trail_type'] = 'MAILSEEN';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
    $auditTrail['basicDetail']['table_name'] = ERP_RFQ_LIST;
    $auditTrail['basicDetail']['column_name'] = 'rfqId'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $rfq_id;  // primary key
    $auditTrail['basicDetail']['document_number'] = $rfq_code;
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['party_id'] = 0;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = 'RFQ Response from Vendor';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    $auditTrail['basicDetail']['action_sqlQuery'] = '';
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = '';
    $auditTrail['basicDetail']['created_by'] = $vendor_name;
    $auditTrail['basicDetail']['updated_by'] = $vendor_name;
    $auditTrail['basicDetail']['location_id'] = $locationid;
    $auditTrail['basicDetail']['branch_id'] = $company_branch_id;
    $auditTrail['basicDetail']['company_id'] = $companyid;

    $auditTrail['action_data']['Vendor Details']['Vendor Name'] = $vendor_name;
    $auditTrail['action_data']['Vendor Details']['Vendor '.$taxNumber] = $vendor_pan;
    $auditTrail['action_data']['Vendor Details']['Vendor '.$businessTaxID] =$vendor_gst;
    $auditTrail['action_data']['Vendor Details']['Vendor Constofbusiness'] = $vendor_constofbusiness;
    $auditTrail['action_data']['Vendor Details']['Vendor Email'] = $vendor_email;
    $auditTrail['action_data']['Vendor Details']['Vendor Phone'] = $vendor_phone;
    
    foreach($_POST['v_detail'] as $data) 
    {
        $each = explode("|",$data);
        // print_r($each['1']);
        $item_id = $each['0'];
        $item_code = $each['1'];
        $item_name = $each['2'];
        $item_desc = $each['3'];
        $auditTrail['action_data']['Items'][$item_code]['Item Name'] = $item_name;
        $auditTrail['action_data']['Items'][$item_code]['Item Code'] = $item_code;
        $auditTrail['action_data']['Items'][$item_code]['Item Desc'] = $item_desc;
    }
    $auditTrail['action_data']['Response']['Send By'] = $vendor_name;
    $auditTrail['action_data']['Response']['Send At'] = formatDateTime($currentTime);
 $auditTrailreturn = generateAuditTrailByMail($auditTrail);
    $returnData = [
        "status" => "success",
        "message" => "Data saved successfully"
    ];
    echo json_encode($returnData);
}
else{
    $returnData = [
        "status" => "failed",
        "message" => "Please put the quantity, rate and lead time properly"
    ];
    echo json_encode($returnData);
}
}
else{
    $returnData = [
        "status" => "failed",
        "message" => "You already submitted the form."
    ];
    echo json_encode($returnData);
}
}
?>