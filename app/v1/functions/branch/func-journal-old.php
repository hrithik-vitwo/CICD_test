<?php
require_once dirname(__DIR__) . "/company/func-ChartOfAccounts.php";
require_once "func-journal-posting.php";

class Accounting
{

  private function getChartOfAccountsDataDetailsByKeyArr($keys = null)
  {
    return queryGet('SELECT * FROM `' . ERP_ACC_CHART_OF_ACCOUNTS . '` WHERE `status`!="deleted" AND `id` IN (' . $keys . ')');
  }
  private function getChartOfAccountsDataDetailsByKey($key)
  {
    return queryGet('SELECT * FROM `' . ERP_ACC_CHART_OF_ACCOUNTS . '` WHERE `status`!="deleted" AND `id` =' . $key . '');
  }

  private function getCreditDebitAccountsList($functionSlug = null)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    $creditAccountsList = [];
    $debitAccountsList = [];
    $accMappingObj = queryGet('SELECT * FROM `' . ERP_ACC_FUNCTIONAL_MAPPING . '` WHERE `company_id`=' . $company_id . ' AND `slug` = "' . $functionSlug . '"');
    if ($accMappingObj["status"] == "success") {
      $creditAccIdListArr = unserialize($accMappingObj['data']['creditArray']);
      $debitAccIdListArr = unserialize($accMappingObj['data']['debitArray']);
      foreach ($creditAccIdListArr as $crkey => $crvalue) {
        $creditAccountsList[$crkey] = $this->getChartOfAccountsDataDetailsByKey($crvalue)["data"];
      }
      foreach ($debitAccIdListArr as $drkey => $drvalue) {
        $debitAccountsList[$drkey] = $this->getChartOfAccountsDataDetailsByKey($drvalue)["data"];
      }



      /* Rachhel Code
      $creditAccIdListArrStr = implode(",", $creditAccIdListArr);
      if ($creditAccIdListArrStr !== "") {
        $creditAccountsListTemp = $this->getChartOfAccountsDataDetailsByKeyArr($creditAccIdListArrStr);
        if ($creditAccountsListTemp["status"] == "success") {
          $creditAccountsList = $creditAccountsListTemp["data"];
        }
      }
      $debitAccIdListArrStr = implode(",", $debitAccIdListArr);
      if ($debitAccIdListArrStr !== "") {
        $debitAccountsListTemp = $this->getChartOfAccountsDataDetailsByKeyArr($debitAccIdListArrStr);
        if ($debitAccountsListTemp["status"] == "success") {
          $debitAccountsList = $debitAccountsListTemp["data"];
        }
      }*/
    }
    if (count($creditAccountsList) > 0 || count($debitAccountsList) > 0) {
      return [
        "status" => "success",
        "message" => "Credit & debit accounts fetched successfully",
        "creditAccountsList" => $creditAccountsList,
        "debitAccountsList" => $debitAccountsList
      ];
    } else {
      return [
        "status" => "warning",
        "message" => "Credit & debit accounts not found",
        "creditAccountsList" => $creditAccountsList,
        "debitAccountsList" => $debitAccountsList
      ];
    }
  }


  function grnAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];

    $isValidate = validate($inputes, [
      "grnItemList" => "array"
    ], [
      "grnItemList" => "Required"
    ]);

    if ($isValidate["status"] == "success") {
      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $jv_no = 0;
      $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
      $responc = queryGet($sqlPrv);
      if ($responc['status'] == 'success') {
        $redata = $responc['data']['jv_no'];
        $jv_no = getJernalNewCode($redata);
      } else {
        $redata = '';
        $jv_no = getJernalNewCode($redata);
      }

      $party_code = $inputes['party_code'] ?? "";
      $party_name = $inputes['party_name'] ?? "";

      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $inputes['referenceNo'] . "',
                  `remark`='" . $inputes['remarks'] . "',
                  `party_code`='" . $party_code . "',
                  `party_name`='" . $party_name . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $inputes['journalEntryReference'] . "',
                  `documentNo`='" . $inputes['documentNo'] . "',
                  `documentDate`='" . $inputes['documentDate'] . "',
                  `postingDate`='" . $inputes['invoicePostingDate'] . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";

      $rtn = queryInsert($journalins);

      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];
        $drItems = $inputes['grnItemList'];

        foreach ($drItems  as $drkey => $drvalue) {
          $insdr = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $drvalue['parentGlId'] . "',
                        `subGlCode`='" . $drvalue['itemCode'] . "',
                        `subGlName`='" . $drvalue['itemName'] . "',
                        `debit_amount`='" . $drvalue['itemTotalPrice'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";

          queryInsert($insdr);

          $inscr = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $creditAccList[0]['id'] . "',
                        `credit_amount`='" . $drvalue['itemTotalPrice'] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";

          queryInsert($inscr);
        }
        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["journalId"] = $journal_id;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = "";
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }



  // 20-12-2022 18:16
  function grnIvAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];
    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "grIrItems" => "array",
      "taxDetails" => "array",
    ], [
      "BasicDetails" => "Required",
      "grIrItems" => "Required",
      "taxDetails" => "Required"
    ]);

    if ($isValidate["status"] == "success") {
      $grnDetails = $inputes["BasicDetails"];
      $grnItems = $inputes["grIrItems"];
      $vendorDetails = $inputes['vendorDetails'];
      $imputgst = $inputes['taxDetails'];
      $imputtds = $inputes['tdsDetails'];

      $grnPostingJournalId = $grnDetails["grnJournalId"];
      $accountingDocumentNo = $grnDetails["documentNo"];
      $accountingRefNo = $grnDetails["reference"];
      $accountingRemarks =  $grnDetails['remarks'];
      $journalEntryReference = $grnDetails['journalEntryReference'];
      $documentDate = $grnDetails['documentDate'];
      $documentPostingDate = $grnDetails['postingDate'];

      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $jv_no = 0;
      $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
      $responc = queryGet($sqlPrv);
      if ($responc['status'] == 'success') {
        $redata = $responc['data']['jv_no'];
        $jv_no = getJernalNewCode($redata);
      } else {
        $redata = '';
        $jv_no = getJernalNewCode($redata);
      }

      $party_code = $vendorDetails['vendorCode'] ?? "";
      $party_name = $vendorDetails['vendorName'] ?? "";

      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $accountingRefNo . "',
                  `remark`='" . $accountingRemarks . "',
                  `party_code`='" . $party_code . "',
                  `party_name`='" . $party_name . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $journalEntryReference . "',
                  `documentNo`='" . $accountingDocumentNo . "',
                  `documentDate`='" . $documentDate . "',
                  `postingDate`='" . $documentPostingDate . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";

      $rtn = queryInsert($journalins);

      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];
        $totalamount = 0;
        $tdstotal = 0;
        foreach ($grnItems  as $drkey => $drvalue) {
          $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $debitAccList[0]['id'] . "',
                        `debit_amount`='" . $drvalue . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";
          queryInsert($ins);
          $totalamount += $drvalue;
        }
        if (!empty($imputgst['igst']) && ($imputgst['igst'] > 0)) {
          //-------------------------IGST------------------------
          $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $debitAccList[1]['id'] . "',
                        `debit_amount`='" . $imputgst['igst'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`= '" . $created_by . "'";
          queryInsert($ins);
          $totalamount += $imputgst['igst'];
        } else {
          //-------------------------CGST------------------------
          $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $debitAccList[2]['id'] . "',
                        `debit_amount`='" . $imputgst['cgst'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";
          queryInsert($ins);
          $totalamount += $imputgst['cgst'];
          //-------------------------SGST------------------------
          $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $debitAccList[3]['id'] . "',
                        `debit_amount`='" .  $imputgst['sgst'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";
          queryInsert($ins);
          $totalamount += $imputgst['sgst'];
        }

        // if (isset($inputes['tdsDetails']) && !empty($inputes['tdsDetails'])) {
        //   $tdstotal = array_sum($inputes['tdsDetails']);
        //   $insTds = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
        //             SET
        //                 `journal_id`='" . $journal_id . "',
        //                 `glId`='" . $creditAccList[1]['id'] . "',
        //                 `credit_amount`='" . $tdstotal . "',
        //                 `credit_created_by`='" . $created_by . "',
        //                 `credit_updated_by`='" . $created_by . "'";
        //   queryInsert($insTds);
        //   $totalamount -= $tdstotal;
        // }
        //-------------------Vendor G/L-----------------------------
        $ins = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
        SET
            `journal_id`='" . $journal_id . "',
            `glId`='" . $creditAccList[0]['id'] . "',
            `subGlCode`='" . $vendorDetails['vendorCode'] . "',
            `subGlName`='" . $vendorDetails['vendorName'] . "',
            `credit_amount`='" . $totalamount . "',
            `credit_created_by`='" . $created_by . "',
            `credit_updated_by`='" . $created_by . "'";
        queryInsert($ins);

        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["journalId"] = $journal_id;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = '';
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }



  function srnAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];

    $isValidate = validate($inputes, [
      "grnItemList" => "array"
    ], [
      "grnItemList" => "Required"
    ]);

    if ($isValidate["status"] == "success") {
      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $jv_no = 0;
      $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
      $responc = queryGet($sqlPrv);
      if ($responc['status'] == 'success') {
        $redata = $responc['data']['jv_no'];
        $jv_no = getJernalNewCode($redata);
      } else {
        $redata = '';
        $jv_no = getJernalNewCode($redata);
      }

      $party_code = $inputes['party_code'] ?? "";
      $party_name = $inputes['party_name'] ?? "";

      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $inputes['referenceNo'] . "',
                  `remark`='" . $inputes['remarks'] . "',                  
                  `party_code`='" . $party_code . "',
                  `party_name`='" . $party_name . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $inputes['journalEntryReference'] . "',
                  `documentNo`='" . $inputes['documentNo'] . "',
                  `documentDate`='" . $inputes['documentDate'] . "',
                  `postingDate`='" . $inputes['invoicePostingDate'] . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";

      $rtn = queryInsert($journalins);

      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];
        $drItems = $inputes['grnItemList'];

        foreach ($drItems  as $drkey => $drvalue) {
          $insdr = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $drvalue['parentGlId'] . "',
                        `subGlCode`='" . $drvalue['itemCode'] . "',
                        `subGlName`='" . $drvalue['itemName'] . "',
                        `debit_amount`='" . $drvalue['itemTotalPrice'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";

          queryInsert($insdr);

          $inscr = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $creditAccList[0]['id'] . "',
                        `credit_amount`='" . $drvalue['itemTotalPrice'] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";

          queryInsert($inscr);
        }
        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["journalId"] = $journal_id;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = "";
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }



  // 20-12-2022 18:16
  function srnIvAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];
    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "grIrItems" => "array",
      "taxDetails" => "array",
    ], [
      "BasicDetails" => "Required",
      "grIrItems" => "Required",
      "taxDetails" => "Required"
    ]);

    if ($isValidate["status"] == "success") {
      $grnDetails = $inputes["BasicDetails"];
      $grnItems = $inputes["grIrItems"];
      $vendorDetails = $inputes['vendorDetails'];
      $imputgst = $inputes['taxDetails'];
      $imputtds = $inputes['tdsDetails'];

      $grnPostingJournalId = $grnDetails["grnJournalId"];
      $accountingDocumentNo = $grnDetails["documentNo"];
      $accountingRefNo = $grnDetails["reference"];
      $accountingRemarks =  $grnDetails['remarks'];
      $journalEntryReference = $grnDetails['journalEntryReference'];
      $documentDate = $grnDetails['documentDate'];
      $documentPostingDate = $grnDetails['postingDate'];

      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $jv_no = 0;
      $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
      $responc = queryGet($sqlPrv);
      if ($responc['status'] == 'success') {
        $redata = $responc['data']['jv_no'];
        $jv_no = getJernalNewCode($redata);
      } else {
        $redata = '';
        $jv_no = getJernalNewCode($redata);
      }

      $party_code = $vendorDetails['vendorCode'] ?? "";
      $party_name = $vendorDetails['vendorName'] ?? "";

      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $accountingRefNo . "',
                  `remark`='" . $accountingRemarks . "',
                  `party_code`='" . $party_code . "',
                  `party_name`='" . $party_name . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $journalEntryReference . "',
                  `documentNo`='" . $accountingDocumentNo . "',
                  `documentDate`='" . $documentDate . "',
                  `postingDate`='" . $documentPostingDate . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";

      $rtn = queryInsert($journalins);

      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];
        $totalamount = 0;
        $tdstotal = 0;
        foreach ($grnItems  as $drkey => $drvalue) {
          $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $debitAccList[0]['id'] . "',
                        `debit_amount`='" . $drvalue . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";
          queryInsert($ins);
          $totalamount += $drvalue;
        }
        if (!empty($imputgst['igst']) && ($imputgst['igst'] > 0)) {
          //-------------------------IGST------------------------
          $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $debitAccList[1]['id'] . "',
                        `debit_amount`='" . $imputgst['igst'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`= '" . $created_by . "'";
          queryInsert($ins);
          $totalamount += $imputgst['igst'];
        } else {
          //-------------------------CGST------------------------
          $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $debitAccList[2]['id'] . "',
                        `debit_amount`='" . $imputgst['cgst'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";
          queryInsert($ins);
          $totalamount += $imputgst['cgst'];
          //-------------------------SGST------------------------
          $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $debitAccList[3]['id'] . "',
                        `debit_amount`='" .  $imputgst['sgst'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";
          queryInsert($ins);
          $totalamount += $imputgst['sgst'];
        }

        //-------------------------TDS------------------------
        if (isset($imputtds) && !empty($imputtds)) {
          $tdstotal = array_sum($imputtds);
          $ins = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $creditAccList[1]['id'] . "',
                        `credit_amount`='" . $tdstotal . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";
          queryInsert($ins);
          $totalamount -= $tdstotal;
        }
        //-------------------Vendor G/L-----------------------------
        $ins = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
        SET
            `journal_id`='" . $journal_id . "',
            `glId`='" . $creditAccList[0]['id'] . "',
            `subGlCode`='" . $vendorDetails['vendorCode'] . "',
            `subGlName`='" . $vendorDetails['vendorName'] . "',
            `credit_amount`='" . $totalamount . "',
            `credit_created_by`='" . $created_by . "',
            `credit_updated_by`='" . $created_by . "'";
        queryInsert($ins);

        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["journalId"] = $journal_id;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = '';
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }




  // SO PGI ACCOUNTING---25-12-2022 18:16
  function sopgiAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];
    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "FGItems" => "array",
    ], [
      "BasicDetails" => "Required",
      "FGItems" => "Required"
    ]);

    if ($isValidate["status"] == "success") {
      $pgiDetails = $inputes["BasicDetails"];
      $pgiItems = $inputes["FGItems"];

      $pgiPostingJournalId = $pgiDetails["grnJournalId"] ?? 0;
      $accountingDocumentNo = $pgiDetails["documentNo"];
      $accountingRefNo = $pgiDetails["reference"];
      $accountingRemarks =  $pgiDetails['remarks'];
      $journalEntryReference = $pgiDetails['journalEntryReference'];
      $documentDate = $pgiDetails['documentDate'];
      $documentPostingDate = $pgiDetails['postingDate'];

      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $jv_no = 0;
      $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
      $responc = queryGet($sqlPrv);
      if ($responc['status'] == 'success') {
        $redata = $responc['data']['jv_no'];
        $jv_no = getJernalNewCode($redata);
      } else {
        $redata = '';
        $jv_no = getJernalNewCode($redata);
      }

      $party_code = $inputes['customerDetails']['customerCode'] ?? "";
      $party_name = $inputes['customerDetails']['customerName'] ?? "";

      //`parent_id`='" . $pgiPostingJournalId . "',
      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $accountingRefNo . "',
                  `remark`='" . $accountingRemarks . "',
                  `party_code`='" . $party_code . "',
                  `party_name`='" . $party_name . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $journalEntryReference . "',
                  `documentNo`='" . $accountingDocumentNo . "',
                  `documentDate`='" . $documentDate . "',
                  `postingDate`='" . $documentPostingDate . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";
      $rtn = queryInsert($journalins);

      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];
        foreach ($pgiItems  as $crkey => $pgivalue) {
          $pgiItentax = 0;
          if (isset($pgivalue['itemTotalTax1'])) {
            $pgiItentax = $pgivalue['itemTotalTax1'];
          } else {
            $pgiItentax = $pgivalue['totalTax'];
          }
          $withouttaxamount = $pgivalue['totalPrice'] - $pgiItentax;
          $inscr = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $pgivalue['parentGlId'] . "',
                        `subGlCode`='" . $pgivalue['itemCode'] . "',
                        `subGlName`='" . $pgivalue['itemName'] . "',
                        `credit_amount`='" . $withouttaxamount . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";

          queryInsert($inscr);

          $insdr = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $debitAccList[0]['id'] . "',
                        `debit_amount`='" . $withouttaxamount . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";

          queryInsert($insdr);
        }
        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["journalId"] = $journal_id;
        $returnData["sqljournal"] = $journalins;
        $returnData["inputes"] = $inputes;
        $returnData["pgiItems"] = $pgiItems;
        $returnData["inscr"] = $inscr;
        $returnData["insdr"] = $insdr;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = '';
        $returnData["sqljournal"] = $journalins;
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }

  // SO Invoicing ACCOUNTING---28-12-2022 18:16
  function soIvAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    // console($inputes);

    $returnData = [];
    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "customerDetails" => "array",
      "FGItems" => "array",
      "taxDetails" => "array",
    ], [
      "BasicDetails" => "Required",
      "customerDetails" => "Required",
      "FGItems" => "Required",
      "taxDetails" => "Required"
    ]);

    if ($isValidate["status"] == "success") {
      $soivDetails = $inputes["BasicDetails"];
      $soivItems = $inputes["FGItems"];
      $customerDetails = $inputes['customerDetails'];
      $outputgst = $inputes['taxDetails'];

      $soivPostingJournalId = $soivDetails["soivJournalId"] ?? 0;
      $accountingDocumentNo = $soivDetails["documentNo"];
      $accountingRefNo = $soivDetails["reference"];
      $accountingRemarks =  $soivDetails['remarks'];
      $journalEntryReference = $soivDetails['journalEntryReference'];
      $documentDate = $soivDetails['documentDate'];
      $documentPostingDate = $soivDetails['postingDate'];

      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);
      //console($debitCreditAccListObj);
      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];
      //console($creditAccList);
      $jv_no = 0;
      $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
      $responc = queryGet($sqlPrv);
      if ($responc['status'] == 'success') {
        $redata = $responc['data']['jv_no'];
        $jv_no = getJernalNewCode($redata);
      } else {
        $redata = '';
        $jv_no = getJernalNewCode($redata);
      }
      $party_code = $customerDetails['customerCode'] ?? "";
      $party_name = $customerDetails['customerName'] ?? "";
      //  `parent_id`='" . $soivPostingJournalId . "',
      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $accountingRefNo . "',
                  `remark`='" . $accountingRemarks . "',
                  `party_code`='" . $party_code . "',
                  `party_name`='" . $party_name . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $journalEntryReference . "',
                  `documentNo`='" . $accountingDocumentNo . "',
                  `documentDate`='" . $documentDate . "',
                  `postingDate`='" . $documentPostingDate . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";

      $rtn = queryInsert($journalins);

      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];
        $totalamount = 0;
        foreach ($soivItems  as $crkey => $crvalue) {
          $pgiItentax = 0;
          if (isset($crvalue['itemTotalTax1'])) {
            $pgiItentax = $crvalue['itemTotalTax1'];
          } else {
            $pgiItentax = $crvalue['totalTax'];
          }
          $withouttaxamount = $crvalue['totalPrice'] - $pgiItentax;

          $creditGLId = $creditAccList[0]['id'];

          // $creditGLId=$crvalue['parentGlId'];

          $inscr = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $creditGLId . "',
                        `subGlCode`='" . $crvalue['itemCode'] . "',
                        `subGlName`='" . $crvalue['itemName'] . "',
                        `credit_amount`='" . $withouttaxamount . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";
          queryInsert($inscr);
          $totalamount += $withouttaxamount;
        }
        if ($outputgst['igst'] > 0) {
          //-------------------------IGST------------------------
          $insigst = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $creditAccList[1]['id'] . "',
                        `credit_amount`='" . $outputgst['igst'] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`= '" . $created_by . "'";
          queryInsert($insigst);
          $totalamount += $outputgst['igst'];
        } else {
          //-------------------------CGST------------------------
          $ins = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $creditAccList[2]['id'] . "',
                        `credit_amount`='" . $outputgst['cgst'] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";
          queryInsert($ins);
          $totalamount += $outputgst['cgst'];
          //-------------------------SGST------------------------
          $ins = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $creditAccList[3]['id'] . "',
                        `credit_amount`='" .  $outputgst['sgst'] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";
          queryInsert($ins);
          $totalamount += $outputgst['sgst'];
        }

        /* if ($outputgst['TCS']>0) {
          //-------------------------IGST------------------------
          $ins = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $creditAccList[4]['id'] . "',
                        `credit_amount`='" . $outputgst['TCS'] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`= '" . $created_by . "'";
          queryInsert($ins);
          $totalamount += $outputgst['TCS'];
        } */
        //-------------------Customer G/L-----------------------------
        $insdr = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
        SET
            `journal_id`='" . $journal_id . "',
            `glId`='" . $debitAccList[0]['id'] . "',
            `subGlCode`='" . $customerDetails['customerCode'] . "',
            `subGlName`='" . $customerDetails['customerName'] . "',
            `debit_amount`='" . $totalamount . "',
            `debit_created_by`='" . $created_by . "',
            `debit_updated_by`='" . $created_by . "'";
        queryInsert($insdr);

        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["journalId"] = $journal_id;
        $returnData["journalins"] = $journalins;
        $returnData["inputes"] = $inputes;
        $returnData["insdr"] = $insdr;
        $returnData["inscr"] = $inscr;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = '';
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }





  function collectionAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];

    $isValidate = validate($inputes, [
      "BasicDetails" => "array"
    ], [
      "BasicDetails" => "Required"
    ]);

    if ($isValidate["status"] == "success") {
      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $jv_no = 0;
      $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
      $responc = queryGet($sqlPrv);
      if ($responc['status'] == 'success') {
        $redata = $responc['data']['jv_no'];
        $jv_no = getJernalNewCode($redata);
      } else {
        $redata = '';
        $jv_no = getJernalNewCode($redata);
      }
      $customerDetails = $inputes['customerDetails'];

      $party_code=$customerDetails['customer_code']??"";
      $party_name=$customerDetails['trade_name']??"";

      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $inputes['BasicDetails']['reference'] . "',
                  `remark`='" . $inputes['BasicDetails']['remarks'] . "',
                  `party_code`='" . $party_code . "',
                  `party_name`='" . $party_name . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $inputes['BasicDetails']['journalEntryReference'] . "',
                  `documentNo`='" . $inputes['BasicDetails']['documentNo'] . "',
                  `documentDate`='" . $inputes['BasicDetails']['documentDate'] . "',
                  `postingDate`='" . $inputes['BasicDetails']['postingDate'] . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";

      $rtn = queryInsert($journalins);


      $accounting=array();
      $accounting['journal']['parent_id']=$parent_id;
      $accounting['journal']['parent_slug']=$functionSlug;
      $accounting['journal']['refarenceCode']=$inputes['BasicDetails']['reference'];
      $accounting['journal']['remark']=$inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code']=$party_code;
      $accounting['journal']['party_name']=$party_name;
      $accounting['journal']['jv_no']=$jv_no;
      $accounting['journal']['journalEntryReference']=$inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo']=$inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate']=$inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate']=$inputes['BasicDetails']['postingDate'];


      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];
        $paymentDetails = $inputes['paymentDetails'];
        //console($customerDetails);
        $crItems = $inputes['paymentInvItems'];
        $dramount = 0;
        $sqlBankCash = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='" . $paymentDetails['bankId'] . "'";
        $queryBankCash = queryGet($sqlBankCash)['data'];
        //console($queryBankCash);
        foreach ($crItems  as $crkey => $crvalue) {
          $ins = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $customerDetails['parentGlId'] . "',
                        `subGlCode`='" . $customerDetails['customer_code'] . "',
                        `subGlName`='" . $customerDetails['trade_name'] . "',
                        `credit_amount`='" . $crvalue['recAmt'] . "',
                        `credit_remark`='Payment For - " . $crvalue['invoiceNo'] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";

          queryInsert($ins);
          $dramount += $crvalue['recAmt'];

          $accounting['credit'][]=[
            'glId'=>$customerDetails['parentGlId'],
            'subGlCode'=>$customerDetails['customer_code'],
            'subGlName'=>$customerDetails['trade_name'],
            'credit_amount'=>$crvalue['recAmt'],
            'credit_remark'=>"Payment For - " . $crvalue['invoiceNo']
          ];

        }
        $advance = $paymentDetails['collectPayment'] - $dramount;
        if ($advance > 0) {
          $ins = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $customerDetails['parentGlId'] . "',
                        `subGlCode`='" . $customerDetails['customer_code'] . "',
                        `subGlName`='" . $customerDetails['trade_name'] . "',
                        `credit_amount`='" . $advance . "',
                        `credit_remark`='Advance Amount',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";

          queryInsert($ins);
          $dramount += $advance;


          $accounting['credit'][]=[
            'glId'=>$customerDetails['parentGlId'],
            'subGlCode'=>$customerDetails['customer_code'],
            'subGlName'=>$customerDetails['trade_name'],
            'credit_amount'=>$advance,
            'credit_remark'=>'Advance Amount'
          ];
        }

        $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $queryBankCash['parent_gl'] . "',
                        `subGlCode`='" . $paymentDetails['accCode'] . "',
                        `subGlName`='" . $paymentDetails['accName'] . "',
                        `debit_amount`='" . $dramount . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";
        queryInsert($ins);


        $accounting['debit'][]=[
          'glId'=>$queryBankCash['parent_gl'],
          'subGlCode'=>$paymentDetails['accCode'],
          'subGlName'=>$paymentDetails['accName'],
          'debit_amount'=>$dramount,
          'debit_remark'=>''
        ];

        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["journalId"] = $journal_id;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = "";
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }




  //SO Collections Accounting 10-01-2023
  function paymentAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $isValidate = validate($inputes, [
      "BasicDetails" => "array"
    ], [
      "BasicDetails" => "Required"
    ]);

    if ($isValidate["status"] == "success") {
      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];


      $jv_no = 0;
      $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
      $responc = queryGet($sqlPrv);
      if ($responc['status'] == 'success') {
        $redata = $responc['data']['jv_no'];
        $jv_no = getJernalNewCode($redata);
      } else {
        $redata = '';
        $jv_no = getJernalNewCode($redata);
      }
      $vendorDetails = $inputes['vendorDetails'];

      $party_code = $vendorDetails['vendor_code'] ?? "";
      $party_name = $vendorDetails['trade_name'] ?? "";

      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $inputes['BasicDetails']['reference'] . "',
                  `remark`='" . $inputes['BasicDetails']['remarks'] . "',
                  `party_code`='" . $party_code . "',
                  `party_name`='" . $party_name . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $inputes['BasicDetails']['journalEntryReference'] . "',
                  `documentNo`='" . $inputes['BasicDetails']['documentNo'] . "',
                  `documentDate`='" . $inputes['BasicDetails']['documentDate'] . "',
                  `postingDate`='" . $inputes['BasicDetails']['postingDate'] . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";

      $rtn = queryInsert($journalins);

      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];
        $paymentDetails = $inputes['paymentDetails'];
        //console($vendorDetails);
        $crItems = $inputes['paymentInvItems'];
        $dramount = 0;
        $sqlBankCash = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='" . $paymentDetails['bankId'] . "'";
        $queryBankCash = queryGet($sqlBankCash)['data'];
        //console($queryBankCash);
        foreach ($crItems  as $crkey => $crvalue) {
          $inscr = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $vendorDetails['parentGlId'] . "',
                        `subGlCode`='" . $vendorDetails['vendor_code'] . "',
                        `subGlName`='" . $vendorDetails['trade_name'] . "',
                        `debit_amount`='" . $crvalue['recAmt'] . "',
                        `debit_remark`='Payment For - " . $crvalue['invoiceNo'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";

          queryInsert($inscr);
          $dramount += $crvalue['recAmt'];
        }
        $advance = $paymentDetails['collectPayment'] - $dramount;
        if ($advance > 0) {
          $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $vendorDetails['parentGlId'] . "',
                        `subGlCode`='" . $vendorDetails['vendor_code'] . "',
                        `subGlName`='" . $vendorDetails['trade_name'] . "',
                        `debit_amount`='" . $advance . "',
                        `debit_remark`='Advance Amount',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";

          queryInsert($ins);
          $dramount += $advance;
        }
        $ins = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $queryBankCash['parent_gl'] . "',
                        `subGlCode`='" . $paymentDetails['accCode'] . "',
                        `subGlName`='" . $paymentDetails['accName'] . "',
                        `credit_amount`='" . $dramount . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";
        queryInsert($ins);

        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["journalId"] = $journal_id;
        $returnData['debitCreditAccListObj'] = $debitCreditAccListObj;
        $returnData["inputes"] = $inputes;
        $returnData["inscr"] = $inscr;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = "";
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }
  // Production Declaration ACCOUNTING---25-12-2022 18:16
  function productionDeclarationAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];

    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "consumpProductData" => "array",
    ], [
      "BasicDetails" => "Required",
      "consumpProductData" => "Required"
    ]);
    //console($isValidate);
    if ($isValidate["status"] == "success") {
      $BasicDetails = $inputes["BasicDetails"];
      $consumpProductData = $inputes["consumpProductData"];

      $pgiPostingJournalId = $BasicDetails["grnJournalId"] ?? 0;
      $accountingDocumentNo = $BasicDetails["documentNo"];
      $accountingRefNo = $BasicDetails["reference"];
      $accountingRemarks =  $BasicDetails['remarks'];
      $journalEntryReference = $BasicDetails['journalEntryReference'];
      $documentDate = $BasicDetails['documentDate'];
      $documentPostingDate = $BasicDetails['postingDate'];

      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);
      // console($debitCreditAccListObj);
      // exit;
      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $jv_no = 0;
      $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
      $responc = queryGet($sqlPrv);
      if ($responc['status'] == 'success') {
        $redata = $responc['data']['jv_no'];
        $jv_no = getJernalNewCode($redata);
      } else {
        $redata = '';
        $jv_no = getJernalNewCode($redata);
      }

      //`parent_id`='" . $pgiPostingJournalId . "',
      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $accountingRefNo . "',
                  `remark`='" . $accountingRemarks . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $journalEntryReference . "',
                  `documentNo`='" . $accountingDocumentNo . "',
                  `documentDate`='" . $documentDate . "',
                  `postingDate`='" . $documentPostingDate . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";
      $rtn = queryInsert($journalins);

      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];
        foreach ($consumpProductData  as $crkey => $value) {
          if ($value['type'] == 'RM') {
            $inscr = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $value['parentGlId'] . "',
                        `subGlCode`='" . $value['itemCode'] . "',
                        `subGlName`='" . $value['itemName'] . "',
                        `credit_amount`='" . $value['price'] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";

            queryInsert($inscr);

            $insdr = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $debitAccList[0]['id'] . "',
                        `debit_amount`='" . $value['price'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";

            queryInsert($insdr);
          }
          if ($value['type'] == 'SFG') {
            $inscr = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $value['parentGlId'] . "',
                        `subGlCode`='" . $value['itemCode'] . "',
                        `subGlName`='" . $value['itemName'] . "',
                        `credit_amount`='" . $value['price'] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";

            queryInsert($inscr);

            $insdr = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $debitAccList[1]['id'] . "',
                        `debit_amount`='" . $value['price'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";

            queryInsert($insdr);
          }
        }
        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["jv_no"] = $jv_no;
        $returnData["journalId"] = $journal_id;
        $returnData["sqljournal"] = $journalins;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = '';
        $returnData["sqljournal"] = $journalins;
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }


  // FG/SFG Declaration ACCOUNTING---25-12-2022 18:16
  function FGSFGDeclarationAccountingPosting($inputes, $functionSlug, $parent_id, $jv_no = null)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];
    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "finalProductData" => "array",
    ], [
      "BasicDetails" => "Required",
      "finalProductData" => "Required"
    ]);

    //console($isValidate);
    if ($isValidate["status"] == "success") {
      $BasicDetails = $inputes["BasicDetails"];
      $finalProductData = $inputes["finalProductData"];

      $accountingDocumentNo = $BasicDetails["documentNo"];
      $accountingRefNo = $BasicDetails["reference"];
      $accountingRemarks =  $BasicDetails['remarks'];
      $journalEntryReference = $BasicDetails['journalEntryReference'];
      $documentDate = $BasicDetails['documentDate'];
      $documentPostingDate = $BasicDetails['postingDate'];

      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);
      // console($debitCreditAccListObj);
      //exit;
      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];


      $jv_no = $jv_no;
      if (empty($jv_no)) {
        $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
        $responc = queryGet($sqlPrv);
        if ($responc['status'] == 'success') {
          $redata = $responc['data']['jv_no'];
          $jv_no = getJernalNewCode($redata);
        } else {
          $redata = '';
          $jv_no = getJernalNewCode($redata);
        }
      }

      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $accountingRefNo . "',
                  `remark`='" . $accountingRemarks . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $journalEntryReference . "',
                  `documentNo`='" . $accountingDocumentNo . "',
                  `documentDate`='" . $documentDate . "',
                  `postingDate`='" . $documentPostingDate . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";
      $rtn = queryInsert($journalins);

      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];

        $insdr = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $finalProductData['parentGlId'] . "',
                        `subGlCode`='" . $finalProductData['itemCode'] . "',
                        `subGlName`='" . $finalProductData['itemName'] . "',
                        `debit_amount`='" . $finalProductData['price'] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";

        queryInsert($insdr);

        $inscr = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $creditAccList[0]['id'] . "',
                        `credit_amount`='" . $finalProductData['price'] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";

        queryInsert($inscr);

        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["journalId"] = $journal_id;
        $returnData["sqljournal"] = $journalins;
        $returnData["insdr"] = $insdr;
        $returnData["inscr"] = $inscr;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = '';
        $returnData["sqljournal"] = $journalins;
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }


  //Stock Transfer Accounting 12-05-2023
  function transferAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];

    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "fromItem" => "array",
      "toItem" => "array"
    ], [
      "BasicDetails" => "Required",
      "fromItem" => "Required",
      "toItem" => "Required"
    ]);

    if ($isValidate["status"] == "success") {
      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $jv_no = 0;
      $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
      $responc = queryGet($sqlPrv);
      if ($responc['status'] == 'success') {
        $redata = $responc['data']['jv_no'];
        $jv_no = getJernalNewCode($redata);
      } else {
        $redata = '';
        $jv_no = getJernalNewCode($redata);
      }
      $party_code = "";
      $party_name = "";

      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $inputes['BasicDetails']['reference'] . "',
                  `remark`='" . $inputes['BasicDetails']['remarks'] . "',
                  `party_code`='" . $party_code . "',
                  `party_name`='" . $party_name . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $inputes['BasicDetails']['journalEntryReference'] . "',
                  `documentNo`='" . $inputes['BasicDetails']['documentNo'] . "',
                  `documentDate`='" . $inputes['BasicDetails']['documentDate'] . "',
                  `postingDate`='" . $inputes['BasicDetails']['postingDate'] . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";

      $rtn = queryInsert($journalins);

      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];
        foreach ($inputes['fromItem']  as $crkey => $value) {
          $inscr = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                      SET
                          `journal_id`='" . $journal_id . "',
                          `glId`='" . $value['parentGlId'] . "',
                          `subGlCode`='" . $value['itemCode'] . "',
                          `subGlName`='" . $value['itemName'] . "',
                          `credit_amount`='" . $value['price'] . "',
                          `credit_remark`='Transfer',
                          `credit_created_by`='" . $created_by . "',
                          `credit_updated_by`='" . $created_by . "'";

          queryInsert($inscr);
        }

        $insdr = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                        SET
                            `journal_id`='" . $journal_id . "',
                            `glId`='" . $inputes['toItem']['parentGlId'] . "',
                            `subGlCode`='" . $inputes['toItem']['itemCode'] . "',
                            `subGlName`='" . $inputes['toItem']['itemName'] . "',
                            `debit_amount`='" . $inputes['toItem']['price'] . "',
                            `debit_remark`='Transfer received',
                            `debit_created_by`='" . $created_by . "',
                            `debit_updated_by`='" . $created_by . "'";
        queryInsert($insdr);

        if ($inputes['BasicDetails']['priceDiffrence'] > 0) {
          $insdrDiff = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                      SET
                          `journal_id`='" . $journal_id . "',
                          `glId`='0',
                          `debit_amount`='" . $inputes['BasicDetails']['priceDiffrence'] . "',
                          `debit_remark`='Price Diffrence',
                          `debit_created_by`='" . $created_by . "',
                          `debit_updated_by`='" . $created_by . "'";
          queryInsert($insdrDiff);
        }

        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["journalId"] = $journal_id;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = "";
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    $returnData['status'] = "success";
    $returnData['message'] = "Successfully Inserted";
    $returnData['inputes'] = $inputes;
    return $returnData;
  }

  //Asset Deprecation Accounting 12-05-2023  assetDeprecation
  function assetDeprecationAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];

    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "assetInformation" => "array"
    ], [
      "BasicDetails" => "Required",
      "assetInformation" => "Required"
    ]);

    if ($isValidate["status"] == "success") {
      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $jv_no = 0;
      $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
      $responc = queryGet($sqlPrv);
      if ($responc['status'] == 'success') {
        $redata = $responc['data']['jv_no'];
        $jv_no = getJernalNewCode($redata);
      } else {
        $redata = '';
        $jv_no = getJernalNewCode($redata);
      }
      $party_code = "";
      $party_name = "";

      $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `parent_id`='" . $parent_id . "',
                  `parent_slug`='" . $functionSlug . "',
                  `refarenceCode`='" . $inputes['BasicDetails']['reference'] . "',
                  `remark`='" . $inputes['BasicDetails']['remarks'] . "',
                  `party_code`='" . $party_code . "',
                  `party_name`='" . $party_name . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $inputes['BasicDetails']['journalEntryReference'] . "',
                  `documentNo`='" . $inputes['BasicDetails']['documentNo'] . "',
                  `documentDate`='" . $inputes['BasicDetails']['documentDate'] . "',
                  `postingDate`='" . $inputes['BasicDetails']['postingDate'] . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";

      $rtn = queryInsert($journalins);

      if ($rtn['status'] == 'success') {
        $journal_id = $rtn['insertedId'];
        $insdr = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                        SET
                            `journal_id`='" . $journal_id . "',
                            `glId`='" . $inputes['toItem']['parentGlId'] . "',
                            `subGlCode`='" . $inputes['toItem']['itemCode'] . "',
                            `subGlName`='" . $inputes['toItem']['itemName'] . "',
                            `debit_amount`='" . $inputes['toItem']['price'] . "',
                            `debit_remark`='Transfer received',
                            `debit_created_by`='" . $created_by . "',
                            `debit_updated_by`='" . $created_by . "'";
        queryInsert($insdr);

        foreach ($inputes['fromItem']  as $crkey => $value) {
          $inscr = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                      SET
                          `journal_id`='" . $journal_id . "',
                          `glId`='" . $value['parentGlId'] . "',
                          `subGlCode`='" . $value['itemCode'] . "',
                          `subGlName`='" . $value['itemName'] . "',
                          `credit_amount`='" . $value['price'] . "',
                          `credit_remark`='Transfer',
                          `credit_created_by`='" . $created_by . "',
                          `credit_updated_by`='" . $created_by . "'";

          queryInsert($inscr);
        }

        $returnData['status'] = "success";
        $returnData['message'] = "Successfully Inserted";
        $returnData["journalId"] = $journal_id;
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Somthing went wrong";
        $returnData["journalId"] = "";
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    $returnData['status'] = "success";
    $returnData['message'] = "Successfully Inserted";
    $returnData['inputes'] = $inputes;
    return $returnData;
  }
}



//*************************************/JOURNAL INSERT/******************************************//
function createDataJournal($POST = [])
{
  global $dbCon;
  global $created_by;
  global $company_id;
  global $branch_id;
  global $location_id;
  $returnData = [];
  $isValidate = validate($POST, [
    "journal" => "array"
  ], [
    "journal" => "Required"
  ]);

  if ($isValidate["status"] == "success") {
    $jv_no = 0;
    $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
    $responc = queryGet($sqlPrv);
    if ($responc['status'] == 'success') {
      $redata = $responc['data']['jv_no'];
      $jv_no = getJernalNewCode($redata);
    } else {
      $redata = '';
      $jv_no = getJernalNewCode($redata);
    }
    $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `refarenceCode`='" . $POST['refarenceCode'] . "',
                  `remark`='" . $POST['remark'] . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $POST['journalEntryReference'] . "',
                  `documentNo`='" . $POST['documentNo'] . "',
                  `documentDate`='" . $POST['documentDate'] . "',
                  `postingDate`='" . $POST['postingDate'] . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";

    $rtn = queryInsert($journalins);
    if ($rtn['status'] == 'success') {
      $journal_id = $rtn['insertedId'];
      $drGL = $_POST['journal']['debit']['gl'];
      $drsubgl = $_POST['journal']['debit']['subgl'];
      $drAmt = $_POST['journal']['debit']['amount'];
      $crGL = $_POST['journal']['credit']['gl'];
      $crsubgl = $_POST['journal']['credit']['subgl'];
      $crAmt = $_POST['journal']['credit']['amount'];
      foreach ($drGL  as $drkey => $drvalue) {
        $drsubgl = explode("|", $drsubgl[$drkey]);
        $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $drGL[$drkey] . "',
                        `subGlCode`='" . $drsubgl[0] . "',
                        `subGlName`='" . $drsubgl[1] . "',
                        `debit_amount`='" . $drAmt[$drkey] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";

        $rtn = queryInsert($ins);
      }

      foreach ($crGL  as $crkey => $crvalue) {
        $crsubgl = explode("|", $crsubgl[$crkey]);
        $ins = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $crGL[$crkey] . "',
                        `subGlCode`='" . $crsubgl[0] . "',
                        `subGlName`='" . $crsubgl[1] . "',
                        `credit_amount`='" . $crAmt[$crkey] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";

        $rtn = queryInsert($ins);
      }
      $returnData['status'] = "success";
      $returnData['message'] = "Successfully Inserted";
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Somthing went wrong";
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Invalid form inputes";
    $returnData['errors'] = $isValidate["errors"];
  }
  return $returnData;
}

function JournalEntryByFunctionMapp($POST = [])
{
  global $dbCon;
  global $created_by;
  global $company_id;
  global $branch_id;
  global $location_id;
  $returnData = [];
  /* $isValidate = validate($POST, [
    "journal" => "array"
  ], [
    "journal" => "Required"
  ]);
  if ($isValidate["status"] == "success") {
    $jv_no = 0;
    $sqlPrv = "SELECT jv_no FROM `" . ERP_ACC_JOURNAL . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
    $responc = queryGet($sqlPrv);
    if ($responc['status'] == 'success') {
      $redata = $responc['data']['jv_no'];
      $jv_no = getJernalNewCode($redata);
    } else {
      $redata = '';
      $jv_no = getJernalNewCode($redata);
    }
    $journalins = "INSERT INTO `" . ERP_ACC_JOURNAL . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `refarenceCode`='" . $POST['refarenceCode'] . "',
                  `remark`='" . $POST['remark'] . "',
                  `jv_no`='" . $jv_no . "',
                  `journalEntryReference`='" . $POST['journalEntryReference'] . "',
                  `documentDate`='" . $POST['documentDate'] . "',
                  `postingDate`='" . $POST['postingDate'] . "',
                  `journal_created_by`='" . $created_by . "',
                  `journal_updated_by`='" . $created_by . "'";

    $rtn = queryInsert($journalins);
    if ($rtn['status'] == 'success') {
      $journal_id = $rtn['insertedId'];
      $drGL = $POST['journal']['debit']['gl'];
      $drAmt = $POST['journal']['debit']['amount'];
      $crGL = $POST['journal']['credit']['gl'];
      $crAmt = $POST['journal']['credit']['amount'];
      foreach ($drGL  as $drkey => $drvalue) {
        $ins = "INSERT INTO `" . ERP_ACC_DEBIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $drGL[$drkey] . "',
                        `debit_amount`='" . $drAmt[$drkey] . "',
                        `debit_created_by`='" . $created_by . "',
                        `debit_updated_by`='" . $created_by . "'";

        $rtn = queryInsert($ins);
      }

      foreach ($crGL  as $crkey => $crvalue) {
        $ins = "INSERT INTO `" . ERP_ACC_CREDIT . "` 
                    SET
                        `journal_id`='" . $journal_id . "',
                        `glId`='" . $crGL[$crkey] . "',
                        `credit_amount`='" . $crAmt[$crkey] . "',
                        `credit_created_by`='" . $created_by . "',
                        `credit_updated_by`='" . $created_by . "'";

        $rtn = queryInsert($ins);
      }
      $returnData['status'] = "success";
      $returnData['message'] = "Successfully Inserted";
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Somthing went wrong";
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Invalid form inputes";
    $returnData['errors'] = $isValidate["errors"];
  }*/
  $returnData['status'] = "success";
  $returnData['message'] = "Successfully Inserted";
  return $returnData;
}

function JournalEntryByFunctionMappNew($POST = [])
{
}



//*************************************/END/******************************************//