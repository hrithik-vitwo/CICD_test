<?php
require_once dirname(__DIR__) . "/company/func-ChartOfAccounts.php";

class AccountingPostingjournal
{
  private $company_id;
  private $branch_id;
  private $location_id;
  private $compOpeningDate;
  private $created_by;
  private $updated_by;
  private $rollbackFlag = true;
  function __construct(bool $rollbackFlag = true)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $compOpeningDate;
    global $created_by;
    global $updated_by;
    $this->company_id = $company_id;
    $this->branch_id = $branch_id;
    $this->location_id = $location_id;
    $this->compOpeningDate = $compOpeningDate;
    $this->created_by = $created_by;
    $this->updated_by = $updated_by;
    $this->rollbackFlag = gettype($rollbackFlag) == 'boolean' ? $rollbackFlag : true;
  }


  private function getMonthsForPrevPosting($postingDate = null)
  {
    $postingDate = $postingDate != null ? $postingDate : date("Y-m-d");
    $startDate = new DateTime(date("Y-m-01", strtotime($postingDate)));
    $endDate = new DateTime(date("Y-m-01"));
    $months = [];
    $current = clone $startDate;
    while ($current <= $endDate) {
      $months[] = $current->format('Y-m');
      $current->modify('+1 month');
    }
    return $months;
  }

  function post($data = [])
  {
    $journalData = $data["journal"] ?? [];
    //validate data start:
    if (gettype($data) != 'array') {
      return [
        "status" => "warning",
        "message" => "Invalid data"
      ];
    } else {
      if (!isset($data["journal"]) || count($data["journal"]) <= 0) {
        return [
          "status" => "warning",
          "message" => "Journal data not found"
        ];
      }
      if (!isset($data["debit"]) || count($data["debit"]) <= 0) {
        return [
          "status" => "warning",
          "message" => "Debit data not found"
        ];
      }
      if (!isset($data["credit"]) || count($data["credit"]) <= 0) {
        return [
          "status" => "warning",
          "message" => "Credit data not found"
        ];
      }
    }

    if( new DateTime(date("Y-m-d", strtotime($this->compOpeningDate))) > new DateTime(date("Y-m-d", strtotime($journalData['postingDate']?? ""))) ){
      $journalData['postingDate'] = $this->compOpeningDate;
    }

    $totalCreditAmount = array_sum(array_column($data["credit"], "credit_amount")) + 0;
    $totalDebitAmount = array_sum(array_column($data["debit"], "debit_amount")) + 0;
  
    if (abs($totalCreditAmount) != abs($totalDebitAmount)) {
     // echo 1;
      return [
        "status" => "warning",
        "message" => "Debit and credit amount mismatched",
        "data" => $data
      ];
    }
    
   
    //validate data end:
    // ============================================
    $dbConObj = new Database(true);
    $dbConObj->setActionName("Accounting");
    $dbConObj->setSuccessMsg("Accounting Successfully Posted");
    $dbConObj->setErrorMsg("Accounting Posting Failure");


    $jv_no = 0;
    $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$this->company_id AND branch_id=$this->branch_id and location_id=$this->location_id order by id desc limit 1";
    $responc = $dbConObj->queryGet($sqlPrv);
    if ($responc['status'] == 'success') {
      $redata = $responc['data']['jv_no'];
      $jv_no = getJernalNewCode($redata);
    } else {
      $redata = '';
      $jv_no = getJernalNewCode($redata);
    }

    $openingBalUpdateMonths = $this->getMonthsForPrevPosting($journalData['postingDate']);
    $openingBalUpdateMonthsNum = count($openingBalUpdateMonths);
    $journalInsertObj = $dbConObj->queryInsert("INSERT INTO `" . ERP_ACC_JOURNAL . "` 
    SET
      `company_id`='" . $this->company_id . "',
      `branch_id`='" . $this->branch_id . "',
      `location_id`='" . $this->location_id . "',
      `parent_id`='" . $journalData['parent_id'] . "',
      `parent_slug`='" . $journalData['parent_slug'] . "',
      `refarenceCode`='" . $journalData['refarenceCode'] . "',
      `remark`='" . $journalData['remark'] . "',
      `party_code`='" . $journalData['party_code'] . "',
      `party_name`='" . $journalData['party_name'] . "',
      `jv_no`='" . $jv_no . "',
      `journalEntryReference`='" . $journalData['journalEntryReference'] . "',
      `documentNo`='" . $journalData['documentNo'] . "',
      `documentDate`='" . $journalData['documentDate'] . "',
      `postingDate`='" . $journalData['postingDate'] . "',
      `journal_created_by`='" . $this->created_by . "',
      `journal_updated_by`='" . $this->created_by . "'");

    $journal_id = $journalInsertObj["insertedId"];
    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
                $auditTrail = array();
                $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
                $auditTrail['basicDetail']['table_name'] = ERP_ACC_JOURNAL;
                $auditTrail['basicDetail']['column_name'] = 'id'; // Primary key column
                $auditTrail['basicDetail']['document_id'] = $journal_id;  // primary key
                $auditTrail['basicDetail']['document_number'] = $jv_no;
                $auditTrail['basicDetail']['action_code'] = $action_code;
                $auditTrail['basicDetail']['party_id'] = 0;
                $auditTrail['basicDetail']['action_referance'] = '';
                $auditTrail['basicDetail']['action_title'] = 'New Journal created';  //Action comment
                $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
                $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
                $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
                $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
                $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($journalInsertObj["query"]);
                $auditTrail['basicDetail']['others'] = '';
                $auditTrail['basicDetail']['remark'] = $journalData['remark'];
                $auditTrail['action_data']['Journal Details']['JV No'] =  $jv_no;
                $auditTrail['action_data']['Journal Details']['Document Number'] = $journalData['documentNo'];
                $auditTrail['action_data']['Journal Details']['For '] = $journalData['journalEntryReference'];
                $auditTrail['action_data']['Journal Details']['Refarance Code '] = $journalData['refarenceCode'];
                $auditTrail['action_data']['Journal Details']['postingDate'] = $journalData['postingDate'];
                $auditTrail['action_data']['Journal Details']['Document Date'] = $journalData['documentDate'];
                $auditTrail['action_data']['Journal Details']['created by'] = getCreatedByUser($this->created_by);
    foreach ($data["credit"] as $creditData) {

      $creditData['credit_amount'] = floatval(str_replace(',', '', $creditData['credit_amount']));
      $creditData['credit_amount'] = $creditData['credit_amount'] > 0 ? $creditData['credit_amount'] : 0;

      if ($creditData['credit_amount'] == 0 && $creditData['credit_amount'] == 0.00 && $creditData['credit_amount'] == '' && $creditData['credit_amount'] == null) {
        continue;
      } else {
        $dbConObj->queryInsert("INSERT INTO `" . ERP_ACC_CREDIT . "` 
              SET
                `journal_id`='" . $journal_id . "',
                `glId`='" . $creditData['glId'] . "',
                `subGlCode`='" . $creditData['subGlCode'] . "',
                `subGlName`='" . addslashes($creditData['subGlName']) . "',
                `credit_amount`='" . $creditData['credit_amount'] . "',
                `credit_remark`='" . $creditData['credit_remark'] . "',
                `credit_created_by`='" . $this->created_by . "',
                `credit_updated_by`='" . $this->created_by . "'");
                if($creditData['cr_cost_center']){
                  $insert_mapping = queryInsert("INSERT INTO `erp_jv_cost_and_profit_center_mapping` SET 
                  `jv_id`=$journal_id,
                  `cost_profit_id` = '".$creditData['cr_cost_center']."',
                  `map_type` = 'cost_center',
                  `activity` = 'credit',
                  `value`= '" . floatval(str_replace(',', '', $creditData['credit_amount'])) . "',
                  `created_by` = '" . $this->created_by . "',
                  `updated_by` ='" . $this->created_by . "'");
                 }
                if($creditData['cr_func_area']){
                  $insert_mapping_func=queryInsert("INSERT INTO `erp_jv_cost_and_profit_center_mapping` SET 
                  `jv_id`=$journal_id,
                  `cost_profit_id` = '".$creditData['cr_cost_center']."',
                  `functional_area`='".$creditData['cr_func_area']."',
                  `map_type` = 'functional_area',
                  `activity` = 'credit',
                  `value`= '" . floatval(str_replace(',', '', $creditData['credit_amount'])) . "',
                  `created_by` = '" . $this->created_by . "',
                  `updated_by` ='" . $this->created_by . "'");
                }


                // if($creditData['glId'] == 150){
                //   //grn_payment
                //   $customer = queryGet("SELECT * FROM `erp_customer` WHERE `customer_code` = '" . $creditData['subGlCode'] . "' AND `company_id` = '" .$this->company_id. "' ");
                //   $customer_id = $customer['data']['customer_id'];
                //   //insert to payment/collection table
  
                //   $insert_payment = queryInsert("INSERT INTO `erp_branch_sales_order_payments` 
                //                             SET
                //                             `company_id` = '.$this->company_id.',
                //                             `branch_id` = '.$this->branch_id.',
                //                             `location_id` = '.$this->location_id.',
                //                             `customer_id` = $customer_id,
                //                             `journal_id` = $journal_id,
                //                             `collect_payment` ='" . floatval(str_replace(',', '', $creditData['credit_amount'])) . "',
                //                             `created_by` = '" . $this->created_by . "',
                //                             `updated_by` ='" . $this->created_by . "'");
  
                // }

        if ($openingBalUpdateMonthsNum > 1) {
          foreach ($openingBalUpdateMonths as $monthKey => $oneMonth) {
            $prevCheckObj = $dbConObj->queryGet('SELECT * FROM `erp_opening_closing_balance` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $creditData['glId'] . ' AND `subgl`="' . $creditData['subGlCode'] . '"');
            if ($prevCheckObj["status"] == "success") {
              if ($monthKey == 0) {
                $dbConObj->queryUpdate('UPDATE `erp_opening_closing_balance` SET `closing_val`=`closing_val`-' . $creditData['credit_amount'] . ' WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $creditData['glId'] . ' AND `subgl`="' . $creditData['subGlCode'] . '"');
                // echo $oneMonth . "=>Change the closing balance\n";
              } elseif ($monthKey == $openingBalUpdateMonthsNum - 1) {
                $dbConObj->queryUpdate('UPDATE `erp_opening_closing_balance` SET `opening_val`=`opening_val`-' . $creditData['credit_amount'] . ' WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $creditData['glId'] . ' AND `subgl`="' . $creditData['subGlCode'] . '"');
                // echo $oneMonth . "=>Change the opening balance\n";
              } else {
                $dbConObj->queryUpdate('UPDATE `erp_opening_closing_balance` SET `opening_val`=`opening_val`-' . $creditData['credit_amount'] . ', `closing_val`=`closing_val`-' . $creditData['credit_amount'] . ' WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $creditData['glId'] . ' AND `subgl`="' . $creditData['subGlCode'] . '"');
                // echo $oneMonth . "=>Change the opening and closing balance both\n";
              }
            } else {
              if ($monthKey == 0) {
                $saveObj = $dbConObj->queryInsert('INSERT INTO `erp_opening_closing_balance` SET `closing_val`=' . $creditData['credit_amount'] * -1 . ', `company_id`=' . $this->company_id . ', `branch_id`=' . $this->branch_id . ', `location_id`=' . $this->location_id . ', `date` = "' . $oneMonth . '-01", `gl`=' . $creditData['glId'] . ', `subgl`="' . $creditData['subGlCode'] . '"');
                // echo $oneMonth . "=>add the closing balance\n";
              } elseif ($monthKey == $openingBalUpdateMonthsNum - 1) {
                $saveObj = $dbConObj->queryInsert('INSERT INTO `erp_opening_closing_balance` SET `opening_val`=' . $creditData['credit_amount'] * -1 . ', `company_id`=' . $this->company_id . ', `branch_id`=' . $this->branch_id . ', `location_id`=' . $this->location_id . ', `date` = "' . $oneMonth . '-01", `gl`=' . $creditData['glId'] . ', `subgl`="' . $creditData['subGlCode'] . '"');
                // echo $oneMonth . "=>add the opening balance\n";
              } else {
                $saveObj = $dbConObj->queryInsert('INSERT INTO `erp_opening_closing_balance` SET `opening_val`=' . $creditData['credit_amount'] * -1 . ', `closing_val`=' . $creditData['credit_amount'] * -1 . ', `company_id`=' . $this->company_id . ', `branch_id`=' . $this->branch_id . ', `location_id`=' . $this->location_id . ', `date` = "' . $oneMonth . '-01", `gl`=' . $creditData['glId'] . ', `subgl`="' . $creditData['subGlCode'] . '"');
                // echo $oneMonth . "=>add the opening and closing balance both\n";
              }
            }
          }
        }
      }
      $auditTrail['action_data']['Credit Details']['Sub Ledger'] = addslashes($creditData['subGlName']);
      $auditTrail['action_data']['Credit Details']['Credit Amount'] = $creditData['credit_amount']; 
    }
   

    foreach ($data["debit"] as $debitData) {
      $debitData['debit_amount'] = floatval(str_replace(',', '', $debitData['debit_amount']));
      $debitData['debit_amount'] = $debitData['debit_amount'] > 0 ? $debitData['debit_amount'] : 0;

      if ($debitData['debit_amount'] == 0 && $debitData['debit_amount'] == 0.00 && $debitData['debit_amount'] == '' && $debitData['debit_amount'] == null) {
        continue;
      } else {
        $dbConObj->queryInsert("INSERT INTO `" . ERP_ACC_DEBIT . "` 
            SET
              `journal_id`='" . $journal_id . "',
              `glId`='" . $debitData['glId'] . "',
              `subGlCode`='" . $debitData['subGlCode'] . "',
              `subGlName`='" . addslashes($debitData['subGlName']) . "',
              `debit_amount`='" . floatval(str_replace(',', '', $debitData['debit_amount'])) . "',
              `debit_remark`='" . $debitData['debit_remark'] . "',
              `debit_created_by`='" . $this->created_by . "',
              `debit_updated_by`='" . $this->created_by . "'");
              if($debitData['dr_cost_center']){
                $insert_mapping = queryInsert("INSERT INTO `erp_jv_cost_and_profit_center_mapping` SET 
                `jv_id`=$journal_id,
                `cost_profit_id` = '".$debitData['dr_cost_center']."',
                `map_type` = 'cost_center',
                `activity` = 'debit',
                `value`= '" . floatval(str_replace(',', '', $debitData['debit_amount'])) . "',
                `created_by` = '" . $this->created_by . "',
                `updated_by` ='" . $this->created_by . "'");
              }
              if($debitData['dr_func_area']){
                $insert_mapping_func=queryInsert("INSERT INTO `erp_jv_cost_and_profit_center_mapping` SET 
                `jv_id`=$journal_id,
                `cost_profit_id` = '".$debitData['dr_cost_center']."',
                `functional_area`='".$debitData['dr_func_area']."',
                `map_type` = 'functional_area',
                `activity` = 'debit',
                `value`= '" . floatval(str_replace(',', '', $debitData['debit_amount'])) . "',
                `created_by` = '" . $this->created_by . "',
                `updated_by` ='" . $this->created_by . "'");
              }
              // if($debitData['glId'] == 150){
              //   //grn_payment
              //   $vendor = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_code` = '" . $debitData['subGlCode'] . "' AND `company_id` = '" .$this->company_id. "' ");
              //   $vendor_id = $vendor['data']['vendor_id'];
              //   //insert to payment/collection table

              //   $insert_payment = queryInsert("INSERT INTO `erp_grn_payments` 
              //                             SET
              //                             `company_id` = '.$this->company_id.',
              //                             `branch_id` = '.$this->branch_id.',
              //                             `location_id` = '.$this->location_id.',
              //                             `vendor_id` = $vendor_id,
              //                             `journal_id` = $journal_id,
              //                             `collect_payment` ='" . floatval(str_replace(',', '', $debitData['debit_amount'])) . "',
              //                             `created_by` = '" . $this->created_by . "',
              //                               `updated_by` ='" . $this->created_by . "'");

                                          



              // }
        if ($openingBalUpdateMonthsNum > 1) {
          foreach ($openingBalUpdateMonths as $monthKey => $oneMonth) {

            $prevCheckObj = $dbConObj->queryGet('SELECT * FROM `erp_opening_closing_balance` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $debitData['glId'] . ' AND `subgl`="' . $debitData['subGlCode'] . '"');

            if ($prevCheckObj["status"] == "success") { 

              if ($monthKey == 0) {
                $dbConObj->queryUpdate('UPDATE `erp_opening_closing_balance` SET `updated_by`="Auto", `closing_val`=`closing_val`+' . $debitData['debit_amount'] . ' WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $debitData['glId'] . ' AND `subgl`="' . $debitData['subGlCode'] . '"');
                // echo $oneMonth . "=>Change the closing balance\n";
              } elseif ($monthKey == $openingBalUpdateMonthsNum - 1) {
                $dbConObj->queryUpdate('UPDATE `erp_opening_closing_balance` SET `updated_by`="Auto", `opening_val`=`opening_val`+' . $debitData['debit_amount'] . ' WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $debitData['glId'] . ' AND `subgl`="' . $debitData['subGlCode'] . '"');
                // echo $oneMonth . "=>Change the opening balance\n";
              } else {
                $dbConObj->queryUpdate('UPDATE `erp_opening_closing_balance` SET `updated_by`="Auto", `opening_val`=`opening_val`+' . $debitData['debit_amount'] . ', `closing_val`=`closing_val`+' . $debitData['debit_amount'] . ' WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND DATE_FORMAT(`date`, "%Y-%m") = "' . $oneMonth . '" AND `gl`=' . $debitData['glId'] . ' AND `subgl`="' . $debitData['subGlCode'] . '"');
                // echo $oneMonth . "=>Change the opening and closing balance both\n";
              }
            } else {
              if ($monthKey == 0) {
                $saveObj = $dbConObj->queryInsert('INSERT INTO `erp_opening_closing_balance` SET `closing_val`=' . $debitData['debit_amount'] . ', `company_id`=' . $this->company_id . ', `branch_id`=' . $this->branch_id . ', `location_id`=' . $this->location_id . ', `date` = "' . $oneMonth . '-01", `gl`=' . $debitData['glId'] . ', `subgl`="' . $debitData['subGlCode'] . '"');
                // echo $oneMonth . "=>add the closing balance\n";
              } elseif ($monthKey == $openingBalUpdateMonthsNum - 1) {
                $saveObj = $dbConObj->queryInsert('INSERT INTO `erp_opening_closing_balance` SET `opening_val`=' . $debitData['debit_amount'] . ', `company_id`=' . $this->company_id . ', `branch_id`=' . $this->branch_id . ', `location_id`=' . $this->location_id . ', `date` = "' . $oneMonth . '-01", `gl`=' . $debitData['glId'] . ', `subgl`="' . $debitData['subGlCode'] . '"');
                // echo $oneMonth . "=>add the opening balance\n";
              } else {
                $saveObj = $dbConObj->queryInsert('INSERT INTO `erp_opening_closing_balance` SET `opening_val`=' . $debitData['debit_amount'] . ', `closing_val`=' . $debitData['debit_amount'] . ', `company_id`=' . $this->company_id . ', `branch_id`=' . $this->branch_id . ', `location_id`=' . $this->location_id . ', `date` = "' . $oneMonth . '-01", `gl`=' . $debitData['glId'] . ', `subgl`="' . $debitData['subGlCode'] . '"');
                // echo $oneMonth . "=>add the opening and closing balance both\n";
              }
            }
          }
        }
      }
      $auditTrail['action_data']['Debit Details']['Sub Ledger'] = addslashes($debitData['subGlName']);
      $auditTrail['action_data']['Debit Details']['Debit Amount'] = $debitData['debit_amount']; 
    }

    $accPostingObj = $dbConObj->queryFinish();
    if ($accPostingObj["status"] == "success") {
      $auditTrailreturn = generateAuditTrail($auditTrail);
      $accPostingObj["journalId"] = $journal_id;
      $accPostingObj["data"] = $data;
    }
    return $accPostingObj;
  }
  function cost_center_post($data = []){

  }
}