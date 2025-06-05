<?php
require_once("../../app/v1/connection-branch-admin.php");

// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");

require_once("../../app/v1/functions/admin/func-company.php");


require_once("../../app/v1/functions/branch/func-credit-note.php");
// require_once("../../app/v1/functions/common/templates/template-creditnote.controller.php");

$company_data = getCompanyDataDetails($company_id);
$gl_account_length = $company_data['data']['gl_account_length'];
$countrycode = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
$components = getLebels($countrycode)['data'];
$components = json_decode($components, true);
$lable = (getLebels($companyCountry)['data']);
$lable = json_decode($lable, true);
$tdslable = ($lable['source_taxation']);
$tcslable = $lable['transaction_taxation'];

// $templateCreditNoteControllerObj = new TemplateCreditNoteController();

// if (isset($_POST["createdata"])) {
//     // console($_POST);
//     $addNewObj = createCreditNote($_POST);
//     // console($addNewObj);
//     swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
// }

// date checker
$check_var_sql = queryGet("SELECT * FROM `" . ERP_MONTH_VARIANT . "` WHERE `month_variant_id`=$admin_variant");
$check_var_data = $check_var_sql['data'];
// console($check_var_sql);
$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];

if (isset($_POST["add-table-settings"])) {
    // console($_POST);
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    // console($editDataObj);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if ($_POST['action'] == "cn") {
    $addCredit = credit_note_add_by_rule_book($_POST + $_FILES);
    if ($addCredit['status'] == "success") {
        swalAlert($addCredit["status"], $addCredit['credit_note_no'], $addCredit["message"], $_SERVER['PHP_SELF']);
    } else {
        swalToast($addCredit["status"], $addCredit["message"]);
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

    ion-icon {
        color: #000;
        font-size: 10px;
        font-weight: 700;
    }

    .credit-note-basic {
        border: 1px solid #aeaeae;
        padding: 10px;
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
    <div class="content-wrapper notes-credit-debit is-credit-notes">
        <section class="content">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Credit Note List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Credit Note</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>

                <form method="POST" id="drnote" name="drnote" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="cn">
                    <section class="credit-notes">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card credit-note-form-card">
                                    <div class="card-header p-2">
                                        <div class="head p-2">
                                            <h4>Create New Credit Note</h4>
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
                                                    <?php if ($companyCountry == '103') { ?>
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
                                                    <?php } ?>
                                                    <?php
                                                    if ($components['compliance_invoice'] == 'true') { ?>
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
                                                    <?php } ?>
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

                                                    <!--                                                     
                                                    <div class="col-lg-4 col-md-12 col-sm-12">
                                                        <div class="form-inline my-3">
                                                            <p class="fw-normal">Bill Type : </p>
                                                            &nbsp;
                                                            <p id="bill_type"></p>
                                                        </div>
                                                    </div> -->

                                                    <div class="col-lg-8 col-md-12 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Credit Note Varient <span class="text-danger">*</span></label>
                                                            <select name="iv_varient" class="form-control" id="iv_varient" required>
                                                                <?php
                                                                $iv_varient = queryGet("SELECT * FROM `erp_cn_varient` WHERE company_id=$company_id AND status='active' ORDER BY id ASC", true);
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
                                                            <input type="date" value="<?= $min ?>" min="<?= $min ?>" max="<?= $max ?>" name="posting_date" class="form-control" id='partyCreditDate'>
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
                                                            <label for="" class="">Contacts</label>
                                                            <button type="button" data-toggle="modal" data-target="#configModal" style="border: none; font-size: 10px; padding: 0px 5px; margin-bottom: 5px;" class="btn btn-sm btn-primary">
                                                                Add New
                                                            </button>
                                                        </div>
                                                        <select name="companyConfigId" class="form-control" id="config">
                                                            <option value="">Select One</option>
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
                                            <input type="hidden" name="gstdetails">

                                            <table class="table table-hover table-nowrap mb-0 credit-notes-table" id="inv_items">
                                                <thead>
                                                    <tr>
                                                        <th>Item Details</th>
                                                        <th>Account</th>
                                                        <th>Quantity</th>
                                                        <th>Rate</th>
                                                        <th id="customerdis2">Discount(%)</th>
                                                        <th id="customertds2">TDS(%)</th>
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
                                                    <tr class="items_row" id="<?= $rand ?>">
                                                        <input type="hidden" name="gstdetails">
                                                        <td style="width:20%">
                                                            <select name="item[<?= $rand ?>][item_id]" class="form-control item_select item_select_<?= $rand ?>" data-attr="<?= $rand ?>">
                                                                <option value='0'>SELECT ITEM</option>
                                                                <?php

                                                                $item_sql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id` = $company_id", true)['data'];
                                                                foreach ($item_sql as $item) {

                                                                ?>

                                                                    <option value="<?= $item['itemId'] . '_' . $item['goodsType'] ?>"><?= $item['itemName'] . '[' .  $item['itemCode']  . ']' ?></option>
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
                                                                    <input type="hidden" name="item[<?= $rand ?>][stockQty]" class="form-control checkQty" id="checkQty_<?= $rand ?>" value="<?= decimalQuantityPreview($sumOfBatches); ?>">

                                                                    <!-- Button to Open the Modal -->
                                                                    <div class="qty-modal py-2">
                                                                        <p class="font-bold text-center checkQtySpan inputQuantityClass" id="checkQtySpan_<?= $rand ?>"><?= decimalQuantityPreview($sumOfBatches); ?></p>
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
                                                                    <input class="form-control" type="hidden" id="checkQtyVal_<?= $rand ?>" name="item[<?= $rand ?>][sumOfBatches]" value="<?= decimalQuantityPreview($sumOfBatches) ?>">
                                                                </span>

                                                                <input type="number" name="item[<?= $rand ?>][qty]" class="form-control itemQty inputQuantityClass" id="itemQty_<?= $rand ?>" value="<?= decimalQuantityPreview($data['qty']) ?>">
                                                            </div>
                                                        </td>
                                                        <td class="text-right"><input type="number" name="item[<?= $rand ?>][rate]" class="form-control price inputAmountClass" id="price_<?= $rand ?>" value="<?= decimalValuePreview($data['unitPrice']) ?>"></td>



                                                        <td class="text-right customerdis"><input type="number" name="item[<?= $rand ?>][dis]" class="form-control itemDis inputAmountClass" id="itemDis_<?= $rand ?>" value="">
                                                        </td>
                                                        <td class="text-right  customertds"><input type="number" name="item[<?= $rand ?>][total_tds_per]" class="form-control itemTds inputAmountClass" id="total_tds_per_<?= $rand ?>" value="0">
                                                        </td>
                                                        <input type="hidden" name="item[<?= $rand ?>][disval]" class="form-control itemDisVal" id="itemDisVal_<?= $rand ?>" value="0">
                                                        <input type="hidden" name="item[<?= $rand ?>][tdsval]" class="form-control itemTdsVal" id="itemTdsVal_<?= $rand ?>" value="0">


                                                        <td>
                                                            <div class="tax-amount d-flex gap-2">
                                                                <input type="number" class="form-control tax inputAmountClass" name="item[<?= $rand ?>][tax]" id="tax_<?= $rand ?>" value="<?= decimalQuantityPreview($data['tax']) ?>">
                                                                <span class="percent-position">%</span>
                                                                <input type="hidden" class="form-control tax_amount" name="item[<?= $rand ?>][tax_amount]" id="tax_amount_<?= $rand ?>">
                                                            </div>
                                                        </td>
                                                        <td class="text-right amount" id="amount_<?= $rand ?>"><?= decimalValuePreview($amount) ?>
                                                            <input type="hidden" value="<?= decimalValuePreview($amount) ?>" id="amountHidden_<?= $rand ?>" name="item[<?= $rand ?>][amount]">
                                                        </td>
                                                        <td>
                                                            <a style="cursor: pointer" class="btn btn-danger add-btn-minus-bill">
                                                                <i class="fa fa-minus"></i>
                                                            </a>
                                                        </td>
                                                    </tr>

                                                </tbody>

                                                <tr id="disrow">
                                                    <td colspan="5" class="text-right">Total Discount</td>
                                                    <td colspan="2" class="text-right"><span style="" id="total_discount_c">0.00</span>
                                                        <!-- <input type="number" step="any" id="total_discount1" style="float: right;" class="form-control text-right" name="total_discount" value=""> -->
                                                        <input type="hidden" step="any" id="total_discount" style="float: right;" class="form-control text-right" name="total_discount" value="">


                                                    </td>

                                                </tr>
                                                <tr id="subtotalTr">
                                                    <td colspan="5" class="text-right">Sub Total</td>
                                                    <td colspan="2" class="text-right" id="subTotal">
                                                        <input type="hidden" id="subTotal" name="subTotal" value="">

                                                    </td>
                                                </tr>

                                                <tr id="tdsrow" style="display: none;">
                                                    <td colspan="5" class="text-right">Total <?= $tdslable ?></td>
                                                    <td colspan="2" class="text-right" id="total_tds_c">
                                                        <input type="number" step="any" style="float: right;" id="total_tds" readonly class="form-control text-right" name="total_tds" value="0.0">

                                                    </td>
                                                </tr>
                                                <tr id="subtotalTr">
                                                    <td colspan="5" class="text-right">Total <?= $tcslable ?></td>
                                                    <td colspan="2" class="text-right" id="total_tcs_c">
                                                        <input t type="number" step="any" id="total_tcs" style="float: right;" class="form-control text-right" name="total_tcs" value="0.0">

                                                    </td>
                                                </tr>

                                                <tr id="subtotalTr">
                                                    <td colspan="5" class="text-right">Round Off</td>
                                                    <td colspan="2" class="text-right" id="total_round_off_c">
                                                        <div class="adjust-currency d-flex gap-2 " style="float: right;">
                                                            <select id="round_sign" name="round_sign" class="form-control text-center">
                                                                <option value="+">+</option>
                                                                <option value="-">-</option>
                                                            </select>
                                                            <input type="number" name="round_value" step="any" id="round_value" value="" class="form-control text-right">
                                                        </div>

                                                    </td>


                                                </tr>

                                                <tr>
                                                    <td colspan="5" class="text-right font-bold"> Total</td>
                                                    <td colspan="2" class="text-right font-bold" id="grandTotal">


                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> <input type="hidden" name="gstdetails" value=""></td>
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
                        <button type="submit" id="addNewCreditNoteFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" id="cnbtn" value="add_post">Submit</button>
                        <div id="creditNoteMessage"></div>
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
                        <h5 class="modal-title" id="configModalLabel">Contact Details</h5>
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
    $url = BRANCH_URL . 'location/manage-credit-notes.php';
?>
    <script>
        window.location.href = "<?php echo $url; ?>";
    </script>
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
            url: `ajaxs/credit-note/ajax-generate-cn-number.php`,
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

    $(document).on("input keyup paste blur", ".inputQuantityClass", function() {
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

    $(document).on("input keyup paste blur", ".inputAmountClass", function() {
        let val = $(this).val();
        let base = <?= $decimalValue ?>;
        // Allow only numbers and one decimal point
        if (val.includes(".")) {
            let parts = val.split(".");
            if (parts[1].length > base) {
                $(this).val(parts[0] + "." + parts[1].substring(0, base)); // Restrict extra decimals
            }
        }
    });

    function datevariant(vid) {
        if (vid == null) {
            vid = $("#iv_varient").val();
        }
        $.ajax({
            type: "GET",
            url: `ajaxs/credit-note/ajax-date-variant.php?vr=${vid}`,
            beforeSend: function() {
                // $("#itemsDropDown").html(`Loding...`);
            },
            success: function(response) {
                let data = JSON.parse(response);
                if (data["dateStatus"] == 1) {
                    $("#partyCreditDate").attr("max", data["end_date"]);
                    $("#partyCreditDate").attr("min", data["start_date"]);
                    $("#partyCreditDate").val(data["start_date"]);
                    $("#addNewCreditNoteFormSubmitBtn").attr("disabled", false);
                    $("#partyCreditDate").attr("disabled", false);
                    $("#creditNoteMessage").html("");
                } else {
                    $("#partyCreditDate").attr("max", "");
                    $("#partyCreditDate").attr("min", "");
                    $("#partyCreditDate").val("");
                    $("#partyCreditDate").attr("disabled", true);
                    $("#creditNoteMessage").html("Post Date Variant not Matched");
                    $("#addNewCreditNoteFormSubmitBtn").attr("disabled", true);
                }
                // console.log(data);
            }
        });

    }

    datevariant(null);

    $("#iv_varient").on("change", function() {
        let vid = $(this).val();
        let functionalArea = $("#profitCenterDropDown").val();

        $.ajax({
            type: "POST",
            url: `ajaxs/credit-note/ajax-generate-cn-number.php`,
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

        datevariant(vid);
    });


    $('#addNewJournalForm').on('submit', function() {
        let dtotal = 0;
        $(".dr-amount").each(function() {
            let velu = helperAmount($(this).val());
            if (velu > 0) {
                dtotal += velu;
            }
        });
        let ctotal = 0;
        $(".cr-amount").each(function() {
            let velu = helperAmount($(this).val());
            if (velu > 0) {
                ctotal += velu;
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
        let valllAc = helperAmount($(this).val());
        calculateDrAmount();
    });

    function calculateDrAmount() {
        let sum = 0;
        $(".dr-amount").each(function() {
            let velu = helperAmount($(this).val());
            if (velu > 0) {
                sum += velu;
            }
        });
        sum = sum;
        $('.debit-total').html(decimalAmount(sum));
    }

    $(document).on("keyup keydown paste", '.cr-amount', function() {
        let valllAc = helperAmount($(this).val());
        calculateCrAmount();
    });

    function calculateCrAmount() {
        let sum = 0;
        $(".cr-amount").each(function() {
            let velu = helperAmount($(this).val());
            if (velu > 0) {
                sum += velu;
            }
        });
        sum = sum;
        $('.credit-total').html(decimalAmount(sum));
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
            alert("Please Check Atleast 5");
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
        var newRow = ` <tr class="items_row"  id="${rand}">
                                                        <td style="width:20%">
                                                            <select name="item[${rand}][item_id]" class="form-control item_select item_select_${rand}" data-attr="${rand}">
                                                                <option>SELECT ITEM</option>
                                                                <?php
                                                                $item_sql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id` = $company_id", true)['data'];
                                                                foreach ($item_sql as $item) {


                                                                ?>



                                                                    <option value="<?= $item['itemId'] . '_' . $item['goodsType'] ?>"><?= $item['itemName'] . '[' .  $item['itemCode']  . ']' ?></option>
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
                                                            <div >
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
                                                                <input class="form-control" type="hidden" id="checkQtyVal_${rand}" name="item[${rand}][sumOfBatches]" value="">
                                                                </span>

                                                                <input type="number" name="item[${rand}][qty]" class="form-control itemQty inputQuantityClass" id="itemQty_${rand}" value="">
                                                            </div>                                                        
                                                        </td>
                                                        <td class="text-right"><input type="number" name="item[${rand}][rate]" class="form-control price inputAmountClass" id="price_${rand}" value=""></td>
                                                         <td class="text-right customerdis" id="customerdis">
                                                        <input type="number" name="item[${rand}][dis]" class="form-control itemDis inputAmountClass" id="itemDis_${rand}" value="">
                                                                                                                <input type="hidden" name="item[${rand}][disval]" class="form-control itemDisVal" id="itemDisVal_${rand}" value="0">
                                                                                                                </td>
                                                     <td class="text-right customertds" id="customertds">     
                                                     <input type="number" name="item[${rand}][total_tds_per]" class="form-control itemTds inputAmountClass" id="total_tds_per_${rand}" value="0">

<input type="hidden" name="item[${rand}][tdsval]" class="form-control itemTdsVal" id="itemTdsVal_${rand}" value="0">

</td>
                                                        <td>

                                                            <div class="d-flex gap-2">
                                                                <input type="number" class="form-control tax inputAmountClass" name="item[${rand}][tax]" id="tax_${rand}" value="">
                                                                <span class="percent-position">%</span>
                                                                <input type="hidden" class="form-control tax_amount" name="item[${rand}][tax_amount]" id="tax_amount_${rand}">
                                                            </div>
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

        if ($('input[name="select_customer_vendor"]:checked').val() == 'Customer') {
            $("#customerdis").show();
            $(".customerdis2").show();
            $("#customertds2").hide();
            $(".customertds").hide();

        } else {
            $(".customerdis").hide();
            $("#customerdis2").hide();
            $("#customertds2").show();
            $(".customertds").show();
        }

        $(`.item_select_${rand}`).select2();

        $(`.account_${rand}`).select2();
        // console.log('ap');
    });

    // $(document).on("click", ".add-btn-minus", function() {
    //     $(this).parent().parent().remove();
    //     calculateAllItemsGrandAmount();
    //     calculateAllItemTax();
    // });
</script>





<script>
    $(document).on("click", ".add-btn-minus-bill", function() {
        $(this).parent().parent().parent().remove();
        calculateAllItemsGrandAmount();
        calculateAllItemTax();
    });





    $(document).on("change", "#vendor_customer", function() {

        if ($('input[name="select_customer_vendor"]:checked').val() == 'Customer') {
            $("#customerdis").show();
            $(".customerdis2").show();
            $("#customertds2").hide();
            $(".customertds").hide();


        } else {
            $(".customerdis").hide();
            $("#customerdis2").hide();
            $("#customertds2").show();
            $(".customertds").show();
        }
        $('.inv_items').html('');
        //  alert(1);
        let value = $("#vendor_customer").find(':selected').val();
        //   alert(value);
        var splitValues = value.split('|');

        let id = splitValues[0];



        let dataAttrVal = splitValues[1];

        //  alert(dataAttrVal);
        $.ajax({

            type: "GET",

            url: `ajaxs/credit-note/ajax-address-details.php`,
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
                taxGenerate(obj['bill_address_id'], obj['shipping_address_id']);

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
        let sourceadd = $('#supplyAddress').val();
        let destadd = $('#destinationAddress').val();


        $.ajax({

            type: "GET",
            url: `ajaxs/credit-note/ajax-bill-details-aus.php`, //Need to change this ajax 
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
                taxGenerate(sourceadd, destadd);
                $("#inv_items").html(response);
                // calculateAllItemTax();

            }
        });


        $.ajax({

            type: "GET",

            url: `ajaxs/credit-note/ajax-bill-address-details.php`,
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
        let country_id = <?= json_decode($companyCountry) ?>;
        totalTax = 0;
        $(".tax_amount").each(function() {
            //  alert(1);
            let itemTotalTax = helperAmount($(this).val());

            totalTax += itemTotalTax > 0 ? itemTotalTax : 0;

        });

        let source = $('#supplyAddress').val();
        //   alert(source);
        let dest = $('#destinationAddress').val();
        //  alert(dest);
        if (country_id == '103') {
            if (source == dest) {
                // alert(1);
                tax_each = totalTax / 2;
                $('#CGST').html(decimalAmount(tax_each));
                $('#SGST').html(decimalAmount(tax_each));
                // $('#igst_span').html(0);
                // $('#sgst').val(tax_each);
                // $('#cgst').val(tax_each);
                // $('#igst').val(0);
                $('#hidden_CGST').html(decimalAmount(totalTax));
                $('#hidden_SGST').html(decimalAmount(totalTax));

            } else {
                // $('#sgst_span').html(0);
                // $('#cgst_span').html(0);
                // $('#igst_span').html(totalTax);
                // $('#sgst').val(0);
                // $('#cgst').val(0);
                $('#IGST').html(decimalAmount(totalTax));
                $('#hidden_IGST').html(decimalAmount(totalTax));
            }
        } else {
            $('#GST').html(decimalAmount(totalTax));
            $('#hidden_GST').val(decimalAmount(totalTax));
        }
        // New Code start For GST Cal
        var gstDetailsArray = [];
        $("tr.gst").each(function() {
            var gstType = $(this).find(".totalCal").text().trim();
            // var taxPercentage = $(this).find("input[type='hidden']").val();
            var taxPercentage = $(this).find("input[type='hidden']:first").val();
            var grandTaxAmtId = "#grandTaxAmt_" + gstType;
            var grandTaxAmtval = "#grandTaxAmtval_" + gstType;
            // Calculate the tax amount
            var taxAmount = helperAmount(totalTax * taxPercentage / 100);

            // Update the HTML and input fields
            $(grandTaxAmtId).html(decimalAmount(taxAmount));
            $(grandTaxAmtval).val(decimalAmount(taxAmount));
            // Add the GST details to the array
            gstDetailsArray.push({
                gstType: gstType,
                taxPercentage: taxPercentage,
                taxAmount: taxAmount
            });
        });

        // Create a single JSON object with all GST details
        var gstDetailsJson = JSON.stringify(gstDetailsArray);

        // Pass the JSON to the input field with name 'gstdetails'
        $("input[name='gstdetails']").val(gstDetailsJson);
        console.log("gstDetailsJson" + gstDetailsJson);

    }


    $(document).on("keyup", "#total_tds", function() {
        calculateAllItemsGrandAmount();
    });
    $(document).on("keyup", "#total_tcs", function() {
        calculateAllItemsGrandAmount();
    });
    $(document).on("keyup", "#total_discount", function() {
        calculateAllItemsGrandAmount();
    });




    function calculateAllItemsGrandAmount() {
        let grandTotalBeforeDiscount = 0;
        let grandTotal = 0;
        let tdiscount = 0;
        let ttds = 0;


        let total_tcs = helperAmount($("#total_tcs").val()) || 0;
        let round_value = helperAmount($("#round_value").val()) || 0;
        // let total_discount1 = helperAmount($("#total_discount1").val()) || 0;
        let round_val_operator = $('#round_sign').val();

        // Calculate total item amount before discount
        $(".amount").each(function() {
            grandTotalBeforeDiscount += helperAmount($(this).text()) || 0;
        });

        // Calculate total discount value

        $(".itemDisVal").each(function() {
            tdiscount += helperAmount($(this).val()) || 0;

        });

        $(".itemTdsVal").each(function() {
            ttds += helperAmount($(this).val()) || 0;

        });





        // Apply discount
        // grandTotalBeforeDiscount -= tdiscount;


        if (round_val_operator === '+') {
            grandTotal = grandTotalBeforeDiscount + round_value;
        } else {
            grandTotal = grandTotalBeforeDiscount - round_value;
        }


        grandTotal = grandTotal - ttds + total_tcs;
        // Update DOM elements with calculated values
        $("#total_tds").val(inputValue(ttds));
        $("#total_discount_c").text(inputValue(tdiscount));
        $("#total_discount").val(inputValue(tdiscount));
        $("#grandTotal").text(inputValue(grandTotal));
        $("#grandTotalHidden").val(inputValue(grandTotal));
        $("#subTotalHidden").val(inputValue(grandTotalBeforeDiscount));
        $("#subTotal").text(inputValue(grandTotalBeforeDiscount));
    }



    calculateAllItemsGrandAmount();
    calculateAllItemTax();


    function calculateOneItemRowAmount(rowNum) {
        let qty = helperQuantity($(`#itemQty_${rowNum}`).val());

        let dis = helperAmount($(`#itemDis_${rowNum}`).val() || 0);
        let tds = helperAmount($(`#total_tds_per_${rowNum}`).val() || 0);

        let discount = 0;
        let tdsamt = 0;
        qty = qty > 0 ? qty : 1;

        let unitPrice = helperAmount($(`#price_${rowNum}`).val());

        unitPrice = unitPrice > 0 ? unitPrice : 0;
        if (dis > 0) {
            discount = (unitPrice * (dis / 100));
            unitPrice -= (unitPrice * (dis / 100));
        }
        discount = discount * qty;
        if (tds > 0) {
            tdsamt = (unitPrice * (tds / 100));

        }
        tdsamt = tdsamt * qty;

        let tax = helperAmount($(`#tax_${rowNum}`).val());



        tax = tax > 0 ? tax : 0;
        //alert(tax);


        let tax_amount = (tax / 100 * unitPrice) * qty;

        let totalPrice = (unitPrice * qty) + tax_amount;

        // alert(totalPrice);

        $(`#itemDisVal_${rowNum}`).val(inputValue(discount));
        $(`#tax_amount_${rowNum}`).val(inputValue(tax_amount));
        $(`#amount_${rowNum}`).html(inputValue(totalPrice));
        $(`#amountHidden_${rowNum}`).val(inputValue(totalPrice));
        $(`#itemTdsVal_${rowNum}`).val(inputValue(tdsamt));

        calculateAllItemsGrandAmount();
        calculateAllItemTax();
    }


    $(document).on("keyup", ".itemQty", function() {
        // alert(1);

        let rowNum = ($(this).attr("id")).split("_")[1];
        // alert(rowNum);
        calculateOneItemRowAmount(rowNum);


    });
    $(document).on("keyup", ".itemDis", function() {
        // alert(1);

        let rowNum = ($(this).attr("id")).split("_")[1];
        // alert(rowNum);
        calculateOneItemRowAmount(rowNum);


    });
    $(document).on("keyup", ".itemTds", function() {


        let rowNum = ($(this).attr("id")).split("_")[3];

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
                $('#tdsrow').hide();
                $('#disrow').show();
                $(".customerdis").show();
                $("#customerdis2").show();
                $("#customertds2").hide();
                $(".customertds").hide();
            } else {
                $(".compInvoiceTypeDiv").hide();
                $('#tdsrow').show();
                $('#disrow').hide();
                $(".customerdis").hide();
                $("#customerdis2").hide();
                $("#customertds2").show();
                $(".customertds").hide();
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
            url: `ajaxs/credit-note/credit-vendor-customer-details.php`,
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
            url: `ajaxs/credit-note/ajax-bill-list.php`,
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

        var itemval = $(this).val().split("_");
        //console.log(itemval[1]);
        var itemType = itemval[1];
        console.log(itemType);
        // console.log('itemType :');
        // console.log('bye');


        // alert(attr);
        $.ajax({

            type: "GET",

            url: `ajaxs/credit-note/ajax-gl.php`,
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
                    // console.log('not 5/7');
                    $(`.custom_batch_${attr}`).removeClass('d-none');
                    $(`#itemQty_${attr}`).prop("readonly", true);
                } else {
                    // console.log('yes 5/7');
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
            url: `ajaxs/credit-note/ajax-items-stock-list.php`,
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

        $(".items_row").each(function() {
            let rowId = $(this).attr("id");
            let itemrow = $(`.item_select_${rowId}`).val();
            console.log(itemrow);
            let itemId = itemrow.split("_")[0];
            let itemType = itemrow.split("_")[1];

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
                    $(`.customitemreleaseDiv${rowId}`).show();
                    $(`.customitemreleaseDiv${rowId}`).html(response);
                }
            });
        });

        $.ajax({
            type: "POST",
            url: `ajaxs/debit-note/ajax-items-stock-check.php`,
            data: {
                act: "singleItemStockCheck",
                invoicedate: invoicedate,
                itemId: itemId
            },
            beforeSend: function() {
                $(".tableDataBody").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {

                let itemData = JSON.parse(response);
                console.log(itemData);

                if (itemData.status === "success") {
                    console.log('hello1');
                    console.log(itemData['data']['sumofbatch']);
                    $(`#itemQty_${rowId}`).val(0);
                    $(`#checkQty_${rowId}`).val(itemData['data']['sumofbatch']);
                    $(`#checkQtyVal_${rowId}`).val(itemData['data']['sumofbatch']);
                    $(`#checkQtySpan_${rowId}`).html(itemData['data']['sumofbatch']);
                    $(`#custom_${rowId}`).prop('checked', true);
                    $(`#itemSellType_${rowId}`).html('CUSTOM1');
                    $(`.enterQty`).val('');
                } else {

                    console.log('hello2');
                }
            }
        });
        let sourceadd = $('#supplyAddress').val();
        let destadd = $('#destinationAddress').val();
        taxGenerate(sourceadd, destadd);

    });



    // ***********************************************
    // ***********************************************

    let timeoutId;
    $(document).on("keyup keydown paste", ".manualBatchNumber", function() {
        clearTimeout(timeoutId);

        // Retrieve rndcode
        let rndcode = $(this).data("rnds");
        // console.log("rndcode:", rndcode);

        // Set the HTML content to "Checking..."
        $(".manualBatchNumberDate" + rndcode).html(`Checking...`);

        // Remove any spaces from the batchNumber input
        let batchNumber = $(this).val().replace(/\s/g, '');
        // Set the input field as readonly before making the AJAX call
        $(".manualBatchNumberBornDate" + rndcode).prop('readonly', true);
        $(this).val(batchNumber); // Assuming you want to remove spaces in the displayed value



        // Set a new timeout for 20 seconds after the last keyup event
        timeoutId = setTimeout(function() {
            $.ajax({
                type: "POST",
                url: `ajaxs/credit-note/ajax-batch-details.php`,
                data: {
                    act: "batchCheck",
                    batchNumber: batchNumber
                },
                beforeSend: function() {
                    $(".manualBatchNumberDate" + rndcode).html(`Checking...`);
                },
                success: function(response) {
                    // console.log(response);

                    let resData = JSON.parse(response);
                    if (resData['status'] == "success" && resData['numRows'] > 0) {
                        $(".manualBatchNumberBornDate" + rndcode).val(resData['data']['bornDate']).prop('readonly', true);
                        $(".manualBatchNumberDate" + rndcode).html(`Existing Batch.`);
                    } else {
                        $(".manualBatchNumberBornDate" + rndcode).val('').prop('readonly', false);
                        $(".manualBatchNumberDate" + rndcode).html(`Consider as New Batch.`);
                    }
                }
            });
        }, 1500); // 1.5 seconds delay
    });

    $(document).on("keyup paste keydown", ".enterQtyManual", function() {
        let enterQty = helperQuantity($(this).val());
        var rdcodeSt = $(this).data("rdcode");
        rdatrr = rdcodeSt.split("|");
        let rdcode = rdatrr[0]; // Change the variable name to rdcode
        let rdBatch = rdatrr[1];

        console.log(enterQty);
        if (enterQty > 0) {
            console.log("01");
            totalquentity(rdcodeSt);
        } else {
            $(this).val('');
            console.log("02");
            totalquentity(rdcodeSt);
        }
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
        let enterQty = helperQuantity(($(this).val()));
        var rdcodeSt = $(this).data("rdcode");
        var maxqty = $(this).data("maxval");
        let rdatrr = [];
        rdatrr = rdcodeSt.split("|");
        let rdcode = rdatrr[0]; // Change the variable name to rdcode
        let rdBatch = rdatrr[1];

        console.log(enterQty);
        console.log("ok");
        // if ($('input[name="select_customer_vendor"]:checked').val() == 'Customer') {
        //     totalquentity(rdcodeSt);
        //     $('.batchCheckbox' + rdBatch).prop('checked', true);
        // } else {
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
        // }
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
            var value = helperQuantity($(this).val()) || 0;
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
        $(".items_row").each(function() {
            let rowId = $(this).attr("id");
            let itemrow = $(`.item_select_${rowId}`).val();
            console.log(itemrow);
            let itemId = itemrow.split("_")[0];
            let itemType = itemrow.split("_")[1];

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
                    $(`.customitemreleaseDiv${rowId}`).show();
                    $(`.customitemreleaseDiv${rowId}`).html(response);
                }
            });
        });

        StringRowData = JSON.stringify(rowData);
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
                                $(`#checkQtyVal_${key}`).val(itemData[key]);
                                $(`#checkQtySpan_${key}`).html(itemData[key]);
                                $(`#custom_${key}`).prop('checked', true);
                                $(`#itemSellType_${key}`).html('CUSTOM');
                                $(`.enterQty`).val('');
                            }
                        }
                    }
                }
            });
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

    function taxGenerate(bilingId, shippingId) {
        //alert(1);
        let country_id = '<?php echo $companyCountry ?>';

        $.ajax({
            type: 'GET',
            url: `ajaxs/credit-note/ajax-generate-cn-tax.php`,
            data: {
                act: 'getTaxComponent',
                country_id: country_id,
                bilingId: bilingId,
                shippingId: shippingId,
            },
            beforeSend: function() {},
            success: function(response) {
                // console.log(response)
                $('.gst').remove();
                $("#subtotalTr").before(response);
                window.calculateAllItemTax();

            },
        });
    }
</script>
<script src="<?= BASE_URL; ?>public/validations/creditNotesValidation.js"></script>