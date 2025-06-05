<?php
require_once dirname(__DIR__) . "/company/func-ChartOfAccounts.php";
require_once "journal-controller.php";

class Accounting
{

  public function getChartOfAccountsDataDetailsByKeyArr($keys = null)
  {
    return queryGet('SELECT * FROM `' . ERP_ACC_CHART_OF_ACCOUNTS . '` WHERE `status`!="deleted" AND `id` IN (' . $keys . ')');
  }
  public function getChartOfAccountsDataDetailsByKey($key)
  {
    return queryGet('SELECT * FROM `' . ERP_ACC_CHART_OF_ACCOUNTS . '` WHERE `status`!="deleted" AND `id` =' . $key . '');
  }

  public function getCreditDebitAccountsList($functionSlug = null)
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



  function grnSrnMultiAccountingPosting($inputes, $functionSlug, $parent_id)
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
      $debitCreditAccListGRNObj = $this->getCreditDebitAccountsList("grn");

      if ($debitCreditAccListGRNObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "GRN Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccListGRN = $debitCreditAccListGRNObj["debitAccountsList"];
      $creditAccListGRN = $debitCreditAccListGRNObj["creditAccountsList"];

      $debitCreditAccListSRNObj = $this->getCreditDebitAccountsList("srn");

      if ($debitCreditAccListSRNObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "SRN Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccListSRN = $debitCreditAccListSRNObj["debitAccountsList"];
      $creditAccListSRN = $debitCreditAccListSRNObj["creditAccountsList"];


      $party_code = $inputes['party_code'] ?? "";
      $party_name = $inputes['party_name'] ?? "";


      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['referenceNo'];
      $accounting['journal']['remark'] = $inputes['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['invoicePostingDate'];


      $drItems = $inputes['grnItemList'];

      foreach ($drItems  as $drkey => $drvalue) {
        $accounting['credit'][] = [
          'glId' => $creditAccListSRN[0]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $drvalue['itemTotalPrice'],
          'credit_remark' => ''
        ];

        $accounting['debit'][] = [
          'glId' => $drvalue['parentGlId'],
          'subGlCode' => $drvalue['itemCode'],
          'subGlName' => $drvalue['itemName'],
          'debit_amount' => $drvalue['itemTotalPrice'],
          'debit_remark' => ''
        ];
      }
      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
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

      $party_code = $inputes['party_code'] ?? "";
      $party_name = $inputes['party_name'] ?? "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['referenceNo'];
      $accounting['journal']['remark'] = $inputes['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['invoicePostingDate'];

      $drItems = $inputes['grnItemList'];

      foreach ($drItems  as $drkey => $drvalue) {

        $accounting['credit'][] = [
          'glId' => $creditAccList[0]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $drvalue['itemTotalPrice'],
          'credit_remark' => ''
        ];

        $accounting['debit'][] = [
          'glId' => $drvalue['parentGlId'],
          'subGlCode' => $drvalue['itemCode'],
          'subGlName' => $drvalue['itemName'],
          'debit_amount' => $drvalue['itemTotalPrice'],
          'debit_remark' => ''
        ];
      }
      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
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
      $inputtcs = $inputes['tcsDetails'];
      $roundOffValue = $inputes["roundOffValue"] ?? 0;

      $grnPostingJournalId = $grnDetails["grnJournalId"];
      $accountingDocumentNo = $grnDetails["documentNo"];
      $accountingRefNo = $grnDetails["reference"];
      $accountingRemarks =  $grnDetails['remarks'];
      $journalEntryReference = $grnDetails['journalEntryReference'];
      $documentDate = $grnDetails['documentDate'];
      $documentPostingDate = $grnDetails['postingDate'];

      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);
      // console($debitCreditAccListObj);
      // exit();

      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $roundOffGL = $accMapp['data']['0']['roundoff_gl'];

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


      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $accountingRefNo;
      $accounting['journal']['remark'] = $accountingRemarks;
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting['journal']['documentNo'] = $accountingDocumentNo;
      $accounting['journal']['documentDate'] = $documentDate;
      $accounting['journal']['postingDate'] = $documentPostingDate;


      $totalamount = 0;
      $tdstotal = 0;
      foreach ($grnItems  as $drkey => $drvalue) {
        $accounting['debit'][] = [
          'glId' => $debitAccList[0]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $drvalue['baseAmount'],
          'debit_remark' => ''
        ];

        $totalamount += $drvalue['baseAmount'];
      }
      if (!empty($imputgst['igst']) && ($imputgst['igst'] > 0)) {
        //-------------------------IGST------------------------          
        $totalamount += $imputgst['igst'];

        $accounting['debit'][] = [
          'glId' => $debitAccList[1]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['igst'],
          'debit_remark' => ''
        ];
      } else {
        //-------------------------CGST------------------------
        $totalamount += $imputgst['cgst'];

        $accounting['debit'][] = [
          'glId' => $debitAccList[2]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['cgst'],
          'debit_remark' => ''
        ];
        //-------------------------SGST------------------------
        $totalamount += $imputgst['sgst'];

        $accounting['debit'][] = [
          'glId' => $debitAccList[3]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['sgst'],
          'debit_remark' => ''
        ];
      }

      if (isset($inputes['tdsDetails']) && !empty($inputes['tdsDetails'])) {
        $tdstotal = array_sum($inputes['tdsDetails']);

        $accounting['credit'][] = [
          'glId' => $creditAccList[1]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $tdstotal,
          'credit_remark' => 'TDS Amount'
        ];
        $totalamount -= $tdstotal;
      }

      if (isset($inputes['tcsDetails']) && !empty($inputes['tcsDetails'])) {
        $tcstotal = array_sum($inputes['tcsDetails']);

        $accounting['debit'][] = [
          'glId' => $debitAccList[4]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $tcstotal,
          'debit_remark' => 'TCS Amount'
        ];
        $totalamount += $tcstotal;
      }


      //-------------------Vendor G/L-----------------------------
      $totalamount += $roundOffValue;
      $accounting['credit'][] = [
        'glId' => $creditAccList[0]['id'],
        'subGlCode' => $vendorDetails['vendorCode'],
        'subGlName' => $vendorDetails['vendorName'],
        'credit_amount' => $totalamount,
        'credit_remark' => ''
      ];


      if (!empty($roundOffGL) && $roundOffValue != 0) {
        if ($roundOffValue < 0) {
          $accounting['credit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => abs($roundOffValue),
            'credit_remark' => 'Rounding off'
          ];
        } else {
          $accounting['debit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => abs($roundOffValue),
            'debit_remark' => 'Rounding off'
          ];
        }
      }


      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }
  function grnIvAccountingPostingItc($inputes, $functionSlug, $parent_id)
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
      $roundOffValue = $inputes["roundOffValue"] ?? 0;

      $grnPostingJournalId = $grnDetails["grnJournalId"];
      $accountingDocumentNo = $grnDetails["documentNo"];
      $accountingRefNo = $grnDetails["reference"];
      $accountingRemarks =  $grnDetails['remarks'];
      $journalEntryReference = $grnDetails['journalEntryReference'];
      $documentDate = $grnDetails['documentDate'];
      $documentPostingDate = $grnDetails['postingDate'];

      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $roundOffGL = $accMapp['data']['0']['roundoff_gl'];

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


      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $accountingRefNo;
      $accounting['journal']['remark'] = $accountingRemarks;
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting['journal']['documentNo'] = $accountingDocumentNo;
      $accounting['journal']['documentDate'] = $documentDate;
      $accounting['journal']['postingDate'] = $documentPostingDate;


      $totalamount = 0;
      $tdstotal = 0;
      foreach ($grnItems  as $drkey => $drvalue) {
        $accounting['debit'][] = [
          'glId' => $debitAccList[0]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $drvalue['baseAmount'],
          'debit_remark' => ''
        ];

        $totalamount += $drvalue['baseAmount'];
      }
     

      if (isset($inputes['tdsDetails']) && !empty($inputes['tdsDetails'])) {
        $tdstotal = array_sum($inputes['tdsDetails']);

        $accounting['credit'][] = [
          'glId' => $creditAccList[1]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $tdstotal,
          'credit_remark' => 'TDS Amount'
        ];
        $totalamount -= $tdstotal;
      }

      //-------------------Vendor G/L-----------------------------
      $totalamount += $roundOffValue;
      $accounting['credit'][] = [
        'glId' => $creditAccList[0]['id'],
        'subGlCode' => $vendorDetails['vendorCode'],
        'subGlName' => $vendorDetails['vendorName'],
        'credit_amount' => $totalamount,
        'credit_remark' => ''
      ];


      if (!empty($roundOffGL) && $roundOffValue != 0) {
        if ($roundOffValue < 0) {
          $accounting['credit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => abs($roundOffValue),
            'credit_remark' => 'Rounding off'
          ];
        } else {
          $accounting['debit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => abs($roundOffValue),
            'debit_remark' => 'Rounding off'
          ];
        }
      }


      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
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


      $party_code = $inputes['party_code'] ?? "";
      $party_name = $inputes['party_name'] ?? "";


      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['referenceNo'];
      $accounting['journal']['remark'] = $inputes['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['invoicePostingDate'];


      $drItems = $inputes['grnItemList'];

      foreach ($drItems  as $drkey => $drvalue) {
        $accounting['credit'][] = [
          'glId' => $creditAccList[0]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $drvalue['itemTotalPrice'],
          'credit_remark' => ''
        ];

        $accounting['debit'][] = [
          'glId' => $drvalue['parentGlId'],
          'subGlCode' => $drvalue['itemCode'],
          'subGlName' => $drvalue['itemName'],
          'debit_amount' => $drvalue['itemTotalPrice'],
          'debit_remark' => ''
        ];
      }
      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
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
    // return $inputes;
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
      $rcm = $imputgst['rcm'];
      $imputtds = $inputes['tdsDetails'];
      $roundOffValue = $inputes["roundOffValue"] ?? 0;

      $grnPostingJournalId = $grnDetails["grnJournalId"];
      $accountingDocumentNo = $grnDetails["documentNo"];
      $accountingRefNo = $grnDetails["reference"];
      $accountingRemarks =  $grnDetails['remarks'];
      $journalEntryReference = $grnDetails['journalEntryReference'];
      $documentDate = $grnDetails['documentDate'];
      $documentPostingDate = $grnDetails['postingDate'];

      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $roundOffGL = $accMapp['data']['0']['roundoff_gl'];

      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $party_code = $vendorDetails['vendorCode'] ?? "";
      $party_name = addslashes($vendorDetails['vendorName']) ?? "";


      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $accountingRefNo;
      $accounting['journal']['remark'] = $accountingRemarks;
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting['journal']['documentNo'] = $accountingDocumentNo;
      $accounting['journal']['documentDate'] = $documentDate;
      $accounting['journal']['postingDate'] = $documentPostingDate;


      $totalamount = 0;
      $tdstotal = 0;
      $totalgst = 0;
      foreach ($grnItems  as $drkey => $drvalue) {
        $accounting['debit'][] = [
          'glId' => $debitAccList[0]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $drvalue['baseAmount'],
          'debit_remark' => ''
        ];

        $totalamount += $drvalue['baseAmount'];
      }
      if (!empty($imputgst['igst']) && ($imputgst['igst'] > 0)) {
        //-------------------------IGST------------------------          
        $totalamount += $imputgst['igst'];
        $totalgst += $imputgst['igst'];

        $accounting['debit'][] = [
          'glId' => $debitAccList[1]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['igst'],
          'debit_remark' => ''
        ];
      } else {
        //-------------------------CGST------------------------
        $totalamount += $imputgst['cgst'];
        $totalgst += $imputgst['cgst'];

        $accounting['debit'][] = [
          'glId' => $debitAccList[2]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['cgst'],
          'debit_remark' => ''
        ];
        //-------------------------SGST------------------------
        $totalamount += $imputgst['sgst'];
        $totalgst += $imputgst['sgst'];

        $accounting['debit'][] = [
          'glId' => $debitAccList[3]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['sgst'],
          'debit_remark' => ''
        ];
      }
      if ($rcm == 1) {
        if (isset($creditAccList[2]['id'])) {
          $accounting['credit'][] = [
            'glId' => $creditAccList[2]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => $totalgst,
            'credit_remark' => 'RCM Amount'
          ];
          $totalamount -= $totalgst;
        }
      }

      if (isset($inputes['tdsDetails']) && !empty($inputes['tdsDetails'])) {
        $tdstotal = array_sum($inputes['tdsDetails']);

        $accounting['credit'][] = [
          'glId' => $creditAccList[1]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $tdstotal,
          'credit_remark' => 'TDS Amount'
        ];
        $totalamount -= $tdstotal;
      }

      if (isset($inputes['tcsDetails']) && !empty($inputes['tcsDetails'])) {
        $tcstotal = array_sum($inputes['tcsDetails']);

        $accounting['debit'][] = [
          'glId' => $debitAccList[4]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $tcstotal,
          'debit_remark' => 'TCS Amount'
        ];
        $totalamount += $tcstotal;
      }

      //-------------------Vendor G/L-----------------------------
      $totalamount += $roundOffValue;
      $accounting['credit'][] = [
        'glId' => $creditAccList[0]['id'],
        'subGlCode' => $vendorDetails['vendorCode'],
        'subGlName' => addslashes($vendorDetails['vendorName']),
        'credit_amount' => $totalamount,
        'credit_remark' => ''
      ];



      if (!empty($roundOffGL) && $roundOffValue != 0) {
        if ($roundOffValue < 0) {
          $accounting['credit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => abs($roundOffValue),
            'credit_remark' => 'Rounding off'
          ];
        } else {
          $accounting['debit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => abs($roundOffValue),
            'debit_remark' => 'Rounding off'
          ];
        }
      }

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
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


      $party_code = $inputes['customerDetails']['customerCode'] ?? "";
      $party_name = $inputes['customerDetails']['customerName'] ?? "";


      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $accountingRefNo;
      $accounting['journal']['remark'] = $accountingRemarks;
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting['journal']['documentNo'] = $accountingDocumentNo;
      $accounting['journal']['documentDate'] = $documentDate;
      $accounting['journal']['postingDate'] = $documentPostingDate;

      foreach ($pgiItems  as $crkey => $pgivalue) {
        // $pgiItentax = 0;
        // if (isset($pgivalue['itemTotalTax1'])) {
        //   $pgiItentax = $pgivalue['itemTotalTax1'];
        // } else {
        //   $pgiItentax = $pgivalue['totalTax'];
        // }
        // $withouttaxamount = $pgivalue['totalPrice'] - $pgiItentax;

        $withouttaxamount = $pgivalue['goodsMainPrice'] * $pgivalue['qty'];

        $accounting['credit'][] = [
          'glId' => $pgivalue['parentGlId'],
          'subGlCode' => $pgivalue['itemCode'],
          'subGlName' => $pgivalue['itemName'],
          'credit_amount' => $withouttaxamount,
          'credit_remark' => ''
        ];

        $accounting['debit'][] = [
          'glId' => $debitAccList[0]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $withouttaxamount,
          'debit_remark' => ''
        ];
      }

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }

  function sopgiAccountingPostingByRuleBook($inputes, $functionSlug, $parent_id)
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


      $party_code = $inputes['customerDetails']['customerCode'] ?? "";
      $party_name = $inputes['customerDetails']['customerName'] ?? "";


      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $accountingRefNo;
      $accounting['journal']['remark'] = $accountingRemarks;
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting['journal']['documentNo'] = $accountingDocumentNo;
      $accounting['journal']['documentDate'] = $documentDate;
      $accounting['journal']['postingDate'] = $documentPostingDate;

      foreach ($pgiItems  as $crkey => $pgivalue) {
        // $pgiItentax = 0;
        // if (isset($pgivalue['itemTotalTax1'])) {
        //   $pgiItentax = $pgivalue['itemTotalTax1'];
        // } else {
        //   $pgiItentax = $pgivalue['totalTax'];
        // }
        // $withouttaxamount = $pgivalue['totalPrice'] - $pgiItentax;

        $withouttaxamount = $pgivalue['goodsMainPrice'] * $pgivalue['qty'];

        $accounting['credit'][] = [
          'glId' => $pgivalue['parentGlId'],
          'subGlCode' => $pgivalue['itemCode'],
          'subGlName' => $pgivalue['itemName'],
          'credit_amount' => $withouttaxamount,
          'credit_remark' => ''
        ];

        $accounting['debit'][] = [
          'glId' => $debitAccList[0]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $withouttaxamount,
          'debit_remark' => ''
        ];
      }

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
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
      $compInvoiceType = $inputes["compInvoiceType"];
      $soivItems = $inputes["FGItems"];
      $customerDetails = $inputes['customerDetails'];
      $outputgst = $inputes['taxDetails'];
      $roundOffValue = $inputes["roundOffValue"] ?? 0;

      $soivPostingJournalId = $soivDetails["soivJournalId"] ?? 0;
      $accountingDocumentNo = $soivDetails["documentNo"];
      $accountingRefNo = $soivDetails["reference"];
      $accountingRemarks =  $soivDetails['remarks'];
      $journalEntryReference = $soivDetails['journalEntryReference'];
      $documentDate = $soivDetails['documentDate'];
      $documentPostingDate = $soivDetails['postingDate'];

      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $roundOffGL = $accMapp['data']['0']['roundoff_gl'];
      $compInvoiceTypeval = 'domestic';
      if ($compInvoiceType == 'R') {
        //Domestic Transaction
        $compInvoiceTypeval = 'domestic';
      } else {
        //Export Transaction        
        $compInvoiceTypeval = 'export';
      }

      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);
      // console($debitCreditAccListObj);
      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available" . $company_id
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $party_code = $customerDetails['customerCode'] ?? "";
      $party_name = $customerDetails['customerName'] ?? "";
      //  `parent_id`='" . $soivPostingJournalId . "',

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $accountingRefNo;
      $accounting['journal']['remark'] = $accountingRemarks;
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting['journal']['documentNo'] = $accountingDocumentNo;
      $accounting['journal']['documentDate'] = $documentDate;
      $accounting['journal']['postingDate'] = $documentPostingDate;

      $totalamount = 0;
      foreach ($soivItems  as $crkey => $crvalue) {
        $goodsType = $crvalue['goodsType'];
        $salesAccc = 'sales_goods_domestic';
        if ($compInvoiceTypeval == 'domestic') {
          if ($goodsType == 5 || $goodsType == 7 || $goodsType == 10) {
            $salesAccc = 'sales_services_domestic';
          } else if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
            $salesAccc = 'sales_goods_domestic';
          } else {
            $salesAccc = 'sales_goods_domestic';
          }
        } else if ($compInvoiceTypeval == 'export') {
          if ($goodsType == 5 || $goodsType == 7 || $goodsType == 10) {
            $salesAccc = 'sales_services_export';
          } else if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
            $salesAccc = 'sales_goods_export';
          } else {
            $salesAccc = 'sales_goods_export';
          }
        } else {
          $salesAccc = 'sales_goods_export';
        }



        $pgiItentax = 0;
        if (isset($crvalue['itemTotalTax1'])) {
          $pgiItentax = $crvalue['itemTotalTax1'];
        } else {
          $pgiItentax = $crvalue['totalTax'];
        }
        $withouttaxamount = $crvalue['totalPrice'] - $pgiItentax;

        // $creditGLId = $creditAccList[0]['id'];
        $creditGLId = $accMapp['data']['0'][$salesAccc];

        $accounting['credit'][] = [
          'glId' => $creditGLId,
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $withouttaxamount,
          'credit_remark' => ''
        ];

        $totalamount += $withouttaxamount;
      }
      if ($outputgst['igst'] > 0) {
        //-------------------------IGST------------------------
        $accounting['credit'][] = [
          'glId' => $creditAccList[1]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $outputgst['igst'],
          'credit_remark' => ''
        ];

        $totalamount += $outputgst['igst'];
      } else {
        //-------------------------CGST------------------------
        $accounting['credit'][] = [
          'glId' => $creditAccList[2]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $outputgst['cgst'],
          'credit_remark' => ''
        ];
        $totalamount += $outputgst['cgst'];
        //-------------------------SGST------------------------
        $accounting['credit'][] = [
          'glId' => $creditAccList[3]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $outputgst['sgst'],
          'credit_remark' => ''
        ];
        $totalamount += $outputgst['sgst'];
      }

      if ($outputgst['TCS'] > 0) {
        //-------------------------IGST------------------------
        $accounting['credit'][] = [
          'glId' => $creditAccList[4]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $outputgst['TCS'],
          'credit_remark' => ''
        ];
        $totalamount += $outputgst['TCS'];
      }

      //-------------------Customer G/L-----------------------------
      $totalamount += $roundOffValue;
      $accounting['debit'][] = [
        'glId' => $debitAccList[0]['id'] ?? $customerDetails['customerGlId'],
        'subGlCode' => $customerDetails['customerCode'],
        'subGlName' => $customerDetails['customerName'],
        'debit_amount' => $totalamount,
        'debit_remark' => ''
      ];

      if (!empty($roundOffGL) && $roundOffValue != 0) {
        if ($roundOffValue < 0) {
          $accounting['debit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => abs($roundOffValue),
            'debit_remark' => 'Rounding off'
          ];
        } else {
          $accounting['credit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => abs($roundOffValue),
            'credit_remark' => 'Rounding off'
          ];
        }
      }


      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }

  function soIvAccountingPostingByRuleBook($inputes, $functionSlug, $parent_id)
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
      $compInvoiceType = $inputes["compInvoiceType"];
      $soivItems = $inputes["FGItems"];
      $customerDetails = $inputes['customerDetails'];
      $outputgst = $inputes['taxDetails'];
      $roundOffValue = $inputes["roundOffValue"] ?? 0;

      $soivPostingJournalId = $soivDetails["soivJournalId"] ?? 0;
      $accountingDocumentNo = $soivDetails["documentNo"];
      $accountingRefNo = $soivDetails["reference"];
      $accountingRemarks =  $soivDetails['remarks'];
      $journalEntryReference = $soivDetails['journalEntryReference'];
      $documentDate = $soivDetails['documentDate'];
      $documentPostingDate = $soivDetails['postingDate'];

      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $roundOffGL = $accMapp['data']['0']['roundoff_gl'];
      $compInvoiceTypeval = 'domestic';
      if ($compInvoiceType == 'R') {
        //Domestic Transaction
        $compInvoiceTypeval = 'domestic';
      } else {
        //Export Transaction        
        $compInvoiceTypeval = 'export';
      }

      $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);
      // console($debitCreditAccListObj);
      
      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available" . $company_id
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $party_code = $customerDetails['customerCode'] ?? "";
      $party_name = $customerDetails['customerName'] ?? "";
      //  `parent_id`='" . $soivPostingJournalId . "',

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $accountingRefNo;
      $accounting['journal']['remark'] = $accountingRemarks;
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting['journal']['documentNo'] = $accountingDocumentNo;
      $accounting['journal']['documentDate'] = $documentDate;
      $accounting['journal']['postingDate'] = $documentPostingDate;

      $totalamount = 0;
      foreach ($soivItems  as $crkey => $crvalue) {
        $goodsType = $crvalue['goodsType'];
        $salesAccc = 'sales_goods_domestic';
        if ($compInvoiceTypeval == 'domestic') {
          if ($goodsType == 5 || $goodsType == 7 || $goodsType == 10) {
            $salesAccc = 'sales_services_domestic';
          } else if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
            $salesAccc = 'sales_goods_domestic';
          } else {
            $salesAccc = 'sales_goods_domestic';
          }
        } else if ($compInvoiceTypeval == 'export') {
          if ($goodsType == 5 || $goodsType == 7 || $goodsType == 10) {
            $salesAccc = 'sales_services_export';
          } else if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
            $salesAccc = 'sales_goods_export';
          } else {
            $salesAccc = 'sales_goods_export';
          }
        } else {
          $salesAccc = 'sales_goods_export';
        }



        $pgiItentax = 0;
        if (isset($crvalue['itemTotalTax1'])) {
          $pgiItentax = $crvalue['itemTotalTax1'];
        } else {
          $pgiItentax = $crvalue['totalTax'];
        }
        $withouttaxamount = $crvalue['totalPrice'] - $pgiItentax;

        // $creditGLId = $creditAccList[0]['id'];
        $creditGLId = $accMapp['data']['0'][$salesAccc];

        $accounting['credit'][] = [
          'glId' => $creditGLId,
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $withouttaxamount,
          'credit_remark' => ''
        ];

        $totalamount += $withouttaxamount;
      }
     
        //-------------------------GST------------------------
        $accounting['credit'][] = [
          'glId' => $creditAccList[1]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $outputgst['gst'],
          'credit_remark' => ''
        ];

        $totalamount += $outputgst['gst'];

      if ($outputgst['TCS'] > 0) {
        $accounting['credit'][] = [
          'glId' => $creditAccList[4]['id'],
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $outputgst['TCS'],
          'credit_remark' => ''
        ];
        $totalamount += $outputgst['TCS'];
      }

      //-------------------Customer G/L-----------------------------
      $totalamount += $roundOffValue;
      $accounting['debit'][] = [
        'glId' => $debitAccList[0]['id'] ?? $customerDetails['customerGlId'],
        'subGlCode' => $customerDetails['customerCode'],
        'subGlName' => $customerDetails['customerName'],
        'debit_amount' => $totalamount,
        'debit_remark' => ''
      ];

      if (!empty($roundOffGL) && $roundOffValue != 0) {
        if ($roundOffValue < 0) {
          $accounting['debit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => abs($roundOffValue),
            'debit_remark' => 'Rounding off'
          ];
        } else {
          $accounting['credit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => abs($roundOffValue),
            'credit_remark' => 'Rounding off'
          ];
        }
      }
      // console($accounting);

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }



  //SO Collections Accounting 10-01-2023
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


      $customerDetails = $inputes['vendorDetails'];
      $roundOffValue = $inputes["roundOffValue"] ?? 0;
      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $roundOffGL = $accMapp['data']['0']['roundoff_gl'];


      $party_code = $customerDetails['customer_code'] ?? "";
      $party_name = $customerDetails['trade_name'] ?? "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];


      $paymentDetails = reset($inputes['paymentDetails']);
      $crItems = $inputes['paymentInvItems'];
      $dramount = 0;
      $sqlBankCash = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='" . $paymentDetails['bankId'] . "'";
      $queryBankCash = queryGet($sqlBankCash)['data'];
      foreach ($crItems  as $crkey => $crvalue) {
        if (!empty($crvalue['recAmt'])) {
          $accounting['credit'][] = [
            'glId' => $customerDetails['parentGlId'],
            'subGlCode' => $customerDetails['customer_code'],
            'subGlName' => $customerDetails['trade_name'],
            'credit_amount' => $crvalue['recAmt'],
            'credit_remark' => "Payment For - " . $crvalue['invoiceNo']
          ];
          $dramount += $crvalue['recAmt'];
        }
      }

      $advance = $paymentDetails['collectPayment'] - $dramount;
      if ($advance > 0) {
        $dramount += $advance;
        $accounting['credit'][] = [
          'glId' => $customerDetails['parentGlId'],
          'subGlCode' => $customerDetails['customer_code'],
          'subGlName' => $customerDetails['trade_name'],
          'credit_amount' => $advance,
          'credit_remark' => 'Advance Amount'
        ];
      }
      $accounting['debit'][] = [
        'glId' => $queryBankCash['parent_gl'],
        'subGlCode' => $queryBankCash['acc_code'],
        'subGlName' => $queryBankCash['bank_name'],
        'debit_amount' => $dramount + $roundOffValue,
        'debit_remark' => ''
      ];

      if (!empty($roundOffGL) && $roundOffValue != 0) {
        if ($roundOffValue < 0) {
          $accounting['debit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => abs($roundOffValue),
            'debit_remark' => 'Rounding off'
          ];
        } else {
          $accounting['credit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => abs($roundOffValue),
            'credit_remark' => 'Rounding off'
          ];
        }
      }
      // return $accounting;

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }


  //Invoice Collection Accounting 22-02-2024
  function multicollectionAccountingPosting($inputes, $functionSlug, $parent_id)
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

      $paymentDetails = $inputes['paymentDetails'];
      $flug = 0;

      foreach ($paymentDetails as $vendKey => $paymentDetails) {

        // Default fields
        $crItems = $paymentDetails['paymentItems'];
        $dramount = 0;

        $accMapp = getAllfetchAccountingMappingTbl($company_id);
        $roundOffGL = $accMapp['data']['0']['roundoff_gl'];
        $writeoffGL = $accMapp['data']['0']['writtenoff_gl'];
        $financialChargeGL = $accMapp['data']['0']['bankcharges_gl'];
        $forexGL = $accMapp['data']['0']['foreignexchange_gl'];

        $party_gl = $paymentDetails['customer_parentGlId'] ?? 0;
        $party_code = $paymentDetails['customer_code'] ?? "";
        $party_name = $paymentDetails['customer_name'] ?? "";

        $parent_id = $paymentDetails['paymentId'];
        $refarenceCode = $paymentDetails['paymentCode'];
        $bankId = $paymentDetails['bankId'];

        // Create the accounting array
        $accounting = array();
        $accounting['journal']['parent_id'] = $parent_id;
        $accounting['journal']['parent_slug'] = $functionSlug;
        $accounting['journal']['refarenceCode'] = $refarenceCode;
        $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
        $accounting['journal']['party_code'] = $party_code;
        $accounting['journal']['party_name'] = $party_name;
        $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
        $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
        $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
        $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];

        // Query for bank/cash account
        $sqlBankCash = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='" . $bankId . "'";
        $queryBankCash = queryGet($sqlBankCash)['data'];

        // Calculate the amounts
        foreach ($crItems as $crkey => $crvalue) {
          $dramount += $crvalue['recAmt'];

          $roundOffValue = $crvalue["roundoff"] ?? 0;
          $writeoff = $crvalue["writeoff"] ?? 0;
          $financialCharge = $crvalue["financial_charge"] ?? 0;
          $forex = $crvalue["forex"] ?? 0;
          $tds = $crvalue["tds"] ?? 0;

          // First Entry: Credit for payment items
          $accounting['credit'][] = [
            'glId' => $party_gl,
            'subGlCode' => $party_code,
            'subGlName' => $party_name,
            'credit_amount' => $crvalue['recAmt'],
            'credit_remark' => 'Payment For - ' . $crvalue['invoiceNo']
          ];
        }

        // Calculate advance amount
        $advance = ($paymentDetails['collectPayment'] - $dramount) ?? 0;

        // if ($advance > 0) {
        //     // Second Entry: Credit for advance amount
        //     $dramount += $advance;
        //     $accounting['credit'][] = [
        //         'glId' => $party_gl,
        //         'subGlCode' => $party_code,
        //         'subGlName' => $party_name,
        //         'credit_amount' => $advance,
        //         'credit_remark' => 'Advance Amount'
        //     ];
        // }

        // Fourth Entry: Debit for Bank Cash (for total amount)
        $totaldebitamount = $dramount + $roundOffValue + $writeoff + $financialCharge + $forex + $tds;
        $accounting['debit'][] = [
          'glId' => $queryBankCash['parent_gl'],
          'subGlCode' => $queryBankCash['acc_code'],
          'subGlName' => $queryBankCash['bank_name'],
          'debit_amount' => $totaldebitamount,
          'debit_remark' => 'Payment for invoice'
        ];
        if (!empty($roundOffGL) && $roundOffValue != 0) {
          if ($roundOffValue < 0) {
            $accounting['debit'][] = [
              'glId' => $roundOffGL,
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => abs($roundOffValue),
              'debit_remark' => 'Rounding off'
            ];
          } else {
            $accounting['credit'][] = [
              'glId' => $roundOffGL,
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => abs($roundOffValue),
              'credit_remark' => 'Rounding off'
            ];
          }
        }
        if (!empty($writeoffGL) && $writeoff != 0) {
          if ($writeoff < 0) {
            $accounting['debit'][] = [
              'glId' => $writeoffGL,
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => abs($writeoff),
              'debit_remark' => 'Writeback'
            ];
          } else {
            $accounting['credit'][] = [
              'glId' => $writeoffGL,
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => abs($writeoff),
              'credit_remark' => 'Writeback'
            ];
          }
        }
        if (!empty($financialChargeGL) && $financialCharge != 0) {
          if ($financialCharge < 0) {
            $accounting['debit'][] = [
              'glId' => $financialChargeGL,
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => abs($financialCharge),
              'debit_remark' => 'FinancialCharge'
            ];
          } else {
            $accounting['credit'][] = [
              'glId' => $financialChargeGL,
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => abs($financialCharge),
              'credit_remark' => 'FinancialCharge'
            ];
          }
        }
        if (!empty($forexGL) && $forex != 0) {
          if ($forex < 0) {
            $accounting['debit'][] = [
              'glId' => $forexGL,
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => abs($forex),
              'debit_remark' => 'Forex'
            ];
          } else {
            $accounting['credit'][] = [
              'glId' => $forexGL,
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => abs($forex),
              'credit_remark' => 'Forex'
            ];
          }
        }
        if (!empty($tdsGlId) && $tds != 0) {
          if ($tds < 0) {
            $accounting['debit'][] = [
              'glId' => $tdsGlId,
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => abs($tds),
              'debit_remark' => 'Forex'
            ];
          } else {
            $accounting['credit'][] = [
              'glId' => $tdsGlId,
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => abs($tds),
              'credit_remark' => 'Forex'
            ];
          }
        }

        // console($accounting);


        // Submit accounting posting
        $accPostingObj = new AccountingPosting();
        $accresponce = $accPostingObj->post($accounting);

        $returnData['accountingPosting'][$refarenceCode] = $accresponce;
        if ($accresponce['status'] == 'success') {
          $JournalId = $accresponce['journalId'];
          $sqlpayment = "UPDATE `" . ERP_GRN_PAYMENTS . "`
                            SET
                                `journal_id`=$JournalId 
                            WHERE `payment_id`='$parent_id'  ";
          queryUpdate($sqlpayment);
        } else {
          $flug++;
        }
      }

      if ($flug == 0) {
        $returnData['status'] = "success";
        $returnData['message'] = "Accounting posting has been successfully submitted.";
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong in account posting.";
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }

    $returnData['accounting'] = $accounting;
    $returnData['post'] = $inputes;
    return $returnData;
  }




  //GRNIV Payment Accounting 10-01-2023
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

      $vendorDetails = $inputes['vendorDetails'];

      $roundOffValue = $inputes["roundOffValue"] ?? 0;
      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $roundOffGL = $accMapp['data']['0']['roundoff_gl'];

      $party_code = $vendorDetails['vendor_code'] ?? "";
      $party_name = $vendorDetails['trade_name'] ?? "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];

      $paymentDetails = $inputes['paymentDetails'];
      //console($vendorDetails);
      $crItems = $inputes['paymentInvItems'];
      $dramount = 0;
      $sqlBankCash = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='" . $paymentDetails['bankId'] . "'";
      $queryBankCash = queryGet($sqlBankCash)['data'];
      //console($queryBankCash);
      foreach ($crItems  as $crkey => $crvalue) {
        $dramount += $crvalue['recAmt'];
        $accounting['debit'][] = [
          'glId' => $vendorDetails['parentGlId'],
          'subGlCode' => $vendorDetails['vendor_code'],
          'subGlName' => $vendorDetails['trade_name'],
          'debit_amount' => $crvalue['recAmt'],
          'debit_remark' => 'Payment For -' . $crvalue['invoiceNo']
        ];
      }
      $advance = $paymentDetails['collectPayment'] - $dramount;
      if ($advance > 0) {
        $accounting['debit'][] = [
          'glId' => $vendorDetails['parentGlId'],
          'subGlCode' => $vendorDetails['vendor_code'],
          'subGlName' => $vendorDetails['trade_name'],
          'debit_amount' => $advance,
          'debit_remark' => 'Advance Amount'
        ];
        $dramount += $advance;
      }
      $totalcreditamount = $dramount + $roundOffValue;
      $accounting['credit'][] = [
        'glId' => $queryBankCash['parent_gl'],
        'subGlCode' => $paymentDetails['accCode'],
        'subGlName' => $paymentDetails['accName'],
        'credit_amount' => $totalcreditamount,
        'credit_remark' => ''
      ];


      if (!empty($roundOffGL) && $roundOffValue != 0) {
        if ($roundOffValue < 0) {
          $accounting['credit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => abs($roundOffValue),
            'credit_remark' => 'Rounding off'
          ];
        } else {
          $accounting['debit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => abs($roundOffValue),
            'debit_remark' => 'Rounding off'
          ];
        }
      }


      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }


  //GRNIV Payment Accounting 22-02-2024
  function multipaymentAccountingPosting($inputes, $functionSlug, $parent_id)
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

      $paymentDetails = $inputes['paymentDetails'];
      $flug = 0;
      foreach ($paymentDetails as $vendKey => $paymentDetails) {

        $roundOffValue = $paymentDetails["roundOffValue"] ?? 0;
        $writeback = $paymentDetails["writeback"] ?? 0;
        $financialCharge = $paymentDetails["financial_charge"] ?? 0;
        $forex = $paymentDetails["forex"] ?? 0;

        $accMapp = getAllfetchAccountingMappingTbl($company_id);
        $roundOffGL = $accMapp['data']['0']['roundoff_gl'];
        $writebackGL = $accMapp['data']['0']['writtenback_gl'];
        $financialChargeGL = $accMapp['data']['0']['bankcharges_gl'];
        $forexGL = $accMapp['data']['0']['foreignexchange_gl'];

        $party_gl = $paymentDetails['vendorParentGl'] ?? 0;
        $party_code = $paymentDetails['vendor_code'] ?? "";
        $party_name = $paymentDetails['vendor_name'] ?? "";

        $parent_id = $paymentDetails['paymentId'];
        $refarenceCode = $paymentDetails['paymentCode'];
        $bankId = $paymentDetails['bankId'];

        $accounting = array();
        $accounting['journal']['parent_id'] = $parent_id;
        $accounting['journal']['parent_slug'] = $functionSlug;
        $accounting['journal']['refarenceCode'] = $refarenceCode;
        $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
        $accounting['journal']['party_code'] = $party_code;
        $accounting['journal']['party_name'] = $party_name;
        $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
        $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
        $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
        $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];

        //console($paymentDetails);
        $crItems = $paymentDetails['paymentItems'];
        $dramount = 0;
        $sqlBankCash = "SELECT * FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='" . $bankId . "'";
        $queryBankCash = queryGet($sqlBankCash)['data'];
        //console($queryBankCash);
        foreach ($crItems  as $crkey => $crvalue) {
          $dramount += $crvalue['recAmt'];
          $accounting['debit'][] = [
            'glId' => $party_gl,
            'subGlCode' => $party_code,
            'subGlName' => $party_name,
            'debit_amount' => $crvalue['recAmt'],
            'debit_remark' => 'Payment For -' . $crvalue['grnCode']
          ];
        }

        $totalcreditamount = $dramount + $roundOffValue + $writeback + $financialCharge + $forex;
        $accounting['credit'][] = [
          'glId' => $queryBankCash['parent_gl'],
          'subGlCode' => $queryBankCash['acc_code'],
          'subGlName' => $queryBankCash['bank_name'],
          'credit_amount' => $totalcreditamount,
          'credit_remark' => ''
        ];


        if (!empty($roundOffGL) && $roundOffValue != 0) {
          if ($roundOffValue < 0) {
            $accounting['credit'][] = [
              'glId' => $roundOffGL,
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => abs($roundOffValue),
              'credit_remark' => 'Rounding off'
            ];
          } else {
            $accounting['debit'][] = [
              'glId' => $roundOffGL,
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => abs($roundOffValue),
              'debit_remark' => 'Rounding off'
            ];
          }
        }

        if (!empty($writebackGL) && $writeback != 0) {
          if ($writeback < 0) {
            $accounting['credit'][] = [
              'glId' => $writebackGL,
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => abs($writeback),
              'credit_remark' => 'Writeback'
            ];
          } else {
            $accounting['debit'][] = [
              'glId' => $writebackGL,
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => abs($writeback),
              'debit_remark' => 'Writeback'
            ];
          }
        }

        if (!empty($financialChargeGL) && $financialCharge != 0) {
          if ($financialCharge < 0) {
            $accounting['credit'][] = [
              'glId' => $financialChargeGL,
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => abs($financialCharge),
              'credit_remark' => 'FinancialCharge'
            ];
          } else {
            $accounting['debit'][] = [
              'glId' => $financialChargeGL,
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => abs($financialCharge),
              'debit_remark' => 'FinancialCharge'
            ];
          }
        }

        if (!empty($forexGL) && $forex != 0) {
          if ($forex < 0) {
            $accounting['credit'][] = [
              'glId' => $forexGL,
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => abs($forex),
              'credit_remark' => 'Forex'
            ];
          } else {
            $accounting['debit'][] = [
              'glId' => $forexGL,
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => abs($forex),
              'debit_remark' => 'Forex'
            ];
          }
        }


        $accPostingObj = new AccountingPosting();
        $accresponce = $accPostingObj->post($accounting);
        $returnData['accountingPosting'][$refarenceCode] = $accresponce;
        if ($accresponce['status'] == 'success') {
          $JournalId = $accresponce['journalId'];
          $sqlpayment = "UPDATE `" . ERP_GRN_PAYMENTS . "`
                        SET
                            `journal_id`=$JournalId 
                        WHERE `payment_id`='$parent_id'  ";
          queryUpdate($sqlpayment);
        } else {
          $flug++;
        }
      }
      if ($flug == 0) {
        $returnData['status'] = "success";
        $returnData['message'] = "Accounting posting has been successfully submitted.";
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "Something went wrong in account posting.";
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }


  //Payroll Accounting 25-06-2023
  function payrollAccountingPosting($inputes, $functionSlug, $parent_id)
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

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];

      $payrollDetails = $inputes['payrollDetails'];

      $pfPayble = $payrollDetails['sum_pf_employee'] + $payrollDetails['sum_pf_employeer'];
      $esiPayble = $payrollDetails['sum_esi_employee'] + $payrollDetails['sum_esi_employeer'];

      $sum_ptax = $payrollDetails['sum_ptax'];
      $sum_tds = $payrollDetails['sum_tds'];

      $sum_gross = $payrollDetails['sum_gross'];

      $slryPayble = $sum_gross + $payrollDetails['sum_pf_employee'] + $payrollDetails['sum_esi_employee'] + $sum_ptax + $sum_tds;
      $sum_pf_employeer = $payrollDetails['sum_pf_employeer'];
      $sum_esi_employeer = $payrollDetails['sum_esi_employeer'];
      $sum_salary_payable = ($sum_gross + $payrollDetails['sum_pf_employeer'] + $payrollDetails['sum_esi_employeer']) - ($pfPayble + $esiPayble + $sum_ptax + $sum_tds);
      $dramount = 0;


      //sum_pf
      $accounting['credit'][] = [
        'glId' => $creditAccList[0]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'credit_amount' => $pfPayble,
        'credit_remark' => ''
      ];
      //sum_esi
      $accounting['credit'][] = [
        'glId' => $creditAccList[1]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'credit_amount' => $esiPayble,
        'credit_remark' => ''
      ];
      //sum_ptax
      $accounting['credit'][] = [
        'glId' => $creditAccList[2]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'credit_amount' => $sum_ptax,
        'credit_remark' => ''
      ];
      //sum_tds
      $accounting['credit'][] = [
        'glId' => $creditAccList[3]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'credit_amount' => $sum_tds,
        'credit_remark' => ''
      ];
      //sum_gross
      $accounting['credit'][] = [
        'glId' => $creditAccList[4]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'credit_amount' => $sum_salary_payable,
        'credit_remark' => ''
      ];

      //----------------------DR----------------------
      $accounting['debit'][] = [
        'glId' => $debitAccList[0]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'debit_amount' => $sum_gross,
        'debit_remark' => ''
      ];
      $accounting['debit'][] = [
        'glId' => $debitAccList[1]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'debit_amount' => $sum_pf_employeer,
        'debit_remark' => ''
      ];
      $accounting['debit'][] = [
        'glId' => $debitAccList[2]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'debit_amount' => $sum_esi_employeer,
        'debit_remark' => ''
      ];


      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }

  //Payroll Accounting 01-07-2024
  function salaryPayrollAccountingPosting($inputs, $functionSlug, $parent_id)
  {
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;

    // Validate the inputs
    $isValidate = validate($inputs, [
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
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['referenceCode'] = $inputs['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputs['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputs['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputs['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputs['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputs['BasicDetails']['postingDate'];

      $payrollDetails = $inputs['payrollDetails'];

      $amount = $payrollDetails['amount'];
      $bank_gl = $payrollDetails['bank_gl'];
      $bank_code = $payrollDetails['bank_code'];
      $bank_name = $payrollDetails['bank_name'];

      $accounting['credit'][] = [
        'glId' => $bank_gl,
        'subGlCode' => $bank_code,
        'subGlName' => $bank_name,
        'credit_amount' => $amount,
        'credit_remark' => ''
      ];

      //----------------------DR----------------------
      $accounting['debit'][] = [
        'glId' => $creditAccList[4]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'debit_amount' => $amount,
        'debit_remark' => ''
      ];

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputs";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }

  //Payroll Accounting 01-07-2024
  function pfPayrollAccountingPosting($inputs, $functionSlug, $parent_id)
  {
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;

    // Validate the inputs
    $isValidate = validate($inputs, [
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
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['referenceCode'] = $inputs['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputs['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputs['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputs['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputs['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputs['BasicDetails']['postingDate'];

      $payrollDetails = $inputs['payrollDetails'];

      $amount = $payrollDetails['amount'];
      $bank_gl = $payrollDetails['bank_gl'];
      $bank_code = $payrollDetails['bank_code'];
      $bank_name = $payrollDetails['bank_name'];

      $accounting['credit'][] = [
        'glId' => $bank_gl,
        'subGlCode' => $bank_code,
        'subGlName' => $bank_name,
        'credit_amount' => $amount,
        'credit_remark' => ''
      ];

      //----------------------DR----------------------
      $accounting['debit'][] = [
        'glId' => $creditAccList[0]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'debit_amount' => $amount,
        'debit_remark' => ''
      ];

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputs";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }

  //Payroll Accounting 01-07-2024
  function esiPayrollAccountingPosting($inputs, $functionSlug, $parent_id)
  {
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;

    // Validate the inputs
    $isValidate = validate($inputs, [
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
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['referenceCode'] = $inputs['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputs['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputs['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputs['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputs['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputs['BasicDetails']['postingDate'];

      $payrollDetails = $inputs['payrollDetails'];

      $amount = $payrollDetails['amount'];
      $bank_gl = $payrollDetails['bank_gl'];
      $bank_code = $payrollDetails['bank_code'];
      $bank_name = $payrollDetails['bank_name'];

      $accounting['credit'][] = [
        'glId' => $bank_gl,
        'subGlCode' => $bank_code,
        'subGlName' => $bank_name,
        'credit_amount' => $amount,
        'credit_remark' => ''
      ];

      //----------------------DR----------------------
      $accounting['debit'][] = [
        'glId' => $creditAccList[1]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'debit_amount' => $amount,
        'debit_remark' => ''
      ];

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputs";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }

  //Payroll Accounting 01-07-2024
  function ptPayrollAccountingPosting($inputs, $functionSlug, $parent_id)
  {
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;

    // Validate the inputs
    $isValidate = validate($inputs, [
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
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['referenceCode'] = $inputs['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputs['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputs['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputs['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputs['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputs['BasicDetails']['postingDate'];

      $payrollDetails = $inputs['payrollDetails'];

      $amount = $payrollDetails['amount'];
      $bank_gl = $payrollDetails['bank_gl'];
      $bank_code = $payrollDetails['bank_code'];
      $bank_name = $payrollDetails['bank_name'];

      $accounting['credit'][] = [
        'glId' => $bank_gl,
        'subGlCode' => $bank_code,
        'subGlName' => $bank_name,
        'credit_amount' => $amount,
        'credit_remark' => ''
      ];

      //----------------------DR----------------------
      $accounting['debit'][] = [
        'glId' => $creditAccList[2]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'debit_amount' => $amount,
        'debit_remark' => ''
      ];

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputs";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }

  //Payroll Accounting 01-07-2024
  function tdsPayrollAccountingPosting($inputs, $functionSlug, $parent_id)
  {
    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;

    // Validate the inputs
    $isValidate = validate($inputs, [
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
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['referenceCode'] = $inputs['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputs['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputs['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputs['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputs['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputs['BasicDetails']['postingDate'];

      $payrollDetails = $inputs['payrollDetails'];

      $amount = $payrollDetails['amount'];
      $bank_gl = $payrollDetails['bank_gl'];
      $bank_code = $payrollDetails['bank_code'];
      $bank_name = $payrollDetails['bank_name'];

      $accounting['credit'][] = [
        'glId' => $bank_gl,
        'subGlCode' => $bank_code,
        'subGlName' => $bank_name,
        'credit_amount' => $amount,
        'credit_remark' => ''
      ];

      //----------------------DR----------------------
      $accounting['debit'][] = [
        'glId' => $creditAccList[3]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'debit_amount' => $amount,
        'debit_remark' => ''
      ];

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputs";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }

  //Depreciation Accounting 27-06-2023
  function depreciationAccountingPosting($inputes, $functionSlug, $parent_id)
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

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];

      $assetDetail = $inputes['asset'];

      $accounting['credit'][] = [
        'glId' => $creditAccList[0]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'credit_amount' => $assetDetail['amount'] * -1,
        'credit_remark' => ''
      ];
      $accounting['debit'][] = [
        'glId' => $debitAccList[0]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'debit_amount' => $assetDetail['amount'],
        'debit_remark' => ''
      ];


      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }



  // Production Declaration ACCOUNTING---25-12-2022 18:16  ***
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

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];

      $consumpProductData = $inputes["consumpProductData"];
      foreach ($consumpProductData  as $crkey => $value) {
        if ($value['type'] == 'RM') {
          $accounting['credit'][] = [
            'glId' => $value['parentGlId'],
            'subGlCode' => $value['itemCode'],
            'subGlName' => $value['itemName'],
            'credit_amount' => $value['price'],
            'credit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $debitAccList[0]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $value['price'],
            'debit_remark' => ''
          ];
        } else {
          $accounting['credit'][] = [
            'glId' => $value['parentGlId'],
            'subGlCode' => $value['itemCode'],
            'subGlName' => $value['itemName'],
            'credit_amount' => $value['price'],
            'credit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $debitAccList[1]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $value['price'],
            'debit_remark' => ''
          ];
        }
      }

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }

    return $returnData;
  }


  // FG/SFG Declaration ACCOUNTING---25-12-2022 18:16 ***
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

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];


      $finalProductData = $inputes["finalProductData"];
      $totalAmount = $finalProductData['cogm_m'] + $finalProductData['cogm_a'];

      $accounting['debit'][] = [
        'glId' => $finalProductData['parentGlId'],
        'subGlCode' => $finalProductData['itemCode'],
        'subGlName' => $finalProductData['itemName'],
        'debit_amount' => $totalAmount,
        'debit_remark' => ''
      ];
      $accounting['credit'][] = [
        'glId' => $creditAccList[0]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'credit_amount' => $finalProductData['cogm_m'],
        'credit_remark' => ''
      ];
      $accounting['credit'][] = [
        'glId' => $creditAccList[1]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'credit_amount' => $finalProductData['cogm_a'],
        'credit_remark' => ''
      ];


      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }

    return $returnData;
  }




  // Production Declaration ACCOUNTING---25-12-2022 18:16  ***
  function productionDeclarationAccountingPostingProject($inputes, $functionSlug, $parent_id)
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

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];

      $consumpProductData = $inputes["consumpProductData"];
      foreach ($consumpProductData  as $crkey => $value) {
        if ($value['type'] == 'RM') {
          $accounting['credit'][] = [
            'glId' => $value['parentGlId'],
            'subGlCode' => $value['itemCode'],
            'subGlName' => $value['itemName'],
            'credit_amount' => $value['price'],
            'credit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $debitAccList[0]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $value['price'],
            'debit_remark' => ''
          ];
        } else if ($value['type'] == 'SFG') {
          $accounting['credit'][] = [
            'glId' => $value['parentGlId'],
            'subGlCode' => $value['itemCode'],
            'subGlName' => $value['itemName'],
            'credit_amount' => $value['price'],
            'credit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $debitAccList[1]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $value['price'],
            'debit_remark' => ''
          ];
        } else {
          $accounting['credit'][] = [
            'glId' => $value['parentGlId'],
            'subGlCode' => $value['itemCode'],
            'subGlName' => $value['itemName'],
            'credit_amount' => $value['price'],
            'credit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $debitAccList[2]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $value['price'],
            'debit_remark' => ''
          ];
        }
      }

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }

    return $returnData;
  }


  // stockPostingProductionOrder ACCOUNTING---25-12-2022 18:16  ***
  function stockPostingProductionOrderAccountingPosting($inputes, $functionSlug, $parent_id)
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
    //console($isValidate);
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

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];

      $consumpProductData = $inputes["FGItems"];
      foreach ($consumpProductData  as $crkey => $value) {
        if ($value['goodsType'] == 1) {
          // RM
          $accounting['credit'][] = [
            'glId' => $value['parentGlId'],
            'subGlCode' => $value['itemCode'],
            'subGlName' => $value['itemName'],
            'credit_amount' => $value['price'],
            'credit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $debitAccList[0]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $value['price'],
            'debit_remark' => ''
          ];
        } else if ($value['goodsType'] == 2) {
          // SFG
          $accounting['credit'][] = [
            'glId' => $value['parentGlId'],
            'subGlCode' => $value['itemCode'],
            'subGlName' => $value['itemName'],
            'credit_amount' => $value['price'],
            'credit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $debitAccList[1]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $value['price'],
            'debit_remark' => ''
          ];
        } else {
          $accounting['credit'][] = [
            'glId' => $value['parentGlId'],
            'subGlCode' => $value['itemCode'],
            'subGlName' => $value['itemName'],
            'credit_amount' => $value['price'],
            'credit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $debitAccList[0]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $value['price'],
            'debit_remark' => ''
          ];
        }
      }

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }

    return $returnData;
  }




  // stockPostingCostcenter ACCOUNTING---25-12-2022 18:16  ***
  function stockPostingCostcenterAccountingPosting($inputes, $functionSlug, $parent_id)
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
    //console($isValidate);
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

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];

      $consumpProductData = $inputes["FGItems"];
      foreach ($consumpProductData  as $crkey => $value) {
        if ($value['goodsType'] == 1) {
          // RM
          $accounting['credit'][] = [
            'glId' => $value['parentGlId'],
            'subGlCode' => $value['itemCode'],
            'subGlName' => $value['itemName'],
            'credit_amount' => $value['price'],
            'credit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $debitAccList[0]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $value['price'],
            'debit_remark' => ''
          ];
        } else if ($value['goodsType'] == 2) {
          // SFG
          $accounting['credit'][] = [
            'glId' => $value['parentGlId'],
            'subGlCode' => $value['itemCode'],
            'subGlName' => $value['itemName'],
            'credit_amount' => $value['price'],
            'credit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $debitAccList[0]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $value['price'],
            'debit_remark' => ''
          ];
        } else {
          $accounting['credit'][] = [
            'glId' => $value['parentGlId'],
            'subGlCode' => $value['itemCode'],
            'subGlName' => $value['itemName'],
            'credit_amount' => $value['price'],
            'credit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $debitAccList[0]['id'],
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $value['price'],
            'debit_remark' => ''
          ];
        }
      }

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }

    return $returnData;
  }




  // stockDifferenceBookToPhysical ACCOUNTING---25-12-2022 18:16  ***
  function stockDifferenceBookToPhysicalAccountingPosting($inputes, $functionSlug, $parent_id)
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
    //console($isValidate);
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

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];

      $consumpProductData = $inputes["FGItems"];
      foreach ($consumpProductData  as $crkey => $value) {
        if ($value['sign'] == "-") {
          if ($value['goodsType'] == 1) {
            // RM
            $accounting['credit'][] = [
              'glId' => $value['parentGlId'],
              'subGlCode' => $value['itemCode'],
              'subGlName' => $value['itemName'],
              'credit_amount' => $value['price'],
              'credit_remark' => ''
            ];

            $accounting['debit'][] = [
              'glId' => $debitAccList[0]['id'],
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => $value['price'],
              'debit_remark' => ''
            ];
          } else if ($value['goodsType'] == 2) {
            // SFG
            $accounting['credit'][] = [
              'glId' => $value['parentGlId'],
              'subGlCode' => $value['itemCode'],
              'subGlName' => $value['itemName'],
              'credit_amount' => $value['price'],
              'credit_remark' => ''
            ];

            $accounting['debit'][] = [
              'glId' => $debitAccList[0]['id'],
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => $value['price'],
              'debit_remark' => ''
            ];
          } else {
            $accounting['credit'][] = [
              'glId' => $value['parentGlId'],
              'subGlCode' => $value['itemCode'],
              'subGlName' => $value['itemName'],
              'credit_amount' => $value['price'],
              'credit_remark' => ''
            ];

            $accounting['debit'][] = [
              'glId' => $debitAccList[0]['id'],
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => $value['price'],
              'debit_remark' => ''
            ];
          }
        } else {
          if ($value['goodsType'] == 1) {
            // RM
            $accounting['debit'][] = [
              'glId' => $value['parentGlId'],
              'subGlCode' => $value['itemCode'],
              'subGlName' => $value['itemName'],
              'debit_amount' => $value['price'],
              'debit_remark' => ''
            ];

            $accounting['credit'][] = [
              'glId' => $debitAccList[0]['id'],
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => $value['price'],
              'credit_remark' => ''
            ];
          } else if ($value['goodsType'] == 2) {
            // SFG
            $accounting['debit'][] = [
              'glId' => $value['parentGlId'],
              'subGlCode' => $value['itemCode'],
              'subGlName' => $value['itemName'],
              'debit_amount' => $value['price'],
              'debit_remark' => ''
            ];

            $accounting['credit'][] = [
              'glId' => $debitAccList[0]['id'],
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => $value['price'],
              'credit_remark' => ''
            ];
          } else {
            $accounting['debit'][] = [
              'glId' => $value['parentGlId'],
              'subGlCode' => $value['itemCode'],
              'subGlName' => $value['itemName'],
              'debit_amount' => $value['price'],
              'debit_remark' => ''
            ];

            $accounting['credit'][] = [
              'glId' => $debitAccList[0]['id'],
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => $value['price'],
              'credit_remark' => ''
            ];
          }
        }
      }

      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }

    return $returnData;
  }


  // stockDifferenceBookToPhysical ACCOUNTING---25-12-2022 18:16  ***
  function stockDifferenceMaterialToMaterialAccountingPosting($inputes, $functionSlug, $parent_id)
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
    //console($isValidate);
    if ($isValidate["status"] == "success") {
      // $debitCreditAccListObj = $this->getCreditDebitAccountsList($functionSlug);

      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $priceDifferenceGL = $accMapp['data']['0']['price_difference_gl'];

      // if ($debitCreditAccListObj["status"] != "success") {
      //   return [
      //     "status" => "warning",
      //     "message" => "Debit & Credit Account list is not available"
      //   ];
      //   die();
      // }

      // $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      // $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];

      $consumpProductData = $inputes["FGItems"];
      $totalamount = 0;
      foreach ($consumpProductData  as $crkey => $value) {
        $totalamount = 0;
        $price = $value['price'];
        $price_difference = $value['price_diff'];
        $accounting['credit'][] = [
          'glId' => $value['parentGlId'],
          'subGlCode' => $value['itemCode'],
          'subGlName' => $value['itemName'],
          'credit_amount' => $price,
          'credit_remark' => ''
        ];
        $totalamount = $price + $price_difference;


        $accounting['debit'][] = [
          'glId' => $value['toItem']['parentGlId'],
          'subGlCode' => $value['toItem']['itemCode'],
          'subGlName' => $value['toItem']['itemName'],
          'debit_amount' => $totalamount,
          'debit_remark' => ''
        ];


        if (!empty($priceDifferenceGL) && $price_difference != 0) {
          if ($price_difference < 0) {
            $accounting['debit'][] = [
              'glId' => $priceDifferenceGL,
              'subGlCode' => '',
              'subGlName' => '',
              'debit_amount' => abs($price_difference),
              'debit_remark' => 'Price Difference'
            ];
          } else {
            $accounting['credit'][] = [
              'glId' => $priceDifferenceGL,
              'subGlCode' => '',
              'subGlName' => '',
              'credit_amount' => abs($price_difference),
              'credit_remark' => 'Price Difference'
            ];
          }
        }
      }



      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }

    return $returnData;
  }



  // FG/SFG Declaration ACCOUNTING---25-12-2022 18:16 ***
  function FGSFGDeclarationAccountingPostingProject($inputes, $functionSlug, $parent_id, $jv_no = null)
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

      $party_code = "";
      $party_name = "";

      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $inputes['BasicDetails']['reference'];
      $accounting['journal']['remark'] = $inputes['BasicDetails']['remarks'];
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $inputes['BasicDetails']['journalEntryReference'];
      $accounting['journal']['documentNo'] = $inputes['BasicDetails']['documentNo'];
      $accounting['journal']['documentDate'] = $inputes['BasicDetails']['documentDate'];
      $accounting['journal']['postingDate'] = $inputes['BasicDetails']['postingDate'];


      $finalProductData = $inputes["finalProductData"];
      $totalAmount = $finalProductData['cosp_m'] + $finalProductData['cosp_a'] + $finalProductData['cosp_i'];

      $accounting['debit'][] = [
        'glId' => $finalProductData['parentGlId'],
        'subGlCode' => $finalProductData['itemCode'],
        'subGlName' => $finalProductData['itemName'],
        'debit_amount' => $totalAmount,
        'debit_remark' => ''
      ];
      $accounting['credit'][] = [
        'glId' => $creditAccList[0]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'credit_amount' => $finalProductData['cosp_m'],
        'credit_remark' => ''
      ];
      $accounting['credit'][] = [
        'glId' => $creditAccList[1]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'credit_amount' => $finalProductData['cosp_a'],
        'credit_remark' => ''
      ];
      $accounting['credit'][] = [
        'glId' => $creditAccList[2]['id'],
        'subGlCode' => '',
        'subGlName' => '',
        'credit_amount' => $finalProductData['cosp_i'],
        'credit_remark' => ''
      ];


      $accPostingObj = new AccountingPosting();
      $returnData = $accPostingObj->post($accounting);
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }

    return $returnData;
  }




  // 10-12-2023 18:16
  function dNoteForCustomerAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];
    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "items" => "array"
    ], [
      "BasicDetails" => "Required",
      "items" => "Required"
    ]);


    if ($isValidate["status"] == "success") {
      $items = $inputes["items"];
      $partyDetails = $inputes['partyDetails'];
      $imputgst = $inputes['taxDetails'];
      $roundOffValue = $inputes["roundOffValue"] ?? 0;

      $accountingDocumentNo = $inputes["documentNo"];
      $accountingRefNo = $inputes["referenceNo"];
      $accountingRemarks =  $inputes['remarks'];
      $journalEntryReference = $inputes['journalEntryReference'];
      $documentDate = $inputes['documentDate'];
      $documentPostingDate = $inputes['invoicePostingDate'];

      $compInvoiceType = $inputes['compInvoiceType'] ?? '';

      $compInvoiceTypeval = 'domestic';
      if ($compInvoiceType == 'R') {
        //Domestic Transaction
        $compInvoiceTypeval = 'domestic';
      } else {
        //Export Transaction        
        $compInvoiceTypeval = 'export';
      }

      //---------------------------------Mapped G/L--------------------------------------
      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $roundOffGL = $accMapp['data']['0']['roundoff_gl'];

      //---------------------------------------------------------------------------------

      $igstgl = '';
      $cgstgl = '';
      $sgstgl = '';
      $cogsGlId = '';

      $debitCreditAccListObj = $this->getCreditDebitAccountsList('SOInvoicing');  //Invoice
      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $igstgl = $creditAccList[1]['id'];
      $cgstgl = $creditAccList[2]['id'];
      $sgstgl = $creditAccList[3]['id'];


      $pgidebitCreditAccListObj = $this->getCreditDebitAccountsList('PGI');  //PGI
      if ($pgidebitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }
      $pgidebitAccList = $pgidebitCreditAccListObj["debitAccountsList"];
      $pgicreditAccList = $pgidebitCreditAccListObj["creditAccountsList"];

      $cogsGlId = $pgidebitAccList[0]['id'];





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

      $partyparentGlId = $partyDetails['parentGlId'] ?? "";
      $party_code = $partyDetails['partyCode'] ?? "";
      $party_name = $partyDetails['partyName'] ?? "";


      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $accountingRefNo;
      $accounting['journal']['remark'] = $accountingRemarks;
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting['journal']['documentNo'] = $accountingDocumentNo;
      $accounting['journal']['documentDate'] = $documentDate;
      $accounting['journal']['postingDate'] = $documentPostingDate;

      $accounting2 = array();
      $accounting2['journal']['parent_id'] = $parent_id;
      $accounting2['journal']['parent_slug'] = $functionSlug;
      $accounting2['journal']['refarenceCode'] = $accountingRefNo;
      $accounting2['journal']['remark'] = $accountingRemarks;
      $accounting2['journal']['party_code'] = $party_code;
      $accounting2['journal']['party_name'] = $party_name;
      $accounting2['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting2['journal']['documentNo'] = $accountingDocumentNo;
      $accounting2['journal']['documentDate'] = $documentDate;
      $accounting2['journal']['postingDate'] = $documentPostingDate;


      $totalamount = 0;
      $tdstotal = 0;
      foreach ($items  as $itemkey => $itemvalue) {

        $salesAccc = 'sales_goods_domestic';

        $goodsType = $itemvalue['goodsType'] ?? '';
        if (!empty($goodsType)) {
          if ($compInvoiceTypeval == 'domestic') {
            if ($goodsType == 5 || $goodsType == 7 || $goodsType == 10) {
              $salesAccc = 'sales_services_domestic';
            } else if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
              $salesAccc = 'sales_goods_domestic';
            } else {
              $salesAccc = 'sales_goods_domestic';
            }
          } else if ($compInvoiceTypeval == 'export') {
            if ($goodsType == 5 || $goodsType == 7 || $goodsType == 10) {
              $salesAccc = 'sales_services_export';
            } else if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
              $salesAccc = 'sales_goods_export';
            } else {
              $salesAccc = 'sales_goods_export';
            }
          } else {
            $salesAccc = 'sales_goods_domestic';
          }
          $selectedSalesAc = $accMapp['data']['0'][$salesAccc];
        } else {
          $selectedSalesAc = $itemvalue['accountGl'];
        }

        $accounting['credit'][] = [
          'glId' => $selectedSalesAc,
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $itemvalue['withouttax'],
          'credit_remark' => ''
        ];

        if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
          $accounting2['credit'][] = [
            'glId' => $itemvalue['accountGl'],
            'subGlCode' => $itemvalue['subgl_code'],
            'subGlName' => $itemvalue['subgl_name'],
            'credit_amount' => $itemvalue['withouttax'],
            'credit_remark' => ''
          ];

          //-------------------COGS G/L-----------------------------
          $accounting2['debit'][] = [
            'glId' => $cogsGlId,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $itemvalue['withouttax'],
            'debit_remark' => ''
          ];
        }

        $totalamount += $itemvalue['withouttax'];
      }


      if (!empty($imputgst['igst']) && ($imputgst['igst'] > 0)) {
        //-------------------------IGST------------------------          
        $totalamount += $imputgst['igst'];

        $accounting['credit'][] = [
          'glId' => $igstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $imputgst['igst'],
          'credit_remark' => ''
        ];
      } else {
        //-------------------------CGST------------------------
        $totalamount += $imputgst['cgst'];

        $accounting['credit'][] = [
          'glId' => $cgstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $imputgst['cgst'],
          'credit_remark' => ''
        ];
        //-------------------------SGST------------------------
        $totalamount += $imputgst['sgst'];

        $accounting['credit'][] = [
          'glId' => $sgstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $imputgst['sgst'],
          'credit_remark' => ''
        ];
      }

      //-------------------Customer G/L-----------------------------
      $totalamount += $roundOffValue;
      $accounting['debit'][] = [
        'glId' => $partyparentGlId,
        'subGlCode' => $party_code,
        'subGlName' => $party_name,
        'debit_amount' => $totalamount,
        'debit_remark' => ''
      ];


      if (!empty($roundOffGL) && $roundOffValue != 0) {
        if ($roundOffValue < 0) {
          $accounting['debit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => abs($roundOffValue),
            'debit_remark' => 'Rounding off'
          ];
        } else {
          $accounting['credit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => abs($roundOffValue),
            'credit_remark' => 'Rounding off'
          ];
        }
      }


      $accPostingObj = new AccountingPosting();
      $returnDataAcc = $accPostingObj->post($accounting);
      $returnData['status'] = $returnDataAcc['status'];
      $returnData['journalId'] = $returnDataAcc['journalId'];
      $returnData['goodsJournalId'] = 0;
      $returnData['datares']['accounting1Input'] = $accounting;
      $returnData['datares']['accounting1'] = $returnDataAcc;

      if (count($accounting2['credit']) > 0) {
        $accPostingObj2 = new AccountingPosting();
        $returnDataAcc2 = $accPostingObj2->post($accounting2);
        $returnData['datares']['accounting2Input'] = $accounting2;
        $returnData['datares']['accounting2'] = $returnDataAcc2;
        $returnData['goodsJournalId'] = $returnDataAcc2['journalId'] ?? 0;
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }



  // 10-12-2023 18:16
  function dNoteForVendorAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];
    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "items" => "array"
    ], [
      "BasicDetails" => "Required",
      "items" => "Required"
    ]);


    if ($isValidate["status"] == "success") {
      $items = $inputes["items"];
      $partyDetails = $inputes['partyDetails'];
      $imputgst = $inputes['taxDetails'];
      $roundOffValue = $inputes["roundOffValue"] ?? 0;

      $accountingDocumentNo = $inputes["documentNo"];
      $accountingRefNo = $inputes["referenceNo"];
      $accountingRemarks =  $inputes['remarks'];
      $journalEntryReference = $inputes['journalEntryReference'];
      $documentDate = $inputes['documentDate'];
      $documentPostingDate = $inputes['invoicePostingDate'];


      //---------------------------------Mapped G/L--------------------------------------
      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $roundOffGL = $accMapp['data']['0']['roundoff_gl'];

      //---------------------------------------------------------------------------------

      $igstgl = '';
      $cgstgl = '';
      $sgstgl = '';



      //grn GL Code
      $grndebitCreditAccListObj = $this->getCreditDebitAccountsList('grniv');  //grn
      if ($grndebitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }
      $grndebitAccList = $grndebitCreditAccListObj["debitAccountsList"];
      $grncreditAccList = $grndebitCreditAccListObj["creditAccountsList"];

      $grirGlId = $grndebitAccList[0]['id'];

      $igstgl = $grndebitAccList[1]['id'];
      $cgstgl = $grndebitAccList[2]['id'];
      $sgstgl = $grndebitAccList[3]['id'];

      $tdsGlId = $grncreditAccList[1]['id'];

      //srn GL Code
      $srndebitCreditAccListObj = $this->getCreditDebitAccountsList('srniv');  //srn
      if ($srndebitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }
      $srndebitAccList = $srndebitCreditAccListObj["debitAccountsList"];
      $srncreditAccList = $srndebitCreditAccListObj["creditAccountsList"];

      $srirGlId = $srndebitAccList[0]['id'];
      $rcmGlId = $srncreditAccList[2]['id'];




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

      $partyparentGlId = $partyDetails['parentGlId'] ?? "";
      $party_code = $partyDetails['partyCode'] ?? "";
      $party_name = $partyDetails['partyName'] ?? "";


      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $accountingRefNo;
      $accounting['journal']['remark'] = $accountingRemarks;
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting['journal']['documentNo'] = $accountingDocumentNo;
      $accounting['journal']['documentDate'] = $documentDate;
      $accounting['journal']['postingDate'] = $documentPostingDate;

      $accounting2 = array();
      $accounting2['journal']['parent_id'] = $parent_id;
      $accounting2['journal']['parent_slug'] = $functionSlug;
      $accounting2['journal']['refarenceCode'] = $accountingRefNo;
      $accounting2['journal']['remark'] = $accountingRemarks;
      $accounting2['journal']['party_code'] = $party_code;
      $accounting2['journal']['party_name'] = $party_name;
      $accounting2['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting2['journal']['documentNo'] = $accountingDocumentNo;
      $accounting2['journal']['documentDate'] = $documentDate;
      $accounting2['journal']['postingDate'] = $documentPostingDate;


      $totalamount = 0;
      $tdstotal = 0;
      foreach ($items  as $itemkey => $itemvalue) {

        $goodsType = $itemvalue['goodsType'] ?? '';
        // if (!empty($goodsType)) {
        //   if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
        //     $irAccc = $grirGlId;
        //   }
        // } else {
        //   $irAccc = $itemvalue['accountGl'];
        // }


        if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {

          $accounting2['credit'][] = [
            'glId' => $itemvalue['accountGl'],
            'subGlCode' => $itemvalue['subgl_code'],
            'subGlName' => $itemvalue['subgl_name'],
            'credit_amount' => $itemvalue['withouttax'],
            'credit_remark' => ''
          ];

          //-------------------GRIR/SRIR G/L-----------------------------
          $accounting['credit'][] = [
            'glId' => $grirGlId,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => $itemvalue['withouttax'],
            'credit_remark' => ''
          ];

          $accounting2['debit'][] = [
            'glId' => $grirGlId,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $itemvalue['withouttax'],
            'debit_remark' => ''
          ];
        } else {
          $accounting2['credit'][] = [
            'glId' => $itemvalue['accountGl'],
            'subGlCode' => $itemvalue['subgl_code'],
            'subGlName' => $itemvalue['subgl_name'],
            'credit_amount' => $itemvalue['withouttax'],
            'credit_remark' => ''
          ];

          $accounting['credit'][] = [
            'glId' => $srirGlId,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => $itemvalue['withouttax'],
            'credit_remark' => ''
          ];

          $accounting2['debit'][] = [
            'glId' => $srirGlId,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $itemvalue['withouttax'],
            'debit_remark' => ''
          ];
        }

        $totalamount += $itemvalue['withouttax'];
      }


      if (!empty($imputgst['igst']) && ($imputgst['igst'] > 0)) {
        //-------------------------IGST------------------------          
        $totalamount += $imputgst['igst'];

        $accounting['credit'][] = [
          'glId' => $igstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $imputgst['igst'],
          'credit_remark' => ''
        ];
      } else {
        //-------------------------CGST------------------------
        $totalamount += $imputgst['cgst'];

        $accounting['credit'][] = [
          'glId' => $cgstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $imputgst['cgst'],
          'credit_remark' => ''
        ];
        //-------------------------SGST------------------------
        $totalamount += $imputgst['sgst'];

        $accounting['credit'][] = [
          'glId' => $sgstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'credit_amount' => $imputgst['sgst'],
          'credit_remark' => ''
        ];
      }

      //-------------------Vendor G/L-----------------------------
      $totalamount += $roundOffValue;
      $accounting['debit'][] = [
        'glId' => $partyparentGlId,
        'subGlCode' => $party_code,
        'subGlName' => $party_name,
        'debit_amount' => $totalamount,
        'debit_remark' => ''
      ];


      if (!empty($roundOffGL) && $roundOffValue != 0) {
        if ($roundOffValue < 0) {
          $accounting['debit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => abs($roundOffValue),
            'debit_remark' => 'Rounding off'
          ];
        } else {
          $accounting['credit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => abs($roundOffValue),
            'credit_remark' => 'Rounding off'
          ];
        }
      }


      $accPostingObj = new AccountingPosting();


      $returnDataAcc = $accPostingObj->post($accounting);
      $returnData['status'] = $returnDataAcc['status'];
      $returnData['journalId'] = $returnDataAcc['journalId'];
      $returnData['goodsJournalId'] = 0;
      $returnData['datares']['accounting1Input'] = $accounting;
      $returnData['datares']['accounting1'] = $returnDataAcc;

      if (count($accounting2['credit']) > 0) {
        $accPostingObj2 = new AccountingPosting();
        $returnDataAcc2 = $accPostingObj2->post($accounting2);
        $returnData['datares']['accounting2Input'] = $accounting2;
        $returnData['datares']['accounting2'] = $returnDataAcc2;
        $returnData['datares']['grndebitCreditAccListObj'] = $grndebitCreditAccListObj;
        $returnData['goodsJournalId'] = $returnDataAcc2['journalId'] ?? 0;
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }



  // 10-12-2023 18:16
  function cNoteForCustomerAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];
    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "items" => "array"
    ], [
      "BasicDetails" => "Required",
      "items" => "Required"
    ]);


    if ($isValidate["status"] == "success") {
      $items = $inputes["items"];
      $partyDetails = $inputes['partyDetails'];
      $imputgst = $inputes['taxDetails'];
      $roundOffValue = $inputes["roundOffValue"] ?? 0;

      $accountingDocumentNo = $inputes["documentNo"];
      $accountingRefNo = $inputes["referenceNo"];
      $accountingRemarks =  $inputes['remarks'];
      $journalEntryReference = $inputes['journalEntryReference'];
      $documentDate = $inputes['documentDate'];
      $documentPostingDate = $inputes['invoicePostingDate'];

      $compInvoiceType = $inputes['compInvoiceType'] ?? '';

      $compInvoiceTypeval = 'domestic';
      if ($compInvoiceType == 'R') {
        //Domestic Transaction
        $compInvoiceTypeval = 'domestic';
      } else {
        //Export Transaction        
        $compInvoiceTypeval = 'export';
      }

      //---------------------------------Mapped G/L--------------------------------------
      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $roundOffGL = $accMapp['data']['0']['roundoff_gl'];

      //---------------------------------------------------------------------------------

      $igstgl = '';
      $cgstgl = '';
      $sgstgl = '';
      $cogsGlId = '';

      $debitCreditAccListObj = $this->getCreditDebitAccountsList('SOInvoicing');  //Invoice
      if ($debitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }

      $debitAccList = $debitCreditAccListObj["debitAccountsList"];
      $creditAccList = $debitCreditAccListObj["creditAccountsList"];

      $igstgl = $creditAccList[1]['id'];
      $cgstgl = $creditAccList[2]['id'];
      $sgstgl = $creditAccList[3]['id'];


      $pgidebitCreditAccListObj = $this->getCreditDebitAccountsList('PGI');  //PGI
      if ($pgidebitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }
      $pgidebitAccList = $pgidebitCreditAccListObj["debitAccountsList"];
      $pgicreditAccList = $pgidebitCreditAccListObj["creditAccountsList"];

      $cogsGlId = $pgidebitAccList[0]['id'];





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

      $partyparentGlId = $partyDetails['parentGlId'] ?? "";
      $party_code = $partyDetails['partyCode'] ?? "";
      $party_name = $partyDetails['partyName'] ?? "";


      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $accountingRefNo;
      $accounting['journal']['remark'] = $accountingRemarks;
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting['journal']['documentNo'] = $accountingDocumentNo;
      $accounting['journal']['documentDate'] = $documentDate;
      $accounting['journal']['postingDate'] = $documentPostingDate;

      $accounting2 = array();
      $accounting2['journal']['parent_id'] = $parent_id;
      $accounting2['journal']['parent_slug'] = $functionSlug;
      $accounting2['journal']['refarenceCode'] = $accountingRefNo;
      $accounting2['journal']['remark'] = $accountingRemarks;
      $accounting2['journal']['party_code'] = $party_code;
      $accounting2['journal']['party_name'] = $party_name;
      $accounting2['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting2['journal']['documentNo'] = $accountingDocumentNo;
      $accounting2['journal']['documentDate'] = $documentDate;
      $accounting2['journal']['postingDate'] = $documentPostingDate;


      $totalamount = 0;
      $tdstotal = 0;
      foreach ($items  as $itemkey => $itemvalue) {

        $salesAccc = 'sales_goods_domestic';

        $goodsType = $itemvalue['goodsType'] ?? '';
        if (!empty($goodsType)) {
          if ($compInvoiceTypeval == 'domestic') {
            if ($goodsType == 5 || $goodsType == 7 || $goodsType == 10) {
              $salesAccc = 'sales_services_domestic';
            } else if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
              $salesAccc = 'sales_goods_domestic';
            } else {
              $salesAccc = 'sales_goods_domestic';
            }
          } else if ($compInvoiceTypeval == 'export') {
            if ($goodsType == 5 || $goodsType == 7 || $goodsType == 10) {
              $salesAccc = 'sales_services_export';
            } else if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
              $salesAccc = 'sales_goods_export';
            } else {
              $salesAccc = 'sales_goods_export';
            }
          } else {
            $salesAccc = 'sales_goods_domestic';
          }
          $selectedSalesAc = $accMapp['data']['0'][$salesAccc];
        } else {
          $selectedSalesAc = $itemvalue['accountGl'];
        }

        $accounting['debit'][] = [
          'glId' => $selectedSalesAc,
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $itemvalue['withouttax'],
          'debit_remark' => ''
        ];

        if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
          $accounting2['debit'][] = [
            'glId' => $itemvalue['accountGl'],
            'subGlCode' => $itemvalue['subgl_code'],
            'subGlName' => $itemvalue['subgl_name'],
            'debit_amount' => $itemvalue['withouttax'],
            'debit_remark' => ''
          ];

          //-------------------COGS G/L-----------------------------
          $accounting2['credit'][] = [
            'glId' => $cogsGlId,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => $itemvalue['withouttax'],
            'credit_remark' => ''
          ];
        }

        $totalamount += $itemvalue['withouttax'];
      }


      if (!empty($imputgst['igst']) && ($imputgst['igst'] > 0)) {
        //-------------------------IGST------------------------          
        $totalamount += $imputgst['igst'];

        $accounting['debit'][] = [
          'glId' => $igstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['igst'],
          'debit_remark' => ''
        ];
      } else {
        //-------------------------CGST------------------------
        $totalamount += $imputgst['cgst'];

        $accounting['debit'][] = [
          'glId' => $cgstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['cgst'],
          'debit_remark' => ''
        ];
        //-------------------------SGST------------------------
        $totalamount += $imputgst['sgst'];

        $accounting['debit'][] = [
          'glId' => $sgstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['sgst'],
          'debit_remark' => ''
        ];
      }

      //-------------------Customer G/L-----------------------------
      $totalamount += $roundOffValue;
      $accounting['credit'][] = [
        'glId' => $partyparentGlId,
        'subGlCode' => $party_code,
        'subGlName' => $party_name,
        'credit_amount' => $totalamount,
        'credit_remark' => ''
      ];


      if (!empty($roundOffGL) && $roundOffValue != 0) {
        if ($roundOffValue < 0) {
          $accounting['credit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => abs($roundOffValue),
            'credit_remark' => 'Rounding off'
          ];
        } else {
          $accounting['debit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => abs($roundOffValue),
            'debit_remark' => 'Rounding off'
          ];
        }
      }


      $accPostingObj = new AccountingPosting();

      $returnDataAcc = $accPostingObj->post($accounting);
      $returnData['status'] = $returnDataAcc['status'];
      $returnData['journalId'] = $returnDataAcc['journalId'];
      $returnData['goodsJournalId'] = 0;
      $returnData['datares']['accounting1Input'] = $accounting;
      $returnData['datares']['accounting1'] = $returnDataAcc;

      if (count($accounting2['credit']) > 0) {
        $accPostingObj2 = new AccountingPosting();
        $returnDataAcc2 = $accPostingObj2->post($accounting2);
        $returnData['datares']['accounting2Input'] = $accounting2;
        $returnData['datares']['accounting2'] = $returnDataAcc2;
        $returnData['goodsJournalId'] = $returnDataAcc2['journalId'] ?? 0;
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }


  // 10-12-2023 18:16
  function cNoteForVendorAccountingPosting($inputes, $functionSlug, $parent_id)
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    $returnData = [];
    $isValidate = validate($inputes, [
      "BasicDetails" => "array",
      "items" => "array"
    ], [
      "BasicDetails" => "Required",
      "items" => "Required"
    ]);


    if ($isValidate["status"] == "success") {
      $items = $inputes["items"];
      $partyDetails = $inputes['partyDetails'];
      $imputgst = $inputes['taxDetails'];
      $roundOffValue = $inputes["roundOffValue"] ?? 0;

      $accountingDocumentNo = $inputes["documentNo"];
      $accountingRefNo = $inputes["referenceNo"];
      $accountingRemarks =  $inputes['remarks'];
      $journalEntryReference = $inputes['journalEntryReference'];
      $documentDate = $inputes['documentDate'];
      $documentPostingDate = $inputes['invoicePostingDate'];


      //---------------------------------Mapped G/L--------------------------------------
      $accMapp = getAllfetchAccountingMappingTbl($company_id);
      $roundOffGL = $accMapp['data']['0']['roundoff_gl'];

      //---------------------------------------------------------------------------------

      $igstgl = '';
      $cgstgl = '';
      $sgstgl = '';



      //grn GL Code
      $grndebitCreditAccListObj = $this->getCreditDebitAccountsList('grniv');  //grn
      if ($grndebitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }
      $grndebitAccList = $grndebitCreditAccListObj["debitAccountsList"];
      $grncreditAccList = $grndebitCreditAccListObj["creditAccountsList"];

      $grirGlId = $grndebitAccList[0]['id'];

      $igstgl = $grndebitAccList[1]['id'];
      $cgstgl = $grndebitAccList[2]['id'];
      $sgstgl = $grndebitAccList[3]['id'];

      $tdsGlId = $grncreditAccList[1]['id'];

      //srn GL Code
      $srndebitCreditAccListObj = $this->getCreditDebitAccountsList('srniv');  //srn
      if ($srndebitCreditAccListObj["status"] != "success") {
        return [
          "status" => "warning",
          "message" => "Debit & Credit Account list is not available"
        ];
        die();
      }
      $srndebitAccList = $srndebitCreditAccListObj["debitAccountsList"];
      $srncreditAccList = $srndebitCreditAccListObj["creditAccountsList"];

      $srirGlId = $srndebitAccList[0]['id'];
      $rcmGlId = $srncreditAccList[2]['id'];




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

      $partyparentGlId = $partyDetails['parentGlId'] ?? "";
      $party_code = $partyDetails['partyCode'] ?? "";
      $party_name = $partyDetails['partyName'] ?? "";


      $accounting = array();
      $accounting['journal']['parent_id'] = $parent_id;
      $accounting['journal']['parent_slug'] = $functionSlug;
      $accounting['journal']['refarenceCode'] = $accountingRefNo;
      $accounting['journal']['remark'] = $accountingRemarks;
      $accounting['journal']['party_code'] = $party_code;
      $accounting['journal']['party_name'] = $party_name;
      $accounting['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting['journal']['documentNo'] = $accountingDocumentNo;
      $accounting['journal']['documentDate'] = $documentDate;
      $accounting['journal']['postingDate'] = $documentPostingDate;

      $accounting2 = array();
      $accounting2['journal']['parent_id'] = $parent_id;
      $accounting2['journal']['parent_slug'] = $functionSlug;
      $accounting2['journal']['refarenceCode'] = $accountingRefNo;
      $accounting2['journal']['remark'] = $accountingRemarks;
      $accounting2['journal']['party_code'] = $party_code;
      $accounting2['journal']['party_name'] = $party_name;
      $accounting2['journal']['journalEntryReference'] = $journalEntryReference;
      $accounting2['journal']['documentNo'] = $accountingDocumentNo;
      $accounting2['journal']['documentDate'] = $documentDate;
      $accounting2['journal']['postingDate'] = $documentPostingDate;


      $totalamount = 0;
      $tdstotal = 0;
      foreach ($items  as $itemkey => $itemvalue) {

        $goodsType = $itemvalue['goodsType'] ?? '';
        // if (!empty($goodsType)) {
        //   if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {
        //     $irAccc = $grirGlId;
        //   }
        // } else {
        //   $irAccc = $itemvalue['accountGl'];
        // }


        if ($goodsType == 1 || $goodsType == 2 || $goodsType == 3 || $goodsType == 4 || $goodsType == 9) {

          $accounting2['debit'][] = [
            'glId' => $itemvalue['accountGl'],
            'subGlCode' => $itemvalue['subgl_code'],
            'subGlName' => $itemvalue['subgl_name'],
            'debit_amount' => $itemvalue['withouttax'],
            'debit_remark' => ''
          ];

          //-------------------GRIR/SRIR G/L-----------------------------
          $accounting['debit'][] = [
            'glId' => $grirGlId,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => $itemvalue['withouttax'],
            'debit_remark' => ''
          ];

          $accounting2['credit'][] = [
            'glId' => $grirGlId,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => $itemvalue['withouttax'],
            'credit_remark' => ''
          ];
        } else {

          $accounting2['debit'][] = [
            'glId' => $itemvalue['accountGl'],
            'subGlCode' => $itemvalue['subgl_code'],
            'subGlName' => $itemvalue['subgl_name'],
            'debit_amount' => $itemvalue['withouttax'],
            'debit_remark' => ''
          ];

          $accounting['debit'][] = [
            'glId' => $srirGlId,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => $itemvalue['withouttax'],
            'credit_remark' => ''
          ];

          $accounting2['credit'][] = [
            'glId' => $srirGlId,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => $itemvalue['withouttax'],
            'credit_remark' => ''
          ];
        }

        $totalamount += $itemvalue['withouttax'];
      }


      if (!empty($imputgst['igst']) && ($imputgst['igst'] > 0)) {
        //-------------------------IGST------------------------          
        $totalamount += $imputgst['igst'];

        $accounting['debit'][] = [
          'glId' => $igstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['igst'],
          'debit_remark' => ''
        ];
      } else {
        //-------------------------CGST------------------------
        $totalamount += $imputgst['cgst'];

        $accounting['debit'][] = [
          'glId' => $cgstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['cgst'],
          'debit_remark' => ''
        ];
        //-------------------------SGST------------------------
        $totalamount += $imputgst['sgst'];

        $accounting['debit'][] = [
          'glId' => $sgstgl,
          'subGlCode' => '',
          'subGlName' => '',
          'debit_amount' => $imputgst['sgst'],
          'debit_remark' => ''
        ];
      }

      //-------------------Vendor G/L-----------------------------
      $totalamount += $roundOffValue;
      $accounting['credit'][] = [
        'glId' => $partyparentGlId,
        'subGlCode' => $party_code,
        'subGlName' => $party_name,
        'credit_amount' => $totalamount,
        'credit_remark' => ''
      ];


      if (!empty($roundOffGL) && $roundOffValue != 0) {
        if ($roundOffValue < 0) {
          $accounting['credit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'credit_amount' => abs($roundOffValue),
            'credit_remark' => 'Rounding off'
          ];
        } else {
          $accounting['debit'][] = [
            'glId' => $roundOffGL,
            'subGlCode' => '',
            'subGlName' => '',
            'debit_amount' => abs($roundOffValue),
            'debit_remark' => 'Rounding off'
          ];
        }
      }


      $accPostingObj = new AccountingPosting();

      $returnDataAcc = $accPostingObj->post($accounting);
      $returnData['status'] = $returnDataAcc['status'];
      $returnData['journalId'] = $returnDataAcc['journalId'];
      $returnData['goodsJournalId'] = 0;
      $returnData['datares']['accounting1Input'] = $accounting;
      $returnData['datares']['accounting1'] = $returnDataAcc;

      if (count($accounting2['credit']) > 0) {
        $accPostingObj2 = new AccountingPosting();
        $returnDataAcc2 = $accPostingObj2->post($accounting2);
        $returnData['datares']['accounting2Input'] = $accounting2;
        $returnData['datares']['accounting2'] = $returnDataAcc2;
        $returnData['goodsJournalId'] = $returnDataAcc2['journalId'] ?? 0;
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Invalid form inputes";
      $returnData['errors'] = $isValidate["errors"];
    }
    return $returnData;
  }



  //Stock Transfer Accounting 12-05-2023 ***
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

  //Asset Deprecation Accounting 12-05-2023  assetDeprecation ***
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
function createDataJournaltest($POST = [])
{

  //console($_POST);
  // exit();

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

    $parent_id = 0;


    $party_code = '';
    $party_name = '';

    $accounting = array();
    $accounting['journal']['parent_id'] = $parent_id;
    $accounting['journal']['parent_slug'] = 'journal';
    $accounting['journal']['refarenceCode'] = $POST['refarenceCode'];
    $accounting['journal']['remark'] = $POST['remark'];
    $accounting['journal']['journalEntryReference'] = $POST['journalEntryReference'];
    $accounting['journal']['documentNo'] = $POST['documentNo'];
    $accounting['journal']['documentDate'] = $POST['documentDate'];
    $accounting['journal']['postingDate'] = $POST['postingDate'];

    $drGL = $_POST['journal']['debit']['gl'];
    $drsubgl = $_POST['journal']['debit']['subgl'];
    $drAmt = $_POST['journal']['debit']['amount'];
    $crGL = $_POST['journal']['credit']['gl'];
    $crsubgl = $_POST['journal']['credit']['subgl'];
    $crAmt = $_POST['journal']['credit']['amount'];


    $drGL = $_POST['journal']['debit'];
    $crGL = $_POST['journal']['credit'];

    foreach ($drGL  as $drkey => $drvalue) {
      $drsubglCode = '';
      $drsubglName = '';
      $dr_cost_center = $drvalue['cost_center'];
      $dr_func_area=$drvalue['functional_area'];
      if (isset($drvalue['subgl'])) {
        $drsubgl = explode("|", $drvalue['subgl']);
        $drsubglCode = $drsubgl[0];
        $drsubglName = $drsubgl[1];
        if (isset($drsubgl[2]) && $drsubgl[2] == 'party') {
          $party_code = $drsubglCode;
          $party_name = $drsubglName;
        }
      }
      $gl = $drvalue['gl'];
      $dramount = round($drvalue['amount'], 2);
      $accounting['debit'][] = [
        'glId' => $gl,
        'subGlCode' => $drsubglCode,
        'subGlName' => $drsubglName,
        'debit_amount' => $dramount,
        'debit_remark' => '',
        'dr_cost_center' => $dr_cost_center,
        'dr_func_area' =>$dr_func_area
      ];
    }

    foreach ($crGL  as $crkey => $crvalue) {
      $crsubglCode = '';
      $crsubglName = '';
      $cr_cost_center = $crvalue['cost_center'];
      $cr_func_area=$crvalue['functional_area'];
      if (isset($crvalue['subgl'])) {
        $crsubgl = explode("|", $crvalue['subgl']);
        $crsubglCode = $crsubgl[0];
        $crsubglName = $crsubgl[1];
        if (isset($crsubgl[2]) && $crsubgl[2] == 'party') {
          $party_code = $crsubglCode;
          $party_name = $crsubglName;
        }
      }
      $gl = $crvalue['gl'];
      $cramount = round($crvalue['amount'], 2);
      $accounting['credit'][] = [
        'glId' => $gl,
        'subGlCode' => $crsubglCode,
        'subGlName' => $crsubglName,
        'credit_amount' => $cramount,
        'credit_remark' => '',
        'cr_cost_center' => $cr_cost_center,
        'cr_func_area' =>$cr_func_area
      ];
    }

    // $cost_center = $_POST['cost_center'];



    $accounting['journal']['party_code'] = $party_code;
    $accounting['journal']['party_name'] = $party_name;

    $accPostingObj = new AccountingPostingjournal();
    $returnData = $accPostingObj->post($accounting);
    $journal_id = $returnData['data']['journal_id'];



    // console($returnData);
    // echo 'ok';
    // echo $returnData['lastInsertedId'];
    // exit();
    if ($returnData['status'] != 'success') {
      $returnData['status'] = "warning";
      $returnData['message'] = "Something went wrong";
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

function JournalEntryByFunctionMappNew($POST = []) {}



//*************************************/END/******************************************//