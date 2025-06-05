<?php

require_once("func-journal.php");
require_once("func-items-controller.php");
require_once "func-branch-failed-accounting-controller.php";
function credit_note_add($POST)
{
  //  console($POST);
  $accountingObj = new Accounting();
  $itemController = new ItemsController();
  global $dbCon;
  global $location_id;
  global $company_id;
  global $branch_id;
  global $created_by;

  $returnData = [];
  $party_id = 0;
  $party_code = 0;
  $party_name = 0;



  $isValidate = validate($POST, [
    "vendor_customer" => "required",
    "posting_date" => "required",
    "billToInput" => "required",
    "shipToInput" => "required"

  ]);
  if ($isValidate["status"] != "success") {
    $returnData['status'] = "warning";
    $returnData['message'] = "Invalid form inputes";
    $returnData['errors'] = $isValidate["errors"];
    return $returnData;
  }
  $party = explode('|', $POST['vendor_customer']);



  if ($POST['select_customer_vendor'] == 'Customer') {
    $creditor_type = "customer";
    $party_id = $party[0];
    $customer_sql = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = $party_id");
    $party_code = $customer_sql['data']['customer_code'];
    $party_name = addslashes($customer_sql['data']['trade_name']);
    $partyMail = $customer_sql['data']['customer_authorised_person_email'];
    $mailValid = $customer_sql['data']['isMailValid'];
  } else {
    $creditor_type = "vendor";
    $party_id = $party[0];
    $vendor_sql = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id` = $party_id");
    $party_code = $vendor_sql['data']['vendor_code'];
    $party_name = addslashes($vendor_sql['data']['trade_name']);
    $partyMail = $vendor_sql['data']['vendor_authorised_person_email'];
    $mailValid = $vendor_sql['data']['isMailValid'];
  }


  $bill = $POST['bill'];
  if ($bill == 'select invoice' || $bill == "") {
    $parent_id = 0;
  } else {
    $parent_id = explode('|', $POST['bill'])[0] ?? 0;
    $parent_id_code = explode('|', $POST['bill'])[2] ?? '';
  }


  $compInvoiceType = $POST['compInvoiceType'] ?? '';

  $reasons = $POST['reasons'];
  $posting_date = $POST['posting_date'];
  $remark = addslashes($POST['note']) ?? '';
  $source_address = addslashes($POST['source']);
  $destination_address = addslashes($POST['destination']);
  $subtotal = $POST['grandTotal'];
  $bill_address = $POST['billToInput'] ?? 0;
  $ship_address = $POST['shipToInput'] ?? 0;
  $round_value = isset($_POST['round_value']) && $_POST['round_value'] !== '' ? $_POST['round_value'] : 0;
  $round_sign = isset($_POST['round_sign']) && $_POST['round_sign'] !== '' ? $_POST['round_sign'] : '';

  $adjustment = $round_sign . '' . $round_value;



  $attachment = $POST['attachment'];
  $name = $attachment["name"];
  $tmpName = $attachment["tmp_name"];
  $size = $attachment["size"];

  $contactDetails = $POST['companyConfigId'];

  $taxDetails = [];
  $taxDetails['cgst'] = $POST['cgst'] ?? 0;
  $taxDetails['sgst'] = $POST['sgst'] ?? 0;
  $taxDetails['igst'] = $POST['igst'] ?? 0;

  $cgst = $POST['cgst'] ?? 0;;
  $sgst = $POST['sgst'] ?? 0;
  $igst = $POST['igst'] ?? 0;


  $allowed_types = ['jpg', 'png', 'jpeg', 'pdf'];
  $maxsize = 2 * 1024 * 1024; // 10 MB


  $fileUploaded = uploadFile(["name" => $name, "tmp_name" => $tmpName, "size" => $size], COMP_STORAGE_DIR . "/others/", $allowed_types, $maxsize, 0);
  // console($fileUploaded);
  $attachment_name = $fileUploaded['data'];


  $invoice_current_dueObj = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE so_invoice_id=$parent_id");
  $invoice_current_due = $invoice_current_dueObj['data']['due_amount'];

  $cnSql = "SELECT *  FROM erp_credit_note WHERE creditNoteReference='" . $parent_id . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND `creditors_type`='customer' AND status='active'";


  $cnRes = queryGet($cnSql, true);
  if ($cnRes['numRows'] > 0) {
    $cnData = $cnRes['data'];

    $totalCreditNoteAmount = queryGet("SELECT SUM(total) AS totalCreditNoteAmount FROM erp_credit_note WHERE creditNoteReference='" . $parent_id . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id GROUP BY creditNoteReference;")['data']['totalCreditNoteAmount'] ?? 0;
  }

  $invoice_current_due = $invoice_current_due - $totalCreditNoteAmount;

  if ($invoice_current_dueObj['numRows'] > 0 && $subtotal > $invoice_current_due) {
    $returnData['status'] = "Warning";
    $returnData['message'] = "Credit note amount can not greter than invoice due amount";
    return $returnData;
    exit();
  }




  $insert_credit_sql = "INSERT INTO  `erp_credit_note`
                      SET
                      `company_id`=$company_id,
                      `branch_id`=$branch_id,
                      `location_id`=$location_id,
                      `creditors_type`='" . $creditor_type . "',
                      `party_id`=$party_id,
                      `party_code`='" . $party_code . "',
                      `party_name`='" . $party_name . "',
                      `credit_note_no`= '',
                      `variant_id` = 0,
                      `creditNoteReference` = '" . $parent_id . "',
                      `postingDate` = '" . $posting_date . "',
                      `remark` = '" . $remark . "',
                      `source_address` = '" . $source_address . "',
                      `destination_address` = '" . $destination_address . "',
                      `billing_address` = '" . $bill_address . "',
                      `shipping_address` = '" . $ship_address . "',
                      `contact_details`='" . $contactDetails . "',
                      `total` = '" . $subtotal . "',
                      `adjustment` = '" . $adjustment . "',
                      `attachment` = '" . $attachment_name . "',
                      `status` = 'active',
                      `cgst`='" . $cgst . "',
                      `sgst`='" . $sgst . "',
                      `igst`='" . $igst . "',
                      `created_by` = '" . $created_by . "',
                      `updated_by` ='" . $created_by . "'";
  // console($insert_credit);
  // exit();
  $insert_credit = queryInsert($insert_credit_sql);
  if ($insert_credit['status'] == "success") {

    $credit_note_id = $insert_credit['insertedId'] ?? 0;

    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
    $auditTrail = array();
    $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
    $auditTrail['basicDetail']['table_name'] = 'erp_credit_note';
    $auditTrail['basicDetail']['column_name'] = 'cr_note_id'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $credit_note_id;  // primary key
    $auditTrail['basicDetail']['party_type'] = $creditor_type;
    $auditTrail['basicDetail']['party_id'] = $party_id;
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = 'Credit Note Add';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert_credit_sql);
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = $remark;

    $auditTrail['action_data']['Credit Note Detail']['party name'] = $party_name;
    $auditTrail['action_data']['Credit Note Detail']['party type'] = $creditor_type;
    $auditTrail['action_data']['Credit Note Detail']['party code'] = $party_code;
    $auditTrail['action_data']['Credit Note Detail']['postingDate'] = $posting_date;
    $auditTrail['action_data']['Credit Note Detail']['source_address'] = getStateDetail($source_address)["data"];
    $auditTrail['action_data']['Credit Note Detail']['destination_address'] = getStateDetail($destination_address)['data'];
    $auditTrail['action_data']['Credit Note Detail']['billing_address'] = getStateDetail($bill_address)['data'];
    $auditTrail['action_data']['Credit Note Detail']['shipping_address'] = getStateDetail($ship_address)['data'];
    $auditTrail['action_data']['Credit Note Detail']['contact_details'] = $contactDetails;
    $auditTrail['action_data']['Credit Note Detail']['shipping_address'] = $ship_address;
    $auditTrail['action_data']['Credit Note Detail']['total'] = $subtotal;
    $auditTrail['action_data']['Credit Note Detail']['adjustment'] = $adjustment;
    $auditTrail['action_data']['Credit Note Detail']['attachment'] = $attachment_name;
    $auditTrail['action_data']['Credit Note Detail']['cgst'] = $cgst;
    $auditTrail['action_data']['Credit Note Detail']['igst'] = $igst;
    $auditTrail['action_data']['Credit Note Detail']['sgst'] = $sgst;
    $auditTrail['action_data']['Credit Note Detail']['created_by'] = $created_by;
    $auditTrail['action_data']['Credit Note Detail']['updated_by'] = $created_by;

    if (isset($POST['repost']) && $POST['repost'] == 1) {

      $reverse_cn_id = $POST['reverse_cn_id'];
      $variant_id = $POST['last_iv_varient'];
      $update_reverse_cn = queryUpdate("UPDATE `erp_credit_note` SET `status`='reposted' WHERE `cr_note_id` = $reverse_cn_id");
      // console($update_reverse_cn);
      $credit_note_no = $POST['iv_varient'];
      $invoice_no_serialized = ($POST['repost_serialized']);
    } else {
      $variant_id = $POST['iv_varient'];

      $IvNoByVerientresponse = getCNNumberByVerient($POST['iv_varient']);
      $credit_note_no = $IvNoByVerientresponse['iv_number'];
      $invoice_no_serialized = $IvNoByVerientresponse['iv_number_array'];
    }



    $updateInv = "UPDATE `erp_credit_note` 
                    SET 
                    `variant_id` = $variant_id,
                    `credit_note_no`='$credit_note_no',
                    `credit_note_no_serialized`='$invoice_no_serialized'
                 WHERE cr_note_id='$credit_note_id'";
    queryUpdate($updateInv);
    $credit_note_no_unserialized = unserialize($invoice_no_serialized);
    $auditTrail['basicDetail']['document_number'] = $credit_note_no;
    $auditTrail['action_data']['Debit Note Detail']['prefix'] = $credit_note_no_unserialized['prefix'];
    $auditTrail['action_data']['Debit Note Detail']['fy'] = $credit_note_no_unserialized['fy'];
    $auditTrail['action_data']['Debit Note Detail']['serial'] = $credit_note_no_unserialized['serial'];

    $credit_items = $POST['item'];
    $items = [];
    foreach ($credit_items as $key => $item) {
      // $item_code = $item['item_code'];
      $qty = $item['qty'];
      // echo "ok";
      $rate = $item['rate'];
      // echo "ok";
      $tax = $item['tax'];
      $withouttax = $qty * $rate;
      //  echo "ok";
      $tax_amount = ($tax / 100) * ($qty * $rate);
      $itemigst = 0;
      $itemcgst = 0;
      $itemsgst = 0;
      if ($igst > 0) {
        $itemigst = $tax_amount;
      } else {
        $itemcgst = $tax_amount / 2;
        $itemsgst = $tax_amount / 2;
      }
      //  echo "ok";
      $amount = ($qty * $rate) + ($tax_amount);
      // $amount = $item['amount'];
      $itemArrys = array();
      $itemArrys = explode('_', $item['item_id']);
      $item_id = $itemArrys[0] ?? 0;

      $subgl_code = '';
      $subgl_name = '';
      $goodsType = '';
      $account = $item['account'];
      $uom = '';

      if (count($itemArrys) > 0) {
        $itemglQry = queryGet("SELECT baseUnitMeasure,parentGlId,itemCode,itemName,goodsType FROM `" . ERP_INVENTORY_ITEMS . "` WHERE itemId='" . $item_id . "' AND company_id = '" . $company_id . "' ");
        $itemgl = $itemglQry['data'];
        $subgl_code = $itemgl['itemCode'];
        $subgl_name = $itemgl['itemName'];
        $goodsType = $itemgl['goodsType'];
        $uom = $itemgl['baseUnitMeasure'];
        $account = $itemgl['parentGlId'];
      }
      $items[$key]['accountGl'] = $account;
      $items[$key]['goodsType'] = $goodsType;
      $items[$key]['subgl_code'] = $subgl_code;
      $items[$key]['subgl_name'] = $subgl_name;
      $items[$key]['withouttax'] = $withouttax;
      $items[$key]['tax'] = $tax_amount;

      $insert_credit_items = queryInsert("INSERT INTO  `credit_note_item`
                                                        SET
                                                        `account` = '" . $account . "',
                                                        `item_id`=$item_id,
                                                        `invoice_id` = $parent_id,
                                                        `credit_note_id`= $credit_note_id,
                                                        `item_qty` = '" . $qty . "',
                                                        `item_rate` = '" . $rate . "',
                                                        `item_tax` = '" . $tax . "',
                                                        `cgst`='" . $itemcgst . "',
                                                        `sgst`='" . $itemsgst . "',
                                                        `igst`='" . $itemigst . "',
                                                        `item_amount` = '" . $amount . "',
                                                        `created_by` = '" . $created_by . "',
                                                        `updated_by` ='" . $created_by . "'
                                                        ");

      if ($insert_credit_items['status'] == "success") {
        $itemName = $itemController->getItemById($item_id);
        $accountGl = getChartOfAccountsDataDetails($account);
        $auditTrail['action_data']['Credit Note Item Detail']['item'] = $itemName['data']['itemName'];
        $auditTrail['action_data']['Credit Note Item Detail']['account'] = $accountGl['data']['gl_label'];
        $auditTrail['action_data']['Credit Note Item Detail']['item_qty'] = $qty;
        $auditTrail['action_data']['Credit Note Item Detail']['item_rate'] = $rate;
        $auditTrail['action_data']['Credit Note Item Detail']['item_tax'] = $tax;
        $auditTrail['action_data']['Credit Note Item Detail']['cgst'] = $itemcgst;
        $auditTrail['action_data']['Credit Note Item Detail']['sgst'] = $itemsgst;
        $auditTrail['action_data']['Credit Note Item Detail']['igst'] = $itemigst;
        $auditTrail['action_data']['Credit Note Item Detail']['item_amount'] = $amount;
        $manualbatchselectionQty = $item['manualbatchselection']['qty'] ?? 0;
        $manualbatchselectionSL = $item['manualbatchselection']['storageLocation'];

        if (count($item['batchselection']) > 0) {

          // echo count($item['batchselection']).'--Batch Selection';

          $filteredBatchSelection = [];

          foreach ($item['batchselection'] as $key => $value) {
            $explodes = explode('_', $key);
            $logRef = $explodes[0];
            $slocation = $explodes[1];

            $keysval = $logRef . $slocation;

            if (!empty($value)) {
              $filteredBatchSelection[$keysval] = $value;
            }
          }

          $keysString = "'" . implode("', '", array_keys($filteredBatchSelection)) . "'";


          $selStockLog = itemQtyStockChecking($item_id, "'rmWhOpen', 'fgWhOpen'", 'ASC', "$keysString", $posting_date, 1);
          // console($selStockLog);
          $itemOpenStocks = $selStockLog['sumOfBatches'];



          foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
            // $explodes = explode('_', $logdata['logRef']);
            // $logRef = $explodes[0];
            $logRef = $logdata['logRef'];
            $keysval = $logdata['logRefConcat'];
            $usedQuantity = $filteredBatchSelection[$keysval];
            $bornDate = $logdata['bornDate'];
            $storage_location_id = $logdata['storage_location_id'];
            $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
            // $uom = '';
            if ($POST['select_customer_vendor'] == 'Customer') {
              $qtyyy = $usedQuantity;
            } else {
              $qtyyy = $usedQuantity;
            }

            $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                      SET 
                          companyId = '" . $company_id . "',
                          branchId = '" . $branch_id . "',
                          locationId = '" . $location_id . "',
                          parentId='". $credit_note_id ."',
                          storageLocationId = '" . $storage_location_id . "',
                          storageType ='" . $storageLocationTypeSlug . "',
                          itemId = '" . $item_id . "',
                          itemQty = '" . $qtyyy . "',
                          itemUom = '" . $uom . "',
                          itemPrice = '" . $rate . "',
                          refActivityName='CN',
                          logRef = '" . $logRef . "',
                          refNumber='" . $credit_note_no . "',
                          bornDate='" . $bornDate . "',
                          postingDate='" . $posting_date . "',
                          createdBy = '" . $created_by . "',
                          updatedBy = '" . $created_by . "'";

            $insStockreturn1 = queryInsert($insStockSummary1);
            // console($insStockreturn1);
            $itemNameStock = $itemController->getItemById($item_id);
            $auditTrail['action_data']['Stock Log Detail']['storage location'] = $storageLocationTypeSlug;
            $auditTrail['action_data']['Stock Log Detail']['Item Name'] = $itemNameStock['data']['item_name'];
            $auditTrail['action_data']['Stock Log Detail']['Item Quantity'] = $qtyyy;
            $auditTrail['action_data']['Stock Log Detail']['itemUom'] = $uom;
            $auditTrail['action_data']['Stock Log Detail']['itemPrice'] = $rate;
            $auditTrail['action_data']['Stock Log Detail']['Refarance Id'] = $logRef;
            $auditTrail['action_data']['Stock Log Detail']['bornDate'] = $bornDate;
            $auditTrail['action_data']['Stock Log Detail']['postingDate'] = $posting_date;
            $returnData['insStockreturn1'][] = $insStockreturn1;
            $returnData['insStockreturn2'][] = $selStockLog;
          }
        }

        if ($manualbatchselectionQty > 0 && !empty($manualbatchselectionSL)) {
          $manualBatchNumber = $item['manualbatchselection']['batchNumber'] ? $item['manualbatchselection']['batchNumber'] : "ST" . time();
          $manualBatchDate = $item['manualbatchselection']['bornDate'] ? $item['manualbatchselection']['bornDate'] : date('Y-m-d H:i:s');

          $btachData = queryGet("SELECT logRef,storageType, DATE_FORMAT(bornDate, '%Y-%m-%d') AS bornDate FROM erp_inventory_stocks_log WHERE logRef = '" . $manualBatchNumber . "' ORDER BY bornDate ASC LIMIT 1");

          $explodessl = explode('|', $manualbatchselectionSL);
          $slId = $explodessl[0];
          $storageType = $explodessl[1];

          $refNumber = $manualBatchNumber ?? $credit_note_no;
          $insStockSummaryManual = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
              SET 
                  companyId = '" . $company_id . "',
                  branchId = '" . $branch_id . "',
                  locationId = '" . $location_id . "',
                  parentId='". $credit_note_id ."',
                  storageLocationId = '" . $slId . "',
                  storageType ='" . $storageType . "',
                  itemId = '" . $item_id . "',
                  itemQty = '" . $manualbatchselectionQty . "',
                  itemUom = '" . $uom . "',
                  itemPrice = '" . $rate . "',
                  refActivityName='CNMANUAL',
                  logRef = '" . $manualBatchNumber . "',
                  refNumber='" . $credit_note_no . "',
                  bornDate='" . $manualBatchDate . "',
                  postingDate='" . $posting_date . "',
                  createdBy = '" . $created_by . "',
                  updatedBy = '" . $created_by . "'";

          $insStockreturnmanual = queryInsert($insStockSummaryManual);
          $itemNameStock = $itemController->getItemById($item_id);
          $auditTrail['action_data']['Stock Log Detail']['storage location'] = $storageType;
          $auditTrail['action_data']['Stock Log Detail']['Item Name'] = $itemNameStock['data']['item_name'];
          $auditTrail['action_data']['Stock Log Detail']['Item Quantity'] = $manualbatchselectionQty;
          $auditTrail['action_data']['Stock Log Detail']['itemUom'] = $uom;
          $auditTrail['action_data']['Stock Log Detail']['itemPrice'] = $rate;
          $auditTrail['action_data']['Stock Log Detail']['Refarance Id'] = $refNumber;
          $auditTrail['action_data']['Stock Log Detail']['bornDate'] = $manualBatchDate;
          $auditTrail['action_data']['Stock Log Detail']['postingDate'] = $posting_date;

          $returnData['insStockreturn3'][] = $insStockreturnmanual;
        }

        $returnData['status'] = "Success";
        $returnData['message'] = "Credit Note Created Successfully";
      } else {
        $returnData['status'] = "Warning";
        $returnData['message'] = "Something Went Wrong";
      }
    }


    /************************************Accounting Start ****************************************/

    $remarks = "Credit Note for " . $reasons . " " . $parent_id_code . " " . $remark;

    $accslug = $POST['select_customer_vendor'] . "CN"; // CN/DN

    $roundOffGL = 0;

    $symbol = $_POST["round_sign"];
    $roundOffValue = $_POST["round_value"];
    if ($symbol == "+") {
      $roundOffGL = $roundOffValue;
    } else {
      $roundOffGL = $roundOffValue * -1;
    }

    $partyDetails = [];

    if ($POST['select_customer_vendor'] == 'Vendor') {
      $vendorDetailsObj = queryGet('SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=' . $party_id);

      $partyDetails['partyCode'] = $vendorDetailsObj["data"]["vendor_code"] ?? 0;
      $partyDetails['partyName'] = $vendorDetailsObj["data"]["trade_name"] ?? 0;
      $partyDetails['parentGlId'] = $vendorDetailsObj["data"]["parentGlId"] ?? 0;
    } else {
      $vendorDetailsObj = queryGet('SELECT * FROM `erp_customer` WHERE `customer_id`=' . $party_id);

      $partyDetails['partyCode'] = $vendorDetailsObj["data"]["customer_code"] ?? 0;
      $partyDetails['partyName'] = $vendorDetailsObj["data"]["trade_name"] ?? 0;
      $partyDetails['parentGlId'] = $vendorDetailsObj["data"]["parentGlId"] ?? 0;
    }


    $postingAccountingData = [
      "documentNo" => $credit_note_no,
      "documentDate" => $posting_date,
      "invoicePostingDate" => $posting_date,
      "referenceNo" => $parent_id_code,
      "type" => 'CN',
      "for" => $POST['select_customer_vendor'],
      "journalEntryReference" => 'CN',
      "remarks" => addslashes($remarks),
      "compInvoiceType" =>  $compInvoiceType,
      "items" =>  $items,
      "roundOffValue" => $roundOffGL,
      "partyDetails" => $partyDetails,
      "taxDetails" => $taxDetails
    ];

    // console($postingAccountingData);

    $accPostingObj = [];

    if ($POST['select_customer_vendor'] == 'Vendor') {
      $accPostingObj = $accountingObj->cNoteForVendorAccountingPosting($postingAccountingData, $accslug, $credit_note_id);
    } else {

      $accPostingObj = $accountingObj->cNoteForCustomerAccountingPosting($postingAccountingData, $accslug, $credit_note_id);
    }

    if ($accPostingObj["status"] == "success" && $accPostingObj["journalId"] != "") {
      $queryObj = queryUpdate('UPDATE `erp_credit_note` SET `journal_id`=' . $accPostingObj["journalId"] . ' , `goods_journal_id` = ' . $accPostingObj['goodsJournalId'] . ' WHERE `cr_note_id`=' . $credit_note_id);
      $auditTrail['action_data']['Account Detail']['documentNo'] = $postingAccountingData['documentNo'];
      $auditTrail['action_data']['Account Detail']['documentDate'] = $postingAccountingData['documentDate'];
      $auditTrail['action_data']['Account Detail']['invoicePostingDate'] = $postingAccountingData['invoicePostingDate'];
      $auditTrail['action_data']['Account Detail']['compInvoiceType'] = $postingAccountingData['compInvoiceType'];
      $auditTrail['action_data']['Account Detail']['roundOffValue'] = $postingAccountingData['roundOffValue'];
      $auditTrail['action_data']['Account Detail']['partyDetails'] = $postingAccountingData['partyDetails'];
      $auditTrail['action_data']['Account Detail']['taxDetails'] = $postingAccountingData['taxDetails'];

      $returnData['status'] = "success";
      $returnData['message'] = "Credit note saved successfully";
      $returnData['credit_note_no'] = $credit_note_no;
      $returnData['postingAccountingData'] = $postingAccountingData;
      $returnData['acc'] = $accPostingObj;
    } else {

      $returnData['status'] = "success";
      $returnData['message'] = "Credit note saved successfully with out accounting!";
      $returnData['credit_note_no'] = $credit_note_no;
      $returnData['postingAccountingData'] = $postingAccountingData;
      $returnData['acc'] = $accPostingObj;
    }

    /************************************Accounting End ****************************************/

    /************************************Mail Sent ****************************************/
    //  $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));
    // $invoicelink = BASE_URL . 'branch/location/branch-so-invoice-view.php?inv_id=' . $encodeInv_id;

    // $invoicelinkWhatsapp = 'branch-so-invoice-view.php?inv_id=' . $encodeInv_id;
    $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
    $company_name = $companyDetailsObj['company_name'];
    $total = $subtotal + $cgst + $sgst + $igst;

    if ($mailValid == 'yes') {

      $to = $partyMail;
      $sub = 'Credit Note ' . $credit_note_no . ' for Your Recent Purchase';
      $msg = '
                <div>
                <div><strong>Dear ' . $party_name . ',</strong></div>
                <p>
                    I hope this email finds you well. I am writing to inform you that an Credit Note for your recent purchase with <b>' . $company_name . '</b> has been generated and is now ready for payment.
                </p>
                <strong>
                    Credit Note details:
                </strong>
                <div style="display:grid">
                    <span>
                        Credit Note Number: ' . $credit_note_no . '
                    </span>
                    <span>
                        Amount Due: ' . $total . '
                    </span>
                </div>
                <p>
                    To make a payment, please follow the instructions included in the attached credit note. If you have any questions or need assistance with the payment process, please do not hesitate to contact our finance team.
                </p>
                <p>
                    Thank you for your business and for choosing <b>' . $company_name . '</b>. We appreciate your continued support and look forward to serving you again in the future.
                </p>
                <div style="display:grid">
                    Best regards for, <span><b>' . $company_name . '</b></span>
                </div>
                <p>
                <a href="' . BASE_URL . 'branch/location/classic-view/invoice-preview-print.php?cr_note_id=' . base64_encode($credit_note_id) . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Credit Note</a>
                
                </p>
                </div>';
      SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $party_code, 'creditNote', $credit_note_id, $credit_note_no);
    }
    $auditTrailreturn = generateAuditTrail($auditTrail);
  } else {
    $returnData['status'] = "Warning";
    $returnData['message'] = "Something Went Wrong";
  }
  //exit();
  return $returnData;
}
function credit_note_add_by_rule_book($POST)
{
  //  console($POST);
  //  exit;
  $accountingObj = new Accounting();
  $itemController = new ItemsController();
  $failedAccController= new FailedAccController();
  global $dbCon;
  global $location_id;
  global $company_id;
  global $branch_id;
  global $created_by;

  $returnData = [];
  $party_id = 0;
  $party_code = 0;
  $party_name = 0;



  $isValidate = validate($POST, [
    "vendor_customer" => "required",
    "posting_date" => "required",
    "billToInput" => "required",
    "shipToInput" => "required"

  ]);
  if ($isValidate["status"] != "success") {
    $returnData['status'] = "warning";
    $returnData['message'] = "Invalid form inputes";
    $returnData['errors'] = $isValidate["errors"];
    return $returnData;
  }
  $party = explode('|', $POST['vendor_customer']);



  if ($POST['select_customer_vendor'] == 'Customer') {
    $creditor_type = "customer";
    $party_id = $party[0];
    $customer_sql = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = $party_id");
    $party_code = $customer_sql['data']['customer_code'];
    $party_name = addslashes($customer_sql['data']['trade_name']);
    $partyMail = $customer_sql['data']['customer_authorised_person_email'];
    $mailValid = $customer_sql['data']['isMailValid'];
  } else {
    $creditor_type = "vendor";
    $party_id = $party[0];
    $vendor_sql = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id` = $party_id");
    $party_code = $vendor_sql['data']['vendor_code'];
    $party_name = addslashes($vendor_sql['data']['trade_name']);
    $partyMail = $vendor_sql['data']['vendor_authorised_person_email'];
    $mailValid = $vendor_sql['data']['isMailValid'];
  }


  $bill = $POST['bill'];
  if ($bill == 'select invoice' || $bill == "") {
    $parent_id = 0;
  } else {
    $parent_id = explode('|', $POST['bill'])[0] ?? 0;
    $parent_id_code = explode('|', $POST['bill'])[2] ?? '';
  }


  $compInvoiceType = $POST['compInvoiceType'] ?? '';

  $reasons = $POST['reasons'];
  $posting_date = $POST['posting_date'];
  $remark = addslashes($POST['note']) ?? '';
  $source_address = addslashes($POST['source']);
  $destination_address = addslashes($POST['destination']);
  $subtotal = $POST['grandTotal'];
  $bill_address = $POST['billToInput'] ?? 0;
  $ship_address = $POST['shipToInput'] ?? 0;
  $round_value = isset($_POST['round_value']) && $_POST['round_value'] !== '' ? $_POST['round_value'] : 0;
  $round_sign = isset($_POST['round_sign']) && $_POST['round_sign'] !== '' ? $_POST['round_sign'] : '';
  $tcs = $_POST['total_tcs'] ?? 0.00;
  $tds = $_POST['total_tds'] ?? 0.00;
  $discount = $_POST['total_discount'] ?? 0.00;

  $adjustment = $round_sign . '' . $round_value;


  $attachment = $POST['attachment'];
  $name = $attachment["name"];
  $tmpName = $attachment["tmp_name"];
  $size = $attachment["size"];

  $contactDetails = $POST['companyConfigId'];
  // $taxData = json_decode($POST['gstdetails'], true);


  $taxDetails = [];
  $taxDetails['cgst'] = $POST['cgst'] ?? 0;
  $taxDetails['sgst'] = $POST['sgst'] ?? 0;
  $taxDetails['igst'] = $POST['igst'] ?? 0;
  $taxDetails['gst'] =  0;

  $cgst =  0;;
  $sgst =  0;
  $igst =  0;
  $taxComponents = '';

  $companyCountry = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
  $taxData = json_decode($_POST['gstdetails'], true);
  $getItemTaxRule = getItemTaxRule($companyCountry, $source_address, $destination_address);
  $json_data = $getItemTaxRule['data'];
  $data = json_decode($json_data, true);

  if ($companyCountry == "103") {
    $taxComponents = $_POST['gstdetails'];

    foreach ($taxData as $tax) {
      if ($tax['gstType'] === 'CGST') {
        $cgst = $tax['taxAmount'] ?? 0;
        $taxDetails['cgst'] = $cgst;
      } elseif ($tax['gstType'] === 'SGST') {
        $sgst = $tax['taxAmount'] ?? 0;
        $taxDetails['sgst'] = $sgst;
      } elseif ($tax['gstType'] === "IGST") {
        $igst = $tax['taxAmount'] ?? 0;
        $taxDetails['igst'] = $igst;
      }
    }
  } else {
    $total_gst = $_POST['grandTaxAmtInp'];
    $taxComponents = $_POST['gstdetails'];
    $taxDetails['gst'] =  $taxData[0]['taxAmount'];
  }

  $taxComponent_tax = json_decode($taxComponents, true);


  $allowed_types = ['jpg', 'png', 'jpeg', 'pdf'];
  $maxsize = 2 * 1024 * 1024; // 10 MB


  $fileUploaded = uploadFile(["name" => $name, "tmp_name" => $tmpName, "size" => $size], COMP_STORAGE_DIR . "/others/", $allowed_types, $maxsize, 0);
  // console($fileUploaded);
  $attachment_name = $fileUploaded['data'];




  if (
    $creditor_type == 'customer'
  ) {
    $invoice_current_dueObj = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE so_invoice_id=$parent_id");
    $cnSql = "SELECT *  FROM erp_credit_note WHERE creditNoteReference='" . $parent_id . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND `creditors_type`='customer' AND status='active'";


    $cnRes = queryGet($cnSql, true);
    if ($cnRes['numRows'] > 0) {
      $cnData = $cnRes['data'];

      $totalCreditNoteAmount = queryGet("SELECT SUM(total) AS totalCreditNoteAmount FROM erp_credit_note WHERE creditNoteReference='" . $parent_id . "' AND company_id=$company_id AND `creditors_type`='customer' AND branch_id=$branch_id AND location_id=$location_id GROUP BY creditNoteReference;")['data']['totalCreditNoteAmount'] ?? 0;
    }
    $invoice_current_due = $invoice_current_dueObj['data']['due_amount'];
    $invoice_current_due = $invoice_current_due - $totalCreditNoteAmount;

    if ($invoice_current_dueObj['numRows'] > 0 && $subtotal > $invoice_current_due) {
      $returnData['status'] = "Warning";
      $returnData['message'] = "Credit note amount can not greter than invoice due amount";
      return $returnData;
      exit();
    }
  }
  if (
    $creditor_type == 'vendor'
  ) {
    $invoice_current_dueObj = queryGet("SELECT * FROM `erp_grninvoice` WHERE grnIvId=$parent_id");
    $invoice_current_due = $invoice_current_dueObj['data']['dueAmt'];
    $cnSql = "SELECT *  FROM erp_credit_note WHERE creditNoteReference='" . $parent_id . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND `creditors_type`='vendor' AND status='active'";


    $cnRes = queryGet($cnSql, true);
    if ($cnRes['numRows'] > 0) {
      $cnData = $cnRes['data'];

      $totalCreditNoteAmount = queryGet("SELECT SUM(total) AS totalCreditNoteAmount FROM erp_credit_note WHERE creditNoteReference='" . $parent_id . "' AND `creditors_type`='vendor' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id GROUP BY creditNoteReference;")['data']['totalCreditNoteAmount'] ?? 0;
    }
    $invoice_current_due = $invoice_current_due - $totalCreditNoteAmount;
    if ($invoice_current_dueObj['numRows'] > 0 && $subtotal > $invoice_current_due) {
      $returnData['status'] = "Warning";
      $returnData['message'] = "Credit note amount can not greter than invoice due amount";
      return $returnData;
      exit();
    }
  }
  $itemsQtyUpdate = [];


  $insert_credit_sql = "INSERT INTO  `erp_credit_note`
                      SET
                      `company_id`=$company_id,
                      `branch_id`=$branch_id,
                      `location_id`=$location_id,
                      `creditors_type`='" . $creditor_type . "',
                      `party_id`=$party_id,
                      `party_code`='" . $party_code . "',
                      `party_name`='" . $party_name . "',
                      `credit_note_no`= '',
                      `variant_id` = 0,
                      `creditNoteReference` = '" . $parent_id . "',
                      `postingDate` = '" . $posting_date . "',
                      `remark` = '" . $remark . "',
                      `source_address` = '" . $source_address . "',
                      `destination_address` = '" . $destination_address . "',
                      `billing_address` = '" . $bill_address . "',
                      `shipping_address` = '" . $ship_address . "',
                      `contact_details`='" . $contactDetails . "',
                      `total` = '" . $subtotal . "',
                      `adjustment` = '" . $adjustment . "',
                      `attachment` = '" . $attachment_name . "',
                      `status` = 'active',
                      `cgst`='" . $cgst . "',
                      `sgst`='" . $sgst . "',
                      `igst`='" . $igst . "',
                      `tds`='" . $tds . "',
                      `tcs`='" . $tcs . "',
                      `discount`='" . $discount . "',
                      `taxComponents`='" . $taxComponents . "',
                      `created_by` = '" . $created_by . "',
                      `updated_by` ='" . $created_by . "'";

  $insert_credit = queryInsert($insert_credit_sql);

  if ($insert_credit['status'] == "success") {

    $credit_note_id = $insert_credit['insertedId'] ?? 0;

    $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
    $auditTrail = array();
    $auditTrail['basicDetail']['trail_type'] = 'ADD';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
    $auditTrail['basicDetail']['table_name'] = 'erp_credit_note';
    $auditTrail['basicDetail']['column_name'] = 'cr_note_id'; // Primary key column
    $auditTrail['basicDetail']['document_id'] = $credit_note_id;  // primary key
    $auditTrail['basicDetail']['party_type'] = $creditor_type;
    $auditTrail['basicDetail']['party_id'] = $party_id;
    $auditTrail['basicDetail']['action_code'] = $action_code;
    $auditTrail['basicDetail']['action_referance'] = '';
    $auditTrail['basicDetail']['action_title'] = 'Credit Note Add';  //Action comment
    $auditTrail['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
    $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
    $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
    $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
    $auditTrail['basicDetail']['action_sqlQuery'] = base64_encode($insert_credit_sql);
    $auditTrail['basicDetail']['others'] = '';
    $auditTrail['basicDetail']['remark'] = $remark;

    $auditTrail['action_data']['Credit Note Detail']['party name'] = $party_name;
    $auditTrail['action_data']['Credit Note Detail']['party type'] = $creditor_type;
    $auditTrail['action_data']['Credit Note Detail']['party code'] = $party_code;
    $auditTrail['action_data']['Credit Note Detail']['postingDate'] = formatDateWeb($posting_date);
    // $auditTrail['action_data']['Credit Note Detail']['source_address'] = getStateDetail($source_address)["data"];
    // $auditTrail['action_data']['Credit Note Detail']['destination_address'] = getStateDetail($destination_address)['data'];
    // $auditTrail['action_data']['Credit Note Detail']['billing_address'] = getStateDetail($bill_address)['data'];
    // $auditTrail['action_data']['Credit Note Detail']['shipping_address'] = getStateDetail($ship_address)['data'];
    // $auditTrail['action_data']['Credit Note Detail']['contact_details'] = $contactDetails;
    // $auditTrail['action_data']['Credit Note Detail']['shipping_address'] = $ship_address;
    $auditTrail['action_data']['Credit Note Detail']['total'] = decimalValuePreview($subtotal);
    // $auditTrail['action_data']['Credit Note Detail']['adjustment'] = $adjustment;
    // $auditTrail['action_data']['Credit Note Detail']['attachment'] = $attachment_name;
    // $auditTrail['action_data']['Credit Note Detail']['cgst'] = $cgst;
    // $auditTrail['action_data']['Credit Note Detail']['igst'] = $igst;
    // $auditTrail['action_data']['Credit Note Detail']['sgst'] = $sgst;
    foreach ($taxComponent_tax as $tax) {
      $auditTrail['action_data']['Invoice Details'][$tax['gstType']] = decimalValuePreview($tax['taxAmount']);
    }
    $auditTrail['action_data']['Credit Note Detail']['created_by'] = getCreatedByUser($created_by);
    $auditTrail['action_data']['Credit Note Detail']['updated_by'] = getCreatedByUser($created_by);

    if (isset($POST['repost']) && $POST['repost'] == 1) {

      $reverse_cn_id = $POST['reverse_cn_id'];
      $variant_id = $POST['last_iv_varient'];
      $update_reverse_cn = queryUpdate("UPDATE `erp_credit_note` SET `status`='reposted' WHERE `cr_note_id` = $reverse_cn_id");
      // console($update_reverse_cn);
      $credit_note_no = $POST['iv_varient'];
      $invoice_no_serialized = base64_decode($POST['repost_serialized']);
    } else {
      $variant_id = $POST['iv_varient'];

      $IvNoByVerientresponse = getCNNumberByVerient($POST['iv_varient']);
      $credit_note_no = $IvNoByVerientresponse['iv_number'];
      $invoice_no_serialized = $IvNoByVerientresponse['iv_number_array'];
    }



    $updateInv = "UPDATE `erp_credit_note` 
                    SET 
                    `variant_id` = $variant_id,
                    `credit_note_no`='$credit_note_no',
                    `credit_note_no_serialized`='$invoice_no_serialized'
                 WHERE cr_note_id='$credit_note_id'";
    queryUpdate($updateInv);
    $credit_note_no_unserialized = unserialize($invoice_no_serialized);
    $auditTrail['basicDetail']['document_number'] = $credit_note_no;
    // $auditTrail['action_data']['Debit Note Detail']['prefix'] = $credit_note_no_unserialized['prefix'];
    // $auditTrail['action_data']['Debit Note Detail']['fy'] = $credit_note_no_unserialized['fy'];
    // $auditTrail['action_data']['Debit Note Detail']['serial'] = $credit_note_no_unserialized['serial'];

    $credit_items = $POST['item'];
    $items = [];
    foreach ($credit_items as $key => $item) {
      // console($item);
      // $item_code = $item['item_code'];
      $qty = $item['qty'];
      // echo "ok";
      $rate = $item['rate'];
      $rate2 = $item['rate'];
      $dis = $item['disval'] ?? 0;

      $gstArray=[];
      // echo "ok";
      $tax = $item['tax'];
      $rate = $rate - ($dis / $qty);

      $withouttax = $qty * $rate;

      //  echo "ok";
      $tax_amount = ($tax / 100) * ($qty * $rate);
      foreach ($data['tax'] as $taxDetail) {
        $gstDetails = [
          'gstType' => $taxDetail['taxComponentName'],
          'taxPercentage' => $taxDetail['taxPercentage'],
          'taxAmount' => $tax_amount/(100/$taxDetail['taxPercentage'])
        ];
        $gstArray[] = $gstDetails;
      }
      $gstDetails=json_encode($gstArray);
      $itemigst = 0;
      $itemcgst = 0;
      $itemsgst = 0;
      $itemtds = $item['total_tds_per'] ?? 0;

      if ($companyCountry == '103') {
        if ($igst > 0) {
          $itemigst = $tax_amount;
        } else {
          $itemcgst = $tax_amount / 2;
          $itemsgst = $tax_amount / 2;
        }
      }
      //  echo "ok";
      $amount = ($qty * $rate) + ($tax_amount);
      // $amount = $item['amount'];
      $itemArrys = array();
      $itemArrys = explode('_', $item['item_id']);
      $item_id = $itemArrys[0] ?? 0;

      $subgl_code = '';
      $subgl_name = '';
      $goodsType = '';
      $account = $item['account'];
      $uom = '';

      if (count($itemArrys) > 0) {
        $itemglQry = queryGet("SELECT baseUnitMeasure,parentGlId,itemCode,itemName,goodsType FROM `" . ERP_INVENTORY_ITEMS . "` WHERE itemId='" . $item_id . "' AND company_id = '" . $company_id . "' ");
        $itemgl = $itemglQry['data'];
        $subgl_code = $itemgl['itemCode'];
        $subgl_name = $itemgl['itemName'];
        $goodsType = $itemgl['goodsType'];
        $uom = $itemgl['baseUnitMeasure'];
        $account = $itemgl['parentGlId'];
      }
      $items[$key]['accountGl'] = $account;
      $items[$key]['goodsType'] = $goodsType;
      $items[$key]['subgl_code'] = $subgl_code;
      $items[$key]['subgl_name'] = $subgl_name;
      $items[$key]['withouttax'] = $withouttax;
      $items[$key]['tax'] = $tax_amount;

      $insert_credit_items = queryInsert("INSERT INTO  `credit_note_item`
                                                        SET
                                                        `account` = '" . $account . "',
                                                        `item_id`=$item_id,
                                                        `invoice_id` = $parent_id,
                                                        `credit_note_id`= $credit_note_id,
                                                        `item_qty` = '" . $qty . "',
                                                        `item_rate` = '" . $rate2 . "',
                                                        `item_dis_rate` = '" . $rate . "',
                                                        `item_tax` = '" . $tax . "',
                                                        `cgst`='" . $itemcgst . "',
                                                        `sgst`='" . $itemsgst . "',
                                                        `igst`='" . $itemigst . "',
                                                        `tds`='" . $itemtds . "',
                                                        `taxComponents`='".$gstDetails."',
                                                        `item_amount` = '" . $amount . "',
                                                        `discount_amount` = '" . $dis . "',
                                                        `created_by` = '" . $created_by . "',
                                                        `updated_by` ='" . $created_by . "'
                                                        ");


      if ($insert_credit_items['status'] == "success") {
        $itemName = $itemController->getItemById($item_id);
        $accountGl = getChartOfAccountsDataDetails($account);
        $auditTrail['action_data']['Credit Note Item Detail']['item'] = $itemName['data']['itemName'];
        $auditTrail['action_data']['Credit Note Item Detail']['account'] = $accountGl['data']['gl_label'];
        $auditTrail['action_data']['Credit Note Item Detail']['item_qty'] = decimalQuantityPreview($qty);
        $auditTrail['action_data']['Credit Note Item Detail']['item_rate'] = decimalValuePreview($rate);
        $auditTrail['action_data']['Credit Note Item Detail']['item_tax'] = decimalValuePreview($tax);
        // $auditTrail['action_data']['Credit Note Item Detail']['cgst'] = $itemcgst;
        // $auditTrail['action_data']['Credit Note Item Detail']['sgst'] = $itemsgst;
        // $auditTrail['action_data']['Credit Note Item Detail']['igst'] = $itemigst;
        $auditTrail['action_data']['Credit Note Item Detail']['item_amount'] = decimalValuePreview($amount);
        $manualbatchselectionQty = $item['manualbatchselection']['qty'] ?? 0;
        $manualbatchselectionSL = $item['manualbatchselection']['storageLocation'];

        if (count($item['batchselection']) > 0) {

          // echo count($item['batchselection']).'--Batch Selection';

          $filteredBatchSelection = [];

          foreach ($item['batchselection'] as $key => $value) {
            $explodes = explode('_', $key);
            $logRef = $explodes[0];
            $slocation = $explodes[1];

            $keysval = $logRef . $slocation;

            if (!empty($value)) {
              $filteredBatchSelection[$keysval] = $value;
            }
          }

          $keysString = "'" . implode("', '", array_keys($filteredBatchSelection)) . "'";


          $selStockLog = itemQtyStockChecking($item_id, "'rmWhOpen', 'fgWhOpen'", 'ASC', "$keysString", $posting_date, 1);
          // console($selStockLog);
          $itemOpenStocks = $selStockLog['sumOfBatches'];



          foreach ($selStockLog['data'] as $stockLogKey => $logdata) {
            // $explodes = explode('_', $logdata['logRef']);
            // $logRef = $explodes[0];
            $logRef = $logdata['logRef'];
            $keysval = $logdata['logRefConcat'];
            $usedQuantity = $filteredBatchSelection[$keysval];
            $bornDate = $logdata['bornDate'];
            $storage_location_id = $logdata['storage_location_id'];
            $storageLocationTypeSlug = $logdata['storageLocationTypeSlug'];
            // $uom = '';
            if ($POST['select_customer_vendor'] == 'Customer') {
              $qtyyy = $usedQuantity;
            } else {
              $qtyyy = $usedQuantity;
            }

            $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                      SET 
                          companyId = '" . $company_id . "',
                          branchId = '" . $branch_id . "',
                          locationId = '" . $location_id . "',
                          parentId='". $credit_note_id ."',
                          storageLocationId = '" . $storage_location_id . "',
                          storageType ='" . $storageLocationTypeSlug . "',
                          itemId = '" . $item_id . "',
                          itemQty = '" . $qtyyy . "',
                          itemUom = '" . $uom . "',
                          itemPrice = '" . $rate2 . "',
                          refActivityName='CN',
                          logRef = '" . $logRef . "',
                          refNumber='" . $credit_note_no . "',
                          bornDate='" . $bornDate . "',
                          postingDate='" . $posting_date . "',
                          createdBy = '" . $created_by . "',
                          updatedBy = '" . $created_by . "'";

            $insStockreturn1 = queryInsert($insStockSummary1);
            // console($insStockreturn1);
            $itemsQtyUpdate[] = [
              'itemId' => $item_id,
              'qty' => $usedQuantity,
              'type' => "cn",
              "id" => $credit_note_id,
              "rate"=> $rate2
            ];
            $itemNameStock = $itemController->getItemById($item_id);
            $auditTrail['action_data']['Stock Log Detail']['storage location'] = $storageLocationTypeSlug;
            // $auditTrail['action_data']['Stock Log Detail']['Item Name'] = $itemNameStock['data']['item_name'];
            $auditTrail['action_data']['Stock Log Detail']['Item Quantity'] = decimalQuantityPreview($qtyyy);
            // $auditTrail['action_data']['Stock Log Detail']['itemUom'] = $uom;
            $auditTrail['action_data']['Stock Log Detail']['itemPrice'] = decimalValuePreview($rate);
            $auditTrail['action_data']['Stock Log Detail']['Refarance Id'] = $logRef;
            $auditTrail['action_data']['Stock Log Detail']['bornDate'] = formatDateTime($bornDate);
            $auditTrail['action_data']['Stock Log Detail']['postingDate'] = formatDateWeb($posting_date);
            $returnData['insStockreturn1'][] = $insStockreturn1;
            $returnData['insStockreturn2'][] = $selStockLog;
          }
        }

        if ($manualbatchselectionQty > 0 && !empty($manualbatchselectionSL)) {
          $manualBatchNumber = $item['manualbatchselection']['batchNumber'] ? $item['manualbatchselection']['batchNumber'] : "ST" . time();
          $manualBatchDate = $item['manualbatchselection']['bornDate'] ? $item['manualbatchselection']['bornDate'] : date('Y-m-d H:i:s');

          $btachData = queryGet("SELECT logRef,storageType, DATE_FORMAT(bornDate, '%Y-%m-%d') AS bornDate FROM erp_inventory_stocks_log WHERE logRef = '" . $manualBatchNumber . "' ORDER BY bornDate ASC LIMIT 1");

          $explodessl = explode('|', $manualbatchselectionSL);
          $slId = $explodessl[0];
          $storageType = $explodessl[1];

          $refNumber = $manualBatchNumber ?? $credit_note_no;
          $insStockSummaryManual = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
              SET 
                  companyId = '" . $company_id . "',
                  branchId = '" . $branch_id . "',
                  locationId = '" . $location_id . "',
                  parentId='". $credit_note_id ."',
                  storageLocationId = '" . $slId . "',
                  storageType ='" . $storageType . "',
                  itemId = '" . $item_id . "',
                  itemQty = '" . $manualbatchselectionQty . "',
                  itemUom = '" . $uom . "',
                  itemPrice = '" . $rate . "',
                  refActivityName='CNMANUAL',
                  logRef = '" . $manualBatchNumber . "',
                  refNumber='" . $credit_note_no . "',
                  bornDate='" . $manualBatchDate . "',
                  postingDate='" . $posting_date . "',
                  createdBy = '" . $created_by . "',
                  updatedBy = '" . $created_by . "'";

          $insStockreturnmanual = queryInsert($insStockSummaryManual);
          $itemNameStock = $itemController->getItemById($item_id);
          $auditTrail['action_data']['Stock Log Detail']['storage location'] = $storageLocationTypeSlug;
          // $auditTrail['action_data']['Stock Log Detail']['Item Name'] = $itemNameStock['data']['item_name'];
          $auditTrail['action_data']['Stock Log Detail']['Item Quantity'] = decimalQuantityPreview($qtyyy);
          // $auditTrail['action_data']['Stock Log Detail']['itemUom'] = $uom;
          $auditTrail['action_data']['Stock Log Detail']['itemPrice'] = decimalValuePreview($rate);
          $auditTrail['action_data']['Stock Log Detail']['Refarance Id'] = $logRef;
          $auditTrail['action_data']['Stock Log Detail']['bornDate'] = formatDateTime($bornDate);
          $auditTrail['action_data']['Stock Log Detail']['postingDate'] = formatDateWeb($posting_date);

          $returnData['insStockreturn3'][] = $insStockreturnmanual;
        }

        $returnData['status'] = "Success";
        $returnData['message'] = "Credit Note Created Successfully";
      } else {
        $returnData['status'] = "Warning";
        $returnData['message'] = "Something Went Wrong";
      }
    }


    /************************************Accounting Start ****************************************/

    $remarks = "Credit Note for " . $reasons . "-" . $parent_id_code . " " . $remark;

    $accslug = $POST['select_customer_vendor'] . "CN"; // CN/DN

    $roundOffGL = 0;

    $symbol = $_POST["round_sign"];
    $roundOffValue = $_POST["round_value"];
    if ($symbol == "+") {
      $roundOffGL = $roundOffValue;
    } else {
      $roundOffGL = $roundOffValue * -1;
    }

    $partyDetails = [];

    if ($POST['select_customer_vendor'] == 'Vendor') {
      $vendorDetailsObj = queryGet('SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=' . $party_id);

      $partyDetails['partyCode'] = $vendorDetailsObj["data"]["vendor_code"] ?? 0;
      $partyDetails['partyName'] = $vendorDetailsObj["data"]["trade_name"] ?? 0;
      $partyDetails['parentGlId'] = $vendorDetailsObj["data"]["parentGlId"] ?? 0;
    } else {
      $vendorDetailsObj = queryGet('SELECT * FROM `erp_customer` WHERE `customer_id`=' . $party_id);

      $partyDetails['partyCode'] = $vendorDetailsObj["data"]["customer_code"] ?? 0;
      $partyDetails['partyName'] = $vendorDetailsObj["data"]["trade_name"] ?? 0;
      $partyDetails['parentGlId'] = $vendorDetailsObj["data"]["parentGlId"] ?? 0;
    }


    $postingAccountingData = [
      "documentNo" => $credit_note_no,
      "documentDate" => $posting_date,
      "invoicePostingDate" => $posting_date,
      "referenceNo" => $parent_id_code,
      "type" => 'CN',
      "for" => $POST['select_customer_vendor'],
      "journalEntryReference" => 'CN',
      "remarks" => addslashes($remarks),
      "compInvoiceType" =>  $compInvoiceType,
      "items" =>  $items,
      "roundOffValue" => $roundOffGL,
      "partyDetails" => $partyDetails,
      "taxDetails" => $taxDetails,
      "tdsDetails" => $tds,
      "tcsDetails" => $tcs,
    ];

    // console($postingAccountingData);

    $accPostingObj = [];

    if ($POST['select_customer_vendor'] == 'Vendor') {
      if ($companyCountry == 103) {
        $accPostingObj = $accountingObj->cNoteForVendorAccountingPosting($postingAccountingData, $accslug, $credit_note_id);
      } else {
        $accPostingObj = $accountingObj->cNoteForVendorAccountingPosting_by_rule_book($postingAccountingData, $accslug, $credit_note_id);
      }
    } else {
      if ($companyCountry == 103) {
        $accPostingObj = $accountingObj->cNoteForCustomerAccountingPosting($postingAccountingData, $accslug, $credit_note_id);
      } else {
        $accPostingObj = $accountingObj->cNoteForCustomerAccountingPosting_by_rule_book($postingAccountingData, $accslug, $credit_note_id);
      }
    }

    if ($accPostingObj["status"] == "success" && $accPostingObj["journalId"] != "") {
      $queryObj = queryUpdate('UPDATE `erp_credit_note` SET `journal_id`=' . $accPostingObj["journalId"] . ' , `goods_journal_id` = ' . $accPostingObj['goodsJournalId'] . ' WHERE `cr_note_id`=' . $credit_note_id);
      // $auditTrail['action_data']['Account Detail']['documentNo'] = $postingAccountingData['documentNo'];
      // $auditTrail['action_data']['Account Detail']['documentDate'] = $postingAccountingData['documentDate'];
      // $auditTrail['action_data']['Account Detail']['invoicePostingDate'] = $postingAccountingData['invoicePostingDate'];
      // $auditTrail['action_data']['Account Detail']['compInvoiceType'] = $postingAccountingData['compInvoiceType'];
      // $auditTrail['action_data']['Account Detail']['roundOffValue'] = $postingAccountingData['roundOffValue'];
      // $auditTrail['action_data']['Account Detail']['partyDetails'] = $postingAccountingData['partyDetails'];
      // $auditTrail['action_data']['Account Detail']['taxDetails'] = $postingAccountingData['taxDetails'];

      // stockQtyImpact($itemsQtyUpdate);
      foreach ($itemsQtyUpdate as $item) {
         $vClass = fetchValuationByItemId($item['itemId']);
        if($vClass=="v")
        {
          $mwp = calculateNewMwp($item['itemId'], $item['qty'], $item['rate'], "GRN");
        }else{
          summeryDirectStockUpdateByItemId($item['itemId'],abs($item['qty']),"+");
          
        }
      }
      
      $returnData['status'] = "success";
      $returnData['message'] = "Credit note saved successfully";
      $returnData['credit_note_no'] = $credit_note_no;
      $returnData['postingAccountingData'] = $postingAccountingData;
      $returnData['acc'] = $accPostingObj;
    } else {
      $failedAcc=$failedAccController->logAccountingFailure($credit_note_id,"CreditNote");
      
      $returnData['status'] = "success";
      $returnData['message'] = "Credit note saved successfully with out accounting!";
      $returnData['credit_note_no'] = $credit_note_no;
      $returnData['postingAccountingData'] = $postingAccountingData;
      $returnData['acc'] = $accPostingObj;
      $returnData['failedAcc'] =$failedAcc;
    }

    /************************************Accounting End ****************************************/

    /************************************Mail Sent ****************************************/
    //  $duedate = date("Y-m-d", strtotime("+" . $creditPeriod . " days"));
    // $invoicelink = BASE_URL . 'branch/location/branch-so-invoice-view.php?inv_id=' . $encodeInv_id;

    // $invoicelinkWhatsapp = 'branch-so-invoice-view.php?inv_id=' . $encodeInv_id;
    $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
    $company_name = $companyDetailsObj['company_name'];
    $total = $subtotal + $cgst + $sgst + $igst;
    if ($mailValid == 'yes') {
      $to = $partyMail;
      $sub = 'Credit Note ' . $credit_note_no . ' for Your Recent Purchase';
      $msg = '
                <div>
                <div><strong>Dear ' . $party_name . ',</strong></div>
                 <p>
                    I hope this email finds you well. I am writing to inform you that an Credit Note for your recent purchase with <b>' . $company_name . '</b> has been generated and is now ready for payment.
                </p>
                <strong>
                    Credit Note details:
                </strong>
                <div style="display:grid">
                    <span>
                        Credit Note Number : ' . $credit_note_no . '
                    </span>
                    <span>
                        Amount Due: ' . $total . '
                    </span>
                </div>
                <p>
                    To make a payment, please follow the instructions included in the attached credit note. If you have any questions or need assistance with the payment process, please do not hesitate to contact our finance team.
                </p>
                <p>
                    Thank you for your business and for choosing <b>' . $company_name . '</b>. We appreciate your continued support and look forward to serving you again in the future.
                </p>
                <div style="display:grid">
                    Best regards for, <span><b>' . $company_name . '</b></span>
                </div>
                <p>
                <a href="' . BASE_URL . 'branch/location/classic-view/invoice-preview-print-taxcomponents.php?cr_note_id=' . base64_encode($credit_note_id) . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View Credit Note </a>
                
                </p>
                </div>';
      SendMailByMySMTPmailTemplate($to, $sub, $msg, null, $party_code, 'creditNote', $credit_note_id, $credit_note_no);
    }
    $auditTrailreturn = generateAuditTrail($auditTrail);
  } else {
    $returnData['status'] = "Warning";
    $returnData['message'] = "Something Went Wrong";
  }
  //exit();
  return $returnData;
}

function creation_date_selections()
{
  global $dbCon;
  global $company_id;
  global $admin_variant;
  $status = 0;

  $returnData = [];
  //$customerPO = $POST['customerPO'];
  $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
  $check_var_data = $check_var_sql['data'];
  $max = $check_var_data['month_end'];
  $min = $check_var_data['month_start'];
  $month_var = date("m-Y", strtotime($max));


  //  //return $company_id;
  //   $sql = queryGet("SELECT (SELECT MAX(postingDate) FROM erp_credit_note WHERE postingDate < (SELECT postingDate FROM erp_credit_note WHERE credit_note_no = '$credit_note_no' AND `variant_id` = $variant_id AND company_id = $company_id LIMIT 1)) AS previous_date, (SELECT MIN(postingDate) FROM erp_credit_note WHERE postingDate > (SELECT postingDate FROM erp_credit_note WHERE credit_note_no = '$credit_note_no' AND `variant_id` = $variant_id AND company_id = $company_id LIMIT 1)) AS next_date FROM erp_credit_note WHERE credit_note_no = '$credit_note_no' AND `variant_id` = $variant_id AND company_id = $company_id");

  $sql = queryGet("SELECT t1.postingDate AS prev_date FROM erp_credit_note t1 WHERE t1.company_id = $company_id ORDER BY t1.postingDate DESC LIMIT 1;");

  $current_date = date("Y-m-d");
  $current_date_variant = date("m-Y");

  if (is_null($sql['data']['prev_date'])) {
    // console("Both Dates is null");
    if ($current_date_variant == $month_var) {
      $status = 1;
      $prev_date = $min;
      $next_date = $current_date;
    } elseif ($month_var > $current_date_variant) {
      $status = 0;
      $prev_date = '';
      $next_date = '';
    } else {
      $status = 1;
      $prev_date = $min;
      $next_date = $max;
    }
  } else {
    $prevMonthVariant = date("m-Y", strtotime($sql['data']['prev_date']));
    if ($month_var == $prevMonthVariant) {
      $status = 1;
      $prev_date = $sql['data']['prev_date'];
      $next_date = $current_date;
    } elseif ($month_var > $prevMonthVariant) {
      if ($month_var == $current_date_variant) {
        $status = 1;
        $prev_date = $min;
        $next_date = $current_date;
      } elseif ($month_var > $current_date_variant) {
        $status = 0;
        $prev_date = '';
        $next_date = '';
      } else {
        $status = 1;
        $prev_date = $min;
        $next_date = $max;
      }
    } else {
      $status = 0;
      $prev_date = '';
      $next_date = '';
    }
  }

  if ($sql['status'] == 'success') {

    $returnData['status'] = 'success';
    $returnData['message'] = 'ok';
    $returnData['start_date'] = $prev_date;
    $returnData['end_date'] = $next_date;
    $returnData['dateStatus'] = $status;
  } else {
    $returnData['status'] = 'warning';
    $returnData['message'] = 'something went wrong';
    $returnData['dateStatus'] = $status;
  }

  return $returnData;
}

function date_select($variant_id, $cr_id)
{
  global $dbCon;
  global $company_id;
  global $admin_variant;
  $status = 0;

  $returnData = [];
  //$customerPO = $POST['customerPO'];
  $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
  $check_var_data = $check_var_sql['data'];
  $max = $check_var_data['month_end'];
  $min = $check_var_data['month_start'];
  $month_var = date("m-Y", strtotime($max));


  //  //return $company_id;
  //   $sql = queryGet("SELECT (SELECT MAX(postingDate) FROM erp_credit_note WHERE postingDate < (SELECT postingDate FROM erp_credit_note WHERE credit_note_no = '$credit_note_no' AND `variant_id` = $variant_id AND company_id = $company_id LIMIT 1)) AS previous_date, (SELECT MIN(postingDate) FROM erp_credit_note WHERE postingDate > (SELECT postingDate FROM erp_credit_note WHERE credit_note_no = '$credit_note_no' AND `variant_id` = $variant_id AND company_id = $company_id LIMIT 1)) AS next_date FROM erp_credit_note WHERE credit_note_no = '$credit_note_no' AND `variant_id` = $variant_id AND company_id = $company_id");

  $sql = queryGet("SELECT
(SELECT t1.postingDate
 FROM erp_credit_note t1
 WHERE t1.company_id = $company_id AND t1.cr_note_id < $cr_id AND t1.variant_id = $variant_id
 ORDER BY t1.postingDate DESC
 LIMIT 1) AS prev_date,
(SELECT t2.postingDate
 FROM erp_credit_note t2
 WHERE t2.company_id = $company_id AND t2.cr_note_id > $cr_id AND t2.variant_id = $variant_id
 ORDER BY t2.postingDate ASC
 LIMIT 1) AS next_date;");

  //   console($variant_id);
  //  console($min);
  //  console($max);
  //  console("--------------------------------");
  //  console($sql['data']['prev_date']);
  //  console($sql['data']['next_date']);

  if (is_null($sql['data']['prev_date']) && is_null($sql['data']['next_date'])) {
    // console("Both Dates is null");
    $prev_date = $min;
    $next_date = $max;
  } elseif (is_null($sql['data']['prev_date']) && !is_null($sql['data']['next_date'])) {
    // console("Previous Date is null");
    $nextMonthVariant =  date("m-Y", strtotime($sql['data']['next_date']));
    if ($month_var < $nextMonthVariant) {
      $status = 1;
      $prev_date = $min;
      $next_date = $max;
    }
    if ($month_var == $nextMonthVariant) {
      $status = 1;
      $prev_date = $min;
      $next_date = $sql['data']['next_date'];
    } else {
      $status = 0;
      $prev_date = '';
      $next_date = '';
    }
  } elseif (!is_null($sql['data']['prev_date']) && is_null($sql['data']['next_date'])) {
    // console("Next Date is null");
    $prevMonthVariant = date("m-Y", strtotime($sql['data']['prev_date']));
    if ($month_var > $prevMonthVariant) {
      $status = 1;
      $prev_date = $min;
      $next_date = $max;
    } elseif ($month_var == $prevMonthVariant) {
      $status = 1;
      $prev_date = $sql['data']['prev_date'];
      $next_date = $max;
    } else {
      $status = 0;
      $prev_date = '';
      $next_date = '';
    }
  } else {
    // console("Both Dates is not null");

    $minPrevDate = min(new DateTime($sql['data']['prev_date']), new DateTime($sql['data']['next_date']));
    $maxNextDate = max(new DateTime($sql['data']['prev_date']), new DateTime($sql['data']['next_date']));

    $pre_month_var = $minPrevDate->format('m-Y');
    $next_month_var = $maxNextDate->format('m-Y');

    if ($pre_month_var == $month_var && $next_month_var != $month_var) {
      $status = 1;
      $prev_date = $minPrevDate->format('Y-m-d');
      $next_date = $max;
    } elseif ($pre_month_var != $month_var && $next_month_var == $month_var) {
      $status = 1;
      $prev_date = $min;
      $next_date = $maxNextDate->format('Y-m-d');
    } elseif ($pre_month_var == $month_var && $next_month_var == $month_var) {
      $prev_date = $minPrevDate->format('Y-m-d');
      $next_date = $maxNextDate->format('Y-m-d');
      $status = 1;
    } else {
      $status = 0;
      $prev_date = '';
      $next_date = '';
    }
  }
  // console($prev_date);
  // console($next_date);
  // console("------------------------------>".$status);
  // return $sql; 

  // $prev_date =  getMinDate($sql['data']['prev_date'], $sql['data']['next_date']);
  //  $next_date = getMDate($sql['data']['prev_date'],$sql['data']['next_date']);
  // console($sql);
  //  exit();







  //   if($prev_my != $month_var && $next_my != $month_var){

  //     $start_date = '';
  //     $end_date =  '';
  //     $status = 0;


  //   }
  //   elseif($prev_my == $month_var && $next_my != $month_var){

  //     $start_date = $prev_date;
  //     $end_date = $max;
  //     $status = 1;

  //   }
  //   elseif($prev_my != $month_var && $next_my == $month_var){
  //     $start_date = $min_date;
  //     $end_date = $next_date;
  //     $status = 1;


  //   }
  //   else{

  //     $start_date = $prev_date;
  //     $end_date = $next_date;
  //     $status = 1;

  //   }


  // return $start_date;




  if ($sql['status'] == 'success') {

    $returnData['status'] = 'success';
    $returnData['message'] = 'ok';
    $returnData['start_date'] = $prev_date;
    $returnData['end_date'] = $next_date;
    $returnData['dateStatus'] = $status;
  } else {
    $returnData['status'] = 'warning';
    $returnData['message'] = 'something went wrong';
    $returnData['dateStatus'] = $status;
  }

  return $returnData;
}
