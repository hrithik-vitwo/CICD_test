<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");


// console($_SESSION);

if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"]);
}

if (isset($_POST["visit"])) {
  $newStatusObj = VisitBranches($_POST);
  redirect(BRANCH_URL);
}

if (isset($_POST["createdata"])) {
  $addNewObj = createDataBranches($_POST);
  if ($addNewObj["status"] == "success") {
    $branchId = base64_encode($addNewObj['branchId']);
    redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
    swalToast($addNewObj["status"], $addNewObj["message"]);
    // console($addNewObj);
  } else {
    swalToast($addNewObj["status"], $addNewObj["message"]);
  }
}

if (isset($_POST["editdata"])) {
  $editDataObj = updateDataBranches($_POST);

  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();


$company = $BranchSoObj->fetchCompanyDetails()['data'];
$currencyIcon = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_icon'];

if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
  // console($_POST);
  // exit;
  $addBranchSo = $BranchSoObj->insertServiceInvoice($_POST);
  // console($addBranchSo);
  if ($addBranchSo['status'] == "success") {
    swalAlert($addBranchSo["status"], $addBranchSo['invoiceNo'], $addBranchSo["message"], "manage-invoices.php");
  } else {
    swalAlert($addBranchSo["status"], 'Warning', $addBranchSo["message"]);
  }
}
?>
<style>
  .service-invoice-card .card-body {
    min-height: 100%;
    height: 270px !important;
  }
  .card.po-vendor-details-view .card-body {
    height: auto !important;
  }
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">



<div class="content-wrapper">
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
          <div class="modal-header card-header py-2 px-3">
            <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
          </div>
          <div id="notesModalBody" class="modal-body card-body">
          </div>
        </div>
      </div>
    </div>
  <section class="content">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
        <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Service Invoice List</a></li>
        <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Service Invoice</a></li>
        <li class="back-button">
          <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
            <i class="fa fa-reply po-list-icon"></i>
          </a>
        </li>
      </ol>
    </div>
    <form action="" method="POST" id="addNewSOForm">
      <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
          <div class="card service-invoice-card so-creation-card po-creation-card ">
            <div class="card-header">
              <div class="row customer-info-head">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="head">
                    <i class="fa fa-user"></i>
                    <h4>Customer Info</h4>
                    <input type="hidden" class="customerIdInp" value="0">

                  </div>
                </div>
              </div>
            </div>
            <div class="card-body others-info vendor-info so-card-body">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="row customer-info-form-view">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="input-box customer-select">
                          <span class="text-danger">*</span>
                          <select name="customerId" id="customerDropDown" class="form-control" required>
                            <option value="">Select Customer</option>
                          </select>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="customer-info-text" id="customerInfo">
                        <div class="watermark">

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
          <div class="card service-invoice-card so-creation-card po-creation-card ">
            <div class="card-header">
              <div class="row others-info-head">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="head">
                    <i class="fa fa-info"></i>
                    <h4>Others Info</h4>
                  </div>
                </div>
              </div>
            </div>
            <?php
            // console('$company_id, $branch_id, $location_id');
            // console($_SESSION);
            ?>
            <div class="card-body others-info vendor-info so-card-body">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="row others-info-form-view" style="row-gap: 17px;"> 
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <label>Invoice Date: <span class="text-danger">*</span></label>
                      <div>
                        <input type="date" value="<?= date("Y-m-d") ?>" name="invoiceDate" id="invoiceDate" class="form-control" required />
                        <span class="input-group-addon"></span>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <label>Invoice Time: <span class="text-danger">*</span></label>
                      <div>
                        <input type="time" name="invoiceTime" id="invoiceTime" class="form-control" value="<?=date("H:i")?>" required />
                        <span class="input-group-addon"></span>
                      </div>
                    </div>
                    <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                      <label>Delivery Date: <span class="text-danger">*</span></label>
                      <div>
                        <input type="date" value="<?= date("Y-m-d") ?>" name="deliveryDate" id="deliveryDate" class="form-control" required />
                        <span class="input-group-addon"></span>
                      </div>
                    </div> -->
                    <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="form-input">
                        <label for="">Profile Center <span class="text-danger">*</span></label>
                        <select name="profitCenter" class="selct-vendor-dropdown" id="profitCenterDropDown" required>
                          <option value="">Profit Center</option>
                          <?php
                          $funcList = $BranchSoObj->fetchFunctionality()['data'];
                          foreach ($funcList as $func) {
                          ?>
                            <option value="<?= $func['functionalities_id'] ?>"><?= $func['functionalities_name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div> -->
                    <input type="hidden" value="open" name="approvalStatus" id="approvalStatus">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="form-input">
                        <label for="">Credit Period (Days)<span class="text-danger">*</span></label>
                        <input type="text" name="creditPeriod" class="form-control" id="inputCreditPeriod" placeholder="Credit Period " required />
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="form-input">
                        <label for="">Select Bank <span class="text-danger">*</span></label>
                        <select name="bankId" class="form-control" id="bankId" required>
                          <option value="">Select Bank</option>
                          <?php
                          $bankList = $BranchSoObj->fetchCompanyBank()['data'];
                          foreach ($bankList as $bank) {
                            if($bank['bank_name'] != ""){
                            ?>
                            <option value="<?= $bank['id'] ?>"><?php if($bank['bank_name']){ echo '🏦'.$bank['bank_name']; }elseif($bank['cash_account']){echo '💰'.$bank['cash_account'];} ?></option>
                          <?php } } ?>
                        </select>
                      </div>
                    </div>
                    <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="form-input">
                        <label for="">Select Sales Person <span class="text-danger">*</span></label>
                        <select name="kamId" class="form-control" id="kamDropDown" required>
                          <option value="">Select Sales Person</option>
                          <?php
                          $funcList = $BranchSoObj->fetchKamDetails()['data'];
                          foreach ($funcList as $func) {
                          ?>
                            <option value="<?= $func['kamId'] ?>"><?= $func['kamName'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
          <div class="card items-select-table">
            <div class="head-item-table">
              <div class="advanced-serach">
                <div class="hamburger quickadd-hamburger">
                  <div class="wrapper-action">
                    <i class="fa fa-plus"></i>
                  </div>
                </div>
                <div class="nav-action quick-add-input" id="quick-add-input">
                  <div class="form-inline">
                    <label for=""><span class="text-danger">*</span>Quick Add </label>
                    <select id="itemsDropDown" class="form-control">
                      <option value="">Service Type</option>
                      <option value="hello">hello</option>
                      <option value="hello1">hello1</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <!-- <a class="btn btn-primary items-search-btn" data-bs-toggle="modal" data-bs-target="#exampleModal"> <i class="fa fa-search mr-2"></i>Advance Search</a> -->
            <small class="py-2 px-1 rounded alert-warning specialDiscount" id="specialDiscount" style="display: none;">Special Discount</small>
            <table class="table table-sales-order mt-0">
              <thead>
                <tr>
                  <th>Service Code</th>
                  <th>Service Name</th>
                  <th>HSN Code</th>
                  <th>Qty</th>
                  <th>Rate</th>
                  <th>Base Amount</th>
                  <th>Disc %</th>
                  <th>Disc. Amt.</th>
                  <th>GST(%)</th>
                  <th>GST(<span class="rupee-symbol"><?= $currencyIcon ?></span>)</th>
                  <th>Amount</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="itemsTable"></tbody>
              <span id="spanItemsTable"></span>
              <tbody>
                <tr>
                  <td colspan="10" class="text-right p-2" style="border: none; background: none;"> </td>
                  <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Sub Total-</sup></td>
                  <input type="hidden" name="grandSubTotalAmtInp" id="grandSubTotalAmtInp" value="0">
                  <td class="p-2" style="border: none; background: none;padding: 0px !important;"><span class="rupee-symbol"><?= $currencyIcon ?></span><span id="grandSubTotalAmt">0.00</span></th>
                </tr>
                <tr>
                  <td colspan="10" class="text-right p-2" style="border: none; background: none;"> </td>
                  <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Total Discount-</td>
                  <input type="hidden" name="grandTotalDiscountAmtInp" id="grandTotalDiscountAmtInp" value="0">
                  <td class="p-2" style="border: none; background: none;padding: 0px !important;"><span class="rupee-symbol"><?= $currencyIcon ?></span><span id="grandTotalDiscount">0.00</span></td>
                </tr>

                <tr class="p-2">
                  <td colspan="10" class="text-right p-2" style="border: none; background: none;"> </td>
                  <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Total Tax-</td>
                  <input type="hidden" name="grandTaxAmtInp" id="grandTaxAmtInp" value="0">
                  <td class="p-2" style="border: none; background: none;padding: 0px !important;"><span class="rupee-symbol"><?= $currencyIcon ?></span><span id="grandTaxAmt">0.00</span></td>
                </tr>
                <tr class="p-2">
                  <td colspan="10" class="text-right p-2" style="border: none; background: none;"> </td>
                  <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border-top: 3px double !important; background: none;padding: 0px !important;">Total Amount-</td>
                  <input type="hidden" name="grandTotalAmtInp" id="grandTotalAmtInp" value="0">
                  <td class="p-2 font-weight-bold" style="border-top: 3px double !important; background: none;padding: 0px !important;"><span class="rupee-symbol"><?= $currencyIcon ?></span><span id="grandTotalAmt">0.00</span></th>
                </tr>
              </tbody>
            </table>

            <div class="modal fade items-filter-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Advanced Filter Search</h5>
                  </div>
                  <div class="modal-body">

                    <div class="accordion-item filter-serch-accodion">
                      <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button collapsed btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                          Advanced Search Filter
                        </button>
                      </h2>
                      <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                          <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                              <div class="card filter-search-card">
                                <div class="card-body">
                                  <div class="serch-input">
                                    <input type="text" class="form-control" placeholder="search">
                                    <select name="" id="" class="form-control form-select filter-select">
                                      <option value="">search</option>
                                      <option value="">search</option>
                                      <option value="">search</option>
                                    </select>
                                    <input type="text" class="form-control" placeholder="search">
                                    <select name="" id="" class="form-control form-select filter-select">
                                      <option value="">search</option>
                                      <option value="">search</option>
                                      <option value="">search</option>
                                    </select>
                                    <input type="text" class="form-control" placeholder="search">
                                    <select name="" id="" class="form-control form-select filter-select">
                                      <option value="">search</option>
                                      <option value="">search</option>
                                      <option value="">search</option>
                                    </select>
                                  </div>
                                  <button class="btn btn-primary items-search-btn"><i class="fa fa-search mr-2"></i>Search</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
          <button type="submit" name="addNewInvoiceFormSubmitBtn" onclick="return confirm('Are you sure to submitted?')" id="serviceInvoiceCreationBtn" class="btn btn-primary items-search-btn float-right">Submit</button>
        </div>
      </div>
    </form>
  </section>
</div>

<!-- Modal -->
<div class="modal fade" id="addNewCustomerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content card">
      <div class="modal-header card-header py-2 px-3">
        <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Customer</h4>
        <button type="button" class="close text-white" data-dismiss="modal" id="addCustomerCloseBtn" aria-label="Close">x</button>
      </div>
      <div id="notesModalBody" class="modal-body card-body">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
          <div class="form-input my-2">
            <input type="text" name="customerName" id="customerName" placeholder="Enter name" class="form-control">
          </div>
          <div class="form-input my-2">
            <input type="email" name="customerEmail" id="customerEmail" placeholder="Enter email" class="form-control">
          </div>
          <div class="form-input my-2">
            <input type="number" name="customerPhone" id="customerPhone" placeholder="Enter phone *" class="form-control" required>
            <span id="customerPhoneMsg" class="text-xs"></span>
          </div>
          <div class="form-input my-2">
            <button type="submit" name="addCustomerBtn" class="form-control" id="addCustomerBtn">Add</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
require_once("../common/footer.php");
?>

<script>
  $(document).on("click", ".dlt-popup", function() {
    $(this).parent().parent().remove();
  });

  function rm() {
    // $(event.target).closest("tr").remove();
    $(this).parent().parent().parent().remove();
  }

  function addMultiQty(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    //$(`.modal-add-row_${id}`).append(`<tr><td><span class='has-float-label'><input type='date' name='listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]' class='form-control' placeholder='delivery date' required><label>Delivery date</label></span></td><td><span class='has-float-label'><input type='text' name='listItem[${id}][deliverySchedule][${addressRandNo}][quantity]' class='form-control multiQuantity' data-itemid="${id}" id='multiQuantity_${addressRandNo}' placeholder='quantity' required><label>quantity</label></span></td><td><a class='btn btn-danger' onclick='rm()'><i class='fa fa-minus'></i></a></td></tr>`);
    $(`.modal-add-row_${id}`).append(`
      <div class="modal-add-row">
        <div class="row modal-cog-right">
          <div class="col-lg-5 col-md-5 col-sm-5">
              <div class="form-input">
                  <label>Delivery date</label>
                  <input type="date" name="listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]" class="form-control multiDeliveryDate" id="multiDeliveryDate_${id}" placeholder="delivery date" value="<?= $_GET['deliveryDate'] ?>">

              </div>
          </div>
          <div class="col-lg-5 col-md-5 col-sm-5">
              <div class="form-input">
                  <label>Quantity</label>
                  <input type="text" name="listItem[${id}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity" data-itemid="${id}" id="multiQuantity_${id}" placeholder="quantity" value="0">

              </div>
          </div>
          <div class="col-lg-2 col-md-2 col-sm-2 dlt-popup">
              <a style="cursor: pointer" class="btn btn-danger">
                  <i class="fa fa-minus"></i>
              </a>
          </div>
        </div>
      </div>`);
  }
</script>



<script>
  $(document).ready(function() {
    loadItems();

    loadCustomers();


    // **************************************
    function loadItems() {
      // alert();
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-services.php`,
        beforeSend: function() {
          $("#itemsDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          $("#itemsDropDown").html(response);
        }
      });
    }

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

    // add customers
    $("#addCustomerBtn").on("click", function(e) {
      e.preventDefault();
      let customerName = $("#customerName").val();
      let customerEmail = $("#customerEmail").val();
      let customerPhone = $("#customerPhone").val();
      if (customerPhone != "") {
        $.ajax({
          type: "POST",
          url: `ajaxs/so/ajax-customers.php`,
          data: {
            act: "addCustomer",
            customerName,
            customerEmail,
            customerPhone
          },
          beforeSend: function() {
            $("#addCustomerBtn").prop('disabled', true);
            $("#addCustomerBtn").text(`Adding...`);
          },
          success: function(response) {
            console.log('response...');
            console.log(response);
            let data = JSON.parse(response);
            console.log('data...');
            console.log(data);

            $("#customerDropDown").html(response);
            if (data.status === "success") {
              $("#customerName").val("");
              $("#customerEmail").val("");
              $("#customerPhone").val("");
              $("#addCustomerBtn").text(`Add`);
              $("#addCustomerBtn").prop('disabled', false);
              $("#addCustomerCloseBtn").trigger("click");
              loadCustomers();
            }
          }
        });
      } else {
        $("#customerPhoneMsg").html(`<span class="text-xs text-danger">Phone number is required</span>`);
      }
    });

    
    // get customer details by id
    $("#customerDropDown").on("change", function() {
      let customerId = $(this).val();

      if (customerId > 0) {
        $(document).on("click", ".billToCheckbox", function() {
          if ($('input.billToCheckbox').is(':checked')) {
            // $(".shipTo").html(`checked ${customerId}`);
            $.ajax({
              type: "GET",
              url: `ajaxs/so/ajax-customers-address.php`,
              data: {
                act: "customerAddress",
                customerId
              },
              beforeSend: function() {
                $("#shipTo").html(`Loding...`);
              },
              success: function(response) {
                console.log(response);
                $("#shipTo").html(response);
              }
            });
          } else {
            $(".changeAddress").click();
            // $("#shipTo").html(`unchecked ${customerId}`);
          }
        });

        $(".customerIdInp").val(customerId);
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-customers-list.php`,
          data: {
            act: "listItem",
            customerId
          },
          beforeSend: function() {
            $("#customerInfo").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            console.log(response);
            $("#customerInfo").html(response);
            let creditPeriod = $("#spanCreditPeriod").text();
            $("#inputCreditPeriod").val(creditPeriod);

            let customerGst = $(".customerGstin").html();
            console.log(customerGst);
            console.log('customerGst');
          }
        });
      }
    });

    $(document).on("click", "#pills-home-tab", function() {
      $("#saveChanges").html('<button type="button" class="btn btn-primary go">Go</button>');
    });
    $(document).on("click", "#pills-profile-tab", function() {
      $("#saveChanges").html('<button type="button" class="btn btn-primary" id="save">Save</button>');
    });

    // 👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀
    $(document).on('click', '.go', function() {
      let the_value = $('input[name=radioBtn]:radio:checked').val();

      console.log(the_value);
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers-address.php`,
        data: {
          act: "shipAddressRadio",
          addressKey: the_value
        },
        beforeSend: function() {
          $(`.go`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
        },
        success: function(response) {
          console.log(response);
          $(".address-change-modal").hide();
          $(".modal-backdrop").hide();
          $("#shipTo").html(response);
          $("#shippingAddressInp").val(response);
          $('input.billToCheckbox').prop('checked', false);
          $(".go").html('<button type="button" class="btn btn-primary go">Go</button>');
        }
      });
    });

    // submit address form
    $(document).on('click', '#save', function() {
      let customerId = $('.customerIdInp').val();
      let billingNo = $("#billingNo").val();
      let flatNo = $("#flatNo").val();
      let streetName = $("#streetName").val();
      let location = $("#location").val();
      let city = $("#city").val();
      let pinCode = $("#pinCode").val();
      let district = $("#district").val();
      let state = $("#state").val();

      if (billingNo != '' || flatNo != '' || streetName != '' || location != '' || city != '' || pinCode != '' || district != '' || state != '') {
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-customers-address.php`,
          data: {
            act: "shipAddressSave",
            customerId,
            billingNo,
            flatNo,
            streetName,
            location,
            city,
            pinCode,
            district,
            state
          },
          beforeSend: function() {
            $(`#save`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          },
          success: function(response) {
            // console.log(response);
            $(".address-change-modal").hide();
            $(".modal-backdrop").hide();
            $("#shipTo").html(response);
            $('input.billToCheckbox').prop('checked', false);
          }
        });
      } else {
        alert(`All field are required`);
      }
    });
    // 👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀

    // get item details by id
    $("#itemsDropDown").on("change", function() {
      let itemId = $(this).val();
      if (itemId > 0) {
        let deliveryDate = $('#deliveryDate').val();
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-services-list.php`,
          data: {
            act: "listItem",
            itemId,
            deliveryDate
          },
          beforeSend: function() {
            $(`#spanItemsTable`).html(`Loding...`);
          },
          success: function(response) {
            console.log(response);
            $("#itemsTable").append(response);
            calculateGrandTotalAmount();
            $(`#spanItemsTable`).html(``);
          }
        });
      }
    });
    $(document).on("click", ".delItemBtn", function() {
      // let id = ($(this).attr("id")).split("_")[1];
      // $(`#delItemRowBtn_${id}`).remove();
      $(this).parent().parent().remove();
      calculateGrandTotalAmount();
    });

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


    // 🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴
    // auto calculation 
    function calculateGrandTotalAmount() {
      let totalAmount = 0;
      let totalTaxAmount = 0;
      let totalDiscountAmount = 0;
      let grandSubTotalAmt = 0;
      $(".itemTotalPrice").each(function() {
        totalAmount += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });
      $(".itemTotalTax").each(function() {
        totalTaxAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
      });
      $(".itemTotalDiscount").each(function() {
        totalDiscountAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
      });
      $(".itemBaseAmountInp").each(function() {
        grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });
      console.log("Grand = ", totalAmount, totalTaxAmount, totalDiscountAmount);
      // let grandSubTotalAmt = totalAmount - totalTaxAmount - totalDiscountAmount;
      $("#grandSubTotalAmt").html(grandSubTotalAmt.toFixed(2));
      $("#grandSubTotalAmtInp").val(grandSubTotalAmt.toFixed(2));
      $("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2));
      $("#grandTotalDiscountAmtInp").val(totalDiscountAmount.toFixed(2));
      $("#grandTaxAmt").html(totalTaxAmount.toFixed(2));
      $("#grandTaxAmtInp").val(totalTaxAmount.toFixed(2));
      $("#grandTotalAmt").html(totalAmount.toFixed(2));
      $("#grandTotalAmtInp").val(totalAmount.toFixed(2));
    }

    function calculateOneItemAmounts(rowNo) {
      let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
      let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;
      let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;
      let itemTax = (parseFloat($(`#itemTax_${rowNo}`).val())) ? parseFloat($(`#itemTax_${rowNo}`).val()) : 0;

      $(`#multiQuantity_${rowNo}`).val(itemQty);

      let basicPrice = itemUnitPrice * itemQty;
      let totalDiscount = basicPrice * itemDiscount / 100;
      let priceWithDiscount = basicPrice - totalDiscount;
      let totalTax = priceWithDiscount * itemTax / 100;
      let totalItemPrice = priceWithDiscount + totalTax;

      console.log(itemQty, itemUnitPrice, itemDiscount, itemTax);

      $(`#itemBaseAmountInp_${rowNo}`).val(basicPrice.toFixed(2));
      $(`#itemBaseAmountSpan_${rowNo}`).text(basicPrice.toFixed(2));
      $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount.toFixed(2));
      $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toFixed(2));
      $(`#itemTotalTax_${rowNo}`).html(totalTax.toFixed(2));
      $(`#itemTotalTax1_${rowNo}`).val(totalTax.toFixed(2));
      $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice.toFixed(2));
      $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice.toFixed(2));
      $(`#mainQty_${rowNo}`).html(itemQty);
      calculateGrandTotalAmount();
    }

    // #######################################################
    function calculateQuantity(rowNo, itemId, thisVal) {
      // console.log("code", rowNo);
      let itemQty = (parseFloat($(`#itemQty_${itemId}`).val()) > 0) ? parseFloat($(`#itemQty_${itemId}`).val()) : 0;
      let totalQty = 0;
      // console.log("calculateQuantity() ========== Row:", rowNo);
      // console.log("Total qty", itemQty);
      $(".multiQuantity").each(function() {
        if ($(this).data("itemid") == itemId) {
          totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
          // console.log('Qtys":', $(this).val());
        }
      });

      let avlQty = itemQty - totalQty;

      // console.log("Avl qty:", avlQty);

      if (avlQty < 0) {
        let totalQty = 0;
        $(`#multiQuantity_${rowNo}`).val('');
        $(".multiQuantity").each(function() {
          if ($(this).data("itemid") == itemId) {
            totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            // console.log('Qtys":', $(this).val());
          }
        });
        let avlQty = itemQty - totalQty;

        $(`#mainQtymsg_${itemId}`).show();
        $(`#mainQtymsg_${itemId}`).html("[Error! Delivery QTY should equal to order QTY.]");
        $(`#mainQty_${itemId}`).html(avlQty);
      } else {
        let totalQty = 0;
        $(".multiQuantity").each(function() {
          if ($(this).data("itemid") == itemId) {
            totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            // console.log('Qtys":', $(this).val());
          }
        });

        let avlQty = itemQty - totalQty;

        $(`#mainQtymsg_${itemId}`).hide();
        $(`#mainQty_${itemId}`).html(avlQty);
      }
      if (avlQty == 0) {
        $(`#saveClose_${itemId}`).show();
        $(`#saveCloseLoading_${itemId}`).hide();
      } else {
        $(`#saveClose_${itemId}`).hide();
        $(`#saveCloseLoading_${itemId}`).show();
        $(`#setAvlQty_${itemId}`).html(avlQty);
      }
    }

    $(document).on("keyup blur click", ".itemQty", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemVal = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
      if (itemVal <= 0) {
        // let itemVal = $(`#itemQty_${rowNo}`).val(1);
        document.getElementById("serviceInvoiceCreationBtn").disabled = true;
      } else {
        document.getElementById("serviceInvoiceCreationBtn").disabled = false;
      }
      calculateOneItemAmounts(rowNo);
    });

    $(document).on("keyup", ".itemUnitPrice", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      calculateOneItemAmounts(rowNo);
    });

    function checkSpecialDiscount() {
      let isSpecialDiscountApplied = false;

      $(".itemDiscount").each(function() {
        let rowNum = ($(this).attr("id")).split("_")[1];
        let discountPercentage = parseFloat($(this).val());
        discountPercentage = discountPercentage > 0 ? discountPercentage : 0;
        let maxDiscountPercentage = parseFloat($(`#itemMaxDiscount_${rowNum}`).html());
        maxDiscountPercentage = maxDiscountPercentage > 0 ? maxDiscountPercentage : 0;
        if (discountPercentage > maxDiscountPercentage) {
          isSpecialDiscountApplied = true;
        }
      });

      if (isSpecialDiscountApplied) {
        $(`#approvalStatus`).val(`12`);
        console.log('max');
      } else {
        $(`#approvalStatus`).val(`14`);
        console.log('ok');
      }
    }


    $(document).on("keyup", ".itemDiscount", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(this).val();
      calculateOneItemAmounts(rowNo);
      // itemMaxDiscount(rowNo, keyValue);
      checkSpecialDiscount();
      // $(`#itemTotalDiscount1_${rowNo}`).attr('disabled', 'disabled');
    });

    // #######################################################
    $(document).on("keyup blur click change", ".multiQuantity", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemid = ($(this).data("itemid"));
      let thisVal = ($(this).val());
      calculateQuantity(rowNo, itemid, thisVal);
    });

    // #######################################################
    $(document).on("click", ".itemRemarksIcon", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      $(`#itemRemarks_${rowNo}`).toggle();
    });

    // #######################################################
    $(document).on("blur", ".itemTotalDiscount1", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemDiscountAmt = ($(this).val());

      let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
      let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;

      let totalAmt = itemQty * itemUnitPrice;
      let discountPercentage = itemDiscountAmt * 100 / totalAmt;

      $(`#itemDiscount_${rowNo}`).val(discountPercentage.toFixed(2));

      // let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;

      console.log('total', itemQty, itemUnitPrice, discountPercentage);
      calculateOneItemAmounts(rowNo);

      // $(`#itemDiscount_${rowNo}`).attr('disabled', 'disabled');
      // discountCalculate(rowNo, thisVal);
    });

    // allItemsBtn
    $("#allItemsBtn").on('click', function() {
      window.location.href = "";
    })

    // itemWiseSearch
    $("#itemWiseSearch").on('click', function() {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-so-list.php`,
        data: {
          act: "itemWiseSearch"
        },
        beforeSend: function() {
          $(".tableDataBody").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          console.log(response);
          $(".tableDataBody").html(response);
        }
      });
    })

    $(function() {
      $("#datepicker").datepicker({
        autoclose: true,
        todayHighlight: true
      }).datepicker('update', new Date());
    });

  });

  $(document).on("click", "#btnSearchCollpase", function() {
    sec = document.getElementById("btnSearchCollpase").parentElement;
    coll = sec.getElementsByClassName("collapsible-content")[0];

    if (sec.style.width != '100%') {
      sec.style.width = '100%';
    } else {
      sec.style.width = 'auto';
    }

    if (coll.style.height != 'auto') {
      coll.style.height = 'auto';
    } else {
      coll.style.height = '0px';
    }

    $(this).children().toggleClass("fa-search fa-times");
  });


  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('show');
    $('#overlay').toggleClass('show');
    $('.nav-action').toggleClass('show');
  });



  $('#itemsDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('#customerDropDown')
    .select2()
    .on('select2:open', () => {
      $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewCustomerModal">Add New</a></div>`);
    });
  $('#profitCenterDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('#kamDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
</script>

<script src="<?= BASE_URL; ?>public/validations/serviceInvoiceValidation.js"></script>