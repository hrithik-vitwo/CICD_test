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
// compnay currrency data
$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];
$currency_name = $companyCurrencyData['currency_name'];

// imranali59059💰💰💰💰💰💰💰💰💰💰💰💰💰
if (isset($_POST['submitCollectPaymentBtn'])) {
  console($_POST);
  exit();
  $addCollectPayment = $grnObj->insertVendorPayment($_POST, $_FILES);
  console($addCollectPayment);
  exit();

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
  /* Basic button styling */
  .btn {
    margin-right: 10px;
    padding: 10px 20px;
    font-size: 16px;
    text-align: center;
    cursor: pointer;
    border-radius: 5px;
  }

  /* Default state of the buttons */
  .btn-primary {
    background-color: #007bff;
    /* Default blue background */
    color: #fff;
    border: 1px solid #007bff;
    transition: background-color 0.3s ease, color 0.3s ease;
  }

  /* Hover effect */
  .btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
  }

  /* Active (selected) tab styling */
  .customer-vendor-div .btn.active {
    background-color: #28a745;
    /* New active background color (green) */
    color: #fff;
    font-weight: bold;
    border-color: #218838;
    /* Darker green for border */
  }


  /* cn settale modal css */
  /* Default style for the checkbox (unchecked state) */
  .form-check-input {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 20px;
    height: 20px;
    border: 2px solidrgb(17, 255, 0);
    border-radius: 50%;
    position: relative;
    transition: all 0.3s ease;
    background-color: #28a745;
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
    background-color: #28a745;
    /* White checkmark */
    border-radius: 50%;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
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
</style>


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
            <h5>Manage Sub-GL Transaction</h5>
          </div>
          <div class="col-6">
            <div class="float-right d-flex">
              <div class="mx-2"><button class="btn btn-success" type="button" id="submitCollectPaymentBtn">Collect</button></div>
            </div>
          </div>
        </div>
        <div class="mb-3  customer-vendor-div">
          <a id="vendorTableBtn" class="btn btn-primary active">Pay to Customer</a>
          <a id="customerTableBtn" class="btn btn-primary">Collect from Vendor</a>
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
                <div class="totalCaptureAmtDiv"><span style="font-family: 'Font Awesome 5 Free';" id="totalCaptureAmt">0</span> amount captured as Open advanced</div>
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
                  <div class="col-lg-6 col-md-6 col-sm-6">
                    <label for="" class="label-hidden"></label>
                    <input type="text" name="paymentDetails[collectPayment]" class="form-control collectTotalAmt" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1">
                    <input type="hidden" name="paymentDetails[vendorId]" id="vendorId" value="collect">

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
                      <p class="text-xs"> Total Advance</p>
                      <!-- <p class="text-xs font-bold rupee-symbol">₹ <span class="totalInvAmt">0</span></p> -->
                      <p class="text-xs font-bold rupee-symbol"><?= $companyCurrencyData["currency_name"] ?> <span class="total_outstanding_amount1"> 0</span></p>
                    </div>
                  </div>

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
                    <p class="text-xs text-right font-bold rupee-symbol"><?= $companyCurrencyData["currency_name"] ?> <span class="remaningAmt">0</span></p>
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


          <div class="collectionDiv col-lg-12 col-md-12 col-sm-12">

            <input type="hidden" name="paymentDetails[advancedPayAmt]" value="" class="advancedPayAmt">

            <div class="collectionLineItemsDiv col-lg-12 col-md-12 col-sm-12">
              <table id="dataTable_detailed_view" class="invTable">
                <thead>
                  <tr>
                    <th>Sl No</th>
                    <th>Action</th>
                    <th>Customer Code </th>
                    <th>Customer Name </th>
                    <th>Constitution of Business</th>
                    <th>GSTIN</th>
                    <th>Phone</th>
                  </tr>
                </thead>
                <tbody id="invDetailsBody">

                </tbody>
              </table>
            </div>

            <div class="vendorDiv col-lg-12 col-md-12 col-sm-12">
              <table id="dataTable_detailed_view_vednor" class="vendorTable">
                <thead>
                  <tr>
                    <th>Sl No</th>
                    <th>Action</th>
                    <th>Customer Code </th>
                    <th>Customer Name </th>
                    <th>Constitution of Business</th>
                    <th>GSTIN</th>
                    <th>Phone</th>
                  </tr>
                </thead>
                <tbody id="vendorDetailsBody">

                </tbody>

              </table>

            </div>
          </div>


        </div>
    </div>
    </form>
</div>
<section>
  </div>

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
    function loadCustomers() {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers-vendor-list.php`,

        beforeSend: function() {
          // $("#customerDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          let res = JSON.parse(response);
          console.log(res);
          appendCustomer(res.data);
          $(".invTable").show();
          $(".vendorTable").hide();
        }
      });
    }



    function appendCustomer(cust_data) {
      const tableBody = $('#invDetailsBody');
      console.log(cust_data);
      let rows = '';
      let sl_no = 1;

      cust_data.forEach(row => {
        rows += `
        <tr>
           <td><p class="text-center">${sl_no}</p></td>
           <td><p class="text-center"><input type="checkbox" data-id=${row.customer_id} class="form-check-input" name="customer_checkbox" id="customer_checkbox"></p></td>
            <td><p class="text-center">${row.customer_code}</p></td>
            <td><p class="text-center">${row.trade_name}</p></td>
            <td><p class="text-center">${row.constitution_of_business}</p></td>
            <td><p class="text-center">${row.customer_gstin}</p></td>
            <td><p class="text-center">${row.customer_authorised_person_phone}</p></td>
        </tr>`;
        sl_no++;
      });

      tableBody.append(rows);
    }
    loadCustomers();


    $(document).on("change", ".form-check-input", function() {
      $(".form-check-input").not(this).prop("checked", false);
    });

    // Select both buttons
    const customerTableBtn = document.getElementById('customerTableBtn');
    const vendorTableBtn = document.getElementById('vendorTableBtn');

    // Add click event listeners to each button
    customerTableBtn.addEventListener('click', function() {
      // Add active class to 'Collect from Vendor' button and remove from 'Pay for Customer' button
      customerTableBtn.classList.add('active');
      vendorTableBtn.classList.remove('active');
     

    });

    vendorTableBtn.addEventListener('click', function() {
      // Add active class to 'Pay for Customer' button and remove from 'Collect from Vendor' button
      vendorTableBtn.classList.add('active');
      customerTableBtn.classList.remove('active');
      customerTableBtn.disabled = true;

    });

    $("#submitCollectPaymentBtn").on("click", function() {
      $('#exampleModal').modal('show');
      let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
      $(".advancedPayAmt").val(collectTotalAmt);
      let checkedCheckbox = $(".form-check-input:checked"); // Get the checked checkbox
      let dataId = checkedCheckbox.data("id");
      $("#vendorId").val(dataId);
      $('#totalCaptureAmt').text(collectTotalAmt);

    })
  </script>