<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-open-close.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");

$company_data = getCompanyDataDetails($company_id);
$gl_account_length = $company_data['data']['gl_account_length'];
$opening_date = $company_data['data']['opening_date'];



if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

?>
<style>
  .content-wrapper table tr.debot-credit-tr td {
    font-size: 12px;
    text-align: left;
    color: #3b3b3b;
    vertical-align: middle;
    background: #f0f5fa;
    padding: 0px 15px;
    white-space: nowrap;
  }

  tbody.debit-credit-1 td {
    padding: 5px;
    border: none;
  }


  tbody.debit-credit-1 tr.debot-credit-tr td {
    background: #b5c5d3;
    text-align: center;
    padding: 0.25rem;
  }

  .green-text {
    color: #14ca14 !important;
    font-weight: 600;
  }

  .red-text {
    color: red !important;
    font-weight: 600;
  }

  p#warningAmountsub {
    position: relative;
    top: 20px;
  }

  .col-storage-location .select2-container {
    display: block;
  }


  .statement-section {
    padding: 10px 25px;
  }

  .statement-section .select-year select {
    max-width: 170px;
    background: #e8e8e8;
  }

  .statement-section .btns {
    text-align: right;
  }

  .row.state-head {
    margin-top: 30px;
  }

  .statement-section .intro-head {
    width: 258px;
  }

  .statement-section .intro-head h2 {
    font-size: 15px;
    font-weight: 600;
    border-bottom: 1px solid #d4d4d4;
    padding-bottom: 4px
  }

  .statement-section .state-head p {
    font-size: 11px !important;
  }

  #selectDebitSub {
    display: block;
  }

  .statement-section {
    padding: 10px 25px;
  }

  .statement-section .select-year select {
    max-width: 170px;
    background: #e8e8e8;
  }

  .statement-section .btns {
    text-align: right;
  }

  .row.state-head {
    margin-top: 30px;
  }

  .statement-section .intro-head {
    width: 258px;
  }

  .statement-section .intro-head h2 {
    font-size: 15px;
    font-weight: 600;
    border-bottom: 1px solid #d4d4d4;
    padding-bottom: 4px
  }

  .statement-section .state-head p {
    font-size: 11px !important;
  }

  .acc-summary .row .col-12:first-child p {
    font-weight: 600;
    background: #c5ced6;
  }

  .acc-summary .row .col-lg-12:nth-child(2) hr {
    margin: 0;
    padding: 0;
  }

  .acc-summary .row .display-flex-space-between {
    margin: 0;
    padding: 7px 15px;
  }

  .acc-summary .row .display-flex-space-between p:last-child {
    text-align: right !important;
  }

  .row.state-table {
    font-size: 11px !important;
    margin-top: 30px;
  }

  .state-col-th {
    background: #003060;
    color: #fff;
    padding: 7px 15px;
  }

  .state-col-td {
    color: #000;
    padding: 7px 15px;
    font-size: 10px;
  }

  .row.body-state-table:nth-child(odd) .state-col-td {
    background: #bdc5cd96;
  }

  .statement-section .btns button ion-icon.md.hydrated {
    position: relative;
    top: 2px;
    margin-right: 2px;
  }

  .ledger-view-table tbody tr td {
    background: #fff !important;
  }

  .ledger-view-table tbody tr:nth-child(even) td {
    background: #f4f4f496 !important;
  }

  .ledger-list-view {
    overflow-x: auto;
  }

  .ledger-tab .nav-link,
  .ledger-tab .nav-link:hover {
    color: #000;
  }

  .ledger-tab .nav-link.active {
    color: #fff;
    background: #003060;
    border-radius: 5px;
  }

  .ledger-tab .nav-link:not(.active):hover {
    color: #000;
  }

  .ledger-tab .nav-link.active,
  .ledger-tab .nav-link.active:hover {
    color: #fff;
    background: #003060;
    border-radius: 5px;
  }

  footer.main-footer.text-muted {
    display: none !important;
  }

  .is-subLeger-report .subledger-select span.select2.select2-container.select2-container--default {
    width: 47% !important;
  }

  .is-subLeger-report .ledger-select span.select2.select2-container.select2-container--default {
    width: 47% !important;
  }


  @media (max-width: 575px) {
    .ledger-list-view .ledger-view-table tr td {
      white-space: nowrap !important;
    }

  }
</style>

<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<?php 

if(isset($_GET['gl']) && isset($_GET['fromDate']) && isset($_GET['toDate'])){

  $gl = $_GET['gl'];
  $get_to_date = $_GET['toDate']; 
  $get_start_date = $_GET['fromDate'];
  ?>

<div class="content-wrapper is-subLeger-report">
  <section class="content">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
        <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create</a></li>
        <li class="back-button">
          <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
            <i class="fa fa-reply po-list-icon"></i>
          </a>
        </li>
      </ol>

      <div class="card pgi-body-card bg-white">
        <div class="card-header">
          <div class="head p-2">
            <h4>Ledger View Report</h4>
          </div>
        </div>
        <div class="card-body px-4">
          <div class="pgi-body">
            <div class="row function_row_main">
              <?php

              $chartOfAcc = getAllChartOfAccountsByconditionForMapping($company_id, true);

              // console($chartOfAcc);

              if ($chartOfAcc['status'] == 'success') {
                $list = '';
                foreach ($chartOfAcc['data'] as $chart) {
                  $list .= '<option value="' . $chart['id'] . '" data-attr="' . $chart['gl_label'] . '">' . $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code'] . '</option>';
                }
              }



              $subchartOfAcc = queryGet("SELECT customer_code AS code, trade_name AS name, parentGlId, 'Customer' AS type
              FROM erp_customer
              UNION ALL
              SELECT vendor_code AS code, trade_name AS name, parentGlId, 'Vendor' AS type
              FROM erp_vendor_details
              UNION ALL
              SELECT itemCode AS code, itemName AS name, parentGlId, 'Item' AS type
              FROM erp_inventory_items
              UNION ALL
              SELECT sl_code AS code, sl_name AS name, parentGlId, 'SubGL' AS type
              FROM erp_extra_sub_ledger", true);
              // console($chartOfAcc);

              // console($chartOfAcc);

              if ($subchartOfAcc['status'] == 'success') {
                $list = '';
                foreach ($subchartOfAcc['data'] as $subchart) {

                  $list .= '<option value="' . $subchart['code'] . '" data-attr="' . $subchart['parentGlId'] . '">' . $subchart['name'] . '&nbsp;||&nbsp;' . $subchart['code'] . '</option>';
                }
              }

              $function_id = rand(0000, 9999);

              ?>
              <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="form-input function-mapp-main">


                  <div class="row">

                    <div class="col-lg-4 col-md-4 col-sm-4">
                      <div class="d-flex align-items-center gap-4 text-nowrap ledger-select">
                        <label for="" class="mb-0">Select a Ledger</label>
                        <select id="ledger_<?= rand(0000, 9999); ?>" name="gl" class="form-control select2 ledger" required>
                          <option value="">Select G/L</option>
                          <?php if ($chartOfAcc['status'] == 'success') {
                            foreach ($chartOfAcc['data'] as $chart) {

                          ?>
                              <option value=<?= $chart['id'] ?> data-attr='<?= $chart['gl_label'] ?>' <?php if ($chart['id'] == $gl) {
                                                                                                          echo 'selected';
                                                                                                        } ?>><?= $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code'] ?></option>
                          <?php
                            }
                          }
                          ?>
                        </select>
                      </div>
                    </div>


                    <div class="col-lg-4 col-md-4 col-sm-4" id="subLedger_div" style="display:none" ;>
                      <div class="d-flex align-items-center gap-4 text-nowrap subledger-select">
                        <label for="" class="mb-0">Select a Sub Ledger</label>
                        <select id="subLedgerList debit_<?= rand(0000, 9999); ?>" name="gl" class="form-control select2 selectDebitSub" required>
                          <option value="">Select Sub Ledger</option>
                          <?php if ($subchartOfAcc['status'] == 'success') {
                            foreach ($subchartOfAcc['data'] as $subchart) {

                          ?>
                              <option value=<?= $subchart['code'] ?> data-parent=<?= $subchart['parentGlId'] ?>><?= $subchart['name'] . '&nbsp;||&nbsp;' . $subchart['code'] ?></option>
                          <?php
                            }
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 text-right">
                      <div class="custom-date-filter d-flex justify-content-end align-items-center gap-4 text-nowrap">
                        <label for="" class="mb-0">Select Date</label>
                        <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="">
                        <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange">
                        <div class="date-range-input d-flex">
                          <div class="form-input">
                            <input type="date" class="form-control" name="from_date" id="from_date" required="" value="<?php echo $get_start_date; ?>">
                          </div>
                          <div class="form-input gap-0">
                            <label class="mb-0 mx-2" for="">To</label>
                            <input type="date" class="form-control" name="to_date" id="to_date" required="" value="<?php echo $get_to_date; ?>">
                          </div>
                          <button type="submit" class="btn btn-primary float-right ml-3" id="rangeid" name="add_date_form">Apply</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="ledger-view"></div>
        </div>



      </div>

    </div>
  </section>
</div>
<?php

}


?>


<?php

require_once("../common/footer.php");
?>
<script>

var selectedOption = <?= $gl ?>;
//  alert(selectedOption);
let start_date = "<?= $get_start_date ?>";
let to_date = "<?= $get_to_date ?>";
  get_ledger_report(selectedOption, start_date, to_date);
  subledger_list(selectedOption);

  // $(document).on("change", '.selectDebitSub', function() {


  //   var dataAttrValue = $(this).find('option:selected').data('attr');
  //   console.log(dataAttrValue);
  //   let valllAc = $(this).val();
  //   // alert(valllAc);

  //   $.ajax({
  //     type: "GET",
  //     url: `<?= LOCATION_URL ?>ajaxs/ledger/ajax-gl-value.php?gl=${valllAc}`,
  //     beforeSend: function() {
  //       // $('.credit-sub').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
  //     },
  //     success: function(response) {
  //       //console.log(11111111);
  //       console.log(response);
  //       $('.ledger-view').html(response);
  //     }
  //   });
  // });

  $('.selectDebitSub')

    .select2()

    .on('select2:open', () => {


    });

  $('.ledger')

    .select2()

    .on('select2:open', () => {


    });


  $(".ledger").on("change", function() {
    // alert(1);
    var selectedOption = $(this).find("option:selected").val();
    //  alert(selectedOption);
    var start_date = $("#from_date").val();
    var to_date = $("#to_date").val();


    subledger_list(selectedOption);
    get_ledger_report(selectedOption, start_date, to_date)

  });

  function subledger_list(selectedOption){

    $.ajax({
      type: "POST",
      url: `<?= LOCATION_URL ?>ajaxs/ledger/ajax-subledger-list.php`,
      data: {
        selectedOption

      },
      beforeSend: function() {
        // $('.credit-sub').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
      },
      success: function(response) {
        // console.log(response);
        /// alert('ok');
        // alert(response);
        var obj = JSON.parse(response);
        //  alert(obj['list']);
        //  alert(obj['numRows']);
        if (obj['numRows'] > 0) {
          $("#subLedger_div").show();
          $('.selectDebitSub').html(obj['list']);
        } else {
          $("#subLedger_div").hide();
          $('.selectDebitSub').html();
        }
      }
    });

  }



  $(".selectDebitSub").on("change", function() {
    // alert(1);
    var code = $(this).val();
    var start_date = $("#from_date").val();
    var to_date = $("#to_date").val();
    var selectedOption = $(this).find("option:selected");

    // Retrieve the value of data-parent attribute


    var parentgl = selectedOption.data("parent");
    // alert(parentgl);


    get_sub_ledger_report(code, start_date, to_date, parentgl)
    //alert(gl);
  });

  $(document).on("click", '#rangeid', function() {

    //alert(1);
    var code = $(".selectDebitSub").val() ?? '0';
    var start_date = $("#from_date").val();
    var to_date = $("#to_date").val();

    var selectedOption = $('.selectDebitSub').find("option:selected");

    // Retrieve the value of data-parent attribute
    var gl = $('.ledger').val();

    var parentgl = selectedOption.data("parent") ?? '0';
    //  alert(parentgl);
    // alert(code);
    // alert(gl);
    // alert(parentgl);
    if (code != 0 || parentgl == gl) {
      //  alert(0);
      get_sub_ledger_report(code, start_date, to_date, parentgl)
    } else {
      // alert(1);
      get_ledger_report(gl, start_date, to_date) 
    }




  });

  function get_ledger_report(gl, start_date, to_date) {
   // alert(1);
    $.ajax({
      type: "POST",
      url: `<?= LOCATION_URL ?>ajaxs/ledger/ajax-gl-value.php`,
      data: {
        start_date,
        to_date,
        gl

      },
      beforeSend: function() {
        // $('.credit-sub').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
      },
      success: function(response) {
        //console.log(11111111);
        console.log(response);
        $('.ledger-view').html(response);
      }
    });

  }



  function get_sub_ledger_report(code, start_date, to_date, parentgl) {
    $.ajax({
      type: "POST",
      url: `<?= LOCATION_URL ?>ajaxs/ledger/ajax-subgl-value.php`,
      data: {
        start_date,
        to_date,
        code,
        parentgl

      },
      beforeSend: function() {
        // $('.credit-sub').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
      },
      success: function(response) {
        //console.log(11111111);
        console.log(response);
        $('.ledger-view').html(response);
      }
    });

  }




</script>

<script>
  function exportToExcel() {
    // Select the table element containing the ledger report
    var table = document.querySelector('.ledger-view-table');

    // Convert the table to a workbook
    var wb = XLSX.utils.table_to_book(table, {
      sheet: "Ledger Report"
    });

    // Save the workbook as an Excel file
    XLSX.writeFile(wb, 'ledger_report.xlsx');
  }
</script>
<script>
  function exportToExcelMonth() {
    // Select the table element containing the ledger report
    var table = document.querySelector('.ledger-view-table-month');

    // Convert the table to a workbook
    var wb = XLSX.utils.table_to_book(table, {
      sheet: "Month on Month Ledger Report"
    });

    // Save the workbook as an Excel file
    XLSX.writeFile(wb, 'ledger_report_month_on_month.xlsx');
  }
</script>

<script>
    $(document).on("change", "#from_date", function() {
        var fromDate = new Date($(this).val());
        var toDateInput = $('#to_date');

        // Set the minimum date for the "To Date" field
        toDateInput.prop('min', $(this).val());

        // Reset the value of "To Date" if it's invalid
        var toDate = new Date(toDateInput.val());
        if (toDate < fromDate) {
            toDateInput.val('');
        }

        // Enable or disable "To Date" field based on the selection of "From Date"
        if ($(this).val() !== '') {
            toDateInput.prop('disabled', false);
        } else {
            toDateInput.prop('disabled', true);
        }
    });


    $(document).on("change", "#to_date", function() {
        var fromDateInput = $('#from_date');
        var toDate = new Date($(this).val()); 
        fromDateInput.prop('max', $(this).val());

        var fromDate = new Date(fromDateInput.val());
        if (toDate < fromDate) {
        
            fromDateInput.val('');
        }
       

         // Enable or disable "To Date" field based on the selection of "From Date"
         if ($(this).val() !== '') {
            fromDateInput.prop('disabled', false);
        } else {
            fromDateInput.prop('disabled', true);
        }
    });
</script>