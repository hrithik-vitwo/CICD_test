<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");


$check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];

$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];

// console($_SESSION);

// fetch company details
$companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
$companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
$branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
$companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
$locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];



if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();

if (isset($_POST['addNewPgiFormSubmitBtn'])) {
  //console($_POST);
  // exit;
  $addBranchSoDeliveryPgi = $BranchSoObj->insertBranchPgi($_POST);
  // console('$addBranchSoDeliveryPgi 📗📗📗📗');
  // console($addBranchSoDeliveryPgi);
  if ($addBranchSoDeliveryPgi['status'] == "success") {
    swalAlert($addBranchSoDeliveryPgi["status"], $addBranchSoDeliveryPgi['pgiNo'], $addBranchSoDeliveryPgi["message"], $_SERVER['PHP_SELF']);
  } else {
    // console($addBranchSoDeliveryPgi);
    swalAlert($addBranchSoDeliveryPgi["status"], 'Warning', $addBranchSoDeliveryPgi["message"]);
  }
}

// console($singleSoDetails);
?>
<style>
  .pgi-modal .modal-header {
    height: 310px !important;
  }

  .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
    font-size: 12px;
  }

  .pgi-creation .card-body {
    min-height: 100%;
    height: 110px;
  }

  .pgi-creation .card-body label {
    z-index: 99 !important;
  }

  .content-wrapper .card .card-body {
    overflow: auto;
  }

  .printable-view .h3-title {
    visibility: hidden;
  }

  .rupee-symbol {
    font-size: 30px !important;
  }

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
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<?php
if (isset($_GET['create-pgi'])) {
  $getId = base64_decode($_GET['create-pgi']);
  $singleSoDetails = $BranchSoObj->fetchBranchSoDeliveryById($getId)['data'][0];
  // console($singleSoDetails);
  $funcObj = $BranchSoObj->fetchFunctionalityById($singleSoDetails['profit_center']);
  $funcList = $funcObj['data'];
?>
  <div class="content-wrapper is-pgi">
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
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>PGI List</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
              Create PGI</a></li>
          <li class="back-button">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
              <i class="fa fa-reply po-list-icon"></i>
            </a>
          </li>
        </ol>

        <form action="" method="POST" id="addNewSOForm">
          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="card pgi-creation">
                <div class="card-header p-3">
                  <div class="head">
                    <i class="fa fa-user"></i>
                    <h4>
                      Customer info
                    </h4>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row" id="customerInfo">
                    <input type="hidden" name="soNumber" value="<?= $singleSoDetails['so_number'] ?>">
                    <input type="hidden" name="deliveryNo" value="<?= $singleSoDetails['delivery_no'] ?>">
                    <input type="hidden" name="deliveryId" value="<?= $singleSoDetails['so_delivery_id'] ?>">
                    <input type="hidden" name="customer_billing_address" value="<?= $singleSoDetails['customer_billing_address'] ?>">
                    <input type="hidden" name="customer_shipping_address" value="<?= $singleSoDetails['customer_shipping_address'] ?>">
                    <?php
                    // console($BranchSoObj->fetchCustomerDetails($singleSoDetails['customer_id'])['data'][0]);
                    $customerDetails = $BranchSoObj->fetchCustomerDetails($singleSoDetails['customer_id'])['data'][0];
                    ?>
                    <input type="hidden" name="customerId" value="<?= $singleSoDetails['customer_id'] ?>">

                    <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                      <div class="form-inline">
                        <label for="" class="text-xs font-bold">Customer Code:&nbsp;</label>
                        <p class="text-xs mb-0"><?= $customerDetails['customer_code'] ?></p>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                      <div class="form-inline">
                        <label for="" class="text-xs font-bold">Name:&nbsp;</label>
                        <p class="text-xs mb-0"><?= $customerDetails['trade_name'] ?></p>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                      <div class="form-inline">
                        <label for="" class="text-xs font-bold">GSTIN:&nbsp;</label>
                        <p class="text-xs mb-0"><?= $customerDetails['customer_gstin'] ?></p>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                      <div class="form-inline">
                        <label for="" class="text-xs font-bold">Status:&nbsp;</label>
                        <p class="status text-xs mb-0"><?= $customerDetails['customer_status'] ?></p>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="card pgi-creation">
                <div class="card-header p-3">
                  <div class="head">
                    <i class="fa fa-user"></i>
                    <h4>
                      Others info
                    </h4>
                  </div>
                </div>
                <div class="card-body ">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input">
                        <label for="">PGI Posting Date</label>
                        <input type="date" name="pgiDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>" value="<?php echo $singleSoDetails['delivery_date'] ?>" id="pgiDate" required>
                        <span class="pgiDateMsg"></span>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input">
                        <label for="">Profile Center</label>
                        <input type="text" name="profitCenter" placeholder="Profit Center" class="form-control" value="<?= $funcList['functionalities_name'] ?>" readonly>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input">
                        <label for="">Customer PO Number</label>
                        <input type="text" name="customerPO" placeholder="customer po number" class="form-control" value="<?= $singleSoDetails['customer_po_no'] ?>" readonly>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 col-mf-12 col-sm-12">
              <div class="card">
                <div class="card-body">
                  <div class="head justify-content-start mb-3 mt-3">
                    <i class="fa fa-shopping-cart po-list-icon float-left"></i>
                    <h6 class="mb-0">
                      Items Info
                    </h6>
                  </div>

                  <table class="table-sales-order">
                    <thead>
                      <th>Line No.</th>
                      <th>Item Code</th>
                      <th>Item Name</th>
                      <th>Stock</th>
                      <th>Schedule Date</th>
                      <th>Qty</th>
                      <th>Remove</th>
                    </thead>
                    <tbody id="itemsTable">
                      <?php
                      // echo $singleSoDetails['so_delivery_id'];
                      // console($BranchSoObj->fetchBranchSoDeliveryItems($singleSoDetails['so_delivery_id'])['data']);
                      $itemDetails = $BranchSoObj->fetchBranchSoDeliveryItems($singleSoDetails['so_delivery_id'])['data'];
                      $randCode = rand(000000, 999999) ?? 0;
                      foreach ($itemDetails as $key => $item) {
                        // console($item);
                        $qtyObj = $BranchSoObj->itemQtyStockCheck($item['inventory_item_id'], "'fgWhReserve'", "DESC", "", $min);
                        $sumOfBatches = $qtyObj['sumOfBatches'];
                      ?>
                        <tr class="rowDel itemRow" id="delItemRowBtn_<?= $item['inventory_item_id'] ?>_<?= $key ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][so_delivery_item_id]" value="<?= $item['so_delivery_item_id'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][itemDeliveryId]" value="<?= $item['so_delivery_id'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][inventoryItemId]" value="<?= $item['inventory_item_id'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][unitPrice]" value="<?= $item['unitPrice'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][deliveryStatus]" value="<?= $item['deliveryStatus'] ?>">

                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][itemDesc]" value="<?= $item['itemDesc'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][hsnCode]" value="<?= $item['hsnCode'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][tax]" value="<?= $item['tax'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][totalTax]" value="<?= $item['totalTax'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][tolerance]" value="<?= $item['tolerance'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][totalDiscount]" value="<?= $item['totalDiscount'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][itemTotalDiscount]" value="<?= $item['totalDiscountAmt'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][unitPrice]" value="<?= $item['unitPrice'] ?>">
                          <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][itemTotalPrice]" value="<?= $item['totalPrice'] ?>">

                          <td>
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][lineNo]" value="<?= $item['lineNo'] ?>">
                            <?= $item['lineNo'] ?>
                          </td>
                          <td>
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][itemCode]" value="<?= $item['itemCode'] ?>">
                            <?= $item['itemCode'] ?>
                          </td>
                          <td>
                            <input class="form-control" type="hidden" name="listItem[<?= $key ?>][itemName]" value="<?= $item['itemName'] ?>">
                            <?= $item['itemName'] ?>
                          </td>
                          <td>
                            <input class="form-control sumOfBatches_<?= $key ?>" type="hidden" name="listItem[<?= $key ?>][batchNo]" value="<?= $sumOfBatches ?>">
                            <span class="pgiStockCount" id="pgiStockCount_<?= $key ?>"><?= $sumOfBatches ?></span>
                          </td>
                          <td>
                            <?php
                            $schedule = $BranchSoObj->fetchBranchSoItemsDeliveryScheduleById($item['delivery_date'])['data'][0];
                            // console($schedule);
                            ?>
                            <span><?= $schedule['delivery_date'] ?></span>
                            <input type="hidden" name="listItem[<?= $key ?>][deliveryDate]" value="<?= $item['delivery_date'] ?>">
                            <input type="hidden" name="listItem[<?= $key ?>][itemQty]" value="<?= $item['qty'] ?>">
                            <small>(Qty-<?= $schedule['qty'] ?>)</small>
                            <!-- <div>
                          <select name="listItem[<?= $key ?>][deliveryDate2]" class="form-control text-center deliveryScheduleQty" id="deliveryScheduleQty_<?= $key ?>">
                            <option value="">Date ></option>
                            <?php
                            $deliverySchedule = $BranchSoObj->fetchBranchSoItemsDeliverySchedule($item['so_delivery_item_id'])['data'];
                            // console($deliverySchedule);
                            foreach ($deliverySchedule as $dSchedule) {
                            ?>
                              <option value="<?= $dSchedule['so_delivery_id'] ?>" data-quantity="<?= $dSchedule['qty'] ?>" data-deliveryDate="<?= $dSchedule['delivery_date'] ?>"><?= $dSchedule['delivery_date'] ?> / (<span class="span"><?= $dSchedule['qty'] ?></span> <?= $item['uom'] ?>)</option>
                            <?php } ?>
                          </select>
                        </div> -->
                            <!-- <small>
                          Total
                          (<?= $item['qty'] ?>
                          <?= $item['uom'] ?>)
                        </small> -->
                          </td>
                          <td>
                            <input step="any" type="number" name="listItem[<?= $key ?>][enterQty]" value="<?= $item['qty'] ?>" class="form-control full-width itemQty" id="itemQty_<?= $key ?>" readonly>
                            <input type="hidden" name="listItem[<?= $key ?>][uom]" value="<?= $item['uom'] ?>">
                            <?php echo getUomDetail($item['uom'])['data']['uomName']; ?>
                          </td>
                          <td class="action-flex-btn">
                            <!-- <i style="cursor: pointer; color: red; margin-right: 10px !important; border-color: red;
                        width: 17px;
                        height: 17px;
                        border-radius: 50%;
                        border: 1.5px solid red;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;" id="delItemBtn_<?= $item['so_delivery_item_id'] ?>" class="delItemBtn mx-1 fa fa-minus"></i> -->
                            <a class="btn btn-danger delItemBtn btn-xs">
                              <i class="fa fa-minus" id="delItemBtn_<?= $item['so_delivery_item_id'] ?>"></i>
                            </a>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <button id="pgibtn" type="submit" name="addNewPgiFormSubmitBtn" class="btn btn-primary mt-3 mb-2 float-right" onclick="return confirm('Are you sure to submit?')">Submit</button>
            </div>
          </div>

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
                    <button type="submit" class="btn btn-primary btnstyle" onclick="return confirm('Are you sure to submit?')">Submit</button>
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
<?php } else { ?>
  <div class="content-wrapper is-pgi">
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
                  <h3 class="card-title">Manage SO PGI</h3>
                  <!-- <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create-sales-order-delivery" class="btn btn-sm btn-primary btnstyle m-2" style="line-height: 32px;"><i class="fa fa-plus"></i> Add New</a> -->
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
              } ?>
              <div class="card card-tabs" style="border-radius: 20px;">
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
                            <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
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
                      $cond .= " AND `pgi_no` like '%" . $_REQUEST['keyword2'] . "%' OR `pgiDate` like '%" . $_REQUEST['keyword2'] . "%'";
                    } else {
                      if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                        $cond .= " AND `pgi_no` like '%" . $_REQUEST['keyword'] . "%'  OR `pgiDate` like '%" . $_REQUEST['keyword'] . "%'";
                      }
                    }

                    $sql_list = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` WHERE 1 " . $cond . " AND company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' " . $sts . " ORDER BY so_delivery_pgi_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                    $qry_list = mysqli_query($dbCon, $sql_list);
                    $num_list = mysqli_num_rows($qry_list);


                    $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY_PGI . "` WHERE 1 " . $cond . " AND company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' " . $sts . " ";
                    $countQry = mysqli_query($dbCon, $countShow);
                    $rowCount = mysqli_fetch_array($countQry);
                    $count = $rowCount[0];
                    $cnt = $GLOBALS['start'] + 1;
                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_SALES_ORDER_DELIVERY_PGI", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                    $settingsCheckbox = unserialize($settingsCh);
                    if ($num_list > 0) {
                    ?>
                      <table class="table defaultDataTable table-hover">
                        <thead>
                          <tr class="alert-light">
                            <th>#</th>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <th>PGI No.</th>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <th>Customer PO</th>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <th>Delivery Date</th>
                            <?php  }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <th>Customer Name</th>
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
                          // $soList = $BranchSoObj->fetchBranchSoDeliveryPgiListing()['data'];
                          foreach ($qry_list as $oneSoList) {
                            // console($oneSoList);
                            $soDetails = queryGet("SELECT conversion_rate, currency_name FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE so_number='" . $oneSoList['so_number'] . "'")['data'];

                            $conversion_rate = $soDetails['conversion_rate'];
                            $currency_name = $soDetails['currency_name'];
                          ?>
                            <tr>
                              <td><?= $cnt++ ?></td>
                              <?php if (in_array(1, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['pgi_no'] ?></td>
                              <?php }
                              if (in_array(2, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['customer_po_no'] ?></td>
                              <?php }
                              if (in_array(3, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['pgiDate'] ?></td>
                              <?php }
                              if (in_array(4, $settingsCheckbox)) {
                              ?>
                                <td><?= $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0]['trade_name'] ?></td>
                              <?php }
                              if (in_array(5, $settingsCheckbox)) { ?>
                                <td>
                                  <div class="status listStatus"><?= $oneSoList['pgiStatus'] ?></div>
                                </td>
                              <?php }
                              if (in_array(6, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['totalItems'] ?></td>
                              <?php } ?>
                              <td>
                                <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneSoList['so_delivery_pgi_id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                <!-- <a href="branch-so-invoice.php?pgi-invoice=<?= base64_encode($oneSoList['so_delivery_pgi_id']) ?>" style="cursor: pointer;" class="btn btn-sm"><i class="fa fa-download"></i></a> -->
                                <?php if ($oneSoList['status'] == 'active') { ?>
                                  <a style="cursor:pointer" data-id="<?= $oneSoList['so_delivery_pgi_id']; ?>" class="btn btn-sm reversePGI" title="Reverse Now">
                                    <i class="far fa-undo po-list-icon"></i>
                                  </a>
                                <?php } ?>
                                <!-- right modal start here  -->
                                <div class="modal fade right pgi-modal customer-modal classic-view-modal" id="fluidModalRightSuccessDemo_<?= $oneSoList['so_delivery_pgi_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                  <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                    <!--Content-->
                                    <div class="modal-content">
                                      <!--Header-->
                                      <div class="modal-header">
                                        <div class="customer-head-info">
                                          <div class="customer-name-code">
                                            <h2 class="d-flex gap-2"><span class="rupee-symbol">&#x20B9;</span><?= number_format($oneSoList['totalAmount'], 2) ?></h2>
                                            <p class="heading lead"><?= $oneSoList['pgi_no'] ?></p>
                                            <p>Cust PO/REF :&nbsp;<?= $oneSoList['customer_po_no'] ?></p>
                                          </div>
                                          <?php
                                          $customerDetails = $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0];
                                          ?>
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
                                        <!-- <div class="d-flex justify-content-between">
                                          <h2 class="text-white mt-2 mb-2 d-flex gap-2"><span class="rupee-symbol">₹</span><?= number_format($oneSoList['totalAmount'], 2) ?></h2>
                                          <p class="heading lead text-right mt-2 mb-2"><?= $oneSoList['pgi_no'] ?></p>
                                        </div>
                                        <p class="text-sm text-right mb-2"><?= $oneSoList['so_number'] ?></p>
                                        <p class="text-sm text-right mb-2"><?= $oneSoList['customer_po_no'] ?></p>
                                        <p class="text-sm text-right mb-2">Delivery Date: <?= $oneSoList['pgiDate'] ?></p> -->
                                        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" class="white-text">×</span>
                                  </button> -->
                                        <div class="display-flex-space-between mt-2 mb-3">

                                          <ul class="nav nav-tabs" id="myTab" role="tablist">
                                            <li class="nav-item">
                                              <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= str_replace('/', '-', $oneSoList['pgi_no']) ?>" role="tab" aria-controls="home" aria-selected="true">Info</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link" id="classic-view-tab" data-toggle="tab" href="#classic-view<?= str_replace('/', '-', $oneSoList['pgi_no']) ?>" role="tab" aria-controls="classic-view" aria-selected="false"><ion-icon name="apps-outline" class="mr-2"></ion-icon> Classic View</a>
                                            </li>
                                            <!-- -------------------Audit History Button Start------------------------- -->
                                            <li class="nav-item">
                                              <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $oneSoList['pgi_no']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $oneSoList['pgi_no']) ?>" href="#history<?= str_replace('/', '-', $oneSoList['pgi_no']) ?>" role="tab" aria-controls="history<?= $onePrList['rfqId']  ?>" aria-selected="false"><i class="fa fa-history mr-2"></i>Trail</a>
                                            </li>
                                            <!-- -------------------Audit History Button End------------------------- -->
                                          </ul>

                                          <div class="action-btns display-flex-gap" id="action-navbar">
                                            <form action="" method="POST">

                                              <!-- <a href="#" name="vendorEditBtn">
                                            <i title="Edit" style="font-size: 1.2em" class="fa fa-edit po-list-icon"></i>
                                          </a> -->
                                              <!-- <a href="#">
                                            <i title="Delete" style="font-size: 1.2em" class="fa fa-trash po-list-icon"></i>
                                          </a> -->
                                              <!-- <a href="#">
                                            <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on po-list-icon"></i>
                                          </a> -->
                                            </form>
                                          </div>
                                        </div>

                                      </div>
                                      <!--Body-->
                                      <div class="modal-body">

                                        <div class="tab-content pt-0" id="myTabContent">
                                          <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $oneSoList['pgi_no']) ?>" role="tabpanel" aria-labelledby="home-tab">
                                            <?php if ($oneSoList['invoiceStatus'] == 9) { ?>
                                              <a href="direct-create-invoice.php?pgi_to_invoice=<?= base64_encode($oneSoList['so_delivery_pgi_id']) ?>" name="vendorEditBtn" class="btn btn-primary float-right mb-3">
                                                <i class="fa fa-plus"></i>
                                                Create Invoice
                                              </a>
                                            <?php } elseif ($oneSoList['invoiceStatus'] == 1) { ?>
                                              <a class="btn btn-success float-right mb-3">
                                                <i class="fa fa-check mr-2" style="border-radius: 50%; background: #fff; padding: 5px; color: #198754;"></i>
                                                <span>Invoice Created</span>
                                              </a>
                                            <?php } else { ?>
                                              <a class="btn btn-danger float-right mb-3">
                                                <i class="fa fa-exclamation mr-2" style="border-radius: 50%; background: #fff; padding: 5px; color: #198754;"></i>
                                                <span>Not Found</span>
                                              </a>
                                            <?php } ?>

                                            <!--------Customer Details--------->
                                            <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                              <div class="accordion-item">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                  <button class="accordion-button btn btn-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#customerDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                    Customer Details
                                                  </button>
                                                </h2>
                                                <div id="customerDetails" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                  <div class="accordion-body p-0">
                                                    <?php
                                                    $customerDetails = $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0];
                                                    // console($customerDetails);
                                                    ?>
                                                    <div class="card">
                                                      <div class="card-body p-3">
                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs">Customer Code :</p>
                                                          <p class="font-bold text-xs"><?= $customerDetails['customer_code'] ?></p>
                                                        </div>
                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs">Name :</p>
                                                          <p class="font-bold text-xs"><?= $customerDetails['trade_name'] ?></p>
                                                        </div>
                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs">GST :</p>
                                                          <p class="font-bold text-xs"><?= $customerDetails['customer_gstin'] ?></p>
                                                        </div>
                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs">Status :</p>
                                                          <p class="font-bold text-xs"><?= $customerDetails['customer_status'] ?></p>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>

                                            <!--------Item Details--------->
                                            <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                              <div class="accordion-item">
                                                <h2 class="accordion-header" id="flush-headingOne">
                                                  <button class="accordion-button btn btn-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#itemDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                    Items
                                                  </button>
                                                </h2>
                                                <div id="itemDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                  <div class="accordion-body p-0">
                                                    <?php
                                                    $itemDetails = $BranchSoObj->fetchBranchSoDeliveryItemsPgi($oneSoList['so_delivery_pgi_id'])['data'];
                                                    // console($itemDetails);
                                                    foreach ($itemDetails as $onePgiItem) {
                                                      $unitPrice = $onePgiItem['unitPrice'] * $conversion_rate;
                                                      $totalDiscount = $onePgiItem['totalDiscount'] * $conversion_rate;
                                                    ?>
                                                      <div class="card">
                                                        <div class="card-body p-3">
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Item Code :</p>
                                                            <p class="font-bold text-xs"> <?= $onePgiItem['itemCode'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Name :</p>
                                                            <p class="font-bold text-xs"><?= $onePgiItem['itemName'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">QTY :</p>
                                                            <p class="font-bold text-xs"><?= $onePgiItem['qty'] ?></p>
                                                          </div>
                                                          <?php if ($totalDiscount > 0) { ?>
                                                            <div class="display-flex-space-between">
                                                              <p class="font-bold text-xs">Total Discount :</p>
                                                              <p class="font-bold text-xs">% <?= $totalDiscount ?></p>
                                                            </div>
                                                          <?php } ?>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Unit Price :</p>
                                                            <p class="font-bold text-xs"><span class="rupee-symbol">₹</span><?= $unitPrice ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Delivery Date :</p>
                                                            <p class="font-bold text-xs"><?= $onePgiItem['delivery_date'] ?></p>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    <?php } ?>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>

                                            <!-- <div class="row px-3 p-0 m-0 mb-2" style="place-items: self-start;">
                                          <div class="col-md-12">
                                            <div class="shadow-sm py-2 px-2" style="background: #dfdfdf; margin-bottom: 15px;">
                                              basic Details
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row border mx-2 mt-n2 py-2 shadow-sm bg-light">
                                              <div class="col-md-6">
                                                <span class="text-secondary"><strong>SO Number:</strong> <?= $oneSoList['so_number'] ?></span>
                                              </div>
                                              <div class="col-md-6">
                                                <span class="text-secondary"><strong>Customer PO:</strong> <?= $oneSoList['customer_po_no'] ?></span>
                                              </div>
                                              <div class="col-md-6">
                                                <span class="text-secondary"><strong>Delivery Date :</strong> <?= $oneSoList['pgiDate'] ?></span>
                                              </div>
                                              <div class="col-md-6">
                                                <span class="text-secondary"><strong>Total Amount :</strong> <?= $oneSoList['totalAmount'] ?></span>
                                              </div>
                                              <div class="col-md-6">
                                                <span class="text-secondary"><strong>Total Items :</strong> <?= $oneSoList['totalItems'] ?></span>
                                              </div>
                                            </div>
                                          </div>
                                        </div> -->
                                            <!-- <div class="row px-3 p-0 m-0 mb-2" style="place-items: self-start;">
                                          <div class="col-md-12">
                                            <div class="shadow-sm py-2 px-2" style="background: #dfdfdf; margin-bottom: 15px;">
                                              Customer Details
                                            </div>
                                          </div>
                                          <?php
                                          $customerDetails = $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0];
                                          // console($customerDetails);
                                          ?>
                                          <div class="col-md-12">
                                            <div class="row border mx-2 mt-n2 py-2 shadow-sm bg-light">
                                              <div class="col-md-6">
                                                <span class="text-secondary"><strong>Customer Code:</strong> <?= $customerDetails['customer_code'] ?></span>
                                              </div>
                                              <div class="col-md-6">
                                                <span class="text-secondary"><strong>Customer Name:</strong> <?= $customerDetails['trade_name'] ?></span>
                                              </div>
                                              <div class="col-md-6">
                                                <span class="text-secondary"><strong>Customer GST :</strong> <?= $customerDetails['customer_gstin'] ?></span>
                                              </div>
                                              <div class="col-md-6">
                                                <span class="text-secondary"><strong>Status :</strong> <?= $customerDetails['customer_status'] ?></span>
                                              </div>
                                            </div>
                                          </div>
                                        </div> -->
                                            <!-- <div class="row px-3 p-0 m-0 mb-2" style="place-items: self-start;">
                                          <div class="col-md-12">
                                            <div class="shadow-sm py-2 px-2" style="background: #dfdfdf; margin-bottom: 15px;">
                                              Items <?= $oneSoList['so_delivery_pgi_id'] ?>
                                            </div>
                                          </div>
                                          <?php
                                          $itemDetails = $BranchSoObj->fetchBranchSoDeliveryItemsPgi($oneSoList['so_delivery_pgi_id'])['data'];
                                          // console($itemDetails);
                                          foreach ($itemDetails as $onePgiItem) {
                                          ?>
                                            <div class="col-md-12 my-2">
                                              <div class="row border mx-2 mt-n2 py-2 shadow-sm bg-light">
                                                <div class="col-md-12" style="background: #dfdfdf">
                                                  <span class="text-secondary"><strong>Item Details - </strong></span>
                                                </div>
                                                <div class="col-md-6">
                                                  <span class="text-secondary"><strong>Item Code:</strong> <?= $onePgiItem['itemCode'] ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                  <span class="text-secondary"><strong>Item Name:</strong> <?= $onePgiItem['itemName'] ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                  <span class="text-secondary"><strong>Tolerance :</strong> <?= $onePgiItem['tolerance'] ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                  <span class="text-secondary"><strong>Total Discount :</strong> <?= $onePgiItem['totalDiscount'] ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                  <span class="text-secondary"><strong>Unit Price :</strong> <?= $onePgiItem['unitPrice'] ?></span>
                                                </div>
                                                <div class="col-md-6">
                                                  <span class="text-secondary"><strong>Delivery Date :</strong> <?= $onePgiItem['delivery_date'] ?></span>
                                                </div>
                                              </div>
                                            </div>
                                          <?php } ?>
                                        </div> -->
                                          </div>


                                          <div class="tab-pane fade" id="classic-view<?= str_replace('/', '-', $oneSoList['pgi_no']) ?>" role="tabpanel" aria-labelledby="profile-tab">
                                            <div class="card classic-view bg-transparent">
                                              <div class="card-body classic-view-so-table" style="overflow: auto;">
                                                <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" id="printButton">Print Table</button> -->
                                                <button type="button" class="btn btn-primary classic-view-btn float-right" onclick="window.print(); return false;">Print</button>
                                                <?php

                                                $companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);

                                                //console($companyData);
                                                ?>
                                                <div class="printable-view">
                                                  <h3 class="h3-title text-center font-bold text-sm mb-4">PGI</h3>
                                                  <table class="classic-view table-bordered">
                                                    <tbody>
                                                      <tr>
                                                        <td colspan="5" class="border-right">
                                                          <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                                          <p><?= $companyData['location_building_no'] ?>, <?= $companyData['location_flat_no'] ?></p>
                                                          <p><?= $companyData['location'] ?>, <?= $companyData['location_street_name'] ?>, <?= $companyData['location_pin_code'] ?></p>
                                                          <p><?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?></p>
                                                          <p><?= $companyData['location_state'] ?></p>
                                                          <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                                                          <p>Company’s PAN: <?= $companyData['company_pan'] ?></p>
                                                        </td>
                                                        <td colspan="3">
                                                          <p>PGI Number</p>
                                                          <p class="font-bold"><?= $oneSoList['pgi_no'] ?></p>
                                                        </td>
                                                        <td colspan="3">
                                                          <p>Dated</p>
                                                          <p class="font-bold"><?= $oneSoList['pgiDate'] ?></p>
                                                        </td>
                                                      </tr>
                                                      <tr>
                                                        <td colspan="5" class="border-right">
                                                          <p>Buyer (Bill to)</p>
                                                          <p class="font-bold"><?= $customerDetails['trade_name'] ?></p>
                                                          <p><?= $oneSoList['billingAddress'] ?></p>
                                                          <p>GSTIN/UIN : <?= $customerDetails['customer_gstin'] ?></p>
                                                          <!-- <p>State Name : Maharashtra, Code : 27</p> -->
                                                        </td>
                                                        <td colspan="5" class="border-right">
                                                          <p>Consignee (Ship to)</p>
                                                          <p class="font-bold"><?= $customerDetails['trade_name'] ?></p>
                                                          <p><?= $oneSoList['shippingAddress'] ?></p>
                                                        </td>
                                                      </tr>
                                                      <tr>
                                                        <th>Sl No.</th>
                                                        <th>Particulars</th>
                                                        <th>HSN/SAC </th>
                                                        <th>Quantity</th>
                                                        <th>Rate</th>
                                                        <th>UOM</th>
                                                        <th>Discount</th>
                                                        <th>GST</th>
                                                        <th>Total Amount</th>
                                                      </tr>
                                                      <?php
                                                      $itemDetails = $BranchSoObj->fetchBranchSoDeliveryItemsPgi($oneSoList['so_delivery_pgi_id'])['data'];
                                                      //console($itemDetails);
                                                      foreach ($itemDetails as $onePgiItem) {
                                                        $unitPrice = $onePgiItem['unitPrice'] * $conversion_rate;
                                                        $totalDiscount = $onePgiItem['totalDiscount'] * $conversion_rate;
                                                      ?>


                                                        <tr>
                                                          <td class="text-center"><?= ++$i ?></td>
                                                          <td class="text-center">
                                                            <p class="font-bold"><?= $onePgiItem['itemName'] ?></p>
                                                            <p class="text-italic"><?= $onePgiItem['itemCode'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $onePgiItem['hsnCode'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $onePgiItem['qty'] ?></p>
                                                          </td>
                                                          <td class="text-right">
                                                            <p><?= $onePgiItem['unitPrice'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $onePgiItem['uom'] ?></p>
                                                          </td>
                                                          <td class="text-right">
                                                            <p><?= $onePgiItem['totalDiscountAmt'] ?></p>
                                                            <p class="font-bold text-italic">(<?= $onePgiItem['totalDiscount'] ?>%)</p>
                                                          </td>
                                                          <td class="text-right">
                                                            <p><?= $onePgiItem['totalTax'] ?></p>
                                                            <p class="font-bold text-italic">(<?= $onePgiItem['tax'] ?>%)</p>
                                                          </td>
                                                          <td class="text-right"><?= $onePgiItem['totalPrice'] ?></td>
                                                        </tr>
                                                      <?php } ?>
                                                      <tr>
                                                        <td colspan="10" class="text-right font-bold">
                                                          <?= $oneSoList['totalAmount'] ?>
                                                        </td>
                                                      </tr>
                                                      <tr>
                                                        <td colspan="5">
                                                          <p>Amount Chargeable (in words)</p>
                                                          <p class="font-bold"><?= number_to_words_indian_rupees($oneSoList['totalAmount']); ?> ONLY</p>
                                                        </td>
                                                        <td colspan="5" class="text-right">E. & O.E</td>
                                                      </tr>
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
                                                        <td colspan="5">
                                                          <p>Remarks:</p>
                                                          <p>Created By: <b><?= getCreatedByUser($oneSoList['created_by']) ?></b></p>
                                                        </td>
                                                        <td colspan="5" class="text-right border">
                                                          <p class="text-center font-bold"> for <?= $companyData['company_name'] ?></p>
                                                          <p class="text-center sign-img">
                                                            <img width="60" src="../../public/storage/signature/<?= $companyData['signature'] ?>" alt="signature">
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
                                          <div class="tab-pane fade" id="history<?= str_replace('/', '-', $oneSoList['pgi_no']) ?>" role="tabpanel" aria-labelledby="history-tab">

                                            <div class="audit-head-section mb-3 mt-3 ">
                                              <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneSoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['created_at']) ?></p>
                                              <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneSoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['updated_at']) ?></p>
                                            </div>
                                            <hr>
                                            <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $oneSoList['pgi_no']) ?>">

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
                                  </div>
                                </div>
                              </td>
                            </tr>

                          <?php } ?>

                        </tbody>
                        <tbody>
                          <tr>
                            <td colspan="<?= $settingsCheckboxCount + 7; ?>">
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
                        <input type="hidden" name="pageTableName" value="ERP_BRANCH_SALES_ORDER_DELIVERY_PGI" />
                        <div class="modal-body">
                          <div id="dropdownframe"></div>
                          <div id="main2">
                            <table>
                              <tr>
                                <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                  PGI No.</td>
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

    // start date checker
    function delivery_posting_date_checker() {
      let date = $("#pgiDate").val();
      let max = '<?php echo $max; ?>';
      let min = '<?php echo $min; ?>';

      if (date < min) {
        $(".pgiDateMsg").html(`<p class="text-danger text-xs" id="podatelabel">Invalid date</p>`);
        document.getElementById("deliveryCreationBtn").disabled = true;
      } else if (date > max) {
        $(".pgiDateMsg").html(`<p class="text-danger text-xs" id="podatelabel">Invalid date</p>`);
        document.getElementById("deliveryCreationBtn").disabled = true;
      } else {
        $(".pgiDateMsg").html("");
        document.getElementById("deliveryCreationBtn").disabled = false;
      }
    }
    // $("#pgiDate ").on("keyup", function() {
    //   delivery_posting_date_checker();
    // });

    
    // invoice date *****************************************
    $("#pgiDate").on("change", function(e) {
      // dynamic value
      // delivery_posting_date_checker();
      let url = window.location.search;
      let param = url.split("=")[0];

      var invoicedate = $(this).val();
      var rowData = {};
      let flag = 0;
      $(".itemRow").each(function() {
        let rowId = $(this).attr("id").split("_")[2];
        let itemId = $(this).attr("id").split("_")[1];
        rowData[rowId] = itemId;

        // $.ajax({
        //   type: "GET",
        //   url: `ajaxs/so/ajax-items-stock-list.php`,
        //   data: {
        //     act: "itemStock",
        //     invoiceDate: invoicedate,
        //     itemId: itemId,
        //     randCode: rowId
        //   },
        //   beforeSend: function() {
        //     // $(".tableDataBody").html(`<option value="">Loding...</option>`);
        //   },
        //   success: function(response) {
        //     $(`.customitemreleaseDiv${rowId}`).hide();
        //     $(`.customitemreleaseDiv${rowId}`).html(response);
        //   }
        // });
      });

      StringRowData = JSON.stringify(rowData);
          Swal.fire({
            icon: `warning`,
            title: `Note`,
            text: `Available stock has been recalculated`,
            // showCancelButton: true,
            // confirmButtonColor: '#3085d6',
            // cancelButtonColor: '#d33',
            // confirmButtonText: 'Confirm'
          });


          $.ajax({
            type: "POST",
            url: `ajaxs/so/ajax-items-reserve-stock-check.php`,
            data: {
              act: "itemStockCheck",
              sl: "fgWhReserve",
              invoicedate: invoicedate,
              rowData: StringRowData
            },
            beforeSend: function() {
              // $(".tableDataBody").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
              let data = JSON.parse(response);
              let itemData = data.data;
              console.log(data);
              if (data.status === "success") {
                for (let key in itemData) {
                  
                  if (itemData.hasOwnProperty(key)) {

                    $(`.sumOfBatches_${key}`).val(itemData[key]);
                    $(`#pgiStockCount_${key}`).html(itemData[key]);
                  }
                }
              }
            }
          });
    });
//************************************************************************ */
    // end date checker

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
    $(document).on("click", "a.delItemBtn", function() {
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
      console.log(qtyVal);
      $(`#itemQty_${qtyVal3}`).val(qtyVal);
    });

    // pgi submit button
    $("#pgibtn").on("click", function(event) {
      let isValidQty = true;

      $(".itemQty").each(function() {
        let row = ($(this).attr("id")).split("_")[1];

        let qty = $(`#itemQty_${row}`).val();
        let stock = $(`.sumOfBatches_${row}`).val();
        console.log(qty + '<=' + stock);
        if (stock<qty) {
          isValidQty = false;
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Stock not enough for this item!!'
          })
          return false;
        }
      });

      if (!isValidQty) {
        event.preventDefault(); // prevent the default action (e.g., form submission)
        return;
      }

    })

  });

  $('.reversePGI').click(function(e) {
    e.preventDefault(); // Prevent default click behavior

    var dep_keys = $(this).data('id');
    var $this = $(this); // Store the reference to $(this) for later use

    Swal.fire({
      icon: 'warning',
      title: 'Are you sure?',
      text: 'You want to reverse this?',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Reverse'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: 'POST',
          data: {
            dep_keys: dep_keys,
            dep_slug: 'reversePGI'
          },
          url: 'ajaxs/ajax-reverse-post.php',
          beforeSend: function() {
            $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
          },
          success: function(response) {
            var responseObj = JSON.parse(response);
            console.log(responseObj);

            if (responseObj.status == 'success') {
              $this.parent().parent().find('.listStatus').html('Reverse');
              $this.hide();
            } else {
              $this.html('<i class="far fa-undo po-list-icon"></i>');
            }

            let Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 4000
            });
            Toast.fire({
              icon: responseObj.status,
              title: '&nbsp;' + responseObj.message
            }).then(function() {
              // location.reload();
            });
          }
        });
      }
    });
  });
</script>

<script src="<?= BASE_URL; ?>public/validations/pgiValidation.js"></script>