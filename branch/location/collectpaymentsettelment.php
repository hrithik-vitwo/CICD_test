<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");

require_once("../../app/v1/functions/branch/func-vendors-controller.php");


if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// add PGI form ➕➕➕➕➕➕➕➕➕➕➕➕➕
$BranchSoObj = new BranchSo();
$vendorObj = new VendorController();
// compnay currrency data
$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];
$currency_name = $companyCurrencyData['currency_name'];

$check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];
$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];

// imranali59059💰💰💰💰💰💰💰💰💰💰💰💰💰
if (isset($_POST['submitCollectPaymentBtn'])) {

  $addCollectPayment = $BranchSoObj->insertCollectPayment($_POST, $_FILES);

  if ($addCollectPayment['status'] == "success") {
    swalToast($addCollectPayment["status"], $addCollectPayment["message"], LOCATION_URL . "collect-payment.php");
  } else {
    swalToast($addCollectPayment["status"], $addCollectPayment["message"]);
  }
}

// ✅✅✅✅✅✅✅✅✅✅✅✅✅
$customerList = $BranchSoObj->fetchCustomerList()['data'];
$vendorList = $vendorObj->getAllDataVendor()['data'];
$fetchInvoiceByCustomer = $BranchSoObj->fetchBranchSoInvoiceBycustomerId(1)['data'];

// 🏁🍉🏁🍉🏁🍉🏁🍉🏁🍉🏁🍉🏁🍉🏁🍉🏁🍉🏁🍉🏁🍉🏁🍉🏁🍉🏁🍉🏁🍉
if (isset($_GET['customerId'])) {
  $customerId = $_GET['customerId'];
  $customerDetails = $BranchSoObj->fetchCustomerDetails($customerId)['data'][0];
}
$invoiceArray = [];
if (isset($_GET['collect-payment'])) {
  $encodedParam = $_GET['collect-payment'];
  $decodedParam = urldecode($encodedParam);
  $invoiceArray = explode(',', $decodedParam);
  $invoiceArray = array_map('trim', $invoiceArray);
  $invoiceArray = array_map(function ($element) {
    return str_replace('"', '', $element);
  }, $invoiceArray);
} else {
  echo "collect-payment parameter not found in the URL.";
}
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
  /* cn settale modal css */
  /* Default style for the checkbox (unchecked state) */
  .form-check-input {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 20px;
    height: 20px;
    border: 2px solid #007bff;
    border-radius: 50%;
    position: relative;
    transition: all 0.3s ease;
    background-color: #fff;
  }

  /* Style when checkbox is checked */
  .form-check-input:checked {
    border-color: #28a745;
    /* Green border when checked */
    background-color: #28a745;
    /* Green background when checked */
  }

  /* Adding a checkmark inside the checkbox when it is checked */
  .form-check-input:checked::after {
    content: '';
    position: absolute;
    width: 12px;
    height: 12px;
    background-color: #003060;
    /* White checkmark */
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }

  /* Hover effect for the checkbox */
  .form-check-input:hover {
    border-color: #0056b3;
    /* Blue border on hover */
    background-color: #e0f7fa;
    /* Light blue background on hover */
  }

  /* cn settale modal css end*/
  .text {
    font-size: 1.2em;
  }

  .textColor {
    color: #0090ff;
    font-weight: bold;
  }

  .verticalAlign {
    text-align: right;
    vertical-align: bottom !important;
  }

  .tableStriped {
    background-color: #f2f2f2 !important;
  }

  .customPadding {
    padding-top: 180px !important;
  }

  .borderWhite {
    border: #fff;
  }

  .borderBlue {
    border-bottom: 3px solid #0090ff;
  }

  .dropdown-item.small-text {
    font-size: 0.8rem;
  }

  /* ######################################### */
  /* // design input type file STYLE  */

  .image-input input {
    display: none;
  }


  .is-collect-payment .select2-container {
    width: 100% !important;
  }

  .image-input label i {
    font-size: 125%;
    margin-right: 0.3rem;
  }

  .image-input label:hover i {
    animation: shake 0.35s;
  }

  .image-input img {
    max-width: 175px;
    display: none;
  }

  .image-input span {
    display: none;
    cursor: pointer;
  }

  /******new****/

  .image-input label {
    display: flex;
    align-items: center;
    margin-top: 1em;
    justify-content: center;
    background: #fff;
    box-shadow: 6px 4px 11px -3px #00000070;
    padding: 20px;
    border-radius: 7px;
    border: 2px dashed #dcdcdc;
  }

  img.image-preview {
    object-fit: contain;
    aspect-ratio: 6/3;
    margin: auto;
  }

  .card.collect-payment-card {
    height: 323px;
    min-height: 50%;
    min-width: 600px;
  }

  /*******settlement*******/

  .settlement-card {
    min-height: 90%;
  }

  .settlement-card .image-input {
    overflow: auto;
    height: auto;
    background: #FFF;
    padding: 10px;
    border-radius: 12px;
    margin-top: 15px;
    box-shadow: 0px 3px 9px -5px #000;
  }

  @media (max-width: 575px) {
    .card.collect-payment-card {
      height: max-content;
      min-height: auto;
    }

    .card.collect-payment-card select {
      margin-top: 2em;
    }
  }

  @keyframes shake {
    0% {
      transform: rotate(0deg);
    }

    25% {
      transform: rotate(10deg);
    }

    50% {
      transform: rotate(0deg);
    }

    75% {
      transform: rotate(-10deg);
    }

    100% {
      transform: rotate(0deg);
    }
  }

  /* for on scroll */

  .collectionLineItemsDiv {
    overflow-y: auto;
    max-height: 457px;
    /* padding: 13px; */
    /* Set an appropriate height */
  }

  .collectionLineItemsDivDn {
    overflow-y: auto;
    max-height: 457px;
    /* padding: 13px; */
    /* Set an appropriate height */
  }

  .content-wrapper table tr th {
    padding: 10px 15px;
    background: #003060;
    color: #fff;
    border-right: 1px solid #fff;
    font-weight: 500;
    font-size: 12px;
    text-align: left;
    white-space: nowrap;
  }

  thead {
    position: sticky;
    top: 0;
    z-index: 1;
  }

  /* style fot vendor modal */

  .modal-container {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .modal-box {
    background-color: #ffffff;
    padding: 30px;
    width: 40%;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    position: relative;
    animation: fadeIn 0.3s ease-in-out;
  }

  .modal-close {
    position: absolute;
    top: 12px;
    right: 16px;
    font-size: 20px;
    cursor: pointer;
    color: #333;
  }

  .modal-header {
    text-align: center;
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 20px;
  }

  .form-group {
    margin-bottom: 16px;
  }

  .form-label {
    font-weight: 500;
    display: block;
    margin-bottom: 5px;
  }

  .form-input,
  .form-select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border 0.3s;
  }

  .form-input:focus,
  .form-select:focus {
    border-color: #007bff;
    outline: none;
  }

  .submit-btn {
    background-color: #28a745;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
    display: block;
    margin: 10px auto;
  }

  .submit-btn:hover {
    background-color: #218838;
  }

  .submit-btn:disabled {
    background-color: #6c757d;
    cursor: not-allowed;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: scale(0.9);
    }

    to {
      opacity: 1;
      transform: scale(1);
    }
  }
</style>
<?php
if (isset($_GET['collect-payment'])) {
?>

  <div class="content-wrapper is-collect-payment">
    <!-- Modal -->
    <div class="modal fade" id="exampleCollectionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="exampleCollectionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
          <div class="modal-header card-header py-2 px-3">
            <h4 class="modal-title font-monospace text-md text-white" id="exampleCollectionModalLabel"><i
                class="fa fa-info"></i>&nbsp;Notes</h4>
            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
          </div>
          <div id="notesModalBody" class="modal-body card-body">
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <form action="" method="POST">
          <!--Header-->
          <input type="hidden" name="paymentDetails[paymentCollectType]" value="collect">
          <div class="row m-0 p-0 py-2 my-2">
            <div class="col-6">
              <h5>Collect Payment</h5>
            </div>
            <div class="col-6">
              <div class="float-right d-flex">
                <div class="mx-2"><button class="btn btn-success" type="button"
                    id="submitCollectPaymentBtn">Collect</button></div>
              </div>
            </div>
          </div>
          <!-- Collect Payment Modal -->
          <div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Collection</h5>
                  <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="totalPaidAmtDiv"><span style="font-family: 'Font Awesome 5 Free';"
                      id="totalReceiveAmt">0</span> amount received against invoice</div>
                  <div class="totalCaptureAmtDiv"><span style="font-family: 'Font Awesome 5 Free';"
                      id="totalCaptureAmt">0</span> amount captured as Open advanced</div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" name="submitCollectPaymentBtn" class="btn btn-primary">Confirm</button>
                </div>
              </div>
            </div>
          </div>
          <!--Body-->



          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="card collect-payment-card">
                <div class="card-header p-3">
                  <h4>Info</h4>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <?php if (isset($_GET['customerId'])) { ?>
                        <input type="hidden" name="paymentDetails[customerId]" class="form-control" id="customerSelect"
                          value="<?= $customerDetails['customer_id'] ?>" readonly>
                        <input type="text" class="form-control" id="customerSelect"
                          value="<?= $customerDetails['trade_name'] ?>" readonly>
                      <?php } else { ?>
                        <select name="paymentDetails[customerId]" class="select2 form-control" id="customerSelect">
                          <option value="">Select Customer</option>
                          <?php foreach ($customerList as $customer) { ?>
                            <option value="<?= $customer['customer_id'] ?>">
                              <?= $customer['trade_name'] ?>(<?= $customer['customer_code'] ?>)
                            </option>
                          <?php } ?>
                        </select>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <label for="" class="label-hidden"></label>
                      <input type="number" name="paymentDetails[collectPayment]" step="any"
                        class="form-control collectTotalAmt inputAmountClass" placeholder="Enter amount"
                        aria-label="Username" aria-describedby="basic-addon1">
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <?php
                      $fetchCOADetails = get_acc_bank_cash_accounts()['data'];
                      // console($fetchCOADetails);
                      ?>
                      <label for="" class="label-hidden"></label>
                      <select name="paymentDetails[bankId]" class="form-control">
                        <option value="">Select Bank</option>
                        <?php
                        foreach ($fetchCOADetails as $one) {
                          $account_no = "";
                          if ($one['account_no'] != "") {
                            $account_no = "(" . $one['account_no'] . ")";
                          }
                          if ($one['bank_name'] != "") {
                        ?>
                            <option value="<?= $one['id'] ?>"><?= $one['bank_name'] ?><?= $account_no ?></option>
                        <?php }
                        } ?>
                      </select>
                    </div>
                  </div>
                  <div class="row mt-5">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="totalamount">
                        <p class="text-xs"> Total Outstanding</p>
                        <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalInvAmt">0</span></p> -->
                        <p class="text-xs font-bold rupee-symbol"><?= $companyCurrencyData["currency_name"] ?> <span
                            class="total_outstanding_amount1"> 0</span></p>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="totaldueamount">
                        <p class="text-xs">Total Due</p>
                        <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalDueAmt">0</span></p> -->
                        <p class="text-xs font-bold rupee-symbol"><?= $companyCurrencyData["currency_name"] ?> <span
                            class="total_due_amount1">0</span></p>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="totaloverdue">
                        <p class="text-xs">Total Overdue</p>
                        <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalOverDueAmt">0</span></p> -->
                        <p class="text-xs font-bold rupee-symbol"><?= $companyCurrencyData["currency_name"] ?> <span
                            class="total_overdue_amount1">0</span></p>
                      </div>
                    </div>
                    <!-- round-off section start -->
                    <!-- <div class="row my-3">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="round-off-section">
                          <div class="round-off-head d-flex gap-2">
                            <input type="checkbox" class="checkbox" name="round_off_checkbox" id="round_off_checkbox">
                            <p class="text-xs">Adjust Amount</p>
                          </div>
                          <div id="round_off_hide">
                            <div class="row">
                              <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="adjust-currency d-flex gap-3">
                                  <select id="round_sign" name="currency" class="form-control text-center">
                                    <option value="+">+</option>
                                    <option value="-">-</option>
                                  </select>
                                  <input type="number" step="any" id="round_value" value="0" class="form-control text-center">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="totaldueamount d-flex gap-3">
                                  <p class="text-xs font-bold">Adjusted Amount</p>
                                  <input type="hidden" name="paymentDetails[adjustedCollectAmount]" class="adjustedCollectAmountInp">
                                  <p class="text-xs text-success font-bold rupee-symbol">₹ <span class="adjustedDueAmt">0</span></p>
                                  <input type="hidden" name="paymentDetails[roundOffValue]" class="roundOffValueHidden">
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> -->
                    <!-- round-off section finish -->
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="card collect-payment-card">
                <div class="card-header p-3">
                  <h4>Info</h4>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <p class="text-xs text-right">Remaining</p>
                      <p class="text-xs text-right font-bold rupee-symbol"><?= $companyCurrencyData["currency_name"] ?>
                        <span class="remaningAmt">0</span>
                      </p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="image-input">
                        <input type="file" name="paymentDetails[paymentAdviceImg]" accept="image/*" id="imageInput">
                        <label for="imageInput" class="image-button"><i class="fa fa-image po-list-icon mr-2"></i> Upload
                          Payment Advice</label>
                        <img src="" class="image-preview">
                        <span class="change-image float-right mt-3"><button type="button" class=" btn btn-danger"><i
                              class="fa fa-times mr-2"></i>Remove</button></span>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input totalamount">
                        <label for="">Transaction Date</label>
                        <input type="date" name="paymentDetails[documentDate]" class="form-control" aria-label="Username"
                          aria-describedby="basic-addon1">
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input totaldueamount">
                        <label for="">Posting Date</label>
                        <input type="date" name="paymentDetails[postingDate]" class="form-control" aria-label="Username"
                          aria-describedby="basic-addon1" max="<?= $max ?>" min="<?= $min ?>">
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input totaloverdue">
                        <label for="">Transaction Id / Doc. No.</label>
                        <input type="text" placeholder="Tnx. Id / Doc. No." name="paymentDetails[tnxDocNo]"
                          class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <span class="text-xs text-danger float-right" style="display:none" id="greaterMsg">Can't collect the greater
              amount</span>
            <div class="collectionDiv col-lg-12 col-md-12 col-sm-12">
              <div class="mb-3">
                <a id="invListBtn" class="btn btn-primary">Invoice List</a>
                <a id="dnListTable" class="btn btn-primary">Debit Note List</a>
                <a id="collectVendor" class="btn btn-success">Collect From Vendor</a>

              </div>

              <input type="hidden" name="paymentDetails[advancedPayAmt]" value="" class="advancedPayAmt">
              <div class="collectionLineItemsDiv col-lg-12 col-md-12 col-sm-12">

                <table id="dataTable_detailed_view" class="invTable">
                  <thead>
                    <tr>
                      <th>Invoice No</th>
                      <th>Status</th>
                      <th>Due Dates</th>
                      <th>Invoice Amt.</th>
                      <th>Due Amt.</th>
                      <th style="width: 15%">Rec. Amt.</th>
                      <th>Adjusted Amt. (<span id="adjustAmtCurr"></span>)</th>
                      <th>Due %</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="invDetailsBody">

                  </tbody>

                </table>
              </div>
              <div class="collectionLineItemsDivDn col-lg-12 col-md-12 col-sm-12">

                <table id="dataTable_detailed_view_cn" class="dnTable">
                  <thead>
                    <tr>
                      <th>Debit No</th>
                      <th>Status</th>
                      <th>Total Amt.</th>
                      <th>Due Amt.</th>
                      <th style="width: 15%">Rec. Amt.</th>
                      <th>Due %</th>
                    </tr>
                  </thead>
                  <tbody id="dnDetailsBody">

                  </tbody>

                </table>

              </div>
            </div>


          </div>
      </div>

      <div class="row p-0 m-0">
        <!-- <div class="col-md-6">

              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1"><i class="fa fa-user"></i></span>
                </div>
                <select name="paymentDetails[customerId]" class="form-control" id="customerSelect">
                  <option value="">Select Customer</option>
                  <?php foreach ($customerList as $customer) { ?>
                    <option value="<?= $customer['customer_id'] ?>"><?= $customer['trade_name'] ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">₹</span>
                </div>
                <input type="text" name="paymentDetails[collectPayment]" value="0" class="form-control collectTotalAmt px-3 mr-1" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1">
                <select name="paymentDetails[bankId]" class="form-control mx-1">
                  <option value="">Select Bank</option>
                  <?php
                  $fetchCOADetails = get_acc_bank_cash_accounts()['data'];
                  foreach ($fetchCOADetails as $one) {
                  ?>
                    <option value="<?= $one['id'] ?>"><?= $one['bank_name'] ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="bg-light p-2">
                <input type="hidden" name="paymentDetails[totalDueAmt]" class="totalDueAmtInp" value="0">
                <input type="hidden" name="paymentDetails[totalInvAmt]" class="totalInvAmtInp" value="0">
                <input type="hidden" name="paymentDetails[remaningAmt]" class="remaningAmtInp" value="0">
                <h6>Total Invoice Amt: <strong class="totalInvAmt">0</strong> </h6>
                <h6>Current Due: <strong class="totalDueAmt">0</strong> </h6>
                <h6>Over Due: <strong class="overDueAmt">0</strong> </h6>
              </div>
            </div> -->
        <!-- <div class="col-md-6">
              <span>Remaining: <strong class="remaningAmt">0</strong></span>

              <div class="mt-3">
                <div class="image-input">
                  <input type="file" class="form-control" name="paymentDetails[paymentAdviceImg]" accept="image/*" id="imageInput">
                  <label for="imageInput" class="image-button"><i class="far fa-image"></i> Upload Payment Advice</label>
                  <img src="" class="image-preview">
                  <span class="change-image text-danger"><i class="fa fa-times"> Remove</i></span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <label for="">Document Date</label>
                  <input type="date" name="paymentDetails[documentDate]" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <div class="col-md-4">
                  <label for="">Posting Date</label>
                  <input type="date" name="paymentDetails[postingDate]" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <div class="col-md-4">
                  <label for="">Transaction Id / Doc. No.</label>
                  <input type="text" placeholder="Tnx. Id / Doc. No." name="paymentDetails[tnxDocNo]" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                </div>
              </div>
            </div> -->

      </div>
      </form>
  </div>
  <section>
    </div>

    <!-- Action Modal Start -->
    <div class="modal fade right customer-modal classic-view-modal" id="collectActionModal" role="dialog"
      data-backdrop="static" aria-hidden="true">
      <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" style="max-width: 30%;"
        role="document">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <div class="text-light text-nowrap">
              <p class="text-sm my-2"></p>
              <p class="text-xs my-2"><span class="text-muted">Invoice Number: <span id='modalInvNo'></span></p>
              <p class="text-xs my-2"><span class="text-muted">Invoice Amount:</span> <span id="modalInvAmt"></span></p>
              <p class="text-xs my-2"><span class="text-muted">Due Amt:</span> <span id='modalRemainAmt'></span></p>
              <input type="hidden" name="modalDueamt" id="modalDueAmt" value="">
              <input type="hidden" name="modalDueamt" id="modalDueAmtModal" value="">
              <input type="hidden" name="modalInvId" id="modalInvId" value="">
            </div>
          </div>

          <!-- Modal Body -->
          <div class="modal-body p-3">
            <!-- Round Off -->
            <div class="card mb-3">
              <div class="card-header py-1 text-light">Round Off</div>
              <div class="card-body py-1">
                <div class="d-flex gap-2 m-0 p-0">
                  <div class="input-group input-group-sm w-50">
                    <select class="form-control inputRoundOffSign adjustmentInputSign" id="inputRoundOffSign">
                      <option value="+"> + </option>
                      <option value="-"> - </option>
                    </select>
                  </div>
                  <div class="input-group input-group-sm">
                    <input type="number" step="any" id="inputRoundOffInr"
                      class="form-control border py-3 text-right inputRoundOffInr adjustmentInputValue inputAmountClass"
                      placeholder="0.00">
                    <span class="text-small spanErrorAmtroundoff" id="spanErrorAmtroundoff"></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Write Off -->
            <div class="card mb-3">
              <div class="card-header py-1 text-light">Write Off</div>
              <div class="card-body py-1">
                <div class="d-flex gap-2 m-0 p-0">
                  <div class="input-group input-group-sm w-50">
                    <select id="inputWriteBackSign" class="form-control inputWriteBackSign adjustmentInputSign">
                      <option value="+"> + </option>
                    </select>
                  </div>
                  <div class="input-group input-group-sm">
                    <input type="number" step="any" id="inputWriteBackInr"
                      class="form-control border py-3 text-right inputWriteBackInr adjustmentInputValue inputAmountClass"
                      placeholder="0.00">
                    <span class="text-small spanErrorAmtWriteBack" id="spanErrorAmtWriteBack_"></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Financial Charges -->
            <div class="card mb-3">
              <div class="card-header py-1 text-light">Financial Charges</div>
              <div class="card-body py-1">
                <div class="d-flex gap-2 m-0 p-0">
                  <div class="input-group input-group-sm w-50">
                    <select id="inputFinancialChargesSign"
                      class="form-control inputFinancialChargesSign adjustmentInputSign">
                      <option value="+"> + </option>
                      <option value="-"> - </option>
                    </select>
                  </div>
                  <div class="input-group input-group-sm">
                    <input type="number" step="any" id="inputFinancialChargesInr"
                      class="form-control border py-3 text-right inputFinancialChargesInr adjustmentInputValue inputAmountClass"
                      placeholder="0.00">
                    <span class="text-small spanErrorAmtFinancialCharges" id="spanErrorAmtFinancialCharges_"></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Forex Loss/Gain -->
            <div class="card mb-3">
              <div class="card-header py-1 text-light">Forex Loss/Gain</div>
              <div class="card-body py-1">
                <div class="d-flex gap-2 m-0 p-0">
                  <div class="input-group input-group-sm w-50">
                    <select id="inputForexLossGainSign" class="form-control inputForexLossGainSign">
                      <option value="+"> + </option>
                      <option value="-"> - </option>
                    </select>
                  </div>
                  <div class="input-group input-group-sm">
                    <input type="number" step="any" id="inputForexLossGainInr"
                      class="form-control border py-3 text-right inputForexLossGainInr inputAmountClass"
                      placeholder="0.00">
                    <span class="text-small spanErrorAmtForexLossGain" id="spanErrorAmtForexLossGain"></span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Total TDS -->
            <div class="card mb-3">
              <div class="card-header py-1 text-light">Total TDS</div>
              <div class="card-body py-1">
                <div class="d-flex gap-2 m-0 p-0">
                  <div class="input-group input-group-sm w-50">
                    <select id="inputTotalTdsSign" readonly class="form-control inputTotalTdsSign">
                      <option value="+" selected> - </option>
                    </select>
                  </div>
                  <div class="input-group input-group-sm">
                    <input type="number" step="any" id="inputinputTotalTdsInr"
                      class="form-control border py-3 text-right inputinputTotalTdsInr adjustmentInputValue inputAmountClass"
                      placeholder="0.00">
                    <span class="text-small spanErrorAmtTOtalTds" id="spanErrorAmtTOtalTds"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Action Modal End -->


    <!-- Collect From vendor Modal start  -->
    <div id="collectionModal" class="modal-container">
      <div class="modal-box">
        <span id="closeModalBtn" class="modal-close">&times;</span>
        <h2 class="modal-header">Collection From Vendor</h2>
        <form id="collectvendorForm" method="POST">
          <div class="form-group">
            <input type="hidden" name="paymentDetails[paymentCollectType]" value="collect">
            <input type="hidden" name="type" value="vendor">

            <label class="form-label" for="vendor">Select Vendor</label>
            <select name="paymentDetails[vendorId]" class="form-control" id="vendorSelect">
              <option value="">Select Vendor</option>
              <?php foreach ($vendorList as $vendor) {
              ?>
                <option value="<?= $vendor['vendor_id'] ?>"><?= $vendor['trade_name'] ?>(<?= $vendor['vendor_code'] ?>)
                </option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="amount">Amount</label>
            <input type="number" id="collectVendorAmount" name="paymentDetails[collectPayment]" step="any"
              class="form-control inputAmountClass" placeholder="Enter amount" aria-label="Username"
              aria-describedby="basic-addon1">

          </div>
          <div class="form-group">
            <?php
            $fetchCOADetails = get_acc_bank_cash_accounts()['data'];
            ?>
            <label class="form-label" for="bank">Select Bank</label>
            <select name="paymentDetails[bankId]" class="form-control">
              <option value="">Select Bank</option>
              <?php
              foreach ($fetchCOADetails as $one) {
                $account_no = "";
                if ($one['account_no'] != "") {
                  $account_no = "(" . $one['account_no'] . ")";
                }
                if ($one['bank_name'] != "") {
              ?>
                  <option value="<?= $one['id'] ?>"><?= $one['bank_name'] ?><?= $account_no ?></option>
              <?php }
              } ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="date1">Transaction Date</label>
            <input type="date" name="paymentDetails[documentDate]" class="form-control" aria-label="Username"
              aria-describedby="basic-addon1">

          </div>
          <div class="form-group">
            <label class="form-label" for="date2">Posting Date</label>
            <input type="date" name="paymentDetails[postingDate]" class="form-control" aria-label="Username"
              aria-describedby="basic-addon1" max="<?= $max ?>" min="<?= $min ?>">

          </div>
          <div class="form-group">
            <label class="form-label" for="transactionId">Transaction ID/ Doc No</label>
            <input type="text" placeholder="Tnx. Id / Doc. No." name="paymentDetails[tnxDocNo]" class="form-control"
              aria-label="Username" aria-describedby="basic-addon1">

          </div>


          <button class="submit-btn" id="collectFromVendorBtn" type="submit">Receipt</button>
        </form>
        <div>
          <p class="text-xs">*Confirming the receipt button - the user is confirming the processing of the transaction as
            a receipt from a vendor.</p>
        </div>
      </div>
    </div>
    <!-- Collect From vendor Modal end -->


  <?php } elseif (isset($_GET['adjust-payment'])) { ?>
    <div class="content-wrapper">
      <section class="content">
        <div class="container-fluid">
          <form action="" method="POST">
            <!--Header-->
            <input type="hidden" name="paymentDetails[paymentCollectType]" value="adjust">
            <div class="row m-0 p-0 py-2 my-2">
              <div class="col-6">
                <h5>Settlement</h5>
              </div>
              <!-- <div class="col-6">
              <div class="float-right d-flex">
                <div class="mx-2"><button class="btn btn-success" type="button" data-toggle="modal" data-target="#exampleModal" id="submitCollectPaymentBtn">POST</button></div>
                <div class="mx-2 btn btn-danger " data-dismiss="modal" aria-label="Close">X</div>
              </div>
            </div> -->
            </div>
            <!-- Collect Payment Modal -->
            <!-- <div class="modal" id="exampleModal" style="    height: 200px; width: 100%;" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Settlement</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div><span style="font-family: 'Font Awesome 5 Free';" id="totalReceiveAmt">0</span> amount paid against invoice</div>
                  <div><span style="font-family: 'Font Awesome 5 Free';" id="totalCaptureAmt">0</span> amount captured as an advance</div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="submitCollectPaymentBtn" class="btn btn-primary">Confirm</button>
                </div>
              </div>
            </div>
          </div> -->
            <!--Body-->



            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="card settlement-card">
                  <div class="card-header p-3">
                    <h4>Info</h4>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <select name="paymentDetails[customerId]" class="form-control select2" id="customerSelect">
                          <option value="">Select Customer</option>
                          <?php foreach ($customerList as $customer) { ?>
                            <option value="<?= $customer['customer_id'] ?>">
                              <?= $customer['trade_name'] ?>(<?= $customer['customer_code'] ?>)
                            </option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <!-- <div class="row mt-5">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="totalamount">
                        <p class="text-xs"> Total Invoice Amount</p>
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="totalInvAmt">0</span></p>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="totaldueamount">
                        <p class="text-xs">Current Due Amount</p>
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="totalDueAmt">0</span></p>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="totaloverdue">
                        <p class="text-xs">Overdue Amount</p>
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="totalOverDueAmt">0</span></p>
                      </div>
                    </div>
                  </div> -->
                    <div class="row mt-5">
                      <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="totalamount">
                          <p class="text-xs"> Total Outstanding</p>
                          <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalInvAmt">0</span></p> -->
                          <p class="text-xs font-bold rupee-symbol"><?= $currency_name ?> <span
                              class="total_outstanding_amount1">0</span></p>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="totaldueamount">
                          <p class="text-xs">Total Due</p>
                          <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalDueAmt">0</span></p> -->
                          <p class="text-xs font-bold rupee-symbol"><?= $currency_name ?> <span
                              class="total_due_amount1">0</span></p>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="totaloverdue">
                          <p class="text-xs">Total Overdue</p>
                          <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalOverDueAmt">0</span></p> -->
                          <p class="text-xs font-bold rupee-symbol"><?= $currency_name ?> <span
                              class="total_overdue_amount1">0</span></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="card settlement-card">
                  <div class="card-header p-3">
                    <h4>Info</h4>
                  </div>
                  <div class="card-body">
                    <div class="row" style="display: none;">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <p class="text-xs text-right">Remaining</p>
                        <p class="text-xs text-right font-bold rupee-symbol"><?= $currency_name ?> <span
                            class="remaningAmt">0</span></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="image-input">
                          <h6 class="text-sm">Advanced List</h6>
                          <div class="advancedAmtList" style="max-height: 200px;">

                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12">

                <div class="inputTableRow"></div>
              </div>
            </div>


            <!-- <div class="row p-0 m-0">
              <div class="col-md-6">

                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1"><i class="fa fa-user"></i></span>
                  </div>
                  <select name="paymentDetails[customerId]" class="form-control" id="customerSelect">
                    <option value="">Select Customer</option>
                    <?php foreach ($customerList as $customer) { ?>
                      <option value="<?= $customer['customer_id'] ?>"><?= $customer['trade_name'] ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="bg-light p-2">
                  <input type="hidden" name="paymentDetails[totalDueAmt]" class="totalDueAmtInp" value="0">
                  <input type="hidden" name="paymentDetails[totalInvAmt]" class="totalInvAmtInp" value="0">
                  <input type="hidden" name="paymentDetails[remaningAmt]" class="remaningAmtInp" value="0">
                  <h6>Total Invoice Amt: <strong class="totalInvAmt">0</strong> </h6>
                  <h6>Current Due: <strong class="totalDueAmt">0</strong> </h6>
                  <h6>Over Due: <strong class="totalOverDueAmt">0</strong> </h6>
                </div>
              </div>
              <div class="col-md-6">
                <span>Remaining: <strong class="remaningAmt">0</strong></span>
                <div class="bg-light p-2 border my-2 shadow-sm" style="min-height:131px; max-height:231px; overflow:scroll;">
                  <div class="advancedAmtList">

                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="inputTableRow"></div>
              </div>
            </div> -->
          </form>
        </div>
        <section>
    </div>
    </div>
  <?php } elseif (isset($_GET['adjust-cn'])) { ?>
    <div class="content-wrapper">
      <section class="content">
        <div class="container-fluid">
          <form action="" method="POST">
            <!--Header-->
            <input type="hidden" name="paymentDetails[paymentCollectType]" value="adjust">
            <div class="row m-0 p-0 py-2 my-2">
              <div class="col-6">
                <h5>Settlement</h5>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="card settlement-card">
                  <div class="card-header p-3">
                    <h4>Info</h4>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <select name="paymentDetails[customerId]" class="form-control select2" id="customerSelect">
                          <option value="">Select Customer</option>
                          <?php foreach ($customerList as $customer) { ?>
                            <option value="<?= $customer['customer_id'] ?>">
                              <?= $customer['trade_name'] ?>(<?= $customer['customer_code'] ?>)
                            </option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="row mt-5">
                      <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="totalamount">
                          <p class="text-xs"> Total Outstanding</p>
                          <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalInvAmt">0</span></p> -->
                          <p class="text-xs font-bold rupee-symbol"><?= $currency_name ?> <span
                              class="total_outstanding_amount1">0</span></p>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="totaldueamount">
                          <p class="text-xs">Total Due</p>
                          <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalDueAmt">0</span></p> -->
                          <p class="text-xs font-bold rupee-symbol"><?= $currency_name ?> <span
                              class="total_due_amount1">0</span></p>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="totaloverdue">
                          <p class="text-xs">Total Overdue</p>
                          <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalOverDueAmt">0</span></p> -->
                          <p class="text-xs font-bold rupee-symbol"><?= $currency_name ?> <span
                              class="total_overdue_amount1">0</span></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="card settlement-card">
                  <div class="card-header p-3">
                    <h4>Info</h4>
                  </div>
                  <div class="card-body">
                    <div class="row" style="display: none;">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <p class="text-xs text-right">Remaining</p>
                        <p class="text-xs text-right font-bold rupee-symbol">₹ <span class="remaningAmt">0</span></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="image-input">
                          <h6 class="text-sm">Advanced List</h6>
                          <div class="advancedAmtList" style="max-height: 200px;">

                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12">

                <div class="inputTableRow"></div>
              </div>
            </div>
            <div class="collectionLineItemsDiv col-lg-12 col-md-12 col-sm-12">

              <table id="dataTable_detailed_view" class="cnTable">
                <thead>
                  <tr>
                    <th>Cr Note No</th>
                    <th>Status</th>
                    <th>CrNote Dates</th>
                    <th>CrNote Amt.</th>
                    <th>Settlement</th>
                  </tr>
                </thead>
                <tbody id="cnDetailsBody">

                </tbody>

              </table>
            </div>

          </form>
        </div>
        <section>
    </div>

    <!-- right modal start here  -->
    <div class="modal fade right customer-modal settlement-modal" id="cnSettlementModal" tabindex="-1" role="dialog"
      aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
        <!--Content-->

        <div class="modal-content">
          <!--Header-->
          <div class="modal-header">
            <h5 class="text-white">Invoice List </h5>
            <input type="hidden" class="inv-<?= $one['so_invoice_id'] ?>-dueAmtOnModalStatic"
              value="<?= inputValue($one['due_amount']) ?>">
          </div>
          <!--Body-->
          <div class="modal-body pl-4 pr-4 pt-5" id="cnInvModalBodyDiv">

          </div>

        </div>
      </div>
    </div>
    <!-- right modal end here  -->
    </div>
  <?php
} else {
  $url = BRANCH_URL . 'location/collect-payment.php';
  ?>
    <script>
      window.location.href = "<?= $url; ?>";
    </script>
  <?php
} ?>

  <?php
  require_once("../common/footer.php");
  ?>
  <script>
    $(document).ready(function() {
      $(".select2").select2();
    })
  </script>
  <script>
    function rm() {
      $(event.target).closest("tr").remove();
    }

    function addMultiQty(id) {
      let addressRandNo = Math.ceil(Math.random() * 100000);
      $(`.modal-add-row_${id}`).append(`<tr><td><span class='has-float-label'><input type='date' name='listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]' class='form-control' placeholder='delivery date'><label>Delivery date</label></span></td><td><span class='has-float-label'><input type='text' name='listItem[${id}][deliverySchedule][${addressRandNo}][quantity]' class='form-control' placeholder='quantity'><label>quantity</label></span></td><td><a class='btn btn-danger' onclick='rm()'><i class='fa fa-minus'></i></a></td></tr>`);
    }
  </script>
  <script>
    $(document).ready(function() {
      // console.log("ok");

      $('.reverseCollection').click(function(e) {
        e.preventDefault(); // Prevent default click behavior

        var dep_keys = $(this).data('id');
        var $this = $(this); // Store the reference to $(this) for later use

        Swal.fire({
          icon: 'warning',
          title: 'Are you sure?',
          text: 'You want to reverse this?',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Reverse'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              type: 'POST',
              data: {
                dep_keys: dep_keys,
                dep_slug: 'reverseCollection'
              },
              url: 'ajaxs/ajax-reverse-post.php',
              beforeSend: function() {
                $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
              },
              success: function(response) {
                var responseObj = JSON.parse(response);
                // console.log(responseObj);

                if (responseObj.status == 'success') {
                  $this.parent().parent().find('.listStatus').html('reverse');
                  $this.hide();
                } else {
                  $this.html('<i class="far fa-undo po-list-icon"></i>');
                }

                let Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 4000
                });
                Toast.fire({
                  icon: responseObj.status,
                  title: '&nbsp;' + responseObj.message
                }).then(function() {
                  // location.reload();
                });
              }
            });
          }
        });
      });

      // console.log("ok1");

      let collectTotalAmt = helperAmount($(".collectTotalAmt").val()) ? helperAmount($(".collectTotalAmt").val()) : 0;
      if (collectTotalAmt <= 0 || collectTotalAmt == "") {
        $("#submitCollectPaymentBtn").prop("disabled", true);
      } else {
        $("#submitCollectPaymentBtn").prop("disabled", false);
      }
      // console.log("ok12");

      // var staticRemain = 0;
      // $('#itemsDropDown')
      //   .select2()
      //   .on('select2:open', () => {
      //     // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
      //   });
      // $('#customerSelect')
      //   .select2()
      //   .on('select2:open', () => {
      //     // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
      //   });

      // console.log("ok16")

      // customers ********************************
      function loadCustomers() {
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-customers.php`,
          beforeSend: function() {
            $("#customerDropDown").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            $("#customerDropDown").html(response);
          }
        });
      }
      loadCustomers();
      // console.log("ok10")

      // get customer details by id
      $("#customerDropDown").on("change", function() {
        let itemId = $(this).val();

        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-customers-list.php`,
          data: {
            act: "listItem",
            itemId
          },
          beforeSend: function() {
            $("#customerInfo").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            // console.log(response);
            $("#customerInfo").html(response);
          }
        });
      });
      // **************************************
      function loadItems() {
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-items.php`,
          beforeSend: function() {
            $("#itemsDropDown").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            $("#itemsDropDown").html(response);
          }
        });
      }
      loadItems();

      // get item details by id
      $("#itemsDropDown").on("change", function() {
        let itemId = $(this).val();

        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-items-list.php`,
          data: {
            act: "listItem",
            itemId
          },
          beforeSend: function() {
            //  $("#itemsTable").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            console.log(response);
            $("#itemsTable").append(response);
          }
        });
      });
      $(document).on("click", ".delItemBtn", function() {
        // let id = ($(this).attr("id")).split("_")[1];
        // $(`#delItemRowBtn_${id}`).remove();
        $(this).parent().parent().remove();
      })

      $(document).on('submit', '#addNewItemForm', function(event) {
        event.preventDefault();
        let formData = $("#addNewItemsForm").serialize();
        $.ajax({
          type: "POST",
          url: `ajaxs/so/ajax-items.php`,
          data: formData,
          beforeSend: function() {
            $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
            $("#addNewItemsFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');
          },
          success: function(response) {
            $("#goodTypeDropDown").html(response);
            $('#addNewItemsForm').trigger("reset");
            $("#addNewItemsFormModal").modal('toggle');
            $("#addNewItemsFormSubmitBtn").html("Submit");
            $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
          }
        });
      });

      $(document).on("keyup change", ".qty", function() {
        let id = $(this).val();
        var sls = $(this).attr("sls");
        alert(sls);
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-items-list.php`,
          data: {
            act: "totalPrice",
            itemId: "ss",
            id
          },
          beforeSend: function() {
            $(".totalPrice").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            console.log(response);
            $(".totalPrice").html(response);
          }
        });
      })

      // console.log("ok03")

      $(".deliveryScheduleQty").on("change", function() {
        let qtyVal3 = helperQuantity(($(this).attr("id")).split("_")[1]);
        let qtyVal = helperQuantity($(this).find(":selected").data("quantity"));
        // let qtyVal2 = $(this).find(":selected").data("deliverydate");
        // let qtyVal = $(this).find(":selected").children("span");
        // $( "#myselect option:selected" ).text();
        // console.log(qtyVal);
        $(`#itemQty_${qtyVal3}`).val(decimalQuantity(qtyVal));
      });

      // console.log("ok18")

      function calculateDueAmt() {
        let totalDueAmt = 0;
        let totalInvAmt = 0;
        let overDueAmt = 0;
        $(".dueAmt").each(function() {
          totalDueAmt += (helperAmount($(this).text()) > 0) ? helperAmount($(this).text()) : 0;
        });
        $(".invAmt").each(function() {
          totalInvAmt += (helperAmount($(this).text()) > 0) ? helperAmount($(this).text()) : 0;
        });
        $(".totalDueAmt").html(decimalAmount(totalDueAmt));
        $(".totalInvAmt").html(decimalAmount(totalInvAmt));
        $(".totalDueAmtInp").val(decimalAmount(totalDueAmt));
        $(".totalInvAmtInp").val(decimalAmount(totalInvAmt));
      }

      if (window.location.search === '?adjust-cn') {
        $(".totaldueamount").parent().hide();
      }
      // imranali59059👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰
      // console.log("ok13")
      // select customer 
      $("#customerSelect").on("change", function() {
        let customerSelect = $(this).val();
        console.log('customerSelect');
        console.log(customerSelect);
        if (window.location.search === '?adjust-payment') {
          adjustPayment(customerSelect);
        } else if (window.location.search === '?adjust-cn') {
          loadUnrefCreditNote(customerSelect);
          advancedAmtlist(customerSelect)
          creditNoteOutstandingAmt(customerSelect)
        } else {
          collectPayment(customerSelect);
        }
      });


      function advancedAmtlist(customerSelect) {
        $.ajax({
          type: "POST",
          url: `ajaxs/so/ajax-invoice-customer-advanced.php`,
          data: {
            customerSelect
          },
          beforeSend: function() {
            $(".advancedAmtList").html(`<option value="">Loading...</option>`);
          },
          success: function(response) {
            $(".advancedAmtList").html(response);
          }
        });
      }

      function creditNoteOutstandingAmt(customerSelect) {
        $.ajax({
          type: "POST",
          url: `ajaxs/ajax-credit-note-due-amount.php`,
          dataType: 'JSON',
          data: {
            act: "creditNote-dueAmt",
            id: customerSelect,
          },
          beforeSend: function() {
            // $(".advancedAmtList").html(`<option value="">Loading...</option>`);
          },
          success: function(response) {
            // $(".advancedAmtList").html(response);
            console.log(response)

            if (response.status == "success") {
              let total_outstanding_amount = response.total_outstanding_amount
              let total_overdue_amount = response.total_overdue_amount

              $(".total_outstanding_amount1").text(decimalAmount(total_outstanding_amount));
              // $(".total_due_amount1").text(decimalAmount(total_due_amount));
              $(".total_overdue_amount1").text(decimalAmount(total_overdue_amount));
            }


          }
        });
      }

      function adjustPayment(customerSelect) {
        $.ajax({
          type: "POST",
          url: `ajaxs/so/ajax-invoice-customer-advanced.php`,
          data: {
            customerSelect
          },
          beforeSend: function() {
            $(".advancedAmtList").html(`<option value="">Loading...</option>`);
          },
          success: function(response) {
            $(".advancedAmtList").html(response);

            // calculateDueAmt();
            // let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
            // $(".remaningAmt").html(advancedPayAmt);
            // console.log('first', response);
          }
        });
        $.ajax({
          type: "POST",
          url: `ajaxs/so/ajax-invoice-customer-list2.php`,
          data: {
            customerSelect
          },
          beforeSend: function() {
            $(".inputTableRow").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            // console.log({response : response})
            $(".inputTableRow").html(response);

            let total_overdue_amount = helperAmount($(".total_overdue_amount").val());
            let total_due_amount = helperAmount($(".total_due_amount").val());
            let total_outstanding_amount = helperAmount($(".total_outstanding_amount").val());

            console.log({
              total_overdue_amount: total_overdue_amount,
              total_due_amount: total_due_amount,
              total_outstanding_amount: total_outstanding_amount
            })

            $(".total_outstanding_amount1").text(decimalAmount(total_outstanding_amount));
            $(".total_due_amount1").text(decimalAmount(total_due_amount));
            $(".total_overdue_amount1").text(decimalAmount(total_overdue_amount));

            calculateDueAmt();
            let advancedPayAmt = helperAmount($(".advancedPayAmt").text()) ? helperAmount($(".advancedPayAmt").text()) : 0;
            $(".remaningAmt").html(decimalAmount(advancedPayAmt));
            console.log('first', advancedPayAmt);
          }
        });
      }

      // cn adjustment js start


      function loadUnrefCreditNote(customerSelect) {
        custId = customerSelect;
        $('#cnDetailsBody').html('');

        $.ajax({
          type: "POST",
          url: `ajaxs/ajax-untagged-credit-notes.php`,
          data: {
            act: "unrefcreditnote",
            id: custId,
          },
          beforeSend: function() {
            // $(".cnTable").html(`<option value="">Loading...</option>`);
          },
          success: function(res) {
            // $(".cnTable").html('');
            try {
              const response = JSON.parse(res);
              console.log(response)
              appendCreditNote(response.data);

            } catch (err) {
              console.log(err);
            }
          }
        });
      }

      function appendCreditNote(invData) {
        const tableBody = $('#cnDetailsBody');
        let rows = '';

        invData.forEach(row => {
          // Assuming you need a button to trigger a modal, define the action button HTML
          let actBtn = `
            <i class="fas fa-handshake po-list-icon creditNoteSettle"
               id="creditNoteSettlement" 
               style="cursor:pointer"
               aria-hidden="true"
               data-toggle="modal"
               data-custid=${row.party_id}
               data-target="#cnSettlementModal"
               data-crid=${row.cr_note_id}
               >
            </i>
        `;

          // Building the row content
          rows += `
            <tr>
                <td><p class="text-center">${row.credit_note_no}</p></td>
                <td><p class="text-center">${row.status}</p></td>
                <td><p class="text-center">${formatDate(row.postingDate)}</p></td>
                <td class="duePercentage text-right" id="duePercentage_${row.total}">
                    <p class="text-center">${decimalAmount(row.total)}</p>
                </td>
                <td>${actBtn}</td>
            </tr>
        `;
        });

        // Append all rows to the table body
        tableBody.append(rows);
      }

      $(document).on("click", ".creditNoteSettle", function() {
        let customer_id = $(this).data('custid');
        let cr_note_id = $(this).data('crid');
        // console.log("customer_id" + customer_id);
        $("#cnInvModalBodyDiv").empty(); // Clear the modal content
        $(".form-check-input").prop("checked", false);
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-invoice-customer-list-fetch.php`,
          data: {
            act: "cnrefInvoiceData",
            id: customer_id,
          },
          success: function(res) {
            // Parse the response
            const response = JSON.parse(res);
            console.log(response);
            let invData = response.data;
            let rows = '';
            invData.forEach(row => {
              rows += `
        <div class="card">
            <div class="card-body">
                <div class="row border align-center my-2">
                    <div class="col-md-6">
                        <h6 class="text-success text-sm">Invoice Amount <span class="rupee-symbol">₹</span><span class="inv-${row.so_invoice_id}-advancedAmtSpan" id="inv-${row.so_invoice_id}">${decimalAmount(row.all_total_amt)}</span></h6>
                        <h6 class="text-success text-sm">Due Amount <span class="rupee-symbol">₹</span><span class="inv-${row.so_invoice_id}-advancedAmtSpan" id="inv-${row.so_invoice_id}-advancedAmtSpan">${decimalAmount(row.due_amount)}</span></h6>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid advance-list-cash">
                            <p class="text-right text-sm m-2 font-weight-bold">${row.invoice_no}</p>
                            <p class="text-right text-xs m-2">${formatDate(row.invoice_date)}</p>
                        </div>
                    </div>
                </div>
                <!-- Checkbox and Settle Button -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="invoiceCheck_${row.so_invoice_id}" data-invoice-id="${row.so_invoice_id}">
                            <label class="form-check-label" for="invoiceCheck_${row.so_invoice_id}">Select for Settlement</label>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-primary btn-sm settle-btn" data-invoice-id="${row.so_invoice_id}" data-crId="${cr_note_id}" id="settleBtn_${row.so_invoice_id}">Settle</button>
                    </div>
                </div>
            </div>
        </div>
    `;
            });

            // Append the rows to the modal body div
            $("#cnInvModalBodyDiv").append(rows);


          }
        });
      });

      $(document).on("change", ".form-check-input", function() {
        // Uncheck all checkboxes except the one that triggered the event
        $(".form-check-input").not(this).prop("checked", false);
      });

      $(document).on("click", ".settle-btn", function() {
        // $("#cnInvModalBodyDiv").html("");
        const invoiceId = $(this).data('invoice-id');
        const cust_id = $('#creditNoteSettlement').data('custid');
        const crnoteId = $(this).data('crid');

        // alert(crnoteId)

        // Check if the corresponding checkbox is checked
        const checkbox = $(`#invoiceCheck_${invoiceId}`);
        if (checkbox.prop("checked")) {
          // If checked, proceed with the settlement
          console.log("Settling Invoice ID: " + invoiceId);
          console.log("cust_id: " + cust_id);
          console.log("crnoteId: " + crnoteId);
          // exit();

          $.ajax({
            type: "POST",
            url: "ajaxs/ajax-untagged-credit-notes.php",
            data: {
              act: "settleInvoice",
              invoiceId: invoiceId,
              custId: cust_id,
              crnoteId: crnoteId
            },
            success: function(res) {
              const response = JSON.parse(res);
              Swal.fire({
                icon: response.status,
                title: response.message,
                timer: 1000,
                showConfirmButton: false,
              })

            },
            error: function(error) {
              alert("Error settling invoice.");
            }
          });
        } else {
          alert("Please select the checkbox to settle this invoice.");
        }

      })


      // cn adjustment js end


      /*
            ------------ collect payment form js start -----------------------------
      */

      // all required variables for form 
      let custId
      let debouceFlag = false;
      let pageCollection = 1;
      let sl_no = 0;
      let modalInputArray = [];

      let debouceFlagDebit = true;
      let pageDebit = 1;
      let loadDn = 0;

      let currency = ""
      let companyCurrency = ""


      // main scroll event for data loading
      $(".collectionLineItemsDiv").on('scroll', function() {
        const element = $(".collectionLineItemsDiv")[0];
        const scrollTop = element.scrollTop;
        const scrollHeight = element.scrollHeight;
        const clientHeight = element.clientHeight;
        const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
        if (scrollPercentage >= 70) {
          if (custId == null || custId == undefined) {
            alert('Select Customer First');
          } else {
            loadCollectionInv();
          }
        }
      });
      // scroll event for debit note
      $(".collectionLineItemsDivDn").on('scroll', function() {
        const element = $(".collectionLineItemsDivDn")[0];
        const scrollTop = element.scrollTop;
        const scrollHeight = element.scrollHeight;
        const clientHeight = element.clientHeight;
        const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
        if (scrollPercentage >= 70) {
          if (custId == null || custId == undefined) {
            alert('Select Customer First');
          } else {
            loadCollectionInv();
          }
        }
      });

      // this will trigeer when customer will be selected
      function collectPayment(customerSelect) {
        custId = customerSelect;
        debouceFlag = true;
        debouceFlagDebit = true;
        sl_no = 0;
        loadDn = 0;
        pageCollection = 1;
        pageDebit = 1;


        $('#invDetailsBody').html('');
        $('#dnDetailsBody').html('');
        $(".invTable").show();
        $(".dnTable").hide();

        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-invoice-customer-list-fetch.php`,
          data: {
            act: "custInvAmountDetail",
            id: custId,
          },
          success: function(res) {
            try {
              const response = JSON.parse(res);
              console.log(response)
              let data = response.data;
              console.log(data.totalAdvanceAmt)
              $(".total_outstanding_amount1").text(decimalAmount(data.dataObj.total_outstanding_amount));
              $(".total_due_amount1").text(decimalAmount(data.dataObj.total_due_amount));
              $(".total_overdue_amount1").text(decimalAmount(data.dataObj.total_overdue_amount));
              // $(".advancedPayAmt").val(data.totalAdvanceAmt);
              $("#adjustAmtCurr").text(data.companyCurrency);
              companyCurrency = data.companyCurrency;
            } catch (err) {
              console.log(err);
            }

          }
        });
        loadCollectionInv();
      }

      // load function for infinite scrolling
      function loadCollectionInv() {
        let loadLimit = 10;
        if (debouceFlag) {
          $.ajax({
            type: "GET",
            url: `ajaxs/so/ajax-invoice-customer-list-fetch.php`,
            data: {
              act: "customerInvoiceData",
              id: custId,
              limit: loadLimit,
              page: pageCollection,
            },
            beforeSend: function() {
              debouceFlag = false;
              // $("#invDetailsBody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
            },
            success: function(res) {

              try {
                const response = JSON.parse(res);

                if (response.status == "success") {

                  const data = response.data;
                  const invoiceDataObj = data.invoiceData;
                  appendCollectionInv(invoiceDataObj);
                  pageCollection++;
                  if (response.numRows == loadLimit) {
                    debouceFlag = true;
                  }
                } else {
                  let tableBody = $('#invDetailsBody');
                  let tr = `<td colspan='5'><p>No Invoice Found </p></td>`;
                  tableBody.append(tr);
                }

              } catch (error) {
                console.error(error);
                console.log(res);

              }


            }
          });
        }
      }

      // append data into table with conditions
      function appendCollectionInv(invData) {

        const tableBody = $('#invDetailsBody');
        let rows = '';
        invData.forEach(row => {

          let duePercentange = helperQuantity(((row.dueAmount ?? 0) / (row.dataObj.all_total_amt ?? 1)) * 100);
          let inDiv = '';
          let inputRow = '';
          let actBtn = '';

          // let creditPeriod = Number(row.dataObj.credit_period) ?? 0;

          // const date = new Date();
          // date.setDate(date.getDate() + creditPeriod);
          // let dueDate = date.toISOString().split('T')[0];


          const creditPeriod = Number(row.dataObj.credit_period) || 0; // Ensures a valid number
          const date = new Date();
          date.setDate(date.getDate() + creditPeriod);
          const dueDate = date.toISOString().split('T')[0];

          currency = row.dataObj.currency_name ?? 'INR';

          if (row.dataObj.journal_id == null || row.dataObj.journal_id == '' || row.dataObj.journal_id == 0) {
            inDiv = `
            <div class="input-group-prepend">
              <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">❌</span>
            </div>
            `;
            inputRow = `<input readonly type="text" class="form-control receiveAmt px-3 text-right inputAmountClass" style="background-color: #c6e5d4 !important;" placeholder="Accounting document not found " aria-label="Username" aria-describedby="basic-addon1">`;
          } else {

            if (row.dueAmount <= 0) {
              inDiv = `
          <div class="input-group-prepend">
            <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">✅</span>
          </div>
          `;
              inputRow = `<input readonly type="text" class="form-control receiveAmt px-3 text-right inputAmountClass" style="background-color: #c6e5d4 !important;" placeholder="No Due Payment" aria-label="Username" aria-describedby="basic-addon1">`;
            } else {
              inDiv = `  
                          <div class="input-group-prepend">
                            <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">${row.dataObj.currency_name}</span>
                          </div>
                          `;
              inputRow = `<input type="text" name="paymentInvDetails[${custId}][${sl_no}][recAmt]" class="form-control receiveAmt px-3 text-right inputAmountClass" id="receiveAmt_${row.dataObj.so_invoice_id}" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1" data-id="${row.so_invoice_id}" data-dueamt="${row.dataObj.due_amount}" data-invamt="${row.dataObj.all_total_amt}">`;
              actBtn = `<a style="cursor:pointer" data-toggle="modal" class="collectActionModalBtn" data-id="${row.dataObj.so_invoice_id}" data-no="${row.dataObj.invoice_no}" data-dueamt="${row.dueAmount}" data-amount="${row.dataObj.all_total_amt}">
                        <i class="fa fa-cog po-list-icon adjustModal" data-target="#collectActionModal"></i>
                    </a> `;
            }
          }

          const statusClasses = {
            "paid": "status",
            "partial paid": "status-warning"
          };

          let statusClass = statusClasses[row.statuslabel] || "status-danger";


          const inputHidden = `
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][invoiceId]" value="${row.dataObj.so_invoice_id}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][invoiceNo]" value="${row.dataObj.invoice_no}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][invoiceStatus]" value="${row.statuslabel}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][creditPeriod]" value="${row.dataObj.credit_period}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][invAmt]" value="${decimalAmount(row.dataObj.all_total_amt)}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][dueAmt]" id="dueAmount_${sl_no}" value="${decimalAmount(row.dataObj.due_amount)}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][customer_id]" value="${custId}">

            
                    
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputRoundOffInrWithSign]" id="inputRoundOffInrWithSign_${row.dataObj.so_invoice_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputRoundOffWithSign]" id="inputRoundOffWithSign_${row.dataObj.so_invoice_id}" value="0">

                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputWriteBackInrWithSign]" id="inputWriteBackInrWithSign${row.dataObj.so_invoice_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputWriteBackWithSign]" id="inputWriteBackWithSign_${row.dataObj.so_invoice_id}" value="0">
                    
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputFinancialChargesWithSign]" id="inputFinancialChargesWithSign_${row.dataObj.so_invoice_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputFinancialChargesInrWithSign]" id="inputFinancialChargesInrWithSign_${row.dataObj.so_invoice_id}" value="0">

                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputForexLossGainInrWithSign]" id="inputForexLossGainInrWithSign_${row.dataObj.so_invoice_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputForexLossGainWithSign]" id="inputForexLossGainWithSign_${row.dataObj.so_invoice_id}" value="0">

                    <input type="hidden" class="alltdsamt" name="paymentInvDetails[${custId}][${sl_no}][inputTotalTdsWithSign]" id="inputTotalTdsWithSign_${row.dataObj.so_invoice_id}" value="0">                    
                    
                    <input type="hidden" id="inputPreviousCurrencyRate_${row.dataObj.so_invoice_id}" value="${row.inputPreviousCurrentRate}">
                    <input type="hidden" id="inputCurrentCurrencyRate_${row.dataObj.so_invoice_id}" value="${row.dataObj.conversion_rate}">
                    <input type="hidden" id="inputInvoiceCurrencyName_${row.dataObj.so_invoice_id}" value="${row.dataObj.currency_name}">
                    <input type="hidden" id="inputCompanyCurrencyName_${row.dataObj.so_invoice_id}" value="${row.inputCompanyCurrencyName}">
                 
                    `;

          rows += `
        <tr>
            ${inputHidden}
            <td><p class="text-center">${row.dataObj.invoice_no}</p></td>
            <td><p class="text-center ${statusClass}">${row.statuslabel}</p></td>
            <td><p class="text-center">${formatDate(dueDate)}</p></td>
            <td class="invAmt invoiceAmt text-right" id="invoiceAmt_${row.dataObj.so_invoice_id}"><p class="text-right">${decimalAmount(row.dataObj.all_total_amt)}</p></td>
            <td class="dueAmt" id="dueAmt_${row.dataObj.so_invoice_id}"><p class="text-right">${decimalAmount(row.dueAmount)}</p></td>
            <td><div class="input-group enter-amount-input m-0">${inDiv}${inputRow}</div></td>
            <td>
              <div class="input-group input-group-sm m-0">
                <div class="input-group-prepend">
                            <span class="input-group-text">${row.dataObj.currency_name}</span>
                </div>
                <input type="number" step="any" id="inputInvoiceAdjustAmt_${row.dataObj.so_invoice_id}" name="" class="form-control border py-3 text-right inputInvoiceAdjustAmt" placeholder="${decimalAmount(0)}" readonly>
                <span id="spanInvoiceAdjustAmt_${row.dataObj.so_invoice_id}" class="text-small spanInvoiceAdjustAmt"></span>
              </div>
            </td>
            <td class="duePercentage text-right" id="duePercentage_${row.dataObj.so_invoice_id}"><p class="text-center">${decimalQuantity(Math.round(duePercentange))}%</p></td>
            <td>
                ${actBtn}
            </td>
        </tr>`;
          sl_no++;
        });

        tableBody.append(rows);

      }

      // inv line of items adjut modal
      $(document).on("click", ".collectActionModalBtn", function() {
        let id = $(this).data('id');
        let no = $(this).data('no');
        let dueAmt = helperAmount($(this).data('dueamt'));
        let amount = helperAmount($(this).data('amount'));

        $("#modalInvNo").html(no);
        $("#modalInvAmt").html(decimalAmount(amount));
        $("#modalRemainAmt").html(decimalAmount(dueAmt));

        $("#modalInvId").val(id);
        $("#modalDueAmtModal").val(dueAmt);
        let adj = parseFloat($(`#inputInvoiceAdjustAmt_${id}`).val()) || 0;


        if (adj !== 0) {
          let totaltdss = helperAmount(previousValues[id].tds) || 0;

          if (totaltdss > 0) {
            dueAmt -= totaltdss;
            $("#modalRemainAmt").html(decimalAmount(dueAmt));
          } else {
            dueAmt += adj;
            $("#modalRemainAmt").html(decimalAmount(dueAmt));
          }

        }

        if (modalInputArray[id]) {
          putModalData(id);
        } else {
          clearModalFields();
        }
        $('#collectActionModal').modal('show');
      });

      // two arrays for data storing and bulding adjust amount
      const inpFields = {
        roundOff: {
          select: "inputRoundOffSign",
          input: "inputRoundOffInr"
        },
        writeBack: {
          select: "inputWriteBackSign",
          input: "inputWriteBackInr"
        },
        financialCharges: {
          select: "inputFinancialChargesSign",
          input: "inputFinancialChargesInr"
        },
        forexLossGain: {
          select: "inputForexLossGainSign",
          input: "inputForexLossGainInr"
        },
        totalTds: {
          select: "inputTotalTdsSign",
          input: "inputinputTotalTdsInr"
        }
      };

      const sections = ["roundOff", "writeBack", "financialCharges", "forexLossGain", "totalTds"];

      // four function for adjust modal functionality

      function gatherData(invId) {
        let inputData = {};
        sections.forEach((section, index) => {
          const selectBoxId = `${inpFields[section].select}`;
          const inputFieldId = `${inpFields[section].input}`;

          const selectBoxValue = $(`#${selectBoxId}`).val();
          const inputFieldValue = parseFloat($(`#${inputFieldId}`).val()) || 0;

          inputData[section] = {
            selectBox: selectBoxValue,
            value: inputFieldValue,
          };
        });
        modalInputArray[invId] = inputData;
        addHiddenFields(invId);
      }

      function putModalData(invId) {
        if (!modalInputArray[invId]) {
          console.error(`No data found for invId: ${invId}`);
          return;
        }

        const inputData = modalInputArray[invId];

        sections.forEach(section => {
          const selectBoxId = inpFields[section].select;
          const inputFieldId = inpFields[section].input;

          const sectionData = inputData[section];

          if (sectionData) {
            $(`#${selectBoxId}`).val(sectionData.selectBox);
            $(`#${inputFieldId}`).val(sectionData.value);
          } else {
            console.warn(`No data found for section: ${section}`);
          }
        });
      }

      function clearModalFields() {
        sections.forEach(section => {
          const selectBoxId = inpFields[section].select;
          const inputFieldId = inpFields[section].input;

          if ($(`#${selectBoxId}`).length) {
            $(`#${selectBoxId}`).prop('selectedIndex', 0); // First option
          } else {
            console.warn(`Select box with ID ${selectBoxId} not found.`);
          }

          // Set the input box value to zero
          if ($(`#${inputFieldId}`).length) {
            $(`#${inputFieldId}`).val(0); // Set to 0
          } else {
            console.warn(`Input field with ID ${inputFieldId} not found.`);
          }
        });
      }

      // it  will add data to hidden fields for main array building
      function addHiddenFields(invId) {
        const data = modalInputArray[invId];

        if (!data) {
          console.error(`No data found for invId: ${invId}`);
          return;
        }

        const inputPatterns = {
          roundOff: ["inputRoundOffWithSign_", "inputRoundOffInrWithSign_"],
          forexLossGain: ["inputForexLossGainWithSign_", "inputForexLossGainInrWithSign_"],
          financialCharges: ["inputFinancialChargesWithSign_", "inputFinancialChargesInrWithSign_"],
          writeBack: ["inputWriteBackWithSign_", "inputWriteBackInrWithSign"],
          totalTds: ["inputTotalTdsWithSign_"],
        };

        $.each(data, (key, {
          selectBox,
          value
        }) => {

          let selectValue = `${value}`;
          if (value != 0) {
            selectValue = `${selectBox}${value}`;
          }

          const patterns = inputPatterns[key] || [];

          patterns.forEach(pattern => {
            const inputId = `${pattern}${invId}`;
            const $inputField = $(`#${inputId}`);
            if ($inputField.length) {
              $inputField.val(selectValue);
            } else {
              console.warn(`Input field not found: ${inputId}`);
            }
          });
        });

      }

      // all events for Object to track previous values for each input
      let previousValues = {};

      $.each(inpFields, function(key, fields) {
        $(document).on("keyup", "#" + fields.input, function() {
          let id = $("#modalInvId").val();
          gatherData(id);
          calculateAdjustmentModalTotalAmount(id, fields.input);
        });

        $(document).on("change", "#" + fields.select, function() {
          let id = $("#modalInvId").val();
          gatherData(id);
          calculateAdjustmentModalTotalAmount(id, fields.select);
        });
      });

      //
      function calculateAdjustmentModalTotalAmount(id, fieldChanged) {
        // Initialize previous value if not already set
        if (!previousValues[id]) previousValues[id] = {};

        // Get current values from hidden inputs (convert strings to numbers safely)
        let roundOff = helperAmount(document.getElementById(`inputRoundOffWithSign_${id}`).value) || 0;
        let writeBack = helperAmount(document.getElementById(`inputWriteBackWithSign_${id}`).value) || 0;
        let financialCharges = helperAmount(document.getElementById(`inputFinancialChargesWithSign_${id}`).value) || 0;
        let forexLossGain = helperAmount(document.getElementById(`inputForexLossGainWithSign_${id}`).value) || 0;
        let totalTds = helperAmount(document.getElementById(`inputTotalTdsWithSign_${id}`).value) || 0;
        let currentCurrencyRate = $(`#inputCurrentCurrencyRate_${id}`).val();
        let companyCurrencyName = $(`#inputCompanyCurrencyName_${id}`).val();
        let invoiceCurrencyName = $(`#inputInvoiceCurrencyName_${id}`).val();

        let newRemainAmt = 0;
        //Only for


        // Calculate the total adjustment amount
        let totalAmount = helperAmount(roundOff + writeBack + financialCharges + forexLossGain + totalTds);

        // Get the modal remaining amount
        let modalRemainAmt = helperAmount($('#modalRemainAmt').html()) || 0;

        // Adjust modal remaining amount based on the field that changed
        let previousTotalAmount = helperAmount(previousValues[id].totalAmount) || 0;
        if (totalTds > 0) {
          modalRemainAmt = helperAmount($(`#dueAmt_${id} p`).text().trim()) || 0;
          newRemainAmt = helperAmount(modalRemainAmt - totalTds);

        } else {
          newRemainAmt = helperAmount(modalRemainAmt - previousTotalAmount + totalAmount);
        }

        // Update the adjusted invoice amount
        // console.log("currentCurrencyRate" + currentCurrencyRate);

        $(`#inputInvoiceAdjustAmt_${id}`).val(decimalAmount(totalAmount * currentCurrencyRate));
        if (companyCurrencyName != invoiceCurrencyName) {
          $(`#spanInvoiceAdjustAmt_${id}`).html(`${companyCurrencyName}: ${decimalAmount(totalAmount)}`);
        }
        // // Update the modal remaining amount
        // totalTds = parseFloat(totalTds) || 0;
        // newRemainAmt = parseFloat(newRemainAmt) || 0;


        $('#modalRemainAmt').html(decimalAmount(newRemainAmt));

        // Store the current total amount as the previous value

        if (totalTds > 0) {
          previousValues[id].tds = totalTds;
        } else {
          previousValues[id].totalAmount = totalAmount;
        }

        calCulateTotalAdjustAmount(id);
        calDuePercentage(id);
      }

      // check recive amount validation
      $(document).on("keyup", ".receiveAmt", function() {

        if ($(this).val() != "") {
          let value = helperAmount($(this).val());
          let thisDueAmount = helperAmount($(this).data("dueamt"));
          let thisInvAmount = helperAmount($(this).data("invamt"));

          if (!isNaN(value) && !isNaN(thisDueAmount) && !isNaN(thisInvAmount)) {
            if (value > thisDueAmount || value > thisInvAmount) {
              Swal.fire({
                icon: "warning",
                title: "Please enter a valid amount",
                showConfirmButton: true,
                timer: 1000,
              });
              $(this).val('');
            }
          } else {
            Swal.fire({
              icon: "warning",
              title: "Please enter a valid amount",
              showConfirmButton: true,
              timer: 1000,
            });
            $(this).val('');
          }
        }
      });

      function calCulateTotalAdjustAmount(rowId) {

        let collectTotalAmt = helperAmount($(".collectTotalAmt").val()) ? helperAmount($(".collectTotalAmt").val()) : 0;
        let totalDueAmt = 0;
        let totalRecAmt = 0;
        let totalAdjustAmt = 0;

        $(".receiveAmt").each(function() {
          totalRecAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
        });
        $(".inputInvoiceAdjustAmt").each(function() {
          totalAdjustAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
        });

        let adjustAmt = helperAmount(totalRecAmt + totalAdjustAmt);
        let remaintTotalAmt = helperAmount(collectTotalAmt - adjustAmt);

        if (adjustAmt > collectTotalAmt) {
          $(".remaningAmt").text(decimalAmount(collectTotalAmt));
          $(".remaningAmtInp").val(decimalAmount(collectTotalAmt));
          $("#submitCollectPaymentBtn").prop("disabled", true);
          $("#greaterMsg").show();
        } else {
          $(".remaningAmt").text(decimalAmount(remaintTotalAmt));
          $(".remaningAmtInp").val(decimalAmount(remaintTotalAmt));
          $("#submitCollectPaymentBtn").prop("disabled", false);
          $("#greaterMsg").hide();
        }

      }

      function calDuePercentage(rowId) {
        // Fetch values
        let recAmt = helperAmount($(`#receiveAmt_${rowId}`).val().replace(/,/g, ''));
        let invoiceAmt = helperAmount($(`#invoiceAmt_${rowId}`).text().replace(/,/g, ''));
        let dueAmtText = helperAmount($(`#dueAmt_${rowId}`).text().replace(/,/g, '')); // Remove commas
        let dueAmt = (helperAmount(dueAmtText) > 0) ? helperAmount(dueAmtText) : 0;
        let adjustAmt = (helperAmount($(`#inputInvoiceAdjustAmt_${rowId}`).val()) > 0) ? helperAmount($(`#inputInvoiceAdjustAmt_${rowId}`).val()) : 0;

        // Calculate total received amount (received + adjustments)
        let totalRecv = helperAmount(recAmt + adjustAmt);

        // Calculate the due amount after considering the received amount and adjustments
        let adjustedDueAmt = helperAmount(dueAmt - totalRecv);
        // Calculate the due percentage
        let duePercentage = helperQuantity((adjustedDueAmt / invoiceAmt) * 100);

        // Round the due percentage to two decimal places for clarity
        // duePercentage = Math.round(duePercentage * 100) / 100;

        // Display the result
        $(`#duePercentage_${rowId}`).text(`${duePercentage}%`);
      }

      /*
          ------------ DN related scripts-------------
      */

      $(".invTable").show();
      $(".dnTable").hide();

      $(document).on("click", "#invListBtn", function() {
        $(".invTable").show();
        $(".dnTable").hide();
      });

      $(document).on("click", "#dnListTable", function() {
        let custSelect = $("#customerSelect").val();
        if (loadDn == 0 && custSelect != 0 && custSelect != undefined && custSelect !== null && custSelect != '' && custSelect != 0) {
          debouceFlagDebit = true;
          loadDebitNoteData();
        }
        loadDn++;
        $(".invTable").hide();
        $(".dnTable").show();
      });

      // load function for infinite scrolling
      function loadDebitNoteData() {
        let loadLimit = 10;
        if (debouceFlagDebit) {
          $.ajax({
            type: "GET",
            url: `ajaxs/so/ajax-invoice-customer-list-fetch.php`,
            data: {
              act: "debitNoteData",
              id: custId,
              limit: loadLimit,
              page: pageDebit,
            },
            beforeSend: function() {
              debouceFlagDebit = false;
            },
            success: function(res) {

              try {
                const response = JSON.parse(res);
                console.log(response);

                if (response.status == "success") {
                  const data = response.data;
                  appendDebitNoteData(data);
                  pageDebit++;
                  if (response.numRows == loadLimit) {
                    debouceFlagDebit = true;
                  }
                } else {
                  let tableBody = $('#dnDetailsBody');
                  let tr = `<td colspan='4'><p>No Debit Note </p></td>`;
                  tableBody.append(tr);
                }

              } catch (error) {
                console.error(error);
                // console.log(res);

              }


            }
          });
        }
      }

      // append debit note data
      function appendDebitNoteData(dnData) {
        const tableBody = $('#dnDetailsBody');
        let rows = '';
        dnData.forEach(row => {
          const inputHidden = `
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][invoiceId]" value="${row.dr_note_id}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][invoiceNo]" value="${row.debit_note_no}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][invAmt]" value="${row.total}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][type]" value="dn">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][dueAmt]" id="dueAmount_${sl_no}" value="${decimalAmount(row.due_amount)}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][customer_id]" value="${custId}">

            
                    
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputRoundOffInrWithSign]" id="inputRoundOffInrWithSign_${row.dr_note_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputRoundOffWithSign]" id="inputRoundOffWithSign_${row.dr_note_id}" value="0">

                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputWriteBackInrWithSign]" id="inputWriteBackInrWithSign${row.dr_note_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputWriteBackWithSign]" id="inputWriteBackWithSign_${row.dr_note_id}" value="0">
                    
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputFinancialChargesWithSign]" id="inputFinancialChargesWithSign_${row.dr_note_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputFinancialChargesInrWithSign]" id="inputFinancialChargesInrWithSign_${row.dr_note_id}" value="0">

                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputForexLossGainInrWithSign]" id="inputForexLossGainInrWithSign_${row.dr_note_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputForexLossGainWithSign]" id="inputForexLossGainWithSign_${row.dr_note_id}" value="0">

                    <input type="hidden" class="alltdsamt" name="paymentInvDetails[${custId}][${sl_no}][inputTotalTdsWithSign]" id="inputTotalTdsWithSign_${row.dr_note_id}" value="0">                    
                    
                    <input type="hidden" id="inputPreviousCurrencyRate_${row.dr_note_id}" value="0">
                    <input type="hidden" id="inputCurrentCurrencyRate_${row.dr_note_id}" value="0">
                    <input type="hidden" id="inputInvoiceCurrencyName_${row.dr_note_id}" value="0">
                    <input type="hidden" id="inputCompanyCurrencyName_${row.dr_note_id}" value="0">
                 
                    `;


          let duePercentange = helperQuantity(((row.due_amount ?? 0) / (row.total ?? 1)) * 100);
          let inDiv = '';
          let actBtn = '';

          if (row.journal_id == null || row.journal_id == '' || row.journal_id == 0) {
            inDiv = `
                <div class="input-group-prepend">
                  <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">❌</span>
                </div>
                      `;
            inputRow = `<input readonly type="text" class="form-control receiveAmt px-3 text-right inputAmountClass" style="background-color: #c6e5d4 !important;" placeholder="Accounting Document Not Found" aria-label="Username" aria-describedby="basic-addon1">`;

          } else {

            if (row.due_amount <= 0) {
              inDiv = `
                <div class="input-group-prepend">
                  <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">✅</span>
                </div>
                      `;
              inputRow = `<input readonly type="text" class="form-control receiveAmt px-3 text-right inputAmountClass" style="background-color: #c6e5d4 !important;" placeholder="No Due Payment" aria-label="Username" aria-describedby="basic-addon1">`;
            } else {
              inDiv = `          
              <div class="input-group-prepend">
                <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">₹</span>
              </div>
                  `;
              // inputRow = `<input type="text" name="paymentInvDetails[${custId}][${sl_no}][recAmt]" class="form-control receiveAmt px-3 text-right" id="receiveAmt_${row.dataObj.so_invoice_id}" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1" data-id="${row.so_invoice_id}" data-dueamt="${row.dataObj.due_amount}" data-invamt="${row.dataObj.all_total_amt}">`;
              inputRow = `<input type="text" name="paymentInvDetails[${custId}][${sl_no}][recAmt]" class="form-control receiveAmt px-3 text-right inputAmountClass" id="receiveAmt_${row.dr_note_id}" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1" data-id="${row.dr_note_id}" data-type="dn" data-dueamt="${decimalAmount(row.due_amount)}" data-invamt="${decimalAmount(row.total)}">`;
            }
          }

          rows += `
             <tr>
                  ${inputHidden}
                  <td><p class="text-center">${row.debit_note_no}</p></td>
                  <td><p class="text-center">${row.status}</p></td>
                  <td class="invAmt invoiceAmt text-right" id="invoiceAmt_${row.dr_note_id}" data-type="dn"><p class="text-right">${decimalAmount(row.total)}</p></td>
                  <td class="dueAmt" id="dueAmt_${row.dr_note_id}"><p class="text-right">${decimalAmount(row.due_amount)}</p></td>
                  <td><div class="input-group enter-amount-input m-0">${inDiv}${inputRow}</div></td>
                  <td class="duePercentage text-right" id="duePercentage_${row.dr_note_id}"><p class="text-center">${duePercentange}%</p></td>
                  <td>
                      ${actBtn}
                  </td>
            </tr>`;
          sl_no++;
        });

        tableBody.append(rows);



      }

      /*
      
            ------------ collect payment form js end -----------------------------

      */

      // 🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁
      // console.log("okurl");

      const urlParams = new URLSearchParams(window.location.search);

      // console.log("ok");
      console.log(urlParams);
      // console.log("ok");

      // Check if the "customerId" parameter exists
      if (urlParams.has('customerId')) {
        // "customerId" parameter exists in the URL
        const customerId = urlParams.get('customerId');
        console.log('customerId exists:', customerId);
        collectPaymentMultiInvoice(customerId);
      } else {
        // "customerId" parameter does not exist in the URL
        console.log('customerId does not exist');
      }
      // 🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁
      // collectPayment multi invoice
      function collectPaymentMultiInvoice(customerSelect) {
        $.ajax({
          type: "POST",
          url: `ajaxs/so/ajax-invoice-customer-selected.php`,
          data: {
            customerSelect,
            invoiceArray: <?= json_encode($invoiceArray) ?>,
            customerId: <?= $customerId ?? 0 ?>
          },
          beforeSend: function() {
            $(".inputTableRow").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            $(".inputTableRow").html(response);

            let total_overdue_amount = helperAmount($(".total_overdue_amount").val());
            let total_due_amount = helperAmount($(".total_due_amount").val());
            let total_outstanding_amount = helperAmount($(".total_outstanding_amount").val());

            $(".total_outstanding_amount1").text(decimalAmount(total_outstanding_amount));
            $(".total_due_amount1").text(decimalAmount(total_due_amount));
            $(".total_overdue_amount1").text(decimalAmount(total_overdue_amount));

            calculateDueAmt();
            let advancedPayAmt = helperAmount($(".advancedPayAmt").text()) ? helperAmount($(".advancedPayAmt").text()) : 0;
            $(".remaningAmt").html(decimalAmount(advancedPayAmt));
            console.log('first', advancedPayAmt);
            let collectTotalAmt = helperAmount($(".collectTotalAmt").val()) ? helperAmount($(".collectTotalAmt").val()) : 0;
            if (collectTotalAmt <= 0 || collectTotalAmt === "") {
              $("#submitCollectPaymentBtn").prop("disabled", true);
            } else {
              $("#submitCollectPaymentBtn").prop("disabled", false);
            }
            $(".collectTotalAmt").val("");
          }
        });
      }


      $(document).on("input keyup paste blur", ".inputAmountClass", function() {
        let val = $(this).val();
        let base = <?= $decimalValue ?>;
        // Allow only numbers and one decimal point
        if (val.includes(".")) {
          let parts = val.split(".");
          if (parts[1].length > base) {
            $(this).val(parts[0] + "." + parts[1].substring(0, base)); // Restrict extra decimals
          }
        }
      });

      /*
        collection old js that uses start
      */

      // imranali59059💰💰💰💰💰💰💰💰💰💰💰💰💰
      // collect payment Amount  key up event on collecction total
      $(document).on("keyup", ".collectTotalAmt", function() {
        let thisAmt = (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
        let advancedPayAmt = helperAmount($(".advancedPayAmt").text()) ? helperAmount($(".advancedPayAmt").text()) : 0;
        let rem = helperAmount(thisAmt + advancedPayAmt) ? helperAmount(thisAmt + advancedPayAmt) : 0;
        let collectTotalAmt = helperAmount($(".collectTotalAmt").val()) ? helperAmount($(".collectTotalAmt").val()) : 0;
        let totalReceiveAmt = 0;
        let totalTdsAmt = 0;
        staticRemain = rem;

        $(".receiveAmt").each(function() {
          totalReceiveAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
        });
        $(".alltdsamt").each(function() {
          totalTdsAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
        });

        if (thisAmt < totalReceiveAmt) {
          $("#submitCollectPaymentBtn").prop("disabled", true);
        } else {
          $("#submitCollectPaymentBtn").prop("disabled", false);

          $(".remaningAmt").text(decimalAmount(rem - totalReceiveAmt - totalTdsAmt));

          if (collectTotalAmt <= 0 || collectTotalAmt === "") {
            $("#submitCollectPaymentBtn").prop("disabled", true);
          } else {
            $("#greaterMsg").hide();
            $("#submitCollectPaymentBtn").prop("disabled", false);
          }

        }
        // $(".dueAmt").each(function() {
        //   let recVal = (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
        //   if(thisAmt > recVal){

        //   }else{

        //   }
        // });

      });
      // received payment amount🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢 
      $(document).on("keyup", ".receiveAmt", function() {
        let rowId = ($(this).attr("id")).split("_")[1];
        let recAmt = (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
        let invoiceAmt = helperAmount($(`#invoiceAmt_${rowId}`).text());
        let dueAmtText = helperAmount($(`#dueAmt_${rowId}`).text().replace(/,/g, '')); // Remove commas
        let dueAmt = (helperAmount(dueAmtText) > 0) ? helperAmount(dueAmtText) : 0;
        let adjustAmt = (helperAmount($(`#inputInvoiceAdjustAmt_${rowId}`).val()) > 0) ? helperAmount($(`#inputInvoiceAdjustAmt_${rowId}`).val()) : 0;

        (dueAmtText > 0) ? (dueAmtText) : 0;
        // alert("dueAmt"+dueAmt);
        // alert("invoiceAmt"+invoiceAmt);
        // alert("recAmt"+recAmt);
        // alert($(`#dueAmt_${rowId}`).text());
        // alert(parseFloat(dueAmtText))

        let collectTotalAmt = helperAmount($(".collectTotalAmt").val()) ? helperAmount($(".collectTotalAmt").val()) : 0;
        let remaningAmt = helperAmount($(".remaningAmt").text());

        var totalDueAmt = 0;
        var totalRecAmt = 0;
        var totalAdjustAmt = 0;

        // alert("recAmt+adjustAmt"+(adjustAmt+recAmt));


        let duePercentage = helperQuantity(((dueAmt - (recAmt + (adjustAmt))) / (invoiceAmt)) * 100);
        $(`#duePercentage_${rowId}`).text(`${duePercentage}%`);

        $(".receiveAmt").each(function() {
          totalRecAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
        });

        $(".inputInvoiceAdjustAmt").each(function() {
          totalAdjustAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
        });

        // console.log("totalAdjustAmt"+totalAdjustAmt)
        let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
        let rem = helperAmount(collectTotalAmt + advancedPayAmt);
        staticRemain = rem;
        // let remaintTotalAmt = parseFloat(collectTotalAmt) - parseFloat(totalRecAmt);
        let remaintTotalAmt = helperAmount(staticRemain - totalRecAmt + totalAdjustAmt);
        // calDuePercentage(rowId);
        if (totalRecAmt > collectTotalAmt) {
          console.log('over');
          $(".remaningAmt").text(decimalAmount(collectTotalAmt));
          $(".remaningAmtInp").val(decimalAmount(collectTotalAmt));
          $("#submitCollectPaymentBtn").prop("disabled", true);
          $("#greaterMsg").show();
        } else {
          console.log('ok');
          $(".remaningAmt").text(decimalAmount(remaintTotalAmt));
          $(".remaningAmtInp").val(decimalAmount(remaintTotalAmt));
          $("#submitCollectPaymentBtn").prop("disabled", false);
          $("#greaterMsg").hide();
        }
        console.log('due amt', dueAmt, recAmt);
        if (recAmt <= dueAmt) {
          $(`#warningMsg_${rowId}`).hide();
        } else {
          $(`#warningMsg_${rowId}`).show();
        }
      });

      function checkValues() {
        let tdsTotal = 0;
        let colleTotal = 0;

        let tdsGreaterThanZero = false;
        let colleZero = true;

        $('.alltdsamt').each(function() {
          let value = parseFloat($(this).val()) || 0; // Convert to number, default to 0
          tdsTotal += value;
          if (value > 0) {
            tdsGreaterThanZero = true;
          }
        });

        $('.receiveAmt').each(function() {
          let value = parseFloat($(this).val()) || 0;
          colleTotal += value;
          if (value > 0) {
            colleZero = false; // If any value > 0, set to false
          }
        });

        console.log("Total TDS Value:", tdsTotal);
        console.log("Total Colle Value:", colleTotal);

        if (tdsGreaterThanZero && colleZero) {
          let bankSelect = $("[name='paymentDetails[bankId]']");
          bankSelect.val("0"); // Set value to 0
          bankSelect.prop("disabled", true);
        }
      }



      // main submit button for last modal

      $("#submitCollectPaymentBtn").on("click", function() {
        checkValues();
        var isChecked = $('#round_off_checkbox').is(':checked');
        let collectTotalAmt = helperAmount($(".collectTotalAmt").val()) ? helperAmount($(".collectTotalAmt").val()) : 0;
        let adjustedCollectAmountInp = helperAmount($(".adjustedCollectAmountInp").val()) ? helperAmount($(".adjustedCollectAmountInp").val()) : 0;
        let totalRecAmt2 = 0;
        let totalAdjustAmt = 0;
        let totalRec = 0;
        let advancedPayAmt2 = helperAmount($(".advancedPayAmt").text()) ? helperAmount($(".advancedPayAmt").text()) : 0;

        if (isChecked) {


          $(".receiveAmt").each(function() {
            totalRecAmt2 += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
          });

          $(".inputInvoiceAdjustAmt").each(function() {
            totalAdjustAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
          });

          totalRec = helperAmount(totalRecAmt2 + totalAdjustAmt);


          let totalCaptureAmt = helperAmount(adjustedCollectAmountInp + advancedPayAmt2 - totalRec);
          $("#totalReceiveAmt").text(`${companyCurrency} ${decimalAmount(totalRec)}`);
          $("#totalCaptureAmt").text(`${companyCurrency} ${decimalAmount(totalCaptureAmt)}`);
          $(".advancedPayAmt").val(totalCaptureAmt);
        } else {
          $(".receiveAmt").each(function() {
            totalRecAmt2 += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
          });

          $(".inputInvoiceAdjustAmt").each(function() {
            totalAdjustAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
          });

          totalRec = helperAmount(totalRecAmt2 + totalAdjustAmt);
          let totalCaptureAmt = helperAmount(collectTotalAmt + advancedPayAmt2 - totalRec);
          $("#totalReceiveAmt").text(`${companyCurrency} ${decimalAmount(totalRec)}`);
          $("#totalCaptureAmt").text(`${companyCurrency} ${decimalAmount(totalCaptureAmt)}`);
          $(".advancedPayAmt").val(decimalAmount(totalCaptureAmt));

        }

        if (totalCaptureAmt === 0) {
          $(".totalCaptureAmtDiv").hide();
        } else {
          $(".totalCaptureAmtDiv").show();
        }
      });

      /*
        collection old js that uses end
      */

      /*
        collection vendor Modal js that start
      */
      $(document).ready(function() {
        $("#collectionModal").hide();

        $("#collectVendor").on("click", function() {
          $("#collectionModal").fadeIn();
        });

        $("#closeModalBtn").on("click", function() {
          $("#collectionModal").fadeOut();
          $('#collectvendorForm input, #collectvendorForm select').removeClass('is-invalid');
          $('#collectvendorForm')[0].reset();
        });

        $("#collectionModal").on("click", function(event) {
          if (event.target === this) {
            $("#collectionModal").fadeOut();
            $('#collectvendorForm input, #collectvendorForm select').removeClass('is-invalid');
            $('#collectvendorForm')[0].reset();
          }
        });

        $("#collectVendorAmount").on("input", function() {
          let value = helperAmount($(this).val());
          if (value < 0) {
            alert("Amount cannot be negative!");
            $(this).val("");
          }
        });

        $(document).on("click", "#collectFromVendorBtn", function(e) {
          e.preventDefault();
          let isValid = true;

          $('#collectvendorForm input, #collectvendorForm select').each(function() {
            if ($(this).val() === "" || $(this).val() === null) {
              $(this).addClass("is-invalid");
              isValid = false;
            } else {
              $(this).removeClass("is-invalid");
            }
          });

          if (!isValid) {
            Swal.fire({
              icon: 'warning',
              title: 'Kindly fill in all the fields.',
              showConfirmButton: true,
            });
            return;
          }


          $(this).prop("disabled", true).text("waiting...");
          let formData = $('#collectvendorForm').serialize();

          $.ajax({
            url: 'ajaxs/ajax-collect-form-vendor.php',
            type: 'POST',
            data: formData,
            success: function(response) {
              try {
                let res = JSON.parse(response);
                if (res.status = "success") {
                  Swal.fire({
                    icon: res.status,
                    title: res.message,
                    timer: 3000,
                    showConfirmButton: false,
                  }).then(() => {
                    location.reload();
                  })
                }

              } catch (error) {
                console.log(response);

              }
            },
            error: function(xhr, status, error) {
              console.log('Error: ' + error);
            }
          });

        });


      });


      /*
        collection vendor Modal js that start
      */

      // ******************************************************************
      $(document).on("click", ".paymentSettlement", function() {
        let inv_id = ($(this).attr("id")).split("_")[1];
        advancedAmtInpFunc(inv_id);
        console.log('inv_id');
        console.log(inv_id);
      });

      function advancedAmtInpFunc(inv_id) {
        console.log("called")
        var payment_id = "";
        $(document).on("keyup", `.inv-${inv_id}-advancedAmtInp`, function() {
          payment_id = ($(this).attr("id")).split("_")[1];
          let enterAdvAmt = helperAmount($(this).val()) > 0 ? helperAmount($(this).val()) : 0;
          // console.log("EnterAmt is this " +enterAdvAmt)
          let staticAdvancedAmtInp = helperAmount($(`#inv-${inv_id}-staticAdvancedAmtInp_${payment_id}`).val()) > 0 ? helperAmount($(`#inv-${inv_id}-staticAdvancedAmtInp_${payment_id}`).val()) : 0;
          // console.log("staticAdvancedAmtInp" + staticAdvancedAmtInp)
          let sumAdv = helperAmount(staticAdvancedAmtInp - enterAdvAmt);
          // console.log("sum adv is " + sumAdv)
          let dueAmtOnModalStatic = helperAmount($(`.inv-${inv_id}-dueAmtOnModalStatic`).val());
          // console.log("dueAmtOnModalStatic  is this " + dueAmtOnModalStatic)
          let totalEnterAdvAmt = 0;

          if (enterAdvAmt > staticAdvancedAmtInp) {
            $(`#inv-${inv_id}-advancedAmtSpan_${payment_id}`).html(decimalAmount(staticAdvancedAmtInp));
            $(`#inv-${inv_id}-advancedAmtMsg_${payment_id}`).text(`Enter correct value`);
            $(this).val('');
          } else {
            $(`#inv-${inv_id}-advancedAmtSpan_${payment_id}`).html(decimalAmount(sumAdv));
          }

          $(`.inv-${inv_id}-advancedAmtInp`).each(function() {
            totalEnterAdvAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
          });

          let itemDueAmt = helperAmount($(`#dueAmt_${inv_id}`).html());
          let dueAmtOnModalCal = helperAmount(itemDueAmt - totalEnterAdvAmt);
          console.log("due amt on modal cal is " + dueAmtOnModalCal)
          if (dueAmtOnModalStatic < totalEnterAdvAmt) {
            console.log("dueamt logic called")
            $(`#dueAmtAdvancedAmtMsg_${inv_id}`).text(`Enter correct value`);
            $(`.inv-${inv_id}-dueAmtOnModal`).text(0);
            $(`#invoiceAddBtn_${inv_id}`).attr('disabled', 'disabled');
          } else {
            $(`#dueAmtAdvancedAmtMsg_${inv_id}`).text('');
            $(`.inv-${inv_id}-dueAmtOnModal`).text(decimalAmount(dueAmtOnModalCal));
            $(`#invoiceAddBtn_${inv_id}`).removeAttr("disabled");
          }
          $(`#receiveAmt_${inv_id}`).val(decimalAmount(totalEnterAdvAmt));
          setTimeout(() => {
            $(`#inv-${inv_id}-advancedAmtMsg_${payment_id}`).hide();
          }, 3000);
        });

      }

      // *********************************************************************
      $(document).on("click", `.invoiceAddBtn`, function() {
        let inv_id = $(this).val();
        // alert(inv_id);
        let customerId = $(`#customerId_${inv_id}`).val();
        let payments = [];
        let paymentAmt = 0;
        let i = 0;
        $(`.inv-${inv_id}-advancedAmtInp`).each(function() {
          var paymentId = $(this).data('advancedid');
          paymentAmt += (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
          let payAmt = (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
          payments[paymentId] = payAmt;
        });

        if (paymentAmt == 0) {
          $(`#dueAmtAdvancedAmtMsg_${inv_id}`).text(`Please enter amount`);
        } else {

          payments = JSON.stringify(payments);
          $.ajax({
            type: "POST",
            url: `ajaxs/so/ajax-invoice-settlement.php`,
            data: {
              payments,
              inv_id,
              paymentAmt,
              customerId
            },
            beforeSend: function() {
              $(`#invoiceAddBtn_${inv_id}`).html(`Posting...`);
              $(`#invoiceAddBtn_${inv_id}`).attr('disabled', 'disabled');
            },
            success: function(response) {
              let data = JSON.parse(response);
              console.log(data);
              $(`#postMsg_${inv_id}`).html(data.message);
              $(`#invoiceAddBtn_${inv_id}`).html(`POST`);
              adjustPayment(customerId);
            }
          });
        }
        setTimeout(() => {
          $(`#postMsg_${inv_id}`).hide();
          $(`#dueAmtAdvancedAmtMsg_${inv_id}`).html('');
        }, 3000);
      });

      // roundoff function start 👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾 
      $("#round_off_hide").hide();
      $(document).on('change', '.checkbox', function() {
        if (this.checked) {
          $("#round_off_hide").show();
        } else {
          $("#round_off_hide").hide();
        }
      });

      $("#directInvoiceCreationBtn").on("click", function() {
        var isChecked = $('#round_off_checkbox').prop('checked');

        if (isChecked) {
          console.warn('Checkbox is checked.');
          $("#round_off_checkbox").val(1);
        } else {
          console.warn('Checkbox is not checked.');
          $("#round_off_checkbox").val(0);
        }
      });

      function roundofftotal(total_value, sign, roudoff) {
        let final_value = 0;
        if (sign === "+") {
          final_value = total_value + roudoff;
        } else {
          final_value = total_value - roudoff;
        }
        $(".adjustedDueAmt").html(decimalAmount(final_value));
        $(".adjustedCollectAmountInp").val(decimalAmount(final_value));
      }

      $(document).on("keyup", "#round_value", function() {
        let roundValue = (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
        let total_value = (helperAmount($(".collectTotalAmt").val()) > 0) ? helperAmount($(".collectTotalAmt").val()) : 0;
        var sign = $('#round_sign').val();
        roundofftotal(total_value, sign, roundValue);
        $(".roundOffValueHidden").val(decimalAmount(sign + roundValue));
      });
      // roundoff function end 👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾


      // imranali59059 🖼️🖼️🖼️
      // dynamically image upload and show 
      $('#pic').on("change", function(e) {
        let url = $(this).val();
        let img = $('.load_img');
        let tmppath = URL.createObjectURL(e.target.files[0]);
        img.attr('src', tmppath);
        $(".imageUrl").html(url);
      });

      // imranali59059 📁📁📁📁📁📁📁📁📁📁📁📁📁
      // design input type file STYLE
      $('#imageInput').on('change', function() {
        $input = $(this);
        if ($input.val().length > 0) {
          fileReader = new FileReader();
          fileReader.onload = function(data) {
            $('.image-preview').attr('src', data.target.result);
          }
          fileReader.readAsDataURL($input.prop('files')[0]);
          $('.image-button').css('display', 'none');
          $('.image-preview').css('display', 'block');
          $('.change-image').css('display', 'block');
        }
      });

      $('.change-image').on('click', function() {
        $control = $(this);
        $('#imageInput').val('');
        $preview = $('.image-preview');
        $preview.attr('src', '');
        $preview.css('display', 'none');
        $control.css('display', 'none');
        $('.image-button').css('display', 'block');
      });

      // enter btn hit to block submit form  
      $(document).ready(function() {
        $(window).keydown(function(event) {
          if (event.keyCode == 13) {
            event.preventDefault();
            return false;
          }
        });
      });
    });
  </script>

  <script src="<?= BASE_URL; ?>public/validations/collectionValidation.js"></script>

  <!-- collectPayment action script -->

  <script>
    $(document).ready(function() {
      $('#submitCollectPaymentBtn').click(function() {
        let tableData = [];

        // Loop through each table row
        $('table tr').each(function() {
          let rowData = {};

          // Loop through each cell in the row
          $(this).find('td, input').each(function() {
            const inputField = $(this).find('input, select'); // Check for input or select

            if (inputField.length) {
              // If input exists, store its value with a unique key (or name attribute)
              rowData[$(inputField).attr('name') || `field_${tableData.length}`] = $(inputField).val();
            } else {
              // Otherwise, store the cell's text
              const columnKey = $(this).attr('data-key') || `column_${$(this).index()}`;
              rowData[columnKey] = $(this).text().trim();
            }
          });

          if (Object.keys(rowData).length > 0) {
            tableData.push(rowData);
          }
        });

        // Output serialized data to console
        console.log('Serialized Table Data:', tableData);
      });
    });
  </script>