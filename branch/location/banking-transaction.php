<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-branch-pr-controller.php");
require_once("../../app/v1/functions/branch/bankReconciliationStatement.controller.php");
// console($_SESSION);
?>


<!-- <link rel="stylesheet" href="../../public/assets/manage-rfq.css">
<link rel="stylesheet" href="../../public/assets/animate.css"> -->


<style>
    /* .dataTable thead {
        position: sticky;
        top: 62px;
    } */

    .innerBankTransDiv .dataTable thead {
        top: 0px !important;
    }
</style>

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<link rel="stylesheet" href="../../public/assets/banking.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">


<style>
    .body-container {
        align-items: flex-start !important;
    }

    .grn-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #ffffff59;
        backdrop-filter: blur(2px);
        display: grid;
        place-items: center;
        z-index: 999999;
    }

    .custom-select-inner {
        max-width: 50px;
    }

    .filter-list.transaction-filter-list {
        top: 0;
        left: 0;
    }

    .filter-list.transaction-filter-list a.btn.active {
        background: #003060;
        color: #fff;
    }

    .text-style {
        color: white !important;
        background-color: #28a745;
        padding: 3px 5px;
        border-radius: 5px;
        font-weight: 600;
    }

    .innerBankTransDiv th:last-child {
        position: sticky;
        right: 0;
    }

    .innerBankTransDiv td:last-child {
        position: sticky;
        right: 0;
        background-color: #fff !important;
        /* force solid white background */
        z-index: 10;
        /* higher than other elements */
    }

    .banking-transaction-modal .btn-section {
        background-color: #fff;
        padding: 5px 0;
        gap: 10px;
    }

    #mannual-transaction .tab-pane-body {
        height: calc(100vh - 340px);
        overflow-y: hidden;
        overflow-x: hidden;
    }


    .customerWiseBankingDivNonAcc {
        overflow-y: auto;
        max-height: 500px;
        /* Set an appropriate height */
    }

    .vendorWiseBankingDivNonAcc {
        overflow-y: auto;
        max-height: 500px;
        /* Set an appropriate height */
    }

    .custAccDiv {
        overflow-y: auto;
        max-height: 500px;
    }

    .vendAccDiv {
        overflow-y: auto;
        max-height: 500px;
    }

    .custwiseSelectDiv {
        overflow-y: auto;
        max-height: 500px;
    }

    .vendwiseSelectDiv {
        overflow-y: auto;
        max-height: 500px;
    }
</style>
<style>
    .innerBankTransDiv .dt-top-container {
        display: flex;
        align-items: center;
        padding: 0px;
        gap: 0;
        height: 3rem;
        position: sticky;
        top: 0;
        left: 0;
        width: 100%;

    }

    .innerBankTransDiv .dataTables_wrapper .BankTransDatatable {
        clear: both;
        margin-top: 0px !important;
        margin-bottom: 6px !important;
        max-width: none !important;
        border-collapse: separate !important;
        border-spacing: 0;
    }



    .innerBankTransDiv .dataTables_wrapper .dt-top-container .dataTables_filter {
        display: flex !important;
        align-items: center;
        justify-content: start;
        position: absolute;
        right: 5px;
        top: 0px;
    }



    .innerBankTransDiv .dataTables_wrapper .dt-top-container .dataTables_filter input {
        margin-left: 0;
        display: inline-block;
        width: auto;
        padding-left: 30px;
        border: 1px solid #bfbdbd;
        color: #1B2559;
        height: 30px;
        border-radius: 8px;
    }

    /* .innerTableHeadPos {
        position: sticky;
        top: 0px;
        z-index: 1;
    } */




    .is-banking-transaction .dataTables_wrapper {
        /* overflow-y: auto; */
        height: calc(100vh - 230px);
    }

    .is-banking-transaction .innerBankTransDiv .dataTables_wrapper {
        overflow-y: hidden;
        height: calc(100vh - 355px);
    }

    .dataTable td p {
        margin: 5px 0;
        font-size: 9px !important;
        white-space: nowrap !important;
    }

    #dataTable_detailed_view_wrapper .dt-top-container {
        background-color: white !important;
        ;
    }
</style>

<?php

$allbtnActive = "";
$recognisedbtnActive = "";
$unrecognisedbtnActive = "";

$bankId = isset($_GET["bank"]) ? base64_decode(base64_decode(base64_decode($_GET["bank"]))) : 0;


$tnxType = "";
if (isset($_GET["act"]) && $_GET["act"] == "recognised") {
    $tnxType = "recognised";
    $recognisedbtnActive = "active";
} elseif (isset($_GET["act"]) && $_GET["act"] == "unrecognised") {
    $unrecognisedbtnActive = "active";
    $tnxType = "unrecognised";
} else {
    $tnxType = "all";
    $allbtnActive = "active";
}


$brsObj = new BankReconciliationStatement($bankId, $tnxType);
$bankTnxObj = $brsObj->getBankStatements();
$uncategorized_count = $brsObj->getUncategorizedCount($bankId);
$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = $fileNameWithoutExtension . 'download_all_' . $currentDateTime;
if (!isset($_COOKIE["cookieBankList"])) {
    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieBankList", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    } else {
        for ($i = 0; $i < 5; $i++) {
            $isChecked = ($i < 5) ? 'checked' : '';
        }
    }
}
// $tnxType = "";
// if (isset($_GET["act"]) && $_GET["act"] == "recognised") {
//     $tnxType = "recognised";
//     $recognisedbtnActive = "active";
// } elseif (isset($_GET["act"]) && $_GET["act"] == "unrecognised") {
//     $unrecognisedbtnActive = "active";
//     $tnxType = "unrecognised";
// } else {
//     $tnxType = "all";
//     $allbtnActive = "active";
// }


$columnMapping = [
    [
        'name' => 'Date',
        'icon' => '',
        'slag' => 's.tnx_date',
        'dataType' => 'date'
    ],
    [
        'name' => 'Details',
        'icon' => '',
        'slag' => 's.particular',
        'dataType' => 'string'
    ],
    [
        'name' => 'Account',
        'icon' => '',
        'slag' => 'bank_ac_val',
        'dataType' => 'string'
    ],
    [
        'name' => 'Deposits',
        'icon' => '',
        'slag' => 's.deposit_amt',
        'dataType' => 'number'
    ],
    [
        'name' => 'Withdrawal',
        'icon' => '',
        'slag' => 's.withdrawal_amt',
        'dataType' => 'number'
    ],
    [
        'name' => 'Yet to settle',
        'icon' => '',
        'slag' => 's.remaining_amt',
        'dataType' => 'number'
    ]
];

?>
<div id="loaderGRN" class="grn-loader" style="display: none;">
    <img src="<?= BASE_URL ?>public/assets/gif/loadingGRN-data.gif" width="150" alt="">
</div>
<div class="content-wrapper report-wrapper is-sales-orders is-banking-transaction vitwo-alpha-global">
    <section class="content banking-import-statement">

        <!-- main content list start -->

        <div class="container-fluid">
            <!-- <div class="head">
                <h2 class="text-lg font-bold">Bank Transactions</h2>
            </div> -->
            <?php
            $brsObj = new BankReconciliationStatement($bankId, $tnxType);
            $bankTnxObj = $brsObj->getBankStatements();
            $uncategorized_count = $brsObj->getUncategorizedCount($bankId);
            // console($uncategorized_count);
            // $branchSoObj = new BranchSo();
            // $amountInBook = 130600.00;
            $amountInBank = $bankTnxObj["totalAmount"];
            $amountInUnrecognised = $amountInBook - $amountInBank;
            // console($bankTnxObj);
            ?>
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
                                                <h3 class="card-title mb-0">Bank Transactions
                                                </h3>
                                            </div>
                                            <div class="title" id="counttotal">
                                            </div>
                                        </div>


                                        <div class="right-block">
                                            <div class="page-list-filer filter-list">
                                                <a href="" class="filter-link active" name="all"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>All
                                                </a>
                                                <a href="" class="filter-link" name="recognised"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>Categorized
                                                </a>
                                                <a href="" class="filter-link" name="unrecognised"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>Uncategorized</a>
                                            </div>
                                            <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()">
                                                <ion-icon name="expand-outline"></ion-icon>
                                            </button>
                                            <button type="button" id="revealList" class="page-list">
                                                <ion-icon name="funnel-outline"></ion-icon>
                                            </button>

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
                                                    <select name="" id="bankRecListLimit" class="custom-select">
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
                                                        <button class="ion-paginationlistnew">
                                                            <ion-icon name="list-outline"
                                                                class="ion-paginationlistnew md hydrated"
                                                                role="img" aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllistnew">
                                                            <ion-icon name="list-outline"
                                                                class="ion-fulllistnew md hydrated"
                                                                role="img" aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- <a href="manage-discount-variation-actions.php?create" class="btn btn-create mobile-page mobile-create"
                                                type="button">
                                                <ion-icon name="add-outline"></ion-icon>
                                                Create
                                            </a> -->


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
                                                                            $cookieTableStockReport = json_decode($_COOKIE["cookieBankList"], true) ?? [];

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
                                                                        ?>

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






                                            <!-- edit modal start  -->


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

        <div class="modal fade right global-view-modal banking-transaction-modal" id="unrecognisedTnxModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex justify-content-between py-2">
                            <div class="banking-amount">
                                <div id="price_value_hidden" style="display: none;"></div>
                                <h3><span class="rupee-symbol">₹</span><span id="price_value"></span></h3>
                                <div class="text-dark mb-2" id="calculativevalue"></div>
                                <div class="text-dark mb-2" id="remainingValue"></div>
                                <span id="warning_text"></span>
                            </div>
                            <div class="right-btns d-flex gap-3 nav nav-tabs my-0" id="nav-tab" role="tablist">
                                <button class="btn active" id="matchTransaction" data-bs-toggle="tab" data-bs-target="#match-transaction" type="button" role="tab">Match Transaction</button>
                                <button class="btn" id="catergorizeMannualy" data-bs-toggle="tab" data-bs-target="#mannual-transaction" type="button" role="tab">Categorize Manually </button>
                            </div>
                        </div>
                        <div class="row pt-2 banking-number-info">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <p class="text-sm text-right mb-2"><span id="utrNumber"></span></p>
                                <p class="text-xs text-right mb-2"><span id="particular"></span></p>
                                <p class="text-xs text-right mb-2">Date: <span id="dateModal"></span></p>
                            </div>
                        </div>
                        <ul class="nav nav-tabs match-transactions" id="myMatchTransactionTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#accountantTab" type="button" role="tab" aria-controls="home" aria-selected="true">Non Accounted</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#nonaccountantTab" type="button" role="tab" aria-controls="profile" aria-selected="false">Accounted</button>
                            </li>
                        </ul>
                    </div>

                    <div class="modal-body">
                        <div class="tab-content">
                            <div class="tab-pane fade" id="mannual-transaction" role="tabpanel" tabindex="0">
                                <div class="btn-section d-flex justify-content-end">
                                    <!-- <button class="btn btn-danger py-2">Cancel</button> -->
                                    <button class="btn btn-primary match-btn py-2" id="manualtransactionbutton">Match</button>
                                </div>
                                <div class="tab-pane-body">
                                    <div class="row pb-4">
                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                            <label for="">Select Transaction Category</label>
                                            <select name="typeofselect" id="selectTransactionCategory" class="form-control selectTransactionCategory">
                                                <option value="Select">Select Category</option>
                                                <option value="vendor_payment">Vendor Payment</option>
                                                <option value="customer_payment">Receive from Customer</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12" id="transactionCategorySubDropdownDiv">
                                            <div id="transCategoryVendor">
                                                <!-- <p style="font-size: small;">Select Vendor</p> -->
                                                 <label for="">Select Vendor</label>
                                                <select name="" id="selectVendorDropdown" class="form-control selectVendorDropdown">

                                                </select>

                                            </div>

                                            <div id="transCategoryCustomer">
                                                <!-- <p style="font-size: small;">Select Customer</p> -->
                                                 <label for="">Select Customer</label>
                                                <select name="" id="selectCustomerDropdown" class="form-control selectCustomerDropdown">

                                                </select>

                                            </div>

                                        </div>
                                    </div>
                                    <div id="reconciliationFormDiv" class="innerSelectWiseDiv innerBankTransDiv">
                                        <table id="manualSelectWiseCust" class="exportTable classic-view recon-classic-table BankTransDatatable">
                                            <thead>
                                                <tr>
                                                    <th class="text-left">Invoice No</th>
                                                    <th class="text-left">Invoice Date</th>
                                                    <th class="text-left">Invoice Status</th>
                                                    <th class="text-left">Invoice Amt.</th>
                                                    <th class="text-left">Due Amt.</th>
                                                    <th class="text-left">Enter Rec. Amt.</th>
                                                </tr>
                                            </thead>
                                            <tbody id="manualSelectWiseCustTableBody">

                                            </tbody>
                                        </table>
                                        <table id="manualSelectWiseVend" class="exportTable classic-view recon-classic-table BankTransDatatable">
                                            <thead>
                                                <tr>
                                                    <th>Invoice No</th>
                                                    <th>Document No</th>
                                                    <th>Invoice Date</th>
                                                    <th>Status</th>
                                                    <th>Invoice Amount</th>
                                                    <th>Due Amount</th>
                                                    <th style="width: 20% !important;">Enter Rec. Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody id="manualSelectWiseVendTableBody">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>


                            <div class="tab-pane fade show active" id="match-transaction" role="tabpanel" tabindex="0">
                                <div class="tab-pane-body match-transaction-tab">
                                    <div class="tab-content match-transac-tab-content pt-0" id="myTabContent">
                                        <div class="tab-pane fade show active" id="accountantTab" role="tabpanel" aria-labelledby="home-tab">
                                            <div class="tab-pane-body acc-nonacc-panebody">

                                                <!-- Non accountant innertab customer and vendor start -->

                                                <ul class="nav nav-tabs my-0" id="myTab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active" id="customerWiseBanking-tab" data-bs-toggle="tab" data-bs-target="#customerWiseBanking" type="button" role="tab" aria-controls="customerWiseBanking" aria-selected="true"><ion-icon name="person"></ion-icon>Customer Wise</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="vendorWisebanking-tab" data-bs-toggle="tab" data-bs-target="#vendorWisebanking" type="button" role="tab" aria-controls="vendorWisebanking" aria-selected="false"><ion-icon name="person"></ion-icon>Vendor Wise</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content acc-tab-content" id="myTabContent">
                                                    <div class="tab-pane fade show active" id="customerWiseBanking" role="tabpanel" aria-labelledby="customerWiseBanking-tab">
                                                        <div class="possible-match-head d-flex justify-content-between">
                                                            <div class="btn-section d-flex justify-content-start match-transac-btn">
                                                                <button class="btn btn-danger py-2" id="toogleDataCustNonAcc">Toggle Data</button>
                                                                <button class="btn btn-primary match-btn py-2" id="matchtransactionbutton">Match</button>
                                                            </div>
                                                            <div class="left">
                                                                <!-- <h3 class="text-sm font-bold">Possible Matches</h3> -->
                                                            </div>

                                                        </div>
                                                        <hr>

                                                        <!-- for customer wise -->
                                                        <div class="innerNonAccountedCustWiseDiv innerBankTransDiv">
                                                            <table id="nonAccountedCustWise" class="exportTable classic-view recon-classic-table BankTransDatatable">
                                                                <thead class="innerTableHeadPos">
                                                                    <tr>
                                                                        <th>Sl No</th>
                                                                        <th>Customer Code</th>
                                                                        <th>Customer Name</th>
                                                                        <th>Invoice No</th>
                                                                        <th>Invoice Date</th>
                                                                        <th>Status</th>
                                                                        <th>Invoice Amount</th>
                                                                        <th>Due Amount</th>
                                                                        <th>Enter Rec Amount</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="customerNonAccBody">
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade" id="vendorWisebanking" role="tabpanel" aria-labelledby="vendorWisebanking-tab">
                                                        <div class="possible-match-head d-flex justify-content-between">
                                                            <div class="btn-section d-flex justify-content-start match-transac-btn">
                                                                <button class="btn btn-danger py-2" id="toogleDataVendNonAcc">ASC</button>
                                                                <button class="btn btn-primary match-btn py-2" id="matchVendorNonAcc">Match</button>
                                                            </div>
                                                            <div class="left">
                                                                <!-- <h3 class="text-sm font-bold">Possible Matches</h3> -->
                                                            </div>
                                                        </div>
                                                        <hr>

                                                        <!-- for vendor wise -->
                                                        <div class="innerNonAccountedVendWiseDiv innerBankTransDiv">
                                                            <table id="nonAccountedVendWise" class="exportTable recon-classic-table classic-view BankTransDatatable">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Sl No</th>
                                                                        <th>Vendor Code</th>
                                                                        <th>Vendor Name</th>
                                                                        <th>Invoice No</th>
                                                                        <th>Document No</th>
                                                                        <th>Invoice Date</th>
                                                                        <th>Status</th>
                                                                        <th>Invoice Amount</th>
                                                                        <th>Due Amount</th>
                                                                        <th>Rec. Amount</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="vendorNonAccBody">
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                    </div>
                                                </div>

                                                <!--non  accountant innertab customer and vendor finish -->



                                                <div id="nonAccountsLists"></div>

                                            </div>

                                        </div>
                                        <!-- Account tab -->
                                        <div class="tab-pane fade" id="nonaccountantTab" role="tabpanel" aria-labelledby="profile-tab">

                                            <div class="tab-pane-body acc-nonacc-panebody noacc-tab-body">
                                                <ul class="nav nav-tabs my-0" id="myTab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active" id="nonAccCustomerWiseBanking-tab" data-bs-toggle="tab" data-bs-target="#nonAccCustomerWiseBanking" type="button" role="tab" aria-controls="nonAccCustomerWiseBanking" aria-selected="true">Customer Wise</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="nonAccVendorWisebanking-tab" data-bs-toggle="tab" data-bs-target="#nonAccVendorWisebanking" type="button" role="tab" aria-controls="nonAccVendorWisebanking" aria-selected="false">Vendor Wise</button>
                                                    </li>
                                                </ul>
                                                <div class="tab-content acc-tab-content" id="myTabContent">
                                                    <div class="tab-pane fade show active" id="nonAccCustomerWiseBanking" role="tabpanel" aria-labelledby="nonAccCustomerWiseBanking-tab">
                                                        <div class="possible-match-head d-flex justify-content-between">
                                                            <div class="btn-section d-flex justify-content-start match-transac-btn">
                                                                <button class="btn btn-danger py-2" id="toogleDataCustAcc">ASC</button>
                                                                <button class="btn btn-primary match-btn py-2" id="matchCustomerAcc">Match</button>
                                                            </div>
                                                            <div class="left">
                                                                <!-- <h3 class="text-sm font-bold">Possible Matches Non Account</h3> -->
                                                            </div>

                                                        </div>
                                                        <hr>

                                                        <!-- for  acc Customer wise -->
                                                        <div class="innerAccountedCustWiseDiv innerBankTransDiv">
                                                            <table id="AccountedCustWise" class="exportTable recon-classic-table classic-view BankTransDatatable">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-left">Sl No</th>
                                                                        <th class="text-left">Customer Code</th>
                                                                        <th class="text-left">Customer Name</th>
                                                                        <th class="text-left">Transaction No</th>
                                                                        <th class="text-left">Posting Date</th>
                                                                        <th class="text-left">Document Date</th>
                                                                        <th class="text-left">Collect Amount</th>
                                                                        <th class="text-left">Reconciled Amount</th>
                                                                        <th class="text-left">Unreconciled Amount</th>
                                                                        <th class="text-left" style="width: 30%;">Enter Amt.</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="custAccBody">
                                                                </tbody>
                                                            </table>


                                                        </div>

                                                    </div>
                                                    <div class="tab-pane fade" id="nonAccVendorWisebanking" role="tabpanel" aria-labelledby="nonAccVendorWisebanking-tab">
                                                        <div class="possible-match-head d-flex justify-content-between">
                                                            <div class="btn-section d-flex justify-content-start match-transac-btn">
                                                                <button class="btn btn-danger py-2" id="toogleDataVendAcc">Asc</button>
                                                                <button class="btn btn-primary match-btn py-2" id="matchVendorAcc">Match</button>
                                                            </div>
                                                            <div class="left">
                                                                <!-- <h3 class="text-sm font-bold">Possible Matches</h3> -->
                                                            </div>
                                                        </div>
                                                        <hr>

                                                        <!-- for  acc Vendor wise -->
                                                        <div class="innerAccountedVendWiseDiv innerBankTransDiv">
                                                            <table id="AccountedVendWise" class="exportTable recon-classic-table classic-view BankTransDatatable">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-left">Sl No</th>
                                                                        <th class="text-left">Vendor Code</th>
                                                                        <th class="text-left">Vendor Name</th>
                                                                        <th class="text-left">Transaction No</th>
                                                                        <th class="text-left">Posting Date</th>
                                                                        <th class="text-left">Document Date</th>
                                                                        <th class="text-left">Collect Amount</th>
                                                                        <th class="text-left">Reconciled Amount</th>
                                                                        <th class="text-left">Unreconciled Amount</th>
                                                                        <th class="text-left" style="width: 30%;">Enter Amt.</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody id="vendAccBody">
                                                                </tbody>
                                                            </table>

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

        <!-- global list modal end -->

    </section>
</div>
<?php
require_once("../common/footer2.php");

?>
<script>
    let statement_id = 0;
    $(document).ready(function() {

        $('#unrecognisedTnxModal').on('hidden.bs.modal', function() {
            openid = null; // Remove this if you want to retain the value
        });
        $('.select2').select2();
        const log = console.log;

        let tableNonAccountedCustWise;

        tableNonAccountedCustWise = $('#nonAccountedCustWise').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"customerWiseBankingDivNonAcc"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function(settings, json) {
                $('#nonAccountedCustWise_filter input[type="search"]').attr('placeholder', 'Search....');
            },


            buttons: [],
            // select: true,
            "bPaginate": false,
        });
        let tableNonAccountedVendWise;

        tableNonAccountedVendWise = $('#nonAccountedVendWise').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"vendorWiseBankingDivNonAcc"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function(settings, json) {
                $('#nonAccountedVendWise_filter input[type="search"]').attr('placeholder', 'Search....');
            },


            buttons: [],
            // select: true,
            "bPaginate": false,
        });
        let tableAccountedCustWise;

        tableAccountedCustWise = $('#AccountedCustWise').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"custAccDiv"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function(settings, json) {
                $('#AccountedCustWise_filter input[type="search"]').attr('placeholder', 'Search....');
            },


            buttons: [],
            // select: true,
            "bPaginate": false,
        });
        let tableAccountedVendWise;

        tableAccountedVendWise = $('#AccountedVendWise').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"vendAccDiv"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function(settings, json) {
                $('#AccountedVendWise_filter input[type="search"]').attr('placeholder', 'Search....');
            },


            buttons: [],
            // select: true,
            "bPaginate": false,
        });
        let tablevendorDueList;

        tablevendorDueList = $('#vendorDueList').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"vendDueListDiv"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function(settings, json) {
                $('#vendorDueList_filter input[type="search"]').attr('placeholder', 'Search....');
            },


            buttons: [],
            // select: true,
            "bPaginate": false,
        });
        let tablemanualSelectWiseCustlist;

        tablemanualSelectWiseCustlist = $('#manualSelectWiseCust').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"custwiseSelectDiv"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function(settings, json) {
                $('#manualSelectWiseCust_filter input[type="search"]').attr('placeholder', 'Search....');
            },


            buttons: [],
            // select: true,
            "bPaginate": false,
        });
        let tablemanualSelectWiseVendlist;

        tablemanualSelectWiseVendlist = $('#manualSelectWiseVend').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"vendwiseSelectDiv"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function(settings, json) {
                $('#manualSelectWiseVend_filter input[type="search"]').attr('placeholder', 'Search....');
            },


            buttons: [],
            // select: true,
            "bPaginate": false,
        });


        var indexValues = [];
        var dataTable;
        let columnMapping = <?php echo json_encode($columnMapping); ?>
        // let dataPaginate;

        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><"dt-center-in-div"B>r>t<ip>',
                "lengthMenu": [10, 25, 50, 100, 200, 250],
                "ordering": false,
                info: false,
                "initComplete": function(settings, json) {
                    // $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
                },

                buttons: [],
                // select: true,
                "bPaginate": false,
            });

        }
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        var allData;
        var dataPaginate;
        var bankId = <?php echo $bankId; ?>;
        // alert(bankId);


        window.fill_datatable = function(formDatas = '', pageNo = '', limit = '', typetnx = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookieBankList');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/brs/ajax-bank-transaction-list.php",
                dataType: 'json',
                data: {
                    act: 'TransactionList',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit,
                    bank: bankId,
                    typetnx: typetnx
                },
                beforeSend: function() {
                    $("#detailed_tbody").html(`<td colspan=7 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
                },
                success: function(response) {
                    // console.log(response);

                    if (response.status) {
                        var responseObj = response.data;

                        let numRows = response.unrecon; // Replace this with actual JS variable from backend or AJAX

                        // Determine proper message
                        let plural = numRows > 1 ? "(s) are" : " is";
                        let message = `${numRows} transaction ${plural} in the uncategorized status`;

                        // Create span
                        let span = document.createElement('span');
                        span.className = 'text-xs text-danger';
                        span.textContent = message;

                        // Append to div
                        let container = document.getElementById('counttotal');

                        // Clear any existing content
                        container.innerHTML = '';
                        container.appendChild(span);
                        dataPaginate = responseObj;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);
                        $.each(responseObj, function(index, value) {
                            // var formattedDate = value['s.tnx_date'] ? new Date(value['s.tnx_date']).toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' }).split('/').join('/') : '';
                            var reconciledAction = value['s.reconciled_status'] === 'pending' ?
                                `<ion-icon name="eye" class="eye_button" data-toggle="modal" data-open_id="${value['s.id']}" id="unrecognisedTnxTblRow" style="cursor:pointer;font-size:18px" data-tnx="${btoa(JSON.stringify(value.bankObj))}"></ion-icon>` :
                                '';

                            dataTable.row.add([
                                value['s.tnx_date'],
                                `Reference# : ${value['s.particular']}`,
                                // `${value['b.bank_name']} (${value['b.account_no'] ?? '-'})`,
                                `${value['bank_ac_val']}`,
                                value['s.deposit_amt'] > 0 ? `Rs . ${decimalAmount(value['s.deposit_amt'])}` : '',
                                value['s.withdrawal_amt'] > 0 ? `Rs . ${decimalAmount(value['s.withdrawal_amt'])}` : '',
                                `${value['s.remaining_amt'] > 0 ? `${decimalAmount(value['s.remaining_amt'])}` : ''}`,
                                reconciledAction
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
                            if (notVisibleColArr.length > 0) {
                                notVisibleColArr.forEach(function(index) {
                                    dataTable.column(index).visible(false);
                                });
                            }
                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);
                                }
                            });
                        }
                        setTimeout(function() {
                            if (typeof openid !== 'undefined' && openid) {
                                const $icon = $('ion-icon[data-open_id="' + openid + '"]');
                                if ($icon.length) {
                                    $icon.click(); // this will open the modal
                                } else {
                                    $('#unrecognisedTnxModal').modal('hide'); // icon not found, hide modal
                                }
                            } else {
                                $('#unrecognisedTnxModal').modal('hide'); // openid is missing, hide modal
                            }
                        }, 0);
                    } else {
                        $("#detailed_tbody").html(`<td colspan=7 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
                        $('#yourDataTable_paginate').hide();
                        $('#limitText').hide();
                    }
                }
            });
        }

        fill_datatable();


        let currentTnxType = '';

        $('.filter-link').on('click', function(e) {
            e.preventDefault();

            $('.filter-link').removeClass('active');

            $(this).addClass('active');

            currentTnxType = $(this).attr('name') || '';

            fill_datatable(formInputs, '', '', currentTnxType);
        });


        $(document).on("click", ".ion-paginationlistnew", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieBankList')
                },
                beforeSend: function() {
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-paginationlistnew').prop('disabled', true)
                },

                success: function(response) {
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
                    $('.ion-paginationlistnew').prop('disabled', false)
                }
            })

        });
        $(document).on("click", ".ion-fulllistnew", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/brs/ajax-bank-transaction-list.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieBankList'),
                    formDatas: formInputs
                },

                beforeSend: function() {
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-fulllistnew').prop('disabled', true)
                },
                success: function(response) {
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
                    $('.ion-fulllistnew').prop('disabled', false);
                }
            })

        });



        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function(e) {
            var maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit, currentTnxType);
        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $("#bankRecListLimit").val();
            //    console.log(limitDisplay);
            fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay, currentTnxType);

        });

        //<--------------advance search------------------------------->
        $(document).ready(function() {

            $(document).on("change", ".selectOperator", function() {
                let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
                let operatorName = $(this).val();
                let columnName = $(`#columnName_${columnIndex}`).html().trim();
                let inputContainer = $(`#td_${columnIndex}`);
                let inputId;
                if (columnName === 'Date') {
                    inputId = "value2_" + columnIndex;
                }

                if ((columnName === 'Date') && operatorName === 'BETWEEN') {
                    inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input m-input2" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
                } else {
                    $(`#${inputId}`).remove();
                }
                // console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
            });

            $(document).on("click", "#serach_submit", function(event) {
                event.preventDefault();
                let values;
                $(".selectOperator").each(function() {
                    let columnIndex = ($(this).attr("id")).split("_")[1];
                    let columnSlag = $(`#columnSlag_${columnIndex}`).val();
                    let operatorName = $(`#selectOperator_${columnIndex}`).val();
                    let value = $(`#value_${columnIndex}`).val() ?? "";
                    let value2 = $(`#value2_${columnIndex}`).val() ?? "";

                    if (columnSlag.trim() === 's.tnx_date') {
                        values = value2;
                    }

                    if ((columnSlag.trim() === 's.tnx_date') && operatorName == "BETWEEN") {
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

                fill_datatable(formDatas = formInputs, '', '', currentTnxType);
                // $("#myForm")[0].reset();
                $("#myForm")[0].reset();
                $(".m-input2").remove();
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
                        act: 'bank_List',
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
        // Main modal event to start every modal related activity
        $(document).on("click", ".eye_button", function() {
            // clear table prev data
            $("#customerNonAccBody").html('');
            $("#vendorNonAccBody").html('');
            $("#custAccBody").html('');
            $("#vendAccBody").html('');

            if ($.fn.DataTable.isDataTable('#nonAccountedCustWise')) {
                $('#nonAccountedCustWise').DataTable().clear().draw();
            }
            if ($.fn.DataTable.isDataTable('#nonAccountedVendWise')) {
                $('#nonAccountedVendWise').DataTable().clear().draw();
            }
            if ($.fn.DataTable.isDataTable('#AccountedCustWise')) {
                $('#AccountedCustWise').DataTable().clear().draw();
            }
            if ($.fn.DataTable.isDataTable('#AccountedVendWise')) {
                $('#AccountedVendWise').DataTable().clear().draw();
            }

            let price_value = 0;
            $("#calculativevalue").html("");
            $("#warning_text").hide();
            $("#remainingValue").html("");

            // modal heading start
            let listDetail = $(this).data('tnx');
            window.openid = $(this).data("open_id");
            listDetail = atob(listDetail);
            listDetail = JSON.parse(listDetail);
            statement_id = listDetail.id;
            console.log(listDetail);

            $("#price_value_hidden").html(decimalAmount(listDetail.remaining_amt));
            $("#price_value").html(decimalAmount(listDetail.remaining_amt));
            $("#particular").html(listDetail.particular);
            $("#utrNumber").html(listDetail.utr_number);
            $("#dateModal").html(formatDate(listDetail.tnx_date));
            $("#transCategoryCustomer").hide();
            $("#transCategoryVendor").hide();

            price_value = parseFloat($(`#price_value_hidden`).html());

            // Total value
            let totalValuetoshw = 0;

            /*  
            ---------------------------------------------- CUSTOMER NON ACCOUNT ----------------------------------------
            */
            $(".customerWiseBankingDivNonAcc").scrollTop(0);


            let customeridarray = [];
            let pageCustNonACC = 1;
            let debouceFlagCustNonAcc = true;
            let toogleDataCustNonAcc = 1;

            // load the customer list 
            loadCustomerNonAccData();

            // main scroll event for data loading
            $(".customerWiseBankingDivNonAcc").off('scroll').on('scroll', function() {
                const element = $(".customerWiseBankingDivNonAcc")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debouceFlagCustNonAcc) {
                    loadCustomerNonAccData();
                }
            });

            // function load the data from server CUSTOMER NON ACC
            function loadCustomerNonAccData() {
                let amount = parseFloat($(`#price_value_hidden`).html());
                let loadLimit = 50;

                if (debouceFlagCustNonAcc) {
                    // ✅ Add loading row before AJAX call
                    $('#customerNonAccBody').html(`
                        <tr id="loadingRow">
                            <td colspan="8" style="text-align: center;">
                                <span>Loading data...</span>
                            </td>
                        </tr>
                    `);

                    $.ajax({
                        url: 'ajaxs/brs/ajax-bank-transaction-modal-listscroll.php',
                        type: 'GET',
                        data: {
                            act: "bankTransCustomerNonAcc",
                            limit: loadLimit,
                            page: pageCustNonACC,
                            toggel: toogleDataCustNonAcc,
                            amount
                        },
                        beforeSend: function() {
                            debouceFlagCustNonAcc = false;
                        },
                        success: function(res) {
                            try {
                                let response = JSON.parse(res);

                                // ✅ Remove loading row
                                $('#loadingRow').remove();

                                if (response.status == "success") {
                                    let responseObj = response.data;

                                    $.each(responseObj, function(index, row) {
                                        let inputRow = `<input class="form-control custNonAccInput" min="0" data-invamt="${row.invoice_amount}" data-customer_id="${row.customer_id}" data-status="${row.status}" data-inv_no="${row.invoice_no}" data-id="${row.so_invoice_id}" data-dueamount="${row.due_amount}" data-customerarray="${row.customer_id}" type="number" name="customernonacc" >`;

                                        $('#nonAccountedCustWise').DataTable().row.add([
                                            `${row.sl_no ?? "-"}`,
                                            `${row.customer_code ?? "-"}`,
                                            `${row.trade_name ?? "-"}`,
                                            `${row.invoice_no ?? "-"}`,
                                            `${row.invoice_date ?? "-"}`,
                                            `<p style="text-transform: uppercase" class="text-center ${row.status === 'sent' ? 'status-danger' : row.status === 'partial paid' ? 'status-warning' : row.status === 'overdue' ? 'status-danger' : 'status-danger'}">${row.status}</p>`,
                                            `${row.invoice_amount ?? "-"}`,
                                            `${row.due_amount ?? "-"}`,
                                            `<p class="text-center">${inputRow}</p>`
                                        ]).draw(false);
                                    });

                                    pageCustNonACC++;
                                    if (response.numRows == loadLimit) {
                                        debouceFlagCustNonAcc = true;
                                    }
                                } else if (response.status == "warring") {
                                    $('#nonAccountedCustWise').DataTable().clear().draw();
                                    $('#customerNonAccBody').empty();
                                    let obj = `<tr><td colspan="8"><p class="text-center">No Data Found</p></td></tr>`;
                                    $('#customerNonAccBody').append(obj);
                                }

                            } catch (e) {
                                console.log(res);
                                console.error(`Error: ${e}`);
                                $('#loadingRow').remove(); // Remove on error as well
                            }
                        },
                        error: function(error) {
                            console.error("Error fetching data" + error);
                            $('#loadingRow').remove(); // ✅ Remove loading row on error
                        }
                    });
                }
            }

            // function that append data into list
            function appendRowsCustNonAcc(data) {
                const tableBody = $('#customerNonAccBody');
                let rows = '';

                data.forEach(row => {
                    let inputRow = `<input class="form-control custNonAccInput" min="0" data-invamt="${row.invoice_amount}" data-customer_id="${row.customer_id}" data-status="${row.status}" data-inv_no="${row.invoice_no}" data-id="${row.so_invoice_id}" data-dueamount="${row.due_amount}" data-customerarray="${row.customer_id}" type="number" name="customernonacc" >`;
                    rows += `   <tr>
                                    <td><p class="text-center">${row.sl_no}</p></td>
                                    <td><p class="text-center">${row.customer_code}</p></td>
                                    <td><p class="pre-normal">${row.trade_name}</p></td>
                                    <td><p class="pre-normal">${row.invoice_no}</p></td>
                                    <td><p class="text-center">${formatDate(row.invoice_date)}</p></td>
                                    <td>
                                    <p style="text-transform: uppercase" class="text-center ${row.status === 'sent' ? 'status-danger' : row.status === 'partial paid' ? 'status-warning' : row.status === 'overdue' ? 'status-danger' : ''}">${row.status}</p></td>
                                    

                                    <td><p class="pre-normal">${decimalAmount(row.invoice_amount)}</p></td>
                                    <td><p class="pre-normal">${decimalAmount(row.due_amount)}</p></td>
                                    <td><p class="text-center">${inputRow}</p></td>
                                </tr>`;
                });
                tableBody.append(rows);
            }
            // first event on input field validation 
            $(document).on("keyup", ".custNonAccInput", function() {
                if ($(this).val() != "") {
                    let value = parseFloat($(this).val());
                    let thisDueAmount = parseFloat($(this).data("dueamount"));

                    if (!isNaN(value) && !isNaN(thisDueAmount)) {
                        if (value < 0 || value > thisDueAmount) { // Added check for negative values
                            Swal.fire({
                                icon: "warning",
                                title: "Please enter a valid amount",
                                showConfirmButton: true,
                                timer: 3000,
                            });
                            $(this).val('');
                        }
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: "Please enter a valid amount",
                            showConfirmButton: true,
                            timer: 3000,
                        });
                        $(this).val('');
                    }
                }
                customerNonAccValChange();
            });


            function customerNonAccValChange() {
                // Reset total value and customer ID array
                totalValuetoshw = 0;
                customeridarray = [];

                // Loop through all input fields
                $(".custNonAccInput").each(function() {
                    let value = $(this).val();
                    let customer_id_array = $(this).data('customerarray');

                    if (value !== "") {
                        let parsedValue = parseFloat(value);

                        if (!isNaN(parsedValue)) {
                            totalValuetoshw += parsedValue;
                            customeridarray.push(customer_id_array);
                        }
                    }
                });

                // Update UI with new calculated value
                $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + decimalAmount(totalValuetoshw));
                $(`#remainingValue`).html("Remaining Amount: <span class='rupee-symbol'>₹</span>" + decimalAmount(price_value - totalValuetoshw));


                // Check if total value exceeds the threshold price_value
                if (totalValuetoshw > price_value) {
                    $(`#warning_text`).show().html("Price is Exceeding");
                    $('#matchtransactionbutton').prop('disabled', true);
                } else {
                    $(`#warning_text`).hide();
                    $('#matchtransactionbutton').prop('disabled', false);
                }
            }

            // Match btn  event
            $(document).off("click", "#matchtransactionbutton").on("click", "#matchtransactionbutton", function() {
                $("#loaderGRN").show();
                $('#matchtransactionbutton').prop('disabled', true);

                // main array building 
                const allEqual = arr => arr.every(val => val === arr[0]);
                const result = allEqual(customeridarray);
                let customeridd = null;
                if (result && customeridarray.length > 0) {
                    customeridd = customeridarray[0]; // Since all are equal, pick the first one
                }
                if (result == true) {
                    const resultObject = customerNonAccArray(customeridd);
                    let collectPaymentt = resultObject.paymentDetails.collectPayment;
                    Cust_Acc_Submit(resultObject, collectPaymentt, statement_id, listDetail);

                } else {
                    Swal.fire({
                        icon: "warning",
                        title: "choose invoices of single customer",
                        showConfirmButton: true,
                        timer: 1000,
                    });
                }
            });


            function Cust_Acc_Submit(resultObject, collectPaymentt, statement_id, listDetail) {


                if (checkFinalData(resultObject)) {
                    // Api calling
                    $.ajax({
                        type: "POST",
                        url: 'ajaxs/reconciliation/ajax-match-transaction.php',
                        data: {
                            act: "customer",
                            listDetail,
                            idarray: resultObject,
                            statement_id,
                            collectPaymentt
                        },
                        success: function(response) {
                            try {
                                let responseObj = JSON.parse(response);
                                if (responseObj.status == "success") {
                                    Swal.fire({
                                        icon: responseObj.status,
                                        title: responseObj.status,
                                        text: responseObj.message,
                                    }).then(function() {
                                        pageCustNonACC = 1;
                                        $("#loaderGRN").hide();
                                        fill_datatable();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: responseObj.status,
                                        title: responseObj.status,
                                        text: responseObj.message,
                                    }).then(function() {
                                        $("#loaderGRN").hide();
                                    });
                                }

                            } catch (e) {
                                $("#loaderGRN").hide();
                                console.error(`Error :${e}`);
                            }


                        },
                        complete: function(xhr, status) {
                            $('#matchtransactionbutton').prop('disabled', false);
                            $("#loaderGRN").hide();

                        },
                        error: function(err) {
                            console.error(err);
                        }
                    });

                    $('#matchtransactionbutton').prop('disabled', false);
                } else {
                    $("#loaderGRN").hide();
                    Swal.fire({
                        icon: "warning",
                        title: "Something Went Wrong!",
                        showConfirmButton: true,
                        timer: 3000,
                    });
                }
            }

            function checkFinalData(finalData) {
                // Check if paymentDetails is defined and has required properties
                if (!finalData.paymentDetails) {
                    console.error('Error: paymentDetails is missing.');
                    return false;
                }

                const paymentDetails = finalData.paymentDetails;
                if (!paymentDetails.paymentCollectType || !paymentDetails.customerId ||
                    !paymentDetails.collectPayment || !paymentDetails.bankId ||
                    !paymentDetails.documentDate || !paymentDetails.postingDate ||
                    !paymentDetails.tnxDocNo) {
                    console.error('Error: Missing required fields in paymentDetails.');
                    return false;
                }

                // Check if paymentInvDetails is defined and has customer_id as a key
                if (!finalData.paymentInvDetails || !finalData.paymentInvDetails.hasOwnProperty(paymentDetails.customerId)) {
                    console.error('Error: paymentInvDetails for the customerId is missing.');
                    return false;
                }

                // Check if there are any invoices for the given customerId
                const invoices = finalData.paymentInvDetails[paymentDetails.customerId];
                if (!Array.isArray(invoices) || invoices.length === 0) {
                    console.error('Error: No invoices found for customerId ' + paymentDetails.customerId);
                    return false;
                }

                // Check if each invoice object contains the necessary fields
                for (const invoice of invoices) {
                    console.log(invoices);
                    if (!invoice.invoiceId || !invoice.invoiceNo || !invoice.invAmt ||
                        !invoice.dueAmt || !invoice.customer_id || !invoice.invoiceStatus) {
                        console.error('Error: Missing required fields in invoice data.');
                        return false;
                    }
                }

                console.log('Success: finalData is valid and complete.');
                return true;
            }

            // Toggle Data 
            $(document).on("click", "#toogleDataCustNonAcc", function() {
                $('#toogleDataCustNonAcc').prop('disabled', true);
                pageCustNonACC = 1;
                toogleDataCustNonAcc = (toogleDataCustNonAcc == 1) ? 0 : 1;

                debouceFlagCustNonAcc = true;
                if ($.fn.DataTable.isDataTable('#nonAccountedCustWise')) {
                    $('#nonAccountedCustWise').DataTable().clear().draw();
                }
                $("#customerNonAccBody").html('');
                loadCustomerNonAccData();
                $('#toogleDataCustNonAcc').prop('disabled', false);
            });

            /*
                ---------------------------------------------- END OF CUSTOMER NON ACCOUNTED ------------------------------------------
            */

            // modal to show
            $(`#unrecognisedTnxModal`).modal('show');

            /*  
                ----------------------------------------- VENDOR NON ACCOUNTED ---------------------------------------------
            */

            let vendorIdArray = [];
            let pageVendNonACC = 1;
            let debouceFlagVendNonAcc = true;
            let toogleDataVendNonAcc = 1;
            $(".vendorWiseBankingDivNonAcc").scrollTop(0);

            // Load the vendor Non Acc list 
            loadVendorNonAccData();

            // Main scroll event for data loading
            $(".vendorWiseBankingDivNonAcc").off('scroll').on('scroll', function() {
                const element = $(".vendorWiseBankingDivNonAcc")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debouceFlagVendNonAcc) {
                    loadVendorNonAccData();
                }
            });

            // Function load the data from server 
            function loadVendorNonAccData() {
                let amount = parseFloat($(`#price_value_hidden`).html());
                // set limit scroll load

                let loadLimit = 50;
                if (debouceFlagVendNonAcc) {
                    $('#vendorNonAccBody').html(`
                        <tr id="loadingRowVendor">
                            <td colspan="8" style="text-align: center;">
                                <span>Loading data...</span>
                            </td>
                        </tr>
                    `);
                    $.ajax({
                        url: 'ajaxs/brs/ajax-bank-transaction-modal-listscroll.php',
                        type: 'GET',
                        data: {
                            act: "bankTransVendorNonAcc",
                            limit: loadLimit,
                            page: pageVendNonACC,
                            toggel: toogleDataVendNonAcc,
                            amount
                        },
                        beforeSend: function() {
                            debouceFlagVendNonAcc = false;
                        },
                        success: function(res) {
                            try {
                                let response = JSON.parse(res);
                                // console.log(response);
                                $('#loadingRowVendor').remove();
                                if (response.status == "success") {
                                    appendRowsVendorNonAcc(response.data);
                                    pageVendNonACC++;
                                    if (response.numRows == loadLimit) {
                                        debouceFlagVendNonAcc = true;
                                    }
                                } else if (response.status == "warring") {
                                    $('#nonAccountedVendWise').DataTable().clear().draw();
                                    $('#vendorNonAccBody').empty();
                                    let obj = `<tr><td colspan="7"><p class="text-center">No Data Found</p></td></tr>`;
                                    debouceFlagVendNonAcc = false;
                                    $('#vendorNonAccBody').append(obj);
                                }


                            } catch (e) {
                                console.log(res);
                                 $('#loadingRowVendor').remove();
                                console.error(`Error: ${e}`);
                            }
                        },
                        error: function(error) {
                            console.error("Error fetching data" + error);
                             $('#loadingRowVendor').remove();
                        }
                    });
                }
            }
            // Function that append data into list

            function appendRowsVendorNonAcc(responseObj) {

                $.each(responseObj, function(index, row) {
                    let inputRow = `<input class="form-control vendNonAccInput" min=0 data-invamt="${row.inv_amount}" data-grncode="${row.invoice_no}" data-pstatus="${row.status}" data-id="${row.grnIvId}" data-dueamount="${row.dueAmt}" data-vendorarray="${row.vendorId}" type="number" name="vendornonacc" >`;
                    $('#nonAccountedVendWise').DataTable().row.add([
                        `${row.sl_no}`,
                        `${row.vendorCode}`,
                        `${row.vendorName}`,
                        `${row.invoice_no}`,
                        `${row.vendorDocumentNo}`,
                        `${row.postingDate}`,
                        `<p style="text-transform: uppercase" class="text-center ${row.status === 'sent' ? 'status-danger' : row.status === 'partial paid' ? 'status-warning' : row.status === 'overdue' ? 'status-danger' : 'status-danger'}">${row.status}</p>`,
                        `${row.inv_amount}`,
                        `${row.dueAmt}`,
                        `<p class="text-center">${inputRow}</p>`,
                    ]).draw(false);
                })



            }

            // First event on input field validation 
            $(document).on("keyup", ".vendNonAccInput", function() {
                if ($(this).val() != "") {
                    let value = parseFloat($(this).val());
                    let thisDueAmount = parseFloat($(this).data("dueamount"));

                    if (!isNaN(value) && !isNaN(thisDueAmount)) {
                        if (value > thisDueAmount) {
                            Swal.fire({
                                icon: "warning",
                                title: "Please enter a valid amount",
                                showConfirmButton: true,
                                timer: 1000,
                            });
                            $(this).val('');
                        }
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: "Please enter a valid amount",
                            showConfirmButton: true,
                            timer: 1000,
                        });
                        $(this).val('');
                    }
                }
                vendorNonAccChange();
            });

            function vendorNonAccChange() {
                // Reset total value and vendor array
                totalValuetoshw = 0;
                vendorIdArray = [];

                // Loop through all vendor inputs and recalculate the total
                $(".vendNonAccInput").each(function() {
                    let value = $(this).val();
                    let vendor_id_array = $(this).data('vendorarray');

                    if (value !== "") {
                        let parsedValue = parseFloat(value);

                        // Check if the input value is a valid number
                        if (!isNaN(parsedValue)) {
                            totalValuetoshw += parsedValue;
                            vendorIdArray.push(vendor_id_array); // Add vendor ID to the array
                        }
                    }
                });

                // Update the displayed total value
                $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + decimalAmount(totalValuetoshw));
                $(`#remainingValue`).html("Remaining Amount: <span class='rupee-symbol'>₹</span>" + decimalAmount(price_value - totalValuetoshw));


                // Check if the total value exceeds the price limit
                if (totalValuetoshw > price_value) {
                    $(`#warning_text`).show().html("Price is Exceeding");
                    $('#matchVendorNonAcc').prop('disabled', true);
                } else {
                    $(`#warning_text`).hide();
                    $('#matchVendorNonAcc').prop('disabled', false);
                }
            }
            // Toggle Data 
            $(document).on("click", "#toogleDataVendNonAcc", function() {
                $('#toogleDataVendNonAcc').prop('disabled', true);
                pageVendNonACC = 1;
                if (toogleDataVendNonAcc == 0) {
                    toogleDataVendNonAcc == 1;
                    $("#toogleDataVendNonAcc").text('ASC');
                } else {
                    toogleDataVendNonAcc == 0;
                    $("#toogleDataVendNonAcc").text('DESC');
                }
                toogleDataVendNonAcc = (toogleDataVendNonAcc == 1) ? 0 : 1;

                debouceFlagVendNonAcc = true;
                if ($.fn.DataTable.isDataTable('#nonAccountedVendWise')) {
                    $('#nonAccountedVendWise').DataTable().clear().draw();
                }
                $("#vendorNonAccBody").html('');
                loadVendorNonAccData();
                $('#toogleDataVendNonAcc').prop('disabled', false);
            })

            // Match btn  event
            $(document).off("click", "#matchVendorNonAcc").on("click", "#matchVendorNonAcc", function() {

                $('#matchVendorNonAcc').prop('disabled', true);
                $("#loaderGRN").show();
                let valuesArr = [];
                let totalGivenValue = 0;
                // main array building 
                // $(".vendNonAccInput").each(function() {
                //     let value = parseFloat($(this).val());
                //     let dueAmount = parseFloat($(this).data("dueamount"));
                //     let id = parseFloat($(this).data("id"));

                //     if (!isNaN(value) && !isNaN(dueAmount) && !isNaN(id)) {
                //         totalGivenValue += value;
                //         valuesArr.push({
                //             id,
                //             value,
                //             dueAmount,
                //         });
                //     }
                // });

                const allEqual = arr => arr.every(val => val === arr[0]);
                const result = allEqual(vendorIdArray);

                let vendor_id = null;
                if (result && vendorIdArray.length > 0) {
                    vendor_id = vendorIdArray[0]; // Since all are equal, pick the first one
                }
                // console.log(result);
                if (result == true) {
                    // Api calling
                    const vendorresultObject = vendorNonAccArray(vendor_id);
                    let collectPaymentt = vendorresultObject.paymentDetails.collectPayment;
                    vendorAccSubmit(vendorresultObject, collectPaymentt, statement_id, listDetail)


                } else {
                    $("#loaderGRN").hide();
                    Swal.fire({
                        icon: "warning",
                        title: "choose invoices of single Vendor",
                        showConfirmButton: true,
                        timer: 1000,
                    });
                }
            });

            function vendorAccSubmit(vendorvaluesArr, collectPaymentt, statement_id, listDetail) {
                $.ajax({
                    type: "POST",
                    url: 'ajaxs/reconciliation/ajax-match-transaction.php',
                    data: {
                        act: "vendor",
                        listDetail,
                        idarray: vendorvaluesArr,
                        statement_id,
                        collectPaymentt
                    },
                    success: function(response) {
                        console.log(response);

                        let responseObj = JSON.parse(response);
                        if (responseObj.status == "success") {
                            Swal.fire({
                                icon: responseObj.status,
                                title: responseObj.status,
                                text: responseObj.message,
                            }).then(function() {
                                pageCustNonACC = 1;
                                $("#loaderGRN").hide();
                                fill_datatable();
                            });
                        } else {
                            Swal.fire({
                                icon: responseObj.status,
                                title: responseObj.status,
                                text: responseObj.message,
                            }).then(function() {
                                // location.reload();
                                $("#loaderGRN").hide();
                            });
                        }
                    },
                    complete: function(xhr, status) {
                        $("#loaderGRN").hide();
                        $('#matchVendorNonAcc').prop('disabled', false);
                    }
                });
            }


            // ----------Vendor Non Account Array Generate------
            function vendorNonAccArray(vendor_id) {
                const debdocu_date = $("#dateModal").text();
                const debitutrNumber = $("#utrNumber").text();
                let totalGivenValue = 0;
                const paymentInvoiceDetails = [];
                const paymentDetails = {};
                $(".vendNonAccInput").each(function() {
                    let value = parseFloat($(this).val());
                    let dueAmount = parseFloat($(this).data("dueamount"));
                    let id = parseFloat($(this).data("id"));

                    if (value > 0 && !isNaN(value) && !isNaN(dueAmount) && !isNaN(id)) {
                        totalGivenValue += value;
                        paymentInvoiceDetails.push({
                            grnIvId: $(this).data("id"),
                            grnCode: $(this).data("grncode") || '',
                            paymentStatus: $(this).data("pstatus") || 'payable',
                            creditPeriod: 5,
                            invAmt: parseFloat($(this).data("invamt")) || 0,
                            dueAmt: parseFloat($(this).data("dueamount")) || 0,
                            recAmt: value
                        });
                    }
                });
                let postData = {
                    paymentDetails: {
                        paymentCollectType: "collect",
                        vendorId: vendor_id,
                        collectPayment: totalGivenValue,
                        bankId: <?= $bankId ?>,
                        paymentAdviceImg: "", // or formData if file
                        documentDate: convertDate(debdocu_date),
                        postingDate: new Date().toISOString().split('T')[0],
                        tnxDocNo: debitutrNumber,
                        advancedPayAmt: "0.00"
                    },
                    submitCollectPaymentBtn: "",
                    paymentInvoiceDetails
                };

                return postData;
            }

            /*
                ------- END OF VENDOR NON ACCOUNTED --------
            */

            /*
                ------- CUSTOMER  ACCOUNTED --------
            */


            let customeridarrayAcc = [];
            let pageCustAcc = 1;
            let debouceFlagCustAcc = true;
            let toogleDataCustAcc = 1;
            $(".custAccDiv").scrollTop(0);

            // load the customer Acc list 
            loadCustomerAccData();

            // main scroll event for data loading
            $(".custAccDiv").off('scroll').on('scroll', function() {
                const element = $(".custAccDiv")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debouceFlagCustAcc) {
                    loadCustomerAccData();
                }
            });

            // function load the data from server CUSTOMER NON ACC
            function loadCustomerAccData() {
                let amount = parseFloat($(`#price_value_hidden`).html());
                // set limit scroll load
                
                let bankId = <?= $bankId ?>;
                let loadLimit = 50;
                if (debouceFlagCustAcc) {
                    $('#custAccBody').html(`
                        <tr id="loadingRowCustAcc">
                            <td colspan="8" style="text-align: center;">
                                <span>Loading data...</span>
                            </td>
                        </tr>
                    `);
                    $.ajax({
                        url: 'ajaxs/brs/ajax-bank-transaction-modal-listscroll.php',
                        type: 'GET',
                        data: {
                            act: "bankTransCustomerAcc",
                            limit: loadLimit,
                            page: pageCustAcc,
                            toggel: toogleDataCustAcc,
                            bankId,
                            amount
                        },
                        beforeSend: function() {
                            debouceFlagCustAcc = false;
                        },
                        success: function(res) {
                            try {
                                let response = JSON.parse(res);
                                // console.log(response);
                                $('#loadingRowCustAcc').remove();
                                if (response.status == "success") {
                                    appendRowsCustAcc(response.data);
                                    pageCustAcc++;
                                    if (response.numRows == loadLimit) {
                                        debouceFlagCustAcc = true;
                                    }
                                } else if (response.status === "warring") {
                                    $('#AccountedCustWise').DataTable().clear().draw();
                                    $('#vendAccBody').empty();
                                    let obj = `<tr><td colspan="10"><p class="text-center">No Data Found</p></td></tr>`;
                                    debouceFlagCustAcc = false;
                                    $('#vendAccBody').append(obj);
                                }

                            } catch (e) {
                                console.log(res);
                                $('#loadingRowCustAcc').remove();
                                console.error(`Error: ${e}`);
                            }
                        },
                        error: function(error) {
                            $('#loadingRowCustAcc').remove();
                            console.error("Error fetching data" + error);
                        }
                    });
                }
            }
            // function that append data into list
            function appendRowsCustAcc(responseObj) {

                $.each(responseObj, function(index, row) {
                    let inputRow = `<input class="form-control custAccInput" data-paymentId="${row.payment_id}" data-collect_payment="${row.collect_payment}" data-unreconciled_amount="${row.unreconciled_amount}" data-reconciled_amount="${row.reconciled_amount}"  data-customerarray="${row.customer_code}" type="number" name="customeracc" >`;
                    $('#AccountedCustWise').DataTable().row.add([
                        `${row.sl_no}`,
                        `${row.vendor_code}`,
                        `${row.vendor_name}`,
                        `${row.transactionId}`,
                        `${row.postingDate}`,
                        `${row.documentDate}`,
                        `${row.collect_payment}`,
                        `${row.reconciled_amount}`,
                        `${row.unreconciled_amount}`,
                        `${inputRow}`,
                    ]).draw(false);

                });

            }

            // first event on input field validation 
            $(document).on("keyup", ".custAccInput", function() {
                if ($(this).val() != "") {
                    let value = parseFloat($(this).val());
                    let thisDueAmount = parseFloat($(this).data("unreconciled_amount"));

                    if (!isNaN(value) && !isNaN(thisDueAmount)) {
                        if (value > thisDueAmount) {
                            Swal.fire({
                                icon: "warning",
                                title: "Please enter a valid amount",
                                showConfirmButton: true,
                                timer: 1000,
                            });
                            $(this).val('');
                        }
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: "Please enter a valid amount",
                            showConfirmButton: true,
                            timer: 1000,
                        });
                        $(this).val('');
                    }
                }
                customerAccValChange();
            });

            function customerAccValChange() {
                // Reset total value and customer ID array
                totalValuetoshw = 0;
                customeridarrayAcc = [];

                // Loop through all input fields
                $(".custAccInput").each(function() {
                    let value = $(this).val();
                    let customer_id_array = $(this).data('customerarray');

                    if (value !== "") {
                        let parsedValue = parseFloat(value);

                        if (!isNaN(parsedValue)) {
                            totalValuetoshw += parsedValue;
                            customeridarrayAcc.push(customer_id_array);
                        }
                    }
                });

                // Update UI with new calculated value
                $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + decimalAmount(totalValuetoshw));
                $(`#remainingValue`).html("Remaining Amount: <span class='rupee-symbol'>₹</span>" + decimalAmount(price_value - totalValuetoshw));


                // Check if total value exceeds the threshold price_value
                if (totalValuetoshw > price_value) {
                    $(`#warning_text`).show().html("Price is Exceeding");
                    $('#matchCustomerAcc').prop('disabled', true);
                } else {
                    $(`#warning_text`).hide();
                    $('#matchCustomerAcc').prop('disabled', false);
                }
            }

            // Match btn  event
            // $(document).on("click", "#matchCustomerAcc", function() {
            //     $('#matchCustomerAcc').prop('disabled', true);

            //     let valuesArr = [];
            //     let totalGivenValue = 0;
            //     // main array building 
            //     $(".custAccInput").each(function() {
            //         let value = parseFloat($(this).val());
            //         let dueAmount = parseFloat($(this).data("dueamount"));
            //         let id = parseFloat($(this).data("id"));

            //         if (!isNaN(value) && !isNaN(dueAmount) && !isNaN(id)) {
            //             totalGivenValue += value;
            //             valuesArr.push({
            //                 id,
            //                 value,
            //                 dueAmount,
            //             });
            //         }
            //     });

            //     const allEqual = arr => arr.every(val => val === arr[0]);
            //     const result = allEqual(customeridarrayAcc);

            //     if (result == true) {

            //         $.ajax({
            //             type: "POST",
            //             url: 'ajaxs/reconciliation/ajax-match-transaction.php',
            //             data: {
            //                 act: "customerAcc",
            //                 listDetail,
            //                 idarray: valuesArr,
            //                 statement_id
            //             },
            //             success: function(response) {
            //                 try {

            //                     let responseObj = JSON.parse(response);
            //                     if (responseObj.status == "success") {
            //                         Swal.fire({
            //                             icon: responseObj.status,
            //                             title: responseObj.status,
            //                             text: responseObj.message,
            //                         }).then(function() {
            //                             location.reload();
            //                         });
            //                     } else {
            //                         Swal.fire({
            //                             icon: responseObj.status,
            //                             title: responseObj.status,
            //                             text: responseObj.message,
            //                         }).then(function() {
            //                             location.reload();
            //                         });
            //                     }

            //                 } catch (e) {
            //                     console.error(`Error :${e}`);
            //                 }
            //             },
            //             complete: function(xhr, status) {
            //                 $('#matchCustomerAcc').prop('disabled', false);

            //             },
            //             error: function(err) {
            //                 console.error(err);
            //             }
            //         });

            //         $('#matchCustomerAcc').prop('disabled', false);
            //     } else {
            //         Swal.fire({
            //             icon: "warning",
            //             title: "Choose invoices of single customer",
            //             showConfirmButton: true,
            //             timer: 1000,
            //         });
            //     }
            // });

            $(document).off("click", "#matchCustomerAcc").on("click", "#matchCustomerAcc", function() {
                let totalrecon = 0;
                $("#loaderGRN").show();
                $('#matchCustomerAcc').prop('disabled', true);
                let collectionList = [];
                $(".custAccInput").each(function() {
                    let payment_id = $(this).data('paymentid');
                    let collect_payment = parseFloat($(this).data("collect_payment"));
                    let reconciled_amount = parseFloat($(this).data("reconciled_amount"));
                    let unreconciled_amount = parseFloat($(this).data("unreconciled_amount"));
                    let enter_amt = parseFloat($(this).val()) || 0;

                    if (
                        payment_id !== undefined &&
                        !isNaN(collect_payment) &&
                        !isNaN(reconciled_amount) &&
                        !isNaN(unreconciled_amount) &&
                        !isNaN(enter_amt) &&
                        enter_amt > 0 &&
                        enter_amt <= unreconciled_amount
                    ) {
                        totalrecon += enter_amt;
                        collectionList.push({
                            payment_id,
                            collect_payment,
                            reconciled_amount,
                            unreconciled_amount,
                            enter_amt
                        });
                    }
                });
                if (collectionList.length === 0) {
                    $("#loaderGRN").hide();
                    $('#matchCustomerAcc').prop('disabled', false);
                    Swal.fire({
                        icon: 'warning',
                        title: 'No valid entries',
                        text: 'Please enter valid amounts before proceeding.',
                    });
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: 'ajaxs/reconciliation/ajax-match-transaction.php',
                    data: {
                        act: "customerAcc",
                        listDetail,
                        idarray: collectionList,
                        statement_id,
                        totalrecon
                    },
                    success: function(response) {
                        try {
                            let responseObj = JSON.parse(response);
                            Swal.fire({
                                icon: responseObj.status,
                                title: responseObj.status,
                                text: responseObj.message,
                            }).then(function() {
                                if (responseObj.status === "success") {
                                    pageCustNonACC = 1;
                                    $("#loaderGRN").hide();
                                    fill_datatable();

                                }

                            });
                        } catch (e) {
                            console.error(`Error :${e}`);
                            $("#loaderGRN").hide();
                        }
                    },
                    complete: function() {
                        $('#matchVendorAcc').prop('disabled', false);
                        $("#loaderGRN").hide();
                    },
                    error: function(err) {
                        console.error(err);
                    }
                });

            });


            // Toggle Data 
            $(document).on("click", "#toogleDataCustAcc", function() {
                $('#toogleDataCustAcc').prop('disabled', true);
                $("#custAccBody").html('');
                pageCustAcc = 1;
                toogleDataCustAcc = (toogleDataCustAcc == 1) ? 0 : 1;
                debouceFlagCustAcc = true;
                loadCustomerAccData();
                $('#toogleDataCustAcc').prop('disabled', false);
            });

            /*
                ------- END OF CUSTOMER ACCOUNTED --------
            */

            /*
                ------- VENDOR ACCOUNTED --------
            */

            let vendorIdArrayAcc = [];
            let pageVendAcc = 1;
            let debouceFlagVendAcc = true;
            let toogleDataVendAcc = 1;
            $(".vendAccDiv").scrollTop(0);

            // Load the vendor Non Acc list 
            loadVendorAccData();

            // Main scroll event for data loading
            $(".vendAccDiv").off('scroll').on('scroll', function() {
                const element = $(".vendAccDiv")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debouceFlagVendAcc) {
                    loadVendorAccData();
                }
            });

            // Function load the data from server 
            function loadVendorAccData() {
                let amount = parseFloat($(`#price_value_hidden`).html());
                
                let bankId = <?= $bankId ?>;
                let loadLimit = 50;
                if (debouceFlagVendAcc) {
                    $('#vendAccBody').html(`
                        <tr id="loadingRowVendorAcc">
                            <td colspan="8" style="text-align: center;">
                                <span>Loading data...</span>
                            </td>
                        </tr>
                    `);
                    $.ajax({
                        url: 'ajaxs/brs/ajax-bank-transaction-modal-listscroll.php',
                        type: 'GET',
                        data: {
                            act: "bankTransVendorAcc",
                            limit: loadLimit,
                            page: pageVendAcc,
                            toggel: toogleDataVendAcc,
                            bankId,
                            amount
                        },
                        beforeSend: function() {
                            debouceFlagVendAcc = false;
                        },
                        success: function(res) {
                            try {
                                let response = JSON.parse(res);
                                // console.log(response);
                                $('#loadingRowVendorAcc').remove();
                                if (response.status == "success") {
                                    appendRowsVendorAcc(response.data);
                                    pageVendAcc++;
                                    if (response.numRows == loadLimit) {
                                        debouceFlagVendAcc = true;
                                    }
                                } else if (response.status == "warring") {
                                    $('#AccountedVendWise').DataTable().clear().draw();
                                    $('#vendAccBody').empty();
                                    let obj = `<tr><td colspan="10"><p class="text-center">No Data Found</p></td></tr>`;
                                    debouceFlagCustNonAcc = false;
                                    $('#vendAccBody').append(obj);
                                }


                            } catch (e) {
                                console.log(res);
                                $('#loadingRowVendorAcc').remove();
                                console.error(`Error: ${e}`);
                            }
                        },
                        error: function(error) {
                            $('#loadingRowVendorAcc').remove();
                            console.error("Error fetching data" + error);
                        }
                    });
                }
            }
            // Function that append data into list

            function appendRowsVendorAcc(responseObj) {

                $.each(responseObj, function(index, row) {
                    let inputRow = `<input class="form-control vendAccInput" style="width: 100%;" data-paymentId="${row.payment_id}" data-collect_payment="${row.collect_payment}" data-unreconciled_amount="${row.unreconciled_amount}" data-reconciled_amount="${row.reconciled_amount}" data-vendorarray="${row.vendor_code}" type="number" name="vendornonacc" >`;

                    $('#AccountedVendWise').DataTable().row.add([
                        `${row.sl_no}`,
                        `${row.vendor_code}`,
                        `${row.vendor_name}`,
                        `${row.transactionId}`,
                        `${row.postingDate}`,
                        `${row.documentDate}`,
                        `${row.collect_payment}`,
                        `${row.reconciled_amount}`,
                        `${row.unreconciled_amount}`,
                        `${inputRow}`,
                    ]).draw(false);
                });
            }

            // First event on input field validation 
            $(document).on("keyup", ".vendAccInput", function() {
                if ($(this).val() != "") {
                    let value = parseFloat($(this).val());
                    let thisDueAmount = parseFloat($(this).data("unreconciled_amount"));

                    if (!isNaN(value) && !isNaN(thisDueAmount)) {
                        if (value > thisDueAmount) {
                            Swal.fire({
                                icon: "warning",
                                title: "Please enter a valid amount",
                                showConfirmButton: true,
                                timer: 3000,
                            });
                            $(this).val('');
                        }
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: "Please enter a valid amount",
                            showConfirmButton: true,
                            timer: 3000,
                        });
                        $(this).val('');
                    }
                }
                vendorAccChange();
            });



            function vendorAccChange() {
                // Reset total value and vendor array
                totalValuetoshw = 0;
                vendorIdArrayAcc = [];

                // Loop through all vendor inputs and recalculate the total
                $(".vendAccInput").each(function() {
                    let value = $(this).val();
                    let vendor_id_array = $(this).data('vendorarray');

                    if (value !== "") {
                        let parsedValue = parseFloat(value);

                        // Check if the input value is a valid number
                        if (!isNaN(parsedValue)) {
                            totalValuetoshw += parsedValue;
                        }
                    }
                });

                // Update the displayed total value
                $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + decimalAmount(totalValuetoshw));
                $(`#remainingValue`).html("Remaining Amount: <span class='rupee-symbol'>₹</span>" + decimalAmount(price_value - totalValuetoshw));


                // Check if the total value exceeds the price limit
                if (totalValuetoshw > price_value) {
                    $(`#warning_text`).show().html("Price is Exceeding");
                    $('#matchVendorAcc').prop('disabled', true);
                } else {
                    $(`#warning_text`).hide();
                    $('#matchVendorAcc').prop('disabled', false);
                }
            }
            // Toggle Data 
            $(document).on("click", "#toogleDataVendAcc", function() {
                $('#toogleDataVendAcc').prop('disabled', true);
                $("#vendAccBody").html('');
                pageVendAcc = 1;
                toogleDataVendAcc = (toogleDataVendAcc == 1) ? 0 : 1;
                debouceFlagVendAcc = true;
                loadVendorAccData();
                $('#toogleDataVendAcc').prop('disabled', false);
            })

            /*
                --------------Vednor Accounted Start---------
            */

            $(document).off("click", "#matchVendorAcc").on("click", "#matchVendorAcc", function() {
                let totalrecon = 0;
                $("#loaderGRN").show();
                $('#matchVendorAcc').prop('disabled', true);

                let paymentIdList = [];

                $(".vendAccInput").each(function() {
                    let payment_id = $(this).data('paymentid');
                    let collect_payment = parseFloat($(this).data("collect_payment"));
                    let reconciled_amount = parseFloat($(this).data("reconciled_amount"));
                    let unreconciled_amount = parseFloat($(this).data("unreconciled_amount"));
                    let enter_amt = parseFloat($(this).val()) || 0;

                    if (
                        payment_id !== undefined &&
                        !isNaN(collect_payment) &&
                        !isNaN(reconciled_amount) &&
                        !isNaN(unreconciled_amount) &&
                        !isNaN(enter_amt) &&
                        enter_amt > 0 &&
                        enter_amt <= unreconciled_amount
                    ) {
                        totalrecon += enter_amt;
                        paymentIdList.push({
                            payment_id,
                            collect_payment,
                            reconciled_amount,
                            unreconciled_amount,
                            enter_amt
                        });
                    }
                });
                // console.log(paymentIdList);
                // return;

                if (paymentIdList.length === 0) {
                    $("#loaderGRN").hide();
                    $('#matchVendorAcc').prop('disabled', false);
                    Swal.fire({
                        icon: 'warning',
                        title: 'No valid entries',
                        text: 'Please enter valid amounts before proceeding.',
                    });
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: 'ajaxs/reconciliation/ajax-match-transaction.php',
                    data: {
                        act: "vendorpaymentReconciled",
                        listDetail,
                        idarray: paymentIdList,
                        statement_id,
                        totalrecon
                    },
                    success: function(response) {
                        try {
                            let responseObj = JSON.parse(response);
                            Swal.fire({
                                icon: responseObj.status,
                                title: responseObj.status,
                                text: responseObj.message,
                            }).then(function() {
                                if (responseObj.status === "success") {
                                    pageCustNonACC = 1;
                                    $("#loaderGRN").hide();
                                    fill_datatable();

                                }

                            });
                        } catch (e) {
                            console.error(`Error :${e}`);
                            $("#loaderGRN").hide();
                        }
                    },
                    complete: function() {
                        $('#matchVendorAcc').prop('disabled', false);
                        $("#loaderGRN").hide();
                    },
                    error: function(err) {
                        console.error(err);
                    }
                });
            });

            /*
                ------- END OF VENDOR ACCOUNTED --------
            */


            /* 
                ------------  Manual Transaction Script -----------
            */



            $(document).off("click", "#manualtransactionbutton").on("click", "#manualtransactionbutton", function() {

                let totalValue = 0;
                $("#loaderGRN").show();
                let tnx_category = $('.selectTransactionCategory :selected').val();
                let type = "";
                if (tnx_category == "vendor_payment") {
                    type = "vendor";
                } else {
                    type = "customer";
                }
                if (type == "customer") {
                    const selectedCustomerId = document.getElementById("selectCustomerDropdown").value;
                    const custresultObject = customerNonAccArray(selectedCustomerId);
                    let collectPaymenttt = custresultObject.paymentDetails.collectPayment;
                    Cust_Acc_Submit(custresultObject, collectPaymenttt, statement_id, listDetail);
                } else if (type == "vendor") {
                    const selectedVendorId = document.getElementById("selectVendorDropdown").value;
                    const vednorresultObject = vendorNonAccArray(selectedVendorId);
                    let collectPaymenttt = vednorresultObject.paymentDetails.collectPayment;
                    vendorAccSubmit(vednorresultObject, collectPaymenttt, statement_id, listDetail);
                }
            });

            $(document).on("keyup", ".receiveAmt", function() {
                let recAmt = (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                let invoiceAmt = $(`#invoiceAmt`).text();
                let dueAmt = (parseFloat($(`#dueAmt`).text()) > 0) ? parseFloat($(`#dueAmt`).text()) : 0;
                //   let collectTotalAmt = ($(".collectTotalAmt").val()) ? ($(".collectTotalAmt").val()) : 0;
                let duePercentage = ((parseFloat(dueAmt) - parseFloat(recAmt)) / parseFloat(invoiceAmt)) * 100;
                $(`#duePercentage`).text(`${Math.round(duePercentage,2)}%`);

                var totalDueAmt = 0;
                var totalRecAmt = 0;

                $(".receiveAmt").each(function() {
                    totalRecAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                });

                $(`#calculativevalue`).html("<span class='rupee-symbol'>₹</span>" + totalRecAmt.toFixed(2));

                if (recAmt <= dueAmt) {
                    $(`#warningMsg`).hide();
                } else {
                    $(`#warningMsg`).show();
                }

                if (totalRecAmt > price_value) {
                    $(`#warning_text`).show();
                    $(`#warning_text`).html("Price is Exceeding");

                } else {
                    $(`#warning_text`).hide();
                }

                // console.log(dueAmt);

            });



            $("#manualSelectWiseCust_wrapper").hide();
            $("#manualSelectWiseVend_wrapper").hide();

            $('.selectTransactionCategory').val("Select")

            if ($.fn.DataTable.isDataTable('#manualSelectWiseCust')) {
                $('#manualSelectWiseCust').DataTable().clear().draw();
            }
            if ($.fn.DataTable.isDataTable('#manualSelectWiseVend')) {
                $('#manualSelectWiseVend').DataTable().clear().draw();
            }

            $(document).on("change", ".selectTransactionCategory", function() {
                $("#transCategoryCustomer").hide();
                $("#transCategoryVendor").hide();
                // $(`#reconciliationFormDiv`).html("");

                if ($.fn.DataTable.isDataTable('#manualSelectWiseCust')) {
                    $('#manualSelectWiseCust').DataTable().clear().draw();
                }
                if ($.fn.DataTable.isDataTable('#manualSelectWiseVend')) {
                    $('#manualSelectWiseVend').DataTable().clear().draw();
                }

                let row_id = statement_id;
                let tnx_category = $(this).val();
                if (tnx_category == "vendor_payment") {

                    $("#transCategoryCustomer").hide();
                    $("#transCategoryVendor").show();
                    $("#manualSelectWiseCust_wrapper").hide();
                    $("#manualSelectWiseVend_wrapper").show();

                    $.ajax({
                        type: "GET",
                        url: "ajaxs/brs/ajax-bank-transaction-modal-list.php",
                        dataType: 'json',
                        data: {
                            act: "vendorManualSelect",
                            tnxType: "<?= $tnxType ?>",
                            bankId: "<?= $bankId ?>"
                        },
                        beforeSend: function() {},
                        success: function(value) {
                            // console.log(value);
                            let response = value.data;
                            let output = [];
                            output.push(`<option value="">Select Vendor</option>`);
                            $.each(response, function(key, value) {
                                output.push(`<option value="${value.vendor_id}">${value.vendor_code} - ${value.vendor_name}</option>`);
                            });
                            $('#selectVendorDropdown').html(output.join(''));

                        },
                        complete: function() {},
                        error: function(error) {
                            console.log(error);
                        }
                    });

                } else if (tnx_category == "customer_payment") {

                    $("#transCategoryCustomer").show();
                    $("#transCategoryVendor").hide();
                    $("#manualSelectWiseCust_wrapper").show();
                    $("#manualSelectWiseVend_wrapper").hide();

                    $.ajax({
                        type: "GET",
                        url: "ajaxs/brs/ajax-bank-transaction-modal-list.php",
                        dataType: 'json',
                        data: {
                            act: "customerManualSelect",
                            tnxType: "<?= $tnxType ?>",
                            bankId: "<?= $bankId ?>"
                        },
                        beforeSend: function() {},
                        success: function(value) {
                            // console.log(value);
                            let response = value.data;
                            let output = [];
                            output.push(`<option value="">Select Customer</option>`);
                            $.each(response, function(key, value) {
                                output.push(`<option value="${value.customer_id}">${value.customer_code} - ${value.customer_name}</option>`);
                            });
                            $('#selectCustomerDropdown').html(output.join(''));
                        },
                        complete: function() {},
                        error: function(error) {
                            console.log(error);
                        }
                    });

                } else {
                    $("#transCategoryCustomer").hide();
                    $("#transCategoryVendor").hide();
                    // $(`#reconciliationFormDiv`).html("");
                }
            });

            // $("#manualSelectWiseCust_wrapper").hide();

            $(document).on("change", "#selectCustomerDropdown", function() {
                let customer_id = $(this).val();
                if ($.fn.DataTable.isDataTable('#manualSelectWiseCust')) {
                    $('#manualSelectWiseCust').DataTable().clear().draw();
                }
                if ($.fn.DataTable.isDataTable('#manualSelectWiseVend')) {
                    $('#manualSelectWiseVend').DataTable().clear().draw();
                }
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: 'ajaxs/reconciliation/ajax-get-customer-due-invoice-list.php',
                    data: {
                        customer_id
                    },
                    beforeSend: function() {
                        // $("#manualSelectWiseVend").hide();
                        // $("#manualSelectWiseCust").show();
                        // $(`#manualSelectWiseCustTableBody`).html("Loading, Please wait...");
                    },
                    success: function(response) {
                        let responseObj = response.data;
                        $.each(responseObj, function(index, row) {
                            // console.log("went here")
                            $('#manualSelectWiseCust').DataTable().row.add([
                                `${row.invoice_no}`,
                                `${row.invoice_date}`,
                                `<span class='text-uppercase status-danger'>${row.label}</span>`,
                                `<span class='invAmt invoiceAmt text-right' id='invoiceAmt_${row.so_invoice_id}'>${row.all_total_amt}</span>`,
                                `<span class='dueAmt text-right' id='dueAmt_${row.so_invoice_id}'>${row.due_amount}</span>`,
                                `<div class='input-group m-0'>
                                        <div class='input-group-prepend'>
                                            <span class='input-group-text' style='font-family:"Font Awesome 5 Free"' id='basic-addon1'>₹</span>
                                        </div>
                                        <input class='form-control custNonAccInput' min='0' 
                                            data-invamt='${row.all_total_amt}' 
                                            data-customer_id='${customer_id}' 
                                            data-status='${row.label}' 
                                            data-inv_no='${row.invoice_no}' 
                                            data-id='${row.so_invoice_id}' 
                                            data-dueamount='${row.due_amount}' 
                                            data-customerarray='${customer_id}' 
                                            type='number' 
                                            name='customernonacc'>
                                    </div>
                                    <small style='display: none;' class='text-danger mt-n4 warningMsg' id='warningMsg_${row.so_invoice_id}'>Amount Exceeded </small>`,

                            ]).draw(false);
                        })
                    },
                    complete: function(xhr, status) {
                        //     if (xhr.status != 200) {
                        //         $(`#reconciliationFormDiv`).html("Something went wrong, please try again!");
                        //     }
                        //     log('Customer Invoice details request completed with status code:', xhr.status);
                    }
                });
            });

            $(document).on("change", "#selectVendorDropdown", function() {
                let vendor_id = $(this).val();

                if ($.fn.DataTable.isDataTable('#manualSelectWiseCust')) {
                    $('#manualSelectWiseCust').DataTable().clear().draw();
                }
                if ($.fn.DataTable.isDataTable('#manualSelectWiseVend')) {
                    $('#manualSelectWiseVend').DataTable().clear().draw();
                }
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: 'ajaxs/reconciliation/ajax-get-vendor-due-invoice-list.php',
                    data: {
                        vendor_id
                    },
                    beforeSend: function() {
                        // $(`#reconciliationFormDiv`).html("Loading, Please wait...");
                    },
                    success: function(response) {
                        // console.log(response);
                        let responseObj = response.data;
                        $.each(responseObj, function(index, row) {
                            $('#manualSelectWiseVend').DataTable().row.add([
                                `${row.grnivno}`,
                                `${row.vendorDocumentNo}`,
                                `${row.postingDate}`,
                                `<span class='text-uppercase status-danger'>${row.label}</span>`,
                                `${row.grnTotalAmount}`,
                                `${row.dueAmt}`,
                                `<div class='input-group m-0'>
                                    <div class='input-group-prepend'>
                                        <span class='input-group-text' style='font-family:system-ui' id='basic-addon1'>₹</span>
                                    </div>
                                    <input type='text' name='invoice[${row.grnIvId}][recAmt]' class='form-control receiveAmt px-3 text-right vendNonAccInput' data-invamt="${row.grnTotalAmount}" data-grncode="${row.grnivno}" data-pstatus="${row.label}" data-id="${row.grnIvId}" data-dueamount="${row.dueAmt}" data-vendorarray="${row.vendorId}" id='receiveAmt_${row.grnIvId}' placeholder='Amount'>
                                </div>
                                <small style='display: none;' class='text-danger mt-n4 warningMsg' id='warningMsg_${row.grnIvId}'>Amount Exceeded </small>`,
                            ]).draw(false);
                        });

                    },
                    complete: function(xhr, status) {
                        if (xhr.status != 200) {
                            $(`#reconciliationFormDiv`).html("Something went wrong, please try again!");
                        }
                        log('Vendor Invoice details request completed with status code:', xhr.status);
                    }
                });
            });

            /*
                ------ Tab Releted  Event--------
            */

            $(document).on("click", "#customerWiseBanking-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#remainingValue").html("");
                $("#warning_text").hide();
                $(".vendNonAccInput").val('');
            });

            $(document).on("click", "#vendorWisebanking-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#remainingValue").html("");
                $("#warning_text").hide();
                $(".custNonAccInput").val('');
            });

            $(document).on("click", "#profile-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#remainingValue").html("");
                $("#warning_text").hide();
                $(".custNonAccInput").val('');
                $(".vendNonAccInput").val('');
            });

            $(document).on("click", "#home-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#remainingValue").html("");
                $("#warning_text").hide();

            });

            $(document).on("click", "#nonAccCustomerWiseBanking-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#remainingValue").html("");
                $("#warning_text").hide();


            });

            $(document).on("click", "#nonAccVendorWisebanking-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#remainingValue").html("");
                $("#warning_text").hide();


            });

            $(document).on("click", "#vendorWisebanking-tab", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#remainingValue").html("");
                $("#warning_text").hide();

            });

            $(document).on("click", "#catergorizeMannualy", function() {
                totalValuetoshw = 0;
                $("#calculativevalue").html("");
                $("#remainingValue").html("");
                $("#warning_text").hide();
                $(".custAccInput").val('');
                $(".vendAccInput").val('');
                $(".custNonAccInput").val('');
                $(".vendNonAccInput").val('');
            });

        });
    });

    function convertDate(dateStr) {
        var parts = dateStr.split('-'); // ["29", "05", "2025"]
        return parts[2] + '-' + parts[1] + '-' + parts[0];
    }

    function customerNonAccArray(customeridd) {
        const paymentInvDetails = {};
        const invoiceDetailsArray = [];
        let totalGivenValue = 0;
        $(".custNonAccInput").each(function() {
            let value = parseFloat($(this).val());
            let dueAmount = parseFloat($(this).data("dueamount"));
            let id = parseFloat($(this).data("id"));
            let cust_id = ($(this).data("customer_id"));

            if (value > 0 && !isNaN(value) && !isNaN(dueAmount) && !isNaN(id)) {
                totalGivenValue += value;
                invoiceDetailsArray.push({
                    invoiceId: id,
                    invoiceNo: $(this).data("inv_no") || '',
                    invoiceStatus: $(this).data("status") || '',
                    creditPeriod: 5,
                    invAmt: parseFloat($(this).data("invamt")) || 0,
                    dueAmt: dueAmount,
                    customer_id: cust_id,
                    inputRoundOffInrWithSign: 0,
                    inputRoundOffWithSign: 0,
                    inputWriteBackInrWithSign: 0,
                    inputWriteBackWithSign: 0,
                    inputFinancialChargesWithSign: 0,
                    inputFinancialChargesInrWithSign: 0,
                    inputForexLossGainInrWithSign: 0,
                    inputForexLossGainWithSign: 0,
                    inputTotalTdsWithSign: 0,
                    recAmt: value
                });
            }
        });

        const docu_date = $("#dateModal").text();
        const utrNumber = $("#utrNumber").text();
        const paymentCollectType = 'collect';
        const bankId = <?= $bankId ?>;
        const paymentAdviceImg = '';
        const documentDate = convertDate(docu_date);
        const postingDate = new Date().toISOString().split('T')[0];
        const tnxDocNo = utrNumber;
        const advancedPayAmt = 0.00000;
        const submitCollectPaymentBtn = '';
        const customerId = customeridd;
        let collectPayment = totalGivenValue;

        paymentInvDetails[customerId] = invoiceDetailsArray;

        const returnArray = {
            paymentDetails: {
                paymentCollectType,
                customerId,
                collectPayment,
                bankId,
                paymentAdviceImg,
                documentDate,
                postingDate,
                tnxDocNo,
                advancedPayAmt
            },
            submitCollectPaymentBtn,
            paymentInvDetails
        };

        console.log(returnArray);
        // console.log(checkFinalData(returnArray));
        return returnArray;
    }
</script>


<script>
    $('#catergorizeMannualy').on('click', function() {
        $('#myMatchTransactionTab').hide();
    });
    $('#matchTransaction').on('click', function() {
        $('#myMatchTransactionTab').show();
    });
</script>

<script>
    const progress = document.getElementById('progress');
    const prev = document.getElementById('prev');
    const next = document.getElementById('next');
    const circles = document.querySelectorAll('.circle');
    const formSteps = document.querySelectorAll('.form-step');

    let currentActive = 1;

    next.addEventListener('click', () => {
        currentActive++;

        if (currentActive > circles.length) {
            currentActive = circles.length;
        }
        progress.style.width = '100%';
        update();
    });

    prev.addEventListener('click', () => {
        currentActive--;

        if (currentActive < 1) {
            currentActive = 1;
        }
        progress.style.width = '0%';
        update();
    });

    function update() {
        formSteps.forEach((step, idx) => {
            if (idx + 1 === currentActive) {
                step.style.display = 'block';
            } else {
                step.style.display = 'none';
            }
        });

        circles.forEach((circle, idx) => {
            if (idx < currentActive) {
                circle.classList.add('active');
            } else {
                circle.classList.remove('active');
            }
        });

        const actives = document.querySelectorAll('.active');



        if (currentActive === circles.length) {
            next.disabled = false;
        } else {
            next.disabled = false;
        }

        if (currentActive === 1) {
            prev.disabled = true;
        } else {
            prev.disabled = false;
        }
    }

    update(); // Initialize the progress and buttons
</script>