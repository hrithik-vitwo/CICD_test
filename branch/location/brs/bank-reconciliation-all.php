<?php
require_once("../../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/company/func-branches.php");

function getUnrecognisedTransactions()
{
  $dataObj = queryGet('SELECT * FROM `erp_bank_statements` WHERE `company_id`=1 AND `status`="active" AND `reconciled_status`="pending" ORDER BY id DESC', true);
  return [
    "status" => $dataObj["status"],
    "message" => $dataObj["message"],
    "data" => $dataObj["data"],
    "totalAmount" => array_sum(array_column($dataObj["data"], "withdrawal_amt")) + array_sum(array_column($dataObj["data"], "deposit_amt")),
  ];
}

function getBankTransactionStatements($tnxType = "all")
{
  global $company_id;
  global $location_id;
  if ($tnxType == "unrecognised") {
    $dataObj = queryGet('SELECT s.*, b.bank_name, b.account_no FROM `erp_bank_statements` AS s LEFT JOIN `erp_acc_bank_cash_accounts` AS b ON s.bank_id=b.id AND s.company_id=' . $company_id . ' AND s.reconciled_status="pending" ORDER BY s.id DESC LIMIT 10', true);
  } elseif ($tnxType == "recognised") {
    $dataObj = queryGet('SELECT s.*, b.bank_name, b.account_no FROM `erp_bank_statements` AS s LEFT JOIN `erp_acc_bank_cash_accounts` AS b ON s.bank_id=b.id AND s.company_id=' . $company_id . ' AND s.reconciled_status="reconciled" AND s.reconciled_location_id=' . $location_id . ' ORDER BY s.id DESC LIMIT 10', true);
  } else {
    $dataObj = queryGet('SELECT s.*, b.bank_name, b.account_no FROM `erp_bank_statements` AS s LEFT JOIN `erp_acc_bank_cash_accounts` AS b ON s.bank_id=b.id AND s.company_id=' . $company_id . ' AND ((s.reconciled_status="reconciled" AND s.reconciled_location_id=' . $location_id . ') OR s.reconciled_status="pending") ORDER BY s.id DESC LIMIT 10', true);
  }

  return [
    "status" => $dataObj["status"],
    "message" => $dataObj["message"],
    "data" => $dataObj["data"],
    "sql" => $dataObj["query"],
    "totalAmount" => $dataObj["data"][0]["balance_amt"]
  ];
}

function getBankList()
{
  return queryGet('SELECT * FROM `erp_acc_bank_cash_accounts` WHERE `company_id`=1 AND `type_of_account`="bank" AND `status`="active"', true);
}

$amountInBook = 130600.00;

$bankId = isset($_GET["bank"]) ? base64_decode($_GET["bank"]) : 0;

if (isset($_GET["recognised"])) {
  $allTnxObj = getBankTransactionStatements("recognised");
} elseif (isset($_GET["unrecognised"])) {
  $allTnxObj = getBankTransactionStatements("unrecognised");
} else {
  $allTnxObj = getBankTransactionStatements("all");
}
// console($allTnxObj);
$amountInBank = $allTnxObj["totalAmount"];
$amountInUnrecognised = $amountInBook - $amountInBank;
?>
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/listing.css">
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
              <p class="p-0 m-0 text-center font-weight-bold"><?= number_format($amountInBook, 2) ?></p>
            </div>
            <div class="col-3" style="height: 7vh;">
              <p class="p-0 m-0 text-center font-weight-bold">Amount in Bank</p>
              <p class="p-0 m-0 text-center font-weight-bold"><?= number_format($amountInBank, 2) ?></p>
            </div>
            <div class="col-3" style="height: 7vh;">
              <p class="p-0 m-0 text-center font-weight-bold">Unrecognised Amount</p>
              <p class="p-0 m-0 text-center font-weight-bold"><?= number_format($amountInUnrecognised, 2) ?></p>
            </div>
            <div class="col-3" style="height: 7vh;">
              <p class="p-0 m-0 text-center font-weight-bold">Last Feed Date</p>
              <p class="p-0 m-0 text-center font-weight-bold"><?= date_format(date_create("31-03-2023"), "d-M-Y") ?></p>
            </div>
          </div>
          <div class="col-4" style="justify-content:center;">
            <p class="p-0 m-0 text-center font-weight-bold">Select Bank</p>
            <center>
              <?php
                console($_GET);
              ?>
              <select name="" id="bankDropdown" class="form-control col-4">
                <option value="">All Bank</option>
                <?php
                foreach (getBankList()["data"] as $key => $listItem) {
                ?>
                  <option value="<?= $listItem["id"] ?>" <?= $listItem["id"]==$bankId ? "selected" : "" ?>><?= $listItem["bank_name"] ?></option>
                <?php
                }
                ?>
              </select>
              <script>
                $(document).ready(function() {
                  $(document).on("change", "#bankDropdown", function() {
                    let bankId = window.btoa($(this).val());
                    window.location.href = `?bank=${bankId}`;
                    console.log("changeBankDropdown", bankId);
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
        <a href="?all" class="btn <?= (isset($_GET["unrecognised"]) || isset($_GET["recognised"])) ? "btn-secondary" : "btn-light" ?>">All Transactions</a>
        <a href="?unrecognised" class="btn <?= isset($_GET["unrecognised"]) ? "btn-light" : "btn-secondary" ?>">Unrecognised Transactions</a>
        <a href="?recognised" class="btn <?= isset($_GET["recognised"]) ? "btn-light" : "btn-secondary" ?>">Recognised Transactions</a>
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
            foreach ($allTnxObj["data"] as $sl => $listItem) {
              $tnxAmount = $listItem["deposit_amt"] > 0 ? $listItem["deposit_amt"] : $listItem["withdrawal_amt"];
              $bankName = $listItem["bank_name"] != "" ? $listItem["bank_name"] . " (" . $listItem["account_no"] . ")" : "";
            ?>
              <tr class="unrecognisedTnxTblRow" id="unrecognisedTnxTblRow_<?= $listItem["id"] ?>" data-tnx="<?= base64_encode(json_encode($listItem)) ?>" style="cursor:pointer;" data-toggle="modal" data-target="#unrecognisedTnxModal_<?= $listItem["id"] ?>">
                <td><?= $sl + 1; ?></td>
                <td><?= date_format(date_create($listItem["tnx_date"]), "d-M-Y"); ?></td>
                <td width="20%"><?= $listItem["particular"] ?></td>
                <td><?= $listItem["tnx_category"] ?? "-" ?></td>
                <td class="text-right"><?= $listItem["deposit_amt"] > 0 ? number_format($listItem["deposit_amt"], 2) : "" ?></td>
                <td class="text-right"><?= $listItem["withdrawal_amt"] > 0 ? number_format($listItem["withdrawal_amt"], 2) : "" ?></td>
                <td class="text-right"><?= number_format($listItem["balance_amt"], 2) ?></td>
                <td><?= $bankName ?></td>
              </tr>
              <div class="modal fade right recon-modal customer-modal " id="unrecognisedTnxModal_<?= $listItem["id"] ?>" tabindex="-1" role="dialog" data-backdrop="true" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                  <!--Content-->
                  <div class="modal-content">
                    <!--Header-->
                    <div class="modal-header">
                      <div class="d-flex justify-content-between">
                        <h2 class="text-white mt-2 mb-2"><span class="rupee-symbol">₹</span><?= number_format($tnxAmount, 2) ?></h2>
                        <p class="heading lead text-right mt-2 mb-2"><?= $listItem["utr_number"] ?></p>
                      </div>
                      <p class="text-sm text-right mb-2"><?= $listItem["particular"] ?></p>
                      <p class="text-sm text-right mb-2">Date: <?= date_format(date_create($listItem["tnx_date"]), "d-M-Y"); ?></p>
                      <div class="display-flex-space-between mt-2 mb-3">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                          <li class="nav-item">
                            <!-- <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Info</a> -->
                          </li>
                        </ul>
                        <div>
                          <p style="font-size: small;">Select Transaction Category</p>
                          <select name="" id="selectUnrecognisedTransactionCategory_<?= $listItem["id"] ?>" class="form-control selectTransactionCategory">
                            <option value="">Select Category</option>
                            <option value="vendor_payment">Vendor Payment</option>
                            <option value="customer_payment">Customer Payment</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <!--Body-->
                    <div class="modal-body">
                      <div id="unTnxCategoryDiv_<?= $listItem["id"] ?>"></div>
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
    <!-- <div class="card mb-2">
      <div class="card-header p-2">
        <p class="text-light pl-2">Recognised Transactions</p>
      </div>
      <div class="card-body" style="overflow-x: auto;">
        <table class="table defaultDataTable table-nowrap">
          <tbody>
            <tr>
              <th width="5%">Sl</th>
              <th>Date</th>
              <th>Referance</th>
              <th>Type</th>
              <th>Deposite</th>
              <th>Withdrawals</th>
              <th>Balance</th>
            </tr>
            <tr>
              <td>1</td>
              <td>2023-04-19</td>
              <td>S45678789</td>
              <td>Unrecognised</td>
              <td>$615.96</td>
              <td>-</td>
              <td>$615.96</td>
            </tr>
            <tr>
              <td>1</td>
              <td>2023-04-19</td>
              <td>S45678789</td>
              <td>Unrecognised</td>
              <td>$615.96</td>
              <td>-</td>
              <td>$615.96</td>
            </tr>
            <tr>
              <td>1</td>
              <td>2023-04-19</td>
              <td>S45678789</td>
              <td>Unrecognised</td>
              <td>$615.96</td>
              <td>-</td>
              <td>$615.96</td>
            </tr>
            <tr>
              <td>1</td>
              <td>2023-04-19</td>
              <td>S45678789</td>
              <td>Unrecognised</td>
              <td>$615.96</td>
              <td>-</td>
              <td>$615.96</td>
            </tr>
            <tr>
              <td>1</td>
              <td>2023-04-19</td>
              <td>S45678789</td>
              <td>Unrecognised</td>
              <td>$615.96</td>
              <td>-</td>
              <td>$615.96</td>
            </tr>
            <tr>
              <td>1</td>
              <td>2023-04-19</td>
              <td>S45678789</td>
              <td>Unrecognised</td>
              <td>$615.96</td>
              <td>-</td>
              <td>$615.96</td>
            </tr>
            <tr>
              <td>1</td>
              <td>2023-04-19</td>
              <td>S45678789</td>
              <td>Unrecognised</td>
              <td>$615.96</td>
              <td>-</td>
              <td>$615.96</td>
            </tr>
            <tr>
              <td>1</td>
              <td>2023-04-19</td>
              <td>S45678789</td>
              <td>Unrecognised</td>
              <td>$615.96</td>
              <td>-</td>
              <td>$615.96</td>
            </tr>
            <tr>
              <td>1</td>
              <td>2023-04-19</td>
              <td>S45678789</td>
              <td>Unrecognised</td>
              <td>$615.96</td>
              <td>-</td>
              <td>$615.96</td>
            </tr>
            <tr>
              <td>1</td>
              <td>2023-04-19</td>
              <td>S45678789</td>
              <td>Unrecognised</td>
              <td>$615.96</td>
              <td>-</td>
              <td>$615.96</td>
            </tr>
            <tr>
              <td>1</td>
              <td>2023-04-19</td>
              <td>S45678789</td>
              <td>Unrecognised</td>
              <td>$615.96</td>
              <td>-</td>
              <td>$615.96</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div> -->
  </section>
</div>



<?php require_once("../../common/footer.php"); ?>




<script>
  $(document).ready(function() {
    const log = console.log;

    // $("#tblBrsAllTransaction").select2();

    $(document).on("click", ".unrecognisedTnxTblRow", function() {
      const tnxId = ($(this).attr("id")).split("_")[1];
      const tnxDetails = JSON.parse(atob($(this).attr("data-tnx")));


      log("Tnx Row is clicked and the tnx id is " + tnxId);
      log(tnxDetails);



    });

    $(document).on("change", ".selectTransactionCategory", function() {
      let tnxId = ($(this).attr("id")).split("_")[1];
      let tnxCategory = $(this).val();
      log("Tnx Category is " + tnxCategory);
      $(`#unTnxCategoryDiv_${tnxId}`).html("");
      if (tnxCategory == "customer_payment") {
        $.ajax({
          url: "<?= BASE_URL ?>branch/location/ajaxs/bank-recon/ajax-get-customer-payments.php",
          data: {
            data: "vendor"
          },
          success: function(data) {
            $(`#unTnxCategoryDiv_${tnxId}`).html(data);
            log(data);
          },
          error: function(xhr, status, error) {
            log(xhr, status, error);
          }
        });
      }
    });

  });
</script>