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
  private $dbObj;
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
    $this->dbObj = new Database();
  }

  function getBankStatements()
  {

    $condition = "";
    if ($this->bankId > 0) {
      $condition .= ' AND s.bank_id=' . $this->bankId;
    }
    if ($this->tnxType == "unrecognised") {
      // $condition.=' AND s.reconciled_status="pending" AND s.remaining_amt > 0';
      $condition .= ' AND s.reconciled_status="pending" AND s.remaining_amt >= 0';
    }
    if ($this->tnxType == "recognised") {
      $condition .= ' AND s.reconciled_status="reconciled" AND s.reconciled_location_id=' . $this->location_id;
    }

    $dataObj = queryGet('SELECT s.*, b.bank_name, b.account_no FROM `erp_bank_statements` AS s LEFT JOIN `erp_acc_bank_cash_accounts` AS b ON s.bank_id=b.id WHERE s.company_id=' . $this->company_id . ' ' . $condition . ' ORDER BY s.id DESC', true);

    $grandSumObj = queryGet('SELECT SUM(CASE WHEN reconciled_status = "pending" THEN withdrawal_amt + deposit_amt ELSE 0 END) AS unrecognizedAmount, SUM(CASE WHEN reconciled_status = "reconciled" THEN withdrawal_amt + deposit_amt ELSE 0 END) AS recognizedAmount, MAX(tnx_date) AS lastFeedDate FROM erp_bank_statements AS s WHERE s.company_id=' . $this->company_id . ' ' . $condition);

    // console($grandSumObj);

    $dataObj["recognisedAmount"] = $grandSumObj["data"]["recognizedAmount"];
    $dataObj["unrecognisedAmount"] = $grandSumObj["data"]["unrecognizedAmount"];
    $dataObj["lastFeedDate"] = $grandSumObj["data"]["lastFeedDate"] != "" ? $grandSumObj["data"]["lastFeedDate"] : "YYYY-MM-DD";
    $dataObj["totalAmount"] = $dataObj["recognisedAmount"] + $dataObj["unrecognisedAmount"];
    return $dataObj;
  }

  function getVendorList()
  {
    return queryGet('SELECT `vendor_id`, `vendor_code`, `trade_name` AS vendor_name FROM `erp_vendor_details` WHERE `location_id`=' . $this->location_id . ' AND `company_id`=' . $this->company_id, true);
  }
  function getCustomerList()
  {
    return queryGet('SELECT `customer_id`, `customer_code`, `trade_name` AS customer_name FROM `erp_customer` WHERE `location_id`=' . $this->location_id . ' AND `company_id`=' . $this->company_id, true);
  }
  function getBankList()
  {
    return queryGet('SELECT * FROM `erp_acc_bank_cash_accounts` WHERE `company_id`=' . $this->company_id . ' AND `type_of_account`="bank" AND `status`="active"', true);
  }




  function getUncategorizedCount($id)
  {
    return queryGet('SELECT * FROM `erp_bank_statements` WHERE `company_id`=' . $this->company_id . ' AND `bank_id`="' . $id . '" AND `status`="active" AND `reconciled_status`="pending" AND `remaining_amt` >= 0', true);
  }
  function getBankAmount($id)
  {
    return queryGet('SELECT * FROM `erp_bank_statements` WHERE `company_id`=' . $this->company_id . ' AND `bank_id`="' . $id . '" AND `status`="active" ORDER BY `id` DESC LIMIT 1', false);
  }
  function getBooksAmount($gl, $acc_code)
  {
    $credit_amount = queryGet('SELECT SUM(credit.credit_amount) AS credit_amount FROM `erp_acc_credit` AS credit LEFT JOIN `erp_acc_journal` AS journal ON journal.id = credit.journal_id  WHERE journal.`company_id`=' . $this->company_id . ' AND journal.`branch_id`="' . $this->branch_id . '" AND journal.`location_id`="' . $this->location_id . '" AND journal.`journal_status`="active" AND credit.`glId`= "' . $gl . '" AND credit.`subGlCode`= "' . $acc_code . '"');
    $debit_amount = queryGet('SELECT SUM(debit.debit_amount) AS debit_amount FROM `erp_acc_debit` AS debit LEFT JOIN `erp_acc_journal` AS journal ON journal.id = debit.journal_id  WHERE journal.`company_id`=' . $this->company_id . ' AND journal.`branch_id`="' . $this->branch_id . '" AND journal.`location_id`="' . $this->location_id . '" AND journal.`journal_status`="active" AND debit.`glId`= "' . $gl . '" AND debit.`subGlCode`= "' . $acc_code . '"');

    $amount = $debit_amount["data"]["debit_amount"] - $credit_amount["data"]["credit_amount"];
    return $amount;
  }

  function getUncategorizedCountDateFilter($id, $from_date, $to_date)
  {
    // $from_date = date("Y-m-d",$from_date);
    return queryGet('SELECT * FROM `erp_bank_statements` WHERE `company_id`=' . $this->company_id . ' AND `bank_id`="' . $id . '" AND `status`="active" AND `reconciled_status`="pending" AND tnx_date BETWEEN "' . $from_date . '" AND "' . $to_date . '"', true);
  }
  function getBankAmountDateFilter($id, $from_date, $to_date)
  {
    // $from_date = date("Y-m-d",$from_date);
    return queryGet('SELECT * FROM `erp_bank_statements` WHERE `company_id`=' . $this->company_id . ' AND `bank_id`="' . $id . '" AND `status`="active" AND tnx_date BETWEEN "' . $from_date . '" AND "' . $to_date . '" ORDER BY `id` DESC LIMIT 1', false);
  }
  function getBooksAmountDateFilter($gl, $acc_code, $from_date, $to_date)
  {
    // $from_date = date("Y-m-d",$from_date);
    $credit_amount = queryGet('SELECT SUM(credit.credit_amount) AS credit_amount FROM `erp_acc_credit` AS credit LEFT JOIN `erp_acc_journal` AS journal ON journal.id = credit.journal_id  WHERE journal.`company_id`=' . $this->company_id . ' AND journal.`branch_id`="' . $this->branch_id . '" AND journal.`location_id`="' . $this->location_id . '" AND journal.`journal_status`="active" AND credit.`glId`= "' . $gl . '" AND credit.`subGlCode`= "' . $acc_code . '" AND credit.`credit_created_at` BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
    $debit_amount = queryGet('SELECT SUM(debit.debit_amount) AS debit_amount FROM `erp_acc_debit` AS debit LEFT JOIN `erp_acc_journal` AS journal ON journal.id = debit.journal_id  WHERE journal.`company_id`=' . $this->company_id . ' AND journal.`branch_id`="' . $this->branch_id . '" AND journal.`location_id`="' . $this->location_id . '" AND journal.`journal_status`="active" AND debit.`glId`= "' . $gl . '" AND debit.`subGlCode`= "' . $acc_code . '" AND debit.`debit_created_at` BETWEEN "' . $from_date . '" AND "' . $to_date . '"');

    $amount = $debit_amount["data"]["debit_amount"] - $credit_amount["data"]["credit_amount"];
    return $amount;
  }

  function match_transaction_customer()
  {
    return queryGet('SELECT * FROM `erp_branch_sales_order_invoices` AS s LEFT JOIN `erp_status_master` AS status_master ON status_master.`code` = s.`invoiceStatus` LEFT JOIN `erp_customer` AS customer ON customer.`customer_id` = s.`customer_id` WHERE s.`company_id`=' . $this->company_id . ' AND s.`branch_id`=' . $this->branch_id . ' AND s.`location_id`=' . $this->location_id . ' AND s.`invoiceStatus`!=4 AND s.`due_amount` > 0', true);
  }

  function match_transaction_vendor($tnxAmount)
  {
    return queryGet('SELECT
    *
    FROM
        `erp_grninvoice` AS grniv
    LEFT JOIN `erp_status_master` AS status_master
    ON
        status_master.`code` = grniv.`paymentStatus`
    LEFT JOIN `erp_vendor_details` AS vendor
    ON
        vendor.`vendor_id` = grniv.`vendorId`
    WHERE
        grniv.`companyId` = ' . $this->company_id . ' AND grniv.`branchId` = ' . $this->branch_id . ' AND grniv.`locationId` = ' . $this->location_id . ' AND grniv.`paymentStatus` != 4 AND grniv.`dueAmt` > 0', true);
  }

  function getVendorPartyName($utr)
  {
    return queryGet('SELECT * FROM `erp_grn_payments` AS payments LEFT JOIN `erp_vendor_details` AS vendor ON vendor.`vendor_id` = payments.`vendor_id` WHERE payments.`company_id`=' . $this->company_id . ' AND payments.`branch_id`=' . $this->branch_id . ' AND payments.`location_id`=' . $this->location_id . ' AND payments.`transactionId` = "' . $utr . '"');
  }

  function getCustomerPartyName($utr)
  {
    return queryGet('SELECT * FROM `erp_branch_sales_order_payments` AS payments LEFT JOIN `erp_customer` AS customer ON customer.`customer_id` = payments.`customer_id` WHERE payments.`company_id`=' . $this->company_id . ' AND payments.`branch_id`=' . $this->branch_id . ' AND payments.`location_id`=' . $this->location_id . ' AND payments.`transactionId` = "' . $utr . '"');
  }

  function getBankAmountgraph($from_date, $to_date)
  {
    return queryGet('SELECT SUM(balance_amt) AS amount FROM `erp_bank_statements` WHERE `company_id`=' . $this->company_id . ' AND  `status`="active" AND tnx_date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
  }
  function getBooksAmountgraph($from_date, $to_date)
  {
    $credit_amount = queryGet('SELECT SUM(credit.credit_amount) AS credit_amount FROM `erp_acc_credit` AS credit LEFT JOIN `erp_acc_journal` AS journal ON journal.id = credit.journal_id  WHERE journal.`company_id`=' . $this->company_id . ' AND journal.`branch_id`="' . $this->branch_id . '" AND journal.`location_id`="' . $this->location_id . '" AND journal.`journal_status`="active" AND credit.`credit_created_at` BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
    $debit_amount = queryGet('SELECT SUM(debit.debit_amount) AS debit_amount FROM `erp_acc_debit` AS debit LEFT JOIN `erp_acc_journal` AS journal ON journal.id = debit.journal_id  WHERE journal.`company_id`=' . $this->company_id . ' AND journal.`branch_id`="' . $this->branch_id . '" AND journal.`location_id`="' . $this->location_id . '" AND journal.`journal_status`="active" AND debit.`debit_created_at` BETWEEN "' . $from_date . '" AND "' . $to_date . '"');

    $amount = $debit_amount["data"]["debit_amount"] - $credit_amount["data"]["credit_amount"];
    return $amount;
  }


  function getBankAmountgraphEach($bank_id, $from_date, $to_date)
  {
    return queryGet('SELECT SUM(balance_amt) AS amount FROM `erp_bank_statements` WHERE `company_id`=' . $this->company_id . ' AND `bank_id`="' . $bank_id . '" AND  `status`="active" AND tnx_date BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
  }
  function getBooksAmountgraphEach($pgl, $subgl, $from_date, $to_date)
  {
    $credit_amount = queryGet('SELECT SUM(credit.credit_amount) AS credit_amount FROM `erp_acc_credit` AS credit LEFT JOIN `erp_acc_journal` AS journal ON journal.id = credit.journal_id  WHERE journal.`company_id`=' . $this->company_id . ' AND journal.`branch_id`="' . $this->branch_id . '" AND journal.`location_id`="' . $this->location_id . '" AND journal.`journal_status`="active" AND credit.`glId`= "' . $pgl . '" AND credit.`subGlCode`= "' . $subgl . '" AND credit.`credit_created_at` BETWEEN "' . $from_date . '" AND "' . $to_date . '"');
    $debit_amount = queryGet('SELECT SUM(debit.debit_amount) AS debit_amount FROM `erp_acc_debit` AS debit LEFT JOIN `erp_acc_journal` AS journal ON journal.id = debit.journal_id  WHERE journal.`company_id`=' . $this->company_id . ' AND journal.`branch_id`="' . $this->branch_id . '" AND journal.`location_id`="' . $this->location_id . '" AND journal.`journal_status`="active" AND debit.`glId`= "' . $pgl . '" AND debit.`subGlCode`= "' . $subgl . '" AND debit.`debit_created_at` BETWEEN "' . $from_date . '" AND "' . $to_date . '"');

    $amount = $debit_amount["data"]["debit_amount"] - $credit_amount["data"]["credit_amount"];
    return $amount;
  }

  // Match Transaction function for customer 
  function matchTransasctionCustomer($transId, $bankId, $depositAmt)
  {
    $response = [];
    $tranactionId = str_replace(' ', '', $transId);

    //  Transaction ID into sales order payment table
    $sql = "SELECT * FROM `erp_branch_sales_order_payments` AS sp WHERE sp.company_id = '" . $this->company_id . "'  AND sp.branch_id = '" . $this->branch_id . "' AND sp.location_id = '" . $this->location_id . "' AND sp.bank_id = '" . $bankId . "' AND sp.transactionId ='" . $tranactionId . "' ";
    $res = $this->dbObj->queryGet($sql, true);
    // if Found
    if ($res['status'] == "success") {
      $data = $res['data'];
      $collectionAmt = $data['collect_payment'];

      // check if collection  Amount is same as deposit amount
      if ($depositAmt == $collectionAmt) {
        $remainingAmt = $depositAmt - $collectionAmt;
        $response = [
          "status" => "success",
          "flag" => "recon",
          "message" => "Transaction Found Successfully : Id:" . $tranactionId . " AND Collection Amount is same as Deposit Amount",
          "remainingAmt" => $remainingAmt,
          "reconStatus" => "reconciled",
          "transactionId" => $tranactionId
        ];
      }

      // check if collection  Amount is less than deposit amount
      if ($depositAmt > $collectionAmt) {
        $remainingAmt = $depositAmt - $collectionAmt;
        $response = [
          "status" => "success",
          "flag" => "pending",
          "message" => "Transaction Found Successfully : Id:" . $tranactionId . " AND Collection Amount is less than Deposit Amount",
          "remainingAmt" => $remainingAmt,
          "reconStatus" => "pending",
          "transactionId" => $tranactionId
        ];
      }

      // check if collection Amount is greater than Deposit Amount
      if ($depositAmt < $collectionAmt) {
        $remainingAmt = $depositAmt - $collectionAmt;
        $response = [
          "status" => "success",
          "flag" => "greater",
          "message" => "Transaction Found Successfully : Id:" . $tranactionId . " AND Collection Amount is greater than Deposit Amount",
          "remainingAmt" => 0,
          "reconStatus" => "reconciled",
          "transactionId" => $tranactionId
        ];
      }
    } else {
      $response = [
        "status" => "error", 
        "message" => "Transaction Not Found : Id:" . $tranactionId,
        "remainingAmt" => $depositAmt,
        "transactionId" => $tranactionId,
        "reconStatus" => "pending",
        "sql" => $res['query']
      ];
    }

    return $response;
  }

  // Match Transaction Function for vendor payment
  function matchTransasctionVendor($transId, $bankId, $withdrawalAmt)
  {
    $response = [];
    $tranactionId = str_replace(' ', '', $transId);

    //  Transaction ID into vendor payment table
    $sql = "SELECT * FROM `erp_grn_payments` as payment WHERE payment.company_id=$this->company_id AND payment.branch_id=$this->branch_id AND payment.location_id=$this->location_id AND payment.bank_id=$bankId AND payment.transactionId='" . $tranactionId . "'";

    $res = $this->dbObj->queryGet($sql, true);
    // if Found
    if ($res['status'] == "success") {
      $data = $res['data'];
      $paymentAmt = $data['collect_payment'];

      // check if payment  Amount is same as deposit amount
      if ($withdrawalAmt == $paymentAmt) {
        $remainingAmt = $withdrawalAmt - $paymentAmt;
        $response = [
          "status" => "success",
          "flag" => "recon",
          "message" => "Transaction Found Successfully : Id:" . $tranactionId . " AND Payment Amount is same as withdrawl Amount",
          "remainingAmt" => $remainingAmt,
          "reconStatus" => "reconciled",
          "transactionId" => $tranactionId
        ];
      }

      // check if Payment  Amount is less than withdrawl amount
      if ($withdrawalAmt > $paymentAmt) {
        $remainingAmt = $withdrawalAmt - $paymentAmt;
        $response = [
          "status" => "success",
          "flag" => "less",
          "message" => "Transaction Found Successfully : Id:" . $tranactionId . " AND Payment Amount is less than withdrawl Amount",
          "remainingAmt" => $remainingAmt,
          "reconStatus" => "pending",
          "transactionId" => $tranactionId
        ];
      }

      // check if Payment Amount is greater than withdrawl Amount
      if ($withdrawalAmt < $paymentAmt) {
        $remainingAmt = $withdrawalAmt - $paymentAmt;
        $response = [
          "status" => "success",
          "flag" => "greater",
          "message" => "Transaction Found Successfully : Id:" . $tranactionId . " AND Payment Amount is greater than withdrawl Amount",
          "remainingAmt" => 0,
          "reconStatus" => "reconciled",
          "transactionId" => $tranactionId
        ];
      }
    } else {
      $response = [
        "status" => "error",
        "message" => "Transaction Not Found : Id:" . $tranactionId,
        "transactionId" => $tranactionId,
        "remainingAmt" => $withdrawalAmt,
        "reconStatus" => "pending",
        "sql" => $res['query']
      ];
    }

    return $response;
  }
  
}
