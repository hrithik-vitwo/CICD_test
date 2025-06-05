<?php
require_once("app/v1/connection-branch-admin.php");
require_once("app/v1/functions/company/func-ChartOfAccounts.php");
require_once("app/v1/functions/branch/func-journal.php");

// $bplaceObj = queryGet("SELECT * FROM erp_acc_journal WHERE `parent_slug`='VendorDNGoods'", true);
// $bplaceObj = queryGet("SELECT * FROM erp_acc_journal WHERE `parent_slug`='CustomerDNGoods'", true);


// console($bplaceObj);
// foreach ($bplaceObj['data'] as $key => $custRow) {
//     if ($custRow['parent_id'] >0){
//          $journalId=$custRow['id'];
//          $dr_note_id=$custRow['parent_id'];
//         $updateInv = "UPDATE `erp_debit_note` 
//                         SET 
//                             `goods_journal_id`=$journalId
//                         WHERE dr_note_id=$dr_note_id";
//         $update= queryUpdate($updateInv);
//         console($update);

//     }else{
//         console("Not update-----".$custRow);
//     }
// }

// $bplaceObj = queryGet("SELECT * FROM erp_acc_journal WHERE `parent_slug`='VendorCNGoods'", true);
// $bplaceObj = queryGet("SELECT * FROM erp_acc_journal WHERE `parent_slug`='CustomerCNGoods'", true);


// console($bplaceObj);
// foreach ($bplaceObj['data'] as $key => $custRow) {
//     if ($custRow['parent_id'] >0){
//          $journalId=$custRow['id'];
//          $cr_note_id=$custRow['parent_id'];
//         $updateInv = "UPDATE `erp_credit_note` 
//                         SET 
//                             `goods_journal_id`=$journalId
//                         WHERE cr_note_id=$cr_note_id";
//         $update= queryUpdate($updateInv);
//         console($update);

//     }else{
//         console("Not update-----".$custRow);
//     }
// }


// ------------------------Add functional Mapping----------------------------------------
// for ($i = 2; $i <= 4; $i++) {

//     $dnvarientdata= "INSERT INTO `erp_acc_functional_mapping` (`company_id`, `function_name`, `slug`, `creditArray`, `debitArray`, `mapp_created_at`, `map_created_by`, `map_updated_at`, `map_updated_by`, `map_status`) VALUES ($i, 'Stock Posting Production Order', 'stockPostingProductionOrder', 'a:2:{i:0;s:3:\" 82\";i:1;s:3:\" 83\";}', 'a:2:{i:0;s:3:\"183\";i:1;s:3:\"184\";}', '2024-01-11 16:39:23', '$i|company', '2024-01-11 16:39:23', '$i|company', 'active'), ($i, 'Stock Posting Costcenter', 'stockPostingCostcenter', 'a:1:{i:0;s:3:\" 82\";}', 'a:1:{i:0;s:3:\"208\";}', '2024-01-11 16:39:23', '$i|company', '2024-01-11 16:39:23', '$i|company', 'active'), ($i, 'Stock Difference Book To Physical', 'stockDifferenceBookToPhysical', 'a:1:{i:0;s:2:\"82\";}', 'a:1:{i:0;s:3:\"241\";}', '2024-01-11 16:39:23', '$i|company', '2024-01-11 16:39:23', '$i|company', 'active')";

//     mysqli_query($dbCon, $dnvarientdata);

// }


// $bplaceObj = queryGet("SELECT * FROM erp_customer_address WHERE customer_address_state!=''", true);
// console($bplaceObj);


// foreach ($bplaceObj['data'] as $key => $custRow) {
//     $stateObj = queryGet("SELECT * FROM erp_gst_state_code WHERE (gstStateName='" . $custRow['customer_address_state'] . "' OR gstStateAlphaCode='" . $custRow['customer_address_state'] . "')");
//     console($stateObj);

//     $customer_address_id=$custRow['customer_address_id'];

//     if ($stateObj['status'] == 'success') {
//         $statecode = $stateObj['data']['gstStateCode'];

//         $updateInv = "UPDATE `erp_customer_address` 
//                     SET 
//                     `customer_address_state_code`='$statecode'
//                     WHERE customer_address_id=$customer_address_id";
//         $update= queryUpdate($updateInv);
//         console($update);
//     }
// }




// $bplaceObj = queryGet("SELECT * FROM erp_vendor_bussiness_places WHERE vendor_business_state!=''", true);
// console($bplaceObj);


// foreach ($bplaceObj['data'] as $key => $custRow) {
//     $stateObj = queryGet("SELECT * FROM erp_gst_state_code WHERE (gstStateName='" . $custRow['vendor_business_state'] . "' OR gstStateAlphaCode='" . $custRow['vendor_business_state'] . "') ");
//     console($stateObj);

//     $vendor_business_id=$custRow['vendor_business_id'];

//     if ($stateObj['status'] == 'success') {
//         $statecode = $stateObj['data']['gstStateCode'];

//         $updateInv = "UPDATE `erp_vendor_bussiness_places` 
//                     SET 
//                     `state_code`='$statecode'
//                     WHERE vendor_business_id=$vendor_business_id";
//         $update= queryUpdate($updateInv);
//         console($update);
//     }
// }





// $customerDetailsObj = queryGet("SELECT * FROM erp_branch_sales_order_invoices WHERE customerDetails='N;'", true);
// //  console($customerDetailsObj);

// foreach ($customerDetailsObj['data'] as $key => $custRow) {
//     console($custRow);
//     $customer_id = $custRow['customer_id'];
//     $inv_id= $custRow['so_invoice_id'];
//     $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,customer_code,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `erp_customer` WHERE `customer_id`='$customer_id'")['data'];
//     $customer_authorised_person_email = $customerDetailsObj['customer_authorised_person_email'];
//     $customer_authorised_person_phone = $customerDetailsObj['customer_authorised_person_phone'];
//     $customer_name = addslashes($customerDetailsObj['customer_name']);
//     $customerCode = $customerDetailsObj["customer_code"] ?? 0;
//     $customerParentGlId = $customerDetailsObj["parentGlId"] ?? 0;
//     $customerName = addslashes($customerDetailsObj['customer_name']);
//     $customer_Gst = $customerDetailsObj['customer_gstin'];

//     echo $customerDetailsSerialize = serialize($customerDetailsObj);

//     $updateInv = "UPDATE `erp_branch_sales_order_invoices` 
//                             SET 
//                             `customerDetails`='$customerDetailsSerialize'
//                          WHERE so_invoice_id=$inv_id";
//     queryUpdate($updateInv);

//     echo '<br>---------------------------------<br>';
// }



// for ($i = 1; $i <= 4; $i++) {
//     $cnvarientdata = "INSERT INTO `erp_cn_varient` (`id`, `company_id`, `flag_default`, `last_inv_no`, `title`, `verient_serialized`, `iv_number_example`, `seperator`, `reset_time`, `description`, `created_at`, `created_by`, `updated_at`, `updated_by`, `status`) 
//     VALUES (NULL, $i, '0', NULL, 'Default Variant', 'a:2:{s:6:\"prefix\";s:2:\"CN\";s:6:\"serial\";s:10:\"0000000001\";}', 'CN-0000000001', '-', 'never', '', '2023-04-17 09:43:23', '$i|company', '2023-04-17 09:43:23', '$i|company', '1')
//     ";
//     mysqli_query($dbCon, $cnvarientdata);
    
//     $dnvarientdata = "INSERT INTO `erp_dn_varient` (`id`, `company_id`, `flag_default`, `last_inv_no`, `title`, `verient_serialized`, `iv_number_example`, `seperator`, `reset_time`, `description`, `created_at`, `created_by`, `updated_at`, `updated_by`, `status`) 
//     VALUES (NULL, $i, '0', NULL, 'Default Variant', 'a:2:{s:6:\"prefix\";s:2:\"DN\";s:6:\"serial\";s:10:\"0000000001\";}', 'DN-0000000001', '-', 'never', '', '2023-04-17 09:43:23', '$i|company', '2023-04-17 09:43:23', '$i|company', '1')
//     ";
//     mysqli_query($dbCon, $dnvarientdata);

// }


///-------------------TDs Update -----------------------

// $selectUsedJour = queryGet("SELECT * FROM `erp_tds_details_2` ", true);
// console($selectUsedJour);
// foreach ($selectUsedJour['data'] as $key=> $Jourrow) {
//     $TDSRate=$Jourrow['TDSRate'];
    
//     echo $key;
//     console($Jourrow);
//     $id=$Jourrow['id'];
//     $slab_serializedarr=[
//         [0, null, $TDSRate],
//     ];
//     console ($slab_serializedarr);

//     echo $slab_serialized=serialize($slab_serializedarr);

//     $update=queryUpdate("UPDATE erp_tds_details_2 SET `slab_serialized` = '".$slab_serialized."' WHERE `id` = ".$id."");
//     console($update);
//     echo $id.'------------------------------------<br>';
// }


////---- GL Code to id update in In

// $selectUsedJour = queryGet("SELECT * FROM `erp_inventory_items` WHERE parentGlId LIKE'%100%'", true);
// // console($selectUsedJour);
// foreach ($selectUsedJour['data'] as $key=> $Jourrow) {
//     $itemId=$Jourrow['itemId'];
//     $company_idd=$Jourrow['company_id'];
//     $gl_code=$Jourrow['parentGlId'];
//     $tableName = "erp_acc_coa_" . $company_idd . "_table";
//     console($Jourrow);
//     $selectCoa = queryGet("SELECT * FROM " . $tableName . " WHERE gl_code=$gl_code");
//     echo $key;
//     console($selectCoa);
//     $glId=$selectCoa['data']['id'];

//     $update=queryUpdate("UPDATE erp_inventory_items SET `parentGlId` =$glId WHERE `itemId` = ".$itemId."");
//     console($update);
//     echo $glId.'------------------------------------<br>';
// }







// //////////////////This code for Dlt tranction function

// $selectUsedJour = queryGet("SELECT * FROM `erp_acc_journal` WHERE company_id=2", true);
// console($selectUsedJour);
// foreach ($selectUsedJour['data'] as $Jourrow) {
//     $jid=$Jourrow['id'];
//     $jjj=queryDelete("DELETE FROM `erp_acc_journal` WHERE `id` = $jid");
//     console($jjj);
//     $ddd= queryDelete("DELETE FROM `erp_acc_debit` WHERE `journal_id` = $jid");
//     console($ddd);
//     $ccc=queryDelete("DELETE FROM `erp_acc_credit` WHERE `journal_id` = $jid");
//     console($ccc);

//     console("-------------------------");
// }



/// /// This code for COA Modification And Number Generation
// for ($i = 1; $i <= 29; $i++) {
//     $tableName = "erp_acc_coa_" . $i . "_table";

//     $selectUsedCoa = queryGet("SELECT DISTINCT(temp_table.gl_id) FROM
//              (SELECT debit.glId AS gl_id FROM erp_acc_journal as journal INNER JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.company_id=1
//              UNION
//              SELECT credit.glId AS gl_id FROM erp_acc_journal as journal INNER JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.company_id=1) AS temp_table LEFT JOIN erp_acc_coa_1_table AS coa ON temp_table.gl_id=coa.id", true);

//     // console($selectUsedCoa);
//     foreach ($selectUsedCoa['data'] as $selectUsedCoarow) {
//         $selectCoaTxn=queryUpdate("UPDATE ".$tableName." SET `txn_status` = 1 WHERE `id` = ".$selectUsedCoarow['gl_id']."");
//         console($selectCoaTxn);
//     }

//     $selectCoa = queryGet("SELECT * FROM " . $tableName . "", true);

//     console($selectCoa);
//     $gl_code1='';
//     $gl_code2='';
//     $gl_code3='';
//     $gl_code4='';
//     foreach ($selectCoa['data'] as $coarow) {
//         console($coarow);
//         $selectCoa=queryUpdate("UPDATE ".$tableName." SET sp_id=p_id WHERE `id` = ".$coarow['id']."");
//         if ($coarow['glStType'] == 'group') {
//             $selectCoa=queryUpdate("UPDATE ".$tableName." SET `gl_code` ='' WHERE `id` = ".$coarow['id']."");
//             console($selectCoa);
//         } else {
             
//             $typeAcc = $coarow['typeAcc'];
//             if ($typeAcc == 1) {
//                 echo $gl_code1 = getCOASerialNumber($gl_code1, $typeAcc);
//                 $selectCoa=queryUpdate("UPDATE ".$tableName." SET `gl_code` =$gl_code1  WHERE `id` = ".$coarow['id']."");
//                 console($selectCoa);
//             }
//             if ($typeAcc == 2) {
//                 echo $gl_code2 = getCOASerialNumber($gl_code2, $typeAcc);
//                 $selectCoa=queryUpdate("UPDATE ".$tableName." SET `gl_code` =$gl_code2  WHERE `id` = ".$coarow['id']."");
//                 console($selectCoa);
//             }
//             if ($typeAcc == 3) {
//                 echo $gl_code3 = getCOASerialNumber($gl_code3, $typeAcc);
//                 $selectCoa=queryUpdate("UPDATE ".$tableName." SET `gl_code` =$gl_code3  WHERE `id` = ".$coarow['id']."");
//                 console($selectCoa);
//             }
//             if ($typeAcc == 4) {
//                echo $gl_code4 = getCOASerialNumber($gl_code4, $typeAcc);
//                 $selectCoa=queryUpdate("UPDATE ".$tableName." SET `gl_code` =$gl_code4  WHERE `id` = ".$coarow['id']."");
//                 console($selectCoa);
//             }
//         }
//     }
// }
