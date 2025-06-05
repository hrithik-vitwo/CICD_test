<?php
// include_once("../../../app/v1/connection-branch-admin.php");
// include_once("../../common/header.php");
// include_once("../../common/navbar.php");
// include_once("../../common/sidebar.php");
// include_once("../../common/pagination.php");
// // Add Functions
// include_once("../../../app/v1/functions/branch/func-customers.php");
// include_once("../../../app/v1/functions/branch/func-journal.php");
// include_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
// include_once("../../../app/v1/functions/admin/func-company.php");
// include_once("../../../app/v1/functions/branch/func-goods-controller.php");


// if (!isset($_COOKIE["cookiesoDelivery"])) {
//     $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
//     $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
//     $settingsCheckbox_concised_view = unserialize($settingsCh);
//     if (settingsCheckbox_concised_view) {
//         setcookie("cookiesoDelivery", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
//     } else {
//         for ($i = 0; $i < 5; $i++) {
//             $isChecked = ($i < 5) ? 'checked' : '';
//         }
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
        'name' => 'Prepared Date',
        'slag' => 'preparedDate',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'COGM-M',
        'slag' => 'cogm_m',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'COGM-A',
        'slag' => 'cogm_a',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'COGM',
        'slag' => 'cogm',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'COGS',
        'slag' => 'cogs',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'MSP',
        'slag' => 'msp',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'bomStatus',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ]
];

?>


    <link rel="stylesheet" href="../../../public/assets/stock-report-new.css">

    <!-- Content Wrapper detailed-view -->
    <div class="content-wrapper report-wrapper is-stock-new vitwo-alpha-global">
        <!-- Content Header (Page header) -->
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php
                ?>

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
                                                    <h3 class="card-title mb-0">BOM List</h3>
                                                </div>
                                            </div>

                                            <div class="right-block">
                                                
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
                                                                                if ($columnIndex === 0) {
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
    </div>

    </td>

</tr>


<!-----add form modal start --->
<div class="modal fade hsn-dropdown-modal"  id="addToLocation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
    <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <form method="POST" action="">
                    <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                    <input type="hidden" id="item_id" name="item_id" value="">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">
                                <div class="card-header">
                                    <h4>Storage Details</h4>
                                </div>
                                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">
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
                        <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                <div class="card-header">

                                    <h4>Pricing and Discount

                                        <span class="text-danger">*</span>

                                    </h4>

                                </div>

                                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">Target price</label>

                                                        <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">Max Discount</label>

                                                        <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-body" style="height: 500px; overflow: auto;">
                <div class="card">

                </div>
            </div>
        </div>
    </div>


<?php
    require_once("../../common/footer2.php");
 ?>


