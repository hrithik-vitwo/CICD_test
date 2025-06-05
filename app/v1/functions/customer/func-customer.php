<?php


function getAllDataCustomer($customer_id) 
{
    global $dbCon;
    $returnData = [];
  $sql = "SELECT * FROM `" . ERP_CUSTOMER . "`  WHERE `customer_id`=$customer_id";
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
//    console($POST);
// // exit();
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
    $designation = $POST["desg"];
 
  $const= $POST["const"];
    $customer_id = $POST['customer_id'];
   $picture = "";
   $picture = uploadFile($POST["profile_photo"], "../public/storage/picture/",["jpg","jpeg","png"]);
   if($picture['status']=='success'){
    $picture=$picture['data'];
   }else{
    $picture='';
   }
//echo $logo;


//echo $favicon;

   echo $ins = "UPDATE`" . ERP_CUSTOMER . "` 
    SET
    `customer_code`='" . $code . "',
    `trade_name`='" .$trade_name. "',
    `customer_gstin`='" . $gst . "',
    `customer_pan`='" .  $pan  . "',
    `customer_website`='" . $website . "',
    `customer_picture`='".$picture."',
    `constitution_of_business`='" . $const . "',
    `customer_authorised_person_name`= '".$name."', 
    `customer_authorised_person_email`='" . $email . "',
    `customer_authorised_person_phone` ='".$phone."',
    `customer_authorised_person_designation`='".$designation."'
    WHERE `customer_id`='".$customer_id."'";
    
    $returnData = queryUpdate($ins);
 //exit();
   return $returnData;

}

?>