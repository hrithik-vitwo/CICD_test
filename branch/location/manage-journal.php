<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");

require_once("../common/sidebar.php");
require_once("../common/pagination.php");
// administratorLocationAuth();
// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
require_once("../../app/v1/functions/common/templates/template-sales-order.controller.php");




if (!isset($_COOKIE["cookieManageJournal"])) {
  $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
  $settingsCheckbox_concised_view = unserialize($settingsCh);
  if ($settingsCheckbox_concised_view) {
    setcookie("cookieManageJournal", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
  }
}

// export download file name section
$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = $fileNameWithoutExtension . 'download_all_' . $currentDateTime;

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
    'dataType' => 'number'
  ],
  [
    'name' => 'JV No',
    'slag' => 'journal.jv_no',
    'icon' => '<ion-icon name="location-outline"></ion-icon>',
    'dataType' => 'string'
  ],
  [
    'name' => 'Document No',
    'slag' => 'journal.documentNo',
    'icon' => '<ion-icon name="document-outline"></ion-icon>',
    'dataType' => 'string'
  ],
  [
    'name' => 'Posting Date',
    'slag' => 'journal.postingDate',
    'icon' => '<ion-icon name="document-outline"></ion-icon>',
    'dataType' => 'date'
  ],
  [
    'name' => 'Created Date',
    'slag' => 'journal.journal_created_at',
    'icon' => '<ion-icon name="document-outline"></ion-icon>',
    'dataType' => 'date'
  ],
  [
    'name' => 'Created By',
    'slag' => 'journal.journal_created_by',
    'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
    'dataType' => 'string'
  ],
  [
    'name' => 'Party Code',
    'slag' => 'journal.party_code',
    'icon' => '<ion-icon name="document-outline"></ion-icon>',
    'dataType' => 'string'
  ],
  [
    'name' => 'Party Name',
    'slag' => 'journal.party_name',
    'icon' => '<ion-icon name="document-outline"></ion-icon>',
    'dataType' => ''
  ],
  [
    'name' => 'Transaction Type',
    'slag' => 'journal.journalEntryReference',
    'icon' => '<ion-icon name="code-outline"></ion-icon>',
    'dataType' => 'string'
  ],
  [
    'name' => 'Narration',
    'slag' => 'journal.remark',
    'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
    'dataType' => 'string'
  ],
  [
    'name' => 'Debit Amout',
    'slag' => 'debit_table.total_debit',
    'icon' => '<ion-icon name="albums-outline"></ion-icon>',
    'dataType' => 'number'
  ],
  [
    'name' => 'Credit Amount',
    'slag' => 'credit_table.total_credit',
    'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
    'dataType' => 'number'
  ]
];

?>

<style>
  .d-flex.classic-view-footer.gap-3 {
    justify-content: space-between !important;
    margin-top: 50px;
}
</style>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="public/assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<style>
    .global-view-modal .modal-body {
        overflow: auto;
    }
</style>
<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-sales-orders vitwo-alpha-global">
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
              <div class="p-0 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                <!---------------------- Search START -->
                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space" style="width:100%">
                    <div class="left-block">
                      <div class="label-select">
                        <h3 class="card-title mb-0">Journal Entry List</h3>
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
                              <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal1"> <ion-icon name="settings-outline"></ion-icon></a>
                              <div class="filter-search">
                                <div class="icon-search" data-toggle="modal" data-target="#btnSearchCollpase_modal">
                                  <ion-icon name="filter-outline"></ion-icon>
                                  Advance Filter
                                </div>
                              </div>
                              <div class="exportgroup mobile-page mobile-export">
                                <button class="exceltype btn btn-primary btn-export" type="button">
                                  <ion-icon name="download-outline"></ion-icon>
                                </button>
                                <ul class="export-options">
                                  <li>
                                    <button>
                                      <ion-icon name="list-outline" class="ion-fulllistJournal md hydrated" id="exportAllBtn" role="img" aria-label="list outline"></ion-icon>Export
                                    </button>
                                  </li>
                                  <li>
                                    <button>
                                      <ion-icon name="list-outline" class="ion-paginationlistJournal md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                    </button>
                                  </li>
                                </ul>
                              </div>
                              <a href="#" class="btn btn-create mobile-page mobile-create" type="button">
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
                            <button class="ion-paginationlistJournal">
                              <ion-icon name="list-outline" class="ion-paginationlistJournal md hydrated" role="img" aria-label="list outline"></ion-icon>Export
                            </button>
                          </li>
                          <li>

                            <button class="ion-fulllistJournal">
                              <ion-icon name="list-outline" class="ion-fulllistJournal md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                            </button>
                          </li>
                        </ul>
                      </div>
                      <a href="manage-journal-actions.php?show=create" class="btn btn-create" type="button">
                        <ion-icon name="add-outline"></ion-icon>
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
                                      $cookieTableStockReport = json_decode($_COOKIE["cookieManageJournal"], true) ?? [];

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
                                      if ($columnIndex === 0 || $columnIndex === 10 || $columnIndex === 11) {
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
                                  <p class="info-detail default-address"><ion-icon name="location-outline"></ion-icon><span id="default_address">

                                    </span></p>
                                </div>
                              </div>
                            </div>
                            <div class="modal-body">
                              <nav>
                                <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                                  <!-- <button class="nav-link " id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button> -->
                                  <button class="nav-link classicview-btn classicview-link ViewfirstTab active" id="nav-classicview-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-classicview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="print-outline"></ion-icon>Print Preview</button>
                                  <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                </div>
                              </nav>
                              <div class="tab-content global-tab-content" id="nav-tabContent">

                                <div class="tab-pane fade transactional-data-tabpane " id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                  <div class="d-flex nav-overview-tabs">

                                  </div>
                                  <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                      <div class="items-table">
                                        <h4>Customer Details</h4>
                                        <div class="customer-details">
                                          <div class="name-code">
                                            <div class="details name">
                                              <p id="custName"></p>
                                            </div>
                                            <div class="details code">
                                              <p id="custCode"></p>
                                            </div>
                                          </div>
                                          <div class="details gstin">
                                            <label for="">GSTIN</label>
                                            <p id="custgst"></p>
                                          </div>
                                          <div class="details pan">
                                            <label for="">PAN</label>
                                            <p id="custpan"></p>
                                          </div>
                                          <div class="address-contact">
                                            <div class="address-customer">
                                              <div class="details">
                                                <label for="">Billing Address</label>
                                                <p id="billAddress" class="pre-normal"></p>
                                              </div>
                                              <div class="details">
                                                <label for="">Shipping Address</label>
                                                <p class="pre-normal" id="shipAddress"></p>
                                              </div>
                                              <div class="details">
                                                <label for="">Place of Supply</label>
                                                <p id="placeofSup"></p>
                                              </div>
                                            </div>
                                            <div class="contact-customer">
                                              <div class="details dotted-border-area">
                                                <label for="">Contacts</label>
                                                <p> <ion-icon name="mail-outline"></ion-icon><span id="custEmail"> </span></p>
                                                <p> <ion-icon name="call-outline"></ion-icon><span id="custPhone"></span></p>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>

                                      <div class="items-table">
                                        <h4>Other Details</h4>
                                        <div class="other-info">
                                          <div class="details">
                                            <label for="">Posting Date</label>
                                            <p id="postingDate"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Posting Time</label>
                                            <p id="postingTime"> </p>
                                          </div>
                                          <div class="details">
                                            <label for="">Delivery Date</label>
                                            <p id="delvDate"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Valid Till</label>
                                            <p id="validTill"></p>
                                          </div>

                                          <div class="details">
                                            <label for="">Credit Period</label>
                                            <p id="creditPeriod"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Sales Person</label>
                                            <p id="salesPerson"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Functional Area</label>
                                            <p id="funcnArea"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Compliance Invoice Type</label>
                                            <p id="compilaceInv"></p>
                                          </div>
                                          <div class="details">
                                            <label for="">Reference Document Link</label>
                                            <p>: <a href="#" id="refDoc"></a></p>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                      <div class="items-view items-calculation" id="item-div-main">
                                        <div class="card item-cards">
                                          <div class="card-body">
                                            <div class="row-section row-first">
                                              <div class="left-info">
                                                <ion-icon name="cube-outline"></ion-icon>
                                                <div class="item-info">
                                                  <p class="code" id="cardSoNo"></p>
                                                  <p class="name" id="cardCustPo"></p>
                                                </div>
                                              </div>
                                              <div class="right-info">
                                                <div class="item-info">
                                                  <p class="code" id="totalItem"></p>
                                                  <!-- <p class="name" id="subTotal_inr"></p> -->
                                                </div>
                                              </div>
                                            </div>
                                            <div class="row-section row-tax">
                                              <div class="left-info">
                                                <div class="item-info">
                                                  <p>Sub Total</p>
                                                  <p>Total Discount</p>
                                                  <p>Taxable Amount</p>
                                                  <p id="igstP">IGST</p>
                                                  <div id="csgst" style="display: none;">
                                                    <p>CGST</p>
                                                    <p>SGST</p>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="right-info">
                                                <div class="item-info">
                                                  <p id="sub_total"></p>
                                                  <p id="totalDis"></p>
                                                  <p id="taxableAmt"></p>
                                                  <p id="igst"></p>
                                                  <div id="csgstVal">
                                                    <p id="cgstVal"></p>
                                                    <p id="sgstVal"></p>
                                                  </div>

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
                                                  <p class="amount" id="total_amount"></p>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="del_status">
                                            </div>
                                          </div>
                                          <div class="items-table">
                                            <div class="details">
                                              <label for="">Remarks</label>
                                              <p id="remark"></p>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="row orders-table">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                      <div class="items-table">
                                        <h4>Item Details</h4>
                                        <div class="multiple-item-table">
                                          <div class="row head-state-table">
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Code</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Name</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">HSN</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Qty</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Currency</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Unit Price</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Base Amount</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Discount</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Taxable Amount</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">GST(%)</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">GST Amount(<span id="currencyHead"></span>)</div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Total Amount</div>
                                          </div>
                                          <div id="itemTableBody">

                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>


                                </div>

                                <div class="tab-pane classicview-pane fade show active" id="nav-classicview" role="tabpanel" aria-labelledby="nav-classicview-tab">
                                  <a href="" class="btn btn-primary classic-view-btn float-right" id="classicViewPrint" target="_blank">Print</a>
                                  <div class="card classic-view bg-transparent" id="innerClassicView">

                                  </div>
                                </div>
                                <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
                                  <div class="inner-content">
                                    <div class="audit-head-section mb-3 mt-3 ">
                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span><span class="created_by_trail"></span><span class="font-bold text-normal"> on </span> <span class="created_at_trail"></span></p>
                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span><span class="updated_by"></span><span class="font-bold text-normal"> on </span> <span class="updated_at"></span> </span></p>
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
                  </div>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
  <div id="loaderModal" class="modal" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Downloading, please wait...</p>
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>
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
  let csvContent;
  let csvContentBypagination;

  $(document).ready(function() {
    var indexValues = [];
    var dataTable;
    var columnMapping = <?php echo json_encode($columnMapping); ?>;


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
    var allData;
    var dataPaginate;

    function fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = '') {
      var fdate = "<?php echo $f_date; ?>";
      var to_date = "<?php echo $to_date; ?>";
      var comid = <?php echo $company_id; ?>;
      var locId = <?php echo $location_id; ?>;
      var bId = <?php echo $branch_id; ?>;
      var checkboxSettings = Cookies.get('cookieManageJournal');
      var notVisibleColArr = [];

      $.ajax({
        type: "POST",
        url: "ajaxs/ajax-manage-journal.php",
        dataType: 'json',
        data: {
          act: 'tdata',
          comid: comid,
          locId: locId,
          bId: bId,
          formDatas: formDatas,
          pageNo: pageNo,
          limit: limit,
          columnMapping: columnMapping
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
          // csvContent = response.csvContent;
          // csvContentBypagination = response.csvContentBypagination;

          if (response.status) {
            var responseObj = response.data;
            dataPaginate = responseObj;
            $('#yourDataTable_paginate').show();
            $('#limitText').show();

            dataTable.clear().draw();
            dataTable.columns().visible(false);
            dataTable.column(-1).visible(true);

            $.each(responseObj, function(index, value) {
              let revBtn = '';
              if (value['journal.reverse_jid'] == '0' && value['journal.journalEntryReference'] != "reverseJournal") {
                // console.log(value.del_status);
                revBtn = `
                                    <li>
                                        <button class="reverseJournal" data-id="${value.id}" data-code="${value['journal.jv_no']}" ><ion-icon name="repeat-outline"></ion-icon>Reverse</button>
                                    </li>`;
              }

              dataTable.row.add([
                value['sl_no'],
                `<a href="#" class="soModal" data-id="${value['id']}">${value['journal.jv_no']}</a>`,
                value['journal.documentNo'],
                value['journal.postingDate'],
                value['journal.journal_created_at'],
                value['journal.journal_created_by'],
                value['journal.party_code'],
                value['journal.party_name'],
                value['journal.journalEntryReference'],
                `<p class="pre-normal">${trimString(value['journal.remark'],20)}</p>`,
                (value['debit_table.total_debit']),
                (value['credit_table.total_credit']),

                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                        ${revBtn}
                                        <li>
                                            <button class="soModal"  data-id="${value.id}"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
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

              // console.log('Cookie is blank.');
            }
          } else {

            $("#detailed_tbody").html(`<td colspan=17 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
            $('#yourDataTable_paginate').hide();
            $('#limitText').hide();
          }
          $("#globalModalLoader").remove();
        },
        complete: function() {
          $("#globalModalLoader").remove();

        },
      });
    }

    fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping);

    $(document).on("click", ".ion-paginationlistJournal", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieManageJournal')
                },
                beforeSend:function(){
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-paginationlistJournal').prop('disabled', true)
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
                    $('.ion-paginationlistJournal').prop('disabled', false)
                }
            })

        });
        $(document).on("click", ".ion-fulllistJournal", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-journal.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieManageJournal'),
                    formDatas: formInputs
                },

                beforeSend:function(){
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-fulllistJournal').prop('disabled', true)
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
                    $('.ion-fulllistJournal').prop('disabled', false);
                }
            })

        });



    //    ----- page length limit-----
    let formInputs = {};
    $(document).on("change", ".custom-select", function(e) {
      var maxlimit = $(this).val();
      fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);
    });
    //    ------------ pagination-------------
    $(document).on("click", "#pagination a", function(e) {
      e.preventDefault();
      var page_id = $(this).attr('id');
      var limitDisplay = $("#itemsPerPage").val();
      //    console.log(limitDisplay);
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
          } else if (columnSlag === 'journal.journal_created_at') {
            values = value2;
          } else if (columnSlag === 'journal.postingDate') {
            values = value3;
          }
          if ((columnSlag === 'delivery_date' || columnSlag === 'so_date' || columnSlag === 'journal.journal_created_at' || columnSlag === 'journal.postingDate') && operatorName == "BETWEEN") {
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
        $("#myForm")[0].reset();
        $(".m-input2").remove();
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
            act: 'manageJournal',
            fromData: fromData
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
      if (columnName === 'Delivery Date') {
        inputId = "value4_" + columnIndex;
      } else if (columnName === 'Created Date') {
        inputId = "value2_" + columnIndex;
      } else if (columnName === 'Posting Date') {
        inputId = "value3_" + columnIndex;
      }

      if ((columnName === 'Delivery Date' || columnName === 'Posting Date' || columnName === 'Created Date') && operatorName === 'BETWEEN') {
        inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input m-input2" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
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


<!------------ modal ajax--------- -->

<script>
  $(document).on("click", ".soModal", function() {

    $('#viewGlobalModal').modal('show');
    $('.ViewfirstTab').tab('show');
    let id = $(this).data('id');
    console.log(id);
    // $('.auditTrail').attr("data-ccode", id);
    $("#innerClassicView").html('');

    $.ajax({
      type: "GET",
      url: "ajaxs/modals/fa/ajax-manage-journal-modal.php",
      dataType: 'json',
      data: {
        act: "modalData",
        id
      },
      beforeSend: function() {},
      success: function(value) {
        console.log(value);
        if (value.status) {
          var responseObj = value.data;
          $('.auditTrail').attr("data-ccode", responseObj.dataObj.jv_no);
          $(".left #amount").html(responseObj.companyCurrency + " " + responseObj.dataObj.total_debit);
          $("#amount-words").html("(" + responseObj.totalDebitInWord + ")");
          $("#po-numbers").html(responseObj.dataObj.jv_no);
          $(".right #cus_name").html(responseObj.dataObj.documentNo);
          $("#default_address").html(formatDate(responseObj.dataObj.postingDate));

          $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print.php?journalId=${btoa(id)}`);
          $(".created_by_trail").html('');
          $(".updated_by").html('');
          // $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
          // $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);
          $('.created_by_trail').html(responseObj.createdBy);
          $('.created_at_trail').html(responseObj.createdAt);
          $('.updated_by').html(responseObj.updatedBy);
          $('.updated_at').html(responseObj.updateAt);
        }
      },
      error: function(error) {
        console.log(error);
      }
    });
    $.ajax({
      type: "GET",
      url: "ajaxs/modals/fa/ajax-manage-journal-modal.php",
      data: {
        act: "classicView",
        id
      },
      beforeSend: function() {
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
        // console.log(response);
        $("#innerClassicView").html(response);
      },
      complete: function() {
        $('#viewGlobalModal .modal-body .load-wrapp').remove();

      },
      error: function(error) {
        console.log(error);
      }
    });

  });

  $(document).on("click", ".reverseJournal", function(e) {
    // e.preventDefault(); // Prevent default click behavior

    var dep_keys = $(this).data('id');
    let code = $(this).data('code');
    var $this = $(this); // Store the reference to $(this) for later use

    Swal.fire({
      icon: 'warning',
      title: 'Are you sure?',
      text: `You want to reverse this ${code} ?`,
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
            dep_slug: 'reverseJOURNAL'
          },
          url: 'ajaxs/ajax-reverse-post.php',
          beforeSend: function() {
            $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
          },
          success: function(response) {
            var responseObj = JSON.parse(response);
            console.log(responseObj);

            if (responseObj.status == 'success') {
              // $this.parent().parent().find('.reverseStatus').html('reverse');
              $this.html('Reversed');
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
              location.reload();
            });
          }
        });
      }
    });
  });
</script>