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
$wrong_sl_query = queryGet("SELECT * FROM erp_extra_sub_ledger AS sl LEFT JOIN erp_acc_coa_11_table AS coa ON sl.parentGlId = coa.id WHERE sl.`company_id` = $company_id AND coa.lock_status = 1",true);
//console($wrong_sl_query);
//$compOpeningDate = $company_opening_query["data"]["opening_date"];

foreach($wrong_sl_query['data'] as $data){
    $sl_code = $data['sl_code'];
    $gl_id = $data['parentGlId'];

    $select_dr = queryGet("SELECT * FROM erp_acc_debit AS dr LEFT JOIN erp_acc_journal AS journal ON dr.journal_id = journal.id WHERE journal.company_id = $company_id AND dr.subGlCode = '$sl_code'",true);
   // console($select_dr);
   foreach($select_dr['data'] as $dr){
    $dr_id = $dr['debit_id'];

    $update_dr = queryUpdate("UPDATE erp_acc_debit SET `subGlCode` = ' ',`subGlName` = ' ' WHERE `debit_id` = $dr_id");

    if($update_dr['status'] != 'success'){
        $returnData['status'] = 'warning';
        $returnData['query'] = $update_dr;
        return $returnData;

    }

   }
    
    $select_cr = queryGet("SELECT * FROM erp_acc_credit AS cr LEFT JOIN erp_acc_journal AS journal ON cr.journal_id = journal.id WHERE journal.company_id = $company_id AND cr.subGlCode = '$sl_code'",true);
    //console($select_cr);
    foreach($select_cr['data'] as $cr){
        $cr_id = $dr['credit_id'];
        $update_cr = queryUpdate("UPDATE erp_acc_credit SET `subGlCode` = ' ',`subGlName` = ' ' WHERE `credit_id` = $cr_id");


        if($update_cr['status'] != 'success'){
            $returnData['status'] = 'warning';
            $returnData['query'] = $update_cr;
            return $returnData;
    
        }
    
    }


}


// $customers = queryGet("SELECT opening.*,cust.customer_id AS customerId,cust.customer_created_at AS customer_created_at  FROM `erp_opening_closing_balance` AS opening INNER JOIN `erp_customer` AS cust ON cust.customer_code = opening.subgl  WHERE opening.`company_id` = $company_id AND opening.`date` = '".$compOpeningDate ."' AND opening.`gl` = 88",true);


//console($customers);

// foreach($customers['data'] as $customer){
//     //console($customer);

//     $customer_id = $customer['customerId'];
//     $customer_opening = $customer['opening_val'];
//     $date = $customer['customer_created_at'];

    

//     $inv = queryGet("SELECT SUM(`all_total_amt`) AS total_amount FROM `erp_branch_sales_order_invoices` WHERE `customer_id` = $customer_id AND `company_id` = $company_id AND `type` = 'migration'");


//     //console($inv);

//     if($inv['data']['total_amount'] != '' || $inv['data']['total_amount'] != null  ){
     
//       $inv_amount = $inv['data']['total_amount'];
//     }
//     else{
//         $inv_amount = 0;
//     }

//     $balance =  $customer_opening - $inv_amount ;

//     $insvalidation = queryInsert("INSERT INTO `erp_receiveable_balance`
//                                 SET 
//                                     `company_id`='$company_id',
//                                     `branch_id`='$branch_id',
//                                     `location_id`='$location_id',
//                                     `customer_id`='$customer_id',
//                                     `customer_amount`='$customer_opening',
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

// }


return 'ok';

?>