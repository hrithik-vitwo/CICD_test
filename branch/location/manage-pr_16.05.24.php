<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-branch-pr-controller.php");
require_once("../../app/v1/functions/branch/func-items-controller.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");


$ItemsObj = new ItemsController();

$BranchPoObj = new BranchPo();


// $variant = $_SESSION['visitBranchAdminInfo']['flAdminVariant'];
$check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];
// console($_SESSION);
// // console($check_var_sql);
// console($check_var_sql);
$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];
// console($_SESSION); 
$today = date("Y-m-d");
if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"],);
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


if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchPrObj = new BranchPr();

if (isset($_POST['addNewPRFormSubmitBtn'])) {

  // console($_POST);
  // exit();

  $addBranchPr = $BranchPrObj->addBranchPr($_POST);

  if ($addBranchPr["status"] == "success") {
    swalAlert($addBranchPr["status"], ucfirst($addBranchPr["status"]), $addBranchPr["message"], BASE_URL . "branch/location/manage-pr.php");
  } else {
    swalToast($addBranchPr["status"], $addBranchPr["message"]);
  }
}

if (isset($_POST["editNewPRFormSubmitBtn"])) {
  // console($_POST);
  // exit();
  $editBranchPr = $BranchPrObj->updatePR($_POST);
  // $branchId = base64_encode($addNewObj['branchId']);
  // redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
  swalAlert($editBranchPr["status"], ucfirst($editBranchPr["status"]), $editBranchPr["message"], BASE_URL . "branch/location/manage-pr.php");
}



if (isset($_POST['addNewRFQFormSubmitBtn'])) {

  // console($_POST);
  // exit();

  $addBranchRfq = $BranchPrObj->addBranchRFQ($_POST);

  swalToast($addBranchRfq["status"], $addBranchRfq["message"]);
}



if (isset($_GET["close-pr"])) {
  $pr_id = $_GET['close-pr'];
  $update = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_REQUEST . "` SET `pr_status`=10 WHERE `purchaseRequestId`=$pr_id");
}
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
  .matrix-card .row:nth-child(1):hover {

    pointer-events: none;

  }

  .matrix-card .row:hover {

    border-radius: 0 0 10px 10px;

  }

  .matrix-card .row:nth-child(1) {

    background: #fff;

  }

  .matrix-card .row .col {

    display: flex;

    align-items: center;

  }

  .matrix-accordion button {

    color: #fff;

    border-radius: 15px !important;

    margin: 20px 0;

  }

  .accordion-button:not(.collapsed) {

    color: #fff;

  }

  .accordion-button::after {

    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

  }

  .accordion-button:not(.collapsed)::after {

    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

  }

  .accordion-item {

    border-radius: 15px !important;

    margin-bottom: 2em;

  }

  .info-h4 {

    font-size: 20px;

    font-weight: 600;

    color: #003060;

    padding: 0px 10px;

  }

  .tab-content li a span,
  .tab-content li a i {

    font-weight: 600;

  }


  .float-add-btn {

    display: flex !important;

  }

  .pr-modal .modal-header.pt-3 {

    height: 315px;

  }

  .tab-content>.tab-pane li {
    margin-left: 0;
  }


  .is-pr .pr-modal .accordion-item button.accordion-button {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    white-space: normal;
    gap: 8px;
  }


  @media (max-width: 575px) {

    .rfq-modal .modal-body {

      padding: 20px !important;

    }

  }

  @media(max-width: 390px) {

    .display-flex-space-between .matrix-btn {

      position: relative;

      top: 10px;

    }

  }


  .printable-view .h3-title {
    visibility: hidden;
  }


  /*********move to listing.css************/

  .is-pr-creation .card.pr-creation-card {
    height: auto;
    max-height: 200px;
  }

  .is-pr-creation .card.pr-creation-card .card-body {
    padding: 0 20px;
  }

  /*********move to listing.css************/


  @media print {
    body {
      visibility: hidden;
    }


    .printable-view {
      visibility: visible !important;
    }

    .printable-view .h3-title {
      visibility: visible;
    }

    .classic-view-modal .modal-dialog {
      max-width: 100% !important;
    }

    .classic-view-modal .modal-dialog .modal-header {
      height: 0 !important;
    }

    .classic-view-modal table.classic-view th {
      font-size: 12px !important;
      padding: 5px 10px !important;
    }

    table.classic-view td p {
      font-size: 12px !important;
    }

  }
</style>


<?php
if (isset($_GET['pr-creation'])) { ?>
  <div class="content-wrapper is-po is-pr-creation">
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

    <section class="content">
      <div class="container-fluid">


        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Purchase Request List</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Purchase Request</a></li>

          <li class="back-button">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
              <i class="fa fa-reply po-list-icon"></i>
            </a>
          </li>
        </ol>

        <form action="" method="POST" id="addNewSOForm">


          <div class="row">

            <div class="col-lg-6 col-md-6 col-sm-12">
              <div class="card pr-creation-card so-creation-card">
                <div class="card-header">
                  <div class="row others-info-head">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="head">
                        <i class="fa fa-info"></i>
                        <h4>PR Types</h4>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body others-info">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="row others-info-form-view">

                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="form-input">
                            <label for="date">PR Types</label>
                            <select name="pr_type" id="usetypesDropdown" class="form-control ">
                              <option value="">Select</option>
                              <option value="material">Material</option>
                              <option value="servicep">Service Purchase</option>
                              <option value="asset">Asset</option>
                            </select>
                          </div>
                        </div>


                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="card pr-creation-card so-creation-card">
                <div class="card-header">
                  <div class="row others-info-head">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="head">
                        <i class="fa fa-pen"></i>
                        <h4>Info</h4>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body others-info">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="row others-info-form-view">


                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="form-input">

                            <label for="date">Required Date<span class="text-danger">*</span></label>
                            <input type="date" name="expDate" class="form-control" id="expDate" value="<?= $today ?>" />
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="form-input">

                            <label for="date">PR Date<span class="text-danger">*</span></label>
                            <input type="date" id="prDate" name="prDate" class="form-control" max="<?= $max ?>" min="<?= $min ?>" />
                            <p id="prdatelabel"></p>
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="form-input">
                            <label for="date">Reference Number</label>
                            <input id="refNo" type="text" name="refNo" class="form-control" />

                          </div>
                        </div>




                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="form-input">
                            <label for="date">Validity Period</label>
                            <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" required>
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
              <div class="card item-select-table" style="overflow-x: auto;">
                <div class="head-item-table">
                  <div class="advanced-serach">
                    <form action="" method="POST">
                      <div class="hamburger quickadd-hamburger">
                        <div class="wrapper-action">
                          <i class="fa fa-plus"></i>
                        </div>
                      </div>
                      <div class="nav-action quick-add-input" id="quick-add-input">
                        <div class="form-inline">
                          <label for=""><span class="text-danger">*</span>Quick Add </label>
                          <select id="itemsDropDown" class="form-control">
                            <option value="">Items</option>
                          </select>
                        </div>
                      </div>


                  </div>
                </div>

                <!-- <label for="">Quick Add</label>
                  <select id="itemsDropDown" class="form-control">
                    <option value="">Goods Type</option>
                    <option value="hello">hello</option>
                    <option value="hello1">hello1</option>
                  </select> -->
                <!-- <a class="btn btn-primary items-search-btn" data-bs-toggle="modal" data-bs-target="#exampleModal"> <i class="fa fa-search mr-2"></i>Advance Search</a> -->
                <table class="table table-sales-order">
                  <thead>
                    <tr>
                      <th>Item Code</th>
                      <th>Item Name</th>
                      <th>Qty</th>
                      <th>UOM</th>
                      <th>Note</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="itemsTable">

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

                        <div class="card filter-add-item-card">
                          <div class="card-header">
                            <button class="btn btn-primary"><i class="fa fa-plus"></i> Add</button>
                          </div>
                          <div class="card-body">
                            <table class="filter-add-item">
                              <thead>
                                <tr>
                                  <th><input type="checkbox"></th>
                                  <th>Item Code</th>
                                  <th>Item Code</th>
                                  <th>Item Code</th>
                                  <th>Item Code</th>
                                  <th>Item Code</th>
                                  <th>Item Code</th>
                                  <th>Item Code</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td><input type="checkbox"></td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                </tr>
                                <tr>
                                  <td><input type="checkbox"></td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                </tr>
                                <tr>
                                  <td><input type="checkbox"></td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                </tr>
                                <tr>
                                  <td><input type="checkbox"></td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                </tr>
                                <tr>
                                  <td><input type="checkbox"></td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                <tr>
                                  <td><input type="checkbox"></td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                <tr>
                                  <td><input type="checkbox"></td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                <tr>
                                  <td><input type="checkbox"></td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                <tr>
                                  <td><input type="checkbox"></td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                  <td>12</td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <button type="submit" name="addNewPRFormSubmitBtn" id="prbtn" class="btn btn-xs btn-primary items-search-btn float-right">Submit</button>


      </div>

      <!-- <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <ul class="list-group">
                    <li class="list-group-item text-center h4 p-0 m-0" style="background:#f4f4f4">Customer Info</li>
                  </ul>
                </div>
                <div class="col-md-12">
                  <span class="has-float-label">
                    <select id="customerDropDown" name="customerId" class="form-control">
                      <option value="">Select Customers</option>
                    </select>
                  </span>
                </div>
                <div class="col-md-12">
                  <div class="row" id="customerInfo">

                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <ul class="list-group">
                    <li class="list-group-item text-center h4 p-0 m-0" style="background:#f4f4f4">Others Info</li>
                  </ul>
                </div>
                <div class="col-md-6">
                  <span class="has-float-label">
                    <input type="date" name="deliveryDate" class="form-control" />
                    <label for="">Delivery Date</label>
                  </span>
                </div>
                <div class="col-md-6">
                  <span class="has-float-label">
                    <select name="profitCenter" class="form-control">
                      <option value="">Profit Center</option>
                      <option value="hello">hello</option>
                      <option value="hello1">hello1</option>
                    </select>
                  </span>
                </div>
                <div class="col-md-12">
                  <span class="has-float-label">
                    <input type="text" name="customerPO" placeholder="customer po number" class="form-control">
                    <label>Customer PO Number</label>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <ul class="list-group">
                <li class="list-group-item text-center h4 p-0 m-0" style="background:#f4f4f4">Others Info</li>
              </ul>
            </div>
            <div class="col-md-6 mb-3">
              <span class="has-float-label">
                <select id="itemsDropDown" class="form-control form-control-border borderColor">
                  <option value="">Goods Type</option>
                  <option value="hello">hello</option>
                  <option value="hello1">hello1</option>
                </select>
              </span>
            </div>
          </div>
          <table class="table-sales-order">
            <thead>
              <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Discount</th>
                <th>Total Price</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="itemsTable"></tbody>
            <tbody>
              <tr>
                <td colspan="4" class="text-right" style="border: none;"> </td>
                <td colspan="0" class="text-right">Total Amount</td>
                <input type="hidden" name="totalAmt" value="20000">
                <td colspan="2">20,000</th>
              </tr>
              <tr>
                <td colspan="4" class="text-right" style="border: none;"> </td>
                <td colspan="0" class="text-right">Total Discount</td>
                <input type="hidden" name="totalDiscount" value="1866">
                <td colspan="2">1866</td>
              </tr>
              <tr>
                <td colspan="4" class="text-right" style="border: none;"> </td>
                <td colspan="0" class="text-right">Sub Total</td>
                <input type="hidden" name="subTotal" value="18,000">
                <td colspan="2">18,000</td>
              </tr>
            </tbody>
            <tfoot>
              <th colspan="4" class="text-right" style="border: none;"> </th>
              <th colspan="0" class="text-right" style="border: none;"></th>
              <td colspan="2" style="border: none;">
                <button type="submit" name="addNewSOFormSubmitBtn" class="btn btn-primary float-right">Final Submit</button>
              </td>
            </tfoot>
          </table>

        </div>
      </div> -->
      </form>
      <!-- modal -->
      <div class="modal" id="addNewItemsFormModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header py-1" style="background-color: #003060; color:white;">
              <h4 class="modal-title">Add New Items</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <!-- <form action="" method="post" id="addNewItemsForm"> -->
              <div class="col-md-12 mb-3">
                <div class="input-group">
                  <input type="text" name="itemName" class="m-input" required>
                  <label>Item Name</label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="input-group">
                  <input type="text" name="itemDesc" class="m-input" required>
                  <label>Item Description</label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="input-group btn-col">
                  <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                </div>
              </div>
              <!-- </form> -->
            </div>
          </div>
        </div>
      </div>
      <!-- modal end -->
  </div>
  </section>
  </div>
<?php
} else if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $pr_sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE `purchaseRequestId`=$id";
  $resultObj = queryGet($pr_sql);
  $row = $resultObj["data"];
  $pr_ite_sql = "SELECT *  FROM `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` WHERE `prId`='" . $id . "' ";
  $pr = queryGet($pr_ite_sql, true);
  $pr_data = $pr['data'];
  // console($row);
  //console($pr_data);
?>

  <div class="content-wrapper is-pr">
    <section class="content">
      <div class="container-fluid">


        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Purchase Request List</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Purchase Request</a></li>
          <li class="back-button">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
              <i class="fa fa-reply po-list-icon"></i>
            </a>
          </li>
        </ol>

        <form action="" method="POST" id="addNewSOForm">
          <input type="hidden" value="<?= $id ?>" name="prId">
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
              <div class="card pr-creation-card so-creation-card">
                <div class="card-header">
                  <div class="row others-info-head">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="head">
                        <i class="fa fa-info"></i>
                        <h4>Info</h4>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-body others-info">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="row others-info-form-view">
                        <div class="col-lg-3 col-md-3 col-sm-12">
                          <div class="form-input">

                            <label for="date">Required Date<span class="text-danger">*</span></label>
                            <input type="date" name="expDate" class="form-control" id="expDate" value="<?= $row['expectedDate'] ?>" />
                          </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-12">
                          <div class="form-input">
                            <label for="date">PR Date<span class="text-danger">*</span></label>
                            <input type="date" id="prDate" name="prDate" class="form-control" id="prDate" max="<?= $max ?>" min="<?= $min ?>" value="<?= $row['pr_date'] ?>" />
                            <p id="prdatelabel"></p>
                          </div>
                        </div>



                        <div class="col-lg-3 col-md-4 col-sm-12">
                          <div class="form-input">
                            <label for="date">Validity Period</label>
                            <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" value="<?= $row['validityperiod'] ?>" required>
                          </div>
                        </div>


                        <div class="col-lg-3 col-md-3 col-sm-12">
                          <div class="form-input">
                            <label for="date">Reference Number</label>
                            <input id="refNo" type="text" name="refNo" class="form-control" value="<?= $row['refNo'] ?>" />
                          </div>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-12">
                          <div class="form-input">
                            <label for="date">PR Types</label>
                            <select name="pr_type" id="usetypesDropdown" class="form-control" disabled>
                              <option value="">Select</option>
                              <option value="material" <?php if ($row['pr_type'] == 'material') {
                                                          echo 'selected';
                                                        } ?>>Material</option>
                              <option value="servicep" <?php if ($row['pr_type'] == 'servicep') {
                                                          echo 'selected';
                                                        } ?>>Service Purchase</option>
                              <option value="asset" <?php if ($row['pr_type'] == 'asset') {
                                                      echo 'selected';
                                                    } ?>>Asset</option>
                            </select>
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
          <div class="card item-select-table" style="overflow-x: auto;">
            <div class="head-item-table">
              <div class="advanced-serach">

                <!-- <div class="hamburger quickadd-hamburger">
                        <div class="wrapper-action">
                          <i class="fa fa-plus"></i>
                        </div>
                      </div>
                      <div class="nav-action quick-add-input" id="quick-add-input">
                        <div class="form-inline">
                          <label for=""><span class="text-danger">*</span>Quick Add </label>
                          <select id="itemsDropDown" class="form-control">
                            <option value="">Goods Type</option>
                          </select>
                        </div>
                      </div> -->


              </div>
            </div>

            <!-- <label for="">Quick Add</label>
                  <select id="itemsDropDown" class="form-control">
                    <option value="">Goods Type</option>
                    <option value="hello">hello</option>
                    <option value="hello1">hello1</option>
                  </select> -->
            <!-- <a class="btn btn-primary items-search-btn" data-bs-toggle="modal" data-bs-target="#exampleModal"> <i class="fa fa-search mr-2"></i>Advance Search</a> -->
            <table class="table table-sales-order">
              <thead>
                <tr>
                  <th>Item Code</th>
                  <th>Item Name</th>
                  <th>Qty</th>
                  <th>Note</th>
                  <!-- <th>Action</th> -->
                </tr>
              </thead>
              <tbody id="itemsTable">


                <?php

                foreach ($pr_data as $data) {
                  //  console($data);
                  $qty = $data['itemQuantity'];
                  $itemId = $data['itemId'];
                  $prItemId = $data['prItemId'];
                  $getItemObj = $ItemsObj->getItemById($itemId);
                  // console($getItemObj);
                  $itemCode = $getItemObj['data']['itemCode'];
                  $itemID = $getItemObj['data']['itemId'];



                  $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

                ?>




                  <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">

                    <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
                    <input class="form-control full-width" type="hidden" value="<?= $prItemId ?>" name="listItem[<?= $randCode ?>][pritemId]">
                    <td>
                      <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
                      <?= $getItemObj['data']['itemCode'] ?>
                    </td>
                    <td>
                      <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
                      <?= $getItemObj['data']['itemName'] ?>
                    </td>
                    <td class="flex-display">
                      <input step="any" type="number" name="listItem[<?= $randCode ?>][qty]" value="<?= $qty ?>" class="form-control full-width itemQty" id="itemQty_<?= $randCode ?>">
                      <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                      <input type="hidden" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomId'] ?>" name="listItem[<?= $randCode ?>][uom]">
                    </td>
                    <td>
                      <input type="text" name="listItem[<?= $randCode ?>][note]" value="<?= $data['itemNote'] ?>" class="form-control full-width note" id="note_<?= $randCode ?>">

                    </td>

                  </tr>
                <?php
                } ?>


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

                    </div>

                    <div class="card filter-add-item-card">
                      <div class="card-header">
                        <button class="btn btn-primary"><i class="fa fa-plus"></i> Add</button>
                      </div>
                      <div class="card-body">

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <button type="submit" name="editNewPRFormSubmitBtn" class="btn btn-xs btn-primary items-search-btn float-right">Update</button>


  </div>

  <!-- <div class="card"> 
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <ul class="list-group">
                    <li class="list-group-item text-center h4 p-0 m-0" style="background:#f4f4f4">Customer Info</li>
                  </ul>
                </div>
                <div class="col-md-12">
                  <span class="has-float-label">
                    <select id="customerDropDown" name="customerId" class="form-control">
                      <option value="">Select Customers</option>
                    </select>
                  </span>
                </div>
                <div class="col-md-12">
                  <div class="row" id="customerInfo">

                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <ul class="list-group">
                    <li class="list-group-item text-center h4 p-0 m-0" style="background:#f4f4f4">Others Info</li>
                  </ul>
                </div>
                <div class="col-md-6">
                  <span class="has-float-label">
                    <input type="date" name="deliveryDate" class="form-control" />
                    <label for="">Delivery Date</label>
                  </span>
                </div>
                <div class="col-md-6">
                  <span class="has-float-label">
                    <select name="profitCenter" class="form-control">
                      <option value="">Profit Center</option>
                      <option value="hello">hello</option>
                      <option value="hello1">hello1</option>
                    </select>
                  </span>
                </div>
                <div class="col-md-12">
                  <span class="has-float-label">
                    <input type="text" name="customerPO" placeholder="customer po number" class="form-control">
                    <label>Customer PO Number</label>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <ul class="list-group">
                <li class="list-group-item text-center h4 p-0 m-0" style="background:#f4f4f4">Others Info</li>
              </ul>
            </div>
            <div class="col-md-6 mb-3">
              <span class="has-float-label">
                <select id="itemsDropDown" class="form-control form-control-border borderColor">
                  <option value="">Goods Type</option>
                  <option value="hello">hello</option>
                  <option value="hello1">hello1</option>
                </select>
              </span>
            </div>
          </div>
          <table class="table-sales-order">
            <thead>
              <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Discount</th>
                <th>Total Price</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="itemsTable"></tbody>
            <tbody>
              <tr>
                <td colspan="4" class="text-right" style="border: none;"> </td>
                <td colspan="0" class="text-right">Total Amount</td>
                <input type="hidden" name="totalAmt" value="20000">
                <td colspan="2">20,000</th>
              </tr>
              <tr>
                <td colspan="4" class="text-right" style="border: none;"> </td>
                <td colspan="0" class="text-right">Total Discount</td>
                <input type="hidden" name="totalDiscount" value="1866">
                <td colspan="2">1866</td>
              </tr>
              <tr>
                <td colspan="4" class="text-right" style="border: none;"> </td>
                <td colspan="0" class="text-right">Sub Total</td>
                <input type="hidden" name="subTotal" value="18,000">
                <td colspan="2">18,000</td>
              </tr>
            </tbody>
            <tfoot>
              <th colspan="4" class="text-right" style="border: none;"> </th>
              <th colspan="0" class="text-right" style="border: none;"></th>
              <td colspan="2" style="border: none;">
                <button type="submit" name="addNewSOFormSubmitBtn" class="btn btn-primary float-right">Final Submit</button>
              </td>
            </tfoot>
          </table>

        </div>
      </div> -->
  </form>
  <!-- modal -->

  <!-- modal end -->
  </div>
  </section>
  </div>

<?php
} else { ?>
  <div class="content-wrapper is-pr">
    <!-- Content Header (Page header) -->
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

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="p-0 pt-1 my-2">
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage Purchase Request</h3>
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?pr-creation" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a>
                </li>
              </ul>
            </div>
            <div class="filter-list">
              <a href="manage-pr.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
              <a href="pr-list.php?item" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
              <a href="pr-list.php" class="btn "><i class="fa fa-lock-open mr-2 "></i>Open PR</a>
              <a href="pr-list.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PR</a>
            </div>
            <?php
            $keywd = '';
            if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
              $keywd = $_REQUEST['keyword'];
            } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
              $keywd = $_REQUEST['keyword2'];
            } ?>
            <div class="card card-tabs" style="border-radius: 20px;">
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-2 col-md-2 col-sm-12">
                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-12">
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

                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Request</h5>

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
              <!-- <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog" aria-hidden="true"></i></a> -->
              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                  <?php
                  // console($_POST);
                  $cond = '';

                  $sts = " AND `status`!='deleted'";
                  if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                    $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                  }

                  if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                    $cond .= " AND expectedDate between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                  }


                  if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                    $cond .= " AND (`prCode` like '%" . $_REQUEST['keyword2'] . "%' OR `refNo` like '%" . $_REQUEST['keyword2'] . "%' OR `description` like '%" . $_REQUEST['keyword2'] . "%')";
                  } else {
                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                      $cond .= " AND (`prCode` like '%" . $_REQUEST['keyword'] . "%'  OR `refNo` like '%" . $_REQUEST['keyword'] . "%' OR `description` like '%" . $_REQUEST['keyword'] . "%')";
                    }
                  }
                  $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' " . $sts . "  ORDER BY purchaseRequestId desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                  $qry_list = queryGet($sql_list, true);
                  $num_list = $qry_list['numRows'];


                  $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE 1 " . $cond . " AND company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];


                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_PURCHASE_REQUEST", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($num_list > 0) {
                  ?>
                    <table class="table defaultDataTable table-hover">
                      <thead>
                        <tr class="alert-light">
                          <th>#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th>PR Number</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th>Required Date</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th>Reference Number</th>
                          <?php  }

                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th>Status</th>
                          <?php }

                          if (in_array(5, $settingsCheckbox)) { ?>
                            <th>Validity Period</th>
                          <?php }


                          if (in_array(6, $settingsCheckbox)) { ?>
                            <th>Created By</th>

                          <?php } ?>

                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $soList = $qry_list['data'];

                        // console($soList);

                        foreach ($soList as $onePrList) {
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $onePrList['prCode'] ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= formatDateORDateTime($onePrList['expectedDate']) ?></td>
                            <?php }

                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $onePrList['refNo'] ?></td>
                            <?php }

                            if (in_array(4, $settingsCheckbox)) { ?>
                              <td><?php
                                  if ($onePrList['pr_status'] == 10) {
                                    echo "closed";
                                  } else if ($onePrList['pr_status'] == 9) {
                                    echo "open";
                                  }


                                  ?></td>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td>
                                <?php

                                if ($onePrList['validityperiod'] != '') {
                                  $date1 = new DateTime($onePrList['validityperiod']);
                                  $date2 = new DateTime(date('Y-m-d'));

                                  $interval = $date1->diff($date2);
                                  $countdays = $interval->format('%a');
                                  $day = "";
                                  if ($countdays > 1) {
                                    $day = "days";
                                  } else {
                                    $day = "day";
                                  }


                                  if ($onePrList['validityperiod'] < date('Y-m-d')) {
                                    echo "expired";
                                  } else {
                                    echo $countdays . " " . $day . " Remaining";
                                  }
                                } else {
                                  echo '-';
                                }
                                // echo "Difference in days: " . $interval->format('%a days');




                                ?>
                              </td>
                            <?php }




                            if (in_array(6, $settingsCheckbox)) { ?>
                              <td>
                                <?php
                                echo getCreatedByUser($onePrList['created_by']);
                                echo $onePrList['created_by'];
                                ?>
                              </td>
                            <?php } ?>
                            <td>

                              <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePrList['purchaseRequestId'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                              <!-- right modal start here  -->
                              <div class="modal fade right customer-modal pr-modal classic-view-modal" id="fluidModalRightSuccessDemo_<?= $onePrList['purchaseRequestId'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                  <!--Content-->
                                  <div class="modal-content">
                                    <!--Header-->
                                    <div class="modal-header pt-3">
                                      <p class="heading lead  mt-2 mb-4">PR Code : <?= $onePrList['prCode'] ?></p>
                                      <p class="text-sm  mt-2 mb-2">Ref Number : <?= $onePrList['refNo'] ?></p>
                                      <p class="text-sm  mt-2 mb-2">Required Date : <?= $onePrList['expectedDate'] ?></p>
                                      <p class="text-sm  mt-2 mb-2">status: <span class="status status-modal ml-2"><?php if ($onePrList['status'] != null) {
                                                                                                                      echo $onePrList['status'];
                                                                                                                    } else {
                                                                                                                      echo "PENDING";
                                                                                                                    }  ?></span></p>
                                      <p class="text-xs mt-2 mb-2">Note : <?= $onePrList['description'] ?></p>

                                      <ul class="nav nav-tabs" id="myTab" role="tablist">

                                        <li class="nav-item">
                                          <a class="nav-link active" id="home-tab<?= $onePrList['prCode'] ?>" data-toggle="tab" href="#home<?= $onePrList['prCode'] ?>" role="tab" aria-controls="home<?= $onePrList['prCode'] ?>" aria-selected="true">Info</a>
                                        </li>

                                        <li class="nav-item">
                                          <a class="nav-link" id="classic-view-tab" data-toggle="tab" href="#classic-view<?= $onePrList['prCode'] ?>" role="tab" aria-controls="classic-view" aria-selected="false"><ion-icon name="apps-outline" class="mr-2"></ion-icon> Classic View</a>
                                        </li>

                                        <!-- -------------------Audit History Button Start------------------------- -->
                                        <li class="nav-item">
                                          <a class="nav-link auditTrail" id="history-tab<?= $onePrList['prCode'] ?>" data-toggle="tab" data-ccode="<?= $onePrList['prCode'] ?>" href="#history<?= $onePrList['prCode'] ?>" role="tab" aria-controls="history<?= $onePrList['prCode'] ?>" aria-selected="false">Trail</a>
                                        </li>
                                        <!-- -------------------Audit History Button End------------------------- -->
                                      </ul>


                                    </div>
                                    <!--Body-->
                                    <div class="modal-body px-4">
                                      <div class="tab-content pt-1" id="myTabContent">
                                        <div class="tab-pane fade show active" id="home<?= $onePrList['prCode'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                          <div class="col-md-12">

                                            <div class="purchase-create-section mt-2 mb-4" id="action-navbar">
                                              <form action="" method="POST">
                                                <?php if ($onePrList['pr_status'] == 10) {
                                                } else {
                                                ?>
                                                  <!-- <a class="btn btn-primary create-purchase float-right" href="manage-purchases-orders.php?pr-po-creation=<?= $onePrList['purchaseRequestId'] ?>">Create Purchase Order</a> -->

                                                  <a class="btn btn-primary create-purchase float-right" href="manage-pr.php?close-pr=<?= $onePrList['purchaseRequestId'] ?>"> Close PR</a>
                                                <?php
                                                }
                                                ?>

                                                <input type="hidden" value="<?= $onePrList['prCode'] ?>" name="prCode">
                                                <input type="hidden" value="<?= $onePrList['purchaseRequestId'] ?>" name="prid">
                                                <?php if ($onePrList['status'] == "") { ?>
                                                  <i title="Delete" style="font-size: 1.2em" class="fa fa-trash text-danger mx-3"></i>
                                                <?php } ?>
                                                <!-- <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on text-primary mx-3"></i> -->
                                            </div>
                                          </div>
                                          <div class="card">

                                            <div class="card-body p-3">
                                              <div class="display-flex rfq-item-title mt-2 mb-2">
                                                <h4 class="info-h4 mb-0">
                                                  Item
                                                </h4>
                                                <div class="action-btn-flex">
                                                  <a href="<?= LOCATION_URL ?>manage-rfq.php?prid=<?= $onePrList['purchaseRequestId'] ?>" class="btn btn-primary"><i class="fa fa-list pr-2"></i> RFQ LIST</a>
                                                  <?php if ($onePrList['pr_status'] == 10) {
                                                  } else {
                                                  ?>
                                                    <button type="submit" name="addNewRFQFormSubmitBtn" class="btn btn-primary float-right"><i class="fa fa-plus pr-2"></i> Add To RFQ</button>
                                                    <a href="manage-pr.php?edit=<?= $onePrList['purchaseRequestId'] ?>" type="submit" name="editNewRFQFormSubmitBtn" class="btn btn-primary float-right"><i class="fa fa-edit pr-2"></i>Edit</a>
                                                  <?php
                                                  }
                                                  ?>
                                                </div>
                                              </div>
                                              <hr class="mt-1 mb-1">


                                              <div class="row px-3 p-0 m-0 mb-2">


                                                <?php
                                                $itemDetails = $BranchPrObj->fetchBranchPrItems($onePrList['purchaseRequestId'])['data'];
                                                // console($itemDetails);
                                                // exit();
                                                // console($_POST);
                                                foreach ($itemDetails as $oneItem) {

                                                ?>



                                                  <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                    <div class="accordion-item">
                                                      <h2 class="accordion-header" id="flush-headingOne">
                                                        <input type="checkbox" class="rfq-item-checkbox" name="itemId[]" value="<?= $oneItem['itemId'] ?>" />
                                                        <button class="accordion-button btn btn-primary collapsed mb-1 pl-5" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                          <?= $oneItem['itemName'] ?>
                                                          &nbsp;
                                                          <p class="font-bold text-xs">Remaining Quantity : <?= $oneItem['remainingQty'] ?></p>

                                                          <?php
                                                          $itemId = $oneItem['itemId'];
                                                          $prId = $onePrList['purchaseRequestId'];
                                                          $query = "SELECT * FROM erp_rfq_items WHERE prId = '$prId' AND ItemId = '$itemId'";
                                                          $qry = queryGet($query, true);
                                                          $num = $qry['numRows'];

                                                          if ($num != 0) {
                                                          ?>
                                                            <span class="badge badge-primary ml-2">Added</span>
                                                          <?php
                                                          }
                                                          ?>
                                                        </button>
                                                      </h2>
                                                      <div id="basicDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                        <div class="accordion-body p-0">
                                                          <div class="card bg-white">

                                                            <div class="card-body p-3">

                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">Item Code :</p>
                                                                <p class="font-bold text-xs"><?= $oneItem['itemCode'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">Item Name :</p>
                                                                <p class="font-bold text-xs"><?= $oneItem['itemName'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">Item Quantity :</p>
                                                                <p class="font-bold text-xs"><?= $oneItem['itemQuantity'] ?></p>
                                                              </div>
                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">UOM :</p>
                                                                <p class="font-bold text-xs"><?= $oneItem['uomName'] ?></p>
                                                              </div>

                                                              <div class="display-flex-space-between">
                                                                <p class="font-bold text-xs">Note :</p>
                                                                <p class="font-bold text-xs"><?= $oneItem['itemNote'] ?></p>
                                                              </div>

                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                <?php } ?>
                                                </form>
                                              </div>
                                            </div>
                                          </div>
                                        </div>


                                        <div class="tab-pane fade" id="classic-view<?= $onePrList['prCode'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                          <div class="card classic-view bg-transparent">
                                            <div class="card-body classic-view-so-table" style="overflow: auto;">
                                              <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" id="printButton">Print Table</button> -->
                                              <button type="button" class="btn btn-primary classic-view-btn float-right" onclick="window.print(); return false;">Print</button>
                                              <div class="printable-view">
                                                <h3 class="h3-title text-center font-bold text-sm mb-4">Purchase Request</h3>

                                                <?php

                                                $companyData = $BranchPoObj->fetchCompanyDetailsById($company_id)['data'];

                                                //console($companyData);

                                                ?>
                                                <table class="classic-view table-bordered">
                                                  <tbody>
                                                    <tr>
                                                      <td colspan="3">
                                                        <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                                        <p><?= $companyData['company_flat_no'] ?>, <?= $companyData['company_building'] ?></p>
                                                        <p><?= $companyData['company_district'] ?>,<?= $companyData['company_location'] ?>,<?= $companyData['company_pin'] ?></p>
                                                        <p><?= $companyData['company_city'] ?></p>
                                                        <p>Company’s PAN: <?= $companyData['company_pan'] ?></p>
                                                        <p>State Name :<?= $companyData['company_state'] ?></p>
                                                      </td>
                                                      <td class="border-right-none">
                                                        <p>Purchase Request Number</p>
                                                        <p class="font-bold"><?= $onePrList['prCode'] ?></p>
                                                      </td>
                                                      <td class="border-left-none">
                                                        <p>Dated</p>
                                                        <p class="font-bold"><?= formatDateORDateTime($onePrList['pr_date']) ?></p>
                                                      </td>
                                                    </tr>
                                                  </tbody>
                                                  <tbody>
                                                    <tr>
                                                      <th>Sl No.</th>
                                                      <th>Particulars</th>
                                                      <th>Quantity</th>
                                                      <th>UOM</th>
                                                      <th>Note</th>
                                                    </tr>
                                                    <?php
                                                    foreach ($itemDetails as $oneItemList) {

                                                      //console($oneItemList)

                                                    ?>

                                                      <tr>
                                                        <td class="text-center">1</td>
                                                        <td class="text-center">
                                                          <p class="font-bold"><?= $oneItemList['itemName'] ?></p>
                                                          <p class="text-italic"><?= $oneItemList['itemCode'] ?></p>
                                                        </td>
                                                        <td class="text-center"><?= $oneItemList['itemQuantity'] ?></td>
                                                        <td class="text-center"><?= $oneItemList['uomName'] ?></td>
                                                        <td class="text-center"><?= $oneItemList['itemNote'] ?></td>
                                                      </tr>

                                                    <?php

                                                    }
                                                    ?>


                                                    <!-- <tr>
                                                      <td colspan="5">
                                                        <p>Amount Chargeable (in words)</p>
                                                        <p class="font-bold">ONE THOUSAND TWO HUNDRED AND SIXTY ONLY</p>
                                                      </td>
                                                      <td colspan="5" class="text-right">E. & O.E</td>
                                                    </tr> -->
                                                    <!-- <tr>
                                                      <td colspan="5"></td>
                                                      <td colspan="5">
                                                        <p class="font-bold">Company’s Bank Details</p>
                                                        <p>Bank Name :</p>
                                                        <p>A/c No. :</p>
                                                        <p>Branch & IFS Code :</p>
                                                      </td>
                                                    </tr> -->
                                                    <tr>
                                                      <td colspan="3">
                                                        <p>Remarks:</p>
                                                        <p>Created By: <b><?= getCreatedByUser($onePrList['created_by']) ?></b></p>
                                                      </td>
                                                      <td colspan="2" class="text-right border">
                                                        <p class="text-center font-bold"> for 6 Livo</p>
                                                        <p class="text-center sign-img">
                                                          <img width="100" src="../../public/storage/signature/<?= $companyData['signature'] ?>" alt="signature">
                                                        </p>
                                                      </td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                              </div>

                                            </div>
                                          </div>
                                        </div>


                                        <!-- -------------------Audit History Tab Body Start------------------------- -->
                                        <div class="tab-pane fade" id="history<?= $onePrList['prCode'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                          <div class="audit-head-section mb-3 mt-3 ">
                                            <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($onePrList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePrList['created_at']) ?></p>
                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($onePrList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePrList['updated_at']) ?></p>
                                          </div>
                                          <hr>
                                          <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $onePrList['prCode'] ?>">

                                            <ol class="timeline">

                                              <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                <div class="new-comment font-bold">
                                                  <p>Loading...
                                                  <ul class="ml-3 pl-0">
                                                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                  </ul>
                                                  </p>
                                                </div>
                                              </li>
                                              <p class="mt-0 mb-5 ml-5">Loading...</p>

                                              <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                <div class="new-comment font-bold">
                                                  <p>Loading...
                                                  <ul class="ml-3 pl-0">
                                                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                  </ul>
                                                  </p>
                                                </div>
                                              </li>
                                              <p class="mt-0 mb-5 ml-5">Loading...</p>


                                            </ol>
                                          </div>
                                        </div>
                                        <!-- -------------------Audit History Tab Body End------------------------- -->
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
                          <td colspan="9">
                            <!-- Start .pagination -->

                            <?php
                            if ($count > 0 && $count > $GLOBALS['show']) {
                            ?>
                              <div class="pagination align-right">
                                <?php pagination($count, "frm_opts"); ?>
                              </div>

                              <!-- End .pagination -->

                            <?php  } ?>

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
                      <input type="hidden" name="pageTableName" value="ERP_VENDOR_DETAILS" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                PR Number</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                Required Date </td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                Reference Number</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                Status</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                validity period</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                Created By</td>
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
  <!-- For Pegination------->
  <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                echo  $_REQUEST['pageNo'];
                                              } ?>">
  </form>
  <!-- End Pegination from------->
<?php } ?>

<?php
require_once("../common/footer.php");
?>
<script>



  $(document).on("click", ".add-btn-minus", function() {
    $(this).parent().parent().remove();
  });

  function srch_frm() {
    if ($('#form_date_s').val().trim() != '' && $('#to_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
      //$("#phone_r_err").html("Your Phone Number");
      alert("Enter To Date");
      $('#to_date_s').focus();
      return false;
    }
    if ($('#to_date_s').val().trim() != '' && $('#form_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
      //$("#phone_r_err").html("Your Phone Number");
      alert("Enter From Date");
      $('#form_date_s').focus();
      return false;
    }

  }

  function table_settings() {
    var favorite = [];
    $.each($("input[name='settingsCheckbox[]']:checked"), function() {
      favorite.push($(this).val());
    });
    var check = favorite.length;
    if (check < 5) {
      alert("Please Check Atleast 5");
      return false;
    }

  }


  function rm() {
    $(event.target).closest("tr").remove();
  }

  function addMultiQty(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row_${id}`).append(`<tr><td><span class='has-float-label'><input type='date' name='listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]' class='form-control' placeholder='delivery date'><label>Delivery date</label></span></td><td><span class='has-float-label'><input type='text' name='listItem[${id}][deliverySchedule][${addressRandNo}][quantity]' class='form-control' placeholder='quantity'><label>quantity</label></span></td><td><a class='btn btn-danger' onclick='rm()'><i class='fa fa-minus'></i></a></td></tr>`);
  }


  function addDeliveryQty(randCode) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row-delivery_${randCode}`).append(`
                                          <div class="row">
                                        <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Delivery date</label>
                                            <input type="date" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Quantity</label>
                                            <input type="text" data-attr="${randCode}" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity multiQty_${randCode}" id="multiQuantity_${addressRandNo}" placeholder="quantity" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                    <div class="add-btn-minus">
                                            <a style="cursor: pointer" class="btn btn-danger qty_minus" data-attr="${randCode}">
                                              <i class="fa fa-minus"></i>
                                            </a>
                                            </div>
                                    </div>
                                </div>`);
  }
</script>
<script>
  $(document).ready(function() {
    $('#itemsDropDown')
      .select2()
      .on('select2:open', () => {
        // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
      });
    $('#customerDropDown')
      .select2()
      .on('select2:open', () => {
        // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
      });


    $("#usetypesDropdown").on("change", function() {
      // alert(1);
      let type = $(this).val();
      console.log(type);
      if (type != "") {

        $.ajax({
          type: "GET",
          url: `ajaxs/pr/ajax-items.php`,
          data: {

            type
          },
          beforeSend: function() {
            $("#itemsDropDown").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            console.log(response);
            $("#itemsDropDown").html(response);
          }
        });
      } else {
        $("#itemsDropDown").html('');
      }
    });



    // function loadItems() {
    //   $.ajax({
    //     type: "GET",
    //     url: `ajaxs/pr/ajax-items.php`,
    //     beforeSend: function() {
    //       $("#itemsDropDown").html(`<option value="">Loding...</option>`);
    //     },
    //     success: function(response) {
    //       $("#itemsDropDown").html(response);
    //     }
    //   });
    // }
    // loadItems();

    // get item details by id
    $("#itemsDropDown").on("change", function() {
      let itemId = $(this).val();
      const searchValue = itemId;
      let flag = 0;
      $('.pr_item_list').each(function(index) {
        if ($(this).val().includes(searchValue)) {
          console.log(`Search value ${searchValue} found in field ${index + 1}.`);
          flag++;
        } else {
          console.log(`Search value ${searchValue} not found`);
        }
      });
      if (flag == 0) {

        $.ajax({
          type: "GET",
          url: `ajaxs/pr/ajax-items-list.php`,
          data: {
            act: "listItem",
            itemId
          },
          beforeSend: function() {
            //  $("#itemsTable").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            // console.log(response);
            $("#itemsTable").append(response);
          }
        });
      } else {
        //   alert("item already exists!");
        Swal.fire({
          title: 'item already exists!Do you want to add again?',

          showCancelButton: true,
          confirmButtonText: 'Save',

        }).then((result) => {
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            $.ajax({
              type: "GET",
              url: `ajaxs/pr/ajax-items-list.php`,
              data: {
                act: "listItem",
                itemId
              },
              beforeSend: function() {
                //  $("#itemsTable").html(`<option value="">Loding...</option>`);
              },
              success: function(response) {
                // console.log(response);
                $("#itemsTable").append(response);
              }
            });
            Swal.fire('Saved!', '', 'success')
          } else if (result.isDenied) {
            Swal.fire('Changes are not saved', '', 'info')
          }
        })
      }
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

  })
  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('show');
    $('#overlay').toggleClass('show');
    $('.nav-action').toggleClass('show');
  });


  function check_date() {

    let date = $("#prDate").val();

    let max = '<?php echo $max; ?>';
    let min = '<?php echo $min; ?>';


    if (date < min) {


      $("#prdatelabel").html(`<p class="text-danger text-xs" id="prdatelabel">Invalid PR creation Date</p>`);
      document.getElementById("prbtn").disabled = true;
    } else if (date > max) {
      $("#prdatelabel").html(`<p class="text-danger text-xs" id="prdatelabel">Invalid PR creation Date</p>`);
      document.getElementById("prbtn").disabled = true;
    } else {
      $("#prdatelabel").html("");
      document.getElementById("prbtn").disabled = false;

    }



  }

  function compare_date() {
    let prDate = $("#prDate").val();
    let expDate = $("#expDate").val();
    if (expDate < prDate) {
      console.log("error");
      $("#prdatelabel").html(`<p class="text-danger text-xs" id="prdatelabel">Can not be greater than Required Date</p>`);
      document.getElementById("prbtn").disabled = true;

    } else {
      $("#prdatelabel").html("");
      document.getElementById("prbtn").disabled = false;
    }
  }

  $("#prDate ").keyup(function() {

    check_date();
    compare_date();


  });
  $("#expDate").change(function() {
    compare_date();
  });

  $("#prDate").change(function() {
    compare_date();
  });
</script>

<script src="<?= BASE_URL; ?>public/validations/prValidation.js"></script>