<?php
require_once("../../app/v1/connection-branch-admin.php");
$pageName =  basename($_SERVER['PHP_SELF'], '.php');
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


if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}



?>


<style>
  .chartContainer {
    width: 100%;
    height: 500px;
    margin-top: 6em;
  }

  .content-wrapper table tr:nth-child(2n+1) td {
    background: #b5c5d3;
  }

  tfoot.individual-search tr th {
    padding: 5px !important;
    border-right: 1px solid #fff !important;
  }

  .vertical-align {
    vertical-align: middle;
  }

  /* .green-text {
    color: #14ca14 !important;
    font-weight: 600;
  }

  .red-text {
    color: red !important;
    font-weight: 600;
  } */

  .dataTables_scrollHeadInner tr th {
    position: sticky;
    top: -1px;
  }

  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row {
    display: flex !important;
    align-items: center;
    justify-content: end;
  }

  /* div.dataTables_wrapper {
    overflow: hidden;
  } */

  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(1),
  div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(3) {
    padding: 10px 20px;
  }

  div.dataTables_wrapper div.dataTables_length select {
    width: 60% !important;
    appearance: none !important;
    -webkit-appearance: none;
    -moz-appearance: none;
  }

  .dataTables_scroll {
    position: relative;
    margin-bottom: 10px;
  }

  .dataTables_scroll::-webkit-scrollbar {
    visibility: hidden;
  }

  .dataTables_scrollBody tfoot th {
    background: none !important;
  }

  .dataTables_scrollHead {
    margin-bottom: 40px;
  }

  .dataTables_scrollBody {
    max-height: 75vh !important;
    height: 75% !important;
    overflow: scroll !important;
  }

  .dataTables_scrollFoot {
    position: absolute;
    top: 37px;
    height: 50px;
    overflow-y: scroll;
  }

  div.dataTables_wrapper div.dataTables_filter input {
    margin-left: 10px;
  }

  div.dataTables_scrollFoot>.dataTables_scrollFootInner th {
    border: 0;
  }

  .dataTables_filter {
    padding-right: 0 !important;
  }

  div.dataTables_wrapper div.dataTables_paginate ul.pagination {
    padding: 0;
    border: 0;
  }

  .dt-top-container {
    display: flex;
    align-items: center;
    padding: 0 20px;
    gap: 0;
  }

  .transactional-book-table tr td {
    white-space: pre-line !important;
  }

  .dataTables_length {
    margin-left: 50px;
  }

  a.btn.add-col.setting-menu.waves-effect.waves-light {
    position: absolute !important;
    display: flex;
    justify-content: space-between;
    top: 10px !important;
  }

  div.dataTables_wrapper div.dataTables_length label {
    margin-bottom: 0;
  }

  div.dataTables_wrapper div.dataTables_info {
    padding-left: 20px;
    position: relative;
    top: 0;
  }

  .dataTables_paginate {
    position: relative;
    right: 20px;
    bottom: 20px;
    margin-top: -15px;
  }

  .dt-center-in-div {
    display: block;
    /* order: 3; */
    margin-left: auto;
  }

  .dt-buttons.btn-group.flex-wrap button {
    background-color: #003060 !important;
    border-color: #003060 !important;
    border-radius: 7px !important;
  }

  /* .setting-row .col .btn.setting-menu {
    position: absolute !important;
    right: 255px;
    top: 10px;
  } */

  .dt-buttons.btn-group.flex-wrap {
    gap: 10px;
  }


  table.dataTable>thead .sorting:before,
  table.dataTable>thead .sorting:after,
  table.dataTable>thead .sorting_asc:before,
  table.dataTable>thead .sorting_asc:after,
  table.dataTable>thead .sorting_desc:before,
  table.dataTable>thead .sorting_desc:after,
  table.dataTable>thead .sorting_asc_disabled:before,
  table.dataTable>thead .sorting_asc_disabled:after,
  table.dataTable>thead .sorting_desc_disabled:before,
  table.dataTable>thead .sorting_desc_disabled:after {

    display: block !important;

  }

  .dataTable thead tr th,
  .dataTable tfoot.individual-search tr th {
    padding-right: 30px !important;
    border-right: 0 !important;
  }

  select.fy-dropdown {
    max-width: 100px;
  }

  .report-wrapper .daybook-filter-list.filter-list {
    display: flex;
    gap: 6px;
    justify-content: flex-start;
    position: relative;
    top: 45px;
    left: 255px;
    float: right;
  }

  .daybook-filter-list.filter-list a.active {
    background-color: #003060;
    color: #fff;
  }

  .date-range-input {
    gap: 7px;
  }

  .date-range-input .form-input {
    width: 100%;
  }

  .report-wrapper table tr td {
    background: #e7ebef;
  }

  .reports-card .filter-list a {
    background: #dedede;
    color: #003060;
    z-index: 9;
  }

  .report-wrapper .reports-card {
    background: #fff;
  }

  .report-wrapper table tr:nth-child(2n+1) td {
    background: #ffffff;
  }

  .label-select {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  @media (max-width: 769px) {
    .dt-buttons.btn-group.flex-wrap {
      gap: 10px;
      position: absolute;
      top: -39px;
      right: 60px;
    }

    .dt-buttons.btn-group.flex-wrap button {
      max-width: 60px;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
      margin-top: -10px;
    }


  }

  @media (max-width :575px) {
    .dataTables_scrollFoot {
      position: absolute;
      top: 28px;
    }

    .dt-top-container {
      display: flex;
      align-items: baseline;
      padding: 0 20px;
      gap: 20px;
      flex-direction: column-reverse;
      flex-wrap: nowrap;
    }

    .dataTables_length {
      margin-left: 0;
      margin-bottom: 1em;
    }



    div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    }

    .dt-center-in-div {
      margin: 3px auto;
    }

    div.dataTables_filter {
      right: 0;
      margin-top: 0;
      position: relative;
      right: -43px;
    }

    .dt-buttons.btn-group.flex-wrap {
      gap: 10px;
      position: relative;
      top: 0;
      right: 0;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
      margin-top: 40px;
    }

    .dataTables_length label {
      font-size: 0;
    }
  }

  @media (max-width: 376px) {
    div.dataTables_wrapper div.dataTables_filter {
      margin-top: 0;
      padding-left: 0 !important;
    }



    div.dataTables_wrapper div.dataTables_filter input {
      max-width: 150px;
    }

    select.fy-dropdown {
      max-width: 100px;
    }



    /* div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    } */
  }
</style>

<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<!-- Resources -->
<script src="../../public/assets/core.js"></script>
<script src="../../public/assets/charts.js"></script>
<script src="../../public/assets/animated.js"></script>
<script src="../../public/assets/forceDirected.js"></script>
<script src="../../public/assets/sunburst.js"></script>


<?php
// One single Query


?>
<!-- Content Wrapper. Graph View -->
<div class="content-wrapper report-wrapper">
  <!-- Content Header (Page header) -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">

      <!-- row -->
      <div class="row p-0 m-0">
        <div class="col-12 mt-2 p-0">
          <div class="card card-tabs reports-card">
            <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
              <!---------------------- Search START -->
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                  <div class="label-select">
                    <h3 class="card-title mb-0">Order to Sales Report</h3>
                  </div>

                  <div class="fy-custom-section">
                    <div class="fy-dropdown-section">
                      <?php
                      $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                      if (isset($_POST['from_date'])) {
                        $f_date = $_POST['from_date'];
                        $to_date = $_POST['to_date'];
                        //echo 1;


                      } else {

                        $start = explode('-', $variant_sql['data'][0]['year_start']);
                        $end = explode('-', $variant_sql['data'][0]['year_end']);
                        $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                        $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                        $_POST['from_date'] = $f_date;
                        $_POST['to_date'] = $to_date;
                        $_POST['drop_val'] = 'fYDropdown';
                        $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                      }

                      ?>
                      <h6 class="text-xs font-bold">Financial Year</h6>
                      <div class="dropdown-fyear">
                        <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                          <option value="">--Select FY--</option>
                          <?php
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

                          <option value="customrange" <?php if ($_POST['drop_id'] == '') {
                                                        echo "selected";
                                                      } ?>>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                          </option>
                        </select>

                        <label class="mb-0" for="">OR</label>


                        <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                          <option value="">--Select One--</option>
                          <option value="0" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 0) {
                                              echo "selected";
                                            } ?>>Today Report</option>
                          <option value="6" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 6) {
                                              echo "selected";
                                            } ?>>Last 7 Days</option>
                          <option value="14" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 14) {
                                                echo "selected";
                                              } ?>>Last 15 Days</option>
                          <option value="29" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 29) {
                                                echo "selected";
                                              } ?>>Last 30 Days</option>
                          <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                                echo "selected";
                                              } ?>>Last 45 Days</option>
                          <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                                echo "selected";
                                              } ?>>Last 60 Days</option>
                        </select>
                      </div>
                      <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                    </div>

                    <div class="customrange-section">
                      <h6 class="text-xs font-bold">Custom Range</h6>
                      <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                        <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                        <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                        <div class="date-range-input d-flex">
                          <div class="form-input">
                            <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                          </div>
                          <div class="form-input">
                            <label class="mb-0" for="">TO</label>
                            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                          </div>
                        </div>
                        <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                      </form>
                      <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                    </div>

                    <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                  </div>

                </li>
              </ul>
              <!---------------------- Search END -->
            </div>
            <div class="daybook-filter-list filter-list">
              <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn active waves-effect waves-light"><i class="fa fa-stream mr-2  active"></i>Visual Representation</a>
            </div>

            <div class="tab-content" id="custom-tabs-two-tabContent">
              <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">

                <?php
                //Graph View SQL

                $sql_list = "SELECT COUNT(so.so_number) AS num_of_orders FROM erp_branch_sales_order AS so WHERE so.company_id=$company_id AND so.branch_id=$branch_id AND so.so_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND so.status='active';";

                $queryset = queryGet($sql_list, true);
                // console($queryset);

                $sql_list_2 = "SELECT COUNT(DISTINCT(invoices.so_number)) AS num_of_invoices FROM erp_branch_sales_order AS so LEFT JOIN erp_branch_sales_order_invoices AS invoices ON so.so_number=invoices.so_number WHERE so.company_id=$company_id AND so.branch_id=$branch_id AND so.so_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND so.status='active' AND invoices.company_id=$company_id AND invoices.branch_id=$branch_id;";

                $queryset_2 = queryGet($sql_list_2, true);
                // console($queryset_2);

                $chartData = json_encode($queryset, true);
                $chartData2 = json_encode($queryset_2, true);

                $num_list = $queryset['numRows'];


                if ($num_list > 0) {
                  $i = 1;
                ?>

                  <div class="container-fluid mt-10">

                    <div class="row">
                      <div class="col-md-12 col-sm-12 d-flex">
                        <div class="card flex-fill reports-card">
                          <div class="card-body">
                            <div id="chartDivOrderToSales" class="chartContainer"></div>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>

                <?php } else { ?>
                  <p>No data Found</p>
                <?php } ?>
              </div>


            </div>
          </div>
        </div>
      </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.Content Wrapper. Graph View -->


<?php

require_once("../common/footer.php");
?>

<script>
  function table_settings_concised_view() {
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


  $(document).ready(function() {



    $('.select2')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal3">
    Add New
  </a></div>`);
      });
    //**************************************************************
    $('.select4')
      .select4()
      .on('select4:open', () => {
        $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
    Add New
  </a></div>`);
      });
  });
</script>

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

    $("#dataTable tfoot th").each(function() {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
    });

    // DataTable
    var columnSl = 0;
    var table = $("#dataTable").DataTable({
      dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
      buttons: ['copy', 'csv', 'excel', 'print'],
      "lengthMenu": [
        [1000, 5000, 10000, -1],
        [1000, 5000, 10000, 'All'],
      ],
      "scrollY": 200,
      "scrollX": true,
      "ordering": false,


      initComplete: function() {
        this.api()
          .columns()
          .every(function() {
            columnSl++;
            console.log(`columnSl=${columnSl}`);
            if (columnSl == 8 || columnSl == 10) {
              //For Dropdown column search
              /*var column = this;
              var select = $('<select class="form-control p-0"><option value="">All</option></select>')
                .appendTo($(column.footer()).empty())
                .on('change', function() {
                  var val = $.fn.dataTable.util.escapeRegex($(this).val());
                  console.log(val);
                  column.search(val ? '^' + val + '$' : '', true, false).draw();
                });

              column
                .data()
                .unique()
                .sort()
                .each(function(d, j) {
                  select.append('<option value="' + d + '">' + d + '</option>');
                });*/
            }
            if (columnSl == 4 || columnSl == 5) {
              var column = this;
              var select = $('<input type="text" class="form-control" placeholder="dd-mm-yyyy">')
                .appendTo($(column.footer()).empty());
            }
          });
      },
    });
    // Apply the search
    columnSl2 = 0;
    table.columns().every(function() {
      columnSl2++;
      if (columnSl2 == 4 || columnSl2 == 5) {
        var that = this;
        $('input', this.footer()).on('keyup change', function() {
          let searchVal = `${(this.value).split("-")[2]}-${(this.value).split("-")[1]}-${(this.value).split("-")[0]}`;
          that.search(searchVal).draw();
        });
      } else {
        var that = this;
        $('input', this.footer()).on('keyup change', function() {
          that.search(this.value).draw();
        });
      }
    });

  });
</script>

<script>
  var elem = document.getElementById("listTabPan");

  function openFullscreen() {
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) {
      /* Safari */
      elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
      /* IE11 */
      elem.msRequestFullscreen();
    }
  }
</script>

<script>
  $(function() {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left'
      },
      function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
      });
  });
</script>

<!-- CHANGES -->
<script>
  $(function() {
    $('input[name="daterange"]').daterangepicker({
      opens: 'left'
    }, function(start, end, label) {
      console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
  });
  $('#fYDropdown').change(function() {
    var title = $(this).val();
    if (title == "customrange") {
      $("#drop_val").val('customrange');
      $("#from_date").val('');
      $("#to_date").val('');
      $("#from_date").focus();
    } else {
      let start = $(this).find(':selected').data('start');
      let end = $(this).find(':selected').data('end');
      //alert(start);
      $("#from_date").val(start);
      $("#to_date").val(end);
      $("#drop_val").val('fYDropdown');
      $("#drop_id").val(title);
      $('#date_form').submit();
    }
  });

  $('#quickDropdown').change(function() {
    var days = $(this).val();
    var today = new Date();
    var seven_days_ago = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));

    var end = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
    var start = seven_days_ago.getFullYear() + '-' + ('0' + (seven_days_ago.getMonth() + 1)).slice(-2) + '-' + ('0' + seven_days_ago.getDate()).slice(-2);

    // alert(start);
    // alert(end);
    $("#from_date").val(start);
    $("#to_date").val(end);
    $("#drop_val").val('quickDrop');
    $("#drop_id").val(days);

    $('#date_form').submit();
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
</script>
<!-- CHANGES -->


<!-- CHART FUNCTION -->
<script>
  var chartData = <?php echo $chartData; ?>;
  var chartData2 = <?php echo $chartData2; ?>;

  am4core.ready(function() {

    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end

    // Create chart instance
    var chart = am4core.create("chartDivOrderToSales", am4charts.XYChart);
    chart.logo.disabled = true;

    // Add data
    chart.data = [{
      "category": "Orders",
      "value": Number(chartData.data[0].num_of_orders)
    }, {
      "category": "Invoices",
      "value": Number(chartData2.data[0].num_of_invoices)
    }];

    // Create axes

    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "category";
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.renderer.minGridDistance = 30;

    categoryAxis.renderer.labels.template.adapter.add("dy", function(dy, target) {
      if (target.dataItem && target.dataItem.index & 2 == 2) {
        return dy + 25;
      }
      return dy;
    });

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

    // Create series
    var series = chart.series.push(new am4charts.ColumnSeries());
    series.dataFields.valueY = "value";
    series.dataFields.categoryX = "category";
    series.name = "value";
    series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
    series.columns.template.fillOpacity = .8;

    var columnTemplate = series.columns.template;
    columnTemplate.strokeWidth = 2;
    columnTemplate.strokeOpacity = 1;

    series.columns.template.column.cornerRadiusTopLeft = 10;
    series.columns.template.column.cornerRadiusTopRight = 10;
    series.columns.template.column.fillOpacity = 0.8;

    // on hover, make corner radiuses bigger
    var hoverState = series.columns.template.column.states.create("hover");
    hoverState.properties.cornerRadiusTopLeft = 0;
    hoverState.properties.cornerRadiusTopRight = 0;
    hoverState.properties.fillOpacity = 1;

    series.columns.template.adapter.add("fill", function(fill, target) {
      return chart.colors.getIndex(target.dataItem.index);
    });

  });
</script>