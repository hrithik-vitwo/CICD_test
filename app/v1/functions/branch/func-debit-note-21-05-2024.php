<?php

require_once("func-journal.php");

function debit_note_add($POST)
{

  $accountingObj = new Accounting();
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
    $debitor_type = "customer";
    $party_id = $party[0];
    $customer_sql = queryGet("SELECT * FROM `erp_customer` WHERE `customer_id` = $party_id");
    $party_code = $customer_sql['data']['customer_code'];
    $party_name = addslashes($customer_sql['data']['trade_name']);
  } else {
    $debitor_type = "vendor";
    $party_id = $party[0];
    $vendor_sql = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_id` = $party_id");
    $party_code = $vendor_sql['data']['vendor_code'];
    $party_name = addslashes($vendor_sql['data']['trade_name']);
  }


  $bill = $POST['bill'];
  if ($bill == 'select invoice') {
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
  $adjustment = $POST['round_sign'] . '' . $POST['round_value'];


  $attachment = $POST['attachment'];
  $name = $attachment["name"];
  $tmpName = $attachment["tmp_name"];
  $size = $attachment["size"];
  $contactDetails=$POST['companyConfigId'];
  

  $taxDetails = [];
  $taxDetails['cgst'] = $POST['cgst'] ?? 0;
  $taxDetails['sgst'] = $POST['sgst'] ?? 0;
  $taxDetails['igst'] = $POST['igst'] ?? 0;

  $cgst = $POST['cgst'] ?? 0;
  $sgst = $POST['sgst'] ?? 0;
  $igst = $POST['igst'] ?? 0;


  $allowed_types = ['jpg', 'png', 'jpeg', 'pdf'];
  $maxsize = 2 * 1024 * 1024; // 10 MB


  $fileUploaded = uploadFile(["name" => $name, "tmp_name" => $tmpName, "size" => $size], COMP_STORAGE_DIR . "/others/", $allowed_types, $maxsize, 0);
  // console($fileUploaded);
  $attachment_name = $fileUploaded['data'];

  // exit();



  $insert_debit = queryInsert("INSERT INTO  `erp_debit_note`
                      SET
                      `company_id`=$company_id,
                      `branch_id`=$branch_id,
                      `location_id`=$location_id,
                      `debitor_type`='" . $debitor_type . "',
                      `party_id`=$party_id,
                      `party_code`='" . $party_code . "',
                      `party_name`='" . $party_name . "',
                      `debit_note_no`= '',
                      `debitNoteReference` = '" . $parent_id . "',
                      `postingDate` = '" . $posting_date . "',
                      `remark` = '" . $remark . "',
                      `source_address` = '" . $source_address . "',
                      `destination_address` = '" . $destination_address . "',
                      `billing_address` = '" . $bill_address . "',
                      `shipping_address` = '" . $ship_address . "',
                      `contact_details`='".$contactDetails."',
                      `total` = '" . $subtotal . "',
                      `adjustment` = '" . $adjustment . "',
                      `attachment` = '" . $attachment_name . "',
                      `reasons` = '".$reasons."',
                      `status` = 'active',
                      `cgst`='" . $cgst . "',
                      `sgst`='" . $sgst . "',
                      `igst`='" . $igst . "',
                      `created_by` = '" . $created_by . "',
                      `updated_by` ='" . $created_by . "'
                      ");


  if ($insert_debit['status'] == "success") {

    $debit_note_id = $insert_debit['insertedId'] ?? 0;

    $IvNoByVerientresponse = getDNNumberByVerient($POST['iv_varient']);
    $debit_note_no = $IvNoByVerientresponse['iv_number'];
    $invoice_no_serialized = $IvNoByVerientresponse['iv_number_array'];

    $updateInv = "UPDATE `erp_debit_note` 
                    SET 
                    `debit_note_no`='$debit_note_no',
                    `debit_note_no_serialized`='$invoice_no_serialized'
                 WHERE dr_note_id='$debit_note_id'";
    queryUpdate($updateInv);

    $debit_items = $POST['item'];
    $items = [];
    foreach ($debit_items as $key => $item) {
      // $item_code = $item['item_code'];
      $qty = $item['qty'];
      // echo "ok";
      $rate = $item['rate'];
      // echo "ok";
      $tax = $item['tax'];
      $withouttax = $qty * $rate;
      //  echo "ok";
      $tax_amount = ($tax / 100) * ($qty * $rate);
      $itemigst=0;
      $itemcgst=0;
      $itemsgst=0;
      if($igst>0){
        $itemigst=$tax_amount;
      }else{
        $itemcgst=$tax_amount/2;
        $itemsgst=$tax_amount/2;
      }
      //  echo "ok";
      $amount = ($qty * $rate) + ($tax_amount);
      // $amount = $item['amount'];
      $itemArrys = array();
      $itemArrys = explode('|', $item['item_id']);

      $item_id = 0;
      $subgl_code = '';
      $subgl_name = '';
      $goodsType = '';

      if (count($itemArrys) > 1) {
        $item_id = $itemArrys[0] ?? 0;
        $subgl_code = $itemArrys[1] ?? 0;
        $subgl_name = $itemArrys[2] ?? 0;
        $goodsType = $itemArrys[3] ?? 0;
      }
      $account = $item['account'] ?? 0;
      $items[$key]['accountGl'] = $account;
      $items[$key]['goodsType'] = $goodsType;
      $items[$key]['subgl_code'] = $subgl_code;
      $items[$key]['subgl_name'] = $subgl_name;
      $items[$key]['withouttax'] = $withouttax;
      $items[$key]['tax'] = $tax_amount;



      $insert_debit_items = queryInsert("INSERT INTO  `debit_note_item`
                                                        SET
                                                        `account` = '" . $account . "',
                                                        `item_id`=$item_id,
                                                        `invoice_id` = $parent_id,
                                                        `debit_note_id`= $debit_note_id,
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
      if ($insert_debit_items['status'] == "success") {
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
            $uom = '';
            if ($POST['select_customer_vendor'] == 'Customer') {
              $qtyyy = $usedQuantity * -1;
            } else {
              $qtyyy = $usedQuantity;
            }

            $insStockSummary1 = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                      SET 
                          companyId = '" . $company_id . "',
                          branchId = '" . $branch_id . "',
                          locationId = '" . $location_id . "',
                          storageLocationId = '" . $storage_location_id . "',
                          storageType ='" . $storageLocationTypeSlug . "',
                          itemId = '" . $item_id . "',
                          itemQty = '" . $qtyyy . "',
                          itemUom = '" . $uom . "',
                          itemPrice = '" . $rate . "',
                          refActivityName='DN',
                          logRef = '" . $logRef . "',
                          refNumber='" . $debit_note_no . "',
                          bornDate='" . $bornDate . "',
                          postingDate='" . $posting_date . "',
                          createdBy = '" . $created_by . "',
                          updatedBy = '" . $created_by . "'";

            $insStockreturn1 = queryInsert($insStockSummary1);
            // console($insStockreturn1);

            $returnData['insStockreturn1'][] = $insStockreturn1;
            $returnData['insStockreturn2'][] = $selStockLog;
          }
        }

        $returnData['status'] = "Success";
        $returnData['message'] = "Debit Note Created Successfully";
      } else {
        $returnData['status'] = "Warning";
        $returnData['message'] = "Something Went Wrong";
      }
    }


    /************************************Accounting Start ****************************************/

    $remarks = "Debit Note for " . $reasons . " " . $parent_id_code . " " . $remark;

    $accslug = $POST['select_customer_vendor'] . "DN"; // CN/DN

    $roundOffGL = 0;

    $symbol = $_POST["round_sign"];
    $roundOffValue = $_POST["round_value"];
    if ($symbol == "add") {
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
      "documentNo" => $debit_note_no,
      "documentDate" => $posting_date,
      "invoicePostingDate" => $posting_date,
      "referenceNo" => $parent_id_code,
      "type" => 'DN',
      "for" => $POST['select_customer_vendor'],
      "journalEntryReference" => 'DN',
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
      $accPostingObj = $accountingObj->dNoteForVendorAccountingPosting($postingAccountingData, $accslug, $debit_note_id);
    } else {

      $accPostingObj = $accountingObj->dNoteForCustomerAccountingPosting($postingAccountingData, $accslug, $debit_note_id);
    }

    if ($accPostingObj["status"] == "success" && $accPostingObj["journalId"] != "") {
      $queryObj = queryUpdate('UPDATE `erp_debit_note` SET `journal_id`=' . $accPostingObj["journalId"] . ' WHERE `dr_note_id`=' . $debit_note_id);



      $returnData['status'] = "success";
      $returnData['message'] = "Debit note saved successfully";
      $returnData['debit_note_no'] = $debit_note_no;
      $returnData['postingAccountingData'] = $postingAccountingData;
      $returnData['acc'] = $accPostingObj;
    } else {

      $returnData['status'] = "success";
      $returnData['message'] = "Debit note saved successfully with out accounting!";
      $returnData['debit_note_no'] = $debit_note_no;
      $returnData['postingAccountingData'] = $postingAccountingData;
      $returnData['acc'] = $accPostingObj;
    }


    /************************************Accounting End ****************************************/
  } else {
    $returnData['status'] = "Warning";
    $returnData['message'] = "Something Went Wrong";
  }

  return $returnData;
}
