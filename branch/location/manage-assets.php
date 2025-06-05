<?php
require_once("../../app/v1/connection-branch-admin.php");

if (!isset($_COOKIE["cookiesassets"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiesassets", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    }
}

require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
require_once("../../app/v1/functions/branch/func-goods-controller.php");
$pageName =  basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
    echo "Session Timeout";
    exit;
}

$goodsController = new GoodsController();

// if (isset($_POST["createLocationItem"])) {

//     console($_POST);
//     exit();
//     $addNewObj = $goodsController->createGoodsLocation($_POST);
//     swalToast($addNewObj["status"], $addNewObj["message"]);
// }
$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Item Code',
        'slag' => 'itemCode',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Item Name',
        'slag' => 'itemName',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'UOM',
        'slag' => 'uom',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Group',
        'slag' => 'group',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Type',
        'slag' => 'type',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Moving Weighted Price',
        'slag' => 'mwp',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Valuation Class',
        'slag' => 'val_class',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'BOM Status',
        'slag' => 'bom',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'status',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ]
];

?>


<!-- <link rel="stylesheet" href="../../../public/assets/new_listing.css"> -->
<!-- <link rel="stylesheet" href="../../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<style>
    .global-view-modal .modal-body {
        overflow: auto;
    }
</style>
<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-goods-location vitwo-alpha-global">

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
                                    <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space" style="width:100%">
                                        <div class="left-block">
                                            <div class="label-select">

                                                <?php
                                                $assetFlag = "";
                                                if (isset($_GET['assetC']) && $_GET['assetC'] == '') {
                                                    $assetFlag = "assetC";
                                                ?>
                                                    <h3 class="card-title mb-0">Asset Under Construction</h3>

                                                <?php
                                                } else {
                                                ?>
                                                    <h3 class="card-title mb-0">Manage Asset</h3>

                                                <?php
                                                }
                                                ?>

                                                <input type="hidden" name="assetFlag" id="assetFlag" value="<?= $assetFlag ?>">
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <?php require_once("components/mm/assets-tabs.php"); ?>
                                            <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()"><i class="fa fa-expand fa-2x"></i></button>
                                        </div>
                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>

                            <div class="card card-tabs mb-0" style="border-radius: 20px;">
                                <div class="card-body">

                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane dataTableTemplate dataTable_stock fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                            <div class="filter-action">
                                                <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal1"> <ion-icon name="settings-outline"></ion-icon> Manage Column</a>
                                                <div class="length-row">
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
                                                <div class="filter-search">

                                                    <div class="icon-search" data-toggle="modal" data-target="#btnSearchCollpase_modal">
                                                        <p>Advance Search</p>
                                                        <ion-icon name="filter-outline"></ion-icon>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="goods.php?create" class="btn btn-create waves-effect waves-light" type="button">
                                                <ion-icon name="add-outline" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
                                                Create
                                            </a>

                                            <table id="dataTable_detailed_view" class="table table-hover table-nowrap stock-new-table transactional-book-table">

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
                                                            <h4 class="modal-title text-sm">Detailed View Column Settings</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form name="table_settings_detailed_view" method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
                                                            <div class="modal-body" style="max-height: 450px;">
                                                                <!-- <h4 class="modal-title">Detailed View Column Settings</h4> -->
                                                                <input type="hidden" id="tablename" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                                                <input type="hidden" id="pageTableName" name="pageTableName" value="ERP_TEST_<?= $pageName ?>" />
                                                                <div class="modal-body">
                                                                    <div id="dropdownframe"></div>
                                                                    <div id="main2">
                                                                        <div class="checkAlltd d-flex gap-2 mb-3 pl-2">
                                                                            <input type="checkbox" class="grand-checkbox" value="" />
                                                                            <p class="text-xs font-bold">Check All</p>
                                                                        </div>

                                                                        <table class="colomnTable">
                                                                            <?php

                                                                            foreach ($columnMapping as $index => $column) {

                                                                            ?>
                                                                                <tr>
                                                                                    <td valign="top" style="width: 165px">

                                                                                        <input type="checkbox" class="settingsCheckbox_detailed" name="settingsCheckbox[]" id="settingsCheckbox_detailed_view[]" value='<?= $column['slag'] ?>'>
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
                                                                <button type="submit" id="check-box-submt" name="check-box-submit" data-dismiss="modal" class="btn btn-primary">Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!---------------------------------Table Model End--------------------------------->

                                            <div class="modal " id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-sm" id="exampleModalLongTitle">Advanced Filter</h5>
                                                        </div>
                                                        <form id="myForm" method="post" action="">
                                                            <div class="modal-body">

                                                                <table>
                                                                    <tbody>
                                                                        <?php
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", "=", "!=", "BETWEEN"];

                                                                        foreach ($columnMapping as $columnIndex  => $column) {
                                                                            if ($columnIndex === 0 || $columnIndex===3|| $columnIndex===4|| $columnIndex===5|| $columnIndex===6|| $columnIndex===7|| $columnIndex===8 ) {
                                                                                continue;
                                                                            } ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="icon-filter d-flex align-items-center gap-2">
                                                                                        <?= $column['icon'] ?>
                                                                                        <p id="columnName_<?= $columnIndex ?>"><?= $column['name'] ?></p>
                                                                                        <input type="hidden" id="columnSlag_<?= $columnIndex ?>" value="<?= $column['slag'] ?>">
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <select class="form-control selectOperator" id="selectOperator_<?= $columnIndex ?>" name="operator[]" val="">
                                                                                        <?php
                                                                                        if (($column['dataType'] === 'date')) {
                                                                                            $operator = array_slice($operators, -3, 3);
                                                                                            foreach ($operator as $oper) {
                                                                                        ?>
                                                                                                <option value="<?= $oper ?>"><?= $oper ?></option>
                                                                                                <?php
                                                                                            }
                                                                                        } else {
                                                                                            $operator = array_slice($operators, 0, 2);
                                                                                            foreach ($operator as $oper) {
                                                                                                if ($oper === 'CONTAINS') {
                                                                                                ?>
                                                                                                    <option value="LIKE"><?= $oper ?></option>
                                                                                                <?php
                                                                                                } else { ?>

                                                                                                    <option value="NOT LIKE"><?= $oper ?></option>

                                                                                        <?php
                                                                                                }
                                                                                            }
                                                                                        } ?>
                                                                                    </select>
                                                                                </td>
                                                                                <td id="td_<?= $columnIndex ?>">
                                                                                    <input type="<?= ($column['dataType'] === 'date') ? 'date' : 'input' ?>" data-operator-val="" name="value[]" class="fld form-control m-input" id="value_<?= $columnIndex ?>" placeholder="Enter Keyword" value="">
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" id="serach_reset" class="btn btn-primary" data-dismiss="modal">Reset</button>
                                                                <button type="submit" id="serach_submit" class="btn btn-primary" data-dismiss="modal">Search</button>
                                                            </div>
                                                        </form>
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
    </section>
    <!-- /.content -->

    <!-----add form modal start --->
    <div class="modal fade asset-depriciate-modal add-assets-list-modal" id="addToLocation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
        <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
            <div class="modal-content">
                <form method="POST" id="addLocationForm">
                    <input type="hidden" name="createLocationItem" id="createLocationItem" value="createLocation">
                    <input type="hidden" id="item_id" name="item_id" value="">
                    <div class="modal-header">
                        <h4 class="text-sm">Storage Details <span class="text-danger">*</span></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <div class="form-input my-2">
                                                    <label for="">Storage Control</label>
                                                    <input type="text" name="storageControl" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <div class="form-input my-2">
                                                    <label for="">Max Storage Period</label>
                                                    <input type="text" name="maxStoragePeriod" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <div class="form-input my-2">
                                                    <label class="label-hidden" for="">Min Time Unit</label>
                                                    <select id="minTime" name="minTime" class="select2 form-control">
                                                        <option value="">Min Time Unit</option>
                                                        <option value="Day">Day</option>
                                                        <option value="Month">Month</option>
                                                        <option value="Hours">Hours</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Minimum Remain Self life</label>
                                                    <input type="text" name="minRemainSelfLife" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label class="label-hidden" for="">Max Time Unit</label>
                                                    <select id="maxTime" name="maxTime" class="select2 form-control">
                                                        <option value="">Max Time Unit</option>
                                                        <option value="Day">Day</option>
                                                        <option value="Month">Month</option>
                                                        <option value="Hours">Hours</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-header">
                        <h4 class="text-sm">Pricing and Discount</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="row goods-info-form-view customer-info-form-view">
                                            <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Target price</label>
                                                    <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Max Discount</label>
                                                    <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary save-close-btn btn-xs float-right add_data my-1 mr-2" id="addLocationFormSubBtn" value="add_post">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end of add from modal  -->
    <?php
    $companyd = queryGet("SELECT `depreciation_schedule`,`depreciation_type` FROM `erp_companies` WHERE `company_id`= '" . $company_id . "'");
    $method = $companyd['data']['depreciation_type'];
    $schudel = $companyd['data']['depreciation_schedule'];
    ?>
    <!-----add put to modal start modal start --->
    <div class="modal fade asset-depriciate-modal manage-asset-modal" id="addPutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
        <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Put to Use</h4>
                </div>
                <form method="POST" id="putUseForm">
                    <input type="hidden" name="puttouse" id="puttouse" value="out">
                    <input type="hidden" name="item_id" id="itemIdPut" value="">
                    <input type="hidden" name="storageLocationId" id="storageLocationId">
                    <input type="hidden" name="storageType" id="storageType">
                    <input type="hidden" name="stockLogId" id="stockLogId">
                    <input type="hidden" name="dep_schedule" id="dep_schedule" value="<?= $schudel ?>">
                    <div class="modal-body" style="max-height: 500px !important;">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="row">

                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="row">

                                            <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Asset Code</label>
                                                    <input type="text" name="assetCode" class="form-control" id="assetCodePut" value="" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Asset Name</label>
                                                    <input type="text" name="assetName" class="form-control" id="assetNamePut" value="" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Select Batch-No</label>
                                                    <select id="batchno" name="batchno" class="form-control">
                                                        <option value="">Select Batch</option>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-lg-3 col-md-3 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Receive Date *</label>
                                                    <input type="date" name="rcvDate" class="form-control" id="rcvDate" value="" readonly>
                                                </div>
                                            </div>

                                            <div class="col-lg-3 col-md-3 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Put to use Date *</label>
                                                    <input type="date" name="useDate" id="useDate" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Quantity</label>
                                                    <input type="text" name="qty" id="asset_qty" min=0 oninput="validateMaxQty(this)" class="form-control asset_qty calcu" value="">
                                                    <small id="error-msg" style="color: red; display: none;"></small>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Rate</label>
                                                    <input type="text" name="rate" readonly id="asset_rate" class="form-control asset_rate calcu" value="" data-attr="">
                                                    <input type="hidden" name="price1" readonly id="asset_price" class="form-control asset_price calcu" value="" data-attr="">

                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Total</label>
                                                    <input type="text" name="total" readonly id="asset_value" class="form-control asset_value calcu" value="">
                                                </div>
                                            </div>


                                            <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Cost Center</label>
                                                    <select id="costcenter" name="costcenter" class="form-control">
                                                        <option value="">Select Cost Center</option>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-lg-6 col-md-6 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">UOM</label>
                                                    <select id="buomDrop" name="uom" readonly class="form-control">
                                                        <option value="">Unit of Measurement</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Residual Value (%)</label>
                                                    <input type="text" name="scrap" id="asset_scrap" class="form-control inputQuantityClass asset_scrap calcu" value="5">
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Residual Value(INR)</label>
                                                    <input type="text" name="scrap_val" id="asset_scrap_val" class="form-control asset_scrap_val calcu" value="">
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Depreciation Value (%)</label>
                                                    <input type="text" name="dep_percentage" id="dep_percentage" class="form-control asset_scrap" value="" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                                                <div class="form-input my-2">
                                                    <label for="">Equip No List</label>
                                                    <div class="row" id="inputContainer"></div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary save-close-btn btn-xs float-right add_data" id="add_data" value="add_post">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- put to modal end -->

    <!-- Global View start-->
    <div class="modal right fade goods-item-modal global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="top-details">
                        <div class="left">
                            <p class="info-detail amount" id="amounts">
                                <ion-icon name="wallet-outline"></ion-icon>
                                <span class="amount-value" id="amount"> </span>
                            </p>
                            <span class="amount-in-words" id="amount-words"></span>
                            <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span id="po-numbers"> </span></p>
                        </div>
                        <div class="right">
                            <p class="info-detail name"><ion-icon name="business-outline"></ion-icon><span id="cus_name"></span></p>
                            <p class="info-detail default-address"><ion-icon name="location-outline"></ion-icon><span id="default_address"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <nav>
                        <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                            <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                            <!-- <button class="nav-link classicview-btn classicview-link" id="nav-classicview-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-classicview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Preview</button> -->
                            <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                        </div>
                    </nav>
                    <div class="tab-content global-tab-content" id="nav-tabContent">

                        <div class="tab-pane fade show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                            <!-- <div class="d-flex nav-overview-tabs">

                                                                    </div> -->
                            <div class="row" id="assetItem">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <div class="view-block details-block">
                                        <div class="items-table">
                                            <h6>Basic Details</h6>
                                            <div class="item-details">
                                                <div class="form-input">
                                                    <label for="">Asset Name</label>
                                                    <p><span id="itemName"></span></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="">Asset Description</label>
                                                    <p><span id="itemDesc"></span></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="">HSN</label>
                                                    <p><span id="hsnCode"></span></p>
                                                    <p class="note"><span id="hsnDesc"></span></p>
                                                </div>
                                                <div class="d-flex justify-content-between mb-3">
                                                    <div class="form-input">
                                                        <label for="">Moving Weighted Price</label>
                                                        <p><span id="movWeightPrice"></span></p>
                                                    </div>
                                                    <div class="d-flex uom-flex">
                                                        <div class="form-input mt-0">
                                                            <label for="">Base UOM</label>
                                                            <p><span id="baseUom"></span></p>
                                                        </div>
                                                        <div class="form-input mt-0">
                                                            <label for="">Alternate UOM</label>
                                                            <p> <span id="altUom"></span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="view-block group-block">
                                        <div class="items-table">
                                            <h6>Classification </h6>
                                            <div class="item-details">
                                                <div class="row">
                                                    <div class="col-6 col-lg-6 col-md-6 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Asset Type</label>
                                                            <p><span id="itemType"></span></p>
                                                        </div>

                                                        <div class="form-input mt-5">
                                                            <p class="note">Note : <span id="groupNote"></span></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-lg-6 col-md-6 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Asset Classification</label>
                                                            <p id="assetClassification"></p>
                                                        </div>

                                                        <div class="form-input">
                                                            <label for="">Gl Code</label>
                                                            <p id="assetGlCode"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="view-block specification-block">
                                        <div class="items-table">
                                            <h6>Specification Details</h6>
                                            <div class="spec-details" id="specificationDiv">
                                                <div class="form-input">
                                                    <label for="">Net Weight</label>
                                                    <p><span id="netWeightSpec"></span> </p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="">Gross Weight</label>
                                                    <p><span id="grossWeightSpec"></span> </p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="">Height</label>
                                                    <p><span id="heightSpec"></span> </p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="">Width</label>
                                                    <p><span id="widthSpec"></span> </p>

                                                </div>
                                                <div class="form-input">
                                                    <label for="">Length</label>
                                                    <p><span id="lengthSpec"></span> </p>

                                                </div>
                                                <div class="form-input">
                                                    <label for="">Volume in CM3</label>
                                                    <p><span id="volumenCmSpec"></span></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="">Volume in M3</label>
                                                    <p><span id="volumenMSpec"></span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                    <div class="view-block item-image-block">
                                        <div class="items-table">
                                            <h6>Asset Images</h6>
                                            <div class="goods-img">
                                                <div class="img-block" id="itemImages">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="view-block storage-block">
                                        <div class="items-table">
                                            <h6>Storage Details</h6>
                                            <div class="storage-details">
                                                <div class="form-input">
                                                    <label for="storageControl">Storage Control</label>
                                                    <p id="storageControl"></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="maxStoragePeriod">Max Storage Period</label>
                                                    <p id="maxStoragePeriod"></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="defaultStorageLocation">Default Storage Location</label>
                                                    <p id="defaultStorageLocation"></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="minimumRemainSelfLife">Minimum Remain Self life</label>
                                                    <p id="minimumRemainSelfLife"></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="minTimeUnit">Min Time Unit</label>
                                                    <p id="minTimeUnit"></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="maxTimeUnit">Max Time Unit</label>
                                                    <p id="maxTimeUnit"></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="minimumStock">Minimum Stock</label>
                                                    <p id="minimumStock"></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="maximumUnit">Maximum Unit</label>
                                                    <p id="maximumUnit"></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="qaStorageLocation">QA Storage Location</label>
                                                    <p id="qaStorageLocation"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="view-block price-discount-block">
                                        <div class="items-table">
                                            <h6>Pricing and Discount</h6>
                                            <div class="storage-details">
                                                <div class="form-input">
                                                    <label for="">Default MRP</label>
                                                    <p><span id="defMrp"></span></p>
                                                </div>
                                                <div class="form-input">
                                                    <label for="">Default Discount (%)</label>
                                                    <p><span id="defDiscount"></span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="view-block specification-block">
                                        <div class="items-table">
                                            <h6>Technical Specification Details</h6>
                                            <div id="techSpecification"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane classicview-pane fade" id="nav-classicview" role="tabpanel" aria-labelledby="nav-classicview-tab">
                            <a href="" class="btn btn-primary classic-view-btn float-right" id="classicViewPrint" target="_blank">Print</a>
                            <div class="card classic-view bg-transparent">

                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
                            <div class="inner-content">
                                <div class="audit-head-section mb-3 mt-3 ">
                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span><span class="created_by_trail"></span></p>
                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span><span class="updated_by"> </span></p>
                                </div>
                                <hr>
                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent">


                                </div>
                                <div class="modal fade right audit-history-modal" id="innerModal" role="dialog" aria-labelledby="innerModalLabel" aria-modal="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content auditTrailBodyContentLineDiv">

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

<?php

require_once("../common/footer2.php");
?>


<script>
    function generateInputs(count) {
        let container = $("#inputContainer");
        container.empty(); // Clear existing inputs

        if (count > 0) {
            for (let i = 0; i < count; i++) {
                let millisecPart = Date.now().toString().slice(-6);
                let uniqueValue = "Equip-" + millisecPart + (i + 1); // Unique default value

                let rowDiv = $("<div>")
                    .addClass("col-lg-4 col-md-4 col-12 col-sm-12 unique-input-div my-2") // Unique div for each input
                    .append(
                        $("<input>")
                        .attr("type", "text")
                        .attr("name", "equiplist[]")
                        .val(uniqueValue) // Set default value
                        .addClass("dynamic-input form-control")
                        .on("input", function() {
                            checkForDuplicates($(this));
                        })
                    );

                container.append(rowDiv);
            }
        }
    }

    function checkForDuplicates(inputField) {
        let inputValue = inputField.val().trim();
        let allValues = $(".dynamic-input").map(function() {
            return $(this).val().trim();
        }).get();

        // Remove existing error messages
        inputField.next(".error-message").remove();

        // Check if the input is empty
        if (inputValue === "") {
            inputField.css("border", "2px solid red");
            inputField.after("<small class='error-message' style='color: red;'>This field cannot be empty!</small>");
            updateSubmitButton();
            return;
        }

        // Count occurrences in input fields (frontend check)
        let occurrences = allValues.filter(v => v === inputValue).length;

        if (occurrences > 1) {
            inputField.css("border", "2px solid red");
            inputField.after("<small class='error-message' style='color: red;'>Duplicate value found!</small>");
            updateSubmitButton();
            return;
        }

        // Check in database via AJAX
        $.ajax({
            url: "ajaxs/modals/mm/ajax-manage-asset-modal.php", // Server-side script
            type: "GET",
            data: {
                act: "duplicate",
                value: inputValue
            },
            success: function(response) {
                if (response.trim() === "exists") {
                    inputField.css("border", "2px solid red");
                    inputField.after("<small class='error-message' style='color: red;'>Value already exists in database!</small>");
                } else {
                    inputField.css("border", "").next(".error-message").remove();

                }
                updateSubmitButton();
            },
            error: function() {
                console.error("Database check failed.");
                updateSubmitButton();
            }
        });
    }

    function updateSubmitButton() {
        let hasError = $(".error-message").length > 0;
        $("#add_data").prop("disabled", hasError);
    }

    // $("#inputForm").submit(function(event) {
    //     let inputValues = $(".dynamic-input").map(function() {
    //         return $(this).val();
    //     }).get();

    //     // Store as JSON string in the hidden input field
    //     $("#hiddenInput").val(JSON.stringify(inputValues));
    // });
</script>

<script>
    $(document).on("click", "#serach_reset", function(e) {
      e.preventDefault();
      $("#myForm")[0].reset();
      $("#serach_submit").click();
    });

    // Enter to search
    $(document).on("keypress", "#myForm input", function(e) {
      if (e.key === "Enter") {
        $("#serach_submit").click();
        e.preventDefault();
      }
    });
    $(document).ready(function() {
        var indexValues = [];
        var dataTable;

        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
                "lengthMenu": [10, 25, 50, 100, 200, 250],
                "ordering": false,
                info: false,
                "initComplete": function(settings, json) {
                    $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
                },

                buttons: [],
                // select: true,
                "bPaginate": false,
            });

        }
        $('#dataTable_detailed_view thead tr').append('<th>Action</th> <th>Add</th>');

        initializeDataTable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            let fdate = "<?php echo $f_date; ?>";
            let to_date = "<?php echo $to_date; ?>";
            let comid = <?php echo $company_id; ?>;
            let locId = <?php echo $location_id; ?>;
            let bId = <?php echo $branch_id; ?>;
            let columnMapping = <?php echo json_encode($columnMapping); ?>;
            let checkboxSettings = Cookies.get('cookiesassets');
            let notVisibleColArr = [];
            let assetFlagVal = $("#assetFlag").val();


            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-assets.php",
                dataType: 'json',
                data: {
                    act: 'assets',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit,
                    assetFlagVal
                },
                beforeSend: function() {
                    $("#detailed_tbody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
                },
                success: function(response) {
                    // console.log(response);
                    if (response.status) {
                        var responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(length - 1).visible(true);
                        dataTable.column(length - 2).visible(true);

                        $.each(responseObj, function(index, value) {
                            let btn = ``;
                            if (value.checkRes.flag == "allList") {
                                if (value.checkRes.status == "success") {
                                    btn = `<button class="btn btn-success" type="button">Added</button>`;
                                } else {
                                    btn = `<button class="btn btn-primary addLocationBtn" data-id="${value.itemId}">Add</button>`;
                                }
                            } else if (value.checkRes.flag == "assetC") {
                                if (value.checkRes.numRows == 0) {
                                    btn = `<button class="btn btn-success" type="button">In Use / Out Of Stock</button>`;
                                } else {
                                    btn = `<button class="btn btn-primary putUseBtn" data-id="${value.itemId}">Put to use</button>`;
                                }
                            }

                            let status = ``;
                            if (value.status == 'active') {
                                status = `<p class='status-bg status-open'>Active</p>`;
                            } else if (value.status == 'deleted') {
                                status = `<p class='status-bg status-closed'>Deleted</p>`;
                            } else {
                                status = `<p class='status-bg status-open'>${value.status}</p>`;
                            }

                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.itemId}" data-code="${value.itemCode}" data-toggle="modal" data-target="#viewGlobalModal">${value.itemCode}</a>`,
                                `<p class="pre-normal">${value.itemName}</p>`,
                                value.uom,
                                `<p class="pre-normal"> ${value.group_name}</p>`,
                                value.type_name,
                                decimalAmount(value.mwp),
                                value.val_class,
                                value.bom_status,
                                status,
                                btn,
                                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                        <li>
                                            <button data-toggle="modal" data-target="#editModal"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                        </li>
                                        <li>
                                            <button data-toggle="modal" data-target="#viewModal"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>
                                        </li>
                                        <li>
                                            <button class="soModal" data-id="${value.itemId}" data-code="${value.itemCode}" data-toggle="modal" data-target="#viewModal"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                        </li>
                                  
                                    </ul>
                                   
                                </div>`
                            ]).draw(false);

                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (checkboxSettings) {
                            var checkedColumns = JSON.parse(checkboxSettings);

                            $(".settingsCheckbox_detailed").each(function(index) {
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
                                notVisibleColArr.forEach(function(index) {
                                    dataTable.column(index).visible(false);
                                });
                            }

                            console.log('Cookie value:', checkboxSettings);

                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);

                                }
                            });

                            console.log('Cookie is blank.');
                        }
                    } else {

                        $("#detailed_tbody").html(`<td colspan=17 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
                        $('#yourDataTable_paginate').hide();
                        $('#limitText').hide();
                    }
                }
            });
        }

        fill_datatable();


        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function(e) {
            var maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);

        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a ", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $(".custom-select").val();

            fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay);

        });

        //<--------------advance search------------------------------->
        $(document).ready(function() {
            $(document).on("click", "#serach_submit", function(event) {
                event.preventDefault();
                let values;
                $(".selectOperator").each(function() {
                    let columnIndex = ($(this).attr("id")).split("_")[1];
                    let columnSlag = $(`#columnSlag_${columnIndex}`).val();
                    let operatorName = $(`#selectOperator_${columnIndex}`).val();
                    let value = $(`#value_${columnIndex}`).val() ?? "";
                    let value2 = $(`#value2_${columnIndex}`).val() ?? "";


                    if ((columnSlag === 'so.posting_date') && operatorName == "BETWEEN") {
                        formInputs[columnSlag] = {
                            operatorName,
                            value: {
                                fromDate: value,
                                toDate: value2
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
                console.log("FormInputs:", formInputs);

                fill_datatable(formDatas = formInputs);

            });
        });

        // -------------checkbox----------------------

        $(document).ready(function() {
            var columnMapping = <?php echo json_encode($columnMapping); ?>;

            var indexValues = [];

            function toggleColumnVisibility(columnIndex, checkbox) {
                var column = dataTable.column(columnIndex);
                column.visible(checkbox.checked);

            }

            $("input[name='settingsCheckbox[]']").change(function() {
                var columnVal = $(this).val();
                console.log(columnVal);

                var index = columnMapping.findIndex(function(column) {
                    return column.slag === columnVal;
                });
                console.log(index);
                toggleColumnVisibility(index, this);
            });

            $(".grand-checkbox").on("click", function() {
                $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);
                $("input[name='settingsCheckbox[]']").each(function() {
                    var columnVal = $(this).val();
                    // console.log(columnVal);
                    var index = columnMapping.findIndex(function(column) {
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

    $(document).ready(function() {
        $(document).on("click", "#check-box-submt", function(event) {
            // console.log("Hiiiii");
            event.preventDefault();
            // $("#myModal1").modal().hide();
            $('#btnSearchCollpase_modal').modal('hide');
            var tablename = $("#tablename").val();
            var pageTableName = $("#pageTableName").val();
            var settingsCheckbox = [];
            var formData = {};
            $(".settingsCheckbox_detailed").each(function() {
                if ($(this).prop('checked')) {
                    var chkBox = $(this).val();
                    settingsCheckbox.push(chkBox);
                    formData = {
                        tablename,
                        pageTableName,
                        settingsCheckbox
                    };
                }
            });

            console.log(formData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: "json",
                    data: {
                        act: 'assets',
                        formData: formData
                    },
                    success: function(response) {
                        // console.log(response);
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer: 1000,
                            showConfirmButton: false,
                        })
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });

            }
        });
    });
</script>
<!-- -----fromDate todate input add--- -->
<script>
    $(document).ready(function() {
        $(document).on("change", ".selectOperator", function() {
            let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
            let operatorName = $(this).val();
            let columnName = $(`#columnName_${columnIndex}`).html();
            let inputContainer = $(`#td_${columnIndex}`);
            let inputId;
            if (columnName === 'Posting Date') {
                inputId = "value2_" + columnIndex;
            }

            if ((columnName === 'Posting Date') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
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

<script>
    document.querySelector('table.stock-new-table').onclick = ({
        target
    }) => {
        if (!target.classList.contains('more')) return
        document.querySelectorAll('.dropout.active').forEach(
            (d) => d !== target.parentElement && d.classList.remove('active')
        )
        target.parentElement.classList.toggle('active')
    }
</script>

<script>
    function buildTree(treeData, act) {
        const rootUl = document.createElement('ul');

        treeData.forEach(element => {
            const li = document.createElement('li');
            const p = document.createElement('p');
            if (act == 'lst') {
                p.textContent = `${element}`;
            } else {
                p.textContent = `${element.goodGroupName}`;
            }
            li.appendChild(p);
            rootUl.appendChild(li);
        });

        return rootUl;
    }
    $(document).on("click", ".soModal", function() {
        let itemId = $(this).data('id');
        let code = $(this).data('code');
        $('.auditTrail').attr("data-ccode", code);
        // $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print.php?goods=${btoa(itemId)}`);
        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');
        console.log(itemId);

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/mm/ajax-manage-asset-modal.php",
            dataType: 'json',
            data: {
                act: 'modalData',
                itemId
            },
            beforeSend: function() {

            },
            success: function(value) {
                let responseObj = value.data;
                let dataObj = value.data.dataObj;

                // top nav
                $("#amount").html(dataObj.itemName);
                $("#po-numbers").html(dataObj.itemCode);
                $("#default_address").html(dataObj.itemDesc);
                $("#cus_name").html(responseObj.classification.glName);

                if (responseObj.type == 'asset') {
                    $('#otherItem').show();
                    $('#serviceItem').hide();

                    //Item Basic Details
                    $("#itemName").html(dataObj.itemName);
                    $("#itemDesc").html(dataObj.itemDesc);
                    $("#hsnCode").html(dataObj.hsnCode);
                    $("#hsnDesc").html(responseObj.hsnDesc);
                    $("#movWeightPrice").html(responseObj.movWeightPrice);
                    $("#baseUom").html(responseObj.baseUnitMeasure);
                    $("#altUom").html(responseObj.issueUnitMeasure);

                    //Images from item
                    if (responseObj.images.length > 0) {
                        $("#itemImages").html('');
                        $.each(responseObj.images, function(index, val) {
                            let imgUrl = `<?= COMP_STORAGE_URL ?>/others/${val}`;
                            // console.log(imgUrl);
                            let obj = `<div class="imgs">                       
                                            <img src="${imgUrl}" alt="">
                                    </div>`;
                            $("#itemImages").append(obj);
                        });
                    } else {
                        let obj = `<div class="imgs"><p>No Images Found </p></div>`
                        $("#itemImages").html(obj);
                    }

                    //Group
                    $("#itemType").html(responseObj.classification.glName);
                    //Item  Asset classification
                    $("#assetClassification").html(responseObj.assetClass);
                    // Item Gl Code
                    $("#assetGlCode").html(responseObj.assetGlCode);


                    //Specification Details
                    if (dataObj.netWeight != '' || dataObj.grossWeight != '' || dataObj.height != '' || dataObj.width != '' || dataObj.length != '' || dataObj.volumeCubeCm != '' || dataObj.volume != '') {
                        if (dataObj.netWeight != '') {
                            $('#netWeightSpec').html(`${dataObj.netWeight} ${dataObj.weight_unit}`);
                        }
                        if (dataObj.grossWeight != '') {
                            $('#grossWeightSpec').html(`${dataObj.grossWeight}  ${dataObj.weight_unit}`);
                        }
                        if (dataObj.height != '') {
                            $('#heightSpec').html(`${dataObj.height} ${dataObj.measuring_unit}`);
                        }
                        if (dataObj.width != '') {
                            $('#widthSpec').html(`${dataObj.width} ${dataObj.measuring_unit}`);
                        }
                        if (dataObj.length != '') {
                            $('#lengthSpec').html(`${dataObj.length} ${dataObj.measuring_unit}`);
                        }
                        if (dataObj.volumeCubeCm != '') {
                            $('#volumenCmSpec').html(`${dataObj.volumeCubeCm}`);
                        }
                        if (dataObj.volume != '') {
                            $('#volumenMSpec').html(`${dataObj.volume}`);
                        }
                    } else {
                        $("#specificationDiv").html('');
                    }

                    //Tech Specifications
                    $('#techSpecification').html('');
                    let techSpecification = responseObj.techSpecification;
                    if (techSpecification.length > 0 && (techSpecification[0].specification != '' && techSpecification[0].specification_detail != '')) {
                        $.each(techSpecification, function(index, val) {
                            if (val.specification != '' && val.specification_detail != '') {
                                let obj = `<div class="spec-details">
                                                                                        <div class="form-input">
                                                                                            <label for="">Specifiaction</label>
                                                                                            <p>${val.specification}</p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">Description</label>
                                                                                            <p>${val.specification_detail}</p>
                                                                                        </div>
                                                                                    </div>`;
                                $('#techSpecification').append(obj);
                            }

                        });
                    }

                    //Default mrp and discount
                    $('#defMrp').html(`${responseObj.companyCurrency} ${responseObj.itemPrice}`);
                    $('#defDiscount').html(`${responseObj.itemMaxDiscount} %`);

                    //Storage details
                    let storageDetails = responseObj.storageDetails;
                    let summaryData = responseObj.summaryData;

                    $('#storageControl').html(storageDetails.storageControl);
                    $("#maxStoragePeriod").html(storageDetails.maxStoragePeriod);
                    $("#minimumRemainSelfLife").html(storageDetails.minRemainSelfLife);
                    $("#minTimeUnit").html(storageDetails.minRemainSelfLifeTimeUnit);
                    $("#maxTimeUnit").html(storageDetails.maxStoragePeriodTimeUnit);
                    $("#minimumStock").html(decimalQuantity(summaryData.min_stock));
                    $("#maximumUnit").html(decimalQuantity(summaryData.max_stock));
                    $("#defaultStorageLocation").html(responseObj.defaultStorageLocationName);
                    $("#qaStorageLocation").html(responseObj.qaStorageLocationName);

                    // end of other
                }
                // trail part
                $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
                $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);

            },
            error: function(error) {
                console.log(error);
            }
        });

        // $.ajax({
        //     type: "GET",
        //     url: "ajaxs/modals/mm/ajax-manage-goods-modal.php",
        //     data: {
        //         act: 'classicView',
        //         itemId
        //     },
        //     beforeSend: function() {
        //     },
        //     success: function(res){
        //         $(".classic-view").html(res);
        //     },
        // });

    });

    // -------------- Add Location Script --------------------
    $(document).on('click', ".addLocationBtn", function() {
        let itemId = $(this).data('id');
        $("#item_id").val(itemId);
        $('#addToLocation').modal('show');
    });

    $(document).on("submit", "#addLocationForm", function(e) {
        e.preventDefault();
        let addLocationForm = $("#addLocationForm");
        $.ajax({
            type: "POST",
            url: "ajaxs/modals/mm/ajax-manage-asset-modal.php",
            dataType: "json",
            data: addLocationForm.serialize(),
            beforeSend: function() {
                $("#addLocationFormSubBtn").html(`waiting.......`);
                $("#addLocationFormSubBtn").removeClass("btn-primary");
                $("#addLocationFormSubBtn").addClass("btn-warning");
            },
            success: function(response) {
                console.log(response);
                if (response.status == "success") {
                    $("#addLocationFormSubBtn").html(`success`);
                    $("#addLocationFormSubBtn").removeClass("btn-warning");
                    $("#addLocationFormSubBtn").addClass("btn-success");

                    Swal.fire({
                        icon: response.status,
                        title: response.message,
                        timer: 3000,
                        showConfirmButton: false,
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(error) {
                console.log(error);
            },
        });
    });

    function validateMaxQty(input) {
        const maxQty = parseInt(input.max); // Get the max attribute value
        const errorMsg = document.getElementById("error-msg"); // Error message element

        if (input.value > maxQty) {
            // Show error message if quantity exceeds the limit
            errorMsg.textContent = `The quantity cannot exceed ${maxQty}.`;
            errorMsg.style.display = "block";
            generateInputs(0);
        } else {
            // Hide error message if quantity is valid
            errorMsg.style.display = "none";
            generateInputs(input.value);
        }
    }
    $('#batchno').change(function() {
        var selectedOption = $(this).find(':selected');
        var qty = selectedOption.data('qty') || '';
        var createdat = selectedOption.data('create_date') || '';
        var itemUomc = selectedOption.data('itemuom') || '';
        var grn_itemPrice = selectedOption.data('grn_itemprice') || '';
        var itemPrice = selectedOption.data('itemprice') || '';
        var storageType = selectedOption.data('storagetype') || '';
        var storageLocationId = selectedOption.data('storagelocationid') || '';
        var stockLogId = selectedOption.data('stocklogid') || '';
        validateMaxQty(0);
        if (selectedOption.val()) { // If a valid option is selected
            $("#stockLogId").val(stockLogId);
            $('#asset_qty').val('');
            $("#useDate").val('');
            $('#rcvDate').val(createdat);
            $('#buomDrop').val(itemUomc).trigger('change');
            $("#asset_rate").val(grn_itemPrice);
            $("#asset_price").val(itemPrice);
            $('#asset_qty').attr('max', qty);
            $("#useDate").attr('min', createdat);
            $("#storageLocationId").val(storageLocationId);
            $("#storageType").val(storageType);
        } else { // Reset all fields to their default states
            $("#stockLogId").val('');
            $("#stockLogId").val('');
            $('#asset_qty').val('');
            $("#useDate").val('');
            $('#rcvDate').val('');
            $('#buomDrop').val('').trigger('change');
            $("#asset_rate").val('');
            $("#asset_price").val('');
            $('#asset_qty').removeAttr('max');
            $("#useDate").removeAttr('min');
            $("#storageLocationId").val('');
            $("#storageType").val('');
        }
    });


    function recalculate() {
        // Get current values
        let qty = helperQuantity($('#asset_qty').val()) || 0;
        let price = helperAmount($('#asset_rate').val()) || 0;
        let total = helperAmount($('#asset_value').val()) || 0;
        let scrapValue = helperAmount($('#asset_scrap_val').val()) || 0;
        let scrapPercent = helperQuantity($('#asset_scrap').val()) || 0;

        // Recalculate total
        total = qty * price;
        $('#asset_value').val(decimalAmount(total)); // Update total
        scrapValue = total * (scrapPercent / 100);
        $('#asset_scrap_val').val(decimalAmount(scrapValue));
        // If scrap percentage changes, calculate scrap value
        if ($(this).attr('id') === 'asset_scrap') {
            scrapValue = total * (scrapPercent / 100);
            $('#asset_scrap_val').val(decimalAmount(scrapValue));
        }

        // If scrap value changes, calculate scrap percentage
        if ($(this).attr('id') === 'asset_scrap_val') {
            scrapPercent = (scrapValue / total) * 100;
            $('#asset_scrap').val(decimalQuantity(scrapPercent));
        }

    }

    // Attach change event to all input fields
    $('.calcu').on('input', recalculate);
    // -------------- Put to Use Script --------------------

    $(document).on('click', ".putUseBtn", function() {
        let itemId = $(this).data('id');
        let ajax1Completed = false;
        let ajax2Completed = false;
        let ajax3Completed = false;
        let dataObj, batch, uomList, costCenterList;
        $('#putUseForm').trigger('reset');

        // Optional: Reset dropdowns and other custom fields
        $('#batchno').val('').trigger('change');
        $('#buomDrop').val('').trigger('change');
        $('.form-input').find('.error-msg').remove();

        // Show loader overlay
        $("body").append('<div id="loader-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; display: flex; justify-content: center; align-items: center;"><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""></div>');

        function checkAndOpenModal() {
            if (ajax1Completed && ajax2Completed) {
                // Fill modal fields with fetched data
                let output = [];
                output.push(`<option value="">Select Batch-No</option>`);
                $.each(batch.data, function(key, value) {
                    output.push(`<option value="${value.logRef}" 
                              data-itemUom="${dataObj.itemUom}" 
                              data-qty="${inputQuantity(value.total_itemQty)}" 
                              data-create_date="${value.first_created_date}"
                              data-grn_itemPrice="${inputValue(value.grn_itemPrice)}"
                              data-itemPrice="${inputValue(value.itemPrice)}"
                              data-storageType="${value.storageType}"
                              data-storageLocationId="${value.storageLocationId}"
                              data-stockLogId="${value.stockLogId}">
                              ${value.logRef}</option>`);
                });
                $('#batchno').html(output.join(''));
                $("#itemIdPut").val(dataObj.itemId);
                $("#assetNamePut").val(dataObj.itemName);
                $("#assetCodePut").val(dataObj.itemCode);
                $("#asset_rate").val(dataObj.movingWeightedPrice);
                $("#dep_percentage").val(decimalQuantity(dataObj.depPercentage));

                // Populate UOM dropdown
                let uomOptions = [];
                uomOptions.push(`<option value="">Select Unit of Measurement</option>`);
                $.each(uomList, function(key, value) {
                    uomOptions.push(`<option value="${value.uomId}">${value.uomName}</option>`);
                });
                $('#buomDrop').html(uomOptions.join(''));

                let costCenter = [];
                costCenter.push(`<option value="">Select Unit of Measurement</option>`);
                $.each(costCenterList, function(key, value) {
                    costCenter.push(`<option value="${value.CostCenter_id}">${value.CostCenter_code} ${value.CostCenter_desc}</option>`);
                });
                $('#costcenter').html(costCenter.join(''));


                // Hide loader and show modal
                $("#loader-overlay").remove();
                $('#addPutModal').modal('show');
            }
        }

        // First AJAX request
        $.ajax({
            type: "GET",
            url: "ajaxs/modals/mm/ajax-manage-asset-modal.php",
            dataType: "json",
            data: {
                act: 'putToUse',
                itemId
            },
            success: function(value) {
                if (value.status) {
                    dataObj = value.data;
                    batch = value.batchlist;
                    ajax1Completed = true;
                    checkAndOpenModal(); // Check if the modal can be shown
                }
            },
            error: function() {
                $("#loader-overlay").remove();
                alert("Failed to fetch asset details. Please try again.");
            }
        });

        // Second AJAX request
        $.ajax({
            type: "GET",
            url: "ajaxs/modals/mm/ajax-manage-asset-modal.php",
            dataType: "json",
            data: {
                act: 'uomList'
            },
            success: function(res) {
                if (res.status == "success") {
                    uomList = res.data;
                    ajax2Completed = true;
                    checkAndOpenModal(); // Check if the modal can be shown
                }
            },
            error: function() {
                $("#loader-overlay").remove();
                alert("Failed to fetch UOM list. Please try again.");
            }
        });
        $.ajax({
            type: "GET",
            url: "ajaxs/modals/mm/ajax-manage-asset-modal.php",
            dataType: "json",
            data: {
                act: 'costCenterList'
            },
            success: function(res) {
                if (res.success == "true") {
                    costCenterList = res.data;
                    ajax3Completed = true;
                    checkAndOpenModal(); // Check if the modal can be shown
                }
            },
            error: function() {
                $("#loader-overlay").remove();
                alert("Cost Center. Please try again.");
            }
        });
    });

    const fieldsToValidate = [{
            id: '#batchno',
            name: 'Batch-No',
            type: 'select'
        },
        {
            id: '#rcvDate',
            name: 'Receive Date',
            type: 'text'
        },
        {
            id: '#useDate',
            name: 'Put to Use Date',
            type: 'date'
        },
        {
            id: '#asset_qty',
            name: 'Quantity',
            type: 'number'
        },
        {
            id: '#asset_rate',
            name: 'Rate',
            type: 'text'
        },
        {
            id: '#asset_scrap',
            name: 'Reschedule Value (%)',
            type: 'number'
        },
        {
            id: '#asset_scrap_val',
            name: 'Residual Value',
            type: 'text'
        },
        {
            id: '#dep_percentage',
            name: 'Depreciation Percentage',
            type: 'number'
        },
        {
            id: '#storageLocationId',
            name: 'Storage Location ID',
            type: 'hidden'
        },
        {
            id: '#storageType',
            name: 'Storage Type',
            type: 'hidden'
        },
        {
            id: '#buomDrop',
            name: 'Unit of Measurement (UOM)',
            type: 'select'
        },
        {
            id: '#costcenter',
            name: 'Select Cost Center',
            type: 'select'
        },
        {
            id: '#asset_value',
            name: 'Total',
            type: 'text'
        }, // Total field validation
    ];

    // Function to check all fields and remove error messages if filled
    $('#putUseForm').on('input change', 'input, select', function() {
        // Check if there are any error messages in the form
        const formHasErrorMessages = $('#putUseForm').find('.error-msg').length > 0;

        // If there are error messages, proceed with the validation logic
        if (formHasErrorMessages) {
            fieldsToValidate.forEach(function(field) {
                const fieldElement = $(field.id); // Get the field by its selector
                const errorMsg = fieldElement.closest('.form-input').find('.error-msg'); // Get the error message if exists

                // Check if field value is filled (non-empty)
                if (fieldElement.val().trim() !== '') {
                    // Only remove the error message if it exists
                    if (errorMsg.length > 0) {
                        errorMsg.remove(); // Remove error message if it exists
                    }
                } else {
                    // Only add the error message if it does not exist already
                    if (errorMsg.length === 0) {
                        fieldElement.closest('.form-input').append(`<span class="error-msg" style="color: red;">${field.name} is required</span>`);
                    }
                }
            });
        }
    });



    $(document).on('submit', '#putUseForm', function(e) {
        e.preventDefault();

        // Clear previous error messages
        $('.form-input').find('.error-msg').remove();

        // Initialize validation status
        let isValid = true;

        // Required fields with validation rules
        fieldsToValidate.forEach(field => {
            const element = $(field.id);
            const value = element.val();
            if (!value) {
                isValid = false;
                const errorMsg = `<small class="error-msg" style="color: red;">${field.name} is required.</small>`;
                element.closest('.form-input').append(errorMsg);
            }
        });

        // Additional validation for specific fields
        const qtyField = $('#asset_qty');
        const rateField = $('#asset_rate');
        const totalField = $('#asset_value');
        const maxQty = qtyField.attr('max');
        const enteredQty = qtyField.val();
        const enteredRate = rateField.val();
        const calculatedTotal = (enteredQty && enteredRate) ? (parseFloat(enteredQty) * parseFloat(enteredRate)) : '';

        // Validate "Quantity" max constraint
        if (enteredQty && maxQty && parseFloat(enteredQty) > parseFloat(maxQty)) {
            isValid = false;
            const errorMsg = `<small class="error-msg" style="color: red;">Quantity cannot exceed ${maxQty}.</small>`;
            qtyField.closest('.form-input').append(errorMsg);
        }

        // Validate and auto-fill "Total"
        if (!calculatedTotal || calculatedTotal <= 0) {
            isValid = false;
            const errorMsg = `<small class="error-msg" style="color: red;">Total must be a positive value.</small>`;
            totalField.closest('.form-input').append(errorMsg);
        } else {
            totalField.val(calculatedTotal); // Auto-update "Total" field
        }

        const useDateField = $('#useDate');
        const minUseDate = useDateField.attr('min');
        const enteredUseDate = useDateField.val();
        if (enteredUseDate && minUseDate && new Date(enteredUseDate) < new Date(minUseDate)) {
            isValid = false;
            const errorMsg = `<small class="error-msg" style="color: red;">Put to Use Date cannot be earlier than ${minUseDate}.</small>`;
            useDateField.closest('.form-input').append(errorMsg);
        }

        // Prevent multiple submissions
        $("body").append('<div id="loader-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; display: flex; justify-content: center; align-items: center;"><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""></div>');

        $('#add_data').prop('disabled', true);
        // Disable the button to prevent re-submit

        let putUseForm = $("#putUseForm");
        console.log(putUseForm.serialize());
        console.log("okkk1");

        if (isValid) {
            $.ajax({
                type: "POST",
                url: "ajaxs/modals/mm/ajax-manage-asset-modal.php",
                dataType: "json",
                data: putUseForm.serialize(),
                beforeSend: function() {
                    console.log("before sending request");
                },
                success: function(response) {
                    console.log("Response status:", response.status); // This will show exactly what's returned

                    if (response.status.trim().toLowerCase() === "success") {
                        $("#loader-overlay").remove();
                        // Show a success message with SweetAlert
                        Swal.fire({
                            icon: 'success', // You can use 'success' or response.status to customize
                            title: response.message, // Display the success message
                            timer: 3000, // Duration before it auto-closes
                            showConfirmButton: false, // Hide the confirm button
                        }).then(() => {
                            // Reload the page once the Swal dialog closes
                            location.reload();
                        });
                    } else {
                        // If the response is not "Success", you can handle it here (optional)
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong!',
                            text: response.message || 'An error occurred while processing.',
                        });
                    }
                },
                error: function(error) {
                    console.log("error occurred");
                    console.log(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Something went wrong!',
                        text: 'An error occurred while submitting the form.',
                    });
                },
                complete: function() {
                    // Re-enable the submit button after the request is complete (success or failure)
                    $("#loader-overlay").remove();
                    $("#add_data").prop('disabled', false);
                }
            });
        } else {
            // Re-enable the submit button if validation fails
            // submitButton.prop('disabled', false);
            $("#loader-overlay").remove();
            $("#add_data").prop('disabled', false);
        }
    });
</script>