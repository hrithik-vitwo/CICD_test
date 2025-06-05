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
$posType = "";
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
// if (isset($_POST['addNewInvoiceFormSubmitBtn'])) {
//     console($_POST);
//     $addGoodsInvoice = $BranchSoObj->insertBranchDirectInvoice($_POST);
//     console($addGoodsInvoice);
//     // if ($addGoodsInvoice['status'] == "success") {
//     //     swalAlert($addGoodsInvoice["status"], $addGoodsInvoice['invoiceNo'], $addGoodsInvoice["message"], 'manage-invoices.php');
//     // } else {
//     //     swalAlert($addGoodsInvoice["status"], 'Warning', $addGoodsInvoice["message"]);
//     // }
// }
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

<div class="content-wrapper">
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
                <input type="hidden" name="act" value="<?= $posType ?>">
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
                                <div class="card-body others-info vendor-info so-card-body">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row customer-info-form-view" style="row-gap: 15px;">
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="input-box customer-select">
                                                        <span class="text-danger">*</span>
                                                        <select name="customerId" id="customerDropDown" class="form-control">
                                                            <option value="">Select Customer</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="">Select Sales Person <span class="text-danger">*</span></label>
                                                        <select name="kamId" class="form-control" id="kamDropDown">
                                                            <option value="">Select Sales Person</option>
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
                                                <h5 class="modal-title" id="exampleModalLabel">Add Invoice</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h3 class="font-bold text-sm modalCustomer"></h3>
                                                <p class="text-sm font-bold mb-3">11 items</p>
                                                <h4 class="font-bold text-md">Total <span class="grandTotalAmt">0.00</span></h4>
                                                <div class="form-inline gap-2">
                                                    <label for="" class="text-xs">Payment Method</label>
                                                    <select name="bankId" class="form-control select2" id="bankId" required>
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


                                                <div class="form-input mt-2 mb-3">
                                                    <label for="paid">Paid</label>
                                                    <input type="text" class="form-control paidAmount">
                                                </div>
                                                <h4 class="font-bold text-md">Change <span class="changeAmount">0.00</span></h4>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" name="addNewInvoiceFormSubmitBtn" id="posFinalSubmitBtn">Confirm</button>
                                            </div>
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
                                    <input type="hidden" name="groupIdSearch" id="groupIdSearch" value="0">

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

<?php require_once("../../common/footer.php"); ?>

<script>
    $(document).ready(function() {

        loadCustomers();

        // load customer 
        function loadCustomers() {
            $.ajax({
                type: "GET",
                url: `../ajaxs/so/ajax-customers.php`,
                data: {
                    customerId: '<?= $customerId ?>'
                },
                beforeSend: function() {
                    $("#customerDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $("#customerDropDown").html(response);
                }
            });
        }




        // customer dropdown
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
                        // console.log(response);
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
                                console.log(data2);
                                if (data2.status == "success") {
                                    let profit_center = data2.data.profit_center;
                                    let kamId = data2.data.kamId;
                                    let complianceInvoiceType = data2.data
                                        .complianceInvoiceType;
                                    let placeOfSupply = data2.data.placeOfSupply;
                                    let invoiceNoFormate = data2.data
                                        .invoiceNoFormate;
                                    let bank = data2.data.bank;

                                    $("#profitCenterDropDown").val(profit_center)
                                        .trigger("change");
                                    $("#compInvoiceType").val(complianceInvoiceType)
                                        .trigger("change");
                                    $("#kamDropDown").val(kamId).trigger("change");
                                    $("#bankId").val(bank).trigger("change");
                                    $("#placeOfSupply1").val(placeOfSupply).trigger(
                                        "change");
                                    $("#iv_varient").val(invoiceNoFormate).trigger(
                                        "change");
                                } else {
                                    console.log('somthing went wrong');
                                    $("#profitCenterDropDown").val('').trigger(
                                        "change");
                                    $("#compInvoiceType").val('R').trigger(
                                        "change");
                                    $("#kamDropDown").val('').trigger("change");
                                    $("#bankId").val('').trigger("change");
                                    $("#placeOfSupply1").val('').trigger("change");
                                    $("#iv_varient").val('').trigger("change");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log("Error 2:", error);
                            }
                        });
                    }
                });
            }
        });


        document.getElementById("printBtn").addEventListener("click", function() {
            var contentToPrint = document.getElementById("contentToPrint");
            var printWindow = window.open('', '', 'height=500,width=800');
            printWindow.document.write('<html><head><title>Print</title></head><body>');
            printWindow.document.write(contentToPrint.innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        });

        // itemSearchInput
        // $("#itemSearchInput").on('keyup', function() {
        //     let searchText = $(this).val();

        //     $.ajax({
        //         type: "GET",
        //         url: `ajax/ajax-items-search.php`,
        //         data: {
        //             act: "itemSearch",
        //             searchText
        //         },
        //         beforeSend: function() {
        //             $(".itemGroupListDiv").html(`<option value="">Loding...</option>`);
        //         },
        //         success: function(response) {
        //             $(".itemGroupListDiv").html(response);
        //         }
        //     });
        // });

        // Function to handle item search
        //         $(".itemSearchInput").on("keyup", function() {
        //             var searchValue = $(this).val().toLowerCase();
        //             var itemsFound = false; // Flag to check if any items match the search

        //             $(".oneItemCard").each(function() {
        //                 var itemName = $(this).find("p").text().toLowerCase();

        //                 if (itemName.includes(searchValue)) {
        //                     $(this).show();
        //                     itemsFound = true; // At least one item is found
        //                 } else {
        //                     $(this).hide();
        //                 }
        //             });

        //             // Show/hide the "not found" message based on the search result
        //             if (!itemsFound) {
        //                 $(".dataNotFound").html('Data not found');
        //             } else {
        //                 $(".dataNotFound").html('');
        //             }
        //         });


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
            $('#groupIdSearch').val(groupId);
            // alert(groupId);

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
            console.log(itemStock)
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
                alert(`Do not have available stocks`);
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
        $("#posFinalSubmitBtn").on("click", function(e) {
            e.preventDefault();
            let formData = $("#posFormData").serialize();
            let customerDropDown = $("#customerDropDown").val();
            let kamDropDown = $("#kamDropDown").val();
            var itemsTbody = $("#itemsTbody").html();

            if (customerDropDown != "" && kamDropDown != "") {
                if (itemsTbody != "") {
                    $.ajax({
                        type: "POST",
                        url: `ajax/ajax-pos-create.php`,
                        data: formData,
                        beforeSend: function() {
                            $("#posFinalSubmitBtn").text(`Submitting...`);
                        },
                        success: function(response) {

                            $("#posFinalSubmitBtn").text('Submitted');
                            let data = JSON.parse(response);
                            console.log("data => 🍿🍿", data);
                            if (data.status === "error") {
                                $("#alertModal").modal("show");
                                $(".errorMsg").html(
                                    `<span class="text-danger">${data.message}</span>`);
                                $(".alertModalOkBtn").html(
                                    `<a href="" type="button" class="btn btn-secondary">OK</a>`
                                );
                            } else if (data.status === "warning") {
                                $("#alertModal").modal("show");
                                $(".errorMsg").html(
                                    `<span class="text-danger">${data.message}</span>`);
                                $(".alertModalOkBtn").html(
                                    `<button type="button" class="btn btn-secondary">OK</button>`
                                );
                            }
                            if (data.type === "pos_invoice") {
                                if (data.status === "success") {
                                    $(".saveSuccessFullyMsg").html(data.message);
                                    let basicDetails = data.InvoicingInputData.BasicDetails;
                                    let customerDetails = data.InvoicingInputData
                                        .customerDetails;
                                    let itemsObj = data.InvoicingInputData.FGItems;
                                    let companyDetails = data.InvoicingInputData.companyDetails;

                                    let grandSubTotal = 0;
                                    let grandTotal = 0;
                                    let grandTotalDiscount = 0;
                                    let i = 1;
                                    // let grandTotal = ;
                                    let items = $.map(itemsObj, function(item, index) {
                                        grandSubTotal += (parseFloat(item.unitPrice) *
                                            item.qty);
                                        grandTotal += parseFloat(item.totalPrice);
                                        grandTotalDiscount += parseFloat(item
                                            .itemTotalDiscount);
                                        return '<tr>' +
                                            '<td>' + i++ + '</td>' +
                                            '<td>' + item.itemName + '</td>' +
                                            '<td>' + item.qty + '</td>' +
                                            '<td class="text-right">' + item
                                            .totalPrice + '</td>' +
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
                        </table>
                        `);
                                } else if (data.status === "low") {
                                    $("#alertModal").modal("show");
                                    $(".errorMsg").html(
                                        `<span class="text-danger">Stock is low</span>`);
                                } else {
                                    $("#alertModal").modal("show");
                                    $(".errorMsg").html(
                                        `<span class="text-warning">${data.message}</span>`);
                                }
                            } else if (data.type === "pos_salesorder") {
                                if (data.status === "success") {
                                    $("#alertModal").modal("show");
                                    $(".refNumberMsg").html(
                                        `<span class="text-dark">${data.soNumber}</span>`);
                                    $(".errorMsg").html(
                                        `<span class="text-success">${data.message}</span>`);
                                    $(".alertModalOkBtn").html(
                                        `<a href="" type="button" class="btn btn-secondary">OK</a>`
                                    );
                                } else {
                                    $("#alertModal").modal("show");
                                    $(".errorMsg").html(
                                        `<span class="text-success">${data.message}</span>`);
                                }
                            }
                        }
                    });
                } else {
                    $("#alertModal").modal("show");
                    $(".errorMsg").html(`<span class="text-warning">Please add item.</span>`);
                }
            } else {
                $("#alertModal").modal("show");
                $(".errorMsg").html(`<span class="text-warning">All fields are required.</span>`);
            }
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


    $("#bankId").select2({
        dropdownParent: $("#paymentConfirm")
    });

    $("#payOption").select2({

    });
</script>

<script src="<?= BASE_URL; ?>public/validations/soValidation.js"></script>
<script src="<?= BASE_URL; ?>public/validations/goodsInvoiceValidation.js"></script>
<script src="https://code.getmdl.io/1.2.0/material.min.js"></script>