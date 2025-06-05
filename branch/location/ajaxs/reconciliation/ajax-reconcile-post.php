<?php
include_once("../../../../app/v1/connection-branch-admin.php");

$responseData = [];

$reconMonth = date('m');
$reconYear = date('Y');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    global $created_by;
   //  console($_POST);
   //  exit();

$recon = $_POST['reconData'];
$open = $_POST['openData'];

foreach($open as $open){
$opening_bal = $open['loc_opening_balance'] ?? 0;
$opening_date = $open['loc_opening_date'] ?? 0;
$party_opening = $open['opening_balance'] ?? 0;
$party_opening_date = $open['opening_date'] ?? 0;

// console($recon);
// console($open);
// exit();

$insert_opening = queryInsert("INSERT INTO `erp_partner_reconciliation` SET
`company_id`=$company_id,
`branch_id`=$branch_id,
`location_id`=$location_id,
`statement_date`='".$opening_date."',
`partner_statement_date`='".$party_opening_date."',
`statement_transaction`='".$transaction."',
`partner_statement_transaction`='".$partner_transaction."',
`opening_balance`='".$opening_bal."',
`partner_opening_balance`='".$party_opening."',
`reconcile_percentage`='".$matchedConditions."',
`created_by`='".$created_by."',
`updated_by`='".$created_by."'
");

}

foreach($recon as $reconcile){


// console($reconcile);
    
   
   $date = $reconcile['statementDate'] ?? 0;
   $partner_date = $reconcile['localStatementDate'] ?? 0;
   $transaction = $reconcile['statementTransaction'];
   $partner_transaction = $reconcile['localStatementTransaction'] ?? 0;
   $debit = $reconcile['statementDebit'] ?? 0;
   $partner_debit = $reconcile['localStatementDebit'] ?? 0;
   $credit = $reconcile['statementCredit'] ?? 0;
   $partner_credit = $reconcile['localStatementCredit'] ?? 0;
   $journal_id = $reconcile['statementId'] ?? 0;
   $matchedConditions = $reconcile['matchedConditions'] ?? 0;
   $partyStatementId = $reconcile['partyStatementId'] ?? 0;
   $doc_no = $reconcile['statementDocument'];
   $party_doc_no = $reconcile['localStatementDocument'];

   $insert = queryInsert("INSERT INTO `erp_partner_reconciliation` SET
                            `journal_id`=$journal_id,
                            `company_id`=$company_id,
                            `branch_id`=$branch_id,
                            `location_id`=$location_id,
                            `statement_date`='".$date."',
                            `partner_statement_date`='".$partner_date."',
                            `doc_no` = '".$doc_no."',
                            `partner_doc_no` = '".$party_doc_no."',
                            `statement_transaction`='".$transaction."',
                            `partner_statement_transaction`='".$partner_transaction."',
                            `statement_credit`='".$credit."',
                            `partner_statement_credit`='".$partner_credit."',
                            `statement_debit`='".$debit."',
                            `partner_statement_debit`='".$partner_debit."',
                            `reconcile_percentage`='".$matchedConditions."',
                            `created_by`='".$created_by."',
                            `updated_by`='".$created_by."'
                            ");
                           //// console($insert);
                           // exit();
                        //    $update = queryInsert("UPDATE `erp_acc_journal` SET `reconcile_status` = 1 WHERE `id`=$journal_id ");
                        //   // console($update);

                           
                        //    $update_partner = queryInsert("UPDATE `erp_vendor_customer_reconciliation` SET `recon_journal_id` = $journal_id WHERE `id`= $partyStatementId");
                           //console($update_partner);
                          // exit();

                          // exit();

                            if($insert['status'] == 'success'){

                                $update = queryUpdate("UPDATE `erp_acc_journal` SET `reconcile_status` = 1 WHERE `id`=$journal_id");
                              // console($update);
                                 if($update['status'] == 'success'){

                                    $update_partner_sql = "UPDATE `erp_vendor_customer_reconciliation` SET `recon_journal_id` = $journal_id WHERE `id`= $partyStatementId";
                                    $update_partner = queryUpdate($update_partner_sql);
                                  // console($update_fun);
                                  

                                    if($update_partner['status'] == 'success'){

                                       $returnData['status'] = "Success";
                                       $returnData['message'] = "Successful";

                                    }
                                    else{
                                       $returnData['status'] = "Warning";
                                       $returnData['message'] = "something went wrong";  
                                    }
                                 }
                                 else{
                                    $returnData['status'] = "Warning";
                                    $returnData['message'] = "something went wrong";

                                 }
                                  
                            }
                            else{

                                $returnData['status'] = "Warning";
                                 $returnData['message'] = "something went wrong";

                            }


}
}
?>