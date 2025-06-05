<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/pagination.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/export.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");

$so_controller = new BranchSo();



if (isset($_POST["add-table-settings"])) {

    // console($_POST);
    // exit();

    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    // console($editDataObj);
    // exit();
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩  
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩  





?>

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
    .matrix-card .row:nth-child(1):hover {

        pointer-events: none;

    }

    .matrix-card .row:hover {

        border-radius: 0 0 10px 10px;

    }

    .matrix-card .row:nth-child(1) {

        background: #fff;

    }

    .matrix-card .row .col {

        display: flex;

        align-items: center;

    }

    .matrix-accordion button {

        color: #fff;

        border-radius: 15px !important;

        margin: 20px 0;

    }

    .accordion-button:not(.collapsed) {

        color: #fff;

    }

    .accordion-button::after {

        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

    }

    .accordion-button:not(.collapsed)::after {

        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

    }

    .accordion-item {

        border-radius: 15px !important;

        margin-bottom: 2em;

    }

    .info-h4 {

        font-size: 20px;

        font-weight: 600;

        color: #003060;

        padding: 0px 10px;

    }

    .rfq-modal .tab-content li a span,
    .rfq-modal .tab-content li a i {

        font-weight: 600 !important;

    }


    .float-add-btn {

        display: flex !important;

    }

    .items-search-btn {

        display: flex;

        align-items: center;

        gap: 5px;

        border: 1px solid #fff !important;

    }

    .card.existing-vendor .card-header,
    .card.other-vendor .card-header {

        display: flex;

        justify-content: space-between;

    }

    .card.existing-vendor a.btn-primary,
    .card.other-vendor a.btn-primary {

        padding: 3px 12px;

        margin-right: 10px;

        float: right;

        border: 1px solid #fff !important;

    }



    .card-body::after,
    .card-footer::after,
    .card-header::after {

        display: none;

    }

    .row.rfq-vendor-list-row-value {

        border-bottom: 1px solid #fff;

        margin: 0;

        align-items: center;

    }

    .row.rfq-vendor-list-row {

        margin: 0;

        border-bottom: 1px solid #fff;

        align-items: center;

    }

    .rfq-email-filter-modal .modal-dialog {

        max-width: 650px;

    }

    .date-range-input {
        gap: 13px;
        justify-content: flex-end;
    }

    .row.custom-range-row {
        align-items: center;
    }

    .goods-flex-btn form {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .customrange-section {
        position: absolute;
        bottom: 20px;
        right: 270px;
    }

    .vendor-gstin {
        margin: 90px auto;
    }

    .display-none {
        display: none;
    }

    .stock-action-bts {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        padding-right: 1em;
    }

    input.btn.btn-primary {
        background-color: #003060 !important;
        border-color: #003060 !important;
        margin: 20px 0px 0px;
        float: right;
    }

    .width-input {
        width: 130px;
    }


    @media (max-width: 575px) {

        .rfq-modal .modal-body {

            padding: 20px !important;

        }

    }

    @media(max-width: 390px) {

        .display-flex-space-between .matrix-btn {

            position: relative;

            top: 10px;

        }

    }
</style>


<style>
    .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        border: 1px solid #ccc;
        z-index: 9999;
    }
</style>

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        /* background-color: rgba(0, 0, 0, 0.4); */
    }

    .add-stock-list-modal .modal-dialog {
        width: 100%;
        max-width: 70%;
    }



    .add-stock-list-modal .modal-content {
        background-color: #fefefe;
        padding: 20px;
        border: 1px solid #888;
        margin: 0 auto;
        height: 500px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>



<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">


                    <div class="filter-list">
                        <a href="manage-grn-barcode.php" class="btn active waves-effect waves-light"><i class="fa fa-stream mr-2 active"></i>Batch Wise Stock Log</a>
                    </div>

                    <div class="card card-tabs" style="border-radius: 20px;">

                        <div class="card-body">
                            <div class="row filter-serach-row">


                                <div class="col-lg-12 col-md-12 col-sm-12">

                                    <div class="row custom-range-row">
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position: absolute; z-index: 999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>

                                        <div class="col-lg-10 col-md-10 col-sm-12">
                                            <div class="section serach-input-section">
                                                <input type="text" id="myInput" placeholder="" class="field form-control" />
                                                <div class="icons-container">
                                                    <div class="icon-search">
                                                        <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
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
                                                <h5 class="modal-title" id="exampleModalLongTitle">Filter
                                                    Payroll</h5>

                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                        <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                                                echo $_REQUEST['keyword'];
                                                                                                                                                                            } ?>">
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                        <select id="pr" name="pr" class="fld form-control m-input">
                                                            <option value="">ALL</option>
                                                            <?php

                                                            $pr_query = "SELECT * FROM erp_branch_purchase_request WHERE company_id = '$company_id' AND branch_id = '$branch_id' AND location_id = '$location_id'";
                                                            $pr_query_list = queryGet($pr_query, true);
                                                            $pr_list = $pr_query_list['data'];
                                                            foreach ($pr_list as $pr_row) {
                                                            ?>
                                                                <option value="<?= $pr_row['purchaseRequestId'] ?>" <?php if (isset($_GET['prid']) && $_GET['prid'] == $pr_row['purchaseRequestId']) echo ("selected"); ?>><?= $pr_row['prCode'] ?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>


                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                    echo $_REQUEST['form_date_s'];
                                                                                                                                                } ?>" />
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                        <input class="fld form-control" type="date" name="to_date_s" id="to_date_s" value="<?php if (isset($_REQUEST['to_date_s'])) {
                                                                                                                                                echo $_REQUEST['to_date_s'];
                                                                                                                                            } ?>" />
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync fa-spin"></i>Reset</a>
                                                <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                                                    Search</button>
                                            </div>



                                        </div>
                                    </div>
                                </div>

                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                        <?php
                                        $cond = '';
                                        global $company_id;
                                        global $branch_id;
                                        global $location_id;


                                        $batch = queryGet("SELECT
                                            warh.warehouse_id,
                                            warh.warehouse_code,
                                            warh.warehouse_name,
                                            loc.storage_location_id,
                                            loc.storage_location_code,
                                            loc.storage_location_name,
                                            loc.storage_location_type,
                                            loc.storageLocationTypeSlug,
                                            log.itemId,
                                            items.itemCode,
                                            items.itemName,
                                            SUM(log.itemQty) AS itemQty,
                                            ROUND(AVG(log.itemPrice),2) AS avg_price,
                            
                                            log.itemUom,
                                            log.logRef,
                                            log.bornDate
                                        FROM
                                            erp_inventory_stocks_log AS log
                                        LEFT JOIN erp_storage_location AS loc
                                        ON
                                            log.storageLocationId = loc.storage_location_id
                                        LEFT JOIN erp_storage_warehouse AS warh
                                        ON
                                            warh.warehouse_id = loc.warehouse_id
                                        LEFT JOIN erp_inventory_items AS items
                                        ON
                                        log.itemId=items.itemId
                                        WHERE
                                        log.companyId=1
                                        AND log.branchId=1
                                        AND log.locationId=1
                                        GROUP BY
                                            loc.storage_location_id,
                                            loc.storage_location_code,
                                            loc.storage_location_name,
                                            loc.storage_location_type,
                                            loc.storageLocationTypeSlug,
                                            log.itemUom,
                                            log.logRef,
                                            log.bornDate,
                                            log.itemId,
                                            items.itemCode,
                                            items.itemName
                                        ORDER BY
                                            log.bornDate ASC;", true);

                                        //   $stock = $so_controller->itemQtyStockCheck()

                                        //    console($batch);





                                        // $num_list = $batch['numRows'];

                                        $count = $batch['numRows'];
                                        $cnt = $GLOBALS['start'] + 1;
                                        // exit();
                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_STOCK_COUNT_BATCH", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                        $settingsCheckbox = unserialize($settingsCh);
                                        $settingsCheckboxCount = count($settingsCheckbox);

                                        ?>
                                        <!-- <div class="stock-action-bts mt-2 mb-2">
                                            <button class="btn btn-primary" onclick="ExportToExcel('xlsx','export_batch', 'export_batch')"><i class="fa fa-download mr-2"></i> Export</button>
                                            <button class="btn btn-primary" data-toggle="modal" data-target="#addnewBatchFile"><i class="fa fa-plus mr-2"></i>Add New</button>
                                        </div> -->
                                        <table class="table  table-hover text-nowrap p-0 m-0" id="export_batch">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>#</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>Batch Number</th>

                                                    <?php }

                                                    if (in_array(2, $settingsCheckbox)) { ?>

                                                        <th>Item Code</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>

                                                        <th>Item Name</th>
                                                    <?php }
                                                    if (in_array(4, $settingsCheckbox)) { ?>

                                                        <th>Storage Type</th>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>

                                                        <th>Item Quantity</th>
                                                    <?php }
                                                    if (in_array(6, $settingsCheckbox)) { ?>

                                                        <th>Item UOM</th>
                                                    <?php }
                                                    if (in_array(7, $settingsCheckbox)) { ?>

                                                        <th>Item Price</th>
                                                    <?php }
                                                    if (in_array(8, $settingsCheckbox)) { ?>

                                                        <th>Born Date</th>
                                                    <?php }




                                                    ?>



                                                    <th class="display-none">Physical Quantity</th>
                                                    <th>Action</th>




                                                </tr>
                                            </thead>



                                            <tbody>
                                                <?php
                                                // console($BranchPrObj->fetchBranchSoListing()['data']);
                                                $sl = 1;
                                                foreach ($batch['data'] as $data) {
                                                    //  console($data);

                                                ?>


                                                    <tr style="cursor:pointer">
                                                        <td><?= $cnt++ ?></td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $data['logRef'] ?>

                                                            </td>

                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= $data['itemCode'] ?>

                                                            </td>

                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td><?= $data['itemName'] ?>

                                                            </td>

                                                        <?php }

                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <td><?= $data['storage_location_type'] ?>
                                                            </td>

                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td><?= $data['itemQty'] ?>
                                                            </td>

                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td><?= $data['itemUom'] ?>
                                                            </td>

                                                        <?php }
                                                        if (in_array(7, $settingsCheckbox)) { ?>
                                                            <td><?= $data['avg_price'] ?>

                                                            </td>

                                                        <?php }

                                                        if (in_array(8, $settingsCheckbox)) { ?>
                                                            <td><?= formatDateORDateTime($data['bornDate'], false) ?>

                                                            </td>

                                                        <?php }






                                                        ?>
                                                        <td class="display-none"></td>
                                                        <td style="width: 20%">
                                                            <div class="d-flex gap-2">
                                                                <input type="number" class="form-control width-input" name="" id="codeQuantity_<?= $sl ?>" value="<?= $data['itemQty'] ?>">
                                                                <input type="hidden" name="" id="itemCode_<?= $sl ?>" value="<?= $data['itemCode'] ?>">
                                                                <input type="hidden" name="" id="batchNumber_<?= $sl ?>" value="<?= $data['logRef'] ?>">
                                                                <input type="hidden" name="" id="bornDate_<?= $sl ?>" value="<?= $data['bornDate'] ?>">
                                                                <button type="button" id="printButtonId_<?= $sl ?>" class="printButton btn btn-primary">Print</button>
                                                            </div>
                                                        </td>

                                                    </tr>


                                                <?php
                                                    $sl++;
                                                }
                                                ?>
                                            </tbody>
                                            <tbody>
                                                <tr>
                                                    <td colspan="<?= $settingsCheckboxCount + 2; ?>">
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



                                        <div class="modal fade" id="addnewBatchFile">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content card bg-white p-0">
                                                    <div class="modal-header card-header p-3">
                                                        <h4 class="modal-title" id="exampleModalLabel">Import Excel File</h4>
                                                    </div>
                                                    <div class="modal-body card-body p-3">
                                                        <form id="uploadBatchForm" enctype="multipart/form-data">
                                                            <input class="form-control" type="file" id="excelBatchFile" name="excelBatchFile" accept=".xls, .xlsx">
                                                            <input class="btn btn-primary" type="submit" value="Preview">
                                                        </form>
                                                        <div id="previewBatchModal" class="modal add-stock-list-modal">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h3 class="card-title">Excel Preview</h2>
                                                                            <span class="close">&times;</span>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div id="excelBatchData">

                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button class="btn btn-primary" id="insertBatchButton">Insert into Database</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                                        <input type="hidden" name="pageTableName" value="ERP_STOCK_COUNT_BATCH" />
                                                        <div class="modal-body">
                                                            <div id="dropdownframe"></div>
                                                            <div id="main2">
                                                                <table>
                                                                    <tr>
                                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                            Batch Number</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                            Item Code </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                            Item Name</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                                            Storage Type</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                                            Item Quantity </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                                            Item UOM</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                                            Item Price</td>
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </section>
</div>
<!-- End Pegination from------->

<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                    echo  $_REQUEST['pageNo'];
                                                } ?>">
</form>



<?php
require_once("../common/footer.php");
?>
<script>
    $(document).on("click", ".printButton", function() {
        let id = $(this).attr("id").split("_")[1];
        var quantity = $(`#codeQuantity_${id}`).val();
        var itemCode = $(`#itemCode_${id}`).val();
        var bornDate = $(`#bornDate_${id}`).val();
        var batchNumber = $(`#batchNumber_${id}`).val();

        window.open(`manage-grn-all-barcode.php?id=${id}&quantity=${quantity}&itemCode=${itemCode}&bornDate=${bornDate}&batchNumber=${batchNumber}`, '_blank');
    });

    $(document).on("click", ".remove_row", function() {

        var value = $(this).data('value');

        for (let l = 0; l < test.length; l++) {
            var array_each = test[l].split("|");
            if (array_each[0].includes(value) == true) {
                test.splice(l, 1);
            }
        }
        $(this).parent().parent().remove();
    })


    $(document).on("click", ".remove_row_other", function() {
        $(this).parent().parent().remove();
    })

    function rm() {
        $(event.target).closest("<div class='row others-vendor'>").remove();
    }


    function addMultiQty(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);



        $(`.modal-add-row_${id}`).append(`
    <div class="modal-body pl-3 pr-3" style="overflow: hidden;">
    <div class="row" style="align-items: end;">
                          <div class="col-lg-5 col-md-5 col-sm-12">
                            <div class="form-input">
                              <label for="date">Vendor Name</label>
                              <input type="text" id="eachName_${addressRandNo}" name="OthersVendor[${addressRandNo}][name]" class="form-control each_name" placeholder="Vendor Name" />
                            </div>
                          </div>
                            <div class="col-lg-5 col-md-5 col-sm-12">
                              <div class="form-input">
                                <label for="date">Vendor Email</label>
                                <input type="text" id="eachEmail_${addressRandNo}" name="OthersVendor[${addressRandNo}][email]" class="form-control each_email" placeholder="Vendor Email" />
                              </div>
                            </div>
                              <div class="col-lg-2 col-md-2 text-center remove_row_other" data-value="${addressRandNo}">
                                <a class="btn btn-danger" type="button">
                                  <i class="fa fa-minus"></i></a>
                              </div>
                            </div>
                            </div>`);
    }
</script>
<script>
    $(document).ready(function() {
        $('#itemsDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });
        $('#customerDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });
        // customers ********************************
        function loadCustomers() {
            $.ajax({
                type: "GET",
                url: `ajaxs/pr/ajax-customers.php`,
                beforeSend: function() {
                    $("#customerDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $("#customerDropDown").html(response);
                }
            });
        }
        loadCustomers();
        // get customer details by id
        $("#customerDropDown").on("change", function() {
            let itemId = $(this).val();
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-customers-list.php`,
                data: {
                    act: "listItem",
                    itemId
                },
                beforeSend: function() {
                    $("#customerInfo").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $("#customerInfo").html(response);
                }
            });
        });
        // **************************************
        function loadItems() {
            $.ajax({
                type: "GET",
                url: `ajaxs/pr/ajax-items.php`,
                beforeSend: function() {
                    $("#itemsDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $("#itemsDropDown").html(response);
                }
            });
        }
        loadItems();
        // get item details by id
        $("#itemsDropDown").on("change", function() {
            let itemId = $(this).val();
            $.ajax({
                type: "GET",
                url: `ajaxs/pr/ajax-items-list.php`,
                data: {
                    act: "listItem",
                    itemId
                },
                beforeSend: function() {
                    //  $("#itemsTable").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $("#itemsTable").append(response);
                }
            });
        });
        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
        })
        $(document).on('submit', '#addNewItemForm', function(event) {
            event.preventDefault();
            let formData = $("#addNewItemsForm").serialize();
            $.ajax({
                type: "POST",
                url: `ajaxs/so/ajax-items.php`,
                data: formData,
                beforeSend: function() {
                    $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
                    $("#addNewItemsFormSubmitBtn").html(
                        '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...'
                    );
                },
                success: function(response) {
                    $("#goodTypeDropDown").html(response);
                    $('#addNewItemsForm').trigger("reset");
                    $("#addNewItemsFormModal").modal('toggle');
                    $("#addNewItemsFormSubmitBtn").html("Submit");
                    $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
                }
            });
        });
        $(document).on("keyup change", ".qty", function() {
            let id = $(this).val();
            var sls = $(this).attr("sls");
            alert(sls);
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-items-list.php`,
                data: {
                    act: "totalPrice",
                    itemId: "ss",
                    id
                },
                beforeSend: function() {
                    $(".totalPrice").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $(".totalPrice").html(response);
                }
            });
        })
    })




    $(document).ready(function() {
        $('#uploadForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            $.ajax({
                url: 'ajaxs/stockCount/preview.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // alert(response);
                    $('#excelData').html(response);
                    $('#previewModal').show();
                }
            });
        });

        // $('#insertButton').click(function() {
        //     //alert(1);
        //     $.ajax({
        //         url: 'insert.php',
        //         type: 'POST',
        //         success: function(response) {
        //             alert(response);
        //             $('#previewModal').hide();
        //         }
        //     });
        // });

        $('.close').click(function() {
            $('#previewModal').hide();
        });
    });




    function submitForm() {
        var tableData = [];
        var table = document.getElementById("previewTable");
        var rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

        for (var i = 0; i < rows.length; i++) {
            var rowData = [];
            var cells = rows[i].getElementsByTagName("td");
            for (var j = 0; j < cells.length; j++) {
                rowData.push(cells[j].innerHTML);
            }
            tableData.push(rowData);
        }

        $.ajax({
            url: "ajaxs/stockCount/insert.php",
            type: "POST",
            data: {
                tableData: JSON.stringify(tableData)
            },
            success: function(response) {
                // var returnData = JSON.parse(response);
                console.log(response);
                // Check the status and message
                // if (returnData.status === "success" && returnData.message === "Stock Count Inserted") {
                //     // Display a success alert using alert()
                //     alert('Stock Count Inserted');
                // }
            }
        });
    }



    // Add event listener to the submit button
    document.getElementById('insertButton').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent the default form submission

        submitForm();
    });
</script>


<script>
    $(document).ready(function() {
        $('#uploadBatchForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            $.ajax({
                url: 'ajaxs/stockCount/previewBatch.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // alert(response);
                    $('#excelBatchData').html(response);
                    $('#previewBatchModal').show();
                }
            });
        });

        // $('#insertButton').click(function() {
        //     //alert(1);
        //     $.ajax({
        //         url: 'insert.php',
        //         type: 'POST',
        //         success: function(response) {
        //             alert(response);
        //             $('#previewBatchModal').hide();
        //         }
        //     });
        // });

        $('.close').click(function() {
            $('#previewBatchModal').hide();
        });
    });







    function submitBatchForm() {
        var tableData = [];
        var table = document.getElementById("previewBatchTable");
        var rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

        for (var i = 0; i < rows.length; i++) {
            var rowData = [];
            var cells = rows[i].getElementsByTagName("td");
            for (var j = 0; j < cells.length; j++) {
                rowData.push(cells[j].innerHTML);
            }
            tableData.push(rowData);
        }

        $.ajax({
            url: "ajaxs/stockCount/insertBatch.php",
            type: "POST",
            data: {
                tableData: JSON.stringify(tableData)
            },
            success: function(response) {
                console.log(response);
                // var returnData = JSON.parse(response);
                // alert(response);
                // Check the status and message
                // if (returnData.status === "success" && returnData.message === "Stock Count Inserted") {
                //     // Display a success alert using alert()
                //     alert('Stock Count Inserted');
                // }
            }
        });
    }


    document.getElementById('insertBatchButton').addEventListener('click', function(e) {

        e.preventDefault(); // Prevent the default form submission

        submitBatchForm();
    });
</script>