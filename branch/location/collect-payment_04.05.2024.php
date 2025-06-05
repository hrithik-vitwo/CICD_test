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


// imranali59059💰💰💰💰💰💰💰💰💰💰💰💰💰
if (isset($_POST['submitCollectPaymentBtn'])) {
  // console($_POST);
  // console($_FILES);
  $addCollectPayment = $BranchSoObj->insertCollectPayment($_POST, $_FILES);
  // console($addCollectPayment);
  if ($addCollectPayment['status'] == "success") {
    swalToast($addCollectPayment["status"], $addCollectPayment["message"]);
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
    min-height: 100%;
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
                        <select name="paymentDetails[customerId]" class="form-control" id="customerSelect">
                          <option value="">Select Customer</option>
                          <?php foreach ($customerList as $customer) { ?>
                            <option value="<?= $customer['customer_id'] ?>"><?= $customer['trade_name'] ?></option>
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
                        <input type="date" name="paymentDetails[postingDate]" class="form-control" aria-label="Username" aria-describedby="basic-addon1">
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
            <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="inputTableRow mt-3"></div>
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
                      <select name="paymentDetails[customerId]" class="form-control" id="customerSelect">
                        <option value="">Select Customer</option>
                        <?php foreach ($customerList as $customer) { ?>
                          <option value="<?= $customer['customer_id'] ?>"><?= $customer['trade_name'] ?></option>
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
<?php } else { ?>
  <div class="content-wrapper is-collect-payment">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <h3 class="py-3 mb-4">All Invoices</h3>
        <div class="row mb-3">
          <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="d-flex">
              <a href="manage-invoices.php" class="btn active mr-2" style="background: #dbe5ee"><i class="fa fa-stream"></i> Invoices List</a>
              <a href="collect-payment.php" class="btn mr-2" style="background: #003060; color: white;"><i class="fa fa-list"></i> Payment Received List</a>
              <a href="manage-invoices.php?payment-due" class="btn" style="background: #dbe5ee;"><i class="fa fa-list"></i> Due List</a>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 d-flex justify-content-end create-btns">
            <div class="btn-group mr-2">
              <button type="button" class="btn dropdown-toggle btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-plus"></i> Create Invoice
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item small-text" href="direct-create-invoice.php">Goods Invoice</a></li>
                <li><a class="dropdown-item small-text" href="direct-create-invoice.php?create_service_invoice">Service Invoice</a></li>
              </ul>
            </div>
            <div class="btn-group">
              <button type="button" class="btn dropdown-toggle btn-primary" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-plus"></i> Collection
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item small-text" href="?collect-payment">Collect Payment</a></li>
                <li><a class="dropdown-item small-text" href="?adjust-payment">Settlement</a></li>
              </ul>
            </div>
          </div>
        </div>
        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <?php
            $paymentList = $BranchSoObj->fetchCustomerPayments()['data'];
            // console($paymentList);
            ?>
            <div class="card card-tabs" style="border-radius: 20px;">
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-1 col-md-2 col-sm-12">
                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-lg-11 col-md-11 col-sm-12">
                      <div class="row table-header-item">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="filter-search">
                            <div class="section serach-input-section">
                              <input type="text" id="myInput" placeholder="" class="field form-control" />
                              <div class="icons-container">
                                <div class="icon-search">
                                  <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                </div>
                                <div class="icon-close">
                                  <i class="fa fa-search po-list-icon" onclick="javascript:alert('Hello World!')" id="myBtn"></i>
                                  <script>
                                    var input = document.getElementById("myInput");
                                    input.addEventListener("keypress", function(event) {
                                      if (event.key === "Enter") {
                                        event.preventDefault();
                                        document.getElementById("myBtn").click();
                                      }
                                    });
                                  </script>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter
                              Customers</h5>

                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                      echo $_REQUEST['keyword'];
                                                                                                                                                    } ?>">
                              </div>
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <select name="vendor_status_s" id="vendor_status_s" class="fld form-control" style="appearance: auto;">
                                  <option value=""> Status </option>
                                  <option value="active" <?php if (isset($_REQUEST['status_s']) && 'active' == $_REQUEST['status_s']) {
                                                            echo 'selected';
                                                          } ?>>Active
                                  </option>
                                  <option value="inactive" <?php if (isset($_REQUEST['status_s']) && 'inactive' == $_REQUEST['status_s']) {
                                                              echo 'selected';
                                                            } ?>>Inactive
                                  </option>
                                  <option value="draft" <?php if (isset($_REQUEST['status_s']) && 'draft' == $_REQUEST['status_s']) {
                                                          echo 'selected';
                                                        } ?>>Draft</option>
                                </select>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                          echo $_REQUEST['form_date_s'];
                                                                                                                        } ?>" />
                              </div>
                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                          echo $_REQUEST['form_date_s'];
                                                                                                                        } ?>" />
                              </div>
                            </div>

                          </div>
                          <div class="modal-footer">
                            <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync fa-spin"></i>Reset</a>
                            <a type="button" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                              Search</a>
                          </div>
                        </div>
                      </div>
                    </div>

              </form>
              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                  <?php
                  $cond = '';

                  $sts = " AND `vendor_status` !='deleted'";
                  if (isset($_REQUEST['vendor_status_s']) && $_REQUEST['vendor_status_s'] != '') {
                    $sts = ' AND vendor_status="' . $_REQUEST['vendor_status_s'] . '"';
                  }

                  if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                    $cond .= " AND branch_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                  }

                  if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                    $cond .= " AND (`vendor_code` like '%" . $_REQUEST['keyword'] . "%' OR `vendor_name` like '%" . $_REQUEST['keyword'] . "%' OR `vendor_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                  }

                  $sql_list = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE 1 " . $cond . "  AND company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . "  ORDER BY vendor_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);


                  $countShow = "SELECT count(*) FROM `" . ERP_VENDOR_DETAILS . "` WHERE 1 " . $cond . " AND company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_VENDOR_DETAILS", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($num_list > 0) {
                  ?>
                    <table class="table defaultDataTable table-hover">
                      <thead>
                        <tr class="alert-light">
                          <th class="borderNone">#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th class="borderNone">Posting Date</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th class="borderNone">Transaction Id</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th class="borderNone">Payment Type</th>
                          <?php  }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th class="borderNone">Collect Payment</th>
                          <?php }
                          if (in_array(5, $settingsCheckbox)) { ?>
                            <th class="borderNone">Created At</th>
                          <?php }
                          if (in_array(6, $settingsCheckbox)) { ?>
                            <th class="borderNone">Status</th>
                          <?php } ?>

                          <th class="borderNone">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        foreach ($paymentList as $oneSoList) {
                          // console($oneSoList);
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['postingDate'] ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['transactionId'] ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['paymentCollectType'] ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) {
                            ?>
                              <td><?= $oneSoList['collect_payment'] ?></td>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['created_at'] ?></td>
                            <?php }
                            if (in_array(6, $settingsCheckbox)) { ?>
                              <td class="text-success font-weight-bold text-capitalize listStatus"><?= $oneSoList['status'] ?></td>
                            <?php } ?>
                            <td class="d-flex">
                              <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneSoList['payment_id'] ?>"><i class="fa fa-eye po-list-icon"></i></a>
                              <!-- <a style="cursor: pointer;" class="btn btn-sm"><i class="fa fa-eye"></i></a> -->
                              <!-- <a href="branch-so-invoice-2.php?invoice-no=<?= base64_encode($oneSoList['payment_id']) ?>" style="cursor: pointer;" class="btn btn-sm">
                                <i class="fa fa-download"></i>
                              </a> -->
                              <?php if ($oneSoList['status'] == 'active') { ?>
                                <a style="cursor:pointer" data-id="<?= $oneSoList['payment_id']; ?>" class="btn btn-sm reverseCollection" title="Reverse Now">
                                  <i class="far fa-undo po-list-icon"></i>
                                </a>
                              <?php } ?>
                            </td>
                          </tr>
                          <!-- right modal start here  -->
                          <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $oneSoList['payment_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                              <!--Content-->
                              <div class="modal-content">
                                <!--Header-->
                                <div class="modal-header">
                                  <p class="heading lead"><?= $oneSoList['invoice_no'] ?></p>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" class="white-text">×</span>
                                  </button>
                                </div>
                                <!--Body-->
                                <div class="modal-body" style="padding: 0;">
                                  <ul class="nav nav-tabs">
                                    <li class="nav-item"><a class="nav-link active" href="#preview<?= $oneSoList['payment_id'] ?>" data-bs-toggle="tab">Preview</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#otherDetails<?= $oneSoList['payment_id'] ?>" data-bs-toggle="tab">Other Details</a></li>
                                  </ul>
                                  <div class="tab-content">
                                    <div class="col-md-12">
                                      <div class="shadow-sm bg-light py-2 mx-2 my-2" id="action-navbar" style="text-align:right">
                                        <form action="" method="POST">
                                          <!-- <a href="branch-so-invoice-2.php?invoice-no=<?= base64_encode($oneSoList['payment_id']) ?>" name="vendorEditBtn">
                                          <span class="text-info font-weight-bold shadow-sm px-2">INVOICE</span>
                                        </a> -->
                                          <a href="#" name="vendorEditBtn">
                                            <i title="Edit" style="font-size: 1.2em" class="fa fa-edit text-success mx-3"></i>
                                          </a>
                                          <i title="Delete" style="font-size: 1.2em" class="fa fa-trash text-danger mx-3"></i>
                                          <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on text-primary mx-3"></i>
                                        </form>
                                      </div>
                                    </div>
                                    <div class="tab-pane show active" id="preview<?= $oneSoList['payment_id'] ?>">
                                      <!-- ################################## -->
                                      <div class="container my-3">
                                        <?php
                                        $paymentDetails = $BranchSoObj->fetchCustomerPaymentLogDetails($oneSoList['payment_id'])['data'];
                                        ?>
                                        <?php
                                        foreach ($paymentDetails as $one) {
                                          $invoice_no = $BranchSoObj->fetchBranchSoInvoiceById($one['invoice_id'])['data'][0]['invoice_no'];
                                          // console("imranali59059");
                                          // console($invoiceDetails);
                                          if ($one['payment_type'] == "pay") {
                                        ?>
                                            <div class="card shadow-sm p-2">
                                              <p>Invoice No: <strong><?= $invoice_no ?></strong></p>
                                              <p>Payment Type: <strong><?= $one['payment_type'] ?></strong></p>
                                              <p>Payment Amount: <strong><?= $one['payment_amt'] ?></strong></p>
                                            </div>
                                        <?php }
                                        } ?>
                                      </div>
                                      <!-- ################################## -->
                                    </div>
                                    <div class="tab-pane" id="otherDetails<?= $oneSoList['payment_id'] ?>">
                                      <div class="card p-5">
                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi ipsum ex soluta natus consequuntur voluptatem sed voluptate eum nulla. Molestias harum maxime ipsa? Error, ullam fugit possimus qui at excepturi reprehenderit culpa facilis alias sed nobis iusto ipsam? Suscipit aut tenetur numquam molestiae cum fugit optio iure quo veniam? Facere perspiciatis nostrum dolores aperiam id adipisci ducimus modi rerum natus consectetur animi fuga dicta nemo iure mollitia minus voluptatibus repellat, quas iste voluptatum? Voluptatem ex veritatis inventore debitis, ea dignissimos nisi veniam iure ipsum enim magni consequuntur repudiandae voluptatibus earum officiis cum quaerat cupiditate reprehenderit. Ducimus rem nisi deserunt consequuntur minus. Animi delectus quas laborum qui, exercitationem quo vero excepturi assumenda quos esse hic deleniti odit rem totam officia beatae recusandae ratione. Id quisquam nemo natus quam quae necessitatibus magnam cupiditate dolorem odio asperiores, doloremque quibusdam quo dignissimos eligendi fuga voluptas quasi maxime perferendis eveniet. Praesentium commodi et omnis, placeat quos deleniti nisi laborum a aliquam quasi incidunt, autem earum optio? Repellat eligendi qui aperiam earum ex ullam, voluptas doloremque commodi assumenda. Eaque omnis numquam quaerat rerum. Fugiat aliquid voluptatem id numquam nulla, ea quibusdam iste quisquam at voluptates, quia similique sunt tenetur odio veritatis enim praesentium rem repudiandae porro consequatur itaque? Odio veritatis ut ipsa officia fugit ipsum modi obcaecati doloremque animi cum maxime, et nisi ab doloribus tenetur culpa voluptatibus. Ducimus quidem nulla eveniet sapiente, quibusdam praesentium est sint soluta illo veniam! Error officia, sunt mollitia ab adipisci tenetur corrupti aliquid. Ullam nihil quam magnam vitae quisquam enim sequi quae ad! Totam, sint natus! Fuga aliquam explicabo distinctio dolorum facilis tenetur accusamus commodi quam rem nostrum? At beatae quibusdam nemo nulla vero repellat in quis ducimus doloremque inventore facilis officia repellendus ex, neque eligendi officiis accusamus fuga, asperiores sit. Nisi facilis repudiandae ab magnam voluptates totam? Praesentium quam, deleniti iusto reiciendis fuga qui. Ratione, quidem molestiae adipisci sint, quia animi eligendi accusamus, expedita unde numquam delectus amet earum quisquam dignissimos. Quam, harum, provident sapiente est non quas cupiditate perspiciatis natus eius dolor modi corrupti. Eius eos nihil quo dolorum in ullam mollitia, sed sapiente? Sed, iure veritatis quasi nihil ut omnis corrupti numquam sapiente consectetur eaque voluptatem possimus doloremque labore magnam quos! Veniam corporis odio sapiente officia eius. Laboriosam eaque ducimus impedit aliquam quaerat eius minima, provident, corporis, similique cumque quod rem ipsum blanditiis enim unde veritatis modi autem quae suscipit. Expedita, quaerat assumenda rerum deserunt velit repudiandae doloribus sint vitae laborum vel magni est soluta debitis, eaque earum ducimus fugiat asperiores dignissimos unde qui cum reiciendis enim dolorum delectus. Alias ad commodi aliquam veniam sint iusto quis nulla quaerat expedita accusamus ipsa tempore numquam esse quod similique quibusdam soluta dolorum deleniti, ut odit ea, cum in enim totam. Consequuntur, accusamus. Nemo, accusantium recusandae odio tempora voluptatum dolor non necessitatibus iusto autem deleniti expedita ducimus cupiditate libero cumque, hic reiciendis sed amet quidem vero aperiam explicabo, molestiae debitis animi! Id repudiandae a perspiciatis fugiat nisi dolore neque praesentium, quidem necessitatibus totam in explicabo, autem, nulla eum. Culpa, magni!
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <!--/.Content-->
                            </div>
                          </div>
                          <!-- right modal end here  -->
                        <?php } ?>
                      </tbody>
                    </table>
                  <?php } else { ?>
                    <table class="table defaultDataTable table-hover text-nowrap">
                      <thead>
                        <tr>
                          <td>

                          </td>
                        </tr>
                      </thead>
                    </table>
                </div>
              <?php } ?>
              </div>
              <!---------------------------------Table settings Model Start--------------------------------->

              <div class="modal" id="myModal2">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Table Column Settings</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                      <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                      <input type="hidden" name="pageTableName" value="ERP_VENDOR_DETAILS" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                Invoice No.</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                Customer PO</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                Delivery Date</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                Customer Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                Status</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                Total Items</td>
                            </tr>
                          </table>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button type="submit" name="add-table-settings" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!---------------------------------Table Model End--------------------------------->

            </div>
          </div>
        </div>
      </div>
  </div>
  </div>
  </section>
  </div>
  <!-- End Pegination from------->
<?php } ?>

<?php
require_once("../common/footer.php");
?>
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
              console.log(responseObj);

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


    let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
    if (collectTotalAmt <= 0 || collectTotalAmt == "") {
      $("#submitCollectPaymentBtn").prop("disabled", true);
    } else {
      $("#submitCollectPaymentBtn").prop("disabled", false);
    }

    var staticRemain = 0;
    $('#itemsDropDown')
      .select2()
      .on('select2:open', () => {
        // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
      });
    $('#customerSelect')
      .select2()
      .on('select2:open', () => {
        // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
      });
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

    $(".deliveryScheduleQty").on("change", function() {
      let qtyVal3 = ($(this).attr("id")).split("_")[1];
      let qtyVal = $(this).find(":selected").data("quantity");
      // let qtyVal2 = $(this).find(":selected").data("deliverydate");
      // let qtyVal = $(this).find(":selected").children("span");
      // $( "#myselect option:selected" ).text();
      console.log(qtyVal);
      $(`#itemQty_${qtyVal3}`).val(qtyVal);
    });

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

    // collectPayment
    function collectPayment(customerSelect) {
      $.ajax({
        type: "POST",
        url: `ajaxs/so/ajax-invoice-customer-list.php`,
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

    // 🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁
    const urlParams = new URLSearchParams(window.location.search);

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

    // imranali59059💰💰💰💰💰💰💰💰💰💰💰💰💰
    // collect payment Amount 
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
      // $(".dueAmt").each(function() {
      //   let recVal = (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
      //   if(thisAmt > recVal){

      //   }else{

      //   }
      // });

      $(".remaningAmt").text(rem - totalReceiveAmt);

      if (collectTotalAmt <= 0 || collectTotalAmt === "") {
        $("#submitCollectPaymentBtn").prop("disabled", true);
      } else {
        $("#submitCollectPaymentBtn").prop("disabled", false);
      }
    })
    // received payment amount🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢 
    $(document).on("keyup", ".receiveAmt", function() {
      let rowId = ($(this).attr("id")).split("_")[1];
      let recAmt = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      let invoiceAmt = $(`#invoiceAmt_${rowId}`).text();
      let dueAmt = (parseFloat($(`#dueAmt_${rowId}`).text()) > 0) ? parseFloat($(`#dueAmt_${rowId}`).text()) : 0;
      let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
      let remaningAmt = $(".remaningAmt").text();

      var totalDueAmt = 0;
      var totalRecAmt = 0;

      let duePercentage = ((parseFloat(dueAmt) - parseFloat(recAmt)) / parseFloat(invoiceAmt)) * 100;
      $(`#duePercentage_${rowId}`).text(`${Math.round(duePercentage,2)}%`);

      $(".receiveAmt").each(function() {
        totalRecAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });

      let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
      let rem = parseFloat(collectTotalAmt) + parseFloat(advancedPayAmt);
      staticRemain = rem;
      // let remaintTotalAmt = parseFloat(collectTotalAmt) - parseFloat(totalRecAmt);
      let remaintTotalAmt = parseFloat(staticRemain) - parseFloat(totalRecAmt);
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

    $("#submitCollectPaymentBtn").on("click", function() {
      var isChecked = $('#round_off_checkbox').is(':checked');
      let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
      let adjustedCollectAmountInp = ($(".adjustedCollectAmountInp").val()) ? ($(".adjustedCollectAmountInp").val()) : 0;
      let totalRecAmt2 = 0;
      let advancedPayAmt2 = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;

      if (isChecked) {
        $(".receiveAmt").each(function() {
          totalRecAmt2 += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        });
        let totalCaptureAmt = (parseFloat(adjustedCollectAmountInp) + parseFloat(advancedPayAmt2)) - (parseFloat(totalRecAmt2));
        $("#totalReceiveAmt").text(`₹${totalRecAmt2}`);
        $("#totalCaptureAmt").text(`₹${totalCaptureAmt}`);
      } else {
        $(".receiveAmt").each(function() {
          totalRecAmt2 += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        });
        let totalCaptureAmt = (parseFloat(collectTotalAmt) + parseFloat(advancedPayAmt2)) - (parseFloat(totalRecAmt2));
        $("#totalReceiveAmt").text(`₹${totalRecAmt2}`);
        $("#totalCaptureAmt").text(`₹${totalCaptureAmt}`);
      }

      if (totalCaptureAmt === 0) {
        $(".totalCaptureAmtDiv").hide();
      } else {
        $(".totalCaptureAmtDiv").show();
      }
    });

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