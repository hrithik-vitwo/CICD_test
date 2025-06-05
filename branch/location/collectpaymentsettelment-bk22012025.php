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
require_once("../../app/v1/functions/branch/func-grn-controller.php");

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// add PGI form ➕➕➕➕➕➕➕➕➕➕➕➕➕
$BranchSoObj = new BranchSo();
$grnObj = new GrnController();

$check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];
// console($check_var_sql);
$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];

// imranali59059💰💰💰💰💰💰💰💰💰💰💰💰💰
if (isset($_POST['submitCollectPaymentBtn'])) {

  $addCollectPayment = $BranchSoObj->insertCollectPayment($_POST, $_FILES);
  // console($addCollectPayment);
  // exit();
  if ($addCollectPayment['status'] == "success") {
    swalToast($addCollectPayment["status"], $addCollectPayment["message"], LOCATION_URL . "collect-payment.php");
  } else {
    swalToast($addCollectPayment["status"], $addCollectPayment["message"]);
  }
}

// ✅✅✅✅✅✅✅✅✅✅✅✅✅
$customerList = $BranchSoObj->fetchCustomerList()['data'];
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
</style>
<?php
if (isset($_GET['collect-payment'])) {
?>

  <div class="content-wrapper is-collect-payment">
    <!-- Modal -->
    <div class="modal fade" id="exampleCollectionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleCollectionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
          <div class="modal-header card-header py-2 px-3">
            <h4 class="modal-title font-monospace text-md text-white" id="exampleCollectionModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
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
                <div class="mx-2"><button class="btn btn-success" type="button" id="submitCollectPaymentBtn">Collect</button></div>
              </div>
            </div>
          </div>
          <!-- Collect Payment Modal -->
          <div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Collection</h5>
                  <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="totalPaidAmtDiv"><span style="font-family: 'Font Awesome 5 Free';" id="totalReceiveAmt">0</span> amount received against invoice</div>
                  <div class="totalCaptureAmtDiv"><span style="font-family: 'Font Awesome 5 Free';" id="totalCaptureAmt">0</span> amount captured as an advanced</div>
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
                        <input type="hidden" name="paymentDetails[customerId]" class="form-control" id="customerSelect" value="<?= $customerDetails['customer_id'] ?>" readonly>
                        <input type="text" class="form-control" id="customerSelect" value="<?= $customerDetails['trade_name'] ?>" readonly>
                      <?php } else { ?>
                        <select name="paymentDetails[customerId]" class="select2 form-control" id="customerSelect">
                          <option value="">Select Customer</option>
                          <?php foreach ($customerList as $customer) { ?>
                            <option value="<?= $customer['customer_id'] ?>"><?= $customer['trade_name'] ?>(<?= $customer['customer_code'] ?>)</option>
                          <?php } ?>
                        </select>
                      <?php } ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <label for="" class="label-hidden"></label>
                      <input type="text" name="paymentDetails[collectPayment]" class="form-control collectTotalAmt" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1">
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
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="total_outstanding_amount1">0</span></p>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="totaldueamount">
                        <p class="text-xs">Total Due</p>
                        <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalDueAmt">0</span></p> -->
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="total_due_amount1">0</span></p>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="totaloverdue">
                        <p class="text-xs">Total Overdue</p>
                        <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalOverDueAmt">0</span></p> -->
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="total_overdue_amount1">0</span></p>
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
                      <p class="text-xs text-right font-bold rupee-symbol">₹ <span class="remaningAmt">0</span></p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="image-input">
                        <input type="file" name="paymentDetails[paymentAdviceImg]" accept="image/*" id="imageInput">
                        <label for="imageInput" class="image-button"><i class="fa fa-image po-list-icon mr-2"></i> Upload Payment Advice</label>
                        <img src="" class="image-preview">
                        <span class="change-image float-right mt-3"><button type="button" class=" btn btn-danger"><i class="fa fa-times mr-2"></i>Remove</button></span>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input totalamount">
                        <label for="">Transaction Date</label>
                        <input type="date" name="paymentDetails[documentDate]" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input totaldueamount">
                        <label for="">Posting Date</label>
                        <input type="date" name="paymentDetails[postingDate]" value="<?= $min ?>" min="<?= $min ?>" max="<?= $max ?>" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input totaloverdue">
                        <label for="">Transaction Id / Doc. No.</label>
                        <input type="text" placeholder="Tnx. Id / Doc. No." name="paymentDetails[tnxDocNo]" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <span class="text-xs text-danger float-right" style="display:none" id="greaterMsg">Can't collect the greater amount</span>
            <div class="collectionLineItemsDiv col-lg-12 col-md-12 col-sm-12">
              <input type="hidden" name="paymentDetails[advancedPayAmt]" value="" class="advancedPayAmt">

              <table id="dataTable_detailed_view">
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
  <div class="modal fade right customer-modal classic-view-modal" id="collectActionModal" role="dialog" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" style="max-width: 30%;" role="document">
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
                  <input type="number" step="any" id="inputRoundOffInr" class="form-control border py-3 text-right inputRoundOffInr adjustmentInputValue" placeholder="0.00">
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
                  <input type="number" step="any" id="inputWriteBackInr" class="form-control border py-3 text-right inputWriteBackInr adjustmentInputValue" placeholder="0.00">
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
                  <select id="inputFinancialChargesSign" class="form-control inputFinancialChargesSign adjustmentInputSign">
                    <option value="+"> + </option>
                    <option value="-"> - </option>
                  </select>
                </div>
                <div class="input-group input-group-sm">
                  <input type="number" step="any" id="inputFinancialChargesInr" class="form-control border py-3 text-right inputFinancialChargesInr adjustmentInputValue" placeholder="0.00">
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
                  <input type="number" step="any" id="inputForexLossGainInr" class="form-control border py-3 text-right inputForexLossGainInr" placeholder="0.00">
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
                  <select id="inputTotalTdsSign" class="form-control inputTotalTdsSign">
                    <option value="+"> + </option>
                    <option value="-" selected="selected"> - </option>
                  </select>
                </div>
                <div class="input-group input-group-sm">
                  <input type="number" step="any" id="inputinputTotalTdsInr" class="form-control border py-3 text-right inputinputTotalTdsInr adjustmentInputValue" placeholder="0.00">
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
                          <option value="<?= $customer['customer_id'] ?>"><?= $customer['trade_name'] ?>(<?= $customer['customer_code'] ?>)</option>
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
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="total_outstanding_amount1">0</span></p>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="totaldueamount">
                        <p class="text-xs">Total Due</p>
                        <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalDueAmt">0</span></p> -->
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="total_due_amount1">0</span></p>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="totaloverdue">
                        <p class="text-xs">Total Overdue</p>
                        <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalOverDueAmt">0</span></p> -->
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="total_overdue_amount1">0</span></p>
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
<?php } else {
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
  // initializeDataTable();

  // function initializeDataTable() {
  //   dataTable = $("#dataTable_detailed_view").DataTable({
  //     dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
  //     "lengthMenu": [10, 25, 50, 100, 200, 250],
  //     "ordering": false,
  //     info: false,
  //     "initComplete": function(settings, json) {
  //       $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
  //     },

  //     buttons: [],
  //     // select: true,
  //     "bPaginate": false,

  //   });

  // }


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

    let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
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
      let qtyVal3 = ($(this).attr("id")).split("_")[1];
      let qtyVal = $(this).find(":selected").data("quantity");
      // let qtyVal2 = $(this).find(":selected").data("deliverydate");
      // let qtyVal = $(this).find(":selected").children("span");
      // $( "#myselect option:selected" ).text();
      console.log(qtyVal);
      $(`#itemQty_${qtyVal3}`).val(qtyVal);
    });

    // console.log("ok18")

    function calculateDueAmt() {
      let totalDueAmt = 0;
      let totalInvAmt = 0;
      let overDueAmt = 0;
      $(".dueAmt").each(function() {
        totalDueAmt += (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
      });
      $(".invAmt").each(function() {
        totalInvAmt += (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
      });
      $(".totalDueAmt").html(totalDueAmt);
      $(".totalInvAmt").html(totalInvAmt.toFixed(2));
      $(".totalDueAmtInp").val(totalDueAmt.toFixed(2));
      $(".totalInvAmtInp").val(totalInvAmt.toFixed(2));
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
      } else {
        collectPayment(customerSelect);
      }
    });

    function adjustPayment(customerSelect) {
      $.ajax({
        type: "POST",
        url: `ajaxs/so/ajax-invoice-customer-advanced.php`,
        data: {
          customerSelect
        },
        beforeSend: function() {
          $(".advancedAmtList").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          $(".advancedAmtList").html(response);

          // calculateDueAmt();
          // let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
          // $(".remaningAmt").html(advancedPayAmt);
          console.log('first', response);
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
          $(".inputTableRow").html(response);

          let total_overdue_amount = $(".total_overdue_amount").val();
          let total_due_amount = $(".total_due_amount").val();
          let total_outstanding_amount = $(".total_outstanding_amount").val();

          $(".total_outstanding_amount1").text(total_outstanding_amount);
          $(".total_due_amount1").text(total_due_amount);
          $(".total_overdue_amount1").text(total_overdue_amount);

          calculateDueAmt();
          let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
          $(".remaningAmt").html(advancedPayAmt);
          console.log('first', advancedPayAmt);
        }
      });
    }

    /*
    
          ------------ collect payment form js start -----------------------------

    */

    // all required variables for form 
    let custId
    let debouceFlag = false;
    let pageCollection = 1;
    let sl_no = 0;
    let modalInputArray = [];

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

    // this will trigeer when customer will be selected
    function collectPayment(customerSelect) {
      custId = customerSelect;
      debouceFlag = true;
      sl_no = 0;
      $('#invDetailsBody').html('');

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
              console.log(response);

              if (response.status == "success") {
                const data = response.data;
                const invoiceDataObj = data.invoiceData;
                appendCollectionInv(invoiceDataObj);
                pageCollection++;
                if (response.numRows == loadLimit) {
                  debouceFlag = true;
                }
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

        let duePercentange = ((row.dueAmount ?? 0) / (row.dataObj.all_total_amt ?? 1)) * 100;
        let inDiv = '';
        let inputRow = '';
        let actBtn = '';
        if (row.dueAmount <= 0) {
          inDiv = `
          <div class="input-group-prepend">
            <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">✅</span>
          </div>
          `;
          inputRow = `<input readonly type="text" class="form-control receiveAmt px-3 text-right" style="background-color: #c6e5d4 !important;" placeholder="No Due Payment" aria-label="Username" aria-describedby="basic-addon1">`;
        } else {
          inDiv = `          
          <div class="input-group-prepend">
            <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">₹</span>
          </div>
          `;
          inputRow = `<input type="text" name="paymentInvDetails[${custId}][${sl_no}][recAmt]" class="form-control receiveAmt px-3 text-right" id="receiveAmt_${row.dataObj.so_invoice_id}" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1" data-id="${row.so_invoice_id}" data-dueamt="${row.dataObj.due_amount}" data-invamt="${row.dataObj.all_total_amt}">`;
          actBtn = `<a style="cursor:pointer" data-toggle="modal" class="collectActionModalBtn" data-id="${row.dataObj.so_invoice_id}" data-no="${row.dataObj.invoice_no}" data-dueamt="${row.dueAmount}" data-amount="${row.dataObj.all_total_amt}">
                      <i class="fa fa-cog po-list-icon adjustModal" data-target="#collectActionModal"></i>
                  </a> `;
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
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][invAmt]" value="${row.dataObj.all_total_amt}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][dueAmt]" id="dueAmount_${sl_no}" value="${row.dataObj.due_amount}">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][customer_id]" value="${custId}">

            
                    
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputRoundOffInrWithSign]" id="inputRoundOffInrWithSign_${row.dataObj.so_invoice_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputRoundOffWithSign]" id="inputRoundOffWithSign_${row.dataObj.so_invoice_id}" value="0">

                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputWriteBackInrWithSign]" id="inputWriteBackInrWithSign${row.dataObj.so_invoice_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputWriteBackWithSign]" id="inputWriteBackWithSign_${row.dataObj.so_invoice_id}" value="0">
                    
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputFinancialChargesWithSign]" id="inputFinancialChargesWithSign_${row.dataObj.so_invoice_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputFinancialChargesInrWithSign]" id="inputFinancialChargesInrWithSign_${row.dataObj.so_invoice_id}" value="0">

                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputForexLossGainInrWithSign]" id="inputForexLossGainInrWithSign_${row.dataObj.so_invoice_id}" value="0">
                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputForexLossGainWithSign]" id="inputForexLossGainWithSign_${row.dataObj.so_invoice_id}" value="0">

                    <input type="hidden" name="paymentInvDetails[${custId}][${sl_no}][inputTotalTdsWithSign]" id="inputTotalTdsWithSign_${row.dataObj.so_invoice_id}" value="0">                    
                    
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
            <td><p class="text-center">${row.dataObj.credit_period}</p></td>
            <td class="invAmt invoiceAmt text-right" id="invoiceAmt_${row.dataObj.so_invoice_id}"><p class="text-right">${decimalAmount(row.dataObj.all_total_amt)}</p></td>
            <td class="dueAmt" id="dueAmt_${row.dataObj.so_invoice_id}"><p class="text-right">${decimalAmount(row.dueAmount)}</p></td>
            <td><div class="input-group enter-amount-input m-0">${inDiv}${inputRow}</div></td>
            <td>
              <div class="input-group input-group-sm m-0">
                <div class="input-group-prepend">
                            <span class="input-group-text">${row.dataObj.currency_name}</span>
                </div>
                <input type="number" step="any" id="inputInvoiceAdjustAmt_${row.dataObj.so_invoice_id}" name="" class="form-control border py-3 text-right inputInvoiceAdjustAmt" placeholder="0.00" readonly>
                <span id="spanInvoiceAdjustAmt_${row.dataObj.so_invoice_id}" class="text-small spanInvoiceAdjustAmt"></span>
              </div>
            </td>
            <td class="duePercentage text-right" id="duePercentage_${row.dataObj.so_invoice_id}"><p class="text-center">${Math.round(duePercentange)}%</p></td>
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
      let dueAmt = $(this).data('dueamt');
      let amount = $(this).data('amount');

      $("#modalInvNo").html(no);
      $("#modalInvAmt").html(decimalAmount(amount));
      $("#modalRemainAmt").html(decimalAmount(dueAmt));

      $("#modalInvId").val(id);
      $("#modalDueAmtModal").val(dueAmt);

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
      let roundOff = parseFloat(document.getElementById(`inputRoundOffWithSign_${id}`).value) || 0;
      let writeBack = parseFloat(document.getElementById(`inputWriteBackWithSign_${id}`).value) || 0;
      let financialCharges = parseFloat(document.getElementById(`inputFinancialChargesWithSign_${id}`).value) || 0;
      let forexLossGain = parseFloat(document.getElementById(`inputForexLossGainWithSign_${id}`).value) || 0;
      let totalTds = parseFloat(document.getElementById(`inputTotalTdsWithSign_${id}`).value) || 0;
      let currentCurrencyRate = $(`#inputCurrentCurrencyRate_${id}`).val();
      let companyCurrencyName = $(`#inputCompanyCurrencyName_${id}`).val();
      let invoiceCurrencyName = $(`#inputInvoiceCurrencyName_${id}`).val();



      // Calculate the total adjustment amount
      let totalAmount = roundOff + writeBack + financialCharges + forexLossGain + totalTds;

      // Get the modal remaining amount
      let modalRemainAmt = parseFloat($('#modalRemainAmt').html()) || 0;

      // Adjust modal remaining amount based on the field that changed
      let previousTotalAmount = previousValues[id].totalAmount || 0;
      let newRemainAmt = modalRemainAmt - previousTotalAmount + totalAmount;

      // Update the adjusted invoice amount
      // console.log("currentCurrencyRate" + currentCurrencyRate);
      $(`#inputInvoiceAdjustAmt_${id}`).val((totalAmount * currentCurrencyRate).toFixed(2));
      if (companyCurrencyName != invoiceCurrencyName) {
        $(`#spanInvoiceAdjustAmt_${id}`).html(`${companyCurrencyName}: ${totalAmount.toFixed(2)}`);
      }
      // Update the modal remaining amount
      $('#modalRemainAmt').html(newRemainAmt.toFixed(2));

      // Store the current total amount as the previous value
      previousValues[id].totalAmount = totalAmount;
      calCulateTotalAdjustAmount(id);
      calDuePercentage(id);
    }

    // check recive amount validation
    $(document).on("keyup", ".receiveAmt", function() {
      if ($(this).val() != "") {
        let value = parseFloat($(this).val());
        let thisDueAmount = parseFloat($(this).data("dueamt"));
        let thisInvAmount = parseFloat($(this).data("invamt"));

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

      let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
      let totalDueAmt = 0;
      let totalRecAmt = 0;
      let totalAdjustAmt = 0;

      $(".receiveAmt").each(function() {
        totalRecAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });
      $(".inputInvoiceAdjustAmt").each(function() {
        totalAdjustAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });

      let adjustAmt = parseFloat(totalRecAmt) + parseFloat(totalAdjustAmt);
      let remaintTotalAmt = parseFloat(collectTotalAmt) - adjustAmt;

      if (adjustAmt > collectTotalAmt) {
        $(".remaningAmt").text(collectTotalAmt);
        $(".remaningAmtInp").val(collectTotalAmt);
        $("#submitCollectPaymentBtn").prop("disabled", true);
        $("#greaterMsg").show();
      } else {
        $(".remaningAmt").text(remaintTotalAmt);
        $(".remaningAmtInp").val(remaintTotalAmt);
        $("#submitCollectPaymentBtn").prop("disabled", false);
        $("#greaterMsg").hide();
      }

    }

    function calDuePercentage(rowId) {
      // Fetch values
      let recAmt = parseFloat($(`#receiveAmt_${rowId}`).val().replace(/,/g, ''));
      let invoiceAmt = parseFloat($(`#invoiceAmt_${rowId}`).text().replace(/,/g, ''));
      let dueAmtText = $(`#dueAmt_${rowId}`).text().replace(/,/g, ''); // Remove commas
      let dueAmt = (parseFloat(dueAmtText) > 0) ? parseFloat(dueAmtText) : 0;
      let adjustAmt = (parseFloat($(`#inputInvoiceAdjustAmt_${rowId}`).val()) > 0) ? parseFloat($(`#inputInvoiceAdjustAmt_${rowId}`).val()) : 0;

      // Calculate total received amount (received + adjustments)
      let totalRecv = recAmt + adjustAmt;

      // Calculate the due amount after considering the received amount and adjustments
      let adjustedDueAmt = dueAmt - totalRecv;
      // Calculate the due percentage
      let duePercentage = (adjustedDueAmt / invoiceAmt) * 100;

      // Round the due percentage to two decimal places for clarity
      duePercentage = Math.round(duePercentage * 100) / 100;

      // Display the result
      $(`#duePercentage_${rowId}`).text(`${duePercentage}%`);
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

          let total_overdue_amount = $(".total_overdue_amount").val();
          let total_due_amount = $(".total_due_amount").val();
          let total_outstanding_amount = $(".total_outstanding_amount").val();

          $(".total_outstanding_amount1").text(total_outstanding_amount);
          $(".total_due_amount1").text(total_due_amount);
          $(".total_overdue_amount1").text(total_overdue_amount);

          calculateDueAmt();
          let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
          $(".remaningAmt").html(advancedPayAmt);
          console.log('first', advancedPayAmt);
          let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
          if (collectTotalAmt <= 0 || collectTotalAmt === "") {
            $("#submitCollectPaymentBtn").prop("disabled", true);
          } else {
            $("#submitCollectPaymentBtn").prop("disabled", false);
          }
          $(".collectTotalAmt").val("");
        }
      });
    }

    
    /*
      collection old js that uses start
    */ 

    // imranali59059💰💰💰💰💰💰💰💰💰💰💰💰💰
    // collect payment Amount  key up event on collecction total
    $(document).on("keyup", ".collectTotalAmt", function() {
      let thisAmt = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
      let rem = (parseFloat(thisAmt) + parseFloat(advancedPayAmt)) ? (parseFloat(thisAmt) + parseFloat(advancedPayAmt)) : 0;
      let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
      let totalReceiveAmt = 0;
      staticRemain = rem;

      $(".receiveAmt").each(function() {
        totalReceiveAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });

      if (thisAmt < totalReceiveAmt) {
        $("#submitCollectPaymentBtn").prop("disabled", true);
      } else {
        $("#submitCollectPaymentBtn").prop("disabled", false);

        $(".remaningAmt").text(rem - totalReceiveAmt);

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
      let recAmt = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      let invoiceAmt = $(`#invoiceAmt_${rowId}`).text();
      let dueAmtText = $(`#dueAmt_${rowId}`).text().replace(/,/g, ''); // Remove commas
      let dueAmt = (parseFloat(dueAmtText) > 0) ? parseFloat(dueAmtText) : 0;
      let adjustAmt = (parseFloat($(`#inputInvoiceAdjustAmt_${rowId}`).val()) > 0) ? parseFloat($(`#inputInvoiceAdjustAmt_${rowId}`).val()) : 0;

      (parseFloat(dueAmtText) > 0) ? parseFloat(dueAmtText): 0;
      // alert("dueAmt"+dueAmt);
      // alert("invoiceAmt"+invoiceAmt);
      // alert("recAmt"+recAmt);
      // alert($(`#dueAmt_${rowId}`).text());
      // alert(parseFloat(dueAmtText))

      let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
      let remaningAmt = $(".remaningAmt").text();

      var totalDueAmt = 0;
      var totalRecAmt = 0;
      var totalAdjustAmt = 0;

      // alert("recAmt+adjustAmt"+(adjustAmt+recAmt));


      let duePercentage = ((parseFloat(dueAmt) - (parseFloat(recAmt) + (adjustAmt))) / parseFloat(invoiceAmt)) * 100;
      $(`#duePercentage_${rowId}`).text(`${Math.round(duePercentage,2)}%`);

      $(".receiveAmt").each(function() {
        totalRecAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });

      $(".inputInvoiceAdjustAmt").each(function() {
        totalAdjustAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });

      // console.log("totalAdjustAmt"+totalAdjustAmt)
      let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
      let rem = parseFloat(collectTotalAmt) + parseFloat(advancedPayAmt);
      staticRemain = rem;
      // let remaintTotalAmt = parseFloat(collectTotalAmt) - parseFloat(totalRecAmt);
      let remaintTotalAmt = parseFloat(staticRemain) - (parseFloat(totalRecAmt) + parseFloat(totalAdjustAmt));
      // calDuePercentage(rowId);
      if (totalRecAmt > collectTotalAmt) {
        console.log('over');
        $(".remaningAmt").text(collectTotalAmt);
        $(".remaningAmtInp").val(collectTotalAmt);
        $("#submitCollectPaymentBtn").prop("disabled", true);
        $("#greaterMsg").show();
      } else {
        console.log('ok');
        $(".remaningAmt").text(remaintTotalAmt);
        $(".remaningAmtInp").val(remaintTotalAmt);
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
    // main submit button for last modal
    $("#submitCollectPaymentBtn").on("click", function() {
      var isChecked = $('#round_off_checkbox').is(':checked');
      let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
      let adjustedCollectAmountInp = ($(".adjustedCollectAmountInp").val()) ? ($(".adjustedCollectAmountInp").val()) : 0;
      let totalRecAmt2 = 0;
      let totalAdjustAmt = 0;
      let totalRec=0;
      let advancedPayAmt2 = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;

      if (isChecked) {


        $(".receiveAmt").each(function() {
          totalRecAmt2 += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        });

        $(".inputInvoiceAdjustAmt").each(function() {
        totalAdjustAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
       });
        
        totalRec=totalRecAmt2+totalAdjustAmt;


        let totalCaptureAmt = (parseFloat(adjustedCollectAmountInp) + parseFloat(advancedPayAmt2)) - (parseFloat(totalRec));
        $("#totalReceiveAmt").text(`₹${totalRec}`);
        $("#totalCaptureAmt").text(`₹${totalCaptureAmt}`);
        $(".advancedPayAmt").val(totalCaptureAmt);
      } else {
        $(".receiveAmt").each(function() {
          totalRecAmt2 += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        });
        
        $(".inputInvoiceAdjustAmt").each(function() {
        totalAdjustAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
       });
        
        totalRec=totalRecAmt2+totalAdjustAmt;
        let totalCaptureAmt = (parseFloat(collectTotalAmt) + parseFloat(advancedPayAmt2)) - (parseFloat(totalRec));
        $("#totalReceiveAmt").text(`₹${totalRec}`);
        $("#totalCaptureAmt").text(`₹${totalCaptureAmt}`);
        $(".advancedPayAmt").val(totalCaptureAmt);

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

    // ******************************************************************
    $(document).on("click", ".paymentSettlement", function() {
      let inv_id = ($(this).attr("id")).split("_")[1];
      advancedAmtInpFunc(inv_id);
      console.log('inv_id');
      console.log(inv_id);
    });

    function advancedAmtInpFunc(inv_id) {
      var payment_id = "";
      $(document).on("keyup", `.inv-${inv_id}-advancedAmtInp`, function() {
        payment_id = ($(this).attr("id")).split("_")[1];
        let enterAdvAmt = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        let staticAdvancedAmtInp = (parseFloat($(`#inv-${inv_id}-staticAdvancedAmtInp_${payment_id}`).val()) > 0) ? parseFloat($(`#inv-${inv_id}-staticAdvancedAmtInp_${payment_id}`).val()) : 0;
        let sumAdv = (staticAdvancedAmtInp - enterAdvAmt);
        let dueAmtOnModalStatic = $(`.inv-${inv_id}-dueAmtOnModalStatic`).val();
        let totalEnterAdvAmt = 0;

        if (enterAdvAmt > staticAdvancedAmtInp) {
          $(`#inv-${inv_id}-advancedAmtSpan_${payment_id}`).html(staticAdvancedAmtInp);
          $(`#inv-${inv_id}-advancedAmtMsg_${payment_id}`).text(`Enter correct value`);
          $(this).val('');
        } else {
          $(`#inv-${inv_id}-advancedAmtSpan_${payment_id}`).html(sumAdv);
        }

        $(`.inv-${inv_id}-advancedAmtInp`).each(function() {
          totalEnterAdvAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        });

        let itemDueAmt = $(`#dueAmt_${inv_id}`).html();
        let dueAmtOnModalCal = (itemDueAmt - totalEnterAdvAmt);

        if (dueAmtOnModalStatic < totalEnterAdvAmt) {
          $(`#dueAmtAdvancedAmtMsg_${inv_id}`).text(`Enter correct value`);
          $(`.inv-${inv_id}-dueAmtOnModal`).text(0);
          $(`#invoiceAddBtn_${inv_id}`).attr('disabled', 'disabled');
        } else {
          $(`#dueAmtAdvancedAmtMsg_${inv_id}`).text('');
          $(`.inv-${inv_id}-dueAmtOnModal`).text(dueAmtOnModalCal.toFixed(2));
          $(`#invoiceAddBtn_${inv_id}`).removeAttr("disabled");
        }
        $(`#receiveAmt_${inv_id}`).val(totalEnterAdvAmt);
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
        paymentAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        let payAmt = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
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
      $(".adjustedDueAmt").html(final_value.toFixed(2));
      $(".adjustedCollectAmountInp").val(final_value.toFixed(2));
    }

    $(document).on("keyup", "#round_value", function() {
      let roundValue = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      let total_value = (parseFloat($(".collectTotalAmt").val()) > 0) ? parseFloat($(".collectTotalAmt").val()) : 0;
      var sign = $('#round_sign').val();
      roundofftotal(total_value, sign, roundValue);
      $(".roundOffValueHidden").val(sign + roundValue);
    });
    // roundoff function end 👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾


    // imranali59059 🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️
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