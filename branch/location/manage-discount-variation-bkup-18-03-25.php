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
                                                <input type="text" class="form-control" id="discount_percentage_edit" name="discount_percentage" value="<?= $data['discount_percentage'] ?>">
                                                <input type="hidden" class="form-control" id="discount_percentage_backup" name="discount_percentage_backup" value="<?= $data['discount_percentage'] ?>">

                                                <span class="error discount_percentage"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" id="discount_max_edit_val_div">
                                            <div class="form-input mb-3">
                                                <label> Discount Maximum Value </label>
                                                <input type="text" class="form-control" id="discount_max_edit_val" name="discount_max_val" value="<?= $data['discount_max_value'] ?>">
                                                <input type="hidden" class="form-control" id="discount_max_edit_val_backup" name="discount_max_edit_val_backup" value="<?= $data['discount_max_value'] ?>">

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
                                        <input type="text" class="form-control" id="min_val" name="min_val" value="<?= $data['minimum_value'] ?>">
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
                                        <input type="text" class="form-control" id="min_qty" name="min_qty" value="<?= $data['minimum_qty'] ?>">
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
} else {


    $keywd = '';
    if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
        $keywd = $_REQUEST['keyword'];
    } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
        $keywd = $_REQUEST['keyword2'];
    }
?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper is-discount-varient">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="p-0 pt-1 my-2">
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                    <h3 class="card-title">Manage Discount Variant</h3>
                                </li>
                            </ul>
                        </div>
                        <div class="card card-tabs" style="border-radius: 20px;">
                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="return srch_frm();">

                                <div class="card-body">

                                    <div class="row filter-serach-row">

                                        <div class="col-lg-2 col-md-2 col-sm-12">

                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                        </div>

                                        <div class="col-lg-10 col-md-10 col-sm-12">

                                            <div class="row table-header-item">

                                                <div class="col-lg-11 col-md-11 col-sm-11">

                                                    <div class="section serach-input-section">



                                                        <input type="text" id="myInput" name="keyword" value="<?= $keywd ?>" placeholder="" class="field form-control" />

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

                                                <div class="col-lg-1 col-md-1 col-sm-1">

                                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

                                                </div>

                                            </div>



                                        </div>

                                    </div>

                                </div>

                            </form>





                            <!-- <div class="modal fade add-modal discount-varient-modal" id="funcAddForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                                        <input type="hidden" name="createDiscountVarient" id="createDiscountVarient" value="">
                                        <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

                                        <div class="modal-content card">
                                            <div class="modal-header card-header pt-2 pb-2 px-3">
                                                <h4 class="text-xs text-white mb-0">Create Discount Variant</h4>
                                             
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
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
                                                                    <option value="<?= $pr_row['customer_discount_group_id'] ?>"><?= $pr_row['customer_discount_group'] ?></option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                            <span class="error customer_group"></span>
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
                                                        </div>

                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <div class="form-input mb-3">
                                                            <label> Discount Percentage </label>
                                                            <input type="text" class="form-control" id="discount_percentage" name="discount_percentage">
                                                            <span class="error discount_percentage"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <div class="form-input mb-3">
                                                            <label> Discount Maximum Value </label>
                                                            <input type="text" class="form-control" id="discount_max_val" name="discount_max_val">
                                                            <span class="error discount_max_val"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <div class="form-input mb-3">
                                                            <label> Discount Value</label>
                                                            <input type="text" class="form-control" id="discount_val" name="discount_val">
                                                            <span class="error discount_val"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <div class="form-input mb-3">
                                                            <label> Term Of Payment </label>
                                                            <input type="text" class="form-control" id="term_of_payment" name="term_of_payment">
                                                            <span class="error term_of_payment"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <div class="form-input mb-3">
                                                            <label> Valid From </label>
                                                            <input type="date" class="form-control" id="valid_from" name="valid_from">
                                                            <span class="error valid_from"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <div class="form-input mb-3">
                                                            <label>Valid Upto</label>
                                                            <input type="date" class="form-control" id="valid_upto" name="valid_upto">
                                                            <span class="error valid_upto"></span>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                                        <div class="form-input mb-3">
                                                            <label> Minimum Value</label>
                                                            <input type="text" class="form-control" id="min_val" name="min_val">
                                                            <span class="error min_val"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2">
                                                        <div class="form-input mb-3">
                                                            <label class="label-hidden"></label>
                                                            <select id="condition" name="condition" class="fld form-control m-input">
                                                                <option value="AND">AND</option>
                                                                <option value="OR">OR</option>
                                                            </select>
                                                            <span class="error coupon_code"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                                        <div class="form-input mb-3">
                                                            <label> Minimum Qty</label>
                                                            <input type="text" class="form-control" id="min_qty" name="min_qty">
                                                            <span class="error min_qty"></span>
                                                        </div>
                                                    </div>


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
                                                                    <option value="<?= $row['discount_coupon_code'] ?>"><?= $row['discount_coupon_code'] ?></option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                            <span class="error coupon_code"></span>
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
                            </div> -->

                        </div>

                    </div>
                </div>







                <div class="tab-content" id="custom-tabs-two-tabContent">
                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                        <?php
                        $cond = '';

                        $sts = " AND `status` !='deleted'";
                        if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                            $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                        }

                        if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                            $cond .= " AND createdAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                        }

                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                            $cond .= " AND (item_group.item_discount_group like '%" . $_REQUEST['keyword'] . "%' OR customer_group.customer_discount_group like '%" . $_REQUEST['keyword'] . "%' OR varient.discount_type like '%" . $_REQUEST['keyword'] . "%' OR varient.discount_value like '%" . $_REQUEST['keyword'] . "%' OR varient.discount_percentage like '%" . $_REQUEST['keyword'] . "%')";
                        }

                        $sql_list = queryGet("SELECT varient.*,item_group.item_discount_group_id,item_group.item_discount_group,customer_group.customer_discount_group_id,customer_group.customer_discount_group FROM `erp_discount_variant_master` as varient LEFT JOIN  `erp_item_discount_group` as item_group ON item_group.item_discount_group_id = varient.item_discount_group_id LEFT JOIN `erp_customer_discount_group` as customer_group ON customer_group.customer_discount_group_id = varient.customer_discount_group_id WHERE 1 " . $cond . "  AND varient.`company_id`=$company_id AND varient.`branch_id`=$branch_id AND varient.`location_id`=$location_id ORDER BY discount_variant_id  desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ", true);

                        // console($sql_list);
                        //AND  layer.'warehouse_id'=warehouse.'warehouse_id' 
                        //as sl ,".ERP_WAREHOUSE." as warehouse
                        $countShow = "SELECT COUNT(*) FROM `erp_discount_variant_master` WHERE " . $cond . "  `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id  ";
                        $countQry = mysqli_query($dbCon, $countShow);
                        $rowCount = mysqli_fetch_array($countQry);
                        $count = $rowCount[0];
                        $cnt = $GLOBALS['start'] + 1;
                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_DISCOUNT_VARIENT", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                        $settingsCheckbox = unserialize($settingsCh);
                        if ($sql_list['numRows'] > 0) { ?>
                            <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                                <thead>
                                    <tr class="alert-light">
                                        <th>#</th>
                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                            <th>Customer Discount Group</th>
                                        <?php }
                                        if (in_array(2, $settingsCheckbox)) { ?>
                                            <th>Item Discount Group</th>
                                        <?php }
                                        if (in_array(3, $settingsCheckbox)) { ?>
                                            <th>Discount Percentage</th>
                                        <?php  }
                                        if (in_array(4, $settingsCheckbox)) { ?>
                                            <th>Discount Maximum Value</th>
                                        <?php }

                                        if (in_array(5, $settingsCheckbox)) { ?>
                                            <th>Discount Value</th>
                                        <?php }
                                        if (in_array(6, $settingsCheckbox)) { ?>
                                            <th>Term Of Payment</th>
                                        <?php }
                                        if (in_array(7, $settingsCheckbox)) { ?>
                                            <th>Valid From</th>
                                        <?php  }
                                        if (in_array(8, $settingsCheckbox)) { ?>
                                            <th>Valid Upto</th>
                                        <?php }

                                        if (in_array(9, $settingsCheckbox)) { ?>
                                            <th>Minimum Value/Quantity</th>
                                        <?php }
                                        if (in_array(10, $settingsCheckbox)) { ?>
                                            <th>Coupon Code</th>
                                        <?php }
                                        if (in_array(11, $settingsCheckbox)) { ?>
                                            <th>Created By</th>
                                        <?php }
                                        if (in_array(12, $settingsCheckbox)) { ?>
                                            <th>Created At</th>
                                        <?php }

                                        ?>

                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $customerModalHtml = "";
                                    foreach ($sql_list['data'] as $row) {

                                    ?>
                                        <tr>
                                            <td><?= $cnt++ ?></td>
                                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                <td><?= $row['customer_discount_group'] ?></td>
                                            <?php }
                                            if (in_array(2, $settingsCheckbox)) { ?>
                                                <td><?= $row['item_discount_group'] ?></td>
                                            <?php }
                                            if (in_array(3, $settingsCheckbox)) { ?>
                                                <td><?php if ($row['discount_type'] ==  'percentage') {
                                                        echo $row['discount_percentage'];
                                                    } else {
                                                        echo '-';
                                                    } ?></td>
                                            <?php }
                                            if (in_array(4, $settingsCheckbox)) { ?>
                                                <td><?php if ($row['discount_type'] ==  'percentage') {
                                                        echo $row['discount_max_value'];
                                                    } else {
                                                        echo '-';
                                                    } ?></td>
                                            <?php }
                                            if (in_array(5, $settingsCheckbox)) { ?>
                                                <td><?php if ($row['discount_type'] ==  'value') {
                                                        echo $row['discount_value'];
                                                    } else {
                                                        echo '-';
                                                    } ?></td>
                                            <?php }
                                            if (in_array(6, $settingsCheckbox)) { ?>
                                                <td><?= $row['term_of_payment']  ?></td>
                                            <?php }
                                            if (in_array(7, $settingsCheckbox)) { ?>
                                                <td><?= formatDateORDateTime($row['valid_from'])  ?></td>
                                            <?php }
                                            if (in_array(8, $settingsCheckbox)) { ?>
                                                <td><?= formatDateORDateTime($row['valid_upto'])  ?></td>
                                            <?php }
                                            if (in_array(9, $settingsCheckbox)) { ?>
                                                <td><?php
                                                    //    echo $row['minimum_value'];
                                                    //    echo  $row['minimum_qty'];
                                                    if ($row['minimum_value'] != 0 && $row['minimum_qty'] != 0) {

                                                        echo  $row['minimum_qty'] . '(quantity)' .  $row['condition'] . ' ' . $row['minimum_value'] . '(value)';
                                                    } else if ($row['minimum_value'] != 0 && $row['minimum_qty'] == 0) {
                                                        echo $row['minimum_value'] . '(value)';
                                                    } else if ($row['minimum_qty'] != 0 && $row['minimum_value'] == 0) {
                                                        echo $row['minimum_qty'] . '(quantity)';
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                            <?php }
                                            if (in_array(10, $settingsCheckbox)) { ?>
                                                <td><?= $row['coupon'] ?></td>
                                            <?php }


                                            if (in_array(11, $settingsCheckbox)) { ?>
                                                <td><?= getCreatedByUser($row['created_by']) ?></td>
                                            <?php }

                                            if (in_array(12, $settingsCheckbox)) { ?>
                                                <td><?= formatDateORDateTime($row['created_at']) ?></td>
                                            <?php }

                                            ?>

                                            <td>
                                                <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $row['discount_variant_id']; ?>" style="cursor: pointer;" class="btn btn-sm" title="Edit Branch"><i class="fa fa-edit po-list-icon"></i></a>

                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>


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
                                            <input type="hidden" name="pageTableName" value="ERP_DISCOUNT_VARIENT" />
                                            <div class="modal-body">
                                                <div id="dropdownframe"></div>
                                                <div id="main2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                Customer Discount Group </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                Item Discount Group</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                Discount Percentage</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                                Discount Maximum Value</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                                Discount Value </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                                Term Of Payment</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                                Valid From</td>
                                                        </tr>


                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />
                                                                Valid Upto</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox9" value="9" />
                                                                Minimum Value/Quantity</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(10, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="10" />
                                                                Coupon Code</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(11, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox11" value="11" />
                                                                Created By</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(12, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox12" value="12" />
                                                                Created At</td>
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



                            <?php
                            if ($count > 0 && $count > $GLOBALS['show']) {
                            ?>
                                <div class="pagination align-right">
                                    <?php pagination($count, "frm_opts"); ?>
                                </div>

                                <!-- End .pagination -->

                            <?php  } ?>

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
                <?= $customerModalHtml ?>


            </div>
    </div>
    </div>
    </div>
    </div>
    <!-- /.row -->
    </div>
    </section>
    <!-- /.content -->
    </div>


    <!-- /.Content Wrapper. Contains page content -->
    <!-- For Pegination------->
    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>
    <!-- End Pegination from------->


<?php
}
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