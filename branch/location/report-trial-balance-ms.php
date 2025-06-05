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
if (!isset($_COOKIE["cookieTrialBalance"])) {
    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    
    if (isset($settingsTable['data'][0]['settingsCheckbox']) && !empty($settingsTable['data'][0]['settingsCheckbox'])) {
        $settingsCh = $settingsTable['data'][0]['settingsCheckbox'];
        $settingsCheckbox_concised_view = unserialize($settingsCh);
        
        if ($settingsCheckbox_concised_view) {
            setcookie("cookieTrialBalance", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
        }
    } else {
        $settingsCheckbox_concised_view = []; // Replace with logic to fetch all slag values
        setcookie("cookieTrialBalance", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    }
}
$columnMapping = [
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
        'name' => 'Opening',
        'slag' => 'opening_val',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Debit',
        'slag' => 'debit_amount',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Credit',
        'slag' => 'credit_amount',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Closing',
        'slag' => 'closing_val',
        'icon' => '',
        'dataType' => 'number'
    ]
];



?>


<link rel="stylesheet" href="../../public/assets/sales-order.css">

<link rel="stylesheet" href="../../public/assets/new_listing.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">

<style>
  .vitwo-alpha-global .dataTables_wrapper {
    overflow: auto;
    height: calc(100vh - 183px);
}


/* Apply to th with text-pos class */
.vitwo-alpha-global .stock-new-table thead tr th.text-pos {
  text-align: right !important;
}
</style>
<?php


$keywd = '';
if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
  $keywd = $_REQUEST['keyword'];
} else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
  $keywd = $_REQUEST['keyword2'];
}

$variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);
$start = explode('-', $variant_sql['data'][0]['year_start']);
$end = explode('-', $variant_sql['data'][0]['year_end']);
$start_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
$end_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper report-wrapper vitwo-alpha-global">
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
                        <h3 class="card-title font-bold text-md">Trial Balance(Detailed View)</h3>
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
                                      <input type="date" class="form-control" name="from_date" id="from_date" value="<?=$start_date?>" required />
                                    </div>
                                    <div class="form-input">
                                      <label class="mb-0" for="">To</label>
                                      <input type="date" class="form-control" name="to_date" id="to_date" value="<?=$end_date?>" required />
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
                        <a href="manage-daybook-concised.php" class=""><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>Transactional Day Book
                        </a>
                        <a href="manage-daybook.php" class="filter-link active"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>Trial Balance(Detailed View)
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

                            <!-- <h5>Search and Export</h5> -->
                            
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
                      

                      <div class="exportgroup">
                        <button class="exceltype btn btn-primary btn-export" type="button">
                          <ion-icon name="download-outline"></ion-icon>
                          Export
                        </button>
                        <ul class="export-options">
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
                              if($column['dataType'] == 'number') {
                                $class = 'text-pos';
                              } else {
                                $class = '';
                              }
                            ?>
                              <th class=<?=$class?> data-value="<?= $index ?>"><?= $column['name'] ?></th>
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
                                      $cookieTableStockReport = json_decode($_COOKIE["cookieTrialBalance"], true) ?? [];

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
        dom: '<"dt-top-container"<l><"dt-center-in-div"B>r><"billList_wrapper"t><ip>',
        "lengthMenu": [10, 25, 50, 100, 200, 250],
        "ordering": false,
        info: false,
        "initComplete": function(settings, json) {
          // $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
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

    let currentStartDate = "<?php echo $start_date; ?>";
    let currentEndDate = "<?php echo $end_date; ?>";

    function fill_datatable() {
    var comid = <?php echo $company_id; ?>;
    var locId = <?php echo $location_id; ?>;
    var bId = <?php echo $branch_id; ?>;
    // var currentStartDate = "<?php echo isset($f_date) ? $f_date : date('Y-m-d', strtotime('-1 day')); ?>";
    // var currentEndDate = "<?php echo isset($to_date) ? $to_date : date('Y-m-d'); ?>";

    $.ajax({
        type: "POST",
        url: "ajaxs/TrialBalance/ajax-trial-balance.php",
        dataType: 'json',
        data: {
            act: 'trialBalanceDatatable',
            comid: comid,
            locId: locId,
            bId: bId,
            from_date: currentStartDate,
            to_date: currentEndDate
        },
        beforeSend: function() {
            $("#detailed_tbody").html(`<td colspan=6 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
        },
        success: function(response) {
            if (response.status && response.data.length > 0) {
                var responseObj = response.data;
                dataTable.clear().draw();

                $.each(responseObj, function(index, value) {
                    // Determine if the row is a special row (Profit/Loss, Adjustment, or Total)
                    var isSpecialRow = value.gl_label === "Profit/Loss" || 
                                      value.gl_label === "Opening Balance Adjustment" || 
                                      value.gl_label === "Total";
                    var boldClass = isSpecialRow ? 'font-weight-bold' : '';

                    dataTable.row.add([
                        `<p class="${boldClass}">${value.gl_code || ''}</p>`,
                        `<p class="${boldClass}">${value.gl_label || ''}</p>`,
                        `<p class="text-right ${boldClass}">${value.opening_val}</p>`,
                        `<p class="text-right ${boldClass}">${value.debit_amount}</p>`,
                        `<p class="text-right ${boldClass}">${value.credit_amount}</p>`,
                        `<p class="text-right ${boldClass}">${value.closing_val}</p>`
                    ]).draw(false);
                });
            } else {
                $("#detailed_tbody").html(`<td colspan=6 class='else-td not-found-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
            }
        },
        error: function(error) {
            console.log(error);
            $("#detailed_tbody").html(`<td colspan=6 class='else-td not-found-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/no-transaction.gif" width="150" alt=""><p>Error Loading Data</p></td>`);
        }
    });
}

    var tablename = $("#tablename").val();
    var pageTableName = $("#pageTableName").val();
    var settingsCheckbox = [];

    // Gather all slag values from checkboxes
    var settingsCheckbox = <?= json_encode(array_column($columnMapping, 'slag')); ?>;

    var fromData = {
        tablename,
        pageTableName,
        settingsCheckbox
    };

    // Send AJAX request to store all slag values in cookie
    $.ajax({
        type: "POST",
        url: "ajaxs/ajax-save-cookies.php",
        dataType: 'json',
        data: {
            act: 'trialBalance',
            fromData: fromData
        },
        success: function (response) {
            console.log(response);
        },
        error: function (error) {
            console.log(error);
        }
    });

    fill_datatable();

    $('#fYDropdown').on('change', function() {
      let sel = $(this).find('option:selected');
      currentStartDate = sel.data('start');
      currentEndDate = sel.data('end');
      $("#from_date").val(currentStartDate);
      $("#to_date").val(currentEndDate);
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
      $("#from_date").val(currentStartDate);
      $("#to_date").val(currentEndDate);
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

    $(document).on("click", ".ion-fulllistnew", function(e) {
      e.preventDefault(); // prevent default action, if any
        // If totalRows is within safe range, skip confirmation
        $.ajax({
          type: "POST",
          url: "ajaxs/TrialBalance/ajax-trial-balance.php",
          dataType: "json",
          data: {
            act: 'alldata',
            coloum: columnMapping,
            sql_data_checkbox: Cookies.get('cookieTrialBalance'),
            // formDatas: formInputs,
            // from_date: currentStartDate,
            // to_date: currentEndDate,
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