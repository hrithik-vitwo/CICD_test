<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-customers-controller.php");


// date checker
$check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];
// console($check_var_data);

$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];

$BranchSoObj = new BranchSo();
$CustomersObj = new CustomersController();

$quotationsJson = '';
$itemIdJson = '';
$customerId = 0;
$pgiCode = '';

$extraremarks = '';
$declaration = '';
$CreditPeriod = '';
$SalesPerson = '';
$FunctionalArea = '';
$SelectBank = '';

$serviceList = $BranchSoObj->fetchItemServices()['data'];

$company = $BranchSoObj->fetchCompanyDetails()['data'];
$currencyIcon = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_icon'];
$currencyName = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_name'];

$branchGstin = $BranchSoObj->fetchBranchDetailsById($branch_id)['data']['branch_gstin'];
$branchGstinCode = substr($branchGstin, 0, 2);
if (isset($_POST["createdatamultiform"])) {
  $addNewObj = $BranchSoObj->createDataCustomer($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}


if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {

  $addGoodsInvoice = $BranchSoObj->insertQuotation($_POST, $_FILES);

  if ($addGoodsInvoice['status'] == "success") {
    swalAlert($addGoodsInvoice["status"], $addGoodsInvoice['quotationNo'], $addGoodsInvoice["message"], 'manage-quotations-tax.php');
  } else {
    swalAlert($addGoodsInvoice["status"], 'warning', $addGoodsInvoice["message"]);
  }
}

$invoiceType = '';
?>

<style>
  .direct-create-invoice-card {
    height: auto !important;
    min-height: 100%;
    margin-bottom: 2em;
  }

  .direct-create-invoice-card .card-body {
    min-height: 100%;
    height: 330px !important;
  }

  .card.po-vendor-details-view .card-body {
    height: auto !important;
  }

  .advanced-serach .nav-action {
    flex-direction: row;
    gap: 30px;
    width: 35% !important;
  }

  .advanced-serach .form-inline {
    flex-flow: row;
  }

  div#quick-add-input span.select2.select2-container.select2-container--default {
    width: 120px !important;
  }

  .advanced-serach .form-inline select {
    width: 120px !important;
  }

  .static-currency::before,
  .dynamic-currency::before {
    bottom: 25px !important;
  }

  .so-card-body .static-currency input,
  .so-card-body .dynamic-currency input,
  .dynamic-currency select {
    height: 32px !important;
  }

  .card-body.others-info.vendor-info.so-card-body {
    height: 100% !important;
  }

  .modal.add-customer-modal .modal-dialog {
    max-width: 70%;
  }

  .modal.add-customer-modal .modal-dialog .modal-content .modal-body {
    height: 80vh;
  }

  .text-small {
    font-size: 0.8em;
  }

  .text-large {
    font-size: 1.1em;
  }

  .convertedDiv {
    display: none;
  }

  .itemDropdownDiv {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
  }

  .itemDropdownDiv label {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    margin-bottom: 0;
  }

  select.order-for-select {
    width: auto !important;
  }

  .head-item-table #quick-add-input.show {
    transform: translateX(55%) !important;
  }

  .recurringDiv {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
  }

  .round-off-section {
    flex-direction: column;
  }

  div#round_off_hide {
    flex-direction: column;
  }

  .type-flex {
    display: flex;
    justify-content: flex-start;
    gap: 2px;
  }

  .type-flex .invoice-varient-form {
    flex: 1 1 50%;
  }

  .discount-view {
    display: grid;
    grid-template-columns: 2fr 2fr;
    margin-bottom: 10px;
    gap: 15px;
    height: auto;
    max-height: 500px;
    overflow: auto;
  }

  .discount-view .d-flex {
    background: #e8e8e8;
    padding: 5px;
    border-radius: 7px;
  }

  .delivery-discount {
    margin-top: 20px;
  }

  .delivery-discount .nav-tabs {
    justify-content: flex-end;
    margin-right: 30px;
  }

  .delivery-discount .nav-tabs .nav-link,
  .delivery-discount .nav-tabs .nav-link.active {
    padding: 5px 15px;
    font-size: 0.75rem;
    text-align: center;
    color: #000;
    margin: -1px;
    width: auto;
  }

  .delivery-discount .nav-tabs .nav-link.active {
    color: #003060;
    font-weight: 600;
  }

  .modal.discountViewModal .modal-body {
    background: #ececec;
  }

  .modal.discountViewModal .modal-body .tab-content {
    padding-top: 20px;
    background: #fff;
    padding-left: 15px;
    padding-right: 15px;
    border-radius: 7px;
    height: 100%;
  }

  .discount-card {
    background: #003060;
    border-radius: 7px;
    padding: 11px 15px;
    white-space: normal;
    color: #fff;
    line-height: 1.7;
    position: relative;
    overflow: hidden;
  }

  .discount-card::after {
    content: '';
    display: inline-block;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background-color: #f7f7f71a;
    position: absolute;
    bottom: -45%;
    left: -76px;
  }

  .discount-card::before {
    content: '';
    display: inline-block;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background-color: #f7f7f71a;
    position: absolute;
    top: -56%;
    right: -98px;
  }

  .discount-card .validity-days {
    background: transparent;
    padding: 4px 0;
    margin: 7px 0;
    border-top: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
    border-radius: 0;
  }

  .discount-card label {
    font-size: 0.6rem !important;
    color: #ccc;
  }

  .discount-card p {
    font-size: 0.7rem;
  }

  .invoice-field {
    display: flex;
    align-items: flex-start;
    flex-direction: column;
  }
</style>


<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.3/dist/js/bootstrap.bundle.min.js"></script>

<input type="hidden" value="<?= $branchGstinCode ?>" class="branchGstin" id="branchGstin">

<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

        <li class="breadcrumb-item active"><a href="manage-quotations-tax.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Quotation List</a></li>
        <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Quotation</a></li>
        <li class="back-button">
          <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
            <i class="fa fa-reply po-list-icon"></i>
          </a>
        </li>
      </ol>
    </div>
    <span style="display: none;" class="companyCurrencyName"><?= $currencyName ?></span>
    <form action="" method="POST" id="addNewSOForm" enctype="multipart/form-data">

      <input type="hidden" name="invoice_id" value="<?= $invoiceId ?>">

      <input type="hidden" value="<?= $invoiceType ?>" name="ivType">
      <input type="hidden" value="<?= $currencyName ?>" name="currencyName" class="currencyName">



      <div class="row main-create-template">
        <div class="col-lg-6 col-md-6 col-sm-12">
          <div class="card direct-create-invoice-card so-creation-card">
            <div class="card-header">
              <div class="row customer-info-head">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="head">
                    <i class="fa fa-user"></i>
                    <h4>Customer Info</h4>
                    <input type="hidden" class="customerIdInp" value="0">
                    <input type="hidden" name="shipToLastInsertedId" value="0" id="shipToLastInsertedId">
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body others-info vendor-info so-card-body <?php if ($edit_invoice && $edit_so) {
                                                                          echo "displayOverLay";
                                                                        } ?>">
              <!-- <div class="overlay" style="position: absolute;background: rgba(0,0,0,0.5);width: 100%;height: 100%;"></div> -->
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="row customer-info-form-view">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="input-box customer-select">
                        <span class="text-danger">*</span>
                        <?php if (isset($_GET['quotation']) || isset($_GET['quotation_to_so']) || isset($_GET['so_to_invoice']) || isset($_GET['party_order_to_so']) || isset($_GET['party_order_to_quotation']) || isset($_GET['pgi_to_invoice']) ||  isset($_GET['edit_invoice']) || $_GET['edit_so'] || isset($_GET['proforma_to_so']) || isset($_GET['proforma_to_invoice'])) { ?>
                          <?php
                          $getCustomerObj = $CustomersObj->getDataCustomerDetails($customerId);
                          $customerName = $getCustomerObj['data'][0]['trade_name'];
                          $customer_id = $getCustomerObj['data'][0]['customer_id'];
                          $customer_code = $getCustomerObj['data'][0]['customer_code'];
                          ?>
                          <input type="text" name="customerName" class="form-control" value="<?= $customerName ?>" readonly>
                          <input type="hidden" name="customerId" id="customerDropDown" class="form-control" value="<?= $customer_id ?>" readonly>
                        <?php } else if (isset($_GET['repost_invoice'])) {
                          $getCustomerObj = $CustomersObj->getDataCustomerDetails($customerId);
                          $customerName = $getCustomerObj['data'][0]['trade_name'];
                          $customer_code = $getCustomerObj['data'][0]['customer_code'];
                          $customer_id = $getCustomerObj['data'][0]['customer_id'];
                        ?>
                          <select name="customerId" id="customerDropDown" class="form-control select2" required>
                            <option value="">Select Customer</option>
                            <!-- <option value="<?= $customerName ?>" selected><?= $customer_code ?> | <?= $customerName ?></option> -->
                            <option value="<?= $customer_id ?>" selected><?= $customer_code ?> | <?= $customerName ?></option>
                          </select>
                        <?php } else { ?>
                          <select name="customerId" id="customerDropDown" class="form-control select2" required>
                            <option value="">Select Customer</option>
                          </select>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="customer-info-text" id="customerInfo">
                        <div class="watermark">

                        </div>
                      </div>
                    </div>
                    <div>
                      <?php if ($companyCountry == 103) { ?>
                        <div class="form-input">
                          <label for="">Place of supply <span class="text-danger">*</span></label>
                          <select name="placeOfSupply" class="form-control select2" id="placeOfSupply1" required>
                            <option value="">Place of supply</option>
                            <?php
                            $stateNameList = fetchStateName()['data'];
                            // Custom comparison function for sorting
                            function compareByStateCode($a, $b)
                            {
                              return $a['gstStateCode'] - $b['gstStateCode'];
                            }
                            usort($stateNameList, 'compareByStateCode');
                            foreach ($stateNameList as $one) {
                            ?>
                              <option value="<?= ltrim($one['gstStateCode'], '0') ?>"><?= $one['gstStateCode'] ?> - <?= $one['gstStateName'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
          <div class="card direct-create-invoice-card so-creation-card">
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
            <div class="card-body sales-order-creation-direct others-info vendor-info so-card-body pt-0">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">

                  <input type="hidden" value="14" name="approvalStatus" id="approvalStatus">


                  <div class="row others-info-form-view" style="row-gap: 5px;">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <label>Posting Date: <span class="text-danger">*</span></label>
                      <div>
                        <input type="date" name="postingDate" value="" id="postingDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>" required />
                        <span class="input-group-addon"></span>
                      </div>
                    </div>


                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="form-input">
                        <label for="date">Validity Period: <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" required>
                        <span id="dateError" class="text-danger" style="font-size: 14px;"></span>
                      </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="dynamic-currency-conversion">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="">Currency Rate</label>
                            <input type="text" class="form-control" id="curr_rate" name="curr_rate" value="1">
                          </div>
                          <div class="col-md-6">
                            <?php $curr = queryGet("SELECT * FROM `erp_currency_type` ORDER BY currency_name ASC ", true); ?>
                            <div class="form-input">
                              <label for="">Customer Currency</label>
                              <select id="" name="currency" class="form-control currencyDropdown rupee-symbol">
                                <?php
                                foreach ($curr['data'] as $data) {
                                ?>
                                  <option <?php if ($data['currency_name'] === $currencyName) {
                                            echo "selected";
                                          } ?> value="<?= $data['currency_id'] ?>≊<?= $data['currency_icon'] ?>≊<?= $data['currency_name'] ?>"><?= $data['currency_icon'] ?><?= $data['currency_name'] ?></option>
                                <?php
                                }
                                ?>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- <label for="" class="label-hidden">label</label> -->
                    </div>

                    <div class="col-lg-6 col-mg-6 col-sm-6">

                      <div class="row">
                        <div class="col-md-6">
                          <label for="">Quotation Type</label>
                          <select name="potypes" id="potypes" required class="form-control typesDropdown">
                            <option value="">Select Quotation Type</option>
                            <option id="domestic" value="domestic">Domestic</option>
                            <option id="international" value="international">International
                            </option>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <div class="radio-condition" id="incoTerms">
                            <div class="radio-types-fob-cif" style="display: none;">
                              <label>Inco Terms</label>
                              <div style="display: flex; justify-content: stretch;">
                                <div class="form-input-radio form-input-fob">

                                  <input type="radio" value="FOB" required name="domestic">
                                  <div class="tooltip-label">
                                    <label for="">FOB</label>
                                    <div class="help-tip fob-tooltip">
                                      <p>Free On Board or Freight on Board</p>
                                    </div>
                                  </div>
                                </div>
                                <div class="form-input-radio form-input-cif">
                                  <input type="radio" value="CIF" required name="domestic">
                                  <div class="tooltip-label">
                                    <label for="">CIF</label>
                                    <div class="help-tip cif-tooltip">
                                      <p>Cost, insurance, and freight is an international
                                        shipping agreement</p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="radio-types-ex-for" style="display: none;">

                              <label for="">Inco Terms</label>
                              <div style="display: flex; justify-content: stretch;">
                                <div class="form-input-radio form-input-ex-work">
                                  <input type="radio" value="exwork" required name="domestic">
                                  <div class="tooltip-label">
                                    <label for="">Ex Work</label>
                                    <div class="help-tip ex-work-tooltip">
                                      <p>An domestic trade term that describes when a
                                        seller makes a product available at a designated
                                        location</p>
                                    </div>
                                  </div>
                                </div>

                                <div class="form-input-radio form-input-for">
                                  <input type="radio" value="FOR" required name="domestic">
                                  <div class="tooltip-label">
                                    <label for="">FOR</label>
                                    <div class="help-tip for-tooltip">
                                      <p>F.O.R. stands for “Free on Road” means the goods which is being sent from source station to its destination includes transportation and all other transit expenses. All applicable taxes and duties on goods remains extra.</p>
                                    </div>
                                  </div>
                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                    <?php if ($companyCountry == 103) { ?>
                      <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="form-input">
                          <label for="">Compliance Invoice Type <span class="text-danger">*</span></label>
                          <select name="compInvoiceType" class="form-control" id="compInvoiceType" required>
                            <?php
                            if ($branchGstin == "") {
                              foreach (fetchInvoiceTypeWithoutgst()['data'] as $one) { ?>
                                <option <?php echo (is_null($branchGstin) && $one['code'] == "R") || (!is_null($branchGstin) && $one['code'] == "E") ? "selected" : ""; ?> value="<?= $one['code'] ?>">
                                  <?= $one['title'] ?>
                                </option>
                              <?php
                              }
                            } else {
                              foreach (fetchInvoiceTypeWithgst()['data'] as $one) { ?>
                                <option <?php echo (is_null($branchGstin) && $one['code'] == "R") || (!is_null($branchGstin) && $one['code'] == "E") ? "selected" : ""; ?> value="<?= $one['code'] ?>">
                                  <?= $one['title'] ?>
                                </option>
                            <?php
                              }
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                    <?php } ?>



                    <!-- quotaion credit period div -->
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-input">
                            <label for="leadtime">Lead Time <span class="text-danger">*</span></label>
                            <input
                              type="number"
                              name="leadtime"
                              class="form-control"
                              id="leadtime"
                              placeholder="Lead Time"
                              min="1"
                              required
                              oninput="this.value = Math.max(0, this.value)" />


                          </div>

                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label for="">Credit Period (Days)<span class="text-danger">*</span></label>
                            <input type="text" name="creditPeriod" class="form-control" id="inputCreditPeriod" placeholder="Credit Period " value="<?= $CreditPeriod; ?>" required oninput="this.value = this.value === '' || isNaN(this.value) ? '0' : Math.max(0, parseInt(this.value) || 0)" />

                          </div>
                        </div>
                      </div>

                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="form-input">
                        <label for="">Attach Reference</label>
                        <input type="file" name="attachment" class="form-control" id="attachment">
                      </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <?php
                      $declarationText = '';

                      $declarationText = $company['sales_invoice_declaration'];

                      ?>
                      <label for="" class="">Remark </label>
                      <textarea name="extra_remark" id="extra_remark" placeholder="Remarks" class="form-control" rows="2"><?php echo $extraremarks;  ?></textarea>

                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">

                      <label for="" class="">Declaration </label>
                      <textarea name="declaration_note" id="declaration_note" placeholder="Declaration" class="form-control" rows="2"><?= $declarationText ?></textarea>
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
          <div class="card items-select-table mt-4">
            <div class="head-item-table">
              <div class="advanced-serach">

                <div class="hamburger quickadd-hamburger">
                  <div class="wrapper-action">
                    <i class="fa fa-plus"></i>
                  </div>
                </div>
                <div class="nav-action quick-add-input d-flex" id="quick-add-input">
                  <div class="d-flex align-items-center gap-2">
                    <label for="" class="text-nowrap" style="width: 100%;">Order For <span class="text-danger">*</span></label>
                    <select name="goodsType" class="form-control order-for-select" id="goodsType" required>
                      <option value="">Select One</option>
                      <option value="material">Goods</option>
                      <option value="service">Services</option>
                      <option value="project">Projects</option>
                    </select>
                  </div>

                  <div class="itemDropdownDiv orderFor" style="display: none;">
                    <input name="orderFor" checked class="orderForRadio" type="radio" value="service" id="orderForService" />
                    <label for="orderForService" style="width: 100%; cursor:pointer;user-select: none;">Order for service<span class="text-danger">*</span></label>
                  </div>
                  <div class="itemDropdownDiv orderFor" style="display: none;">
                    <input name="orderFor" class="orderForRadio" type="radio" value="project" id="orderForProject" />
                    <label for="orderForProject" style="width: 100%; cursor:pointer;user-select: none;">Order for project<span class="text-danger">*</span></label>
                  </div>
                  <div class="itemDropdownDiv gap-2 quickAdd">
                    <label for="">Quick Add <span class="text-danger">*</span></label>
                    <select id="itemsDropDown" class="form-control select2">
                      <option value="">Select One</option>
                    </select>
                  </div>


                </div>
              </div>
            </div>


            <div class="card">
              <div class="card-body" style="overflow: auto;">
                <table class="table table-sales-order mt-0">
                  <thead>
                    <tr>
                      <th>Item Code</th>
                      <th>Item Name</th>
                      <th>HSN Code</th>
                      <th>MOQ</th>
                      <th>Qty</th>
                      <th>MRP</th>
                      <th>Rate</th>
                      <th>Trade Discount</th>
                      <th>Gross Amt.</th>
                      <th>Cash Discount</th>
                      <th>Taxable Amount</th>
                      <th>GST (%)</th>
                      <th class="text-right">GST Amount (<?= $currencyName ?>)</th>
                      <th class="text-right">Total Price</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="itemsTable"></tbody>
                  <span id="spanItemsTable"></span>
                  <tbody>
                    <tr>

                      <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>

                      <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Sub Total</sup></td>
                      <input type="hidden" name="gstdetails">
                      <input type="hidden" name="grandSubTotalAmtInp" id="grandSubTotalAmtInp" value="0">
                      <td class="text-right pr-2" style="border: none; background: none;">
                        <small class="text-large font-weight-bold text-success">
                          <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandSubTotalAmt">0.00</span>
                        </small>
                        <small class="text-small font-weight-bold text-primary convertedDiv">
                          (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandSubTotalAmt">0.00</span>)
                        </small>
                      </td>
                    </tr>
                    <!-- trade discount -->
                    <tr>

                      <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>

                      <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Trade Discount</td>
                      <input type="hidden" name="grandTotalDiscountAmtInp" id="grandTotalDiscountAmtInp" value="0">
                      <td class="text-right pr-2" style="border: none; background: none;">
                        <small class="text-large font-weight-bold text-success">
                          <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTotalDiscount">0.00</span>
                        </small>
                        <small class="text-small font-weight-bold text-primary convertedDiv">
                          (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandTotalDiscountAmount">0.00</span>)
                        </small>
                      </td>
                    </tr>

                    <!-- cash discount -->
                    <tr>

                      <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>

                      <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Cash Discount</td>
                      <input type="hidden" name="grandTotalCashDiscountAmtInp" id="grandTotalCashDiscountAmtInp" value="0">
                      <td class="text-right pr-2" style="border: none; background: none;">
                        <small class="text-large font-weight-bold text-success">
                          <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTotalCashDiscount">0.00</span>
                        </small>
                        <small class="text-small font-weight-bold text-primary convertedDiv">
                          (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandTotalCashDiscountAmount">0.00</span>)
                        </small>
                      </td>
                    </tr>


                    <tr>

                    </tr>
                    <tr class="p-2">

                      <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>

                      <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border-top: 3px double !important; background: none;padding: 0px !important;">Total Amount</td>
                      <input type="hidden" name="grandTaxAmtInp" id="grandTaxAmtInp">
                      <input type="hidden" name="grandTotalAmtInp" id="grandTotalAmtInp" class="grandTotalAmounttInp" value="0">
                      <td class="font-weight-bold text-right pr-2" style="border-top: 3px double !important; background: none;">
                        <small class="text-large font-weight-bold text-success">
                          <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTotalAmt" class="grandTotalAmount">0.00</span>
                        </small>
                        <small class="text-small font-weight-bold text-primary convertedDiv">
                          (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandTotalAmt">0.00</span>)
                        </small>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>


          <!-- <div class="row fob-section" style="margin: 0px 0px 20px 0px;padding: 10px 0px;border-radius: 10px;box-shadow: 0 0 15px #d8d8d8b3;border: 1px solid #d0d0d0;">
            <div class="d-flex">
              <label for="fob" style="display: flex; align-items: center;" class="mb-0">
                <p class="pr-2"> If this is the FOB/FOR, Please Check </p>
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
             
                  <select name="otherCostDetails[12345][services]" class="selct-vendor-dropdown" id="servicesDropDown">
                    <option value="">Select One</option>
                    <?php foreach ($serviceList as $service) { ?>
                      <option value="<?= $service["itemId"] ?>_<?= $service["itemCode"] ?>_<?= $service["itemName"] ?>_<?= $service["service_unit"] ?>"><?= $service['itemName'] ?><small>(<?= $service['itemCode'] ?>)[<?= $service['goodsType'] ?>]</small></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-5 col-md-12 col-sm-12">
                <div class="form-input">
                  <label for="">Qty</label>
                  <input step="any" type="number" class="form-control" placeholder="Qty" name="otherCostDetails[12345][qty]">
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
          </div> -->
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
          <button type="submit" name="addNewInvoiceFormSubmitBtn" onclick="return confirm('Are you sure to submitted?')" id="directInvoiceCreationBtn" class="btn btn-primary items-search-btn float-right">Submit</button>
        </div>
      </div>
    </form>
  </section>
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
                            <label>GSTIN/ABN</label>
                            <input type="text" class="form-control" name="customer_gstin" id="customer_gstin" value="">
                            <div class="float-left" id="gstinStsChck" style="font-size: 10px;"><small></small></div>
                          </div>

                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Pan/TFN *</label>
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
                          <div class="form-input selDiv">
                            <label>State</label>

                            <select id="state" name="state" class="form-control stateDropDown">
                              <?php
                              $stateNameList = fetchStateName()['data'];

                              usort($stateNameList, 'compareByStateCode');
                              foreach ($stateNameList as $one) {
                              ?>
                                <option value="<?= ($one['gstStateName']) ?>"><?= $one['gstStateCode'] ?> - <?= $one['gstStateName'] ?></option>
                              <?php } ?>
                            </select>
                            <!-- <input type="text" class="form-control" name="state" id="state" value=""> -->
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
                            <select id="company_currency" name="company_currency" class="form-control mt-0 form-control-border borderColor">
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

<script>
  var potypes = jQuery('#potypes');

  potypes.on('change', function() {
    if ($(this).val() === 'international') {
      $('.radio-types-ex-for').hide();
      $('.radio-types-fob-cif').show();
    } else if ($(this).val() === 'domestic') {
      $('.radio-types-fob-cif').hide();
      $('.radio-types-ex-for').show();
    } else {
      $('.radio-types-fob-cif').hide();
      $('.radio-types-ex-for').hide();

    }
  });
</script>


<?php require_once("../common/footer.php"); ?>
<?php
$quotation = true;
require_once("../../api/v2/proccess/validation/sales_order.js.php");
?>