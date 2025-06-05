<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-customers-controller.php");

// console($_SESSION);

// exit();
//COMPANY COUNTRY
//$get_state = queryGet("SELECT * FROM `erp_companies` WHERE company_id = $company_id");
// console($get_state);
// if($companyCountry == 'Not India') {
//     //echo 'not indiaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
// }
// else{
//    // echo 'Indiaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
// }

// date checker
$check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];
// console($check_var_data);

$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];

$quotation_createion = isset($_GET['quotation_creation']);
$sales_order_creation = isset($_GET['sales_order_creation']);
$quotation_to_so = isset($_GET['quotation_to_so']);
$create_service_invoice = isset($_GET['create_service_invoice']);
$so_to_invoice = isset($_GET['so_to_invoice']);
$pgi_to_invoice = isset($_GET['pgi_to_invoice']);
$party_order_to_so = isset($_GET['party_order_to_so']);
$party_order_to_quotation = isset($_GET['party_order_to_quotation']);
$proforma_invoice = isset($_GET['proforma_invoice']);
$repost_invoice = isset($_GET['repost_invoice']);
$edit_invoice = isset($_GET['edit_invoice']);
$edit_so = isset($_GET['edit_so']);
$proforma_to_so = isset($_GET['proforma_to_so']);
$proforma_to_invoice = isset($_GET['proforma_to_invoice']);


$invoiceType = 'direct';

if (isset($_GET['create_service_invoice'])) {
  $invoiceType = "service";
} elseif (isset($_GET['quotation'])) {
  $invoiceType = "quotation_to_invoice";
} elseif (isset($_GET['so_to_invoice'])) {
  $invoiceType = "so_to_invoice";
} elseif (isset($_GET['joborder_to_invoice'])) {
  $invoiceType = "project";
} elseif (isset($_GET['pgi_to_invoice'])) {
  $invoiceType = "pgi_to_invoice";
} elseif (isset($_GET['party_order_to_so'])) {
  $invoiceType = "party_order_to_so";
} elseif (isset($_GET['party_order_to_quotation'])) {
  $invoiceType = "party_order_to_quotation";
} elseif (isset($_GET['proforma_invoice'])) {
  $invoiceType = "proforma_invoice";
} elseif (isset($_GET['repost_invoice'])) {
  $invoiceType = "repost_invoice";
} elseif (isset($_GET['edit_invoice'])) {
  $invoiceType = "edit_invoice";
} elseif (isset($_GET['edit_so'])) {
  $invoiceType = "edit_so";
} elseif (isset($_GET['proforma_to_invoice'])) {
  $invoiceType = "proforma_to_invoice";
}




if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}



$BranchSoObj = new BranchSo();
$CustomersObj = new CustomersController();

$quotationsJson = '';
$itemIdJson = '';
$customerId = 0;
$pgiCode = '';

$extraremarks = '';
$declaration = '';
$CreditPeriod = '';
$SalesPerson = '';
$FunctionalArea = '';
$SelectBank = '';

// quotation creation
if (isset($_GET['quotation'])) {
  $quotation_id = base64_decode($_GET['quotation']);
  $quotationList = $BranchSoObj->getQuotations($quotation_id);
  $quotationItemList = $BranchSoObj->getQuotationItems($quotation_id);

  $customerId = $quotationList['data']['customer_id'];

  $quotationsJson = json_encode($quotationList['data']);
  $itemIdJson = json_encode($quotationItemList['data']);
}

// quotation to sales-order creation
if (isset($_GET['quotation_to_so'])) {
  $quotation_id = base64_decode($_GET['quotation_to_so']);
  $quotationList = $BranchSoObj->getQuotations($quotation_id);
  $quotationItemList = $BranchSoObj->getQuotationItems($quotation_id);
  $quotationPostingDate = $quotationList['posting_date'];
  $customerId = $quotationList['data']['customer_id'];
  $quotationGoodsType = $quotationList['data']['goodsType'];
  $quotationsJson = json_encode($quotationList['data']);
  $itemIdJson = json_encode($quotationItemList['data']);

  $validityPeriod = $quotationList['data']['validityperiod'];
  if ($validityPeriod != null && $validityPeriod != "") {
    $dateTimeNow = new DateTime();
    $validityDate = new DateTime($validityPeriod);
    $dateTimeNow->setTime(0, 0, 0);
    $validityDate->setTime(0, 0, 0);
    if ($validityDate < $dateTimeNow) {
      swalAlert("warning", 'Expired', "Quotation is expired", 'manage-quotations.php');
    }
  }
}

// repost invoice creation
if (isset($_GET['repost_invoice'])) {
  $invoiceId = base64_decode($_GET['repost_invoice']);
  $invoiceList = $BranchSoObj->fetchInvoiceDetails($invoiceId);
  $invoiceItemList = $BranchSoObj->fetchInvoiceItems($invoiceId);
  $inv_variant_id = $invoiceList['data']['inv_variant_id'];

  $customerId = $invoiceList['data']['customer_id'];
  $repost_invoice_no = $invoiceList['data']['invoice_no'];

  $extraremarks = $invoiceList['data']['remarks'];
  $declaration = $invoiceList['data']['declaration_note'];

  $CreditPeriod = $invoiceList['data']['credit_period'];
  $SalesPerson = $invoiceList['data']['kamId'];
  $FunctionalArea = $invoiceList['data']['profit_center'];
  $SelectBank = $invoiceList['data']['customer_id'];

  $InvoiceJson = json_encode($invoiceList['data']);
  $itemIdJson = json_encode($invoiceItemList['data']);
}
//so edit
if (isset($_GET['edit_so'])) {
  // console($_GET);
  // exit();
  $invoiceId = base64_decode($_GET['edit_so']);
  $invoiceList = $BranchSoObj->fetchSoDetailsById($invoiceId);
  $invoiceItemList = $BranchSoObj->fetchBranchSoItems($invoiceId);

  //  console($invoiceList);
  //  console($invoiceItemList);
  $customerId = $invoiceList['data'][0]['customer_id'];
  $CreditPeriod = $invoiceList['data'][0]['credit_period'];

  $SalesPerson = $invoiceList['data'][0]['kamId'];
  $FunctionalArea = $invoiceList['data'][0]['profit_center'];

  $InvoiceJson = json_encode($invoiceList['data']);
  $itemIdJson = json_encode($invoiceItemList['data']);
}

// edit invoice creation
if (isset($_GET['edit_invoice'])) {
  $invoiceId = base64_decode($_GET['edit_invoice']);
  $invoiceList = $BranchSoObj->fetchInvoiceDetails($invoiceId);
  $invoiceItemList = $BranchSoObj->fetchInvoiceItems($invoiceId);

  $customerId = $invoiceList['data']['customer_id'];

  $InvoiceJson = json_encode($invoiceList['data']);
  $itemIdJson = json_encode($invoiceItemList['data']);
}

// party_order_to_so creation
if (isset($_GET['party_order_to_so'])) {
  $partyOrderId = base64_decode($_GET['party_order_to_so']);
  $partyOrderList = $BranchSoObj->getPartyOrders($partyOrderId);
  $partyOrderItemList = $BranchSoObj->getPartyOrderItems($partyOrderId);
  $partyOrderPostingDate = $partyOrderList['posting_date'];

  $customerId = $partyOrderList['data']['customer_id'];
  $partyOrderGoodsType = $partyOrderList['data']['goodsType'];

  $quotationGoodsType = $partyOrderList['data']['goodsType'] ?? 'material';

  $partyOrderJson = json_encode($partyOrderList['data']);
  $itemIdJson = json_encode($partyOrderItemList['data']);
}

// party_order_to_quotation creation
if (isset($_GET['party_order_to_quotation'])) {
  $partyOrderId = base64_decode($_GET['party_order_to_quotation']);
  $partyOrderList = $BranchSoObj->getPartyOrders($partyOrderId);
  $partyOrderItemList = $BranchSoObj->getPartyOrderItems($partyOrderId);
  $partyOrderPostingDate = $partyOrderList['posting_date'];

  $customerId = $partyOrderList['data']['customer_id'];
  $partyOrderGoodsType = $partyOrderList['data']['goodsType'];

  $partyOrderJson = json_encode($partyOrderList['data']);
  $itemIdJson = json_encode($partyOrderItemList['data']);
}

$so_id = 0;
// sales-order to invoice creation
if (isset($_GET['so_to_invoice'])) {
  $so_id = base64_decode($_GET['so_to_invoice']);
  $soList = $BranchSoObj->fetchSalesOrderById($so_id);
  $soItemList = $BranchSoObj->getSalesOrderItems($so_id);

  $customerId = $soList['data']['customer_id'];

  $soJson = json_encode($soList['data']);
  $itemIdJson = json_encode($soItemList['data']);
}

// sales-order to invoice creation
if (isset($_GET['joborder_to_invoice'])) {
  $so_id = base64_decode($_GET['joborder_to_invoice']);
  $soList = $BranchSoObj->fetchSalesOrderById($so_id);
  // $soItemList = $BranchSoObj->getSalesOrderItems($so_id);
  // $soItemList = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` WHERE so_id='" . $so_id . "' AND completion_value!=0 AND invStatus='pending'", true);
  $soItemList = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` WHERE so_id='" . $so_id . "' AND invStatus='pending'", true);

  $customerId = $soList['data']['customer_id'];

  $soJson = json_encode($soList['data']);
  $itemIdJson = json_encode($soItemList['data']);
}

// pgi to invoice creation
if (isset($_GET['pgi_to_invoice'])) {
  $pgi_id = base64_decode($_GET['pgi_to_invoice']);
  $pgiList = $BranchSoObj->fetchPGIById($pgi_id);
  $pgiItemList = $BranchSoObj->getPGIItems($pgi_id);

  $customerId = $pgiList['data']['customer_id'];

  $pgiCode = $pgiList['data']['pgi_no'];
  $itemIdJson = json_encode($pgiItemList['data']);
}


// proforma inv to so
if (isset($_GET['proforma_to_so'])) {
  $dbObj = new Database();
  $proformaId = base64_decode($_GET['proforma_to_so']);

  $proformaSql = "SELECT * FROM `erp_proforma_invoices` as proformaInv WHERE proformaInv.proforma_invoice_id=$proformaId AND proformaInv.company_id=$company_id AND proformaInv.branch_id=$branch_id AND proformaInv.location_id=$location_id AND proformaInv.status='active'";
  $proformaItemListSql = "SELECT proInvItem.*, inventory.goodsType FROM `erp_proforma_invoice_items` AS proInvItem JOIN `erp_inventory_items` AS inventory ON inventory.itemId = proInvItem.inventory_item_id WHERE proInvItem.proforma_invoice_id =$proformaId  AND proInvItem.status = 'active' AND inventory.company_id = $company_id";

  $proformaList = $dbObj->queryGet($proformaSql);
  $proformaItemList = $dbObj->queryGet($proformaItemListSql, true);

  $goodsType = [
    "2" => "good",
    "3" => "good",
    "4" => "good",
    "5" => "service",
    "7" => "service",
    "9" => "asset",
    "10" => "project",
  ];

  $quotationGoodsType = "";
  $isServiceCount = 0;
  $isGoodCount = 0;
  $isProjectCount = 0;

  foreach ($proformaItemList['data'] as $key => $dataObj) {
    $type = $goodsType[$dataObj['goodsType']];
    if ($type == 'service') {
      $isServiceCount++;
    } else if ($type == 'good') {
      $isGoodCount++;
    } else if ($type == 'project') {
      $isProjectCount++;
    }
  }

  if ($isProjectCount > 0) {
    $quotationGoodsType = "project";
  } else {
    if ($isGoodCount > 0) {
      $quotationGoodsType = "material";
    } else if ($isServiceCount > 0 && $isGoodCount == 0) {
      $quotationGoodsType = "service";
    }
  }

  $quotationPostingDate = $proformaList['data']['invoice_date'];
  $customerId = $proformaList['data']['customer_id'];

  $quotationsJson = json_encode($proformaList['data']);
  $itemIdJson = json_encode($proformaItemList['data']);

  $validityPeriod = $proformaList['data']['validityperiod'];
  if ($validityPeriod != null && $validityPeriod != "") {
    $dateTimeNow = new DateTime();
    $validityDate = new DateTime($validityPeriod);
    $dateTimeNow->setTime(0, 0, 0);
    $validityDate->setTime(0, 0, 0);
    if ($validityDate < $dateTimeNow) {
      swalAlert("warning", 'Expired', "Proforma is expired", 'manage-proforma-invoice.php');
    }
  }
}

// proforma inv to invoice
if (isset($_GET['proforma_to_invoice'])) {
  $dbObj = new Database();
  $proformaId = base64_decode($_GET['proforma_to_invoice']);

  $proformaSql = "SELECT * FROM `erp_proforma_invoices` as proformaInv WHERE proformaInv.proforma_invoice_id=$proformaId AND proformaInv.company_id=$company_id AND proformaInv.branch_id=$branch_id AND proformaInv.location_id=$location_id AND proformaInv.status='active'";
  $proformaItemListSql = "SELECT proInvItem.*, inventory.goodsType FROM `erp_proforma_invoice_items` AS proInvItem JOIN `erp_inventory_items` AS inventory ON inventory.itemId = proInvItem.inventory_item_id WHERE proInvItem.proforma_invoice_id =$proformaId  AND proInvItem.status = 'active' AND inventory.company_id = $company_id";

  $proformaList = $dbObj->queryGet($proformaSql);
  $proformaItemList = $dbObj->queryGet($proformaItemListSql, true);
  $customerId = $proformaList['data']['customer_id'];
  $quotationList = $proformaList;
  $quotationsJson = json_encode($proformaList['data']);
  $itemIdJson = json_encode($proformaItemList['data']);
}

$serviceList = $BranchSoObj->fetchItemServices()['data'];

$company = $BranchSoObj->fetchCompanyDetails()['data'];
$currencyIcon = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_icon'];
$currencyName = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_name'];

$branchGstin = $BranchSoObj->fetchBranchDetailsById($branch_id)['data']['branch_gstin'];
$branchGstinCode = substr($branchGstin, 0, 2);

if ($quotation_createion) {

  if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
    $addGoodsInvoice = $BranchSoObj->insertQuotation($_POST, $_FILES);
    if ($addGoodsInvoice['status'] == "success") {
      swalAlert($addGoodsInvoice["status"], $addGoodsInvoice['quotationNo'], $addGoodsInvoice["message"], 'manage-quotations.php');
    } else {
      swalAlert($addGoodsInvoice["status"], 'warning', $addGoodsInvoice["message"]);
    }
  }
} else if ($party_order_to_so) {
  if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
    $addGoodsInvoice = $BranchSoObj->addBranchSo($_POST, $_FILES);
    if ($addGoodsInvoice['status'] == "success") {
      swalAlert($addGoodsInvoice["status"], $addGoodsInvoice['soNumber'], $addGoodsInvoice["message"], "manage-sales-orders.php");
    } else {
      swalAlert($addGoodsInvoice["status"], 'warning', $addGoodsInvoice["message"]);
    }
  }
} else if ($sales_order_creation || $quotation_to_so || $proforma_to_so) {
  if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
    $addGoodsInvoice = $BranchSoObj->addBranchSo($_POST, $_FILES);
    if ($addGoodsInvoice['status'] == "success") {
      if ($proforma_to_so) {
        $responseClose = $BranchSoObj->closeProformaInvoiceById(base64_decode($_POST['proformaId']));
        if ($responseClose['status'] != "success") {
          swalAlert($responseClose["status"], 'warning', $responseClose["message"]);
        }
      }
      swalAlert($addGoodsInvoice["status"], $addGoodsInvoice['soNumber'], $addGoodsInvoice["message"], "manage-sales-orders.php");
    } else {
      swalAlert($addGoodsInvoice["status"], 'warning', $addGoodsInvoice["message"]);
    }
  }
} else if ($proforma_invoice) {
  if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
    $addProformaInvoice = $BranchSoObj->insertProformaInvoice($_POST, $_FILES);
    if ($addProformaInvoice['status'] == "success") {
      swalAlert($addProformaInvoice["status"], $addProformaInvoice['invoiceNo'], $addProformaInvoice["message"], 'manage-proforma-invoice.php');
    } else {
      swalAlert($addProformaInvoice["status"], 'Warning', $addProformaInvoice["message"]);
    }
  }
} else if ($edit_invoice) {
  if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {

    $editGoodsInvoice = $BranchSoObj->editBranchDirectInvoice($_POST, $_FILES);
    // console($editGoodsInvoice);
    // if ($editGoodsInvoice['status'] == "success") {
    //   swalAlert($editGoodsInvoice["status"], $editGoodsInvoice['invoiceNo'], $editGoodsInvoice["message"], 'manage-invoices.php');
    // } else {
    //   swalAlert($editGoodsInvoice["status"], 'Warning', $editGoodsInvoice["message"]);
    // }`
  }
} else if ($edit_so) {
  if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
    $editBranchSo = $BranchSoObj->editBranchSo($_POST + $_FILES);
    if ($editBranchSo['status'] == 'success') {
      swalAlert($editBranchSo["status"], $editBranchSo["status"], $editBranchSo["message"], LOCATION_URL . 'manage-sales-orders.php');
    } else {
      swalAlert($editBranchSo["status"], $editBranchSo["status"], 'Warning', $editBranchSo["message"]);
    }
  }
} else if ($create_service_invoice) {
  if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
    $addGoodsInvoice = $BranchSoObj->insertBranchDirectInvoice($_POST, $_FILES);
    if ($addGoodsInvoice['status'] == "success") {
      swalAlert($addGoodsInvoice["status"], $addGoodsInvoice['invoiceNo'], $addGoodsInvoice["message"], 'manage-invoices.php');
    } else {
      swalAlert($addGoodsInvoice["status"], 'Warning', $addGoodsInvoice["message"]);
    }
  }
} else {
  if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
    $addGoodsInvoice = $BranchSoObj->insertBranchDirectInvoice($_POST, $_FILES);
    if ($addGoodsInvoice['status'] == "success") {
      if ($proforma_to_invoice) {
        $responseClose = $BranchSoObj->closeProformaInvoiceById(base64_decode($_POST['proformaId']));
        if ($responseClose['status'] != "success") {
          swalAlert($responseClose["status"], 'warning', $responseClose["message"]);
        }
      }
      swalAlert($addGoodsInvoice["status"], $addGoodsInvoice['invoiceNo'], $addGoodsInvoice["message"], 'manage-invoices.php');
    } else {
      swalAlert($addGoodsInvoice["status"], 'Warning', $addGoodsInvoice["message"]);
    }
  }
}

if (isset($_POST["createdatamultiform"])) {
  $addNewObj = $BranchSoObj->createDataCustomer($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}
?>

<style>
  .direct-create-invoice-card {
    height: auto !important;
    min-height: 100%;
    margin-bottom: 2em;
  }

  .direct-create-invoice-card .card-body {
    min-height: 100%;
    height: 330px !important;
  }

  .card.po-vendor-details-view .card-body {
    height: auto !important;
  }

  .advanced-serach .nav-action {
    flex-direction: row;
    gap: 30px;
    width: 35% !important;
  }

  .advanced-serach .form-inline {
    flex-flow: row;
  }

  div#quick-add-input span.select2.select2-container.select2-container--default {
    width: 120px !important;
  }

  .advanced-serach .form-inline select {
    width: 120px !important;
  }

  .static-currency::before,
  .dynamic-currency::before {
    bottom: 25px !important;
  }

  .so-card-body .static-currency input,
  .so-card-body .dynamic-currency input,
  .dynamic-currency select {
    height: 32px !important;
  }

  .card-body.others-info.vendor-info.so-card-body {
    height: 100% !important;
  }

  .modal.add-customer-modal .modal-dialog {
    max-width: 70%;
  }

  .modal.add-customer-modal .modal-dialog .modal-content .modal-body {
    height: 80vh;
  }

  .text-small {
    font-size: 0.8em;
  }

  .text-large {
    font-size: 1.1em;
  }

  .convertedDiv {
    display: none;
  }

  .itemDropdownDiv {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
  }

  .itemDropdownDiv label {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    margin-bottom: 0;
  }

  select.order-for-select {
    width: auto !important;
  }

  .head-item-table #quick-add-input.show {
    transform: translateX(55%) !important;
  }

  .recurringDiv {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
  }

  .round-off-section {
    flex-direction: column;
  }

  div#round_off_hide {
    flex-direction: column;
  }

  .type-flex {
    display: flex;
    justify-content: flex-start;
    gap: 2px;
  }

  .type-flex .invoice-varient-form {
    flex: 1 1 50%;
  }

  .discount-view {
    display: grid;
    grid-template-columns: 2fr 2fr;
    margin-bottom: 10px;
    gap: 15px;
    height: auto;
    max-height: 500px;
    overflow: auto;
  }

  .discount-view .d-flex {
    background: #e8e8e8;
    padding: 5px;
    border-radius: 7px;
  }

  .delivery-discount {
    margin-top: 20px;
  }

  .delivery-discount .nav-tabs {
    justify-content: flex-end;
    margin-right: 30px;
  }

  .delivery-discount .nav-tabs .nav-link,
  .delivery-discount .nav-tabs .nav-link.active {
    padding: 5px 15px;
    font-size: 0.75rem;
    text-align: center;
    color: #000;
    margin: -1px;
    width: auto;
  }

  .delivery-discount .nav-tabs .nav-link.active {
    color: #003060;
    font-weight: 600;
  }

  .modal.discountViewModal .modal-body {
    background: #ececec;
  }

  .modal.discountViewModal .modal-body .tab-content {
    padding-top: 20px;
    background: #fff;
    padding-left: 15px;
    padding-right: 15px;
    border-radius: 7px;
    height: 100%;
  }

  .discount-card {
    background: #003060;
    border-radius: 7px;
    padding: 11px 15px;
    white-space: normal;
    color: #fff;
    line-height: 1.7;
    position: relative;
    overflow: hidden;
  }

  .discount-card::after {
    content: '';
    display: inline-block;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background-color: #f7f7f71a;
    position: absolute;
    bottom: -45%;
    left: -76px;
  }

  .discount-card::before {
    content: '';
    display: inline-block;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background-color: #f7f7f71a;
    position: absolute;
    top: -56%;
    right: -98px;
  }

  .discount-card .validity-days {
    background: transparent;
    padding: 4px 0;
    margin: 7px 0;
    border-top: 1px solid #ccc;
    border-bottom: 1px solid #ccc;
    border-radius: 0;
  }

  .discount-card label {
    font-size: 0.6rem !important;
    color: #ccc;
  }

  .discount-card p {
    font-size: 0.7rem;
  }

  .invoice-field {
    display: flex;
    align-items: flex-start;
    flex-direction: column;
  }
</style>


<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.3/dist/js/bootstrap.bundle.min.js"></script>

<input type="hidden" value="<?= $branchGstinCode ?>" class="branchGstin" id="branchGstin">

<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
        <?php if ($sales_order_creation) { ?>
          <li class="breadcrumb-item active"><a href="manage-sales-orders.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Orders List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Sales Orders</a></li>
        <?php } else if ($proforma_invoice) { ?>
          <li class="breadcrumb-item active"><a href="manage-proforma-invoice.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Proforma Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Proforma Invoice</a></li>
        <?php } else if ($create_service_invoice) { ?>
          <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Service Invoice</a></li>
        <?php } else if ($quotation_createion) { ?>
          <li class="breadcrumb-item active"><a href="manage-quotations.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Quotation List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Quotation</a></li>
        <?php } else if (isset($_GET['quotation'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Quotation to Invoice</a></li>
        <?php } else if (isset($_GET['party_order_to_so'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-party-orders.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Party Order List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Party Order to Sales Order</a></li>
        <?php } else if (isset($_GET['party_order_to_quotation'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-party-orders.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Party Quotation List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Party Order to Quotation</a></li>
        <?php } else if (isset($_GET['quotation_to_so'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-sales-orders.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Order List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Quotation to Sales Order</a></li>
        <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create PGI to Invoice</a></li>
        <?php } else if (isset($_GET['repost_invoice'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Repost Invoice</a></li>
        <?php } else if (isset($_GET['edit_invoice'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Edit Invoice</a></li>
        <?php } else if (isset($_GET['edit_so'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>SO List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Edit SO</a></li>
        <?php } else if (isset($_GET['proforma_to_so'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-sales-orders.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Order List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Proforma to Sales Order</a></li>
        <?php } else if (isset($_GET['proforma_to_invoice'])) { ?>
          <li class="breadcrumb-item active"><a href="manage-proforma-invoice.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Proforma Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Proforma to Invoice</a></li>
        <?php } else { ?>
          <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
          <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Goods Invoice</a></li>
        <?php } ?>
        <li class="back-button">
          <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
            <i class="fa fa-reply po-list-icon"></i>
          </a>
        </li>
      </ol>
    </div>
    <span style="display: none;" class="companyCurrencyName"><?= $currencyName ?></span>
    <form action="" method="POST" id="addNewSOForm" enctype="multipart/form-data">
      <input type="hidden" name="invoice_id" value="<?= $invoiceId ?>">
      <?php if (isset($_GET['joborder_to_invoice'])) { ?>
        <input type="hidden" value="<?= $so_id ?>" name="so_id">
      <?php } ?>
      <input type="hidden" value="<?= $invoiceType ?>" name="ivType">
      <input type="hidden" value="<?= $currencyName ?>" name="currencyName" class="currencyName">
      <?php if (isset($_GET['quotation_to_so'])) { ?>
        <input type="hidden" value="<?= $_GET['quotation_to_so'] ?>" name="quotationId" class="quotation_to_so">
      <?php } else if (isset($_GET['quotation'])) { ?>
        <input type="hidden" value="<?= $_GET['quotation'] ?>" name="quotationId" class="quotation_to_so">
      <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
        <input type="hidden" value="<?= $_GET['pgi_to_invoice'] ?>" name="pgi_to_invoice" class="pgi_to_invoice">
        <input type="hidden" value="<?= $pgiCode ?>" name="pgiCode" class="pgi_no">
      <?php } else if (isset($_GET['so_to_invoice'])) { ?>
        <input type="hidden" value="<?= $_GET['so_to_invoice'] ?>" name="so_to_invoice" class="so_to_invoice">
      <?php } else if (isset($_GET['proforma_to_so'])) { ?>
        <input type="hidden" value="<?= $_GET['proforma_to_so'] ?>" name="proformaId" class="quotation_to_so">
      <?php } else if (isset($_GET['proforma_to_invoice'])) { ?>
        <input type="hidden" value="<?= $_GET['proforma_to_invoice'] ?>" name="proformaId" class="quotation_to_so">
      <?php }  ?>


      <div class="row main-create-template">
        <div class="col-lg-6 col-md-6 col-sm-12">
          <div class="card direct-create-invoice-card so-creation-card">
            <div class="card-header">
              <div class="row customer-info-head">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="head">
                    <i class="fa fa-user"></i>
                    <h4>Customer Info</h4>
                    <input type="hidden" class="customerIdInp" value="0">
                    <input type="hidden" name="shipToLastInsertedId" value="0" id="shipToLastInsertedId">
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body others-info vendor-info so-card-body <?php if ($edit_invoice && $edit_so) {
                                                                          echo "displayOverLay";
                                                                        } ?>">
              <!-- <div class="overlay" style="position: absolute;background: rgba(0,0,0,0.5);width: 100%;height: 100%;"></div> -->
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="row customer-info-form-view">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="input-box customer-select">
                        <span class="text-danger">*</span>
                        <?php if (isset($_GET['quotation']) || isset($_GET['quotation_to_so']) || isset($_GET['so_to_invoice']) || isset($_GET['party_order_to_so']) || isset($_GET['party_order_to_quotation']) || isset($_GET['pgi_to_invoice']) ||  isset($_GET['edit_invoice']) || $_GET['edit_so'] || isset($_GET['proforma_to_so']) || isset($_GET['proforma_to_invoice'])) { ?>
                          <?php
                          $getCustomerObj = $CustomersObj->getDataCustomerDetails($customerId);
                          $customerName = $getCustomerObj['data'][0]['trade_name'];
                          $customer_id = $getCustomerObj['data'][0]['customer_id'];
                          $customer_code = $getCustomerObj['data'][0]['customer_code'];
                          ?>
                          <input type="text" name="customerName" class="form-control" value="<?= $customerName ?>" readonly>
                          <input type="hidden" name="customerId" id="customerDropDown" class="form-control" value="<?= $customer_id ?>" readonly>
                        <?php } else if (isset($_GET['repost_invoice'])) {
                          $getCustomerObj = $CustomersObj->getDataCustomerDetails($customerId);
                          $customerName = $getCustomerObj['data'][0]['trade_name'];
                          $customer_code = $getCustomerObj['data'][0]['customer_code'];
                          $customer_id = $getCustomerObj['data'][0]['customer_id'];
                        ?>
                          <select name="customerId" id="customerDropDown" class="form-control select2" required>
                            <option value="">Select Customer</option>
                            <!-- <option value="<?= $customerName ?>" selected><?= $customer_code ?> | <?= $customerName ?></option> -->
                            <option value="<?= $customer_id ?>" selected><?= $customer_code ?> | <?= $customerName ?></option>
                          </select>
                        <?php } else { ?>
                          <select name="customerId" id="customerDropDown" class="form-control select2" required>
                            <option value="">Select Customer</option>
                          </select>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <div class="customer-info-text" id="customerInfo">
                        <div class="watermark">

                        </div>
                      </div>
                    </div>
                    <div>
                      <div class="form-input">
                        <label for="">Place of supply <span class="text-danger">*</span></label>
                        <select name="placeOfSupply" class="form-control select2" id="placeOfSupply1" required>
                          <option value="">Place of supply</option>
                          <?php
                          $stateNameList = fetchStateName()['data'];
                          // Custom comparison function for sorting
                          function compareByStateCode($a, $b)
                          {
                            return $a['gstStateCode'] - $b['gstStateCode'];
                          }
                          usort($stateNameList, 'compareByStateCode');
                          foreach ($stateNameList as $one) {
                          ?>
                            <option value="<?= ltrim($one['gstStateCode'], '0') ?>"><?= $one['gstStateCode'] ?> - <?= $one['gstStateName'] ?></option>
                          <?php } ?>
                        </select>
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
            <div class="card-body sales-order-creation-direct others-info vendor-info so-card-body pt-0">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">

                  <input type="hidden" value="14" name="approvalStatus" id="approvalStatus">
                  <?php if ($repost_invoice) { ?>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <label>Invoice No: <?= $repost_invoice_no ?><span class="text-danger">*</span></label>
                      <div>

                        <input type="hidden" name="repostInvoiceId" class="form-control" id="repostInvoiceId" value="<?= base64_decode($_GET['repost_invoice']) ?>" />
                        <input type="hidden" name="repostInvoiceNo" value="<?= $repost_invoice_no ?>" id="repostInvoiceNo" class="form-control" readonly />
                        <span class="input-group-addon"></span>
                      </div>
                    </div>
                  <?php } ?>
                  <?php if ($quotation_createion) { ?>
                    <div class="row others-info-form-view" style="row-gap: 5px; justify-content: center;">
                      <div class="col-lg-6 col-md-6 col-sm-6">
                        <label>Posting Date: <span class="text-danger">*</span></label>
                        <div>
                          <input type="date" name="postingDate" value="" id="postingDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>" required />
                          <span class="input-group-addon"></span>
                        </div>
                      </div>
                      <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                        <label for="" class="label-hidden">label</label>
                        <div class="static-currency mt-2">
                          <input type="text" class="form-control" value="1" readonly="">
                          <input type="text" class="form-control text-right" value="INR" readonly="">
                        </div>
                      </div> -->

                      <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="form-input">
                          <label for="date">Validity Period: <span class="text-danger">*</span></label>
                          <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                      </div>

                      <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="dynamic-currency-conversion">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="">Currency Rate</label>
                              <input type="text" class="form-control" id="curr_rate" name="curr_rate" value="1">
                            </div>
                            <div class="col-md-6">
                              <?php $curr = queryGet("SELECT * FROM `erp_currency_type` ORDER BY currency_name ASC ", true); ?>
                              <div class="form-input">
                                <label for="">Customer Currency</label>
                                <select id="" name="currency" class="form-control currencyDropdown rupee-symbol">
                                  <?php
                                  foreach ($curr['data'] as $data) {
                                  ?>
                                    <option <?php if ($data['currency_name'] === $currencyName) {
                                              echo "selected";
                                            } ?> value="<?= $data['currency_id'] ?>≊<?= $data['currency_icon'] ?>≊<?= $data['currency_name'] ?>"><?= $data['currency_icon'] ?><?= $data['currency_name'] ?></option>
                                  <?php
                                  }
                                  ?>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- <label for="" class="label-hidden">label</label> -->
                      </div>
                      <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="form-input">
                          <label for="">Compliance Invoice Type <span class="text-danger">*</span></label>
                          <select name="compInvoiceType" class="form-control" id="compInvoiceType" required>
                            <?php
                            if ($branchGstin == "") {
                              foreach (fetchInvoiceTypeWithoutgst()['data'] as $one) { ?>
                                <option <?php echo (is_null($branchGstin) && $one['code'] == "R") || (!is_null($branchGstin) && $one['code'] == "E") ? "selected" : ""; ?> value="<?= $one['code'] ?>">
                                  <?= $one['title'] ?>
                                </option>
                              <?php
                              }
                            } else {
                              foreach (fetchInvoiceTypeWithgst()['data'] as $one) { ?>
                                <option <?php echo (is_null($branchGstin) && $one['code'] == "R") || (!is_null($branchGstin) && $one['code'] == "E") ? "selected" : ""; ?> value="<?= $one['code'] ?>">
                                  <?= $one['title'] ?>
                                </option>
                            <?php
                              }
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <?php if (!isset($_GET['sales_order_creation'])) { ?>

                        <!-- <div class="col-lg-4col-md-4 col-sm-4">
                          <label for="date">Posting Period:</label>
                          <input type="date" name="podatecreation" id="podatecreation" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
                          <p id="podatelabel"></p>
                        </div> -->
                      <?php } ?>

                      <!-- quotaion credit period div -->
                      <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="form-input">
                          <label for="">Credit Period (Days)<span class="text-danger">*</span></label>
                          <input type="text" name="creditPeriod" class="form-control" id="inputCreditPeriod" placeholder="Credit Period " value="<?= $CreditPeriod; ?>" required />
                        </div>
                      </div>
                      <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="form-input">
                          <label for="">Attach Reference</label>
                          <input type="file" name="attachment" class="form-control" id="attachment">
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-4">
                        <label for="" class="">Remark </label>
                        <textarea name="extra_remark" id="extra_remark" placeholder="Remarks" class="form-control" rows="2"><?php echo $extraremarks;  ?></textarea>
                      </div>
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <?php
                        $declarationText = '';
                        if (isset($_GET['create_service_invoice'])) {
                          $declarationText = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='service'")['data']['descText'];
                          if (empty($declarationText)) {
                            $declarationText = $company['sales_invoice_declaration'];
                          }
                        } elseif (isset($_GET['quotation']) || isset($_GET['proforma_to_invoice'])) {
                          $declarationText = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='quotation_to_invoice'")['data']['descText'];
                        } elseif (isset($_GET['so_to_invoice'])) {
                          $declarationText = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='so_to_invoice'")['data']['descText'];
                        } elseif (isset($_GET['pgi_to_invoice'])) {
                          $declarationText = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='pgi_to_invoice'")['data']['descText'];
                        } elseif (isset($_GET['repost_invoice'])) {
                          $declarationText = $declaration;
                        } else {
                          $declarationText = $company['sales_invoice_declaration'];
                        }
                        ?>
                        <label for="" class="">Declaration </label>
                        <textarea name="declaration_note" id="declaration_note" placeholder="Declaration" class="form-control" rows="2"><?= $declarationText ?></textarea>
                      </div>

                    </div>

                  <?php } else { ?>
                    <div class="row others-info-form-view" style="row-gap: 5px; justify-content: center;">

                      <div class="row dotted-border-area">
                        <?php if ($sales_order_creation || $quotation_to_so || $proforma_to_so) { ?>
                          <div class="col-lg-4 col-md-4 col-sm-4">
                            <label>Posting Date: <span class="text-danger">*</span></label>
                            <div>
                              <?php
                              $month = date("n", strtotime($min));
                              if (date("m") == $month) {
                                $min = date("Y-m-d");
                              }
                              ?>
                              <input type="date" value="<?= $min ?>" name="soDate" id="soDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>" required />
                              <span class="input-group-addon soDateMsg"></span>
                            </div>
                          </div>
                          <div class="col-lg-4 col-md-4 col-sm-4">
                            <label>Posting Time: <span class="text-danger">*</span></label>
                            <div>
                              <input type="time" name="postingTime" id="postingTime" value="<?= date("H:i") ?>" class="form-control" required />
                              <span class="input-group-addon postingTimeMsg"></span>
                            </div>
                          </div>

                          <div class="col-lg-4 col-md-4 col-sm-4">
                            <label>Delivery Date: <span class="text-danger">*</span></label>
                            <div>
                              <input type="date" value="<?= date("Y-m-d") ?>" name="deliveryDate" id="deliveryDate" class="form-control" required />
                              <span class="input-group-addon deliveryDateMsg"></span>
                            </div>
                          </div>

                          <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="form-input">
                              <label for="date">Validity Period: <span class="text-danger">*</span></label>
                              <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                          </div>

                          <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="form-input">
                              <label for=""> Customer Order Number <span class="text-danger">*</span></label>
                              <input type="text" name="customerPO" class="form-control" placeholder="Customer Order Number" required />
                            </div>
                          </div>
                        <?php } else if ($_GET['edit_so']) {
                        ?>
                          <div class="col-lg-4 col-md-4 col-sm-4">
                            <label>Posting Date: <span class="text-danger">*</span></label>
                            <div>
                              <?php
                              $month = date("n", strtotime($min));
                              if (date("m") == $month) {
                                $min = date("Y-m-d");
                              }
                              ?>
                              <input type="date" value="<?= $invoiceList['data'][0]['so_date'] ?>" name="soDate" id="soDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>" required />
                              <span class="input-group-addon soDateMsg"></span>
                            </div>
                          </div>
                          <div class="col-lg-4 col-md-4 col-sm-4">
                            <label>Posting Time: <span class="text-danger">*</span></label>
                            <div>
                              <input type="time" name="postingTime" id="postingTime" value="<?= $invoiceList['data'][0]['soPostingTime'] ?>" class="form-control" required />
                              <span class="input-group-addon postingTimeMsg"></span>
                            </div>
                          </div>

                          <div class="col-lg-4 col-md-4 col-sm-4">
                            <label>Delivery Date: <span class="text-danger">*</span></label>
                            <div>
                              <input type="date" value="<?= $invoiceList['data'][0]['delivery_date'] ?>" name="deliveryDate" id="deliveryDate" class="form-control" required />
                              <span class="input-group-addon deliveryDateMsg"></span>
                            </div>
                          </div>

                          <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="form-input">
                              <label for="date">Validity Period</label>
                              <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" value="<?= $invoiceList['data'][0]['validityperiod'] ?>" required>
                            </div>
                          </div>

                          <div class="col-lg-4 col-md-4 col-sm-4">
                            <div class="form-input">
                              <label for=""> Customer Order Number <span class="text-danger">*</span></label>
                              <input type="text" name="customerPO" value="<?= $invoiceList['data'][0]['customer_po_no'] ?>" class="form-control" placeholder="Customer Order Number" required />
                            </div>
                          </div>
                        <?php

                        } else { ?>

                          <?php if ($_GET['party_order_to_so']) { ?>
                            <div class="col-lg-4 col-md-4 col-sm-4 <?php if ($edit_invoice) {
                                                                      echo "displayOverLay";
                                                                    } ?>">
                              <label>Posting Date: <span class="text-danger">*</span></label>
                              <div>
                                <input type="date" value="<?= date("Y-m-d") ?>" name="invoiceDate" id="invoiceDate" class="form-control" required />
                                <span class="input-group-addon"></span>
                              </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 <?php if ($edit_invoice) {
                                                                      echo "displayOverLay";
                                                                    } ?>">
                              <label>Posting Time: <span class="text-danger">*</span></label>
                              <div>
                                <input type="time" value="<?= date("H:i") ?>" name="invoiceTime" id="invoiceTime" class="form-control" required />
                                <span class="input-group-addon"></span>
                              </div>
                            </div>
                          <?php } else { ?>
                            <div class="col-lg-4 col-md-4 col-sm-4 <?php if ($edit_invoice) {
                                                                      echo "displayOverLay";
                                                                    } ?>">
                              <label>Invoice Date: <span class="text-danger">*</span></label>
                              <div>
                                <!-- <input type="date" value="<?= date("Y-m-d") ?>" name="invoiceDate" id="invoiceDate" class="form-control" required /> -->
                                <input type="date" value="<?= date("Y-m-d") ?>" name="invoiceDate" id="invoiceDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>" required />
                                <span class="input-group-addon"></span>
                              </div>
                            </div>
                            <!-- <div class="col-lg-4 col-md-4 col-sm-4 <?php if ($edit_invoice) {
                                                                          echo "displayOverLay";
                                                                        } ?>"> -->
                            <!-- <label>Reference No: </label>
                              <div>
                               
                                <input type="number" value="" name="refNumber" id="refNumber" class="form-control"  />
                                <span class="input-group-addon"></span>
                              </div>
                            </div> -->

                            <div class="col-lg-4 col-md-4 col-sm-4 <?php if ($edit_invoice) {
                                                                      echo "displayOverLay";
                                                                    } ?>">
                              <label>Invoice Time: <span class="text-danger">*</span></label>
                              <div>
                                <input type="time" value="<?= date("H:i") ?>" name="invoiceTime" id="invoiceTime" class="form-control" required />
                                <span class="input-group-addon"></span>
                              </div>
                            </div>
                            <!-- <div class="col-lg-4 col-md-4 col-sm-4 <?php if ($edit_invoice) {
                                                                          echo "displayOverLay";
                                                                        } ?>">

                              <label for="date">Posting Period</label>
                              <input type="date" name="podatecreation" id="podatecreation" class="form-control" min="<?= $min ?>" max="<?= $max ?>">

                            </div> -->
                          <?php } ?>


                          <!-- validity period for Proformainvoice -->

                          <?php if (isset($_GET['proforma_invoice']) || isset($_GET['party_order_to_so'])) { ?>
                            <div class="col-lg-3 col-md-4 col-sm-12">
                              <div class="form-input">
                                <label for="date">Validity Period</label>
                                <input type="date" class="form-control" id="dateInputvalid" name="validitydate" min="<?php echo date('Y-m-d'); ?>" required>
                              </div>
                            </div>
                          <?php } ?>

                          <!-- Validity period End here.. -->
                        <?php } ?>


                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="form-input">
                            <label for="">Credit Period (Days)<span class="text-danger">*</span></label>
                            <input type="text" name="creditPeriod" class="form-control" id="inputCreditPeriod" placeholder="Credit Period " value="<?= $CreditPeriod; ?>" required />
                          </div>
                        </div>

                      </div>

                      <div class="row dotted-border-area">
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="form-input">
                            <label for="">Select Sales Person <span class="text-danger">*</span></label>
                            <select name="kamId" class="form-control select2" id="kamDropDown" required>
                              <option value="">Select Sales Person</option>
                              <?php
                              $funcList = $BranchSoObj->fetchKamDetails()['data'];
                              foreach ($funcList as $func) {
                              ?>
                                <option value="<?= $func['kamId'] ?>" <?php if ($func['kamId'] == $SalesPerson) {
                                                                        echo 'selected';
                                                                      } ?>><?= $func['kamName'] ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="form-input">
                            <label for="">Functional Area <span class="text-danger">*</span></label>
                            <select name="profitCenter" class="selct-vendor-dropdown select2 form-control" id="profitCenterDropDown" required readonly>
                              <option value="">Functional Area</option>
                              <?php
                              $funcList = $BranchSoObj->fetchFunctionality()['data'];
                              foreach ($funcList as $func) {
                              ?>
                                <option value="<?= $func['functionalities_id'] ?>" <?php if ($func['functionalities_id'] == $FunctionalArea) {
                                                                                      echo 'selected';
                                                                                    } ?>><?= $func['functionalities_name'] ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <?php if (!isset($_GET['quotation_to_so']) && !isset($_GET['sales_order_creation']) && !isset($_GET['quotation_createion']) && !isset($_GET['party_order_to_so']) && !isset($_GET['party_order_to_quotation']) && !isset($_GET['proforma_invoice']) && !isset($_GET['edit_so']) && !isset($_GET['proforma_to_so'])) { ?>
                          <!-- invoice formate -->
                          <div class="col-lg-4 col-md-4 col-sm-4 <?php if ($edit_invoice) {
                                                                    echo "displayOverLay";
                                                                  } ?>">
                            <div class="type-flex">
                              <div class="form-input invoice-varient-form">
                                <label for="">Invoice Variant <span class="text-danger">*</span></label>
                                <?php if (isset($_GET['repost_invoice'])) { ?>
                                  <input type="hidden" name="repostInvoiceId" class="form-control" id="repostInvoiceId" value="<?= base64_decode($_GET['repost_invoice']) ?>" />
                                  <input type="hidden" name="iv_varient" class="form-control" id="repostInvoiceId" value="<?= ($inv_variant_id) ?>" />
                                  <input type="text" name="repostInvoiceNo" class="form-control" id="repostInvoiceNo" value="<?= $repost_invoice_no ?>" placeholder="Repost Invoice No" readonly />
                                <?php } else { ?>
                                  <select name="iv_varient" class="form-control" id="iv_varient" required>
                                    <?php
                                    $iv_varient = queryGet("SELECT * FROM `erp_iv_varient` WHERE company_id=$company_id AND status='active' ORDER BY id ASC", true);
                                    $ivselecetd = '';
                                    foreach ($iv_varient['data'] as $vkey => $iv_varientdata) {
                                      if ($vkey == 0) {
                                        $ivselecetd = $iv_varientdata['iv_number_example'];
                                      }
                                    ?>
                                      <option value="<?= $iv_varientdata['id'] ?>" <?php if ($vkey == 0) {
                                                                                      echo 'selected';
                                                                                    } ?>><?= $iv_varientdata['title'] ?></option>
                                    <?php } ?>
                                  </select>
                                <?php } ?>
                              </div>
                              <div class="form-input type-select-form">
                                <label for=""></label>
                                <select class="form-control invoiceNumberType" name="invoiceNumberType" id="invoiceNumberType">
                                  <option value="live">Live</option>
                                  <option value="manual">Manual</option>
                                </select>
                              </div>
                            </div>
                            <div class="invoice-field my-2">
                              <p class="label-bold text-italic" id="liveInvoice" style="white-space: pre-line;"><span class="mr-1">e.i- </span> <span class="ivnumberexample text-sm"><?= $ivselecetd; ?></span></p>
                              <input class="form-control" type="text" id="ivnumberManual" name="ivnumberManual" style="display: none;" placeholder="Enter Inv No:- ">
                            </div>
                          </div>
                          <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                          <label for="" class="">Remark </label>
                          <textarea name="extra_remark" id="extra_remark" placeholder="Remarks" class="form-control" rows="2"></textarea>
                        </div> -->


                        <?php } ?>

                      </div>
                      <div class="row dotted-border-area">
                        <?php if ($branchGstin != '') { ?>
                          <div class="col-lg-3 col-md-3 col-sm-3 <?php if ($edit_invoice) {
                                                                    echo "displayOverLay";
                                                                  } ?>">
                            <div class="form-input">
                              <label for="">Compliance Invoice Type <span class="text-danger">*</span></label>
                              <?php if (isset($_GET['quotation']) || isset($_GET['proforma_to_invoice'])) { ?>
                                <select name="compInvoiceType" class="form-control" id="compInvoiceType" required>
                                  <?php foreach (fetchInvoiceType()['data'] as $one) { ?>
                                    <option <?php if ($one['code'] == "LUT") {
                                              echo "selected";
                                            } ?> value="<?= $one['code'] ?>"><?= $one['title'] ?></option>
                                  <?php } ?>
                                </select>
                              <?php } else { ?>
                                <select name="compInvoiceType" class="form-control" id="compInvoiceType" required>
                                  <?php foreach (fetchInvoiceType()['data'] as $one) { ?>
                                    <option <?php if ($one['code'] == "R") {
                                              echo "selected";
                                            } ?> value="<?= $one['code'] ?>"><?= $one['title'] ?></option>
                                  <?php } ?>
                                </select>
                              <?php } ?>
                            </div>
                          </div>
                        <?php } ?>
                        <div class="col-lg-6 col-md-6 col-sm-6 <?php if ($edit_invoice) {
                                                                  echo "displayOverLay";
                                                                } ?>">
                          <div class="dynamic-currency-conversion">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-input">
                                  <label for="">Currency Rate</label>
                                  <?php if (isset($_GET['quotation']) || isset($_GET['proforma_to_invoice'])) { ?>
                                    <input type="text" class="form-control" id="curr_rate" name="curr_rate" value="<?= $quotationList['data']['conversion_rate'] ?>">
                                  <?php } else { ?>
                                    <input type="text" class="form-control" id="curr_rate" name="curr_rate" value="1">
                                  <?php } ?>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-input">
                                  <label for="">Customer Currency</label>
                                  <?php $curr = queryGet("SELECT * FROM `erp_currency_type` ORDER BY currency_name ASC ", true); ?>
                                  <?php if (isset($_GET['quotation']) || isset($_GET['proforma_to_invoice'])) { ?>
                                    <select name="currency" class="form-control currencyDropdown rupee-symbol select2" id="currencyDropdown">
                                      <?php
                                      foreach ($curr['data'] as $data) {
                                      ?>
                                        <option <?php if ($data['currency_name'] === $quotationList['data']['currency_name']) {
                                                  echo "selected";
                                                } ?> value="<?= $data['currency_id'] ?>≊<?= $data['currency_icon'] ?>≊<?= $data['currency_name'] ?>"><?= $data['currency_icon'] ?><?= $data['currency_name'] ?></option>
                                      <?php } ?>
                                    </select>
                                  <?php } else { ?>
                                    <select name="currency" class="form-control currencyDropdown rupee-symbol select2" id="currencyDropdown">
                                      <?php
                                      foreach ($curr['data'] as $data) {
                                      ?>
                                        <option <?php if ($data['currency_name'] === $currencyName) {
                                                  echo "selected";
                                                } ?> value="<?= $data['currency_id'] ?>≊<?= $data['currency_icon'] ?>≊<?= $data['currency_name'] ?>"><?= $data['currency_icon'] ?><?= $data['currency_name'] ?></option>
                                      <?php } ?>
                                    </select>
                                  <?php } ?>
                                </div>
                              </div>
                            </div>

                            <div class="display-flex" style="justify-content: flex-end;">
                              <p class="label-bold text-italic" style="white-space: pre-line;"><span class="mr-1">*</span>Transaction Currency is <b><?= $currencyName ?></b></p>
                            </div>
                          </div>
                        </div>
                        <?php if (!$sales_order_creation && !$edit_so && !$quotation_to_so && !$proforma_to_so) { ?>
                          <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="form-input">
                              <label for="">Select Bank <span class="text-danger">*</span></label>
                              <?php
                              $bankList = $BranchSoObj->fetchCompanyBank();
                              // console('$bankList');
                              // console($bankList);
                              ?>
                              <select name="bankId" class="form-control" id="bankId" required>
                                <option value="">Select Bank</option>
                                <?php
                                foreach ($bankList['data'] as $bank) {
                                  if ($bank['bank_name'] != "") {
                                ?>
                                    <option value="<?= $bank['id'] ?>" <?php if ($bank['id'] == $SelectBank) {
                                                                          echo 'selected';
                                                                        } ?>><?php if ($bank['bank_name']) {
                                                                                echo '🏦' . $bank['bank_name'];
                                                                              } elseif ($bank['cash_account']) {
                                                                                echo '💰' . $bank['cash_account'];
                                                                              } ?></option>
                                <?php }
                                } ?>
                              </select>
                            </div>
                          </div>
                        <?php } ?>
                        <?php //console($BranchSoObj->fetchFunctionality()) 
                        ?>
                      </div>

                      <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="form-input">
                          <label for="">Place of supply <span class="text-danger">*</span></label>
                          <select name="placeOfSupply" class="form-control" id="placeOfSupply" required>
                            <option value="">Place of supply</option>
                            <?php
                            $stateNameList = fetchStateName()['data'];
                            foreach ($stateNameList as $one) {
                            ?>
                              <option value="<?= $one['gstStateCode'] ?>"><?= $one['gstStateCode'] ?> - <?= $one['gstStateName'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div> -->

                      <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="form-input">
                          <label for="" class="label-hidden">Label</label>
                          <div class="static-currency">
                            <input type="text" class="form-control" value="1" readonly="">
                            <input type="text" class="form-control text-right companyCurrencyName" value="<?= $currencyName ?>" readonly="">
                          </div>
                        </div>
                        <label for="" class="label-hidden">label</label>

                      </div> -->


                      <div class="row dotted-border-area">
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="form-input">
                            <label for="">Attach Reference</label>
                            <input type="file" name="attachment" class="form-control" id="attachment">
                          </div>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <label for="" class="">Remark </label>
                          <textarea name="extra_remark" id="extra_remark" placeholder="Remarks" class="form-control" rows="2"><?php echo $extraremarks;  ?></textarea>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div style="display: flex; justify-content: space-between; align-items: center">
                            <label for="" class="">Contacts</label>
                            <button type="button" data-toggle="modal" data-target="#configModal" style="border: none; font-size: 10px; padding: 0px 5px; margin-bottom: 5px;" class="btn btn-sm btn-primary">
                              Add New
                            </button>
                          </div>
                          <?php
                          $configListObj = "SELECT * FROM `erp_config_invoices` WHERE `company_id` = $company_id AND `branch_id` = $branch_id AND `location_id` = $location_id";
                          $configList = queryGet($configListObj, true);
                          // console($configList);
                          ?>
                          <!-- <div id="configDropdown">
                            <select name="config" class="form-control" id="config">
                              <option value="" id="configValueSelect">Select One</option>
                            </select>
                          </div> -->
                          <select name="companyConfigId" class="form-control" id="config">
                            <option value="">Select One</option>
                            <?php foreach ($configList['data'] as $config) { ?>
                              <option value="<?= $config['config_id'] ?>"><?= $config['name'] ?> /<?= $config['email'] ?> / <?= $config['phone'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>

                      <!-- Config Modal -->
                      <div class="modal fade" id="configModal" tabindex="-1" role="dialog" aria-labelledby="configModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="configModalLabel">Recipient Details</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <div class="form-input">
                                <label for="">Name</label>
                                <input type="name" class="form-control" name="configName" id="configName" placeholder="Name">
                              </div>


                              <div class="form-input">
                                <label for="">Email</label>
                                <input type="email" class="form-control" name="configEmail" id="configEmail" placeholder="Email">
                              </div>

                              <div class="form-input">
                                <label for="">Phone</label>
                                <input type="number" class="form-control" name="configPhone" id="configPhone" placeholder="Phone">
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal" id="handleConfigClose">Close</button>
                              <button type="button" class="btn btn-primary" id="handleConfigSave">Save</button>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                    <div class="row">
                      <?php if (!isset($_GET['quotation_to_so']) && !isset($_GET['sales_order_creation']) && !isset($_GET['quotation_createion']) && !isset($_GET['party_order_to_so']) && !isset($_GET['party_order_to_quotation']) && !isset($_GET['edit_so']) && !isset($_GET['quotation_to_so'])) { ?>

                        <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                          <label for="" class="">Remark </label>
                          <textarea name="extra_remark" id="extra_remark" placeholder="Remarks" class="form-control" rows="2"></textarea>
                        </div> -->

                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <?php
                          $declarationText = '';
                          if (isset($_GET['create_service_invoice'])) {
                            $declarationText = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='service'")['data']['descText'];
                            if (empty($declarationText)) {
                              $declarationText = $company['sales_invoice_declaration'];
                            }
                          } elseif (isset($_GET['quotation']) || isset($_GET['proforma_to_invoice'])) {
                            $declarationText = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='quotation_to_invoice'")['data']['descText'];
                          } elseif (isset($_GET['so_to_invoice'])) {
                            $declarationText = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='so_to_invoice'")['data']['descText'];
                          } elseif (isset($_GET['pgi_to_invoice'])) {
                            $declarationText = queryGet("SELECT * FROM `" . ERP_DOCUMENT_DECLARATION . "` WHERE declarationType='pgi_to_invoice'")['data']['descText'];
                          } elseif (isset($_GET['repost_invoice'])) {
                            $declarationText = $declaration;
                          } else {
                            $declarationText = $company['sales_invoice_declaration'];
                          }
                          ?>
                          <label for="" class="">Declaration </label>
                          <textarea name="declaration_note" id="declaration_note" placeholder="Declaration" class="form-control" rows="2"><?= $declarationText ?></textarea>
                        </div>
                      <?php } ?>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <?php
        if (!$sales_order_creation && !$quotation_createion && !$quotation_to_so && !$party_order_to_so && !$party_order_to_quotation && !$edit_so && !$proforma_to_so) {
        ?>
          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="card direct-create-invoice-card so-creation-card">
              <!-- <div class="card-header">
                <div class="row customer-info-head">
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="head">
                      <i class="fa fa-user"></i>
                      <h4>Transport Details</h4>
                    </div>
                  </div>
                </div>
              </div> -->


            </div>
          </div>
        <?php
        }
        ?>

      </div>







      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
          <div class="card items-select-table mt-4 <?php if ($edit_invoice) {
                                                      echo "displayOverLay";
                                                    } ?>">
            <div class="head-item-table">
              <div class="advanced-serach">
                <?php if (!isset($_GET['joborder_to_invoice']) && !isset($_GET['so_to_invoice']) && !isset($_GET['pgi_to_invoice'])) { ?>
                  <div class="hamburger quickadd-hamburger">
                    <div class="wrapper-action">
                      <i class="fa fa-plus"></i>
                    </div>
                  </div>
                <?php } ?>
                <div class="nav-action quick-add-input d-flex" id="quick-add-input">
                  <?php if ($sales_order_creation || $quotation_createion || $quotation_to_so || $party_order_to_so || $party_order_to_quotation || $proforma_to_so) { ?>
                    <div class="d-flex align-items-center gap-2">
                      <label for="" class="text-nowrap" style="width: 100%;">Order For <span class="text-danger">*</span></label>
                      <select name="goodsType" class="form-control order-for-select" id="goodsType" required>
                        <option value="">Select One</option>
                        <!-- <option <?php if ($quotationGoodsType == "both") {
                                        echo "selected";
                                      } ?> value="both">Both</option> -->
                        <option <?php if ($quotationGoodsType == "material") {
                                  echo "selected";
                                } ?> value="material">Goods</option>
                        <option <?php if ($quotationGoodsType == "service") {
                                  echo "selected";
                                } ?> value="service">Services</option>
                        <option <?php if ($quotationGoodsType == "project") {
                                  echo "selected";
                                } ?> value="project">Projects</option>
                      </select>
                    </div>
                  <?php } ?>
                  <div class="itemDropdownDiv orderFor" style="display: none;">
                    <input name="orderFor" checked class="orderForRadio" type="radio" value="service" id="orderForService" />
                    <label for="orderForService" style="width: 100%; cursor:pointer;user-select: none;">Order for service<span class="text-danger">*</span></label>
                  </div>
                  <div class="itemDropdownDiv orderFor" style="display: none;">
                    <input name="orderFor" class="orderForRadio" type="radio" value="project" id="orderForProject" />
                    <label for="orderForProject" style="width: 100%; cursor:pointer;user-select: none;">Order for project<span class="text-danger">*</span></label>
                  </div>
                  <div class="itemDropdownDiv gap-2 quickAdd">
                    <label for="">Quick Add <span class="text-danger">*</span></label>
                    <select id="itemsDropDown" class="form-control select2">
                      <option value="">Select One</option>
                    </select>
                  </div>

                  <?php if ($sales_order_creation || $quotation_to_so || $party_order_to_so || $proforma_to_so) { ?>

                    <div class="recurringDiv" style="display: none;">
                      <input type="checkbox" name="makeRecurring" class="makeRecurringClass" id="makeRecurring">
                      <label for="makeRecurring" style="user-select: none; margin-bottom: 0;">Make Recurring</label>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>

            <!-- Recurring Modal -->
            <div class="modal fade" id="recurringModal" data-bs-backdrop="true" data-bs-keyboard="false" tabindex="-1" aria-labelledby="recurringModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content itemModalContent">
                  <div class="modal-header card-header p-3 rounded">
                    <h4 class="modal-title font-monospace text-md text-white" id="itemModalLabel">Subscription</h4>
                    <button type="button" class="close text-white subscriptionClose" data-dismiss="modal" aria-label="Close">x</button>
                  </div>
                  <div id="itemModalBody" class="modal-body">
                    <div class="row">
                      <div class="col-12">
                        <div class="form-input">
                          <label for="">Repeat Every <span class="text-danger">*</span></label>
                          <select name="repeatEvery" class="form-control" id="repeatEveryDropDown">
                            <option value="">Select One</option>
                            <option value="1">Day</option>
                            <option value="7">Week</option>
                            <option value="15">15th Days</option>
                            <option value="30">Monthly</option>
                            <option value="92">Quarterly</option>
                            <option value="183">Half-Yearly</option>
                            <option value="366">Yearly</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-8">
                        <div class="form-input">
                          <label for="">Start On</label>
                          <input type="date" class="form-control" value="<?= date("Y-m-d") ?>" name="startOn" id="startOn">
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="form-input">

                          <label for="">Invoice Variant <span class="text-danger">*</span></label>
                          <select name="iv_variant" class="form-control" id="IvVariantDropDown">
                            <?php
                            $iv_varient = queryGet("SELECT * FROM `erp_iv_varient` WHERE company_id=$company_id AND status='active' ORDER BY id ASC", true);
                            $ivselecetd = '';
                            foreach ($iv_varient['data'] as $vkey => $iv_varientdata) {
                              if ($vkey == 0) {
                                $ivselecetd = $iv_varientdata['iv_number_example'];
                              }
                            ?>
                              <option value="<?= $iv_varientdata['id'] ?>" <?php if ($vkey == 0) {
                                                                              echo 'selected';
                                                                            } ?>><?= $iv_varientdata['title'] ?></option>
                            <?php } ?>
                          </select>

                        </div>
                      </div>
                      <div class="col-8">
                        <div class="form-input">
                          <label for="">End On</label>
                          <input type="date" class="form-control" name="endOn" id="endOn">
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="form-input">
                          <label for="" class="label-hidden"></label>
                          <div class="d-flex gap-2 mt-2">
                            <label for="" class="mb-0">Never Expire</label>
                            <input type="checkbox" name="neverExpire" id="neverExpire">
                          </div>
                        </div>
                      </div>
                      <div class="col-4">
                        <button type="button" class="btn btn-primary mt-2 subscriptionClose" data-dismiss="modal" aria-label="Close">Save</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- <div class="currency-section w-25">
              <div class="form-input">
                <label for="">Currency Conversion</label>
                <select id="" name="currency" class="form-control">
                  <?php
                  $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                  foreach ($curr['data'] as $data) {
                  ?>
                    <option value="<?= $data['currency_id'] ?>"><?= $data['currency_name'] ?></option>
                  <?php
                  }
                  ?>

                </select>
              </div>
              <div class="form-input">
                <label for="">Currency Conversion Rate</label>
                <input type="number" class="form-control" id="curr_rate" name="curr_rate" value="1">
              </div>
            </div> -->
            <!-- <a class="btn btn-primary items-search-btn" data-bs-toggle="modal" data-bs-target="#exampleModal"> <i class="fa fa-search mr-2"></i>Advance Search</a> -->
            <!-- <small class="py-2 px-1 rounded alert-dark specialDiscount" id="specialDiscount" style="display: none;">Special Discount</small> -->

            <div class="card">
              <div class="card-body" style="overflow: auto;">
                <table class="table table-sales-order mt-0">
                  <thead>
                    <tr>
                      <th>Item Code</th>
                      <th>Item Name</th>
                      <th>HSN Code</th>
                      <?php if (isset($_GET['so_to_invoice']) && $soList['data']['goodsType'] == "service") { ?>
                        <th>Stock</th>
                      <?php } else if (isset($_GET['repost_invoice'])) { ?>
                        <th>Stock</th>
                      <?php } else { ?>
                        <th>Stock</th>
                      <?php }
                      if (isset($_GET['so_to_invoice']) || isset($_GET['quotation']) || isset($_GET['quotation_to_so']) || isset($_GET['party_order_to_so']) || isset($_GET['party_order_to_quotation']) || isset($_GET['pgi_to_invoice']) || isset($_GET['edit_so']) || isset($_GET['proforma_to_so']) || isset($_GET['proforma_to_invoice'])) { ?>
                        <th>Previous Order Qty</th>
                      <?php } ?>
                      <!-- <th>Qty</th> -->
                      <?php if (isset($_GET['joborder_to_invoice'])) { ?>

                        <th>Invoice Qty</th>
                      <?php } else { ?>
                        <th>Qty</th>
                      <?php } ?>
                      <th>MRP</th>
                      <th>Rate</th>
                      <th>Trade Discount</th>
                      <th>Gross Amt.</th>
                      <th>Cash Discount</th>
                      <?php
                      if ($companyCountry == 'Not India') {
                      ?>

                        <th style="display:none;">Taxable Amount</th>
                        <th style="display:none;">GST (%)</th>
                        <th class="text-right" style="display:none;">GST Amount (<?= $currencyName ?>)</th>
                      <?php

                      } else {
                      ?>
                        <th>Taxable Amount</th>
                        <th>GST (%)</th>
                        <th class="text-right">GST Amount (<?= $currencyName ?>)</th>


                      <?php

                      }

                      ?>


                      <th class="text-right">Total Price</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="itemsTable"></tbody>
                  <span id="spanItemsTable"></span>
                  <tbody>
                    <tr>
                      <?php if (isset($_GET['create_service_invoice'])) { ?>
                        <td colspan="11" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else if (isset($_GET['proforma_to_invoice'])) { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else if (isset($_GET['so_to_invoice'])) { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } ?>
                      <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Sub Total</sup></td>
                      <input type="hidden" name="grandSubTotalAmtInp" id="grandSubTotalAmtInp" value="0">
                      <td class="text-right pr-2" style="border: none; background: none;">
                        <small class="text-large font-weight-bold text-success">
                          <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandSubTotalAmt">0.00</span>
                        </small>
                        <small class="text-small font-weight-bold text-primary convertedDiv">
                          (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandSubTotalAmt">0.00</span>)
                        </small>
                      </td>
                    </tr>
                    <!-- trade discount -->
                    <tr>
                      <?php if (isset($_GET['create_service_invoice'])) { ?>
                        <td colspan="11" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else if (isset($_GET['proforma_to_invoice'])) { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else if (isset($_GET['so_to_invoice'])) { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } ?>
                      <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Trade Discount</td>
                      <input type="hidden" name="grandTotalDiscountAmtInp" id="grandTotalDiscountAmtInp" value="0">
                      <td class="text-right pr-2" style="border: none; background: none;">
                        <small class="text-large font-weight-bold text-success">
                          <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTotalDiscount">0.00</span>
                        </small>
                        <small class="text-small font-weight-bold text-primary convertedDiv">
                          (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandTotalDiscountAmount">0.00</span>)
                        </small>
                      </td>
                    </tr>

                    <!-- cash discount -->
                    <tr>
                      <?php if (isset($_GET['create_service_invoice'])) { ?>
                        <td colspan="11" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else if (isset($_GET['proforma_to_invoice'])) { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else if (isset($_GET['so_to_invoice'])) { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } ?>
                      <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">Cash Discount</td>
                      <input type="hidden" name="grandTotalCashDiscountAmtInp" id="grandTotalCashDiscountAmtInp" value="0">
                      <td class="text-right pr-2" style="border: none; background: none;">
                        <small class="text-large font-weight-bold text-success">
                          <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTotalCashDiscount">0.00</span>
                        </small>
                        <small class="text-small font-weight-bold text-primary convertedDiv">
                          (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandTotalCashDiscountAmount">0.00</span>)
                        </small>
                      </td>
                    </tr>


                    <?php
                    if ($companyCountry == 'Not India') {
                    } else {

                    ?>

                      <tr class="p-2 igstTr" style="display:none">
                        <?php if (isset($_GET['create_service_invoice'])) { ?>
                          <td colspan="11" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else if (isset($_GET['proforma_to_invoice'])) { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else if (isset($_GET['so_to_invoice'])) { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } ?>

                        <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">IGST</td>
                        <input type="hidden" name="grandTaxAmtInp" id="grandTaxAmtInp" value="0">
                        <td class="text-right pr-2" style="border: none; background: none;">
                          <small class="text-large font-weight-bold text-success">
                            <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTaxAmt">0.00</span>
                          </small>
                          <small class="text-small font-weight-bold text-primary convertedDiv">
                            (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandTaxAmount">0.00</span>)
                          </small>
                        </td>
                      </tr>
                      <tr class="p-2 cgstTr" style="display:none">
                        <?php if (isset($_GET['create_service_invoice'])) { ?>
                          <td colspan="11" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else if (isset($_GET['proforma_to_invoice'])) { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else if (isset($_GET['so_to_invoice'])) { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } ?>
                        <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">CGST</td>
                        <!-- <input type="hidden" name="grandSgstCgstAmtInp" id="grandSgstCgstAmtInp" value="0"> -->
                        <td class="text-right pr-2" style="border: none; background: none;">
                          <small class="text-large font-weight-bold text-success">
                            <span class="rupee-symbol"><?= $currencyName ?> </span><span class="grandSgstCgstAmt">0.00</span>
                          </small>
                          <small class="text-small font-weight-bold text-primary convertedDiv">
                            (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span class="convertedGrandSgstCgstAmt">0.00</span>)
                          </small>
                        </td>
                      </tr>
                      <tr class="p-2 sgstTr" style="display:none">
                        <?php if (isset($_GET['create_service_invoice'])) { ?>
                          <td colspan="11" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else if (isset($_GET['proforma_to_invoice'])) { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else if (isset($_GET['so_to_invoice'])) { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } else { ?>
                          <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                        <?php } ?>

                        <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">SGST</td>
                        <!-- <input type="hidden" name="grandSgstCgstAmtInp" id="grandSgstCgstAmtInp" value="0"> -->
                        <td class="text-right pr-2" style="border: none; background: none;">
                          <small class="text-large font-weight-bold text-success">
                            <span class="rupee-symbol"><?= $currencyName ?> </span><span class="grandSgstCgstAmt">0.00</span>
                          </small>
                          <small class="text-small font-weight-bold text-primary convertedDiv">
                            (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span class="convertedGrandSgstCgstAmt">0.00</span>)
                          </small>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>
                    <tr>
                      <?php if (isset($_GET['pgi_to_invoice'])) { ?>
                        <td colspan="12"></td>
                      <?php } else if (isset($_GET['so_to_invoice'])) { ?>
                        <td colspan="12"></td>
                      <?php } else { ?>
                        <td colspan="12"></td>
                      <?php } ?>
                      <td colspan="2">
                        <!-- round off 👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾 -->
                        <?php if (!$sales_order_creation && !$quotation_createion && !$quotation_to_so && !$proforma_to_so) { ?>
                          <!-- tcs section start -->
                          <div class="row">
                            <div class="col-6 pl-0">
                              <div class="round-off-head d-flex gap-2 pl-0">
                                <input type="hidden" name="paymentDetails[tcsValue]" value="0" class="tcsValueInp">
                                <input type="checkbox" class="tcscheckbox" name="tcs_checkbox" id="tcs_checkbox">
                                <label style="user-select: none" class="text-xs" for="tcs_checkbox">TCS</label>
                              </div>
                            </div>
                            <div class="col-6 pr-0">
                              <div id="tcsAmtshowhidediv">
                                <div class="col-lg-12 col-md-12 col-sm-12 px-0 d-flex gap-2">
                                  <div class="d-flex gap-2">
                                    <label style="user-select: none" class="text-xs" for="tcs_checkbox"> Amount</label>
                                  </div>
                                  <div class="d-flex gap-2">
                                    <input type="number" step="any" id="tcs_amount" value=" " class="form-control text-center">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- tcs section end -->
                          <!-- round-off section start -->
                          <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 px-0">
                              <div class="round-off-section p-0">
                                <div class="round-off-head d-flex gap-2">
                                  <input type="checkbox" class="checkbox" name="round_off_checkbox" id="round_off_checkbox">
                                  <label style="user-select: none" class="text-xs" for="round_off_checkbox">Adjust Amount</label>
                                </div>
                                <div id="round_off_hide">
                                  <div class="row round-off calculte-input px-0">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                      <div class="adjust-currency d-flex gap-2">
                                        <select id="round_sign" class="form-control text-center">
                                          <option value="+">+</option>
                                          <option value="-">-</option>
                                        </select>
                                        <input type="number" step="any" id="round_value" value="0" class="form-control text-center">
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row" style="width: 100%;">
                                    <div class="col-lg-12 col-md-12 col-sm-12 px-0">
                                      <div class="totaldueamount d-flex justify-content-between border-top border-white pt-2">
                                        <p class="font-bold">Adjusted Amount</p>
                                        <input type="hidden" name="paymentDetails[adjustedCollectAmount]" class="adjustedCollectAmountInp">
                                        <input type="hidden" name="paymentDetails[roundOffValue]" value="0" class="roundOffValueHidden">
                                        <div style="border-top: 2px double; padding: 2px;">
                                          <p class="text-success font-bold rupee-symbol">₹ <span class="adjustedDueAmt">0</span></p>
                                          <small class="text-small font-weight-bold text-primary convertedDiv">
                                            (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span class="convertedAdjustedDueAmt">0.00</span>)
                                          </small>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- round-off section finish -->

                        <?php } ?>
                        <!-- round off 👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾 -->
                      </td>
                    </tr>
                    <tr class="p-2">
                      <?php if (isset($_GET['create_service_invoice'])) { ?>
                        <td colspan="11" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else if (isset($_GET['so_to_invoice'])) { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } else { ?>
                        <td colspan="12" class="text-right p-2" style="border: none; background: none;"> </td>
                      <?php } ?>
                      <td colspan="0" class="text-left p-2 font-weight-bold totalCal" style="border-top: 3px double !important; background: none;padding: 0px !important;">Total Amount</td>
                      <input type="hidden" name="grandTotalAmtInp" id="grandTotalAmtInp" class="grandTotalAmounttInp" value="0">
                      <td class="font-weight-bold text-right pr-2" style="border-top: 3px double !important; background: none;">
                        <small class="text-large font-weight-bold text-success">
                          <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTotalAmt" class="grandTotalAmount">0.00</span>
                        </small>
                        <small class="text-small font-weight-bold text-primary convertedDiv">
                          (<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?></span><span id="convertedGrandTotalAmt">0.00</span>)
                        </small>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <?php
          if (!$sales_order_creation && !$quotation_createion && !$quotation_to_so && !$party_order_to_so && !$party_order_to_quotation && !$edit_so && !$proforma_to_so) {
            $select_tc = queryGet("SELECT * FROM `erp_terms_and_condition_format` WHERE tc_slug='invoice' AND company_id = $company_id", true);
            //  console($select_tc);

          ?>
            <div class="row tc-section" style="margin: 0px 0px 20px 0px;padding: 10px 0px;border-radius: 10px;box-shadow: 0 0 15px #d8d8d8b3;border: 1px solid #d0d0d0;">
              <div class="d-flex">
                <p> Select Terms & Conditions</p>
                <select name="terms_and_condition" class="selct-terms-and-condition select2 form-control" id="terms-and-condition">
                  <option value="0">Select Terms and Conditions</option>
                  <?php foreach ($select_tc['data'] as $tc) { ?>
                    <option value="<?= $tc["tc_id"] ?>"><?= $tc['tc_variant'] ?></option>
                  <?php } ?>
                </select>
                <button type="button" class="btn-view btn btn-primary previewBtn" data-toggle="modal" data-target="#previewModal">Preview
                  <i id="statusItemBtn" class="statusItemBtn fa fa-eye"></i>
                </button>



              </div>
            </div>

            <div class="modal modal-left left-item-modal fade previewModal discountViewModal discountViewModal" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="left_modal">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title tc-modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body discountViewBody">
                    <!-- <h6 class="tc-modal-title"></h6> -->
                    <p class='tc-modal-body'></p>


                  </div>
                  <div class="modal-footer modal-footer-fixed">
                    <button type="button" class="btn btn-primary w-100" data-dismiss="modal">Save & Close</button>
                  </div>
                </div>
              </div>
            </div>
          <?php
          }
          ?>

          <div class="row fob-section" style="margin: 0px 0px 20px 0px;padding: 10px 0px;border-radius: 10px;box-shadow: 0 0 15px #d8d8d8b3;border: 1px solid #d0d0d0;">
            <div class="d-flex">
              <label for="fob" style="display: flex; align-items: center;" class="mb-0">
                <p class="pr-2"> If this is the FOB/FOR, Please Check </p>
                <input type="checkbox" id="fob">
                <input type="hidden" name="fobCheckbox" id="fobCheckbox" value="unchecked">
              </label>
            </div>
          </div>
          <div class="card p-3 items-select-table modal-add-row_537" id="otherCostCard" style="display: none;">
            <h6>Please raise the service purchase request form</h6>
            <div class="row othe-cost-infor">
              <div class="col-lg-5 col-md-12 col-sm-12">
                <div class="form-input">
                  <label for="">Services</label>
                  <!-- <textarea class="form-control" placeholder="Description" name="otherCostDetails[12345][services]"></textarea> -->
                  <select name="otherCostDetails[12345][services]" class="selct-vendor-dropdown" id="servicesDropDown">
                    <option value="">Select One</option>
                    <?php foreach ($serviceList as $service) { ?>
                      <option value="<?= $service["itemId"] ?>_<?= $service["itemCode"] ?>_<?= $service["itemName"] ?>_<?= $service["service_unit"] ?>"><?= $service['itemName'] ?><small>(<?= $service['itemCode'] ?>)[<?= $service['goodsType'] ?>]</small></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-5 col-md-12 col-sm-12">
                <div class="form-input">
                  <label for="">Qty</label>
                  <input step="any" type="number" class="form-control" placeholder="Qty" name="otherCostDetails[12345][qty]">
                </div>
              </div>
              <div class="col-lg-2 col-md-6 col-sm-6">
                <div class="add-btn-plus">
                  <a style="cursor: pointer" class="btn btn-primary" onclick="addOtherCost(537)">
                    <i class="fa fa-plus"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
          <button type="submit" name="addNewInvoiceFormSubmitBtn" onclick="return confirm('Are you sure to submitted?')" id="directInvoiceCreationBtn" class="btn btn-primary items-search-btn float-right">Submit</button>
        </div>
      </div>
    </form>
  </section>
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
<!-- Modal -->
<div class="modal fade add-customer-modal" id="addNewCustomerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content card">
      <div class="modal-header card-header py-2 px-3">
        <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Customer</h4>
        <button type="button" class="close text-white" data-dismiss="modal" id="addCustomerCloseBtn" aria-label="Close">x</button>
      </div>
      <div id="notesModalBody" class="modal-body card-body">

        <!-- <div class="form-input my-2">
            <input type="text" name="customerName" id="customerName" placeholder="Enter name" class="form-control">
          </div>
          <div class="form-input my-2">
            <input type="email" name="customerEmail" id="customerEmail" placeholder="Enter email" class="form-control">
          </div>
          <div class="form-input my-2">
            <input type="number" name="customerPhone" id="customerPhone" placeholder="Enter phone *" class="form-control" required>
            <span id="customerPhoneMsg" class="text-xs"></span>
          </div>
          <div class="form-input my-2">
            <button type="submit" name="addCustomerBtn" class="form-control" id="addCustomerBtn">Add</button>
          </div> -->

        <div class="row">
          <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
            <div class="multisteps-form__progress">
              <button class="multisteps-form__progress-btn js-active" type="button" title="User Info">Basic Details</button>
              <button class="multisteps-form__progress-btn" type="button" title="Comments" id="poc_btn" disabled>POC Details</button>
            </div>
          </div>
        </div>
        <!--form panels-->
        <div class="row">
          <div class="col-12 col-lg-8 m-auto">
            <form class="multisteps-form__form" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
              <input type="hidden" name="createdatamultiform" id="createdatamultiform" value="">
              <input type="hidden" name="company_id" id="company_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]; ?>">
              <input type="hidden" name="company_branch_id" id="company_branch_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]; ?>">

              <!--single form panel-->
              <div class="multisteps-form__panel js-active" data-animation="scaleIn">
                <div class="card vendor-details-card mb-0">
                  <div class="card-header p-3">
                    <div class="display-flex">
                      <div class="head">
                        <i class="fa fa-user"></i>
                        <h4>Basic Details</h4>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <div class="row">

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>GSTIN</label>
                            <input type="text" class="form-control" name="customer_gstin" id="customer_gstin" value="">
                          </div>

                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Pan *</label>
                            <input type="text" class="form-control" name="customer_pan" id="customer_pan" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Customer Name</label>
                            <input type="text" class="form-control" name="trade_name" id="trade_name" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Constitution of Business</label>
                            <input type="text" class="form-control" name="con_business" id="con_business" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input selDiv">
                            <label>State</label>

                            <select id="state" name="state" class="form-control stateDropDown">
                              <?php
                              $stateNameList = fetchStateName()['data'];

                              usort($stateNameList, 'compareByStateCode');
                              foreach ($stateNameList as $one) {
                              ?>
                                <option value="<?= ($one['gstStateName']) ?>"><?= $one['gstStateCode'] ?> - <?= $one['gstStateName'] ?></option>
                              <?php } ?>
                            </select>
                            <!-- <input type="text" class="form-control" name="state" id="state" value=""> -->
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>City</label>
                            <input type="text" class="form-control" name="city" id="city" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>District</label>
                            <input type="text" class="form-control" name="district" id="district" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Location</label>
                            <input type="text" class="form-control" name="location" id="location" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Building Number</label>
                            <input type="text" class="form-control" name="build_no" id="build_no" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Flat Number</label>
                            <input type="text" class="form-control" name="flat_no" id="flat_no" value="">

                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-input">
                            <label>Street Name</label>
                            <input type="text" class="form-control" name="street_name" id="street_name" value="">

                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-input">
                            <label>Pin Code</label>
                            <input type="number" class="form-control" name="pincode" id="pincode" value="">
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-input">
                            <label for="">Company currency</label>
                            <select id="company_currency" name="company_currency" class="form-control mt-0 form-control-border borderColor">
                              <!--<option value="">Select Currency</option>-->
                              <?php
                              $listResult = getAllCurrencyType();
                              if ($listResult["status"] == "success") {
                                foreach ($listResult["data"] as $listRow) {
                              ?>
                                  <option value="<?php echo $listRow['currency_id']; ?>"><?php echo $listRow['currency_name']; ?></option>
                              <?php }
                              } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6" style="display:none;">
                          <div class="form-input">
                            <label>Opening Blance</label>
                            <input type="hidden" class="form-control" name="opening_balance" id="customer_opening_balance" value="0">
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="form-input">
                            <label>Credit Period(In Days)</label>
                            <input type="text" class="form-control" name="credit_period" id="customer_credit_period" value="">

                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex">
                      <button class="btn btn-primary ml-auto js-btn-next" id="customerRegFrmNextBtn" type="button" data-toggle="modal" data-target="#visitingCard" title="Next">Next</button>
                    </div>
                  </div>
                </div>
              </div>
              <!--single form panel-->
              <div class="modal fade" id="visitingCard" style="z-index: 999999;" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content m-auto" style="max-width: 375px; border-radius: 20px;">

                    <div class="modal-body p-0" style="overflow: hidden; border-radius: 20px;">
                      <div id="uploadGrnInvoiceDiv" class="create-grn">
                        <div class="upload-files-container">
                          <div class="card visiting-card-upload">
                            <div class="card-header">
                              <div class="head">
                                <h4>Upload Visiting Card</h4>
                              </div>
                            </div>
                            <div class="card-body">
                              <div class="drag-file-area">
                                <i class="fa fa-arrow-up po-list-icon text-center m-auto"></i>
                                <br>
                                <input type="file" class="form-control" id="visitingFileInput" name="" placeholder="Visiting Card Upload" required />
                              </div>
                              <div class="file-block">
                                <div class="progress-bar"> </div>
                              </div>
                              <button type="button" class="upload-button btn btn-primary visiting_card_btn" name="" id="visiting_card_btn"> Upload </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="multisteps-form__panel" data-animation="scaleIn">

                <div class="card vendor-details-card mb-0">
                  <div class="card-header">
                    <div class="head">
                      <h4>POC Details</h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <div class="row">
                        <div class="col-md-12">
                          <label for="">Upload Visiting Card<span class="visiting_loder"></span></label>
                          <input class="visiting_card form-control" type="file" name="visiting_card" id="visiting_card">
                        </div>

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Name of Person*</label>
                            <input type="text" class="form-control" name="customer_authorised_person_name" id="adminName" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Designation</label>
                            <input type="text" class="form-control" name="customer_authorised_person_designation" id="customer_authorised_person_designation" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Phone Number*</label>
                            <input type="text" class="form-control" name="customer_authorised_person_phone" id="adminPhone" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Alternative Phone </label>
                            <input type="text" class="form-control" name="customer_authorised_alt_phone" id="customer_authorised_person_phone" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Email*</label>
                            <input type="email" class="form-control" name="customer_authorised_person_email" id="adminEmail" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Alternative Email</label>
                            <input type="email" class="form-control" name="customer_authorised_alt_email" id="customer_authorised_person_email" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Login Password [Will be send to the POC email]</label>
                            <input type="text" class="form-control" name="adminPassword" id="adminPassword" value="<?php echo rand(00000, 999999) ?>">

                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-input">
                            <label for="">Choose Image</label>
                            <input type="file" class="form-control" name="customer_picture" id="customer_picture">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-input">
                            <label for="" style="visibility: hidden;">Visible for all</label>
                            <select id="customer_visible_to_all" name="customer_visible_to_all" class="select2 form-control mt-0 borderColor">
                              <option value="No"> Only for this location</option>
                              <option value="Yes" selected>Visible For All</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex">
                      <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
                      <button class="btn ml-auto btn-danger add_data" type="button" title="Save As Draft" value="add_draft">Save As Draft</button>
                      <button id="customerCreateBtn" class="btn btn-primary ml-auto add_data" type="button" title="Final Submit" value="add_post">Final Submit</button>
                    </div>
                  </div>
                </div>

              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php require_once("../common/footer.php"); ?>

<script>
  // js validator for email,name and phone 

  function validateInputs(name, email, phone) {
    const nameRegex = /^[A-Za-z\s]+$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    // const phoneRegex = /^(\+?\d{1,2}\s?)?(\(?\d{3}\)?[\s-]?)?[\d\s-]{7,10}$/;
    const phoneRegex = /^[1-9]\d{9}$/
    let validationMessage = "";
    if (!nameRegex.test(name)) {
      validationMessage += "Invalid name. name should contain only alphabets.<br>";
    }
    if (!emailRegex.test(email)) {
      validationMessage += "Invalid email. enter a valid email address.<br>";
    }
    if (!phoneRegex.test(phone)) {
      validationMessage += "Invalid number. enter a valid phone number.<br>";
    }
    return validationMessage;
  }



  // Function to update a query parameter in the URL
  function updateQueryParam(paramName, paramValue) {
    // Get the current URL
    var currentUrl = new URL(window.location.href);

    // Set the new parameter value
    currentUrl.searchParams.set(paramName, paramValue);

    // Update the URL in the browser
    window.history.replaceState({}, '', currentUrl);
  }

  $("#profitCenterDropDown").on("change", function() {
    let functionalArea = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 1;
    console.log(functionalArea, 'functionalArea');
    $.ajax({
      type: "POST",
      url: `ajaxs/so/ajax-generate-inv-number.php`,
      data: {
        act: "getVerientExamplecopy",
        functionalArea: functionalArea
      },
      beforeSend: function() {
        // $("#itemsDropDown").html(`Loading...`);
      },
      success: function(response) {
        let data = JSON.parse(response);
        console.log('data');
        console.log(data);
        $("#iv_varient").val(data['id']);
        $(".ivnumberexample").html(data['iv_number_example']);
      }
    });
  });

  $("#iv_varient").on("change", function() {
    let vid = $(this).val();
    let functionalArea = $("#profitCenterDropDown").val();

    $.ajax({
      type: "POST",
      url: `ajaxs/so/ajax-generate-inv-number.php`,
      data: {
        act: "getVerientExamplecopy",
        functionalArea: functionalArea,
        vid: vid
      },
      beforeSend: function() {
        // $("#itemsDropDown").html(`Loading...`);
      },
      success: function(response) {
        let data = JSON.parse(response);
        $(".ivnumberexample").html(data['iv_number_example']);
      }
    });
  });


  $(document).on("click", ".dlt-popup", function() {
    $(this).parent().parent().remove();
  });

  function rm() {
    // $(event.target).closest("tr").remove();
    $(this).parent().parent().parent().remove();
  }

  function addOtherCost(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row_${id}`).append(`<div class="row othe-cost-infor">
          <div class="col-lg-5 col-md-12 col-sm-12">
              <div class="form-input">
                  <label for="">Services</label>
                  <select name="otherCostDetails[${addressRandNo}][services]" class="selct-vendor-dropdown" id="servicesDropDown_${addressRandNo}">
                    <option value="">Select One</option>
                      <?php foreach ($serviceList as $service) { ?>
                        <option value="<?= $service["itemId"] ?>_<?= $service["itemCode"] ?>_<?= $service["itemName"] ?>_<?= $service["service_unit"] ?>"><?= $service['itemName'] ?><small>(<?= $service['itemCode'] ?>)[<?= $service['goodsType'] ?>]</small></option>
                      <?php } ?>
                  </select>
              </div>
          </div>
          <div class="col-lg-5 col-md-12 col-sm-12">
              <div class="form-input">
                  <label for="">Qty</label>
                  <input step="any" type="number" class="form-control" placeholder="Qty" name="otherCostDetails[${addressRandNo}][qty]">
              </div>
          </div>
          <div class="col-lg-2 col-md-6 col-sm-6">
              <div class="add-btn-minus">
                  <a style="cursor: pointer" class="btn btn-danger">
                      <i class="fa fa-minus"></i>
                  </a>
              </div>
          </div>
      </div>`);

    $(`#servicesDropDown_${addressRandNo}`)
      .select2()
      .on('select2:open', () => {
        // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
      });


  }






  $(document).on("click", ".add-btn-minus", function() {
    $(this).parent().parent().remove();
  });

  function addMultiQty(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    //$(`.modal-add-row_${id}`).append(`<tr><td><span class='has-float-label'><input type='date' name='listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]' class='form-control' placeholder='delivery date' required><label>Delivery date</label></span></td><td><span class='has-float-label'><input type='text' name='listItem[${id}][deliverySchedule][${addressRandNo}][quantity]' class='form-control multiQuantity' data-itemid="${id}" id='multiQuantity_${addressRandNo}' placeholder='quantity' required><label>quantity</label></span></td><td><a class='btn btn-danger' onclick='rm()'><i class='fa fa-minus'></i></a></td></tr>`);
    $(`.modal-add-row_${id}`).append(`
      <div class="modal-add-row">
        <div class="row modal-cog-right">
          <div class="col-lg-5 col-md-5 col-sm-5">
              <div class="form-input">
                  <label>Delivery Date</label>
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
    // 👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀
    $(document).on('click', '.go', function() {
      let the_value = $('input[name=radioBtn]:radio:checked').val();
      let address_id = $('input[name=radioBtn]:radio:checked').data('addid');
      let stateCode = $('input[name=radioBtn]:radio:checked').data('statecode');
      // alert(stateCode);

      $(".address-change-modal").toggle();
      $("html").css({
        "overflow": "auto"
      });
      $("#shipTo").html(the_value);
      $("#placeOfSupply1").val(stateCode).trigger("change");
      $("#shippingAddressInp").val(the_value);
      $("#shipping_address_id").val(address_id);
      $('input.billToCheckbox').prop('checked', false);
    });

    $('#fob').on('change', function() {
      if ($('#fob').is(':checked')) {
        $('#fobCheckbox').val('checked');
      } else {
        $('#fobCheckbox').val('unchecked');
      }
    });

    loadItems();
    // loadCustomers();
    var customer__ID = '<?= $customerId ?>';
    // **************************************
    function loadItems() {
      // alert();
      let value = $('#goodsType').val();
      let searchUrl = window.location.search;

      goodsType = (value != null && value != undefined) ? value : (searchUrl === "?create_service_invoice" ? 'service' : 'material');

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items-goods-type.php`,
        beforeSend: function() {
          $("#itemsDropDown").html(`<option value="">Loading...</option>`);
        },
        data: {
          act: "goodsType",
          goodsType: goodsType
        },
        success: function(response) {
          $("#itemsDropDown").html(response);
        }
      });
    };

    $("#fob").on("click", function() {
      // alert();
      if ($('#fob').is(':checked')) {
        $("#otherCostCard").show();
      } else {
        $("#otherCostCard").hide();
      }
    });

    $("#soDate").on("change", function() {
      let soDate = $(this).val();
      $(".multiDeliveryDate").val(soDate);
    })

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
            let data = JSON.parse(response);

            $("#customerDropDown").html(response);
            if (data.status === "success") {
              $("#customerName").val("");
              $("#customerEmail").val("");
              $("#customerPhone").val("");
              $("#addCustomerBtn").text(`Add`);
              $("#addCustomerBtn").prop('disabled', false);
              $("#addCustomerBtn").text(`Add`);
              $("#addCustomerCloseBtn").trigger("click");
              // loadCustomers();
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
        data: {
          customerId: '<?= $customerId ?>'
        },
        beforeSend: function() {
          $("#customerDropDown").html(`<option value="">Loading... </option>`);
        },
        success: function(response) {
          $("#customerDropDown").html(response);
          console.log('response');
          console.log(response);
        }
      });
    }

    function addCustomerFunc(customerId) {
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers-address.php`,
        data: {
          act: "customerAddress",
          customerId
        },
        beforeSend: function() {
          $("#shipTo").html(`Loading...`);
        },
        success: function(response) {
          let data = JSON.parse(response);
          $("#shipTo").html(data.data);
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
          $("#customerInfo").html(`<option value="">Loading...</option>`);
        },
        success: function(response) {
          $("#customerInfo").html(response);
          let creditPeriod = $("#spanCreditPeriod").text();
          $("#inputCreditPeriod").val(creditPeriod);

          let customerGstinCode = $(".customerGstinCode").val();
          let branchGstinCode = $(".branchGstin").val();
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

    addCustomerFunc('<?= $customerId; ?>');

    // get customer details by id
    $("#customerDropDown").on("change", function() {
      let customerId = $(this).val();

      if (customerId > 0) {
        $(document).on("click", ".billToCheckbox", function() {
          if ($('input.billToCheckbox').is(':checked')) {
            // $(".shipTo").html(`checked ${customerId}`);
            addCustomerFunc(customerId);
          } else {
            $(".changeAddress").click();
            // $("#shipTo").html(`unchecked ${customerId}`);
          }
        });
        $(".customerIdInp").val(customerId);
        customerDetailsInfo(customerId);
      }

      $(".deliveryScheduleModal").each(function() {
        //  alert(1);
        var target = $(this).attr('id');

        //  alert(target);
        if (target) {

          var rowNo = target.substring(target.lastIndexOf('_') + 1); // Extract randCode
          //alert(rowNo);
          let item_id = $(`#itemId_${rowNo}`).val();
          // alert(item_id);
          let customer_id = $('#customerDropDown').val();
          let days = $('#inputCreditPeriod').val();
          //  alert(days);
          $(`.discountView_${rowNo}`).empty();
          $(`#itemDiscount_${rowNo}`).val(0);
          $(`#itemTotalDiscount_${rowNo}`).val(0);
          $(`#itemTotalDiscount1_${rowNo}`).val(0);
          calculateOneItemAmounts(rowNo);
          if (isNaN(days) || days <= 0) {
            alert('Please enter a valid positive integer for credit period');

          } else {
            discount_varients(rowNo, customer_id, item_id, days);
          }


          $.ajax({
            type: "GET",
            url: `ajaxs/mrp/ajax-mrp.php`,
            data: {

              customer_id: customer_id,
              item_id: item_id,

            },
            beforeSend: function() {
              //   alert(1);
            },
            success: function(response) {
              // alert(response);
              console.log(response);
              $(`#originalItemUnitPriceInp_${rowNo}`).val(response);
              $(`#originalChangeItemUnitPriceInp_${rowNo}`).val(response);
              calculateOneItemAmounts(rowNo);

            }
          });

        } else {
          console.log('not ok');
        }
      });
    });

    customerDetailsInfo(customer__ID);

    function customerDetailsInfo(customerId) {
      let searchUrl = window.location.search;
      let param = searchUrl.split("=")[0];
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-customers-list.php`,
        data: {
          act: "listItem",
          customerId
        },
        beforeSend: function() {
          $("#customerInfo").html(`<option value="">Loading...</option>`);
        },
        success: function(response) {
          $("#customerInfo").html(response);
          let creditPeriod = $("#spanCreditPeriod").text();
          $("#inputCreditPeriod").val(creditPeriod);

          let stateCodeSpanElement = $(".stateCodeSpan");
          let stateCodeSpan = stateCodeSpanElement.length > 0 ? stateCodeSpanElement.text().trim() : null;
          $("#placeOfSupply1").val(stateCodeSpan).trigger("change");

          let customerGstinCode = $(".customerGstinCode").val();
          let branchGstinCode = $(".branchGstin").val();
          if (customerGstinCode !== "") {
            console.log('customerGstinCode is not empty 🏁🏁🏁', customerGstinCode)
            if (customerGstinCode === branchGstinCode) {
              $(".igstTr").hide();
              $(".cgstTr").show();
              $(".sgstTr").show();
            } else {
              $(".igstTr").show();
              $(".cgstTr").hide();
              $(".sgstTr").hide();
            }
          } else {
            $(document).on("change", "#placeOfSupply1", function() {
              let placeOfSupply1 = $(this).val();
              //alert(placeOfSupply1);
              let customerGstinCode = $(".customerGstinCode").val();
              //  alert(customerGstinCode);
              if (customerGstinCode === "") {
                if (placeOfSupply1 === branchGstinCode) {
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
          if (param != "?repost_invoice") {
            // Second AJAX request
            $.ajax({
              url: "ajaxs/so/ajax-customers-invoice-log.php",
              type: "GET",
              data: {
                act: "customersInvoiceLog",
                customerId
              },
              success: function(response2) {
                // console.log(response2);
                let data2 = JSON.parse(response2);
                if (data2.status == "success") {
                  let profit_center = data2.data.profit_center;
                  let kamId = data2.data.kamId;
                  let complianceInvoiceType = data2.data.complianceInvoiceType;
                  let placeOfSupply = data2.data.placeOfSupply;
                  let invoiceNoFormate = data2.data.invoiceNoFormate;
                  let bank = data2.data.bank;

                  $("#profitCenterDropDown").val(profit_center).trigger("change");
                  $("#compInvoiceType").val(complianceInvoiceType).trigger("change");
                  $("#kamDropDown").val(kamId).trigger("change");
                  $("#bankId").val(bank).trigger("change");
                  // $("#placeOfSupply1").val(placeOfSupply).trigger("change");
                  $("#iv_varient").val(invoiceNoFormate).trigger("change");
                } else {
                  console.log('somthing went wrong');
                  $("#profitCenterDropDown").val('').trigger("change");
                  // $("#compInvoiceType").val('R').trigger("change");
                  $("#kamDropDown").val('').trigger("change");
                  $("#bankId").val('').trigger("change");
                  // $("#placeOfSupply1").val('').trigger("change");
                  $("#iv_varient").val('').trigger("change");
                }
              },
              error: function(xhr, status, error) {
                console.log("Error 2:", error);
              }
            });
          }
        }
      });
    }

    $(document).on("click", "#pills-home-tab", function() {
      $("#saveChanges").html('<button type="button" class="btn btn-primary go">Go</button>');
    });
    $(document).on("click", "#pills-profile-tab", function() {
      $("#saveChanges").html('<button type="button" class="btn btn-primary" id="save">Save</button>');
    });

    // // AddressEdit
    // $(document).on("click", ".billAddressEdit", function() {
    //   let addressId = ($(this).attr("id")).split("_")[1];
    //   $(`.changeAddress`).click();
    //   $(`.newaddress`).click();
    //   alert(addressId)
    // });

    // $(document).on("click", ".shipAddressEdit", function() {
    //   let addressId = ($(this).attr("id")).split("_")[1];
    //   $(`.changeAddress`).click();
    //   $(`.newaddress`).click();
    //   alert(addressId)
    // });

    // subscription
    $("#makeRecurring").on("click", function() {
      if ($(this).is(":checked")) {
        $("#recurringModal").modal("show");
      } else {
        $("#recurringModal").modal("hide");

        $("#repeatEveryDropDown").val('');
        $("#startOn").val('');
        $("#endOn").val('');
        $("#neverExpire").val('');
      }
    });
    $(".subscriptionClose").on('click', function() {
      $("#recurringModal").modal("hide");
    });

    // handleConfig function to fetch and update options
    function handleConfig() {
      // console.log('handleConfig');
      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-config-invoice.php`,
        beforeSend: function() {
          $("#config").html(`<option value="">Loading...</option>`);
        },
        data: {
          act: "getContact"
        },
        success: function(response) {
          let data = JSON.parse(response);
          // console.log(data);
          if (data.status == "success") {
            // console.log(data.data);

            // Get the select element
            let selectElement = document.getElementById('config');

            // Clear existing options
            selectElement.innerHTML = '';

            // First not selected 
            let optEl = document.createElement('option');
            optEl.value = "";
            optEl.textContent = "Select One";
            selectElement.appendChild(optEl);

            // Create and add new options based on data
            data.data.forEach(option => {
              let optionElement = document.createElement('option');
              optionElement.value = option.config_id;
              optionElement.textContent = option.email;
              selectElement.appendChild(optionElement);
            });
          }
        }
      });
    }

    // $('#configDropdown').on('change', function() {
    //     handleConfig();
    // });


    // handleConfigSave
    function handleConfigSave() {
      let configName = $("#configName").val();
      let configEmail = $("#configEmail").val();
      let configPhone = $("#configPhone").val();
      console.log(configName, configEmail, configPhone);

      if (configEmail == "" || configPhone == "" || configName == "") {
        swal.fire({
          icon: `error`,
          title: `Note`,
          text: `Please fill all the fields`
        })
        return;
      }

      const validationMessage = validateInputs(configName, configEmail, configPhone);
      if (validationMessage) {
        swal.fire({
          icon: `warning`,
          title: `Validation Failed!`,
          html: `${validationMessage}`
        });
        return;
      }

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-config-invoice.php`,
        data: {
          act: "handleConfigSave",
          configName,
          configEmail,
          configPhone
        },
        success: function(response) {
          let data = JSON.parse(response);
          console.log(data);
          if (data.status == "success") {
            swal.fire({
              icon: `success`,
              title: `Note`,
              text: `${data.message}`
            });

            $("#configEmail").val("");
            $("#configName").val("");
            $("#configPhone").val("");
            $('#handleConfigClose').click();
            handleConfig();
          } else {
            swal.fire({
              icon: `error`,
              title: `Note`,
              text: `${data.message}`
            })
          }
        },
        error: function(xhr, status, error) {
          console.log("Error:", error);
        }
      });
    }
    // handleConfigSave function
    $(document).on("click", "#handleConfigSave", function() {
      handleConfigSave();
    })

    // submit address form
    $(document).on('click', '#save', function() {
      let customerId = $('.customerIdInp').val();
      let recipientName = $("#recipientName").val();
      let billingNo = $("#billingNo").val();
      let flatNo = $("#flatNo").val();
      let streetName = $("#streetName").val();
      let location = $("#location").val();
      let city = $("#city").val();
      let pinCode = $("#pinCode").val();
      let district = $("#district").val();
      let state = $("#state").val();
      let stateCode = $("#stateCode").val();

      if (billingNo != '') {
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-customers-address.php`,
          data: {
            act: "shipAddressSave",
            customerId,
            recipientName,
            billingNo,
            flatNo,
            streetName,
            location,
            city,
            pinCode,
            district,
            state,
            stateCode
          },
          beforeSend: function() {
            $(`#save`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          },

          success: function(response) {
            let data = JSON.parse(response);
            console.log(data);
            $(".address-change-modal").hide();
            $(".modal-backdrop").hide();
            $("#shipTo").html(data.data);
            $("#shipToLastInsertedId").val(data.lastInsertedId);
            $('input.billToCheckbox').prop('checked', false);
            $(`#save`).html('Save');

            // input value null
            $("#recipientName").val('');
            $("#billingNo").val('');
            $("#flatNo").val('');
            $("#streetName").val('');
            $("#location").val('');
            $("#city").val('');
            $("#pinCode").val('');
            $("#district").val('');
            $("#state").val('');
            $("#stateCode").val('');
            $(`#pills-home-tab`).click();
            $(`.closeButton`).click();
          }
        });
      } else {
        alert(`All fields are required`);
      }
    });

    // get item details by id
    function itemAutoAdd(itemIdArry) {

      let customer_id = $('#customerDropDown').val();
      let url = window.location.search;
      let param = url.split("=")[0];

      let othersdata = '<?= $pgiCode ?>';


      let companyCurrencyName = '<?= $currencyName ?>';
      let currencyName = ($('.currencyDropdown').val()).split("≊")[2];

      // to toggle FOB
      if (param === "?sales_order_creation" || param === "?quotation_to_so" || param === "?party_order_to_so" || param === "?party_order_to_quotation" || param === "?proforma_to_so") {
        $(".fob-section").show();


      } else {
        $(".fob-section").hide();
        $(".tc-section").show();


      }

      if (itemIdArry != '') {
        const sanitizedJSONData = itemIdArry.replace(/[\x00-\x1F\x7F-\x9F]/g, '');
        var itemIdArryTo = JSON.parse(sanitizedJSONData);
        var invoicedate = $("#invoiceDate").val();
        var compInvoiceType = $("#compInvoiceType").val();
        $.each(itemIdArryTo, function(index, value) {
          if (value.inventory_item_id > 0) {
            $.ajax({
              type: "GET",
              url: `ajaxs/so/ajax-items-list-direct-new-country.php`,
              data: {
                act: "listItem",
                itemId: value.inventory_item_id,
                type: param,
                othersdata: othersdata,
                invoicedate: invoicedate,
                customer_id: customer_id,
                compInvoiceType: compInvoiceType,
                items: value

              },
              beforeSend: function() {
                $(`#spanItemsTable`).html(`Loading...`);
              },
              success: function(response) {
                //  alert(response);
                console.log(response)

                $("#itemsTable").append(response);
                calculateGrandTotalAmount();
                $(`#spanItemsTable`).html(``);

                if (companyCurrencyName !== currencyName) {
                  $(`.convertedDiv`).show();
                } else {
                  $(`.convertedDiv`).hide();
                }
              }
            });
          }
        });
      }
    }

    itemAutoAdd('<?= $itemIdJson; ?>');
    // get item details by id
    $("#itemsDropDown").on("change", function() {
      let itemId = $(this).val();
      let url = window.location.search;
      let param = url.split("=")[0];
      // dynamic value
      let customer_id = $('#customerDropDown').val();
      const currentURL = window.location.href;
      const ccurl = new URL(currentURL);
      const searchParams = new URLSearchParams(ccurl.search);
      const searchValue = searchParams.get(param.substring(1));

      let companyCurrencyName = '<?= $currencyName ?>';
      let currencyName = ($('.currencyDropdown').val()).split("≊")[2];

      var invoicedate = $("#invoiceDate").val();
      var compInvoiceType = $("#compInvoiceType").val();
      if (itemId > 0) {
        let deliveryDate = $('#deliveryDate').val();
        $.ajax({
          type: "GET",
          url: `ajaxs/so/ajax-items-list-direct-new-country.php`,
          data: {
            act: "listItem",
            type: param,
            valueId: searchValue,
            invoicedate: invoicedate,
            compInvoiceType: compInvoiceType,
            customer_id: customer_id,
            itemId
          },
          beforeSend: function() {
            $(`#spanItemsTable`).html(`Loading...`);
          },
          success: function(response) {

            //alert(response);


            console.log(response);

            $("#itemsTable").append(response);
            calculateGrandTotalAmount();
            $(`#spanItemsTable`).html(``);
            currency_conversion();

            let deliveryDate = $("#deliveryDate").val();
            $(".multiDeliveryDate").val(deliveryDate);

            if (companyCurrencyName !== currencyName) {
              $(`.convertedDiv`).show();
            } else {
              $(`.convertedDiv`).hide();
            }

            // gstin validation
            let branchGstin = $("#branchGstin").val("");
            if (branchGstin === "") {
              for (elem of $(".itemTotalTax")) {
                let rowNo = ($(elem).attr("id")).split("_")[1];

                $(`#itemTax_${rowNo}`).val(0);
                $(`#itemTaxPercentage_${rowNo}`).html(0);

                calculateOneItemAmounts(rowNo);
              };
              calculateGrandTotalAmount();
            }

            $('#itemsDropDown option:first').prop('selected', true);
          }

        });
      }
    });


    $(document).ready(function() {
      // Event delegation to handle modal opening
      $(document).on('show.bs.modal', '.deliveryScheduleModal', function() {
        // Display alert when modal is opened
        var modal = $(this); // Reference to the current modal
        var target = $(this).attr('id'); // Get the data-target attribute value
        // alert(target);


        if (target) {

          var rowNo = target.substring(target.lastIndexOf('_') + 1); // Extract randCode
          // alert(rowNo);
          let item_id = $(`#itemId_${rowNo}`).val();
          // alert(item_id);
          let customer_id = $('#customerDropDown').val();
          let days = $('#inputCreditPeriod').val();
          //  alert(days);
          $(`.discountView_${rowNo}`).empty();

          if (isNaN(days) || days <= 0) {
            alert('Please enter a valid positive integer for credit period');

          } else {
            discount_varients(rowNo, customer_id, item_id, days);
          }

        } else {
          console.log('not ok');
        }
      });





    });

    $('#inputCreditPeriod').keyup(function() {
      // alert(1);

      $(".deliveryScheduleModal").each(function() {
        var target = $(this).attr('id');
        if (target) {

          var rowNo = target.substring(target.lastIndexOf('_') + 1); // Extract randCode
          // alert(rowNo);
          let item_id = $(`#itemId_${rowNo}`).val();
          // alert(item_id);
          let customer_id = $('#customerDropDown').val();
          let days = $('#inputCreditPeriod').val();

          $(`.discountView_${rowNo}`).empty();
          $(`#itemDiscount_${rowNo}`).val(0);
          $(`#itemTotalDiscount_${rowNo}`).val(0);
          $(`#itemTotalDiscount1_${rowNo}`).val(0);
          calculateOneItemAmounts(rowNo);
          if (isNaN(days) || days <= 0) {
            alert('Please enter a valid positive integer for credit period');

          } else {
            discount_varients(rowNo, customer_id, item_id, days);
          }
        } else {
          console.log('not ok');
        }

      });

    });



    function discount_varients(rowNo, customer_id, item_id, days) {
      //console.log(days);
      let itemVal = $(`#itemBaseAmountInp_${rowNo}`).val();
      // alert(itemVal);

      let previousDocDiscountVariantId = $(`#previousDiscountVarientId_${rowNo}`).val();


      let qty = $(`#itemQty_${rowNo}`).val();

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-item-discount-rachhel.php`,
        data: {
          customer_id: customer_id,
          item_id: item_id,
          days: days,
          qty: qty,
          value: itemVal
        },
        beforeSend: function() {
          //$(`#spanItemsTable`).html(`Loading...`);
        },
        success: function(response) {

          console.log("Item discount data:", response);
          //  alert(rowNo);
          //   console.log(Array.isArray(JSON.parse(response)));
          var obj = JSON.parse(response);


          $.each(obj, function(index, item) {

            var rand = Math.ceil(Math.random() * 100000);

            console.log({
              previousDocDiscountVariantId
            })

            let selectedDiscountDivNote = item.discount_variant_id == previousDocDiscountVariantId ? `<p class="pre-normal text-warning">In the previous document, this discount was selected!</p>` : ``;

            if (item.discount_type == 'value') {
              // console.log(item.discount_type);
              var div = `<div class="discount-card">
                            <input type="radio" class="discount_radio" name="discount" id="discount_radio` + rand + `" data-attr="` + rand + `" value="` + item.discount_value + `">
                            <p><b>Rs.` + item.discount_value + ` off </b> </p>
                            <p>`
              if (item.minimum_value != 0 && item.minimum_value != null) {
                div += ` on minimum purchase value of<b>Rs.` + item.minimum_value + `</b> `
              }
              if (item.minimum_value != null && item.minimum_qty != null) {

                div += `` + item.condition;
              }
              if (item.minimum_qty != 0 && item.minimum_qty != null) {
                div += ` minimum quantity of <b>` + item.minimum_qty + `</b> `
              }
              div += `</p><div class='d-flex justify-content-between validity-days'>
                              <div class='form-inline'>
                                <label>Valid from</label>
                                <p>` + item.valid_from + `</p>
                              </div>
                              <div class='form-inline'>
                                <label>Valid upto</label>
                                <p>` + item.valid_upto + `</p>
                              </div>
                            </div>`

              if (item.term_of_payment != null && item.term_of_payment != 0) {
                div += `<div class='form-input'>
                             <label>Terms of Payment</label>
                             <p>` + item.term_of_payment + ` Days</p>
                          </div>`;
              }
              div += `</div>
                          <input type="hidden" id="discount_variant_id_${rand}" name="discount_variant_id" value="` + item.discount_variant_id + `">
                          <input type="hidden" id="percentage_val_${rand}" name="percentage_val" value="` + item.discount_value + `">
                          <input type="hidden" id="discount_type_${rand}" name="discount_type" value="` + item.discount_type + `">
                          <input type="hidden" id="max_val_${rand}" name="max_val" value="` + item.discount_max_value + `">
                          <input type="hidden" id="min_val_${rand}" name="min_val" value="` + item.minimum_value + `">
                          <input type="hidden" id="min_qty_${rand}" name="min_qty" value="` + item.minimum_qty + `">
                          <input type="hidden" id=base_price_${rand}" name="base_price" value="` + itemVal + `">
                        `;
            } else {
              var div = `
                          <div class="discount-card">
                            <input type="radio" class="discount_radio" name="discount" id="discount_radio" data-attr = "` + rand + `" value="` + item.discount_percentage + `">
                            <p><b>` + item.discount_percentage + ` % off </b>`;

              if (item.discount_max_value != 0 && item.discount_max_value != null) {
                div += `upto Rs.` + item.discount_max_value;
              }

              if (item.minimum_value != 0 && item.minimum_value != null) {
                div += ` on minimum purchase value <b>Rs.` + item.minimum_value + `</b>`;
              }
              if (item.minimum_value != null && item.minimum_qty != null) {
                div += `` + item.condition;
              }
              if (item.minimum_qty != 0 && item.minimum_qty != null) {
                div += ` minimum quantity of <b>` + item.minimum_qty + `</b>`;
              }

              div += `<div class='d-flex justify-content-between validity-days'>
                              <div class='form-inline'>
                                <label>Valid from</label>
                                <p>` + item.valid_from + `</p>
                              </div>
                              <div class='form-inline'>
                                <label>Valid upto</label>
                                <p>` + item.valid_upto + `</p>
                              </div>
                            </div>`;

              if (item.term_of_payment != null && item.term_of_payment != 0) {
                div += `<div class='form-input'>
                             <label>Terms of Payment</label>
                             <p>` + item.term_of_payment + ` Days</p>
                          </div>`;
              }


              div += `${selectedDiscountDivNote}
                        </div>
                          <input type="hidden" id="discount_variant_id_${rand}" name="discount_variant_id" value="` + item.discount_variant_id + `">
                          <input type="hidden" id="percentage_val_${rand}" name="percentage_val" value="` + item.discount_percentage + `">
                          <input type="hidden" id="discount_type_${rand}" name="discount_type" value="` + item.discount_type + `">
                          <input type="hidden" id="max_val_${rand}" name="max_val" value="` + item.discount_max_value + `">
                          <input type="hidden" id="min_val_${rand}" name="min_val" value="` + item.minimum_value + `">
                          <input type="hidden" id="min_qty_${rand}" name="min_qty" value="` + item.minimum_qty + `">
                          <input type="hidden" id="base_price_${rand}" name="base_price" value="` + itemVal + `">
                          `;
            }


            $(`.discountView_${rowNo}`).append(div);

            // Here you can perform any action you want with the current element
          });
        }
      });

      $(`.discountView_${rowNo}`).on('click', '.discount_radio', function() {
        // alert(this.value); // Show value of the clicked radio button
        var random = $(this).data('attr');
        //  alert(random);
        console.log({
          random,
          rowNo
        })
        var percentage_val = $(`#percentage_val_${random}`).val();
        //alert(percentage_val);
        var discount_type = $(`#discount_type_${random}`).val();
        var discount_varient_id = $(`#discount_variant_id_${random}`).val();
        console.log({
          discount_varient_id
        });
        //alert(discount_type);
        var max_val = $(`#max_val_${random}`).val();
        // alert(max_val);
        var base_price = itemVal;
        // alert(base_price);

        let itemQty = parseFloat($(`#itemQty_${rowNo}`).val()) > 0 ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
        let itemCashDiscountAmount = parseFloat($(`#itemTotalCashDiscount_${rowNo}`).text()) || 0;
        let originalChangeItemUnitPriceInp = parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
        let basicPrice = originalChangeItemUnitPriceInp * itemQty;

        let item_id = $(`#itemId_${rowNo}`).val();
        $(`#itemTradeDiscountPercentageInp_${rowNo}`).val(`${percentage_val}`);
        $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${percentage_val}`);

        if (discount_type == 'percentage') {
          let itemQty = $(`#itemQty_${rowNo}`).val();

          var percentage_amount = (percentage_val / 100) * base_price;
          let itemGrossAmount = (itemQty * originalChangeItemUnitPriceInp) - percentage_amount;
          $(`#itemGrossAmountSpan_${rowNo}`).text(`${itemGrossAmount.toFixed(2)}`);

          $(`#itemGrossAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(percentage_amount));
          $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(percentage_amount) - parseFloat(itemCashDiscountAmount));
          if (max_val > 0 && max_val !== null) {
            if (percentage_amount > max_val) {

              var new_base_price = base_price - max_val;
              $(`#itemTotalDiscount1_${rowNo}`).val(max_val);
              $(`#itemDiscount_${rowNo}`).val(percentage_val);
              $(`#itemDiscountVarientId_${rowNo}`).val(discount_varient_id);
              $(`#itemTotalDiscountHidden_${rowNo}`).val(max_val);
              $(`#itemTotalDiscount_${rowNo}`).html(max_val);
              var dis = $(`#itemTotalDiscount1_${rowNo}`).val();
              $(`#itemTradeDiscountAmountInp_${rowNo}`).val(max_val);
              $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(max_val);
              var percentage = parseFloat($(`#itemDiscount_${rowNo}`).val()).toFixed(2);
              $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${percentage}`)

              calculateOneItemAmounts(rowNo);
            }

          } else {
            // alert(1);
            var new_base_price = base_price - percentage_amount;
            $(`#itemTotalDiscount1_${rowNo}`).val(percentage_amount);
            $(`#itemDiscount_${rowNo}`).val(percentage_val);
            $(`#itemDiscountVarientId_${rowNo}`).val(discount_varient_id);
            $(`#itemTotalDiscountHidden_${rowNo}`).val(percentage_amount);
            $(`#itemTotalDiscount_${rowNo}`).html(percentage_amount);

            $(`#itemTradeDiscountAmountInp_${rowNo}`).val(percentage_amount);
            $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(percentage_amount);
            var percentage = parseFloat($(`#itemDiscount_${rowNo}`).val()).toFixed(2);
            $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${percentage}`)

            calculateOneItemAmounts(rowNo);
          }

        } else {
          var percent = (percentage_val * 100) / base_price
          $(`#itemTotalDiscount1_${rowNo}`).val(percentage_val);
          $(`#itemDiscount_${rowNo}`).val(percent);
          $(`#itemDiscountVarientId_${rowNo}`).val(discount_varient_id);
          $(`#itemTotalDiscountHidden_${rowNo}`).val(percentage_val);
          $(`#itemTotalDiscount_${rowNo}`).html(percentage_val);
          $(`#itemTradeDiscountAmountInp_${rowNo}`).val(percentage_val);
          // $(`#itemTradeDiscountAmountInp_${rowNo}`).val(percentage_amount);
          $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(percentage_amount);
          var percentage = parseFloat($(`#itemDiscount_${rowNo}`).val()).toFixed(2);
          $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(`${percentage}`);
          $(`#itemTradeDiscountName_${rowNo}`).val("cash");
          calculateOneItemAmounts(rowNo);
          // calculateQuantity(rowNo, item_id, percentage_val);
          // calculateGrandTotalAmount();
        }

        calculateQuantity(rowNo, item_id, percentage_val);
        calculateOneItemAmounts(rowNo);
        calculateGrandTotalAmount();
      });
    }




    // roundoff function start 👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾👇🏾 
    $("#round_off_hide").hide();
    $(document).on('change', '.checkbox', function() {
      if (this.checked) {
        $("#round_off_hide").show();
      } else {
        $("#round_off_hide").hide();
      }
    });

    function roundofftotal(total_value, sign, roudoff) {
      let final_value = 0;
      let tcsAmount = (parseFloat($('#tcs_amount').val()) > 0) ? parseFloat($('#tcs_amount').val()) : 0;

      if (sign === "+") {
        final_value = total_value + roudoff + tcsAmount;
      } else {
        final_value = total_value + tcsAmount - roudoff;
      }
      $(".adjustedDueAmt").html(final_value.toFixed(2));
      $(".adjustedCollectAmountInp").val(final_value.toFixed(2));
      $("#grandTotalAmtInp").html(final_value.toFixed(2));


    }

    $(document).on("change", "#round_sign", function() {
      let roundValue = (parseFloat($("#round_value").val()) > 0) ? parseFloat($("#round_value").val()) : 0;
      let total_value = (parseFloat($("#grandTotalAmtInp").val()) > 0) ? parseFloat($("#grandTotalAmtInp").val()) : 0;
      var sign = $('#round_sign').val();
      roundofftotal(total_value, sign, roundValue);
      $(".roundOffValueHidden").val(sign + roundValue);
    });

    $(document).on("keyup", "#round_value", function() {
      let roundValue = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      let total_value = (parseFloat($("#grandTotalAmtInp").val()) > 0) ? parseFloat($("#grandTotalAmtInp").val()) : 0;
      var sign = $('#round_sign').val();
      roundofftotal(total_value, sign, roundValue);
      $(".roundOffValueHidden").val(sign + roundValue);
      calculateGrandTotalAmount();
    });
    // roundoff function end 👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾👆🏾

    // tcs function section start

    function tcsTotal(total_value, tcsAmount) {
      let final_value = 0;
      final_value = total_value + tcsAmount;
      console.log("final_value" + final_value)
      $("#grandTotalAmt").html(final_value.toFixed(2));
      $('.tcsValueInp').val(tcsAmount.toFixed(2))
      // $(".grandTotalAmounttInp").html(final_value.toFixed(2));
      $(".adjustedCollectAmountInp").val(final_value.toFixed(2));
      $("#grandTotalAmtInp").html(final_value.toFixed(2));


    }

    $(document).on("keyup", "#tcs_amount", function() {
      let tcsAmount = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
      let total_value = (parseFloat($("#grandTotalAmtInp").val()) > 0) ? parseFloat($("#grandTotalAmtInp").val()) : 0;
      tcsTotal(total_value, tcsAmount);
      $(".roundOffValueHidden").val(sign + roundValue);
      calculateGrandTotalAmount();
    });
    // tcs function section end

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
          $(".totalPrice").html(`<option value="">Loading...</option>`);
        },
        success: function(response) {
          console.log(response);
          $(".totalPrice").html(response);
        }
      });
    });

    // #######################################################
    $(document).on("click", ".toggleServiceRemarksPen", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      $(`#itemRemarks_${rowNo}`).toggle();
    });

    // -- generated by chatGPT 🤖🤖🤖 || imranali59059 || original code is above 👆🏾
    //// one item calculation 
    // function calculateOneItemAmounts(rowNo) {
    //   let param = window.location.search.split("=")[0];
    //   let baseAmountIsCheck = $(`#baseAmountRadio_${rowNo}`).is(":checked");
    //   console.log('window.location.search.split("=")[0]', param);

    //   //alert(rowNo);totalItemAmountModal
    //   let itemQty = parseFloat($(`#itemQty_${rowNo}`).val()) || 0;
    //   let originalItemUnitPrice = parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
    //   let convertedItemUnitPrice = parseFloat($(`#convertedItemUnitPriceSpan_${rowNo}`).text()) || 0;
    //   let itemDiscount = parseFloat($(`#itemDiscount_${rowNo}`).val()) || 0;
    //   let itemCashDiscount = parseFloat($(`#itemCashDiscount_${rowNo}`).val()) || 0;
    //   let convertedCurrencyValue = parseFloat($(`#curr_rate`).val()) || 0;

    //   let itemTradeDiscountAmount = parseFloat($(`#itemTradeDiscountAmountSpan_${rowNo}`).text()) || 0;
    //   let itemCashDiscountAmount = parseFloat($(`#itemCashDiscountAmountSpan_${rowNo}`).text()) || 0;
    //   let itemGrossAmount = parseFloat($(`#itemGrossAmountSpan_${rowNo}`).text()) || 0;
    //   let itemTaxableAmount = parseFloat($(`#itemTaxableAmountSpan_${rowNo}`).text()) || 0;
    //   let itemTradeDiscountPercentageSpan = parseFloat($(`#itemTradeDiscountPercentageSpan_${rowNo}`).text()) || 0;
    //   let itemCashDiscountPercentageSpan = parseFloat($(`#itemCashDiscountPercentageSpan_${rowNo}`).text()) || 0;

    //   let itemTradeDiscountAmountInp = parseFloat($(`#itemTradeDiscountAmountInp_${rowNo}`).val()) || 0;
    //   let itemCashDiscountAmountInp = parseFloat($(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val()) || 0;

    //   let convertedTradeDiscountAmount = itemTradeDiscountAmountInp * convertedCurrencyValue;
    //   let convertedGrossAmount = itemGrossAmount * convertedCurrencyValue;
    //   let convertedCashDiscountAmount = itemCashDiscountAmountInp * convertedCurrencyValue;
    //   let convertedTaxableAmount = itemTaxableAmount * convertedCurrencyValue;

    //   $(`#convertedItemTradeDiscountAmountSpan_${rowNo}`).text(convertedTradeDiscountAmount.toFixed(2));
    //   $(`#convertedGrossAmountSpan_${rowNo}`).text(convertedGrossAmount.toFixed(2));
    //   $(`#convertedCashDiscountAmountSpan_${rowNo}`).text(convertedCashDiscountAmount.toFixed(2));
    //   $(`#convertedTaxableAmountSpan_${rowNo}`).text(convertedTaxableAmount.toFixed(2));

    //   //alert(itemDiscount);
    //   let itemTax = parseFloat($(`#itemTax_${rowNo}`).val()) || 0;
    //   //alert(itemTax);

    //   $(`#multiQuantity_${rowNo}`).val(itemQty);

    //   let basicPrice = itemQty * originalItemUnitPrice;
    //   let convertedBasicPrice = convertedItemUnitPrice * itemQty;

    //   let totalDiscount = parseFloat($(`#itemTotalDiscount1_${rowNo}`).val()) || 0;
    //   let convertedTotalDiscount = convertedBasicPrice * itemDiscount / 100;
    //   let convertedTotalCashDiscount = convertedBasicPrice * itemCashDiscount / 100;
    //   let grossAmount = basicPrice - (parseFloat(basicPrice) * itemTradeDiscountPercentageSpan / 100);
    //   let cashDiscountAmount = (basicPrice * itemCashDiscountPercentageSpan) / 100;

    //   // let priceWithDiscount = basicPrice - totalDiscount;
    //   // let priceWithDiscount = (basicPrice - itemGrossAmount) - itemCashDiscountAmount;

    //   let priceWithDiscount = itemTaxableAmount;

    //   let convertedPriceWithDiscount = convertedBasicPrice - convertedTotalDiscount;
    //   let taxableAmount = grossAmount.toFixed(2) - cashDiscountAmount.toFixed(2);
    //   let totalTax = taxableAmount * itemTax / 100;
    //   //alert(totalTax);
    //   let convertedTotalTax = convertedPriceWithDiscount * itemTax / 100;

    //   let totalItemPrice = taxableAmount + totalTax;
    //   //alert(totalItemPrice);
    //   let convertedTotalItemPrice = convertedPriceWithDiscount + convertedTotalTax;

    //   console.log(`itemGrossAmountSpan =>>`, basicPrice, `-`, itemTradeDiscountAmount)

    //   console.log({
    //     itemQty,
    //     originalItemUnitPrice,
    //     convertedItemUnitPrice,
    //     itemDiscount,
    //     itemCashDiscount,
    //     convertedCurrencyValue,
    //     itemTradeDiscountAmount,
    //     itemCashDiscountAmount,
    //     itemGrossAmount,
    //     itemTaxableAmount,
    //     itemTradeDiscountPercentageSpan,
    //     itemCashDiscountPercentageSpan,
    //     basicPrice,
    //     totalDiscount,
    //     convertedTotalDiscount,
    //     grossAmount,
    //     cashDiscountAmount,
    //     priceWithDiscount,
    //     convertedPriceWithDiscount,
    //     totalTax,
    //   });




    //   // $(`#itemGrossAmountSpan_${rowNo}`).text(grossAmount.toFixed(2));
    //   $(`#itemGrossAmountSpan_${rowNo}`).text(basicPrice.toFixed(2) - totalDiscount.toFixed(2));
    //   $(`#itemGrossAmountInCashDiscount_${rowNo}`).text(basicPrice.toFixed(2) - totalDiscount.toFixed(2));
    //   // $(`#itemGrossAmountInCashDiscount_${rowNo}`).html(parseFloat(basicPrice) - (parseFloat(basicPrice) * itemTradeDiscountPercentageSpan / 100));
    //   $(`#grossAmountRadio_${rowNo}`).val(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount));
    //   // $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount) - parseFloat(itemCashDiscountAmount));
    //   $(`#itemTaxableAmountSpan_${rowNo}`).text(grossAmount.toFixed(2) - cashDiscountAmount.toFixed(2));    

    //   $(`#itemBaseAmountInp_${rowNo}`).val(basicPrice.toFixed(2));
    //   $(`#itemBaseAmountInCashDiscount_${rowNo}`).html(basicPrice.toFixed(2));
    //   $(`#baseAmountRadio_${rowNo}`).val(basicPrice.toFixed(2));
    //   $(`#itemBaseAmountSpan_${rowNo}`).text(basicPrice.toFixed(2));
    //   $(`#convertedItemBaseAmountSpan_${rowNo}`).text(convertedBasicPrice.toFixed(2));

    //   $(`#itemTotalDiscountHidden_${rowNo}`).val(totalDiscount.toFixed(2));
    //   $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toFixed(2));

    //   if(param === "?so_to_invoice"){
    //     $(`#itemTradeDiscountAmountSpan_${rowNo}`).text((basicPrice * itemTradeDiscountPercentageSpan) / 100);
    //   }else{
    //     $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(totalDiscount.toFixed(2));
    //   }        

    //   if (baseAmountIsCheck) {
    //     $(`#itemCashDiscountAmountSpan_${rowNo}`).text(cashDiscountAmount);
    //     $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(cashDiscountAmount);
    //   }else{
    //     $(`#itemCashDiscountAmountSpan_${rowNo}`).text((grossAmount * itemCashDiscountPercentageSpan) / 100);
    //     $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val((grossAmount * itemCashDiscountPercentageSpan) / 100);
    //   }

    //   $(`#itemTradeDiscountAmountInp_${rowNo}`).val((basicPrice * itemTradeDiscountPercentageSpan) / 100);

    //   $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount.toFixed(2));
    //   $(`#convertedItemDiscountAmountSpan_${rowNo}`).html(convertedTotalDiscount.toFixed(2));
    //   $(`#convertedItemCashDiscountAmountSpan_${rowNo}`).html(convertedTotalCashDiscount.toFixed(2));

    //   $(`#itemTotalTax1_${rowNo}`).val(totalTax.toFixed(2));
    //   $(`#itemTotalTax_${rowNo}`).html(totalTax.toFixed(2));
    //   $(`#convertedItemTaxAmountSpan_${rowNo}`).html(convertedTotalTax.toFixed(2));

    //   $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice.toFixed(2));
    //   $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice.toFixed(2));
    //   $(`#convertedItemTotalPriceSpan_${rowNo}`).html(convertedTotalItemPrice.toFixed(2));

    //   $(`#totalItemAmountModal_${rowNo}`).html(totalItemPrice.toFixed(2));

    //   calculateGrandTotalAmount();
    //   roundOffCal();
    // }

    // one item calculation 
    function calculateOneItemAmounts(rowNo) {
      let param = window.location.search.split("=")[0];
      let baseAmountIsCheck = $(`#baseAmountRadio_${rowNo}`).is(":checked");
      console.log('window.location.search.split("=")[0]', param);

      //alert(rowNo);totalItemAmountModal
      let itemQty = parseFloat($(`#itemQty_${rowNo}`).val()) || 0;
      let originalItemUnitPrice = parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
      let convertedItemUnitPrice = parseFloat($(`#convertedItemUnitPriceSpan_${rowNo}`).text()) || 0;
      let itemDiscount = parseFloat($(`#itemDiscount_${rowNo}`).val()) || 0;
      let itemDiscountVarientId = parseFloat($(`#itemDiscountVarientId_${rowNo}`).val()) || 0;
      let itemCashDiscount = parseFloat($(`#itemCashDiscount_${rowNo}`).val()) || 0;
      let convertedCurrencyValue = parseFloat($(`#curr_rate`).val()) || 0;

      let basicPrice = itemQty * originalItemUnitPrice;

      let itemTradeDiscountAmount = parseFloat($(`#itemTradeDiscountAmountSpan_${rowNo}`).text()) || 0;
      let itemCashDiscountAmount = parseFloat($(`#itemCashDiscountAmountSpan_${rowNo}`).text()) || 0;
      let itemGrossAmount = parseFloat($(`#itemGrossAmountSpan_${rowNo}`).text()) || 0;
      let itemTradeDiscountPercentageSpan = parseFloat($(`#itemTradeDiscountPercentageSpan_${rowNo}`).text()) || 0;
      let itemCashDiscountPercentageSpan = parseFloat($(`#itemCashDiscountPercentageSpan_${rowNo}`).text()) || 0;

      let itemTradeDiscountAmountInp = parseFloat($(`#itemTradeDiscountAmountInp_${rowNo}`).val()) || 0;
      let itemCashDiscountAmountInp = parseFloat($(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val()) || 0;

      let grossAmount = basicPrice - (parseFloat(basicPrice) * itemTradeDiscountPercentageSpan / 100);

      // console(grossAmount);
      // let grossAmount=itemGrossAmount;  

      let itemTradeDiscountName = $(`#itemTradeDiscountName_${rowNo}`).val();
      if (itemTradeDiscountName == 'cash') {
        // alert(1);
        grossAmount = basicPrice - itemTradeDiscountAmount;

      } else {
        //  alert(0);
        grossAmount = basicPrice - (parseFloat(basicPrice) * itemTradeDiscountPercentageSpan / 100);
      }

      let itemTaxableAmount = parseFloat((grossAmount - itemCashDiscountAmount).toFixed(2)) || 0;

      let convertedTradeDiscountAmount = itemTradeDiscountAmountInp * convertedCurrencyValue;
      let convertedGrossAmount = itemGrossAmount * convertedCurrencyValue;
      let convertedCashDiscountAmount = itemCashDiscountAmountInp * convertedCurrencyValue;
      let convertedTaxableAmount = itemTaxableAmount * convertedCurrencyValue;

      $(`#convertedItemTradeDiscountAmountSpan_${rowNo}`).text(convertedTradeDiscountAmount.toFixed(2));
      $(`#convertedGrossAmountSpan_${rowNo}`).text(convertedGrossAmount.toFixed(2));
      $(`#convertedCashDiscountAmountSpan_${rowNo}`).text(convertedCashDiscountAmount.toFixed(2));
      $(`#convertedTaxableAmountSpan_${rowNo}`).text(convertedTaxableAmount.toFixed(2));

      //alert(itemDiscount);
      let itemTax = parseFloat($(`#itemTax_${rowNo}`).val()) || 0;
      //alert(itemTax);

      $(`#multiQuantity_${rowNo}`).val(itemQty);

      let convertedBasicPrice = convertedItemUnitPrice * itemQty;

      let totalDiscount = parseFloat($(`#itemTotalDiscount1_${rowNo}`).val()) || 0;
      let convertedTotalDiscount = convertedBasicPrice * itemDiscount / 100;
      let convertedTotalCashDiscount = convertedBasicPrice * itemCashDiscount / 100;

      // let cashDiscountAmount = (basicPrice * itemCashDiscountPercentageSpan) / 100;
      let cashDiscountAmount = itemCashDiscountAmount;

      // let priceWithDiscount = basicPrice - totalDiscount;
      // let priceWithDiscount = (basicPrice - itemGrossAmount) - itemCashDiscountAmount;

      let priceWithDiscount = itemTaxableAmount;

      let convertedPriceWithDiscount = convertedBasicPrice - convertedTotalDiscount;
      let taxableAmount = grossAmount.toFixed(2) - cashDiscountAmount.toFixed(2);
      let totalTax = taxableAmount * itemTax / 100;
      //alert(totalTax);
      let convertedTotalTax = convertedPriceWithDiscount * itemTax / 100;

      let totalItemPrice = taxableAmount + totalTax;
      //alert(totalItemPrice);
      let convertedTotalItemPrice = convertedPriceWithDiscount + convertedTotalTax;

      console.log(`itemGrossAmountSpan =>>`, basicPrice, `-`, itemTradeDiscountAmount)

      console.log({
        itemQty,
        originalItemUnitPrice,
        convertedItemUnitPrice,
        itemDiscount,
        itemCashDiscount,
        convertedCurrencyValue,
        itemTradeDiscountAmount,
        itemCashDiscountAmount,
        itemGrossAmount,
        itemTaxableAmount,
        itemTradeDiscountPercentageSpan,
        itemCashDiscountPercentageSpan,
        basicPrice,
        totalDiscount,
        convertedTotalDiscount,
        grossAmount,
        cashDiscountAmount,
        priceWithDiscount,
        convertedPriceWithDiscount,
        totalTax,
      });




      // $(`#itemGrossAmountSpan_${rowNo}`).text(grossAmount.toFixed(2));
      $(`#itemGrossAmountSpan_${rowNo}`).text(basicPrice.toFixed(2) - totalDiscount.toFixed(2));
      $(`#itemGrossAmountInCashDiscount_${rowNo}`).text(basicPrice.toFixed(2) - totalDiscount.toFixed(2));
      // $(`#itemGrossAmountInCashDiscount_${rowNo}`).html(parseFloat(basicPrice) - (parseFloat(basicPrice) * itemTradeDiscountPercentageSpan / 100));
      $(`#grossAmountRadio_${rowNo}`).val(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount));
      // $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount) - parseFloat(itemCashDiscountAmount));
      $(`#itemTaxableAmountSpan_${rowNo}`).text(grossAmount.toFixed(2) - cashDiscountAmount.toFixed(2));

      $(`#itemBaseAmountInp_${rowNo}`).val(basicPrice.toFixed(2));
      $(`#itemBaseAmountInCashDiscount_${rowNo}`).html(basicPrice.toFixed(2));
      $(`#baseAmountRadio_${rowNo}`).val(basicPrice.toFixed(2));
      $(`#itemBaseAmountSpan_${rowNo}`).text(basicPrice.toFixed(2));
      $(`#convertedItemBaseAmountSpan_${rowNo}`).text(convertedBasicPrice.toFixed(2));

      $(`#itemTotalDiscountHidden_${rowNo}`).val(totalDiscount.toFixed(2));
      $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toFixed(2));

      if (param === "?so_to_invoice") {
        $(`#itemTradeDiscountAmountSpan_${rowNo}`).text((basicPrice * itemTradeDiscountPercentageSpan) / 100);
      } else {
        $(`#itemTradeDiscountAmountSpan_${rowNo}`).text(totalDiscount.toFixed(2));
      }

      if (baseAmountIsCheck) {
        $(`#itemCashDiscountAmountSpan_${rowNo}`).text(cashDiscountAmount);
        $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(cashDiscountAmount);
      } else {
        $(`#itemCashDiscountAmountSpan_${rowNo}`).text((grossAmount * itemCashDiscountPercentageSpan) / 100);
        $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val((grossAmount * itemCashDiscountPercentageSpan) / 100);
      }

      // $(`#itemTradeDiscountAmountInp_${rowNo}`).val((basicPrice * itemTradeDiscountPercentageSpan) / 100);

      $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount.toFixed(2));
      $(`#convertedItemDiscountAmountSpan_${rowNo}`).html(convertedTotalDiscount.toFixed(2));
      $(`#convertedItemCashDiscountAmountSpan_${rowNo}`).html(convertedTotalCashDiscount.toFixed(2));

      $(`#itemTotalTax1_${rowNo}`).val(totalTax.toFixed(2));
      $(`#itemTotalTax_${rowNo}`).html(totalTax.toFixed(2));
      $(`#convertedItemTaxAmountSpan_${rowNo}`).html(convertedTotalTax.toFixed(2));

      $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice.toFixed(2));
      $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice.toFixed(2));
      $(`#convertedItemTotalPriceSpan_${rowNo}`).html(convertedTotalItemPrice.toFixed(2));

      $(`#totalItemAmountModal_${rowNo}`).html(totalItemPrice.toFixed(2));

      calculateGrandTotalAmount();
      roundOffCal();
    }

    // -- generated by chatGPT 🤖🤖🤖 || imranali59059 || original code is above 👆🏾
    function calculateGrandTotalAmount() {
      console.log('first')
      let totalAmount = 0;
      let totalAmountOriginal = 0;

      let totalTaxAmount = 0;
      let totalTaxAmountOriginal = 0;
      let convertedItemTaxAmountSpan = 0;

      let totalDiscountAmount = 0;
      let totalDiscountAmountOriginal = 0;
      let convertedItemDiscountAmountSpan = 0;

      let itemBaseAmountSpan = 0;
      let itemBaseAmountInpOriginal = 0;
      let convertedItemBaseAmountSpan = 0;
      let convertedItemTotalPrice = 0;
      let totalCashDiscountAmount = 0;
      let curr_rate = parseFloat($(`#curr_rate`).val()) || 0;
      let adjustedDueAmt = parseFloat($(`.adjustedDueAmt`).text()) || 0;
      $(`.convertedAdjustedDueAmt`).text(adjustedDueAmt * curr_rate);

      // item total price
      $(".itemTotalPrice1").each(function() {
        totalAmount += parseFloat($(this).text().replace(/,/g, "")) || 0;
      });
      // alert(totalAmount);
      $(".itemTotalPrice").each(function() {
        totalAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      // alert(totalAmountOriginal);
      $(".convertedItemTotalPriceSpan").each(function() {
        convertedItemTotalPrice += parseFloat($(this).text().replace(/,/g, "")) || 0;
      });
      // alert(convertedItemTotalPrice);

      // item total tax
      $(".itemTotalTax1").each(function() {
        totalTaxAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      //alert(totalTaxAmountOriginal);
      $(".itemTotalTax").each(function() {
        totalTaxAmount += parseFloat($(this).html().replace(/,/g, "")) || 0;

      });
      //  alert(totalTaxAmount);
      $(".convertedItemTaxAmountSpan").each(function() {
        convertedItemTaxAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });

      // item total discount
      $(".itemTotalDiscountHidden").each(function() {
        totalDiscountAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      //  alert(totalDiscountAmountOriginal);
      // alert(totalDiscountAmountOriginal);
      // $(".itemTotalDiscount").each(function() {
      //   totalDiscountAmount += parseFloat($(this).html().replace(/,/g, "")) || 0;
      // });

      $(".itemTradeDiscountAmountInp").each(function() {
        totalDiscountAmount += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });

      $(".itemCashDiscountAmountHiddenInp").each(function() {
        totalCashDiscountAmount += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });

      //alert(totalDiscountAmount);
      //alert(totalDiscountAmount);
      $(".convertedItemDiscountAmountSpan").each(function() {
        convertedItemDiscountAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });
      // alert(convertedItemDiscountAmountSpan)

      // item base amount
      $(".itemBaseAmountInp").each(function() {
        itemBaseAmountInpOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
      });
      // alert(itemBaseAmountInpOriginal);
      $(".itemBaseAmountSpan").each(function() {
        itemBaseAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });
      //  alert(itemBaseAmountSpan);
      $(".convertedItemBaseAmountSpan").each(function() {
        convertedItemBaseAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
      });
      // alert(convertedItemBaseAmountSpan);

      let compInvoiceType = $("#compInvoiceType").val();
      // alert(compInvoiceType);
      let grandTotalAmountAfterOriginal = totalAmountOriginal - totalTaxAmount;
      let grandTotalAmountAfter = totalAmount - totalTaxAmount;
      //  alert(grandTotalAmountAfter);
      let convertedGrandTotalAmountWithoutTax = convertedItemTotalPrice - convertedItemTaxAmountSpan;

      let grandTotalCashDiscount = $("#grandTotalCashDiscount").text();
      let convertedGrandTotalCashDiscountAmount = grandTotalCashDiscount * curr_rate;

      console.log('compInvoiceType ✅✅✅');
      console.log(compInvoiceType);
      console.log('compInvoiceType ✅✅✅');

      if (compInvoiceType === "CBW" || compInvoiceType === "LUT" || compInvoiceType === "SEWOP" || compInvoiceType === "E") {
        //alert(111111111111);
        $(".grandSgstCgstAmt").html(0);
        $(".convertedGrandSgstCgstAmt").html(0);

        $("#grandTaxAmt").html(0);
        $("#convertedGrandTaxAmount").html(0);

        $("#grandTaxAmtInp").val(0);

        // $(".itemTaxPercentage").text(0);
        // $(".itemTotalTax").text(0);
        // $(".itemTotalTax1").val(0);
        // $(".convertedItemTaxAmountSpan").text(0);
        // alert('ok');
        $("#grandSubTotalAmt").html(itemBaseAmountSpan.toFixed(2));
        $("#grandSubTotalAmtInp").val(itemBaseAmountInpOriginal.toFixed(2));
        $("#convertedGrandSubTotalAmt").text(convertedItemBaseAmountSpan.toFixed(2));

        //$("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2));
        $("#grandTotalDiscount").html(convertedItemDiscountAmountSpan.toFixed(2));
        $("#grandTotalDiscountAmtInp").val(totalDiscountAmountOriginal.toFixed(2));

        $("#grandTotalCashDiscount").html(totalCashDiscountAmount.toFixed(2));
        $("#grandTotalCashDiscountAmtInp").val(totalCashDiscountAmount.toFixed(2));

        $("#convertedGrandTotalDiscountAmount").text(convertedItemDiscountAmountSpan.toFixed(2));

        $("#grandTotalAmt").html(grandTotalAmountAfter.toFixed(2));
        $("#grandTotalAmtInp").val(grandTotalAmountAfter.toFixed(2));
        $("#convertedGrandTotalAmt").text(convertedGrandTotalAmountWithoutTax.toFixed(2));


      } else {
        // alert('ok');
        $(".grandSgstCgstAmt").html((totalTaxAmount / 2).toFixed(2));
        // alert(1);
        $(".convertedGrandSgstCgstAmt").html((convertedItemTaxAmountSpan / 2));
        // alert(2);

        $("#grandTaxAmt").html(totalTaxAmount.toFixed(2));
        // alert(3);
        $("#convertedGrandTaxAmount").html(convertedItemTaxAmountSpan.toFixed(2));

        $("#convertedGrandTotalCashDiscountAmount").html(convertedGrandTotalCashDiscountAmount.toFixed(2));
        // alert(4);
        $("#grandTaxAmtInp").val(totalTaxAmountOriginal.toFixed(2));
        // alert(5);

        // $(".itemTaxPercentage").text(0);
        // $(".itemTotalTax").text(0);
        // $(".itemTotalTax1").val(0);
        // $(".convertedItemTaxAmountSpan").text(0);

        $("#grandSubTotalAmt").html(itemBaseAmountSpan.toFixed(2));
        // alert(6);
        $("#grandSubTotalAmtInp").val(itemBaseAmountInpOriginal.toFixed(2));
        // alert(7);
        $("#convertedGrandSubTotalAmt").text(convertedItemBaseAmountSpan.toFixed(2));
        // alert(8);

        //  $("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2));

        $("#grandTotalDiscount").html(convertedItemDiscountAmountSpan.toFixed(2));

        $("#grandTotalDiscountAmtInp").val(totalDiscountAmountOriginal.toFixed(2));

        $("#grandTotalCashDiscount").html(totalCashDiscountAmount.toFixed(2));
        $("#grandTotalCashDiscountAmtInp").val(totalCashDiscountAmount.toFixed(2));

        // alert(10);
        $("#convertedGrandTotalDiscountAmount").text(convertedItemDiscountAmountSpan.toFixed(2));
        // alert(11);

        $("#grandTotalAmt").html(totalAmount.toFixed(2));
        $("#grandTotalAmtInp").val(totalAmountOriginal.toFixed(2));
        $("#convertedGrandTotalAmt").text(convertedItemTotalPrice.toFixed(2));
      }

    }

    // currency conversion
    function currency_conversion() {
      for (elem of $(".convertedItemUnitPriceSpan")) {
        let rowNo = ($(elem).attr("id")).split("_")[1];
        let newVal = $("#curr_rate").val() * $(`#originalChangeItemUnitPriceInp_${rowNo}`).val();

        // alert($("#curr_rate").val());
        // alert($(`#originalChangeItemUnitPriceInp_${rowNo}`).val());
        newVal = newVal > 0 ? newVal : $(elem).val();

        $(elem).text(newVal.toFixed(2));
        calculateOneItemAmounts(rowNo);
      };

      let currencyIcon = ($('.currencyDropdown').val()).split("≊")[2];
      $(".currency-symbol-dynamic").text(currencyIcon);
      calculateGrandTotalAmount();
    }

    // change dynamic currency 
    $(".currencyDropdown").on("change", function() {
      let currencyIcon = ($(this).val()).split("≊")[1];
      let currencyName = ($(this).val()).split("≊")[2];
      let companyCurrencyName = $('.companyCurrencyName').text();

      if (companyCurrencyName !== currencyName) {
        currency_conversion();
        $.ajax({
          url: `ajaxs/so/ajax-currency.php`,
          type: 'GET',
          data: {
            act: 'currencyPage',
            currency: companyCurrencyName,
            currencyName
          },
          beforeSend: function() {
            $("#curr_rate").val(`Loading...`);
            $("#curr_rate").prop('disabled', true);
          },
          success: function(result) {
            let data = JSON.parse(result);
            let rate = data.data.rate;
            $(".currency-symbol-dynamic").text(currencyName);
            $("#curr_rate").val(rate);
            $("#curr_rate").prop('disabled', false);
            currency_conversion();
          },
        });
        $(`.convertedDiv`).show();
      } else {
        $("#curr_rate").val(1);
        currency_conversion();
        $(`.convertedDiv`).hide();
      }
    });

    $(document).on("keyup keydown", "#curr_rate", function() {
      currency_conversion();
    });

    currency_conversion();

    // #######################################################
    function calculateQuantity(rowNo, itemId, thisVal) {
      let itemQty = (parseFloat($(`#itemQty_${itemId}`).val()) > 0) ? parseFloat($(`#itemQty_${itemId}`).val()) : 0;
      let totalQty = 0;
      $(".multiQuantity").each(function() {
        if ($(this).data("itemid") == itemId) {
          totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
        }
      });
      let avlQty = itemQty - totalQty;

      if (avlQty < 0) {
        let totalQty = 0;
        $(`#multiQuantity_${rowNo}`).val('');
        $(".multiQuantity").each(function() {
          if ($(this).data("itemid") == itemId) {
            totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
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
      if (parseFloat(keyValue) > parseFloat(itemMaxDis)) {
        $(`#itemSpecialDiscount_${rowNo}`).text(`Special Discount`);
        $(`#itemSpecialDiscount_${rowNo}`).show();
        $(`#specialDiscount`).show();
      } else {
        $(`#itemSpecialDiscount_${rowNo}`).hide();
        $(`#specialDiscount`).hide();
      }
    }

    // item qty check
    $(document).on("keyup", ".itemQty", function() {



      let rowNo = ($(this).attr("id")).split("_")[1];
      console.log('rowNo', rowNo);

      $(`#itemDiscount_${rowNo}`).val(0);

      $(`#discount_variant_id_${rowNo}`).val(0);
      $(`#itemDiscountVarientId_${rowNo}`).val(0);

      $(`#itemTotalDiscount1_${rowNo}`).val(0);
      $(`#itemTotalDiscountHidden_${rowNo}`).val(0);

      $(`#itemTradeDiscountPercentageSpan_${rowNo}`).html(0);
      $(`#itemCashDiscountPercentageSpan_${rowNo}`).html(0);

      $(`#itemTradeDiscountAmountSpan_${rowNo}`).html(0);
      $(`#itemCashDiscountAmountSpan_${rowNo}`).html(0);

      $(`#itemTotalCashDiscountHidden_${rowNo}`).val(0);
      $(`#itemCashDiscount_${rowNo}`).val(0);
      $(`#itemTotalCashDiscount1_${rowNo}`).val(0);


      let itemVal = parseFloat($(`#itemQty_${rowNo}`).val()) > 0 ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
      let itemTradeDiscountAmount = parseFloat($(`#itemTradeDiscountAmountSpan_${rowNo}`).text()) || 0;
      let itemCashDiscountAmount = parseFloat($(`#itemCashDiscountAmountSpan_${rowNo}`).text()) || 0;
      let originalItemUnitPrice = parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) || 0;
      let basicPrice = originalItemUnitPrice * itemVal;



      // Check if the corresponding "checkQty_" element exists
      if ($(`#checkQty_${rowNo}`).length > 0) {
        let checkQty = parseFloat($(`#checkQty_${rowNo}`).val()) > 0 ? parseFloat($(`#checkQty_${rowNo}`).val()) : 0;


        // $(`#itemGrossAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount));
        // $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount) - parseFloat(itemCashDiscountAmount));

        // if (checkQty) {
        let splitQty = parseFloat($(`#checkQty_${rowNo}`).val()) > 0 ? parseFloat($(`#checkQty_${rowNo}`).val()) : 0;
        console.log('itemVal, checkQty', itemVal, checkQty, splitQty);

        if (itemVal <= splitQty) {
          $(`#itemSpecialDiscount_${rowNo}`).text(`Special Discount`);
          $(`#qtyMsg_${rowNo}`).hide();
        } else {
          console.log('wrong...');
          $(`#itemQty_${rowNo}`).val("");
          $(`#qtyMsg_${rowNo}`).show();
        }
        // }
      } else {
        // $(`#itemGrossAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount));
        // $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(basicPrice) - parseFloat(itemTradeDiscountAmount) - parseFloat(itemCashDiscountAmount));
      }
      calculateOneItemAmounts(rowNo);
    });

    $(document).on("change", ".itemQty", function() {
      // alert("The 'Trade Discount' and 'Cash Discount' has been changed!");
      // let Toast = Swal.mixin({
      //   toast: true,
      //   position: 'top-end',
      //   showConfirmButton: false,
      //   timer: 3000
      // });
      // Toast.fire({
      //   icon: 'info',
      //   title: `The Trade Discount and Cash Discount has been changed!`
      // });

    });



    // $(document).on("keyup", ".invoiceQty", function() {
    //   let rowNo = ($(this).attr("id")).split("_")[1];
    //   let invQty = (parseFloat($(`#invoiceQty_${rowNo}`).val()) > 0) ? parseFloat($(`#invoiceQty_${rowNo}`).val()) : 0;
    //   let checkItemQty = (parseFloat($(`#remainingQtyHidden_${rowNo}`).val()) > 0) ? parseFloat($(`#remainingQtyHidden_${rowNo}`).val()) : 0;

    //   if (checkItemQty) {
    //     let splitQty = parseFloat(($(`#remainingQtyHidden_${rowNo}`).val()));
    //     let remQty = (invQty - checkItemQty);
    //     if (invQty <= splitQty) {
    //       $(`#invoiceQtyMsg_${rowNo}`).hide();
    //       $(`#remainingQty_${rowNo}`).val(Math.abs(remQty));
    //     } else {
    //       console.warn('wrong...');
    //       $(`#invoiceQtyMsg_${rowNo}`).show();
    //       $(`#remainingQty_${rowNo}`).val(0);
    //     }
    //   }
    // });

    // invoice date *****************************************
    $("#invoiceDate").on("change", function(e) {
      // dynamic value
      let url = window.location.search;
      let param = url.split("=")[0];

      var invoicedate = $(this).val();
      var rowData = {};
      let flag = 0;
      $(".itemRow").each(function() {

        let itemType = $(this).attr("goodsType");
        let goodsType = $(this).data('id');
        // alert(goodsType);
        if (goodsType != 5) {
          flag++;
        }
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
            // $(".tableDataBody").html(`<option value="">Loading...</option>`);
          },
          success: function(response) {
            $(`.customitemreleaseDiv${rowId}`).hide();
            $(`.customitemreleaseDiv${rowId}`).html(response);
          }
        });
      });

      StringRowData = JSON.stringify(rowData);
      if (param !== "?create_service_invoice") {
        if (flag > 0) {
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
              $(".tableDataBody").html(`<option value="">Loading...</option>`);
            },
            success: function(response) {
              let data = JSON.parse(response);
              let itemData = data.data;
              console.log(data);
              if (data.status === "success") {
                for (let key in itemData) {
                  if (itemData.hasOwnProperty(key)) {

                    $(`#itemQty_${key}`).val(0);
                    $(`#checkQty_${key}`).val(itemData[key]);
                    $(`#checkQtySpan_${key}`).html(itemData[key]);
                    $(`#fifo_${key}`).prop('checked', true);
                    $(`#itemSellType_${key}`).html('FIFO');
                    $(`.enterQty`).val('');
                  }
                }
              }
            }
          });
        }
      }
    });

    $(document).on("keyup blur", ".originalChangeItemUnitPriceInp", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      calculateOneItemAmounts(rowNo);
      currency_conversion();
    });

    $("#compInvoiceType").on("change", function() {
      let compInvoiceType = $(this).val();
      // console.log(compInvoiceType);

      // For GST calculation----
      for (elem of $(".itemTotalTax")) {
        let rowNo = ($(elem).attr("id")).split("_")[1];

        let itemTaxBkup = $(`#itemTaxBkup_${rowNo}`).val();
        // alert(itemTaxBkup);

        if (compInvoiceType === "CBW" || compInvoiceType === "LUT" || compInvoiceType === "SEWOP" || compInvoiceType === "E") {
          $(`#itemTax_${rowNo}`).val(0);
          $(`#itemTaxPercentage_${rowNo}`).html(0);
        } else {

          $(`#itemTax_${rowNo}`).val(itemTaxBkup);
          $(`#itemTaxPercentage_${rowNo}`).html(itemTaxBkup);
        }

        calculateOneItemAmounts(rowNo);
      };

      calculateGrandTotalAmount();
    });

    $("#directInvoiceCreationBtn").on("click", function(event) {
      // console.log(event);
      var isChecked = $('#round_off_checkbox').prop('checked');
      let isValidQuantity = true;
      let isValidunitPrice = true;

      $(".itemQty").each(function() {
        let qty = $(this).val();
        if (qty <= 0 || qty === "") {
          isValidQuantity = false;
          alert("Please enter valid quantity");
          return false; // exits the each loop
        }
      });

      $(".originalChangeItemUnitPriceInp").each(function() {
        let price = $(this).val();
        if (price <= 0 || price === "") {
          isValidunitPrice = false;
          alert("Please enter unit price");
          return false; // exits the each loop
        }
      });

      if (!isValidQuantity) {
        event.preventDefault(); // prevent the default action (e.g., form submission)
        return;
      }

      if (!isValidunitPrice) {
        event.preventDefault(); // prevent the default action (e.g., form submission)
        return;
      }

      if (isChecked) {
        console.warn('Checkbox is checked.');
        $("#round_off_checkbox").val(1);
      } else {
        console.warn('Checkbox is not checked.');
        $("#round_off_checkbox").val(0);
      }
    });

    $("#round_off_checkbox").on("change", function() {
      roundOffCal();
    });

    function roundOffCal() {
      let grandTotalAmt = $("#grandTotalAmt").text();
      $(".adjustedDueAmt").text(grandTotalAmt);
      $(".adjustedCollectAmountInp").val(grandTotalAmt);
    }
    // $("#directInvoiceCreationBtn").on("click", function() {
    //   let flag = 0;
    //   let stockQty = 0;

    //   $(".checkQty").each(function() {
    //     let qty = $(this).val();
    //     stockQty = qty.split("_")[1];
    //     if (stockQty == 0) {
    //       flag++;
    //     }
    //   });

    //   $(".itemQty").each(function() {
    //     let qty = $(this).val();
    //     if (qty <= 0 || qty === "") {
    //       flag++;
    //     }
    //   });

    //   if (flag) {
    //     alert("Please choose valid input.")
    //   } else {
    //     confirm("Are you sure you want to Submit?");
    //   }
    // });

    $("#goodsType").on("change", function() {
      let goodsType = $(this).val();
      // alert("111");

      if (goodsType === "service") {
        // alert("777");
        $(".recurringDiv").show();
        // $(".fob-section").hide();
        // $(".orderFor").show();
        // alert("555");

        $("#orderForService").prop("checked", true);
        // alert("222");

        let orderForRadio = '';

        // $(".orderForRadio").on("click", function() {
        //   if ($(this).is(":checked")) {
        //     orderForRadio = $(this).val();
        //     if (orderForRadio === "service") {
        //       goodsType = "service";
        //       $("#goodsType").val("service");
        //     } else {
        //       goodsType = "project";
        //       $("#goodsType").val("project");
        //     }
        //   }
        //   $.ajax({
        //     type: "GET",
        //     url: `ajaxs/so/ajax-items-goods-type.php`,
        //     data: {
        //       act: "goodsType",
        //       goodsType
        //     },
        //     beforeSend: function() {
        //       $("#itemsDropDown").html(`<option>Loading...</option>`);
        //     },
        //     success: function(response) {
        //       $("#itemsDropDown").html(response);
        //     }
        //   });
        // });
        // $(".quickAdd").show();
      } else if (goodsType === "project") {
        $(".recurringDiv").hide();
      } else {
        $("#orderForService").prop("checked", false);
        $(".orderFor").hide();
        $(".recurringDiv").hide();
        $(".fob-section").show();
        // $(".quickAdd").show();
      }
      // alert("333");

      $.ajax({
        type: "GET",
        url: `ajaxs/so/ajax-items-goods-type.php`,
        data: {
          act: "goodsType",
          goodsType
        },
        beforeSend: function() {
          $("#itemsDropDown").html(`<option>Loading...</option>`);
        },
        success: function(response) {
          $("#itemsDropDown").html(response);
        }
      });
      // alert("444");
    });

    $("#neverExpire").on('click', function() {
      let rec = $(this);
      if (rec.is(':checked')) {
        $("#endOn").val("");
        $("#endOn").prop('disabled', true);
      } else {
        $("#endOn").prop('disabled', false);
      }
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

    function cashDiscountFunc(rowNo, keyValue) {
      let baseAmountIsCheck = $(`#baseAmountRadio_${rowNo}`).is(":checked");
      let itemBaseAmount = $(`#itemBaseAmountInp_${rowNo}`).val();
      console.log('baseAmountIsCheck', baseAmountIsCheck, keyValue);
      let itemQty = $(`#itemQty_${rowNo}`).val();
      let unitPrice = $(`#originalChangeItemUnitPriceInp_${rowNo}`).val();
      let itemGrossAmount = parseFloat($(`#itemGrossAmountSpan_${rowNo}`).text()) || 0;
      let discountAmt = parseFloat(itemGrossAmount) * parseFloat(keyValue) / 100;

      if (baseAmountIsCheck) {
        console.log('baseAmountIsCheck');

        discountAmt = parseFloat(itemBaseAmount) * parseFloat(keyValue) / 100;
      }

      $(`#itemTotalCashDiscount1_${rowNo}`).val(discountAmt);

      $(`#itemCashDiscountPercentageInp_${rowNo}`).val(`${keyValue}`);
      $(`#itemCashDiscountPercentageSpan_${rowNo}`).text(`${keyValue}`);
      $(`#itemCashDiscountAmountSpan_${rowNo}`).text(discountAmt);
      // console.log(discountAmt);
      $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(discountAmt);
      $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(itemGrossAmount) - discountAmt);
    }

    $(document).on("keyup", ".itemCashDiscount", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(this).val();

      cashDiscountFunc(rowNo, keyValue);
      calculateOneItemAmounts(rowNo);
      checkSpecialDiscount();
    });



    function cashDiscountTotalFunc(rowNo, keyValue) {
      let baseAmountIsCheck = $(`#baseAmountRadio_${rowNo}`).is(":checked");
      //alert(baseAmountIsCheck);
      let itemBaseAmount = $(`#itemBaseAmountInp_${rowNo}`).val();
      console.log('baseAmountIsCheck', baseAmountIsCheck);
      let itemQty = $(`#itemQty_${rowNo}`).val();
      let unitPrice = $(`#originalChangeItemUnitPriceInp_${rowNo}`).val();
      let itemGrossAmount = parseFloat($(`#itemGrossAmountSpan_${rowNo}`).text()) || 0;

      // let discountAmt = parseFloat(itemGrossAmount) * parseFloat(keyValue) / 100;
      let discPercentage = (100 * parseFloat(keyValue)) / parseFloat(itemGrossAmount);
      if (baseAmountIsCheck) {
        // alert(1);
        //  discountAmt = parseFloat(itemBaseAmount) * parseFloat(keyValue) / 100;
        discPercentage = (100 * parseFloat(keyValue)) / parseFloat(itemBaseAmount);
      }

      $(`#itemCashDiscount_${rowNo}`).val(discPercentage);
      //alert(keyValue);
      $(`#itemCashDiscountPercentageInp_${rowNo}`).val(`${discPercentage}`);
      $(`#itemCashDiscountPercentageSpan_${rowNo}`).text(`${discPercentage}`);
      //  alert(keyValue);
      $(`#itemCashDiscountAmountSpan_${rowNo}`).text(keyValue);
      //  alert(keyValue);
      $(`#itemCashDiscountAmountHiddenInp_${rowNo}`).val(keyValue);
      $(`#itemTaxableAmountSpan_${rowNo}`).text(parseFloat(itemGrossAmount) - keyValue);
    }




    $(document).on("keyup", ".itemTotalCashDiscount1", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(this).val();

      cashDiscountTotalFunc(rowNo, keyValue);
      calculateOneItemAmounts(rowNo);
      checkSpecialDiscount();
    });

    $(document).on("change", ".grossAmountRadio", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(`#itemCashDiscount_${rowNo}`).val();
      cashDiscountFunc(rowNo, keyValue);
      calculateOneItemAmounts(rowNo);
      checkSpecialDiscount();
    });

    $(document).on("change", ".baseAmountRadio", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(`#itemCashDiscount_${rowNo}`).val();
      cashDiscountFunc(rowNo, keyValue);
      calculateOneItemAmounts(rowNo);
      checkSpecialDiscount();
    });

    $(document).on("change", ".cashDiscountTypeRadioBtn", function() {
      let rowNo = ($(this).attr("id")).split("_")[1];
      let discountType = $(this).data("cash-discount-type");
      $(`#selectedCashDiscountType_${rowNo}`).val(discountType);
    });

    $(document).on("keyup", ".itemDiscount", function() {
      console.log('this is item discount')
      let rowNo = ($(this).attr("id")).split("_")[1];
      let keyValue = $(this).val();

      if (keyValue < 0) {
        $(this).val(0);
      }

      if (keyValue > 100) {
        $(this).val(100);
      }

      $(`#itemTradeDiscountPercentageInp_${rowNo}`).val(keyValue);
      $(`#itemTradeDiscountPercentageSpan_${rowNo}`).text(keyValue);

      calculateOneItemAmounts(rowNo);
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
      let originalItemUnitPrice = (parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) > 0) ? parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}`).val()) : 0;

      let totalAmt = itemQty * originalItemUnitPrice;
      let discountPercentage = itemDiscountAmt * 100 / totalAmt;

      $(`#itemDiscount_${rowNo}`).val(discountPercentage);

      calculateOneItemAmounts(rowNo);
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
          $(".tableDataBody").html(`<option value="">Loading...</option>`);
        },
        success: function(response) {
          $(".tableDataBody").html(response);
        }
      });
    })

    $(function() {
      $("#datepicker").datepicker({
        autoclose: true,
        todayHighlight: true
      }).datepicker('update', new Date());
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

      $(".qty" + rdcode).each(function() {
        // Parse the value as a number and add it to the sum
        var value = parseFloat($(this).val()) || 0;
        sum += value;
      });

      // console.log("Sum: " + sum);

      $("#itemSelectTotalQty_" + rdcode).html(sum);
      $("#itemQty_" + rdcode).val(sum);
      console.log('first => ' + rdcode);
      calculateOneItemAmounts(rdcode);
    }
    // ***********************************************
    // ***********************************************
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

  }); // document ready end here

  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('show');
    $('#overlay').toggleClass('show');
    $('.nav-action').toggleClass('show');
  });

  $(`#terms-and-condition`)
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });

  $('#itemsDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('.currencyDropdown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  // $('#customerDropDown')
  //   .select2()
  //   .on('select2:open', () => {
  //     $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewCustomerModal">Add New</a></div>`);
  //   });

  $('#customerDropDown').select2({
    placeholder: 'Select Customer',
    ajax: {
      url: 'ajaxs/so/ajax-customerslst-select2.php',
      dataType: 'json',
      delay: 50,
      data: function(params) {
        return {
          searchTerm: params.term // search term
        };
      },
      processResults: function(data) {
        return {
          results: data
        };
      },
      cache: true
    }
  }).on('select2:open', function(e) {
    var $results = $(e.target).data('select2').$dropdown.find('.select2-results');

    // Conditionally add the 'Add New' button based on the element
    if (!$results.find('a').length) {
      $results.append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewCustomerModal">Add New</a></div>`);
    }
  });

  $('#profitCenterDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('#servicesDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('#kamDropDown')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
  $('#placeOfSupply1')
    .select2()
    .on('select2:open', () => {
      // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
    });
</script>

<script src="<?= BASE_URL; ?>public/validations/soValidation.js?v=1"></script>
<!-- <script src="<?= BASE_URL; ?>public/validations/goodsInvoiceValidation.js"></script> -->

<script src="https://code.getmdl.io/1.2.0/material.min.js"></script>
<script>
  // *** multi step form *** //

  // DOM elements
  const DOMstrings = {
    stepsBtnClass: 'multisteps-form__progress-btn',
    stepsBtns: document.querySelectorAll(`.multisteps-form__progress-btn`),
    stepsBar: document.querySelector('.multisteps-form__progress'),
    stepsForm: document.querySelector('.multisteps-form__form'),
    stepsFormTextareas: document.querySelectorAll('.multisteps-form__textarea'),
    stepFormPanelClass: 'multisteps-form__panel',
    stepFormPanels: document.querySelectorAll('.multisteps-form__panel'),
    stepPrevBtnClass: 'js-btn-prev',
    stepNextBtnClass: 'js-btn-next'
  };

  //remove class from a set of items
  const removeClasses = (elemSet, className) => {

    elemSet.forEach(elem => {

      elem.classList.remove(className);

    });

  };

  //return exect parent node of the element
  const findParent = (elem, parentClass) => {

    let currentNode = elem;

    while (!currentNode.classList.contains(parentClass)) {
      currentNode = currentNode.parentNode;
    }

    return currentNode;

  };

  //get active button step number
  const getActiveStep = elem => {
    return Array.from(DOMstrings.stepsBtns).indexOf(elem);
  };

  //set all steps before clicked (and clicked too) to active
  const setActiveStep = activeStepNum => {

    //remove active state from all the state
    removeClasses(DOMstrings.stepsBtns, 'js-active');

    //set picked items to active
    DOMstrings.stepsBtns.forEach((elem, index) => {

      if (index <= activeStepNum) {
        elem.classList.add('js-active');
      }

    });
  };

  //get active panel
  const getActivePanel = () => {

    let activePanel;

    DOMstrings.stepFormPanels.forEach(elem => {

      if (elem.classList.contains('js-active')) {

        activePanel = elem;

      }

    });

    return activePanel;

  };

  //open active panel (and close unactive panels)
  const setActivePanel = activePanelNum => {

    //remove active class from all the panels
    removeClasses(DOMstrings.stepFormPanels, 'js-active');

    //show active panel
    DOMstrings.stepFormPanels.forEach((elem, index) => {
      if (index === activePanelNum) {
        elem.classList.add('js-active');
        setFormHeight(elem);
      }
    });
  };

  //set form height equal to current panel height
  const formHeight = activePanel => {
    const activePanelHeight = activePanel.offsetHeight;
    DOMstrings.stepsForm.style.height = `${activePanelHeight}px`;
  };

  const setFormHeight = () => {
    const activePanel = getActivePanel();
    formHeight(activePanel);
  };

  //STEPS BAR CLICK FUNCTION
  DOMstrings.stepsBar.addEventListener('click', e => {

    //check if click target is a step button
    const eventTarget = e.target;

    if (!eventTarget.classList.contains(`${DOMstrings.stepsBtnClass}`)) {
      return;
    }

    //get active button step number
    const activeStep = getActiveStep(eventTarget);

    //set all steps before clicked (and clicked too) to active
    setActiveStep(activeStep);

    //open active panel
    setActivePanel(activeStep);
  });

  //PREV/NEXT BTNS CLICK
  DOMstrings.stepsForm.addEventListener('click', e => {

    const eventTarget = e.target;

    //check if we clicked on `PREV` or NEXT` buttons
    if (!(eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`) || eventTarget.classList.contains(`${DOMstrings.stepNextBtnClass}`))) {
      return;
    }

    //find active panel
    const activePanel = findParent(eventTarget, `${DOMstrings.stepFormPanelClass}`);

    let activePanelNum = Array.from(DOMstrings.stepFormPanels).indexOf(activePanel);

    //set active step and active panel onclick
    if (eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`)) {
      activePanelNum--;

    } else {

      activePanelNum++;

    }

    setActiveStep(activePanelNum);
    setActivePanel(activePanelNum);

  });

  //SETTING PROPER FORM HEIGHT ONLOAD
  window.addEventListener('load', setFormHeight, false);

  //SETTING PROPER FORM HEIGHT ONRESIZE
  window.addEventListener('resize', setFormHeight, false);

  //changing animation via animation select !!!YOU DON'T NEED THIS CODE (if you want to change animation type, just change form panels data-attr)

  const setAnimationType = newType => {
    DOMstrings.stepFormPanels.forEach(elem => {
      elem.dataset.animation = newType;
    });
  };

  // selector onchange - changing animation
  const animationSelect = document.querySelector('.pick-animation__select');

  animationSelect.addEventListener('change', () => {
    const newAnimationType = animationSelect.value;

    setAnimationType(newAnimationType);
  });
</script>
<script>
  $(document).on("click", ".add_data", function() {
    var data = this.value;
    $("#createdatamultiform").val(data);
    // confirm('Are you sure to Submit?')
    $("#add_frm").submit();
  });
</script>

<!-- script for adding customer with GSTIN  -->
<script>
  $('#customer_gstin').focusout(function() {
    let customerGstNo = $('#customer_gstin').val();

    $.ajax({
      type: "GET",
      dataType: 'json',
      url: `<?= COMPANY_URL ?>ajaxs/ajax-gst-details.php?gstin=${customerGstNo}`,
      success: function(response) {
        let data = response.data;
        let city;
        // console.log(response)
        if (response.status == "success") {
          $('#customer_pan').prop('readonly', true);

          if (data.pradr.addr.city) {
            city = data.pradr.addr.city;
          } else {
            city = data.pradr.addr.loc;
          }
          $('#customer_pan').val((data.gstin).substring(2, 12))

          $('#trade_name').val(data.lgnm)
          $('#con_business').val(data.ctb)
          $(`.selDiv  option:eq(${(data.gstin).slice(0,2)-1})`).prop('selected', true);
          $('#city').val(city)
          $('#district').val(data.pradr.addr.dst)
          $('#location').val(data.pradr.addr.loc)
          $('#build_no').val(data.pradr.addr.bno)
          $('#flat_no').val(data.pradr.addr.flno)
          $('#street_name').val(data.pradr.addr.st)
          $('#pincode').val(data.pradr.addr.pncd)

        } else {
          $('#customer_pan').prop('readonly', false);
        }
      }
    });

  })

  // tcs hide show function
  $("#tcsAmtshowhidediv").hide();
  $(document).on('change', '.tcscheckbox', function() {
    if (this.checked) {
      $("#tcsAmtshowhidediv").show();
    } else {
      $("#tcsAmtshowhidediv").hide();
    }
  });
</script>

<script>
  $(document).ready(function() {
    // On click of the Preview button
    $('.previewBtn').click(function() {
      // Get the selected value from the <select> element
      var selectedValue = $('#terms-and-condition').val();

      // Perform AJAX request based on the selected value
      $.ajax({
        url: 'ajaxs/so/ajax-tc.php', // Replace with your API endpoint or server URL
        type: 'GET',
        data: {
          value: selectedValue, // Send the selected value to the server
          act: "tc"
        },

        success: function(response) {
          // console.log(response);
          let obj = JSON.parse(response);

          $('.tc-modal-title').html(obj['termHead']);
          $('.tc-modal-body').html(obj['termscond']);
          // Assuming the response contains the content you want to show in the modal
          // You can adjust this depending on your response structure
          // $('#modalBody').html(response.data); // Populate the modal with the response data
        },
        error: function(error) {
          // Handle any error that occurs during the AJAX request
          console.log('Error:', error);
          $('#modalBody').html('An error occurred while fetching the data.');
        }
      });
    });
  });
</script>