<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
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
$check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];
$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];
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


  $addBranchPr = $BranchPrObj->addBranchPr($_POST);
  // console($addBranchPr);
  // exit();
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


  $addBranchRfq = $BranchPrObj->addBranchRFQ($_POST);

  swalToast($addBranchRfq["status"], $addBranchRfq["message"]);
}



if (isset($_GET["close-pr"])) {
  $pr_id = $_GET['close-pr'];
  $update = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_REQUEST . "` SET `pr_status`=10 WHERE `purchaseRequestId`=$pr_id");
  swalToast($update["status"], $update["message"]);
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
                            <input type="date" name="expDate" class="form-control" id="expDate" value="<?= $today ?>" min = "<?= $today ?>" />
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
                            <p id ="dateInputvalidLabel"></p>
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
            <div class="col-lg-12 col-md-12 col-sm-12">
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
                        <div class="col-lg-2 col-md-2 col-sm-12">
                          <div class="form-input">

                            <label for="date">Required Date<span class="text-danger">*</span></label>
                            <input type="date" name="expDate" class="form-control" id="expDate" value="<?= $row['expectedDate'] ?>" min = "<?= $today ?>"/>
                          </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12">
                          <div class="form-input">
                            <label for="date">PR Date<span class="text-danger">*</span></label>
                            <input type="date" id="prDate" name="prDate" class="form-control" id="prDate" max="<?= $max ?>" min="<?= $min ?>" value="<?= $row['pr_date'] ?>" />
                            <p id="prdatelabel"></p>
                          </div>
                        </div>



                        <div class="col-lg-2 col-md-2 col-sm-12">
                          <div class="form-input">
                            <label for="date">Validity Period</label>
                            <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" value="<?= $row['validityperiod'] ?>" required>
                            <p id ="dateInputvalidLabel"></p>
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
                      <input step="any" type="number" name="listItem[<?= $randCode ?>][qty]" value="<?= $qty ?>" class="form-control full-width itemQty inputQuantityClass" id="itemQty_<?= $randCode ?>">
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
} else {
  $url = BRANCH_URL . 'location/manage-pr.php';
?>
  <script>
    window.location.href = "<?php echo $url; ?>";
  </script>
<?php
} ?>

<?php
require_once("../common/footer.php");
?>
<script>
    function inputQuantity(number) {
    if (number != null || number != "") {
      number = number ?? 0;
      let num = parseFloat(number);
      if (isNaN(num)) {
        return number;
      }
      let base = <?= $decimalQuantity ?>;
      let res = num.toFixed(base);
      return res.replace(/,/g, ''); // Ensure no commas
    }
    return "";
  }
  // $(document).on("click", ".add-btn-minus", function() {
  //   $(this).parent().parent().remove();
  // });

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


  function addDeliveryQty(randCode,itemid) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row-delivery_${randCode}`).append(`
                                          <div class="row">
                                        <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Delivery date</label>
                                            <input type="date" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][multiDeliveryDate]" class="form-control delDate delDate_${addressRandNo}" id="delivery-date" placeholder="delivery date" data-attr="${addressRandNo}"  data-itemid="${itemid}" value="">
                                            
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Quantity</label>
                                            <input type="text" data-attr="${randCode}" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity multiQty_${randCode}" id="multiQuantity_${addressRandNo}" placeholder="quantity" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                    <div class="add-btn-minus" data-itemid="${itemid}" data-attr="${randCode}">
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
      // console.log(type);
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
            // console.log(response);
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
      let expdate=$("#expDate").val();
      let flag = 0;
      $('.pr_item_list').each(function(index) {
        if ($(this).val().includes(searchValue)) {
          // console.log(`Search value ${searchValue} found in field ${index + 1}.`);
          flag++;
        } else {
          // console.log(`Search value ${searchValue} not found`);
        }
      });
      if (flag == 0) {

        $.ajax({
          type: "GET",
          url: `ajaxs/pr/ajax-items-list.php`,
          data: {
            act: "listItem",
            itemId,
            date:expdate
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
      // alert(sls);
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
          // console.log(response);
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
      // console.log("error");
      $("#prdatelabel").html(`<p class="text-danger text-xs" id="prdatelabel">Can not be greater than Required Date</p>`);
      document.getElementById("prbtn").disabled = true;

    } else {
      $("#prdatelabel").html("");
      document.getElementById("prbtn").disabled = false;
    }
  }

function compareValDate(){

  let dateInputvalid = $("#dateInputvalid").val();
  //alert(dateInputvalid);
  let expDate = $("#expDate").val();
  //alert(expDate);
  if(expDate > dateInputvalid){
    $("#dateInputvalidLabel").html(`<p class="text-danger text-xs" id="prdatelabel">Can not be lesser than Required Date</p>`);
    document.getElementById("prbtn").disabled = true;
  }
  else{

    $("#dateInputvalidLabel").html(`<p class="text-danger text-xs" id="prdatelabel"</p>`);
    document.getElementById("prbtn").disabled = false;

  }
}

$("#dateInputvalid").keyup(function() {
 // alert(1);

compareValDate();

});


$("#dateInputvalid").change(function() {

compareValDate();

});



  $("#prDate ").keyup(function() {

    check_date();
    compare_date();


  });
  $("#expDate").change(function() {
    compare_date();
    compareValDate();
  });

  $("#prDate").change(function() {
    compare_date();
  });
//   $(document).on("click", "#prbtn", function (e) {
//         let inputqtyvalue = inputQuantity($('.itemQty').val());
//         $('.itemQty').val(inputqtyvalue);
//         let delqtyvalue = inputQuantity($('.multiQuantity ').val());
//         $('.multiQuantity ').val(delqtyvalue);
        
// });

// $(document).on("keyup change", ".delDate", function() {

//  let delDate = $(this).val();
//   let expDate = $("#expDate").val();
//   if(delDate < expDate){
//     alert(1);
//   } 
 
//   });

  // alert(1);
 

</script>

<script src="<?= BASE_URL; ?>public/validations/prValidation.js"></script>