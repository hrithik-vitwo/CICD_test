<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");

// console($_SESSION);

$quotation_createion = isset($_GET['quotation_createion']);
$sales_order_creation = isset($_GET['sales_order_creation']);
$quotation_to_so = isset($_GET['quotation_to_so']);
$create_service_invoice = isset($_GET['create_service_invoice']);
$so_to_invoice = isset($_GET['so_to_invoice']);
$pgi_to_invoice = isset($_GET['pgi_to_invoice']);
$invoiceType = 'direct';

if ($_GET['create_service_invoice']) {
  $invoiceType = "service";
} elseif ($_GET['quotation']) {
  $invoiceType = "quotation_to_invoice";
} elseif ($_GET['so_to_invoice']) {
  $invoiceType = "so_to_invoice";
} elseif ($_GET['pgi_to_invoice']) {
  $invoiceType = "pgi_to_invoice";
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

$BranchSoObj = new BranchSo();


$getItemsGroupObj = $BranchSoObj->getItemsGroup();
$getItemsGroup = $getItemsGroupObj['data'];

$fetchAllItemSummaryObj = $BranchSoObj->fetchAllItemSummary();
$fetchAllItemSummary = $fetchAllItemSummaryObj['data'];

// console($fetchAllItemSummaryObj);
?>

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/jquery.fancy.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<style>
  .wrapper.wrapper-isotop {
    background: #fff;
    max-width: 100%;
    height: 547px;
    box-shadow: 0px 25px 30px -20px rgba(0, 0, 0, 0.2);
    overflow-y: auto;
    overflow-x: hidden;
  }

  .wrapper-isotop .filter {
    padding: 20px 0;
    margin-bottom: 20px;
    border-bottom: solid 1px #e3e3e3;
    text-align: center;
    font-size: 12px;
    position: sticky;
    top: calc(100% - 548px);
    background: #fff;
    z-index: 99;
  }

  .wrapper-isotop .filter a {
    margin-right: 10px;
    color: #666;
    text-decoration: none;
    border: 1px solid #ccc;
    padding: 4px 15px;
    border-radius: 50px;
    display: inline-block;
  }

  .wrapper-isotop .filter a.current {
    background: #003060;
    border: 1px solid #003060;
    color: #f9f9f9;
  }

  .wrapper-isotop .grid {
    margin: 0 auto;
    padding: 10px;
    -webkit-perspective: 1000px;
    perspective: 1000px;
  }

  .wrapper-isotop .grid-item {
    width: 180px;
    height: 100px;
    margin-bottom: 10px;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
    position: relative;
  }

  .wrapper-isotop .fancybox {
    display: block;
    width: 100%;
    height: 100%;
    height: 100%;
    width: 100%;
    border-radius: 4px;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    transition: all 0.5s;
    background-color: #666;
  }

  .grid-item:hover .fancybox {
    transform: scale(1.1);
  }

  .content-wrapper {
    background: #dbe5ee !important;
    height: auto !important;
  }

  .card-header {
    background: #fff !important;
    border-radius: 0 !important;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .card-header h4,
  .card-header ion-icon {
    color: #000 !important;
    font-weight: 600;
  }

  .card-body {
    background: #fff;
    min-height: auto !important;
  }

  .fancybox-wrap.fancybox-desktop.fancybox-type-inline.fancybox-opened,
  .fancybox-overlay-fixed {
    display: none !important;
  }

  @media (max-width: 575px) {
    .card.direct-create-invoice-card .customer-info-form-view .select2-container {
      width: 100% !important;
    }

    .input-box.customer-select span.select2.select2-container.select2-container {
      width: 100% !important;
    }

    .wrapper-isotop .filter a {
      width: 40%;
      margin-right: 0;
      color: #666;
      text-decoration: none;
      border: 1px solid #ccc;
      padding: 10px 15px;
      border-radius: 50px;
      display: inline-block;
      margin: 10px;
    }

    .btns-group {
      margin: 15px 0;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 7px;
    }

    .btns-group button {
      width: 100px;
      padding: 10px;
    }
  }
</style>

<input type="hidden" value="<?= $branchGstinCode ?>" class="branchGstin">

<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
        <?php if ($sales_order_creation) { ?>
          <li class="breadcrumb-item active"><a href="manage-sales-orders.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Orders List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Sales Orders</a></li>
        <?php } else if ($create_service_invoice) { ?>
          <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Service Invoice</a></li>
        <?php } else if ($quotation_createion) { ?>
          <li class="breadcrumb-item active"><a href="manage-quotations.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Quotation List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Quotation</a></li>
        <?php } else if (isset($_GET['quotation'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Quotation to Invoice</a></li>
        <?php } else if (isset($_GET['quotation_to_so'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-sales-orders.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Order List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Quotation to Sales Order</a></li>
        <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create PGI to Invoice</a></li>
        <?php } else { ?>
          <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Goods Invoice</a></li>
        <?php } ?>
        <li class="back-button">
          <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
            <i class="fa fa-reply po-list-icon"></i>
          </a>
        </li>
      </ol>

      <span style="display: none;" class="companyCurrencyName"><?= $currencyName ?></span>
      <form action="" method="POST" id="addNewSOForm">

        <input type="hidden" value="<?= $invoiceType ?>" name="ivType">
        <input type="hidden" value="<?= $currencyName ?>" name="currencyName" class="currencyName">
        <?php if (isset($_GET['quotation_to_so'])) { ?>
          <input type="hidden" value="<?= $_GET['quotation_to_so'] ?>" name="quotationId" class="quotation_to_so">
        <?php } else if (isset($_GET['quotation'])) { ?>
          <input type="hidden" value="<?= $_GET['quotation'] ?>" name="quotationId" class="quotation_to_so">
        <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
          <input type="hidden" value="<?= $_GET['pgi_to_invoice'] ?>" name="pgi_to_invoice" class="pgi_to_invoice">
        <?php } else if (isset($_GET['so_to_invoice'])) { ?>
          <input type="hidden" value="<?= $_GET['so_to_invoice'] ?>" name="so_to_invoice" class="so_to_invoice">
        <?php } ?>
        <div class="row">
          <div class="col-lg-5 col-md-5 col-sm-12">
            <div class="card direct-create-invoice-card so-creation-card">
              <div class="card-header">

                <ion-icon name="people-outline"></ion-icon>
                <h4>Customer Info</h4>
                <input type="hidden" class="customerIdInp" value="0">
              </div>

              <div class="card-body others-info vendor-info so-card-body">
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="row customer-info-form-view" style="row-gap: 15px;">
                      <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="input-box customer-select">
                          <span class="text-danger">*</span>
                          <select name="customerId" id="customerDropDown" class="form-control" required>
                            <option value="">Select Customer</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-6 col-md-6 col-sm-12">
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
                      </div>
                      <div style="display: none;" class="col-lg-6 col-md-6 col-sm-6">
                        <!-- <label for="" class="label-hidden">label</label> -->
                        <label for="">Customer Conversion Rate</label>
                        <div class="dynamic-currency mt-2">
                          <input type="text" class="form-control" id="curr_rate" name="curr_rate" value="1">
                          <select id="" name="currency" class="form-control currencyDropdown rupee-symbol">
                            <?php
                            $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                            foreach ($curr['data'] as $data) {
                            ?>
                              <option value="<?= $data['currency_id'] ?>_<?= $data['currency_icon'] ?>_<?= $data['currency_name'] ?>"><?= $data['currency_icon'] ?><?= $data['currency_name'] ?></option>
                            <?php
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="form-input">
                          <label for="">Barcode Scanner</label>
                          <input type="text" class="form-control" name="barcodescanner" placeholder="barcode scanner">
                        </div>
                      </div>
                      <div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="card bg-white items-select-table rounded-0">
              <div class="head-item-table">
                <div class="advanced-serach">
                  <!-- <div class="hamburger quickadd-hamburger">
                    <div class="wrapper-action">
                      <i class="fa fa-plus"></i>
                    </div>
                  </div> -->
                  <div class="nav-action quick-add-input d-flex" id="quick-add-input">
                    <?php if ($sales_order_creation || $quotation_createion || $quotation_to_so) { ?>
                      <div class="form-inline">
                        <label for="" style="width: 100%;">Order For <span class="text-danger">*</span></label>
                        <select name="goodsType" class="form-control" id="goodsType" required>
                          <option value="">Select One</option>
                          <option <?php if ($quotationGoodsType == "both") {
                                    echo "selected";
                                  } ?> value="both">Both</option>
                          <option <?php if ($quotationGoodsType == "material") {
                                    echo "selected";
                                  } ?> value="material">Goods</option>
                          <option <?php if ($quotationGoodsType == "service") {
                                    echo "selected";
                                  } ?> value="service">Services</option>
                        </select>
                      </div>
                    <?php } ?>
                    <div class="form-inline">
                      <label for="">Quick Add <span class="text-danger">*</span></label>
                      <select id="itemsDropDown" class="form-control">
                        <option value="">Select One</option>
                      </select>
                    </div>
                    <div class="form-inline recurringDiv" style="display: none;">
                      <input type="checkbox" name="makeRecurring" id="makeRecurring" data-toggle="modal" data-target="#recurringModal">
                      <label for="makeRecurring" style="user-select: none;">Make Recurring</label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Recurring Modal -->
              <div class="modal fade" id="recurringModal" data-bs-backdrop="true" data-bs-keyboard="false" tabindex="-1" aria-labelledby="recurringModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content card itemModalContent">
                    <div class="modal-header card-header py-2 px-3">
                      <h4 class="modal-title font-monospace text-md text-white" id="itemModalLabel">Subscription</h4>
                      <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div id="itemModalBody" class="modal-body card-body">
                      <div class="row">
                        <div class="col-12">
                          <div class="form-input">
                            <label for="">Repeat Every <span class="text-danger">*</span></label>
                            <select name="repeatEvery" class="form-control" id="repeatEveryDropDown">
                              <option value="1">Day</option>
                              <option value="7">Week</option>
                              <option value="15">15th Days</option>
                              <option value="30">Monthly</option>
                              <option value="92">Quarterly</option>
                              <option value="183">Half-Yearly</option>
                              <option value="366">Yearly</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="form-input">
                            <label for="">Start On</label>
                            <input type="date" class="form-control" value="<?= date("Y-m-d") ?>" name="startOn" id="startOn">
                          </div>
                        </div>
                        <div class="col-8">
                          <div class="form-input">
                            <label for="">End On</label>
                            <input type="date" class="form-control" name="endOn" id="endOn">
                          </div>
                        </div>
                        <div class="col-4">
                          <div class="form-input" style="margin-top: 29px !important;">
                            <label for="">Never Expire</label>
                            <input type="checkbox" name="neverExpire" id="neverExpire">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <small class="py-2 px-1 rounded alert-dark specialDiscount" id="specialDiscount" style="display: none;">Special Discount</small>
              <table class="table table-sales-order mt-0">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody id="itemsTable"></tbody>
                <span id="spanItemsTable"></span>
                <tbody>
                  <tr>
                    <td colspan="2" class="text-left p-2 totalCal bg-light" style="border-bottom: 1px solid #ccc;">Sub Total</sup></td>
                    <input type="hidden" name="grandSubTotalAmtInp" id="grandSubTotalAmtInp" value="0">
                    <td colspan="2" class="p-2 text-right" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                      <small class="text-large font-weight-bold text-success">
                        <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandSubTotalAmt">0.00</span>
                      </small>
                      <small class="text-small font-weight-bold text-primary convertedDiv">
                        (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandSubTotalAmt">0.00</span>)
                      </small>
                    </td>
                  </tr>
                  <tr>

                    <td colspan="2" class="text-left p-2 totalCal bg-light" style="border-bottom: 1px solid #ccc;">Total Discount</td>
                    <input type="hidden" name="grandTotalDiscountAmtInp" id="grandTotalDiscountAmtInp" value="0">
                    <td colspan="2" class="p-2 text-right" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                      <small class="text-large font-weight-bold text-success">
                        <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTotalDiscount">0.00</span>
                      </small>
                      <small class="text-small font-weight-bold text-primary convertedDiv">
                        (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandTotalDiscountAmount">0.00</span>)
                      </small>
                    </td>
                  </tr>

                  <tr class="p-2 igstTr" style="display:none">

                    <td colspan="2" class="text-left p-2 totalCal bg-light" style="border-bottom: 1px solid #ccc;">IGST</td>
                    <input type="hidden" name="grandTaxAmtInp" id="grandTaxAmtInp" value="0">
                    <td colspan="2" class="p-2 text-right" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                      <small class="text-large font-weight-bold text-success">
                        <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTaxAmt">0.00</span>
                      </small>
                      <small class="text-small font-weight-bold text-primary convertedDiv">
                        (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandTaxAmount">0.00</span>)
                      </small>
                    </td>
                  </tr>
                  <tr class="p-2 cgstTr" style="display:none">

                    <td colspan="2" class="text-left p-2 totalCal bg-light" style="border-bottom: 1px solid #ccc;">CGST</td>
                    <!-- <input type="hidden" name="grandSgstCgstAmtInp" id="grandSgstCgstAmtInp" value="0"> -->
                    <td colspan="2" class="p-2 text-right" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                      <small class="text-large font-weight-bold text-success">
                        <span class="rupee-symbol"><?= $currencyName ?> </span><span class="grandSgstCgstAmt">0.00</span>
                      </small>
                      <small class="text-small font-weight-bold text-primary convertedDiv">
                        (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span class="convertedGrandSgstCgstAmt">0.00</span>)
                      </small>
                    </td>
                  </tr>
                  <tr class="p-2 sgstTr" style="display:none">

                    <td colspan="2" class="text-left p-2 totalCal bg-light" style="border-bottom: 1px solid #ccc;">SGST</td>
                    <!-- <input type="hidden" name="grandSgstCgstAmtInp" id="grandSgstCgstAmtInp" value="0"> -->
                    <td colspan="2" class="p-2 text-right" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                      <small class="text-large font-weight-bold text-success">
                        <span class="rupee-symbol"><?= $currencyName ?> </span><span class="grandSgstCgstAmt">0.00</span>
                      </small>
                      <small class="text-small font-weight-bold text-primary convertedDiv">
                        (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span class="convertedGrandSgstCgstAmt">0.00</span>)
                      </small>
                    </td>
                  </tr>
                  <tr class="p-2">
                    <td colspan="2" class="text-left p-2 font-weight-bold totalCal bg-light" style="border-bottom: 1px solid #ccc;">Total Amount</td>
                    <input type="hidden" name="grandTotalAmtInp" id="grandTotalAmtInp" value="0">
                    <td colspan="2" class="p-2 text-right font-weight-bold" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                      <small class="text-large font-weight-bold text-success">
                        <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTotalAmt">0.00</span>
                      </small>
                      <small class="text-small font-weight-bold text-primary convertedDiv">
                        (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandTotalAmt">0.00</span>)
                      </small>
                    </td>
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
            <div class="row fob-section" style="margin: 0px 0px 20px 0px;padding: 10px 0px;border-radius: 10px;box-shadow: 0 0 15px #d8d8d8b3;border: 1px solid #d0d0d0;">
              <div class="d-flex">
                <label for="fob" style="display: flex; align-items: center;" class="mb-0">
                  <p class="pr-2"> If this is the FOB, Please Check </p>
                  <input type="checkbox" id="fob">
                  <input type="hidden" name="fobCheckbox" id="fobCheckbox" value="unchecked">
                </label>
              </div>
            </div>
            <div class="card p-3 items-select-table modal-add-row_537" id="otherCostCard" style="display: none;">
              <h6>Please raise the service purchase request form</h6>
              <div class="row othe-cost-infor">
                <div class="col-lg-5 col-md-12 col-sm-12">
                  <div class="form-input">
                    <label for="">Services</label>
                    <!-- <textarea class="form-control" placeholder="Description" name="otherCostDetails[12345][services]"></textarea> -->
                    <input type="hidden" name="otherCostDetails[12345][itemCode]">
                    <input type="hidden" name="otherCostDetails[12345][itemName]">
                    <select name="otherCostDetails[12345][services]" class="selct-vendor-dropdown" id="servicesDropDown">
                      <option value="">Select One</option>
                      <?php foreach ($serviceList as $service) { ?>
                        <option value="<?= $service["itemId"] ?>"><?= $service['itemName'] ?><small>(<?= $service['itemCode'] ?>)[<?= $service['goodsType'] ?>]</small></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12">
                  <div class="form-input">
                    <label for="">Qty</label>
                    <input step="0.01" type="number" class="form-control" placeholder="Qty" name="otherCostDetails[12345][qty]">
                  </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                  <div class="add-btn-plus">
                    <a style="cursor: pointer" class="btn btn-primary" onclick="addOtherCost(537)">
                      <i class="fa fa-plus"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12">
            <div class="wrapper wrapper-isotop">
              <div class="filter">
                <a href="#" data-filter="*" class="current">All</a>
                <?php foreach ($getItemsGroup as $groupKey => $oneGroup) { ?>
                  <a href="#" data-filter=".<?= $oneGroup['goodGroupId'] ?>_group"><?= $oneGroup['goodGroupName'] ?></a>
                <?php } ?>
                <!-- <a href="#" data-filter="*" class="current">All Categories</a>
                <a href="#" data-filter=".category-one">Category-one</a>
                <a href="#" data-filter=".category-two">Category-two</a>
                <a href="#" data-filter=".category-three">Category-three</a>
                <a href="#" data-filter=".category-four">Category-four</a> -->
              </div>
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 px-5">
                  <div class="form-input">
                    <input type="search" class="form-control" placeholder="serach">
                  </div>
                </div>
              </div>
              <div class="grid">
                <?php
                foreach ($fetchAllItemSummary as $summaryKey => $oneSummary) {
                  $itemStocks = $BranchSoObj->deliveryCreateItemQty($oneSummary['itemId'])['sumOfBatches'];
                ?>
                  <div class="grid-item <?= $oneSummary['goodsGroup'] ?>_group">
                    <a href="" type="button" class="fancybox text-light" style="display: flex; align-items: center; justify-content: center;flex-direction: column; text-decoration: none;">
                      <div class="oneItem" id="oneItem_<?= $oneSummary['itemId'] ?>">
                        <?= $oneSummary['itemName'] ?>
                      </div>
                      <div class="text-xs">
                        <?= $itemStocks ?>
                      </div>
                    </a>
                  </div>
                <?php } ?>

                <!-- <div class="grid-item category-three">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-three">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-one">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-one">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-four">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-three">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-three">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-three">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-two">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-four">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-three">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-three">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-two">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-three">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-three">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-two">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-three">
                  <a class="fancybox" href="#"></a>
                </div>
                <div class="grid-item category-three">
                  <a class="fancybox" href="#"></a>
                </div> -->
              </div>
            </div>
          </div>
        </div>
        <div class="btns-group">
          <button class="btn btn-danger">Cancel</button>
          <button class="btn btn-primary">Payment</button>
        </div>
      </form>
  </section>
</div>
</div>
<!-- Modal -->
<div class="modal fade" id="itemModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="itemModalContent modal-content card">
      <div class="modal-header card-header py-2 px-3">
        <h4 class="modal-title font-monospace text-md text-white" id="itemModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
      </div>
      <div id="itemModalBody" class="modal-body card-body">
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade add-customer-modal" id="addNewCustomerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content card">
      <div class="modal-header card-header py-2 px-3">
        <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Customer</h4>
        <button type="button" class="close text-white" data-dismiss="modal" id="addCustomerCloseBtn" aria-label="Close">x</button>
      </div>
      <div id="notesModalBody" class="modal-body card-body">


        <div class="row">
          <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
            <div class="multisteps-form__progress">
              <button class="multisteps-form__progress-btn js-active" type="button" title="User Info">Basic Details</button>
              <button class="multisteps-form__progress-btn" type="button" title="Comments" id="poc_btn" disabled>POC Details</button>
            </div>
          </div>
        </div>
        <!--form panels-->
        <div class="row">
          <div class="col-12 col-lg-8 m-auto">
            <form class="multisteps-form__form" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
              <input type="hidden" name="createdatamultiform" id="createdatamultiform" value="">
              <input type="hidden" name="company_id" id="company_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]; ?>">
              <input type="hidden" name="company_branch_id" id="company_branch_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]; ?>">

              <!--single form panel-->
              <div class="multisteps-form__panel js-active" data-animation="scaleIn">
                <div class="card vendor-details-card mb-0">
                  <div class="card-header p-3">
                    <div class="display-flex">
                      <div class="head">
                        <i class="fa fa-user"></i>
                        <h4>Basic Details</h4>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <div class="row">

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>GSTIN</label>
                            <input type="text" class="form-control" name="customer_gstin" id="customer_gstin" value="">

                          </div>

                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Pan *</label>
                            <input type="text" class="form-control" name="customer_pan" id="customer_pan" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Customer Name</label>
                            <input type="text" class="form-control" name="trade_name" id="trade_name" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Constitution of Business</label>
                            <input type="text" class="form-control" name="con_business" id="con_business" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>States</label>

                            <!-- <select id="state" name="state" class="form-control stateDropDown">
                      <?php
                      $state_sql = queryGet("SELECT * FROM `erp_gst_state_code`", true);
                      $state_data = $state_sql['data'];
                      foreach ($state_data as $data) {

                      ?>

                                  <option value="<?= $data['gstStateName'] ?>" ><?= $data['gstStateName'] ?></option>  
                                  <?php
                                }
                                  ?>
                      </select>  -->

                            <input type="text" class="form-control" name="state" id="state" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>City</label>
                            <input type="text" class="form-control" name="city" id="city" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>District</label>
                            <input type="text" class="form-control" name="district" id="district" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Location</label>
                            <input type="text" class="form-control" name="location" id="location" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Building Number</label>
                            <input type="text" class="form-control" name="build_no" id="build_no" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Flat Number</label>
                            <input type="text" class="form-control" name="flat_no" id="flat_no" value="">

                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-input">
                            <label>Street Name</label>
                            <input type="text" class="form-control" name="street_name" id="street_name" value="">

                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-input">
                            <label>Pin Code</label>
                            <input type="number" class="form-control" name="pincode" id="pincode" value="">
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-input">
                            <label for="">Company currency</label>
                            <select id="company_currency" name="currency" class="form-control mt-0 form-control-border borderColor">
                              <!--<option value="">Select Currency</option>-->
                              <?php
                              $listResult = getAllCurrencyType();
                              if ($listResult["status"] == "success") {
                                foreach ($listResult["data"] as $listRow) {
                              ?>
                                  <option value="<?php echo $listRow['currency_id']; ?>"><?php echo $listRow['currency_name']; ?></option>
                              <?php }
                              } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6" style="display:none;">
                          <div class="form-input">
                            <label>Opening Blance</label>
                            <input type="hidden" class="form-control" name="opening_balance" id="customer_opening_balance" value="0">
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-input">
                            <label>Credit Period(In Days)</label>
                            <input type="text" class="form-control" name="credit_period" id="customer_credit_period" value="">

                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex">
                      <button class="btn btn-primary ml-auto js-btn-next" id="customerRegFrmNextBtn" type="button" data-toggle="modal" data-target="#visitingCard" title="Next">Next</button>
                    </div>
                  </div>
                </div>
              </div>
              <!--single form panel-->
              <div class="modal fade" id="visitingCard" style="z-index: 999999;" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content m-auto" style="max-width: 375px; border-radius: 20px;">

                    <div class="modal-body p-0" style="overflow: hidden; border-radius: 20px;">
                      <div id="uploadGrnInvoiceDiv" class="create-grn">
                        <div class="upload-files-container">
                          <div class="card visiting-card-upload">
                            <div class="card-header">
                              <div class="head">
                                <h4>Upload Visiting Card</h4>
                              </div>
                            </div>
                            <div class="card-body">
                              <div class="drag-file-area">
                                <i class="fa fa-arrow-up po-list-icon text-center m-auto"></i>
                                <br>
                                <input type="file" class="form-control" id="visitingFileInput" name="" placeholder="Visiting Card Upload" required />
                              </div>
                              <div class="file-block">
                                <div class="progress-bar"> </div>
                              </div>
                              <button type="button" class="upload-button btn btn-primary visiting_card_btn" name="" id="visiting_card_btn"> Upload </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="multisteps-form__panel" data-animation="scaleIn">

                <div class="card vendor-details-card mb-0">
                  <div class="card-header">
                    <div class="head">
                      <h4>POC Details</h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <div class="row">
                        <div class="col-md-12">
                          <label for="">Upload Visiting Card<span class="visiting_loder"></span></label>
                          <input class="visiting_card form-control" type="file" name="visiting_card" id="visiting_card">
                        </div>

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Name of Person*</label>
                            <input type="text" class="form-control" name="customer_authorised_person_name" id="adminName" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Designation</label>
                            <input type="text" class="form-control" name="customer_authorised_person_designation" id="customer_authorised_person_designation" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Phone Number*</label>
                            <input type="text" class="form-control" name="customer_authorised_person_phone" id="adminPhone" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Alternative Phone </label>
                            <input type="text" class="form-control" name="customer_authorised_alt_phone" id="customer_authorised_person_phone" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Email*</label>
                            <input type="email" class="form-control" name="customer_authorised_person_email" id="adminEmail" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Alternative Email</label>
                            <input type="email" class="form-control" name="customer_authorised_alt_email" id="customer_authorised_person_email" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Login Password [Will be send to the POC email]</label>
                            <input type="text" class="form-control" name="adminPassword" id="adminPassword" value="<?php echo rand(00000, 999999) ?>">

                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-input">
                            <label for="">Choose Image</label>
                            <input type="file" class="form-control" name="customer_picture" id="customer_picture">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-input">
                            <label for="" style="visibility: hidden;">Visible for all</label>
                            <select id="customer_visible_to_all" name="customer_visible_to_all" class="select2 form-control mt-0 borderColor">
                              <option value="No"> Only for this location</option>
                              <option value="Yes" selected>Visible For All</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex">
                      <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
                      <button class="btn ml-auto btn-danger add_data" type="button" title="Save As Draft" value="add_draft">Save As Draft</button>
                      <button id="customerCreateBtn" class="btn btn-primary ml-auto add_data" type="button" title="Final Submit" value="add_post">Final Submit</button>
                    </div>
                  </div>
                </div>

              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php require_once("../common/footer.php"); ?>

<script>
  $("#profitCenterDropDown").on("change", function() {
    let functionalArea = $(this).val();

    $.ajax({
      type: "POST",
      url: `ajaxs/so/ajax-generate-inv-number.php`,
      data: {
        act: "getVerientExamplecopy",
        functionalArea: functionalArea
      },
      beforeSend: function() {
        // $("#itemsDropDown").html(`Loding...`);
      },
      success: function(response) {
        console.log('getVerientExamplecopy response...');
        // console.log(JSON.stringify(response));
        let data = JSON.parse(response);
        console.log(data);
        $("#iv_varient").val(data['id']);
        $(".ivnumberexample").html(data['iv_number_example']);
      }
    });
  });

  $("#iv_varient").on("change", function() {
    let vid = $(this).val();
    let functionalArea = $("#profitCenterDropDown").val();

    $.ajax({
      type: "POST",
      url: `ajaxs/so/ajax-generate-inv-number.php`,
      data: {
        act: "getVerientExamplecopy",
        functionalArea: functionalArea,
        vid: vid
      },
      beforeSend: function() {
        // $("#itemsDropDown").html(`Loding...`);
      },
      success: function(response) {
        console.log('getVerientExamplecopy response...');
        // console.log(JSON.stringify(response));
        let data = JSON.parse(response);
        console.log(data);
        // $("#iv_varient").val(data['id']);
        $(".ivnumberexample").html(data['iv_number_example']);
      }
    });
  });


  $(document).on("click", ".dlt-popup", function() {
    $(this).parent().parent().remove();
  });

  function rm() {
    // $(event.target).closest("tr").remove();
    $(this).parent().parent().parent().remove();
  }

  function addOtherCost(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row_${id}`).append(`<div class="row othe-cost-infor">
                                          <div class="col-lg-5 col-md-12 col-sm-12">
                                          <input type="hidden" name="otherCostDetails[${addressRandNo}][itemCode]">
                                          <input type="hidden" name="otherCostDetails[${addressRandNo}][itemName]">
                                              <div class="form-input">
                                                  <label for="">Services</label>
                                                  <select name="otherCostDetails[${addressRandNo}][services]" class="selct-vendor-dropdown" id="servicesDropDown_${addressRandNo}">
                                                    <option value="">Select One</option>
                                                      <?php foreach ($serviceList as $service) { ?>
                                                        <option value="<?= $service["itemId"] ?>"><?= $service['itemName'] ?><small>(<?= $service['itemCode'] ?>)[<?= $service['goodsType'] ?>]</small></option>
                                                      <?php } ?>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="col-lg-5 col-md-12 col-sm-12">
                                              <div class="form-input">
                                                  <label for="">Qty</label>
                                                  <input step="0.01" type="number" class="form-control" placeholder="Qty" name="otherCostDetails[${addressRandNo}][qty]">
                                              </div>
                                          </div>
                                          <div class="col-lg-2 col-md-6 col-sm-6">
                                              <div class="add-btn-minus">
                                                  <a style="cursor: pointer" class="btn btn-danger">
                                                      <i class="fa fa-minus"></i>
                                                  </a>
                                              </div>
                                          </div>
                                      </div>`);

    $(`#servicesDropDown_${addressRandNo}`)
      .select2()
      .on('select2:open', () => {
        // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
      });
  }
  $(document).on("click", ".add-btn-minus", function() {
    $(this).parent().parent().remove();
  });

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
    // 👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀
    $(document).on('click', '.go', function() {
      let the_value = $('input[name=radioBtn]:radio:checked').val();
      let stateCode = the_value.slice(-2);

      $(".address-change-modal").hide();
      $(".modal-backdrop").hide();
      $("#shipTo").html(the_value);
      $("#placeOfSupply1").val(stateCode);
      $("#shippingAddressInp").val(the_value);
      $('input.billToCheckbox').prop('checked', false);

      // $.ajax({
      //   type: "GET",
      //   url: `ajaxs/so/ajax-customers-address.php`,
      //   data: {
      //     act: "shipAddressRadio",
      //     addressKey: the_value
      //   },
      //   beforeSend: function() {
      //     $(`.go`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
      //   },
      //   success: function(response) {
      //     let stateCodeSpan = $('.stateCodeSpan').html();
      //     $(".address-change-modal").hide();
      //     $(".modal-backdrop").hide();
      //     $("#shipTo").html(response);
      //     $("#placeOfSupply1").val(stateCodeSpan);
      //     $("#shippingAddressInp").val(response);
      //     $('input.billToCheckbox').prop('checked', false);
      //     $(".go").html('<button type="button" class="btn btn-primary go">Go</button>');
      //   }
      // });
    });

    $('#fob').on('change', function() {
      if ($('#fob').is(':checked')) {
        $('#fobCheckbox').val('checked');
      } else {
        $('#fobCheckbox').val('unchecked');
      }
    });

    loadItems();
    loadCustomers();

    // **************************************
    function loadItems() {
      // alert();
      let value = $('#goodsType').val();
      let searchUrl = window.location.search;

      goodsType = (value != null && value != undefined) ? value : (searchUrl === "?create_service_invoice" ? 'service' : 'material');

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items-goods-type.php`,
        beforeSend: function() {
          $("#itemsDropDown").html(`<option value="">Loding...</option>`);
        },
        data: {
          act: "goodsType",
          goodsType: goodsType
        },
        success: function(response) {
          $("#itemsDropDown").html(response);
        }
      });
    };

    $("#fob").on("click", function() {
      // alert();
      if ($('#fob').is(':checked')) {
        $("#otherCostCard").show();
      } else {
        $("#otherCostCard").hide();
      }
    });

    $("#soDate").on("change", function() {
      let soDate = $(this).val();
      $(".multiDeliveryDate").val(soDate);
    })

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
            console.log(JSON.stringify(response));
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
              $("#addCustomerBtn").text(`Add`);
              $("#addCustomerCloseBtn").trigger("click");
              loadCustomers();
            }
          }
        });
      } else {
        $("#customerPhoneMsg").html(`<span class="text-xs text-danger">Phone number is required</span>`);
      }
    });

    // customers ********************************
    function loadCustomers() {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers.php`,
        data: {
          customerId: '<?= $customerId ?>'
        },
        beforeSend: function() {
          $("#customerDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          $("#customerDropDown").html(response);
        }
      });
    }

    function addCustomerFunc(customerId) {
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

          let customerGstinCode = $(".customerGstinCode").val();
          let branchGstinCode = $(".branchGstin").val();
          console.log(customerGstinCode, branchGstinCode);

          if (customerGstinCode === branchGstinCode) {
            $(".igstTr").hide();
            $(".cgstTr").show();
            $(".sgstTr").show();
          } else {
            $(".igstTr").show();
            $(".cgstTr").hide();
            $(".sgstTr").hide();
          }
        }
      });
    }

    addCustomerFunc('<?= $customerId; ?>');

    // get customer details by id
    $("#customerDropDown").on("change", function() {
      let customerId = $(this).val();

      if (customerId > 0) {
        $(document).on("click", ".billToCheckbox", function() {
          if ($('input.billToCheckbox').is(':checked')) {
            // $(".shipTo").html(`checked ${customerId}`);
            addCustomerFunc(customerId);
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

            let customerGstinCode = $(".customerGstinCode").val();
            let branchGstinCode = $(".branchGstin").val();
            console.log(customerGstinCode, branchGstinCode);

            if (customerGstinCode === branchGstinCode) {
              $(".igstTr").hide();
              $(".cgstTr").show();
              $(".sgstTr").show();
            } else {
              $(".igstTr").show();
              $(".cgstTr").hide();
              $(".sgstTr").hide();
            }

            // Second AJAX request
            $.ajax({
              url: "ajaxs/so/ajax-customers-invoice-log.php",
              type: "GET",
              data: {
                act: "customersInvoiceLog",
                customerId
              },
              success: function(response2) {
                let data2 = JSON.parse(response2);
                if (data2.status == "success") {
                  console.log("response 2");
                  console.log(data2.data);
                  let profit_center = data2.data.profit_center;
                  let kamId = data2.data.kamId;
                  let complianceInvoiceType = data2.data.complianceInvoiceType;
                  let placeOfSupply = data2.data.placeOfSupply;
                  let invoiceNoFormate = data2.data.invoiceNoFormate;
                  let bank = data2.data.bank;

                  $("#profitCenterDropDown").val(profit_center).trigger("change");
                  $("#compInvoiceType").val(complianceInvoiceType).trigger("change");
                  $("#kamDropDown").val(kamId).trigger("change");
                  $("#bankId").val(bank).trigger("change");
                  $("#placeOfSupply1").val(placeOfSupply).trigger("change");
                  $("#iv_varient").val(invoiceNoFormate).trigger("change");
                } else {
                  console.log('somthing went wrong');
                  $("#profitCenterDropDown").val('').trigger("change");
                  $("#compInvoiceType").val('R').trigger("change");
                  $("#kamDropDown").val('').trigger("change");
                  $("#bankId").val('').trigger("change");
                  $("#placeOfSupply1").val('').trigger("change");
                  $("#iv_varient").val('').trigger("change");
                }
              },
              error: function(xhr, status, error) {
                console.log("Error 2:", error);
              }
            });
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

    // // AddressEdit
    // $(document).on("click", ".billAddressEdit", function() {
    //   let addressId = ($(this).attr("id")).split("_")[1];
    //   $(`.changeAddress`).click();
    //   $(`.newaddress`).click();
    //   alert(addressId)
    // });

    // $(document).on("click", ".shipAddressEdit", function() {
    //   let addressId = ($(this).attr("id")).split("_")[1];
    //   $(`.changeAddress`).click();
    //   $(`.newaddress`).click();
    //   alert(addressId)
    // });

    // submit address form
    $(document).on('click', '#save', function() {
      let customerId = $('.customerIdInp').val();
      let recipientName = $("#recipientName").val();
      let billingNo = $("#billingNo").val();
      let flatNo = $("#flatNo").val();
      let streetName = $("#streetName").val();
      let location = $("#location").val();
      let city = $("#city").val();
      let pinCode = $("#pinCode").val();
      let district = $("#district").val();
      let state = $("#state").val();
      let stateCode = $("#stateCode").val();
      console.log(customerId, billingNo, flatNo, streetName, location, city, pinCode, district, state);

      if (billingNo != '') {
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-customers-address.php`,
          data: {
            act: "shipAddressSave",
            customerId,
            recipientName,
            billingNo,
            flatNo,
            streetName,
            location,
            city,
            pinCode,
            district,
            state,
            stateCode
          },
          beforeSend: function() {
            $(`#save`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          },
          success: function(response) {
            console.log(response);
            $(".address-change-modal").hide();
            $(".modal-backdrop").hide();
            $("#shipTo").html(response);
            $('input.billToCheckbox').prop('checked', false);
          }
        });
      } else {
        alert(`All fields are required`);
      }
    });

    // 👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀

    // if ('<?= $customer_id ?>' > 0) {
    //   $.ajax({
    //     type: "GET",
    //     url: `ajaxs/so/ajax-customers-address.php`,
    //     data: {
    //       act: "customerAddress",
    //       customerId: ''
    //     },
    //     beforeSend: function() {
    //       $("#shipTo").html(`Loding...`);
    //     },
    //     success: function(response) {
    //       console.log(response);
    //       $("#shipTo").html(response);
    //     }
    //   });
    // }

    // get item details by id
    function itemAutoAdd(itemIdArry) {
      let url = window.location.search;
      let param = url.split("=")[0];

      // to toggle FOB
      if (param === "?sales_order_creation" || param === "?quotation_to_so") {
        $(".fob-section").show();
      } else {
        $(".fob-section").hide();
      }

      if (itemIdArry != '') {
        var itemIdArryTo = JSON.parse(itemIdArry);
        $.each(itemIdArryTo, function(index, value) {

          if (value.inventory_item_id > 0) {
            // let deliveryDate = $('#deliveryDate').val();
            $.ajax({
              type: "GET",
              url: `ajaxs/so/ajax-items-list-direct.php`,
              data: {
                act: "listItem",
                itemId: value.inventory_item_id,
                type: param,
                items: value
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
      }
    }

    itemAutoAdd('<?= $itemIdJson; ?>');
    // get item details by id
    $("#itemsDropDown").on("change", function() {

      let itemId = $(this).val();

      let url = window.location.search;
      let param = url.split("=")[0];

      // dynamic value
      const currentURL = window.location.href;
      const ccurl = new URL(currentURL);
      const searchParams = new URLSearchParams(ccurl.search);
      const searchValue = searchParams.get(param.substring(1));

      let companyCurrencyName = '<?= $currencyName ?>';
      let currencyName = ($('.currencyDropdown').val()).split("_")[2];

      if (itemId > 0) {
        let deliveryDate = $('#deliveryDate').val();
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-items-list-direct.php`,
          data: {
            act: "listItem",
            type: param,
            valueId: searchValue,
            itemId
          },
          beforeSend: function() {
            $(`#spanItemsTable`).html(`Loding...`);
          },
          success: function(response) {
            console.log(response);
            $("#itemsTable").append(response);
            calculateGrandTotalAmount();
            $(`#spanItemsTable`).html(``);
            currency_conversion();

            let deliveryDate = $("#deliveryDate").val();
            $(".multiDeliveryDate").val(deliveryDate);

            if (companyCurrencyName !== currencyName) {
              $(`.convertedDiv`).show();
            } else {
              $(`.convertedDiv`).hide();
            }

          }
        });
      }
    });

    // **********
    $(".oneItem").on("click", function() {
      let itemId = ($(this).attr("id")).split("_")[1];

      console.log('itemId******')
      console.log(itemId)

      let url = window.location.search;
      let param = url.split("=")[0];

      // dynamic value
      const currentURL = window.location.href;
      const ccurl = new URL(currentURL);
      const searchParams = new URLSearchParams(ccurl.search);
      const searchValue = searchParams.get(param.substring(1));

      let companyCurrencyName = '<?= $currencyName ?>';
      let currencyName = ($('.currencyDropdown').val()).split("_")[2];

      if (itemId > 0) {
        let deliveryDate = $('#deliveryDate').val();
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-items-list-direct.php`,
          data: {
            act: "listItem",
            type: param,
            valueId: searchValue,
            itemId
          },
          beforeSend: function() {
            $(`#spanItemsTable`).html(`Loding...`);
          },
          success: function(response) {
            console.log(response);
            $("#itemsTable").append(response);
            calculateGrandTotalAmount();
            $(`#spanItemsTable`).html(``);
            currency_conversion();

            let deliveryDate = $("#deliveryDate").val();
            $(".multiDeliveryDate").val(deliveryDate);

            if (companyCurrencyName !== currencyName) {
              $(`.convertedDiv`).show();
            } else {
              $(`.convertedDiv`).hide();
            }

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
    });

    // #######################################################
    $(document).on("click", ".toggleServiceRemarksPen", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      $(`#itemRemarks_${rowNo}`).toggle();
    });

    // 🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴

    // ----------
    // -- this is original code -- 👇🏾
    // ----------
    // function calculateOneItemAmounts(rowNo) {
    //   let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;

    //   // let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;
    //   let originalItemUnitPrice = (parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) > 0) ? parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) : 0;
    //   // let itemUnitPriceOriginal = (parseFloat($(`#itemUnitPriceHidden_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPriceHidden_${rowNo}`).val()) : 0;
    //   let convertedItemUnitPrice = (parseFloat($(`#convertedItemUnitPriceSpan_${rowNo}`).text()) > 0) ? parseFloat($(`#convertedItemUnitPriceSpan_${rowNo}`).text()) : 0;
    //   let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;
    //   let itemTax = (parseFloat($(`#itemTax_${rowNo}`).val())) ? parseFloat($(`#itemTax_${rowNo}`).val()) : 0;

    //   $(`#multiQuantity_${rowNo}`).val(itemQty);

    //   let basicPrice = originalItemUnitPrice * itemQty;
    //   let convertedBasicPrice = convertedItemUnitPrice * itemQty;

    //   let totalDiscount = basicPrice * itemDiscount / 100;
    //   let convertedTotalDiscount = convertedBasicPrice * itemDiscount / 100;

    //   let priceWithDiscount = basicPrice - totalDiscount;
    //   let convertedpriceWithDiscount = convertedBasicPrice - convertedTotalDiscount;

    //   let totalTax = priceWithDiscount * itemTax / 100;
    //   let convertedTotalTax = convertedpriceWithDiscount * itemTax / 100;

    //   let totalItemPrice = (priceWithDiscount + totalTax);
    //   let convertedTotalItemPrice = (convertedpriceWithDiscount + convertedTotalTax);

    //   console.log(itemQty, originalItemUnitPrice, itemDiscount, itemTax);

    //   $(`#itemBaseAmountInp_${rowNo}`).val(basicPrice.toFixed(2).toLocaleString());
    //   $(`#itemBaseAmountSpan_${rowNo}`).text(basicPrice.toFixed(2).toLocaleString());
    //   $(`#convertedItemBaseAmountSpan_${rowNo}`).text(convertedBasicPrice.toFixed(2).toLocaleString());

    //   $(`#itemTotalDiscountHidden_${rowNo}`).val(totalDiscount.toFixed(2).toLocaleString());
    //   $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toFixed(2).toLocaleString());
    //   $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount.toFixed(2).toLocaleString());
    //   $(`#convertedItemDiscountAmountSpan_${rowNo}`).html(convertedTotalDiscount.toFixed(2).toLocaleString());

    //   $(`#itemTotalTax1_${rowNo}`).val(totalTax.toFixed(2).toLocaleString());
    //   $(`#itemTotalTax_${rowNo}`).html(totalTax.toFixed(2).toLocaleString());
    //   $(`#convertedItemTaxAmountSpan_${rowNo}`).html(convertedTotalTax.toFixed(2).toLocaleString());

    //   $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice.toFixed(2).toLocaleString());
    //   $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice.toFixed(2).toLocaleString());
    //   $(`#convertedItemTotalPriceSpan_${rowNo}`).html(convertedTotalItemPrice.toFixed(2).toLocaleString());

    //   $(`#totalItemAmountModal_${rowNo}`).html(totalItemPrice.toFixed(2).toLocaleString());
    //   calculateGrandTotalAmount();
    // }

    // -- generated by chatGPT 🤖🤖🤖 || imranali59059 || original code is above 👆🏾
    // one item calculation 
    function calculateOneItemAmounts(rowNo) {
      let itemQty = parseFloat($(`#itemQty_${rowNo}`).val()) || 0;
      let originalItemUnitPrice = parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
      let convertedItemUnitPrice = parseFloat($(`#convertedItemUnitPriceSpan_${rowNo}`).text()) || 0;
      let itemDiscount = parseFloat($(`#itemDiscount_${rowNo}`).val()) || 0;
      let itemTax = parseFloat($(`#itemTax_${rowNo}`).val()) || 0;

      $(`#multiQuantity_${rowNo}`).val(itemQty);

      let basicPrice = originalItemUnitPrice * itemQty;
      let convertedBasicPrice = convertedItemUnitPrice * itemQty;

      let totalDiscount = basicPrice * itemDiscount / 100;
      let convertedTotalDiscount = convertedBasicPrice * itemDiscount / 100;

      let priceWithDiscount = basicPrice - totalDiscount;
      let convertedPriceWithDiscount = convertedBasicPrice - convertedTotalDiscount;

      let totalTax = priceWithDiscount * itemTax / 100;
      let convertedTotalTax = convertedPriceWithDiscount * itemTax / 100;

      let totalItemPrice = priceWithDiscount + totalTax;
      let convertedTotalItemPrice = convertedPriceWithDiscount + convertedTotalTax;

      console.log(itemQty, originalItemUnitPrice, itemDiscount, itemTax);

      $(`#itemBaseAmountInp_${rowNo}`).val(basicPrice.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));
      $(`#itemBaseAmountSpan_${rowNo}`).text(basicPrice.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));
      $(`#convertedItemBaseAmountSpan_${rowNo}`).text(convertedBasicPrice.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));

      $(`#itemTotalDiscountHidden_${rowNo}`).val(totalDiscount.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));
      $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));
      $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));
      $(`#convertedItemDiscountAmountSpan_${rowNo}`).html(convertedTotalDiscount.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));

      $(`#itemTotalTax1_${rowNo}`).val(totalTax.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));
      $(`#itemTotalTax_${rowNo}`).html(totalTax.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));
      $(`#convertedItemTaxAmountSpan_${rowNo}`).html(convertedTotalTax.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));

      $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));
      $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));
      $(`#convertedItemTotalPriceSpan_${rowNo}`).html(convertedTotalItemPrice.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));

      $(`#totalItemAmountModal_${rowNo}`).html(totalItemPrice.toLocaleString(undefined, {
        minimumFractionDigits: 2
      }));
      calculateGrandTotalAmount();
    }


    // ----------
    // -- this is original code -- 👇🏾
    // ----------
    // auto calculation 
    // function calculateGrandTotalAmount() {
    //   let totalAmount = 0;
    //   let totalAmountOriginal = 0;

    //   let totalTaxAmount = 0;
    //   let totalTaxAmountOriginal = 0;
    //   let convertedItemTaxAmountSpan = 0;

    //   let totalDiscountAmount = 0;
    //   let totalDiscountAmountOriginal = 0;
    //   let convertedItemDiscountAmountSpan = 0;

    //   let itemBaseAmountSpan = 0;
    //   let itemBaseAmountInpOriginal = 0;
    //   let convertedItemBaseAmountSpan = 0;
    //   let convertedItemTotalPrice = 0;

    //   // item total price
    //   $(".itemTotalPrice1").each(function() {
    //     totalAmount += (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
    //   });
    //   $(".itemTotalPrice").each(function() {
    //     totalAmountOriginal += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
    //   });
    //   $(".convertedItemTotalPriceSpan").each(function() {
    //     convertedItemTotalPrice += (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
    //   });

    //   // item total tax
    //   $(".itemTotalTax1").each(function() {
    //     totalTaxAmountOriginal += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
    //   });
    //   $(".itemTotalTax").each(function() {
    //     totalTaxAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
    //   });
    //   $(".convertedItemTaxAmountSpan").each(function() {
    //     convertedItemTaxAmountSpan += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
    //   });

    //   // item total tax
    //   $(".itemTotalDiscountHidden").each(function() {
    //     totalDiscountAmountOriginal += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
    //   });
    //   $(".itemTotalDiscount").each(function() {
    //     totalDiscountAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
    //   });
    //   $(".convertedItemDiscountAmountSpan").each(function() {
    //     convertedItemDiscountAmountSpan += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
    //   });

    //   // item total tax
    //   $(".itemBaseAmountInp").each(function() {
    //     itemBaseAmountInpOriginal += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
    //   });
    //   $(".itemBaseAmountSpan").each(function() {
    //     itemBaseAmountSpan += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
    //   });
    //   $(".convertedItemBaseAmountSpan").each(function() {
    //     convertedItemBaseAmountSpan += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
    //   });

    //   let compInvoiceType = $("#compInvoiceType").val();
    //   let grandTotalAmountAfterOriginal = (totalAmountOriginal - totalTaxAmount);
    //   let grandTotalAmountAfter = (totalAmount - totalTaxAmount);
    //   let convertedGrandTotalAmountWithoutTax = (convertedItemTotalPrice - convertedItemTaxAmountSpan);

    //   if (compInvoiceType === "CBW" || compInvoiceType === "LUT" || compInvoiceType === "SEWOP") {
    //     $(".grandSgstCgstAmt").html(0);
    //     $(".convertedGrandSgstCgstAmt").html(0);

    //     $("#grandTaxAmt").html(0);
    //     $("#convertedGrandTaxAmount").html(0);

    //     $("#grandTaxAmtInp").val(0);

    //     $("#grandTotalAmt").html(grandTotalAmountAfter.toFixed(2).toLocaleString());
    //     $("#grandTotalAmtInp").val(grandTotalAmountAfter.toFixed(2).toLocaleString());
    //     $("#convertedGrandTotalAmt").text(convertedGrandTotalAmountWithoutTax.toFixed(2).toLocaleString());
    //   } else {
    //     $(".grandSgstCgstAmt").html(totalTaxAmount.toFixed(2).toLocaleString() / 2);
    //     $(".convertedGrandSgstCgstAmt").html(convertedItemTaxAmountSpan.toFixed(2).toLocaleString() / 2);

    //     $("#grandTaxAmt").html(totalTaxAmount.toFixed(2).toLocaleString());
    //     $("#convertedGrandTaxAmount").html(convertedItemTaxAmountSpan.toFixed(2).toLocaleString());

    //     $("#grandTaxAmtInp").val(totalTaxAmountOriginal.toFixed(2).toLocaleString());

    //     $("#grandSubTotalAmt").html(itemBaseAmountSpan.toFixed(2).toLocaleString());
    //     $("#grandSubTotalAmtInp").val(itemBaseAmountInpOriginal.toFixed(2).toLocaleString());
    //     $("#convertedGrandSubTotalAmt").text(convertedItemBaseAmountSpan.toFixed(2).toLocaleString());

    //     $("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2).toLocaleString());
    //     $("#grandTotalDiscountAmtInp").val(totalDiscountAmountOriginal.toFixed(2).toLocaleString());
    //     $("#convertedGrandTotalDiscountAmount").text(convertedItemDiscountAmountSpan.toFixed(2).toLocaleString());

    //     $("#grandTotalAmt").html(totalAmount.toFixed(2).toLocaleString());
    //     $("#grandTotalAmtInp").val(totalAmountOriginal.toFixed(2).toLocaleString());
    //     $("#convertedGrandTotalAmt").text(convertedItemTotalPrice.toFixed(2).toLocaleString());
    //   }
    // }

    // -- generated by chatGPT 🤖🤖🤖 || imranali59059 || original code is above 👆🏾
    function calculateGrandTotalAmount() {
      let totalAmount = 0;
      let totalAmountOriginal = 0;

      let totalTaxAmount = 0;
      let totalTaxAmountOriginal = 0;
      let convertedItemTaxAmountSpan = 0;

      let totalDiscountAmount = 0;
      let totalDiscountAmountOriginal = 0;
      let convertedItemDiscountAmountSpan = 0;

      let itemBaseAmountSpan = 0;
      let itemBaseAmountInpOriginal = 0;
      let convertedItemBaseAmountSpan = 0;
      let convertedItemTotalPrice = 0;

      // item total price
      $(".itemTotalPrice1").each(function() {
        totalAmount += parseFloat($(this).text().replace(/,/g, "")) || 0;
      });
      $(".itemTotalPrice").each(function() {
        totalAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      $(".convertedItemTotalPriceSpan").each(function() {
        convertedItemTotalPrice += parseFloat($(this).text().replace(/,/g, "")) || 0;
      });

      // item total tax
      $(".itemTotalTax1").each(function() {
        totalTaxAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      $(".itemTotalTax").each(function() {
        totalTaxAmount += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });
      $(".convertedItemTaxAmountSpan").each(function() {
        convertedItemTaxAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });

      // item total discount
      $(".itemTotalDiscountHidden").each(function() {
        totalDiscountAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      $(".itemTotalDiscount").each(function() {
        totalDiscountAmount += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });
      $(".convertedItemDiscountAmountSpan").each(function() {
        convertedItemDiscountAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });

      // item base amount
      $(".itemBaseAmountInp").each(function() {
        itemBaseAmountInpOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      $(".itemBaseAmountSpan").each(function() {
        itemBaseAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });
      $(".convertedItemBaseAmountSpan").each(function() {
        convertedItemBaseAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });

      let compInvoiceType = $("#compInvoiceType").val();
      let grandTotalAmountAfterOriginal = totalAmountOriginal - totalTaxAmount;
      let grandTotalAmountAfter = totalAmount - totalTaxAmount;
      let convertedGrandTotalAmountWithoutTax = convertedItemTotalPrice - convertedItemTaxAmountSpan;

      if (compInvoiceType === "CBW" || compInvoiceType === "LUT" || compInvoiceType === "SEWOP") {
        $(".grandSgstCgstAmt").html(0);
        $(".convertedGrandSgstCgstAmt").html(0);

        $("#grandTaxAmt").html(0);
        $("#convertedGrandTaxAmount").html(0);

        $("#grandTaxAmtInp").val(0);

        $("#grandTotalAmt").html(grandTotalAmountAfter.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
        $("#grandTotalAmtInp").val(grandTotalAmountAfter.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
        $("#convertedGrandTotalAmt").text(convertedGrandTotalAmountWithoutTax.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
      } else {
        $(".grandSgstCgstAmt").html((totalTaxAmount / 2).toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
        $(".convertedGrandSgstCgstAmt").html((convertedItemTaxAmountSpan / 2).toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));

        $("#grandTaxAmt").html(totalTaxAmount.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
        $("#convertedGrandTaxAmount").html(convertedItemTaxAmountSpan.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));

        $("#grandTaxAmtInp").val(totalTaxAmountOriginal.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));

        $("#grandSubTotalAmt").html(itemBaseAmountSpan.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
        $("#grandSubTotalAmtInp").val(itemBaseAmountInpOriginal.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
        $("#convertedGrandSubTotalAmt").text(convertedItemBaseAmountSpan.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));

        $("#grandTotalDiscount").html(totalDiscountAmount.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
        $("#grandTotalDiscountAmtInp").val(totalDiscountAmountOriginal.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
        $("#convertedGrandTotalDiscountAmount").text(convertedItemDiscountAmountSpan.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));

        $("#grandTotalAmt").html(totalAmount.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
        $("#grandTotalAmtInp").val(totalAmountOriginal.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
        $("#convertedGrandTotalAmt").text(convertedItemTotalPrice.toLocaleString(undefined, {
          minimumFractionDigits: 2
        }));
      }
    }


    // currency conversion
    function currency_conversion() {
      for (elem of $(".convertedItemUnitPriceSpan")) {
        let rowNo = ($(elem).attr("id")).split("_")[1];
        let newVal = $("#curr_rate").val() * $(`#originalChangeItemUnitPriceInp_${rowNo}`).val();
        newVal = newVal > 0 ? newVal : $(elem).val();

        $(elem).text(newVal.toFixed(2).toLocaleString());
        calculateOneItemAmounts(rowNo);
      };

      let currencyIcon = ($('.currencyDropdown').val()).split("_")[2];
      $(".currency-symbol-dynamic").text(currencyIcon);

      calculateGrandTotalAmount();

    }
    // change dynamic currency 
    $(".currencyDropdown").on("change", function() {
      let currencyIcon = ($(this).val()).split("_")[1];
      let currencyName = ($(this).val()).split("_")[2];
      let companyCurrencyName = $('.companyCurrencyName').text();

      if (companyCurrencyName !== currencyName) {
        currency_conversion();
        $.ajax({
          url: `ajaxs/so/ajax-currency.php`,
          type: 'GET',
          data: {
            act: 'currencyPage',
            currency: companyCurrencyName,
            currencyName
          },
          beforeSend: function() {
            $("#curr_rate").val(`Loding...`);
            $("#curr_rate").prop('disabled', true);
          },
          success: function(result) {
            let data = JSON.parse(result);
            let rate = data.data.rate;
            $(".currency-symbol-dynamic").text(currencyName);
            $("#curr_rate").val(rate);
            $("#curr_rate").prop('disabled', false);
            currency_conversion();
          },
        });
        $(`.convertedDiv`).show();
      } else {
        $("#curr_rate").val(1);
        currency_conversion();
        $(`.convertedDiv`).hide();
      }
    });

    $(document).on("keyup keydown", "#curr_rate", function() {
      currency_conversion();
    });

    currency_conversion();

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

    function itemMaxDiscount(rowNo, keyValue = 0) {
      let itemMaxDis = $(`#itemMaxDiscount_${rowNo}`).html();
      console.log('this is max discount', itemMaxDis);
      console.log('this is key value', keyValue);
      if (parseFloat(keyValue) > parseFloat(itemMaxDis)) {
        console.log('max discount is over');
        $(`#itemSpecialDiscount_${rowNo}`).text(`Special Discount`);
        $(`#itemSpecialDiscount_${rowNo}`).show();
        // $(`#specialDiscount`).show();
      } else {
        $(`#itemSpecialDiscount_${rowNo}`).hide();
        // $(`#specialDiscount`).hide();
      }
    }

    $(document).on("keyup blur", ".itemQty", function() {
      console.log('hello, I am qty');
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemVal = parseFloat($(`#itemQty_${rowNo}`).val());
      let checkQty = $(`#checkQty_${rowNo}`).val();
      console.log('checkQty');
      console.log(checkQty);

      if (checkQty) {
        let splitQty = parseFloat(($(`#checkQty_${rowNo}`).val()).split("_")[1]);
        if (itemVal <= splitQty) {
          console.log('ok...');
          // console.log('all',checkQty, itemVal, splitQty);
          $(`#itemSpecialDiscount_${rowNo}`).text(`Special Discount`);
          $(`#qtyMsg_${rowNo}`).hide();
          // document.getElementById("directInvoiceCreationBtn").disabled = false;
        } else {
          console.log('wrong...');
          $(`#itemQty_${rowNo}`).val("");
          $(`#qtyMsg_${rowNo}`).show();
          // document.getElementById("directInvoiceCreationBtn").disabled = true;
          // console.log('all',checkQty, itemVal, splitQty);
        }
      }

      // if (itemVal <= 0) {
      //   // let itemVal = $(`#itemQty_${rowNo}`).val(1);
      //   document.getElementById("directInvoiceCreationBtn").disabled = true;
      // } else {
      //   document.getElementById("directInvoiceCreationBtn").disabled = false;
      // }
      calculateOneItemAmounts(rowNo);
    });

    $(document).on("keyup blur", ".originalChangeItemUnitPriceInp", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      calculateOneItemAmounts(rowNo);
      currency_conversion();
    });

    $("#compInvoiceType").on("change", function() {
      calculateGrandTotalAmount();
    });
    // $("#directInvoiceCreationBtn").on("click", function() {
    //   let flag = 0;
    //   let stockQty = 0;

    //   $(".checkQty").each(function() {
    //     let qty = $(this).val();
    //     stockQty = qty.split("_")[1];
    //     if (stockQty == 0) {
    //       flag++;
    //     }
    //   });

    //   $(".itemQty").each(function() {
    //     let qty = $(this).val();
    //     if (qty <= 0 || qty === "") {
    //       flag++;
    //     }
    //   });

    //   if (flag) {
    //     alert("Please choose valid input.")
    //   } else {
    //     confirm("Are you sure you want to Submit?");
    //   }
    // });



    $("#goodsType").on("change", function() {
      let goodsType = $(this).val();

      if (goodsType === "service") {
        $(".recurringDiv").show();
      } else {
        $(".recurringDiv").hide();
      }

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items-goods-type.php`,
        data: {
          act: "goodsType",
          goodsType
        },
        beforeSend: function() {
          $("#itemsDropDown").html(`Loding...`);
        },
        success: function(response) {
          console.log(response);
          $("#itemsDropDown").html(response);
        }
      });
    });

    // recurring modal 
    $("#makeRecurring").on('click', function() {
      let rec = $(this);
      if (rec.is(':checked')) {
        $("#recurringModal").css('display', 'block');
      } else {
        $("#recurringModal").css('display', 'none');
      }
    });
    $("#neverExpire").on('click', function() {
      let rec = $(this);
      if (rec.is(':checked')) {
        $("#endOn").val("");
        $("#endOn").prop('disabled', true);
      } else {
        $("#endOn").prop('disabled', false);
      }
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
      itemMaxDiscount(rowNo, keyValue);
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
    $(document).on("blur", ".itemTotalDiscount1", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemDiscountAmt = ($(this).val());

      let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
      let originalItemUnitPrice = (parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) > 0) ? parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) : 0;

      let totalAmt = itemQty * originalItemUnitPrice;
      let discountPercentage = itemDiscountAmt * 100 / totalAmt;

      $(`#itemDiscount_${rowNo}`).val(discountPercentage.toFixed(2).toLocaleString());

      // let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;

      console.log('total', itemQty, originalItemUnitPrice, discountPercentage);
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
  $('.currencyDropdown')
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
  $('#servicesDropDown')
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

<script src="<?= BASE_URL; ?>public/validations/soValidation.js"></script>
<script src="<?= BASE_URL; ?>public/validations/goodsInvoiceValidation.js"></script>


<script src="https://code.getmdl.io/1.2.0/material.min.js"></script>
<script>
  // *** multi step form *** //


  //DOM elements
  const DOMstrings = {
    stepsBtnClass: 'multisteps-form__progress-btn',
    stepsBtns: document.querySelectorAll(`.multisteps-form__progress-btn`),
    stepsBar: document.querySelector('.multisteps-form__progress'),
    stepsForm: document.querySelector('.multisteps-form__form'),
    stepsFormTextareas: document.querySelectorAll('.multisteps-form__textarea'),
    stepFormPanelClass: 'multisteps-form__panel',
    stepFormPanels: document.querySelectorAll('.multisteps-form__panel'),
    stepPrevBtnClass: 'js-btn-prev',
    stepNextBtnClass: 'js-btn-next'
  };

  //remove class from a set of items
  const removeClasses = (elemSet, className) => {

    elemSet.forEach(elem => {

      elem.classList.remove(className);

    });

  };

  //return exect parent node of the element
  const findParent = (elem, parentClass) => {

    let currentNode = elem;

    while (!currentNode.classList.contains(parentClass)) {
      currentNode = currentNode.parentNode;
    }

    return currentNode;

  };

  //get active button step number
  const getActiveStep = elem => {
    return Array.from(DOMstrings.stepsBtns).indexOf(elem);
  };

  //set all steps before clicked (and clicked too) to active
  const setActiveStep = activeStepNum => {

    //remove active state from all the state
    removeClasses(DOMstrings.stepsBtns, 'js-active');

    //set picked items to active
    DOMstrings.stepsBtns.forEach((elem, index) => {

      if (index <= activeStepNum) {
        elem.classList.add('js-active');
      }

    });
  };

  //get active panel
  const getActivePanel = () => {

    let activePanel;

    DOMstrings.stepFormPanels.forEach(elem => {

      if (elem.classList.contains('js-active')) {

        activePanel = elem;

      }

    });

    return activePanel;

  };

  //open active panel (and close unactive panels)
  const setActivePanel = activePanelNum => {

    //remove active class from all the panels
    removeClasses(DOMstrings.stepFormPanels, 'js-active');

    //show active panel
    DOMstrings.stepFormPanels.forEach((elem, index) => {
      if (index === activePanelNum) {

        elem.classList.add('js-active');

        setFormHeight(elem);

      }
    });

  };

  //set form height equal to current panel height
  const formHeight = activePanel => {

    const activePanelHeight = activePanel.offsetHeight;

    DOMstrings.stepsForm.style.height = `${activePanelHeight}px`;

  };

  const setFormHeight = () => {
    const activePanel = getActivePanel();

    formHeight(activePanel);
  };

  //STEPS BAR CLICK FUNCTION
  DOMstrings.stepsBar.addEventListener('click', e => {

    //check if click target is a step button
    const eventTarget = e.target;

    if (!eventTarget.classList.contains(`${DOMstrings.stepsBtnClass}`)) {
      return;
    }

    //get active button step number
    const activeStep = getActiveStep(eventTarget);

    //set all steps before clicked (and clicked too) to active
    setActiveStep(activeStep);

    //open active panel
    setActivePanel(activeStep);
  });

  //PREV/NEXT BTNS CLICK
  DOMstrings.stepsForm.addEventListener('click', e => {

    const eventTarget = e.target;

    //check if we clicked on `PREV` or NEXT` buttons
    if (!(eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`) || eventTarget.classList.contains(`${DOMstrings.stepNextBtnClass}`))) {
      return;
    }

    //find active panel
    const activePanel = findParent(eventTarget, `${DOMstrings.stepFormPanelClass}`);

    let activePanelNum = Array.from(DOMstrings.stepFormPanels).indexOf(activePanel);

    //set active step and active panel onclick
    if (eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`)) {
      activePanelNum--;

    } else {

      activePanelNum++;

    }

    setActiveStep(activePanelNum);
    setActivePanel(activePanelNum);

  });

  //SETTING PROPER FORM HEIGHT ONLOAD
  window.addEventListener('load', setFormHeight, false);

  //SETTING PROPER FORM HEIGHT ONRESIZE
  window.addEventListener('resize', setFormHeight, false);

  //changing animation via animation select !!!YOU DON'T NEED THIS CODE (if you want to change animation type, just change form panels data-attr)

  const setAnimationType = newType => {
    DOMstrings.stepFormPanels.forEach(elem => {
      elem.dataset.animation = newType;
    });
  };

  //selector onchange - changing animation
  const animationSelect = document.querySelector('.pick-animation__select');

  animationSelect.addEventListener('change', () => {
    const newAnimationType = animationSelect.value;

    setAnimationType(newAnimationType);
  });
</script>
<script>
  $(document).on("click", ".add_data", function() {
    var data = this.value;
    $("#createdatamultiform").val(data);
    // confirm('Are you sure to Submit?')
    $("#add_frm").submit();
  });
</script>
<script>
  $(function() {
    // Masonry Grid
    $(".grid").isotope({
      filter: "*",
      // itemSelector: '.grid-item',
      masonry: {
        columnWidth: 180,
        fitWidth: true, // When enabled, you can center the container with CSS.
        gutter: 10
      }
      // layoutMode: 'fitRows'
    });

    $(".filter a").click(function() {
      $(".filter .current").removeClass("current");
      $(this).addClass("current");

      var selector = $(this).data("filter");
      $(".grid").isotope({
        filter: selector
      });
      return false;
    });

    // Fancybox
    $(".fancybox").fancybox({
      helpers: {
        overlay: {
          locked: false
        }
      }
    });
  });
</script>
<script src="../../public/assets/isotop.min.js"></script>
<script src="../../public/assets/jquery.fancybox.min.js"></script>