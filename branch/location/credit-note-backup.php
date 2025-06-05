<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
require_once("../../app/v1/functions/branch/func-debit-credit-notes.php");

$company_data = getCompanyDataDetails($company_id);
$gl_account_length = $company_data['data']['gl_account_length'];

if (isset($_POST["createdata"])) {
  // console($_POST);
    $addNewObj = createCreditNote($_POST);
  // console($addNewObj);
  swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
}

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
</style>

<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
        <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Credit Note List</a></li>
        <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Credit Note</a></li>
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
              <h4>Create new credit note</h4>
            </div>
          </div>
          <div class="card-body">
            <div class="pgi-body">
              <div class="row function_row_main">
                <?php
                $getAllSoNumber = getAllSoNumber()['data'];
                $getAllPoNumber = getAllPoNumber()['data'];
                $getAllSoPoNumber = array_merge($getAllSoNumber, $getAllPoNumber);
                // console('getAllSoNumber***********');
                // console($getAllSoPoNumber);
                ?>
                <?php
                $chartOfAcc = getAllChartOfAccountsByconditionForMapping($company_id, true);
                if ($chartOfAcc['status'] == 'success') {
                  $list = '';
                  foreach ($chartOfAcc['data'] as $chart) {
                    $list .= '<option value="' . $chart['id'] . '">' . $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code'] . '</option>';
                  }
                }
                $function_id = rand(0000, 9999);
                ?>
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="form-input function-mapp-main">
                    <div class="row">
                      <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="">Document Date </label>
                        <input type="date" id="documentDate" name="documentDate" class="form-control" value="" required>
                      </div>
                      <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="">Document Number </label>
                        <input type="text" id="documentNo" name="documentNo" class="form-control" value="" required>
                      </div>
                      <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="">Posting Date </label>
                        <input type="date" id="postingDate" name="postingDate" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                      </div>
                      <div class="col-lg-4 col-md-6 col-sm-12">
                        <label for="">Refarence Code </label>
                        <!-- <input type="text" id="refarenceCode" name="refarenceCode" class="form-control" value="" required> -->
                        <select name="refarenceCode" class="form-control">
                          <option value="">Select One</option>
                          <?php
                          foreach ($getAllSoPoNumber as $key => $one) {
                          if($one['code'] !=""){
                          ?>
                          <!-- <input type="text" name="parent_slug" value="<?=$one['parent_slug']?>"> -->
                            <option value="<?= $one['code'] ?>|<?= $one['parent_id'] ?>|<?= $one['parent_slug'] ?>"><?= $one['code'] ?></option>
                          <?php } } ?>
                        </select>
                      </div>
                      <div class="col-lg-4 col-md-12 col-sm-12">
                        <label for="">For : </label>
                        <div class="sub-content">
                          <span>
                            <input type="radio" name="creditNoteReference" class="reff-type-radio select_payment_entry" value="Payment/Expenses" data-target="div-option-pay" checked="">
                            <label> Payment/Expenses</label>
                          </span>
                          <span>
                            <input type="radio" name="creditNoteReference" class="reff-type-radio select_payment_entry" value="Collection" data-target="div-option-collect" checked="">
                            <label> Collection</label>
                          </span>
                          <span>
                            <input type="radio" name="creditNoteReference" class="reff-type-radio select_payment_entry" value="Purchase" data-target="div-option-purchase">
                            <label> Purchase</label>
                          </span>
                          <span>
                            <input type="radio" name="creditNoteReference" class="reff-type-radio select_payment_entry" value="Production" data-target="div-option-production">
                            <label> Production</label>
                          </span>
                          <span>
                            <input type="radio" name="creditNoteReference" class="reff-type-radio select_payment_entry" value="Sales" data-target="div-option-sales">
                            <label>Sales</label>
                          </span>
                          <span>
                            <input type="radio" name="creditNoteReference" class="reff-type-radio select_payment_entry" value="Other" data-target="div-option-other">
                            <label>Other</label>
                          </span>
                        </div>
                      </div>

                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <label for="">Remark </label>
                        <textarea name="remark" id="remark" class="form-control" rows="4" required></textarea>
                      </div>

                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <hr>
                      </div>
                      <div class="col-lg-7 col-md-7 col-sm-7"> <label>Particular</label></div>
                      <div class="col-lg-2 col-md-2 col-sm-2"> <label>Debit Amount</label></div>
                      <div class="col-lg-2 col-md-2 col-sm-2"> <label>Credit Amount</label></div>
                      <div class="col-lg-1 col-md-1 col-sm-1"> <label></label></div>
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <hr>
                      </div>

                      <div class="col-lg-12 col-md-12 col-sm-12 d-inline-flex my-2">
                        <label for="" style="color: red;">Debit </label> &nbsp;&nbsp; 
                      </div>
                      <div class="col-12 debit-main">
                        <div class="row">
                          <div class="col-lg-7 col-md-7 col-sm-7">
                            <select id="debit_<?= rand(0000, 9999); ?>" name="journal[debit][gl][]" class="form-control" required>
                              <option value="">Select Debit G/L</option>
                              <?php echo $list; ?>
                            </select>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2">
                            <input step="0.01" type="number" id="dr_<?= rand(0000, 9999); ?>" name="journal[debit][amount][]" class="form-control dr-amount" value="" placeholder="Enter Amount" required>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2"></div>
                          <div class="col-lg-1 col-md-1 col-sm-1"><button value="<?= $function_id; ?>" type="button" class=" btn btn-primary add-debit">
                          <i class="fa fa-plus"></i>
                        </button></div>
                        </div>
                      </div>

                      <div class="col-lg-12 col-md-12 col-sm-12 d-inline-flex my-2">
                        <label for="" style="color: green;">Credit </label> &nbsp;&nbsp; 
                      </div>
                      <div class="col-12 credit-main">
                        <div class="row">
                          <div class="col-lg-7 col-md-7 col-sm-7">
                            <select id="credit_<?= rand(0000, 9999); ?>" name="journal[credit][gl][]" class="form-control" required>
                              <option value="">Select Credit G/L</option>
                              <?php echo $list; ?>
                            </select>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2"></div>
                          <div class="col-lg-2 col-md-2 col-sm-2">
                            <input step="0.01" type="number" id="cr_<?= rand(0000, 9999); ?>" name="journal[credit][amount][]" class="form-control cr-amount" value="" placeholder="Enter Amount" required>
                          </div>
                          <div class="col-lg-1 col-md-1 col-sm-1"><button type="button" value="<?= $function_id; ?>" class="btn btn-primary add-credit">
                          <i class="fa fa-plus"></i>
                        </button></div>
                        </div>
                      </div>
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <hr>
                      </div>
                      <div class="col-lg-7 col-md-7 col-sm-7"> <label>Total Amount</label></div>
                      <div class="col-lg-2 col-md-2 col-sm-2"> <label class="debit-total">0.00</label></div>
                      <div class="col-lg-2 col-md-2 col-sm-2"> <label class="credit-total">0.00</label></div>
                      <div class="col-lg-1 col-md-1 col-sm-1"> <label></label></div>
                      <div class="col-lg-12 col-md-12 col-sm-12">
                        <hr>
                      </div>
                    </div>
                  </div>
                </div>


              </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
              <button type="submit" name="createdata" id="createdata" class="btn btn-primary save-close-btn float-right waves-effect waves-light">Submit</button>
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
  $('#addNewJournalForm').on('submit', function() {
    let dtotal = 0;
    $(".dr-amount").each(function() {
      let velu = parseFloat($(this).val());
      if (velu > 0) {
        dtotal += parseFloat(velu);
      }
    });
    let ctotal = 0;
    $(".cr-amount").each(function() {
      let velu = parseFloat($(this).val());
      if (velu > 0) {
        ctotal += parseFloat(velu);
      }
    });

    if (dtotal != ctotal) {
      if (dtotal != ctotal) {
        let Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        });
        Toast.fire({
          icon: `warning`,
          title: `&nbsp;Debit and credit mismatch!`
        });
        return false;
      }
      return false;
    }
  });

  $(document).on("keyup keydown paste", '.dr-amount', function() {
    let valllAc = $(this).val();
    calculateDrAmount();
  });
  function calculateDrAmount(){
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

  $(document).on("keyup keydown paste", '.cr-amount', function() {
    let valllAc = $(this).val();
    calculateCrAmount();
  });
  function calculateCrAmount(){
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

  $(document).on("click", ".add-debit", function() {
    let function_id = $(this).val();
    let rand_no = Math.ceil(Math.random() * 100000);
    var bullet_point_html = `<div class="row"><div class="col-lg-7 col-md-7 col-sm-7">
                          <select id="debit_${rand_no}" name="journal[debit][gl][]" class="form-control" required>
                          <option value="">Select Debit G/L</option>
                           <?= $list; ?>
                          </select>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2">
                          <input step="0.01" type="number" id="dr_${rand_no}" name="journal[debit][amount][]" class="form-control dr-amount" value="" placeholder="Enter Amount" required>                                    
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2"></div>
                          <div class="col-lg-1 col-md-1 col-sm-1">
                          <button type="button" class="btn btn-danger delete_new_bullet_point">
                            <i class="fa fa-minus"></i>
                          </button>
                        </div></div>`;
    $('.debit-main').append(bullet_point_html);
  });

  $(document).on("click", ".add-credit", function() {
    let function_id = $(this).val();
    let rand_no = Math.ceil(Math.random() * 100000);
    var bullet_point_html = `<div class="row"><div class="col-lg-7 col-md-7 col-sm-7">
                          <select id="credit_${rand_no}" name="journal[credit][gl][]" class="form-control" required>
                          <option value="">Select Credit G/L</option>
                           <?= $list; ?>
                          </select>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-2"></div>
                          <div class="col-lg-2 col-md-2 col-sm-2">
                          <input step="0.01" type="number" id="cr_${rand_no}" name="journal[credit][amount][]" class="form-control cr-amount" value="" placeholder="Enter Amount" required>    
                          </div>
                          <div class="col-lg-1 col-md-1 col-sm-1">
                          <button type="button" class="btn btn-danger delete_new_bullet_point">
                            <i class="fa fa-minus"></i>
                          </button>
                        </div></div>`;
    $('.credit-main').append(bullet_point_html);
  });

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