<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");
require_once("../../../app/v1/functions/company/func-branches.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");

// require_once("controller/pos.controller.php");

$la= getLebels($companyCountry);


$currencyName= getSingleCurrencyType($company_currency);
$quotation_createion = isset($_GET['quotation_createion']);
$sales_order_creation = isset($_GET['sales_order_creation']);
$quotation_to_so = isset($_GET['quotation_to_so']);
$create_service_invoice = isset($_GET['create_service_invoice']);
$so_to_invoice = isset($_GET['so_to_invoice']);
$pgi_to_invoice = isset($_GET['pgi_to_invoice']);
$invoiceType = 'pos';
$posType = "pos_invoice";
if (isset($_GET['pos_invoice'])) {
    $invoiceType = "pos";
    $posType = "pos_invoice";
} else if (isset($_GET['pos_salesorder'])) {
    $invoiceType = "pos";
    $posType = "pos_salesorder";
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}


$BranchSoObj = new BranchSo();

$getItemsGroupObj = $BranchSoObj->getItemsGroup();
$getItemsGroup = $getItemsGroupObj['data'];

$fetchAllItemSummaryObj = $BranchSoObj->fetchAllItemSummary();
$fetchAllItemSummary = $fetchAllItemSummaryObj['data'];
if (isset($_POST["createdatamultiform"])) {
  $addNewObj = $BranchSoObj->createDataCustomer($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}

?>

<link rel="stylesheet" href="../../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../../public/assets/listing.css">
<link rel="stylesheet" href="../../../public/assets/jquery.fancy.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<style>

    .loader-container {
            display: none; /* Initially hidden */
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            justify-content: center;
            align-items: center;
            z-index: 9999;
            text-align: center;
        }

        
    .error-message {
    color: red;
    margin-top: 10px;
}
.item-image {
    display: block;
    margin: 0 auto 10px;
    border-radius: 8px;
}


.wrapper.wrapper-isotop {
    background: #fff;
    max-width: 100%;
    height: 547px;
    box-shadow: 0px 25px 30px -20px rgba(0, 0, 0, 0.2);
    overflow-y: auto;
    overflow-x: hidden;
}

.wrapper-isotop .filter {
    padding: 20px 0;
    margin-bottom: 20px;
    border-bottom: solid 1px #e3e3e3;
    text-align: center;
    font-size: 12px;
    position: sticky;
    top: calc(100% - 548px);
    background: #fff;
    z-index: 99;
}

.wrapper-isotop .filter a {
    margin-right: 10px;
    color: #666;
    text-decoration: none;
    border: 1px solid #ccc;
    padding: 4px 15px;
    border-radius: 50px;
    display: inline-block;
}

.wrapper-isotop .filter a.current {
    background: #003060;
    border: 1px solid #003060;
    color: #f9f9f9;
}

.wrapper-isotop .grid {
    margin: 0 auto;
    padding: 10px;
    -webkit-perspective: 1000px;
    perspective: 1000px;
}
.active-payment {
    background-color: green !important;
    color: white !important;
}

.wrapper-isotop .grid-item {
    width: 180px;
    height: 100px;
    margin-bottom: 10px;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
    position: relative;
}

.wrapper-isotop .fancybox {
    display: block;
    width: 100%;
    height: 100%;
    height: 100%;
    width: 100%;
    border-radius: 4px;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    transition: all 0.5s;
    background-color: #666;
}

.grid-item:hover .fancybox {
    transform: scale(1.1);
}

.content-wrapper {
    background: #dbe5ee !important;
    height: auto !important;
}

.card-header {
    background: #fff !important;
    border-radius: 0 !important;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h4,
.card-header ion-icon {
    color: #000 !important;
    font-weight: 600;
}

#fullscreen-div {
    width: 100%;
    height: 100%;
    background-color: #dbe5ee;
}

.walkinCustomerDiv {
    display: none;
}

.card-body {
    background: #fff;
    min-height: auto !important;
}

.fancybox-wrap.fancybox-desktop.fancybox-type-inline.fancybox-opened,
.fancybox-overlay-fixed {
    display: none !important;
}

.pos-input-td input {
    height: 20px !important;
    box-shadow: none !important;
    border-radius: 2px !important;
    border-bottom: 0 !important;
}

.pos-input-td input.itemQtyWidth {
    height: 30px !important;
    width: 40px;
}

.pos-item-list .currency-symbol {
    border: 0 !important;
}

#itemsTbody tr td {
    padding: 5px 12px !important;
}

td.inp-td.pos-input-td.text-center button {
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 13px;
}

td.inp-td.pos-input-td.text-center input {
    border-radius: 0;
}

.decrement-btn {
    border-radius: 5px 0px 0px 5px;
}

.increment-btn {
    border-radius: 0px 5px 5px 0px;
}

.is-pos .bank-select-pos .select2-container {
    width: 100% !important;
}

.select-bank-paid .form-input {
    width: 100%;
}

.card.selection-card-items button {
    background: #e1f0ff;
    border: 0 !important;
    font-size: 12px !important;
    color: #000;
    padding: 10px 0;
    transition-delay: 0.2s;
    border-bottom: 2px solid #5b9ad9 !important;
}

.card.selection-card-items button:hover,
.card.selection-card-items button.active {
    background: #003060;
    color: #fff;
    border: 0;
}

/* div.oneItemCard p {
    text-align: center;
}

div.oneItemCard {
    height: 150px;
    width: 98%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 5px;
    color: #fff;
    cursor: pointer;
    background: #003060;
    font-size: 10px !important;
    border-radius: 7px;
    margin: 2px;
    line-height: 20px;
} */
 .oneItemCard {
    display: flex;
    cursor: pointer;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    background-color: #003060;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    color:white;
    height: 100%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.item-image {
    width: 100%;
    height: auto;
    max-height: 150px; /* Limit image height */
    object-fit: contain; /* Ensures the image covers the container proportionally */
    border-radius: 8px;
    margin-bottom: 10px;
}

.item-details {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
}

.item-details p {
    font-weight: bold;
    margin: 5px 0;
}

.item-details .text-danger {
    color: #dc3545;
    font-weight: bold;
}


.row.pos-row .card.same-height {
    min-height: 100%;
}

table.table.table-sales-order.pos-item-list.mt-0 tr th {
    background: #e1f0ff;
    color: #000;
}

table.invoice-pos tr th,
table.invoice-pos tr td {
    background: #fff !important;
    color: #000 !important;
}

tbody.border-bottom-td tr th,
tbody.border-bottom-td tr td {
    border-bottom: 1px solid #ccc;
    padding: 10px 0;
}

.modal.invoice-modal-pos.show .modal-dialog {
    max-width: 21%;
}

.error-qty-msg {
    font-size: 10px;
}

.barcodescannerLoading {
    border-color: green;
}

.modal.add-customer-modal .modal-dialog {
    max-width: 70%;
}

.modal.add-customer-modal .modal-dialog .modal-content .modal-body {
    height: 80vh;
}

.input-hidden {
    display: none;
}

.pay-method {
    gap: 12px;
    margin: 15px 0 10px;
}

.pay-method label {
    padding: 20px;
    display: flex;
    justify-content: space-around;
    font-size: 0.8rem !important;
    align-items: center;
    gap: 7px;
    width: 100%;
}

@media (max-width: 575px) {
    .card.direct-create-invoice-card .customer-info-form-view .select2-container {
        width: 100% !important;
    }

    .input-box.customer-select span.select2.select2-container.select2-container {
        width: 100% !important;
    }

    .wrapper-isotop .filter a {
        width: 40%;
        margin-right: 0;
        color: #666;
        text-decoration: none;
        border: 1px solid #ccc;
        padding: 10px 15px;
        border-radius: 50px;
        display: inline-block;
        margin: 10px;
    }

    .btns-group {
        margin: 15px 0;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 7px;
    }

    .btns-group button {
        width: 100px;
        padding: 10px;
    }
}

.fixed-left {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 9;
}

.scroll-right {
    overflow-y: auto;
    height: 100vh;
}

.card.selection-card-items button {
    white-space: nowrap;
    padding: 7px 2rem;
    font-size: 12px;
    font-weight: 600;
}

.pos-item-name {
    white-space: nowrap;
    width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>

<input type="hidden" value="<?php echo $branchGstinCode ?>" class="branchGstin">

  <div id="loadingSpinner" class="loader-container">
        
    </div>
<div class="content-wrapper is-pos vitwo-alpha-global">
    <section class="content">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i
                            class="fas fa-home po-list-icon"></i> Home</a></li>
                <?php if ($sales_order_creation) { ?>
                <li class="breadcrumb-item active"><a href="manage-sales-orders.php" class="text-dark"><i
                            class="fa fa-list po-list-icon"></i>Sales Orders List</a></li>
                <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                        Create Sales Orders</a></li>
                <?php } else if ($create_service_invoice) { ?>
                <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i
                            class="fa fa-list po-list-icon"></i>Invoice List</a></li>
                <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                        Create Service Invoice</a></li>
                <?php } else if ($quotation_createion) { ?>
                <li class="breadcrumb-item active"><a href="manage-quotations.php" class="text-dark"><i
                            class="fa fa-list po-list-icon"></i>Quotation List</a></li>
                <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                        Create Quotation</a></li>
                <?php } else if (isset($_GET['quotation'])) { ?>
                <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i
                            class="fa fa-list po-list-icon"></i>Invoice List</a></li>
                <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                        Create Quotation to Invoice</a></li>
                <?php } else if (isset($_GET['quotation_to_so'])) { ?>
                <li class="breadcrumb-item active"><a href="manage-sales-orders.php" class="text-dark"><i
                            class="fa fa-list po-list-icon"></i>Sales Order List</a></li>
                <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                        Create Quotation to Sales Order</a></li>
                <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
                <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i
                            class="fa fa-list po-list-icon"></i>Invoice List</a></li>
                <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                        Create PGI to Invoice</a></li>
                <?php } else { ?>
                <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i
                            class="fa fa-list po-list-icon"></i>Invoice List</a></li>
                <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                        Create Goods Invoice</a></li>
                <?php } ?>
                <li class="back-button">
                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                        <i class="fa fa-reply po-list-icon"></i>
                    </a>
                </li>
                <!-- Fullscreen icon -->
                <div id="fullscreen-icon" style="cursor: pointer; font-size: 25px">&#x26F6;</div>
            </ol>
            <form action="" method="POST" id="posFormData">
            <input type="hidden" value="0.00" name="grandTotalCashDiscountAmtInp"> 
            
                <input type="hidden" value="<?= $invoiceType ?>" name="ivType">
                <input type="hidden" name="act" value="<?= $posType ?>">
                <input type="hidden" value="<?= date("Y-m-d") ?>" name="invoiceDate">
                <!-- <input type="hidden" name="iv_varient" id="iv_varient"> -->
                <select name="iv_varient" hidden class="form-control" id="iv_varient" required>
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
                                                                                    } ?>>
                        <?= $iv_varientdata['title'] ?></option>
                    <?php } ?>
                </select>
                <div class="row pos-row" id="fullscreen-div">
                    <div class="col-lg-6 col-md-6 col-sm-12 fixed-left">
                        <div class="card same-height">
                            <!-- Customer info -->
                            <div class="card direct-create-invoice-card pos-creation-card">
                                <div class="card-header p-3 justify-content-between">
                                    <div class="left d-flex gap-2">
                                        <ion-icon name="people-outline"></ion-icon>
                                        <h4>Customer Info</h4>
                                    </div>
                                    <input type="hidden" class="customerIdInp" value="0">
                                    <div class="right">
                                        <ion-icon name="settings-outline" data-toggle="modal"
                                            data-target="#basicDetailsModal" style="cursor: pointer;"></ion-icon>
                                    </div>

                                </div>
                                <div class="card-body others-info vendor-info so-card-body" style="height: auto;">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row customer-info-form-view" style="row-gap: 15px;">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="input-box customer-select">
                                                        <label for="">Customers <span
                                                                class="text-danger">*</span></label>
                                                        <select name="customerId" id="customerDropDown"
                                                            class="form-control">
                                                            <option value="">Select Customer</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="">Select Sales Person <span
                                                                class="text-danger">*</span></label>
                                                        <select name="kamId" class="form-control" id="kamDropDown">
                                                            <option value="0">Select Sales Person</option>
                                                            <?php
                                                            $funcList = $BranchSoObj->fetchKamDetails()['data'];
                                                            foreach ($funcList as $func) {
                                                            ?>
                                                            <option value="<?= $func['kamId'] ?>">
                                                                <?= $func['kamName'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input"
                                                        style="display: flex; gap: 10px; align-items: center; margin-top: 10px;">
                                                        <label for="walkInCustomerCheckbox"
                                                            style="margin: 0 !important; user-select: none;">Walk In
                                                            Customer </label>
                                                            <?php 
                                                            $query = "SELECT `customer_id`
                                                                        FROM `erp_customer`
                                                                        WHERE `company_id` = $company_id 
                                                                        AND `location_id` = $location_id
                                                                        AND `company_branch_id` = $branch_id 
                                                                        AND `customer_authorised_person_name` = 'Walk In Customer'
                                                                        LIMIT 1";

                                                                $check_customer = queryGet($query);
                                                                $customer_id = $check_customer['data']['customer_id'];
                                                                 ?>
                                                                <input type="hidden" name="walkincustomer" id="walkincustomer" value="<?=$customer_id ?>">
                                                        <input type="checkbox" name="walkInCustomerCheckbox"
                                                            id="walkInCustomerCheckbox">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 walkinCustomerDiv">
                                                    <div class="form-input">
                                                        <label for="">Customer Name</label>
                                                        <input type="text" placeholder="Enter name"
                                                            name="walkInCustomerName" class="form-control"
                                                            id="walkInCustomerName">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 walkinCustomerDiv">
                                                    <div class="form-input">
                                                        <label for="">Customer Mobile</label>
                                                        <input type="number" placeholder="Enter mobile"
                                                            name="walkInCustomerMobile" class="form-control"
                                                            id="walkInCustomerMobile">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="">Barcode Scanner</label>
                                                        <input type="text" name="barcodescanner"
                                                            class="form-control barcodescanner" id="barcodescanner"
                                                            placeholder="Enter item code and batch number (e.g. 11000112/GRN1705490971271)">
                                                    </div>
                                                </div>
                                                <div class="barcodescannerDiv"></div>
                                                <div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- items info -->
                            <div class="card bg-white items-select-table rounded-0">
                                <div class="card-header">
                                    <ion-icon name="people-outline"></ion-icon>
                                    <h4>Items Info</h4>
                                    <input type="hidden" class="customerIdInp" value="0">
                                </div>

                                <small class="py-2 px-1 rounded alert-dark specialDiscount" id="specialDiscount"
                                    style="display: none;">Special Discount</small>
                                <table class="table table-sales-order pos-item-list mt-0">
                                    <thead>
                                        <tr>
                                            <th width="20%">Product</th>
                                            <th width="18%">Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTbody"></tbody>

                                    <tbody>
                                        <tr class="spanItemsTableTr" style="display: none;">
                                            <td colspan="3" class="text-left p-2 totalCal"
                                                style="border-bottom: 1px solid #ccc;"><span
                                                    class="spanItemsTable"></span></td>
                                            <input type="hidden" name="grandSubTotalAmtInp" id="grandSubTotalAmtInp"
                                                value="<?=inputValue(0)?>">
                                            <td colspan="2" class="p-2 text-right"
                                                style="border-bottom: 1px solid #ccc;"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-left p-2 totalCal bg-light"
                                                style="border-bottom: 1px solid #ccc;">Sub Total</sup></td>
                                            <input type="hidden" name="grandSubTotalAmtInp" id="grandSubTotalAmtInp"
                                                value="<?=inputValue(0)?>">
                                            <td colspan="2" class="p-2 text-right"
                                                style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span
                                                        id="grandSubTotalAmt"><?=decimalValuePreview(0)?></span>
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-left p-2 totalCal bg-light"
                                                style="border-bottom: 1px solid #ccc;">Total Discount</td>
                                            <input type="hidden" name="grandTotalDiscountAmtInp"
                                                id="grandTotalDiscountAmtInp" value="0">
                                            <td colspan="2" class="p-2 text-right"
                                                style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span
                                                        id="grandTotalDiscount"><?=decimalValuePreview(0)?></span>
                                                </small>
                                            </td>
                                        </tr>

                                        <tr class="p-2 igstTr" style="display:none">

                                            <td colspan="3" class="text-left p-2 totalCal bg-light"
                                                style="border-bottom: 1px solid #ccc;">IGST</td>
                                            <input type="hidden" name="grandTaxAmtInp" id="grandTaxAmtInp" value="0">
                                            <td colspan="2" class="p-2 text-right"
                                                style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span
                                                        id="grandTaxAmt">0.00</span>
                                                </small>
                                            </td>
                                        </tr>
                                        <tr class="p-2 cgstTr" style="display:none">

                                            <td colspan="3" class="text-left p-2 totalCal bg-light"
                                                style="border-bottom: 1px solid #ccc;">CGST</td>
                                            <!-- <input type="hidden" name="grandSgstCgstAmtInp" id="grandSgstCgstAmtInp" value="0"> -->
                                            <td colspan="2" class="p-2 text-right"
                                                style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span
                                                        class="grandSgstCgstAmt">0.00</span>
                                                </small>
                                            </td>
                                        </tr>
                                        <tr class="p-2 sgstTr" style="display:none">

                                            <td colspan="3" class="text-left p-2 totalCal bg-light"
                                                style="border-bottom: 1px solid #ccc;">SGST</td>
                                            <!-- <input type="hidden" name="grandSgstCgstAmtInp" id="grandSgstCgstAmtInp" value="0"> -->
                                            <td colspan="2" class="p-2 text-right"
                                                style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span
                                                        class="grandSgstCgstAmt">0.00</span>
                                                </small>
                                            </td>
                                        </tr>
                                        <tr class="p-2">
                                            <td colspan="3" class="text-left p-2 font-weight-bold totalCal bg-light"
                                                style="border-bottom: 1px solid #ccc;">Total Amount</td>
                                            <input type="hidden" name="grandTotalAmtInp" id="grandTotalAmtInp"
                                                value="0">
                                            <td colspan="2" class="p-2 text-right font-weight-bold"
                                                style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span
                                                        class="grandTotalAmt"><?=decimalValuePreview(0)?></span>
                                                </small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <span class="saveSuccessFullyMsg"></span>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 btn-group flex-btn d-flex">
                                    <a href="#" 
                                        class="btn btn-danger w-100 p-3 nextorder">Cancel</a>
                                    <button type="button"
                                        class="btn btn-primary w-100 p-3 items-search-btn float-right mr-0"
                                        data-bs-toggle="modal" data-bs-target="#paymentConfirm">Payment</button>
                                </div>

                                <!------Payment confirm modal------->
                                <div class="modal fade" id="paymentConfirm" data-bs-backdrop="static"
                                    data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-md font-bold" id="exampleModalLabel">Add
                                                    Invoice</h5>
                                                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button> -->
                                            </div>
                                            <!-- <form id="paymentForm" action="" method="post">
                                                <div class="modal-body">
                                                    <h3 class="font-bold text-sm modalCustomer"></h3>
                                                    <div class="d-flex">
                                                        <p class="text-sm font-bold mb-3">11</p>
                                                        <h4 class="font-bold text-md">Total <span class="grandTotalAmt">0.00</span></h4>
                                                    </div>

                                                    <div class="d-flex justify-content-between pay-method">
                                                        <label class="btn btn-primary">
                                                            <input type="radio" name="paymentMethod" class="input-hidden" value="cash" checked>
                                                            Pay Cash
                                                        </label>
                                                        <label class="btn btn-primary">
                                                            <input type="radio" name="paymentMethod" class="input-hidden" value="online">
                                                            Pay Online
                                                        </label>
                                                    </div>
                                                    <div class="form-input" id="bankSelect" style="display: none;">
                                                        <label for="bankId" class="text-xs">Payment Method</label>
                                                        <select name="bankId" class="form-control select2 w-100" id="bankId">
                                                            <option value="">Select Bank</option>
                                                            <?php
                                                            $bankList = $BranchSoObj->fetchCompanyBank();
                                                            foreach ($bankList['data'] as $bank) {
                                                                if ($bank['bank_name'] != "") {
                                                            ?>
                                                                    <option value="<?= $bank['id'] ?>"><?php if ($bank['bank_name']) {
                                                                                                            echo '🏦' . $bank['bank_name'];
                                                                                                        } elseif ($bank['cash_account']) {
                                                                                                            echo '💰' . $bank['cash_account'];
                                                                                                        } ?></option>
                                                            <?php }
                                                            } ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-input mt-2 mb-3" id="paidInput">
                                                        <label for="paid">Paid</label>
                                                        <input type="number" name="paid" class="form-control paidAmount">
                                                    </div>
                                                    <h4 class="font-bold text-md">Change <span class="changeAmount">0.00</span></h4>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary" name="addNewInvoiceFormSubmitBtn" id="posFinalSubmitBtn">Confirm</button>
                                                </div>
                                            </form> -->
                                            <form id="paymentForm" action="" method="post">
                                                <div class="modal-body">
                                                    <h3 class="font-bold text-sm modalCustomer"></h3>
                                                    <div class="d-flex justify-content-end">
                                                        <!-- <p class="text-sm font-bold mb-3">11</p> -->
                                                        <h4 class="text-sm">Total <span
                                                                class="grandTotalAmt text-md font-bold"
                                                                id="grandtoalsp"><?=decimalValuePreview(0)?></span></h4>
                                                    </div>

                                                    <div class="d-flex justify-content-between pay-method">
                                                        <label class="btn btn-primary">
                                                            <input type="hidden" name="cashbank_id" id="cashbank_id">
                                                            <input type="radio" name="paymentMethod"
                                                                class="input-hidden" value="cash" checked>
                                                            Pay Cash
                                                        </label>
                                                        <label class="btn btn-primary">
                                                            <input type="radio" name="paymentMethod"
                                                                class="input-hidden" value="online">
                                                            Pay Online
                                                        </label>
                                                    </div>
                                                    <div class="d-flex gap-3 select-bank-paid align-items-start">
                                                        <div class="form-input bank-select-pos" id="bankSelect"
                                                            style="display: none;">
                                                            <label for="bankId">Payment Method</label>
                                                            <select name="bankId"  class="form-control select2 w-100"
                                                                id="bankId">
                                                                <option value="">Select Bank</option>
                                                                <?php
                                                                
                                                                $bankList = $BranchSoObj->fetchCompanyBank();
                                                                usort($bankList['data'], function ($a, $b) {
                                                                    return strcasecmp($a['bank_name'] ?? $a['cash_account'] ?? '', $b['bank_name'] ?? $b['cash_account'] ?? '');
                                                                });

                                                                $optionsHtml = '';

                                                                foreach ($bankList['data'] as $bank) {
                                                                    $bank_id = $bank['id'];
                                                                    
$type_of_account = $bank['type_of_account'];
$bank_id = $bank['id']; // The bank's ID

 if($type_of_account === "cash"):
                                                                     ?>
                                                                <script>
                                                                

                                                                document.getElementById('cashbank_id').value =
                                                                    '<?php echo $bank_id; ?>';
                                                                
                                                                </script>
                                                                <?php endif;
                                                                

                                                                // Fetch access keys
                                                                $query = "SELECT `access_key`, `access_token` FROM
                                                                `erp_payment_gateway`
                                                                WHERE `company_id` = $company_id AND `location_id` =
                                                                $location_id
                                                                AND `branch_id` = $branch_id AND `bank_id` = $bank_id";
                                                                $check_bank = queryGet($query);

                                                                // Skip if keys are missing
                                                                if (empty($check_bank['data']['access_key']) ||
                                                                empty($check_bank['data']['access_token'])) {
                                                                continue;
                                                                }

                                                                $keyId = $check_bank['data']['access_key'];
                                                                $keySecret = $check_bank['data']['access_token'];

                                                                // Validate keys via Razorpay API
                                                                $ch = curl_init("https://api.razorpay.com/v1/payments");
                                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                                                                curl_setopt($ch, CURLOPT_USERPWD, "$keyId:$keySecret");
                                                                curl_exec($ch);
                                                                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                                                curl_close($ch);

                                                                // Add to options if keys are valid
                                                                if ($http_status == 200) {
                                                                $label = $bank['bank_name'] ?? $bank['cash_account'] ??
                                                                'Unknown';
                                                                $icon = $bank['bank_name'] ? '🏦' : '💰';
                                                                $optionsHtml .= "<option value='{$bank['id']}'>{$icon}
                                                                    {$label}</option>";
                                                                }
                                                                }

                                                                // Output all options
                                                                echo $optionsHtml;
                                                                ?>

                                                            </select>
                                                            <div id="errorMessage" class="error-message"></div>
                                                        </div>

                                                        <div class="form-input" id="paidInput">
                                                            <label for="paid">Paid</label>
                                                            <input type="number" name="paid" id="paidamt"
                                                                class="form-control paidAmount">
                                                                <div id="errorMessage1" class="error-message"></div>
                                                            <p class="font-bold text-xs text-right">Change <span
                                                                    class="changeAmount">0.00</span></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal" id="conclose">Close</button>
                                                    <button type="submit" class="btn btn-primary"
                                                        name="addNewInvoiceFormSubmitBtn"
                                                        id="posFinalSubmitBtn1">Confirm</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- alert modal -->
                                <div class="modal fade" id="alertModal" data-bs-backdrop="static" data-keyboard="false"
                                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content" style="width: 100%;">
                                            <!-- <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Message</h5>
                                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> 
                                            </div> -->
                                            <div class="modal-body text-center" style="padding: 31px 0px;">
                                                <span class="refNumberMsg"></span>
                                                <p class="errorMsg"></p>
                                            </div>
                                             <div class="itemListContainer"></div>
                                            <div class="modal-footer" style="display: flex;justify-content: center;">
                                                <button type="button" class="btn btn-secondary alertModalOkBtn"
                                                    data-bs-dismiss="modal">OK</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!------invoice generate modal------->
                                <div class="modal fade invoice-modal-pos" id="invoice" data-bs-backdrop="static"
                                    data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Receipt</h5>
                                                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                                            </div>
                                            <div class="modal-body">
                                                <div id="contentToPrint" class="receiptData"></div>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="#" class="btn btn-secondary nextorder" id="nextorder" >Close</a>
                                                <button class="btn btn-primary" id="printBtn">Print</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- <input type="submit" name="addNewInvoiceFormSubmitBtn" class="btn btn-primary items-search-btn float-right" value="Submit"> -->
                            </div>


                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 scroll-right">
                        <div class="card selection-card-items same-height">
                            <div class="card-header" style="overflow-x: scroll;">
                                <button type="button" class="btn btn-outline-primary border itemGroup"
                                    id="itemGroup_0">All</button>
                                <?php foreach ($getItemsGroup as $groupKey => $oneGroup) { ?>
                                <!-- <a href="#" data-filter=".<?= $oneGroup['goodGroupId'] ?>_group"><?= $oneGroup['goodGroupName'] ?></a> -->
                                <button type="button" class="btn btn-outline-primary border itemGroup"
                                    id="itemGroup_<?= $oneGroup['goodGroupId'] ?>"><?= $oneGroup['goodGroupName'] ?></button>
                                <?php } ?>
                            </div>
                            <div class="card-body">
                                <div class="form-input mb-2">
                                    <input type="search" name="itemSearchInput" class="form-control itemSearchInput"
                                        id="itemSearchInput" placeholder="Serach items here">
                                </div>
                                <div class="background-items">
                                    <span class="dataNotFound text-danger"></span>
                                    <div class="row itemGroupListDiv px-3">
                                        <?php
                                        foreach ($fetchAllItemSummary as $summaryKey => $oneSummary) {
                                            $itemStocks = $BranchSoObj->deliveryCreateItemQty($oneSummary['itemId'])['sumOfBatches'];
                                        ?>
                                        <!-- <input type="hidden" name="itemStocks" value="<?= $itemStocks ?>"> -->
                                        <div class="col-md-3 col-sm-4 col-xs-4 px-0">
                                            <div class="text-xs oneItemCard p-3"
                                                id="oneItemCard_<?= $itemStocks ?>_<?= $oneSummary['itemId'] ?>">
                                                 <?php 
                   $sqlqry = "SELECT `image_name`
FROM `erp_inventory_item_images` 
WHERE `location_id` = " . intval($oneSummary['location_id']) . " 
  AND `branch_id` = " . intval($oneSummary['branch']) . " 
  AND `company_id` = " . intval($oneSummary['company_id']) . " 
  AND `item_id` = " . intval($oneSummary['itemId']) . " 
LIMIT 1;";

$img = queryGet($sqlqry);
                    ?>
                <img 
    data-src="<?= BASE_URL ?>uploads/1/others/<?=$img['data']['image_name'] ?>" 
    alt="<?= $oneSummary['itemName'] ?>"
    class="item-image lazy"
    width="100"
    height="100"
    loading="lazy"
    onerror="this.onerror=null; this.src='<?= BASE_URL ?>uploads/1/others/vitwo_default.jpg';"
/>

                                                <p class="pos-item-name" title="<?= $oneSummary['itemName'] ?>">
                                                    <?= $oneSummary['itemName'] ?></p>
                                                <span class="text-xs"><?= decimalQuantityPreview($itemStocks) ?></span>
                                                <?php if ($itemStocks == 0 || $itemStocks <= 0) { ?>
                                                <span class="text-danger">Out of stock</span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- The POS basic details Modal -->
                <div class="modal" id="basicDetailsModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
                    aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">Basic Details</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <form id="basic_details" action="" method="post">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <label>Posting Date: <span class="text-danger">*</span></label>
                                            <div>
                                                <input type="date" value="<?= date("Y-m-d") ?>" name="invoiceDate"
                                                    id="postingDate" class="form-control" />
                                                <span class="input-group-addon"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <label>Posting Time: <span class="text-danger">*</span></label>
                                            <div>
                                                <input type="time" name="invoiceTime" id="postingTime"
                                                    value="<?= date("H:i") ?>" class="form-control" />
                                                <span class="input-group-addon postingTimeMsg"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <div class="form-input">
                                                <label for="">Profile Center <span class="text-danger">*</span></label>
                                                <select name="profitCenter" class="selct-vendor-dropdown"
                                                    id="profitCenterDropDown">
                                                    <option value="">Profit Center</option>
                                                    <?php
                                                $funcList = $BranchSoObj->fetchFunctionality()['data'];
                                                foreach ($funcList as $func) {
                                                ?>
                                                    <option value="<?= $func['functionalities_id'] ?>">
                                                        <?= $func['functionalities_name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label for="">Customer Conversion Rate</label>
                                            <input type="text" class="form-control" id="curr_rate" name="curr_rate"
                                                value="1" readonly>
                                            <div class="dynamic-currency my-2">
                                                <select id="" name="currency"
                                                    class="form-control currencyDropdown rupee-symbol">
                                                    <?php
                                                $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                                                foreach ($curr['data'] as $data) {
                                                ?>
                                                    <option
                                                        value="<?= $data['currency_id'] ?>≊<?= $data['currency_icon'] ?>≊<?= $data['currency_name'] ?>">
                                                        <?= $data['currency_icon'] ?><?= $data['currency_name'] ?>
                                                    </option>
                                                    <?php
                                                }
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
    </section>
</div>
</div>

<!-- add new customer modal start here 👇👇 -->
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
<!-- add new customer modal end here ☝️☝️ -->



<?php require_once("../../common/footer.php"); ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>


<!-- <script src="<?= BASE_URL; ?>public/validations/soValidation.js?v=1"></script> -->
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

function initializeLazyLoading() {
    const lazyImages = document.querySelectorAll("img.lazy");

    const lazyLoad = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;

                // Create a temporary image to check if the URL is valid
                const tempImage = new Image();
                tempImage.onload = function () {
                    img.src = img.dataset.src; // Load the actual image if valid
                };
                tempImage.onerror = function () {
                    img.src = "<?= BASE_URL ?>uploads/1/others/vitwo_default.jpg"; // Load default image
                };

                // Assign the data-src to the temporary image to test loading
                tempImage.src = img.dataset.src;

                img.classList.remove("lazy");
                observer.unobserve(img); // Stop observing this image
            }
        });
    };

    const observer = new IntersectionObserver(lazyLoad, {
        root: null, // Use the viewport as the root
        rootMargin: "0px 0px 50px 0px", // Start loading 50px before entering the viewport
        threshold: 0.1 // Trigger when 10% of the image is visible
    });

    lazyImages.forEach(img => observer.observe(img));
}

// Initialize lazy loading on DOMContentLoaded
document.addEventListener("DOMContentLoaded", initializeLazyLoading);



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
    let paymentSuccess = false;
    var customer__ID = '<?= $customerId ?>';

    // start customer section 👇👇
    // add customers
    $("#addCustomerBtn").on("click", function(e) {
        e.preventDefault();
        let customerName = $("#customerName").val();
        let customerEmail = $("#customerEmail").val();
        let customerPhone = $("#customerPhone").val();
        if (customerPhone != "") {
            $.ajax({
                type: "POST",
                url: `../ajaxs/so/ajax-customers.php`,
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
            $("#customerPhoneMsg").html(
                `<span class="text-xs text-danger">Phone number is required</span>`);
        }
    });

    function loadCustomers() {
        $.ajax({
            type: "GET",
            url: `../ajaxs/so/ajax-customers.php`,
            data: {
                customerId: '1'
            },
            beforeSend: function() {
                $("#customerDropDown").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                $("#customerDropDown").html(response);
                console.log('response🍉🍉');
                console.log(response);
            }
        });
    }

    function addCustomerFunc(customerId) {
        $.ajax({
            type: "GET",
            url: `../ajaxs/so/ajax-customers-address.php`,
            data: {
                act: "customerAddress",
                customerId
            },
            beforeSend: function() {
                $("#shipTo").html(`Loding...`);
            },
            success: function(response) {
                let data = JSON.parse(response);
                $("#shipTo").html(data.data);
            }
        });

        $(".customerIdInp").val(customerId);
        $.ajax({
            type: "GET",
            url: `../ajaxs/so/ajax-customers-list.php`,
            data: {
                act: "listItem",
                customerId
            },
            beforeSend: function() {
                $("#customerInfo").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                alert(response);
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

    // addCustomerFunc('<?= $customerId; ?>');

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
    });

    // customerDetailsInfo(customer__ID);

    function customerDetailsInfo(customerId) {
        let searchUrl = window.location.search;
        let param = searchUrl.split("=")[0];
        $.ajax({
            type: "GET",
            url: `../ajaxs/so/ajax-customers-list.php`,
            data: {
                act: "listItem",
                customerId
            },
            beforeSend: function() {
                $("#customerInfo").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                $("#customerInfo").html(response);
                let creditPeriod = $("#spanCreditPeriod").text();
                $("#inputCreditPeriod").val(creditPeriod);

                let stateCodeSpanElement = $(".stateCodeSpan");
                let stateCodeSpan = stateCodeSpanElement.length > 0 ? stateCodeSpanElement.text()
                    .trim() : null;
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
                        let customerGstinCode = $(".customerGstinCode").val();
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
                        url: "../ajaxs/so/ajax-customers-invoice-log.php",
                        type: "GET",
                        data: {
                            act: "customersInvoiceLog",
                            customerId
                        },
                        success: function(response2) {
                            let data2 = JSON.parse(response2);
                            if (data2.status == "success") {
                                let profit_center = data2.data.profit_center;
                                let kamId = data2.data.kamId;
                                let complianceInvoiceType = data2.data
                                    .complianceInvoiceType;
                                let placeOfSupply = data2.data.placeOfSupply;
                                let invoiceNoFormate = data2.data.invoiceNoFormate;
                                let bank = data2.data.bank;

                                console.log("iv " + invoiceNoFormate);
                                $("#profitCenterDropDown").val(profit_center).trigger(
                                    "change");
                                $("#compInvoiceType").val(complianceInvoiceType)
                                    .trigger("change");
                                $("#kamDropDown").val(kamId).trigger("change");
                                $("#bankId").val(bank).trigger("change");
                                // $("#placeOfSupply1").val(placeOfSupply).trigger("change");
                                $("#iv_varient").val(invoiceNoFormate).trigger(
                                    "change");
                            } else {
                                console.log('somthing went wrong');
                                $("#profitCenterDropDown").val('').trigger("change");
                                $("#compInvoiceType").val('R').trigger("change");
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
    // end customer section☝️☝️❌❌❌

   document.getElementById("printBtn").addEventListener("click", function(event) {
    // Prevent the default action of the button click (e.g., form submission or page navigation)
    event.preventDefault();
    
    var contentToPrint = document.getElementById("contentToPrint");
    var printWindow = window.open('', '', 'height=500,width=800');
    
    // Write content to the print window
    printWindow.document.write('<html><head><title>Print</title></head><body>');
    printWindow.document.write(contentToPrint.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    // Trigger the print dialog
    printWindow.print();

    // Try closing the print window after a short delay (to give the print dialog time to open)
    setTimeout(function() {
        if (!printWindow.closed) {
            printWindow.close();
        }
    }, 1);  // Adjust the timeout value if necessary
});

document.getElementsByClassName("nextorder")[0].addEventListener("click", function() {
    // Select all modals using their common class name (assuming 'modal' class)
    var modals = document.getElementsByClassName('modal');
    
    // Clear customer drop down, kam drop down, walkInCustomer checkbox, and other fields
    document.getElementById('customerDropDown').value = '';
    $('#customerDropDown').trigger('change');
    
    document.getElementById('kamDropDown').value = '0';
    $('#kamDropDown').trigger('change');
    
    document.getElementById('walkInCustomerCheckbox').checked = false;
    
    document.getElementById('itemsTbody').innerHTML = '';  // Clears all rows in tbody
    
    document.getElementById('paidamt').value = '';
    $(".walkinCustomerDiv").hide();
     $("#hiddenCustomerField").remove();
    document.querySelector('.saveSuccessFullyMsg').textContent = '';
    
    document.getElementById('barcodescanner').value = '';
    $('input[name="paymentMethod"][value="cash"]').prop('checked', true).trigger('change');

    
    calculateOneItemAmounts(0, 0);
    
    // Loop through each modal and hide it
    Array.from(modals).forEach(function(modal) {
        modal.style.display = 'none';  // Hide the modal
    });
});

document.getElementById("nextorder").addEventListener("click", function() {
    // Select all modals using their common class name (assuming 'modal' class)
    var modals = document.querySelectorAll('.modal');
   $('#customerDropDown').val('').trigger('change');
   $("#kamDropDown").val('0').trigger('change');
   $('#walkInCustomerCheckbox').prop('checked', false);
   $('#itemsTbody').empty();
    $(".walkinCustomerDiv").hide();
     $("#hiddenCustomerField").remove();
   $("#paidamt").val('');
   $('.saveSuccessFullyMsg').text('');
   $('input[name="paymentMethod"][value="cash"]').prop('checked', true).trigger('change');
   $("#barcodescanner").val('');
   $("#customerDropDown").val("").trigger("change");
   $("#customerDropDown").prop("disabled", false);
   $("#walkInCustomerName").val('');
   $("#walkInCustomerMobile").val('');     
   calculateOneItemAmounts(0,0);
    // Loop through each modal and hide it
    $('.modal').each(function() {
    $(this).modal('hide'); // Hide the modal
});

});  

$('#conclose').click(function() {
    $("#paidamt").val('');
});
function default_call()
{
    var modals = document.querySelectorAll('.modal');
   $('#customerDropDown').val('').trigger('change');
   $("#kamDropDown").val('0').trigger('change');
   $('#walkInCustomerCheckbox').prop('checked', false);
   $('#itemsTbody').empty();
   
   $("#paidamt").val('');
   $('.saveSuccessFullyMsg').text('');
   $('input[name="paymentMethod"][value="cash"]').prop('checked', true).trigger('change');
   $("#barcodescanner").val('');
   $("#customerDropDown").val("").trigger("change");
        $("#customerDropDown").prop("disabled", false);
        $("#walkInCustomerName").val('');
   $("#walkInCustomerMobile").val('');
   calculateOneItemAmounts(0,0);
    // Loop through each modal and hide it
   $('.modal').each(function() {
    $(this).modal('hide'); // Hide the modal
});

}





    // new search function                                     
    function searchGroup() {
        let groupId = $('#groupIdSearch').val();
        let searchValue = $(this).val().toLowerCase();
        $.ajax({
            type: "GET",
            url: `ajax/ajax-items-search-new.php`,
            data: {
                act: "itemGroupList",
                groupId,
                searchValue
            },
            beforeSend: function() {
                $(".itemGroupListDiv").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                $(".itemGroupListDiv").html(response);
                initializeLazyLoading();
            }
        });
    }

    // debouncing function
    function debounce(func, wait = 250, immediate = false) {
        var timeout;
        return function() {
            var context = this,
                args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    //   new ajax search event
    $(".itemSearchInput").on("keyup", debounce(searchGroup));

    // fetch group wise item list 
    $(".itemGroup").on('click', function() {
        let groupId = ($(this).attr("id")).split("_")[1];

        $.ajax({
            type: "GET",
            url: `ajax/ajax-items-group-list.php`,
            data: {
                act: "itemGroupList",
                groupId
            },
            beforeSend: function() {
                $(".itemGroupListDiv").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                $(".itemGroupListDiv").html(response);
                initializeLazyLoading();
            }
        });
    });

    // fetch items by click
    $(document).on("click", ".oneItemCard", function() {
        let itemStock = ($(this).attr("id")).split("_")[1];
        let itemId = ($(this).attr("id")).split("_")[2];

        if (itemStock > 0) {
            $.ajax({
                type: "GET",
                url: `ajax/ajax-items-list.php`,
                data: {
                    act: "itemsList",
                    itemId
                },
                beforeSend: function() {
                    $(".spanItemsTableTr").show();
                    $(".spanItemsTable").html(`<span>Loding...</span>`);
                },
                success: function(response) {
                    var newRow = $(
                        response); // Convert the response HTML to a jQuery object
                    var newItemId = newRow.data('id');

                    var parts = newItemId.split('_');
                    var itemId = parts[1];
                    var existingRow = $("#itemsTbody").find(`tr[data-id='${newItemId}']`);

                    if (existingRow.length > 0) {


                        var key = existingRow.data('row');
                        let itemQty = (helperQuantity($(`#itemQty_${key}_${itemId}`).val()) >
                            0) ? helperQuantity($(
                            `#itemQty_${key}_${itemId}`).val()) : 0;
                        let checkQty = (helperQuantity($(`#checkQty_${key}`).val()) > 0) ?
                        helperQuantity($(`#checkQty_${key}`)
                                .val()) : 0;
                        $(`#itemQty_${key}_${itemId}`).val(parseInt(itemQty) + 1);
                        itemQty++;



                        checkLiveStock(key, itemId, itemQty, checkQty);

                        calculateOneItemAmounts(key, itemId);
                       $(".spanItemsTableTr").hide();
                        $(`.spanItemsTable`).html('');
                    } else {
                        // If the row doesn't exist, append the new row
                        $("#itemsTbody").append(response);
                        $(".spanItemsTableTr").hide();
                        $(`.spanItemsTable`).html('');

                        // Recalculate the amounts for the new row
                        calculateOneItemAmounts($("#itemsTbody tr").last().data("row"),
                            itemId);
                    }
                }
            });
        } else {
            alert(`Don't have available stocks`);
        }
    });

    // calculate one item amount
    function calculateOneItemAmounts(rowNo, itemId) {
        let itemQty = helperQuantity($(`#itemQty_${rowNo}_${itemId}`).val()) || 0;
        let originalItemUnitPrice = helperAmount($(`#originalChangeItemUnitPriceInp_${rowNo}_${itemId}`).val()) ||
            0;
        let convertedItemUnitPrice = helperAmount($(`#convertedItemUnitPriceSpan_${rowNo}`).text()) || 0;
        let itemDiscount = helperAmount($(`#itemDiscount_${rowNo}_${itemId}`).val()) || 0;
        let itemTax = helperAmount($(`#itemTax_${rowNo}`).val()) || 0;

        $(`#multiQuantity_${rowNo}`).val(itemQty);

        let basicPrice = originalItemUnitPrice * itemQty;
        let convertedBasicPrice = convertedItemUnitPrice * itemQty;

        let totalDiscount = basicPrice * itemDiscount / 100;
        let convertedTotalDiscount = convertedBasicPrice * itemDiscount / 100;

        let priceWithDiscount = basicPrice - totalDiscount;
        let convertedPriceWithDiscount = convertedBasicPrice - convertedTotalDiscount;

        let totalTax = priceWithDiscount * itemTax / 100;
        let convertedTotalTax = convertedPriceWithDiscount * itemTax / 100;

        let totalItemPrice = priceWithDiscount + totalTax;
        let convertedTotalItemPrice = convertedPriceWithDiscount + convertedTotalTax;

        $(`#itemBaseAmountInp_${rowNo}`).val(basicPrice);
        $(`#itemBaseAmountSpan_${rowNo}`).text(inputValue(basicPrice));
        $(`#convertedItemBaseAmountSpan_${rowNo}`).text(convertedBasicPrice);

        $(`#itemTotalDiscountHidden_${rowNo}`).val(totalDiscount);
        $(`#itemTotalDiscount1_${rowNo}_${itemId}`).val(inputValue(totalDiscount));
        $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount);
        $(`#convertedItemDiscountAmountSpan_${rowNo}`).html(convertedTotalDiscount);

        $(`#itemTotalTax1_${rowNo}`).val(totalTax);
        $(`#itemTotalTax_${rowNo}`).html(inputValue(totalTax));
        $(`#convertedItemTaxAmountSpan_${rowNo}`).html(convertedTotalTax);

        $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice);
        $(`#itemTotalPrice1_${rowNo}`).html(inputValue(totalItemPrice));
        $(`#convertedItemTotalPriceSpan_${rowNo}`).html(convertedTotalItemPrice);

        $(`#totalItemAmountModal_${rowNo}`).html(decimalAmount(totalItemPrice));
        calculateGrandTotalAmount();
    }

    $('#paymentConfirm').on('show.bs.modal', function(event) {
        var isValid = true;
        var hasValidItem = false;

        // Validate each item row
        $('.itemRow').each(function() {
            var priceInput = $(this).find('.originalChangeItemUnitPriceInp');
            var qtyInput = $(this).find('.itemQty');

            var price = helperAmount(priceInput.val());
            var qty = parseInt(qtyInput.val());

            if (price === 0 || qty === 0) {
                isValid = false;
                priceInput.addClass('is-invalid');
                qtyInput.addClass('is-invalid');
            } else {
                priceInput.removeClass('is-invalid');
                qtyInput.removeClass('is-invalid');
                hasValidItem = true; // At least one valid item
            }
        });

        // If no valid items
        if (!hasValidItem) {
            isValid = false;
            // alert('Please ensure at least one item');
        }

        // Validate customer dropdown
        if ($('#walkInCustomerCheckbox').is(':checked')) {} else {
            if ($('#customerDropDown').val() === "") {
                isValid = false;

                // Add is-invalid to the select2 container
                var select2Container = $('#customerDropDown').next('.select2-container');
                select2Container.addClass('is-invalid');

                // Append the invalid-feedback message next to the Select2 container
                var customerFeedback = select2Container.next('.invalid-feedback');
                if (customerFeedback.length === 0) {
                    select2Container.after(
                        '<div class="invalid-feedback">Please select a customer.</div>');
                } else {
                    customerFeedback.text('Please select a customer.');
                }
            } else {
                // Remove the error styling
                var select2Container = $('#customerDropDown').next('.select2-container');
                select2Container.removeClass('is-invalid');
                select2Container.next('.invalid-feedback').text('');
            }
        }

        // Validate sales person dropdown
        if ($('#kamDropDown').val() === "0") {
            isValid = false;
            $('#kamDropDown').addClass('is-invalid');
            $('#kamDropDown').next('.invalid-feedback').text('Please select a sales person.');
        } else {
            $('#kamDropDown').removeClass('is-invalid');
            $('#kamDropDown').next('.invalid-feedback').text('');
        }

        // Validate walk-in customer details if checkbox is checked
        if ($('#walkInCustomerCheckbox').is(':checked')) {
            if ($('#walkInCustomerName').val() === "") {
                isValid = false;
                $('#walkInCustomerName').addClass('is-invalid');
                $('#walkInCustomerName').next('.invalid-feedback').text(
                    'Please enter the walk-in customer name.');
            } else {
                $('#walkInCustomerName').removeClass('is-invalid');
                $('#walkInCustomerName').next('.invalid-feedback').text('');
            }

            if ($('#walkInCustomerMobile').val() === "" || !/^\d{10}$/.test($('#walkInCustomerMobile')
                    .val())) {
                isValid = false;
                $('#walkInCustomerMobile').addClass('is-invalid');
                $('#walkInCustomerMobile').next('.invalid-feedback').text(
                    'Please enter a valid 10-digit mobile number for the walk-in customer.');
            } else {
                $('#walkInCustomerMobile').removeClass('is-invalid');
                $('#walkInCustomerMobile').next('.invalid-feedback').text('');
            }
        } else {
            $('#walkInCustomerName').removeClass('is-invalid');
            $('#walkInCustomerName').next('.invalid-feedback').text('');
            $('#walkInCustomerMobile').removeClass('is-invalid');
            $('#walkInCustomerMobile').next('.invalid-feedback').text('');
        }

        // Prevent modal from opening if validation fails
        if (!isValid) {
            event.preventDefault();
            alert('Something went wrong. Please fix the errors before proceeding.');
        }
    });


    // calculate grand total amount
    function calculateGrandTotalAmount() {
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

        // item total price
        $(".itemTotalPrice1").each(function() {
            totalAmount += helperAmount($(this).text().replace(/,/g, "")) || 0.00;
        });
        $(".itemTotalPrice").each(function() {
            totalAmountOriginal += helperAmount($(this).val().replace(/,/g, "")) || 0.00;
        });
        $(".convertedItemTotalPriceSpan").each(function() {
            convertedItemTotalPrice += helperAmount($(this).text().replace(/,/g, "")) || 0.00;
        });

        // item total tax
        $(".itemTotalTax1").each(function() {
            totalTaxAmountOriginal += helperAmount($(this).val().replace(/,/g, "")) || 0.00;
        });
        $(".itemTotalTax").each(function() {
            totalTaxAmount += helperAmount($(this).html().replace(/,/g, "")) || 0.00;
        });
        $(".convertedItemTaxAmountSpan").each(function() {
            convertedItemTaxAmountSpan += helperAmount($(this).html().replace(/,/g, "")) || 0.00;
        });

        // item total discount
        $(".itemTotalDiscountHidden").each(function() {
            totalDiscountAmountOriginal += helperAmount($(this).val().replace(/,/g, "")) || 0.00;
        });
        $(".itemTotalDiscount").each(function() {
            totalDiscountAmount += helperAmount($(this).html().replace(/,/g, "")) || 0.00;
        });
        $(".convertedItemDiscountAmountSpan").each(function() {
            convertedItemDiscountAmountSpan += helperAmount($(this).html().replace(/,/g, "")) || 0.00;
        });

        // item base amount
        $(".itemBaseAmountInp").each(function() {
            itemBaseAmountInpOriginal += helperAmount($(this).val().replace(/,/g, "")) || 0.00;
        });
        $(".itemBaseAmountSpan").each(function() {
            itemBaseAmountSpan += helperAmount($(this).html().replace(/,/g, "")) || 0.00;
        });
        $(".convertedItemBaseAmountSpan").each(function() {
            convertedItemBaseAmountSpan += helperAmount($(this).html().replace(/,/g, "")) || 0.00;
        });

        let compInvoiceType = $("#compInvoiceType").val();
        let grandTotalAmountAfterOriginal = totalAmountOriginal - totalTaxAmount;
        let grandTotalAmountAfter = totalAmount - totalTaxAmount;
        let convertedGrandTotalAmountWithoutTax = convertedItemTotalPrice - convertedItemTaxAmountSpan;

        if (compInvoiceType === "CBW" || compInvoiceType === "LUT" || compInvoiceType === "SEWOP") {
            $(".grandSgstCgstAmt").html(0.00);
            $(".convertedGrandSgstCgstAmt").html(0.00);

            $("#grandTaxAmt").html(0.00);
            $("#convertedGrandTaxAmount").html(0.00);

            $("#grandTaxAmtInp").val(0.00);

            $(".grandTotalAmt").html(inputValue(grandTotalAmountAfter));
            $("#grandTotalAmtInp").val(grandTotalAmountAfter.toFixed(2));
            $("#convertedGrandTotalAmt").text(convertedGrandTotalAmountWithoutTax.toFixed(2));
        } else {
            $(".grandSgstCgstAmt").html((totalTaxAmount / 2).toFixed(2));
            $(".convertedGrandSgstCgstAmt").html((convertedItemTaxAmountSpan / 2).toFixed(2));

            $("#grandTaxAmt").html(decimalAmount(totalTaxAmount));
            $("#convertedGrandTaxAmount").html(convertedItemTaxAmountSpan.toFixed(2));

            $("#grandTaxAmtInp").val(decimalAmount(totalTaxAmountOriginal));

            $("#grandSubTotalAmt").html(inputValue(itemBaseAmountSpan));
            $("#grandSubTotalAmtInp").val(itemBaseAmountInpOriginal.toFixed(2));
            $("#convertedGrandSubTotalAmt").text(convertedItemBaseAmountSpan.toFixed(2));

            $("#grandTotalDiscount").html(inputValue(totalDiscountAmount));
            $("#grandTotalDiscountAmtInp").val(totalDiscountAmountOriginal.toFixed(2));
            $("#convertedGrandTotalDiscountAmount").text(convertedItemDiscountAmountSpan.toFixed(2));

            $(".grandTotalAmt").html(inputValue(totalAmount));
            $("#grandTotalAmtInp").val(totalAmountOriginal.toFixed(2));
            $("#convertedGrandTotalAmt").text(convertedItemTotalPrice.toFixed(2));
        }
    }

    // change amount by click on unit price 
    $(document).on("keyup blur", ".originalChangeItemUnitPriceInp", function() {
        let rowNo = ($(this).attr("id")).split("_")[1];
        let itemId = ($(this).attr("id")).split("_")[2];

        calculateOneItemAmounts(rowNo, itemId);
    });

    // change value by click item qty
    $(document).on("input blur", ".itemQty", function() {
        let rowNo = ($(this).attr("id")).split("_")[1];
        let itemId = ($(this).attr("id")).split("_")[2];
        let enterQty = helperQuantity($(this).val());
        let checkQty = $(`#checkQty_${rowNo}`).val();

        checkLiveStock(rowNo, itemId, enterQty, checkQty);
        calculateOneItemAmounts(rowNo, itemId);
    });

    function checkLiveStock(rowNo, itemId, enterQty, checkQty) {
        $.ajax({
            type: "GET",
            url: `ajax/ajax-items-search.php`,
            data: {
                act: 'checkStock',
                enterQty,
                itemId
            },
            beforeSend: function() {
                $(`#qtyMsg_${rowNo}`).html(`<span class="text-secondary">Loding...</span>`);
                // $(`#posFinalSubmitBtn`).prop('disabled', true);
            },
            success: function(response) {
                if (response === "warning") {
                    $(`#qtyMsg_${rowNo}`).html(`Low Stocks`);
                    // $(`#posFinalSubmitBtn`).prop('disabled', true);
                    $(`#itemQty_${rowNo}_${itemId}`).css("background", "#FFEBEE");
                } else {
                    $(`#itemQty_${rowNo}_${itemId}`).css("background", "#C8E6C9");
                    $(`#qtyMsg_${rowNo}`).html('');
                    // $(`#posFinalSubmitBtn`).prop('disabled', false);
                }
            },
            complete: function() {
                console.log('response is complete');
            }
        });
    }

    // increment
    $(document).on("click", ".itemIncrementBtn", function() {
        let key = ($(this).attr("id")).split("_")[1];
        let itemId = ($(this).attr("id")).split("_")[2];
        console.log(key);
        console.log(itemId);
        let itemQty = (helperQuantity($(`#itemQty_${key}_${itemId}`).val()) > 0) ? helperQuantity($(
            `#itemQty_${key}_${itemId}`).val()) : 0;
        let checkQty = (helperQuantity($(`#checkQty_${key}`).val()) > 0) ? helperQuantity($(`#checkQty_${key}`)
            .val()) : 0;
        $(`#itemQty_${key}_${itemId}`).val(inputQuantity(parseInt(itemQty) + 1));
        itemQty++;

        checkLiveStock(key, itemId, itemQty, checkQty);

        calculateOneItemAmounts(key, itemId);
    });

    // decrement
    $(document).on("click", ".itemDecrementBtn", function() {
        let key = ($(this).attr("id")).split("_")[1];
        let itemId = ($(this).attr("id")).split("_")[2];
        let itemQty = (helperQuantity($(`#itemQty_${key}_${itemId}`).val()) > 0) ? helperQuantity($(
            `#itemQty_${key}_${itemId}`).val()) : 0;
        let checkQty = (helperQuantity($(`#checkQty_${key}`).val()) > 0) ? helperQuantity($(`#checkQty_${key}`)
            .val()) : 0;

        // console.log(itemQty);

        if (parseInt(itemQty) > 0) {
            $(`#itemQty_${key}_${itemId}`).val(inputQuantity(itemQty - 1));
            itemQty--;

        }
        checkLiveStock(key, itemId, itemQty, checkQty);

        // console.log(itemQty);                              

        calculateOneItemAmounts(key, itemId);
    });

    // item max discount function
    function itemMaxDiscount(rowNo, keyValue = 0) {
        let itemMaxDis = $(`#itemMaxDiscount_${rowNo}`).html();

        if (parseFloat(keyValue) > parseFloat(itemMaxDis)) {
            console.log('max discount is over');
            $(`#itemSpecialDiscount_${rowNo}`).text(`Special Discount`);
            $(`#itemSpecialDiscount_${rowNo}`).show();
            // $(`#specialDiscount`).show();
        } else {
            $(`#itemSpecialDiscount_${rowNo}`).hide();
            // $(`#specialDiscount`).hide();
        }
    }

    // checkSpecialDiscount function
    function checkSpecialDiscount() {
        let isSpecialDiscountApplied = false;

        $(".itemDiscount").each(function() {
            let rowNum = ($(this).attr("id")).split("_")[1];
            let discountPercentage = helperAmount($(this).val());
            discountPercentage = discountPercentage > 0 ? discountPercentage : 0;
            let maxDiscountPercentage = helperAmount($(`#itemMaxDiscount_${rowNum}`).html());
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

    // item discount(%) on keyup
    $(document).on("keyup", ".itemDiscount", function() {
        let rowNo = ($(this).attr("id")).split("_")[1];
        let itemId = ($(this).attr("id")).split("_")[2];
        let keyValue = $(this).val();
        calculateOneItemAmounts(rowNo, itemId);
        itemMaxDiscount(rowNo, keyValue);
        checkSpecialDiscount();
    });

    // item discount amount on keyup
    $(document).on("blur", ".itemTotalDiscount1", function() {
        let rowNo = ($(this).attr("id")).split("_")[1];
        let itemId = ($(this).attr("id")).split("_")[2];
        let itemDiscountAmt = ($(this).val());

        let itemQty = (helperQuantity($(`#itemQty_${rowNo}_${itemId}`).val()) > 0) ? helperQuantity($(
            `#itemQty_${rowNo}_${itemId}`).val()) : 0;
        let originalItemUnitPrice = (helperAmount($(`#originalChangeItemUnitPriceInp_${rowNo}_${itemId}`)
            .val()) > 0) ? helperAmount($(`#originalChangeItemUnitPriceInp_${rowNo}_${itemId}`)
            .val()) : 0;

        let totalAmt = itemQty * originalItemUnitPrice;
        let discountPercentage = itemDiscountAmt * 100 / totalAmt;

        $(`#itemDiscount_${rowNo}_${itemId}`).val(discountPercentage);

        calculateOneItemAmounts(rowNo, itemId);
    });

    // delete item btn 
    $(document).on("click", ".delItemBtn", function() {
        $(this).parent().parent().remove();
        calculateGrandTotalAmount();
    });

    // paid amount
    $(".paidAmount").on("keyup", function() {
        let paidAmount = (helperAmount($(this).val()) > 0) ? helperAmount($(this).val()) : 0;
        let grandTotalAmt = (helperAmount($("#grandTotalAmtInp").val()) > 0) ? helperAmount($(
            "#grandTotalAmtInp").val()) : 0;
        let changeAmount = grandTotalAmt - paidAmount;
        if (grandTotalAmt < paidAmount) {
            $(".changeAmount").html(Math.abs(changeAmount.toFixed(2)));
        }
    });

    // barcodescanner
    $("#barcodescanner").on("input", function() {
        let itemCode = ($(this).val()).split("/")[0];
        let barcode = ($(this).val()).split("/")[1];
        console.log(itemCode, barcode);
        $.ajax({
            type: "GET",
            url: `ajax/ajax-barcodescanner.php`,
            data: {
                act: 'barcodescanner',
                itemCode,
                barcode,
            },
            beforeSend: function() {
                $(`.barcodescannerDiv`).html(
                    `<span class="text-secondary">Loding...</span>`);
            },
            success: function(response) {
                let data = JSON.parse(response);
                let itemId = data.itemId;

                // for sentence case 
                var sentenceCaseMessage = data.message.replace(/(^\s*\w|[\.\!\?]\s*\w)/g,
                    function(match) {
                        return match.toUpperCase();
                    });

                if (data.status === "success") {
                    // second ajax (item list ajax)
                    $.ajax({
                        type: "GET",
                        url: `ajax/ajax-items-list.php`,
                        data: {
                            act: "itemsList",
                            itemId
                        },
                        beforeSend: function() {
                            $(".spanItemsTableTr").show();
                            $(".spanItemsTable").html(`<span>Loding...</span>`);
                            $(`#barcodescanner`).css({
                                "border-color": "green",
                                "background-color": "rgb(238 255 241)"
                            });
                        },
                        success: function(response) {
                            $("#itemsTbody").append(response);
                            $(".spanItemsTableTr").hide();
                            $(`.spanItemsTable`).html('');
                            $(`#barcodescanner`).css({
                                "border-color": "rgb(201 201 201)",
                                "background": "none"
                            });
                        }
                    });

                    $(`.barcodescannerDiv`).html('');
                    $(`#barcodescanner`).val('');
                } else {
                    $(`.barcodescannerDiv`).html(
                        `<span class='text-danger'>${sentenceCaseMessage}</span>`);
                    $(`#barcodescanner`).css({
                        "border-color": "red",
                        "background-color": "rgb(255 238 238)"
                    });
                }
            },
            complete: function() {
                console.log('response is complete');
            }
        });
    });

    // pos final submit trigger
    $(document).ready(function() {
        // Set default state
        $('#bankSelect').hide();
        $('#paidInput').show();

         // Update the visibility and behavior of elements based on payment method
    $('input[name="paymentMethod"]').on('change', function () {
        let paymentMethod = $(this).val();
        if (paymentMethod === 'cash') {
            $('#bankSelect').hide();
            $('#paidInput').show();
            $(".paidAmount").prop("readonly", false);
        } else if (paymentMethod === 'online') {
            $('#bankSelect').show();
            $('#paidInput').show();
            let spanValue = helperAmount($("#grandtoalsp").text()) || 0;
            $(".paidAmount").val(spanValue);
            $(".paidAmount").prop("readonly", true);
        }

        // Update active styling for labels
        $(".pay-method label").removeClass("active-payment");
        $(this).closest("label").addClass("active-payment");
    });

    // Initialize the default state based on the selected payment method
    $('input[name="paymentMethod"]:checked').trigger('change');
        //Razorpay Payment Getway Initiate Function
function payment_initiate_1(invoice_no, invoice_id) {
    return new Promise((resolve, reject) => {
        const formData = $("#posFormData").serialize();

        $.ajax({
            type: "POST",
            url: "submitpayment.php",
            data: formData,
            beforeSend: () => $("#posFinalSubmitBtn11").text("Submitting..."),
            success: (data) => {
                if (data.res !== "success") {
                    return reject("Payment initialization failed");
                }

                const options = {
                    key: data.razorpay_key,
                    amount: data.userData.amount * 100,
                    currency: "INR",
                    name: "VITWO",
                    description: data.userData.description,
                    image: "https://vitwo.finance/assets/images/logo/dark-logo.png",
                    order_id: data.userData.rpay_order_id,
                    handler: (response) => {
                        const customerId = parseInt($("#customerDropDown").val(), 10) || 0;
                        const amount = data.userData.amount; // Amount in INR
                        const tnxDocNo = response.razorpay_payment_id; // Payment ID from Razorpay
                        const paymentCollectType = "POS-Online";
                        const bank_id = $("#bankId").val();

                        payment_collect(customerId, invoice_id, invoice_no, amount, bank_id, tnxDocNo, paymentCollectType)
                            .then(resolve)
                            .catch((error) => {
                                console.error("Error collecting payment:", error);
                                resolve(false); // Resolves as false in case of an error
                            });
                    },
                    prefill: {
                        name: data.userData.name,
                        email: data.userData.email,
                        contact: data.userData.mobile,
                    },
                    theme: {
                        color: "#3399cc"
                    },
                    modal: {
                        ondismiss: () =>resolve(false),
                    },
                };

                new Razorpay(options).open();
            },
            error: (xhr) => reject(
                `Error in payment initiation: ${xhr.responseText}`
            ),
        });
    });
}



        function payment_collect(customerId, invoice_id, invoice_no, amount, bankId, tnxDocNo,
            paymentCollectType) {
            const currentDate = new Date().toISOString().split("T")[0];
            if (paymentCollectType === "POS-Offline") {
                tnxDocNo = `POS_Cash${Math.floor(Math.random() * 1000000)}`;
            }

            const postData = {
                action_post: "collect",
                paymentDetails: {
                    paymentCollectType,
                    customerId,
                    collectPayment: amount,
                    bankId,
                    paymentAdviceImg: "",
                    documentDate: currentDate,
                    postingDate: currentDate,
                    tnxDocNo,
                    advancedPayAmt: 0,
                },
                modalDueamt: 0,
                paymentInvDetails: {
                    [customerId]: [{
                        inputRoundOffInrWithSign: 0.0,
                        inputRoundOffWithSign: 0.0,
                        inputWriteBackInrWithSign: 0.0,
                        inputWriteBackWithSign: 0.0,
                        inputFinancialChargesInrWithSign: 0.0,
                        inputFinancialChargesWithSign: 0.0,
                        inputForexLossGainInrWithSign: 0.0,
                        inputForexLossGainWithSign: 0.0,
                        inputTotalTdsWithSign: 0.0,
                        invoiceId: invoice_id,
                        invoiceNo: invoice_no,
                        invoiceStatus: "full paid",
                        creditPeriod: 1,
                        invAmt: amount,
                        dueAmt: amount,
                        customer_id: customerId,
                        recAmt: amount,
                    }, ],
                },
                paymentInvoiceDetails: {
                    [invoice_no]: [{
                        paymentAdjustAMT: "",
                        paymentAdjustINR: 0
                    }],
                },
            };

            return new Promise((resolve, reject) => {
                $.ajax({
                    type: "POST",
                    url: "submitpayment.php",
                    data: JSON.stringify(postData),
                    contentType: "application/json",
                    dataType: "json",
                    success: (response) => resolve(response.res === "success"),
                    error: () => reject(false),
                });
            });
        }

        

function numberToWords(number) {
                const units = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
                const teens = ["Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
                const tens = ["", "Ten", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
                const placeNames = ["", "Thousand", "Lakh", "Crore"]; // Handle Lakh and Crore

                if (number === 0) return "Zero Paisa";

                let integerPart = Math.floor(number);
                let decimalPart = Math.round((number - integerPart) * 100);

                let integerWords = convertIntegerToWords(integerPart, placeNames);
                let decimalWords = convertIntegerToWords(decimalPart, []);

                return `${integerWords} and ${decimalWords} Paisa`;
            }

            function convertIntegerToWords(number, placeNames) {
                if (number === 0) return "";

                const units = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
                const teens = ["Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
                const tens = ["", "Ten", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

                let words = "";
                let chunkCount = 0;

                // Break the number into chunks of 3 digits and convert them
                while (number > 0) {
                    let chunk = number % 1000;
                    if (chunk > 0) {
                        let chunkWords = "";

                        // Handle numbers between 11 and 19
                        if (chunk % 100 < 20 && chunk % 100 > 10) {
                            chunkWords = teens[chunk % 10 - 1];
                        } else {
                            chunkWords = tens[Math.floor(chunk % 100 / 10)] + " " + units[chunk % 10];
                        }

                        // Handle hundreds
                        if (Math.floor(chunk / 100) > 0) {
                            chunkWords = units[Math.floor(chunk / 100)] + " Hundred " + chunkWords;
                        }

                        // Add the place name (Thousand, Lakh, etc.)
                        words = chunkWords + (placeNames[chunkCount] ? " " + placeNames[chunkCount] : "") + " " + words;
                    }
                    chunkCount++;
                    number = Math.floor(number / 1000); // Reduce the number by 1000 each time
                }

                return words.trim();
            }


$('#posFinalSubmitBtn1').on('click', function(e) {
    e.preventDefault();

    // Fetch values again right before validation
    let paid_amt = helperAmount($("#paidamt").val());
    let paymentMethod = $('input[name="paymentMethod"]:checked').val();
    let bank_id = $("#bankId").val();
    let cashbankid = $("#cashbank_id").val();
    let grnt = helperAmount($("#grandtoalsp").text());
    const errorMessageContainer = document.getElementById('errorMessage');
    const errorMessageContainer1 = document.getElementById('errorMessage1');

    // Proceed with the check and logic as before
    if (paymentMethod === "cash") {
        if (cashbankid && cashbankid !== "") {
            console.log("Processing payment with cashbank_id:", cashbankid);
        } else {
            alert("Cash payment method not set.");
            return; // Stop execution if cashbankid is invalid
        }
    } else if (paymentMethod === "online") {
        if (bank_id && bank_id !== "") {
            console.log("Processing online payment with bankid:", bank_id);
        } else {
            errorMessageContainer.textContent = "Select Bank Account";
            errorMessageContainer.classList.add('error');
            return; // Stop execution if bank_id is invalid
        }
    }
    if (isNaN(paid_amt) || paid_amt === "") {
        alert("Enter Paid Amount");
        errorMessageContainer1.textContent = "Enter Paid Amount";
        errorMessageContainer1.classList.add('error');
        return; // Stop execution if paid amount is invalid
    }

    // Check if paid_amt is greater than or equal to grand total (grnt)
    if (paid_amt < grnt) {
        errorMessageContainer1.textContent = "Paid Amount not less then total amount";
        errorMessageContainer1.classList.add('error');
        return; // Stop execution if the paid amount is less than the grand total
    }
    errorMessageContainer.textContent = "";
    errorMessageContainer.classList.remove('error');
    errorMessageContainer1.textContent = "";
    errorMessageContainer1.classList.remove('error');


    $("#posFinalSubmitBtn1").prop("disabled", true);
    $('#conclose').prop("disabled", true);
    const customerName = document.querySelector('.modalCustomer').textContent;
    const totalAmount = helperAmount(document.querySelector('.grandTotalAmt')
        .textContent) * 100; // Convert to paise
    const paidAmount = document.querySelector('.paidAmount').value;


    let=customer_id=0;
    let formData1 = $("#posFormData").serialize();
    let formData2 = $("#paymentForm").serialize();
    let formData3 = $("#basic_details").serialize();

    let walkinCheck = $("#walkInCustomerCheckbox").is(":checked");
    if(walkinCheck){
    formData1 += "&customerId=" + customer_id;
    }
    let combinedFormData = formData1 + '&' + formData2 + '&' + formData3;


    let customerDropDown = $("#customerDropDown").val();
    let kamDropDown = $("#kamDropDown").val();
    var itemsTbody = $("#itemsTbody").html();

    


    if (!walkinCheck) {
        if (customerDropDown == "" && kamDropDown == "") {
            $("#alertModal").modal("show");
            $(".errorMsg").html(
                `<span class="text-warning">All fields are required.</span>`);
            return false;
        }
    }
    let paymentOption = "upi";
    let formData = $("#posFormData").serialize();



    if (itemsTbody != "") {
        $('#loadingSpinner').show();
        $.ajax({
            type: "POST",
            url: `ajax/ajax-pos-create.php`,
            data: combinedFormData,
            beforeSend: function() {
                $("#posFinalSubmitBtn").text(`Submitting...`);
            },
            success: function(response) {
                // return;
                $("#posFinalSubmitBtn").text('Submitted');
                let data = JSON.parse(response);

                $("#amount").val(totalAmount);
                $("#bank_id").val(bank_id);
                if (data.status === "error") {
                    $("#posFinalSubmitBtn1").prop("disabled", false);
                    $('#conclose').prop("disabled", false);
                    $('#loadingSpinner').hide();
                    $("#alertModal").modal("show");
                    $(".errorMsg").html(
                        `<span class="text-danger">${data.message}</span>`
                    );
                    $(".alertModalOkBtn").html(
                        `<a href="" type="button" class="btn btn-secondary">OK</a>`
                    );
                } else if (data.status === "warning") {
                    $("#posFinalSubmitBtn1").prop("disabled", false);
                    $('#conclose').prop("disabled", false);
                    $('#loadingSpinner').hide();
                    $("#alertModal").modal("show");
                    $(".errorMsg").html(
                        `<span class="text-danger">${data.message}</span>`
                    );
                    $(".alertModalOkBtn").html(
                        `<button type="button" class="btn btn-secondary">OK</button>`
                    );
                }

                if (data.type === "pos_invoice") {

                    if (data.status === "success") {
                        let basicDetails = data.InvoicingInputData
                            .BasicDetails;
                        let invoice_no = basicDetails.documentNo;
                        let invoice_id = data.inv_id;
                        let dep_keys = invoice_id;
                        if (paymentMethod === "online") {
                            payment_initiate_1(invoice_no, invoice_id)
                                .then(paymentSuccess => {
                                    if (paymentSuccess) {
                                        $('#loadingSpinner').hide();
                                        
                                        $(".saveSuccessFullyMsg").html(
                                            data.message);

                                        let customerDetails = data
                                            .InvoicingInputData
                                            .customerDetails;
                                        let itemsObj = data
                                            .InvoicingInputData
                                            .FGItems;
                                        let companyDetails = data
                                            .InvoicingInputData
                                            .companyDetails;

                                        let grandSubTotal = 0;
                                        let grandTotal = 0;
                                        let grandTotalDiscount = 0;
                                        let i = 1;
                                        let gst=0;
                                        let items = $.map(itemsObj,
                                            function(item,
                                                index) {
                                                grandSubTotal +=
                                                    (
                                                        helperAmount(
                                                            item
                                                            .unitPrice
                                                        ) * item
                                                        .qty);
                                                grandTotal +=
                                                    helperAmount(
                                                        item
                                                        .totalPrice
                                                    );
                                                grandTotalDiscount
                                                    +=
                                                    helperAmount(
                                                        item
                                                        .itemTotalDiscount
                                                    );
                                                gst+=helperAmount(
                                                        item
                                                        .itemTotalTax1
                                                    );  
                                                    
                                                return '<tr>' +
                                                    '<td class="item-column break-words" style="padding:0px !important;white-space:normal;">' + 
                                                    i++ + " " + item.itemName + 
                                                    '</td>' +
                                                    '<td class="text-right">' + 
                                                    inputQuenty(item.qty) + 
                                                    '</td>' +
                                                    '<td class="text-right">' + 
                                                    inputValue(item.unitPrice) + 
                                                    '</td>' +
                                                    '<td class="text-right">' + 
                                                    inputValue(item.unitPrice * item.qty) + 
                                                    '</td>' +
                                                    '</tr>';
                                            });
                                       
                                        let word=numberToWords(grandTotal);
                                        let totalItems = items
                                            .length;

                                        $("#invoice").modal("show");
                                        $(".receiptData").html(`
        <div class="invoice-container">
            <div class="invoice">
                <div class="text-center">
                    <div class="font-bold">POS Invoice</div>
                    <div class="font-bold">${companyDetails.company_name}</div>
                    <div class="text-xs break-words">${companyDetails.location_flat_no}' '${companyDetails.location_district}' '${companyDetails.location_state}</div>
                    <div class="text-xs">Ph. M. ${companyDetails.companyPhone}</div>
                    <div class="text-xs break-words">E Mail: ${companyDetails.companyEmail}</div>
                    <div class="text-xs break-words">GSTIN:- ${companyDetails.branch_gstin}</div>
                </div>

                <div class="mt-1 flex justify-between text-xs border-y">
                    <div>Bill No.: ${basicDetails.documentNo}</div>
                    <div>Date: ${basicDetails.documentDate}</div>
                </div>

                <div class="mt-1 text-xs">
                    <div class="break-words">Customer : ${customerDetails.customerName}</div>
                </div>
                <table class="mt-1 text-xs">
                    <thead>
                        <tr class="border-y">
                            <th class="item-column">Item</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Rate</th>
                            <th class="text-right">Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                         ${items}
                    </tbody>
                </table>

                <div class="mt-1 border-t text-xs">
                    <div class="flex justify-between">
                        <span>Total Qty</span>
                        <span>${totalItems}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Sub Total.</span>
                        <span>${grandSubTotal.toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Discount Incl.</span>
                        <span>${decimalAmount(grandTotalDiscount)}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span>GST</span>
                        <span>${gst.toFixed(2)}</span>
                    </div>
                </div>

                <div class="mt-1 border-y text-center">
                    <div class="text-lg font-bold">Net Amount</div>
                    <div class="text-xl font-bold">₹ ${grandTotal.toFixed(2)}</div>
                    <p> ${word} </p>
                </div>

               

                <div class="mt-1 text-center text-xs">
                    <div>Have a Nice Day</div>
                    <div>Thanks For Visit</div>
                </div>

                

                </div>
        </div>

        <style>
            .invoice-container {
                font-family: 'Courier New', monospace;
                font-size: 10px;
                margin: 0;
                padding: 5px;
                background-color: #f0f0f0;
                display: flex;
                justify-content: center;
            }

            .invoice {
                width: 100%;
                
                border: 1px solid #000;
                padding: 5px;
                background-color: white;
                box-sizing: border-box;
            }

            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .font-bold { font-weight: bold; }
            .text-xs { font-size: 8px; }
            .mt-1 { margin-top: 2px; }
            .mt-2 { margin-top: 4px; }
            .flex { display: flex; }
            .justify-between { justify-content: space-between; }
            table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed; /* Ensures columns don't overflow */
            }

            th, td {
                padding: 1px 0;
                text-align: left;
                word-break: break-word;
                overflow: hidden;
                text-overflow: ellipsis; /* Prevents text from overflowing */
            }

            .item-column {
                width: 40%;
            }

            .border-y { border-top: 1px solid black; border-bottom: 1px solid black; }
            .border-t { border-top: 1px solid black; }
            .border-b { border-bottom: 1px solid black; }

            .text-lg { font-size: 12px; }
            .text-xl { font-size: 14px; }
            .break-words { 
                word-wrap: break-word; 
                overflow-wrap: break-word;
            }

            .print-button {
                display: block;
                width: 100%;
                padding: 5px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 2px;
                font-size: 12px;
                cursor: pointer;
                margin-top: 5px;
            }

            
        </style>
    `);

                                    } else {
                                        var $this = $(this);
                                        console.log(
                                            'Payment verification failed'
                                        );
                                        $('#loadingSpinner').hide();
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Something Went Wrong !',
                                            text: 'Payment Collection Failed',
                                            // showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Yes'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $.ajax({
                                                    type: 'POST',
                                                    data: {
                                                        dep_keys: dep_keys,
                                                        dep_slug: 'reverseInvoice'
                                                    },
                                                    url: 'ajax/ajax-reverse-post.php',
                                                    beforeSend: function() {
                                                        $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                                                    },
                                                    success: function(response) {
                                                        var responseObj = JSON.parse(response);

                                                        let Toast = Swal.mixin({
                                                            toast: true,
                                                            position: 'top-end',
                                                            showConfirmButton: false,
                                                            timer: 1000
                                                        });
                                                        Toast.fire({
                                                            icon: responseObj.status,
                                                            title: '&nbsp;' + responseObj.message
                                                        }).then(function() {
                                                            // location.reload();
                                                        //   $("#nextorder").trigger('click');
                                                        default_call();

                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    }
                                })
                                .catch(error => {
                                    $('#loadingSpinner').hide();
                                    console.error(
                                        'Error during payment process:',
                                        error);
                                });


                        } else {

                            const customerId = parseInt($("#customerDropDown").val(), 10) || 0;

                            var amount = $('#grandtoalsp').text();
                            var tnxDocNo = '';
                            var paymentCollectType = "POS-Offline";
                            var bankId = $('#cashbank_id').val();
                            payment_collect(
                                    customerId,
                                    invoice_id,
                                    invoice_no, amount, bankId,
                                    tnxDocNo,
                                    paymentCollectType)
                                .then(success => {
                                    if (success) {
                                        $('#loadingSpinner').hide();
                                        $(".saveSuccessFullyMsg")
                                            .html(
                                                data.message);

                                        let customerDetails = data
                                            .InvoicingInputData
                                            .customerDetails;
                                        let itemsObj = data
                                            .InvoicingInputData
                                            .FGItems;
                                        let companyDetails = data
                                            .InvoicingInputData
                                            .companyDetails;

                                        let grandSubTotal = 0;
                                        let grandTotal = 0;
                                        let grandTotalDiscount = 0;
                                        let i = 1;
                                        let gst=0;
                                        let items = $.map(itemsObj,
                                            function(item,
                                                index) {
                                                grandSubTotal +=
                                                    (
                                                        helperAmount(
                                                            item
                                                            .unitPrice
                                                        ) * item
                                                        .qty);
                                                grandTotal +=
                                                    helperAmount(
                                                        item
                                                        .totalPrice
                                                    );
                                                grandTotalDiscount
                                                    +=
                                                    helperAmount(
                                                        item
                                                        .itemTotalDiscount
                                                    );
                                                gst+=helperAmount(
                                                        item
                                                        .itemTotalTax1
                                                    );  
                                                    
                                                return '<tr>' +
                                                    '<td class="item-column break-words" style="padding:0px !important;white-space:normal;">' + 
                                                    i++ + " " + item.itemName + 
                                                    '</td>' +
                                                    '<td class="text-right">' + 
                                                    inputQuenty(item.qty).toFixed(2) + 
                                                    '</td>' +
                                                    '<td class="text-right">' + 
                                                    inputValue(item.unitPrice).toFixed(2) + 
                                                    '</td>' +
                                                    '<td class="text-right">' + 
                                                    inputValue(item.unitPrice * item.qty) + 
                                                    '</td>' +
                                                    '</tr>';
                                            });
                                       
                                        
                                        let totalItems = items
                                            .length;
                                        let word=numberToWords(grandTotal);

                                        $("#invoice").modal("show");
                                        $(".receiptData").html(`
        <div class="invoice-container">
            <div class="invoice">
                <div class="text-center">
                    <div class="font-bold">POS Invoice</div>
                    <div class="font-bold">${companyDetails.company_name}</div>
                    <div class="text-xs break-words">${companyDetails.location_flat_no}' '${companyDetails.location_district}' '${companyDetails.location_state}</div>
                    <div class="text-xs">Ph. M. ${companyDetails.companyPhone}</div>
                    <div class="text-xs break-words">E Mail: ${companyDetails.companyEmail}</div>
                    <div class="text-xs break-words">GSTIN:- ${companyDetails.branch_gstin}</div>
                </div>

                <div class="mt-1 flex justify-between text-xs border-y">
                    <div>Bill No.: ${basicDetails.documentNo}</div>
                    <div>Date: ${basicDetails.documentDate}</div>
                </div>

                <div class="mt-1 text-xs">
                    <div class="break-words">Customer : ${customerDetails.customerName}</div>
                </div>
                <table class="mt-1 text-xs">
                    <thead>
                        <tr class="border-y">
                            <th class="item-column">Item</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Rate</th>
                            <th class="text-right">Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                         ${items}
                    </tbody>
                </table>

                <div class="mt-1 border-t text-xs">
                    <div class="flex justify-between">
                        <span>Total Qty</span>
                        <span>${totalItems}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Sub Total.</span>
                        <span>${grandSubTotal.toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Discount Incl.</span>
                        <span>${decimalAmount(grandTotalDiscount)}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span>GST</span>
                        <span>${gst.toFixed(2)}</span>
                    </div>
                </div>

                <div class="mt-1 border-y text-center">
                    <div class="text-lg font-bold">Net Amount</div>
                    <div class="text-xl font-bold">₹ ${grandTotal.toFixed(2)}</div>
                    <p> ${word} </p>
                </div>

               

                <div class="mt-1 text-center text-xs">
                    <div>Have a Nice Day</div>
                    <div>Thanks For Visit</div>
                </div>

                

                </div>
        </div>

        <style>
            .invoice-container {
                font-family: 'Courier New', monospace;
                font-size: 10px;
                margin: 0;
                padding: 5px;
                background-color: #f0f0f0;
                display: flex;
                justify-content: center;
            }

            .invoice {
                width: 100%;
                
                border: 1px solid #000;
                padding: 5px;
                background-color: white;
                box-sizing: border-box;
            }

            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .font-bold { font-weight: bold; }
            .text-xs { font-size: 8px; }
            .mt-1 { margin-top: 2px; }
            .mt-2 { margin-top: 4px; }
            .flex { display: flex; }
            .justify-between { justify-content: space-between; }
            table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed; /* Ensures columns don't overflow */
            }

            th, td {
                padding: 1px 0;
                text-align: left;
                word-break: break-word;
                overflow: hidden;
                text-overflow: ellipsis; /* Prevents text from overflowing */
            }

            .item-column {
                width: 40%;
            }

            .border-y { border-top: 1px solid black; border-bottom: 1px solid black; }
            .border-t { border-top: 1px solid black; }
            .border-b { border-bottom: 1px solid black; }

            .text-lg { font-size: 12px; }
            .text-xl { font-size: 14px; }
            .break-words { 
                word-wrap: break-word; 
                overflow-wrap: break-word;
            }

            .print-button {
                display: block;
                width: 100%;
                padding: 5px;
                background-color: #4CAF50;
                color: white;
                border: none;
                border-radius: 2px;
                font-size: 12px;
                cursor: pointer;
                margin-top: 5px;
            }

            
        </style>
    `);



                                        console.log(
                                            "Payment collected successfully"
                                        );
                                    } else {
                                        resolve(false);
                                        var $this = $(this);
                                        console.log(
                                            'Payment verification failed'
                                        );
                                        $('#loadingSpinner').hide();
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Something Went Wrong !',
                                            text: 'Payment Collection Failed',
                                            // showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'OK'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $.ajax({
                                                    type: 'POST',
                                                    data: {
                                                        dep_keys: dep_keys,
                                                        dep_slug: 'reverseInvoice'
                                                    },
                                                    url: 'ajax/ajax-reverse-post.php',
                                                    beforeSend: function() {
                                                        $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                                                    },
                                                    success: function(response) {
                                                        var responseObj = JSON.parse(response);

                                                        let Toast = Swal.mixin({
                                                            toast: true,
                                                            position: 'top-end',
                                                            showConfirmButton: false,
                                                            timer: 1000
                                                        });
                                                        Toast.fire({
                                                            icon: responseObj.status,
                                                            title: '&nbsp;' + responseObj.message
                                                        }).then(function() {
                                                            // location.reload();
                                                            $("#nextorder").trigger('click');
                                                            default_call();
                                                        });
                                                    }
                                                });
                                            }
                                        });

                                    }
                                })
                                .catch(error => {
                                    var $this = $(this);
                                   $('#loadingSpinner').hide();
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Something Went Wrong !',
                                        text: 'Payment Collection Failed',
                                        // showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'OK'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $.ajax({
                                                type: 'POST',
                                                data: {
                                                    dep_keys: dep_keys,
                                                    dep_slug: 'reverseInvoice'
                                                },
                                                url: 'ajax/ajax-reverse-post.php',
                                                beforeSend: function() {
                                                    $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                                                },
                                                success: function(response) {
                                                    document.querySelector('.itemListContainer').innerHTML = '';
                                                    var responseObj = JSON.parse(response);

                                                    let Toast = Swal.mixin({
                                                        toast: true,
                                                        position: 'top-end',
                                                        showConfirmButton: false,
                                                        timer: 1000
                                                    });
                                                    Toast.fire({
                                                        icon: responseObj.status,
                                                        title: '&nbsp;' + responseObj.message
                                                    }).then(function() {
                                                        // location.reload();
                                                        $("#nextorder").trigger('click');
                                                        default_call();
                                                    });
                                                }
                                            });
                                        }
                                    });
                                });


                        }




                    } else if (data.status === "low") {
    // Enable buttons and hide spinner
    $("#posFinalSubmitBtn1").prop("disabled", false);
    $('#conclose').prop("disabled", false);
    $('#loadingSpinner').hide();

    // Show the alert modal
    $("#alertModal").modal("show");

    // Set the error message
    $(".errorMsg").html(
        `<span class="text-danger">Stock is low</span>`
    );

    // Create the list of low-stock items
    let itemListHtml = '<table class="table"><thead><tr><th>Item Name</th><th>Requested Qty</th><th>Available Qty</th></tr></thead><tbody>';

    // Loop through the itemlist array and create a row for each item
    data.itemlist.forEach(item => {
        itemListHtml += `
            <tr>
                <td>${item.itemName}</td>
                <td>${item.requestStock}</td>
                <td>${item.availableStock}</td>
            </tr>
        `;
    });

    // Close the table tag
    itemListHtml += '</tbody></table>';

    // Insert the generated HTML into the modal
    $(".itemListContainer").html(itemListHtml);
}
 else {
                        $("#posFinalSubmitBtn1").prop("disabled",
                            false);
                            $('#conclose').prop("disabled", false);
                            $('#loadingSpinner').hide();
                        $("#alertModal").modal("show");

                        $(".errorMsg").html(
                            `<span class="text-warning">${data.message}</span>`
                        );
                    }
                } else if (data.type === "pos_salesorder") {
                    if (data.status === "success") {
                    $('#loadingSpinner').hide();
                        $("#alertModal").modal("show");
                        $(".refNumberMsg").html(
                            `<span class="text-dark">${data.soNumber}</span>`
                        );
                        $(".errorMsg").html(
                            `<span class="text-success">${data.message}</span>`
                        );
                        $(".alertModalOkBtn").html(
                            `<a href="" type="button" class="btn btn-secondary">OK</a>`
                        );


                    } else {
                        $("#posFinalSubmitBtn1").prop("disabled",
                            false);
                            $('#conclose').prop("disabled", false);
                            $('#loadingSpinner').hide();
                        $("#alertModal").modal("show");
                        $(".errorMsg").html(
                            `<span class="text-success">${data.message}</span>`
                        );
                    }
                }
            }
        });
    } else {
        $("#posFinalSubmitBtn1").prop("disabled", false);
        $('#conclose').prop("disabled", false);
        $('#loadingSpinner').hide();
        $("#alertModal").modal("show");
        $(".errorMsg").html(`<span class="text-warning">Please add item.</span>`);
    }

});
    });


    // fullscreen ****************************************************
    const fullscreenDiv = document.getElementById('fullscreen-div');
    const fullscreenIcon = document.getElementById('fullscreen-icon');

    // Add a click event listener to the fullscreen icon
    fullscreenIcon.addEventListener('click', () => {
        toggleFullScreen();
    });

    // Function to toggle full-screen mode
    function toggleFullScreen() {
        // If the document is not currently in full-screen mode
        if (!document.fullscreenElement) {
            // Request full-screen mode for the fullscreenDiv element
            fullscreenDiv.requestFullscreen().catch(err => {
                console.error(`Error attempting to enable full-screen mode: ${err.message}`);
            });
        } else {
            // If the document is already in full-screen mode, exit full-screen
            document.exitFullscreen();
        }
    }
    // ************************

});

$('#paymentConfirm').on('hidden.bs.modal', function() {
    $("#posFinalSubmitBtn1").prop("disabled", false);
    $('#conclose').prop("disabled", false);
});

$("#walkInCustomerCheckbox").on("change", function () {
    if ($(this).is(":checked")) {
        $(".walkinCustomerDiv").show();

        // Get the walk-in customer ID
        var cust = $("#walkincustomer").val();
        console.log("Walk-in customer ID:", cust);

        // Select or append the value in the dropdown
        if ($("#customerDropDown option[value='" + cust + "']").length > 0) {
            $("#customerDropDown").val(cust).trigger("change");
        } else {
            $("#customerDropDown").append(new Option("Walk In Customer", cust, true, true)).trigger("change");
        }

        // Disable dropdown
        $("#customerDropDown").prop("disabled", true);

    } else {
        $(".walkinCustomerDiv").hide();

        // Reset the dropdown
        $("#customerDropDown").val("").trigger("change");
        $("#customerDropDown").prop("disabled", false);

    
    }
});



$('#customerDropDown').select2({
    placeholder: 'Select Customer',
    ajax: {
        url: '../ajaxs/so/ajax-customerslst-select2.php',
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

    if (!$results.find('a').length) {
        $results.append(
            `<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewCustomerModal">Add New</a></div>`
        );
    }
});

$("#bankId").select2({
    dropdownParent: $("#paymentConfirm")
});

$("#payOption").select2({

});
</script>

<!-- <script>
 (function() {
    let devtoolsOpened = false;

    // Disable right-click (context menu)
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();  // Prevent right-click
    });

    // Function to detect if DevTools are open
    function checkDevTools() {
        const widthThreshold = window.outerWidth - window.innerWidth > 100;
        const heightThreshold = window.outerHeight - window.innerHeight > 100;
        
        // If the difference in width/height exceeds the threshold, DevTools is likely open
        if (widthThreshold || heightThreshold) {
            if (!devtoolsOpened) {
                devtoolsOpened = true;
                window.location.reload();  // Reload the page when DevTools is detected
            }
        } else {
            devtoolsOpened = false;  // Reset if DevTools is closed
        }
    }

    // Disable drag-and-drop for elements (prevents inspecting elements)
    document.addEventListener('dragstart', function(e) {
        e.preventDefault();  // Prevent drag events
    });

    // Periodically check if DevTools are open (every second)
    setInterval(checkDevTools, 1000);
})();
</script> -->
<!-- <script src="<?= BASE_URL; ?>public/validations/soValidation.js"></script> -->
<script src="<?= BASE_URL; ?>public/validations/goodsInvoiceValidation.js"></script>
<script src="https://code.getmdl.io/1.2.0/material.min.js"></script>