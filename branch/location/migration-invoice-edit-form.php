<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-open-close.php"); //somdutta
require_once("../../app/v1/functions/branch/func-opening-closing-balance-controller.php"); //New controller
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");



if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
$dbObj = new Database();

$inv_id = base64_decode($_GET['inv_id']);


$oneInvSql = "SELECT * FROM `erp_branch_sales_order_invoices` as inv WHERE inv.company_id=$company_id AND inv.branch_id=$branch_id AND inv.location_id=$location_id  AND inv.type = 'migration'   AND inv.status = 'active' AND inv.so_invoice_id=$inv_id";
$oneInvoicedataObj = $dbObj->queryGet($oneInvSql)['data'];

$getCustomerDetailsSql = "SELECT * FROM `erp_customer` WHERE company_id = $company_id AND company_branch_id = $branch_id AND location_id = $location_id AND customer_id = " . $oneInvoicedataObj['customer_id'] . "";
$getCustomerDetailsSqlDataObj = $dbObj->queryGet($getCustomerDetailsSql)['data'];

if ($_POST['act'] == 'invSubmitForm') {
  $cgst = isset($_POST['cgst']) ? floatval($_POST['cgst']) : 0.0;
  $sgst = isset($_POST['sgst']) ? floatval($_POST['sgst']) : 0.0;
  $igst = isset($_POST['igst']) ? floatval($_POST['igst']) : 0.0;
  $totalTaxAmount = isset($_POST['totalTaxAmount']) ? floatval($_POST['totalTaxAmount']) : 0.0;
  $subtotalAmount = isset($_POST['subtotalAmount']) ? floatval($_POST['subtotalAmount']) : 0.0;
  $allTotalAmount = isset($_POST['allTotalAmount']) ? floatval($_POST['allTotalAmount']) : 0.0;
  $dueAmount = isset($_POST['dueAmount']) ? floatval($_POST['dueAmount']) : 0.0;
  $tcsAmount = isset($_POST['tcsAmount']) ? floatval($_POST['tcsAmount']) : 0.0;
  $adjAmt = isset($_POST['adjAmt']) ? floatval($_POST['adjAmt']) : 0.0;

  $updateInvoiceSql = "UPDATE `erp_branch_sales_order_invoices`
                        SET 
                            `cgst` = $cgst,
                            `sgst` = $sgst,
                            `igst` = $igst,
                            `total_tax_amt` = $totalTaxAmount,
                            `sub_total_amt` = $subtotalAmount,
                            `all_total_amt` = $allTotalAmount,
                            `due_amount` = $dueAmount,
                            `tcs_amount` = $tcsAmount,
                            `adjusted_amount` = $adjAmt
                        WHERE `company_id` = $company_id 
                        AND `branch_id` = $branch_id 
                        AND `so_invoice_id` = " . $_POST['invId'] . "
                      ";

  $updateInvData = $dbObj->queryUpdate($updateInvoiceSql);

  if ($updateInvData == 'success') {
    swalToast($updateInvData["status"], $updateInvData["message"],`manage-opening-close-balance.php`);
  } else {
    swalToast($updateInvData["status"], $updateInvData["message"]);
  }
}

?>
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<style>
  /* General Container Styling */
  .is-sales-orders {
    padding: 20px;
  }

  .dotted-border-area {
    border: 2px dotted #9f9e9e;
    padding: 9px;
    border-radius: 7px;
    width: 7cm;
    position: relative;
  }

  #custPhone {
    padding: 10px;
  }

  /* Flexbox for rows */
  .row {
    display: flex;
    gap: 20px;
    justify-content: space-between;
    margin-bottom: 20px;
  }

  /* Column styling for each section */
  .column {
    flex: 1;
    padding: 24px;
    box-sizing: border-box;
    min-width: 0px;

  }

  /* Items Table Styling */
  .items-table,
  .items-view {
    background: #f9f9f9;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
  }

  /* Card body */
  .card-body {
    padding: 15px;
  }

  /* Heading Style */
  h4 {
    font-size: 16px;
    margin-bottom: 10px;
  }

  /* General Label Styling */
  .details label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
  }

  /* Form Container */
  .inv-form-container,
  .inv-inv-form-container {
    margin-top: 30px;
    width: 100%;
  }

  /* Form Group Styling */
  .form-group {
    margin-bottom: 15px;
    display: flex;
    flex-direction: column;
  }

  /* Input Styling */
  /* Main container styling */
  .main-container {
    width: 100%;
    padding: 20px;
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  /* Form container styling */
  .inv-inv-form-container {
    width: 100%;
    max-width: 900px;
    /* You can adjust the max-width */
    padding: 20px;
    box-sizing: border-box;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  h2 {
    text-align: center;
    margin-bottom: 20px;
  }

  /* Form styling */
  form {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: space-between;
  }

  /* Form group styling */
  .form-group {
    flex: 1 1 calc(50% - 20px);
    /* 2 fields per row */
    min-width: 180px;
    box-sizing: border-box;
    margin-bottom: 15px;
  }

  /* Label styling */
  .form-group label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
  }

  /* Input field styling */
  .form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 0.875rem;
  }

  /* Button styling */
  button {
    background-color: rgb(22, 54, 238);
    color: white;
    padding: 14px 5px;
    font-size: 0.75rem;
    border: none;
    border-radius: 9px;
    cursor: pointer;
    width: 106px;
    margin-top: 71px;
    transition: background-color 0.3s ease;
    text-align: center;
    white-space: nowrap;
    position: relative;
    left: 20cm;
  }


  /* Responsive styles for smaller screens */
  @media (max-width: 1200px) {
    .form-group {
      flex: 1 1 calc(50% - 20px);
      /* 2 fields per row on medium screens */
    }
  }

  @media (max-width: 767px) {
    .form-group {
      flex: 1 1 100%;
      /* 1 field per row on smaller screens */
    }
  }

  .right-info {
    display: flex;
    flex-direction: column;
    gap: 38px;
    align-items: flex-end;
  }

  .left-info {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    position: absolute;
  }
</style>

<div class="content-wrapper">

  <?php

  // console($oneInvoicedataObj);
  // console($getCustomerDetailsSqlDataObj);
  ?>
  <div class="row">
    <!-- Left Column - Customer Details, Other Details, and Card Section -->
    <div class="column left-column">
      <h3>Migrated Invoice Data</h3>
      <div class="items-table">
        <h4>Customer Details</h4>
        <div class="customer-details">
          <div class="name-code">
            <div class="details name">
              <p id="custName"><?= $getCustomerDetailsSqlDataObj['trade_name'] ?>(<?= $getCustomerDetailsSqlDataObj['customer_code'] ?>)</p>
            </div>
          </div>
          <div class="details gstin">
            <label>GSTIN</label>
            <p id="custgst"><?= $getCustomerDetailsSqlDataObj['customer_gstin'] ?></p>
          </div>
          <div class="details pan">
            <label>PAN</label>
            <p id="custpan"><?= $getCustomerDetailsSqlDataObj['customer_pan'] ?></p>
          </div>
          <div class="address-contact">
            <div class="address-customer">
              <div class="details">
                <label>Billing Address</label>
                <p id="billAddress" class="pre-normal"><?= $oneInvoicedataObj['customer_billing_address'] ?></p>
              </div>
              <div class="details">
                <label>Shipping Address</label>
                <p class="pre-normal" id="shipAddress"><?= $oneInvoicedataObj['customer_shipping_address'] ?></p>
              </div>
              <div class="details">
                <label>Place of Supply</label>
                <p id="placeofSup"><?= (getStateDetail($oneInvoicedataObj['placeOfSupply'])['data']['gstStateName'])  ?></p>
              </div>
            </div>
            <div class="contact-customer">
              <div class="details dotted-border-area">
                <label>Contacts</label>
                <p><ion-icon name="mail-outline" role="img" class="md hydrated" aria-label="mail outline"></ion-icon><span id="custEmail"><?= $getCustomerDetailsSqlDataObj['customer_authorised_person_email'] ?></span></p>
                <p><ion-icon name="call-outline" role="img" class="md hydrated" aria-label="call outline"></ion-icon><span id="custPhone"><?= $getCustomerDetailsSqlDataObj['customer_authorised_person_phone'] ?></span></p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="items-table">
        <h4>Other Details</h4>
        <div class="other-info">
          <div class="details">
            <label>Invoice Date</label>
            <p id="invDate"><?= $oneInvoicedataObj['invoice_date']  ?></p>
          </div>
          <div class="details">
            <label>Invoice Time</label>
            <p id="invTime"><?= $oneInvoicedataObj['invoice_time']  ?></p>
          </div>
          <div class="details">
            <label>Credit Period</label>
            <p id="creditPeriod"><?= $oneInvoicedataObj['credit_period']  ?></p>
          </div>

          <div class="details">
            <label>Compliance Invoice Type</label>
            <p id="compilaceInv"><?= $oneInvoicedataObj['compInvoiceType']  ?></p>
          </div>

        </div>
      </div>

      <div class="items-view items-calculation" id="item-div-main">
        <div class="card item-cards">
          <div class="card-body">
            <div class="row-section row-first">
              <div class="left-info">
                <ion-icon name="cube-outline" role="img" class="md hydrated" aria-label="cube outline"></ion-icon>
                <div class="item-info">
                  <p class="code" id="cardSoNo"></p>
                  <p class="name" id="cardCustPo"></p>
                </div>
              </div>
              <div class="right-info">
                <div class="item-info">
                  <p class="code" id="totalItem"></p>
                </div>
              </div>
            </div>
            <div class="row-section row-tax">
              <div class="left-info">
                <div class="item-info">
                  <p>Sub Total</p>
                  <p>Total Discount</p>
                  <p>Taxable Amount</p>
                  <p class="tcsAmount">TCS Amount</p>
                  <?php
                  if ($oneInvoicedataObj['igst'] > 0) {
                  ?>
                    <p id="igstP">IGST</p>
                  <?php } else { ?>
                    <div id="csgst">
                      <p>CGST</p>
                      <p>SGST</p>
                    </div>
                  <?php } ?>
                </div>
              </div>
              <div class="right-info">
                <div class="item-info">
                  <p id="sub_total"><?= decimalValuePreview($oneInvoicedataObj['sub_total_amt'])  ?></p>
                  <p id="totalDis"><?= decimalValuePreview($oneInvoicedataObj['totalDiscount'])  ?></p>
                  <p id="taxableAmt"><?= decimalValuePreview($oneInvoicedataObj['sub_total_amt'] - $oneInvoicedataObj['total_tax_amt']) ?></p>
                  <p class="tcsAmount" id="tcsAmt"><?= decimalValuePreview($oneInvoicedataObj['tcs_amount'])  ?></p>
                  <?php
                  if ($oneInvoicedataObj['igst'] > 0) {
                  ?>
                    <p id="igst"><?= decimalValuePreview($oneInvoicedataObj['igst']) ?></p>
                  <?php } else { ?>
                    <div id="csgstVal">
                      <p id="cgstVal"><?= decimalValuePreview($oneInvoicedataObj['cgst']) ?></p>
                      <p id="sgstVal"><?= decimalValuePreview($oneInvoicedataObj['sgst']) ?></p>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
            <hr>
            <div class="row-section row-total-amount">
              <div class="left-info">
                <div class="item-info">
                  <p class="total">Total Amount</p>
                </div>
              </div>
              <div class="right-info">
                <div class="item-info">
                  <p class="amount" id="total_amount"><?= decimalValuePreview($oneInvoicedataObj['all_total_amt'])  ?></p>
                </div>
              </div>
            </div>
            <div class="del_status"></div>
          </div>
          <div class="items-table">
            <div class="details">
              <label>Remarks</label>
              <p id="remark"><?= $oneInvoicedataObj['remarks']  ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column - Invoice Form Section -->
    <div class="column right-column">
      <div class="inv-main-container">
        <div class="inv-inv-form-container">
          <h2>Adjust Invoice</h2>
          <form action="#" method="POST">
            <input type="hidden" name="act" value="invSubmitForm">
            <input type="hidden" name="invId" value="<?= $oneInvoicedataObj['so_invoice_id']  ?>">

            <div class="form-group">
              <label for="invoiceNo">Invoice No:</label>
              <input type="text" id="invoiceNo" name="invoiceNo" value="<?= $oneInvoicedataObj['invoice_no']  ?>" readonly>
            </div>

            <div class="form-group">
              <label for="postingTime">Posting Time:</label>
              <input type="date" id="postingTime" name="postingTime" value="<?= $oneInvoicedataObj['invoice_date']  ?>" readonly>
            </div>

            <div class="form-group">
              <label for="validityPeriod">Validity Period:</label>
              <input type="number" id="validityPeriod" name="validityPeriod" value="<?= $oneInvoicedataObj['credit_period']  ?>" readonly>
            </div>

            <div class="form-group">
              <label for="cgst">CGST:</label>
              <input type="number" id="cgst" name="cgst" step="0.01" value="<?= $oneInvoicedataObj['cgst']  ?>" required>
            </div>

            <div class="form-group">
              <label for="sgst">SGST:</label>
              <input type="number" id="sgst" name="sgst" step="0.01" value="<?= $oneInvoicedataObj['sgst']  ?>" required>
            </div>

            <div class="form-group">
              <label for="igst">IGST:</label>
              <input type="number" id="igst" name="igst" step="0.01" value="<?= $oneInvoicedataObj['igst']  ?>" required>
            </div>

            <div class="form-group">
              <label for="totalTaxAmount">Total Tax Amount:</label>
              <input type="number" id="totalTaxAmount" name="totalTaxAmount" step="0.01" value="<?= $oneInvoicedataObj['total_tax_amt']  ?>" required>
            </div>

            <div class="form-group">
              <label for="subtotalAmount">Subtotal Amount:</label>
              <input type="number" id="subtotalAmount" name="subtotalAmount" step="0.01" value="<?= $oneInvoicedataObj['sub_total_amt']  ?>" required>
            </div>

            <div class="form-group">
              <label for="allTotalAmount">All Total Amount:</label>
              <input type="number" id="allTotalAmount" name="allTotalAmount" step="0.01" value="<?= $oneInvoicedataObj['all_total_amt']  ?>" required>
            </div>
            <div class="form-group">
              <label for="dueAmount">Due Amount:</label>
              <input type="number" id="dueAmount" name="dueAmount" step="0.01" value="<?= $oneInvoicedataObj['due_amount']  ?>" required>
            </div>
            <div class="form-group">
              <label for="tcsAmount">Tcs Amount:</label>
              <input type="number" id="tcsAmount" name="tcsAmount" step="0.01" value="<?= $oneInvoicedataObj['tcs_amount']  ?>" required>
            </div>
            <div class="form-group">
              <label for="adjAmt">Adjust Amount:</label>
              <input type="number" id="adjAmt" name="adjAmt" step="0.01" value="<?= $oneInvoicedataObj['adjusted_amount']  ?>" required>
            </div>

            <button type="submit" id="submitBtn">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require_once("../common/footer.php");
?>

<script>
  $(document).on("change", "#allTotalAmount", function() {

  })
</script>