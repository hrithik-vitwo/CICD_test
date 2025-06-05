<?php

require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");









$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = $fileNameWithoutExtension . 'download_all_' . $currentDateTime;
if (!isset($_COOKIE["cookieDaybookTransac"])) {
  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
  $settingsCheckbox_concised_view = unserialize($settingsCh);
  if ($settingsCheckbox_concised_view) {
    setcookie("cookieDaybookTransac", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
  } else {
    for ($i = 0; $i < 5; $i++) {
      $isChecked = ($i < 5) ? 'checked' : '';
    }
  }
}
$columnMapping = [
  [
    'name' => '#',
    'slag' => 'sl_no',
    'icon' => '',
    'dataType' => 'number'
  ],
  [
    'name' => 'Branch',
    'slag' => 'branchName',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Location',
    'slag' => 'locationName',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Accounting Document No',
    'slag' => 'jv_no',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Document No',
    'slag' => 'documentNo',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Reference No',
    'slag' => 'referenceCode',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Posting Date',
    'slag' => 'postingDate',
    'icon' => '',
    'dataType' => 'date'
  ],
  [
    'name' => 'Created Date',
    'slag' => 'journal_created_at',
    'icon' => '',
    'dataType' => 'date'
  ],
  [
    'name' => 'Created By',
    'slag' => 'journal_created_by',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Order No',
    'slag' => 'Order_num',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Order Date',
    'slag' => 'summary1.document_date',
    'icon' => '',
    'dataType' => 'date'
  ],
  [
    'name' => 'Party Code',
    'slag' => 'party_code',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Party Name',
    'slag' => 'party_name',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'GL Code',
    'slag' => 'gl_code',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'GL Name',
    'slag' => 'gl_label',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Sub GL Code',
    'slag' => 'sub_gl_code',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Sub GL Name',
    'slag' => 'sub_gl_name',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Transaction Type',
    'slag' => 'journal_entry_ref',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Narration',
    'slag' => 'remark',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Type(Dr/Cr)',
    'slag' => 'type',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Amount',
    'slag' => 'journal.amount',
    'icon' => '',
    'dataType' => 'number'
  ],
  [
    'name' => 'Clearing Document No',
    'slag' => 'clearingDocNo',
    'icon' => '',
    'dataType' => 'string'
  ],
  [
    'name' => 'Clearing Document Date',
    'slag' => 'clearingDocDate',
    'icon' => '',
    'dataType' => 'date'
  ],
  [
    'name' => 'Cleared By',
    'slag' => 'clearedBy',
    'icon' => '',
    'dataType' => 'string'
  ]
];



?>


<link rel="stylesheet" href="../../public/assets/sales-order.css">

<link rel="stylesheet" href="../../public/assets/new_listing.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">

<style>
  #accordionQuality {
    overflow: scroll;
    scrollbar-width: none;
  }

  #accordionQuality .item-status {
    justify-content: flex-start;
  }

  #accordionSpecifications p {
    width: auto;
  }

  .qualityModalTable tr th {
    font-weight: 600 !important;
    color: #000 !important;
    background: #ebebeb !important;
  }

  .global-view-modal .modal-header .left {
    justify-content: center;
  }

  .status-dr::before {
    content: '';
    position: relative;
    left: -4px;
    display: inline-block;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background-color: #00d0068f;
  }

  .status-cr::before {
    content: '';
    position: relative;
    left: -4px;
    display: inline-block;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background-color: #007bff6e;
  }

  .status-cr {
    border-radius: 50%;
    background-color: #007bff6e;
  }

  .status-dr {
    border-radius: 50%;
    background-color: #00d0068f;
  }

  .status-bg {
    padding: 5px 10px;
    border-radius: 7px;
    font-size: 0.6rem;
    font-weight: 600;
    text-align: center;
    position: relative;
    display: inline-block;
  }
</style>

<?php


$keywd = '';
if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
  $keywd = $_REQUEST['keyword'];
} else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
  $keywd = $_REQUEST['keyword2'];
}


$start_date = date('Y-m-d', strtotime('-1 day'));
$end_date = date('Y-m-d');
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper is-daybook report-wrapper vitwo-alpha-global">
  <!-- Content Header (Page header) -->
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <?php ?>
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
                        <h3 class="card-title font-bold text-md">Transactional Day Book (From <small id="fromDate"><?= formatDateWeb($start_date) ?></small> To <small id="toDate"><?= formatDateWeb($end_date) ?></small>)</h3>
                      </div>
                    </div>


                    <div class="right-block">
                      <div id="containerThreeDot">
                        <div id="menu-wrap">
                          <input type="checkbox" class="toggler bg-transparent searchboxop" />
                          <div class="dots">
                            <div></div>
                          </div>
                          <div class="menu">
                            <div class="fy-custom-section fy-dropdown">
                              <div class="fy-dropdown-section">
                                <h6 class="text-xs font-bold">Financial Year</h6>
                                <div class="dropdown-fyear">
                                  <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                                    <option value="">Select FY</option>
                                    <!-- FY options will be appended by JS -->
                                    <?php

                                    $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                                    foreach ($variant_sql['data'] as $key => $data) {
                                      $start = explode('-', $data['year_start']);
                                      $end = explode('-', $data['year_end']);
                                      $startDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                      $endDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));

                                    ?>
                                      <option value="<?= $data['year_variant_id'] ?>" data-start="<?= $startDate ?>" data-end="<?= $endDate ?>" <?php if (($_POST['drop_val'] == 'fYDropdown' && $_POST['drop_id'] == $data['year_variant_id'])) {
                                                                                                                                                  echo "selected";
                                                                                                                                                } ?>><?= $data['year_variant_name'] ?></option>
                                    <?php
                                    }
                                    ?>
                                  </select>

                                  <label class="mb-0" for="">OR</label>

                                  <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                                    <option value="">Days Filter</option>
                                    <option value="0">Today Report</option>
                                    <option value="6">Last 7 Days</option>
                                    <option value="14">Last 15 Days</option>
                                    <option value="29">Last 30 Days</option>
                                    <option value="44">Last 45 Days</option>
                                    <option value="59">Last 60 Days</option>
                                  </select>
                                </div>

                                <h6 class="text-xs font-bold"><span class="finacialYearCla"></span></h6>
                              </div>

                              <div class="customrange-section">
                                <h6 class="text-xs font-bold">Custom Range</h6>
                                <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                  <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                  <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />

                                  <div class="date-range-input d-flex">
                                    <div class="form-input">
                                      <input type="date" class="form-control" name="from_date" id="from_date" value="" required />
                                    </div>
                                    <div class="form-input">
                                      <label class="mb-0" for="">To</label>
                                      <input type="date" class="form-control" name="to_date" id="to_date" value="" required />
                                    </div>
                                  </div>

                                  <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                                </form>
                                <h6 class="text-xs font-bold"><span class="customRangeCla"></span></h6>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="page-list-filer filter-list">
                        <a href="manage-daybook-concised.php" class=""><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>Concised View
                        </a>
                        <a href="manage-daybook.php" class="filter-link active"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>Detailed View
                        </a>
                      </div>
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
                                    <button class="ion-paginationlistnew">
                                      <ion-icon name="list-outline"
                                        class="ion-paginationlistnew md hydrated"
                                        id="exportAllBtn" role="img"
                                        aria-label="list outline"></ion-icon>Export
                                    </button>
                                  </li>
                                  <li>
                                    <button class="ion-fulllistnew">
                                      <ion-icon name="list-outline"
                                        class="ion-fulllistnew md hydrated"
                                        role="img"
                                        aria-label="list outline"></ion-icon>Download
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
                          <select name="" id="dayBookDetailedLimit" class="custom-select">
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
                                      $cookieTableStockReport = json_decode($_COOKIE["cookieDaybookTransac"], true) ?? [];

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
                                      if ($columnIndex == 0 || $columnIndex == 1 || $columnIndex == 2 || $columnIndex === 6 || $columnIndex === 15 || $columnIndex === 16 ||  $columnIndex === 20 || $columnIndex === 21 || $columnIndex === 22 || $columnIndex === 23) {
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
  </section>
  <div class="modal fade right global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
      <!--Content-->
      <div class="modal-content">
        <!--Header-->
        <div class="modal-header">
          <div class="top-details">
            <div class="left">
              <p class="info-detail amount"><ion-icon
                  name="business-outline"></ion-icon><span
                  id="vendorName"></span></p>
              <p class="info-detail po-number"><ion-icon
                  name="information-outline"></ion-icon><span
                  id="vendorCode"></span></p>
              <p class="info-detail po-number"><ion-icon
                  name="information-outline"></ion-icon><span
                  id="invNumber"></span></p>
            </div>
            <div class="right">
              <div class="qa-item-recieve-block">
                <p class="text-sm my-2 font-bold" id="totalreq"> </p>
                <div class="qa-item-recieve-block-sub-item">
                  <p class="text-sm my-2 font-bold" id="totalChecked">Checked :</p>
                  <div class="qa-checked-item">
                    <p class="text-xs my-2" id="htmlPassed"> </p>
                    <p class="text-xs my-2" id="htmlRejected"> </p>
                  </div>
                  <p class="text-xs my-2 font-bold" id="htmlRemaining"> </p>
                </div>
              </div>
            </div>

          </div>
        </div>
        <!--Body-->
        <div class="modal-body">
          <nav>
            <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
              <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Quality Check</button>
              <button class="nav-link relativeHistory" id="nav-relativehistory-tab"
                data-bs-toggle="tab" data-bs-target="#nav-relativehistory" data-stocklogid=""
                type="button" role="tab"
                aria-controls="nav-relativehistory"
                aria-selected="false"><ion-icon
                  name="document-text-outline"></ion-icon>Relative History</button>
              <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
            </div>
          </nav>
          <div class="tab-content global-tab-content" id="nav-tabContent">

            <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
              <div class="d-flex nav-overview-tabs">

              </div>


              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-12">

                  <div class="matrix-accordion p-0" id="accordionQuality">
                    <div class="item-status d-flex gap-4">
                      <table class="qualityModalTable">
                        <thead>
                          <tr>
                            <th>SL.</th>
                            <th>Doc No.</th>
                            <th>Passed</th>
                            <th>Rejected</th>
                            <th>Status</th>
                            <th>Done By</th>
                            <th>Done On</th>
                            <th>Retested &amp; Passed</th>
                            <th>Remarks</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody id="rejectListFrmTableBody">

                        </tbody>
                      </table>

                    </div>

                  </div>


                  <div class="row">
                    <!-- <div class="col-12 col-lg-12 col-md-12 col-sm-12"> -->
                    <!-- <div class=""> -->
                    <div class="row orders-table">
                      <!-- <div class="col-lg-12 col-md-8 col-sm-12 col-8"> -->
                      <div class="items-table">
                        <table>
                          <thead>
                            <tr>
                              <th>Item Code</th>
                              <th>Item Name</th>
                              <th>Type</th>
                              <th>Avalability Check</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <tbody id="itemTableSpecification">


                          </tbody>
                        </table>
                        <!-- <div class="col-lg-12 col-md-12 col-sm-12 col-12"> -->
                        <div class="items-table">
                          <h4>Specifications</h4>
                          <table>
                            <thead>
                              <tr>
                                <th>Item Description</th>
                                <th>Net Weight</th>
                                <th>Gross Weight</th>
                                <th>Volume</th>
                                <th>Volume Cube Cm</th>
                                <th>Height</th>
                                <th>Width</th>
                                <th>Length</th>
                              </tr>
                            </thead>
                            <tbody id="itemSpecificationsDatatable">

                            </tbody>
                          </table>
                        </div>
                        <!-- </div> -->
                      </div>
                      <!-- </div> -->

                    </div>
                    <!-- </div> -->
                    <!-- </div> -->
                  </div>




                </div>

              </div>
            </div>

            <div class="tab-pane fade" id="nav-relativehistory" role="tabpanel" aria-labelledby="nav-relativehistory-tab">
              <table class="table table-hover qualityModalTable">
                <thead>
                  <tr>
                    <th>SL.</th>
                    <th>Doc No.</th>
                    <th>Passed</th>
                    <th>Rejected</th>
                    <th>Status</th>
                    <th>Done By</th>
                    <th>Done On</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="relativeHistoryviewTableData">

                </tbody>
              </table>
            </div>
            <div class="modal fade customer-modal" id="detailedHistoryModal" tabindex="-1" aria-labelledby="detailedHistoryLabel" aria-hidden="true">
              <div class="modal-dialog w-25" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <div class="pdf-view">
                      <span class="float-label">PDF View</span>
                      <p></p>
                    </div>
                    <div class="img-view">
                      <span class="float-label">Image View</span>
                    </div>
                  </div>
                  <!-- <div class="modal-footer">
                                                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                    <button type="button" class="btn btn-primary">Save changes</button>                                                                                                                            </div> -->
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
              <div class="inner-content">
                <div class="audit-head-section mb-3 mt-3 ">
                  <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span><span class="created_by_trail"></span></p>
                  <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span><span class="updated_by"></span></p>
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
      <!--/.Content-->
    </div>
  </div>

  <!-- <div id="loaderModal" class="modal" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Downloading, please wait...</p>
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div> -->
</div>
<!-- /.row -->
<!-- /.content -->


<!-- /.Content Wrapper. Contains page content -->
<!-- For Pegination------->
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
  <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                              echo $_REQUEST['pageNo'];
                                            } ?>">
</form>
<!-- End Pegination from------->


<?php

require_once("../common/footer2.php");
?>


<script>
  var input = document.getElementById("myInput");
  input.addEventListener("keypress", function(event) {
    // console.log(event.key)

    if (event.key === "Enter") {
      event.preventDefault();
      // alert("clicked")
      document.getElementById("myBtn").click();
    }
  });
  var form = document.getElementById("search");

  document.getElementById("myBtn").addEventListener("click", function() {
    form.submit();
  });
</script>








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


<!-- datatable and modal script portion  -->

<script>
  $(document).ready(function() {
    var indexValues = [];
    var dataTable;
    var totalRows;
    let columnMapping = <?php echo json_encode($columnMapping); ?>
    // let dataPaginate;

    function initializeDataTable() {
      dataTable = $("#dataTable_detailed_view").DataTable({
        dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"billList_wrapper"t><ip>',
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
            extend: 'csv',
            text: '<ion-icon name="document-outline" class="ion-csv"></ion-icon> CSV'
          }]
        }],
        // select: true,
        "bPaginate": false,
      });

    }
    // $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

    initializeDataTable();

    var allData;
    var dataPaginate;


    // function full_datatable() {
    //     let fromDate = "<?= $fromDate ?>"; // For Date Filter
    //     let toDate = "<?= $toDate ?>"; // For Date Filter        
    //     let comid = <?= $company_id ?>;
    //     let locId = <?= $location_id ?>;
    //     let bId = <?= $branch_id ?>;

    //     $.ajax({
    //         type: "POST",
    //         url: "ajaxs/discount/ajax-manage-discount-variation-all.php",
    //         dataType: 'json',
    //         data: {
    //             act: 'alldata',
    //         },
    //         beforeSend: function () {

    //         },
    //         success: function (response) {
    //             // all_data = response.all_data;
    //             allData = response.all_data;


    //         },
    //     });
    // };
    // full_datatable();

    let currentStartDate = "<?php echo $start_date; ?>";
    let currentEndDate = "<?php echo $end_date; ?>";

    function fill_datatable(formDatas = '', pageNo = '', limit = '') {
      var comid = <?php echo $company_id; ?>;
      var locId = <?php echo $location_id; ?>;
      var bId = <?php echo $branch_id; ?>;
      var columnMapping = <?php echo json_encode($columnMapping); ?>;
      var checkboxSettings = Cookies.get('cookieDaybookTransac');
      var notVisibleColArr = [];

      $.ajax({
        type: "POST",
        url: "ajaxs/daybook/ajax-manage-daybook-transactional-detailed.php",
        dataType: 'json',
        data: {
          act: 'detailedTransactional',
          comid: comid,
          locId: locId,
          bId: bId,
          formDatas: formDatas,
          pageNo: pageNo,
          limit: limit,
          from_date: currentStartDate,
          to_date: currentEndDate,
        },
        beforeSend: function() {
          $("#detailed_tbody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
        },
        success: function(response) {
          // console.log(response);
          // alert(response)

          if (response.status) {
            var responseObj = response.data;
            dataPaginate = responseObj;
            $('#yourDataTable_paginate').show();
            $('#limitText').show();
            totalRows = response.totalRows;
            dataTable.clear().draw();
            dataTable.columns().visible(false);
            dataTable.column(-1).visible(true);
            $.each(responseObj, function(index, value) {
              //  $('#item_id').val(value.itemId);
              let typeVal = '';
              if (value.type == "DR") {
                typeVal = `<p class="status-dr status-bg">${value.type}</p>`
              } else if (value.type == "CR") {
                typeVal = `<p class="status-cr status-bg">${value.type}</p>`
              }
              dataTable.row.add([
                value.sl_no,
                value['branchName'],
                value['locationName'],
                value['jv_no'],
                value['documentNo'],
                value['referenceCode'], // journal.refarenceCode
                value['postingDate'], // journal.postingDate
                value['journal_created_at'], // journal.journal_created_at
                value['journal_created_by'], // journal.journal_created_by
                value['Order_num'], // journal.Order_num
                value['summary1.document_date'], // journal.documentDate
                value['party_code'], // journal.party_code
                value['party_name'], // journal.party_name
                value['gl_code'], // coa.gl_code
                value['gl_label'], // coa.gl_label
                value['sub_gl_code'],
                value['sub_gl_name'],
                value['journal_entry_ref'], // journal.journal_entry_ref
                value['remark'], // journal.remark
                typeVal, // journal.Type
                value['journal.amount'], // journal.Amount
                value['journal.ClearingDocumentNo'], // calculated ClearingDocumentNo
                value['journal.ClearingDocumentDate'], // calculated ClearingDocumentDate
                value['journal.ClearedBy'],

                ` <div class="dropout">
                                     <button class="more">
                                          <span></span>
                                          <span></span>
                                          <span></span>
                                     </button>
                                     <ul>
                                        <li>
                                            
                                        </li>
                                     </ul>
                                   
                                 </div>`,
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


            } else {
              $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
              $(".settingsCheckbox_detailed").each(function(index) {
                if ($(this).prop("checked")) {
                  dataTable.column(index).visible(true);

                }
              });
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

    $('#fYDropdown').on('change', function() {
      let sel = $(this).find('option:selected');
      currentStartDate = sel.data('start');
      currentEndDate = sel.data('end');
      $("#fromDate").html(formatDate(currentStartDate));
      $("#toDate").html(formatDate(currentEndDate));
      fill_datatable();
    });

    $('#quickDropdown').on('change', function() {
      let days = parseInt($(this).val(), 10) || 0;
      let today = new Date(),
        d = new Date();
      d.setDate(d.getDate() - days);
      currentStartDate = d.toISOString().slice(0, 10);
      currentEndDate = today.toISOString().slice(0, 10);
      $("#fromDate").html(formatDate(currentStartDate));
      $("#toDate").html(formatDate(currentEndDate));
      fill_datatable();
    });


    $('#date_form').on('submit', function(e) {
      e.preventDefault();
      currentStartDate = $('#from_date').val();
      currentEndDate = $('#to_date').val();
      $("#fromDate").html(formatDate(currentStartDate));
      $("#toDate").html(formatDate(currentEndDate));
      fill_datatable();
    });


    function compare_date() {
      let fromDate = $("#from_date").val();
      let toDate = $("#to_date").val();

      const date1 = new Date(fromDate);
      const date2 = new Date(toDate);
      const diffTime = Math.abs(date2 - date1);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      if (fromDate && toDate) {
        if (diffDays > 366) {
          document.getElementById("rangeid").disabled = true;
          $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">Date Range can not be greater than 1 year</p>`);
        } else {
          $(".customRangeCla").html('');
          document.getElementById("rangeid").disabled = false;

          if (toDate < fromDate) {
            $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">From Date can not be greater than To Date</p>`);
            document.getElementById("rangeid").disabled = true;

          } else {
            $(".customRangeCla").html('');
            document.getElementById("rangeid").disabled = false;
          }
        }
      }
    }

    $("#to_date").keyup(function() {
      compare_date();
    });

    $("#from_date").change(function() {
      compare_date();
    });

    $("#to_date").change(function() {
      compare_date();
    });

    // $('#menu-wrap').on('click', function(event) {
    //   event.preventDefault(); // Prevent the default click behavior
    //   event.stopPropagation(); // Stop the event from bubbling up
    // });

    $(document).on("click", ".ion-paginationlistnew", function(e) {
      $.ajax({
        type: "POST",
        url: "../common/exportexcel-new.php",
        dataType: "json",
        data: {
          act: 'paginationlist',
          data: JSON.stringify(dataPaginate),
          coloum: columnMapping,
          sql_data_checkbox: Cookies.get('cookieDaybookTransac'),
          from_date: currentStartDate,
          to_date: currentEndDate,
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
      e.preventDefault(); // prevent default action, if any

      if (totalRows > 10000) {
        Swal.fire({
          title: "Are you sure?",
          text: "Downloading 10,000 rows at once is allowed. Would you like to continue?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Yes, proceed",
          cancelButtonText: "Cancel"
        }).then((result) => {
          if (result.isConfirmed) {
            // Proceed with AJAX call
            $.ajax({
              type: "POST",
              url: "ajaxs/daybook/ajax-manage-daybook-transactional-detailed.php",
              dataType: "json",
              data: {
                act: 'alldata',
                coloum: columnMapping,
                sql_data_checkbox: Cookies.get('cookieDaybookTransac'),
                formDatas: formInputs,
                from_date: currentStartDate,
                to_date: currentEndDate,
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
          } else {
            // User canceled
            console.log("User cancelled download.");
          }
        });
      } else {
        // If totalRows is within safe range, skip confirmation
        $.ajax({
          type: "POST",
          url: "ajaxs/daybook/ajax-manage-daybook-transactional-detailed.php",
          dataType: "json",
          data: {
            act: 'alldata',
            coloum: columnMapping,
            sql_data_checkbox: Cookies.get('cookieDaybookTransac'),
            formDatas: formInputs,
            from_date: currentStartDate,
            to_date: currentEndDate,
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
      }
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
      var limitDisplay = $("#dayBookDetailedLimit").val();
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

          if (columnSlag === 'postingDate') {
            values = value4;
          } else if (columnSlag === 'journal_created_at') {
            values = value2;
          } else if (columnSlag === 'summary1.document_date') {
            values = value3;
          }

          if ((columnSlag === 'postingDate' || columnSlag === 'journal_created_at' || columnSlag === 'summary1.document_date') && operatorName == "BETWEEN") {
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
            act: 'transactionalDaybook',
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

<!-- datatable and modal portion script ⬆️ -->

<script>
  $(document).ready(function() {

  });
</script>



<!-- -----fromDate todate input add--- -->


<script>
  $(document).ready(function() {
    $(document).on("change", ".selectOperator", function() {
      let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
      let operatorName = $(this).val();
      let columnName = $(`#columnName_${columnIndex}`).html().trim();
      let inputContainer = $(`#td_${columnIndex}`);
      let inputId;
      if (columnName === 'Posting Date') {
        inputId = "value4_" + columnIndex;
      } else if (columnName === 'Created Date') {
        inputId = "value2_" + columnIndex;
      } else if (columnName === 'Order Date') {
        inputId = "value3_" + columnIndex;
      }

      if ((columnName === 'Posting Date' || columnName === 'Created Date' || columnName === 'Order Date') && operatorName === 'BETWEEN') {
        inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input m-input2" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
      } else {
        $(`#${inputId}`).remove();
      }
      // console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
    });

  });
</script>


<script>
  $(function() {
    $('[data-toggle="tooltip"]').tooltip()
  })
</script>

<!-- other params isset script portion here  -->

<script>
  function table_settings() {
    var favorite = [];
    $.each($("input[name='settingsCheckbox[]']:checked"), function() {
      favorite.push($(this).val());
    });
    var check = favorite.length;
    if (check < 5) {
      alert("Please Check Atlast 5");
      return false;
    }
  }
</script>


<!-- other portion isset script portion here ⬆️ -->



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
    el.addEventListener("blur", function() {
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

<script>
  $(document).ready(function() {


    $(document).on("click", "#btnSearchCollpase", function() {
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




  });
</script>



<script>
  var input = document.getElementById("myInput");
  input.addEventListener("keypress", function(event) {
    // console.log(event.key)

    if (event.key === "Enter") {
      event.preventDefault();
      // alert("clicked")
      document.getElementById("myBtn").click();
    }
  });
  var form = document.getElementById("search");

  document.getElementById("myBtn").addEventListener("click", function() {
    form.submit();
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