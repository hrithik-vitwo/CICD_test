<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-customers-controller.php");


// console($_SESSION);

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

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();
$customerDetails = new CustomersController();

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

// console($singleSoDetails);
?>
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<style>
  .dropdown-content {
    display: none;
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
        <div class="row align-items-center">
          <div class="col col-6">
            <div class="col-lg-2 d-flex col-md-2 col-sm-12">
              <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
              <a href="direct-create-invoice.php" class="btn shadow" style="color:#003060;line-height: 32px;min-width: 145px !important;z-index:999;"> <i class="fa fa-plus" aria-hidden="true"> Create Invoice</i></a>
            </div>
          </div>
          <div class="col col-6">
            <div class="section serach-input-section">
              <input type="text" id="myInput" placeholder="" class="field form-control">
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
        </div>
        <div class="card list-view-div">
          <div class="card-body">
            <div class="row">
              <div class="col-1">
                <input type="checkbox">
              </div>
              <div class="col col-1">#</div>
              <div class="col">Icon</div>
              <div class="col">Invoice No.</div>
              <div class="col">Invoice Amount</div>
              <div class="col">Delivery date</div>
              <div class="col">Due in (day/s)</div>
              <div class="col">Status</div>
              <div class="col">Action</div>
            </div>
            <hr />

            <?php
            $soList = $BranchSoObj->fetchBranchSoInvoice()['data'];
            // console($soList);
            ?>
            <?php
            $mobileView = '';
            $increment = 1;
            foreach ($soList as $oneSoList) {
              $customerPic = $customerDetails->getDataCustomerDetails($oneSoList['customer_id'])['data'][0]['customer_picture'];
              $customerPicture = '';
              $customerPict = mb_substr($oneSoList['customer_name'], 0, 1);

              ($customerPic != '') ? ($customerPicture = '<img src="' . BASE_URL . 'public/storage/avatar/' . $customerPic . '" class="img-fluid avatar rounded-circle" alt="">') : ($customerPicture = '<div class="img-fluid avatar rounded-circle d-flex justify-content-center align-items-center" style="border: 1px solid grey;">' . $customerPict . '</div>');

              $temDueDate = date_create($oneSoList["invoice_date"]);
              date_add($temDueDate, date_interval_create_from_date_string($oneSoList["credit_period"] . " days"));
              $todayDate = new DateTime(date("Y-m-d"));
              $oneInvDueDays = $todayDate->diff(new DateTime(date_format($temDueDate, "Y-m-d")))->format("%r%a");
              $dueInDaysClass = ($oneInvDueDays >= 0) ? (($oneInvDueDays == 0) ? "status-info" : "status") : "status-danger";

              $oneInvDueDays = ($oneInvDueDays >= 0) ? (($oneInvDueDays >= 1) ? (($oneInvDueDays == 1) ? "Due in 1 day" : "Due in " . $oneInvDueDays . " days") : "Due Today") : (($oneInvDueDays == -1) ? "Overdue by 1 day" : "Overdue by " . abs($oneInvDueDays) . " days");


              // console($oneSoList);
            ?>
              <div class="row">
                <div class="col-1">
                  <input type="checkbox">
                </div>
                <div class="col-1"><?= $increment++ ?></div>
                <div class="col icon-mobile">
                  <?= $customerPicture ?>
                  <p class="company-name mt-1"><?= $oneSoList['customer_name'] ?></p>
                </div>
                <div class="col invoice-num-mobile"><?= $oneSoList['invoice_no'] ?>
                  <p class="item-count mt-1">[<?= $oneSoList['totalItems'] ?> item/s]</p>
                </div>
                <div class="col amount-invoice-mobile"><span class="rupee-symbol"> ₹ </span><?= $oneSoList['all_total_amt'] ?></div>
                <div class="col delivery-date-mobile"><?= $oneSoList['po_date'] ?></div>
                <div class="col delivery-date-mobile">
                  <p class="<?= $dueInDaysClass ?> text-xs w-75 text-center"><?= $oneInvDueDays ?></p>
                </div>
                <div class="col status-mobile">
                  <div class="status-custom text-xs w-75 text-secondary">
                    <?php if ($oneSoList['mailStatus'] == 1) {
                      echo 'SENT <div class="round">
                        <ion-icon name="checkmark-sharp"></ion-icon>
                      </div>';
                    } elseif ($oneSoList['mailStatus'] == 2) {
                      echo '<span class="text-primary">VIEW</span> <div class="round text-primary">
                        <ion-icon name="checkmark-done-sharp"></ion-icon>
                      </div>';
                    } ?>

                    <!-- <div class="round">
                    <ion-icon name="checkmark-done-sharp"></ion-icon>
                    </div> -->
                  </div>
                  <p class="status-date"><?= $oneSoList['updated_at'] ?></p>
                </div>
                <div class="col action-mobile">
                  <div class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                      &#xFE19;
                    </a>
                    <ul class="dropdown-menu border-0 w-50" aria-labelledby="dropdownMenuLink">
                      <!-- <li class="text-center"><a class="text-sm" style="cursor:pointer; text-decoration: none;">Edit</a></li>
                    <hr /> -->
                      <li><a class="text-sm" style="cursor:pointer; text-decoration: none;" data-toggle="modal" data-target="#fluidModalRightSuccessDemo1_<?= $oneSoList['so_invoice_id'] ?>">View</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <hr />
              <!-- manage internal modal start🎈🎈🎈🎈🎈🎈🎈 -->
              <?php
              $invoiceItemDetails = $BranchSoObj->fetchBranchSoInvoiceItems($oneSoList['so_invoice_id'])['data'];
              // console($invoiceDetails);
              // console($invoiceItemDetails);
              ?>

              <!-- right modal start here  -->
              <div class="modal fade right" id="fluidModalRightSuccessDemo1_<?= $oneSoList['so_invoice_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                <div style="max-width: 70%; min-width:50%" class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                  <!--Content-->
                  <div class="modal-content">
                    <!--Header-->
                    <div class="modal-header " style="background: none; border:none; color:#424242">
                      <p class="heading lead"><?= $oneSoList['invoice_no'] ?></p>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="white-text">X</span>
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
                                <img width="220" src="../../public/storage/logo/<?= $oneSoList['company_logo'] ?>" alt="">
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
                                <!-- <div>
                                            <strong class="ml-1 textColor">Sorina TEST 123</strong>
                                          </div> -->
                                <div>
                                  <i class="textColor fa fa-briefcase"></i>
                                  <span><?= $oneSoList['company_gstin'] ?></span>
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
                                  <span>
                                    <?= $oneSoList['company_address'] ?>
                                  </span>
                                </div>
                              </div>
                              <!-- <div class="col-4 d-flex align-items-end flex-column">
                                        </div> -->
                              <div class="col-6 d-flex align-items-end flex-column">
                                <div>
                                  <strong class="ml-1 textColor"><?= $oneSoList['customer_name'] ?></strong>
                                </div>
                                <div>
                                  <strong class="ml-1 textColor"><?= $oneSoList['customer_gstin'] ?></strong>
                                </div>
                                <div>
                                  <i class="textColor fa fa-phone"></i>
                                  <span><?= $oneSoList['customer_phone'] ?></span>
                                </div>
                                <div>
                                  <i class="textColor fa fa-envelope"></i>
                                  <span><?= $oneSoList['customer_email'] ?></span>
                                </div>
                                <div>
                                  <i class="textColor fa fa-info"></i>
                                  <span><?= $oneSoList['customer_address'] ?></span>
                                </div>
                              </div>

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
                                        <div class="col-3 text-right"><?= $item['totalPrice'] ?></div>
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
                                <div>TOTAL TAX</div>
                                <div>TOTAL DISCOUNT</div>
                                <div>TOTAL AMOUNT</div>
                              </div>
                              <div class="col-2 d-flex align-items-end flex-column textColor">
                                <div class=""><?= $oneSoList['sub_total_amt'] ?></div>
                                <div class=""><?= $oneSoList['total_tax_amt'] ?></div>
                                <div class=""><?= $oneSoList['totalDiscount'] ?></div>
                                <div class=""><?= $oneSoList['all_total_amt'] ?></div>
                              </div>
                              <div class="col-12">
                                <strong class="textColor">NOTE:</strong>
                                <div class="text"><?= $oneSoList['company_footer'] ?></div>
                              </div>
                            </div>
                          </div>
                          <!-- ################################## -->
                        </div>
                        <div class="tab-pane" id="otherDetails<?= $oneSoList['so_invoice_id'] ?>">
                          <div class="card p-5">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi ipsum ex soluta natus consequuntur voluptatem sed voluptate eum nulla. Molestias harum maxime ipsa? Error, ullam fugit possimus qui autem deleniti expedita ducimus cupiditate libero cumque, hic reiciendis sed amet quidem vero aperiam explicabo, molestiae debitis animi! Id repudiandae a perspiciatis fugiat nisi dolore neque praesentium, quidem necessitatibus totam in explicabo, autem, nulla eum. Culpa, magni!
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!--/.Content-->
                </div>
              </div>
              <!-- right modal end here  -->
              <!-- mobile view area -->

              <?php
              $mailStatus = '';
              if ($oneSoList['mailStatus'] == 1) {
                $mailStatus = "SENT";
              } elseif ($oneSoList['mailStatus'] == 2) {
                $mailStatus = "VIEW";
              }
              $invDate = date_create($oneSoList['invoice_date']);
              $invoiceDate = date_format($invDate, "F d,Y");
              $poDate = date_create($oneSoList['po_date']);
              $echoPoDate = date_format($poDate, "F d,Y");

              $mobileView .= '<div class="row mb-2 mt-2">
            <div class="col col-3">
              <div class="row mb-0">
                <div class="col col-12 icon-image sm-icon">
                  ' . $customerPicture . '
                </div>
              </div>
              <div class="row mb-0">
                <div class="col col-12 text-center text-xs sm-customer">
                  ' . $oneSoList['customer_name'] . '
                </div>
              </div>
            </div>

            <div class="col-5">
              <div class="row mb-0">
                <div class="col col-12 text-xs sm-inv-num">
                ' . $oneSoList['invoice_no'] . '
                  <p class="item-count mt-1 text-xs">[' . $oneSoList['totalItems'] . ' item/s]</p>
                </div>

                <div class="col col-12 text-lg sm-total-amnt">
                  <span class="rupee-symbol"> ₹ </span>
                  ' . $oneSoList['all_total_amt'] . '
                </div>
                <div class="col col-12 text-xs">
                  <p class="' . $dueInDaysClass . ' text-xs w-100 text-center">' . $oneInvDueDays . '</p>
                </div>
              </div>
            </div>
            <div class="col-3">
              <div class="row mb-0">
                <div class="col col-12">
                  <div class="status-custom text-xs w-75 text-secondary">' . $mailStatus . '
                    <div class="round">
                      <ion-icon name="checkmark-sharp"></ion-icon>
                    </div>
                  </div>
                  <p class="status-date">12 Dec, 22</p>
                </div>
                <!--
                <div class="col col-12">
                  <div class="status-custom text-xs w-100 text-primary">viewed
                    <div class="round">
                      <ion-icon name="checkmark-done-sharp"></ion-icon>
                    </div>
                  </div>
                  <p class="status-date">12 Dec, 22</p>
                </div>
                -->
              </div>
            </div>
            <div class="col-1">
              <div class="dropdown">
                <a class="dropdown-toggle text-lg" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                  &#xFE19;
                </a>

                <ul class="dropdown-menu border-0 w-50 text-center" aria-labelledby="dropdownMenuLink">
                  <li><a class="text-sm" style="cursor:pointer; text-decoration: none;" data-toggle="modal" data-target="#fluidModalRightSuccessDemo2_' . $oneSoList['so_invoice_id'] . '">View</a></li>
                </ul>
              </div>
            </div>
          </div>
          <hr class="m-3">
          
          
          <!-- right modal start here  -->
          <div class="modal fade right" id="fluidModalRightSuccessDemo2_' . $oneSoList['so_invoice_id'] . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                <div style="max-width: 70%; min-width:50%" class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                  <!--Content-->
                  <div class="modal-content">
                    <!--Header-->
                    <div class="modal-header " style="background: none; border:none; color:#424242">
                      <p class="heading lead">' . $oneSoList['invoice_no'] . '</p>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="white-text">×</span>
                      </button>
                    </div>
                    <!--Body-->
                    <div class="modal-body" style="padding: 0;">
                      <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link active" href="#preview' . $oneSoList['so_invoice_id'] . '" data-bs-toggle="tab">Preview</a></li>
                        <li class="nav-item"><a class="nav-link" href="#otherDetails' . $oneSoList['so_invoice_id'] . '" data-bs-toggle="tab">Other Details</a></li>
                      </ul>
                      <div class="tab-content">
                        <div class="col-md-12">
                          <div class="shadow-sm bg-light py-2 mx-2 my-2" id="action-navbar" style="text-align:right">
                            <form action="" method="POST">
                              <!-- <a href="branch-so-invoice-2.php?invoice-no=' . base64_encode($oneSoList['so_invoice_id']) . '" name="vendorEditBtn">
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
                        <div class="tab-pane show active" id="preview' . $oneSoList['so_invoice_id'] . '">


                          <!-- ################################## -->
                          <div class="container my-3">
                            <div class="row p-0 m-0 pb-2" style="border-bottom: 3px solid #0090ff;">
                              <div class="col-6 d-flex align-items-center">
                                <img width="220" src="../../public/storage/logo/' . $oneSoList['company_logo'] . '" alt="">
                              </div>
                              <div class="col-6 d-flex align-items-end flex-column">
                                <div>Original for Recipient</div>
                                <div>
                                  <strong class="textColor">' . $oneSoList['invoice_no'] . '</strong>
                                </div>
                                <div>
                                  <b>Date </b>
                                  <span>' . $invoiceDate . '</span> </span>
                                </div>
                                <div>
                                  <b>Due Date </b>
                                  <span>' . $oneSoList['credit_period'] . '</span> </span>
                                </div>
                                <div>
                                  <b>P.O. Number </b>
                                  <span>' . $oneSoList['po_number'] . '</span> </span>
                                </div>
                                <div>
                                  <b>P.O. Date </b>
                                  <span>' . $echoPoDate . '</span> </span>
                                </div>
                              </div>
                            </div>
                            <div class="row p-0 m-0 py-3" style="border-bottom: 3px solid #0090ff;">
                              <div class="col-6">
                                <!-- <div>
                                            <strong class="ml-1 textColor">Sorina TEST 123</strong>
                                          </div> -->
                                <div>
                                  <i class="textColor fa fa-briefcase"></i>
                                  <span>' . $oneSoList['company_gstin'] . '</span>
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
                                  <span>
                                    ' . $oneSoList['company_address'] . '
                                  </span>
                                </div>
                              </div>
                              <!-- <div class="col-4 d-flex align-items-end flex-column">
                                        </div> -->
                              <div class="col-6 d-flex align-items-end flex-column">
                                <div>
                                  <strong class="ml-1 textColor">' . $oneSoList['customer_name'] . '</strong>
                                </div>
                                <div>
                                  <strong class="ml-1 textColor">' . $oneSoList['customer_gstin'] . '</strong>
                                </div>
                                <div>
                                  <i class="textColor fa fa-phone"></i>
                                  <span>' . $oneSoList['customer_phone'] . '</span>
                                </div>
                                <div>
                                  <i class="textColor fa fa-envelope"></i>
                                  <span>' . $oneSoList['customer_email'] . '</span>
                                </div>
                                <div>
                                  <i class="textColor fa fa-info"></i>
                                  <span>' . $oneSoList['customer_address'] . '</span>
                                </div>
                              </div>

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
                                          <strong>' . $item['itemName'] . '</strong>
                                          <div><small>' . $item['itemDesc'] . '</small></div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-6">
                                      <div class="row">
                                        <div class="col-3 font-weight-bold bg-light">' . $item['hsnCode'] . '</div>
                                        <div class="col-3">' . $item['qty'] . '/' . $item['uom'] . '</div>
                                        <div class="col-3 font-weight-bold bg-light">' . $item['unitPrice'] . '</div>
                                        <div class="col-3 text-right">' . $item['totalPrice'] . '</div>
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
                                <img width="160" src="../../public/storage/' . $oneSoList['company_signature'] . '" alt="">
                              </div>
                              <div class="col-2 d-flex align-items-end flex-column textColor">
                                <div>SUB TOTAL</div>
                                <div>TOTAL TAX</div>
                                <div>TOTAL DISCOUNT</div>
                                <div>TOTAL AMOUNT</div>
                              </div>
                              <div class="col-2 d-flex align-items-end flex-column textColor">
                                <div class="">' . $oneSoList['sub_total_amt'] . '</div>
                                <div class="">' . $oneSoList['total_tax_amt'] . '</div>
                                <div class="">' . $oneSoList['totalDiscount'] . '</div>
                                <div class="">' . $oneSoList['all_total_amt'] . '</div>
                              </div>
                              <div class="col-12">
                                <strong class="textColor">NOTE:</strong>
                                <div class="text">' . $oneSoList['company_footer'] . '</div>
                              </div>
                            </div>
                          </div>
                          <!-- ################################## -->
                        </div>
                        <div class="tab-pane" id="otherDetails' . $oneSoList['so_invoice_id'] . '">
                          <div class="card p-5">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi ipsum ex soluta natus consequuntur voluptatem sed voluptate eum nulla. Molestias harum maxime ipsa? Error, ullam fugit possimus qa perspiciatis fugiat nisi dolore neque praesentium, quidem necessitatibus totam in explicabo, autem, nulla eum. Culpa, magni!
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!--/.Content-->
                </div>
              </div>
              <!-- right modal end here  -->
          ';
              ?>

              <!-- manage internal modal end🎈🎈🎈🎈🎈🎈🎈 -->
            <?php
            }
            ?>
          </div>
        </div>

        <div class="card mobile-view-list">
          <div class="card-body">
            <?php echo $mobileView ?>
          </div>
        </div>
        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <!-- <div class="p-0 pt-1 my-2">
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage SO Invoices</h3>
                </li>
              </ul>
            </div> -->
            <?php
            $soList = $BranchSoObj->fetchBranchSoInvoice()['data'];
            // console($soList);
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
                          // console($oneSoList);
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
                              <td class="text-success font-weight-bold text-capitalize"><?= $oneSoList['invoiceStatus'] ?></td>
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
                          // console($invoiceItemDetails);
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
                                            <img width="220" src="../../public/storage/logo/<?= $oneSoList['company_logo'] ?>" alt="">
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
                                            <!-- <div>
                                            <strong class="ml-1 textColor">Sorina TEST 123</strong>
                                          </div> -->
                                            <div>
                                              <i class="textColor fa fa-briefcase"></i>
                                              <span><?= $oneSoList['company_gstin'] ?></span>
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
                                              <span>
                                                <?= $oneSoList['company_address'] ?>
                                              </span>
                                            </div>
                                          </div>
                                          <!-- <div class="col-4 d-flex align-items-end flex-column">
                                        </div> -->
                                          <div class="col-6 d-flex align-items-end flex-column">
                                            <div>
                                              <strong class="ml-1 textColor"><?= $oneSoList['customer_name'] ?></strong>
                                            </div>
                                            <div>
                                              <strong class="ml-1 textColor"><?= $oneSoList['customer_gstin'] ?></strong>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-phone"></i>
                                              <span><?= $oneSoList['customer_phone'] ?></span>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-envelope"></i>
                                              <span><?= $oneSoList['customer_email'] ?></span>
                                            </div>
                                            <div>
                                              <i class="textColor fa fa-info"></i>
                                              <span><?= $oneSoList['customer_address'] ?></span>
                                            </div>
                                          </div>

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
                                                    <div class="col-3 text-right"><?= $item['totalPrice'] ?></div>
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
                                            <div>TOTAL TAX</div>
                                            <div>TOTAL DISCOUNT</div>
                                            <div>TOTAL AMOUNT</div>
                                          </div>
                                          <div class="col-2 d-flex align-items-end flex-column textColor">
                                            <div class=""><?= $oneSoList['sub_total_amt'] ?></div>
                                            <div class=""><?= $oneSoList['total_tax_amt'] ?></div>
                                            <div class=""><?= $oneSoList['totalDiscount'] ?></div>
                                            <div class=""><?= $oneSoList['all_total_amt'] ?></div>
                                          </div>
                                          <div class="col-12">
                                            <strong class="textColor">NOTE:</strong>
                                            <div class="text"><?= $oneSoList['company_footer'] ?></div>
                                          </div>
                                        </div>
                                      </div>
                                      <!-- ################################## -->
                                    </div>
                                    <div class="tab-pane" id="otherDetails<?= $oneSoList['so_invoice_id'] ?>">
                                      <div class="card p-5">
                                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Sequi ipsum ex soluta natus consequuntur voluptatem sed voluptate eum nulla. Molestias harum maxime ipsa? Error, ullam fugit possimus qui at excepturi reprehenderit culanimi! Id repudiandae a perspiciatis fugiat nisi dolore neque praesentium, quidem necessitatibus totam in explicabo, autem, nulla eum. Culpa, magni!
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
    })

  })
</script>
<script>
  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('show');
    $('#overlay').toggleClass('show');
    $('.nav-action').toggleClass('show');
  });
</script>