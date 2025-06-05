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
  $branchSoDeliveryCreationObj = $BranchSoObj->branchSoDeliveryCreate($_POST);
  if ($branchSoDeliveryCreationObj['status'] == "success") {
    swalAlert($branchSoDeliveryCreationObj["status"], $branchSoDeliveryCreationObj['deliveryNo'], "Delivery Created Succesfully", "manage-sales-orders-delivery-taxComponents.php");
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
  $min = $singleSoDetails['so_date'];
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

                    <?php if ($companyCountry == 103) { ?>
                      <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                        <div class="form-inline">
                          <label for="" class="text-xs font-bold">GSTIN:&nbsp;</label>
                          <p class="text-xs mb-0"><?= $customerDetails['customer_gstin'] ?></p>
                        </div>
                      </div>

                    <?php } ?>
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
                        <input type="text" name="soDeliveryPostingDate" class="form-control" value="<?= formatDateWeb($singleSoDetails['delivery_date']) ?>" readonly />
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
                      // $itemDetails = $BranchSoObj->fetchBranchSoItems($singleSoDetails['so_id'])['data'];
                      $itemDetails = $BranchSoObj->fetchBranchSoItemsDelivery($singleSoDetails['so_id'])['data'];
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
                                        <option value="<?= $dSchedule['so_delivery_id'] ?>" data-quantity="<?= inputQuantity($dSchedule['remainingQty']) ?>" data-deliveryDate="<?= $dSchedule['delivery_date'] ?>"><?= formatDateORDateTime($dSchedule['delivery_date']) ?> / (<span class="span"><?= inputQuantity($dSchedule['remainingQty']) ?></span> <?= $uomName ?>)</option>
                                      <?php } else { ?>
                                        <option value="<?= $dSchedule['so_delivery_id'] ?>" data-quantity="<?= $dSchedule['qty'] ?>" data-deliveryDate="<?= $dSchedule['delivery_date'] ?>"><?= formatDateORDateTime($dSchedule['delivery_date']) ?> / (<span class="span"><?= $dSchedule['qty'] ?></span> <?= $uomName ?>)</option>
                                  <?php
                                      }
                                    }
                                  }
                                  ?>
                                </select>
                              </div>
                              <small class="float-right">
                                Total
                                (<?= decimalQuantityPreview($item['qty']) ?>
                                <?= $uomName ?>)
                              </small>
                            </td>
                            <td>
                              <?php
                              // echo  $min;
                              // echo $asondate = date("Y-m-d");
                              // $qtyObj = $BranchSoObj->deliveryCreateItemQty($item['inventory_item_id']);
                              $qtyObj = $BranchSoObj->itemQtyStockCheck($item['inventory_item_id'], "'rmWhOpen', 'fgWhOpen'", "DESC", "", $min);
                              $qtyObj2 = $BranchSoObj->itemQtyStockCheckWithAcc($item['inventory_item_id'], "'rmWhOpen', 'fgWhOpen'", "DESC", "", $min);

                              // console($qtyObj);
                              $sumOfBatches = $qtyObj2['sumOfBatches'];



                              $batchesDetails = $BranchSoObj->convertToWHSLBatchArray($qtyObj['data']);
                              // console($itemQtyStockCheck);
                              ?>
                              <!-- Button to Open the Modal -->
                              <div class="qty-modal py-2">
                                <p class="font-bold checkQtySpan_<?= $key ?>"><?= decimalQuantityPreview($sumOfBatches) ?></p>
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
                                        <span class="font-bold itemSelectTotalQty" id="itemSelectTotalQty_<?= $key ?>"><?= decimalQuantityPreview(0) ?></span>
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
                                                                <style>
                                                                  input.red-placeholder {
                                                                    color: red;
                                                                    /* Text color */
                                                                    border: 1px solid red;
                                                                    /* Border color */
                                                                  }
                                                                </style>
                                                                <?php
                                                                // console($location['batches']);
                                                                foreach ($location['batches'] as $batchKey => $batch) {
                                                                  // $batchItemUom = $ItemsObj->getBaseUnitMeasureById($batch['itemUom'])['data']['uomName'];
                                                                  $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                  if (in_array($batch['refActivityName'], ['STRGE-LOC', 'PGI', 'REV-INVOICE', 'CN', 'DN', 'MAT-MAT-IN'])) {
                                                                    $batchno = $batch['logRef'];
                                                                  } else {
                                                                    $batchno = $batch['refNumber'];
                                                                  }


                                                                  $batchStatus = $BranchSoObj->checkBatchStatus($batchno, $company_id, $branch_id, $location_id,$batch['refActivityName']);

                                                                  $disbaledstatus = $batchStatus['disabled'];
                                                                  $status = $batchStatus['status'];
                                                                  $placeholderText = $batchStatus['placeholderText'];
                                                                  $placeholderClass = $batchStatus['placeholderClass'];
                                                                ?>
                                                                  <div class="storage-location mb-2">
                                                                    <div class="input-radio">
                                                                      <?php if ($batch['itemQty'] > 0) { ?>
                                                                        <input type="checkbox" <?= $disbaledstatus ?> name="listItem[<?= $key ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                      <?php } else { ?>
                                                                        <input type="checkbox" <?= $disbaledstatus ?> name="listItem[<?= $key ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" disabled>
                                                                      <?php } ?>
                                                                    </div>
                                                                    <div class="d-grid">
                                                                      <p class="text-sm mb-2">
                                                                        <?= $batch['logRef'] ?>
                                                                      </p>
                                                                      <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                        <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= decimalQuantityPreview($batch['itemQty']) ?> <?= $uomName ?> </span>
                                                                      </p>
                                                                    </div>
                                                                    <div class="input">
                                                                      <?php if ($batch['itemQty'] > 0) { ?>
                                                                        <input step="any" <?= $disbaledstatus ?> type="number" name="listItem[<?= $key ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $key . '|' . $batch['logRef']; ?>" class="<?= $placeholderClass ?> form-control inputQuantityClass ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $key; ?>" id="enterQty_<?= $batch['logRef']; ?>" placeholder="<?= $placeholderText ?>">
                                                                      <?php } else { ?>
                                                                        <input step="any" <?= $disbaledstatus ?> type="number" name="listItem[<?= $key ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $key . '|' . $batch['logRef']; ?>" class=" <?= $placeholderClass ?> form-control inputQuantityClass ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $key; ?>" id="enterQty_<?= $batch['logRef']; ?>" placeholder="<?= $placeholderText ?>" disabled>
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
                                <input type="text" name="listItem[<?= $key ?>][qty]" class="form-control delivery-qty inp-design full-width-center originalItemUnitPriceInp itemQty inputQuantityClass" id="itemQty_<?= $key ?>" value="<?= decimalQuantityPreview(0) ?>" readonly>
                                <input type="hidden" name="listItem[<?= $key ?>][uom]" value="<?= $item['uom'] ?>">
                                <?= $uomName ?>
                              </div>
                            </td>
                            <?php if ($item['goodsType'] == 3) { ?>
                              <td class="inp-td">
                                <div class="d-flex align-center justify-content-center">
                                  <input type="hidden" name="listItem[<?= $key ?>][extraOrderType]" class="form-control full-width inp-design full-width-center originalItemUnitPriceInp extraOrderType" id="extraOrderType_<?= $key ?>" value="production">
                                  <input type="text" name="listItem[<?= $key ?>][extraOrder]" class="form-control qty-input inp-design full-width-center originalItemUnitPriceInp extraOrderItemQty inputQuantityClass" id="extraOrder_<?= $key ?>" data-keyss="<?= $key ?>" value="<?= decimalQuantityPreview(0) ?>">
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
                                  <input type="text" name="listItem[<?= $key ?>][extraOrder]" class="form-control qty-input inp-design full-width-center originalItemUnitPriceInp extraOrderItemQty inputQuantityClass" id="extraOrder_<?= $key ?>" data-keyss="<?= $key ?>" value="<?= decimalQuantityPreview(0) ?>">
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
<?php } else {
?>
  <script>
    let url = `<?= BRANCH_URL ?>location/manage-sales-orders-delivery-taxComponents.php`;
    window.location.href = url;
  </script>
<?php
} ?>

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
      var deliveryScheduleQty = helperQuantity($(`#deliveryScheduleQty_${key}`).val());
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


    $(document).on("input keyup paste blur", ".inputQuantityClass", function() {
      console.log("called");
      let val = $(this).val();
      let base = <?= $decimalQuantity ?>;
      // Allow only numbers and one decimal point
      if (val.includes(".")) {
        let parts = val.split(".");
        if (parts[1].length > base) {
          $(this).val(parts[0] + "." + parts[1].substring(0, base)); // Restrict extra decimals
        }
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
          console.log(response);
          if (data.status === "success") {
            for (let key in itemData) {

              if (itemData.hasOwnProperty(key)) {

                $(`.deliveryScheduleQty`).val('');
                $(`.itemQty`).val(0);
                $(`.extraOrderItemQty`).val(0);
                $(`.sumOfBatches_${key}`).val(decimalQuantity(itemData[key]));
                $(`.checkQtySpan_${key}`).html(decimalQuantity(itemData[key]));
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
      enterQty = Number(enterQty);
      maxqty = Number(maxqty);
      let rdatrr = [];
      rdatrr = rdcodeSt.split("|");
      let rdcode = rdatrr[0]; // Change the variable name to rdcode
      let rdBatch = rdatrr[1];

      console.log(enterQty);
      console.log(rdcodeSt);
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
      $("#itemSelectTotalQty_" + rdcode).html(decimalQuantity(0));
      $("#itemQty_" + rdcode).val(decimalQuantity(0));
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
        var value = helperQuantity($(this).val()) || 0;
        sum += value;
      });

      console.log("Sum: " + sum);

      $("#itemSelectTotalQty_" + rdcode).html(decimalQuantity(sum));
      $("#itemQty_" + rdcode).val(decimalQuantity(sum));
      console.log('first => ' + rdcode);


      ////-----------------------------
      let qtyVal = $(`#deliveryScheduleQty_${rdcode}`).find(":selected").data("quantity");

      let deliveryQty = 0;
      let orderQty = 0;
      let extraQty = 0;
      if (num(sum) <= num(qtyVal)) {
        extraQty = qtyVal - sum;
        deliveryQty = sum;
        orderQty = extraQty;
      } else if (num(sum) > num(qtyVal)) {
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
      $(`#itemQty_${rdcode}`).val(decimalQuantity(deliveryQty));
      $(`#extraOrder_${rdcode}`).val(decimalQuantity(orderQty));


      //////--------------------------------
      // calculateOneItemAmounts(rdcode);
    }
    // ***********************************************
    // ***********************************************

    $(".deliveryScheduleQty").on("change", function() {
      let qtyVal3 = ($(this).attr("id")).split("_")[1];
      let qtyVal = $(this).find(":selected").data("quantity");
      $(`.delivery_scheduleDate_${qtyVal3}`).remove();
      $(`#itemQty_${qtyVal3}`).val(decimalQuantity(0));
      $(`#extraOrder_${qtyVal3}`).val(decimalQuantity(0));
      // let qtyVal2 = $(this).find(":selected").data("deliverydate");
      // let qtyVal = $(this).find(":selected").children("span");
      // $( "#myselect option:selected" ).text();
      console.log(qtyVal);

      let sumOfBatches = $(`.sumOfBatches_${qtyVal3}`).val();
      console.log(sumOfBatches);
      let deliveryQty = 0;
      let orderQty = 0;
      if (Number(sumOfBatches) <= Number(qtyVal)) {
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
      $(`#itemQty_${qtyVal3}`).val(decimalQuantity(deliveryQty));
      $(`#extraOrder_${qtyVal3}`).val(decimalQuantity(orderQty));
    });

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
      $(`#itemSelectTotalQty_${itemKey}`).html(decimalQuantity(itemQty));
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