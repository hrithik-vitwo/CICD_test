<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
// if (!isset($_COOKIE["cookiefgStock"])) {
//     $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
//     $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
//     $settingsCheckbox_concised_view = unserialize($settingsCh);
//     if (settingsCheckbox_concised_view) {
//         setcookie("cookiefgStock", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
//     } else {
//         for ($i = 0; $i < 5; $i++) {
//             $isChecked = ($i < 5) ? 'checked' : '';
//         }
//     }
// }

if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusCustomer($_POST, "customer_id", "customer_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
  }


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


// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");


$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Customer Code',
        'slag' => 'customer_code',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => '	Customer Icon',
        'slag' => 'icon ',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Name',
        'slag' => 'trade_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Constitution of Business',
        'slag' => '	constitution_of_business ',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'GSTIN',
        'slag' => 'customer_gstin ',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Email',
        'slag' => 'customer_authorised_person_email ',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Phone',
        'slag' => 'customer_authorised_person_phone ',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Order Volume',
        'slag' => ' vol',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Receipt Amount',
        'slag' => 'recpamount ',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => ' customer_status',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ]

];

?>


<!-- <link rel="stylesheet" href="../../../public/assets/new_listing.css"> -->
<!-- <link rel="stylesheet" href="../../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/stock-report-new.css">

<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper vitwo-alpha-global">
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
                                                <h3 class="card-title mb-0">Manage Customer</h3>
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
                                            <a href="customer-actions.php?create" class="btn btn-create waves-effect waves-light" type="button">
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
                                                                <button type="submit" id="serach_reset"  class="btn btn-primary" data-dismiss="modal">Reset</button>
                                                                <button type="submit" id="serach_submit" class="btn btn-primary" data-dismiss="modal">Search</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            

                                        <!-- Global View start-->

                                        <div class="modal right fade global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <div class="top-details">
                                                            <div class="left">
                                                                <p class="info-detail amount"><ion-icon name="business-outline"></ion-icon><span id="custName"></span></p>
                                                                <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span id="custCode"></span></p>
                                                                <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span id="custCob"></span></p>
                                                                <p class="info-detail ref-number"><ion-icon name="information-outline"></ion-icon><span id="custGst"></span></p>
                                                            </div>
                                                            <div class="right">
                                                                <p class="info-detail name"><ion-icon name="person-outline"></ion-icon><span id="custPerson"></span></p>
                                                                <p class="info-detail qty"><ion-icon name="document-outline"></ion-icon><span id="custPersonDesg"></span></p>
                                                                <p class="info-detail qty"><ion-icon name="call-outline"></ion-icon><span id="custPersonPhone"></span></p>
                                                                <p class="info-detail qty"><ion-icon name="mail-outline"></ion-icon><span id="custPersonMail"></span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <nav>
                                                            <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                                                                <button class="nav-link active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                                                                <button class="nav-link" id="nav-transaction-tab" data-bs-toggle="tab" data-bs-target="#nav-transaction" type="button" role="tab" aria-controls="nav-transaction" aria-selected="false"><ion-icon name="repeat-outline"></ion-icon>Transactional</button>
                                                                <button class="nav-link" id="nav-mail-tab" data-bs-toggle="tab" data-bs-target="#nav-mail" type="button" role="tab" aria-controls="nav-mail" aria-selected="false"><ion-icon name="mail-outline"></ion-icon>Mails</button>
                                                                <button class="nav-link" id="nav-statement-tab" data-bs-toggle="tab" data-bs-target="#nav-statement" type="button" role="tab" aria-controls="nav-statement" aria-selected="false"><ion-icon name="document-text-outline"></ion-icon>Statement</button>
                                                                <button class="nav-link" id="nav-compliance-tab" data-bs-toggle="tab" data-bs-target="#nav-compliance" type="button" role="tab" aria-controls="nav-compliance" aria-selected="false"><ion-icon name="analytics-outline"></ion-icon>Compliance Status</button>
                                                                <button class="nav-link" id="nav-reconciliation-tab" data-bs-toggle="tab" data-bs-target="#nav-reconciliation" type="button" role="tab" aria-controls="nav-reconciliation" aria-selected="false"><ion-icon name="settings-outline"></ion-icon>Reconciliation</button>
                                                                <button class="nav-link" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                                            </div>
                                                            <div id="more-dropdown" class="more-dropdown">
                                                                <!-- Dropdown menu for additional tabs -->
                                                            </div>
                                                        </nav>

                                                        <div class="tab-content global-tab-content" id="nav-tabContent">
                                                            <div class="tab-pane fade show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                                                <div class="d-flex">
                                                                    <a href="#" class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create Invoice</a>
                                                                    <a href="#" class="btn btn-danger"><ion-icon name="close-outline"></ion-icon>Close Invoice</a>
                                                                </div>

                                                                <div class="items-view">
                                                                    <h5 class="title">Details View</h5>
                                                                    <hr>
                                                                    <div class="card">
                                                                        <div class="card-body">
                                                                            <div class="row-section row-first">
                                                                                <div class="left-info">
                                                                                    <ion-icon name="cube-outline"></ion-icon>
                                                                                    <div class="item-info">
                                                                                        <p class="code">33000033</p>
                                                                                        <p class="name">Rider</p>
                                                                                        <p class="desc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta laudantium ut voluptatum nisi id reiciendis.</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="right-info">
                                                                                    <div class="item-info">
                                                                                        <p class="code">2 hours</p>
                                                                                        <p class="name">INR 200.00</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-section row-tax">
                                                                                <div class="left-info">
                                                                                    <div class="item-info">
                                                                                        <p>Sub Total</p>
                                                                                        <p>Total Tax</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="right-info">
                                                                                    <div class="item-info">
                                                                                        <p>INR 200.00</p>
                                                                                        <p>INR 108.00 (18%)</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <hr>
                                                                            <div class="row-section row-total-amount">
                                                                                <div class="left-info">
                                                                                    <div class="item-info">
                                                                                        <p class="total">Total Amount</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="right-info">
                                                                                    <div class="item-info">
                                                                                        <p class="amount">INR 708.00</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="chart-view">
                                                                    <h5 class="title">Graph View</h5>
                                                                    <hr>
                                                                    <div id="chartDivCombinedColumnAndLineChart" class="chartContainer"></div>
                                                                </div>
                                                                <div class="info-view">
                                                                    <h5 class="title">Details View</h5>
                                                                    <hr>
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                            <div class="accordion view-modal-accordion" id="accordionExample">
                                                                                <div class="accordion-item">
                                                                                    <h2 class="accordion-header" id="headingOne">
                                                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic" aria-expanded="true" aria-controls="collapseOne">
                                                                                            <ion-icon name="information-outline"></ion-icon> Basic Details
                                                                                        </button>
                                                                                    </h2>
                                                                                    <div id="collapseBasic" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                                                        <div class="accordion-body">
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Code</label>
                                                                                                <p>: AD525200</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Name</label>
                                                                                                <p>: Rider</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>HSN/SAC</label>
                                                                                                <p>: 98789</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Quantity</label>
                                                                                                <p>: 3</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>UOM</label>
                                                                                                <p>: Hour</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Rate</label>
                                                                                                <p>: 200.00</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Taxable Amount</label>
                                                                                                <p>: 600.00</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Total Amount</label>
                                                                                                <p>: 708.00</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                            <div class="accordion view-modal-accordion" id="accordionExample">
                                                                                <div class="accordion-item">
                                                                                    <h2 class="accordion-header" id="headingOne">
                                                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAddress" aria-expanded="true" aria-controls="collapseOne">
                                                                                            <ion-icon name="information-outline"></ion-icon> Address Details
                                                                                        </button>
                                                                                    </h2>
                                                                                    <div id="collapseAddress" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                                                        <div class="accordion-body">
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>State</label>
                                                                                                <p>: Uttarakhand</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>City</label>
                                                                                                <p>: Selaqui</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>District</label>
                                                                                                <p>: Dehradun</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Location</label>
                                                                                                <p>: Selaqui</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Building Number</label>
                                                                                                <p>: Plot No-11 and 12, Khasra No-1045/2</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Flat Number</label>
                                                                                                <p>: Pargana Pachwa Doon</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Street Name</label>
                                                                                                <p>: Twin Industrial Area Mouza Camp Road</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>PIN Code</label>
                                                                                                <p>: 246541</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane transaction-tab-pane fade" id="nav-transaction" role="tabpanel" aria-labelledby="nav-transaction-tab">
                                                                <div class="inner-content">
                                                                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link active" id="pills-invoicesinner-tab" data-bs-toggle="pill" data-bs-target="#pills-invoicesinner" type="button" role="tab" aria-controls="pills-invoicesinner" aria-selected="true"><ion-icon name="receipt-outline"></ion-icon> Invoices</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="pills-collectioninner-tab" data-bs-toggle="pill" data-bs-target="#pills-collectioninner" type="button" role="tab" aria-controls="pills-collectioninner" aria-selected="false"><ion-icon name="podium-outline"></ion-icon>Collection</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="pills-estimatesinner-tab" data-bs-toggle="pill" data-bs-target="#pills-estimatesinner" type="button" role="tab" aria-controls="pills-estimatesinner" aria-selected="false"><ion-icon name="ticket-outline"></ion-icon>Estimates</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="pills-salesorderinner-tab" data-bs-toggle="pill" data-bs-target="#pills-salesorderinner" type="button" role="tab" aria-controls="pills-salesorderinner" aria-selected="false"><ion-icon name="pricetags-outline"></ion-icon>Sales Order</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="pills-journalinner-tab" data-bs-toggle="pill" data-bs-target="#pills-journalinner" type="button" role="tab" aria-controls="pills-journalinner" aria-selected="false"><ion-icon name="id-card-outline"></ion-icon>Journals</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="pills-creditnotesinner-tab" data-bs-toggle="pill" data-bs-target="#pills-creditnotesinner" type="button" role="tab" aria-controls="pills-creditnotesinner" aria-selected="false"><ion-icon name="document-text-outline"></ion-icon>Credit Notes</button>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="tab-content" id="pills-tabContent">
                                                                        <div class="tab-pane fade show active" id="pills-invoicesinner" role="tabpanel" aria-labelledby="pills-invoicesinner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="pills-collectioninner" role="tabpanel" aria-labelledby="pills-collectioninner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="pills-estimatesinner" role="tabpanel" aria-labelledby="pills-estimatesinner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="pills-salesorderinner" role="tabpanel" aria-labelledby="pills-salesorderinner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="pills-journalinner" role="tabpanel" aria-labelledby="pills-journalinner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="pills-creditnotesinner" role="tabpanel" aria-labelledby="pills-creditnotesinner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane mail-tab-pane fade" id="nav-mail" role="tabpanel" aria-labelledby="nav-mail-tab">
                                                                <div class="inner-content">
                                                                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link active" id="pills-mailInbox-tab" data-bs-toggle="pill" data-bs-target="#pills-mailInbox" type="button" role="tab" aria-controls="pills-mailInbox" aria-selected="true"><ion-icon name="mail-outline"></ion-icon>Inbox</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <div class="float-reminder-btn">
                                                                                <button class="nav-link" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                                                                                    <ion-icon name="notifications-outline"></ion-icon>
                                                                                    Reminder
                                                                                </button>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="tab-content" id="pills-tabContent">
                                                                        <div class="tab-pane fade show active" id="pills-mailInbox" role="tabpanel" aria-labelledby="pills-mailInbox-tab">
                                                                            <div class="inbox-blocks">
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block read-mail">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block read-mail">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block read-mail">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block read-mail">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                            <div class="offcanvas offcanvas-end reminder-offcanvas" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                                                                                <div class="offcanvas-header">
                                                                                    <h5 id="offcanvasRightLabel"><ion-icon name="notifications-outline"></ion-icon>Reminder</h5>
                                                                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
                                                                                        <ion-icon name="close-outline"></ion-icon>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="offcanvas-body">
                                                                                    <div class="row">
                                                                                        <div class="col-12 col-md-3">
                                                                                            <div class="form-input">
                                                                                                <label for="">Days</label>
                                                                                                <input type="text" class="form-control" name="reminerDays">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-12 col-md-7">
                                                                                            <div class="form-input">
                                                                                                <label for="">Operator</label>
                                                                                                <select name="" id="" class="form-control">
                                                                                                    <option value="0">Post of Invoice Date</option>
                                                                                                    <option value="1">Post of Due Date</option>
                                                                                                    <option value="2">Early of Invoice Date</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-12 col-md-2">
                                                                                            <button class="btn btn-primary add-mail-reminder">
                                                                                                <ion-icon name="add-outline"></ion-icon>
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="offcanvas-footer">
                                                                                    <button class="btn btn-primary">
                                                                                        <ion-icon name="send-outline"></ion-icon>
                                                                                        Sent
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- <div class="tab-pane fade" id="pills-manualInbox" role="tabpanel" aria-labelledby="pills-manualInbox-tab">
                                                        
                                                    </div> -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane statement-tab-pane fade" id="nav-statement" role="tabpanel" aria-labelledby="nav-statement-tab">
                                                                <div class="inner-content">
                                                                    <div class="row select-date">
                                                                        <div class="col-12 col-md-3">
                                                                            <div class="form-input">
                                                                                <select name="" id="" class="form-control">
                                                                                    <option value="">Select</option>
                                                                                    <option value="">This Month</option>
                                                                                    <option value="">Custom</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-9">
                                                                            <div class="date-fields">
                                                                                <div class="form-inline">
                                                                                    <label for="">From</label>
                                                                                    <input type="date" class="form-control">
                                                                                </div>
                                                                                <div class="form-inline">
                                                                                    <label for="">To</label>
                                                                                    <input type="date" class="form-control">
                                                                                </div>
                                                                                <button class="btn btn-primary">
                                                                                    <ion-icon name="arrow-forward-outline"></ion-icon>
                                                                                    Apply
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row statement-details">
                                                                        <div class="col-12 col-md-6 col">
                                                                            <div class="title-block">
                                                                                <h6><ion-icon name="document-text-outline"></ion-icon>STATEMENT OF ACCOUNT</h6>
                                                                                <p>2023-01-01 To 2024-03-19</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-6">
                                                                            <div class="amount-block">
                                                                                <h6><ion-icon name="card-outline"></ion-icon>Acount Summary</h6>
                                                                                <div class="info">
                                                                                    <label for=""><ion-icon name="lock-open-outline"></ion-icon>Opening Balance</label>
                                                                                    <p>RS 0</p>
                                                                                </div>
                                                                                <div class="info">
                                                                                    <label for=""><ion-icon name="wallet-outline"></ion-icon>Billed Amount</label>
                                                                                    <p>RS 0</p>
                                                                                </div>
                                                                                <div class="info">
                                                                                    <label for=""><ion-icon name="wallet-outline"></ion-icon>Amount Paid</label>
                                                                                    <p>RS 0</p>
                                                                                </div>
                                                                                <hr>
                                                                                <div class="total-info info">
                                                                                    <label for=""><ion-icon name="wallet-outline"></ion-icon>Amount Paid</label>
                                                                                    <p>RS 0</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-12">
                                                                            <table class="statement-table">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Date</th>
                                                                                        <th>Transaction</th>
                                                                                        <th>Details</th>
                                                                                        <th>Invoice Amount</th>
                                                                                        <th>Payment</th>
                                                                                        <th>Balance</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                </tbody>
                                                                                <tfoot>
                                                                                    <tr class="balnce-due">
                                                                                        <td colspan="5">Balance Due</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                </tfoot>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane compliance-tab-pane fade" id="nav-compliance" role="tabpanel" aria-labelledby="nav-compliance-tab">
                                                                <div class="inner-content">
                                                                    <div class="list-block">
                                                                        <div class="gst-list gst-one-tab">
                                                                            <div class="head">
                                                                                <h4><ion-icon name="document-text-outline"></ion-icon>GST Filed Status For GSTR1</h4>
                                                                            </div>
                                                                            <table>
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Financial Year</th>
                                                                                        <th>Tax Period</th>
                                                                                        <th>Date of Filing</th>
                                                                                        <th>Status</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-non-filed"><ion-icon name="close-outline"></ion-icon>Not Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        <div class="gst-list gst-three-tab">
                                                                            <div class="head">
                                                                                <h4><ion-icon name="document-text-outline"></ion-icon>GST Filed Status For GSTR3B</h4>
                                                                            </div>
                                                                            <table>
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Financial Year</th>
                                                                                        <th>Tax Period</th>
                                                                                        <th>Date of Filing</th>
                                                                                        <th>Status</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-non-filed"><ion-icon name="close-outline"></ion-icon>Not Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane recon-tab-pane fade" id="nav-reconciliation" role="tabpanel" aria-labelledby="nav-reconciliation-tab">
                                                                <div class="inner-content">
                                                                    <div class="date-fields">
                                                                        <div class="form-inline">
                                                                            <label for="">From</label>
                                                                            <input type="date" class="form-control">
                                                                        </div>
                                                                        <div class="form-inline">
                                                                            <label for="">To</label>
                                                                            <input type="date" class="form-control">
                                                                        </div>
                                                                        <button class="btn btn-primary waves-effect waves-light">
                                                                            <ion-icon name="arrow-forward-outline" role="img" class="md hydrated" aria-label="arrow forward outline"></ion-icon>
                                                                            Apply
                                                                        </button>
                                                                    </div>
                                                                    <form action="">
                                                                        <div class="recon-amount-section">
                                                                            <div class="tranasction total-transaction">
                                                                                <div class="form-inline">
                                                                                    <ion-icon name="repeat-outline"></ion-icon>
                                                                                    <div class="form-input">
                                                                                        <label for="">Total Transaction</label>
                                                                                        <p class="amount">20000.00</p>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                            <div class="tranasction total-reconcile">
                                                                                <div class="form-inline">
                                                                                    <ion-icon name="repeat-outline"></ion-icon>
                                                                                    <div class="form-input">
                                                                                        <label for="">Total Reconciled</label>
                                                                                        <p class="amount">10000.00</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tranasction pending-transaction">
                                                                                <div class="form-inline">
                                                                                    <ion-icon name="time-outline"></ion-icon>
                                                                                    <div class="form-input">
                                                                                        <label for="">Pending</label>
                                                                                        <p class="amount">10000.00</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="reconcile-btn">
                                                                            <button class="btn btn-primary">Proceed for Reconciliation</button>
                                                                        </div>
                                                                    </form>
                                                                    <p class="recon-note">All valuesare in <b>INR</b></p>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
                                                                <div class="inner-content">
                                                                    <div class="audit-head-section mb-3 mt-3 ">
                                                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> Sonie Kushwaha <span class="font-bold text-normal"> on </span> 26-02-2024 16:35:10</p>
                                                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> Sonie Kushwaha <span class="font-bold text-normal"> on </span> 26-02-2024 16:35:10</p>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="audit-body-section mt-2 mb-3 auditTrailBodyContentCustomer52400022">
                                                                        <ol class="timeline">

                                                                            <li class="timeline-item mb-0 bg-transparent auditTrailBodyContentLineCustomer" type="button" data-toggle="modal" data-id="2324" data-ccode="52400022" data-target="#innerModal">
                                                                                <span class="timeline-item-icon | filled-icon"><img src="https://www.devalpha.vitwo.ai/public/storage/audittrail/ADD.png" width="25" height="25"></span>
                                                                                <span class="step-count">3</span>
                                                                                <div class="new-comment font-bold">
                                                                                    <p>Sonie Kushwaha </p>
                                                                                    <ul class="ml-3 pl-0">
                                                                                        <li style="list-style: disc; color: #a7a7a7;">26-02-2024 16:35:12</li>
                                                                                    </ul>
                                                                                    <p></p>
                                                                                </div>
                                                                            </li>
                                                                            <p class="mt-0 mb-5 ml-5">New Customer added</p>
                                                                            <li class="timeline-item mb-0 bg-transparent auditTrailBodyContentLineCustomer" type="button" data-toggle="modal" data-id="1891" data-ccode="PO2402011" data-target="#innerModal">
                                                                                <span class="timeline-item-icon | filled-icon"><img src="https://www.devalpha.vitwo.ai/public/storage/audittrail/ADD.png" width="25" height="25"></span>
                                                                                <span class="step-count">2</span>
                                                                                <div class="new-comment font-bold">
                                                                                    <p>Sonie Kushwaha </p>
                                                                                    <ul class="ml-3 pl-0">
                                                                                        <li style="list-style: disc; color: #a7a7a7;">02-02-2024 14:57:02</li>
                                                                                    </ul>
                                                                                    <p></p>
                                                                                </div>
                                                                            </li>
                                                                            <p class="mt-0 mb-5 ml-5"> PO Created</p>
                                                                            <li class="timeline-item mb-0 bg-transparent auditTrailBodyContentLineCustomer" type="button" data-toggle="modal" data-id="1645" data-ccode="62400017" data-target="#innerModal">
                                                                                <span class="timeline-item-icon | filled-icon"><img src="https://www.devalpha.vitwo.ai/public/storage/audittrail/ADD.png" width="25" height="25"></span>
                                                                                <span class="step-count">1</span>
                                                                                <div class="new-comment font-bold">
                                                                                    <p>Anurag </p>
                                                                                    <ul class="ml-3 pl-0">
                                                                                        <li style="list-style: disc; color: #a7a7a7;">25-01-2024 18:04:35</li>
                                                                                    </ul>
                                                                                    <p></p>
                                                                                </div>
                                                                            </li>
                                                                            <p class="mt-0 mb-5 ml-5">New Vendor Add</p>


                                                                        </ol>

                                                                    </div>
                                                                    <div class="modal fade right audit-history-modal" id="innerModal" role="dialog" aria-labelledby="innerModalLabel" aria-modal="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content auditTrailBodyContentLineDiv">
                                                                                <div class="modal-header">
                                                                                    <div class="head-audit">
                                                                                        <p>New Customer added</p>
                                                                                    </div>
                                                                                    <div class="head-audit">
                                                                                        <p>Sonie Kushwaha</p>
                                                                                        <p>26-02-2024 16:35:12</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-body p-0">
                                                                                    <div class="free-space-bg">
                                                                                        <div class="color-define-text">
                                                                                            <p class="update"><span></span> Record Updated </p>
                                                                                            <p class="all"><span></span> New Added </p>
                                                                                        </div>
                                                                                        <ul class="nav nav-tabs pb-0" id="myTab" role="tablist">
                                                                                            <li class="nav-item">
                                                                                                <a class="nav-link active" id="concise-tab" data-toggle="tab" href="#consize" role="tab" aria-controls="concise" aria-selected="true"><i class="fa fa-th-large mr-2" aria-hidden="true"></i> Concised View</a>
                                                                                            </li>

                                                                                            <li class="nav-item">
                                                                                                <a class="nav-link" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="false"><i class="fa fa-list mr-2" aria-hidden="true"></i>Detailed View</a>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>
                                                                                    <div class="tab-content pt-0" id="myTabContent">
                                                                                        <div class="tab-pane fade show active" id="consize" role="tabpanel" aria-labelledby="consize-tab"></div>
                                                                                        <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                                                                                            <div class="dotted-box">
                                                                                                <p class="overlap-title">Customer Detail</p>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer code</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>52400022</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer pan</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>AAAFX0050D</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer gstin</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>29AAAFX0050D1ZL</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Trade name</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>SESHADRI AND COMPANY</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer currency</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>2</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer credit period</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>50</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Constitution of business</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>Partnership</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised person name</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>Sonie</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised person designation</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>Tester</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised person phone</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>7489761502</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised alt phone</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p></p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised person email</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>ksoni@vitwo.in</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised alt email</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p></p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer visible to all</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>Yes</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer created by</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>6|location</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer updated by</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>6|location</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer status</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>active</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="dotted-box">
                                                                                                <p class="overlap-title">Customer Address</p>
                                                                                                <div class="dotted-box">
                                                                                                    <p class="overlap-title">Bengaluru Urban (560003)</p>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address primary flag</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>1</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address building no</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>15/2</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address flat no</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p></p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address street name</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>13TH CROSS, 11MAIN</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address pin code</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>560003</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address location</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>MALLESWARAM</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address city</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>MALLESWARAM</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address district</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>Bengaluru Urban</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address state</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>Karnataka</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address created by</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>6|location</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address updated by</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>6|location</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="dotted-box">
                                                                                                <p class="overlap-title">Mail-Send</p>
                                                                                                <div class="box-content">
                                                                                                    <p>Send status</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>success</p>
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
                    buttons: [{
                            extend: 'copy',
                            text: '<ion-icon name="copy-outline" class="ion-copy"></ion-icon> Copy'
                        },
                        {
                            extend: 'excel',
                            text: '<ion-icon name="document-outline" class="ion-excel"></ion-icon> Excel'
                        },
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
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookiecustomer');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-customer.php",
                dataType: 'json',
                data: {
                    act: 'customer',
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
                                `<a href="#" class="soModal" data-id="${value.customerId}" data-code="${value.customer_code}">${value.customer_code}</a>`,
                                value.cusIcon,
                                value.cusName,
                                value.constitution_of_business,
                                value.customer_gstin,
                                value.customer_email,
                                value.customer_phone,
                                value.orderVolume,
                                value.receipt_amt,
                                value.status,   
                                `<div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                        <li>
                                            <button class="soModal" data-id="${value.customerId}" data-code="${value.customer_code}"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                        </li> 
                                        <li>
                                            <button data-toggle="modal" data-target="#editModal"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                        </li>
                                        <li>
                                            <button data-toggle="modal" data-target="#viewModal"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>
                                        </li>                                   
                                    </ul>                              
                                </div>`
                            ]).draw(false);
                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (!checkboxSettings) {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);

                                }
                            });

                            console.log('Cookie is blank.');
                        } else {
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
                        }
                    } else {

                        $("#detailed_tbody").html(`<td colspan=16 class='text-center'>No data found</td>`);
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
                    data: {
                        act: 'customer',
                        formData: formData
                    },
                    success: function(response) {
                        console.log(response);
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

<!-- reverse delivery btn -->
<script>
    $(document).on("click", ".reverseDelivery", function(e) {
        e.preventDefault();
        var dep_keys = $(this).data('id');
        var $this = $(this);
        console.log(dep_keys);

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'You want to reverse this?',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Reverse'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    data: {
                        dep_keys: dep_keys,
                        dep_slug: 'reverseDelivery'
                    },
                    url: 'ajaxs/ajax-reverse-post.php',
                    beforeSend: function() {
                        $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {
                        var responseObj = JSON.parse(response);
                        console.log(responseObj);

                        if (responseObj.status == 'success') {
                            $this.parent().parent().find('.listStatus').html('Reverse');
                            $this.hide();
                        } else {
                            $this.html('<i class="far fa-undo po-list-icon"></i>');
                        }

                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                        Toast.fire({
                            icon: responseObj.status,
                            title: '&nbsp;' + responseObj.message
                        }).then(function() {
                            // location.reload();
                        });
                    }
                });
            }
        });
    });
</script>




<!------------ modal ajax--------- -->

<script>
    $(document).on("click", ".soModal", function() {
        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');
        $(".classic-view").html('');
        let custId = $(this).data('id');
        let code = $(this).data('code');
        $('.auditTrail').attr("data-ccode", code);
        $.ajax({
            type: "GET",
            url: "ajaxs/modals/customer/ajax-manage-customer-modal.php",
            dataType: 'json',
            data: {
                act: "modalData",
                custId
            },
            beforeSend: function() {
                $('#itemTableBody').html('');
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
            success: function(value) {
                console.log(value);
                if(value.status){
                    let responseObj=value.data;
                    let dataObj=value.data.dataObj;
                    // nav head
                    $("#custName").html(dataObj.trade_name);
                    $("#custCode").html(dataObj.customer_code);
                    $("#custCob").html(dataObj.constitution_of_business);
                    $("#custGst").html(dataObj.customer_gstin);
                    $("#custPerson").html(dataObj.customer_authorised_person_name);
                    $("#custPersonDesg").html(dataObj.customer_authorised_person_designation);
                    $("#custPersonPhone").html(dataObj.customer_authorised_person_phone);
                    $("#custPersonMail").html(dataObj.customer_authorised_person_email);
                }
                $("#globalModalLoader").remove();
            },
            complete: function() {
                $("#globalModalLoader").remove();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
</script>