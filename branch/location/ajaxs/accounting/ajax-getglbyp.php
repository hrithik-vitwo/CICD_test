<?php 
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");


$responce= getAllChartOfAccounts_list_by_p($company_id,$_POST['accType']);
//print_r($responce);
// console($responce);
   
$returnData='';
if($responce["status"]=="success"){
     $returnData.='<option value="">Select GL</option>';
    foreach($responce["data"] as $responce){
        $glId = $responce["id"];
        // if($_POST['accType'] == 3){
        //     // echo $_POST['accType'] ;
        //     $check_prev_entry_dr  = queryGet("SELECT * FROM `erp_acc_debit` AS dr LEFT JOIN `erp_acc_journal` AS journal ON journal.id = dr.journal_id WHERE journal.company_id = $company_id AND dr.glId = $glId");
        //     // console($check_prev_entry_dr);
        //     $dr_num = $check_prev_entry_dr['numRows'];

        //     $check_prev_entry_cr = queryGet("SELECT * FROM `erp_acc_credit` AS cr LEFT JOIN `erp_acc_journal` AS journal ON journal.id = cr.journal_id WHERE journal.company_id = $company_id AND cr.glId = $glId ");

        //     $cr_num = $check_prev_entry_cr['numRows'];
           
       

        //     if($cr_num == 0 || $dr_num == 0){
               
            
               
        //         $returnData.='<option value="'.$responce["id"] .'" >'.$responce["gl_code"]."|".$responce["gl_label"].'</option>';
        //     }
        //     else{
                
        //         $returnData.='<option value="'.$responce["id"] .'" disabled = "true">'.$responce["gl_code"]."|".$responce["gl_label"].'</option>';
        //     }
        // }
      //  else{
            $returnData.='<option value="'.$responce["id"] .'">'.$responce["gl_code"]."|".$responce["gl_label"].'</option>';
       // }
        
         
    }
}else{
    $returnData.='<option value="">Select GL  </option>';
}
echo $returnData;

?>