<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");

// if (!isset($_COOKIE["cookieTableWarehouse"])) {
//     $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
//     $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
//     $settingsCheckbox_concised_view = unserialize($settingsCh);
//     if ($settingsCheckbox_concised_view) {
//         setcookie("cookieStorageloc", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
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


$columnMapping = [
    [
        'name' => 'As on Date',
        'slag' => 'report_date',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
    ],
    [
        'name' => 'Item Code',
        'slag' => 'warehouse_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Item Name',
        'slag' => 'item_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Type',
        'slag' => 'goodTypes.`goodTypeName`',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Total Qty',
        'slag' => 'qty',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'UOM',
        'slag' => 'uom',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Valuation Class',
        'slag' => 'valclass',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Price(MW)',
        'slag' => 'mwprice',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Target Price',
        'slag' => 'price',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Value',
        'slag' => 'value',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Last Received On',
        'slag' => 'last',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ]
];

?>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-stock-new  vitwo-alpha-global">
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
                                                <h3 class="card-title mb-0">Goods Stock</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <div class="stock-as-date">
                                                <p> Stock As On : <span id="asondate"></span></p>
                                            </div>
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

                                            <!-- <table class="table defaultDataTable table-hover" data-paging="true" data-responsive="false"> -->
                                            <table id="dataTable_detailed_view" class="table table-hover table-nowrap stock-new-table transactional-book-table">

                                                <thead>
                                                    <tr>
                                                        <?php
                                                        foreach ($columnMapping as $index => $column) {
                                                            if ($column['dataType'] !== 'date') {
                                                        ?>
                                                                <th data-value="<?= $index ?>"><?= $column['name'] ?></th>
                                                        <?php
                                                            }
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
                                                                            $cookieTableStockReport = json_decode($_COOKIE["cookieTableStockReport"], true) ?? [];

                                                                            foreach ($columnMapping as $index => $column) {
                                                                                if ($column['dataType'] !== 'date') {
                                                                            ?>
                                                                                    <tr>
                                                                                        <td valign="top" style="width: 165px">

                                                                                            <input type="checkbox" class="settingsCheckbox_detailed" name="settingsCheckbox[]" id="settingsCheckbox_detailed_view[]" value='<?= $column['slag'] ?>'>
                                                                                            <?= $column['name'] ?>
                                                                                        </td>
                                                                                    </tr>
                                                                            <?php
                                                                                }
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

                                            <div class="modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", "=", "!=", ">=", ">", "<", "<=", "BETWEEN"];

                                                                        foreach ($columnMapping as $columnIndex  => $column) {
                                                                            if ($columnIndex === 1 || $column['slag'] === 'total_value') {
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
                                                                                            $operator = array_slice($operators, -2, 1);
                                                                                            foreach ($operator as $oper) {
                                                                                        ?>
                                                                                                <option value="<?= $oper ?>"><?= $oper ?></option>
                                                                                            <?php
                                                                                            }
                                                                                        } elseif ($column['dataType'] === "number") {
                                                                                            $operator = array_slice($operators, 2, 6);
                                                                                            foreach ($operator as $oper) { ?>
                                                                                                <option value="<?= $oper ?>"><?= $oper ?></option>
                                                                                                <?php  }
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
                                                                                    <?php $value = ($column['dataType'] === 'date') ? date('Y-m-d') : ''; ?>
                                                                                    <input type="<?= ($column['dataType'] === 'date') ? 'date' : 'input' ?>" data-operator-val="" name="value[]" class="fld form-control m-input" id="value_<?= $columnIndex ?>" placeholder="Enter Keyword" value=<?= $value ?>>
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
    </section>
    <!-- /.content -->
    <!-- right modal main start -->
    <div class="modal fade right customer-modal inventory-p-modal" id="viewGlobalModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
            <!--Content-->
            <div class="modal-content">
                <!--Header-->
                <div class="modal-header">
                    <p id="itemCodeModal"></p>
                    <p id="itemNameModal"></p>
                    <p>Item Price: <span id="itemPrice"></span></p>
                    <p>Item MWP: <span id="itemPriceModal"></span></p>
                    <p>Item Valuation: <span id="itemValModal"></span></p>
                    <p>Item Total Quantity: <span id="itemTotalModal"></span></p>

                    <ul class="nav nav-pills nav-tabs mb-3" id="pills-tab" role="tablist">

                        <li class="nav-item">
                            <a class="nav-link" id="home-tab-stockDetails" data-toggle="tab" href="#home_" role="tab" aria-controls="home" aria-selected="true">Stock Details</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link stockLogView" id="stockLogtab" data-toggle="tab" href="#profile_" role="tab" aria-controls="profile" aria-selected="false">Stock log</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="contact-tab" href="<?= BASE_URL ?>branch/location/manage-stock-transfer.php" target="_blank">Transfer</a>
                        </li>
                    </ul>

                </div>
                <div class="modal-body">
                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="home_" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <div class="row">
                                <div class="col-4">
                                    Type
                                </div>
                                <div class="col-4">
                                    Open
                                </div>
                                <div class="col-4">
                                    Reserve
                                </div>
                                <div class="col-4">
                                    RM warehouse
                                </div>
                                <div class="col-4">
                                    <p id="rmWareopen"></p>
                                </div>
                                <div class="col-4">
                                    <p id="rmWareres"></p>

                                </div>
                                <div class="col-4">
                                    RM production
                                </div>
                                <div class="col-4">
                                    <p id="rmProdopen"></p>

                                </div>
                                <div class="col-4">
                                    <p id="rmProdres"></p>

                                </div>
                                <div class="col-4">
                                    SFG Stock
                                </div>
                                <div class="col-4">
                                    <p id="sfgStockopen"></p>
                                </div>
                                <div class="col-4">
                                    <p id="sfgStockres"></p>
                                </div>
                                <div class="col-4">
                                    FG warehouse
                                </div>
                                <div class="col-4">
                                    <p id="fgwareopen"></p>

                                </div>
                                <div class="col-4">
                                    <p id="fgwareres"></p>

                                </div>
                                <div class="col-4">
                                    FG Marketing
                                </div>
                                <div class="col-4">
                                    <p id="fgMarkopen"></p>

                                </div>
                                <div class="col-4">
                                    <p id="fgMarkres"></p>

                                </div>
                                <div class="col-4">
                                    QA Location
                                </div>
                                <div class="col-4">
                                    <p id="qaMarkopen"></p>

                                </div>
                                <div class="col-4">
                                    <p id="qaMarkres"></p>

                                </div>

                            </div>
                        </div>

                        <div class="tab-pane fade profile-tab-stock-log inventory-p-stock-log stock-log" id="profile_" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <div class="length-row inner-length-row">
                                <span>Show</span>
                                <select name="" id="" class="custom-select-inner" value="25">
                                    <option value="10">10</option>
                                    <option value="25" selected="selected">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="200">200</option>
                                    <option value="250">250</option>
                                </select>
                                <span>Entries</span>
                            </div>
                           
                            <table class="table table-hover stockLogTable transactional-book-table inventory-view-list" id="stockLogsTable" data-responsive="false">
                                <thead>
                                    <tr>
                                        <!-- <th>Sl No</th> -->
                                        <th>Storage Type</th>
                                        <th>Item Quantity</th>
                                        <th>Item UOM</th>
                                        <th>Item Price</th>
                                        <th>Reference</th>
                                        <th>Minimum Stock</th>
                                        <th>Maximum Stock</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>

                                <?php
                                ?>
                                <tbody class="stock-log-body">
                                </tbody>
                            </table>
                            <div class="row custom-table-footer">
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div id="limitTextinner" class="limit-text">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div id="yourDataTable_paginateinner">
                                        <div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <div class="card">
                                <div class="card-body pt-3 pl-4 pr-4 pb-4">

                                    <form action="" method="POST" id="transfer" name="transfer">

                                        <input type="hidden" name="createData" id="createData" value="">
                                        <div class="row po-form-creation">

                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="card so-creation-card po-creation-card">
                                                    <div class="card-header">
                                                        <div class="row others-info-head">
                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                <div class="head">
                                                                    <i class="fa fa-info"></i>
                                                                    <h4>Movement</h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body others-info vendor-info so-card-body">
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12">

                                                                <div class="row info-form-view">
                                                                    <div class="col-lg-6 col-md-6 col-sm-12 form-inline">
                                                                        <label for="">Movement Types</label>
                                                                        <select name="movemenrtypesDropdown" id="movemenrtypesDropdown" class="form-control">
                                                                            <option value="">Select</option>
                                                                            <option value="storage_location">Storage Location to Storage Location</option>
                                                                            <!-- <option value="item">Item To Item</option> -->

                                                                        </select>

                                                                    </div>

                                                                    <div class="col-lg-6 col-md-6 col-sm-12 cost-center-col">

                                                                        <div class="sl">

                                                                            <label for="">Destination Storage Location</label>
                                                                            <select name="sl" class="select2 form-control ">
                                                                                <option value="">Select Storage Location</option>
                                                                                <option value="rmWhOpen">RM Open</option>
                                                                                <option value="rmProdOpen">RM Production Open</option>
                                                                                <option value="sfgStockOpen">SFG Open</option>
                                                                                <option value="fgWhOpen">FG Open</option>
                                                                                <option value="fgMktOpen">FG Market Open</option>

                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row info-form-view">

                                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                                            <label for="date">Creation Dates</label>
                                                                            <input type="date" name="creationDate" class="form-control" min="<?= $min ?>" max="<?= $max ?>">
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

                                                <div class="card items-select-table">

                                                    <div class="col-lg col-md-6 col-sm-6">

                                                    </div>

                                                    <table class="table tabel-hover table-nowrap">
                                                        <thead>
                                                            <tr>
                                                                <th>Item </th>
                                                                <th>UOM</th>
                                                                <th>Source Storage Location</th>
                                                                <th>Qty</th>



                                                            </tr>
                                                        </thead>
                                                        <tbody id="">
                                                            <tr id="">
                                                                <td><select name="item[1][name]" id="itemsDropDown_<?= $oneInvItem["stockSummaryId"] ?>" data-val="<?= $oneInvItem["stockSummaryId"] ?>" class="select2 form-control itemsDropDown itemsDropDown_<?= $oneInvItem["stockSummaryId"] ?>">
                                                                        <option value="<?= $oneInvItem["itemId"] ?>" selected><?= $oneInvItem["itemName"]  ?> </option>

                                                                    </select>
                                                                </td>
                                                                <td>

                                                                    <select name="item[1][uom]" id="uom_<?= $oneInvItem["stockSummaryId"] ?>" class="select2 form-control uom uom_<?= $oneInvItem["stockSummaryId"] ?>">
                                                                        <option value="">UOM</option>


                                                                        <option value="<?= $oneInvItem['baseUnitMeasure'] ?>"><?= $buom ?></option>
                                                                        <option value="<?= $oneInvItem['issueUnitMeasure'] ?>"><?= $iuom ?></option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <select name="item[1][storagelocation]" data-val="<?= $oneInvItem["stockSummaryId"] ?>" id="storagelocation_<?= $oneInvItem["stockSummaryId"] ?>" class="select2 form-control storagelocation storagelocation_<?= $oneInvItem["stockSummaryId"] ?>">
                                                                        <option value="">Select Storage Location</option>
                                                                        <option value="rmWhOpen">RM Open</option>


                                                                        <option value="rmProdOpen">RM Production Open</option>
                                                                        <option value="sfgStockOpen">SFG Open</option>
                                                                        <option value="fgWhOpen">FG Open</option>
                                                                        <option value="fgMktOpen">FG Market Open</option>


                                                                    </select>
                                                                </td>
                                                                <td><input id="quantity_<?= $oneInvItem["stockSummaryId"] ?>" class="form-control quantity quantity_<?= $oneInvItem["stockSummaryId"] ?>" type="number" name="item[1][quantity]">
                                                                    <p id="quan_error_<?= $oneInvItem["stockSummaryId"] ?>" class="text-danger"></p>
                                                                </td>



                                                            </tr>

                                                        </tbody>
                                                    </table>


                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12">

                                                <button type="submit" id="subBtn" name="addNewPOFormSubmitBtn" class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Save & Close</button>

                                            </div>
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

    <!-- right modal main end -->
</div>

<?php
require_once("../common/footer2.php");
?>

<script>
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

                buttons: [{
                    extend: 'collection',
                    text: '<ion-icon name="download-outline"></ion-icon> Export',
                    buttons: [

                        {
                            extend: 'csv',
                            text: '<ion-icon name="document-outline" class="ion-csv"></ion-icon> CSV'
                        }
                    ]
                }],
                // select: true,
                "bPaginate": false,
            });

        }
        let asonDate;

        // display ason date
        $("#asondate").html(formatDate($("#value_0").val()));

        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');
        initializeDataTable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;

            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookiesgoodsStock');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-goods-stock.php",
                dataType: 'json',
                data: {
                    act: 'goodsStock',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit
                },
                beforeSend: function() {
                    $("#detailed_tbody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
                },
                success: function(response) {

                    console.log(response);

                    if (response.status) {
                        var responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(length - 1).visible(true);

                        $.each(responseObj, function(index, value) {
                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.itemId}" data-itemname="${value.itemName}" data-itemcode="${value.itemCode}" data-targetPrice='${value.targetPrice}' data-valuationclass="${value.priceType}" data-totalquan="${value.total_qty}" data-pricemwt="${value.movingWeightedPrice}"  data-toggle="modal" data-target="#viewGlobalModal">${ value.itemCode}</a>`,
                                ` <p class='pre-normal'> ${value.itemName}</p>`,
                                value.goodType,
                                value.total_qty,
                                value.uomName,
                                value.priceType,
                                value.movingWeightedPrice,
                                value.targetPrice,
                                value.value,
                                value.borndate,
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
                                        <button class="soModal" data-id="${value.itemId}" data-itemname="${value.itemName}" data-itemcode="${value.itemCode}" data-valuationclass="${value.valuation_class}" data-totalquan="${value.total_quantity}" data-pricemwt="${value.price}"  data-toggle="modal" data-target="#viewGlobalModal"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
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

                            // console.log('Cookie value:', checkboxSettings);

                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);

                                }
                            });

                            // console.log('Cookie value:', checkboxSettings);
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
                $("#asondate").html(formatDate($("#value_0").val()));
                let values;
                $(".selectOperator").each(function() {
                    let columnIndex = ($(this).attr("id")).split("_")[1];
                    let columnSlag = $(`#columnSlag_${columnIndex}`).val();
                    let operatorName = $(`#selectOperator_${columnIndex}`).val();
                    let value = $(`#value_${columnIndex}`).val() ?? "";
                    let value2 = $(`#value2_${columnIndex}`).val() ?? "";
                    let value3 = $(`#value3_${columnIndex}`).val() ?? "";
                    let value4 = $(`#value4_${columnIndex}`).val() ?? "";

                    if (columnSlag === 'delivery_date') {
                        values = value4;
                    } else if (columnSlag === 'so_date') {
                        values = value2;
                    } else if (columnSlag === 'created_at') {
                        values = value3;
                    }

                    if ((columnSlag === 'delivery_date' || columnSlag === 'so_date' || columnSlag === 'created_at') && operatorName == "BETWEEN") {
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
            var fromData = {};
            $(".settingsCheckbox_detailed").each(function() {
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

            console.log(fromData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: 'json',
                    data: {
                        act: 'goodsStock',
                        fromData: fromData
                    },
                    success: function(response) {
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
            if (columnName === 'Delivery Date') {
                inputId = "value4_" + columnIndex;
            } else if (columnName === 'SO Date') {
                inputId = "value2_" + columnIndex;
            } else if (columnName === 'Created Date') {
                inputId = "value3_" + columnIndex;
            }

            if ((columnName === 'Delivery Date' || columnName === 'SO Date' || columnName === 'Created Date') && operatorName === 'BETWEEN') {
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


<!-- modal script -->

<script>
    $(document).ready(function() {
        let table;

        table = $('#stockLogsTable').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,

            buttons: [{
                extend: 'collection',
                text: '<ion-icon name="download-outline"></ion-icon> Export',
                buttons: [{
                    extend: 'excel',
                    text: '<ion-icon name="document-outline" class="ion-excel"></ion-icon> Excel',
                    filename: '<?= $newFileName ?> '
                }]
            }],
            // select: true,
            "bPaginate": false,
        });

        let itemId;
        let asonDate;

        // display ason date
        $("#asondate").html(formatDate($("#value_0").val()));


        $(document).on("click", ".soModal", function() {

            itemId = $(this).data('id');
            asonDate = $("#value_0").val();
            $("#home-tab-stockDetails").tab('show');
            innerTable(maxlimit = "", page_id = "");

            $("#itemNameModal").html($(this).data('itemname'));
            $("#itemPrice").html($(this).data('targetprice'));
            $("#itemCodeModal").html($(this).data('itemcode'));
            $("#itemValModal").html($(this).data('valuationclass'));
            $("#itemPriceModal").html($(this).data('pricemwt'));
            $("#itemTotalModal").html($(this).data('totalquan'));


            $.ajax({
                type: "GET",
                url: 'ajaxs/modals/wms/ajax-manage-goods-stock-details.php',
                data: {
                    act: "stock-detail",
                    itemId: itemId,
                    dDate: asonDate
                },
                beforeSend: function() {},
                success: function(res) {
                    var jsonObject = JSON.parse(res);
                    console.log(jsonObject);

                    $("#rmWareopen").html(jsonObject.rmWhOpen_qty ?? 0);
                    $("#rmWareres").html(jsonObject.rmWhReserve_qty ?? 0);

                    $("#rmProdopen").html(jsonObject.rmProdOpen_qty ?? 0);
                    $("#rmProdres").html(jsonObject.rmProdReserve_qty ?? 0);

                    $("#sfgStockopen").html(jsonObject.sfgStockOpen_qty ?? 0);
                    $("#sfgStockres").html(jsonObject.sfgStockReserve_qty ?? 0);

                    $("#fgwareopen").html(jsonObject.fgWhOpen_qty ?? 0);
                    $("#fgwareres").html(jsonObject.fgWhReserve_qty ?? 0);

                    $("#fgMarkopen").html(jsonObject.fgMktOpen_qty ?? 0);
                    $("#fgMarkres").html(jsonObject.fgMktReserve_qty ?? 0);

                    $("#qaMarkopen").html(jsonObject.QaLocation_qty);
                    $("#qaMarkres").html(0);


                }
            });


        });

        function innerTable(maxlimit = "", page_id = "") {

            // alert(ddate);
            $.ajax({
                type: "GET",
                url: 'ajaxs/modals/wms/ajax-manage-goods-stock-details.php',
                dataType: "json",
                data: {
                    act: "stocklog",
                    itemId: itemId,
                    maxlimit: maxlimit,
                    page_id: page_id
                },
                beforeSend: function() {
                    $(`.stock-log-body`).html(` <tr>
                    <td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>
                                </tr>`);

                },
                success: function(res) {

                    let resObj = res.data;
                    console.log(res);
                    // let sl = 1;
                    table.clear().draw();
                    $("#limitTextinner").html(res.limitTxt);
                    $("#yourDataTable_paginateinner").html(res.pagination);


                    $.each(resObj, function(index, value) {
                        table.row.add([
                            value.storageType,
                            value.itemQty,
                            value.itemUom,
                            value.itemPrice,
                            value.logRef,
                            value.min_stock,
                            value.max_stock,
                            value.createdBy,
                            value.createdAt
                           
                        ]).draw(false);
                    });

                }
            });

        }

        $(document).on("change", ".custom-select-inner ", function(e) {
            var maxlimit = $(this).val();
            var dateason = $("#form_date_s_inner").val();
            innerTable(maxlimit, page_id = "", ddate = dateason);

        });

        $(document).on("click", "#paginationinner a ", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $(".custom-select-inner").val();
            var dateason = $("#form_date_s_inner").val();
            innerTable(maxlimit = limitDisplay, page_id = page_id, ddate = dateason);

        });

        $(document).on("click", "#dateSearchInner", function(e) {
            var dateason = $("#form_date_s_inner").val();
            innerTable(maxlimit = "", page_id = "", ddate = dateason);
        });
    });
</script>