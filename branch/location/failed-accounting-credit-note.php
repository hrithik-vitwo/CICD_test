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
require_once("../../app/v1/functions/common/templates/template-sales-order.controller.php");
// administratorLocationAuth();

$pageName =  basename($_SERVER['PHP_SELF'], '.php');

if (!isset($_COOKIE["cookiefailedpaymentCreditNote"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiefailedpaymentCreditNote", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    }
}

// export download file name section
$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = explode("-", pathinfo($originalFileName, PATHINFO_FILENAME), 2)[1];
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = 'export_' . $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = 'download_' . $fileNameWithoutExtension . $currentDateTime;

//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
    echo "Session Timeout";
    exit;
}

$templateSalesOrderControllerObj = new TemplateSalesOrderController();

$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Credit Note No.',
        'slag' => 'credit_note_no',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Debitor Type',
        'slag' => 'debitor_type',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Party',
        'slag' => 'party_code',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Reference',
        'slag' => 'reference',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Posting Date',
        'slag' => 'postingDate',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Remarks',
        'slag' => 'remark',
        'icon' => '<ion-icon name="albums-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Source Address',
        'slag' => 's_adress',
        'icon' => '<ion-icon name="code-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Destination Address',
        'slag' => 'des_address',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Taxable Amount',
        'slag' => 'taxableAmount',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Cgst',
        'slag' => 'cgst',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Sgst',
        'slag' => 'sgst',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Igst',
        'slag' => 'igst',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Total Value',
        'slag' => 'total',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Created By',
        'slag' => 'created_by',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'status',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ]

];

?>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

<style>
    .wallet-accountbtn {
    background: #14e93d4a;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    border-radius: 5px;
    color: #025a13;
    font-weight: 600;
    width: 87px;
}
.filter-link {
        cursor: pointer;
    }
</style>


<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper vitwo-alpha-global is-payments">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <?php  ?>
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
                            <div class="p-0 pb-0" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->

                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                    <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space" style="width:100%">
                                        <div class="left-block">
                                            <div class="label-select">
                                                <h3 class="card-title mb-0">Failed Credit Note</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <div class="page-list-filer filter-list">

                                                <style>
                                                    @keyframes pulse {
                                                        0% {
                                                            transform: scale(1);
                                                        }

                                                        50% {
                                                            transform: scale(1.06);
                                                        }

                                                        100% {
                                                            transform: scale(1);
                                                        }
                                                    }

                                                    .activeNotification {
                                                        position: absolute;
                                                        font-weight: bolder;
                                                        margin: 0;
                                                        top: -11px;
                                                        background: #003060 !important;
                                                        color: #fff;
                                                        font-size: 0.8em;
                                                        padding: 1px 10px;
                                                        border-radius: 50px 50px;
                                                        right: -8px;
                                                        box-shadow: inset -1px -1px 3px 0px #a4a9f0;
                                                        animation: pulse 2s infinite;
                                                        /* Add the animation here */
                                                    }

                                                    .pulsing {
                                                        animation: pulse 2s infinite;
                                                        /* Apply the "pulse" animation for 2 seconds and repeat infinitely */
                                                    }

                                                    @media (max-width: 576px) {
                                                        .activeNotification {
                                                            position: absolute;
                                                            top: 10px;
                                                            right: 10px;
                                                            animation: none;
                                                            box-shadow: none;
                                                            background: transparent !important;
                                                            color: #003060;
                                                            font-size: 0.7rem;
                                                        }
                                                    }
                                                </style>


                                                <a class="filter-link active" id="active"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>Credit Note
                                                </a>
                                                <style>
                                                    @keyframes pulse {
                                                        0% {
                                                            transform: scale(1);
                                                        }

                                                        50% {
                                                            transform: scale(1.06);
                                                        }

                                                        100% {
                                                            transform: scale(1);
                                                        }
                                                    }

                                                    .activeNotification {
                                                        position: absolute;
                                                        font-weight: bolder;
                                                        margin: 0;
                                                        top: -11px;
                                                        background: #003060 !important;
                                                        color: #fff;
                                                        font-size: 0.8em;
                                                        padding: 1px 10px;
                                                        border-radius: 50px 50px;
                                                        right: -8px;
                                                        box-shadow: inset -1px -1px 3px 0px #a4a9f0;
                                                        animation: pulse 2s infinite;
                                                        /* Add the animation here */
                                                    }

                                                    .pulsing {
                                                        animation: pulse 2s infinite;
                                                        /* Apply the "pulse" animation for 2 seconds and repeat infinitely */
                                                    }

                                                    @media (max-width: 576px) {
                                                        .activeNotification {
                                                            position: absolute;
                                                            top: 10px;
                                                            right: 10px;
                                                            animation: none;
                                                            box-shadow: none;
                                                            background: transparent !important;
                                                            color: #003060;
                                                            font-size: 0.7rem;
                                                        }
                                                    }
                                                </style>


                                                <a class="filter-link" id="reverse"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>Reverse Credit Note
                                                </a>

                                                <script>
                                                    $(document).ready(function() {
                                                        $('.filter-link').on('click', function(e) {
                                                            $('.filter-link').removeClass('active');
                                                            $(this).addClass('active');
                                                        });
                                                    });
                                                </script>
                                            </div>
                                            <button class="btn btn-sm fillscreen-btn waves-effect waves-light" onclick="openFullscreen()">
                                                <ion-icon name="expand-outline" role="img" class="md hydrated" aria-label="expand outline"></ion-icon>
                                            </button>
                                            <button type="button" id="revealList" class="page-list">
                                                <ion-icon name="funnel-outline" role="img" class="md hydrated" aria-label="funnel outline"></ion-icon>
                                            </button>
                                            <div id="modal-container">
                                                <div class="modal-background">
                                                    <div class="modal">
                                                        <button class="btn-close-modal" is="closeFilterModal">
                                                            <ion-icon name="close-outline" role="img" class="md hydrated" aria-label="close outline"></ion-icon>
                                                        </button>
                                                        <h5>Filter Pages</h5>
                                                        <div class="page-list-filer filter-list mobile-page mobile-filter-list">
                                                        </div>
                                                        <h5>Search and Export</h5>
                                                        <div class="filter-action filter-mobile-search mobile-page">
                                                            <a type="button" class="btn add-col setting-menu waves-effect waves-light" data-toggle="modal" data-target="#myModal1"> <ion-icon name="settings-outline" role="img" class="md hydrated" aria-label="settings outline"></ion-icon></a>
                                                            <div class="filter-search">
                                                                <div class="icon-search" data-toggle="modal" data-target="#btnSearchCollpase_modal">
                                                                    <ion-icon name="filter-outline" role="img" class="md hydrated" aria-label="filter outline"></ion-icon>
                                                                    Advance Filter
                                                                </div>
                                                            </div>
                                                            <div class="exportgroup mobile-page mobile-export">
                                                                <button class="exceltype btn btn-primary btn-export waves-effect waves-light" type="button">
                                                                    <ion-icon name="download-outline" role="img" class="md hydrated" aria-label="download outline"></ion-icon>
                                                                </button>
                                                                <ul class="export-options">
                                                                    <li>
                                                                        <button>
                                                                            <ion-icon name="list-outline" class="ion-fulllist md hydrated" id="exportAllBtn" role="img" aria-label="list outline"></ion-icon>Export
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button>
                                                                            <ion-icon name="list-outline" class="ion-paginationlist md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>

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
                                        <div class="tab-pane dataTableTemplate dataTable_stock fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
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
                                                <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal1"> <ion-icon name="settings-outline"></ion-icon> Manage Column</a>
                                                <div class="length-row">
                                                    <span>Show</span>
                                                    <select name="" id="itemsPerPage" class="custom-select">
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
                                                        <button class="ion-paginationlist">
                                                            <ion-icon name="list-outline" class="ion-paginationlist md hydrated" role="img" aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllist">
                                                            <ion-icon name="list-outline" class="ion-fulllist md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
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
                                                                            $cookieTableStockReport = json_decode($_COOKIE["cookieTableStockReport"], true) ?? [];

                                                                            foreach ($columnMapping as $index => $column) {

                                                                            ?>
                                                                                <tr>
                                                                                    <td valign="top">

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
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", "<", ">", ">=", "<=", "=", "!=", "BETWEEN"];

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
                                                                                        } elseif ($column['dataType'] === 'number') {
                                                                                            $operator = array_slice($operators, 2, 6);
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

                                            <div class="modal right fade global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
                                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                    <div class="modal-content">
                                                        <!-- <div class="modal-header">
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
                                                                    <p class="info-detail default-address"><ion-icon name="location-outline"></ion-icon><span id="default_address">

                                                                        </span></p>
                                                                </div>
                                                            </div>
                                                        </div> -->
                                                        <div class="modal-body">
                                                            <nav>
                                                                <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                                                                    <!-- <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button> -->
                                                                    <!-- <button class="nav-link classicview-btn classicview-link active" id="nav-classicview-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-classicview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Preview</button> -->
                                                                    <!-- <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button> -->
                                                                </div>
                                                            </nav>
                                                            <div class="tab-content global-tab-content" id="nav-tabContent">
                                                                <div class="tab-pane classicview-pane show active" id="nav-classicview" role="tabpanel" aria-labelledby="nav-classicview-tab">
                                                                    <a href="" class="btn btn-primary classic-view-btn float-right" id="classicViewPrint" target="_blank" onclick="">Print</a>
                                                                    <div class="card classic-view bg-transparent" id="printMe">
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
</section>
<!-- /.content -->
</div>

<?php
require_once("../common/footer2.php");
?>

<!-----------mobile filter list------------>


<script>
    $(document).ready(function() {
        $("button.page-list").click(function() {
            var buttonId = $(this).attr("id");
            $("#modal-container").removeAttr("class").addClass(buttonId);
            $(".mobile-transform-card").addClass("modal-active");
        });

        $(".btn-close-modal").click(function() {
            $("#modal-container").toggleClass("out");
            $(".mobile-transform-card").removeClass("modal-active");
        });
    })
</script>


<!-- modal view responsive more tabs -->

<script>
    $(document).ready(function() {
        // Adjust tabs based on window size
        adjustTabs();

        // Listen for window resize event
        $(window).resize(function() {
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
     let query = window.location.search;
    let type = query ? query.substring(1) : '';
    let csvContent;
    let csvContentBypagination;
    var columnMapping = <?php echo json_encode($columnMapping); ?>;

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
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();
        // console.log($('#dataTable_detailed_view thead tr'));

        function fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = '',type = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var checkboxSettings = Cookies.get('cookiefailedpaymentCreditNote');
            var notVisibleColArr = [];
            var invoicetype = type;
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-failed-acc-credit-note.php",
                dataType: 'json',
                data: {
                    act: 'failedCreditNote',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit,
                    columnMapping: columnMapping,
                    invoicetype: invoicetype
                },
                beforeSend: function() {
                    $("#detailed_tbody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
                },
                success: function(response) {
                    console.log(response);
                    csvContent = response.csvContent;
                    csvContentBypagination = response.csvContentBypagination;

                    if (response.status) {
                        var responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);
                        
                        $.each(responseObj, function(index, value) {
                            // let reverseRepostButton = '';
                            // if (value.dn_status == 'active' && value.goods_journal_id > 0) {
                            //     reverseRepostButton = `
                            //         <li>
                            //             <button class="reverseCreditNote" data-id="${value.cr_note_id}" ><ion-icon name="refresh-outline"></ion-icon>Reverse</button>
                            //         </li>`;
                            //     sClass = `status-bg status-open`;
                            // } else if (value.dn_status == 'reverse') {
                            //     reverseRepostButton = `
                            //         <li>
                            //             <button class="repostCreditNote" data-id="${value.cr_note_id}" data-code="${ value.debit_note_no}" ><ion-icon name="repeat-outline"></ion-icon>Repost</button>
                            //         </li>`;
                            //     sClass = `status-bg status-open`;

                            // }
                            let post='';
                            let actionType = `pay_id=${(value.cr_note_id_action)}`;
                            if(response.type=='active'){
                                post=` <div class="view-accoiunt" data-id='${value.cr_note_id}'>
                                    <a href="manage-failed-acc-credit-note-view.php?${actionType}" class="wallet-accountbtn">
                                      <ion-icon name="wallet-outline"></ion-icon>
                                      <span>Post</span>
                                    </a>
                                  </div>`
                                }else{
                                     post=` <div class="view-accoiunt" data-id='${value.cr_note_id}'>
                                    <a href="manage-failed-acc-credit-note-reverse-view.php?${actionType}" class="wallet-accountbtn">
                                      <ion-icon name="wallet-outline"></ion-icon>
                                      <span>Post</span>
                                    </a>
                                  </div>`
                                }

                            dataTable.row.add([
                                value.sl_no,
                                value.credit_note_no,
                                value.debtor_type,
                                value.party_name,
                                value.ref,
                                value.postingDate,
                                value.remark,
                                `<p class='pre-normal'> ${value.source_address}</p>`,
                                `<p class='pre-normal'> ${value.destination_address}</p>`,
                                parseFloat(value.taxableAmount).toFixed(2) ?? '-',
                                parseFloat(value.cgst).toFixed(2) ?? '-',
                                parseFloat(value.sgst).toFixed(2),
                                parseFloat(value.igst).toFixed(2),
                                value.total,
                                value.created_by,
                                value.status,
                                post
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

                            // console.log('Cookie is blank.');
                        }
                    } else {

                        $("#detailed_tbody").html(`<td colspan=17 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
                        $('#yourDataTable_paginate').remove();
                        $('#limitText').remove();
                    }
                }
            });
        }

        
        if (type == 'reverse') {
            $('.filter-link').removeClass('active');
            $('#' + type).addClass('active');
            fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping, type = 'reverse');
        } else {
            fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping, type = 'active');
        }

        $(document).on("click", "#active", function(e) {
            $('.custom-select').val('25');
            if (query) {
                let cleanUrl = window.location.origin + window.location.pathname;
                window.history.pushState({}, '', cleanUrl);
            }
            fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping, type = 'active');
        })
        $(document).on("click", "#reverse", function(e) {
            $('.custom-select').val('25');
            fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping, type = 'reverse');
        })



        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function(e) {
            var maxlimit = $(this).val();
             var type = $('.filter-link.active').attr('id') === 'active' ? 'active' : 'reverse';
            fill_datatable(formInputs, '', maxlimit, '', type);
        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $("#itemsPerPage").val();
            //    console.log(limitDisplay);
             var type = $('.filter-link.active').attr('id') === 'active' ? 'active' : 'reverse';

            fill_datatable(formInputs, page_id, limitDisplay, '', type);

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
                    let value4 = $(`#value5_${columnIndex}`).val() ?? "";

                    if (columnSlag === 'pay.postingDate') {
                        values = value2;
                    } else if (columnSlag === 'pay.created_at') {
                        values = value4;
                    }

                    if ((columnSlag === 'pay.postingDate' || columnSlag === 'pay.created_at') && operatorName == "BETWEEN") {
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

               var type = $('.filter-link.active').attr('id') === 'active' ? 'active' : 'reverse';
               fill_datatable(formInputs, '', '', '', type);
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
                // console.log(columnVal);

                var index = columnMapping.findIndex(function(column) {
                    return column.slag === columnVal;
                });
                // console.log(index);
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

            // console.log(fromData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: 'json',
                    data: {
                        act: 'failedpaymentCreditNote',
                        fromData: fromData
                    },
                    success: function(response) {
                        console.log(response);
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
            } else if (columnName === 'Created Date') {
                inputId = "value5_" + columnIndex;
            }

            if ((columnName === 'Posting Date' || columnName === 'Created Date') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            // console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
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

