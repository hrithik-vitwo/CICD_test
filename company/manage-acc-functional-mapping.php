<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/company/func-ChartOfAccounts.php");
include("../app/v1/functions/company/func-function-mapping.php");
include("../app/v1/functions/admin/func-company.php");

$company_id = $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"];
$company_data = getCompanyDataDetails($company_id);
$gl_account_length = $company_data['data']['gl_account_length'];

if (isset($_POST["updatefuncMappFormSubmitBtn"])) {
  // console($_POST);
  $addNewObj = updatefuncMappForm($_POST, $company_id);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}

?>
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../public/assets/listing.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
  .function-mapp-main {
    background: #ffffff;
    padding: 20px 20px 30px;
    border-radius: 12px;
    margin-bottom: 17px;
    box-shadow: rgb(0 0 0 / 24%) 0px 10px 20px, rgb(0 0 0 / 28%) 0px 6px 6px;
  }

  label.label-float {
    position: absolute;
    top: -10px;
    left: 16px;
    background: transparent;
    padding: 0px 4px;
    font-size: 9px !important;
  }

  .label-float::after {
    position: absolute;
    top: 20px;
    left: 20px;
    width: 200px;
    height: 1px;
    border-top: 1px solid #fff;
  }

  .credit-main,
  .debit-main {
    align-items: center;
    width: 100%;
    justify-content: space-between;
    margin: 6px 0;
  }

  .function-mapp-main .col {
    display: inline-flex;
  }

  .function-mapp-main .col:nth-child(1) {
    display: inline-block;
  }

  .credit-main label,
  .debit-main label {
    margin-bottom: 0;
  }

  .add-credit.relative-add-btn {
    padding: 13px !important;
    width: 20px;
    height: 20px;
  }

  .add-debit.relative-add-btn {
    padding: 13px !important;
    width: 20px;
    height: 20px;
  }

  .function-mapp-main row {
    gap: 8px;
  }

  table.acc-func-map tr td {
    vertical-align: top;
    padding: 25px;
  }

  .label-hidden {
    visibility: hidden;
  }

  table.acc-func-map tbody tr td:nth-child(2) .col:nth-child(n+1) {
    display: flex;
    gap: 5px;
  }

  table.acc-func-map tbody tr td:nth-child(3) .col:nth-child(n+1) {
    display: flex;
    gap: 5px;
  }
</style>
<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
        <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Functional Mapping List</a></li>
      </ol>

      <form action="" method="POST" id="addNewSOForm">

        <div class="card pgi-body-card">

          <div class="pgi-body">
            <div class="row function_row_main">
              <div class="col-lg-12 co-md-12 col-sm-12">
                <button type="button" class="btn btn-primary add_new_function ml-auto mt-3 mr-4 mb-3"><i class="fa fa-plus"></i></button>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12">
                <table class="table table-hover table-nowrap acc-func-map">
                  <thead>
                    <tr>
                      <th>Function Name</th>
                      <th>Debit Account</th>
                      <th>Credit Account</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $chartOfAcc = getAllChartOfAccountsByconditionForFunctionalMapping($company_id);
                    if ($chartOfAcc['status'] == 'success') {
                      $list = '';
                      foreach ($chartOfAcc['data'] as $chart) {
                        $list .= '<option value="' . $chart['id'] . '">' . $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code'] . '</option>';
                      }
                    }

                    $sql = "SELECT * FROM `" . ERP_ACC_FUNCTIONAL_MAPPING . "` WHERE `company_id`='" . $company_id . "' AND `map_status`='active'";
                    if ($res = queryGet($sql, true)) {
                      if ($res['numRows'] != 0) {
                        foreach ($res['data']  as $key => $value) {
                          $function_id = $value['map_id'];
                          $credit = unserialize($value['creditArray']);
                          $debit = unserialize($value['debitArray']);
                          // console($debit);
                          // console($credit);
                          // exit();
                    ?>



                          <tr>
                            <td>
                              <label class="slug">[ <?= $value['slug']; ?> ]</label>
                              <input type="hidden" id="function_id_<?= rand(0000, 9999); ?>" name="function[<?= $function_id; ?>][function_id]" class="form-control" value="<?= $function_id; ?>" required>
                              <input type="text" id="function_name_<?= rand(0000, 9999); ?>" name="function[<?= $function_id; ?>][function_name]" class="form-control" value="<?= $value['function_name']; ?>" required>
                            </td>
                            <td>

                              <?php foreach ($debit  as $dd => $debitval) { ?>
                                <div class="col-sm-12 p-0 mt-2 col">
                                  <label class="label-float">Label</label>
                                  <select name="function[<?= $function_id; ?>][debit][]" id="debit_<?= rand(0000, 9999); ?>" class="form-control debit-dropdown" required>
                                    <option value="">Select Debit G/L</option>
                                    <?php
                                    foreach ($chartOfAcc['data'] as $chart) { ?>
                                      <option value="<?= $chart['id'] ?>" <?php if ($chart['id'] == $debitval) {
                                                                            echo 'selected';
                                                                          } ?>><?= $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code']; ?></option>
                                    <?php }
                                    ?>
                                  </select>
                                  <?php if ($dd != 0) { ?>
                                    <button type="button" class="btn btn-danger delete_new_bullet_point">
                                      <i class="fa fa-minus"></i>
                                    </button>
                                  <?php } else { ?>
                                    <button value="<?= $function_id; ?>" type="button" class=" btn btn-primary add-debit mb-2 float-right">
                                      <i class="fa fa-plus"></i>
                                    </button>
                                  <?php } ?>
                                </div>
                              <?php } ?>
                            </td>
                            <td>

                              <?php foreach ($credit  as $cc => $creditval) { ?>
                                <div class="col-sm-12 p-0 mt-2 col">
                                  <label class="label-float">Label</label>
                                  <select name="function[<?= $function_id; ?>][credit][]" id="credit_<?= rand(0000, 9999); ?>" class="form-control w-100 debit-dropdown required">
                                    <option value="">Select Credit G/L</option>
                                    <?php
                                    foreach ($chartOfAcc['data'] as $chart) { ?>
                                      <option value="<?= $chart['id'] ?>" <?php if ($chart['id'] == $creditval) {
                                                                              echo 'selected';
                                                                            } ?>><?= $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code']; ?></option>
                                  <?php }
                                  ?>
                                  </select>
                                  <?php if ($cc != 0) { ?>
                                    <button type="button" class="btn btn-danger delete_new_bullet_point">
                                      <i class="fa fa-minus"></i>
                                    </button>
                                  <?php } else { ?>
                                    <button type="button" value="<?= $function_id; ?>" class="btn btn-primary add-credit mb-2 float-right">
                                      <i class="fa fa-plus"></i>
                                    </button>
                                  <?php } ?>
                                </div>
                              <?php } ?>
                            </td>
                          </tr>




                          <!-- <div class="col-lg-4 col-md-6 col-sm-12">
                          <div class="form-input function-mapp-main">
                            <div class="row">

                              <div class="col-sm-12">
                                <label for="">Function Name <small class="slug">[ <?= $value['slug']; ?> ]</small></label>
                                <input type="hidden" id="function_id_<?= rand(0000, 9999); ?>" name="function[<?= $function_id; ?>][function_id]" class="form-control" value="<?= $function_id; ?>" required>
                                <input type="text" id="function_name_<?= rand(0000, 9999); ?>" name="function[<?= $function_id; ?>][function_name]" class="form-control" value="<?= $value['function_name']; ?>" required>
                              </div>

                              <div class="col-sm-12">
                                <div class="credit-main d-inline-flex">
                                  <label for="" class="text-green text-sm">Credit</label>
                                  <button type="button" value="<?= $function_id; ?>" class=" btn-xs btn-primary add-credit relative-add-btn">
                                    <i class="fa fa-plus"></i>
                                  </button>
                                </div>
                                <?php foreach ($credit  as $cc => $creditval) { ?>

                                  <div class="col-sm-12 p-0 mt-2 col">
                                    <label for="" class="label-float">Label</label>
                                    <select name="function[<?= $function_id; ?>][credit][]" id="credit_<?= rand(0000, 9999); ?>" class="form-control w-100" required>
                                      <option value="">Select Credit G/L</option>
                                      <?php
                                      foreach ($chartOfAcc['data'] as $chart) { ?>
                                        <option value="<?= $chart['id'] ?>" <?php if ($chart['id'] == $creditval) {
                                                                              echo 'selected';
                                                                            } ?>><?= $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code']; ?></option>
                                      <?php }
                                      ?>
                                    </select>
                                    <?php if ($cc != 0) { ?>
                                      <button type="button" class="btn btn-danger delete_new_bullet_point">
                                        <i class="fa fa-minus"></i>
                                      </button>
                                    <?php } ?>
                                  </div>
                                <?php } ?>
                              </div>

                              <div class="col-sm-12">
                                <div class="debit-main d-inline-flex">
                                  <label for="" class="text-danger text-sm">Debit </label>
                                  <button value="<?= $function_id; ?>" type="button" class=" btn-xs btn-primary add-debit relative-add-btn">
                                    <i class="fa fa-plus"></i>
                                  </button>
                                </div>

                                <?php foreach ($debit  as $dd => $debitval) { ?>
                                  <div class="col-sm-12 p-0 mt-2 col">
                                    <label for="" class="label-float">Label</label>
                                    <select name="function[<?= $function_id; ?>][debit][]" id="debit_<?= rand(0000, 9999); ?>" class="form-control" required>
                                      <option value="">Select Debit G/L</option>
                                      <?php
                                      foreach ($chartOfAcc['data'] as $chart) { ?>
                                        <option value="<?= $chart['id'] ?>" <?php if ($chart['id'] == $debitval) {
                                                                              echo 'selected';
                                                                            } ?>><?= $chart['gl_label'] . '&nbsp;||&nbsp;' . $chart['gl_code']; ?></option>
                                      <?php }
                                      ?>
                                    </select>
                                    <?php if ($dd != 0) { ?>
                                      <button type="button" class="btn btn-danger delete_new_bullet_point">
                                        <i class="fa fa-minus"></i>
                                      </button>
                                    <?php } ?>
                                  </div>
                                <?php } ?>
                              </div>

                            </div>
                          </div>
                        </div> -->
                    <?php


                        }
                      }
                    } ?>

                  </tbody>

                </table>
              </div>
            </div>
          </div>
          <div class="col-lg-12 col-md-12 col-sm-12">
            <button type="submit" name="updatefuncMappFormSubmitBtn" class="btn btn-primary save-close-btn float-right waves-effect waves-light">Update</button>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>
<?php
include("common/footer.php");
?>
<script>
  $(document).ready(function() {
    $(".add_new_function").click(function() {
      let function_id = Math.ceil(Math.random() * 100000);
      var bullet_point_html = `<div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-input function-mapp-main">
                    <div class="row">
                      <div class="col-sm-12">
                        <label for="">Function Name</label>
                        <input type="hidden" id="function_id_${function_id}" name="function[${function_id}][function_id]" class="form-control" value="" >
                        <input type="text" id="function_name_${function_id}" name="function[${function_id}][function_name]" class="form-control" required>
                      </div>
                      <div class="col-sm-12">
                      <div class="credit-main d-inline-flex">
                       
                          <label class="text-green text-sm">Credit </label> &nbsp;&nbsp; <button type="button" value="${function_id}" class=" btn-xs btn-primary add-credit relative-add-btn">
                            <i class="fa fa-plus"></i>
                          </button>
                        </div>
                        <div class="col-sm-12 p-0 mt-2">
                        <label for="label" class="label-float">Label</label>
                          <select name="function[${function_id}][credit][]" id="credit_${function_id}" class="form-control w-100" required>
                          <option value="">Select Credit G/L</option>
                           <?= $list; ?>
                          </select>
                        </div>
                      </div>

                      <div class="col-sm-12">
                      <div class="debit-main d-inline-flex">
                          <label class="text-danger text-sm">Debit </label> &nbsp;&nbsp; <button value="${function_id}" type="button" class=" btn-xs btn-primary add-debit relative-add-btn">
                            <i class="fa fa-plus"></i>
                          </button>
                        </div>
                        <div class="col-sm-12 p-0 mt-2">
                        <label for="label" class="label-float">Label</label>
                          <select name="function[${function_id}][debit][]" id="debit_${function_id}" class="form-control" required>
                          <option value="">Select Debit G/L</option>
                           <?= $list; ?>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>`;
      $(".function_row_main").append(bullet_point_html);
    });

    $(document).on("click", ".add-debit", function() {
      let function_id = $(this).val();
      let rand_no = Math.ceil(Math.random() * 100000);
      var bullet_point_html = `<div class="col-sm-12 d-flex p-0 mt-2">
                          <select name="function[${function_id}][debit][]" id="debit_${rand_no}" class="form-control mr-2" required>
                          <option value="">Select Debit G/L</option>
                           <?= $list; ?>
                          </select>
                          <button type="button" class="btn btn-danger delete_new_bullet_point">
                            <i class="fa fa-minus"></i>
                          </button>
                          </div>`;
      $(this).parent().parent().append(bullet_point_html);
    });

    $(document).on("click", ".add-credit", function() {
      let function_id = $(this).val();
      let rand_no = Math.ceil(Math.random() * 100000);
      var bullet_point_html = `<div class="col-sm-12 d-flex p-0 mt-2">
                          <select name="function[${function_id}][credit][]" id="credit_${rand_no}" class="form-control mr-2" required>
                          <option value="">Select Credit G/L</option>
                           <?= $list; ?>
                          </select>
                          <button type="button" class="btn btn-danger delete_new_bullet_point">
                            <i class="fa fa-minus"></i>
                          </button>
                        </div>`;
      $(this).parent().parent().append(bullet_point_html);
    });

    $(document).on("click", ".delete_new_bullet_point", function() {
      $(this).parent().remove();
    });

  });
</script>

<script>
  function rm() {
    $(event.target).closest("<div class='row others-vendor'>").remove();
  }

  function addMultiQty(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row_${id}`).append(` <div class="col-lg-5 col-md-5 col-sm-12">
        <span class="has-float-label">
          <input type="label" name="OthersVendor[${addressRandNo}][name]" class="form-control" />
          <label for="v_email"></label>
        </span>
      </div>
                        <div class="col-lg-5 col-md-5 col-sm-12">
        <span class="has-float-label">
          <input type="label" name="OthersVendor[${addressRandNo}][email]" class="form-control" />
          <label for="v_email"></label>
        </span>
      </div>
      <div class="col-lg-2 col-md-2 text-center">
        <a class="btn btn-danger" type="button">
          <i class="fa fa-minus"></i></a>
      </div>`);
  }



  $(window).resize(function() {
    var $htmlOrBody = $('html, body'), // scrollTop works on <body> for some browsers, <html> for others
      scrollTopPadding = 8;
    // get input tag's offset top position
    var textareaTop = $(this).offset().top;
    // scroll to the textarea
    $htmlOrBody.scrollTop(textareaTop - scrollTopPadding);

    // OR  To add animation for smooth scrolling, use this. 
    //$htmlOrBody.animate({ scrollTop: textareaTop - scrollTopPadding }, 200);
  });
</script>