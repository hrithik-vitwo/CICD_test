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

$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = $fileNameWithoutExtension . 'download_all_' . $currentDateTime;

if (!isset($_COOKIE["cookiemrpVariant"])) {
    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiemrpVariant", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    } else {
        for ($i = 0; $i < 5; $i++) {
            $isChecked = ($i < 5) ? 'checked' : '';
        }
    }
}




if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}


$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'MRP Group',
        'slag' => 'mrp_variant',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer MRP Group',
        'slag' => 'customer_mrp_group',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Territory',
        'slag' => 'territory_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Valid From',
        'slag' => 'valid_from',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Valid Upto',
        'slag' => 'valid_till',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ]
];
?>

<link rel="stylesheet" href="../../public/assets/listing.css">
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

<!-- <style>
    .popup-container {
        background: #f5f5f5;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .popup-header {
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .popup-text {
        font-size: 14px;
        margin: 5px 0;
    }

    .popup-status {
        font-weight: bold;
        color: #2ac825;
    }

    .popup-tabs {
        list-style: none;
        padding: 0;
        display: flex;
        border-bottom: 2px solid #ddd;
    }

    .tab-item {
        margin-right: 15px;
    }

    .tab-link {
        text-decoration: none;
        padding: 10px 15px;
        display: inline-block;
        color: #333;
        font-weight: bold;
    }

    .tab-link.active {
        border-bottom: 2px solid #d9534f;
    }

    .popup-body {
        padding: 15px;
    }

    .content-card {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);

    }

    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .title {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
    }

    .content-items {
        padding: 10px;
        height: 170px;
        overflow-y: auto;
    }
</style>

<style>
    .expandable-item {
        background: #f5f5f5;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .expandable-header {
        margin-bottom: 5px;
    }

    .expandable-btn {
        width: 100%;
        text-align: left;
        background: #007bff;
        color: white;
        border: none;
        padding: 10px;
        font-weight: bold;
        border-radius: 5px;
    }

    .expandable-btn:hover {
        background: #0056b3;
    }

    .tag-highlight {
        background: #007bff;
        color: white;
        padding: 3px 7px;
        border-radius: 4px;
    }

    .content-box {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .text-bold {
        font-weight: bold;
    }

    .small-text {
        font-size: 12px;
    }

    .tag-status {
        font-weight: bold;
        color: #d9534f;
    }
</style> -->

<!-- css for inner modal table  -->

<style>
    /* Styles for the header row */
.head-state-table.row {
  width: 100%;
  display: flex;
}

/* Styles for the header row children */
.head-state-table.row > .col-lg-2 {
  flex-grow: 1;
  width: auto;
  flex-basis: 0;
}

/* Styles for the body row */
.row.body-state-table {
  width: 100%;
  display: flex;
}

/* Styles for the body row children */
.row.body-state-table > .col-lg-2 {
  flex-grow: 1;
  width: auto;
  flex-basis: 0;
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
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i
                                class="fas fa-home po-list-icon"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i
                                class="fa fa-list po-list-icon"></i>Rate Variant List</a></li>
                    <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                            Create Rate Variant</a></li>
                    <li class="back-button">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                            <i class="fa fa-reply po-list-icon"></i>
                        </a>
                    </li>
                </ol>


                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                    <input type="hidden" name="createMRPVariant" id="createMRPVariant" value="">
                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId"
                        value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

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
                                                    Select customer group to create a rate for any goods. This rate will be
                                                    applicabe for all customer belongs to this group during Quotation, Sales
                                                    Order and Invoice.
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <div class="form-inline my-3 gap-2">
                                                <input type="radio" name="type" value="territory">
                                                <label for="" class="height-label">Territory Group</label>
                                                <span class="label-note">
                                                    Select territory to create a rate for any goods. This rate will be
                                                    applicabe for all customer belongs to this territory during Quotation,
                                                    Sales Order and Invoice.
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
                                                        <option value="<?= $pr_row['customer_mrp_group_id'] ?>">
                                                            <?= $pr_row['customer_mrp_group'] ?>
                                                        </option>
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
                                                        <option value="<?= $row['territory_id'] ?>">
                                                            <?= $row['territory_name'] ?>
                                                        </option>
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
                                                    <select onclick="" name="usetypesDropdown" id="usetypesDropdown"
                                                        class="form-control typesDropdown">
                                                        <option value="">Select</option>
                                                        <?php
                                                        $sql = queryGet("SELECT * FROM `erp_inventory_mstr_good_groups` WHERE `companyId` = $company_id AND (`goodType` = 3 OR `goodType` = 4)", true);

                                                        foreach ($sql['data'] as $data) {
                                                            ?>
                                                            <option value="<?= $data['goodGroupId'] ?>">
                                                                <?= $data['goodGroupName'] ?>
                                                            </option>
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
                                                            <option value="<?= $getItems["itemId"] ?>">
                                                                [<?= $getItems["itemCode"] ?>]<?= $getItems["itemName"] ?>
                                                            </option>

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
                                <button type="submit" class="btn btn-primary add_data mrp_add_btn"
                                    value="add_post">Submit</button>
                            </div>
                        </div>
                </form>

            </div>
        </section>
    </div>





<?php } else if (isset($_GET['edit'])) {
    $mrp_id = $_GET['edit'];
    $mrp_sql = queryGet("SELECT * FROM `erp_mrp_variant` WHERE `mrp_id` = $mrp_id");
    ?>


        <div class="content-wrapper is-mrp-varient">
            <section class="content">
                <div class="container-fluid">


                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i
                                    class="fas fa-home po-list-icon"></i> Home</a></li>
                        <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i
                                    class="fa fa-list po-list-icon"></i>Rate Variant List</a></li>
                        <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
                                Edit Rate Variant</a></li>
                        <li class="back-button">
                            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                                <i class="fa fa-reply po-list-icon"></i>
                            </a>
                        </li>
                    </ol>


                    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="edit_frm" name="edit_frm">
                        <input type="hidden" name="editMRPVariant" id="editMRPVariant" value="">
                        <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId"
                            value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
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
                                                          } ?>><?= $pr_row['customer_mrp_group'] ?></option>
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
                                                          } ?>><?= $row['territory_name'] ?></option>
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
                                            <input type="date" class="form-control" id="valid_from" name="valid_from"
                                                value="<?= $mrp_sql['data']['valid_from'] ?>">
                                            <span class="error valid_from"></span>
                                        </div>
                                    </div>

                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                        <div class="form-input mb-3">
                                            <label>Valid Till</label>
                                            <input type="date" class="form-control" min="<?= $mrp_sql['data']['valid_from'] ?>"
                                                id="valid_till" name="valid_till" value="<?= $mrp_sql['data']['valid_till'] ?>">
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
                                                    <input type="hidden" name="listItem[<?= $randCode ?>][mrp_item_id]"
                                                        value="<?= $mrp_item_id ?>">
                                                    <tr class="rowDel itemRow"
                                                        id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
                                                        <input class="form-control full-width" type="hidden"
                                                            name="listItem[<?= $randCode ?>][itemId]"
                                                            value="<?= $getItemObj['data']['itemId'] ?>">
                                                        <td>
                                                            <input class="form-control full-width" type="hidden"
                                                                name="listItem[<?= $randCode ?>][itemCode]"
                                                                value="<?= $getItemObj['data']['itemCode'] ?>">
                                                        <?= $getItemObj['data']['itemCode'] ?>
                                                        </td>

                                                        <td>
                                                            <input class="form-control" type="hidden"
                                                                name="listItem[<?= $randCode ?>][itemName]"
                                                                value="<?= $getItemObj['data']['itemName'] ?>">
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
                                                        
                                                                $qtyObj = $BranchSoObj->itemQtyStockCheck($getItemObj['data']['itemId'], "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", "DESC", '', $creationDate);

                                                                // console($qtyObj);
                                                                $sumOfBatches = $qtyObj['sumOfBatches'];
                                                                $batchesDetails = $BranchSoObj->convertToWHSLBatchArray($qtyObj['data']);
                                                                // console($itemQtyStockCheck);
                                                                ?>
                                                                <input type="hidden" name="listItem[<?= $randCode ?>][stockQty]"
                                                                    class="form-control checkQty" id="checkQty_<?= $randCode ?>"
                                                                    value="<?= $sumOfBatches; ?>">

                                                                <!-- Button to Open the Modal -->
                                                                <div class="qty-modal py-2">
                                                                    <p class="font-bold text-center checkQtySpan"
                                                                        id="checkQtySpan_<?= $randCode ?>"><?= $sumOfBatches; ?></p>
                                                                    <hr class="my-2 w-50 mx-auto">
                                                                    <div
                                                                        class="text-xs d-flex align-items-center gap-2 justify-content-center">
                                                                        <p class="itemSellType" id="itemSellType_<?= $randCode ?>">
                                                                            CUSTOM</p>
                                                                        <ion-icon name="create-outline" class="stockBtn"
                                                                            id="stockBtn_<?= $randCode ?>" data-bs-toggle="modal"
                                                                            data-bs-target="#stockSetup<?= $randCode ?>"
                                                                            style="cursor: pointer;"></ion-icon>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" class="itemSellTypeHidden"
                                                                    id="itemSellTypeHidden_<?= $randCode ?>"
                                                                    name="listItem[<?= $randCode ?>][itemSellType]" value="CUSTOM">

                                                                <!-- The Modal -->
                                                                <div class="modal fade stock-setup-modal"
                                                                    id="stockSetup<?= $randCode ?>">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">

                                                                            <!-- Modal Header -->
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title text-sm text-white">Stock
                                                                                    Setup (CUSTOM)</h4>
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
                                                                                        <input type="radio"
                                                                                            name="listItem[<?= $randCode ?>][itemreleasetype]"
                                                                                            class="itemreleasetypeclass custom"
                                                                                            data-rdcode="<?= $randCode ?>"
                                                                                            value="CUSTOM"
                                                                                            id="custom_<?= $randCode ?>" checked>
                                                                                        <label for="custom"
                                                                                            class="text-xs mb-0 text-muted">Custom</label>
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
                                                                                        <div class="accordion accordion-flush warehouse-accordion p-0"
                                                                                            id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header w-100"
                                                                                                    id="flush-headingOne">
                                                                                                    <button
                                                                                                        class="accordion-button btn btn-primary warehouse-header waves-effect waves-light"
                                                                                                        type="button"
                                                                                                        data-bs-toggle="collapse"
                                                                                                        data-bs-target="#collapse<?= $whKey ?>"
                                                                                                        aria-expanded="true"
                                                                                                        aria-controls="flush-collapseOne">
                                                                                                    <?= $wareHouse['warehouse_code'] ?>
                                                                                                        |
                                                                                                    <?= $wareHouse['warehouse_name'] ?>
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="collapse<?= $whKey ?>"
                                                                                                    class="accordion-collapse collapse show"
                                                                                                    aria-labelledby="flush-headingOne"
                                                                                                    data-bs-parent="#accordionFlushExample"
                                                                                                    style="">
                                                                                                    <div class="accordion-body p-0">
                                                                                                        <h1></h1>
                                                                                                        <div
                                                                                                            class="card bg-transparent">
                                                                                                            <div class="card-body px-2 mx-3"
                                                                                                                style="background-color: #f9f9f9;">
                                                                                                                <!-- start location accordion -->
                                                                                                            <?php foreach ($wareHouse['storage_locations'] as $locationKey => $location) {
                                                                                                                if ($_GET["prod"] == "1") {
                                                                                                                    if ($location["storage_location_type"] == "RM-PROD") {
                                                                                                                        ?>

                                                                                                                            <div id="locAccordion">
                                                                                                                                <div
                                                                                                                                    class="card bg-transparent">
                                                                                                                                    <div
                                                                                                                                        class="card-header p-2 border rounded-0 bg-transparent border-0 border-bottom">
                                                                                                                                        <a class="btn text-dark w-100 storage-after"
                                                                                                                                            data-bs-toggle="collapse"
                                                                                                                                            href="#collapse<?= $whKey ?><?= $locationKey ?>">
                                                                                                                                        <?= $location['storage_location_code'] ?>
                                                                                                                                            |
                                                                                                                                        <?= $location['storage_location_name'] ?>
                                                                                                                                        </a>
                                                                                                                                    </div>
                                                                                                                                    <div id="collapse<?= $whKey ?><?= $locationKey ?>"
                                                                                                                                        class="collapse"
                                                                                                                                        data-bs-parent="#locAccordion">
                                                                                                                                        <div
                                                                                                                                            class="card-body bg-light mx-3">
                                                                                                                                            <?php
                                                                                                                                            // console($location['batches']);
                                                                                                                                            foreach ($location['batches'] as $batchKey => $batch) {
                                                                                                                                                // $batchItemUom = $ItemsObj->getBaseUnitMeasureById($batch['itemUom'])['data']['uomName'];
                                                                                                                                                $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                                                                                ?>
                                                                                                                                                <div
                                                                                                                                                    class="storage-location mb-2">
                                                                                                                                                    <div
                                                                                                                                                        class="input-radio">
                                                                                                                                                    <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                                                                            <input
                                                                                                                                                                type="radio"
                                                                                                                                                                name="listItem[<?= $randCode ?>][batchselectionchekbox]"
                                                                                                                                                                class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio"
                                                                                                                                                                id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>"
                                                                                                                                                                value="<?= $batch['logRef'] ?>"
                                                                                                                                                                data-mrp="<?= $batch['itemPrice'] ?>"
                                                                                                                                                                data-attr="<?= $randCode ?>"
                                                                                                                                                            <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                                echo 'checked';
                                                                                                                                                            } ?>>
                                                                                                                                                    <?php } else { ?>
                                                                                                                                                            <input
                                                                                                                                                                type="radio"
                                                                                                                                                                name="listItem[<?= $randCode ?>][batchselectionchekbox]"
                                                                                                                                                                class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio"
                                                                                                                                                                id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>"
                                                                                                                                                                value="<?= $batch['logRef'] ?>"
                                                                                                                                                            <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                                echo 'checked';
                                                                                                                                                            } ?>
                                                                                                                                                                data-mrp="<?= $batch['itemPrice'] ?>"
                                                                                                                                                                data-attr="<?= $randCode ?>"
                                                                                                                                                                disabled>
                                                                                                                                                    <?php } ?>
                                                                                                                                                    </div>
                                                                                                                                                    <div
                                                                                                                                                        class="d-grid">
                                                                                                                                                        <p
                                                                                                                                                            class="text-sm mb-2">
                                                                                                                                                        <?= $batch['logRef'] ?>
                                                                                                                                                        </p>
                                                                                                                                                        <p class="text-xs mb-2 font-bold batchItemQty"
                                                                                                                                                            id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                                                                            <span
                                                                                                                                                                class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?>
                                                                                                                                                                ||
                                                                                                                                                            <?= $batch['itemQty'] ?>
                                                                                                                                                            <?= $uomName ?>
                                                                                                                                                            </span>
                                                                                                                                                        </p>
                                                                                                                                                    </div>
                                                                                                                                                    <div
                                                                                                                                                        class="input">

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
                                                                                                                                <div
                                                                                                                                    class="card bg-transparent">
                                                                                                                                    <div
                                                                                                                                        class="card-header p-2 border rounded-0 bg-transparent border-0 border-bottom">
                                                                                                                                        <a class="btn text-dark w-100 storage-after"
                                                                                                                                            data-bs-toggle="collapse"
                                                                                                                                            href="#collapse<?= $whKey ?><?= $locationKey ?>">
                                                                                                                                        <?= $location['storage_location_code'] ?>
                                                                                                                                            |
                                                                                                                                        <?= $location['storage_location_name'] ?>
                                                                                                                                        </a>
                                                                                                                                    </div>
                                                                                                                                    <div id="collapse<?= $whKey ?><?= $locationKey ?>"
                                                                                                                                        class="collapse"
                                                                                                                                        data-bs-parent="#locAccordion">
                                                                                                                                        <div
                                                                                                                                            class="card-body bg-light mx-3">
                                                                                                                                            <?php
                                                                                                                                            // console($location['batches']);
                                                                                                                                            foreach ($location['batches'] as $batchKey => $batch) {
                                                                                                                                                // $batchItemUom = $ItemsObj->getBaseUnitMeasureById($batch['itemUom'])['data']['uomName'];
                                                                                                                                                $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                                                                                ?>
                                                                                                                                                <div
                                                                                                                                                    class="storage-location mb-2">
                                                                                                                                                    <div
                                                                                                                                                        class="input-radio">
                                                                                                                                                    <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                                                                            <input
                                                                                                                                                                type="radio"
                                                                                                                                                                name="listItem[<?= $randCode ?>][batchselectionchekbox]"
                                                                                                                                                                class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio"
                                                                                                                                                                id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>"
                                                                                                                                                                value="<?= $batch['logRef'] ?>"
                                                                                                                                                                data-mrp="<?= $batch['itemPrice'] ?>"
                                                                                                                                                                data-attr="<?= $randCode ?>"
                                                                                                                                                            <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                                echo 'checked';
                                                                                                                                                            } ?>>
                                                                                                                                                    <?php } else { ?>
                                                                                                                                                            <input
                                                                                                                                                                type="radio"
                                                                                                                                                                name="listItem[<?= $randCode ?>][batchselectionchekbox]"
                                                                                                                                                                class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio"
                                                                                                                                                                id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>"
                                                                                                                                                                value="<?= $batch['logRef'] ?>"
                                                                                                                                                                data-mrp="<?= $batch['itemPrice'] ?>"
                                                                                                                                                                data-attr="<?= $randCode ?>"
                                                                                                                                                            <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                                echo 'checked';
                                                                                                                                                            } ?>
                                                                                                                                                                disabled>
                                                                                                                                                    <?php } ?>
                                                                                                                                                    </div>
                                                                                                                                                    <div
                                                                                                                                                        class="d-grid">
                                                                                                                                                        <p
                                                                                                                                                            class="text-sm mb-2">
                                                                                                                                                        <?= $batch['logRef'] ?>
                                                                                                                                                        </p>
                                                                                                                                                        <p class="text-xs mb-2 font-bold batchItemQty"
                                                                                                                                                            id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                                                                            <span
                                                                                                                                                                class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?>
                                                                                                                                                                ||
                                                                                                                                                            <?= $batch['itemQty'] ?>
                                                                                                                                                            <?= $uomName ?>
                                                                                                                                                            </span>
                                                                                                                                                        </p>
                                                                                                                                                    </div>
                                                                                                                                                    <div
                                                                                                                                                        class="input">

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
                                                                                                                            <div
                                                                                                                                class="card bg-transparent">
                                                                                                                                <div
                                                                                                                                    class="card-header p-2 border rounded-0 bg-transparent border-0 border-bottom">
                                                                                                                                    <a class="btn text-dark w-100 storage-after"
                                                                                                                                        data-bs-toggle="collapse"
                                                                                                                                        href="#collapse<?= $whKey ?><?= $locationKey ?>">
                                                                                                                                    <?= $location['storage_location_code'] ?>
                                                                                                                                        |
                                                                                                                                    <?= $location['storage_location_name'] ?>
                                                                                                                                    </a>
                                                                                                                                </div>
                                                                                                                                <div id="collapse<?= $whKey ?><?= $locationKey ?>"
                                                                                                                                    class="collapse"
                                                                                                                                    data-bs-parent="#locAccordion">
                                                                                                                                    <div
                                                                                                                                        class="card-body bg-light mx-3">
                                                                                                                                        <?php
                                                                                                                                        // console($location['batches']);
                                                                                                                                        foreach ($location['batches'] as $batchKey => $batch) {

                                                                                                                                            // $batchItemUom = $ItemsObj->getBaseUnitMeasureById($batch['itemUom'])['data']['uomName'];
                                                                                                                                            $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                                                                            ?>
                                                                                                                                            <div
                                                                                                                                                class="storage-location mb-2">
                                                                                                                                                <div
                                                                                                                                                    class="input-radio">
                                                                                                                                                <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                                                                        <input
                                                                                                                                                            type="radio"
                                                                                                                                                            name="listItem[<?= $randCode ?>][batchselectionchekbox]"
                                                                                                                                                            class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio"
                                                                                                                                                            id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>"
                                                                                                                                                            value="<?= $batch['logRef'] ?>"
                                                                                                                                                            data-mrp="<?= $batch['itemPrice'] ?>"
                                                                                                                                                            data-attr="<?= $randCode ?>"
                                                                                                                                                        <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                            echo 'checked';
                                                                                                                                                        } ?>>
                                                                                                                                                <?php } else { ?>
                                                                                                                                                        <input
                                                                                                                                                            type="radio"
                                                                                                                                                            name="listItem[<?= $randCode ?>][batchselectionchekbox]"
                                                                                                                                                            class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio"
                                                                                                                                                            id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>"
                                                                                                                                                            value="<?= $batch['logRef'] ?>"
                                                                                                                                                            data-mrp="<?= $batch['itemPrice'] ?>"
                                                                                                                                                            data-attr="<?= $randCode ?>"
                                                                                                                                                        <?php if ($batch['logRef'] == $item_data['batch_number']) {
                                                                                                                                                            echo 'checked';
                                                                                                                                                        } ?>
                                                                                                                                                            disabled>
                                                                                                                                                <?php } ?>
                                                                                                                                                </div>
                                                                                                                                                <div
                                                                                                                                                    class="d-grid">
                                                                                                                                                    <p
                                                                                                                                                        class="text-sm mb-2">
                                                                                                                                                    <?= $batch['logRef'] ?>
                                                                                                                                                    </p>
                                                                                                                                                    <p class="text-xs mb-2 font-bold batchItemQty"
                                                                                                                                                        id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                                                                        <span
                                                                                                                                                            class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?>
                                                                                                                                                            ||
                                                                                                                                                        <?= $batch['itemQty'] ?>
                                                                                                                                                        <?= $uomName ?>
                                                                                                                                                        </span>
                                                                                                                                                    </p>
                                                                                                                                                </div>
                                                                                                                                                <div
                                                                                                                                                    class="input">

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
                                                                                <button type="button"
                                                                                    class="btn btn-success proceed"
                                                                                    id="proceed_<?= $randCode ?>"
                                                                                    data-bs-dismiss="modal">Proceed >></button>
                                                                            </div>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <input class="form-control" type="hidden"
                                                                    name="listItem[<?= $randCode ?>][sumOfBatches]"
                                                                    value="<?= $sumOfBatches ?>">

                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="any" name="listItem[<?= $randCode ?>][cost]"
                                                                class="form-control full-width-center cost"
                                                                id="cost_<?= $randCode ?>" data-attr="<?= $randCode ?>"
                                                                value="<?= $item_data['cost'] ?>">


                                                        </td>

                                                        <td>
                                                            <input type="number" step="any"
                                                                name="listItem[<?= $randCode ?>][margin]"
                                                                class="form-control full-width-center margin"
                                                                id="margin_<?= $randCode ?>" data-attr="<?= $randCode ?>"
                                                                value="<?= $item_data['margin'] ?>">
                                                        </td>

                                                        <td>
                                                            <input type="number" step="any" name="listItem[<?= $randCode ?>][mrp]"
                                                                class="form-control full-width-center mrp" id="mrp_<?= $randCode ?>"
                                                                value="<?= $item_data['mrp'] ?>">
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
                                <button type="submit" class="btn btn-primary edit_data mrp_edit_btn"
                                    value="edit_post">Submit</button>
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
        <div class="content-wrapper report-wrapper is-sales-orders vitwo-alpha-global">
            <!-- Content Header (Page header) -->
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">


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
                                                        <h3 class="card-title mb-0">Manage Rate Variant</h3>

                                                    <?php console() ?>

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
                                                                                <button class="ion-paginationlistMrpVariant">
                                                                                    <ion-icon name="list-outline"
                                                                                        class="ion-paginationlistMrpVariant"
                                                                                        id="exportAllBtn" role="img"
                                                                                        aria-label="list outline"></ion-icon>Export
                                                                                </button>
                                                                            </li>
                                                                            <li>
                                                                                <button class="ion-fulllistMrpVariant">
                                                                                    <ion-icon name="list-outline"
                                                                                        class="ion-fulllistMrpVariant md hydrated"
                                                                                        role="img"
                                                                                        aria-label="list outline"></ion-icon>Download
                                                                                </button>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create"
                                                                        class="btn btn-create mobile-page mobile-create addMrpbtn"
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
                                                            <select name="" id="mrpVariantLimit" class="custom-select">
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
                                                                <button class="ion-paginationlistMrpVariant">
                                                                    <ion-icon name="list-outline"
                                                                        class="ion-paginationlistMrpVariant md hydrated"
                                                                        role="img" aria-label="list outline"></ion-icon>Export
                                                                </button>
                                                            </li>
                                                            <li>

                                                                <button class="ion-fulllistMrpVariant">
                                                                    <ion-icon name="list-outline"
                                                                        class="ion-fulllistMrpVariant md hydrated" role="img"
                                                                        aria-label="list outline"></ion-icon>Download
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create"
                                                        class="btn btn-create mobile-page mobile-create addMrpbtn"
                                                        type="button">
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
                                                                    <th class="text-left" data-value="<?= $index ?>">
                                                                    <?= $column['name'] ?>
                                                                    </th>
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
                                                                                    $cookieTableStockReport = json_decode($_COOKIE["cookiemrpVariant"], true) ?? [];

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
                                                                <h4 class="text-xs text-white mb-0">Create Customer MRP
                                                                    Group</h4>
                                                                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                <div class="form-input mb-3">
                                                                                    <label>Customer MRP Group Name* </label>
                                                                                    <input type="text" class="form-control"
                                                                                        id="addmrpGroupName" name="mrpGroupName"
                                                                                        required>
                                                                                    <span class="error name"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="submit" id="add_mrpgrpName"
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
                                                                <input type="hidden" name="id" id="editGroupId" value="">

                                                                <div class="modal-content card">
                                                                    <div class="modal-header card-header pt-2 pb-2 px-3">
                                                                        <h4 class="text-xs text-white mb-0">Edit Customer MRP
                                                                            Group</h4>

                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                <div class="form-input mb-3">
                                                                                    <label>Group Name* </label>
                                                                                    <input type="text" class="form-control"
                                                                                        id="editmrpGroupName"
                                                                                        name="mrpGroupName" value="" required>
                                                                                    <span class="error name"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                                                                        <button type="submit" id="update_mrpgrpName"
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

                                                    <div class="modal right fade global-view-modal" id="viewGlobalModal"
                                                        role="dialog" aria-labelledby="myModalLabel" data-backdrop="true"
                                                        aria-modal="true">
                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success"
                                                            role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <div class="top-details">
                                                                        <div class="left">
                                                                            <p class="info-detail amount" id="amounts">
                                                                                <ion-icon name="wallet-outline" role="img"
                                                                                    class="md hydrated"
                                                                                    aria-label="wallet outline"></ion-icon>
                                                                                <span class="amount-value"
                                                                                    id="mrp_variant"></span>
                                                                            </p>

                                                                            <p class="info-detail po-number"><ion-icon
                                                                                    name="information-outline" role="img"
                                                                                    class="md hydrated"
                                                                                    aria-label="information outline"></ion-icon><span
                                                                                    id="validity"></span></p>
                                                                            <p class="info-detail po-number"><ion-icon
                                                                                    name="information-outline" role="img"
                                                                                    class="md hydrated"
                                                                                    aria-label="information outline"></ion-icon><span
                                                                                    id="status"></span></p>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <nav>
                                                                        <div class="nav nav-tabs global-view-navTabs"
                                                                            id="nav-tab" role="tablist">
                                                                            <button class="nav-link ViewfirstTab active"
                                                                                id="nav-overview-tab" data-bs-toggle="tab"
                                                                                data-bs-target="#nav-overview" type="button"
                                                                                role="tab" aria-controls="nav-overview"
                                                                                aria-selected="true"><ion-icon
                                                                                    name="apps-outline" role="img"
                                                                                    class="md hydrated"
                                                                                    aria-label="apps outline"></ion-icon>Info</button>


                                                                        </div>
                                                                    </nav>
                                                                    <div class="tab-content global-tab-content"
                                                                        id="nav-tabContent">

                                                                        <div class="tab-pane fade transactional-data-tabpane active show"
                                                                            id="nav-overview" role="tabpanel"
                                                                            aria-labelledby="nav-overview-tab">



                                                                            <div class="row orders-table">
                                                                                <div
                                                                                    class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                    <div class="items-table">
                                                                                        <h4>Item Details</h4>
                                                                                        <div class="multiple-item-table">
                                                                                            <div class="row head-state-table">
                                                                                                <div
                                                                                                    class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                                                                    Item Code</div>
                                                                                                <div
                                                                                                    class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                                                                    Item Name</div>
                                                                                                <div
                                                                                                    class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td ">
                                                                                                    Cost</div>
                                                                                                <div
                                                                                                    class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                                                                    Margin</div>
                                                                                                <div
                                                                                                    class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td ">
                                                                                                    MRP</div>
                                                                                                <div
                                                                                                    class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                                                                    Item MRP Status</div>
                                                                                            </div>
                                                                                            <div id="itemTableBody">

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>


                                                                        </div>


                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer"></div>
                                                            </div>
                                                        </div>
                                                    </div>

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
        echo $_REQUEST['pageNo'];
    } ?>">
</form>
<!-- End Pegination from------->


<?php
}

?>
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


    $(document).ready(function () {



        $('#item_summary').select2();
        $('#territory').select2();
        $('#customer_group').select2();
        $('#item_batch').select2();




        $("#item_summary").on('change', function () {

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
                beforeSend: function () {
                    $("#item_batch").html(`<option value="">Loding...</option>`);
                },
                success: function (response) {
                    //   alert(response);
                    $("#item_batch").html(response);
                }
            });




        });

        // Debounce function
        function debounce(func, delay) {
            let timer;
            return function () {
                const context = this;
                const args = arguments;
                clearTimeout(timer);
                timer = setTimeout(() => {
                    func.apply(context, args);
                }, delay);
            };
        }




        //   Keyup event handler
        $('.cost').click(debounce(function () {
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
    $(document).ready(function () {
        $('.hamburger').click(function () {
            $('.hamburger').toggleClass('show');
            $('#overlay').toggleClass('show');
            $('.nav-action').toggleClass('show');
        });
    })
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
            $("#createMRPVariant").val(data);
            //confirm('Are you sure to Submit?')
            $("#SubmitForm").submit();
        });


        $(".edit_data").click(function () {
            var data = this.value;
            $("#editMRPVariant").val(data);
            //confirm('Are you sure to Submit?')
            $("#Edit_data").submit();
        });










    });
</script>


<script>
    var radios = document.querySelectorAll('input[type="radio"][name="type"]');

    radios.forEach(function (radio) {
        radio.addEventListener('click', function () {
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


    $("#usetypesDropdown").on("change", function () {
        let type = $(this).val();


        if (type != "") {
            $.ajax({
                type: "GET",
                url: `ajaxs/mrp/ajax-group-item.php`,
                data: {

                    type
                },
                beforeSend: function () {
                    $("#itemsDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function (response) {
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


    $("#itemsDropDown").on("change", function () {
        let itemId = $(this).val();


        $.ajax({
            type: "GET",
            url: `ajaxs/mrp/ajax-items-list.php`,
            data: {
                act: "listItem",
                itemId,

            },
            beforeSend: function () {
                //  $("#itemsTable").html(`<option value="">Loding...</option>`);
            },
            success: function (response) {
                // console.log(response);

                $("#itemsTable").append(response);
                calculateAllItemsGrandAmount();
                //    currency_conversion();
            }
        });
    });
</script>

<script>
    $(document).on("keyup", ".margin", function () {


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
    $(document).on("keyup", ".cost", function () {


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


    $(document).on("click", ".delItemBtn", function () {

        $(this).parent().parent().remove();

    });

    $(document).on("click", ".proceed", function () {


        var selectedMRP = $(".batchCbox:checked").data('mrp');
        var selectedValue = $(".batchCbox:checked").val();
        var attr = $(".batchCbox:checked").data('attr');
        // alert(attr);
        $(`#cost_${attr}`).val(selectedMRP);
        //  alert(selectedValue);

    });
    $(document).on("change", "#valid_from", function () {
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


<?php
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

<script>
    // $('.btn-edit').on('click', function () {
    //     let gid = $(this).data('gid');

    //     let gname = $(this).data('gname');

    //     $('#editmrpGroupName').val(gname);
    //     $('#editGroupId').val(gid);


    //     $("#editFunctionality").modal('show');

    // });






    $('.m-input').on('keyup', function () {
        $(this).parent().children('.error').hide()
    });
    /*
      $(".add_data").click(function() {
        var data = this.value;
        $("#createdata").val(data);
        let flag = 1;
        var Ragex = "/[0-9]{4}/";
        if ($("#functionalities_name").val() == "") {
          $(".functionalities_name").show();
          $(".functionalities_name").html("functionalities name is requried.");
          flag++;
        } else {
          $(".functionalities_name").hide();
          $(".functionalities_name").html("");
        }
        if ($("#functionalities_desc").val() == "") {
          $(".functionalities_desc").show();
          $(".functionalities_desc").html("Description is requried.");
          flag++;
        } else {
          $(".functionalities_desc").hide();
          $(".functionalities_desc").html("");
        }
        if (flag == 1) {
          $("#add_frm").submit();
        }


      });
      $(".edit_data").click(function() {
        var data = this.value;
        $("#editdata").val(data);
        alert(data);
        //$( "#edit_frm" ).submit();
      });
    */

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
        $.each($("input[name='settingsCheckbox[]']:checked"), function () {
            favorite.push($(this).val());
        });
        var check = favorite.length;
        if (check < 5) {
            alert("Please Check Atlast 5");
            return false;
        }

    }


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

    $(document).ready(function () {

        $('.select2')
            .select2()
            .on('select2:open', () => {
                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal3">
    Add New
  </a></div>`);
            });
        //**************************************************************
        $('.select4')
            .select4()
            .on('select4:open', () => {
                $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
    Add New
  </a></div>`);
            });
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
                url: "ajaxs/mrp/ajax-manage-mrp-variant-all.php",
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
            var checkboxSettings = Cookies.get('cookiemrpVariant');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/mrp/ajax-manage-mrp-variant-all.php",
                dataType: 'json',
                data: {
                    act: 'mrpVariantTable',
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
                                `<a href="#" class="soModal" data-id="${value.mrp_id}" data-toggle="modal" data-target="#viewGlobalModal">${value.mrp_variant}</a>`,
                                `<p>${value.customer_mrp_group}</p>`,
                                `<p>${value.territory_name}</p>`,
                                `<p>${formatDate(value.valid_from)}</p>`,
                                `<p>${formatDate(value.valid_till)}</p>`,

                                ` <div class="dropout">
                                     <button class="more">
                                          <span></span>
                                          <span></span>
                                          <span></span>
                                     </button>
                                     <ul>
                                        <li>
                                        <a href="<?= basename($_SERVER['PHP_SELF']) ?>?edit=${value.mrp_id}">
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

        // $(document).on('click', '.editMrpbtn', function (e) {
        //     var mrpgrpname = $(this).data('mrpgrpname');
        //     var mrpgrpID = $(this).data('mrpgrpid');
        //     $("#editFunctionality").modal('show');
        //     $("#editmrpGroupName").val(mrpgrpname);
        //     $("#editGroupId").val(mrpgrpID);
        // })




        // $(document).on('click', '.addMrpbtn', function (e) {
        //     $("#funcAddForm").modal('show');
        // })



        $(document).on("click", ".soModal", function () {

            $('#viewGlobalModal').modal('show');
            let mrp_id = $(this).data('id');


            $.ajax({
                type: "GET",
                url: "ajaxs/modals/mrp/ajax-manage-mrp-variant-modal.php", // ajaxs/modals/vendor/ajax-manage-vendor-modal.php
                dataType: 'json',
                data: {
                    act: "modalData",
                    mrp_id
                },
                beforeSend: function () {
                    // $('.item-cards').remove();
                    $('#itemTableBody').html('');
                    let loader = `<div class="load-wrapp" id="globalModalLoader">
                                    <div class="load-1">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>`;

                    // Append the new HTML to the modal-body element
                    // $('#viewGlobalModal .modal-body').append(loader);

                },
                success: function (value) {
                    console.log(value);
                    if (value.status) {
                        let responseObj = value.data;
                        $("#mrp_variant").html('MRP Number : ' + responseObj[0].mrp_variant);
                        $("#validity").html('Validity : ' + responseObj[0].valid_from + ' to ' + responseObj[0].valid_till);
                        $("#status").html(responseObj[0].status);

                        $.each(responseObj, function (index, item) {


                            itemContent = `<div class="row body-state-table">
                        
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${item.itemCode}</div>

                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-elipse w-30 text-dark" title="${item.itemName}">${item.itemName}</div>

                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${decimalAmount(item.cost)}</div>

                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${item.margin}</div>

                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${decimalAmount(item.mrp)}</div>

                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${item.item_status}</div>
                                            
                                            
                                        </div>`
                        })
                        $("#itemTableBody").html(itemContent);
                    }
                },
                complete: function () {
                    // $("#globalModalLoader").remove();
                    $('#viewGlobalModal').modal('hide');
                }
            });
        });




        $(document).on("click", ".ion-paginationlistMrpVariant", function (e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiemrpVariant')
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
        $(document).on("click", ".ion-fulllistMrpVariant", function (e) {

            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'fullliststock',
                    data: JSON.stringify(allData),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiemrpVariant')
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
            var limitDisplay = $("#mrpVariantLimit").val();
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
                    // let value3 = $(`#value3_${columnIndex}`).val() ?? "";
                    let value4 = $(`#value4_${columnIndex}`).val() ?? "";

                    if (columnSlag === 'valid_from') {
                        values = value4;
                    } else if (columnSlag === 'valid_till') {
                        values = value2;
                    }

                    if ((columnSlag === 'valid_from' || columnSlag === 'valid_till') && operatorName == "BETWEEN") {
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
                        act: 'mrpVariant',
                        fromData: fromData
                    },
                    success: function (response) {
                        // console.log(response);
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

<!-- -----fromDate todate input add--- -->
<script>
    $(document).ready(function () {
        $(document).on("change", ".selectOperator", function () {
            let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
            let operatorName = $(this).val();
            let columnName = $(`#columnName_${columnIndex}`).html().trim();
            let inputContainer = $(`#td_${columnIndex}`);
            let inputId;
            if (columnName === 'Valid From') {
                inputId = "value4_" + columnIndex;
            } else if (columnName === 'Valid Upto') {
                inputId = "value2_" + columnIndex;
            }

            if ((columnName === 'Valid From' || columnName === 'Valid Upto') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            // console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
        });

    });
</script>

<script>
    $(document).ready(function () {
        $(".expandable-btn").click(function () {
            let target = $(this).attr("data-bs-target");
            $(".expandable-collapse").not(target).collapse("hide");
        });
    });
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

</script>