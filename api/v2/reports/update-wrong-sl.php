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

//for glID -> 243 and 172
$dr_wrong = queryGet("SELECT * FROM `erp_acc_debit` AS cr LEFT JOIN `erp_acc_journal` AS journal ON journal.id = cr.journal_id WHERE journal.company_id = 11 AND (cr.glId = 243 OR cr.glId = 172) AND cr.subGlCode != ''",true);
 console($dr_wrong);

 exit();

//  foreach($dr_wrong['data'] as $dr){
//    echo  $dr_id = $dr['debit_id'];
  
//      $update_dr = queryUpdate("UPDATE erp_acc_debit SET `subGlCode` = ' ',`subGlName` = ' ' WHERE `debit_id` = $dr_id");
//      //console($update_dr);

//     if($update_dr['status'] != 'success'){
//                 $returnData['status'] = 'warning';
//                 $returnData['query'] = $update_dr;
//                 return $returnData;
        
//             }

//  }
// exit();

$cr_wrong = queryGet("SELECT * FROM `erp_acc_credit` AS cr LEFT JOIN `erp_acc_journal` AS journal ON journal.id = cr.journal_id WHERE journal.company_id = 11 AND (cr.glId = 243 OR cr.glId = 172) AND cr.subGlCode != ''",true);
// console($cr_wrong);
foreach($cr_wrong['data'] as $cr){
    $cr_id = $cr['credit_id'];
    $update = queryUpdate("UPDATE erp_acc_credit SET `subGlCode` = ' ',`subGlName` = ' ' WHERE `credit_id` = $cr_id");
    console($update);
    if($update['status'] != 'success'){
                $returnData['status'] = 'warning';
                $returnData['query'] = $update;
                return $returnData;
        
            }
    
 }
 
echo "ok"; 
?>