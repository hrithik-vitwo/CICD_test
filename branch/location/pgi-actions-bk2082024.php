<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
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
  // console($_POST);
  // exit();
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
                      <th>Stock Availabilities</th>
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
                        $qtyObj = $BranchSoObj->itemQtyStockCheck($item['inventory_item_id'], "'fgWhReserve'", "DESC", "", $singleSoDetails['delivery_date']);
                        $masterItemDetails = $BranchSoObj->fetchItemSummaryDetails($item['inventory_item_id'])['data'][0];
                        $sumOfBatches = $qtyObj['sumOfBatches'];                        
                        $batchesDetails = $BranchSoObj->convertToWHSLBatchArray($qtyObj['data']);
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
                            <!-- Button to Open the Modal -->
                            <div class="qty-modal py-2">
                              <p class="font-bold checkQtySpan_<?= $key ?>"><?= $sumOfBatches; ?></p>
                              <!-- <hr class="my-2 w-50 mx-auto"> -->
                              <!-- <div class="text-xs d-flex align-items-center gap-2 justify-content-center">
                                <p class="itemSellType" id="itemSellType_<?= $key ?>"><?= $masterItemDetails['item_sell_type'] ?></p>
                                <ion-icon name="create-outline" class="stockBtn" data-keyval="<?= $key ?>" id="stockBtn_<?= $key ?>" style="cursor: pointer;"></ion-icon>
                              </div> -->
                            </div>
                            
                            <input class="form-control sumOfBatches_<?= $key ?>" type="hidden" name="listItem[<?= $key ?>][batchNo]" value="<?= $sumOfBatches ?>">
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
                          <td>
                            <?php
                            $schedule = $BranchSoObj->fetchBranchSoItemsDeliveryScheduleById($item['delivery_date'])['data'][0];
                            // console($schedule);
                            ?>
                            <span><?=  formatDateORDateTime($schedule['delivery_date']) ?></span>
                            <input type="hidden" name="listItem[<?= $key ?>][deliveryDate]" value="<?= $item['delivery_date'] ?>">
                            <input type="hidden" name="listItem[<?= $key ?>][itemQty]" value="<?= $item['qty'] ?>">
                            <small>(Qty-<?= decimalQuantityPreview($schedule['qty']) ?>)</small>
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
<?php } else {
?>
  <script>
    let url = `<?= BRANCH_URL ?>location/manage-pgi.php`;
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
  // stock pgi
  $(document).ready(function() {


    $(".stockBtn").click(function() {
      var key = $(this).data("keyval");
      let pgiDate = $("#pgiDate").val();
      console.log(key);
      console.log(pgiDate);

      $(`.delivery_scheduleDate_${key}`).remove();
      // var deliveryScheduleQty = $(`#deliveryScheduleQty_${key}`).val();
      if (pgiDate != '') {
        $(`#stockSetup${key}`).modal('show');
      } else {}

    });

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

      console.log("Sum: " + sum);
      console.log("rd code: " + rdcode);

      $("#itemSelectTotalQty_" + rdcode).html(sum);
      $("#itemQty_" + rdcode).val(6);
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

    // end stock here

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

        let qty = Number($(`#itemQty_${row}`).val());
        let stock = Number($(`.sumOfBatches_${row}`).val());

        // Use toFixed() to handle precision issues
        qty = Number(qty.toFixed(6)); // Ensures qty has 6 decimal places
        stock = Number(stock.toFixed(6)); // Ensures stock has 6 decimal places
        console.log(qty + '<=' + stock);
        if (stock < qty) {
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



    // modal stock btn 
    $(".stockBtn").on("click", function() {
      let itemKey = ($(this).attr("id")).split("_")[1];
      let itemQty = (parseFloat($(`#itemQty_${itemKey}`).val()) > 0) ? parseFloat($(`#itemQty_${itemKey}`).val()) : 0;
      $(`#itemSelectTotalQty_${itemKey}`).html(itemQty);
      $(`#itemSelectRemainingQty_${itemKey}`).html(itemQty);
    });

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

<script>

</script>