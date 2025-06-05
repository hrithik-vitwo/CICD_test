<?php
require_once("../../app/v1/connection-branch-admin.php");

// if (!isset($_COOKIE["cookiegooditem"])) {
//     $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
//     $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
//     $settingsCheckbox_concised_view = unserialize($settingsCh);
//     if ($settingsCheckbox_concised_view) {
//         setcookie("cookiegooditem", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
//     }
// }

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

$goodsController = new GoodsController();

if (isset($_POST["creategoodsdata"])) {

    $addNewObj = $goodsController->createGoods($_POST + $_FILES);

    if ($addNewObj["status"] == "success") {

        swalAlert($addNewObj["status"], ucfirst($addNewObj["status"]), $addNewObj["message"], BASE_URL . "branch/location/goods.php");
    } else {
        swalAlert($addNewObj["status"], ucfirst($addNewObj["status"]), $addNewObj["message"]);
    }
}

if (isset($_POST["createLocationItem"])) {
    $addNewObj = $goodsController->createGoodsLocation($_POST);
    // console($_POST);
    // console($addNewObj);
    // exit();
    swalToast($addNewObj["status"], $addNewObj["message"], BASE_URL . "branch/location/goods.php?locItemId='" . base64_encode(1) . "'");
}

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
        'slag' => 'group_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Type',
        'slag' => 'itemtype.goodTypeName',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Price',
        'slag' => 'mwp',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Valuation Class',
        'slag' => 'val_class',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Target Price',
        'slag' => 'target_price',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Created By',
        'slag' => 'item.createdBy',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'BOM Status',
        'slag' => 'isBomRequired',
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


<style>
    .card-header.add-locationitem-header {
        padding: 11px 15px;
    }

    .card-header.add-locationitem-header h4 {
        font-size: 0.9rem;
        color: #fff;
        margin-bottom: 0;
    }
</style>


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

            <!-- trying start-->
            <?php
            $locItemId = 0;
            if ($_GET['locItemId']) {
                $locItemId = base64_decode($_GET['locItemId']);
            }
            ?>
            <input type="hidden" name="getLocationId" id="getLocationId" value="<?= $locItemId ?>">
            <!-- trying end-->

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
                                                <h3 class="card-title mb-0">Item Master</h3> <?php echo ($locItemId != 0) ? ' <span>(Items that are not in this location)</span>' : ''; ?>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <?php require_once("components/mm/goodsList/goodsCommonList.php"); ?>
                                            <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()"><i class="fa fa-expand fa-2x"></i></button>
                                        </div>
                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>

                            <div class="card card-tabs mb-0" style="border-radius: 20px;">
                                <div class="card-body">
                                    <!-- <div class="row filter-search">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row table-header-item">

                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            
                                        </div>
                                    </div> -->
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

                                            <div class="exportgroup">
                                                <button class="exceltype btn btn-primary btn-export" type="button">
                                                    <ion-icon name="download-outline"></ion-icon>
                                                    Export
                                                </button>
                                                <ul class="export-options">
                                                    <li>
                                                        <button class="ion-paginationliststock">
                                                            <ion-icon name="list-outline" class="ion-paginationliststock md hydrated" role="img" aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fullliststock">
                                                            <ion-icon name="list-outline" class="ion-fullliststock md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="goods-actions.php?create" class="btn btn-create waves-effect waves-light" type="button">
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
                                                                            if ($columnIndex === 0 || $column['name'] === 'UOM' ||  $column['name'] === 'Group' ||  $column['name'] === 'Valuation Class' || $column['name'] === 'Target Price' || $column['name'] == 'Price') {
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
                                                                    <button class="nav-link classicview-btn classicview-link" id="nav-classicview-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-classicview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Preview</button>
                                                                    <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                                                </div>
                                                            </nav>
                                                            <div class="tab-content global-tab-content" id="nav-tabContent">

                                                                <div class="tab-pane fade show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">

                                                                    <div class="d-flex nav-overview-tabs" id="navBtn">
                                                                    </div>
                                                                    <!-- other item -->
                                                                    <div class="row" id="otherItem">
                                                                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                                                            <div class="view-block details-block">
                                                                                <div class="items-table">
                                                                                    <h6>Basic Details</h6>
                                                                                    <div class="item-details">
                                                                                        <div class="form-input">
                                                                                            <label for="">Item Name</label>
                                                                                            <p><span id="itemName"></span></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">Item Description</label>
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
                                                                                    <h6>Group</h6>
                                                                                    <div class="item-details">
                                                                                        <div class="row">
                                                                                            <div class="col-6 col-lg-6 col-md-6 col-sm-12">
                                                                                                <div class="form-input">
                                                                                                    <label for="">Item Type</label>
                                                                                                    <p><span id="itemType"></span></p>
                                                                                                </div>
                                                                                                <div class="form-input">
                                                                                                    <label for="">Availability Check</label>
                                                                                                    <p><span id="avCheck"></span></p>
                                                                                                </div>
                                                                                                <div class="form-input mt-5">
                                                                                                    <p class="note">Note : <span id="noteGroup"></span></p>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-6 col-lg-6 col-md-6 col-sm-12">
                                                                                                <div class="form-input">
                                                                                                    <label for="">Item Good Group</label>
                                                                                                    <div class="group-tree" id="itemTree">
                                                                                                    </div>

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
                                                                                    <h6>Item Images</h6>
                                                                                    <div class="goods-img" id="goodImg">
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
                                                                            <div class="view-block price-discount-block" id="priceDiscBlock">
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
                                                                            <div class="view-block group-block">
                                                                                <div class="items-table">
                                                                                    <h6>Other Group Info</h6>
                                                                                    <div class="item-details">
                                                                                        <div class="row">
                                                                                            <div class="col-6 col-lg-6 col-md-6 col-sm-12">
                                                                                                <div class="form-input">
                                                                                                    <label for="">Purchase Group</label>
                                                                                                    <p><span id="purchaseGroupName"></span></p>
                                                                                                </div>

                                                                                            </div>
                                                                                            <div class="col-6 col-lg-6 col-md-6 col-sm-12" id="discountGroupDiv">
                                                                                                <div class="form-input">
                                                                                                    <label for="">Discount Group</label>
                                                                                                    <div class="group-tree" id="discountGroupName">
                                                                                                        <ul class="other-grp">
                                                                                                            <li>
                                                                                                                <p>Test</p>
                                                                                                            </li>
                                                                                                            <li>
                                                                                                                <p>Test</p>
                                                                                                            </li>
                                                                                                            <li>
                                                                                                                <p>Test</p>
                                                                                                            </li>
                                                                                                        </ul>
                                                                                                    </div>

                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- service item -->
                                                                    <div class="row" id="serviceItem">

                                                                        <div class="view-block storage-block">
                                                                            <div class="items-table">
                                                                                <h6>Service Details</h6>
                                                                                <div class="storage-details">
                                                                                    <div class="form-input">
                                                                                        <label for="storageControl">Name</label>
                                                                                        <p id="serviceName"></p>
                                                                                    </div>
                                                                                    <div class="form-input">
                                                                                        <label for="maxStoragePeriod">Description</label>
                                                                                        <p id="serviceDesc"></p>
                                                                                    </div>
                                                                                    <div class="form-input">
                                                                                        <label for="defaultStorageLocation">HSN</label>
                                                                                        <p id="serviceHsn"></p>
                                                                                    </div>
                                                                                    <div class="form-input">
                                                                                        <label for="minimumRemainSelfLife">GL Code</label>
                                                                                        <p id="glCode"></p>
                                                                                    </div>
                                                                                    <div class="form-input">
                                                                                        <label for="minimumRemainSelfLife">GL Details</label>
                                                                                        <p id="glDetails"></p>
                                                                                    </div>
                                                                                    <div class="form-input">
                                                                                        <label for="minTimeUnit">TDS</label>
                                                                                        <p id="serviceTds"></p>
                                                                                    </div>
                                                                                    <div class="form-input">
                                                                                        <label for="maxTimeUnit">Service Unit</label>
                                                                                        <p id="serviceUnit"></p>
                                                                                    </div>
                                                                                    <div class="form-input">
                                                                                        <label for="minimumStock">Service Target Price</label>
                                                                                        <p id="serviceTargetPrice"></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="view-block group-block">
                                                                            <div class="items-table">
                                                                                <h6>Group</h6>
                                                                                <div class="item-details">
                                                                                    <div class="row">
                                                                                        <div class="col-6 col-lg-6 col-md-6 col-sm-12">
                                                                                            <div class="form-input">
                                                                                                <label for="">Service Type</label>
                                                                                                <p><span id="serviceType"></span></p>
                                                                                            </div>

                                                                                            <div class="form-input mt-5">
                                                                                                <p class="note">Note : <span id="noteGroupService"></span></p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-6 col-lg-6 col-md-6 col-sm-12">
                                                                                            <div class="form-input">
                                                                                                <label for="">Service Group</label>
                                                                                                <div class="group-tree" id="serviceTree">
                                                                                                </div>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- other group info  -->
                                                                        <div class="view-block group-block" id="discountGroupDivService">
                                                                            <div class="items-table">
                                                                                <h6>Other Group Info</h6>
                                                                                <div class="item-details">
                                                                                    <div class="row">

                                                                                        <div class="col-6 col-lg-6 col-md-6 col-sm-12">
                                                                                            <div class="form-input">
                                                                                                <label for="">Discount Group</label>
                                                                                                <div class="group-tree" id="discountGroupNameService">
                                                                                                    <ul class="other-grp">
                                                                                                        <li>
                                                                                                            <p>Test</p>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                            <p>Test</p>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                            <p>Test</p>
                                                                                                        </li>
                                                                                                    </ul>
                                                                                                </div>

                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
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


                                            <!-- Add Location Item start-->
                                            <div class="modal fade hsn-dropdown-modal" id="addToLocationModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                                                <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">


                                                        </div>
                                                        <div class="modal-body" style="height: 500px; overflow: auto;">
                                                            <form action="goods-actions.php" method="post" id="locationItemFrom">
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Add Location Item end -->

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    </section>
    <!-- /.content -->
</div>

<?php
require_once("../common/footer2.php");
?>


<script>
    const table = new DataTable('#example', {
        ajax: '../php/staff.php',
        columns: [{
                data: null,
                render: (data) => data.first_name + ' ' + data.last_name
            },
            {
                data: 'position'
            },
            {
                data: 'office'
            },
            {
                data: 'extn'
            },
            {
                data: 'start_date'
            },
            {
                data: 'salary',
                render: DataTable.render.number(null, null, 0, '$')
            }
        ],
        colReorder: true,

    });
</script>

<script>
    let columnMapping = <?php echo json_encode($columnMapping); ?>;
    let data;
    $(document).ready(function() {
        let indexValues = [];
        let dataTable;

        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
                "lengthMenu": [10, 25, 50, 100, 200, 250],
                "ordering": false,
                info: false,
                "initComplete": function(settings, json) {
                    $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
                },

                buttons: [
                    //     {
                    //     extend: 'collection',
                    //     text: '<ion-icon name="download-outline"></ion-icon> Export',
                    //     buttons: [{
                    //         extend: 'csv',
                    //         text: '<ion-icon name="document-outline" class="ion-csv"></ion-icon> CSV'
                    //     }],
                    // }
                ],
                // select: true,
                "bPaginate": false,
            });

        }
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            let fdate = "<?php echo $f_date; ?>";
            let to_date = "<?php echo $to_date; ?>";
            let comid = <?php echo $company_id; ?>;
            let locId = <?php echo $location_id; ?>;
            let bId = <?php echo $branch_id; ?>;
            let columnMapping = <?php echo json_encode($columnMapping); ?>;
            let checkboxSettings = Cookies.get('cookiegooditem');
            let notVisibleColArr = [];
            let locItemId = $("#getLocationId").val();


            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-goods.php",
                dataType: 'json',
                data: {
                    act: 'goods',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit,
                    locItemId
                },
                beforeSend: function() {
                    $("#detailed_tbody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
                    let loader = `<div class="load-wrapp" id="globalModalLoader">
                                    <div class="load-1">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>`;

                    // Append the new HTML to the modal-body element
                    $('#viewGlobalModal .modal-body').append(loader);
                },
                success: function(response) {

                    console.log(response);

                    if (response.status) {
                        let responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();
                        data = responseObj;
                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(length - 1).visible(true);

                        $.each(responseObj, function(index, value) {

                            let status = ``;
                            if (value.status == 'active') {
                                status = `<p class="status-bg status-open stChange" data-id="${value.itemId}" data-code="${value.itemCode}">Active</p>`
                            } else if (value.status == 'inactive') {
                                status = `<p class="status-bg status-approved " data-id="${value.itemId}" data-code="${value.itemCode}">Inactive</p>`
                            } else if (value.status == 'draft') {
                                status = `<p class="status-bg status-partialpaid stChange" data-id="${value.itemId}" data-code="${value.itemCode}">Draft</p>`
                            }

                            let addItemBtn = '';
                            if (value.locItemId == "1") {
                                addItemBtn = `
                                    <li>
                                        <button class="addLocItem" data-id="${value.itemId}" data-type="${value.goodsType}" ><ion-icon name="add-circle-outline"></ion-icon>Add</button>
                                    </li>`;
                            }


                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.itemId}" data-code="${value.itemCode}" data-toggle="modal" data-target="#viewGlobalModal">${ value.itemCode}</a>`,
                                `<p class="pre-normal w-200">${value.itemName}</p>`,
                                value.uom,
                                `<p class='pre-normal'> ${value.group_name}</p>`,
                                value["itemtype.goodTypeName"],
                                value.mwp,
                                value.val_class,
                                value.target_price,
                                value["item.createdBy"],
                                value.bom_status,
                                status,
                                `<div class="dropout">
                                        <button class="more">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </button>
                                        <ul>
                                            <li>
                                                <button class="editBtn" data-toggle="modal" data-id="${value.itemId}" data-code="${value.itemCode}" ><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                            </li>     

                                            <li>
                                            <button class="deleteGood" data-id="${value.itemId}" data-code="${value.itemCode}"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>
                                            </li>  

                                            <li>
                                                <button class="soModal" data-toggle="modal" data-id="${value.itemId}" data-code="${value.itemCode}"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                            </li>

                                            ${addItemBtn}
                                
                                        </ul>                                                                      
                                    </div>`
                            ]).draw(false);
                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (checkboxSettings) {
                            let checkedColumns = JSON.parse(checkboxSettings);

                            $(".settingsCheckbox_detailed").each(function(index) {
                                let columnVal = $(this).val();
                                if (checkedColumns.includes(columnVal)) {
                                    $(this).prop("checked", true);
                                    dataTable.column(index).visible(true);
                                } else {
                                    notVisibleColArr.push(index);
                                }
                            });

                            notVisibleColArr.forEach(function(index) {
                                dataTable.column(index).visible(false);
                            });

                            console.log('Cookie value:', checkboxSettings);

                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);
                                }
                            });
                            console.log("Cookies blank");
                        }


                    } else {

                        $("#detailed_tbody").html(`<td colspan=17 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
                        $('#yourDataTable_paginate').remove();
                        $('#limitText').remove();
                    }
                    $("#globalModalLoader").remove();
                },
                complete: function() {
                    $("#globalModalLoader").remove();

                },
            });
        }

        fill_datatable();

        $(document).on("click", ".ion-paginationliststock", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(data),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiegooditem')
                },
                beforeSend: function() {
                    $('#loaderModal').show();
                    $('.ion-paginationliststock').prop('disabled', true)
                },

                success: function(response) {
                    console.log(response);
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
                },
                complete: function() {
                    // Hide loader modal after request completes
                    $('#loaderModal').hide();
                    $('.ion-paginationliststock').prop('disabled', false);
                }
            })

        });
        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function(e) {
            let maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);

        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a ", function(e) {
            e.preventDefault();
            let page_id = $(this).attr('id');
            let limitDisplay = $(".custom-select").val();

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
                $("#myForm")[0].reset();

            });

            $(document).on("click", "#serach_reset", function(e) {
                e.preventDefault();
                $("#myForm")[0].reset();
                $("#serach_submit").click();
            });

            $(document).on("keypress", "#myForm input", function(e) {
                if (e.key === "Enter") {
                    $("#serach_submit").click();
                    e.preventDefault();
                }
            });
        });

        $(document).on("click", ".ion-fullliststock", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-goods.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    formDatas: formInputs,
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiegooditem')
                },
                beforeSend: function() {
                    $('#loaderModal').show();
                    $('.ion-fullliststock').prop('disabled', true)
                },
                success: function(response) {
                    console.log(response);
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
                },
                complete: function() {
                    // Hide loader modal after request completes
                    $('#loaderModal').hide();
                    $('.ion-fullliststock').prop('disabled', false)
                }
            })

        });
        // -------------checkbox----------------------

        $(document).ready(function() {
            let columnMapping = <?php echo json_encode($columnMapping); ?>;

            let indexValues = [];

            function toggleColumnVisibility(columnIndex, checkbox) {
                let column = dataTable.column(columnIndex);
                column.visible(checkbox.checked);

            }

            $("input[name='settingsCheckbox[]']").change(function() {
                let columnVal = $(this).val();
                console.log(columnVal);

                let index = columnMapping.findIndex(function(column) {
                    return column.slag === columnVal;
                });
                console.log(index);
                toggleColumnVisibility(index, this);
            });

            $(".grand-checkbox").on("click", function() {
                $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);
                $("input[name='settingsCheckbox[]']").each(function() {
                    let columnVal = $(this).val();
                    // console.log(columnVal);
                    let index = columnMapping.findIndex(function(column) {
                        return column.slag === columnVal;
                    });
                    if ($(this).is(':checked')) {
                        indexValues.push(index);
                    } else {
                        let removeIndex = indexValues.indexOf(index);
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
            let tablename = $("#tablename").val();
            let pageTableName = $("#pageTableName").val();
            let settingsCheckbox = [];
            let formData = {};
            $(".settingsCheckbox_detailed").each(function() {
                if ($(this).prop('checked')) {
                    let chkBox = $(this).val();
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
                        act: 'goods',
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
        let elem = document.getElementById("listTabPan")

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
    // build tree  structure for groups
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

        let btnObj = `<button class="editBtn btn btn-primary my-3" d-flex align-items-center data-toggle="modal" data-id="${itemId}" data-code="${code}" ><ion-icon name="create-outline"></ion-icon>Edit</button>`;
        $("#navBtn").html(btnObj);
        $('.auditTrail').attr("data-ccode", code);
        $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print.php?goods=${btoa(itemId)}`);
        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');
        console.log(itemId);

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/mm/ajax-manage-goods-modal.php",
            dataType: 'json',
            data: {
                act: 'modalData',
                itemId
            },
            beforeSend: function() {
                let loader = `<div class="load-wrapp" id="globalModalLoader">
                                    <div class="load-1">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>`;
                $('#viewGlobalModal .modal-body').append(loader);
            },
            success: function(value) {
                let responseObj = value.data;
                let dataObj = value.data.dataObj;
                // top nav
                $("#amount").html(dataObj.itemName);
                $("#po-numbers").html(dataObj.itemCode);
                $("#default_address").html(trimString(dataObj.itemDesc, 30));
                $("#default_address").attr("title", dataObj.itemDesc);
                $("#cus_name").html(responseObj.classification.glName);

                if (responseObj.type == 'other') {
                    $('#otherItem').show();
                    $('#serviceItem').hide();
                    $("#priceDiscBlock").hide();

                    if (dataObj.goodsType == "3") {
                        $("#priceDiscBlock").show();
                    }

                    //Item Basic Details
                    $("#itemName").html(dataObj.itemName);
                    $("#itemDesc").html(dataObj.itemDesc);
                    $("#hsnCode").html(dataObj.hsnCode);
                    $("#hsnDesc").html(trimString(responseObj.hsnDesc, 30));
                    $("#movWeightPrice").html(decimalAmount(responseObj.movWeightPrice));
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
                        let obj = `<p class="text-center text-xs">No Images Found </p>`
                        $("#goodImg").html(obj);
                    }

                    //Group
                    $("#itemType").html(responseObj.classification.glName);
                    $("#avCheck").html(dataObj.availabilityCheck);

                    $("#itemTree").html('');
                    let treeData = responseObj.tree;
                    const rootElement = document.querySelector('#itemTree');

                    // const treeContainer = document.getElementById('tree-container');
                    if (treeData.length > 0) {
                        const tree = buildTree(treeData, "tree");
                        rootElement.appendChild(tree);
                    }

                    // other Group Info
                    $("#purchaseGroupName").html(responseObj.purchaseGroupName);

                    $("#discountGroupName").html('');
                    let discountGroupName = responseObj.discountGroupName;

                    if (discountGroupName.length > 0) {
                        $("#discountGroupDiv").show();
                        let obj = buildTree(discountGroupName, "lst")
                        console.log(obj);
                        $("#discountGroupName").html(obj);
                    } else {
                        $("#discountGroupDiv").hide();
                    }

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
                    $('#defMrp').html(`${responseObj.companyCurrency} ${decimalAmount(responseObj.itemPrice)}`);
                    $('#defDiscount').html(`${decimalAmount(responseObj.itemMaxDiscount)} %`);

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
                } else if (responseObj.type == 'service') {
                    $('#otherItem').hide();
                    $('#serviceItem').show();
                    let serviceDetails = responseObj.serviceDetails;
                    let summaryData = responseObj.summaryData;
                    //Service
                    $("#serviceName").html(serviceDetails.itemName);
                    $("#serviceDesc").html(serviceDetails.itemDesc);
                    $("#serviceHsn").html(serviceDetails.hsnCode);
                    $("#glDetails").html(serviceDetails.glName);
                    $("#glCode").html(serviceDetails.glCode);
                    $("#serviceTargetPrice").html(parseFloat(summaryData.itemPrice).toFixed(2));
                    $("#serviceUnit").html(responseObj.serviceUnit);
                    $("#serviceTds").html(responseObj.dataObj.tds);
                    $("#serviceType").html(responseObj.classification.glName);

                    $("#serviceTree").html('');
                    let treeData = responseObj.tree;
                    const rootElement = document.querySelector('#serviceTree');

                    if (treeData.length > 0) {
                        const tree = buildTree(treeData, "tree");
                        rootElement.appendChild(tree);
                    }
                    // other Group Info
                    $("#discountGroupNameService").html('');
                    let discountGroupName = responseObj.discountGroupName;

                    if (discountGroupName.length > 0) {
                        $("#discountGroupDivService").show();
                        let obj = buildTree(discountGroupName, "lst")
                        console.log(obj);
                        $("#discountGroupNameService").html(obj);
                    } else {
                        $("#discountGroupDivService").hide();
                    }

                }

                // trail part
                $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
                $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);

                $("#globalModalLoader").remove();

            },
            complete: function() {
                $("#globalModalLoader").remove();
            },

            error: function(error) {
                console.log(error);
            }
        });

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/mm/ajax-manage-goods-modal.php",
            data: {
                act: 'classicView',
                itemId
            },
            beforeSend: function() {},
            success: function(res) {
                $(".classic-view").html(res);
            },
        })

    });
    $(document).on("click", ".stChange", function() {
        let itemId = $(this).data('id');
        let code = $(this).data('code');

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: `Are you sure to change status ${code} ?`,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Change'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type: "GET",
                    url: "ajaxs/modals/mm/ajax-manage-goods-modal.php",
                    dataType: "json",
                    data: {
                        act: 'stChange',
                        itemId
                    },
                    beforeSend: function() {

                    },
                    success: function(response) {
                        if (response.status == "success") {
                            Swal.fire({
                                icon: response.status,
                                title: response.message,
                                timer: 1000,
                                showConfirmButton: false,
                            }).then(() => {
                                location.reload();;
                            });
                        }
                    },

                });
            }

        });

    });
    $(document).on("click", ".editBtn", function() {
        let itemId = $(this).data('id');
        let url = `goods-actions.php?edit=${btoa(itemId)}`;
        let code = $(this).data('code');

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: `Are you sure to Edit this item ${code} ?`,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Edit'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });

    });
    $(document).on("click", ".deleteGood", function() {
        let id = $(this).data("id");
        let code = $(this).data("code");

        Swal.fire({
            icon: 'error',
            title: 'Are you sure?',
            text: `Are you sure to Delete ${code} ?`,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type: "GET",
                    url: "ajaxs/modals/mm/ajax-manage-goods-modal.php",
                    dataType: "json",
                    data: {
                        act: 'goodDel',
                        id
                    },
                    beforeSend: function() {},
                    success: function(response) {
                        if (response.status == "success") {
                            Swal.fire({
                                icon: response.status,
                                title: response.message,
                                timer: 1000,
                                showConfirmButton: false,
                            }).then(() => {
                                location.reload();
                            })
                        }
                    },

                });
            }

        });
    });
    $(document).on("click", ".addLocItem", function() {
        let itemId = $(this).data("id");
        let typeId = $(this).data("type");
        $('#locationItemFrom').html('');
        $('#addToLocationModal').modal('show');
        let formHtml = ``;

        formHtml = `
        <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
        <input type="hidden" name="item_id" value="${itemId}">
        <div class="col-lg-12 col-md-12 col-sm-12">
          <div class="card goods-creation-card so-creation-card po-creation-card">
            <div class="card-header add-locationitem-header">
              <h4>Storage Details</h4>
            </div>
            <div class="card-body goods-card-body others-info vendor-info so-card-body p-3">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="row goods-info-form-view customer-info-form-view">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input">
                        <label for="">Storage Control</label>
                        <input type="text" name="storageControl" class="form-control">
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input">
                        <label for="">Max Storage Period</label>
                        <input type="text" name="maxStoragePeriod" class="form-control">
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="form-input">
                        <label class="label-hidden" for="">Min Time Unit</label>
                        <select id="minTime" name="minTime" class="select2 form-control">
                          <option value="">Min Time Unit</option>
                          <option value="Day">Day</option>
                          <option value="Month">Month</option>
                          <option value="Hours">Hours</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="form-input">
                        <label for="">Minimum Remain Self life</label>
                        <input type="text" name="minRemainSelfLife" class="form-control">
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                      <div class="form-input">
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
      `;

        if (typeId === 3 || typeId === 5 || typeId === 4) {
            formHtml += `
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card goods-creation-card so-creation-card po-creation-card">
                <div class="card-header add-locationitem-header">
                    <h4>Pricing and Discount<span class="text-danger">*</span></h4>
                </div>
                <div class="card-body goods-card-body others-info vendor-info so-card-body p-3">
                    <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="row goods-info-form-view customer-info-form-view">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-input">
                            <label for="">Default MRP</label>
                            <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-input">
                            <label for="">Default Discount</label>
                            <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            `;
        }

        formHtml += `
        <div class="col-lg-12 col-md-12 col-sm-12">
          <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
        </div>
      `;
        $('#locationItemFrom').append(formHtml);
    });
</script>