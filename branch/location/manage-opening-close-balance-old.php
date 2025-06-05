<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-open-close.php"); //somdutta
require_once("../../app/v1/functions/branch/func-opening-closing-balance-controller.php"); //New controller
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

  body .select2-container {
    width: 100% !important;
  }


  body span.select2-container--default .select2-selection--single {
    height: 32px !important;
  }
</style>

<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
        <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>List</a></li>
        <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create</a></li>
        <li class="back-button">
          <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
            <i class="fa fa-reply po-list-icon"></i>
          </a>
        </li>
      </ol>
      <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" id="addNewJournalForm">
        <div class="card pgi-body-card">
          <div class="card-header">
            <div class="head p-2">
              <h4>Create new</h4>
            </div>
          </div>
          <div class="card-body">
            <div class="pgi-body">
              <div class="row function_row_main">
                <?php
                if (isset($_POST['createdata'])) {
                  $subGlString = $_POST["subgl"] ?? "";
                  $subGl = (explode("|", $subGlString)[0] ?? "");
                  $data = [
                    [
                      "postinDate" => $_POST["documentDate"] ?? "",
                      "gl" => $_POST["gl"] ?? 0,
                      "subgl" => $subGl,
                      "quantity" => $_POST["quantity"] ?? 0,
                      "rate" => $_POST["rate"] ?? 0,
                      "amount" => $_POST["amount"] ?? 0,
                      "storageLocation" => $_POST["sl"] ?? 0,
                    ]
                  ];
                  
                  $openingClosingBalanceObj = new OpeningClosingBalance();
                  $saveObj = $openingClosingBalanceObj->saveOpeningBalance($data);
                  swalToast($saveObj["status"], $saveObj["message"], $_SERVER['PHP_SELF']);
                }

                $chartOfAcc = getAllChartOfAccountsByconditionForMapping($company_id, true);

                if ($chartOfAcc['status'] == 'success') {
                  $list = '';
                  foreach ($chartOfAcc['data'] as $chart) {
                    $list .= '<option value="' . $chart['id'] . '" data-attr="' . $chart['gl_label'] . '">' . $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code'] . '</option>';
                  }
                }
                ?>
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="form-input function-mapp-main">
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <?php
                        if ($opening_date != NULL || !empty($opening_date)) {
                        ?>
                          <label for="" class="font-bold text-sm mb-4">Document Date : <?= $opening_date ?> &nbsp; [ This is the company opening date. You can not post before or beyond this date. ]</label>
                        <?php
                        } else {
                        ?>
                          <label for="" class="font-bold text-sm mb-4" style="color:red;">No opening date found. Kindly go to company settings and update opening date.</label>
                        <?php
                        }
                        ?>
                        <input type="hidden" id="documentDate" name="documentDate" class="form-control" value="<?= $opening_date ?>" required>
                      </div>



                      <div class="col-lg-12 col-md-12 col-sm-12 debit-main">
                        <div class="row mb-3">
                          <div class="col-lg-2 col-md-2 col-sm-2">
                            <label for="">Select a ledger</label>
                          </div>
                          <div class="col-lg-4 col-md-4 col-sm-4">
                            <select id="debit_<?= rand(0000, 9999); ?>" name="gl" class="form-control select2 selectDebitSub" required>
                              <option value="">Select G/L</option>
                              <?php if ($chartOfAcc['status'] == 'success') {
                                foreach ($chartOfAcc['data'] as $chart) {
                              ?>
                                  <option value=<?= $chart['id'] ?> data-attr='<?= $chart['gl_label'] ?>'><?= $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code'] ?></option>
                              <?php
                                }
                              }
                              ?>
                            </select>
                            <p id="warningLedger" class="text-success text-xs font-bold pt-2"></p>
                          </div>
                          <div class="col-lg-6 col-md-6 col-sm-6">
                            <span class="debit-sub" style="display:none;">
                              <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 text-right">
                                  <label>Select sub ledger</label>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                  <select id="debit_select" name="subgl" class="form-control select2 subglclass">
                                    <option value="">--Select--</option>';
                                  </select>
                                </div>
                              </div>
                            </span>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="form-input">
                              <label for="">Opening Quantity</label>
                              <input type="text" id="dr_quan" name="quantity" class="form-control" value="" placeholder="Enter Quantity" disabled>
                            </div>
                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3" id="rate" style="display:none;">
                            <div class="form-input">
                              <label for="">Rate</label>
                              <input type="text" id="dr_rate" name="rate" class="form-control" value="" placeholder="Enter Rate" disabled>
                            </div>
                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">
                            <div class="form-input">
                              <label for="">Opening Balance</label>
                              <input type="text" id="dr_amount" name="amount" class="form-control dr-amount" value="" placeholder="Enter Amount" step="any" required>
                            </div>
                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3 col-storage-location" id="sl" style="display:none;">
                            <div class="form-input">
                              <label for="" class="label-hidden">Storage Location</label>
                              <select name="sl" class="select2 form-control" id="dr_sl" disabled>
                                <option value="">Storage Location</option>
                                <option value="rmWhOpen">RM Open</option>
                                <option value="rmProdOpen">RM Production Open</option>
                                <option value="sfgStockOpen">SFG Open</option>
                                <option value="fgWhOpen">FG Open</option>
                                <option value="fgMktOpen">FG Market Open</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-lg-6 col-md-6 col-sm-6">
                            <p id="warningAmount" class="text-primary text-sm font-bold"></p>
                            <p id="warningAmountsub" class="text-primary text-sm font-bold"></p>
                          </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
              <?php
              if ($opening_date != NULL || !empty($opening_date)) {
              ?>
                <button type="submit" name="createdata" id="createdata" class="btn btn-primary save-close-btn float-right waves-effect waves-light">Submit</button>
              <?php
              } else {
              ?>
                <button class="btn btn-primary save-close-btn float-right waves-effect waves-light" disabled>Submit</button>
              <?php
              }
              ?>
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
<?php
require_once("../common/footer.php");
?>
<script>
  // Subgl Finding Methods
  $(document).on("change", '.selectDebitSub', function() {
    console.log("Subgl Finding");
    $("#warningAmount").html('');
    $("#warningAmountsub").html('');
    $(".dr-amount").val(0);
    $(".debit-sub").hide();
    var dataAttrValue = $(this).find('option:selected').data('attr');
    console.log(dataAttrValue);
    let valllAc = $(this).val();
    // alert(valllAc);
    if (dataAttrValue == "RM Inventory" || dataAttrValue == "FG Inventory" || dataAttrValue == "SFG Inventory") {
      $("#sl").show();
      $("#rate").show();
      document.getElementById("dr_quan").disabled = false;
      document.getElementById("dr_rate").disabled = false;
      document.getElementById("dr_amount").readOnly = true;

    } else {
      $("#sl").hide();
      $("#rate").hide();

      document.getElementById("dr_quan").disabled = true;
      document.getElementById("dr_rate").disabled = true;
      document.getElementById("dr_amount").readOnly = false;
    }
    $.ajax({
      type: "GET",
      url: `<?= LOCATION_URL ?>ajaxs/ajax-gl-value.php?gl=${valllAc}`,
      beforeSend: function() {
        $('.credit-sub').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
      },
      success: function(response) {
        var obj = jQuery.parseJSON(response);
        $("#warningLedger").html(obj['text']);
        $(".dr-amount").keyup(function() {

          let amount = Number($(this).val());
          let prev = Number(obj['amount']);
          $diff = prev + amount;
          $("#warningAmount").html(`Your opening balance will be Rs ${$diff}`);
        });

        $('#debit_select').html("");
        //  alert(valllAc);
        let mappArray = '<?= getAllfetchAccountingMappingArray($company_id) ?>';
        let mappfinalArray = jQuery.parseJSON(mappArray);
        console.log(`${valllAc} exists in the array.`);
        $.ajax({
          type: "GET",
          url: `<?= LOCATION_URL ?>ajaxs/ajax-open-closing-subgl.php?gl=${valllAc}&type=debit`,
          beforeSend: function() {
            // $('.debit-sub').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          },
          success: function(response) {
            $('#debit_select').html("");
            // console.log(response);
            var obj = jQuery.parseJSON(response);
            console.log(response);
            if (obj['status'] == 'success') {
              $(".debit-sub").show();
              $("#debit_select").html(obj['makingVal']);
            }
            //console.log(responseObj);
          }
        });
      }
    });
  });

  $(document).on("change", '.subglclass', function() {
    $("#warningLedger").html('');
    $("#warningAmount").html('');
    $("#warningAmountsub").html('');
    $(".dr-amount").val(0);
    let val = $(this).val().split('|')[0];
    if ($(this).val != " ") {
      document.getElementById("dr_quan").disabled = false;
      document.getElementById("dr_sl").disabled = false;
      document.getElementById("dr_rate").disabled = false;
    } else {
      document.getElementById("dr_quan").disabled = false;
      document.getElementById("dr_sl").disabled = true;
      document.getElementById("dr_rate").disabled = true;
    }
    //  alert(val);
    $.ajax({
      type: "GET",
      url: `<?= LOCATION_URL ?>ajaxs/ajax-gl-value.php?subgl=${val}`,
      beforeSend: function() {
        // $('.credit-sub').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
      },
      success: function(response) {
        console.log(response);
        var obj = jQuery.parseJSON(response);
        $("#warningSubLedger").html(obj['text']);
        $(".dr-amount").on("keyup keydown paste change", function() {
          // alert(1);
          let amount = Number($(this).val());
          let prev_sub = Number(obj['amount']);
          let new_sub = prev_sub + amount;
          $("#warningAmount").html('');
          $("#warningAmountsub").html(`Your opening balance will be <span class="text-lg font-bold pt-2" style="font-family: cursive"> ₹${new_sub}</span>`);
        });

        $("#dr_rate").keyup(function() {
          let qty = $("#dr_quan").val();
          let rate = $(this).val();
          let val = qty * rate;
          $("#dr_amount").val(val);
          let prev_sub = Number(obj['amount']);
          let new_sub = prev_sub + val;
          $("#warningAmount").html('');
          $("#warningAmountsub").html(`Your opening balance will be <span class="text-lg font-bold pt-2" style="font-family: cursive"> ₹${new_sub}</span>`);
        });


        $("#dr_quan").keyup(function() {
          let qty = $(this).val();
          let rate = $("#dr_rate").val();
          let val = qty * rate;
          $("#dr_amount").val(val);
          let prev_sub = Number(obj['amount']);
          let new_sub = prev_sub + val;
          $("#warningAmount").html('');
          $("#warningAmountsub").html(`Your opening balance will be <span class="text-lg font-bold pt-2" style="font-family: cursive"> ₹${new_sub}</span>`);
        });
      }
    });
  });

  $(document).on("change", '.selectCreditSub', function() {
    let valllAc = $(this).val();
    // alert(valllAc);
    $.ajax({
      type: "GET",
      url: `<?= LOCATION_URL ?>ajaxs/ajax-gl-value.php?gl=${valllAc}`,
      beforeSend: function() {
        $('.credit-sub').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
      },
      success: function(response) {
        // alert(1);
        console.log(response);
        $('.credit-sub').html("");
        //  alert(valllAc);
        let mappArray = '<?= getAllfetchAccountingMappingArray($company_id) ?>';
        let mappfinalArray = jQuery.parseJSON(mappArray);
        //  console.log(mappfinalArray['data']);
        if (mappfinalArray['status'] == 'success') {

          if ($.inArray(valllAc, Object.values(mappfinalArray['data'])) !== -1) {
            //console.log(valllAc + "exists in the array.");
            $.ajax({
              type: "GET",
              url: `<?= LOCATION_URL ?>ajaxs/ajax-subgl-list.php?gl=${valllAc}&type=credit`,
              beforeSend: function() {
                $('.credit-sub').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
              },
              success: function(response) {
                // $('.credit-sub').html("");
                var obj = jQuery.parseJSON(response);
                //  alert(obj);
                $(".credit-sub").html(obj['makingVal']);
                $(".dr-amount").html(obj['warning']);
                //console.log(responseObj);
              }
            });
          } else {
            //console.log(valllAc + " does not exist in the array.");
            $('.credit-sub').html("");
          }
        } else {
          // console.log(valllAc + " does not exist in the array.");
          $('.credit-sub').html("");
        }
      }
    });
  });


  $("#dr_rate").keyup(function() {
    let qty = $("#dr_quan").val();
    let rate = $(this).val();
    let val = qty * rate;
    $("#dr_amount").val(val);
  });


  $("#dr_quan").keyup(function() {
    let qty = $(this).val();
    let rate = $("#dr_rate").val();
    let val = qty * rate;
    $("#dr_amount").val(val);
  });

  //////////////********************//////////////////


  $(document).on("keyup keydown paste", '.cr-amount', function() {
    let valllAc = $(this).val();
    calculateCrAmount();
  });

  function calculateCrAmount() {
    let sum = 0;
    $(".cr-amount").each(function() {
      let velu = parseFloat($(this).val());
      if (velu > 0) {
        sum += parseFloat(velu);
      }
    });
    sum = sum.toFixed(2);
    $('.credit-total').html(sum);
  }

  $(document).on("keyup keydown paste change", '.dr-amount', function() {
    let valllAc = $(this).val();
    calculateDrAmount();
  });

  function calculateDrAmount() {
    let sum = 0;
    $(".dr-amount").each(function() {
      let velu = parseFloat($(this).val());
      if (velu > 0) {
        sum += parseFloat(velu);
      }
    });
    sum = sum.toFixed(2);
    $('.debit-total').html(sum);
  }



  $(document).on("click", ".delete_new_bullet_point", function() {
    $(this).parent().parent().remove();
    calculateDrAmount();
    calculateCrAmount();
  });

  function srch_frm() {
    if ($('#form_date_s').val().trim() != '' && $('#to_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
      //$("#phone_r_err").html("Your Phone Number");
      alert("Enter To Date");
      $('#to_date_s').focus();
      return false;
    }
    if ($('#to_date_s').val().trim() != '' && $('#form_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
      //$("#phone_r_err").html("Your Phone Number");
      alert("Enter From Date");
      $('#form_date_s').focus();
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
        $(".select2-results:not(:has(a))");
        setTimeout(function() {
          $('.select2-search__field').focus();
        }, 0);
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




  $(".subglclass").select2();
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