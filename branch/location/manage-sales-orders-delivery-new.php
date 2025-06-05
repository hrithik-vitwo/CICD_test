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

// console($_SESSION);
if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();

if (isset($_POST['addNewSODeliveryFormSubmitBtn'])) {
  $branchSoDeliveryCreationObj = $BranchSoObj->branchSoDeliveryCreate($_POST);
  // console($branchSoDeliveryCreationObj);
  if ($branchSoDeliveryCreationObj['status'] == "success") {
    // console($branchSoDeliveryCreationObj);

    swalAlert($branchSoDeliveryCreationObj["status"], $branchSoDeliveryCreationObj['deliveryNo'], $branchSoDeliveryCreationObj["message"], $_SERVER['PHP_SELF']);
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

  @media (max-width: 575px) {
    .so-delivery-modal .modal-header {
      height: 288px !important;
    }
  }
</style>
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">



<?php
if (isset($_GET['create-sales-order-delivery'])) {
  $getSoNumber = base64_decode($_GET['create-sales-order-delivery']);
  $singleSoDetails = $BranchSoObj->fetchSoDetailsById($getSoNumber)['data'][0];
?>
  <div class="content-wrapper">
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
                <div class="card-body so-delivery-card">
                  <div class="row mt-4" id="customerInfo" style="row-gap: 17px;">
                    <input type="hidden" name="deliveryNo" value="<?= $singleSoDetails['delivery_no'] ?>">
                    <input type="hidden" name="soNumber" value="<?= $singleSoDetails['so_number'] ?>">
                    <input type="hidden" name="soId" value="<?= $singleSoDetails['so_id'] ?>">
                    <input type="hidden" name="customer_shipping_address" value="<?= $singleSoDetails['shippingAddress'] ?>">
                    <input type="hidden" name="customer_billing_address" value="<?= $singleSoDetails['billingAddress'] ?>">

                    <?php
                    $customerDetails = $BranchSoObj->fetchCustomerDetails($singleSoDetails['customer_id'])['data'][0];

                    // console($BranchSoObj->fetchCustomerDetails($singleSoDetails['customer_id'])['data'][0]);
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
                        <p class="font-weight-bold text-success text-xs mb-0"><?= $customerDetails['customer_status'] ?></p>
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
                      Other info
                    </h4>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row" style="row-gap: 17px;">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="form-input">
                        <label for="">Delivery Posting Date</label>
                        <input type="date" name="deliveryCreationDate" min="<?= $min ?>" max="<?= $max ?>" class="form-control" id="postingDeliveryDate" required />
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
                  <div class="head justify-content-start mb-2 mt-3">
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
                        <th colspan="2">Select Stock</th>
                        <th rowspan="2">Schedule Date</th>
                        <th rowspan="2">Qty</th>
                        <th rowspan="2">Remove</th>
                      </tr>
                    </thead>
                    <tbody id="itemsTable">
                      <?php
                      // console($BranchSoObj->fetchBranchSoDeliveryItems($singleSoDetails['so_id'])['data']);
                      $itemDetails = $BranchSoObj->fetchBranchSoItems($singleSoDetails['so_id'])['data'];
                      $randCode = rand(000000, 999999);
                      foreach ($itemDetails as $key => $item) {

                        $baseUnitMeasure = $ItemsObj->getBaseUnitMeasureById($item['uom']);
                        $uomName = $baseUnitMeasure['data']['uomName'];

                        $deliveryScheduleObj = $BranchSoObj->fetchBranchSoItemsDeliverySchedule($item['so_item_id']);
                        $deliverySchedule = $deliveryScheduleObj['data'];

                        if (count($deliverySchedule) > 0) {
                      ?>
                          <tr class="rowDel itemRow" id="delItemRowBtn_<?= $item['so_item_id'] ?>">
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
                              <?php $mainItemDetails = $ItemsObj->getItemDetailsByCode($item['itemCode'])['data']; ?>
                              <!-- <input type="hidden" name="listItem[<?= $key ?>][openStock]" value="<?= $mainItemDetails['itemOpenStocks'] ?>"> -->
                              <!-- <div class="text-success font-weight-bold"><?= $mainItemDetails['itemOpenStocks'] ?></div> -->
                              <?php
                              $stockSummery = $BranchSoObj->fetchStocksSummaryDetails($item['inventory_item_id'])['data'][0];
                              $fgWhOpen = $stockSummery['fgWhOpen'];
                              $fgWhReserve = $stockSummery['fgWhReserve'] + $stockSummery['fgMktOpen'] + $stockSummery['fgMktReserve'];
                              ?>
                              <!-- <div class="text-muted font-weight-bold"><?= $fgWhOpen ?? 0 ?></div> -->
                              <!-- Button trigger modal -->
                              <a href="#" style="text-decoration: none;" class="text-primary manageStockModalShowBtnId" id="manageStockModalShowBtnId_<?= $key ?>" data-toggle="modal" data-target="#manageStockModalShowBtn<?= $item['inventory_item_id'] ?>">
                                <i class="fa fa-eye"></i>
                              </a>

                              <!-- Modal -->
                              <div class="modal fade" id="manageStockModalShowBtn<?= $item['inventory_item_id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title"><?= $item['itemName'] ?> ( <span class="setItemQty" id="setItemQty_<?= $key ?>">0</span> <?= $uomName ?>)</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                    </div>
                                    <div class="border">
                                      <p>Total Qty: <span class="catchQty" id="catchQty_<?= $key ?>">0</span></p>
                                      <p>Warehouse: 100022222KSKSK</p>
                                      <p>Location: 100022222KSKSK</p>
                                      <p>Batch No. : 100022222KSKSK</p>
                                    </div>
                                    <div class="modal-body" style="height: 500px; overflow:auto">
                                      <?php
                                      $sql = "SELECT warehouse_id as warehouse FROM `erp_storage_location` WHERE company_id = $company_id AND branch_id = $branch_id AND location_id = $location_id GROUP BY warehouse_id";
                                      $warehouses = queryGet($sql, true);
                                      // console($warehouses);
                                      foreach ($warehouses['data'] as $warehouseKey => $warehouse) {
                                        $warehouseDetails = $BranchSoObj->fetchWarehouseDetails($warehouse['warehouse']);
                                        $warehouseName = $warehouseDetails['data']['warehouse_name'];
                                        // console($warehouseDetails);
                                      ?>
                                        <div>
                                          <div class="card-body">
                                            <!-- warehouse --- accordion //--- -->
                                            <div id="accordionWarehouse">
                                              <div class="border">
                                                <?php if ($warehouseName != '') { ?>
                                                  <div class="p-2 d-flex" style="background:#003060;justify-content: space-between;">
                                                    <a class="card-link" style="color: #fff !important; text-decoration: none;" data-toggle="collapse" href="#collapse<?= $warehouse['warehouse'] ?>">
                                                      <?= $warehouseName ?>
                                                    </a>
                                                    <i class="fa fa-angle-down text-light"></i>
                                                  </div>
                                                <?php } else { ?>
                                                  <div class="p-2 d-flex bg-danger" style="justify-content: space-between;">
                                                    <span>Warehouse details not found!</span>
                                                  </div>
                                                <?php } ?>
                                                <div id="collapse<?= $warehouse['warehouse'] ?>" class="collapse" data-parent="#accordionWarehouse">
                                                  <div class="card-body">
                                                    <?php
                                                    $locationObj = $BranchSoObj->fetchLocationListByWarehouse($warehouse['warehouse']);
                                                    // console($locationObj);
                                                    foreach ($locationObj['data'] as $locationKey => $oneLocation) {
                                                      $batchListObj = $BranchSoObj->fetchBatchListByStorageLocation($oneLocation['storage_location_code'], $item['inventory_item_id']);
                                                      $batchList = $batchListObj['data'];
                                                      console($batchListObj);
                                                      $all_reserve_stock = 0;
                                                      $all_open_stock = 0;
                                                      foreach ($batchList as $oneBatch) {
                                                        // console($oneBatch);  
                                                        $all_reserve_stock += $oneBatch['reserve_stock'];
                                                        $all_open_stock += $oneBatch['open_stock'];
                                                      }
                                                      $colorOpen = "";
                                                      $colorReserve = "";

                                                      if ($all_open_stock > 0) {
                                                        $colorOpen = "success";
                                                      } else if ($all_open_stock == 0) {
                                                        $colorOpen = "secondary";
                                                      } else {
                                                        $colorOpen = "danger";
                                                      }

                                                      if ($all_reserve_stock >= 0) {
                                                        $colorReserve = "secondary";
                                                      } else {
                                                        $colorReserve = "danger";
                                                      }
                                                    ?>
                                                      <!-- storage location accordion //--- -->
                                                      <div id="accordionLocation">
                                                        <div class="border">
                                                          <?php if ($oneLocation['storage_location_name'] == "RM WH Open" || $oneLocation['storage_location_name'] == "RM WH Reserve" || $oneLocation['storage_location_name'] == "FG WH Open") { ?>
                                                            <div class="p-2 d-flex" style="background:#bfddfb;justify-content: space-between;">
                                                              <a class="card-link" style="color: #212121!important; text-decoration: none;" data-toggle="collapse" href="#collapse<?= $oneLocation['storage_location_code'] ?>">
                                                                <?= $oneLocation['storage_location_name'] ?>
                                                                <span class="text-<?= $colorOpen ?>">(OPEN <strong><?= $all_open_stock ?></strong> <?= $uomName ?>)</span>
                                                                <samp class="text-<?= $colorReserve ?>">(RESERVE <strong><?= $all_reserve_stock ?></strong> <?= $uomName ?>)</span>
                                                              </a>
                                                              <i class="fa fa-angle-down"></i>
                                                            </div>
                                                          <?php } else { ?>
                                                            <div class="p-2 d-flex" style="background:#d8d8d8;justify-content: space-between;">
                                                              <a class="card-link" style="color: #212121!important; text-decoration: none;">
                                                                <?= $oneLocation['storage_location_name'] ?>
                                                                <span class="text-<?= $colorOpen ?>">(OPEN <strong class=""><?= $all_open_stock ?></strong> <?= $uomName ?>)</span>
                                                                <samp class="text-<?= $colorReserve ?>">(RESERVE <strong><?= $all_reserve_stock ?></strong> <?= $uomName ?>)</span>
                                                              </a>
                                                              <!-- <i class="fa fa-angle-down"></i> -->
                                                            </div>
                                                          <?php } ?>
                                                          <div id="collapse<?= $oneLocation['storage_location_code'] ?>" class="collapse" data-parent="#accordionLocation">
                                                            <div class="card-body">
                                                              <?php
                                                              if (count($batchList) == 0) {
                                                                echo '<p class="float-left text-danger">Data Not Found!</p>';
                                                              }
                                                              foreach ($batchList as $batchKey => $oneBatch) {
                                                                $modalUniqueRefId = $key . "_" . $warehouseKey . "_" . $locationKey . "_" . $batchKey;
                                                                // console($oneBatch); 
                                                                $statusMasterObj = $BranchSoObj->fetchStatusMaster($oneBatch['itemUom']);
                                                                $statusMaster = $statusMasterObj['data'];
                                                                $statusLabel = $statusMaster['label'] ?? 0;
                                                                $open_stock = 0;
                                                                $reserve_stock = 0;
                                                                if ($oneBatch['open_stock'] != "") {
                                                                  $open_stock = $oneBatch['open_stock'];
                                                                }
                                                                if ($oneBatch['reserve_stock'] != "") {
                                                                  $reserve_stock = $oneBatch['reserve_stock'];
                                                                }
                                                                $refinedLogRef = preg_replace("/[^a-zA-Z0-9]/", "", $oneBatch['logRef']);
                                                                if (intval($open_stock) >= 0 && intval($reserve_stock) >= 0) {
                                                              ?>
                                                                  <div class="text-left border border-dark p-2 my-2 ml-2" style="display: flex;justify-content: space-between; align-items: center;">
                                                                    <span>
                                                                      <?= $oneBatch['logRef'] ?>
                                                                      <p style="font-size: 0.7em !important;"><?= $oneBatch['created_at'] ?></p>
                                                                    </span>
                                                                    <div class="row" style="margin-right: 2% !important;">
                                                                      <?php if ($open_stock > 0) { ?>
                                                                        <div class="col-6 innerModalOpenTarget" id="btnModalOpen-<?= $modalUniqueRefId ?>" style="display: flex;justify-content: space-between;align-items: center; user-select: none;cursor: pointer;" data-toggle="modal" data-target="#modalOpen-<?= $modalUniqueRefId ?>">
                                                                          <input type="checkbox" class="openStockCheckbox" id="openStockCheckbox-<?= $modalUniqueRefId ?>">&nbsp; Open(<strong><?= $open_stock ?></strong><?= $uomName ?>)
                                                                        </div>
                                                                      <?php } else { ?>
                                                                        <div class="col-6" style="background: #f0f0f0;padding: 5px 8px;border-radius: 4px;color: gray;display: flex;justify-content: space-between;align-items: center; user-select: none;">
                                                                          <input type="checkbox" disabled>&nbsp; Open(<strong><?= $open_stock ?></strong><?= $uomName ?>)
                                                                        </div>
                                                                      <?php } ?>
                                                                      <?php if ($reserve_stock > 0) { ?>
                                                                        <div class="col-6 innerModalReserveTarget" id="btnModalReserve-<?= $modalUniqueRefId ?>" style="display: flex;justify-content: space-between;align-items: center; user-select: none;cursor: pointer;" data-toggle="modal" data-target="#modalReserve-<?= $modalUniqueRefId ?>">
                                                                          <input type="checkbox" class="reserveStockCheckbox" id="reserveStockCheckbox-<?= $modalUniqueRefId ?>">&nbsp; Reserve(<strong><?= $reserve_stock ?></strong><?= $uomName ?>)
                                                                        </div>
                                                                      <?php } else { ?>
                                                                        <div class="col-6" style="background: #f0f0f0;padding: 5px 8px;border-radius: 4px;color: gray;display: flex;justify-content: space-between;align-items: center; user-select: none;">
                                                                          <input type="checkbox" disabled>&nbsp; Reserve(<strong><?= $reserve_stock ?></strong><?= $uomName ?>)
                                                                        </div>
                                                                      <?php } ?>
                                                                      <!-- Inner open Modal -->
                                                                      <div class="modal fade" id="modalOpen-<?= $modalUniqueRefId ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                          <div class="modal-content" style="max-width: 50%; margin-left: 20%;">
                                                                            <div class="modal-header">
                                                                              <h6 style="font-size:1.2em" class="modal-title"><?= $oneBatch['logRef'] ?></h6>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                              <p>Open(<?= $open_stock ?>)</p>
                                                                              <input type="hidden" value="<?= $open_stock ?>" id="openStockValue-<?= $modalUniqueRefId ?>">
                                                                              <input type="text" value="<?= $open_stock ?>" class="form-control openStockInpValue" id="openStockInpValue-<?= $modalUniqueRefId ?>" placeholder="0.00">
                                                                              <span style="display: none; font-size: 0.9em;" class="text-xs text-danger openStockMessageSpan" id="openStockMessageSpan-<?= $modalUniqueRefId ?>"></span>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                              <button type="button" class="btn btn-primary btmSubmitModalOpen" id="btmSubmitModalOpen-<?= $modalUniqueRefId ?>">OK</button>
                                                                            </div>
                                                                          </div>
                                                                        </div>
                                                                      </div>
                                                                      <!-- Inner reserve Modal -->
                                                                      <div class="modal fade" id="modalReserve-<?= $modalUniqueRefId ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                          <div class="modal-content" style="max-width: 50%; margin-left: 20%;">
                                                                            <div class="modal-header">
                                                                              <h6 style="font-size:1.2em" class="modal-title"><?= $oneBatch['logRef'] ?></h6>

                                                                            </div>
                                                                            <div class="modal-body">
                                                                              <p>Reserve(<?= $reserve_stock ?>)</p>
                                                                              <input type="hidden" id="reserveStockValue-<?= $modalUniqueRefId ?>" value="<?= $reserve_stock ?>">
                                                                              <input type="text" value="<?= $reserve_stock ?>" class="form-control reserveStockInpValue" id="reserveStockInpValue-<?= $modalUniqueRefId ?>" placeholder="0.00">
                                                                              <span style="display: none; font-size: 0.9em;" class="text-xs text-danger reserveStockMessageSpan" id="reserveStockMessageSpan-<?= $modalUniqueRefId ?>"></span>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                              <button type="button" class="btn btn-primary reserveStockBtn" id="btmSubmitModalReserve_<?= $modalUniqueRefId ?>">OK</button>
                                                                            </div>
                                                                          </div>
                                                                        </div>
                                                                      </div>
                                                                    </div>
                                                                  </div>
                                                              <?php }
                                                              } ?>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      </div>
                                                      <!-- storage location accordion //--- -->
                                                    <?php } ?>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      <?php } ?>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                      <button type="button" class="btn btn-primary">Save</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </td>
                            <td>
                              <!-- <input type="hidden" name="listItem[<?= $key ?>][blockStock]" value="<?= $mainItemDetails['itemBlockStocks'] ?>"> -->
                              <!-- <div class="text-muted font-weight-bold"><?= $mainItemDetails['itemBlockStocks'] ?></div> -->
                              <!-- <div class="text-muted font-weight-bold"><?= $fgWhReserve ?? 0 ?></div> -->
                            </td>
                            <td>
                              <div>
                                <select name="listItem[<?= $key ?>][itemDeliveryDateId]" class="form-control text-center deliveryScheduleQty" id="deliveryScheduleQty_<?= $key ?>">
                                  <option value="">Date ></option>
                                  <?php
                                  foreach ($deliverySchedule as $dSchedule) {
                                    if ($dSchedule['remainingQty'] != null) {
                                  ?>
                                      <option value="<?= $dSchedule['so_delivery_id'] ?>" data-quantity="<?= $dSchedule['remainingQty'] ?>" data-deliveryDate="<?= $dSchedule['delivery_date'] ?>"><?= $dSchedule['delivery_date'] ?> / (<span class="span"><?= $dSchedule['remainingQty'] ?></span> <?= $uomName ?>)</option>
                                    <?php
                                    } else {
                                    ?>
                                      <option value="<?= $dSchedule['so_delivery_id'] ?>" data-quantity="<?= $dSchedule['qty'] ?>" data-deliveryDate="<?= $dSchedule['delivery_date'] ?>"><?= $dSchedule['delivery_date'] ?> / (<span class="span"><?= $dSchedule['qty'] ?></span> <?= $uomName ?>)</option>
                                  <?php
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
                              <input type="text" name="listItem[<?= $key ?>][qty]" class="form-control full-width itemQty" id="itemQty_<?= $key ?>" readonly>
                              <input type="hidden" name="listItem[<?= $key ?>][uom]" value="<?= $item['uom'] ?>">
                              <?= $uomName ?>
                            </td>
                            <td class="action-flex-btn">
                              <a class="btn btn-danger delItemBtn" id="delItemBtn_<?= $item['so_item_id'] ?>">
                                <i class="fa fa-minus"></i>
                              </a>


                            </td>
                          </tr>

                      <?php
                        }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <button type="submit" name="addNewSODeliveryFormSubmitBtn" onclick="return confirm('Are you sure to submit?')" class="btn btn-primary mt-3 mb-2 float-right" id="deliveryCreationBtn">Final Submit</button>
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
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-2 col-md-2 col-sm-12">
                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-12">
                      <div class="row table-header-item">
                        <div class="col-lg-11 col-md-11 col-sm-11">
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
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td>
                                <p class="status"><?= $oneSoList['deliveryStatus'] ?></p>
                              </td>
                            <?php }
                            if (in_array(6, $settingsCheckbox)) { ?>
                              <td><?= $oneSoList['totalItems'] ?></td>
                            <?php } ?>
                            <td>
                              <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneSoList['so_delivery_id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                            </td>
                          </tr>
                          <!-- right modal start here  -->
                          <div class="modal fade right so-delivery-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $oneSoList['so_delivery_id'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                              <!--Content-->
                              <div class="modal-content">
                                <!--Header-->
                                <div class="modal-header">

                                  <div class="customer-head-info">
                                    <div class="customer-name-code">
                                      <h2 style="font-size: 22px;"><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= number_format($oneSoList['totalAmount'], 2) ?></h2>
                                      <p class="heading lead"><?= $oneSoList['so_number'] ?></p>
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
                                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" role="tab" aria-controls="home" aria-selected="true">Item Info</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" role="tab" aria-controls="profile" aria-selected="false">Customer Info</a>
                                      </li>
                                      <!-- -------------------Audit History Button Start------------------------- -->
                                      <li class="nav-item">
                                        <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" href="#history<?= str_replace('/', '-', $oneSoList['delivery_no']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $oneSoList['delivery_no'])  ?>" aria-selected="false">Trail</a>
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
                                      // console('itemDeliveryDetails********');
                                      // console($itemDeliveryDetails);
                                      foreach ($itemDeliveryDetails as $oneItem) {
                                        // console($oneItem);
                                        $unitPrice = $oneItem['unitPrice'] * $conversion_rate;
                                        $itemTotalDiscount = $oneItem['itemTotalDiscount'] * $conversion_rate;
                                        $totalPrice = $oneItem['totalPrice'] * $conversion_rate;
                                        $subTotalAmt = ($unitPrice * $oneItem['qty']) - $itemTotalDiscount;
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
                                                    <h4><?= $oneItem['itemCode'] ?></h4>
                                                    <p><?= $oneItem['itemName'] ?></p>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="col-lg-4 col-md-4 col-sm-4">
                                                <div class="right-section">
                                                  <div class="font-weight-bold">
                                                    <span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span> <?= number_format($subTotalAmt, 2) ?>
                                                  </div>
                                                  <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $unitPrice ?> * <?= $oneItem['qty'] ?> <?= $oneItem['uom'] ?></p>
                                                  <!-- <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $unitPrice * $oneItem['qty'] ?></p> -->
                                                  <div class="discount">
                                                    <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $unitPrice * $oneItem['qty'] ?></p>
                                                    (-<?= $oneItem['totalDiscount'] ?>%)
                                                  </div>
                                                  <p style="border-top: 1px solid;">(GST: <?= $oneItem['tax'] ?>%)</p>
                                                  <div class="font-weight-bold">
                                                    <span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span> <?= number_format($totalPrice, 2) ?>
                                                  </div>
                                                  <!-- <div class="discount">
                                                    <p><?= $itemTotalDiscount ?></p>
                                                    (-<?= $oneItem['totalDiscount'] ?>%)
                                                  </div> -->
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
                                        // console($customerDetails);
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

    $(".manageStockModalShowBtnId").on("click", function() {
      let rowKey = ($(this).attr("id")).split("_")[1];
      let deliveryQty = getIntValue($(`#itemQty_${rowKey}`).val());
      console.log('deliveryQty')
      console.log(rowKey, deliveryQty)
      $(`#setItemQty_${rowKey}`).text(deliveryQty);
    });

    // innerModalOpenTarget
    $(".innerModalOpenTarget").on("click", function() {
      let modalUniqueRef = ($(this).attr("id")).split("-")[1];
      let deliveryQty = getIntValue($(`#itemQty_${modalUniqueRef.split("_")[0]}`).val());
      let modalOpenQty = getIntValue($(`#openStockValue-${modalUniqueRef}`).val());

      $(`#openStockInpValue-${modalUniqueRef}`).val(Math.min(deliveryQty, modalOpenQty));
    });

    // innerModalReserveTarget
    $(".innerModalReserveTarget").on("click", function() {
      let modalUniqueRef = ($(this).attr("id")).split("-")[1];
      let deliveryQty = getIntValue($(`#itemQty_${modalUniqueRef.split("_")[0]}`).val());
      let modalReserveQty = getIntValue($(`#reserveStockValue-${modalUniqueRef}`).val());

      $(`#reserveStockInpValue-${modalUniqueRef}`).val(Math.min(deliveryQty, modalReserveQty));
    });

    // open stock change input value on keyup 
    $(".openStockInpValue").on("keyup", function() {
      let inpVal = getIntValue($(this).val());
      let modalUniqueRef = ($(this).attr("id")).split("-")[1];
      let deliveryQty = getIntValue($(`#itemQty_${modalUniqueRef.split("_")[0]}`).val());
      let modalOpenQty = getIntValue($(`#openStockValue-${modalUniqueRef}`).val());

      if (inpVal > deliveryQty) {
        $(`#openStockInpValue-${modalUniqueRef}`).val('');
        $(`#openStockMessageSpan-${modalUniqueRef}`).show();
        $(`#openStockMessageSpan-${modalUniqueRef}`).text();
        $(`#openStockMessageSpan-${modalUniqueRef}`).text('Please enter valid delivery qty');
      } else if (inpVal > modalOpenQty) {
        $(`#openStockInpValue-${modalUniqueRef}`).val('');
        $(`#openStockMessageSpan-${modalUniqueRef}`).show();
        $(`#openStockMessageSpan-${modalUniqueRef}`).text();
        $(`#openStockMessageSpan-${modalUniqueRef}`).text('Please enter valid qty. (open stock)');
      } else {
        $(`#openStockMessageSpan-${modalUniqueRef}`).hide();
        $(`#openStockMessageSpan-${modalUniqueRef}`).text('');
        $(`#openStockInpValue-${modalUniqueRef}`).val(inpVal);
      }
    });

    // reserve stock change input value on keyup 
    $(".reserveStockInpValue").on("keyup", function() {
      let inpVal = getIntValue($(this).val());
      let modalUniqueRef = ($(this).attr("id")).split("-")[1];
      let deliveryQty = getIntValue($(`#itemQty_${modalUniqueRef.split("_")[0]}`).val());
      let modalReserveQty = getIntValue($(`#reserveStockValue-${modalUniqueRef}`).val());

      if (inpVal > deliveryQty) {
        $(`#reserveStockInpValue-${modalUniqueRef}`).val('');
        $(`#reserveStockMessageSpan-${modalUniqueRef}`).show();
        $(`#reserveStockMessageSpan-${modalUniqueRef}`).text();
        $(`#reserveStockMessageSpan-${modalUniqueRef}`).text('Please enter valid delivery qty');
      } else if (inpVal > modalReserveQty) {
        $(`#reserveStockInpValue-${modalUniqueRef}`).val('');
        $(`#reserveStockMessageSpan-${modalUniqueRef}`).show();
        $(`#reserveStockMessageSpan-${modalUniqueRef}`).text();
        $(`#reserveStockMessageSpan-${modalUniqueRef}`).text('Please enter valid qty. (reserve stock)');
      } else {
        $(`#reserveStockMessageSpan-${modalUniqueRef}`).hide();
        $(`#reserveStockMessageSpan-${modalUniqueRef}`).text('');
        $(`#reserveStockInpValue-${modalUniqueRef}`).val(inpVal);
      }
    });

    // btmSubmitModalOpen ok 
    $('.btmSubmitModalOpen').on("click", function() {
      let modalUniqueRef = ($(this).attr("id")).split("-")[1];
      let openInpVal = $(`#openStockInpValue-${modalUniqueRef}`).val();
      console.log('openInpVal')
      console.log(openInpVal)

      let totalOpenSum = 0;
      $(".openStockInpValue").each(function() {
        if ($('.openStockCheckbox').is(":checked")) {
          let totalOpen = getIntValue($(this).val());
          totalOpenSum += totalOpen;
        }
      });

      $(`#catchQty_${modalUniqueRef.split("_")[0]}`).text(totalOpenSum);
    });

    // manageStockModalShowBtn
    $('#manageStockModalShowBtn').modal({
      backdrop: 'static', // Optional: Disable clicking outside modal to close
      keyboard: false // Optional: Disable keyboard interaction
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

<script src="<?= BASE_URL; ?>public/validations/deliveryCreationValidation.js"></script>