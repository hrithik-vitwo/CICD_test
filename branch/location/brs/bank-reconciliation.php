<?php
require_once("../../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/company/func-branches.php");

require_once("controller/bankReconciliationStatement.controller.php");

$bankId = isset($_GET["bank"]) ? base64_decode($_GET["bank"]) : 0;
if (isset($_GET["act"]) && $_GET["act"] == "recognised") {
  $tnxType = "recognised";
} elseif (isset($_GET["act"]) && $_GET["act"] == "unrecognised") {
  $tnxType = "unrecognised";
} else {
  $tnxType = "all";
}

?>
<link rel="stylesheet" href="../../../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
  .recon-modal .modal-dialog {
    width: 100%;
    max-width: 1000px;
  }

  .recon-modal .modal-dialog .modal-body {
    width: 100%;
  }
</style>
<div class="content-wrapper">
  <section class="content">
    <?php
    $brsObj = new BankReconciliationStatement($bankId, $tnxType);
    $bankTnxObj = $brsObj->getBankStatements();
    $vendorListObj = $brsObj->getVendorList();
    $customerListObj = $brsObj->getCustomerList();



    // $branchSoObj = new BranchSo();

    $amountInBook = 130600.00;
    $amountInBank = $bankTnxObj["totalAmount"];
    $amountInUnrecognised = $amountInBook - $amountInBank;
    // console($bankTnxObj);
    ?>
  </section>
  <section class="content">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
      <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i>List</a></li>
      <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create</a></li>
      <li class="back-button">
        <a href="">
          <i class="fa fa-reply po-list-icon"></i>
        </a>
      </li>
    </ol>
    <div class="card mb-2">
      <div class="card-header p-2">
        <p class="text-light pl-2">Summary Details</p>
      </div>
      <div class="card-body">
        <div class="row p-0 m-0 pt-1" style="height: 60px;">
          <div class="row col-8">
            <div class="col-3">
              <p class="p-0 m-0 text-center font-weight-bold">Amount in Books</p>
              <p class="p-0 m-0 text-center font-weight-bold"><?= number_format($bankTnxObj["totalAmount"], 2) ?></p>
            </div>
            <div class="col-3" style="height: 7vh;">
              <p class="p-0 m-0 text-center font-weight-bold">Amount in Bank</p>
              <p class="p-0 m-0 text-center font-weight-bold"><?= number_format($bankTnxObj["recognisedAmount"], 2) ?></p>
            </div>
            <div class="col-3" style="height: 7vh;">
              <p class="p-0 m-0 text-center font-weight-bold">Unrecognised Amount</p>
              <p class="p-0 m-0 text-center font-weight-bold"><?= number_format($bankTnxObj["unrecognisedAmount"], 2) ?></p>
            </div>
            <div class="col-3" style="height: 7vh;">
              <p class="p-0 m-0 text-center font-weight-bold">Last Feed Date</p>
              <p class="p-0 m-0 text-center font-weight-bold"><?= $bankTnxObj["lastFeedDate"] ?></p>
            </div>
          </div>
          <div class="col-4" style="justify-content:center;">
            <p class="p-0 m-0 text-center font-weight-bold">Select Bank</p>
            <center>
              <select name="" id="bankDropdown" class="form-control col-4">
                <option value="">All Bank</option>
                <?php
                foreach ($brsObj->getBankList()["data"] as $key => $listItem) {
                ?>
                  <option value="<?= $listItem["id"] ?>" <?= $listItem["id"] == $bankId ? "selected" : "" ?>><?= $listItem["bank_name"] ?></option>
                <?php
                }
                ?>
              </select>
              <script>
                $(document).ready(function() {
                  $(document).on("change", "#bankDropdown", function() {
                    let bankId = window.btoa($(this).val());
                    const urlParams = new URLSearchParams(window.location.search);
                    if (window.location.search.indexOf("?") >= 0) {
                      window.location.href = `?act=${urlParams.get('act')??"all"}&bank=${bankId}`;
                    } else {
                      window.location.href = `?act=all&bank=${bankId}`;
                    }
                  });
                });
              </script>
            </center>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-2">
      <div class="card-header p-2">
        <a href="?act=all&bank=<?= base64_encode($bankId) ?>" class="btn <?= (!isset($_GET["act"]) || $_GET["act"] == "all") ? "btn-light" : "btn-secondary" ?>">All Transactions</a>
        <a href="?act=unrecognised&bank=<?= base64_encode($bankId) ?>" class="btn <?= (isset($_GET["act"]) && $_GET["act"] == "unrecognised") ? "btn-light" : "btn-secondary" ?>">Unrecognised Transactions</a>
        <a href="?act=recognised&bank=<?= base64_encode($bankId) ?>" class="btn <?= (isset($_GET["act"]) && $_GET["act"] == "recognised") ? "btn-light" : "btn-secondary" ?>">Recognised Transactions</a>
      </div>
      <div class="card-body" style="overflow-x: auto;">
        <div>
        </div>
        <table class="table defaultDataTable table-nowrap" id="tblBrsAllTransaction">
          <thead>
            <tr>
              <th width="5%">Sl</th>
              <th>Date</th>
              <th width="20%">Referance</th>
              <th>Category</th>
              <th>Deposite</th>
              <th>Withdrawals</th>
              <th>Balance</th>
              <th>Bank</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($bankTnxObj["data"] as $sl => $listItem) {
              $tnxAmount = $listItem["deposit_amt"] > 0 ? $listItem["deposit_amt"] : $listItem["withdrawal_amt"];
              $bankName = $listItem["bank_name"] != "" ? $listItem["bank_name"] . " (" . $listItem["account_no"] . ")" : "";
            ?>
              <tr class="unrecognisedTnxTblRow" id="unrecognisedTnxTblRow_<?= $listItem["id"] ?>" data-tnx="<?= base64_encode(json_encode($listItem)) ?>" style="cursor:pointer;" data-toggle="modal" data-target="#unrecognisedTnxModal_<?= $listItem["id"] ?>">
                <td><?= $sl + 1; ?> || <?= $listItem["id"] ?></td>
                <td><?= date_format(date_create($listItem["tnx_date"]), "d-M-Y"); ?></td>
                <td width="20%"><?= $listItem["particular"] ?></td>
                <td><?= $listItem["tnx_category"] ?? "-" ?></td>
                <td class="text-right"><?= $listItem["deposit_amt"] > 0 ? number_format($listItem["deposit_amt"], 2) : "" ?></td>
                <td class="text-right"><?= $listItem["withdrawal_amt"] > 0 ? number_format($listItem["withdrawal_amt"], 2) : "" ?></td>
                <td class="text-right"><?= number_format($listItem["balance_amt"], 2) ?></td>
                <td><?= $bankName ?></td>
              </tr>
              <div class="modal fade right recon-modal customer-modal" id="unrecognisedTnxModal_<?= $listItem["id"] ?>" tabindex="-1" role="dialog" data-backdrop="true" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                  <!--Content-->
                  <div class="modal-content">
                    <!--Header-->
                    <div class="modal-header" style="height: 250px !important;">
                      <div class="d-flex justify-content-between">
                        <h2 class="text-white mt-2 mb-2"><span class="rupee-symbol">₹</span><?= number_format($tnxAmount, 2) ?></h2>
                        <p class="heading lead text-right mt-2 mb-2"><?= $listItem["utr_number"] ?></p>
                      </div>
                      <p class="text-sm text-right mb-2"><?= $listItem["particular"] ?></p>
                      <p class="text-sm text-right mb-2">Date: <?= date_format(date_create($listItem["tnx_date"]), "d-M-Y"); ?></p>
                      <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                          <p style="font-size: small;">Select Transaction Category</p>
                          <select name="" id="selectUnrecognisedTransactionCategory_<?= $listItem["id"] ?>" class="form-control selectTransactionCategory">
                            <option value="">Select Category</option>
                            <option value="vendor_payment">Vendor Payment</option>
                            <option value="customer_payment">Customer Payment</option>
                          </select>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12" id="transactionCategorySubDropdownDiv_<?= $listItem["id"] ?>"></div>
                      </div>
                    </div>
                    <!--Body-->
                    <div class="modal-body">
                      <div id="reconciliationFormDiv_<?= $listItem["id"] ?>"></div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-sm btn-primary">Cancel</button>
                      <button class="btn btn-sm btn-primary">Apply Changes</button>
                    </div>
                  </div>
                </div>
              </div>
            <?php
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

  </section>
</div>



<?php require_once("../../common/footer.php"); ?>
<script>
  $(document).ready(function() {
    const log = console.log;

    $("#tblBrsAllTransaction").dataTable();

    $(document).on("change", ".selectCustomerDropdown", function() {
      let row_id = ($(this).attr("id")).split("_")[1];
      let customer_id = $(this).val();
      console.log("customer", row_id, customer_id);
      $.ajax({
        type: "POST",
        url: 'ajax/ajax-get-customer-due-invoice-list.php',
        data: {
          customer_id
        },
        beforeSend: function() {
          $(`#reconciliationFormDiv_${row_id}`).html("Loding, Please wait...");
        },
        success: function(response) {
          $(`#reconciliationFormDiv_${row_id}`).html(response);
          // log('Data received:', response);
        },
        complete: function(xhr, status) {
          if(xhr.status != 200){
            $(`#reconciliationFormDiv_${row_id}`).html("Something went wrong, please try again!");
          }
          log('Customer Invoice details request completed with status code:', xhr.status);
        }
      });
    });

    $(document).on("change", ".selectVendorDropdown", function() {
      let row_id = ($(this).attr("id")).split("_")[1];
      let vendor_id = $(this).val();
      console.log("vendor", row_id, vendor_id);
      $.ajax({
        type: "POST",
        url: 'ajax/ajax-get-vendor-due-invoice-list.php',
        data: {
          vendor_id
        },
        beforeSend: function() {
          $(`#reconciliationFormDiv_${row_id}`).html("Loding, Please wait...");
        },
        success: function(response) {
          $(`#reconciliationFormDiv_${row_id}`).html(response);
          log('Data received:', response);
        },
        complete: function(xhr, status) {
          if(xhr.status != 200){
            $(`#reconciliationFormDiv_${row_id}`).html("Something went wrong, please try again!");
          }
          log('Vendor Invoice details request completed with status code:', xhr.status);
        }
      });
    });

    $(document).on("click", ".unrecognisedTnxTblRow", function() {
      const tnxId = ($(this).attr("id")).split("_")[1];
      const tnxDetails = JSON.parse(atob($(this).attr("data-tnx")));
      log("Tnx Row is clicked and the tnx id is " + tnxId);
      log(tnxDetails);
    });

    $(document).on("change", ".selectTransactionCategory", function() {
      let row_id = ($(this).attr("id")).split("_")[1];
      console.log("row_id", row_id);
      let tnx_category = $(this).val();
      if (tnx_category == "vendor_payment") {
        $(`#transactionCategorySubDropdownDiv_${row_id}`).html(`
          <p style="font-size: small;">Select Vendor</p>
          <select name="" id="selectVendorDropdown_${row_id}" class="form-control selectVendorDropdown">
            <option value="">Select Vendor</option>
            <?php
            foreach ($vendorListObj["data"] as $vendor) {
            ?>
              <option value="<?= $vendor["vendor_id"] ?>"><?= $vendor["vendor_code"] . " - " . $vendor["vendor_name"] ?></option>
              <?php
            } ?>
          </select>
        `);
        console.log("vendor list");
      } else if (tnx_category == "customer_payment") {
        $(`#transactionCategorySubDropdownDiv_${row_id}`).html(`
          <p style="font-size: small;">Select Customer</p>
          <select name="" id="selectCustomerDropdown_${row_id}" class="form-control selectCustomerDropdown">
            <option value="">Select Customer</option>
            <?php
            foreach ($customerListObj["data"] as $customer) {
            ?>
              <option value="<?= $customer["customer_id"] ?>"><?= $customer["customer_code"] . " - " . $customer["customer_name"] ?></option>
              <?php
            } ?>
          </select>
        `);
        console.log("customer list");
      } else {
        $(`#transactionCategorySubDropdownDiv_${row_id}`).html("");
        console.log("both hide");
      }
    });

  });
</script>