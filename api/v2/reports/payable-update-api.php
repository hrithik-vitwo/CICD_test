<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization,  X-Requested-With");
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/lib/jwt/autoload.php");
require_once("../../../app/v1/lib/validator/autoload.php");
//$company_id = 5;
$returnData = [];
//echo $company_id;

// $company_date = queryGet("SELECT `erp_co` ")
$company_opening_query = queryGet('SELECT `opening_date` FROM `erp_companies` WHERE `company_id`=' . $company_id);
$compOpeningDate = $company_opening_query["data"]["opening_date"];
console($company_opening_query);


$vendors = queryGet("SELECT opening.*,vend.vendor_id AS vendorId,vend.vendor_created_at AS vendor_created_at  FROM `erp_opening_closing_balance` AS opening INNER JOIN `erp_vendor_details` AS vend ON vend.vendor_code = opening.subgl  WHERE opening.`company_id` = $company_id AND opening.`date` = '".$compOpeningDate ."' AND opening.`gl` = 150 AND vend.company_id = $company_id",true);

console($vendors);


//console($vendors);

foreach($vendors['data'] as $vendor){
console($customer);

    $vendor_id = $vendor['vendorId'];
    $vendor_opening = $vendor['opening_val'];
    $date = $vendor['vendor_created_at'];

    

    $inv = queryGet("SELECT SUM(`grnTotalAmount`) AS total_amount FROM `erp_grninvoice` WHERE `vendorId` = $vendor_id AND `companyId` = $company_id AND `grnType` = 'migration'");

    console($inv);


    //console($inv);

    if($inv['data']['total_amount'] != '' || $inv['data']['total_amount'] != null  ){
     
      $inv_amount = $inv['data']['total_amount'];
    }
    else{
        $inv_amount = 0;
    }

    $balance =  $vendor_opening - $inv_amount ;

//     $insvalidation = queryInsert("INSERT INTO `erp_payable_balance`
//                                 SET 
//                                     `company_id`='$company_id',
//                                     `branch_id`='$branch_id',
//                                     `location_id`='$location_id',
//                                     `vendor_id`='$vendor_id',
//                                     `vendor_amount`='$vendor_opening',
//                                     `invoice_amount`='$inv_amount',
//                                     `balance`='$balance',
//                                     `date`='".$date."' 
//                                     ");

//                               console($insvalidation);
                                   

//             if($insvalidation['status'] == 'success'){

//                 $returnData['status'] = 'Success';
//                 $returnData['message'] = 'Inserted Successfully';

                

//             }
//             else{
//                 $returnData['status'] = 'Warning';
//                 $returnData['message'] = 'Insertion Failed';
//                 $returnData['data'] = $insvalidation['sql'];
        
//             }

// console($returnData);

}

exit();
return $returnData;

?>