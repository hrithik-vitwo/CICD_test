<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/common/templates/template-quotation.controller.php");

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
$templateQuotationControllerObj = new TemplateQuotationController();

$company = $BranchSoObj->fetchCompanyDetails()['data'];
$currencyIcon = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_icon'];

$branchGstin = $BranchSoObj->fetchBranchDetailsById($branch_id)['data']['branch_gstin'];
$branchGstinCode = substr($branchGstin, 0, 2);

if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
  console($_POST);
  // exit;
  $addGoodsInvoice = $BranchSoObj->insertQuotation($_POST);
  // console($addGoodsInvoice);

  console("listItem]");
  console(count($_POST['listItem']));
  foreach ($_POST['listItem'] as $one) {
    console("********************************** total items count");
    console($one);
    console(count($one));
  }
  if ($addGoodsInvoice['status'] == "success") {
    // swalToast($addBranchSoItems["status"], $addBranchSoItems["message"]);
    swalAlert($addGoodsInvoice["status"], $addGoodsInvoice['quotationNo'], $addGoodsInvoice["message"]);
    // swalAlert('success','Thank You','Inserted Succefull');
  } else {
    swalAlert($addGoodsInvoice["status"], 'Warning', $addGoodsInvoice["message"]);
  }
}


// fetch company details
$companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
$companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
$branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
$companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
$locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];



?>
<style>
  .loading {
    cursor: wait;
  }


  @media print {
    @page {
      size: A4 !important;
    }

  }

  /* .printable-view .h3-title {
    visibility: hidden;
  }

  @media print {

    page {
      height: 50vh;
    }

    body {
      visibility: hidden;
      height: 90vh !important;

    }

    .printable-view {
      visibility: visible !important;
    }

    .printable-view .h3-title {
      visibility: visibility;
    }

    .classic-view-modal .modal-dialog {
      max-width: 100% !important;
    }

    .classic-view-modal .modal-dialog .modal-header {
      height: 0 !important;
    }

    .classic-view-modal .modal-dialog .modal-body {
      height: 100vh !important;
    }

    .classic-view-modal table.classic-view th {
      font-size: 12px !important;
      padding: 5px 10px !important;
    }

    table.classic-view td p {
      font-size: 12px !important;
    }

  } */
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<input type="hidden" value="<?= $branchGstinCode ?>" class="branchGstin">
<div class="content-wrapper is-sales-quotation">

  <?php if (isset($_GET['create'])) { ?>
    <section class="content">
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="manage-quotations.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Quotation List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Quotation</a></li>
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
            <div class="card direct-create-invoice-card so-creation-card">
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
              <div class="card-body others-info vendor-info so-card-body pt-0">
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="row others-info-form-view" style="row-gap: 17px;">
                      <div class="col-lg-6 col-md-6 col-sm-6">
                        <label>Posting Date: <span class="text-danger">*</span></label>
                        <div>
                          <input type="datetime-local" name="postingDate" id="postingDate" class="form-control" required />
                          <span class="input-group-addon"></span>
                        </div>
                      </div>
                      <input type="hidden" value="open" name="approvalStatus" id="approvalStatus">
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
                        <option value="">Goods Type</option>
                        <option value="hello">hello</option>
                        <option value="hello1">hello1</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <!-- <a class="btn btn-primary items-search-btn" data-bs-toggle="modal" data-bs-target="#exampleModal"> <i class="fa fa-search mr-2"></i>Advance Search</a> -->
              <small class="py-2 px-1 rounded alert-dark specialDiscount" id="specialDiscount" style="display: none;">Special Discount</small>
              <table class="table table-sales-order mt-0">
                <thead>
                  <tr>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>HSN Code</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Base Amount</th>
                    <th>Tax</th>
                    <th>Total Tax</th>
                    <th>Total Price</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="itemsTable"></tbody>
                <span id="spanItemsTable"></span>
                <tbody>
                  <tr>
                    <td colspan="8" class="text-right p-2" style="border: none; background: none;"> </td>
                    <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Sub Total-</sup></td>
                    <input type="hidden" name="grandSubTotalAmtInp" id="grandSubTotalAmtInp" value="0">
                    <td class="p-2" style="border: none; background: none;padding: 0px !important;"><span class="rupee-symbol"><?= $currencyIcon ?></span><span id="grandSubTotalAmt">0.00</span></th>
                  </tr>
                  <tr>
                    <td colspan="8" class="text-right p-2" style="border: none; background: none;"> </td>
                    <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Total Discount-</td>
                    <input type="hidden" name="grandTotalDiscountAmtInp" id="grandTotalDiscountAmtInp" value="0">
                    <td class="p-2" style="border: none; background: none;padding: 0px !important;"><span class="rupee-symbol"><?= $currencyIcon ?></span><span id="grandTotalDiscount">0.00</span></td>
                  </tr>

                  <tr class="p-2 igstTr" style="display:none">
                    <td colspan="8" class="text-right p-2" style="border: none; background: none;"> </td>
                    <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">IGST -</td>
                    <input type="hidden" name="grandTaxAmtInp" id="grandTaxAmtInp" value="0">
                    <td class="p-2" style="border: none; background: none;padding: 0px !important;"><span class="rupee-symbol"><?= $currencyIcon ?></span><span id="grandTaxAmt">0.00</span></td>
                  </tr>
                  <tr class="p-2 cgstTr" style="display:none">
                    <td colspan="8" class="text-right p-2" style="border: none; background: none;"> </td>
                    <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">CGST -</td>
                    <!-- <input type="hidden" name="grandSgstCgstAmtInp" id="grandSgstCgstAmtInp" value="0"> -->
                    <td class="p-2" style="border: none; background: none;padding: 0px !important;"><span class="rupee-symbol"><?= $currencyIcon ?></span><span class="grandSgstCgstAmt">0.00</span></td>
                  </tr>
                  <tr class="p-2 sgstTr" style="display:none">
                    <td colspan="8" class="text-right p-2" style="border: none; background: none;"> </td>
                    <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">SGST -</td>
                    <!-- <input type="hidden" name="grandSgstCgstAmtInp" id="grandSgstCgstAmtInp" value="0"> -->
                    <td class="p-2" style="border: none; background: none;padding: 0px !important;"><span class="rupee-symbol"><?= $currencyIcon ?></span><span class="grandSgstCgstAmt">0.00</span></td>
                  </tr>
                  <!-- <tr class="p-2">
                  <td colspan="8" class="text-right p-2" style="border: none; background: none;"> </td>
                  <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Total Tax-</td>
                  <input type="hidden" name="grandTaxAmtInp" id="grandTaxAmtInp" value="0">
                  <td class="p-2" style="border: none; background: none;padding: 0px !important;"><span class="rupee-symbol"><?= $currencyIcon ?></span><span id="grandTaxAmt">0.00</span></td>
                </tr> -->
                  <tr class="p-2">
                    <td colspan="8" class="text-right p-2" style="border: none; background: none;"> </td>
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
            <button type="submit" name="addNewInvoiceFormSubmitBtn" onclick="return confirm('Are you sure to submitted?')" id="directInvoiceCreationBtn" class="btn btn-primary items-search-btn float-right">Submit</button>
          </div>
        </div>
      </form>
    </section>
  <?php } else { ?>
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="p-0 pt-1 my-2">
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 mb-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Quotation List</h3>
                  <a href="direct-create-invoice.php?quotation_createion" class="btn btn-primary"><i class="fa fa-plus"></i> Create</a>
                </li>
              </ul>
            </div>
            <div class="card card-tabs" style="border-radius: 20px;">
              <?php
              $keywd = '';
              if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
                $keywd = $_REQUEST['keyword'];
              } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
                $keywd = $_REQUEST['keyword2'];
              }
              ?>
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-1 col-md-1 col-sm-12">
                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-lg-11 col-md-11 col-sm-12">
                      <div class="row table-header-item">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="filter-search">
                            <div class="section serach-input-section">
                              <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="<?php echo $keywd; ?>">
                              <div class="icons-container">
                                <div class="icon-search">
                                  <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                </div>
                                <div class="icon-close">
                                  <i class="fa fa-search po-list-icon" id="myBtn"></i>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- <div class="col-lg-1 col-md-1 col-sm-1">
                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create-sales-order-delivery" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                        </div> -->
                      </div>

                    </div>

                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Order</h5>

                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php /*if (isset($_REQUEST['keyword2'])) {
                                                                                                                                                      echo $_REQUEST['keyword2'];
                                                                                                                                                    } */ ?>">
                              </div>
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <select name="status_s" id="status_s" class="fld form-control" style="appearance: auto;">
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
                                <input class="fld form-control" type="date" name="to_date_s" id="to_date_s" value="<?php if (isset($_REQUEST['to_date_s'])) {
                                                                                                                      echo $_REQUEST['to_date_s'];
                                                                                                                    } ?>" />
                              </div>
                            </div>

                          </div>
                          <div class="modal-footer">
                            <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync "></i>Reset</a>-->
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                              Search</button>
                          </div>
                        </div>
                      </div>
                    </div>

              </form>
              <script>
                var input = document.getElementById("myInput");
                input.addEventListener("keypress", function(event) {
                  if (event.key === "Enter") {
                    event.preventDefault();
                    document.getElementById("myBtn").click();
                  }
                });
                var form = document.getElementById("search");
                document.getElementById("myBtn").addEventListener("click", function() {
                  form.submit();
                });
              </script>

              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                  <?php
                  $cond = '';

                  $sts = " AND `status` !='deleted'";
                  if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                    $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                  }

                  if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                    $cond .= " AND created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                  }

                  if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                    $cond .= " AND `quotation_no` like '%" . $_REQUEST['keyword2'] . "%'";
                  } else {
                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                      $cond .= " AND `quotation_no` like '%" . $_REQUEST['keyword'] . "%'";
                    }
                  }

                  $sql_list = "SELECT * FROM `" . ERP_BRANCH_QUOTATIONS . "` WHERE 1 AND company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' " . $cond . " " . $sts . " ORDER BY quotation_id DESC limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);

                  $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_QUOTATIONS . "` WHERE 1 " . $cond . " AND company_id='" . $company_id . "' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_QUOTATIONS", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($num_list > 0) {
                  ?>
                    <table class="table defaultDataTable table-hover text-nowrap">
                      <thead>
                        <tr class="alert-light">
                          <th>#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th>Quotation No.</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th>Posting Date</th>
                          <?php  }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th>Customer Name</th>
                          <?php }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th>Quotation Value</th>
                          <?php }
                          if (in_array(5, $settingsCheckbox)) { ?>
                            <th>Status</th>
                          <?php }
                          if (in_array(6, $settingsCheckbox)) { ?>
                            <th>Total Items</th>
                          <?php } ?>

                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        // console($BranchSoObj->fetchBranchSoListing()['data']);
                        // $soList = $BranchSoObj->fetchBranchSoDeliveryListing()['data'];
                        foreach ($qry_list as $oneSoList) {
                          $customerDetails = $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0];
                          //console($customerDetails);
                          $approvalStatus = 0;
                          if ($oneSoList['approvalStatus'] == 14) {
                            $approvalStatus = "<div class='status-warning'>PENDING</div>";
                          } elseif ($oneSoList['approvalStatus'] == 16) {
                            $approvalStatus = "<div class='status'>ACCEPTED</div>";
                          } elseif ($oneSoList['approvalStatus'] == 17) {
                            $approvalStatus = "<div class='status-danger'>REJECTED</div>";
                          } elseif ($oneSoList['approvalStatus'] == 10) {
                            $approvalStatus = "<div class='status-secondary'>CLOSED</div>";
                          }
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['quotation_no'] ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['posting_date'] ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $customerDetails['trade_name'] ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <td class="text-right"><?= $oneSoList['totalAmount'] ?></td>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td class="approvalStatus" id="approvalStatus_<?= $oneSoList['quotation_id'] ?>"><?= $approvalStatus ?></td>
                            <?php }
                            if (in_array(6, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['totalItems'] ?></td>
                            <?php } ?>
                            <td>
                              <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneSoList['quotation_id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                              <!-- right modal start here  -->
                              <div class="modal fade right so-delivery-modal customer-modal classic-view-modal" id="fluidModalRightSuccessDemo_<?= $oneSoList['quotation_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                  <!--Content-->
                                  <div class="modal-content">
                                    <!--Header-->
                                    <div class="modal-header">
                                      <div class="customer-head-info">
                                        <div class="customer-name-code">
                                          <h2 style="font-size: 22px;"><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= number_format($oneSoList['totalAmount'], 2) ?></h2>
                                          <p class="heading lead"><?= $oneSoList['quotation_no'] ?></p>
                                          <!-- <p>Cust PO/REF :&nbsp;<?= $oneSoList['customer_po_no'] ?></p> -->
                                        </div>
                                        <div class="customer-image">
                                          <div class="name-item-count">
                                            <h5 style="font-size: .8rem;"><?= $customerDetails['trade_name'] ?></h5>
                                            <span>
                                              <div class="round-item-count"><?= $oneSoList['totalItems'] ?></div> Items
                                            </span>
                                          </div>
                                          <i class="fa fa-user"></i>
                                        </div>
                                      </div>

                                      <div class="display-flex-space-between mt-4 mb-3">

                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                          <li class="nav-item">
                                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $oneSoList['quotation_id'] ?>" role="tab" aria-controls="home" aria-selected="true">Item Info</a>
                                          </li>
                                          <li class="nav-item">
                                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= $oneSoList['quotation_id'] ?>" role="tab" aria-controls="profile" aria-selected="false">Customer Info</a>
                                          </li>
                                          <li class="nav-item">
                                            <a class="nav-link" id="classic-view-tab" data-toggle="tab" href="#classic-view<?= $oneSoList['quotation_id'] ?>" role="tab" aria-controls="classic-view" aria-selected="false"><ion-icon name="apps-outline" class="mr-2"></ion-icon> Classic View</a>
                                          </li>
                                        </ul>

                                        <!-- action btn  -->
                                        <div class="action-btns display-flex-gap" id="action-navbar">
                                          <!-- <a href="#" class="btn btn-sm" title="Delete SO"><i class="fa fa-trash po-list-icon"></i></a> -->
                                          <!-- action btn  -->
                                          <?php if ($oneSoList['approvalStatus'] == 14 || $oneSoList['approvalStatus'] == 17 || $oneSoList['approvalStatus'] == 10) { ?>
                                            <button type="button" onclick="return alert('You can not create the invoice. As this is not accepted.')" class="btn btn-primary pgi-create-btn border" title="Create Invoice"><i class="fa fa-box mr-2"></i> Create Invoice</button>
                                            <button type="button" onclick="return alert('You can not create sales order. As this is not accepted.')" class="btn btn-primary pgi-create-btn border" title="Create SO"><i class="fa fa-box mr-2"></i> Create SO</button>
                                          <?php } else { ?>
                                            <a href="direct-create-invoice.php?quotation=<?= base64_encode($oneSoList['quotation_id']) ?>" class="btn btn-primary pgi-create-btn border" title="Create Invoice"><i class="fa fa-box mr-2"></i> Create Invoice</a>
                                            <a href="direct-create-invoice.php?quotation_to_so=<?= base64_encode($oneSoList['quotation_id']) ?>" class="btn btn-primary pgi-create-btn border" title="Create SO"><i class="fa fa-box mr-2"></i> Create SO</a>
                                          <?php } ?>
                                          <?php if ($oneSoList['approvalStatus'] != 10) { ?>
                                            <a href="#" class="btn btn-primary pgi-create-btn border closeQuotation" id="closeQuotation_<?= $oneSoList['quotation_id'] ?>_<?= $oneSoList['quotation_no'] ?>" title="Close Quotation"><i class="fa fa-times mr-2"></i> Close Quotation</a>
                                          <?php } ?>
                                        </div>
                                      </div>
                                    </div>
                                    <!--Body-->
                                    <div class="modal-body">
                                      <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="home<?= $oneSoList['quotation_id'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                          <?php
                                          $fetchQuotationItems = $BranchSoObj->getQuotationItems($oneSoList['quotation_id'])['data'];
                                          // console('fetchQuotationItems********');
                                          // console($fetchQuotationItems);

                                          foreach ($fetchQuotationItems as $oneQuotaionItem) {
                                            $serviceUomName = getUomDetail($oneQuotaionItem['uom'])['data']['uomName'];
                                            $subTotalAmt = ($oneQuotaionItem['unitPrice'] * $oneQuotaionItem['qty']) - $oneQuotaionItem['itemTotalDiscount'];
                                          ?>

                                            <div class="card">
                                              <div class="card-body p-2">
                                                <div class="row">
                                                  <div class="col-lg-8 col-md-8 col-sm-8">
                                                    <div class="left-section">
                                                      <div class="icon-img">
                                                        <i class="fa fa-box"></i>
                                                      </div>
                                                      <div class="code-des">
                                                        <h4><?= $oneQuotaionItem['itemCode'] ?></h4>
                                                        <p><?= $oneQuotaionItem['itemName'] ?></p>
                                                      </div>
                                                    </div>
                                                  </div>
                                                  <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <div class="right-section">
                                                      <div class="font-weight-bold">
                                                        <span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span> <?= number_format($subTotalAmt, 2) ?>
                                                      </div>
                                                      <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneQuotaionItem['unitPrice'] ?> * <?= $oneQuotaionItem['qty'] ?> <?= $serviceUomName ?></p>
                                                      <!-- <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneQuotaionItem['unitPrice'] * $oneQuotaionItem['qty'] ?></p> -->
                                                      <div class="discount">
                                                        <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneQuotaionItem['unitPrice'] * $oneQuotaionItem['qty'] ?></p>
                                                        (-<?= $oneQuotaionItem['totalDiscount'] ?>%)
                                                      </div>
                                                      <p style="border-top: 1px solid;">(GST: <?= $oneQuotaionItem['tax'] ?>%)</p>
                                                      <div class="font-weight-bold">
                                                        <span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span> <?= number_format($oneQuotaionItem['totalPrice'], 2) ?>
                                                      </div>
                                                      <!-- <div class="discount">
                                                    <p><?= $oneQuotaionItem['itemTotalDiscount'] ?></p>
                                                    (-<?= $oneQuotaionItem['totalDiscount'] ?>%)
                                                  </div> -->
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          <?php } ?>
                                        </div>
                                        <div class="tab-pane fade" id="profile<?= $oneSoList['quotation_id'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                          <?php if ($customerDetails != "") { ?>
                                            <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                              <div class="accordion-item">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                  <button class="accordion-button btn btn-primary collapsed text-light" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                    Customer Details
                                                  </button>
                                                </h2>
                                                <div id="basicDetails" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                  <div class="accordion-body p-0">
                                                    <div class="card h-100">
                                                      <div class="card-body p-3" style="height: 245px !important;">
                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs text-left">Code :</p>
                                                          <p class="font-bold text-xs text-left"><?= $customerDetails['customer_code'] ?></p>
                                                        </div>
                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs text-left">GST :</p>
                                                          <p class="font-bold text-xs text-left"><?= $customerDetails['customer_gstin'] ?></p>
                                                        </div>
                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs text-left">Pan :</p>
                                                          <p class="font-bold text-xs text-left"> <?= $customerDetails['customer_pan'] ?></p>
                                                        </div>
                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs text-left">Address :</p>
                                                          <p class="font-bold text-xs text-left w-75"><?= $customerAddressDetails['customer_address_building_no'] . ', ' . $customerAddressDetails['customer_address_flat_no'] . ', ' . $customerAddressDetails['customer_address_street_name'] . ', ' . $customerAddressDetails['customer_address_pin_code'] . ', ' . $customerAddressDetails['customer_address_location'] . ', ' . $customerAddressDetails['customer_address_city'] . ', ' . $customerAddressDetails['customer_address_district'] . ', ' . $customerAddressDetails['customer_address_state'] ?></p>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          <?php
                                          } else {
                                            echo "customer not found";
                                          }
                                          ?>
                                        </div>
                                        <!-- <a href="classic-view/invoice-preview-print.php?quotation_id=<?= base64_encode($oneSoList['quotation_id']) ?>&company_id=<?= $company_id ?>&branch_id=<?= $branch_id ?>&location_id=<?= $location_id ?>" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a> -->

                                        <div class="tab-pane fade" id="classic-view<?= $oneSoList['quotation_id'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                          <div class="card classic-view bg-transparent">
                                            <?php $templateQuotationControllerObj->printQuotation($oneSoList['quotation_id'], $company_id, $branch_id, $location_id) ?>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <!--/.Content-->
                                  </div>
                                </div>
                                <!-- right modal end here  -->
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                      <tbody>
                        <tr>
                          <td colspan="<?= $settingsCheckboxCount + 2; ?>">
                            <!-- Start .pagination -->

                            <?php
                            if ($count > 0 && $count > $GLOBALS['show']) {
                            ?>
                              <div class="pagination align-right">
                                <?php pagination($count, "frm_opts"); ?>
                              </div>

                              <!-- End .pagination -->

                            <?php } ?>

                            <!-- End .pagination -->
                          </td>
                        </tr>
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
                      <input type="hidden" name="pageTableName" value="ERP_BRANCH_SALES_ORDER_DELIVERY" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                Quotation No.</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                Posting Date</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                Customer Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                Quotation Value</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                Status</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
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
<?php } ?>

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
        url: `ajaxs/so/ajax-items-goods-type.php`,
        beforeSend: function() {
          $("#itemsDropDown").html(`<option value="">Loding...</option>`);
        },
        data: {
          act: "goodsType",
          goodsType: "material"
        },
        success: function(response) {
          $("#itemsDropDown").html(response);
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
        beforeSend: function() {
          $("#customerDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          $("#customerDropDown").html(response);
        }
      });
    }
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
          url: `ajaxs/so/ajax-items-list-quotation.php`,
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

    // close Quotation
    $(".closeQuotation").on("click", function() {
      let quotationId = ($(this).attr("id")).split("_")[1];
      let quotationNumber = ($(this).attr("id")).split("_")[2];

      if (!confirm(`Are you sure to close quotation #${quotationNumber}?`)) {
        return false;
      }

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-close.php`,
        data: {
          act: "closeQuotation",
          quotationId
        },
        success: function(response) {
          console.log('response => ', response);
          let data = JSON.parse(response);

          // js swal alert
          let timerInterval;
          Swal.fire({
            icon: data.status,
            title: `Quotation #${quotationNumber} closed successfully!`,
            html: "Close in <b></b> seconds.",
            timer: 2000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading();
              const timer = Swal.getPopup().querySelector("b");
              timerInterval = setInterval(() => {
                timer.textContent = `${(Swal.getTimerLeft() / 1000).toFixed(0)}`;
              }, 100);
            },
            willClose: () => {
              clearInterval(timerInterval);
            }
          }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log("I was closed by the timer");
            }
          });
          $(`#closeQuotation_${quotationId}_${quotationNumber}`).hide();
          $(`#approvalStatus_${quotationId}`).html('<div class="status-secondary">CLOSED</div>');
        }
      });
    })

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
      let itemBaseAmountInp = 0;
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
        itemBaseAmountInp += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });
      // console.log("Grand = ", totalAmount, totalTaxAmount, totalDiscountAmount);
      // let grandSubTotalAmt = totalAmount - totalTaxAmount - totalDiscountAmount;
      $("#grandSubTotalAmt").html(itemBaseAmountInp.toFixed(2));
      $("#grandSubTotalAmtInp").val(itemBaseAmountInp.toFixed(2));
      $("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2));
      $("#grandTotalDiscountAmtInp").val(totalDiscountAmount.toFixed(2));
      $("#grandTaxAmt").html(totalTaxAmount.toFixed(2));
      $("#grandTaxAmtInp").val(totalTaxAmount.toFixed(2));
      $(".grandSgstCgstAmt").html(totalTaxAmount.toFixed(2) / 2);
      $("#grandTotalAmt").html(totalAmount.toFixed(2));
      $("#grandTotalAmtInp").val(totalAmount.toFixed(2));
    }

    function calculateoneQuotaionItemAmounts(rowNo) {
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
      $(`#totalItemAmountModal_${rowNo}`).html(totalItemPrice.toFixed(2));
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
      let rowNo = ($(this).attr("id")).split("_")[1];
      let itemVal = parseFloat($(`#itemQty_${rowNo}`).val());
      let checkQty = parseFloat($(`#checkQty_${rowNo}`).val());
      let splitQty = parseFloat(($(`#checkQty_${rowNo}`).val()).split("_")[1]);

      calculateoneQuotaionItemAmounts(rowNo);
    });

    $(document).on("keyup blur", ".itemUnitPrice", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      calculateoneQuotaionItemAmounts(rowNo);
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
      calculateoneQuotaionItemAmounts(rowNo);
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
      let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;

      let totalAmt = itemQty * itemUnitPrice;
      let discountPercentage = itemDiscountAmt * 100 / totalAmt;

      $(`#itemDiscount_${rowNo}`).val(discountPercentage.toFixed(2));

      // let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;

      console.log('total', itemQty, itemUnitPrice, discountPercentage);
      calculateoneQuotaionItemAmounts(rowNo);

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

    // $(function() {
    //   $("#datepicker").datepicker({
    //     autoclose: true,
    //     todayHighlight: true
    //   }).datepicker('update', new Date());
    // });

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

<script src="<?= BASE_URL; ?>public/validations/goodsInvoiceValidation.js"></script>

<script>
  $(document).ready(function() {
    // Add the 'loading' class to the body element when the page starts loading
    $("body").addClass("loading");
  });

  $(window).on("load", function() {
    // Remove the 'loading' class from the body element when the page finishes loading
    $("body").removeClass("loading");
  });
</script>