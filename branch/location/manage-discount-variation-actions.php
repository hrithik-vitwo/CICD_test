<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

require_once("../../app/v1/functions/branch/func-discount-controller.php");


//console($_SESSION);
//console($_SESSION['logedBranchAdminInfo']['fldAdminBranchId']);
//console(date("Y-m-d H:i:s"));
$discountController = new CustomerDiscountGroupController();

// if (isset($_POST["changeStatus"])) {
//   $newStatusObj = ChangeStatus($_POST, "fldAdminKey", "fldAdminStatus");
//   swalToast($newStatusObj["status"], $newStatusObj["message"]);
// }


// if (isset($_POST["create"])) {
//   $addNewObj = createData($_POST + $_FILES);
//   swalToast($addNewObj["status"], $addNewObj["message"]);
// }

// if (isset($_POST["edit"])) { 
//   $editDataObj = updateData($_POST);

//   swalToast($editDataObj["status"], $editDataObj["message"]);
// }

if (isset($_POST["createDiscountVarient"])) {
    // console($_POST);
    // exit();
    $addNewObj = $discountController->createDiscountVarient($_POST);
    swalToast($addNewObj["status"], $addNewObj["message"], BASE_URL . "branch/location/manage-discount-variation.php");
}
if (isset($_POST["editDiscountVarient"])) {
    $editNewObj = $discountController->editDiscountVarient($_POST);
    // console($_POST);
    swalToast($editNewObj["status"], $editNewObj["message"], BASE_URL . "branch/location/manage-discount-variation.php");
}
if (isset($_POST["editCoupon"])) {

    //console($_SESSION);
    $addNewObj = $discountController->editCoupon($_POST);
    swalToast($addNewObj["status"], $addNewObj["message"]);
}


// if (isset($_POST["editgoodsdata"])) {
//   $addNewObj = $warehouseController->editGoods($_POST);
//   swalToast($addNewObj["status"], $addNewObj["message"]);
// }

if (isset($_POST["add-table-settings"])) {
    // console($_POST);
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>

<link rel="stylesheet" href="../../public/assets/listing.css">

<style>
    .is-discount-varient .discount-varient-modal .modal-dialog {
        max-width: 70%;
        transform: translateY(0px) !important;
    }

    .is-discount-varient .discount-varient-modal .modal-dialog .modal-content .modal-body {
        height: auto;
        max-height: 500px;
        overflow: auto;
    }

    .discount-varient-modalbody .form-input {
        height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .discount-varient-modalbody .form-input label {
        display: flex;
        flex-direction: column;
    }

    .discount-varient-modalbody .form-input label.height-label {
        height: auto;
    }

    .discount-varient-modalbody .row.dotted-border-area {
        margin: 10px 5px 20px;
        position: relative;
        align-items: baseline;
    }

    .discount-varient-modalbody .row.dotted-border-area label.float-label {
        left: 18px;
        position: absolute;
        top: -8px;
        font-weight: 600;
        background: #dbe5ee;
        width: auto;
        text-align: center;
        padding: 0 2px;
    }

    span.label-note {
        font-size: 0.65rem;
        font-style: italic;
        margin: 2px 0 7px;
        position: relative;
        bottom: 0;
        color: #080808;
    }

    span.label-note.d-flex {
        flex-direction: column;
        align-items: baseline;
        bottom: -9px;
    }

    .tooltip-inner {
        font-size: 0.7rem;
        background: #fff;
        border: 1px solid #ccc;
        color: #000;
    }

    .tooltip .tooltip-arrow::before {
        border-top-color: #fff !important;
    }

    .is-dicount-variantion .modal-header {
        padding: 12px 20px !important;
    }

    .is-dicount-variantion .modal-header h4 {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .is-dicount-variantion .modal-footer {
        padding: 10px 13px;
        border-top: 1px solid #ccc;
        justify-content: center;
    }

    .is-dicount-variantion .modal-header h4 span.label-note {
        margin-bottom: 3px;
        position: relative;
        color: #bdbdbd;
    }

    .type-discount {
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        gap: 5px;
        margin: 5px 0;
    }

    @media(max-width: 992px) {
        span.label-note {
            position: relative;
        }
    }
</style>


<?php

if (isset($_GET['create'])) {
?>
    <div class="content-wrapper is-dicount-variantion">
        <section class="content">
            <div class="container-fluid">


                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Discount Variant List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Discount Variant</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>


                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                    <input type="hidden" name="createDiscountVarient" id="createDiscountVarient" value="">
                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

                    <div class="modal-content  card">
                        <div class="modal-header card-header pt-2 pb-2 px-3">
                            <h4 class="text-xs text-white mb-0">Create Discount Variant
                                <span class="label-note">(This will be used in Sales Quotation, Sales Order, Invoice based on the following conditions and combination of <a href="manage-customer-discount-group.php" data-toggle="tooltip" data-placement="top" title="Customer Discount Group cantains customers for whom this discount variant will be applicable.">Customer discount group</a> and <a href="manage-item-discount-group.php" data-toggle="tooltip" data-placement="right" title="Item Discount Group contains Items on which the discount properties will be tagged. And during the Sales document creation the discount % or value will be incalcated as per the below given condition/s.">Item discount group</a>.)</span>
                            </h4>
                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                        </div>
                        <div class="modal-body discount-varient-modalbody py-2 px-3">
                            <div class="row dotted-border-area">
                                <label for="" class="float-label">Master Mapping</label>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-input mb-3">
                                        <label>Customer Discount Group*</label>
                                        <select id="customer_group" name="customer_group" class="fld form-control m-input">
                                            <option value="">SELECT CUSTOMER DISCOUNT GROUP</option>
                                            <?php

                                            $pr_query = "SELECT * FROM `erp_customer_discount_group` WHERE company_id = '$company_id' ";
                                            $pr_query_list = queryGet($pr_query, true);
                                            $pr_list = $pr_query_list['data'];
                                            foreach ($pr_list as $pr_row) {
                                            ?>
                                                <option value="<?= $pr_row['customer_discount_group_id'] ?>"><?= $pr_row['customer_discount_group'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="error customer_group"></span>
                                        <span class="label-note">Customer Discount Group cantains customers for whome this discount variant will be applicable.</span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-input">
                                        <label>Item Discount Group*</label>

                                        <select id="item_group" name="item_group" class="fld form-control m-input">
                                            <option value="">SELECT ITEM DISCOUNT GROUP</option>
                                            <?php

                                            $query = "SELECT * FROM `erp_item_discount_group` WHERE company_id = '$company_id' ";
                                            $query_list = queryGet($query, true);
                                            $list = $query_list['data'];
                                            foreach ($list as $row) {
                                            ?>
                                                <option value="<?= $row['item_discount_group_id'] ?>"><?= $row['item_discount_group'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>

                                        <span class="error item_group"></span>

                                        <span class="label-note">Item Discount Group contains Items on which the discount properties will be tagged. And during the Sales document creation the discount % or value will be incalcated as per the below given condition/s.</span>

                                    </div>

                                </div>
                            </div>

                            <div class="row dotted-border-area">
                                <label for="" class="float-label">Value Configuration</label>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-input mb-3">
                                        <label class="height-label">Select Type of Discount</label>
                                        <!-- <select id="discount_type" name="discount_type" class="fld form-control m-input">\
                                            <option>SELECT DISCOUNT TYPE</option>
                                            <option value="percentage">BY PERCENTAGE</option>
                                            <option value="value">BY VALUE</option>
                                        </select> -->
                                        <div class="d-flex gap-3">
                                            <div class="type-discount by-percent">
                                                <input type="radio" id="discount_type" name="discount_type" value="percentage">
                                                <p>BY PERCENTAGE</p>
                                            </div>
                                            <div class="type-discount by-value">
                                                <input type="radio" id="discount_type" name="discount_type" value="value">
                                                <p>BY VALUE</p>
                                            </div>
                                        </div>

                                        <span class="error discount_type"></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="row align-items-baseline">
                                        <div class="col-lg-6 col-md-6 col-sm-6" id="discount_percentage_div" style="display:none;">
                                            <div class="form-input mb-3">
                                                <label class="height-label"> Discount Percentage </label>
                                                <input type="text" class="form-control" id="discount_percentage" name="discount_percentage">
                                                <span class="error discount_percentage"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" id="discount_max_val_div" style="display:none;">
                                            <div class="form-input mb-3">
                                                <label> Discount Maximum Value</label>
                                                <input type="text" class="form-control" id="discount_max_val" name="discount_max_val">
                                                <span class="error discount_max_val"></span>
                                                <span class="label-note">
                                                    Dicount Maximum value is the maximum limit of amount upto which the discount is applicable
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" id="discount_val_div" style="display:none;">
                                            <div class="form-input mb-3">
                                                <label class="height-label"> Discount Value</label>
                                                <input type="text" class="form-control" id="discount_val" name="discount_val">
                                                <span class="error discount_val"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="row dotted-border-area">
                                <label for="" class="float-label">Conditions</label>
                                <div class="col-lg-5 col-md-5 col-sm-5">
                                    <div class="form-input mb-3">
                                        <label class="height-label"> Minimum Value</label>
                                        <input type="text" class="form-control" id="min_val" name="min_val">
                                        <span class="error_min_val error text-danger"></span>
                                        <span class="label-note">
                                            Minimum Value is the lower limit of taxable amount from which the discount is applicable
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="form-input mb-3">
                                        <label class="label-hidden height-label">Label</label>
                                        <select id="condition" name="condition" class="fld form-control m-input mt-1">
                                            <option value="AND">AND</option>
                                            <option value="OR">OR</option>
                                        </select>
                                        <span class="error coupon_code"></span>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-5">
                                    <div class="form-input mb-3">
                                        <label class="height-label"> Minimum Qty</label>
                                        <input type="text" class="form-control" id="min_qty" name="min_qty">
                                        <span class="error min_qty"></span>
                                        <span class="label-note">
                                            Minimum Quantity is the lower limit of item quantity from which the discount is applicable
                                        </span>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-input mb-3">
                                        <label class="height-label mb-3">Consider payment terms in the condition</label>
                                        <div class="d-flex gap-3">
                                            <div class="type-discount by-percent">
                                                <input type="radio" id="yesDiscount" name="discount" value="yesdiscount">
                                                <p>Yes</p>
                                            </div>
                                            <div class="type-discount by-value">
                                                <input type="radio" id="noDiscount" name="discount" value="nodiscount" checked>
                                                <p>No</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4" id="termOfPaymentSection">
                                    <div class="form-input mb-3">
                                        <label class="height-label"> Term Of Payment</label>
                                        <input type="text" class="form-control" id="term_of_payment" name="term_of_payment" value="0">
                                        <span class="error term_of_payment"></span>
                                        <span class="label-note d-flex">
                                            <span>• If default is selected then this parameter will not be considered in the condition</span>
                                            <span>• Or you can set any number of days. The discount will be applicable during the sales if the credit period is less or equal to the number of the given term of payment. </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-input">
                                                <label> Valid From</label>
                                                <input type="date" class="form-control" id="valid_from" name="valid_from">
                                                <span class="error valid_from"></span>
                                                <span class="label-note">This discount variant is applicable from this date</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-input">
                                                <label>Valid Upto</label>
                                                <input type="date" class="form-control" id="valid_upto" name="valid_upto">
                                                <span class="error valid_upto"></span>
                                                <span class="label-note">
                                                    This discount variant is applicable upto this date
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary add_data coupon_add_btn" value="add_post">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>


<?php
} else if (isset($_GET['edit'])) {
?>
    <div class="content-wrapper is-dicount-variantion">
        <section class="content">
            <div class="container-fluid">

                <?php
                $discount_variant_id = $_GET['edit'];
                $sql_list = queryGet("SELECT * FROM `erp_discount_variant_master` WHERE discount_variant_id=$discount_variant_id;", false);
                // console($sql_list);
                $data = $sql_list['data'];
                ?>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Discount Variant List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Edit Discount Variant</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>


                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                    <input type="hidden" name="editDiscountVarient" id="editDiscountVarient" value="<?= $discount_variant_id ?>">
                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
                    <input type="text" style="display:none" name="discount_variant_id" value="<?= $discount_variant_id ?>">

                    <div class="modal-content card">
                        <div class="modal-header card-header pt-2 pb-2 px-3">
                            <h4 class="text-xs text-white mb-0">Edit Discount Variant
                                <span class="label-note">(This will be used in Sales Quotation, Sales Order, Invoice based on the following conditions and combination of <a href="manage-customer-discount-group.php" data-toggle="tooltip" data-placement="top" title="Customer Discount Group cantains customers for whom this discount variant will be applicable.">Customer discount group</a> and <a href="manage-item-discount-group.php" data-toggle="tooltip" data-placement="right" title="Item Discount Group contains Items on which the discount properties will be tagged. And during the Sales document creation the discount % or value will be incalcated as per the below given condition/s.">Item discount group</a>.)</span>
                            </h4>
                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                        </div>
                        <div class="modal-body discount-varient-modalbody py-2 px-3">
                            <div class="row dotted-border-area">
                                <label for="" class="float-label">Master Mapping</label>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-input mb-3">
                                        <label>Customer Discount Group* </label>
                                        <select id="customer_group" name="customer_group" class="fld form-control m-input">
                                            <option value="">SELECT CUSTOMER DISCOUNT GROUP</option>
                                            <?php

                                            $pr_query = "SELECT * FROM `erp_customer_discount_group` WHERE company_id = '$company_id' ";
                                            $pr_query_list = queryGet($pr_query, true);
                                            $pr_list = $pr_query_list['data'];
                                            foreach ($pr_list as $pr_row) {
                                            ?>
                                                <option value="<?= $pr_row['customer_discount_group_id'] ?>" <?php echo ($data['customer_discount_group_id'] == $pr_row['customer_discount_group_id']) ? 'selected' : '' ?>><?= $pr_row['customer_discount_group'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="error customer_group"></span>
                                        <span class="label-note">Customer Discount Group cantains customers for whome this discount variant will be applicable.</span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-input">
                                        <label>Item Discount Group*</label>

                                        <select id="item_group" name="item_group" class="fld form-control m-input">
                                            <option value="">SELECT ITEM DISCOUNT GROUP</option>
                                            <?php

                                            $query = "SELECT * FROM `erp_item_discount_group` WHERE company_id = '$company_id' ";
                                            $query_list = queryGet($query, true);
                                            $list = $query_list['data'];
                                            foreach ($list as $row) {
                                            ?>
                                                <option value="<?= $row['item_discount_group_id'] ?>" <?php echo ($data['item_discount_group_id'] == $row['item_discount_group_id']) ? 'selected' : '' ?>><?= $row['item_discount_group'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>

                                        <span class="error item_group"></span>
                                        <span class="label-note">Item Discount Group contains Items on which the discount properties will be tagged. And during the Sales document creation the discount % or value will be incalcated as per the below given condition/s.</span>
                                    </div>

                                </div>
                            </div>

                            <div class="row dotted-border-area">
                                <label for="" class="float-label">Value Configuration</label>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-input mb-3">
                                        <label>Select Type of Discount</label>
                                        <select id="discount_type_edit" name="discount_type" class="fld form-control m-input">
                                            <option>SELECT DISCOUNT TYPE</option>
                                            <option value="percentage" <?php echo ($data['discount_type'] == 'percentage') ? 'selected' : '' ?>>BY PERCENTAGE</option>
                                            <option value="value" <?php echo ($data['discount_type'] == 'value') ? 'selected' : '' ?>>BY VALUE</option>
                                        </select>
                                        <span class="error discount_type"></span>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="row align-items-baseline">
                                        <div class="col-lg-6 col-md-6 col-sm-6" id="discount_percentage_edit_div">
                                            <div class="form-input mb-3">
                                                <label> Discount Percentage </label>
                                                <input type="text" class="form-control inputQuantityClass" id="discount_percentage_edit" name="discount_percentage" value="<?= inputQuantity($data['discount_percentage']) ?>">
                                                <input type="hidden" class="form-control" id="discount_percentage_backup" name="discount_percentage_backup" value="<?= inputQuantity($data['discount_percentage']) ?>">

                                                <span class="error discount_percentage"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" id="discount_max_edit_val_div">
                                            <div class="form-input mb-3">
                                                <label> Discount Maximum Value </label>
                                                <input type="text" class="form-control inputAmountClass" id="discount_max_edit_val" name="discount_max_val" value="<?= inputValue($data['discount_max_value']) ?>">
                                                <input type="hidden" class="form-control" id="discount_max_edit_val_backup" name="discount_max_edit_val_backup" value="<?= inputValue($data['discount_max_value']) ?>">

                                                <span class="error discount_max_val"></span>
                                                <span class="label-note">
                                                    Dicount Maximum value is the maximum limit of amount upto which the discount is applicable
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" id="discount_val_edit_div">
                                            <div class="form-input mb-3">
                                                <label> Discount Value</label>
                                                <input type="text" class="form-control" id="discount_edit_val" name="discount_val" value="<?= $data['discount_value'] ?>">
                                                <input type="hidden" class="form-control" id="discount_edit_val_backup" name="discount_val_backup" value="<?= $data['discount_value'] ?>">

                                                <span class="error discount_val"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            <div class="row dotted-border-area">
                                <label for="" class="float-label">Conditions</label>
                                <div class="col-lg-5 col-md-5 col-sm-5">
                                    <div class="form-input mb-3">
                                        <label> Minimum Value</label>
                                        <input type="text" class="form-control inputAmountClass" id="min_val" name="min_val" value="<?= inputValue($data['minimum_value']) ?>">
                                        <span class="error_min_val text-danger"></span>
                                        <span class="label-note">
                                            Minimum Value is the lower limit of taxable amount from which the discount is applicable
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-2">
                                    <div class="form-input mb-3">
                                        <label class="label-hidden"></label>
                                        <select id="condition" name="condition" class="fld form-control m-input mt-1">
                                            <option value="AND" <?php echo ($data['condition'] == 'AND') ? 'selected' : '' ?>>AND</option>
                                            <option value="OR" <?php echo ($data['condition'] == 'OR') ? 'selected' : '' ?>>OR</option>
                                        </select>
                                        <span class="error coupon_code"></span>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-5">
                                    <div class="form-input mb-3">
                                        <label> Minimum Qty</label>
                                        <input type="text" class="form-control inputQuantityClass" id="min_qty" name="min_qty" value="<?= inputQuantity($data['minimum_qty']) ?>">
                                        <span class="error min_qty"></span>
                                        <span class="label-note">
                                            Minimum Quantity is the lower limit of item quantity from which the discount is applicable
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-input mb-3">
                                        <label> Term Of Payment </label>
                                        <input type="text" class="form-control" id="term_of_payment" name="term_of_payment" value="<?= $data['term_of_payment'] ?>">
                                        <span class="error term_of_payment"></span>
                                        <span class="label-note d-flex">
                                            <span>• If default is selected then this parameter will not be considered in the condition</span>
                                            <span>• Or you can set any number of days. The discount will be applicable during the sales if the credit period is less or equal to the number of the given term of payment. </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-input mb-3">
                                        <label> Valid From </label>
                                        <input type="date" class="form-control" id="valid_from" name="valid_from" value="<?= $data['valid_from'] ?>">
                                        <span class="error valid_from"></span>
                                        <span class="label-note">This discount variant is applicable from this date</span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="form-input mb-3">
                                        <label>Valid Upto</label>
                                        <input type="date" class="form-control" min="<?= $data['valid_from'] ?>" id="valid_upto" name="valid_upto" value="<?= $data['valid_upto'] ?>">
                                        <span class="error valid_upto"></span>
                                        <span class="label-note">
                                            This discount variant is applicable upto this date
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- 

                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-input mb-3">
                                    <label>Coupon Code</label>
                                    <select id="coupon_code" name="coupon_code" class="fld form-control m-input">
                                        <option value="">SELECT COUPON CODE</option>
                                        <?php

                                        $query = "SELECT DISTINCT discount_coupon_code
                                                            FROM erp_discount_coupon
                                                            WHERE company_id = '$company_id'";
                                        $query_list = queryGet($query, true);
                                        $list = $query_list['data'];
                                        foreach ($list as $row) {
                                        ?>
                                            <option value="<?= $row['discount_coupon_code'] ?>" <?php echo ($data['coupon'] == $row['discount_coupon_code']) ? 'selected' : '' ?>><?= $row['discount_coupon_code'] ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <span class="error coupon_code"></span>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary add_data coupon_add_btn" value="add_post">Update</button>
                    </div>
            </div>
            </form>

    </div>
    </section>
    </div>





<?php
} 
?>



<?php
require_once("../common/footer.php");
?>

<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
<script>
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

    function checkDiscountType() {
        let distype = $('#discount_type_edit').val();
        if (distype == 'value') {

            $("#discount_val_edit_div").show();
            $("#discount_max_edit_val_div").hide();
            $("#discount_percentage_edit_div").hide();

        } else {

            $("#discount_max_edit_val_div").show();
            $("#discount_percentage_edit_div").show();
            $("#discount_val_edit_div").hide();
        }



    }

    $(document).ready(function() {

        checkDiscountType();


        // for editing page script

        $("#discount_type_edit").on('change', function() {

            // alert(1);
            var val = $(this).val();

            let discount_percentage = $('#discount_percentage_backup').val();
            $('#discount_percentage_edit').val(discount_percentage);



            let discount_max_val = $('#discount_max_edit_val_backup').val();
            $('#discount_max_edit_val').val(discount_max_val);



            let discount_val = $('#discount_edit_val').val();
            $('#discount_edit_val_backup').val(discount_val);


            if (val == 'percentage') {

                $("#discount_max_edit_val_div").show();
                $("#discount_percentage_edit_div").show();
                $("#discount_val_edit_div").hide();
            } else if (val == 'value') {
                $("#discount_val_edit_div").show();
                $("#discount_max_edit_val_div").hide();
                $("#discount_percentage_edit_div").hide();
                $('#discount_max_edit_val').val(0);

            } else {

            }


        });




        $('input[name="discount_type"]').change(function() {

            // alert(1);
            var val = $(this).val();

            if (val == 'percentage') {

                $("#discount_max_val_div").show();
                $("#discount_percentage_div").show();
                $("#discount_val_div").hide();
            } else if (val == 'value') {
                $("#discount_val_div").show();
                $("#discount_max_val_div").hide();
                $("#discount_percentage_div").hide();


            } else {

            }


        });



        $("#coupon_serial").keyup(function() {

            // alert(sl);
            var attr = $(this).data('attr');

            if (attr == 'edit') {
                var sl = $('#coupon_serial_hidden').val();
            } else {
                var sl = $(this).val();
            }


            $.ajax({
                type: "POST",
                url: `ajaxs/discount/ajax-coupon-serial.php`,
                data: {
                    sl
                },

                beforeSend: function() {
                    //$("#warehouseDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    alert(response);
                    if (response > 0) {
                        $(".coupon_serial_error").html('Duplicate Serial Code');
                        $(".coupon_add_btn").prop("disabled", true);

                    } else {
                        $(".coupon_serial_error").html('');
                        $(".coupon_add_btn").prop("disabled", false);

                    }
                }
            });


        });

        $(document).on('keyup', '#discount_val', function() {
            //  alert(1);
            var discount_val = $(this).val();

            var min_val = $("#min_val").val();
            //   alert(discount_val);
            //   alert(min_val);
            if (Number(min_val) < Number(discount_val)) {
               // alert(0);
                $(".error_min_val").html('Minimum Order Value must be greater than discount value');
            } else {
                $(".error_min_val").html('');
            }

        });


        $(document).on('keyup', '#discount_max_val', function() {
            //  alert(1);
            var discount_max_val = $(this).val();

            var min_val = $("#min_val").val();
            //   alert(discount_max_val);
            //   alert(min_val);
            if (Number(min_val) < Number(discount_max_val)) {
                // alert(0);
                $(".error_min_val").html('Minimum Order Value must be greater than discount value');
            } else {
                $(".error_min_val").html('');
            }

        });

        $(document).on('keyup', '#min_val', function() {
            var min_val = $(this).val();
            var discount_max_val = $('#discount_max_val').val();
            var discount_val = $('discount_val').val()

            if (discount_max_val != null) {

                if (Number(min_val) < Number(discount_max_val)) {
                    // alert(0);
                    $(".error_min_val").html('Minimum Order Value must be greater than discount value');
                } else {
                    $(".error_min_val").html('');
                }

            }


            if (discount_val != null) {

                if (Number(min_val) < Number(discount_val)) {
                    // alert(0);
                    $(".error_min_val").html('Minimum Order Value must be greater than discount value');
                } else {
                    $(".error_min_val").html('');
                }

            }


        });

        // $('#warehouseDropDown')
        //   .select2()
        //   .on('select2:open', () => {
        //     //$(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodTypesFormModal">Add New</a></div>`);
        //   });

        // $("#warehouseDropDown").change(function() {
        //   let dataAttrVal = $("#warehouseDropDown").find(':selected').data('goodtype');
        //   if (dataAttrVal == "RM") {
        //     $("#bomCheckBoxDiv").html("");
        //   } else if (dataAttrVal == "SFG") {
        //     $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired"style="width: auto; margin-bottom: 0;" checked>Required BOM`);

        //   } else {
        //     $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired"style="width: auto; margin-bottom: 0;">Required BOM`);
        //   }
        // });

        //**************************************************************
        $('#goodGroupDropDown')
            .select2()
            .on('select2:open', () => {
                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodGroupFormModal">Add New</a></div>`);
            });

        $('#purchaseGroupDropDown')
            .select2()
            .on('select2:open', () => {
                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewPurchaseGroupFormModal">Add New</a></div>`);
            });

        $('#warehouseDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });
    });
</script>
<script>
    $(document).ready(function() {
        $('input[name="discount"]').change(function() {
            if ($('#yesDiscount').is(':checked')) {
                $('#termOfPaymentSection').show();
            } else {
                $('#termOfPaymentSection').hide();
            }
        });

        // Initial check on page load
        if ($('#yesDiscount').is(':checked')) {
            $('#termOfPaymentSection').show();
        } else {
            $('#termOfPaymentSection').hide();
        }
    });
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
</script>

<script>
    $(document).ready(function() {
        // function loadWarehouse() {
        //   $.ajax({
        //     type: "GET",
        //     url: `ajaxs/warehouse/ajax-warehouse.php`,
        //     beforeSend: function() {
        //       $("#warehouseDropDown").html(`<option value="">Loding...</option>`);
        //     },
        //     success: function(response) {
        //       $("#warehouseDropDown").html(response);
        //     }
        //   });
        // }

        $(document).on("change", "#valid_from", function() {
            var fromDate = new Date($(this).val());
            var toDateInput = $('#valid_upto');

            // Set the minimum date for the "To Date" field
            toDateInput.prop('min', $(this).val());

            // Reset the value of "To Date" if it's invalid
            var toDate = new Date(toDateInput.val());
            if (toDate < fromDate) {
                toDateInput.val('');
            }

            // Enable or disable "To Date" field based on the selection of "From Date"
            if ($(this).val() !== '') {
                toDateInput.prop('disabled', false);
            } else {
                toDateInput.prop('disabled', true);
            }
        });



        $(document).on("blur", "#valid_upto", function() {

            var fromDate = new Date($('#valid_from').val());
            var toDate = new Date($(this).val());
            if ($('#valid_from').val() && $('#valid_upto').val()) {
                if (toDate < fromDate) {
                    alert('From Date cannot be greater than To Date');
                    $(this).val('');
                }
            }

        });

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


        $(".add_data").click(function() {
            var data = this.value;
            $("#createDiscountVarient").val(data);
            //confirm('Are you sure to Submit?')
            $("#SubmitForm").submit();
        });


        $(".edit_data").click(function() {
            var data = this.value;
            $("#editStorageLocation").val(data);
            //confirm('Are you sure to Submit?')
            $("#Edit_data").submit();
        });


        //volume calculation
        function calculate_volume() {
            let height = $("#height").val();
            let width = $("#width").val();
            let length = $("#length").val();
            let res = height * length * width;
            let resm = res * 0.000001;
            console.log(res);
            $("#volcm").val(res);
            $("#volm").val(resm);


        }

        // $(document).on("keyup", ".calculate_volume", function(){
        //  calculate_volume();
        // });

        $("#height").keyup(function() {
            calculate_volume();
        });
        $("#width").keyup(function() {
            calculate_volume();
        });
        $("#length").keyup(function() {
            calculate_volume();
        });


        $("#buomDrop").change(function() {
            let res = $(this).val();
            $("#buom").val(res);
            console.log("buomDrop", res);
        });

        $("#iuomDrop").change(function() {
            let rel = $(this).val();
            $("#ioum").val(rel);
            console.log("iuomDrop", rel);
        });



    });
</script>
<script>
    var input = document.getElementById("myInput");
    input.addEventListener("keypress", function(event) {
        // console.log(event.key)

        if (event.key === "Enter") {
            event.preventDefault();
            // alert("clicked")
            document.getElementById("myBtn").click();
        }
    });
    var form = document.getElementById("search");

    document.getElementById("myBtn").addEventListener("click", function() {
        form.submit();
    });

    //     $(document).ready(function() {
    //     $('#valid_upto').change(function() {
    //         alert(1);
    //         var fromDate = new Date($('#valid_from').val());
    //         var toDate = new Date($(this).val());

    //         if (toDate < fromDate) {
    //             alert('To Date cannot be greater than From Date');
    //             $(this).val(''); // Clear the invalid date
    //         }
    //     });
    // });
</script>