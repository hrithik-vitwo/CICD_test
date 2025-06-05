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

// if (isset($_POST["add-table-settings"])) {
//     // console($_POST);
//     $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
//     swalToast($editDataObj["status"], $editDataObj["message"]);
// }



$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = $fileNameWithoutExtension . 'download_all_' . $currentDateTime;
if (!isset($_COOKIE["cookieDiscountVariant"])) {
    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieDiscountVariant", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    } else {
        for ($i = 0; $i < 5; $i++) {
            $isChecked = ($i < 5) ? 'checked' : '';
        }
    }
}
$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Customer Discount Group',
        'slag' => 'customer_discount_group',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Item Discount Group',
        'slag' => 'item_discount_group',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Discount Percentage',
        'slag' => 'discount_percentage',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Discount Maximum Value',
        'slag' => 'discount_max_value',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Discount Value',
        'slag' => 'discount_value',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Term Of Payment',
        'slag' => 'term_of_payment',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Valid From',
        'slag' => 'valid_from',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Valid Upto',
        'slag' => 'valid_upto',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Minimum Value/Quantity',
        'slag' => 'minimum_valueQuantity',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Coupon Code',
        'slag' => 'coupon',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Created By',
        'slag' => 'created_by',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Created At',
        'slag' => 'created_at',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ]
];

?>

<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

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
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i
                                class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i
                                class="fa fa-list po-list-icon"></i>Discount Variant List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                            Create Discount Variant</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>


                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                    <input type="hidden" name="createDiscountVarient" id="createDiscountVarient" value="">
                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId"
                        value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

                    <div class="modal-content  card">
                        <div class="modal-header card-header pt-2 pb-2 px-3">
                            <h4 class="text-xs text-white mb-0">Create Discount Variant
                                <span class="label-note">(This will be used in Sales Quotation, Sales Order, Invoice based
                                    on the following conditions and combination of <a
                                        href="manage-customer-discount-group.php" data-toggle="tooltip" data-placement="top"
                                        title="Customer Discount Group cantains customers for whom this discount variant will be applicable.">Customer
                                        discount group</a> and <a href="manage-item-discount-group.php"
                                        data-toggle="tooltip" data-placement="right"
                                        title="Item Discount Group contains Items on which the discount properties will be tagged. And during the Sales document creation the discount % or value will be incalcated as per the below given condition/s.">Item
                                        discount group</a>.)</span>
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
                                                <option value="<?= $pr_row['customer_discount_group_id'] ?>">
                                                    <?= $pr_row['customer_discount_group'] ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="error customer_group"></span>
                                        <span class="label-note">Customer Discount Group cantains customers for whome this
                                            discount variant will be applicable.</span>
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
                                                <option value="<?= $row['item_discount_group_id'] ?>">
                                                    <?= $row['item_discount_group'] ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>

                                        <span class="error item_group"></span>

                                        <span class="label-note">Item Discount Group contains Items on which the discount
                                            properties will be tagged. And during the Sales document creation the discount %
                                            or value will be incalcated as per the below given condition/s.</span>

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
                                                <input type="radio" id="discount_type" name="discount_type"
                                                    value="percentage">
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
                                        <div class="col-lg-6 col-md-6 col-sm-6" id="discount_percentage_div"
                                            style="display:none;">
                                            <div class="form-input mb-3">
                                                <label class="height-label"> Discount Percentage </label>
                                                <input type="text" class="form-control" id="discount_percentage"
                                                    name="discount_percentage">
                                                <span class="error discount_percentage"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" id="discount_max_val_div"
                                            style="display:none;">
                                            <div class="form-input mb-3">
                                                <label> Discount Maximum Value</label>
                                                <input type="text" class="form-control" id="discount_max_val"
                                                    name="discount_max_val">
                                                <span class="error discount_max_val"></span>
                                                <span class="label-note">
                                                    Dicount Maximum value is the maximum limit of amount upto which the
                                                    discount is applicable
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6" id="discount_val_div" style="display:none;">
                                            <div class="form-input mb-3">
                                                <label class="height-label"> Discount Value</label>
                                                <input type="text" class="form-control" id="discount_val"
                                                    name="discount_val">
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
                                            Minimum Value is the lower limit of taxable amount from which the discount is
                                            applicable
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
                                            Minimum Quantity is the lower limit of item quantity from which the discount is
                                            applicable
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
                                                <input type="radio" id="noDiscount" name="discount" value="nodiscount"
                                                    checked>
                                                <p>No</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4" id="termOfPaymentSection">
                                    <div class="form-input mb-3">
                                        <label class="height-label"> Term Of Payment</label>
                                        <input type="text" class="form-control" id="term_of_payment" name="term_of_payment"
                                            value="0">
                                        <span class="error term_of_payment"></span>
                                        <span class="label-note d-flex">
                                            <span>• If default is selected then this parameter will not be considered in the
                                                condition</span>
                                            <span>• Or you can set any number of days. The discount will be applicable
                                                during the sales if the credit period is less or equal to the number of the
                                                given term of payment. </span>
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
                                                <span class="label-note">This discount variant is applicable from this
                                                    date</span>
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
                            <button type="submit" class="btn btn-primary add_data coupon_add_btn"
                                value="add_post">Submit</button>
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
                        <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i
                                    class="fas fa-home po-list-icon"></i> Home</a></li>
                        <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i
                                    class="fa fa-list po-list-icon"></i>Discount Variant List</a></li>
                        <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                                Edit Discount Variant</a></li>
                        <li class="back-button">
                            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                                <i class="fa fa-reply po-list-icon"></i>
                            </a>
                        </li>
                    </ol>


                    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                        <input type="hidden" name="editDiscountVarient" id="editDiscountVarient"
                            value="<?= $discount_variant_id ?>">
                        <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId"
                            value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
                        <input type="text" style="display:none" name="discount_variant_id" value="<?= $discount_variant_id ?>">

                        <div class="modal-content card">
                            <div class="modal-header card-header pt-2 pb-2 px-3">
                                <h4 class="text-xs text-white mb-0">Edit Discount Variant
                                    <span class="label-note">(This will be used in Sales Quotation, Sales Order, Invoice based
                                        on the following conditions and combination of <a
                                            href="manage-customer-discount-group.php" data-toggle="tooltip" data-placement="top"
                                            title="Customer Discount Group cantains customers for whom this discount variant will be applicable.">Customer
                                            discount group</a> and <a href="manage-item-discount-group.php"
                                            data-toggle="tooltip" data-placement="right"
                                            title="Item Discount Group contains Items on which the discount properties will be tagged. And during the Sales document creation the discount % or value will be incalcated as per the below given condition/s.">Item
                                            discount group</a>.)</span>
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
                                            <span class="label-note">Customer Discount Group cantains customers for whome this
                                                discount variant will be applicable.</span>
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
                                            <span class="label-note">Item Discount Group contains Items on which the discount
                                                properties will be tagged. And during the Sales document creation the discount %
                                                or value will be incalcated as per the below given condition/s.</span>
                                        </div>

                                    </div>
                                </div>

                                <div class="row dotted-border-area">
                                    <label for="" class="float-label">Value Configuration</label>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-input mb-3">
                                            <label>Select Type of Discount</label>
                                            <select id="discount_type_edit" name="discount_type"
                                                class="fld form-control m-input">
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
                                                    <input type="text" class="form-control" id="discount_percentage_edit"
                                                        name="discount_percentage" value="<?= $data['discount_percentage'] ?>">
                                                    <input type="hidden" class="form-control" id="discount_percentage_backup"
                                                        name="discount_percentage_backup"
                                                        value="<?= $data['discount_percentage'] ?>">

                                                    <span class="error discount_percentage"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6" id="discount_max_edit_val_div">
                                                <div class="form-input mb-3">
                                                    <label> Discount Maximum Value </label>
                                                    <input type="text" class="form-control" id="discount_max_edit_val"
                                                        name="discount_max_val" value="<?= $data['discount_max_value'] ?>">
                                                    <input type="hidden" class="form-control" id="discount_max_edit_val_backup"
                                                        name="discount_max_edit_val_backup"
                                                        value="<?= $data['discount_max_value'] ?>">

                                                    <span class="error discount_max_val"></span>
                                                    <span class="label-note">
                                                        Dicount Maximum value is the maximum limit of amount upto which the
                                                        discount is applicable
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6" id="discount_val_edit_div">
                                                <div class="form-input mb-3">
                                                    <label> Discount Value</label>
                                                    <input type="text" class="form-control" id="discount_edit_val"
                                                        name="discount_val" value="<?= $data['discount_value'] ?>">
                                                    <input type="hidden" class="form-control" id="discount_edit_val_backup"
                                                        name="discount_val_backup" value="<?= $data['discount_value'] ?>">

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
                                            <input type="text" class="form-control" id="min_val" name="min_val"
                                                value="<?= $data['minimum_value'] ?>">
                                            <span class="error_min_val text-danger"></span>
                                            <span class="label-note">
                                                Minimum Value is the lower limit of taxable amount from which the discount is
                                                applicable
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2">
                                        <div class="form-input mb-3">
                                            <label class="label-hidden"></label>
                                            <select id="condition" name="condition" class="fld form-control m-input mt-1">
                                                <option value="AND" <?php echo ($data['condition'] == 'AND') ? 'selected' : '' ?>>
                                                    AND</option>
                                                <option value="OR" <?php echo ($data['condition'] == 'OR') ? 'selected' : '' ?>>OR
                                                </option>
                                            </select>
                                            <span class="error coupon_code"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                        <div class="form-input mb-3">
                                            <label> Minimum Qty</label>
                                            <input type="text" class="form-control" id="min_qty" name="min_qty"
                                                value="<?= $data['minimum_qty'] ?>">
                                            <span class="error min_qty"></span>
                                            <span class="label-note">
                                                Minimum Quantity is the lower limit of item quantity from which the discount is
                                                applicable
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input mb-3">
                                            <label> Term Of Payment </label>
                                            <input type="text" class="form-control" id="term_of_payment" name="term_of_payment"
                                                value="<?= $data['term_of_payment'] ?>">
                                            <span class="error term_of_payment"></span>
                                            <span class="label-note d-flex">
                                                <span>• If default is selected then this parameter will not be considered in the
                                                    condition</span>
                                                <span>• Or you can set any number of days. The discount will be applicable
                                                    during the sales if the credit period is less or equal to the number of the
                                                    given term of payment. </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input mb-3">
                                            <label> Valid From </label>
                                            <input type="date" class="form-control" id="valid_from" name="valid_from"
                                                value="<?= $data['valid_from'] ?>">
                                            <span class="error valid_from"></span>
                                            <span class="label-note">This discount variant is applicable from this date</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input mb-3">
                                            <label>Valid Upto</label>
                                            <input type="date" class="form-control" min="<?= $data['valid_from'] ?>"
                                                id="valid_upto" name="valid_upto" value="<?= $data['valid_upto'] ?>">
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
                            <button type="submit" class="btn btn-primary add_data coupon_add_btn"
                                value="add_post">Update</button>
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
        <div class="content-wrapper report-wrapper vitwo-alpha-global">
            <!-- Content Header (Page header) -->
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <?php ?>
                    <!-- row -->
                    <div class="row p-0 m-0">
                        <div class="col-12 p-0">
                            <div class="card card-tabs reports-card">
                                <div class="card-body">
                                    <div class="row filter-serach-row m-0">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row table-header-item">
                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-0 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                        <!---------------------- Search START -->
                                        <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                            <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space"
                                                style="width:100%">
                                                <div class="left-block">
                                                    <div class="label-select">
                                                        <h3 class="card-title mb-0">Manage Discount Variant
                                                        </h3>
                                                    </div>
                                                </div>


                                                <div class="right-block">

                                                    <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()">
                                                        <ion-icon name="expand-outline"></ion-icon>
                                                    </button>
                                                    <button type="button" id="revealList" class="page-list">
                                                        <ion-icon name="funnel-outline"></ion-icon>
                                                    </button>
                                                    <div id="modal-container">
                                                        <div class="modal-background">
                                                            <div class="modal">
                                                                <button class="btn-close-modal" is="closeFilterModal">
                                                                    <ion-icon name="close-outline"></ion-icon>
                                                                </button>
                                                                <h5>Filter Pages</h5>

                                                                <h5>Search and Export</h5>
                                                                <div class="filter-action filter-mobile-search mobile-page">
                                                                    <a type="button" class="btn add-col setting-menu"
                                                                        data-toggle="modal" data-target="#myModal1"> <ion-icon
                                                                            name="settings-outline"></ion-icon></a>
                                                                    <div class="filter-search">
                                                                        <div class="icon-search" data-toggle="modal"
                                                                            data-target="#btnSearchCollpase_modal">
                                                                            <ion-icon name="filter-outline"></ion-icon>
                                                                            Advance Filter
                                                                        </div>
                                                                    </div>
                                                                    <div class="exportgroup mobile-page mobile-export">
                                                                        <button class="exceltype btn btn-primary btn-export"
                                                                            type="button">
                                                                            <ion-icon name="download-outline"></ion-icon>
                                                                        </button>
                                                                        <ul class="export-options">
                                                                            <li>
                                                                                <button class="ion-paginationlistDisVariant">
                                                                                    <ion-icon name="list-outline"
                                                                                        class="ion-paginationlistDisVariant md hydrated"
                                                                                        id="exportAllBtn" role="img"
                                                                                        aria-label="list outline"></ion-icon>Export
                                                                                </button>
                                                                            </li>
                                                                            <li>
                                                                                <button class="ion-fulllistitemDisVariant">
                                                                                    <ion-icon name="list-outline"
                                                                                        class="ion-fulllistitemDisVariant md hydrated"
                                                                                        role="img"
                                                                                        aria-label="list outline"></ion-icon>Download
                                                                                </button>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                    <a href=""
                                                                        class="btn btn-create mobile-page mobile-create additemdiscountMrpbtn"
                                                                        data-toggle="modal" data-target="#funcAddForm"
                                                                        type="button">
                                                                        <ion-icon name="add-outline"></ion-icon>
                                                                        Create
                                                                    </a>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                        <!---------------------- Search END -->
                                    </div>



                                    <div class="card card-tabs mobile-transform-card mb-0" style="border-radius: 20px;">
                                        <div class="card-body">
                                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                                <div class="tab-pane dataTableTemplate dataTable_stock fade show active"
                                                    id="listTabPan" role="tabpanel" aria-labelledby="listTab"
                                                    style="background: #fff; border-radius: 20px;">
                                                    <div class="length-row mobile-legth-row">
                                                        <span>Show</span>
                                                        <select name="" id="" class="custom-select" value="25">
                                                            <option value="10">10</option>
                                                            <option value="25" selected="selected">25</option>
                                                            <option value="50">50</option>
                                                            <option value="100">100</option>
                                                            <option value="200">200</option>
                                                            <option value="250">250</option>
                                                        </select>
                                                        <span>Entries</span>
                                                    </div>
                                                    <div class="filter-action">
                                                        <a type="button" class="btn add-col setting-menu" data-toggle="modal"
                                                            data-target="#myModal1"> <ion-icon
                                                                name="settings-outline"></ion-icon> Manage Column</a>
                                                        <div class="length-row">
                                                            <span>Show</span>
                                                            <select name="" id="disVariantGroupLimit" class="custom-select">
                                                                <option value="10">10</option>
                                                                <option value="25" selected="selected">25</option>
                                                                <option value="50">50</option>
                                                                <option value="100">100</option>
                                                                <option value="200">200</option>
                                                                <option value="250">250</option>
                                                            </select>
                                                            <span>Entries</span>
                                                        </div>
                                                        <div class="filter-search">
                                                            <div class="icon-search" data-toggle="modal"
                                                                data-target="#btnSearchCollpase_modal">
                                                                <p>Advance Search</p>
                                                                <ion-icon name="filter-outline"></ion-icon>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="exportgroup">
                                                        <button class="exceltype btn btn-primary btn-export" type="button">
                                                            <ion-icon name="download-outline"></ion-icon>
                                                            Export
                                                        </button>
                                                        <ul class="export-options">
                                                            <li>
                                                                <button class="ion-paginationlistDisVariant">
                                                                    <ion-icon name="list-outline"
                                                                        class="ion-paginationlistDisVariant md hydrated"
                                                                        role="img" aria-label="list outline"></ion-icon>Export
                                                                </button>
                                                            </li>
                                                            <li>

                                                                <button class="ion-fulllistitemDisVariant">
                                                                    <ion-icon name="list-outline"
                                                                        class="ion-fulllistitemDisVariant md hydrated"
                                                                        role="img" aria-label="list outline"></ion-icon>Download
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <a href="" class="btn btn-create mobile-page mobile-create addMrpbtn"
                                                        data-toggle="modal" data-target="#funcAddForm" type="button">
                                                        <ion-icon name="add-outline"></ion-icon>
                                                        Create
                                                    </a>


                                                    <table id="dataTable_detailed_view"
                                                        class="table table-hover table-nowrap stock-new-table transactional-book-table">

                                                        <thead>
                                                            <tr>
                                                                <?php
                                                                foreach ($columnMapping as $index => $column) {
                                                                    ?>
                                                                    <th data-value="<?= $index ?>"><?= $column['name'] ?></th>
                                                                <?php
                                                                }
                                                                ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="detailed_tbody">
                                                        </tbody>
                                                    </table>
                                                    <div class="row custom-table-footer">
                                                        <div class="col-lg-6 col-md-6 col-12">
                                                            <div id="limitText" class="limit-text">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-12">
                                                            <div id="yourDataTable_paginate">
                                                                <div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <!---------------------------------deialed View Table settings Model Start--------------------------------->
                                            <div class="modal manage-column-setting-modal" id="myModal1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title text-sm">Detailed View Column
                                                                Settings</h4>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form name="table_settings_detailed_view" method="POST"
                                                            action="<?php $_SERVER['PHP_SELF']; ?>">
                                                            <div class="modal-body" style="max-height: 450px;">
                                                                <!-- <h4 class="modal-title">Detailed View Column Settings</h4> -->
                                                                        <input type="hidden" id="tablename" name="tablename"
                                                                            value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                                                        <input type="hidden" id="pageTableName"
                                                                            name="pageTableName"
                                                                            value="ERP_TEST_<?= $pageName ?>" />
                                                                        <div class="modal-body">
                                                                            <div id="dropdownframe"></div>
                                                                            <div id="main2">
                                                                                <div class="checkAlltd d-flex gap-2 mb-3 pl-2">
                                                                                    <input type="checkbox"
                                                                                        class="grand-checkbox" value="" />
                                                                                    <p class="text-xs font-bold">Check All</p>
                                                                                </div>

                                                                                <table class="colomnTable">
                                                                                    <?php
                                                                                    $cookieTableStockReport = json_decode($_COOKIE["cookieDiscountVariant"], true) ?? [];

                                                                                    foreach ($columnMapping as $index => $column) {

                                                                                        ?>
                                                                                        <tr>
                                                                                            <td valign="top">

                                                                                                <input type="checkbox"
                                                                                                    class="settingsCheckbox_detailed"
                                                                                                    name="settingsCheckbox[]"
                                                                                                    id="settingsCheckbox_detailed_view[]"
                                                                                                    value='<?= $column['slag'] ?>'>
                                                                                            <?= $column['name'] ?>
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php
                                                                                    }
                                                                                    ?>

                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="submit" id="check-box-submt"
                                                                            name="check-box-submit" data-dismiss="modal"
                                                                            class="btn btn-primary">Save</button>
                                                                        <button type="button" class="btn btn-danger"
                                                                            data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!---------------------------------Table Model End--------------------------------->

                                            <div class="modal " id="btnSearchCollpase_modal" tabindex="-1" role="dialog"
                                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-sm" id="exampleModalLongTitle">
                                                                Advanced Filter</h5>
                                                        </div>
                                                        <form id="myForm" method="post" action="">
                                                            <div class="modal-body">

                                                                <table>
                                                                    <tbody>
                                                                        <?php
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", "<", ">", ">=", "<=", "=", "!=", "BETWEEN"];

                                                                        foreach ($columnMapping as $columnIndex => $column) {
                                                                            if ($columnIndex === 0) {
                                                                                continue;
                                                                            } ?>
                                                                        <tr>
                                                                            <td>
                                                                                <div
                                                                                    class="icon-filter d-flex align-items-center gap-2">
                                                                                    <?= $column['icon'] ?>
                                                                                    <p
                                                                                        id="columnName_<?= $columnIndex ?>">
                                                                                        <?= $column['name'] ?>
                                                                                    </p>
                                                                                    <input type="hidden"
                                                                                        id="columnSlag_<?= $columnIndex ?>"
                                                                                        value="<?= $column['slag'] ?>">
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <select
                                                                                    class="form-control selectOperator"
                                                                                    id="selectOperator_<?= $columnIndex ?>"
                                                                                    name="operator[]" val="">
                                                                                    <?php
                                                                                    if (($column['dataType'] === 'date')) {
                                                                                        $operator = array_slice($operators, -3, 3);
                                                                                        foreach ($operator as $oper) {
                                                                                            ?>
                                                                                    <option value="<?= $oper ?>">
                                                                                        <?= $oper ?>
                                                                                    </option>
                                                                                    <?php
                                                                                        }
                                                                                    } elseif ($column['dataType'] === 'number') {
                                                                                        $operator = array_slice($operators, 2, 6);
                                                                                        foreach ($operator as $oper) {
                                                                                            ?>
                                                                                    <option value="<?= $oper ?>">
                                                                                        <?= $oper ?>
                                                                                    </option>
                                                                                    <?php

                                                                                        }
                                                                                    } else {
                                                                                        $operator = array_slice($operators, 0, 2);
                                                                                        foreach ($operator as $oper) {
                                                                                            if ($oper === 'CONTAINS') {
                                                                                                ?>
                                                                                    <option value="LIKE">
                                                                                        <?= $oper ?>
                                                                                    </option>
                                                                                    <?php
                                                                                            } else { ?>

                                                                                    <option value="NOT LIKE">
                                                                                        <?= $oper ?>
                                                                                    </option>

                                                                                    <?php
                                                                                            }
                                                                                        }
                                                                                    } ?>
                                                                                </select>
                                                                            </td>
                                                                            <td id="td_<?= $columnIndex ?>">
                                                                                <input
                                                                                    type="<?= ($column['dataType'] === 'date') ? 'date' : 'input' ?>"
                                                                                    data-operator-val="" name="value[]"
                                                                                    class="fld form-control m-input"
                                                                                    id="value_<?= $columnIndex ?>"
                                                                                    placeholder="Enter Keyword"
                                                                                    value="">
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" id="serach_reset"
                                                                    class="btn btn-primary">Reset</button>
                                                                <button type="submit" id="serach_submit"
                                                                    class="btn btn-primary"
                                                                    data-dismiss="modal">Search</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade add-modal func-add-modal" id="funcAddForm"
                                                tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <form action="" id="add_frm" name="add_frm">
                                                        <input type="hidden" name="createdata" id="createdata" value="">
                                                        <input type="hidden" name="fldAdminCompanyId"
                                                            id="fldAdminCompanyId"
                                                            value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

                                                        <div class="modal-content card">
                                                            <div class="modal-header card-header pt-2 pb-2 px-3">
                                                                <h4 class="text-xs text-white mb-0">Create Item
                                                                    Discount
                                                                    Group</h4>
                                                                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                <div class="form-input mb-3">
                                                                                    <label>Item Discount Group Name*
                                                                                    </label>
                                                                                    <input type="text" class="form-control"
                                                                                        id="additemdisGrpname" name="name"
                                                                                        required>
                                                                                    <span class="error name"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="submit" id="add_itemDispgrpName"
                                                                            class="btn btn-primary add_data"
                                                                            value="add_post">Submit</button>

                                                                    </div>
                                                                </div>

                                                            </form>
                                                        </div>
                                                    </div>




                                                    <!-- edit modal start  -->

                                                    <div class="modal fade add-modal func-add-modal" id="editFunctionality"
                                                        tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <form action="" method="POST" id="add_frm" name="add_frm">
                                                                <input type="hidden" name="editdata" id="editdata" value="">
                                                                <input type="hidden" name="id" id="edititemDisgrpid" value="">

                                                                <div class="modal-content card">
                                                                    <div class="modal-header card-header pt-2 pb-2 px-3">
                                                                        <h4 class="text-xs text-white mb-0">Edit Customer
                                                                            Discount
                                                                            Group</h4>

                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                <div class="form-input mb-3">
                                                                                    <label>Group Name* </label>
                                                                                    <input type="text" class="form-control"
                                                                                        id="edititemdisGrpname"
                                                                                        name="CustomerGroupName" value=""
                                                                                        required>
                                                                                    <span class="error name"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                                                                        <button type="submit" id="update_itemDispgrpName"
                                                                            data-dismiss="modal"
                                                                            class="btn btn-primary update_data"
                                                                            value="update_post">Update</button>
                                                                        <!-- <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_post"><i class="fa fa-plus fontSize"></i> Final Submit</button> -->

                                                                    </div>
                                                                </div>

                                                            </form>
                                                        </div>
                                                    </div>
                                                    <!-- edit modal end -->

                                                    <!-- Global View start-->



                                                    <!-- Global View end -->

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
        </div>
        <!-- /.row -->
        <!-- /.content -->


        <!-- /.Content Wrapper. Contains page content -->
        <!-- For Pegination------->
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
        echo $_REQUEST['pageNo'];
    } ?>">
</form>
<!-- End Pegination from------->


<?php
}
require_once("../common/footer2.php");
?>


<script>
    var input = document.getElementById("myInput");
    input.addEventListener("keypress", function (event) {
        // console.log(event.key)

        if (event.key === "Enter") {
            event.preventDefault();
            // alert("clicked")
            document.getElementById("myBtn").click();
        }
    });
    var form = document.getElementById("search");

    document.getElementById("myBtn").addEventListener("click", function () {
        form.submit();
    });
</script>








<!-----------mobile filter list------------>


<script>
    $(document).ready(function () {
        $("button.page-list").click(function () {
            var buttonId = $(this).attr("id");
            $("#modal-container").removeAttr("class").addClass(buttonId);
            $(".mobile-transform-card").addClass("modal-active");
        });

        $(".btn-close-modal").click(function () {
            $("#modal-container").toggleClass("out");
            $(".mobile-transform-card").removeClass("modal-active");
        });
    })
</script>


<!-- modal view responsive more tabs -->

<script>
    $(document).ready(function () {
        // Adjust tabs based on window size
        adjustTabs();

        // Listen for window resize event
        $(window).resize(function () {
            adjustTabs();
        });
    });

    function adjustTabs() {
        var navTabs = $("#nav-tab");
        var moreDropdown = $("#more-dropdown");

        // Reset nav tabs
        navTabs.children().show();
        moreDropdown.empty();

        // Check if tabs overflow the container
        var visibleTabs = 7; // Number of visible tabs
        if ($(window).width() < 576) { // Adjust for mobile devices
            visibleTabs = 3; // Display only one tab on mobile
        } else if ($(window).width() > 576) {
            visibleTabs = 7;
        } else {
            visibleTabs = 7;
        }


        var hiddenTabs = navTabs.children(":gt(" + (visibleTabs) + ")");

        hiddenTabs.hide().appendTo(moreDropdown);

        // If there are hidden tabs, show the "More" dropdown
        if (hiddenTabs.length > 0) {
            moreDropdown.show();
        } else {
            moreDropdown.hide();
        }
    }
</script>


<!-- datatable and modal script portion  -->

<script>

    $(document).ready(function () {
        var indexValues = [];
        var dataTable;
        let columnMapping = <?php echo json_encode($columnMapping); ?>
        // let dataPaginate;

        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"billList_wrapper"t><ip>',
                "lengthMenu": [10, 25, 50, 100, 200, 250],
                "ordering": false,
                info: false,
                "initComplete": function (settings, json) {
                    $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
                },

                buttons: [{
                    extend: 'collection',
                    text: '<ion-icon name="download-outline"></ion-icon> Export',
                    buttons: [{
                        extend: 'csv',
                        text: '<ion-icon name="document-outline" class="ion-csv"></ion-icon> CSV'
                    }]
                }],
                // select: true,
                "bPaginate": false,
            });

        }
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        var allData;
        var dataPaginate;


        function full_datatable() {
            let fromDate = "<?= $fromDate ?>"; // For Date Filter
            let toDate = "<?= $toDate ?>"; // For Date Filter        
            let comid = <?= $company_id ?>;
            let locId = <?= $location_id ?>;
            let bId = <?= $branch_id ?>;

            $.ajax({
                type: "POST",
                url: "ajaxs/discount/ajax-manage-discount-variation-all.php",
                dataType: 'json',
                data: {
                    act: 'alldata',
                },
                beforeSend: function () {

                },
                success: function (response) {
                    // all_data = response.all_data;
                    allData = response.all_data;


                },
            });
        };
        full_datatable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookieDiscountVariant');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/discount/ajax-manage-discount-variation-all.php",
                dataType: 'json',
                data: {
                    act: 'discountVariationTable',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit
                },
                beforeSend: function () {
                    $("#detailed_tbody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
                },
                success: function (response) {
                    // console.log(response);
                    // alert(response)

                    if (response.status) {
                        var responseObj = response.data;
                        dataPaginate = responseObj;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);
                        $.each(responseObj, function (index, value) {
                            //  $('#item_id').val(value.itemId);

                            dataTable.row.add([
                                `<p>${value.sl_no}</p>`,
                                `<p>${value.customer_discount_group}</p>`,
                                `<p>${value.item_discount_group}</p>`,
                                `<p>${value.discount_percentage}</p>`,
                                `<p>${value.discount_max_value}</p>`,
                                `<p>${value.discount_value}</p>`,
                                `<p>${value.term_of_payment}</p>`,
                                `<p>${formatDate(value.valid_from)}</p>`,
                                `<p>${formatDate(value.valid_upto)}</p>`,
                                `<p>${value.minimum_valueQuantity}</p>`,
                                `<p>${value.coupon}</p>`,
                                `<p>${value.created_by}</p>`,
                                `<p>${formatDate(value.created_at)}</p>`,

                                ` <div class="dropout">
                                     <button class="more">
                                          <span></span>
                                          <span></span>
                                          <span></span>
                                     </button>
                                     <ul>
                                        <li>
                                        <a href="<?= basename($_SERVER['PHP_SELF']) ?>?edit=${value.discount_variant_id}">
                                            <button>
                                                <ion-icon name="create-outline" class="ion-edit"></ion-icon>
                                                Edit
                                            </button>
                                        </a>
                                        </li>
                                     </ul>
                                   
                                 </div>`,
                            ]).draw(false);
                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (checkboxSettings) {
                            var checkedColumns = JSON.parse(checkboxSettings);

                            $(".settingsCheckbox_detailed").each(function (index) {
                                var columnVal = $(this).val();
                                if (checkedColumns.includes(columnVal)) {
                                    $(this).prop("checked", true);
                                    dataTable.column(index).visible(true);

                                } else {
                                    notVisibleColArr.push(index);
                                }
                            });
                            // console.log("notVisibleColArr index:", notVisibleColArr);
                            if (notVisibleColArr.length > 0) {
                                notVisibleColArr.forEach(function (index) {
                                    dataTable.column(index).visible(false);
                                });
                            }


                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function (index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);

                                }
                            });
                        }
                    } else {

                        $("#detailed_tbody").html(`<td colspan=17 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
                        $('#yourDataTable_paginate').remove();
                        $('#limitText').remove();
                    }
                }
            });
        }

        fill_datatable();




        $(document).on("click", ".ion-paginationlistDisVariant", function (e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieDiscountVariant')
                },
                // beforeSend:function(){
                //     console.log(sql_data_checkbox);
                // },

                success: function (response) {
                    var blob = new Blob([response.csvContentpage], {
                        type: 'text/csv'
                    });

                    var url = URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = '<?= $newFileName ?>';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);


                }
            })

        });
        $(document).on("click", ".ion-fulllistitemDisVariant", function (e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'fullliststock',
                    data: JSON.stringify(allData),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieDiscountVariant')
                },

                beforeSend: function () {
                },
                success: function (response) {
                    var blob = new Blob([response.csvContentall], {
                        type: 'text/csv'
                    });

                    var url = URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = '<?= $newFileNameDownloadall ?>';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);


                }
            })

        });



        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function (e) {
            var maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);
        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a", function (e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $("#disVariantGroupLimit").val();
            //    console.log(limitDisplay);
            fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay);

        });

        //<--------------advance search------------------------------->
        $(document).ready(function () {
            $(document).on("click", "#serach_submit", function (event) {
                event.preventDefault();
                let values;
                $(".selectOperator").each(function () {
                    let columnIndex = ($(this).attr("id")).split("_")[1];
                    let columnSlag = $(`#columnSlag_${columnIndex}`).val();
                    let operatorName = $(`#selectOperator_${columnIndex}`).val();
                    let value = $(`#value_${columnIndex}`).val() ?? "";
                    let value2 = $(`#value2_${columnIndex}`).val() ?? "";
                    let value3 = $(`#value3_${columnIndex}`).val() ?? "";
                    let value4 = $(`#value4_${columnIndex}`).val() ?? "";

                    if (columnSlag === 'created_at') {
                        values = value4;
                    }
                    else if (columnSlag === 'valid_from') {
                        values = value2;
                    }
                    else if (columnSlag === 'valid_to') {
                        values = value3;
                    }

                    if ((columnSlag === 'updated_at' || columnSlag === 'created_at') && operatorName == "BETWEEN") {
                        formInputs[columnSlag] = {
                            operatorName,
                            value: {
                                fromDate: value,
                                toDate: values
                            }
                        };
                    } else {
                        formInputs[columnSlag] = {
                            operatorName,
                            value
                        };
                    }
                });

                $('#btnSearchCollpase_modal').modal('hide');
                // console.log("FormInputs:", formInputs);

                fill_datatable(formDatas = formInputs);
            });



        });


        // -------------checkbox----------------------

        $(document).ready(function () {
            var columnMapping = <?php echo json_encode($columnMapping); ?>;

            var indexValues = [];

            function toggleColumnVisibility(columnIndex, checkbox) {
                var column = dataTable.column(columnIndex);
                column.visible(checkbox.checked);

            }

            $("input[name='settingsCheckbox[]']").change(function () {
                var columnVal = $(this).val();
                // console.log(columnVal);

                var index = columnMapping.findIndex(function (column) {
                    return column.slag === columnVal;
                });
                // console.log(index);
                toggleColumnVisibility(index, this);
            });

            $(".grand-checkbox").on("click", function () {
                $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);
                $("input[name='settingsCheckbox[]']").each(function () {
                    var columnVal = $(this).val();
                    // console.log(columnVal);
                    var index = columnMapping.findIndex(function (column) {
                        return column.slag === columnVal;
                    });
                    if ($(this).is(':checked')) {
                        indexValues.push(index);
                    } else {
                        var removeIndex = indexValues.indexOf(index);
                        if (removeIndex !== -1) {
                            indexValues.splice(removeIndex, 1);
                        }
                    }
                    toggleColumnVisibility(index, this);
                });
            });

        });

    });

    //    -------------- save cookies--------------------

    $(document).ready(function () {
        $(document).on("click", "#check-box-submt", function (event) {
            // console.log("Hiiiii");
            event.preventDefault();
            // $("#myModal1").modal().hide();
            $('#btnSearchCollpase_modal').modal('hide');
            var tablename = $("#tablename").val();
            var pageTableName = $("#pageTableName").val();
            var settingsCheckbox = [];
            var fromData = {};
            $(".settingsCheckbox_detailed").each(function () {
                if ($(this).prop('checked')) {
                    var chkBox = $(this).val();
                    settingsCheckbox.push(chkBox);
                    fromData = {
                        tablename,
                        pageTableName,
                        settingsCheckbox
                    };
                }
            });

            // console.log(fromData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: 'json',
                    data: {
                        act: 'discountvariant',
                        fromData: fromData
                    },
                    success: function (response) {
                        console.log(response);
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer: 1000,
                            showConfirmButton: false,
                        })
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }
        });



    });
</script>

<!-- datatable and modal portion script ⬆️ -->




<!-- -----fromDate todate input add--- -->


<script>
    $(document).ready(function () {
        $(document).on("change", ".selectOperator", function () {
            let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
            let operatorName = $(this).val();
            let columnName = $(`#columnName_${columnIndex}`).html().trim();
            let inputContainer = $(`#td_${columnIndex}`);
            let inputId;
            if (columnName === 'Created At') {
                inputId = "value4_" + columnIndex;
            } else if (columnName === 'Valid From') {
                inputId = "value2_" + columnIndex;
            }
            else if (columnName === 'Valid Upto') {
                inputId = "value3_" + columnIndex;
            }

            if ((columnName === 'Created At' || columnName === 'Valid From' || columnName === 'Valid Upto') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            // console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
        });

    });
</script>


<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>

<!-- other params isset script portion here  -->

<script>
    function table_settings() {
        var favorite = [];
        $.each($("input[name='settingsCheckbox[]']:checked"), function () {
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

    $(document).ready(function () {

        checkDiscountType();


        // for editing page script

        $("#discount_type_edit").on('change', function () {

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




        $('input[name="discount_type"]').change(function () {

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



        $("#coupon_serial").keyup(function () {

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

                beforeSend: function () {
                    //$("#warehouseDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function (response) {
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

        $(document).on('keyup', '#discount_val', function () {
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


        $(document).on('keyup', '#discount_max_val', function () {
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

        $(document).on('keyup', '#min_val', function () {
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


<!-- other portion isset script portion here ⬆️ -->



<script>
    $(document).ready(function () {
        $('input[name="discount"]').change(function () {
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
        el.addEventListener("blur", function () {
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
    $(document).ready(function () {
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

        $(document).on("change", "#valid_from", function () {
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



        $(document).on("blur", "#valid_upto", function () {

            var fromDate = new Date($('#valid_from').val());
            var toDate = new Date($(this).val());
            if ($('#valid_from').val() && $('#valid_upto').val()) {
                if (toDate < fromDate) {
                    alert('From Date cannot be greater than To Date');
                    $(this).val('');
                }
            }

        });

        $(document).on("click", "#btnSearchCollpase", function () {
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


        $(".add_data").click(function () {
            var data = this.value;
            $("#createDiscountVarient").val(data);
            //confirm('Are you sure to Submit?')
            $("#SubmitForm").submit();
        });


        $(".edit_data").click(function () {
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

        $("#height").keyup(function () {
            calculate_volume();
        });
        $("#width").keyup(function () {
            calculate_volume();
        });
        $("#length").keyup(function () {
            calculate_volume();
        });


        $("#buomDrop").change(function () {
            let res = $(this).val();
            $("#buom").val(res);
            console.log("buomDrop", res);
        });

        $("#iuomDrop").change(function () {
            let rel = $(this).val();
            $("#ioum").val(rel);
            console.log("iuomDrop", rel);
        });



    });
</script>



<script>
    var input = document.getElementById("myInput");
    input.addEventListener("keypress", function (event) {
        // console.log(event.key)

        if (event.key === "Enter") {
            event.preventDefault();
            // alert("clicked")
            document.getElementById("myBtn").click();
        }
    });
    var form = document.getElementById("search");

    document.getElementById("myBtn").addEventListener("click", function () {
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


<script>
    function openFullscreen() {
        var elem = document.getElementById("listTabPan")

        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                /* Safari */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                /* IE11 */
                elem.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                /* Safari */
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                /* IE11 */
                document.msExitFullscreen();
            }
        }
    }

    document.addEventListener('fullscreenchange', exitHandler);
    document.addEventListener('webkitfullscreenchange', exitHandler);
    document.addEventListener('MSFullscreenChange', exitHandler);

    function exitHandler() {
        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
            $(".content-wrapper").removeClass("fullscreen-mode");
        } else {
            $(".content-wrapper").addClass("fullscreen-mode");
        }
    }
</script>