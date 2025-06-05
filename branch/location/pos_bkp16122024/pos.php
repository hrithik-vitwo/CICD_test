<?php
require_once("../../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");
require_once("../../../app/v1/functions/company/func-branches.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");

require_once("controller/pos.controller.php");

// console($_SESSION);

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


// add pos submit btn
if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
   // console($_POST);
    // $addGoodsInvoice = $BranchSoObj->insertBranchDirectInvoice($_POST);
    // console($addGoodsInvoice);
    // // if ($addGoodsInvoice['status'] == "success") {
    // //     swalAlert($addGoodsInvoice["status"], $addGoodsInvoice['invoiceNo'], $addGoodsInvoice["message"], 'manage-invoices.php');
    // // } else {
    // //     swalAlert($addGoodsInvoice["status"], 'Warning', $addGoodsInvoice["message"]);
    // // }
}
?>

<link rel="stylesheet" href="../../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../../public/assets/listing.css">
<link rel="stylesheet" href="../../../public/assets/jquery.fancy.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<style>
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

    div.oneItemCard p {
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

<input type="hidden" value="<?= $branchGstinCode ?>" class="branchGstin">

<div class="content-wrapper is-pos vitwo-alpha-global">
    <section class="content">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <?php if ($sales_order_creation) { ?>
                    <li class="breadcrumb-item active"><a href="manage-sales-orders.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Orders List</a></li>
                    <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                            Create Sales Orders</a></li>
                <?php } else if ($create_service_invoice) { ?>
                    <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
                    <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                            Create Service Invoice</a></li>
                <?php } else if ($quotation_createion) { ?>
                    <li class="breadcrumb-item active"><a href="manage-quotations.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Quotation List</a></li>
                    <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                            Create Quotation</a></li>
                <?php } else if (isset($_GET['quotation'])) { ?>
                    <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
                    <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                            Create Quotation to Invoice</a></li>
                <?php } else if (isset($_GET['quotation_to_so'])) { ?>
                    <li class="breadcrumb-item active"><a href="manage-sales-orders.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Sales Order List</a></li>
                    <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                            Create Quotation to Sales Order</a></li>
                <?php } else if (isset($_GET['pgi_to_invoice'])) { ?>
                    <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
                    <li class="breadcrumb-item active"><a href="#" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                            Create PGI to Invoice</a></li>
                <?php } else { ?>
                    <li class="breadcrumb-item active"><a href="manage-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Invoice List</a></li>
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
                <input type="hidden" value="<?= $invoiceType ?>" name="ivType">
                <input type="text" name="act" value="<?= $posType ?>">
                <input type="hidden" value="<?= date("Y-m-d") ?>" name="invoiceDate">
                
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
                                        <ion-icon name="settings-outline" data-toggle="modal" data-target="#basicDetailsModal" style="cursor: pointer;"></ion-icon>
                                    </div>

                                </div>
                                <div class="card-body others-info vendor-info so-card-body" style="height: auto;">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row customer-info-form-view" style="row-gap: 15px;">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="input-box customer-select">
                                                        <label for="">Customers <span class="text-danger">*</span></label>
                                                        <select name="customerId" id="customerDropDown" class="form-control">
                                                            <option value="">Select Customer</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="">Select Sales Person <span class="text-danger">*</span></label>
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
                                                    <div class="form-input" style="display: flex; gap: 10px; align-items: center; margin-top: 10px;">
                                                        <label for="walkInCustomerCheckbox" style="margin: 0 !important; user-select: none;">Walk In Customer </label>
                                                        <input type="checkbox" name="walkInCustomerCheckbox" id="walkInCustomerCheckbox">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 walkinCustomerDiv">
                                                    <div class="form-input">
                                                        <label for="">Customer Name</label>
                                                        <input type="text" placeholder="Enter name" name="walkInCustomerName" class="form-control" id="walkInCustomerName">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 walkinCustomerDiv">
                                                    <div class="form-input">
                                                        <label for="">Customer Mobile</label>
                                                        <input type="number" placeholder="Enter mobile" name="walkInCustomerMobile" class="form-control" id="walkInCustomerMobile">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="">Barcode Scanner</label>
                                                        <input type="text" name="barcodescanner" class="form-control barcodescanner" id="barcodescanner" placeholder="Enter item code and batch number (e.g. 11000112/GRN1705490971271)">
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

                                <small class="py-2 px-1 rounded alert-dark specialDiscount" id="specialDiscount" style="display: none;">Special Discount</small>
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
                                            <td colspan="3" class="text-left p-2 totalCal" style="border-bottom: 1px solid #ccc;"><span class="spanItemsTable"></span></td>
                                            <input type="hidden" name="grandSubTotalAmtInp" id="grandSubTotalAmtInp" value="0">
                                            <td colspan="3" class="p-2 text-right" style="border-bottom: 1px solid #ccc;"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-left p-2 totalCal bg-light" style="border-bottom: 1px solid #ccc;">Sub Total</sup></td>
                                            <input type="hidden" name="grandSubTotalAmtInp" id="grandSubTotalAmtInp" value="0">
                                            <td colspan="3" class="p-2 text-right" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandSubTotalAmt">0.00</span>
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-left p-2 totalCal bg-light" style="border-bottom: 1px solid #ccc;">Total Discount</td>
                                            <input type="hidden" name="grandTotalDiscountAmtInp" id="grandTotalDiscountAmtInp" value="0">
                                            <td colspan="3" class="p-2 text-right" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTotalDiscount">0.00</span>
                                                </small>
                                            </td>
                                        </tr>

                                        <tr class="p-2 igstTr" style="display:none">

                                            <td colspan="3" class="text-left p-2 totalCal bg-light" style="border-bottom: 1px solid #ccc;">IGST</td>
                                            <input type="hidden" name="grandTaxAmtInp" id="grandTaxAmtInp" value="0">
                                            <td colspan="3" class="p-2 text-right" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span id="grandTaxAmt">0.00</span>
                                                </small>
                                            </td>
                                        </tr>
                                        <tr class="p-2 cgstTr" style="display:none">

                                            <td colspan="3" class="text-left p-2 totalCal bg-light" style="border-bottom: 1px solid #ccc;">CGST</td>
                                            <!-- <input type="hidden" name="grandSgstCgstAmtInp" id="grandSgstCgstAmtInp" value="0"> -->
                                            <td colspan="3" class="p-2 text-right" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span class="grandSgstCgstAmt">0.00</span>
                                                </small>
                                            </td>
                                        </tr>
                                        <tr class="p-2 sgstTr" style="display:none">

                                            <td colspan="3" class="text-left p-2 totalCal bg-light" style="border-bottom: 1px solid #ccc;">SGST</td>
                                            <!-- <input type="hidden" name="grandSgstCgstAmtInp" id="grandSgstCgstAmtInp" value="0"> -->
                                            <td colspan="3" class="p-2 text-right" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span class="grandSgstCgstAmt">0.00</span>
                                                </small>
                                            </td>
                                        </tr>
                                        <tr class="p-2">
                                            <td colspan="3" class="text-left p-2 font-weight-bold totalCal bg-light" style="border-bottom: 1px solid #ccc;">Total Amount</td>
                                            <input type="hidden" name="grandTotalAmtInp" id="grandTotalAmtInp" value="0">
                                            <td colspan="3" class="p-2 text-right font-weight-bold" style="background-color: #fff; border-bottom: 1px solid #ccc;">
                                                <small class="text-large font-weight-bold text-success">
                                                    <span class="rupee-symbol"><?= $currencyName ?> </span><span class="grandTotalAmt">0.00</span>
                                                </small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <span class="saveSuccessFullyMsg"></span>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 btn-group flex-btn d-flex">
                                    <a href="" onclick="return confirm('Are you sure to cancel?')" class="btn btn-danger w-100 p-3">Cancel</a>
                                    <button type="button" class="btn btn-primary w-100 p-3 items-search-btn float-right mr-0" data-bs-toggle="modal" data-bs-target="#paymentConfirm">Payment</button>
                                </div>

                                <!------Payment confirm modal------->
                                <div class="modal fade" id="paymentConfirm" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-md font-bold" id="exampleModalLabel">Add Invoice</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                        <h4 class="text-sm">Total <span class="grandTotalAmt text-md font-bold">0.00</span></h4>
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
                                                    <div class="d-flex gap-3 select-bank-paid align-items-start">
                                                        <div class="form-input bank-select-pos" id="bankSelect" style="display: none;">
                                                            <label for="bankId">Payment Method</label>
                                                            <select name="bankId" class="form-control select2 w-100" id="bankId">
                                                                <option value="">Select Bank</option>
                                                                <?php
                                                                $bankList = $BranchSoObj->fetchCompanyBank();
                                                                foreach ($bankList['data'] as $bank) {
                                                                    $bank_id = $bank['data']['id'];
                                                                    $check_bank = queryGet("SELECT * FROM `erp_payment_gateway` WHERE `company_id` = $company_id AND `bank_id` = $bank_id ");

                                                                    if ($bank['bank_name'] != "") {
                                                                ?>
                                                                        <option value="<?= $bank['id'] ?>" data-attr="0099"><?php if ($bank['bank_name']) {
                                                                                                                                echo '🏦' . $bank['bank_name'];
                                                                                                                            } elseif ($bank['cash_account']) {
                                                                                                                                echo '💰' . $bank['cash_account'];
                                                                                                                            } ?></option>
                                                                <?php }
                                                                } ?>
                                                            </select>
                                                        </div>

                                                        <div class="form-input" id="paidInput">
                                                            <label for="paid">Paid</label>
                                                            <input type="number" name="paid" class="form-control paidAmount">
                                                            <p class="font-bold text-xs text-right">Change <span class="changeAmount">0.00</span></p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary" name="addNewInvoiceFormSubmitBtn" id="posFinalSubmitBtn">Confirm</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- alert modal -->
                                <div class="modal fade" id="alertModal" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content" style="width: 50%;">
                                            <!-- <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Message</h5>
                                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> 
                                            </div> -->
                                            <div class="modal-body text-center" style="padding: 31px 0px;">
                                                <span class="refNumberMsg"></span>
                                                <p class="errorMsg"></p>
                                            </div>
                                            <div class="modal-footer" style="display: flex;justify-content: center;">
                                                <button type="button" class="btn btn-secondary alertModalOkBtn" data-bs-dismiss="modal">OK</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!------invoice generate modal------->
                                <div class="modal fade invoice-modal-pos" id="invoice" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                                                <a href="" class="btn btn-secondary">Close</a>
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
                                <button type="button" class="btn btn-outline-primary border itemGroup" id="itemGroup_0">All</button>
                                <?php foreach ($getItemsGroup as $groupKey => $oneGroup) { ?>
                                    <!-- <a href="#" data-filter=".<?= $oneGroup['goodGroupId'] ?>_group"><?= $oneGroup['goodGroupName'] ?></a> -->
                                    <button type="button" class="btn btn-outline-primary border itemGroup" id="itemGroup_<?= $oneGroup['goodGroupId'] ?>"><?= $oneGroup['goodGroupName'] ?></button>
                                <?php } ?>
                            </div>
                            <div class="card-body">
                                <div class="form-input mb-2">
                                    <input type="search" name="itemSearchInput" class="form-control itemSearchInput" id="itemSearchInput" placeholder="Serach items here">
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
                                                <div class="text-xs oneItemCard p-3" id="oneItemCard_<?= $itemStocks ?>_<?= $oneSummary['itemId'] ?>">
                                                    <p class="pos-item-name" title="<?= $oneSummary['itemName'] ?>"><?= $oneSummary['itemName'] ?></p>
                                                    <span class="text-xs"><?= $itemStocks ?></span>
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
                <div class="modal" id="basicDetailsModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">Basic Details</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <label>Posting Date: <span class="text-danger">*</span></label>
                                        <div>
                                            <input type="date" value="<?= date("Y-m-d") ?>" name="invoiceDate" id="postingDate" class="form-control" />
                                            <span class="input-group-addon"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <label>Posting Time: <span class="text-danger">*</span></label>
                                        <div>
                                            <input type="time" name="invoiceTime" id="postingTime" value="<?= date("H:i") ?>" class="form-control" />
                                            <span class="input-group-addon postingTimeMsg"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input">
                                            <label for="">Profile Center <span class="text-danger">*</span></label>
                                            <select name="profitCenter" class="selct-vendor-dropdown" id="profitCenterDropDown">
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
                                        <input type="text" class="form-control" id="curr_rate" name="curr_rate" value="1" readonly>
                                        <div class="dynamic-currency my-2">
                                            <select id="" name="currency" class="form-control currencyDropdown rupee-symbol">
                                                <?php
                                                $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                                                foreach ($curr['data'] as $data) {
                                                ?>
                                                    <option value="<?= $data['currency_id'] ?>_<?= $data['currency_icon'] ?>_<?= $data['currency_name'] ?>">
                                                        <?= $data['currency_icon'] ?><?= $data['currency_name'] ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                    <div class="form-input">
                                                        <label>States</label>

                                                        <!-- <select id="state" name="state" class="form-control stateDropDown">
                      <?php
                        $state_sql = queryGet("SELECT * FROM `erp_gst_state_code`", true);
                        $state_data = $state_sql['data'];
                        foreach ($state_data as $data) {

                        ?>

                                  <option value="<?= $data['gstStateName'] ?>" ><?= $data['gstStateName'] ?></option>  
                                  <?php
                                }
                                    ?>
                      </select>  -->

                                                        <input type="text" class="form-control" name="state" id="state" value="">

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


                <form id="payment-form" action="charge.php" method="POST">
                    <input type="text" id="razorpay_payment_id" name="payment_id">
                    <input type="text" id="amount" name="amount">
                    <input type="text" id="bank_id" name="bank_id">

                </form>

            </div>
        </div>
    </div>
</div>
<!-- add new customer modal end here ☝️☝️ -->



<?php require_once("../../common/footer.php"); ?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>


<script>
    $(document).ready(function() {

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
                $("#customerPhoneMsg").html(`<span class="text-xs text-danger">Phone number is required</span>`);
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
        });

        customerDetailsInfo(customer__ID);

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

        document.getElementById("printBtn").addEventListener("click", function() {
            var contentToPrint = document.getElementById("contentToPrint");
            var printWindow = window.open('', '', 'height=500,width=800');
            printWindow.document.write('<html><head><title>Print</title></head><body>');
            printWindow.document.write(contentToPrint.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        });

        // // Function to handle item search
        // $(".itemSearchInput").on("keyup", function() {
        //     var searchValue = $(this).val().toLowerCase();
        //     var itemsFound = false; // Flag to check if any items match the search

        //     $(".oneItemCard").each(function() {
        //         var itemName = $(this).find("p").text().toLowerCase();

        //         if (itemName.includes(searchValue)) {
        //             $(this).show();
        //             itemsFound = true; // At least one item is found
        //         } else {
        //             $(this).hide();
        //         }
        //     });

        //     // Show/hide the "not found" message based on the search result
        //     if (!itemsFound) {
        //         $(".dataNotFound").html('Data not found');
        //     } else {
        //         $(".dataNotFound").html('');
        //     }
        // });

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
                        $("#itemsTbody").append(response);
                        $(".spanItemsTableTr").hide();
                        $(`.spanItemsTable`).html('');
                    }
                });
            } else {
                alert(`Don't have available stocks`);
            }
        });

        // calculate one item amount
        function calculateOneItemAmounts(rowNo, itemId) {
            let itemQty = parseFloat($(`#itemQty_${rowNo}_${itemId}`).val()) || 0;
            let originalItemUnitPrice = parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}_${itemId}`).val()) ||
                0;
            let convertedItemUnitPrice = parseFloat($(`#convertedItemUnitPriceSpan_${rowNo}`).text()) || 0;
            let itemDiscount = parseFloat($(`#itemDiscount_${rowNo}_${itemId}`).val()) || 0;
            let itemTax = parseFloat($(`#itemTax_${rowNo}`).val()) || 0;

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
            $(`#itemBaseAmountSpan_${rowNo}`).text(basicPrice);
            $(`#convertedItemBaseAmountSpan_${rowNo}`).text(convertedBasicPrice);

            $(`#itemTotalDiscountHidden_${rowNo}`).val(totalDiscount);
            $(`#itemTotalDiscount1_${rowNo}_${itemId}`).val(totalDiscount);
            $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount);
            $(`#convertedItemDiscountAmountSpan_${rowNo}`).html(convertedTotalDiscount);

            $(`#itemTotalTax1_${rowNo}`).val(totalTax);
            $(`#itemTotalTax_${rowNo}`).html(totalTax);
            $(`#convertedItemTaxAmountSpan_${rowNo}`).html(convertedTotalTax);

            $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice);
            $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice);
            $(`#convertedItemTotalPriceSpan_${rowNo}`).html(convertedTotalItemPrice);

            $(`#totalItemAmountModal_${rowNo}`).html(totalItemPrice);
            calculateGrandTotalAmount();
        }

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
                totalAmount += parseFloat($(this).text().replace(/,/g, "")) || 0;
            });
            $(".itemTotalPrice").each(function() {
                totalAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
            });
            $(".convertedItemTotalPriceSpan").each(function() {
                convertedItemTotalPrice += parseFloat($(this).text().replace(/,/g, "")) || 0;
            });

            // item total tax
            $(".itemTotalTax1").each(function() {
                totalTaxAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
            });
            $(".itemTotalTax").each(function() {
                totalTaxAmount += parseFloat($(this).html().replace(/,/g, "")) || 0;
            });
            $(".convertedItemTaxAmountSpan").each(function() {
                convertedItemTaxAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
            });

            // item total discount
            $(".itemTotalDiscountHidden").each(function() {
                totalDiscountAmountOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
            });
            $(".itemTotalDiscount").each(function() {
                totalDiscountAmount += parseFloat($(this).html().replace(/,/g, "")) || 0;
            });
            $(".convertedItemDiscountAmountSpan").each(function() {
                convertedItemDiscountAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
            });

            // item base amount
            $(".itemBaseAmountInp").each(function() {
                itemBaseAmountInpOriginal += parseFloat($(this).val().replace(/,/g, "")) || 0;
            });
            $(".itemBaseAmountSpan").each(function() {
                itemBaseAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
            });
            $(".convertedItemBaseAmountSpan").each(function() {
                convertedItemBaseAmountSpan += parseFloat($(this).html().replace(/,/g, "")) || 0;
            });

            let compInvoiceType = $("#compInvoiceType").val();
            let grandTotalAmountAfterOriginal = totalAmountOriginal - totalTaxAmount;
            let grandTotalAmountAfter = totalAmount - totalTaxAmount;
            let convertedGrandTotalAmountWithoutTax = convertedItemTotalPrice - convertedItemTaxAmountSpan;

            if (compInvoiceType === "CBW" || compInvoiceType === "LUT" || compInvoiceType === "SEWOP") {
                $(".grandSgstCgstAmt").html(0);
                $(".convertedGrandSgstCgstAmt").html(0);

                $("#grandTaxAmt").html(0);
                $("#convertedGrandTaxAmount").html(0);

                $("#grandTaxAmtInp").val(0);

                $(".grandTotalAmt").html(grandTotalAmountAfter);
                $("#grandTotalAmtInp").val(grandTotalAmountAfter);
                $("#convertedGrandTotalAmt").text(convertedGrandTotalAmountWithoutTax);
            } else {
                $(".grandSgstCgstAmt").html((totalTaxAmount / 2));
                $(".convertedGrandSgstCgstAmt").html((convertedItemTaxAmountSpan / 2));

                $("#grandTaxAmt").html(totalTaxAmount);
                $("#convertedGrandTaxAmount").html(convertedItemTaxAmountSpan);

                $("#grandTaxAmtInp").val(totalTaxAmountOriginal);

                $("#grandSubTotalAmt").html(itemBaseAmountSpan);
                $("#grandSubTotalAmtInp").val(itemBaseAmountInpOriginal);
                $("#convertedGrandSubTotalAmt").text(convertedItemBaseAmountSpan);

                $("#grandTotalDiscount").html(totalDiscountAmount);
                $("#grandTotalDiscountAmtInp").val(totalDiscountAmountOriginal);
                $("#convertedGrandTotalDiscountAmount").text(convertedItemDiscountAmountSpan);

                $(".grandTotalAmt").html(totalAmount);
                $("#grandTotalAmtInp").val(totalAmountOriginal);
                $("#convertedGrandTotalAmt").text(convertedItemTotalPrice);
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
            let enterQty = parseFloat($(this).val());
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
            let itemQty = (parseFloat($(`#itemQty_${key}_${itemId}`).val()) > 0) ? parseFloat($(
                `#itemQty_${key}_${itemId}`).val()) : 0;
            let checkQty = (parseFloat($(`#checkQty_${key}`).val()) > 0) ? parseFloat($(`#checkQty_${key}`)
                .val()) : 0;
            $(`#itemQty_${key}_${itemId}`).val(parseInt(itemQty) + 1);
            itemQty++;
            // console.log(itemId)    
            // console.log(itemQty)                           


            checkLiveStock(key, itemId, itemQty, checkQty);

            calculateOneItemAmounts(key, itemId);
        });

        // decrement
        $(document).on("click", ".itemDecrementBtn", function() {
            let key = ($(this).attr("id")).split("_")[1];
            let itemId = ($(this).attr("id")).split("_")[2];
            let itemQty = (parseFloat($(`#itemQty_${key}_${itemId}`).val()) > 0) ? parseFloat($(
                `#itemQty_${key}_${itemId}`).val()) : 0;
            let checkQty = (parseFloat($(`#checkQty_${key}`).val()) > 0) ? parseFloat($(`#checkQty_${key}`)
                .val()) : 0;

            // console.log(itemQty);

            if (parseInt(itemQty) > 0) {
                $(`#itemQty_${key}_${itemId}`).val(itemQty - 1);
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

            let itemQty = (parseFloat($(`#itemQty_${rowNo}_${itemId}`).val()) > 0) ? parseFloat($(
                `#itemQty_${rowNo}_${itemId}`).val()) : 0;
            let originalItemUnitPrice = (parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}_${itemId}`)
                .val()) > 0) ? parseFloat($(`#originalChangeItemUnitPriceInp_${rowNo}_${itemId}`)
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
            let paidAmount = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            let grandTotalAmt = (parseFloat($("#grandTotalAmtInp").val()) > 0) ? parseFloat($(
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

            $('input[name="paymentMethod"]').on('change', function() {
                let paymentMethod = $(this).val();
                if (paymentMethod === 'cash') {
                    $('#bankSelect').hide();
                    $('#paidInput').show();
                } else if (paymentMethod === 'online') {
                    $('#bankSelect').show();
                    $('#paidInput').show();
                }
            });

            $('#posFinalSubmitBtn').on('click', function(e) {
                e.preventDefault();
                // const paidAmount = $(`.paidAmount`).val();
                const paymentMethod = $('input[name="paymentMethod"]:checked').val();
                //alert(paymentMethod);
                const bank_id = $("#bankId").val();
                const access_token = $("#bankId option:selected").data('attr');
                alert(access_token);
                //  alert(bank_id);
                const customerName = document.querySelector('.modalCustomer').textContent;
                const totalAmount = parseFloat(document.querySelector('.grandTotalAmt').textContent) * 100; // Convert to paise
                const paidAmount = document.querySelector('.paidAmount').value;



                let formData = $("#posFormData").serialize();
                let formDatas = $("#paymentForm").serialize();

                console.log("Clicked");
                console.log(formDatas);
                console.log('ok');

                let customerDropDown = $("#customerDropDown").val();
                let kamDropDown = $("#kamDropDown").val();
                var itemsTbody = $("#itemsTbody").html();

                let walkinCheck = $("#walkInCustomerCheckbox").is(":checked");
                console.log('walkinCheck => 🍉🍉🍉', walkinCheck);

                if (!walkinCheck) {
                    if (customerDropDown == "" && kamDropDown == "") {
                        $("#alertModal").modal("show");
                        $(".errorMsg").html(`<span class="text-warning">All fields are required.</span>`);
                        return false;
                    }
                }

                if (itemsTbody != "") {
                    $.ajax({
                        type: "POST",
                        url: `ajax/ajax-pos-create.php`,
                        data: formData,
                        beforeSend: function() {
                            $("#posFinalSubmitBtn").text(`Submitting...`);
                        },
                        success: function(response) {
//console.log(response);
                            $("#posFinalSubmitBtn").text('Submitted');
                            let data = JSON.parse(response);
                            console.log(data);
                            console.log("data => 🍿🍿", data);
                            console.log(data.type);
                            console.log("clicked");
                            $("#amount").val(totalAmount);
                            $("#bank_id").val(bank_id);

                            function triggerRazorpay() {
                                const options = {
                                    "key": "rzp_test_SRNNcrFvhl0M3C",
                                    "amount": totalAmount,
                                    "bank_id": bank_id,
                                    "currency": "INR",
                                    "name": "Your Company Name",
                                    "description": "Lorem ipsum Dolor Sit",
                                    "image": "https://vitwo.ai/aiassets/logo/Vitwo-AI-LOGO.png",
                                    "prefill": {
                                        "name": customerName,
                                        "email": "somdutta075@gmail.com"
                                    },
                                    "theme": {
                                        "color": "#003060"
                                    },
                                    "method": {
                                        "upi": true
                                    },
                                    "handler": function(response) {
                                        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                                        amount = response.amount;
                                        //bank_id = response.bank_id;
                                        document.getElementById('payment-form').submit();
                                    },
                                    "modal": {
                                        "ondismiss": function() {
                                            alert("Payment Cancelled");
                                        }
                                    }
                                };
                                var rzp = new Razorpay(options);
                                rzp.open();
                            }

                            if (data.status === "error") {
                                $("#alertModal").modal("show");
                                $(".errorMsg").html(`<span class="text-danger">${data.message}</span>`);
                                $(".alertModalOkBtn").html(`<a href="" type="button" class="btn btn-secondary">OK</a>`);
                            } else if (data.status === "warning") {
                                $("#alertModal").modal("show");
                                $(".errorMsg").html(`<span class="text-danger">${data.message}</span>`);
                                $(".alertModalOkBtn").html(`<button type="button" class="btn btn-secondary">OK</button>`);
                            }

                            if (data.type === "pos_invoice") {
                                if (data.status === "success") {
                                    $(".saveSuccessFullyMsg").html(data.message);
                                    let basicDetails = data.InvoicingInputData.BasicDetails;
                                    let customerDetails = data.InvoicingInputData.customerDetails;
                                    let itemsObj = data.InvoicingInputData.FGItems;
                                    let companyDetails = data.InvoicingInputData.companyDetails;

                                    let grandSubTotal = 0;
                                    let grandTotal = 0;
                                    let grandTotalDiscount = 0;
                                    let i = 1;
                                    let items = $.map(itemsObj, function(item, index) {
                                        grandSubTotal += (parseFloat(item.unitPrice) * item.qty);
                                        grandTotal += parseFloat(item.totalPrice);
                                        grandTotalDiscount += parseFloat(item.itemTotalDiscount);
                                        return '<tr>' +
                                            '<td>' + i++ + '</td>' +
                                            '<td>' + item.itemName + '</td>' +
                                            '<td>' + item.qty + '</td>' +
                                            '<td class="text-right">' + item.totalPrice + '</td>' +
                                            '</tr>';
                                    });

                                    let totalItems = items.length;

                                    $("#invoice").modal("show");
                                    $(".receiptData").html(`<table class="invoice-pos">
                                    <thead>
                                        <tr>
                                            <th colspan="4" class="p-1 text-xs text-center">${companyDetails.company_name}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4" class="p-1 text-md text-center">Ref : <span class="receiptInvoiceNo">#${basicDetails.documentNo}</span></th>
                                        </tr>
                                        <tr>
                                            <th colspan="4" class="p-0 text-xs">Date: <span class="receiptDate">${basicDetails.documentDate}</span></th>
                                        </tr>
                                        <tr>
                                            <th colspan="4" class="p-0 text-xs">Customer: <span class="receiptCustomerName">${customerDetails.customerName}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-bottom-td">
                                        <tr>
                                            <th class="pb-1">#</th>
                                            <th class="pb-1">Product</th>
                                            <th class="pb-1">Quantity</th>
                                            <th class="pb-1">Sub Total</th>
                                        </tr>
                                        ${items}
                                        <tr class="totalItemsScope pt-3">
                                            <td colspan="2">Total Items</td>
                                            <td>${totalItems}</td>
                                            <td class="font-bold text-right">${grandSubTotal.toFixed(2)}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="text-right">Discount</td>
                                            <td colspan="2" class="text-right">${grandTotalDiscount.toFixed(2)}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="font-bold">Grand total</td>
                                            <td colspan="2" class="font-bold text-right">${grandTotal.toFixed(2)}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="border-0">${companyDetails.location}</td>
                                            <td colspan="2" class="text-right border-0">Tel: ${companyDetails.companyPhone}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-center border-0">
                                                <img src="https://static.vecteezy.com/system/resources/previews/001/199/360/non_2x/barcode-png.png" style="width: 100%; height:50px" alt="barcode-img">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-center border-0">
                                                <button type="button" class="btn btn-primary">Thank you for your business</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>`);
                                    if (paymentMethod === "online") {

                                        triggerRazorpay();
                                    }
                                } else if (data.status === "low") {
                                    $("#alertModal").modal("show");
                                    $(".errorMsg").html(`<span class="text-danger">Stock is low</span>`);
                                } else {
                                    $("#alertModal").modal("show");
                                    $(".errorMsg").html(`<span class="text-warning">${data.message}</span>`);
                                }
                            } else if (data.type === "pos_salesorder") {
                                if (data.status === "success") {
                                    $("#alertModal").modal("show");
                                    $(".refNumberMsg").html(`<span class="text-dark">${data.soNumber}</span>`);
                                    $(".errorMsg").html(`<span class="text-success">${data.message}</span>`);
                                    $(".alertModalOkBtn").html(`<a href="" type="button" class="btn btn-secondary">OK</a>`);
                                    if (paymentMethod === "online") {

                                        triggerRazorpay();
                                        //if success collection
                                    }
                                    else{
                                        //collection
                                        
                                    }

                                } else {
                                    $("#alertModal").modal("show");
                                    $(".errorMsg").html(`<span class="text-success">${data.message}</span>`);
                                }
                            }
                        }
                    });
                } else {
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



    $("#walkInCustomerCheckbox").on("change", function() {
        if ($(this).is(":checked")) {
            $(".walkinCustomerDiv").show();
            $("#customerDropDown").val("").trigger("change");
            // $("#customerDropDown, #kamDropDown").val("").trigger("change");
            $("#customerDropDown").prop("disabled", true);
            // $("#customerDropDown, #kamDropDown").prop("disabled", true);
        } else {
            $(".walkinCustomerDiv").hide();
            $("#customerDropDown").val("").trigger("change");
            // $("#customerDropDown, #kamDropDown").val("").trigger("change");
            $("#customerDropDown").prop("disabled", false);
            // $("#customerDropDown, #kamDropDown").prop("disabled", false);
        }
    })

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

        // Conditionally add the 'Add New' button based on the element
        if (!$results.find('a').length) {
            $results.append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewCustomerModal">Add New</a></div>`);
        }
    });

    $("#bankId").select2({
        dropdownParent: $("#paymentConfirm")
    });

    $("#payOption").select2({

    });


    // $(document).ready(function(){
    //     $('#posFinalSubmitBtn').on('click', function(e) {
    //         e.preventDefault(); // Prevent the default form submission

    //         var formData = $('#paymentForm').serialize(); // Serialize the form data
    //         console.log(formData);

    //         // $.ajax({
    //         //     type: 'POST',
    //         //     url: $('#paymentForm').attr('action'), // The form action URL
    //         //     data: formData,
    //         //     success: function(response) {
    //         //         // Handle the response from the server
    //         //         console.log('Form submitted successfully');
    //         //         console.log(response);
    //         //     },
    //         //     error: function(xhr, status, error) {
    //         //         // Handle errors here
    //         //         console.log('Form submission failed');
    //         //         console.log(xhr.responseText);
    //         //     }
    //         // });
    //     });
    // });
</script>

<script src="<?= BASE_URL; ?>public/validations/soValidation.js"></script>
<script src="<?= BASE_URL; ?>public/validations/goodsInvoiceValidation.js"></script>
<script src="https://code.getmdl.io/1.2.0/material.min.js"></script>