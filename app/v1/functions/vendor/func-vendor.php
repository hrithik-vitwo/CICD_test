<?php


function getAllDataVendor($vendor_id) 
{
    global $dbCon;
    $returnData = [];
  $sql = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "`  WHERE `vendor_id`=$vendor_id";
    if ($res = mysqli_query($dbCon, $sql)) {
        if (mysqli_num_rows($res) > 0) {
            $returnData['status'] = "success";
            $returnData['message'] = "Data found";
            $returnData['data'] = mysqli_fetch_all($res, MYSQLI_ASSOC);
        } else {
            $returnData['status'] = "warning";
            $returnData['message'] = "Data not found";
            $returnData['data'] = [];
        }
    } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData['data'] = [];
    }
    return $returnData;
}
function saveCustomerSettings($POST){
   //console($POST);
//exit();
    $isValidate = validate($POST, [
        "name" => "required",
        "email" => "required",
        "phone" => "required",
        "code" => "required",
        "gst" => "required",
        "pan" => "required",
        "trade_name" => "required",
        
        "const" => "required",

    ]);

    if ($isValidate["status"] != "success") {
        $returnData['status'] = "warning";
        $returnData['message'] = "Invalid form inputes";
        $returnData['errors'] = $isValidate["errors"];
        return $returnData;
    }

    $trade_name = $POST['trade_name'];
    $name = $POST["name"];
    $email = $POST["email"];
    $phone = $POST["phone"];
    $code = $POST["code"];
    $gst = $POST["gst"];
    $pan = $POST["pan"];
    $website = $POST["website"];
    $currency = $POST["currency"];
    $credit = $POST["credit"];
    $opening_balance = $POST["opening_balance"];
 
  $const= $POST["const"];
    $vendor_id = $POST['vendor_id'];
   $picture = "";
   $picture = uploadFile($POST["profile_photo"], "../public/storage/picture/",["jpg","jpeg","png"]);
   if($picture['status']=='success'){
    $picture=$picture['data'];
   }else{
    $picture='';
   }
 $picture;

//echo $favicon;

    $ins = "UPDATE`" . ERP_VENDOR_DETAILS . "` 
    SET
    `vendor_code`='" . $code . "',
    `trade_name`='" .$trade_name. "',
    `vendor_gstin`='" . $gst . "',
    `vendor_pan`='" .  $pan  . "',
    `vendor_website`='" . $website . "',
    `constitution_of_business`='" . $const . "',
    `vendor_authorised_person_name`= '".$name."', 
    `vendor_authorised_person_email`='" . $email . "',
    `vendor_authorised_person_phone` ='".$phone."',
    `vendor_opening_balance`='".$opening_balance."',
    `vendor_currency`='".$currency."',
    `vendor_credit_period`='".$credit."',
    `vendor_picture`='".$picture."'
    WHERE `vendor_id`=$vendor_id";
    
    $returnData = queryUpdate($ins);

   return $returnData;

}
// Vendor address by Id
function getVendorAddressById($id)
{
    $sql = "SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`= $id AND `vendor_business_primary_flag` = 1";
    $queryRes = queryGet($sql);

    if ($queryRes['numRows'] > 0) {
        $data = $queryRes['data'];

        $res = "Build " . $data['vendor_business_building_no'] . "<br>" .
               "Flat No. " . $data['vendor_business_flat_no'] . ", " . $data['vendor_business_street_name'] . "<br>" .
               $data['vendor_business_location'] . ", " . $data['vendor_business_city'] . ", " . $data['vendor_business_district'] . " " . $data['vendor_business_pin_code'] . "<br>" .
               "State Name: " . $data['vendor_business_state'];

        return $res;
    }

    return "";
}

// function getVendorBuisnessAddress($id) {
//     $table = 'erp_vendor_bussiness_places';
//     $columns = [
//         'vendor_business_building_no',
//         'vendor_business_flat_no',
//         'vendor_business_street_name',
//         'vendor_business_pin_code',
//         'vendor_business_location',
//         'vendor_business_district',
//         'vendor_business_state'
//     ];
//     $whereCondition = "vendor_id=$id"; 
    
//     $sql = "SELECT " . implode(", ", $columns) . " FROM `$table` WHERE $whereCondition LIMIT 1";
//     $queryRes = queryGet($sql);
    
//     if ($queryRes['numRows'] > 0) {
//         $data = $queryRes['data'];
        
//         $addressParts = array_filter(array_map('trim', $data), function($value) {
//             return $value !== null && $value !== '';
//         });
        
//         return implode(', ', $addressParts);
//     }
//     return "";
// }

function ChangeStatusVendor($data = [], $tableKeyField = "", $tableStatusField = "status")
{
    global $dbCon;
    $tableName = ERP_VENDOR_DETAILS;
    $returnData["status"] = null;
    $returnData["message"] = null;
    if (!empty($data)) {
        $id = isset($data["id"]) ? $data["id"] : 0;
        $prevSql = "SELECT * FROM `" . $tableName . "` WHERE `" . $tableKeyField . "`='" . $id . "'";
        $prevExeQuery = mysqli_query($dbCon, $prevSql);
        $prevNumRecords = mysqli_num_rows($prevExeQuery);
        if ($prevNumRecords > 0) {
            $prevData = mysqli_fetch_assoc($prevExeQuery);
            $newStatus = "deleted";
            if ($data["changeStatus"] == "active_inactive") {
                $newStatus = ($prevData[$tableStatusField] == "active") ? "inactive" : "active";
                if ($prevData[$tableStatusField] == "guest") {
                    $newStatus = "active";
                }
                
            }
            $changeStatusSql = "UPDATE `" . $tableName . "` SET `" . $tableStatusField . "`='" . $newStatus . "' WHERE `" . $tableKeyField . "`=" . $id;
            if (mysqli_query($dbCon, $changeStatusSql)) {
                $returnData["status"] = "success";
                $returnData["message"] = "Status has been changed to " . strtoupper($newStatus);
            } else {
                $returnData["status"] = "error";
                $returnData["message"] = "Something went wrong, Try again...!";
            }
            $returnData["changeStatusSql"] = $changeStatusSql;
        } else {
            $returnData["status"] = "warning";
            $returnData["message"] = "Something went wrong, Try again...!";
        }
    } else {
        $returnData["status"] = "warning";
        $returnData["message"] = "Please provide all valid data...!";
    }
    return $returnData;
}

?>