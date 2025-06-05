<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
require_once("../../app/v1/functions/branch/func-debit-note.php");
require_once("../../app/v1/functions/common/templates/template-debitnote.controller.php");
// require_once("../../app/v1/functions/common/templates/template-sales-order.controller.php");

$company_data = getCompanyDataDetails($company_id);
$gl_account_length = $company_data['data']['gl_account_length'];
$templatedebitNoteControllerObj = new TemplateDebitNoteController();

// if (isset($_POST["createdata"])) {
//     // console($_POST);
//     $addNewObj = createCreditNote($_POST);
//     // console($addNewObj);
//     swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
// }

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if ($_POST['action'] == "dn") {
    // console($_POST);

    $addDebit = debit_note_add($_POST + $_FILES);
    // console($_POST);

    if ($addDebit['status'] == "success") {
        swalAlert($addDebit["status"], $addDebit['debit_note_no'], $addDebit["message"], $_SERVER['PHP_SELF']);
    } else {
        swalAlert($addDebit["status"], 'warning', $addDebit["message"]);
    }
}
?>
<style>
    .content-wrapper table tr.debot-credit-tr td {
        font-size: 12px;
        text-align: left;
        color: #3b3b3b;
        vertical-align: middle;
        background: #f0f5fa;
        padding: 0px 15px;
        white-space: nowrap;
    }

    tbody.debit-credit-1 td {
        padding: 5px;
        border: none;
    }


    tbody.debit-credit-1 tr.debot-credit-tr td {
        background: #b5c5d3;
        text-align: center;
        padding: 0.25rem;
    }

    .green-text {
        color: #14ca14 !important;
        font-weight: 600;
    }

    .red-text {
        color: red !important;
        font-weight: 600;
    }

    section.credit-notes .credit-note-form-card .form-input {
        margin: 3px 0;
    }

    section.credit-notes .credit-note-form-card p {
        font-size: 11px;
        font-weight: 400;
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .td-flex {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .td-flex span {
        font-weight: 600;
    }

    tbody.total-row tr td {
        padding: 10px 15px;
    }

    .card.credit-note-table .head {
        white-space: nowrap;
        max-width: 300px;
    }

    p.text-info-below {
        font-size: 10px;
        padding-top: 3px;
        font-weight: 600;
        text-align: right;
    }

    .selection-radio {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .selection-radio div {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .address-to {
        box-shadow: none !important;
    }

    .address-to p {
        font-size: 10px;
        font-weight: 200 !important;
    }


    .credit-note-basic {
        border: 1px solid #aeaeae;
        padding: 20px 10px;
        border-radius: 12px;
        min-height: 100%;
    }

    .credit-notes-table .round-off-section {
        display: flex;
        gap: 20px;
        font-size: 12px;
        margin-left: auto;
        padding: 15px 0;
        flex-direction: column;
    }

    .credit-notes-table .round-off.calculte-input {
        position: absolute;
        top: 25px;
        left: 0;
    }

    #round_off_hide_grn {
        border-radius: 12px;
        padding: 10px 0px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 10px 0;
    }

    #round_off_hide_inv {
        border-radius: 12px;
        padding: 10px 0px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin: 10px 0;
    }

    .formItemErrors {
        color: red;
        font-size: 9px;
        font-weight: 600;
    }

    .table-responsive table tr td {
        position: relative;
    }

    p.validation-message {
        position: relative;
        top: 6px;
        font-weight: 400;
    }

    ol#errorMessage p.validation-message {
        font-size: 11px !important;
        position: relative;
        top: 0;
        padding: 3px 0;
    }

    #loader {
        display: none;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<?php

if (isset($_GET['create'])) {

?>
    <div class="content-wrapper notes-credit-debit is-debit-notes">
        <section class="content">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Debit Note List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Debit Note</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" id="drnote" name="drnote" enctype="multipart/form-data">

                    <input type="hidden" name="action" value="dn">
                    <section class="credit-notes">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card credit-note-form-card">
                                    <div class="card-header p-2">
                                        <div class="head p-2">
                                            <h4>Create New Debit Note</h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12 px-4">
                                                <div class="row credit-note-basic">
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <div class="form-input">
                                                            <label class="label-hidden" for="">Select One <span class="text-danger">*</span></label>
                                                            <fieldset>
                                                                <div class="selection-radio">
                                                                    <div class="customer-select">
                                                                        <input type="radio" class="customerClass" id="select_customer_vendor" name="select_customer_vendor" value="Customer" checked>
                                                                        <label for="customer" class="mb-0">Customer</label>
                                                                    </div>
                                                                    <div class="vendor-select">
                                                                        <input type="radio" class="vendorClass" id="select_customer_vendor" name="select_customer_vendor" value="Vendor">
                                                                        <label for="vendor" class="mb-0">Vendor</label>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <div class="form-input">
                                                            <label id="labeldrop" for="">Select Party <span class="text-danger">*</span></label>
                                                            <select name="vendor_customer" id="vendor_customer" class="form-control select2">

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                        <div class="row address-section">
                                                            <div class="col-lg-6 col-md-12 col-sm-12 pl-0 pr-3">
                                                                <div class="address-to bill-to bg-transparent pl-0 pr-3">
                                                                    <h5>Bill to</h5>
                                                                    <hr class="mt-0 mb-2">
                                                                    <p id="bill_to_address">
                                                                    </p>
                                                                    <input type="hidden" name="billToInput" id="billToInput" value="">

                                                                </div>
                                                            </div>

                                                            <div class="col-lg-6 col-md-12 col-sm-12 pr-0 pl-3">
                                                                <div class="address-to ship-to bg-transparent pr-0 pl-3">
                                                                    <div class="row">
                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                            <h5>Ship to</h5>
                                                                        </div>
                                                                        <div class="col-lg-8 col-md-8 col-sm-8">
                                                                            <!-- <h5 class="display-inline">
                                                                            <div class="checkbox-label">
                                                                                <input type="checkbox" id="addresscheckbox" title="checked here for same as Bill To adress" data-toggle="modal" data-target="#address-change" checked="">
                                                                                <p>Same as Bill to</p>
                                                                            </div>
                                                                            <button type="button" class="btn btn-primary change-address-modal text-xs btn-sm waves-effect waves-light" data-toggle="modal" data-target="#address-change">
                                                                                Change
                                                                            </button>
                                                                        </h5> -->
                                                                        </div>
                                                                    </div>



                                                                    <hr class="mt-0 mb-2">
                                                                    <p id="ship_to_address">

                                                                    </p>

                                                                    <input type="hidden" name="shipToInput" id="shipToInput" value="">

                                                                </div>





                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Source of Supply</label>
                                                            <select name="source" id="supplyAddress" class="form-control">

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Destination of Supply</label>
                                                            <select name="destination" id="destinationAddress" class="form-control">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-md-6 col-sm-12 compInvoiceTypeDiv">
                                                        <div class="form-input">
                                                            <label for="">Compliance Invoice Type <span class="text-danger">*</span></label>
                                                            <select name="compInvoiceType" class="form-control" id="compInvoiceType" required>
                                                                <option value="R" selected>Domestic: R- Regular</option>
                                                                <option value="CBW">Export: CBW - Custom Bonded Warehouse</option>
                                                                <option value="LUT">Export: LUT - LETTER UNDERTAKING</option>
                                                                <option value="SEWOP">Export: SEWOP - SEZ WITHOUT PAYMENT</option>
                                                                <option value="SEWP">Export: SEWP - SEZ Exports with payment</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 px-4">
                                                <div class="row credit-note-basic">
                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Reference</label>
                                                            <select name="bill" id="bill" class="form-control" required>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-12 col-sm-12">
                                                        <div class="form-inline my-3">
                                                            <p class="fw-normal">Bill Type : </p>
                                                            &nbsp;
                                                            <p id="bill_type"></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8 col-md-12 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Debit Note Varient <span class="text-danger">*</span></label>
                                                            <select name="iv_varient" class="form-control" id="iv_varient" required>
                                                                <?php
                                                                $iv_varient = queryGet("SELECT * FROM `erp_dn_varient` WHERE company_id=$company_id AND status='active' ORDER BY id ASC", true);
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
                                                            <div class="display-flex" style="justify-content: flex-end;">
                                                                <p class="label-bold text-italic" style="white-space: pre-line;"><span class="mr-1">e.i- </span> <span class="ivnumberexample text-s"><?= $ivselecetd; ?></span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Party Debit Date <span class="text-danger">*</span></label>
                                                            <input type="date" name="posting_date" class="form-control" id='partyCreditDate'>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Reasons <span class="text-danger">*</span></label>
                                                            <select name="reasons" id="reasons" class="form-control">
                                                                <option value="">Select Reason</option>
                                                                <option value="Sales Return">Sales Return</option>
                                                                <option value="Post Sale Discount">Post Sale Discount</option>
                                                                <option value="Deficiency in Service">Deficiency in Service</option>
                                                                <option value="Correction in invoice">Correction in invoice</option>
                                                                <option value="Change in POS">Change in POS</option>
                                                                <option value="Finalization in provisional assesment">Finalization in provisional assesment</option>
                                                                <option value="Purchase Return">Purchase Return</option>
                                                                <option value="Others">Others</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <!-- add customer details section start -->
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <div style="display: flex; justify-content: space-between; align-items: center">
                                                            <label for="" class="">Contact Details</label>
                                                            <button type="button" data-toggle="modal" data-target="#configModal" style="border: none; font-size: 10px; padding: 0px 5px; margin-bottom: 5px;" class="btn btn-sm btn-primary">
                                                                Add New
                                                            </button>
                                                        </div>
                                                        <?php
                                                        $configListObj = "SELECT * FROM `erp_config_invoices` WHERE `company_id` = $company_id AND `branch_id` = $branch_id AND `location_id` = $location_id";
                                                        $configList = queryGet($configListObj, true);
                                                        // console($configList);
                                                        ?>
                                                        <select name="companyConfigId" class="form-control" id="config">
                                                            <option value="">Select One</option>
                                                            <!-- <?php foreach ($configList['data'] as $config) { ?>
                                                                <option id="contactId" value=""></option>  
                                                            <?php } ?> -->
                                                        </select>
                                                    </div>
                                                    <!-- add customer details section end-->
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <div class="form-input">
                                                            <label for="">Attached file</label>
                                                            <input type="file" name="attachment" class="form-control">
                                                        </div>

                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <div class="form-input">
                                                            <label for="">Notes</label>
                                                            <textarea name="note" id="" cols="55" rows="2" placeholder="notes...." class="form-control"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card credit-note-table">
                                    <!-- <div class="head p-2 mt-3">
                                    <h4 class="mb-0">Items Rated are</h4>
                                    <select name="" id="" class="form-control">
                                        <option value="0">test</option>
                                        <option value="1">test</option>
                                        <option value="2">test</option>
                                    </select>
                                </div> -->
                                    <div class="card-body pl-0 pr-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-nowrap mb-0 debit-note-table credit-notes-table" id="inv_items">
                                                <thead>
                                                    <tr>
                                                        <th>Item Details</th>
                                                        <th>Account</th>
                                                        <th>Quantity</th>
                                                        <th>Rate</th>
                                                        <th style="width: 15%;">Tax</th>
                                                        <th style="width: 10%;">Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="add-row inv_items">

                                                    <?php
                                                    $rand = rand(100, 1000);

                                                    ?>

                                                    <div class="btns-grp d-flex ml-2">
                                                        <a class="btn btn-primary addItemBtn btn-xs">
                                                            <i class="fa fa-plus" id="addItemBtn"></i>
                                                        </a>
                                                    </div>
                                                    <tr class="items_row">
                                                        <td style="width:20%">
                                                            <select name="item[<?= $rand ?>][item_id]" class="form-control item_select" data-attr="<?= $rand ?>">
                                                                <option value='0'>SELECT ITEM</option>
                                                                <?php

                                                                $item_sql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id` = $company_id", true)['data'];
                                                                foreach ($item_sql as $item) {



                                                                ?>



                                                                    <option value="<?= $item['itemId'] . '|' . $item['itemCode'] . '|' . $item['itemName'] . '|' . $item['goodsType'] ?>"><?= $item['itemName'] . '[' .  $item['itemCode']  . ']' ?></option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                            <input type="hidden" value="" name="item[<?= $rand ?>][item_code]">
                                                        </td>
                                                        <td>
                                                            <select name="item[<?= $rand ?>][account]" class="form-control gl_select account_<?= $rand ?>">

                                                                <option>Select Account</option>
                                                                <?php

                                                                $gl_sql = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND glStType='account'  AND `status`!='deleted' ORDER BY gl_code", true);
                                                                foreach ($gl_sql['data'] as $gl) {


                                                                ?>
                                                                    <option value="<?= $gl['id'] ?>"><?= $gl["gl_code"] . "|" . $gl["gl_label"] ?></option>
                                                                <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </td>

                                                        <td class="text-right">
                                                            <div>
                                                                <span class="custom_batch_<?= $rand ?> d-none">
                                                                    <input type="hidden" name="item[<?= $rand ?>][stockQty]" class="form-control checkQty" id="checkQty_<?= $rand ?>" value="<?= $sumOfBatches; ?>">

                                                                    <!-- Button to Open the Modal -->
                                                                    <div class="qty-modal py-2">
                                                                        <p class="font-bold text-center checkQtySpan" id="checkQtySpan_<?= $rand ?>"><?= $sumOfBatches; ?></p>
                                                                        <hr class="my-2 w-50 mx-auto">
                                                                        <div class="text-xs d-flex align-items-center gap-2 justify-content-center">
                                                                            <p class="itemSellType" id="itemSellType_<?= $rand ?>">CUSTOM</p>
                                                                            <ion-icon name="create-outline" class="stockBtn" id="stockBtn_<?= $rand ?>" data-bs-toggle="modal" data-bs-target="#stockSetup<?= $rand ?>" style="cursor: pointer;"></ion-icon>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" class="itemSellTypeHidden" id="itemSellTypeHidden_<?= $rand ?>" name="item[<?= $rand ?>][itemSellType]" value="CUSTOM">

                                                                    <!-- The Modal -->
                                                                    <div class="modal fade stock-setup-modal" id="stockSetup<?= $rand ?>">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">

                                                                                <!-- Modal Header -->
                                                                                <div class="modal-header" style="background: #003060; color: #fff;">
                                                                                    <h4 class="modal-title text-sm text-white">Stock Setup (CUSTOM)</h4>
                                                                                    <p class="text-xs my-2 ml-5">Total Picked Qty :
                                                                                        <span class="font-bold itemSelectTotalQty" id="itemSelectTotalQty_<?= $rand ?>">0</span>
                                                                                    </p>

                                                                                </div>

                                                                                <!-- Modal body -->
                                                                                <div class="modal-body">

                                                                                    <!-- start warehouse accordion -->
                                                                                    <div class="modal-select-type my-3">
                                                                                        <div class="type type-three">
                                                                                            <input type="radio" name="item[<?= $rand ?>][itemreleasetype]" class="itemreleasetypeclass custom" data-rdcode="<?= $rand ?>" value="CUSTOM" id="custom_<?= $rand ?>" checked>
                                                                                            <label for="custom" class="text-xs mb-0 text-muted">Custom</label>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="customitemreleaseDiv<?= $rand ?>">

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
                                                                    <input class="form-control" type="hidden" name="item[<?= $rand ?>][sumOfBatches]" value="<?= $sumOfBatches ?>">
                                                                </span>

                                                                <input type="number" name="item[<?= $rand ?>][qty]" class="form-control itemQty" id="itemQty_<?= $rand ?>" value="<?= $data['qty'] ?>">
                                                            </div>
                                                        </td>
                                                        <td class="text-right"><input type="number" name="item[<?= $rand ?>][rate]" class="form-control price" id="price_<?= $rand ?>" value="<?= $data['unitPrice'] ?>"></td>
                                                        <td>
                                                            <div class="tax-amount d-flex gap-2">
                                                                <input type="number" class="form-control tax" name="item[<?= $rand ?>][tax]" id="tax_<?= $rand ?>" value="<?= $data['tax'] ?>">
                                                                <span class="percent-position">%</span>
                                                                <input type="hidden" class="form-control tax_amount" name="item[<?= $rand ?>][tax_amount]" id="tax_amount_<?= $rand ?>">
                                                            </div>
                                                        </td>
                                                        <td class="text-right amount" id="amount_<?= $rand ?>"><?= $amount ?>
                                                            <input type="hidden" value="<?= $amount ?>" id="amountHidden_<?= $rand ?>" name="item[<?= $rand ?>][amount]">
                                                        </td>
                                                        <td>
                                                            <div class="btns-grp d-flex gap-2">
                                                                <a style="cursor: pointer" class="btn btn-danger add-btn-minus-bill">
                                                                    <i class="fa fa-minus"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>




                                                </tbody>


                                                <tr>
                                                    <td colspan="4" class="text-right">SGST</td>
                                                    <td colspan="2" class="text-right" id="sgst_span"></td>
                                                    <input type="hidden" name="sgst" id="sgst" value='' />
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-right">CGST</td>
                                                    <td colspan="2" class="text-right" id="cgst_span"></td>
                                                    <input type="hidden" name="cgst" id="cgst" value='' />
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-right">IGST</td>
                                                    <td colspan="2" class="text-right" id="igst_span"></td>
                                                    <input type="hidden" name="igst" id="igst" value='' />
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-right">Sub Total</td>
                                                    <td colspan="2" class="text-right" id="subTotal">
                                                        <input type="hidden" id="subTotal" name="subTotal" value="">

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td>
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 px-0">
                                                                <div class="round-off-section p-0">
                                                                    <div class="round-off-head d-flex gap-2">
                                                                        <input type="checkbox" class="checkbox" name="round_off_checkbox" id="round_off_checkbox">
                                                                        <p class="text-xs" for="round_off_checkbox">Adjust Amount</p>
                                                                    </div>
                                                                    <div id="round_off_hide" style="display:none;">
                                                                        <div class="row round-off calculte-input px-0">
                                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                <div class="adjust-currency d-flex gap-2">
                                                                                    <select id="round_sign" name="round_sign" class="form-control text-center">
                                                                                        <option value="+">+</option>
                                                                                        <option value="-">-</option>
                                                                                    </select>
                                                                                    <input type="number" step="any" id="round_value" name="round_value" value="0" class="form-control text-center">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- <div class="row" style="width: 100%;">
                                                                            <div class="col-lg-12 col-md-12 col-sm-12 px-0">
                                                                                <div class="totaldueamount d-flex justify-content-between border-top border-white pt-2">
                                                                                    <p class="font-bold">Adjusted Amount</p>
                                                                                    <input type="hidden" name="paymentDetails[adjustedCollectAmount]" class="adjustedCollectAmountInp">
                                                                                    <p class="text-success font-bold rupee-symbol">₹ <span class="adjustedDueAmt">0</span></p>
                                                                                    <input type="hidden" name="paymentDetails[roundOffValue]" class="roundOffValueHidden">
                                                                                </div>
                                                                            </div>
                                                                        </div> -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <td colspan="4" class="text-right font-bold"> Total</td>
                                                    <td colspan="2" class="text-right font-bold" id="grandTotal">


                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><input type="hidden" id="grandTotalHidden" name="grandTotal" value=""></td>
                                                    <td> <input type="hidden" name="discountAmount" id="discountAmountHidden" class="form-control" value="0"></td>
                                                    <td><input type="hidden" class="form-control" name="subTotal" id="subTotalHidden" value=""></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <button type="submit" name="addNewCreditNoteFormSubmitBtn" id="addNewCreditNoteFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" id="cnbtn" value="add_post">Submit</button>

                    </section>
                </form>
            </div>
        </section>
        <!-- validationModal -->
        <div class="modal fade" id="validateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content card">
                    <div class="modal-header card-header py-2 px-3">
                        <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div id="notesModalBody" class="modal-body card-body">
                        <ol id="errorMessage"></ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Config Modal start -->
        <div class="modal fade" id="configModal" tabindex="-1" role="dialog" aria-labelledby="configModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="configModalLabel">Debit Memo Contact</h5>
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
        <!-- Config Modal end -->
    </div>
<?php
} else {
?>
    <div class="content-wrapper is-debit-notes">
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="p-0 pt-1 my-2">
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                    <h3 class="card-title">Manage Debit Notes</h3>
                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary relative-add-btn waves-effect waves-light"><i class="fa fa-plus"></i></a>
                                </li>
                            </ul>


                        </div>
                        <div class="card card-tabs" style="border-radius: 20px;">
                            <div class="card card-tabs" style="border-radius: 20px;">
                                <form name="search" id="search" action="" method="post" onsubmit="return srch_frm();">
                                    <div class="card-body">
                                        <div class="row filter-serach-row">
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                <a type="button" class="btn add-col waves-effect waves-light" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-12">
                                                <div class="row table-header-item">
                                                    <div class="col-lg-12 col-md-11 col-sm-12">
                                                        <div class="section serach-input-section">
                                                            <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="">
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
                                                                    <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="">
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                    <select name="status_s" id="status_s" class="fld form-control" style="appearance: auto;">
                                                                        <option value=""> Status </option>
                                                                        <option value="active">Active
                                                                        </option>
                                                                        <option value="inactive">Inactive
                                                                        </option>
                                                                        <option value="draft">Draft</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                    <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="">
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                    <input class="fld form-control" type="date" name="to_date_s" id="to_date_s" value="">
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <!-- <a class="btn btn-primary" href="/branch/location/manage-pgi.php"><i class="fa fa-sync "></i>Reset</a>-->
                                                            <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="fa fa-search" aria-hidden="true"></i>
                                                                Search</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

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
                                                    $sql_list = "SELECT * FROM `erp_debit_note` WHERE 1 " . $cond . "  AND`branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id " . $sts . "  ORDER BY dr_note_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                                    $qry_list = queryGet($sql_list, true);
                                                    $num_list = $qry_list['numRows'];

                                                    //console($qry_list);

                                                    $countShow = "SELECT count(*) FROM `erp_debit_note` WHERE 1 " . $cond . " AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id " . $sts . " ";





                                                    $countQry = mysqli_query($dbCon, $countShow);
                                                    $rowCount = mysqli_fetch_array($countQry);
                                                    $count = $rowCount[0];
                                                    $cnt = $GLOBALS['start'] + 1;
                                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_DEBIT_NOTE", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                                    $settingsCheckbox = unserialize($settingsCh);
                                                    if ($num_list > 0) {

                                                    ?>
                                                        <table class="table defaultDataTable table-hover">
                                                            <thead>

                                                                <tr class="alert-light">
                                                                    <th>#</th>
                                                                    <th>Debit Note No.</th>
                                                                    <th>Debitor Type</th>
                                                                    <th>Party</th>
                                                                    <th>Reference</th>
                                                                    <th>Posting Date</th>
                                                                    <th> Remarks</th>
                                                                    <th>Source Address</th>
                                                                    <th>Destination Address</th>
                                                                    <th>Total Value</th>
                                                                    <th>Created By</th>
                                                                    <th>Status</th>

                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                // $cnt = 1;
                                                                foreach ($qry_list['data'] as $data) {

                                                                    $bill_id = $data['debitNoteReference'];
                                                                    $debitor_type = $data['debitor_type'];
                                                                    if ($debitor_type == 'customer') {
                                                                        $iv = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id`=$bill_id");
                                                                        // console($iv);
                                                                        $ref = $iv['data']['invoice_no'];
                                                                        $source_address_sql = queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_address_id`= '" . $data['billing_address'] . "' ")['data'];
                                                                        // console($source_address_sql);

                                                                        $source_address = $source_address_sql['customer_address_building_no'] . ',' . $source_address_sql['customer_address_flat_no'] . ',' . $source_address_sql['customer_address_street_name'] . ',' . $source_address_sql['customer_address_pin_code'] . ',' . $source_address_sql['customer_address_location'] . ',' . $source_address_sql['customer_address_city'] . ',' . $source_address_sql['customer_address_district'] . ',' . $source_address_sql['customer_address_country'] . ',' . $source_address_sql['customer_address_state'];

                                                                        $destination_address_sql =  queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_address_id`= '" . $data['shipping_address'] . "' ")['data'];

                                                                        $destination_address = $destination_address_sql['customer_address_building_no'] . ',' . $destination_address_sql['customer_address_flat_no'] . ',' . $destination_address_sql['customer_address_street_name'] . ',' . $destination_address_sql['customer_address_pin_code'] . ',' . $destination_address_sql['customer_address_location'] . ',' . $destination_address_sql['customer_address_city'] . ',' . $destination_address_sql['customer_address_district'] . ',' . $destination_address_sql['customer_address_country'] . ',' . $destination_address_sql['customer_address_state'];
                                                                    } else {
                                                                        $iv = queryGet("SELECT * FROM `erp_grninvoice` WHERE `grnIvId`=$bill_id");
                                                                        $ref = $iv['data']['invoice_ number'];

                                                                        $source_address_sql = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_business_id`= '" . $data['billing_address'] . "' ")['data'];
                                                                        // console($source_address_sql);

                                                                        $source_address = $source_address_sql['vendor_business_building_no'] . ',' . $source_address_sql['vendor_business_flat_no'] . ',' . $source_address_sql['vendor_business_street_name'] . ',' . $source_address_sql['vendor_business_pin_code'] . ',' . $source_address_sql['vendor_business_location'] . ',' . $source_address_sql['vendor_business_city'] . ',' . $source_address_sql['vendor_business_district'] . ',' . $source_address_sql['vendor_business_country'] . ',' . $source_address_sql['vendor_business_state'];

                                                                        $destination_address_sql =  queryGet("SELECT * FROM `erp_customer_address` WHERE `vendor_business_id`= '" . $data['shipping_address'] . "' ")['data'];

                                                                        $destination_address = $destination_address_sql['vendor_business_building_no'] . ',' . $destination_address_sql['vendor_business_flat_no'] . ',' . $destination_address_sql['vendor_business_street_name'] . ',' . $destination_address_sql['vendor_business_pin_code'] . ',' . $destination_address_sql['vendor_business_location'] . ',' . $destination_address_sql['vendor_business_city'] . ',' . $destination_address_sql['vendor_business_district'] . ',' . $destination_address_sql['vendor_business_country'] . ',' . $destination_address_sql['vendor_business_state'];
                                                                    }

                                                                ?>
                                                                    <tr>
                                                                        <td><?= $cnt++ ?></td>
                                                                        <td><?= $data['debit_note_no'] ?></td>
                                                                        <td><?= $debitor_type ?></td>
                                                                        <td><?= $data['party_code'] ?></td>
                                                                        <td><?= $ref ?></td>
                                                                        <td><?= formatDateORDateTime($data['postingDate']) ?></td>
                                                                        <td><?= $data['remark'] ?></td>
                                                                        <td><?= $source_address ?></td>
                                                                        <td><?= $destination_address ?></td>
                                                                        <td>₹<?= $data['total'] ?></td>
                                                                        <td><?= getCreatedByUser($data['created_by']) ?></td>
                                                                        <td>
                                                                            <div class="status listStatus"><?= $data['status'] ?></div>
                                                                        </td>
                                                                        <td>
                                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $data['dr_note_id'] ?>" class="btn btn-sm waves-effect waves-light"><i class="fa fa-eye po-list-icon"></i></a>
                                                                            <!-- <a href="branch-so-invoice.php?pgi-invoice=MTE=" style="cursor: pointer;" class="btn btn-sm"><i class="fa fa-download"></i></a> -->

                                                                            <!-- right modal start here  -->
                                                                            <div class="modal fade right pgi-modal customer-modal classic-view-modal" id="fluidModalRightSuccessDemo_<?= $data['dr_note_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                                                    <!--Content-->
                                                                                    <div class="modal-content">
                                                                                        <!--Header-->
                                                                                        <div class="modal-header">
                                                                                            <div class="d-flex justify-content-between">
                                                                                                <h2 class="text-white mt-2 mb-2 d-flex gap-2"><?= $data['debit_note_no'] ?></h2>
                                                                                                <p class="heading lead text-right mt-2 mb-2"><?= $data['credit_note_no'] ?></p>
                                                                                            </div>
                                                                                            <p class="text-sm text-right mb-2"><?= $ref ?></p>
                                                                                            <p class="text-sm text-right mb-2">Posting Date: <?= formatDateORDateTime($data['postingDate']) ?></p>

                                                                                            <div class="display-flex-space-between mt-2 mb-3">

                                                                                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                                    <li class="nav-item waves-effect waves-light">
                                                                                                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $data['dr_note_id'] ?>" role="tab" aria-controls="home" aria-selected="true">Info</a>
                                                                                                    </li>
                                                                                                    <li class="nav-item">
                                                                                                        <a class="nav-link" id="classic-view-tab" data-toggle="tab" href="#classicViewTab<?= $data['dr_note_id'] ?>" role="tab" aria-controls="classic-view" aria-selected="false">Classic View</a>
                                                                                                    </li>
                                                                                                    <!----------------Audit History Button Start------------------------- -->
                                                                                                    <li class="nav-item waves-effect waves-light">
                                                                                                        <a class="nav-link auditTrail" id="history-tab<?= $data['dr_note_id'] ?>" data-toggle="tab" data-ccode="<?= $data['dr_note_id'] ?>" href="#history<?= $data['dr_note_id'] ?>" role="tab" aria-controls="history" aria-selected="false"><i class="fa fa-history mr-2"></i>Trail</a>
                                                                                                    </li>
                                                                                                    <!-- -------------------Audit History Button End------------------------- -->
                                                                                                </ul>

                                                                                                <div class="action-btns display-flex-gap" id="action-navbar">
                                                                                                    <form action="" method="POST">


                                                                                                    </form>
                                                                                                </div>
                                                                                            </div>

                                                                                        </div>
                                                                                        <!--Body-->
                                                                                        <div class="modal-body">

                                                                                            <div class="tab-content pt-0" id="myTabContent">
                                                                                                <div class="tab-pane fade show active" id="home<?= $data['dr_note_id'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                                                    <!--------Customer Details--------->
                                                                                                    <!-- <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                                        <div class="accordion-item">
                                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                                <button class="accordion-button btn btn-primary collapsed waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#customerDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                                    Customer Details
                                                                                                                </button>
                                                                                                            </h2>
                                                                                                            <div id="customerDetails" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                                <div class="accordion-body p-0">
                                                                                                                    <div class="card">
                                                                                                                        <div class="card-body p-3">
                                                                                                                            <div class="display-flex-space-between">
                                                                                                                                <p class="font-bold text-xs">Customer Code :</p>
                                                                                                                                <p class="font-bold text-xs">52300005</p>
                                                                                                                            </div>
                                                                                                                            <div class="display-flex-space-between">
                                                                                                                                <p class="font-bold text-xs">Name :</p>
                                                                                                                                <p class="font-bold text-xs">KOLKATA DAIRY PRODUCTS PVT LTD.</p>
                                                                                                                            </div>
                                                                                                                            <div class="display-flex-space-between">
                                                                                                                                <p class="font-bold text-xs">GST :</p>
                                                                                                                                <p class="font-bold text-xs">19AACCK1360N1ZB</p>
                                                                                                                            </div>
                                                                                                                            <div class="display-flex-space-between">
                                                                                                                                <p class="font-bold text-xs">Status :</p>
                                                                                                                                <p class="font-bold text-xs">active</p>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div> -->

                                                                                                    <!--------Item Details--------->
                                                                                                    <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                                        <div class="accordion-item">
                                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                                <button class="accordion-button btn btn-primary collapsed waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#itemDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                                    Items
                                                                                                                </button>
                                                                                                            </h2>
                                                                                                            <div id="itemDetails" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                                <?php
                                                                                                                $iv_item = queryGet("SELECT * FROM `debit_note_item` AS dr_item, `erp_inventory_items` AS item  WHERE item.itemId=dr_item.item_id AND `debit_note_id` = '" . $data['dr_note_id'] . "'", true);
                                                                                                                //console($iv_item);
                                                                                                                foreach ($iv_item['data'] as $item_data) {
                                                                                                                ?>
                                                                                                                    <div class="accordion-body p-0">
                                                                                                                        <div class="card">
                                                                                                                            <div class="card-body p-3">
                                                                                                                                <div class="display-flex-space-between">
                                                                                                                                    <p class="font-bold text-xs">Item Code :</p>
                                                                                                                                    <p class="font-bold text-xs"><?= $item_data['item_code'] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between">
                                                                                                                                    <p class="font-bold text-xs">Item Name :</p>
                                                                                                                                    <p class="font-bold text-xs"><?= $item_data['itemName'] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between">
                                                                                                                                    <p class="font-bold text-xs">Item Quantity :</p>
                                                                                                                                    <p class="font-bold text-xs"><?= $item_data['item_qty'] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between">
                                                                                                                                    <p class="font-bold text-xs">Rate :</p>
                                                                                                                                    <p class="font-bold text-xs">₹<?= $item_data['item_rate'] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between">
                                                                                                                                    <p class="font-bold text-xs">Tax :</p>
                                                                                                                                    <p class="font-bold text-xs"><?= $item_data['item_tax'] ?></p>
                                                                                                                                </div>
                                                                                                                                <div class="display-flex-space-between">
                                                                                                                                    <p class="font-bold text-xs">Amount :</p>
                                                                                                                                    <p class="font-bold text-xs">₹<?= $item_data['item_amount'] ?></p>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                <?php
                                                                                                                }
                                                                                                                ?>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>

                                                                                                </div>
                                                                                                <!-- /*classic-view div start*/ -->
                                                                                                <div class="tab-pane fade" id="classicViewTab<?= $data['dr_note_id'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                                                                                    <a href="classic-view/invoice-preview-print.php?dr_note_id=<?= base64_encode($data['dr_note_id']) ?>" class="btn btn-primary classic-view-btn float-right" target="_blank">Print</a>
                                                                                                    <div class="card classic-view bg-transparent">
                                                                                                        <?php $templatedebitNoteControllerObj->printDebitNotes($data['dr_note_id']) ?>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <!-- /*classic-view div end*/ -->


                                                                                                <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                                                <div class="tab-pane fade" id="history<?= $data['dr_note_id'] ?>" role="tabpanel" aria-labelledby="history-tab">

                                                                                                    <div class="audit-head-section mb-3 mt-3 ">
                                                                                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> Sonie Kushwaha <span class="font-bold text-normal"> on </span> 08-11-2023 18:11:58</p>
                                                                                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> Sonie Kushwaha <span class="font-bold text-normal"> on </span> 08-11-2023 18:11:58</p>
                                                                                                    </div>
                                                                                                    <hr>
                                                                                                    <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $data['dr_note_id'] ?>">

                                                                                                        <ol class="timeline">

                                                                                                            <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                                <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                                <div class="new-comment font-bold">
                                                                                                                    <p>Loading...
                                                                                                                    </p>
                                                                                                                    <ul class="ml-3 pl-0">
                                                                                                                        <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                                    </ul>
                                                                                                                    <p></p>
                                                                                                                </div>
                                                                                                            </li>
                                                                                                            <p class="mt-0 mb-5 ml-5">Loading...</p>

                                                                                                            <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                                                <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                                                <div class="new-comment font-bold">
                                                                                                                    <p>Loading...
                                                                                                                    </p>
                                                                                                                    <ul class="ml-3 pl-0">
                                                                                                                        <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                                    </ul>
                                                                                                                    <p></p>
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
                                                                <?php
                                                                }
                                                                ?>





                                                            </tbody>
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="10">
                                                                        <!-- Start .pagination -->
                                                                        <?php
                                                                        if ($count > 0 && $count > $GLOBALS['show']) {
                                                                        ?>
                                                                            <div class="pagination align-right">
                                                                                <?php pagination($count, "frm_opts"); ?>
                                                                            </div>

                                                                            <!-- End .pagination -->

                                                                        <?php } ?>
                                                                        <!-- End .pagination -->
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                                <!---------------------------------Table settings Model Start--------------------------------->
                                                <div class="modal" id="myModal2">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Table Column Settings</h4>
                                                                <button type="button" class="close" data-dismiss="modal">×</button>
                                                            </div>
                                                            <form name="table-settings" method="post" action="" onsubmit="return table_settings();">
                                                                <input type="hidden" name="tablename" value="tbl_branch_admin_tablesettings">
                                                                <input type="hidden" name="pageTableName" value="ERP_BRANCH_SALES_ORDER_DELIVERY_PGI">
                                                                <div class="modal-body">
                                                                    <div id="dropdownframe"></div>
                                                                    <div id="main2">
                                                                        <table>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td valign="top" style="width: 165px"><input type="checkbox" checked="checked" name="settingsCheckbox[]" id="settingsCheckbox1" value="1">
                                                                                        PGI No.</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td valign="top" style="width: 165px"><input type="checkbox" checked="checked" name="settingsCheckbox[]" id="settingsCheckbox2" value="2">
                                                                                        Customer PO</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td valign="top" style="width: 165px"><input type="checkbox" checked="checked" name="settingsCheckbox[]" id="settingsCheckbox3" value="3">
                                                                                        Delivery Date</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td valign="top" style="width: 165px"><input type="checkbox" checked="checked" name="settingsCheckbox[]" id="settingsCheckbox3" value="4">
                                                                                        Customer Name</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td valign="top" style="width: 165px"><input type="checkbox" checked="checked" name="settingsCheckbox[]" id="settingsCheckbox3" value="5">
                                                                                        Status</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td valign="top" style="width: 165px"><input type="checkbox" name="settingsCheckbox[]" id="settingsCheckbox3" value="6">
                                                                                        Total Items</td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button type="submit" name="add-table-settings" class="btn btn-success waves-effect waves-light">Save</button>
                                                                    <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!---------------------------------Table Model End--------------------------------->

                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- For Pegination------->
    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo $_REQUEST['pageNo'];
                                                    } ?>">
    </form>
    <!-- End Pegination from------->
<?php
}
require_once("../common/footer.php");
?>
<script>
    $("#profitCenterDropDown").on("change", function() {
        let functionalArea = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 1;
        console.log(functionalArea, 'functionalArea');
        $.ajax({
            type: "POST",
            url: `ajaxs/debit-note/ajax-generate-dn-number.php`,
            data: {
                act: "getVerientExamplecopy",
                functionalArea: functionalArea
            },
            beforeSend: function() {
                // $("#itemsDropDown").html(`Loding...`);
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
            url: `ajaxs/debit-note/ajax-generate-dn-number.php`,
            data: {
                act: "getVerientExamplecopy",
                functionalArea: functionalArea,
                vid: vid
            },
            beforeSend: function() {
                // $("#itemsDropDown").html(`Loding...`);
            },
            success: function(response) {
                let data = JSON.parse(response);
                $(".ivnumberexample").html(data['iv_number_example']);
            }
        });
    });


    $('#addNewJournalForm').on('submit', function() {
        let dtotal = 0;
        $(".dr-amount").each(function() {
            let velu = parseFloat($(this).val());
            if (velu > 0) {
                dtotal += parseFloat(velu);
            }
        });
        let ctotal = 0;
        $(".cr-amount").each(function() {
            let velu = parseFloat($(this).val());
            if (velu > 0) {
                ctotal += parseFloat(velu);
            }
        });

        if (dtotal != ctotal) {
            if (dtotal != ctotal) {
                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `warning`,
                    title: `&nbsp;Debit and credit mismatch!`
                });
                return false;
            }
            return false;
        }
    });

    $(document).on("keyup keydown paste", '.dr-amount', function() {
        let valllAc = $(this).val();
        calculateDrAmount();
    });

    function calculateDrAmount() {
        let sum = 0;
        $(".dr-amount").each(function() {
            let velu = parseFloat($(this).val());
            if (velu > 0) {
                sum += parseFloat(velu);
            }
        });
        sum = sum.toFixed(2);
        $('.debit-total').html(sum);
    }

    $(document).on("keyup keydown paste", '.cr-amount', function() {
        let valllAc = $(this).val();
        calculateCrAmount();
    });

    function calculateCrAmount() {
        let sum = 0;
        $(".cr-amount").each(function() {
            let velu = parseFloat($(this).val());
            if (velu > 0) {
                sum += parseFloat(velu);
            }
        });
        sum = sum.toFixed(2);
        $('.credit-total').html(sum);
    }

    $(document).on("click", ".delete_new_bullet_point", function() {
        $(this).parent().parent().remove();
        calculateDrAmount();
        calculateCrAmount();
    });

    function srch_frm() {
        if ($('#form_date_s').val().trim() != '' && $('#to_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
            //$("#phone_r_err").html("Your Phone Number");
            alert("Enter To Date");
            $('#to_date_s').focus();
            return false;
        }
        if ($('#to_date_s').val().trim() != '' && $('#form_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
            //$("#phone_r_err").html("Your Phone Number");
            alert("Enter From Date");
            $('#form_date_s').focus();
            return false;
        }

    }

    function table_settings() {
        var favorite = [];
        $.each($("input[name='settingsCheckbox[]']:checked"), function() {
            favorite.push($(this).val());
        });
        var check = favorite.length;
        if (check < 5) {
            alert("Please Check Atlast 5");
            return false;
        }

    }


    //     $(document).ready(function() {



    // //         $('.select2')
    // //             .select2()
    // //             .on('select2:open', () => {
    // //                 $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal3">
    // //     Add New
    // //   </a></div>`);
    // //             });
    // //         //**************************************************************
    // //         $('.select4')
    // //             .select4()
    // //             .on('select4:open', () => {
    // //                 $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
    // //     Add New
    // //   </a></div>`);
    // //             });
    // //     });
</script>
<script>
    function leaveInput(el) {
        if (el.value.length > 0) {
            if (!el.classList.contains('active')) {
                el.classList.add('active');
            }
        } else {
            if (el.classList.contains('active')) {
                el.classList.remove('active');
            }
        }
    }

    var inputs = document.getElementsByClassName("m-input");
    for (var i = 0; i < inputs.length; i++) {
        var el = inputs[i];
        el.addEventListener("blur", function() {
            leaveInput(this);
        });
    }

    // *** autocomplite select *** //
    wow = new WOW({
        boxClass: 'wow', // default
        animateClass: 'animated', // default
        offset: 0, // default
        mobile: true, // default
        live: true // default
    })
    wow.init();


    $(document).on('click', '.addItemBtn', function() {
        let rand = Math.ceil(Math.random() * 100000);
        var newRow = ` <tr>
                                                        <td style="width:20%">
                                                            <select name="item[${rand}][item_id]" class="form-control item_select item_select_${rand}" data-attr="${rand}">
                                                                <option>SELECT ITEM</option>
                                                                <?php
                                                                $item_sql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id` = $company_id", true)['data'];
                                                                foreach ($item_sql as $item) {


                                                                ?>



                                                                    <option value="<?= $item['itemId'] . '|' . $item['itemCode'] . '|' . $item['itemName'] . '|' . $item['goodsType'] ?>"><?= $item['itemName'] . '[' .  $item['itemCode']  . ']' ?></option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                            <input type="hidden" value="" name="item[${rand}][item_code]">
                                                        </td>
                                                        <td>
                                                            <select name="item[${rand}][account]" class="form-control gl_select account_${rand}">

                                                                <option>Select Account</option>
                                                                <?php

                                                                $gl_sql = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND glStType='account'  AND `status`!='deleted' ORDER BY gl_code", true);
                                                                foreach ($gl_sql['data'] as $gl) {
                                                                ?>
                                                                    <option value="<?= $gl['id'] ?>"><?= $gl["gl_code"] . "|" . $gl["gl_label"] ?></option>
                                                                <?php
                                                                }
                                                                ?>

                                                            </select>
                                                        </td>
                                                        <td class="text-right">
                                                            <div>
                                                                <span class="custom_batch_${rand} d-none">
                                                                <input type="hidden" name="item[${rand}][stockQty]" class="form-control checkQty" id="checkQty_${rand}" value="">

                                                                <!-- Button to Open the Modal -->
                                                                <div class="qty-modal py-2">
                                                                    <p class="font-bold text-center checkQtySpan" id="checkQtySpan_${rand}"></p>
                                                                    <hr class="my-2 w-50 mx-auto">
                                                                    <div class="text-xs d-flex align-items-center gap-2 justify-content-center">
                                                                        <p class="itemSellType" id="itemSellType_${rand}">CUSTOM</p>
                                                                        <ion-icon name="create-outline" class="stockBtn" id="stockBtn_${rand}" data-bs-toggle="modal" data-bs-target="#stockSetup${rand}" style="cursor: pointer;"></ion-icon>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" class="itemSellTypeHidden" id="itemSellTypeHidden_${rand}" name="item[${rand}][itemSellType]" value="CUSTOM">

                                                                <!-- The Modal -->
                                                                <div class="modal fade stock-setup-modal" id="stockSetup${rand}">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">

                                                                            <!-- Modal Header -->
                                                                            <div class="modal-header" style="background: #003060; color: #fff;">
                                                                                <h4 class="modal-title text-sm text-white">Stock Setup (CUSTOM)</h4>
                                                                                <p class="text-xs my-2 ml-5">Total Picked Qty :
                                                                                    <span class="font-bold itemSelectTotalQty" id="itemSelectTotalQty_${rand}">0</span>
                                                                                </p>

                                                                            </div>

                                                                            <!-- Modal body -->
                                                                            <div class="modal-body">

                                                                                <!-- start warehouse accordion -->
                                                                                <div class="modal-select-type my-3">
                                                                                    <div class="type type-three">
                                                                                        <input type="radio" name="item[${rand}][itemreleasetype]" class="itemreleasetypeclass custom" data-rdcode="${rand}" value="CUSTOM" id="custom_${rand}" checked>
                                                                                        <label for="custom" class="text-xs mb-0 text-muted">Custom</label>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="customitemreleaseDiv${rand}">

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
                                                                <input class="form-control" type="hidden" name="item[${rand}][sumOfBatches]" value="">
                                                                </span>

                                                                <input type="number" name="item[${rand}][qty]" class="form-control itemQty" id="itemQty_${rand}" value="">
                                                            </div>                                                        
                                                        </td>
                                                        <td class="text-right"><input type="number" name="item[${rand}][rate]" class="form-control price" id="price_${rand}" value=""></td>
                                                        <td><p class="d-flex gap-2"><input type="number" class="form-control tax" name="item[${rand}][tax]" id="tax_${rand}" value=""><span class="percent-position">%</span>
                                                        <input type="hidden" class="form-control tax_amount" name="item[${rand}][tax_amount]" id="tax_amount_${rand}">
                                                        </p>
                                                        </td>

                                                        <td class="text-right amount" id="amount_${rand}">
                                                            <input type="hidden" value="" id="amountHidden_${rand}" name="item[${rand}][amount]">
                                                        </td>
                                                        <td>
                                                        <div class="btns-grp d-flex gap-2">
                                                            <a style="cursor: pointer" class="btn btn-danger add-btn-minus-bill">
                                                             <i class="fa fa-minus"></i>
                                                            </a>
                                                        </div>
                                                        </td>
                                                    </tr>`;

        $('.inv_items').append(newRow);

        $(`.item_select_${rand}`).select2();

        $(`.account_${rand}`).select2();
        console.log('ap');
    });

    // $(document).on('click', '.removeItemBtn', function() {
    //     $(this).closest('tr').remove();
    // });
</script>





<script>
    // function addMultiRow(id) {

    //     let rand = Math.ceil(Math.random() * 100000);
    //     var newRow = ` <tr>
    //                         <td style="width:20%">
    //                             <select name="item[${rand}][item_id]" id="item_select" class="form-control item_select_${rand}">
    //                                 <option>SELECT ITEM</option>
    //                                 <?php
                                        //                                 $item_sql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id` = $company_id", true)['data'];
                                        //                                 foreach ($item_sql as $item) {
                                        //                                 
                                        ?>
    //                                     <option value="<?= $item['itemId'] . '|' . $item['itemCode'] . '|' . $item['itemName'] . '|' . $item['goodsType'] ?>"><?= $item['itemName'] . '[' .  $item['itemCode']  . ']' ?></option>
    //                                 <?php
                                        //                                 }
                                        //                                 
                                        ?>
    //                             </select>
    //                             <input type="hidden" value="" name="item[${rand}][item_code]">

    //                         </td>

    //                         <td class="text-right"><input type="number" name="item[${rand}][qty]" class="form-control itemQty" id="itemQty_${rand}" value="<?= $data['qty'] ?>"></td>
    //                         <td class="text-right"><input type="number" name="item[${rand}][rate]" class="form-control price" id="price_${rand}" value="<?= $data['unitPrice'] ?>"></td>
    //                         <td class="d-flex gap-2"><input type="number" class="form-control tax" name="item[${rand}][tax]" id="tax_${rand}" value="<?= $data['tax'] ?>"><span class="d-inline-block">%</span>
    //                         <input type="hidden" class="form-control tax_amount" name="item[${rand}][tax_amount]" id="tax_amount_${rand}">
    //                         </td>

    //                         <td class="text-right amount" id="amount_${rand}"><?= $amount ?>
    //                             <input type="hidden" value="<?= $amount ?>" id="amountHidden_${rand}" name="item[${rand}][amount]">
    //                         </td>
    //                         <td>
    //                         <div class="add-btn-minus-bill justify-content-end">
    //                         <a style="cursor: pointer" class="btn btn-danger">
    //                                             <i class="fa fa-minus"></i>
    //                                         </a>
    //                                         </div>
    //                         </td>
    //                     </tr>`;

    //     $(`tbody.add-row-bill_${id}`).append(newRow);


    //     $(`.item_select_${rand}`).select2();

    // }

    // //   $(document).on('click', '.addItemBtn', function() {
    // //    // let id = $(this).data('attr');
    // //         let rand = Math.ceil(Math.random() * 100000);
    // //         var newRow = ` <tr>
    // //                                                         <td style="width:20%">
    // //                                                             <select name="" id="item_select" class="form-control">
    // //                                                                 <option>SELECT ITEM</option>
    // //                                                                 <?php
                                                                            //                                                                     //                                                                 $item_sql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id` = $company_id", true)['data'];
                                                                            //                                                                     //                                                                 foreach ($item_sql as $item) {


                                                                            //                                                                     //                                                                 
                                                                            //                                                                     
                                                                            ?>



    // //                                                                     <option value="<?= $item['itemId'] ?>"><?= $item['itemName'] . '[' .  $item['itemCode']  . ']' ?></option>
    // //                                                                 <?php
                                                                            //                                                                     //                                                                 }
                                                                            //                                                                     //                                                                 
                                                                            //                                                                     
                                                                            ?>
    // //                                                             </select>
    // //                                                             <input type="hidden" value="" name="item[${rand}][item_code]">
    // //                                                             <input type="hidden" value="" name="item[${rand}][item_id]">
    // //                                                         </td>

    // //                                                         <td class="text-right"><input type="number" name="item[${rand}][qty]" class="form-control itemQty" id="itemQty_${rand}" value="<?= $data['qty'] ?>"></td>
    // //                                                         <td class="text-right"><input type="number" name="item[${rand}][rate]" class="form-control price" id="price_${rand}" value="<?= $data['unitPrice'] ?>"></td>
    // //                                                         <td class="d-flex gap-2"><input type="number" class="form-control tax" name="item[${rand}][tax]" id="tax_${rand}" value="<?= $data['tax'] ?>"><span class="d-inline-block">%</span>
    // //                                                         <input type="hidden" class="form-control tax_amount" name="tax_amount[${rand}][tax]" id="tax_amount_${rand}">
    // //                                                         </td>

    // //                                                         <td class="text-right amount" id="amount_${rand}"><?= $amount ?>
    // //                                                             <input type="hidden" value="<?= $amount ?>" id="amountHidden_${rand}" name="item[${rand}][amount]">
    // //                                                         </td>
    // //                                                         <td>
    // //                                                         <div class="add-btn-minus-bill justify-content-end">
    // //                                                         <a style="cursor: pointer" class="btn btn-danger">
    // //                                                                             <i class="fa fa-minus"></i>
    // //                                                                         </a>
    // //                                                                         </div>
    // //                                                         </td>
    // //                                                     </tr>`;

    // //         $('tbody.add-row-bill').append(newRow);
    // //     });

    // // $(document).on('click', '.removeItemBtn', function() {
    // //     $(this).closest('tr').remove();
    // // });

    $(document).on("click", ".add-btn-minus-bill", function() {
        $(this).parent().parent().parent().remove();
        //     var rowCount = $('.items_row').length;
        //     if (rowCount > 1) {
        //     $(this).parent().parent().remove();
        //     console.log(rowCount);
        // } else {
        //     alert("At least one row is required.");
        // }
        calculateAllItemsGrandAmount();
        calculateAllItemTax();
    });





    $(document).on("change", "#vendor_customer", function() {
        //  alert(1);
        let value = $("#vendor_customer").find(':selected').val();
        //   alert(value);
        var splitValues = value.split('|');

        let id = splitValues[0];



        let dataAttrVal = splitValues[1];

        //  alert(dataAttrVal);
        $.ajax({

            type: "GET",

            url: `ajaxs/debit-note/ajax-address-details.php`,
            data: {
                dataAttrVal: dataAttrVal,
                id: id,
                act: "address",
            },
            beforeSend: function() {

                $("#supplyAddress").html(`<option value="">Loding...</option>`);
                $("#destinationAddress").html(`<option value="">Loding...</option>`);
                $("#bill").html(`<option value="">Loding...</option>`);

            },

            success: function(response) {
                // alert(1);
                console.log(response);

                var obj = JSON.parse(response);

                //alert(obj['supply_address']);
                $("#supplyAddress").html(obj['supply_address']);
                $("#destinationAddress").html(obj['destination_address']);
                $("#bill").html(obj['invoice']);

                $("#bill_to_address").html(obj['bill_address']);
                $("#ship_to_address").html(obj['shipping_address']);
                $("#billToInput").val(obj['bill_address_id']);
                $("#shipToInput").val(obj['shipping_address_id']);


            }
        });


    });

    $(document).on("change", "#bill", function() {



        let val = $("#bill").find(':selected').val();
        var splitValues = val.split('|');

        let bill_id = splitValues[0];
        let compInvoiceType = splitValues[3];

        $('#compInvoiceType').val(compInvoiceType);
        var partyCreditDate = $('#partyCreditDate').val();
        // alert(id);
        let attr = splitValues[1];
        // alert(attr);


        $.ajax({

            type: "GET",

            url: `ajaxs/debit-note/ajax-bill-details.php`,
            data: {

                bill_id: bill_id,
                attr: attr,
                act: "bill",
                partyCreditDate: partyCreditDate,
            },
            beforeSend: function() {

                // $("#supplyAddress").html(`<option value="">Loding...</option>`);
                // $("#destinationAddress").html(`<option value="">Loding...</option>`);
                // $("#bill").html(`<option value="">Loding...</option>`);

            },

            success: function(response) {
                // alert(1);
                //  alert(response);
                $("#inv_items").html(response);
                calculateAllItemTax();





            }
        });


        $.ajax({

            type: "GET",

            url: `ajaxs/debit-note/ajax-bill-address-details.php`,
            data: {

                bill_id: bill_id,
                attr: attr,
                act: "address",
            },
            beforeSend: function() {

                // $("#supplyAddress").html(`<option value="">Loding...</option>`);
                // $("#destinationAddress").html(`<option value="">Loding...</option>`);
                // $("#bill").html(`<option value="">Loding...</option>`);

            },

            success: function(response) {
                //alert(1);
                //     console.log(response);
                //   alert(response);
                //billToInput
                //$("#inv_items").html(response);

                var obj = JSON.parse(response);
                console.log(obj);
                $("#bill_to_address").html(obj['bill_address']);
                $("#ship_to_address").html(obj['shipping_address']);
                $("#billToInput").val(obj['bill_address_id']);
                $("#shipToInput").val(obj['shipping_address_id']);


                $("#bill_type").html(obj['bill_type']);





            }
        });




    });


    function calculateAllItemTax() {

        totalTax = 0;
        $(".tax_amount").each(function() {
            //  alert(1);
            let itemTotalTax = parseFloat($(this).val());

            totalTax += itemTotalTax > 0 ? itemTotalTax : 0;

        });

        // alert(totalTax);

        let source = $('#supplyAddress').val();
        let dest = $('#destinationAddress').val();
        // alert(source);
        // alert(dest);
        if (source == dest) {
            // alert(1);
            tax_each = totalTax / 2;
            $('#sgst_span').html(tax_each);
            $('#cgst_span').html(tax_each);
            $('#igst_span').html(0);

            $('#sgst').val(tax_each);
            $('#cgst').val(tax_each);
            $('#igst').val(0);


        } else {
            // alert(0);
            $('#sgst_span').html(0);
            $('#cgst_span').html(0);
            $('#igst_span').html(totalTax);

            $('#sgst').val(0);
            $('#cgst').val(0);
            $('#igst').val(totalTax);

        }

    }

    function calculateAllItemsGrandAmount() {
        // alert(1);
        let grandTotalBeforeDiscount = 0;
        let grandTotal = 0;
        let num = 0;
        let round_value_amount = 0;

        let round_value = $("#round_value").val();
        let round_val_operator = $('#round_sign').val();



        $(".amount").each(function() {
            let itemTotalPrice = parseFloat($(this).html());

            grandTotalBeforeDiscount += itemTotalPrice > 0 ? itemTotalPrice : 0;




            // num = convertNumberToWords(grandTotalBeforeDiscount);
            // console.log(num);
        });

        //  round_value_amount = round_value ;
        if (round_val_operator == '+') {
            grandTotal = grandTotalBeforeDiscount + Number(round_value);
        } else {
            grandTotal = grandTotalBeforeDiscount - Number(round_value);
        }
        //  alert(grandTotal);

        $("#grandTotal").html(grandTotal.toFixed(2));
        $("#grandTotalHidden").val(grandTotal.toFixed(2));
        // $("#discountAmount").html(discount_amount.toFixed(2));
        // $("#discountAmountHidden").val(discount_amount.toFixed(2));
        $("#subTotalHidden").val(grandTotalBeforeDiscount.toFixed(2));
        $("#subTotal").html(grandTotalBeforeDiscount.toFixed(2));

        // $("#grandTotalAmountInput").val(grandTotal.toFixed(2));
    }

    calculateAllItemsGrandAmount();
    calculateAllItemTax();


    function calculateOneItemRowAmount(rowNum) {
        let qty = parseFloat($(`#itemQty_${rowNum}`).val());


        qty = qty > 0 ? qty : 1;

        let unitPrice = parseFloat($(`#price_${rowNum}`).val());

        unitPrice = unitPrice > 0 ? unitPrice : 0;

        let tax = parseFloat($(`#tax_${rowNum}`).val());

        tax = tax > 0 ? tax : 0;
        //alert(tax);
        let tax_amount = (tax / 100 * unitPrice) * qty;

        let totalPrice = (unitPrice * qty) + tax_amount;

        // alert(totalPrice);

        $(`#tax_amount_${rowNum}`).val(tax_amount.toFixed(2));
        $(`#amount_${rowNum}`).html(totalPrice.toFixed(2));
        $(`#amountHidden_${rowNum}`).val(totalPrice.toFixed(2));

        calculateAllItemsGrandAmount();
        calculateAllItemTax();
    }


    $(document).on("keyup", ".itemQty", function() {
        // alert(1);

        let rowNum = ($(this).attr("id")).split("_")[1];
        // alert(rowNum);
        calculateOneItemRowAmount(rowNum);


    });


    $(document).on("keyup", ".price", function() {
        // alert(1);

        let rowNum = ($(this).attr("id")).split("_")[1];
        // alert(rowNum);
        calculateOneItemRowAmount(rowNum);


    });

    $(document).on("keyup", ".tax", function() {
        // alert(1);

        let rowNum = ($(this).attr("id")).split("_")[1];
        // alert(rowNum);
        calculateOneItemRowAmount(rowNum);


    });


    $(document).on("keyup", "#round_value", function() {

        calculateAllItemsGrandAmount();

    });

    $(document).on("change", "#round_sign", function() {

        calculateAllItemsGrandAmount();

    });
</script>


<script>
    $(document).ready(function() {
        $('input[name="select_customer_vendor"]').change(function() {
            // Reset all inputs

            // alert($(this).val());
            let val = $(this).val();

            $('.inv_items').html('');
            $('#bill').html('');
            $('#supplyAddress').val('');
            $('#destinationAddress').val('');
            // $("#drnote :input").not("#select_customer_vendor , #dr_code", "#addNewCreditNoteFormSubmitBtn").each(function() {
            //     // Reset the value to an empty string or default value
            //     $(this).val('');
            // });

            if (val == 'Customer') {
                $(".compInvoiceTypeDiv").show();
                $('#compInvoiceType').prop("required", true);
            } else {
                $(".compInvoiceTypeDiv").hide();
                $('#compInvoiceType').prop("required", false);
            }


            // Preserve the value of the field to be preserved
            // var preservedValue = $("#select_customer_vendor").val();

            // var preservedValue2 = $("#dr_code").val();

            // var preservedValue3 = $("#addNewCreditNoteFormSubmitBtn").val();

            // Restore the preserved value
            // $("#select_customer_vendor").val(preservedValue);
            // $("#dr_code").val(preservedValue2);
            // $("#addNewCreditNoteFormSubmitBtn").val(preservedValue3);


            $('#vendor_customer').val('').trigger('change');



        });
    });


    $('.item_select').select2({

    });

    $('.gl_select').select2({

    });
    $('#vendor_customer').select2({

        placeholder: 'Select Party',
        ajax: {
            url: `ajaxs/debit-note/credit-vendor-customer-details.php`,
            dataType: 'json',
            delay: 50,
            data: function(params) {
                return {
                    searchTerm: params.term, // search term
                    listtype: $('input[name="select_customer_vendor"]:checked').val() // search term
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
    });

    $('#bill').select2({

        placeholder: 'Select Document',
        ajax: {
            url: `ajaxs/debit-note/ajax-bill-list.php`,
            dataType: 'json',
            delay: 50,
            data: function(params) {
                return {
                    searchTerm: params.term, // search term
                    listtype: $('#vendor_customer').find(':selected').val() // search term
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
    });



    $(document).ready(function() {
        $('#round_off_checkbox').on('click', function() {

            var isChecked = $('#round_off_checkbox').prop('checked');
            if (isChecked) {
                $('#round_off_hide').show();
            } else {
                $('#round_value').val('0');
                calculateAllItemsGrandAmount();
                $('#round_off_hide').hide();
            }
        })
    });



    $(document).on('change', '.item_select', function() {
        let val = $(this).val();
        console.log(val);
        let attr = $(this).data('attr');

        var itemval = $(this).val().split("|");
        let itemType = itemval[3];
        console.log(itemType);

        // alert(attr);
        $.ajax({

            type: "GET",

            url: `ajaxs/debit-note/ajax-gl.php`,
            data: {

                val: val,
                attr: attr,
                act: "gl",
            },
            beforeSend: function() {},

            success: function(response) {

                var responsearr = JSON.parse(response);

                // //console.log(response);

                $(`.account_${attr}`).html(responsearr['glhtml']);
                $(`#tax_${attr}`).val(responsearr['taxPercentage']);
                if (val != '' && val != 'SELECT ITEM' && val != 0 && itemType != 5 && itemType != 7) {
                    $(`.custom_batch_${attr}`).removeClass('d-none');
                    $(`#itemQty_${attr}`).prop("readonly", true);
                } else {
                    $(`.custom_batch_${attr}`).addClass('d-none');
                    $(`#itemQty_${attr}`).prop("readonly", false);
                }

            }

        });

        var invoicedate = $('#partyCreditDate').val();
        var rowData = {};
        let flag = 0;
        let itemId = itemval[0];
        let rowId = $(this).data('attr');
        if (itemType != 5) {
            flag++;
        }
        rowData[rowId] = itemId;

        $.ajax({
            type: "GET",
            url: `ajaxs/debit-note/ajax-items-stock-list.php`,
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
                var trimmedResponse = $.trim(response);

                if (trimmedResponse === '') {
                    $(`#itemQty_${attr}`).prop("readonly", false);
                } else {
                    // $(`.customitemreleaseDiv${rowId}`).hide();
                    $(`.customitemreleaseDiv${rowId}`).html(response);
                }
            }
        });

        // StringRowData = JSON.stringify(rowData);
        // if (flag > 0) {
        //     Swal.fire({
        //         icon: `warning`,
        //         title: `Note`,
        //         text: `Available stock has been recalculated`,
        //         // showCancelButton: true,
        //         // confirmButtonColor: '#3085d6',
        //         // cancelButtonColor: '#d33',
        //         // confirmButtonText: 'Confirm'
        //     });


        //     $.ajax({
        //         type: "POST",
        //         url: `ajaxs/debit-note/ajax-items-stock-check.php`,
        //         data: {
        //             act: "itemStockCheck",
        //             invoicedate: invoicedate,
        //             rowData: StringRowData
        //         },
        //         beforeSend: function() {
        //             $(".tableDataBody").html(`<option value="">Loding...</option>`);
        //         },
        //         success: function(response) {
        //             let data = JSON.parse(response);
        //             let itemData = data.data;
        //             console.log(data);
        //             if (data.status === "success") {
        //                 for (let key in itemData) {
        //                     if (itemData.hasOwnProperty(key)) {

        //                         $(`#itemQty_${key}`).val(0);
        //                         $(`#checkQty_${key}`).val(itemData[key]);
        //                         $(`#checkQtySpan_${key}`).html(itemData[key]);
        //                         $(`#fifo_${key}`).prop('checked', true);
        //                         $(`#itemSellType_${key}`).html('FIFO');
        //                         $(`.enterQty`).val('');
        //                     }
        //                 }
        //             }
        //         }
        //     });
        // }



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
        if ($('input[name="select_customer_vendor"]:checked').val() == 'Customer') {
            totalquentity(rdcodeSt);
            $('.batchCheckbox' + rdBatch).prop('checked', true);
        } else {
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
        calculateOneItemRowAmount(rdcode);
    }
    // ***********************************************
    // ***********************************************

    // partyCredit date *****************************************
    $("#partyCreditDate").on("change", function(e) {
        // dynamic value
        let url = window.location.search;
        let param = url.split("=")[0];

        var invoicedate = $(this).val();
        var rowData = {};
        let flag = 0;
        $(".itemRow").each(function() {
            let itemType = $(this).attr("goodsType");
            if (itemType != 5) {
                flag++;
            }
            let rowId = $(this).attr("id").split("_")[2];
            let itemId = $(this).attr("id").split("_")[1];
            rowData[rowId] = itemId;

            $.ajax({
                type: "GET",
                url: `ajaxs/debit-note/ajax-items-stock-list.php`,
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
                    url: `ajaxs/debit-note/ajax-items-stock-check.php`,
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

    // handleContact details field
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Note',
                        text: data.message
                    });
                    $("#configEmail").val("");
                    $("#configPhone").val("");
                    $("#configName").val("");
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
    // handle contact details Save function
    $(document).on("click", "#handleConfigSave", function() {
        handleConfigSave();
    })

    $(document).ready(function() {
        $(document).on("click", '#config', function() {
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-config-invoice.php`,
                data: {
                    act: "getContact",

                },
                success: function(response) {
                    let res = JSON.parse(response);
                    // console.log(res);
                    let responseObj = res.data;
                    $.each(responseObj, function(index, item) {
                        let contact = item.name + '||' + item.email + '||' + item.phone;
                        // console.log(contact);
                        $('#config').append(`<option value='${contact}'>${contact}</option>`);
                    });
                }
            });

        });
    });
</script>
<script src="<?= BASE_URL; ?>public/validations/debitNotesValidation.js"></script>