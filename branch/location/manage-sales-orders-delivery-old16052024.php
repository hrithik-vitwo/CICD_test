<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-items-controller.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");


$check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];

$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();

// fetch company details
$companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
$companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
$branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
$companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
$locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];



if (isset($_POST['addNewSODeliveryFormSubmitBtn'])) {
  // console($_POST);
  $branchSoDeliveryCreationObj = $BranchSoObj->branchSoDeliveryCreate($_POST);
  // console($branchSoDeliveryCreationObj);
  // exit;
  if ($branchSoDeliveryCreationObj['status'] == "success") {
    // console($branchSoDeliveryCreationObj);
    // swalAlert($branchSoDeliveryCreationObj["status"], $branchSoDeliveryCreationObj['deliveryNo'], $branchSoDeliveryCreationObj["message"], $_SERVER['PHP_SELF']);
    swalAlert($branchSoDeliveryCreationObj["status"], 'Success', 'Created Successfully', $_SERVER['PHP_SELF']);
  } else {
    swalAlert($branchSoDeliveryCreationObj["status"], 'Warning', $branchSoDeliveryCreationObj["message"]);
  }
}
?>
<style>
  .display-flex-gap {
    gap: 0 !important;
  }

  .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
    font-size: 12px;
  }

  .customer-head-info {
    align-items: flex-start !important;
  }

  .customer-head-info p {
    width: 158px !important;
  }

  #action-navbar {
    overflow: hidden;
    margin-top: 0 !important;
  }

  .pgi-create-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    max-width: 140px;
    margin-left: auto;
  }

  .so-delivery-modal .modal-header {
    height: 280px;
  }

  .so-delivery-card label {
    z-index: 99 !important;
  }

  .card.pgi-creation .card-body {
    min-height: 100%;
    height: 185px;
  }


  .rupee-symbol {
    font-size: 30px !important;
  }

  .currency-symbol {
    padding: 5px 10px;
    display: flex;
    gap: 6px;
    align-items: center;
    border-radius: 5px;
    width: 50px;
  }

 


  @media (max-width: 575px) {
    .so-delivery-modal .modal-header {
      height: 288px !important;
    }
  }

  .printable-view .h3-title {
    visibility: hidden;
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

<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<?php
if (isset($_GET['create-sales-order-delivery'])) {
  $getSoId = base64_decode($_GET['create-sales-order-delivery']);
  $singleSoDetails = $BranchSoObj->fetchSoDetailsById($getSoId)['data'][0];
?>
  <div class="content-wrapper so-delivery-wrapper">
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
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Order Delivery List</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
              Delivery</a></li>
          <li class="back-button">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
              <i class="fa fa-reply po-list-icon"></i>
            </a>
          </li>
        </ol>
        <form action="" method="POST" id="addNewdeliveryForm" name="addNewdeliveryForm">
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
                <div class="card-body so-delivery-card">
                  <div class="row m-2 delivery-card-first" id="customerInfo">
                    <input type="hidden" name="deliveryNo" value="<?= $singleSoDetails['delivery_no'] ?>">
                    <input type="hidden" name="soNumber" value="<?= $singleSoDetails['so_number'] ?>">
                    <input type="hidden" name="soId" value="<?= $singleSoDetails['so_id'] ?>">
                    <input type="hidden" name="customer_shipping_address" value="<?= $singleSoDetails['shippingAddress'] ?>">
                    <input type="hidden" name="customer_billing_address" value="<?= $singleSoDetails['billingAddress'] ?>">

                    <?php
                    $customerDetails = $BranchSoObj->fetchCustomerDetails($singleSoDetails['customer_id'])['data'][0];

                    //console($customerDetails);
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
                      <div class="form-inline status-delivery">
                        <label for="" class="text-xs font-bold">Status:&nbsp;</label>
                        <p class="font-weight-bold text-success text-xs mb-0"><?= $customerDetails['customer_status'] ?></p>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="card pgi-creation delivery-creation-card">
                <div class="card-header p-3">
                  <div class="head">
                    <i class="fa fa-user"></i>
                    <h4>
                      Other info
                    </h4>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row" style="row-gap: 17px;">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="form-input">
                        <label for="">Delivery Posting Date</label>
                        <input type="date" name="deliveryCreationDate" min="<?= $min ?>" max="<?= $max ?>" value="<?= $min; ?>" class="form-control" id="postingDeliveryDate" required />
                        <span class="postingDeliveryDateMsg"></span>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="form-input">
                        <label for="">Delivery Date</label>
                        <input type="text" name="soDeliveryPostingDate" class="form-control" value="<?= $singleSoDetails['delivery_date'] ?>" readonly />
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6">
                      <div class="form-input">
                        <label>Profit Center</label>
                        <input type="hidden" name="profitCenter" placeholder="Profit Center" class="form-control" value="<?= $singleSoDetails['profit_center'] ?>" readonly>
                        <input type="text" placeholder="Profit Center" class="form-control" value="<?= $BranchSoObj->fetchFunctionalityById($singleSoDetails['profit_center'])['data']['functionalities_name'] ?>" readonly>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6">
                      <div class="form-input">
                        <label for="">SO Number</label>
                        <input type="text" name="so_number" class="form-control" value="<?= $singleSoDetails['so_number'] ?>" readonly />
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6">
                      <div class="form-input">
                        <label for="">Customer PO</label>
                        <input type="text" name="customerPO" placeholder="customer po" class="form-control" value="<?= $singleSoDetails['customer_po_no'] ?>" readonly>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="card" style="overflow-x: auto;">
                <div class="card-body">
                  <div class="head justify-content-start mb-2 mt-3 px-3 pb-2">
                    <i class="fa fa-shopping-cart po-list-icon float-left"></i>
                    <h6 class="mb-0">
                      Items Info
                    </h6>
                  </div>
                  <table class="table table-sales-order mt-0">
                    <thead>
                      <tr>
                        <th rowspan="2">Line No.</th>
                        <th rowspan="2">Item Code</th>
                        <th rowspan="2">Item Name</th>
                        <th rowspan="2">Schedule Date</th>
                        <th>Stock Availabilities</th>
                        <th rowspan="2">Delivery Qty</th>
                        <th rowspan="2">Production/PR Qty</th>
                        <th rowspan="2">Remove</th>
                      </tr>
                    </thead>
                    <tbody id="itemsTable">
                      <?php
                      // console($BranchSoObj->fetchBranchSoDeliveryItems($singleSoDetails['so_id'])['data']);
                      $itemDetails = $BranchSoObj->fetchBranchSoItems($singleSoDetails['so_id'])['data'];
                      $randCode = rand(000000, 999999);
                      foreach ($itemDetails as $key => $item) {
                        $masterItemDetails = $BranchSoObj->fetchItemSummaryDetails($item['inventory_item_id'])['data'][0];
                        // console($masterItemDetails);

                        $baseUnitMeasure = $ItemsObj->getBaseUnitMeasureById($item['uom']);
                        $uomName = $baseUnitMeasure['data']['uomName'];

                        $deliveryScheduleObj = $BranchSoObj->fetchBranchSoItemsDeliverySchedule($item['so_item_id']);
                        $deliverySchedule = $deliveryScheduleObj['data'];

                        if (count($deliverySchedule) > 0) {
                      ?>
                          <tr class="rowDel itemRow" id="delItemRowBtn_<?= $item['inventory_item_id'] ?>_<?= $key ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][itemId]" value="<?= $item['so_item_id'] ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][soId]" value="<?= $item['so_id'] ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][inventoryItemId]" value="<?= $item['inventory_item_id'] ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][itemDesc]" value="<?= $item['itemDesc'] ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][hsnCode]" value="<?= $item['hsnCode'] ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][tax]" value="<?= $item['tax'] ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][totalTax]" value="<?= $item['totalTax'] ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][tolerance]" value="<?= $item['tolerance'] ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][totalDiscount]" value="<?= $item['totalDiscount'] ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][itemTotalDiscount]" value="<?= $item['itemTotalDiscount'] ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][unitPrice]" value="<?= $item['unitPrice'] ?>">
                            <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][itemTotalPrice]" value="<?= $item['totalPrice'] ?>">
                            <td>
                              <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][lineNo]" value="<?= $item['lineNo'] ?>">
                              <?= $item['lineNo'] ?>
                            </td>
                            <td>
                              <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][itemCode]" value="<?= $item['itemCode'] ?>">
                              <input class="form-control full-width" type="hidden" name="listItem[<?= $key ?>][goodsType]" value="<?= $item['goodsType'] ?>">
                              <?= $item['itemCode'] ?>(<?= $item['goodsType'] ?>)
                            </td>
                            <td>
                              <input class="form-control" type="hidden" name="listItem[<?= $key ?>][itemName]" value="<?= $item['itemName'] ?>">
                              <?= $item['itemName'] ?>
                            </td>

                            <td>
                              <div>
                                <input type="hidden" name="listItem[<?= $key ?>][itemTotalQty]" id="itemTotalQty_<?= $key ?>" value="">
                                <select name="listItem[<?= $key ?>][itemDeliveryDateId]" class="form-control text-center deliveryScheduleQty" id="deliveryScheduleQty_<?= $key ?>">
                                  <option value="">Date ></option>
                                  <?php
                                  foreach ($deliverySchedule as $dSchedule) {

                                    if ($dSchedule['remainingQty'] != 0) {
                                      if ($dSchedule['remainingQty'] != "") {
                                  ?>
                                        <option value="<?= $dSchedule['so_delivery_id'] ?>" data-quantity="<?= $dSchedule['remainingQty'] ?>" data-deliveryDate="<?= $dSchedule['delivery_date'] ?>"><?= $dSchedule['delivery_date'] ?> / (<span class="span"><?= $dSchedule['remainingQty'] ?></span> <?= $uomName ?>)</option>
                                      <?php } else { ?>
                                        <option value="<?= $dSchedule['so_delivery_id'] ?>" data-quantity="<?= $dSchedule['qty'] ?>" data-deliveryDate="<?= $dSchedule['delivery_date'] ?>"><?= $dSchedule['delivery_date'] ?> / (<span class="span"><?= $dSchedule['qty'] ?></span> <?= $uomName ?>)</option>
                                  <?php
                                      }
                                    }
                                  }
                                  ?>
                                </select>
                              </div>
                              <small class="float-right">
                                Total
                                (<?= $item['qty'] ?>
                                <?= $uomName ?>)
                              </small>
                            </td>
                            <td>
                              <?php
                              // echo  $min;
                              // echo $asondate = date("Y-m-d");
                              // $qtyObj = $BranchSoObj->deliveryCreateItemQty($item['inventory_item_id']);
                              $qtyObj = $BranchSoObj->itemQtyStockCheck($item['inventory_item_id'], "'rmWhOpen', 'rmWhReserve', 'fgWhOpen'", "DESC", "", $min);
                              // console($qtyObj);
                              $sumOfBatches = $qtyObj['sumOfBatches'];
                              $batchesDetails = $BranchSoObj->convertToWHSLBatchArray($qtyObj['data']);
                              // console($itemQtyStockCheck);
                              ?>
                              <!-- Button to Open the Modal -->
                              <div class="qty-modal py-2">
                                <p class="font-bold checkQtySpan_<?= $key ?>"><?= $sumOfBatches; ?></p>
                                <hr class="my-2 w-50 mx-auto">
                                <div class="text-xs d-flex align-items-center gap-2 justify-content-center">
                                  <p class="itemSellType" id="itemSellType_<?= $key ?>"><?= $masterItemDetails['item_sell_type'] ?></p>
                                  <!-- <ion-icon name="create-outline" class="stockBtn" data-keyval="<?= $key ?>" id="stockBtn_<?= $key ?>" data-bs-toggle="modal" data-bs-target="#stockSetup<?= $key ?>" style="cursor: pointer;"></ion-icon> -->
                                  <ion-icon name="create-outline" class="stockBtn" data-keyval="<?= $key ?>" id="stockBtn_<?= $key ?>" style="cursor: pointer;"></ion-icon>
                                </div>
                              </div>
                              <input type="hidden" class="itemSellTypeHidden" id="itemSellTypeHidden_<?= $key ?>" name="listItem[<?= $key ?>][itemSellType]" value="<?= $masterItemDetails['item_sell_type'] ?>">


                              <!-- The Modal -->
                              <div class="modal fade stock-setup-modal" id="stockSetup<?= $key ?>">
                                <div class="modal-dialog">
                                  <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header" style="background: #003060; color: #fff;">
                                      <h4 class="modal-title text-sm text-white">Stock Setup (<?= $masterItemDetails['item_sell_type'] ?>)</h4>
                                      <p class="text-xs my-2 ml-5">Total Picked Qty :
                                        <span class="font-bold itemSelectTotalQty" id="itemSelectTotalQty_<?= $key ?>">0</span>
                                      </p>
                                    </div>

                                    <!-- Modal body -->
                                    <div class="modal-body">
                                      <span class="error itemStockSelectQty_<?= $key ?>" style="display: none;"></span>

                                      <!-- start warehouse accordion -->
                                      <div class="modal-select-type my-3">
                                        <div class="type type-one">
                                          <input type="radio" name="listItem[<?= $key ?>][itemreleasetype]" class="itemreleasetypeclass fifo" data-rdcode="<?= $key ?>" value="FIFO" id="fifo_<?= $key ?>" <?php if ($masterItemDetails['item_sell_type'] == "FIFO") {
                                                                                                                                                                                                              echo "checked";
                                                                                                                                                                                                            } ?>>
                                          <label for="fifo" class="text-xs mb-0">FIFO</label>
                                        </div>
                                        <div class="type type-two">
                                          <input type="radio" name="listItem[<?= $key ?>][itemreleasetype]" class="itemreleasetypeclass lifo" data-rdcode="<?= $key ?>" value="LIFO" id="lifo_<?= $key ?>" <?php if ($masterItemDetails['item_sell_type'] == "LIFO") {
                                                                                                                                                                                                              echo "checked";
                                                                                                                                                                                                            } ?>>
                                          <label for="lifo" class="text-xs mb-0">LIFO</label>
                                        </div>
                                        <div class="type type-three">
                                          <input type="radio" name="listItem[<?= $key ?>][itemreleasetype]" class="itemreleasetypeclass custom" data-rdcode="<?= $key ?>" value="CUSTOM" id="custom_<?= $key ?>">
                                          <label for="custom" class="text-xs mb-0 text-muted">Custom</label>
                                        </div>
                                      </div>
                                      <!-- <div class="textarea-note my-2">
                                        <textarea class="form-control" cols="6" rows="20" placeholder="notes...."></textarea>
                                      </div> -->
                                      <div class="customitemreleaseDiv<?= $key ?>" style="display: none;">
                                        <?php
                                        // console($qtyObj);
                                        // console($batchesDetails);
                                        foreach ($batchesDetails as $whKey => $wareHouse) {
                                        ?>
                                          <div class="accordion accordion-flush warehouse-accordion p-0" id="accordionFlushExample">
                                            <div class="accordion-item">
                                              <h2 class="accordion-header w-100" id="flush-headingOne">
                                                <button class="accordion-button btn btn-primary warehouse-header waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $whKey ?>" aria-expanded="true" aria-controls="flush-collapseOne">
                                                  <?= $wareHouse['warehouse_code'] ?> | <?= $wareHouse['warehouse_name'] ?>
                                                </button>
                                              </h2>
                                              <div id="collapse<?= $whKey ?>" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" style="">
                                                <div class="accordion-body p-0">
                                                  <h1></h1>
                                                  <div class="card bg-transparent">
                                                    <div class="card-body px-2 mx-3" style="background-color: #f9f9f9;">
                                                      <!-- start location accordion -->
                                                      <?php foreach ($wareHouse['storage_locations'] as $locationKey => $location) {
                                                      ?>
                                                        <div id="locAccordion">
                                                          <div class="card bg-transparent">
                                                            <div class="card-header p-2 border rounded-0 bg-transparent border-0 border-bottom">
                                                              <a class="btn text-dark w-100 storage-after" data-bs-toggle="collapse" href="#collapse<?= $whKey ?><?= $locationKey ?>">
                                                                <?= $location['storage_location_code'] ?> | <?= $location['storage_location_name'] ?>
                                                              </a>
                                                            </div>
                                                            <div id="collapse<?= $whKey ?><?= $locationKey ?>" class="collapse" data-bs-parent="#locAccordion">
                                                              <div class="card-body bg-light mx-3">
                                                                <?php
                                                                // console($location['batches']);
                                                                foreach ($location['batches'] as $batchKey => $batch) {
                                                                  // $batchItemUom = $ItemsObj->getBaseUnitMeasureById($batch['itemUom'])['data']['uomName'];
                                                                  $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                ?>
                                                                  <div class="storage-location mb-2">
                                                                    <div class="input-radio">
                                                                      <?php if ($batch['itemQty'] > 0) { ?>
                                                                        <input type="checkbox" name="listItem[<?= $key ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                      <?php } else { ?>
                                                                        <input type="checkbox" name="listItem[<?= $key ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" disabled>
                                                                      <?php } ?>
                                                                    </div>
                                                                    <div class="d-grid">
                                                                      <p class="text-sm mb-2">
                                                                        <?= $batch['logRef'] ?>
                                                                      </p>
                                                                      <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                        <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= $batch['itemQty'] ?> <?= $uomName ?> </span>
                                                                      </p>
                                                                    </div>
                                                                    <div class="input">
                                                                      <?php if ($batch['itemQty'] > 0) { ?>
                                                                        <input step="0.01" type="number" name="listItem[<?= $key ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $key . '|' . $batch['logRef']; ?>" class="form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $key; ?>" id="enterQty_<?= $batch['logRef']; ?>">
                                                                      <?php } else { ?>
                                                                        <input step="0.01" type="number" name="listItem[<?= $key ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $key . '|' . $batch['logRef']; ?>" class=" form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $key; ?>" id="enterQty_<?= $batch['logRef']; ?>" disabled>
                                                                      <?php } ?>
                                                                    </div>
                                                                  </div>
                                                                  <hr>
                                                                <?php } ?>
                                                              </div>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      <?php } ?>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        <?php } ?>
                                      </div>
                                      <!-- end warehouse accordion -->
                                    </div>

                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-success" data-bs-dismiss="modal">Proceed >></button>
                                    </div>

                                  </div>
                                </div>
                              </div>

                              <input class="form-control sumOfBatches_<?= $key ?>" type="hidden" name="listItem[<?= $key ?>][sumOfBatches]" value="<?= $sumOfBatches ?>">
                            </td>
                            <td class="inp-td">
                              <div class="d-flex gap-2 px-2">
                                <input type="text" name="listItem[<?= $key ?>][qty]" class="form-control delivery-qty inp-design full-width-center originalItemUnitPriceInp itemQty" id="itemQty_<?= $key ?>" value="0" readonly>
                                <input type="hidden" name="listItem[<?= $key ?>][uom]" value="<?= $item['uom'] ?>">
                                <?= $uomName ?>
                              </div>
                            </td>
                            <?php if ($item['goodsType'] == 3) { ?>
                              <td class="inp-td">
                                <div class="d-flex align-center justify-content-center">
                                  <input type="hidden" name="listItem[<?= $key ?>][extraOrderType]" class="form-control full-width inp-design full-width-center originalItemUnitPriceInp extraOrderType" id="extraOrderType_<?= $key ?>" value="production">
                                  <input type="text" name="listItem[<?= $key ?>][extraOrder]" class="form-control qty-input inp-design full-width-center originalItemUnitPriceInp extraOrderItemQty" id="extraOrder_<?= $key ?>" data-keyss="<?= $key ?>" value="0">
                                  <span class="rupee-symbol currency-symbol so-delivery-check-info pr-1"><input type="checkbox" name="listItem[<?= $key ?>][extraOrderCBox]" class="extraOrderCBox" id="extraOrderCBox_<?= $key ?>">
                                    <div class="help-tip ex-work-tooltip">
                                      <p>Production order will be generated for this quantity. </p>
                                    </div>
                                  </span>
                                  <?= $uomName ?>
                                </div>
                              </td>
                            <?php } else { ?>
                              <td class="inp-td">
                                <div class="d-flex align-center justify-content-center">
                                  <input type="hidden" name="listItem[<?= $key ?>][extraOrderType]" class="form-control full-width inp-design full-width-center originalItemUnitPriceInp extraOrderType" id="extraOrderType_<?= $key ?>" value="purchase">
                                  <input type="text" name="listItem[<?= $key ?>][extraOrder]" class="form-control qty-input inp-design full-width-center originalItemUnitPriceInp extraOrderItemQty" id="extraOrder_<?= $key ?>" data-keyss="<?= $key ?>" value="0">
                                  <span class="rupee-symbol currency-symbol so-delivery-check-info pr-1"><input type="checkbox" name="listItem[<?= $key ?>][extraOrderCBox]" class="extraOrderCBox" id="extraOrderCBox_<?= $key ?>">
                                    <div class="help-tip ex-work-tooltip">
                                      <p>PR(Purchase Request) will be generated for this quantity. </p>
                                    </div>
                                  </span>
                                  <?= $uomName ?>
                                </div>
                              </td>
                            <?php } ?>
                            <td class="action-flex-btn">
                              <a class="btn btn-danger delItemBtn" id="delItemBtn_<?= $item['so_item_id'] ?>">
                                <i class="fa fa-minus"></i>
                              </a>
                            </td>
                          </tr>
                        <?php
                        }
                        ?>

                      <?php
                      }
                      ?>
                      <!-- Final Submit Modal -->
                      <div class="modal fade" id="finalSubmitModal" tabindex="-1" role="dialog" aria-labelledby="finalSubmitModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="finalSubmitModalLabel">Preview Details</h5>
                              <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body delpreviewDetails">
                              <div class="row border my-2 p-2">
                                <p class="space-between-class"><strong>Item Code:</strong> <i>5000HJGU</i></p>
                                <p class="space-between-class"><strong>Item Name:</strong> <i class="itemname parent-space">5000HJGU</i></p>
                                <p class="space-between-class"><strong>Total Qty:</strong> <i>5000HJGU</i></p>
                                <p class="space-between-class"><strong>Delivery Qty:</strong> <i>5000HJGU</i></p>
                                <p class="space-between-class"><strong>Production Qty:</strong> <i>5000HJGU</i></p>
                                <p class="space-between-class"><strong>Purchase Request Qty:</strong> <i>5000HJGU</i></p>
                              </div>
                              <div class="row border my-2 p-2">
                                <p class="space-between-class"><strong>Item Code:</strong> <i>5000HJGU</i></p>
                                <p class="space-between-class"><strong>Item Name:</strong> <i class="itemname parent-space">5000HJGU</i></p>
                                <p class="space-between-class"><strong>Total Qty:</strong> <i>5000HJGU</i></p>
                                <p class="space-between-class"><strong>Delivery Qty:</strong> <i>5000HJGU</i></p>
                                <p class="space-between-class"><strong>Production Qty:</strong> <i>5000HJGU</i></p>
                                <p class="space-between-class"><strong>Purchase Request Qty:</strong> <i>5000HJGU</i></p>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <button type="submit" name="addNewSODeliveryFormSubmitBtn" class="btn btn-primary">Final Submit</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- <button type="submit" name="addNewSODeliveryFormSubmitBtn" onclick="return confirm('Are you sure to submit?')" class="btn btn-primary mt-3 mb-2 float-right" id="deliveryCreationBtn">Final Submit</button> -->
              <button type="button" class="btn btn-primary mt-3 mb-2 float-right" id="deliveryCreationBtn">Process</button>
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
<?php } else { ?>
  <div class="content-wrapper so-delivery-wrapper">
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
                  <h3 class="card-title">Sales Order Delivery List</h3>
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create-sales-order-delivery" class="btn btn-primary btnstyle m-2 float-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
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
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                <div class="card-body" style="overflow: auto;">
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
              <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                    <?php
                    $cond = '';

                    $sts = " AND `status` !='deleted'";
                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                      $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                    }
                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                      $cond .= " AND branch_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                    }
                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                      $cond .= " AND `so_number` like '%" . $_REQUEST['keyword2'] . "%' OR `delivery_no` like '%" . $_REQUEST['keyword2'] . "%' OR `delivery_date` like '%" . $_REQUEST['keyword2'] . "%'";
                    } else {
                      if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                        $cond .= " AND `so_number` like '%" . $_REQUEST['keyword'] . "%'  OR `delivery_no` like '%" . $_REQUEST['keyword'] . "%' OR `delivery_date` like '%" . $_REQUEST['keyword'] . "%'";
                      }
                    }
                    // $sql_list = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE 1 " . $cond . "  AND company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . "  ORDER BY vendor_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                    $sql_list = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` WHERE 1 AND company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' " . $cond . " " . $sts . " ORDER BY so_delivery_id DESC limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                    $qry_list = mysqli_query($dbCon, $sql_list);
                    $num_list = mysqli_num_rows($qry_list);

                    $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` WHERE 1 " . $cond . " AND company_id='" . $company_id . "' " . $sts . " ";
                    $countQry = mysqli_query($dbCon, $countShow);
                    $rowCount = mysqli_fetch_array($countQry);
                    $count = $rowCount[0];
                    $cnt = $GLOBALS['start'] + 1;
                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_SALES_ORDER_DELIVERY", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                    $settingsCheckbox = unserialize($settingsCh);
                    if ($num_list > 0) {
                    ?>
                      <table class="table defaultDataTable table-hover text-nowrap">
                        <thead>
                          <tr class="alert-light">
                            <th>#</th>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <th>Delivery No.</th>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <th>SO Number</th>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <th>Delivery Date</th>
                            <?php  }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <th>Customer Name</th>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <th>Delivery Status</th>
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
                            //console($oneSoList);
                            $soDetails = queryGet("SELECT conversion_rate, currency_name FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE so_id='" . $oneSoList['so_id'] . "'")['data'];

                            $conversion_rate = $soDetails['conversion_rate'];
                            $currency_name = $soDetails['currency_name'];
                          ?>
                            <tr>
                              <td><?= $cnt++ ?></td>
                              <?php if (in_array(1, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['delivery_no'] ?></td>
                              <?php }
                              if (in_array(2, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['so_number'] ?></td>
                              <?php }
                              if (in_array(3, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['delivery_date'] ?></td>
                              <?php }
                              if (in_array(4, $settingsCheckbox)) {
                              ?>
                                <td><?= $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0]['trade_name'] ?></td>
                              <?php }
                              if (in_array(5, $settingsCheckbox)) {
                                $status = '';
                                $class = 'status';
                                if ($oneSoList['status'] == 'reverse') {
                                  $status = 'Reversed';
                                  $class = 'status-danger text-xs text-center';
                                } else {
                                  $status = $oneSoList['deliveryStatus'];
                                }
                              ?>
                                <td class="listStatus">
                                  <p class="<?= $class; ?>"><?= $status; ?></p>
                                </td>
                              <?php }
                              if (in_array(6, $settingsCheckbox)) { ?>
                                <td><?= $oneSoList['totalItems'] ?></td>
                              <?php } ?>
                              <td>
                                <div class="d-flex">
                                  <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneSoList['so_delivery_id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                  <?php if ($oneSoList['status'] == 'active') { ?>
                                    <a style="cursor:pointer" data-id="<?= $oneSoList['so_delivery_id']; ?>" class="btn btn-sm reverseDelivery" title="Reverse Now">
                                      <i class="far fa-undo po-list-icon"></i>
                                    </a>
                                  <?php } ?>
                                </div>
                                <!-- right modal start here  -->
                                <div class="modal fade right so-delivery-modal classic-view-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $oneSoList['so_delivery_id'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                  <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                    <!--Content-->
                                    <div class="modal-content">
                                      <!--Header-->
                                      <div class="modal-header">

                                        <div class="customer-head-info">
                                          <div class="customer-name-code">
                                            <h2 class="d-flex gap-2"><span class="rupee-symbol">&#x20B9;</span><?= number_format($oneSoList['totalAmount'], 2) ?></h2>
                                            <p class="heading lead"><?= $oneSoList['delivery_no'] ?></p>
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
                                        <div class="display-flex-space-between mt-4 mb-3">
                                          <ul class="nav nav-tabs" id="myTab" role="tablist">
                                            <li class="nav-item">
                                              <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" role="tab" aria-controls="home" aria-selected="true"><ion-icon name="information-outline" class="mr-2"></ion-icon>Item Info</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" role="tab" aria-controls="profile" aria-selected="false"><ion-icon name="people-outline" class="mr-2"></ion-icon>Customer Info</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link" id="classic-view-tab" data-toggle="tab" href="#classic-view<?= $oneSoList['delivery_no'] ?>" role="tab" aria-controls="classic-view" aria-selected="false"><ion-icon name="apps-outline" class="mr-2"></ion-icon> Classic View</a>
                                            </li>
                                            <!-- -------------------Audit History Button Start------------------------- -->
                                            <li class="nav-item">
                                              <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" href="#history<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $oneSoList['delivery_no'])  ?>" aria-selected="false"><ion-icon name="time-outline" class="mr-2"></ion-icon>Trail</a>
                                            </li>
                                            <!-- -------------------Audit History Button End------------------------- -->
                                          </ul>
                                          <!-- action btn  -->
                                          <div class="action-btns display-flex-gap" id="action-navbar">
                                            <!-- <a href="#" class="btn btn-sm" title="Delete SO"><i class="fa fa-trash po-list-icon"></i></a> -->
                                            <!-- action btn  -->
                                            <a href="manage-pgi.php?create-pgi=<?= base64_encode($oneSoList['so_delivery_id']) ?>" class="btn btn-primary pgi-create-btn" title="Create PGI"><i class="fa fa-box"></i>Create PGI</a>
                                            <!-- <a href="#" class="btn btn-sm" title="Edit SO"><i class="fa fa-edit po-list-icon"></i></a> -->
                                          </div>
                                        </div>
                                      </div>
                                      <!--Body-->
                                      <div class="modal-body">
                                        <div class="tab-content" id="myTabContent">
                                          <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" role="tabpanel" aria-labelledby="home-tab">
                                            <form action="" method="POST">
                                              <div class="hamburger">
                                                <div class="wrapper-action">
                                                  <i class="fa fa-bell fa-2x"></i>
                                                </div>
                                              </div>
                                              <div class="nav-action" id="settings">
                                                <a title="Mail the customer" href="#" name="vendorEditBtn">
                                                  <i class="fa fa-envelope"></i>
                                                </a>
                                              </div>
                                              <div class="nav-action" id="thumb">
                                                <a title="Chat the customer" href="#" name="vendorEditBtn">
                                                  <i class="fab fa-whatsapp" aria-hidden="true"></i>
                                                </a>
                                              </div>
                                              <div class="nav-action" id="create">
                                                <a title="Call the customer" href="#" name="vendorEditBtn">
                                                  <i class="fa fa-phone"></i>
                                                </a>
                                              </div>
                                            </form>
                                            <?php
                                            $itemDeliveryDetails = $BranchSoObj->fetchBranchSoDeliveryItems($oneSoList['so_delivery_id'])['data'];
                                            foreach ($itemDeliveryDetails as $oneDeliveryItem) {
                                              $unitPrice = $oneDeliveryItem['unitPrice'] * $conversion_rate;
                                              $itemTotalDiscount = $oneDeliveryItem['itemTotalDiscount'] * $conversion_rate;
                                              $totalPrice = $oneDeliveryItem['totalPrice'] * $conversion_rate;
                                              $subTotalAmt = ($unitPrice * $oneDeliveryItem['qty']) - $itemTotalDiscount;
                                            ?>
                                              <div class="card">
                                                <div class="card-body p-3">
                                                  <div class="row">
                                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                                      <div class="left-section">
                                                        <div class="icon-img">
                                                          <i class="fa fa-box"></i>
                                                        </div>
                                                        <div class="code-des">
                                                          <h4><?= $oneDeliveryItem['itemCode'] ?></h4>
                                                          <p><?= $oneDeliveryItem['itemName'] ?></p>
                                                        </div>
                                                      </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                      <div class="right-section">
                                                        <div class="font-weight-bold">
                                                          <span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span> <?= number_format($subTotalAmt, 2) ?>
                                                        </div>
                                                        <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $unitPrice ?> * <?= $oneDeliveryItem['qty'] ?> <?= $uomName ?></p>
                                                        <div class="discount">
                                                          <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $unitPrice * $oneDeliveryItem['qty'] ?></p>
                                                          (-<?= $oneDeliveryItem['totalDiscount'] ?>%)
                                                        </div>
                                                        <p style="border-top: 1px solid;">(GST: <?= $oneDeliveryItem['tax'] ?>%)</p>
                                                        <div class="font-weight-bold">
                                                          <span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span> <?= number_format($totalPrice, 2) ?>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            <?php } ?>
                                          </div>
                                          <div class="tab-pane fade" id="profile<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" role="tabpanel" aria-labelledby="profile-tab">
                                            <?php
                                            if ($customerDetails != "") {
                                            ?>
                                              <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button btn btn-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
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
                                                          <!-- <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs text-left">Address :</p>
                                                            <p class="font-bold text-xs text-left w-75"><?= $customerAddressDetails['customer_address_building_no'] . ', ' . $customerAddressDetails['customer_address_flat_no'] . ', ' . $customerAddressDetails['customer_address_street_name'] . ', ' . $customerAddressDetails['customer_address_pin_code'] . ', ' . $customerAddressDetails['customer_address_location'] . ', ' . $customerAddressDetails['customer_address_city'] . ', ' . $customerAddressDetails['customer_address_district'] . ', ' . $customerAddressDetails['customer_address_state'] ?></p>
                                                          </div> -->
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



                                          <div class="tab-pane fade" id="classic-view<?= $oneSoList['delivery_no'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                            <div class="card classic-view bg-transparent">
                                              <div class="card-body classic-view-so-table" style="overflow: auto;">
                                                <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" id="printButton">Print Table</button> -->
                                                <button type="button" class="btn btn-primary classic-view-btn float-right" onclick="window.print(); return false;">Print</button>
                                                <?php

                                                $companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);

                                                //console($companyData);
                                                ?>
                                                <div class="printable-view">
                                                  <h3 class="h3-title text-center font-bold text-sm mb-4">Sales Order Delivery</h3>
                                                  <table class="classic-view table-bordered">
                                                    <tbody>
                                                      <tr>
                                                        <td colspan="5">
                                                          <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                                          <p><?= $companyData['location_building_no'] ?>, <?= $companyData['location_flat_no'] ?></p>
                                                          <p><?= $companyData['location'] ?>, <?= $companyData['location_street_name'] ?>, <?= $companyData['location_pin_code'] ?></p>
                                                          <p><?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?></p>
                                                          <p><?= $companyData['location_state'] ?></p>
                                                          <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                                                          <p>Company’s PAN: <?= $companyData['company_pan'] ?></p>
                                                          <!-- <p>State Name : West Bengal, Code : 19</p> -->
                                                          <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                                        </td>
                                                        <td colspan="3" class="border-right-none">
                                                          <p>Sales Order Delivery Number</p>
                                                          <p class="font-bold"><?= $oneSoList['delivery_no'] ?></p>
                                                        </td>
                                                        <td colspan="3" class="border-left-none">
                                                          <p>Dated</p>
                                                          <p class="font-bold"><?= $oneSoList['delivery_date'] ?></p>
                                                        </td>
                                                      </tr>
                                                      <tr>
                                                        <td colspan="5">
                                                          <p>Buyer (Bill to)</p>
                                                          <p class="font-bold"><?= $customerDetails['trade_name'] ?></p>
                                                          <p><?= $customerDetails['billingAddress'] ?></p>
                                                          <p>GSTIN/UIN : <?= $customerDetails['customer_gstin'] ?></p>
                                                          <!-- <p>State Name : Maharashtra, Code : 27</p> -->
                                                        </td>
                                                        <td colspan="5">
                                                          <p>Consignee (Ship to)</p>
                                                          <p class="font-bold"><?= $customerDetails['trade_name'] ?></p>
                                                          <p><?= $customerDetails['shippingAddress'] ?></p>
                                                          <!-- <p>State Name : Maharashtra, Code : 27</p>
                                                        <p>Place of Supply : Maharashtra</p> -->
                                                        </td>
                                                      </tr>
                                                      <tr>
                                                        <th rowspan="2">Sl No.</th>
                                                        <th rowspan="2">Particulars</th>
                                                        <th rowspan="2">HSN/SAC </th>
                                                        <th rowspan="2">Quantity</th>
                                                        <th rowspan="2">Rate</th>
                                                        <th rowspan="2">UOM</th>
                                                        <th rowspan="2">Discount</th>
                                                        <th colspan="2">IGST</th>
                                                        <th rowspan="2">Total Amount</th>
                                                      </tr>
                                                      <tr>
                                                        <th>Rate</th>
                                                        <th>Amount</th>
                                                      </tr>

                                                      <?php
                                                      // $itemDetails = $BranchSoObj->fetchBranchSoItems($oneSoList['so_id'])['data'];
                                                      $itemDetails = $BranchSoObj->fetchBranchSoDeliveryItems($oneSoList['so_delivery_id'])['data'];
                                                      $flagForBtn = 0;
                                                      $i = 0;
                                                      foreach ($itemDetails as $oneDeliveryItem) {
                                                        $baseUnitMeasure = $ItemsObj->getBaseUnitMeasureById($oneDeliveryItem['uom']);
                                                        $uomName = $baseUnitMeasure['data']['uomName'];

                                                        $deliveryScheduleObj = $BranchSoObj->fetchBranchSoItemsDeliverySchedule($oneDeliveryItem['so_item_id']);
                                                        $deliverySchedule = $deliveryScheduleObj['data'];
                                                        if (count($deliverySchedule) > 0) {
                                                          $flagForBtn++;
                                                        }
                                                        $subTotalAmt = ($oneDeliveryItem['unitPrice'] * $oneDeliveryItem['qty']);
                                                      ?>

                                                        <tr>
                                                          <td class="text-center"><?= ++$i ?></td>
                                                          <td class="text-center">
                                                            <p class="font-bold"><?= $oneDeliveryItem['itemName'] ?></p>
                                                            <p class="text-italic"><?= $oneDeliveryItem['itemCode'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $oneDeliveryItem['hsnCode'] ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $oneDeliveryItem['qty'] ?></p>
                                                          </td>
                                                          <td class="text-right">
                                                            <p><?= number_format($oneDeliveryItem['unitPrice'], 2) ?></p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $uomName ?></p>
                                                          </td>
                                                          <td class="text-right">
                                                            <p><?= $oneDeliveryItem['itemTotalDiscount'] ?></p>
                                                            <p class="font-bold text-italic">(<?= $oneDeliveryItem['totalDiscount'] ?>%)</p>
                                                          </td>
                                                          <td class="text-center">
                                                            <p><?= $oneDeliveryItem['tax'] ?>%</p>
                                                          </td>
                                                          <td class="text-right">
                                                            <p><?= $oneDeliveryItem['totalTax'] ?></p>
                                                          </td>
                                                          <td class="text-right">
                                                            <p><?= $oneDeliveryItem['totalPrice'] ?></p>
                                                          </td>
                                                        </tr>
                                                      <?php } ?>
                                                      <tr>
                                                        <td colspan="10" class="text-right font-bold">
                                                          <p><?= $oneSoList['totalAmount'] ?></p>
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
                                                        <td colspan="5" class="text-right">
                                                          <p class="text-center font-bold"> for <?= $companyData['company_name'] ?></p>
                                                          <p class="text-center sign-img">
                                                            <img width="60" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="signature">
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
                                          <div class="tab-pane fade" id="history<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" role="tabpanel" aria-labelledby="history-tab">

                                            <div class="audit-head-section mb-3 mt-3 ">
                                              <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneSoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['created_at']) ?></p>
                                              <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneSoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['updated_at']) ?></p>
                                            </div>
                                            <hr>
                                            <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>">
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
                            <td colspan="<?= $settingsCheckboxCount + 2; ?>">
                              <!-- Start .pagination -->
                              <?php
                              if ($count > 0 && $count > $GLOBALS['show']) {
                              ?>
                                <div class="pagination align-right">
                                  <?php pagination($count, "frm_opts"); ?>
                                </div>
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
              </div>
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
                              Delivery No.</td>
                          </tr>
                          <tr>
                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                              SO Number</td>
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
                              Total Amount</td>
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
  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('show');
    $('#overlay').toggleClass('show');
    $('.nav-action').toggleClass('show');
  });

  $(document).ready(function() {

    $(".stockBtn").click(function() {
      var key = $(this).data("keyval");
      console.log(key);

      $(`.delivery_scheduleDate_${key}`).remove();
      var deliveryScheduleQty = $(`#deliveryScheduleQty_${key}`).val();
      if (deliveryScheduleQty != '') {
        $(`#stockSetup${key}`).modal('show');
      } else {
        $(`.delivery_scheduleDate_${key}`).remove();
        $(`#deliveryScheduleQty_${key}`)
          .parent()
          .append(
            `<span class="error delivery_scheduleDate_${key}">Schedule Date is required</span>`
          );
      }
    });

    // start date checker
    function delivery_posting_date_checker() {
      let date = $("#postingDeliveryDate").val();
      let max = '<?php echo $max; ?>';
      let min = '<?php echo $min; ?>';

      if (date < min) {
        $(".postingDeliveryDateMsg").html(`<p class="text-danger text-xs" id="podatelabel">Invalid date</p>`);
        document.getElementById("deliveryCreationBtn").disabled = true;
      } else if (date > max) {
        $(".postingDeliveryDateMsg").html(`<p class="text-danger text-xs" id="podatelabel">Invalid date</p>`);
        document.getElementById("deliveryCreationBtn").disabled = true;
      } else {
        $(".postingDeliveryDateMsg").html("");
        document.getElementById("deliveryCreationBtn").disabled = false;
      }
    }
    $("#postingDeliveryDate ").on("keyup", function() {
      delivery_posting_date_checker();
    });
    // end date checker





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

    $(document).on("keyup keydown paste change", ".extraOrderItemQty", function() {
      let value = $(this).val();
      let key = $(this).data("keyss");
      if (value > 0) {
        $(`#extraOrderCBox_${key}`).prop('checked', true);
      } else {
        $(`#extraOrderCBox_${key}`).prop('checked', false);
      }
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
          console.log(response);
          $(".totalPrice").html(response);
        }
      });
    });
    //----------********************************

    // invoice date *****************************************
    $("#postingDeliveryDate").on("change", function(e) {
      // dynamic value
      let url = window.location.search;
      let param = url.split("=")[0];

      var invoicedate = $(this).val();
      var rowData = {};
      let flag = 0;
      $(".itemRow").each(function() {
        let rowId = $(this).attr("id").split("_")[2];
        let itemId = $(this).attr("id").split("_")[1];
        rowData[rowId] = itemId;

        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-items-stock-list.php`,
          data: {
            act: "itemStock",
            invoiceDate: invoicedate,
            itemId: itemId,
            randCode: rowId
          },
          beforeSend: function() {
            // $(".tableDataBody").html(`<option value="">Loding...</option>`);
          },
          success: function(response) {
            $(`.customitemreleaseDiv${rowId}`).hide();
            $(`.customitemreleaseDiv${rowId}`).html(response);
          }
        });
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
            url: `ajaxs/so/ajax-items-stock-check.php`,
            data: {
              act: "itemStockCheck",
              invoicedate: invoicedate,
              rowData: StringRowData
            },
            beforeSend: function() {
              $(".tableDataBody").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
              let data = JSON.parse(response);
              let itemData = data.data;
              console.log(data);
              if (data.status === "success") {
                for (let key in itemData) {
                  
                  if (itemData.hasOwnProperty(key)) {

                    $(`.deliveryScheduleQty`).val('');
                    $(`.itemQty`).val(0);
                    $(`.extraOrderItemQty`).val(0);
                    $(`.sumOfBatches_${key}`).val(itemData[key]);
                    $(`.checkQtySpan_${key}`).html(itemData[key]);
                    $(`.extraOrderCBox`).prop('checked', false);
                    $(`#itemSellType_${key}`).html('FIFO');
                    $(`.enterQty`).val('');
                  }
                }
              }
            }
          });
    });

    // ***********************************************
    // ***********************************************
    $(document).on("click", ".itemreleasetypeclass", function() {
      let itemreleasetype = $(this).val();
      var rdcode = $(this).data("rdcode");
      console.log(rdcode);
      totalquentitydiscut(rdcode);
      $("#itemSellType_" + rdcode).html(itemreleasetype);
      if (itemreleasetype == 'CUSTOM') {
        $(".customitemreleaseDiv" + rdcode).show();
        $("#itemQty_" + rdcode).prop("readonly", true);
      } else {
        $(".customitemreleaseDiv" + rdcode).hide();
        $("#itemQty_" + rdcode).prop("readonly", false);
      }
    });

    $(document).on("keyup paste keydown", ".enterQty", function() {
      let enterQty = $(this).val();
      var rdcodeSt = $(this).data("rdcode");
      var maxqty = $(this).data("maxval");
      let rdatrr = [];
      rdatrr = rdcodeSt.split("|");
      let rdcode = rdatrr[0]; // Change the variable name to rdcode
      let rdBatch = rdatrr[1];

      console.log(enterQty);
      if (enterQty <= maxqty) {
        if (enterQty > 0) {
          console.log("01");
          totalquentity(rdcodeSt);
          $('.batchCheckbox' + rdBatch).prop('checked', true);
        } else {
          $(this).val('');
          console.log("02");
          totalquentity(rdcodeSt);
          $('.batchCheckbox' + rdBatch).prop('checked', false);
        }
      } else {
        $(this).val('');
        console.log("03");
        totalquentity(rdcodeSt);
      }
    });

    function totalquentitydiscut(rdcode) {

      $(".qty" + rdcode).each(function() {
        $(this).val('');
      });
      $("#itemSelectTotalQty_" + rdcode).html(0);
      $("#itemQty_" + rdcode).val(0);
      $('.batchCbox').prop('checked', false);
    }

    function totalquentity(rdcodeSt) {
      let rdatrr = [];
      rdatrr = rdcodeSt.split("|");
      let rdcode = rdatrr[0]; // Change the variable name to rdcode
      let rdBatch = rdatrr[1];
      var sum = 0;
      $(`.itemStockSelectQty_${rdcode}`).hide();
      $(`.itemStockSelectQty_${rdcode}`).html('');
      $(".qty" + rdcode).each(function() {
        // Parse the value as a number and add it to the sum
        var value = parseFloat($(this).val()) || 0;
        sum += value;
      });

      // console.log("Sum: " + sum);

      $("#itemSelectTotalQty_" + rdcode).html(sum);
      $("#itemQty_" + rdcode).val(sum);
      console.log('first => ' + rdcode);

      ////-----------------------------
      let qtyVal = $(`#deliveryScheduleQty_${rdcode}`).find(":selected").data("quantity");

      let deliveryQty = 0;
      let orderQty = 0;
      let extraQty = 0;
      if (sum <= qtyVal) {
        extraQty = qtyVal - sum;
        deliveryQty = sum;
        orderQty = extraQty;
      } else if (sum > qtyVal) {
        deliveryQty = sum;
        orderQty = 0;
        $(`.itemStockSelectQty_${rdcode}`).show();
        $(`.itemStockSelectQty_${rdcode}`).html("You selected more then delivery quantity!");
      } else {
        deliveryQty = qtyVal;
        orderQty = 0;
      }
      if (orderQty > 0) {
        $(`#extraOrderCBox_${rdcode}`).prop("checked", true);
      } else {
        $(`#extraOrderCBox_${rdcode}`).prop("checked", false);
      }
      $(`#itemQty_${rdcode}`).val(deliveryQty);
      $(`#extraOrder_${rdcode}`).val(orderQty);


      //////--------------------------------
      // calculateOneItemAmounts(rdcode);
    }
    // ***********************************************
    // ***********************************************

    $(".deliveryScheduleQty").on("change", function() {
      let qtyVal3 = ($(this).attr("id")).split("_")[1];
      let qtyVal = $(this).find(":selected").data("quantity");
      $(`.delivery_scheduleDate_${qtyVal3}`).remove();
      $(`#itemQty_${qtyVal3}`).val(0);
      $(`#extraOrder_${qtyVal3}`).val(0);
      // let qtyVal2 = $(this).find(":selected").data("deliverydate");
      // let qtyVal = $(this).find(":selected").children("span");
      // $( "#myselect option:selected" ).text();
      console.log(qtyVal);
      let sumOfBatches = $(`.sumOfBatches_${qtyVal3}`).val();
      let deliveryQty = 0;
      let orderQty = 0;
      if (sumOfBatches <= qtyVal) {
        let extraQty = qtyVal - sumOfBatches;
        deliveryQty = sumOfBatches;
        orderQty = extraQty;
      } else {
        deliveryQty = qtyVal;
        orderQty = 0;
      }
      if (orderQty > 0) {
        $(`#extraOrderCBox_${qtyVal3}`).prop("checked", true);
      } else {
        $(`#extraOrderCBox_${qtyVal3}`).prop("checked", false);
      }
      $(`#itemTotalQty_${qtyVal3}`).val(qtyVal);
      $(`#itemQty_${qtyVal3}`).val(deliveryQty);
      $(`#extraOrder_${qtyVal3}`).val(orderQty);
    });

    // Click event handler for elements with class "lifo" or "fifo" 🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁
    // $(".lifo, .fifo").on("click", function() {
    //   let key = ($(this).attr("id")).split("_")[1];
    //   // Disable all batchCheckboxes and enterQty elements
    //   $(".batchCheckbox, .enterQty").prop("disabled", true);
    //   let inpVal = $(this).val();

    //   $(`#itemSellType_${key}`).html(inpVal.toUpperCase());
    //   $(`#itemSellTypeHidden_${key}`).val(inpVal.toUpperCase());
    // });

    // Click event handler for elements with class "custom"
    // $(".custom").on("click", function() {
    //   let key = ($(this).attr("id")).split("_")[1];
    //   let inpVal = $(this).val();
    //   // Enable all batchCheckboxes and enterQty elements
    //   $(".batchCheckbox, .enterQty").prop("disabled", false);

    //   $(`#itemSellType_${key}`).html(inpVal.toUpperCase());
    //   $(`#itemSellTypeHidden_${key}`).val(inpVal.toUpperCase());

    //   $(`#itemSellType_${key}`).html(inpVal.toUpperCase());
    //   $(`#itemSellTypeHidden_${key}`).val(inpVal.toUpperCase());

    //   // Disable batchCheckboxes and enterQty elements with batchItemQty equal to 0
    //   $(".batchItemQty").each(function() {
    //     if ($(this).text() == "0") {
    //       const batchCheckboxId = $(this).attr("id").replace("batchItemQty_", "batchCheckbox_");
    //       $("#" + batchCheckboxId).prop("disabled", true);
    //       const enterQtyId = $(this).attr("id").replace("batchItemQty_", "enterQty_");
    //       $("#" + enterQtyId).prop("disabled", true);
    //     }
    //   });
    // });

    // // Keyup event handler for elements with class "enterQty"
    // $(".enterQty").on("keyup", function() {
    //   const enteredQty = parseInt($(this).val());
    //   const batchItemQtyId = $(this).closest(".storage-location").find(".batchItemQty").attr("id");
    //   const remainingQty = parseInt($("#remainingQty").text());
    //   const batchItemQty = parseInt($("#" + batchItemQtyId).text());
    //   const totalQty = parseInt($("#totalQty").text());

    //   if (isNaN(enteredQty)) {
    //     // If the entered value is not a valid number, show an error or perform appropriate actions
    //     alert("Please enter a valid number");
    //     return;
    //   }

    //   if (enteredQty > batchItemQty) {
    //     // If the entered quantity is greater than batchItemQty, show an alert
    //     alert("Entered quantity is greater than batchItemQty");
    //   }

    //   if (enteredQty > totalQty) {
    //     // If the entered quantity is greater than totalQty, show an alert
    //     alert("Entered quantity is greater than total quantity");
    //   }

    //   if (enteredQty > remainingQty) {
    //     // If the entered quantity is greater than remainingQty, show an alert
    //     alert("Entered quantity is greater than remaining quantity");
    //   }
    // });

    function updateAndValidateQuantity(enterQtyInput) {
      var batchItemQtyElement = enterQtyInput.closest('.storage-location').find('.batchItemQty');
      var remainingQtyElement = $('.itemSelectRemainingQty');
      var batchItemQty = parseFloat(batchItemQtyElement.text());
      var remainingQty = parseFloat(remainingQtyElement.text());
      var enterQty = parseFloat(enterQtyInput.val());

      // Update the remainingQty and batchItemQty based on the entered quantity
      if (!isNaN(enterQty) && enterQty >= 0) {
        var totalUsedQty = 0;
        $('.enterQty').each(function() {
          var qty = parseFloat($(this).val());
          if (!isNaN(qty) && qty >= 0) {
            totalUsedQty += qty;
          }
        });
        remainingQty = parseFloat(remainingQtyElement.text()) - totalUsedQty;
        batchItemQty = parseFloat(batchItemQtyElement.text());
      }

      // Validate the entered quantity against batchItemQty and remainingQty
      if (isNaN(enterQty) || enterQty < 0) {
        enterQtyInput.val('');
      } else if (enterQty > batchItemQty) {
        enterQtyInput.val(batchItemQty);
      } else if (enterQty > remainingQty) {
        enterQtyInput.val(remainingQty);
      }

      // Update the remainingQty and batchItemQty elements
      remainingQtyElement.text(remainingQty);
      batchItemQtyElement.text(batchItemQty);
    }

    // Event listener for input changes in the enterQty field
    // $('.enterQty').on('input', function() {
    //   updateAndValidateQuantity($(this));

    //   // Call the function on page load to initialize remainingQty and batchItemQty
    //   $('.enterQty').each(function() {
    //     updateAndValidateQuantity($(this));
    //   });
    // });


    // modal stock btn 
    $(".stockBtn").on("click", function() {
      let itemKey = ($(this).attr("id")).split("_")[1];
      let itemQty = (parseFloat($(`#itemQty_${itemKey}`).val()) > 0) ? parseFloat($(`#itemQty_${itemKey}`).val()) : 0;
      $(`#itemSelectTotalQty_${itemKey}`).html(itemQty);
      $(`#itemSelectRemainingQty_${itemKey}`).html(itemQty);
    });

    // jquery validation 🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁🏁
    $(document).on("click", "#deliveryCreationBtn", function(e) {
      let validStatus = 0;
      let counter = 0;
      let validCounter = 0;
      let scheduleDateErrorShown = false;

      // DELIVERY POSTING VALIDATION
      if ($("#postingDeliveryDate").val() == "") {
        $(".delivery_postingDeliveryDate").remove();
        $("#postingDeliveryDate")
          .parent()
          .append(
            '<span class="error delivery_postingDeliveryDate">Delivery Posting Date is required</span>'
          );
        $(".delivery_postingDeliveryDate").show();

        $(".notespostingDeliveryDate").remove();
        $("#notesModalBody").append(
          '<p class="notespostingDeliveryDate font-monospace text-danger">Delivery Posting Date is required</p>'
        );
        $('#finalSubmitModal').modal('hide');
      } else {
        $(".delivery_postingDeliveryDate").remove();
        $(".notespostingDeliveryDate").remove();
        validStatus++;
      }

      for (elem of $(".deliveryScheduleQty")) {
        // SCHEDULE DATE VALIDATION
        if ($(elem).val() == "" || typeof($(elem).val()) === "undefined") {
          $(`.delivery_scheduleDate_${counter}`).remove();
          $(`#deliveryScheduleQty_${counter}`)
            .parent()
            .append(
              `<span class="error delivery_scheduleDate_${counter}">Schedule Date is required</span>`
            );
          $(`.delivery_scheduleDate_${counter}`).show();

          $(`.notesscheduleDate_${counter}`).remove();
          $("#notesModalBody").append(
            `<p class="notesscheduleDate_${counter} font-monospace text-danger">Schedule Date is required for Line No. ${counter+1}</p>`
          );
          scheduleDateErrorShown = true;
        } else {
          $(`.delivery_scheduleDate_${counter}`).remove();
          $(`.notesscheduleDate_${counter}`).remove();
          validStatus++;
          validCounter++;
        }
        counter++;
      }

      if (validStatus !== 1 + validCounter || scheduleDateErrorShown) {
        e.preventDefault();
        $("#exampleModal").modal("show");
      } else {
        $('#finalSubmitModal').modal('show');

        let formData = $("#addNewdeliveryForm").serialize();
        $.ajax({
          type: "POST",
          url: `ajaxs/so/ajax-deliverypreview.php`,
          data: formData,
          beforeSend: function() {
            $(".delpreviewDetails").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Scaning...');
          },
          success: function(response) {
            $(".delpreviewDetails").html(response);
            console.log(response);
            // $('#addNewItemsForm').trigger("reset");
            // $("#addNewItemsFormModal").modal('toggle');
            // $("#addNewItemsFormSubmitBtn").html("Submit");
            // $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
          }
        });


      }
    });


  });


  $('.reverseDelivery').click(function(e) {
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
            dep_slug: 'reverseDelivery'
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

<!-- <script src="<?= BASE_URL; ?>public/validations/deliveryCreationValidation.js"></script> -->