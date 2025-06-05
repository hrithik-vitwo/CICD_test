<?php

function getAllSoNumber(){
    global $company_id;
    global $branch_id;
    global $location_id;

    $sql = "SELECT invoice_no AS `code`, 'invoice' as parent_slug, so_invoice_id AS `parent_id` FROM `".ERP_BRANCH_SALES_ORDER_INVOICES."` WHERE company_id='$company_id' AND branch_id='$branch_id' AND location_id='$location_id'";
    
    $returnData = queryGet($sql, true);
        return $returnData;
}

function getAllPoNumber(){
    global $company_id;
    global $branch_id;
    global $location_id;

    $sql = "SELECT grnCode AS `code`, 'grn' as parent_slug, grnId AS `parent_id` FROM `".ERP_GRN."` WHERE companyId='$company_id' AND branchId='$branch_id' AND locationId='$location_id'";
    
    $returnData = queryGet($sql, true);
        return $returnData;
}

//*************************************/debit notes insert/******************************************//
function createDebitNote($POST = [])
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
    $debit_note_no = 0;
    $sqlPrv = "SELECT debit_note_no FROM `" . ERP_DEBIT_NOTES . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
    $responc = queryGet($sqlPrv);
    if ($responc['status'] == 'success') {
      $redata = $responc['data']['debit_note_no'];
      $debit_note_no = getDebitNoteNewCode($redata);
    } else {
      $redata = '';
      $debit_note_no = getDebitNoteNewCode($redata);
    }

    $refarenceCode = explode('|',$POST['refarenceCode']);
    
    $journalins = "INSERT INTO `" . ERP_DEBIT_NOTES . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `refarenceCode`='" . $refarenceCode[0] . "',
                  `parent_id`='" . $refarenceCode[1] . "',
                  `parent_slug`='" . $refarenceCode[2] . "',
                  `remark`='" . $POST['remark'] . "',
                  `debit_note_no`='" . $debit_note_no . "',
                  `debitNoteReference`='" . $POST['debitNoteReference'] . "',
                  `documentNo`='" . $POST['documentNo'] . "',
                  `documentDate`='" . $POST['documentDate'] . "',
                  `postingDate`='" . $POST['postingDate'] . "',
                  `created_by`='" . $created_by . "',
                  `updated_by`='" . $created_by . "'";

    $rtn = queryInsert($journalins);
    if ($rtn['status'] == 'success') {
      $debit_note_id = $rtn['insertedId'];
      $drGL = $_POST['journal']['debit']['gl'];
      $drAmt = $_POST['journal']['debit']['amount'];
      $crGL = $_POST['journal']['credit']['gl'];
      $crAmt = $_POST['journal']['credit']['amount'];
      foreach ($drGL  as $drkey => $drvalue) {
        $ins = "INSERT INTO `" . ERP_DEBIT_NOTE_DEBIT . "` 
                    SET
                        `debit_note_id`='" . $debit_note_id . "',
                        `glId`='" . $drGL[$drkey] . "',
                        `debit_amount`='" . $drAmt[$drkey] . "',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $created_by . "'";

        $rtn = queryInsert($ins);
      }

      foreach ($crGL  as $crkey => $crvalue) {
        $ins = "INSERT INTO `" . ERP_DEBIT_NOTE_CREDIT . "` 
                    SET
                        `debit_note_id`='" . $debit_note_id . "',
                        `glId`='" . $crGL[$crkey] . "',
                        `credit_amount`='" . $crAmt[$crkey] . "',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $created_by . "'";

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

//*************************************/credit notes insert/******************************************//
function createCreditNote($POST = [])
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
    $credit_note_no = 0;
    $sqlPrv = "SELECT credit_note_no FROM `" . ERP_CREDIT_NOTES . "` WHERE company_id=$company_id AND branch_id=$branch_id and location_id=$location_id order by id desc limit 1";
    $responc = queryGet($sqlPrv);
    if ($responc['status'] == 'success') {
      $redata = $responc['data']['credit_note_no'];
      $credit_note_no = getCreditNoteNewCode($redata);
    } else {
      $redata = '';
      $credit_note_no = getCreditNoteNewCode($redata);
    }

    $refarenceCode = explode('|',$POST['refarenceCode']);
    
    $journalins = "INSERT INTO `" . ERP_CREDIT_NOTES . "` 
              SET
                  `company_id`='" . $company_id . "',
                  `branch_id`='" . $branch_id . "',
                  `location_id`='" . $location_id . "',
                  `refarenceCode`='" . $refarenceCode[0] . "',
                  `parent_id`='" . $refarenceCode[1] . "',
                  `parent_slug`='" . $refarenceCode[2] . "',
                  `remark`='" . $POST['remark'] . "',
                  `credit_note_no`='" . $credit_note_no . "',
                  `creditNoteReference`='" . $POST['creditNoteReference'] . "',
                  `documentNo`='" . $POST['documentNo'] . "',
                  `documentDate`='" . $POST['documentDate'] . "',
                  `postingDate`='" . $POST['postingDate'] . "',
                  `created_by`='" . $created_by . "',
                  `updated_by`='" . $created_by . "'";

    $rtn = queryInsert($journalins);
    if ($rtn['status'] == 'success') {
      $credit_note_id = $rtn['insertedId'];
      $drGL = $_POST['journal']['debit']['gl'];
      $drAmt = $_POST['journal']['debit']['amount'];
      $crGL = $_POST['journal']['credit']['gl'];
      $crAmt = $_POST['journal']['credit']['amount'];
      foreach ($drGL  as $drkey => $drvalue) {
        $ins = "INSERT INTO `" . ERP_CREDIT_NOTE_DEBIT . "` 
                    SET
                        `credit_note_id`='" . $credit_note_id . "',
                        `glId`='" . $drGL[$drkey] . "',
                        `debit_amount`='" . $drAmt[$drkey] . "',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $created_by . "'";

        $rtn = queryInsert($ins);
      }

      foreach ($crGL  as $crkey => $crvalue) {
        $ins = "INSERT INTO `" . ERP_CREDIT_NOTE_CREDIT . "` 
                    SET
                        `credit_note_id`='" . $credit_note_id . "',
                        `glId`='" . $crGL[$crkey] . "',
                        `credit_amount`='" . $crAmt[$crkey] . "',
                        `created_by`='" . $created_by . "',
                        `updated_by`='" . $created_by . "'";

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

?>