<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");

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

if (isset($_POST["editdata"])) {
  $editDataObj = updateDataBranches($_POST);

  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}


// add PGI form ➕➕➕➕➕➕➕➕➕➕➕➕➕
$BranchSoObj = new BranchSo();

if (isset($_POST['addNewPgiFormSubmitBtn'])) {
  // console($_POST);
  $addBranchSoDeliveryPgi = $BranchSoObj->insertBranchPgi($_POST);
  // console($addBranchSoDeliveryPgi);
  if ($addBranchSoDeliveryPgi['success'] == "true") {
    $addBranchSoDeliveryPgiItems = $BranchSoObj->insertBranchPgiItems($_POST, $addBranchSoDeliveryPgi['lastID']);
    if ($addBranchSoDeliveryPgiItems['success'] == "true") {
      swalToast($addBranchSoDeliveryPgiItems["success"], $addBranchSoDeliveryPgiItems["message"]);
    } else {
      swalToast($addBranchSoDeliveryPgiItems["success"], $addBranchSoDeliveryPgiItems["message"]);
    }
  } else {
    // console($addBranchSoDeliveryPgi);
    swalToast($addBranchSoDeliveryPgi["success"], $addBranchSoDeliveryPgi["message"]);
  }
}


// imranali59059💰💰💰💰💰💰💰💰💰💰💰💰💰
if (isset($_POST['submitCollectPaymentBtn'])) {
  console($_POST);
  console($_FILES);
  $addCollectPayment = $BranchSoObj->insertCollectPayment($_POST, $_FILES);
  console($addCollectPayment);
  // if ($addCollectPayment['status'] == "success") {
  //   swalToast($addCollectPayment["status"], $addCollectPayment["message"]);
  // } else {
  //   swalToast($addCollectPayment["status"], $addCollectPayment["message"]);
  // }
}

// ✅✅✅✅✅✅✅✅✅✅✅✅✅
$customerList = $BranchSoObj->fetchCustomerList()['data'];
$fetchInvoiceByCustomer = $BranchSoObj->fetchBranchSoInvoiceBycustomerId(1)['data'];

// console($customerList);
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



  /* ######################################### */
  /* // design input type file STYLE  */

  .image-input input {
    display: none;
  }

  .image-input label {
    display: block;
    border: 2px dashed #dcdcdc;
    padding: 40px;
    cursor: pointer;
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
if (isset($_GET['create-pgi'])) {
?>
  <h1>Hello</h1>
<?php } else { ?>

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="p-0 pt-1 my-2">
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage SO Invoices</h3>
                  <button class="btn btn-warning" data-toggle="modal" data-target="#fluidModalRightCollectPayment">Collect Payment</button>

                  <!-- right modal start here  -->
                  <div class="modal fade right" id="fluidModalRightCollectPayment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                    <div style="max-width: 70%; min-width:50%" class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                      <!--Content-->
                      <div class="modal-content">
                        <form action="" method="POST">
                          <!--Header-->
                          <div class="row m-0 p-0 py-2 my-2" style="border-bottom: 1px solid grey;">
                            <div class="col-6">
                              <h5><strong>Collect Payment</strong></h5>
                            </div>
                            <div class="col-6">
                              <div class="float-right d-flex">
                                <div class="mx-2"><button class="btn btn-success" type="button" data-toggle="modal" data-target="#exampleModal" id="submitCollectPaymentBtn">POST</button></div>
                                <div class="mx-2 btn btn-danger " data-dismiss="modal" aria-label="Close">X</div>
                              </div>
                            </div>
                          </div>
                          <!-- Collect Payment Modal -->
                          <div class="modal" id="exampleModal" style="    height: 200px; width: 100%;" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Collect Payments</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <div><span style="font-family: 'Font Awesome 5 Free';" id="totalReceiveAmt">0</span> amount paid against invoice</div>
                                  <div><span style="font-family: 'Font Awesome 5 Free';" id="totalCaptureAmt">0</span> amount capture as an advanced</div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button type="submit" name="submitCollectPaymentBtn" class="btn btn-primary">Confirm</button>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!--Body-->
                          <div class="modal-body" style="padding: 0;">
                            <!-- <ul class="nav nav-tabs">
                            <li class="nav-item"><a class="nav-link active" href="#preview" data-bs-toggle="tab">Preview</a></li>
                            <li class="nav-item"><a class="nav-link" href="#otherDetails" data-bs-toggle="tab">Other Details</a></li>
                          </ul>
                          <div class="tab-content">
                            <div class="tab-pane show active" id="preview">
                              ...
                            </div>
                            <div class="tab-pane" id="otherDetails">
                              <div class="card p-5">
                               ...
                              </div>
                            </div>
                          </div> -->
                            <div class="row p-0 m-0">
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
                                <div class="input-group mb-3">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">₹</span>
                                  </div>
                                  <input type="text" name="paymentDetails[collectPayment]" value="0" class="form-control collectTotalAmt px-3" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                                <div class="bg-light p-2">
                                  <input type="hidden" name="paymentDetails[totalDueAmt]" class="totalDueAmtInp" value="0">
                                  <input type="hidden" name="paymentDetails[totalInvAmt]" class="totalInvAmtInp" value="0">
                                  <input type="hidden" name="paymentDetails[remaningAmt]" class="remaningAmtInp" value="0">
                                  <h6>Total Due Amt: <strong class="totalInvAmt">0</strong> </h6>
                                  <h6>Current Due: <strong class="totalDueAmt">0</strong> </h6>
                                  <h6>Over Due: <strong>0</strong> </h6>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <!-- <span>Advanced Pay: <strong class="advancedPaySpan" id = "adv_pay">0</strong></span> -->
                                <span>Remaining: <strong class="remaningAmt">0</strong></span>
                                <!-- <div class="custom-file">
                                  <input title="Upload Payment Advice" name="paymentDetails[paymentAdviceImg]" style="cursor: pointer;" type="file" class="custom-file-input" id="pic">
                                  <label class="custom-file-label" for="customFile"></label>
                                </div> -->
                                <div class="mt-3">
                                  <div class="image-input">
                                    <input type="file" name="paymentDetails[paymentAdviceImg]" accept="image/*" id="imageInput">
                                    <label for="imageInput" class="image-button"><i class="far fa-image"></i> Upload Payment Advice</label>
                                    <img src="" class="image-preview">
                                    <span class="change-image text-danger"><i class="fa fa-times"> Remove</i></span>
                                  </div>
                                  <!-- <span class="mb-n5">Remaining: <strong class="remaningAmt">0</strong></span> -->
                                </div>
                              </div>
                              <!-- <div class="col-md-4">
                                <img width="180" class="load_img" style="box-shadow: 0 0 5px grey;" src="" alt="">
                              </div> -->

                              <div class="col-md-12">
                                <div class="inputTableRow"></div>
                              </div>
                            </div>
                          </div>
                        </form>
                      </div>
                      <!--/.Content-->
                    </div>
                  </div>
                  <!-- right modal end here  -->
                  <!-- <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create-sales-order-delivery" class="btn btn-sm btn-primary btnstyle m-2" style="line-height: 32px;"><i class="fa fa-plus"></i> Add New</a> -->
                </li>
              </ul>
            </div>
            <?php
            $soList = $BranchSoObj->fetchBranchSoInvoice()['data'];
            console($soList);
            ?>
            <div class="card card-tabs" style="border-radius: 20px;">
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-2 col-md-2 col-sm-12">
                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-12">
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
                            <th class="borderNone">Invoice No.</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th class="borderNone">Customer PO</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th class="borderNone">Delivery Date</th>
                          <?php  }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th class="borderNone">Customer Name</th>
                          <?php }
                          if (in_array(5, $settingsCheckbox)) { ?>
                            <th class="borderNone">Status</th>
                          <?php }
                          if (in_array(6, $settingsCheckbox)) { ?>
                            <th class="borderNone">Total Items</th>
                          <?php } ?>

                          <th class="borderNone">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        foreach ($soList as $oneSoList) {
                          console('****************************************');
                          console($oneSoList);
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['invoice_no'] ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['po_number'] ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['po_date'] ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) {
                            ?>
                              <td><?= $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0]['trade_name'] ?? 0 ?></td>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td class="text-success font-weight-bold text-capitalize"><?= fetchStatusMasterByCode($oneSoList['invoiceStatus'])['data']['label'] ?></td>
                            <?php }
                            if (in_array(6, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['totalItems'] ?></td>
                            <?php } ?>
                            <td>
                              <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneSoList['so_invoice_id'] ?>"><i class="fa fa-eye"></i></a>
                              <!-- <a style="cursor: pointer;" class="btn btn-sm"><i class="fa fa-eye"></i></a> -->
                              <!-- <a href="branch-so-invoice-2.php?invoice-no=<?= base64_encode($oneSoList['so_invoice_id']) ?>" style="cursor: pointer;" class="btn btn-sm">
                                <i class="fa fa-download"></i>
                              </a> -->
                            </td>
                          </tr>

                          <?php
                          $invoiceItemDetails = $BranchSoObj->fetchBranchSoInvoiceItems($oneSoList['so_invoice_id'])['data'];
                          // console($invoiceDetails);
                          $companyDetails = $BranchSoObj->fetchCompanyDetailsById($_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'])['data'];
                          $companyBranchLocationDetails = $BranchSoObj->fetchBranchLocalionDetailsById($_SESSION['logedBranchAdminInfo']['fldAdminLocationId'])['data'];
                          $customerAddressDetails = $BranchSoObj->fetchCustomerAddressDetails($oneSoList['customer_id'])['data'];
                          // console($customerAddressDetails);
                          ?>
                          <!-- right modal start here  -->
                          <div class="modal fade right" id="fluidModalRightSuccessDemo_<?= $oneSoList['so_invoice_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                            <div style="max-width: 70%; min-width:50%" class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                              <!--Content-->
                              <div class="modal-content">
                                <!--Header-->
                                <div class="modal-header " style="background: none; border:none; color:#424242">
                                  <p class="heading lead"><?= $oneSoList['invoice_no'] ?></p>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" class="white-text">×</span>
                                  </button>
                                </div>
                                <!--Body-->
                                <div class="modal-body" style="padding: 0;">
                                  <ul class="nav nav-tabs">
                                    <li class="nav-item"><a class="nav-link active" href="#preview<?= $oneSoList['so_invoice_id'] ?>" data-bs-toggle="tab">Preview</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#otherDetails<?= $oneSoList['so_invoice_id'] ?>" data-bs-toggle="tab">Other Details</a></li>
                                  </ul>
                                  <div class="tab-content">
                                    <div class="col-md-12">
                                      <div class="shadow-sm bg-light py-2 mx-2 my-2" id="action-navbar" style="text-align:right">
                                        <form action="" method="POST">
                                          <!-- <a href="branch-so-invoice-2.php?invoice-no=<?= base64_encode($oneSoList['so_invoice_id']) ?>" name="vendorEditBtn">
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
                                    <div class="tab-pane show active" id="preview<?= $oneSoList['so_invoice_id'] ?>">


                                      <!-- ################################## -->
                                      <div class="container my-3">
                                        <div class="row p-0 m-0 pb-2" style="border-bottom: 3px solid #0090ff;">
                                          <div class="col-6 d-flex align-items-center">
                                            <img width="220" src="../../public/storage/logo/<?= $companyDetails['company_logo'] ?>" alt="">
                                          </div>
                                          <div class="col-6 d-flex align-items-end flex-column">
                                            <div>Original for Recipient</div>
                                            <div>
                                              <strong class="textColor"><?= $oneSoList['invoice_no'] ?></strong>
                                            </div>
                                            <div>
                                              <b>Date </b>
                                              <span><?php $invDate = date_create($oneSoList['invoice_date']);
                                                    echo date_format($invDate, "F d,Y"); ?></span> </span>
                                            </div>
                                            <div>
                                              <b>Due Date </b>
                                              <span><?= $oneSoList['credit_period'] ?></span> </span>
                                            </div>
                                            <div>
                                              <b>P.O. Number </b>
                                              <span><?= $oneSoList['po_number'] ?></span> </span>
                                            </div>
                                            <div>
                                              <b>P.O. Date </b>
                                              <span><?php $poDate = date_create($oneSoList['po_date']);
                                                    echo date_format($poDate, "F d,Y"); ?></span> </span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="row p-0 m-0 py-3" style="border-bottom: 3px solid #0090ff;">
                                          <div class="col-6">
                                            <div>
                                              <strong class="ml-1 textColor"><?= $companyDetails['company_name'] ?></strong>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-phone"></i>
                                              <span>7059746613</span>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-envelope"></i>
                                              <span>imranali59059@gmail.com</span>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-globe"></i>
                                              <span>www.imranali59059.com</span>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-info"></i>
                                              <input type="hidden" name="companyDetails[address]" value="<?= $companyBranchLocationDetails['othersLocation_name'] ?>, <?= $companyBranchLocationDetails['othersLocation_building_no'] ?>, <?= $companyBranchLocationDetails['othersLocation_flat_no'] ?>, <?= $companyBranchLocationDetails['othersLocation_street_name'] ?>, <?= $companyBranchLocationDetails['othersLocation_location'] ?>, <?= $companyBranchLocationDetails['othersLocation_city'] ?>, <?= $companyBranchLocationDetails['othersLocation_district'] ?>, <?= $companyBranchLocationDetails['othersLocation_state'] ?>">
                                              <span>
                                                <?= $companyBranchLocationDetails['othersLocation_name'] ?>,
                                                <?= $companyBranchLocationDetails['othersLocation_building_no'] ?>,
                                                <?= $companyBranchLocationDetails['othersLocation_flat_no'] ?>,
                                                <?= $companyBranchLocationDetails['othersLocation_street_name'] ?>,
                                                <?= $companyBranchLocationDetails['othersLocation_location'] ?>,
                                                <?= $companyBranchLocationDetails['othersLocation_city'] ?>,
                                                <?= $companyBranchLocationDetails['othersLocation_district'] ?>,
                                                <?= $companyBranchLocationDetails['othersLocation_state'] ?>
                                              </span>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-briefcase"></i>
                                              <span class="textColor"><?= $companyDetails['company_gstin'] ?? null ?></span>
                                            </div>
                                            <div>
                                              <!-- <i class="textColor fa fa-briefcase"></i> -->
                                              <strong class="ml-1">State Name: <?= fetchStateNameByGstin($companyDetails['company_gstin']) ?> Code: <?= $companyGstin = substr($companyDetails['company_gstin'], 0, 2); ?></strong>
                                            </div>
                                          </div>
                                          <!-- <div class="col-4 d-flex align-items-end flex-column">
                                        </div> -->
                                          <div class="col-6 d-flex align-items-end flex-column">
                                            <div>
                                              <strong class="ml-1 textColor"><?= $oneSoList['customer_name'] ?? null ?></strong>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-phone"></i>
                                              <span><?= $oneSoList['customer_phone'] ?? null ?></span>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-envelope"></i>
                                              <span><?= $oneSoList['customer_email'] ?? null ?></span>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-info"></i>
                                              <!-- <input type="hidden" name="customerDetails[address]" value="<?= $customerAddressDetails[1]['customer_address_building_no'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_flat_no'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_street_name'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_pin_code'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_location'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_city'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_district'] ?? null ?>, <?= $customerAddressDetails[1]['customer_address_state'] ?? null ?>"> -->
                                              <input type="hidden" name="customerDetails[address]" value="<?= $oneSoList['customer_billing_address'] ?? null ?>">
                                              <!-- <span>
                                                <?= $customerAddressDetails[1]['customer_address_building_no'] ?? null ?>,
                                                <?= $customerAddressDetails[1]['customer_address_flat_no'] ?? null ?>,
                                                <?= $customerAddressDetails[1]['customer_address_street_name'] ?? null ?>,
                                                <?= $customerAddressDetails[1]['customer_address_pin_code'] ?? null ?>,
                                                <?= $customerAddressDetails[1]['customer_address_location'] ?? null ?>,
                                                <?= $customerAddressDetails[1]['customer_address_city'] ?? null ?>,
                                                <?= $customerAddressDetails[1]['customer_address_district'] ?? null ?>,
                                                <?= $customerAddressDetails[1]['customer_address_state'] ?? null ?>
                                              </span> -->
                                              <span><?=$oneSoList['customer_billing_address'] ?? null?></span>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-briefcase"></i>
                                              <strong class="ml-1 textColor"><?= $oneSoList['customer_gstin'] ?? null ?></strong>
                                            </div>
                                            <div>
                                              <!-- <i class="textColor fa fa-briefcase"></i> -->
                                              <strong class="ml-1">State Name: <?= fetchStateNameByGstin($oneSoList['customer_gstin']) ?> Code: <?= $companyGstin = substr($oneSoList['customer_gstin'], 0, 2); ?></strong>
                                            </div>
                                          </div>
                                          <!-- <div class="col-4">
                                            <div>
                                                <h4 class="ml-1">&nbsp;</h4>
                                            </div>
                                            <div>
                                                <i class="textColor fa fa-info"></i>
                                                <span>Long Baharam, 34-38, B Building Madhuria, Tamil Nadu (TN - 33), India</span>
                                            </div>
                                            <div>
                                                <i class="textColor fa fa-phone"></i>
                                                <span>7059746613</span>
                                            </div>
                                            <div>
                                                <i class="textColor fa fa-envelope"></i>
                                                <span>imranali59059@gmail.com</span>
                                            </div>
                                            <div>
                                                <i class="textColor fa fa-globe"></i>
                                                <span>www.imranali59059.com</span>
                                            </div>
                                        </div> -->
                                        </div>
                                        <div class="row p-0 m-0">
                                          <div class="col-md-12" style="overflow: auto;">
                                            <div class="row">
                                              <div class="col-6">
                                                <div class="row">
                                                  <div class="col-1 font-weight-bold bg-light">NO</div>
                                                  <div class="col-5 font-weight-bold">PRODUCT NAME</div>
                                                </div>
                                              </div>
                                              <div class="col-6">
                                                <div class="row">
                                                  <div class="col-3 font-weight-bold bg-light">HSN CODE</div>
                                                  <div class="col-3 font-weight-bold">QTY</div>
                                                  <div class="col-3 font-weight-bold bg-light">UNIT PRICE</div>
                                                  <div class="col-3 font-weight-bold text-right">AMOUNT</div>
                                                </div>
                                              </div>
                                            </div>
                                            <!-- list items here -->
                                            <?php
                                            $i = 1;
                                            foreach ($invoiceItemDetails as $item) {
                                            ?>
                                              <div class="row py-2">
                                                <div class="col-6">
                                                  <div class="row">
                                                    <div class="col-1 font-weight-bold bg-light"><?= $i++; ?></div>
                                                    <div class="col-11">
                                                      <strong><?= $item['itemName'] ?></strong>
                                                      <div><small><?= $item['itemDesc'] ?></small></div>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="col-6">
                                                  <div class="row">
                                                    <div class="col-3 font-weight-bold bg-light"><?= $item['hsnCode'] ?></div>
                                                    <div class="col-3"><?= $item['qty'] ?>/<?= $item['uom'] ?></div>
                                                    <div class="col-3 font-weight-bold bg-light"><?= $item['unitPrice'] ?></div>
                                                    <div class="col-3 text-right"><?= $item['qty'] * $item['unitPrice'] ?></div>
                                                  </div>
                                                </div>
                                              </div>
                                            <?php } ?>
                                            <!-- list items here -->
                                          </div>
                                        </div>

                                        <div class="row p-0 m-0">
                                          <div class="col-8">
                                            <!-- <div>Total: ₹ Twenty Seven Thousand Four Hundred Tinety Rupees Only</div>
                                          <div><a href="#">Pay Now with PayPal </a></div> -->
                                            <div>
                                              <strong class="textColor">AUTHORIZED SIGNATORY</strong>
                                            </div>
                                            <img width="160" src="../../public/storage/<?= $oneSoList['company_signature'] ?>" alt="">
                                          </div>
                                          <div class="col-2 d-flex align-items-end flex-column textColor">
                                            <div>SUB TOTAL</div>
                                            <?php
                                            $companyGstin = substr($companyDetails['company_gstin'], 0, 2);
                                            $customerGstin = substr($oneSoList['customer_gstin'], 0, 2);
                                            $conditionGST = $companyGstin == $customerGstin;
                                            if ($conditionGST) {
                                            ?>
                                              <div>CGST</div>
                                              <div>SGST</div>
                                            <?php } else { ?>
                                              <div>IGST</div>
                                            <?php } ?>
                                            <div>TOTAL DISCOUNT</div>
                                            <div>TOTAL AMOUNT</div>
                                          </div>
                                          <div class="col-2 d-flex align-items-end flex-column textColor">
                                            <div class=""><?= $oneSoList['sub_total_amt'] ?></div>
                                            <?php
                                            if ($conditionGST) {
                                              $gstAmt = ($oneSoList['total_tax_amt'] / 2);
                                            ?>
                                              <div class=""><?= $gstAmt ?></div>
                                              <div class=""><?= $gstAmt ?></div>
                                            <?php } else { ?>
                                              <div class=""><?= $oneSoList['total_tax_amt'] ?></div>
                                            <?php } ?>
                                            <div class=""><?= $oneSoList['totalDiscount'] ?></div>
                                            <div class=""><?= $oneSoList['all_total_amt'] ?></div>
                                          </div>
                                          <div class="col-12">
                                            <strong class="textColor">NOTE:</strong>
                                            <div class="text"><?= $companyDetails['company_footer'] ?></div>
                                          </div>
                                        </div>
                                      </div>
                                      <!-- ################################## -->
                                    </div>
                                    <div class="tab-pane" id="otherDetails<?= $oneSoList['so_invoice_id'] ?>">
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
    var staticRemain = 0;
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
      $(".dueAmt").each(function() {
        totalDueAmt += (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
      });
      $(".invAmt").each(function() {
        totalInvAmt += (parseFloat($(this).text()) > 0) ? parseFloat($(this).text()) : 0;
      });
      $(".totalDueAmt").html(totalDueAmt);
      $(".totalInvAmt").html(totalInvAmt);
      $(".totalDueAmtInp").val(totalDueAmt);
      $(".totalInvAmtInp").val(totalInvAmt);
    }

    // imranali59059👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰👨🏾‍🦰
    // select customer 
    $("#customerSelect").on("change", function() {
      let customerSelect = $(this).val();
      // console.log(advancedPayAmt);

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
          calculateDueAmt();
          let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
          $(".remaningAmt").html(advancedPayAmt);
          console.log('first', advancedPayAmt);
        }
      });
    });

    // imranali59059💰💰💰💰💰💰💰💰💰💰💰💰💰
    // collect payment Amount 
    $(document).on("keyup", ".collectTotalAmt", function() {
      let thisAmt = $(this).val();
      let advancedPayAmt = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
      let rem = (parseFloat(thisAmt) + parseFloat(advancedPayAmt)) ? (parseFloat(thisAmt) + parseFloat(advancedPayAmt)) : 0;
      staticRemain = rem;

      $(".remaningAmt").text(rem);
    })
    // received payment amount🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢🪢 
    $(document).on("blur", ".receiveAmt", function() {
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
      $(".remaningAmt").text(remaintTotalAmt);
      $(".remaningAmtInp").val(remaintTotalAmt);
      console.log('due amt', dueAmt, recAmt);
      if (recAmt <= dueAmt) {
        $(`#warningMsg_${rowId}`).hide();
      } else {
        $(`#warningMsg_${rowId}`).show();
      }
    });

    $("#submitCollectPaymentBtn").on("click", function() {
      let enterAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
      let totalRecAmt2 = 0;
      let advancedPayAmt2 = ($(".advancedPayAmt").text()) ? ($(".advancedPayAmt").text()) : 0;
      $(".receiveAmt").each(function() {
        totalRecAmt2 += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      });
      let totalCaptureAmt = (parseFloat(enterAmt) + parseFloat(advancedPayAmt2)) - (parseFloat(totalRecAmt2));
      console.log(totalRecAmt2, enterAmt);
      $("#totalReceiveAmt").text(`₹${totalRecAmt2}`);
      $("#totalCaptureAmt").text(`₹${totalCaptureAmt}`);
    });

    // imranali59059🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️🖼️
    // dynamically image upload and show 
    $('#pic').on("change", function(e) {
      let url = $(this).val();
      let img = $('.load_img');
      let tmppath = URL.createObjectURL(e.target.files[0]);
      img.attr('src', tmppath);
      $(".imageUrl").html(url);
    });

    // imranali59059📁📁📁📁📁📁📁📁📁📁📁📁📁
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
  })
</script>