<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-items-controller.php");
require_once("../../app/v1/functions/branch/func-mrp-controller.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");

//console($_SESSION);
//console($_SESSION['logedBranchAdminInfo']['fldAdminBranchId']);
//console(date("Y-m-d H:i:s"));
$discountController = new MRPController();
$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();

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

if (isset($_POST["createMRPVariant"])) {



    $addNewObj = $discountController->createMRPVariant($_POST);
    swalToast($addNewObj["status"], $addNewObj["message"]);
}


if (isset($_POST['editMRPVariant'])) {
    $addNewObj = $discountController->editMRPVariant($_POST);
    swalToast($addNewObj["status"], $addNewObj["message"], BASE_URL . "branch/location/manage-mrp-variant.php");
}

// if (isset($_POST["editgoodsdata"])) {
//   $addNewObj = $warehouseController->editGoods($_POST);
//   swalToast($addNewObj["status"], $addNewObj["message"]);
// }

if (isset($_POST["add-table-settings"])) {
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

    .is-mrp-varient div.values {
        display: grid;
        place-items: flex-end;
        grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr;
        align-items: center;
        gap: 8px;
        font-size: 0.7rem;
        font-weight: 600;
        background: #0030601a;
        padding: 0 15px;
        border-radius: 7px;
        max-height: 170px;
        overflow: auto;
    }

    .is-mrp-varient div.values .value-item {
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .is-mrp-varient div.values button {
        transform: scale(0.7);
        font-size: 1rem;
    }

    .head-item-table #quick-add-input.show {
        transform: translateX(55%) !important;
    }

    .advanced-serach .nav-action {
        flex-direction: row;
        gap: 30px;
        width: 35% !important;
    }

    .advanced-serach .form-inline {
        flex-flow: row;
    }

    .advanced-serach .form-inline select {
        width: 120px !important;
    }

    div#quick-add-input span.select2.select2-container.select2-container--default {
        width: 120px !important;
    }

    .head-item-table #quick-add-input.show {
        transform: translateX(55%) !important;
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

    .head-item-table #quick-add-input.show {
        transform: translateX(55%) !important;
    }


    .is-mrp-varient .head-item-table {
        display: flex;
        justify-content: space-between;
        padding: 10px 15px;
        flex-direction: column;
        gap: 10px;
    }

    .mrp-variant-modalbody .form-input {
        height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .mrp-variant-modalbody .form-input label {
        display: flex;
        flex-direction: column;
    }

    .mrp-variant-modalbody .form-input label.height-label {
        height: auto;
    }

    .mrp-variant-modalbody .row.dotted-border-area {
        margin: 10px 5px 20px;
        position: relative;
        align-items: baseline;
    }

    .mrp-variant-modalbody .row.dotted-border-area label.float-label {
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

    .is-mrp-varient .mrp-grp-select .select2-container {
        width: 100% !important;
    }

    .is-mrp-varient .stock-setup-modal .modal-body {
        height: auto;
        max-height: 378px;
    }
    .flex-start {
        justify-content: flex-start !important;
    }
</style>


<?php

if (isset($_GET['create'])) {
    $getAllItemsObj = queryGet("SELECT * FROM `erp_inventory_items` WHERE `company_id` = $company_id", true);
    // console($getAllItemsObj);

?>



    <div class="content-wrapper is-mrp-varient">
        <section class="content">
            <div class="container-fluid">


                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Rate Variant List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Rate Variant</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>


                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                    <input type="hidden" name="createMRPVariant" id="createMRPVariant" value="">
                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

                    <div class="modal-content card">
                        <div class="modal-header card-header pt-2 pb-2 px-3">
                            <h4 class="text-xs text-white mb-0">Create Rate Variant</h4>
                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                        </div>
                        <div class="modal-body mrp-variant-modalbody py-2 px-3">
                            <div class="row align-items-center">
                                <div class="col-lg-10 col-md-10 col-sm-12">
                                    <div class="row dotted-border-area">
                                        <label for="" class="float-label">MRP Type</label>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-inline my-3 gap-2">
                                                <input type="radio" name="type" value="customer">
                                                <label for="" class="height-label">Customer Group</label>
                                                <span class="label-note">
                                                    Select customer group to create a rate for any goods. This rate will be applicabe for all customer belongs to this group during Quotation, Sales Order and Invoice.
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-inline my-3 gap-2">
                                                <input type="radio" name="type" value="territory">
                                                <label for="" class="height-label">Territory Group</label>
                                                <span class="label-note">
                                                    Select territory to create a rate for any goods. This rate will be applicabe for all customer belongs to this territory during Quotation, Sales Order and Invoice.
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 mrp-grp-select">
                                            <div class="form-input mb-3" id="customer_div" style="display:none;">
                                                <label>Customer MRP Group* </label>
                                                <select id="customer_group" name="customer_group" class="form-control">
                                                    <option value="">SELECT CUSTOMER DISCOUNT GROUP</option>
                                                    <?php

                                                    $pr_query = "SELECT * FROM `erp_customer_mrp_group` WHERE company_id = '$company_id' ";
                                                    $pr_query_list = queryGet($pr_query, true);
                                                    $pr_list = $pr_query_list['data'];
                                                    foreach ($pr_list as $pr_row) {
                                                    ?>
                                                        <option value="<?= $pr_row['customer_mrp_group_id'] ?>"><?= $pr_row['customer_mrp_group'] ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <span class="error customer_group"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 mrp-grp-select">
                                            <div class="form-input" id="territory_div" style="display:none;">
                                                <label>Territory</label>
                                                <select id="territory" name="territory" class="form-control">
                                                    <option value="">SELECT Territory</option>
                                                    <?php
                                                    $query = "SELECT * FROM `erp_mrp_territory` WHERE company_id = '$company_id' ";
                                                    $query_list = queryGet($query, true);
                                                    $list = $query_list['data'];
                                                    foreach ($list as $row) {
                                                    ?>
                                                        <option value="<?= $row['territory_id'] ?>"><?= $row['territory_name'] ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>

                                                <span class="error territory"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-12">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-input">
                                                <label>Applicable From</label>
                                                <input type="date" class="form-control" id="valid_from" name="valid_from">
                                                <span class="error valid_from"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="form-input">
                                                <label>Valid Till</label>
                                                <input type="date" class="form-control" id="valid_till" name="valid_till">
                                                <span class="error valid_till"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>









                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card items-select-table">
                                    <div class="head-item-table">

                                        <div class="advanced-serach">
                                            <div class="hamburger quickadd-hamburger">
                                                <div class="wrapper-action">
                                                    <i class="fa fa-plus"></i>
                                                </div>
                                            </div>

                                            <div class="nav-action quick-add-input" id="quick-add-input">
                                                <div class="form-inline">
                                                    <label for="date">Group</label>
                                                    <select onclick="" name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                        <option value="">Select</option>
                                                        <?php
                                                        $sql = queryGet("SELECT * FROM `erp_inventory_mstr_good_groups` WHERE `companyId` = $company_id AND (`goodType` = 3 OR `goodType` = 4)", true);

                                                        foreach ($sql['data'] as $data) {
                                                        ?>
                                                            <option value="<?= $data['goodGroupId'] ?>"><?= $data['goodGroupName'] ?></option>
                                                        <?php }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-inline">
                                                    <label for=""><span class="text-danger">*</span>Quick Add </label>
                                                    <select id="itemsDropDown" class="form-control">
                                                        <option value="">Items</option>
                                                        <?php
                                                        foreach ($getAllItemsObj['data'] as $getItems) {
                                                        ?>
                                                            <option value="<?= $getItems["itemId"] ?>">[<?= $getItems["itemCode"] ?>]<?= $getItems["itemName"] ?></option>

                                                        <?php
                                                        }
                                                        ?>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <table class="table tabel-hover table-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Item Code</th>
                                                    <th>Item Name</th>
                                                    <th>Stock</th>
                                                    <th>Cost</th>
                                                    <th>Margin</th>
                                                    <th>MRP</th>

                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemsTable">

                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary add_data mrp_add_btn" value="add_post">Submit</button>
                            </div>
                        </div>
                </form>

            </div>
        </section>
    </div>





<?php } else if (isset($_GET['edit'])) {
    $mrp_id = $_GET['edit'];
    $mrp_sql = queryGet("SELECT * FROM `erp_mrp_variant` WHERE `mrp_id` = $mrp_id");
    //console($mrp_sql);
?>


    <div class="content-wrapper is-mrp-varient">
        <section class="content">
            <div class="container-fluid">


                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Rate Variant List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Edit Rate Variant</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>


                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="edit_frm" name="edit_frm">
                    <input type="hidden" name="editMRPVariant" id="editMRPVariant" value="">
                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
                    <input type="hidden" name="mrpd" id="mrpd" value="<?= $mrp_id ?>">


                    <div class="modal-content card">
                        <div class="modal-header card-header pt-2 pb-2 px-3">
                            <h4 class="text-xs text-white mb-0">Edit Rate Variant</h4>
                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                        </div>
                        <div class="modal-body py-2 px-3">
                            <div class="row">

                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-input mb-3">
                                        <label>MRP Type</label>
                                        <div class="form-inline gap-3">
                                            <div class="form-inline gap-2">
                                                <input type="radio" name="type" value="customer" <?php if ($mrp_sql['data']['type'] == "customer") {
                                                                                                        echo 'checked';
                                                                                                    } ?> disabled>
                                                <label for="">Customer Group</label>
                                            </div>
                                            <div class="form-inline gap-2">
                                                <input type="radio" name="type" value="territory" <?php if ($mrp_sql['data']['type'] == "territory") {
                                                                                                        echo 'checked';
                                                                                                    } ?> disabled>
                                                <label for="">Territory Group</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                if ($mrp_sql['data']['type'] == "customer") {
                                ?>
                                    <div class="col-lg-6 col-md-6 col-sm-6" id="customer_div">
                                        <div class="form-input mb-3">
                                            <label>Customer MRP Group* </label>
                                            <select id="customer_group" name="customer_group" class="fld form-control m-input">
                                                <option value="">SELECT CUSTOMER DISCOUNT GROUP</option>
                                                <?php

                                                $pr_query = "SELECT * FROM `erp_customer_mrp_group` WHERE company_id = '$company_id' ";
                                                $pr_query_list = queryGet($pr_query, true);
                                                $pr_list = $pr_query_list['data'];
                                                foreach ($pr_list as $pr_row) {
                                                ?>
                                                    <option value="<?= $pr_row['customer_mrp_group_id'] ?>" <?php if ($pr_row['customer_mrp_group_id'] == $mrp_sql['data']['customer_group']) {
                                                                                                                echo 'selected';
                                                                                                            }  ?>><?= $pr_row['customer_mrp_group'] ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                            <span class="error customer_group"></span>
                                        </div>
                                    </div>
                                <?php
                                } else {
                                ?>


                                    <div class="col-lg-6 col-md-6 col-sm-6" id="territory_div">
                                        <div class="form-input">

                                            <label>Territory</label>

                                            <select id="territory" name="territory" class="fld form-control m-input">
                                                <option value="">SELECT Territory</option>
                                                <?php
                                                $query = "SELECT * FROM `erp_mrp_territory` WHERE company_id = '$company_id' ";
                                                $query_list = queryGet($query, true);
                                                $list = $query_list['data'];
                                                foreach ($list as $row) {
                                                ?>
                                                    <option value="<?= $row['territory_id'] ?>" <?php if ($row['territory_id'] == $mrp_sql['data']['territory']) {
                                                                                                    echo 'selected';
                                                                                                }  ?>><?= $row['territory_name'] ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>

                                            <span class="error territory"></span>
                                        </div>

                                    </div>

                                <?php
                                }
                                ?>

                            </div>


                            <div class="row">

                                <div class="col-lg-5 col-md-5 col-sm-5">
                                    <div class="form-input mb-3">
                                        <label>Valid From</label>
                                        <input type="date" class="form-control" id="valid_from" name="valid_from" value="<?= $mrp_sql['data']['valid_from'] ?>">
                                        <span class="error valid_from"></span>
                                    </div>
                                </div>

                                <div class="col-lg-5 col-md-5 col-sm-5">
                                    <div class="form-input mb-3">
                                        <label>Valid Till</label>
                                        <input type="date" class="form-control" min="<?= $mrp_sql['data']['valid_from'] ?>" id="valid_till" name="valid_till" value="<?= $mrp_sql['data']['valid_till'] ?>">
                                        <span class="error valid_till"></span>
                                    </div>
                                </div>





                            </div>









                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card items-select-table">
                                <div class="head-item-table">

                                    <!-- <div class="advanced-serach">
                                        <div class="hamburger quickadd-hamburger">
                                            <div class="wrapper-action">
                                                <i class="fa fa-plus"></i>
                                            </div>
                                        </div>

                                        <div class="nav-action quick-add-input" id="quick-add-input">
                                            <div class="form-inline">
                                                <label for="date">Use Types</label>
                                                <select onclick="" name="usetypesDropdown" id="usetypesDropdown" class="form-control typesDropdown">
                                                    <option value="">Select</option>
                                                    <?php
                                                    $sql = queryGet("SELECT * FROM `erp_inventory_mstr_good_groups` WHERE `companyId` = $company_id", true);

                                                    foreach ($sql['data'] as $data) {
                                                    ?>
                                                        <option value="<?= $data['goodGroupId'] ?>"><?= $data['goodGroupName'] ?></option>
                                                    <?php }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-inline">
                                                <label for=""><span class="text-danger">*</span>Quick Add </label>
                                                <select id="itemsDropDown" class="form-control">
                                                    <option value="">Items</option>

                                                </select>
                                            </div>
                                        </div>
                                    </div> -->

                                    <table class="table tabel-hover table-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Stock</th>
                                                <th>Cost</th>
                                                <th>Margin</th>
                                                <th>MRP</th>


                                            </tr>
                                        </thead>
                                        <tbody id="">

                                            <?php

                                            $item_sql = queryGet("SELECT * FROM `erp_mrp_variant_items` WHERE `mrp_id` = $mrp_id", true);
                                            //console($item_sql);

                                            foreach ($item_sql['data'] as $item_data) {
                                                // console($item_data['cost']);
                                                $mrp_item_id = $item_data['mrp_item_id'];

                                                $itemId = $item_data['item_id'];
                                                $getItemObj = $ItemsObj->getItemById($itemId);
                                                //  console($getItemObj);
                                                $itemCode = $getItemObj['data']['itemCode'];
                                                $lastPricesql = "SELECT * FROM `erp_branch_purchase_order_items`as po_item JOIN `erp_branch_purchase_order` as po ON po_item.`po_id`=po.po_id WHERE `location_id`=$location_id AND `itemCode`=$itemCode ORDER BY po_item.`po_item_id` DESC LIMIT 1";


                                                $last = queryGet($lastPricesql);
                                                $lastRow = $last['data'] ?? "";
                                                $lastPrice = $lastRow['unitPrice'] ?? "0";

                                                $randCode = $getItemObj['data']['itemId'] . rand(00, 99);
                                                $hsn = $getItemObj['data']['hsnCode'];
                                                $gstPercentage = queryGet("SELECT * FROM `erp_hsn_code` WHERE `hsnCode` = '" . $hsn . "'");
                                                // console($randCode);
                                                $gstAmount = ($gstPercentage['data']['taxPercentage'] / 100) * $lastPrice;
                                                $totalAmount = $lastPrice + $gstAmount;

                                            ?>
                                                <input type="hidden" name="listItem[<?= $randCode ?>][mrp_item_id]" value="<?= $mrp_item_id ?>">
                                                <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
                                                    <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
                                                    <td>
                                                        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
                                                        <?= $getItemObj['data']['itemCode'] ?>
                                                    </td>

                                                    <td>
                                                        <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
                                                        <p class="pre-normal"><?= $getItemObj['data']['itemName'] ?></p>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <!-- name="listItem[<?= $randCode ?>][stockQty]"  -->
                                                            <!-- <span class="rupee-symbol currency-symbol currency-symbol-dynamic pr-1">#</span>
                    <select name="listItem[<?= $randCode ?>][stockQty]" class="form-control checkQty" id="checkQty_<?= $randCode ?>">
                        <option value="FgWhOpen_<?= $getItemSummaryObj['fgWhOpen'] ?>">FG Warehouse (<?= $getItemSummaryObj['fgWhOpen'] ?>)</option>
                        <option value="FgMktOpen_<?= $getItemSummaryObj['fgMktOpen'] ?>">FG Mkt Location (<?= $getItemSummaryObj['fgMktOpen'] ?>)</option>
                    </select> -->
                                                            <?php
                                                            // $qtyObj = $BranchSoObj->deliveryCreateItemQty($getItemObj['data']['itemId']);

                                                            $qtyObj = $BranchSoObj->itemQtyStockCheck($getItemObj['data']['itemId'],  "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", "DESC", '', $creationDate);

                                                            // console($qtyObj);
                                                            $sumOfBatches = $qtyObj['sumOfBatches'];
                                                            $batchesDetails = $BranchSoObj->convertToWHSLBatchArray($qtyObj['data']);
                                                            // console($itemQtyStockCheck);
                                                            ?>
                                                            <input type="hidden" name="listItem[<?= $randCode ?>][stockQty]" class="form-control checkQty" id="checkQty_<?= $randCode ?>" value="<?= $sumOfBatches; ?>">

                                                            <!-- Button to Open the Modal -->
                                                            <div class="qty-modal py-2">
                                                                <p class="font-bold text-center checkQtySpan" id="checkQtySpan_<?= $randCode ?>"><?= $sumOfBatches; ?></p>
                                                                <hr class="my-2 w-50 mx-auto">
                                                                <div class="text-xs d-flex align-items-center gap-2 justify-content-center">
                                                                    <p class="itemSellType" id="itemSellType_<?= $randCode ?>">CUSTOM</p>
                                                                    <ion-icon name="create-outline" class="stockBtn" id="stockBtn_<?= $randCode ?>" data-bs-toggle="modal" data-bs-target="#stockSetup<?= $randCode ?>" style="cursor: pointer;"></ion-icon>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" class="itemSellTypeHidden" id="itemSellTypeHidden_<?= $randCode ?>" name="listItem[<?= $randCode ?>][itemSellType]" value="CUSTOM">

                                                            <!-- The Modal -->
                                                            <div class="modal fade stock-setup-modal" id="stockSetup<?= $randCode ?>">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">

                                                                        <!-- Modal Header -->
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title text-sm text-white">Stock Setup (CUSTOM)</h4>
                                                                        </div>

                                                                        <!-- Modal body -->
                                                                        <div class="modal-body">

                                                                            <!-- start warehouse accordion -->
                                                                            <div class="modal-select-type my-3">
                                                                                <!-- <div class="type type-one">
                                            <input type="radio" name="listItem[<?= $randCode ?>][itemreleasetype]" class="itemreleasetypeclass fifo" data-rdcode="<?= $randCode ?>" value="FIFO" id="fifo_<?= $randCode ?>" <?php if ($masterItemDetails['item_sell_type'] == "FIFO") {
                                                                                                                                                                                                                                echo "checked";
                                                                                                                                                                                                                            } ?>>
                                            <label for="fifo" class="text-xs mb-0">FIFO</label>
                                        </div>
                                        <div class="type type-two">
                                            <input type="radio" name="listItem[<?= $randCode ?>][itemreleasetype]" class="itemreleasetypeclass lifo" data-rdcode="<?= $randCode ?>" value="LIFO" id="lifo_<?= $randCode ?>" <?php if ($masterItemDetails['item_sell_type'] == "LIFO") {
                                                                                                                                                                                                                                echo "checked";
                                                                                                                                                                                                                            } ?>>
                                            <label for="lifo" class="text-xs mb-0">LIFO</label>
                                        </div> -->
                                                                                <div class="type type-three">
                                                                                    <input type="radio" name="listItem[<?= $randCode ?>][itemreleasetype]" class="itemreleasetypeclass custom" data-rdcode="<?= $randCode ?>" value="CUSTOM" id="custom_<?= $randCode ?>" checked>
                                                                                    <label for="custom" class="text-xs mb-0 text-muted">Custom</label>
                                                                                </div>
                                                                            </div>
                                                                            <!-- <div class="textarea-note my-2">
                                        <textarea class="form-control" cols="6" rows="20" placeholder="notes...."></textarea>
                                      </div> -->
                                                                            <div class="customitemreleaseDiv<?= $randCode ?>">
                                                                                <?php
                                                                                // console($qtyObj);
                                                                                // console($batchesDetails);
                                                                                foreach ($batchesDetails as $whKey => $wareHouse) {
                                                                                ?>
                                                                                    <div class="accordion accordion-flush warehouse-accordion p-0" id="accordionFlushExample">
                                                                                        <div class="accordion-item">
                                                                                            <h2 class="accordion-header w-100" id="flush-headingOne">
                                                                                                <button class="accordion-button btn btn-primary warehouse-header waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $whKey ?>" aria-expanded="true" aria-controls="flush-collapseOne">
                                                                                                    <?= $wareHouse['warehouse_code'] ?> | <?= $wareHouse['warehouse_name'] ?>
                                                                                                </button>
                                                                                            </h2>
                                                                                            <div id="collapse<?= $whKey ?>" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" style="">
                                                                                                <div class="accordion-body p-0">
                                                                                                    <h1></h1>
                                                                                                    <div class="card bg-transparent">
                                                                                                        <div class="card-body px-2 mx-3" style="background-color: #f9f9f9;">
                                                                                                            <!-- start location accordion -->
                                                                                                            <?php foreach ($wareHouse['storage_locations'] as $locationKey => $location) {
                                                                                                                if ($_GET["prod"] == "1") {
                                                                                                                    if ($location["storage_location_type"] == "RM-PROD") {
                                                                                                            ?>

                                                                                                                        <div id="locAccordion">
                                                                                                                            <div class="card bg-transparent">
                                                                                                                                <div class="card-header p-2 border rounded-0 bg-transparent border-0 border-bottom">
                                                                                                                                    <a class="btn text-dark w-100 storage-after" data-bs-toggle="collapse" href="#collapse<?= $whKey ?><?= $locationKey ?>">
                                                                                                                                        <?= $location['storage_location_code'] ?> | <?= $location['storage_location_name'] ?>
                                                                                                                                    </a>
                                                                                                                                </div>
                                                                                                                                <div id="collapse<?= $whKey ?><?= $locationKey ?>" class="collapse" data-bs-parent="#locAccordion">
                                                                                                                                    <div class="card-body bg-light mx-3">
                                                                                                                                        <?php
                                                                                                                                        // console($location['batches']);
                                                                                                                                        foreach ($location['batches'] as $batchKey => $batch) {
                                                                                                                                            // $batchItemUom = $ItemsObj->getBaseUnitMeasureById($batch['itemUom'])['data']['uomName'];
                                                                                                                                            $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                                                                        ?>
                                                                                                                                            <div class="storage-location mb-2">
                                                                                                                                                <div class="input-radio">
                                                                                                                                                    <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                                                                        <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?= $batch['logRef'] ?>" data-mrp="<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>" <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            } ?>>
                                                                                                                                                    <?php } else { ?>
                                                                                                                                                        <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?= $batch['logRef'] ?>" <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                                                                                                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                                                                                                                                                                                                                                        } ?> data-mrp="<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>" disabled>
                                                                                                                                                    <?php } ?>
                                                                                                                                                </div>
                                                                                                                                                <div class="d-grid">
                                                                                                                                                    <p class="text-sm mb-2">
                                                                                                                                                        <?= $batch['logRef'] ?>
                                                                                                                                                    </p>
                                                                                                                                                    <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                                                                        <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= $batch['itemQty'] ?> <?= $uomName ?> </span>
                                                                                                                                                    </p>
                                                                                                                                                </div>
                                                                                                                                                <div class="input">

                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <hr>
                                                                                                                                        <?php } ?>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>

                                                                                                                    <?php
                                                                                                                    } else {
                                                                                                                    ?>

                                                                                                                        <div id="locAccordion">
                                                                                                                            <div class="card bg-transparent">
                                                                                                                                <div class="card-header p-2 border rounded-0 bg-transparent border-0 border-bottom">
                                                                                                                                    <a class="btn text-dark w-100 storage-after" data-bs-toggle="collapse" href="#collapse<?= $whKey ?><?= $locationKey ?>">
                                                                                                                                        <?= $location['storage_location_code'] ?> | <?= $location['storage_location_name'] ?>
                                                                                                                                    </a>
                                                                                                                                </div>
                                                                                                                                <div id="collapse<?= $whKey ?><?= $locationKey ?>" class="collapse" data-bs-parent="#locAccordion">
                                                                                                                                    <div class="card-body bg-light mx-3">
                                                                                                                                        <?php
                                                                                                                                        // console($location['batches']);
                                                                                                                                        foreach ($location['batches'] as $batchKey => $batch) {
                                                                                                                                            // $batchItemUom = $ItemsObj->getBaseUnitMeasureById($batch['itemUom'])['data']['uomName'];
                                                                                                                                            $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                                                                        ?>
                                                                                                                                            <div class="storage-location mb-2">
                                                                                                                                                <div class="input-radio">
                                                                                                                                                    <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                                                                        <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?= $batch['logRef'] ?>" data-mrp="<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>" <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            } ?>>
                                                                                                                                                    <?php } else { ?>
                                                                                                                                                        <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?= $batch['logRef'] ?>" data-mrp="<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>" <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                echo 'checked';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            } ?> disabled>
                                                                                                                                                    <?php } ?>
                                                                                                                                                </div>
                                                                                                                                                <div class="d-grid">
                                                                                                                                                    <p class="text-sm mb-2">
                                                                                                                                                        <?= $batch['logRef'] ?>
                                                                                                                                                    </p>
                                                                                                                                                    <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                                                                        <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= $batch['itemQty'] ?> <?= $uomName ?> </span>
                                                                                                                                                    </p>
                                                                                                                                                </div>
                                                                                                                                                <div class="input">

                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <hr>
                                                                                                                                        <?php } ?>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>


                                                                                                                    <?php
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    ?>
                                                                                                                    <div id="locAccordion">
                                                                                                                        <div class="card bg-transparent">
                                                                                                                            <div class="card-header p-2 border rounded-0 bg-transparent border-0 border-bottom">
                                                                                                                                <a class="btn text-dark w-100 storage-after" data-bs-toggle="collapse" href="#collapse<?= $whKey ?><?= $locationKey ?>">
                                                                                                                                    <?= $location['storage_location_code'] ?> | <?= $location['storage_location_name'] ?>
                                                                                                                                </a>
                                                                                                                            </div>
                                                                                                                            <div id="collapse<?= $whKey ?><?= $locationKey ?>" class="collapse" data-bs-parent="#locAccordion">
                                                                                                                                <div class="card-body bg-light mx-3">
                                                                                                                                    <?php
                                                                                                                                    // console($location['batches']);
                                                                                                                                    foreach ($location['batches'] as $batchKey => $batch) {

                                                                                                                                        // $batchItemUom = $ItemsObj->getBaseUnitMeasureById($batch['itemUom'])['data']['uomName'];
                                                                                                                                        $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                                                                    ?>
                                                                                                                                        <div class="storage-location mb-2">
                                                                                                                                            <div class="input-radio">
                                                                                                                                                <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                                                                    <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?= $batch['logRef'] ?>" data-mrp="<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>" <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } ?>>
                                                                                                                                                <?php } else { ?>
                                                                                                                                                    <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?= $batch['logRef'] ?>" data-mrp="<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>" <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            echo 'checked';
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } ?> disabled>
                                                                                                                                                <?php } ?>
                                                                                                                                            </div>
                                                                                                                                            <div class="d-grid">
                                                                                                                                                <p class="text-sm mb-2">
                                                                                                                                                    <?= $batch['logRef'] ?>
                                                                                                                                                </p>
                                                                                                                                                <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                                                                    <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= $batch['itemQty'] ?> <?= $uomName ?> </span>
                                                                                                                                                </p>
                                                                                                                                            </div>
                                                                                                                                            <div class="input">

                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        <hr>
                                                                                                                                    <?php } ?>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>

                                                                                                            <?php

                                                                                                                }
                                                                                                            } ?>



                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php } ?>
                                                                            </div>
                                                                            <!-- end warehouse accordion -->
                                                                        </div>

                                                                        <!-- Modal footer -->
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-success proceed" id="proceed_<?= $randCode ?>" data-bs-dismiss="modal">Proceed >></button>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][sumOfBatches]" value="<?= $sumOfBatches ?>">

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="any" name="listItem[<?= $randCode ?>][cost]" class="form-control full-width-center cost" id="cost_<?= $randCode ?>" data-attr="<?= $randCode ?>" value="<?= $item_data['cost'] ?>">


                                                    </td>

                                                    <td>
                                                        <input type="number" step="any" name="listItem[<?= $randCode ?>][margin]" class="form-control full-width-center margin" id="margin_<?= $randCode ?>" data-attr="<?= $randCode ?>" value="<?= $item_data['margin'] ?>">
                                                    </td>

                                                    <td>
                                                        <input type="number" step="any" name="listItem[<?= $randCode ?>][mrp]" class="form-control full-width-center mrp" id="mrp_<?= $randCode ?>" value="<?= $item_data['mrp'] ?>">
                                                    </td>



                                                </tr>
                                            <?php
                                            }
                                            ?>
























                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>









                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary edit_data mrp_edit_btn" value="edit_post">Submit</button>
                        </div>
                    </div>
                </form>

            </div>
        </section>
    </div>




<?php
} else {


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
                                    <h3 class="card-title">Manage Rate Variant</h3>
                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                </li>
                            </ul>
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
                            $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR `itemName` like '%" . $_REQUEST['keyword'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                        }

                        $sql_list = queryGet("SELECT *,varient.`created_by` AS created,varient.`created_at` AS `time` FROM  `erp_mrp_variant` as varient  LEFT JOIN  `erp_mrp_territory` as territory ON territory.territory_id = varient.territory LEFT JOIN `erp_customer_mrp_group` as customer_group ON customer_group.customer_mrp_group_id = varient.customer_group WHERE 1 AND varient.`company_id`=$company_id AND varient.`branch_id`=$branch_id AND varient.`location_id`=$location_id ORDER BY varient.mrp_id  desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ", true);

                        //  console($sql_list);


                        //AND  layer.'warehouse_id'=warehouse.'warehouse_id' 
                        //as sl ,".ERP_WAREHOUSE." as warehouse
                        $countShow = "SELECT COUNT(*) FROM `erp_mrp_variant_items` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id  ";
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
                                            <th>MRP Group</th>
                                        <?php }
                                        if (in_array(2, $settingsCheckbox)) { ?>
                                            <th>Customer MRP Group</th>
                                        <?php }
                                        if (in_array(3, $settingsCheckbox)) { ?>
                                            <th>Territory</th>
                                        <?php  }

                                        if (in_array(4, $settingsCheckbox)) { ?>
                                            <th>Valid From</th>
                                        <?php }
                                        if (in_array(5, $settingsCheckbox)) { ?>
                                            <th>Valid Upto</th>
                                        <?php }
                                        if (in_array(6, $settingsCheckbox)) { ?>
                                            <th>Created By</th>
                                        <?php }
                                        if (in_array(7, $settingsCheckbox)) { ?>
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
                                        $summary_id = $row['item_id'];

                                        $item = queryGet("SELECT * FROM `erp_inventory_items` WHERE itemId = $summary_id");

                                    ?>
                                        <tr>
                                            <td><?= $cnt++ ?></td>
                                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                <td><?= $row['mrp_variant'] ?></td>
                                            <?php }
                                            if (in_array(2, $settingsCheckbox)) { ?>
                                                <td><?= $row['customer_mrp_group'] ?></td>
                                            <?php }
                                            if (in_array(3, $settingsCheckbox)) { ?>
                                                <td><?= $row['territory_name'] ?></td>
                                            <?php }



                                            if (in_array(4, $settingsCheckbox)) { ?>
                                                <td><?= formatDateORDateTime($row['valid_from']) ?></td>
                                            <?php }
                                            if (in_array(5, $settingsCheckbox)) { ?>
                                                <td><?= formatDateORDateTime($row['valid_till']) ?>
                                                </td>
                                            <?php }

                                            if (in_array(6, $settingsCheckbox)) { ?>
                                                <td><?= getCreatedByUser($row['created']) ?></td>
                                            <?php }

                                            if (in_array(7, $settingsCheckbox)) { ?>
                                                <td><?= formatDateORDateTime($row['time']) ?></td>
                                            <?php }

                                            ?>

                                            <td>

                                                <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['mrp_id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>


                                                <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $row['mrp_id']; ?>" style="cursor: pointer;" class="btn btn-sm" title="Edit Branch"><i class="fa fa-edit po-list-icon"></i></a>

                                                <!-- right modal start here  -->
                                                <div class="modal fade right customer-modal pr-modal classic-view-modal" id="fluidModalRightSuccessDemo_<?= $row['mrp_id'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                        <!--Content-->
                                                        <div class="modal-content">
                                                            <!--Header-->
                                                            <div class="modal-header pt-3">

                                                                <p class="text-sm  mt-2 mb-2">MRP Number : <?= $row['mrp_variant'] ?></p>
                                                                <p class="text-sm  mt-2 mb-2">Validity : <?= $row['valid_from'] ?> To <?= $row['valid_till'] ?></p>
                                                                <p class="text-sm  mt-2 mb-2">status: <span class="status status-modal ml-2"><?= $row['status'] ?></span></p>


                                                                <ul class="nav nav-tabs" id="myTab" role="tablist">

                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" id="home-tab<?= $row['mrp_id'] ?>" data-toggle="tab" href="#home<?= $row['mrp_id'] ?>" role="tab" aria-controls="home<?= $row['mrp_id'] ?>" aria-selected="true">Info</a>
                                                                    </li>


                                                                </ul>


                                                            </div>
                                                            <!--Body-->
                                                            <div class="modal-body px-4">
                                                                <div class="tab-content pt-1" id="myTabContent">
                                                                    <div class="tab-pane fade show active" id="home<?= $row['mrp_id'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                        <div class="col-md-12">

                                                                            <div class="purchase-create-section mt-2 mb-4" id="action-navbar">
                                                                                <form action="" method="POST">



                                                                                    <!-- <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on text-primary mx-3"></i> -->
                                                                            </div>
                                                                        </div>
                                                                        <div class="card">

                                                                            <div class="card-body p-3">
                                                                                <div class="display-flex rfq-item-title mt-2 mb-2">
                                                                                    <h4 class="info-h4 mb-0">
                                                                                        Item
                                                                                    </h4>
                                                                                    <div class="action-btn-flex">

                                                                                    </div>
                                                                                </div>
                                                                                <hr class="mt-1 mb-1">


                                                                                <div class="row px-3 p-0 m-0 mb-2">


                                                                                    <?php
                                                                                    $itemDetails = queryGet("SELECT *,variant.`status` AS item_status FROM `erp_mrp_variant_items` as variant LEFT JOIN `erp_inventory_items` as var_items ON variant.item_id = var_items.itemId WHERE variant.`mrp_id` = '" . $row['mrp_id'] . "'", true);
                                                                                    //   console($itemDetails);
                                                                                    // exit();
                                                                                    // console($_POST);
                                                                                    foreach ($itemDetails['data'] as $oneItem) {
                                                                                        //  console($oneItem);

                                                                                    ?>



                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">

                                                                                                    <button class="accordion-button btn btn-primary collapsed mb-1 pl-5" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                        <?= $oneItem['itemName'] ?>
                                                                                                        &nbsp;


                                                                                                        <?php
                                                                                                        $itemId = $oneItem['itemId'];
                                                                                                        $prId = $onePrList['purchaseRequestId'];
                                                                                                        $query = "SELECT * FROM erp_rfq_items WHERE prId = '$prId' AND ItemId = '$itemId'";
                                                                                                        $qry = queryGet($query, true);
                                                                                                        $num = $qry['numRows'];

                                                                                                        if ($num != 0) {
                                                                                                        ?>
                                                                                                            <span class="badge badge-primary ml-2">Added</span>
                                                                                                        <?php
                                                                                                        }
                                                                                                        ?>
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="basicDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">
                                                                                                        <div class="card bg-white">

                                                                                                            <div class="card-body p-3">

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Item Code :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $oneItem['itemCode'] ?></p>
                                                                                                                </div>
                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Item Name :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $oneItem['itemName'] ?></p>
                                                                                                                </div>
                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs"> Cost :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $oneItem['cost'] ?></p>
                                                                                                                </div>
                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Margin :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $oneItem['margin'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">MRP :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $oneItem['mrp'] ?></p>
                                                                                                                </div>
                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Item MRP Status :</p>
                                                                                                                    <p class="font-bold text-xs"> <span class="status status-modal ml-2"><?= $oneItem['item_status'] ?></span></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php } ?>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--/.Content-->
                                                    </div>
                                                </div>
                                                <!-- right modal end here  -->




                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>


                            <!---------------------------------Table settings Model Start--------------------------------->
                            <!-- <div class="modal" id="myModal2">
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
                                                                Customer MRP Group </td>
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
                            </div> -->
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


    $(document).ready(function() {



        $('#item_summary').select2();
        $('#territory').select2();
        $('#customer_group').select2();
        $('#item_batch').select2();




        $("#item_summary").on('change', function() {

            // alert(1);
            var val = $(this).val();
            // alert(val);

            var itemId = $(this).find('option:selected').data('attr');
            // alert(itemId);

            $.ajax({
                type: "GET",
                url: `ajaxs/mrp/ajax-batch.php`,
                data: {
                    val,
                    itemId
                },
                beforeSend: function() {
                    $("#item_batch").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    //   alert(response);
                    $("#item_batch").html(response);
                }
            });




        });

        // Debounce function
        function debounce(func, delay) {
            let timer;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timer);
                timer = setTimeout(() => {
                    func.apply(context, args);
                }, delay);
            };
        }




        //   Keyup event handler
        $('.cost').click(debounce(function() {
            var cost = parseFloat($(this).val());
            // alert(cost);
            var data = $(this).data('attr');
            var margin = parseFloat($(`#margin_${data}`).val());

            // Check if margin is a valid number, if not, set it to 0
            if (isNaN(margin)) {
                margin = 0;
            }

            var mrp = cost + margin;
            $(`#mrp_${data}`).val(mrp.toFixed(2)); // toFixed(2) limits the number of decimal places to 2
        }, 300)); // Adjust the delay as needed


        // Keyup event handler
        // $(document).on("keyup", ".margin", function() {
        //   alert(1);

        //     var margin = parseFloat($(this).val());
        //     var data = $(this).data('attr');
        //     var cost = parseFloat($(`#cost_${data}`).val());

        //     // Check if margin is a valid number, if not, set it to 0
        //     if (isNaN(cost)) {
        //         cost = 0;
        //     }

        //     var mrp = cost + margin;
        //     $(`#mrp_${data}`).val(mrp.toFixed(2)); // toFixed(2) limits the number of decimal places to 2
        // }, 300); // Adjust the delay as needed



    });
</script>

<script>
    $(document).ready(function() {
        $('.hamburger').click(function() {
            $('.hamburger').toggleClass('show');
            $('#overlay').toggleClass('show');
            $('.nav-action').toggleClass('show');
        });
    })
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
            $("#createMRPVariant").val(data);
            //confirm('Are you sure to Submit?')
            $("#SubmitForm").submit();
        });


        $(".edit_data").click(function() {
            var data = this.value;
            $("#editMRPVariant").val(data);
            //confirm('Are you sure to Submit?')
            $("#Edit_data").submit();
        });










    });
</script>


<script>
    var radios = document.querySelectorAll('input[type="radio"][name="type"]');

    radios.forEach(function(radio) {
        radio.addEventListener('click', function() {
            var val = $(this).val();
            // alert("Selected value: " + this.value);
            if (val == 'customer') {
                $("#customer_div").show();
                $("#territory_div").hide();

            } else {

                $("#customer_div").hide();
                $("#territory_div").show();

            }
        });
    });


    $("#usetypesDropdown").on("change", function() {
        let type = $(this).val();


        if (type != "") {
            $.ajax({
                type: "GET",
                url: `ajaxs/mrp/ajax-group-item.php`,
                data: {

                    type
                },
                beforeSend: function() {
                    $("#itemsDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $("#itemsDropDown").html(response);
                }
            });
        } else {
            $("#itemsDropDown").html('');
        }
    });

    $('#usetypesDropdown')
        .select2()
        .on('select2:open', () => {
            // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
        });


    $('#itemsDropDown')
        .select2()
        .on('select2:open', () => {
            // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
        });


    $("#itemsDropDown").on("change", function() {
        let itemId = $(this).val();


        $.ajax({
            type: "GET",
            url: `ajaxs/mrp/ajax-items-list.php`,
            data: {
                act: "listItem",
                itemId,

            },
            beforeSend: function() {
                //  $("#itemsTable").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                // console.log(response);

                $("#itemsTable").append(response);
                calculateAllItemsGrandAmount();
                //    currency_conversion();
            }
        });
    });
</script>

<script>
    $(document).on("keyup", ".margin", function() {


        var margin = parseFloat($(this).val());
        var data = $(this).data('attr');
        // alert(data);
        var cost = parseFloat($(`#cost_${data}`).val());

        // Check if margin is a valid number, if not, set it to 0
        if (isNaN(cost)) {
            cost = 0;
        }

        var mrp = cost + margin;
        $(`#mrp_${data}`).val(mrp.toFixed(2));

    });
    $(document).on("keyup", ".cost", function() {


        var cost = parseFloat($(this).val());
        var data = $(this).data('attr');
        //  alert(data);
        var margin = parseFloat($(`#margin_${data}`).val());

        // Check if margin is a valid number, if not, set it to 0
        if (isNaN(margin)) {
            margin = 0;
        }

        var mrp = cost + margin;
        $(`#mrp_${data}`).val(mrp.toFixed(2));

    });


    $(document).on("click", ".delItemBtn", function() {

        $(this).parent().parent().remove();

    });

    $(document).on("click", ".proceed", function() {


        var selectedMRP = $(".batchCbox:checked").data('mrp');
        var selectedValue = $(".batchCbox:checked").val();
        var attr = $(".batchCbox:checked").data('attr');
        // alert(attr);
        $(`#cost_${attr}`).val(selectedMRP);
        //  alert(selectedValue);

    });
    $(document).on("change", "#valid_from", function() {
        var fromDate = new Date($(this).val());
        var toDateInput = $('#valid_till');

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

   
</script>