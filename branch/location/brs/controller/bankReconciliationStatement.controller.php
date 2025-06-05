<?php
class BankReconciliationStatement
{
  private $company_id;
  private $branch_id;
  private $location_id;
  private $created_by;
  private $updated_by;

  private $bankId;
  private $tnxType;
  function __construct($bankId = 0, $tnxType = "all")
  {
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    $this->company_id = $company_id;
    $this->branch_id = $branch_id;
    $this->location_id = $location_id;
    $this->created_by = $created_by;
    $this->updated_by = $updated_by;
    $this->bankId = $bankId;
    $this->tnxType = $tnxType;
  }

  function getBankStatements()
  {

    $condition = "";
    if($this->bankId>0){
      $condition.=' AND s.bank_id='.$this->bankId;
    }
    if($this->tnxType=="unrecognised"){
      $condition.=' AND s.reconciled_status="pending"';
    }
    if($this->tnxType=="recognised"){
      $condition.=' AND s.reconciled_status="reconciled" AND s.reconciled_location_id=' . $this->location_id;
    }

    $dataObj = queryGet('SELECT s.*, b.bank_name, b.account_no FROM `erp_bank_statements` AS s LEFT JOIN `erp_acc_bank_cash_accounts` AS b ON s.bank_id=b.id WHERE s.company_id=' . $this->company_id . ' '.$condition.' ORDER BY s.id DESC LIMIT 10', true);

    $grandSumObj = queryGet('SELECT SUM(CASE WHEN reconciled_status = "pending" THEN withdrawal_amt + deposit_amt ELSE 0 END) AS unrecognizedAmount, SUM(CASE WHEN reconciled_status = "reconciled" THEN withdrawal_amt + deposit_amt ELSE 0 END) AS recognizedAmount, MAX(tnx_date) AS lastFeedDate FROM erp_bank_statements AS s WHERE s.company_id=' . $this->company_id . ' '.$condition);

    // console($grandSumObj);

    $dataObj["recognisedAmount"] = $grandSumObj["data"]["recognizedAmount"];
    $dataObj["unrecognisedAmount"] = $grandSumObj["data"]["unrecognizedAmount"];
    $dataObj["lastFeedDate"] = $grandSumObj["data"]["lastFeedDate"]!=""?$grandSumObj["data"]["lastFeedDate"]:"YYYY-MM-DD";
    $dataObj["totalAmount"] = $dataObj["recognisedAmount"]+$dataObj["unrecognisedAmount"];
    return $dataObj;
  }

  function getVendorList(){
    return queryGet('SELECT `vendor_id`, `vendor_code`, `trade_name` AS vendor_name FROM `erp_vendor_details` WHERE `location_id`='.$this->location_id.' AND `company_id`='.$this->company_id, true);
  }
  function getCustomerList(){
    return queryGet('SELECT `customer_id`, `customer_code`, `trade_name` AS customer_name FROM `erp_customer` WHERE `location_id`='.$this->location_id.' AND `company_id`='.$this->company_id, true);
  }
  function getBankList()
  {
    return queryGet('SELECT * FROM `erp_acc_bank_cash_accounts` WHERE `company_id`=' . $this->company_id . ' AND `type_of_account`="bank" AND `status`="active"', true);
  }
}


?>