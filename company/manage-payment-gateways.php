  <?php
  include("../app/v1/connection-company-admin.php");
  administratorAuth();
  include("common/header.php");
  include("common/navbar.php");
  include("common/sidebar.php");
  require_once("common/pagination.php");
  include("../app/v1/functions/company/func-credit-terms.php");

  if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusCreditTerms($_POST, "credit_terms_id", "credit_terms_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
  }

  if (isset($_POST["visit"])) {
    $newStatusObj = VisitCreditTerms($_POST);
    swalToast($newStatusObj["status"], $newStatusObj["message"], BRANCH_URL);
  }

  if (isset($_POST["createdata"])) {
    $addNewObj = createDataCreditTerms($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);

    // console($addNewObj);
    if ($addNewObj["status"] == "success") {
      swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
    } else {
      swalToast($addNewObj["status"], $addNewObj["message"]);
    }
  }

  if (isset($_POST["editdata"])) {
    console($_POST);
    //exit();
    $editDataObj = updateDataCreditTerms($_POST);
    swalToast($editDataObj["status"], $editDataObj["message"]);
  }

  if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
  } ?>
  <link rel="stylesheet" href="../public/assets/listing.css">


  <?php
  if (isset($_GET['create'])) {
  ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header mb-2 p-0  border-bottom">
        <?php if (isset($msg)) { ?>
          <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
            <?= $msg ?>
          </div>
        <?php } ?>
        <div class="container-fluid">
          <div class="row pt-2 pb-2">
            <div class="col-md-6">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Credit Terms</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Add Payment Gateway</a></li>
              </ol>
            </div>
            <div class="col-md-6" style="display: flex;">
              <button class="btn btn-danger btnstyle ml-2 add_data" value="add_draft">Save As Draft</button>
              <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_post"><i class="fa fa-plus fontSize"></i> Final Submit</button>
            </div>
          </div>
        </div>
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
            <input type="hidden" name="createPaymentData" id="createPaymentData" value="">
            <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
            <div class="row">
              <div class="col-md-8">
                <div id="accordion">
                  <div class="card card-primary">
                    <div class="card-header cardHeader">
                      <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseOne"> Basic Details </a> </h4>
                    </div>
                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <div class="input-group">
                              <input type="number" class="m-input" id="credit_terms_name" name="credit_terms_name">
                              <label>Credit Period(In Days)* </b></label>
                              <span class="error credit_terms_name"></span>
                            </div>
                          </div>
                          <div class="col-md-6 mb-3">
                            <div class="input-group">
                              <input type="text" name="credit_terms_desc" class="m-input" id="credit_terms_desc" value="">
                              <label>Description</label>
                              <span class="error credit_terms_desc"></span>
                            </div>
                          </div>


                        </div>
                      </div>
                    </div>
                  </div>


                </div>
              </div>
              <!---------------------------------------------------------------------------------------------->
              <div class="col-md-4">
                <div class="card card-primary card-outline card-tabs">
                  <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                      <li class="nav-item"> <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">TAB1</a> </li>
                      <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">TAB2</a> </li>
                      <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-messages-tab" data-toggle="pill" href="#custom-tabs-three-messages" role="tab" aria-controls="custom-tabs-three-messages" aria-selected="false">TAB3</a> </li>
                      <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-settings-tab" data-toggle="pill" href="#custom-tabs-three-settings" role="tab" aria-controls="custom-tabs-three-settings" aria-selected="false">TAB4</a> </li>
                    </ul>
                  </div>
                  <div class="card-body fontSize">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                      <div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab"> 90 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin malesuada lacus ullamcorper
                        dui
                        molestie, sit amet congue quam finibus. Etiam ultricies nunc non magna feugiat commodo. Etiam
                        odio
                        magna, mollis auctor felis vitae, ullamcorper ornare ligula. Proin pellentesque tincidunt nisi,
                        vitae ullamcorper felis aliquam id. Pellentesque habitant morbi tristique senectus et netus et
                        malesuada fames ac turpis egestas. Proin id orci eu lectus blandit suscipit. Phasellus porta,
                        ante
                        et varius ornare, sem enim sollicitudin eros, at commodo leo est vitae lacus. Etiam ut porta
                        sem.
                        Proin porttitor porta nisl, id tempor risus rhoncus quis. In in quam a nibh cursus pulvinar non
                        consequat neque. Mauris lacus elit, condimentum ac condimentum at, semper vitae lectus. Cras
                        lacinia erat eget sapien porta consectetur. </div>
                      <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab"> Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut
                        ligula
                        tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                        Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas
                        sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu
                        lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod
                        pellentesque diam. </div>
                      <div class="tab-pane fade" id="custom-tabs-three-messages" role="tabpanel" aria-labelledby="custom-tabs-three-messages-tab"> Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue
                        id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac
                        tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit
                        condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus
                        tristique.
                        Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est
                        libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id
                        fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna. </div>
                      <div class="tab-pane fade" id="custom-tabs-three-settings" role="tabpanel" aria-labelledby="custom-tabs-three-settings-tab"> Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis
                        ac,
                        ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi
                        euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum
                        placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam.
                        Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet
                        accumsan ex sit amet facilisis. </div>
                    </div>
                  </div>
                  <!-- /.card -->
                </div>
                <!--<div class="w-100 mt-3">
              <button type="submit" name="addInventoryItem" class="gradientBtn btn-success btn btn-block btn-sm"> <i class="fa fa-plus fontSize"></i> Add New </button>
            </div>-->
              </div>
            </div>
          </form>

        </div>
      </section>
      <!-- /.content -->
    </div>
  <?php
  } else if (isset($_GET['edit']) && $_GET["edit"] > 0) {
  ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header mb-2 p-0  border-bottom">
        <?php if (isset($msg)) { ?>
          <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
            <?= $msg ?>
          </div>
        <?php } ?>
        <div class="container-fluid">
          <div class="row pt-2 pb-2">
            <div class="col-md-6">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage CreditTerms</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Edit CreditTerms</a></li>
              </ol>
            </div>
            <div class="col-md-6" style="display: flex;">
              <a href="<?= basename($_SERVER['PHP_SELF']); ?>"><button class="btn btn-danger btnstyle ml-2">Back</button></a>
              <button class="btn btn-danger btnstyle ml-2 edit_data">Save As Draft</button>
              <button class="btn btn-primary btnstyle gradientBtn ml-2 edit_data"><i class="fa fa-plus fontSize"></i> Final Submit</button>
            </div>
          </div>
        </div>
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">

        </div>
      </section>
      <!-- /.content -->
    </div>
  <?php
  } else {
  ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- row -->
          <div class="row p-0 m-0">
            <div class="col-12 mt-2 p-0">
              <ul class="nav nav-tabs border-bottom-0" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn" data-toggle="modal" data-target="#"><i class="fa fa-plus"></i></a>
                </li>
              </ul>
              <div class="card card-tabs">

                <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

                  <div class="card-body">

                    <div class="row filter-serach-row">

                      <div class="col-lg-4 col-md-4 col-sm-12">

                        <h3 class="card-title mb-3 pl-3 font-bold pt-2 text-sm">Manage Payment Gateway</h3>

                        <!-- <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a> -->

                      </div>

                      <div class="col-lg-8 col-md-8 col-sm-12">

                        <div class="row table-header-item">

                          <div class="col-lg-11 col-md-11 col-sm-11">

                            <div class="section serach-input-section">



                              <input type="text" id="myInput" placeholder="" class="field form-control" />

                              <div class="icons-container">

                                <div class="icon-search">

                                  <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>

                                </div>

                                <div class="icon-close">

                                  <i class="fa fa-search po-list-icon" onclick="javascript:alert('Hello World!')" id="myBtn"></i>

                                  <script>
                                    var input = document.getElementById("myInput");

                                    input.addEventListener("keypress", function(event) {

                                      if (event.key === "Enter") {

                                        event.preventDefault();

                                        document.getElementById("myBtn").click();

                                      }

                                    });
                                  </script>

                                </div>

                              </div>

                            </div>

                          </div>

                          <div class="col-lg-1 col-md-1 col-sm-1">

                            <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn" data-toggle="modal" data-target="#createPaymentGateway"><i class="fa fa-plus"></i></a>

                          </div>

                        </div>



                      </div>

                    </div>

                  </div>

                </form>

                <div class="modal fade add-modal payment-gateway" id="createPaymentGateway" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                      <input type="hidden" name="createGatewaydata" id="createGatewaydata" value="">
                      <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

                      <div class="modal-content card">
                        <div class="modal-header card-header pt-2 pb-2 px-3">
                          <h4 class="text-xs text-white mb-0">Create Payment Gateway</h4>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                              <div class="form-input">
                                <label for="">Bank ID</label>
                                <input type="text" class="form-control" name="bankID" id="bankID">
                              </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                              <div class="form-input">
                                <label for="">Gateway Type</label>
                                <input type="text" class="form-control" name="gatewaytype" id="gatewaytype">
                              </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                              <div class="form-input">
                                <label for="">Access Token</label>
                                <input type="text" class="form-control" name="accessToken" id="accessToken">
                              </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                              <div class="form-input">
                                <label for="">Access Key</label>
                                <input type="text" class="form-control" name="accessKey" id="accessKey">
                              </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                              <div class="form-input">
                                <label for="">Url Type</label>
                                <select name="urlType" id="urlTypeid" class="form-control">
                                  <option value="urlDemo">Demo Url</option>
                                  <option value="urlLive">Live Url</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                              <div class="form-input">
                                <label for="">Environment Type</label>
                                <select name="environmentType" id="environmentTypeid" class="form-control">
                                  <option value="entDemo">Demo</option>
                                  <option value="entzlive">Live</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary" id="submitPayment"> Submit</button>
                        </div>
                      </div>

                    </form>
                  </div>
                </div>

                <div class="tab-content" id="custom-tabs-two-tabContent">
                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                    <?php
                    $cond = '';
                    $sts = " AND `credit_terms_status` !='deleted'";
                    if (isset($_REQUEST['credit_terms_status']) && $_REQUEST['credit_terms_status'] != '') {
                      $sts = ' AND credit_terms_status="' . $_REQUEST['credit_terms_status'] . '"';
                    }

                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                      $cond .= " AND credit_terms_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                    }

                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                      $cond .= " AND (`credit_terms_name` like '%" . $_REQUEST['keyword'] . "%' OR `credit_terms_desc` like '%" . $_REQUEST['keyword'] . "%')";
                    }

                    $sql_list = "SELECT pg.payment_gateway_id, pg.bank_id, pg.getway_type, pg.access_token, pg.access_key, pg.url_type, pg.environment, pg.status, pg.created_by, pg.updated_by, pg.created_at, pg.updated_at FROM `erp_payment_gateway`AS pg WHERE pg.branch_id = 1 AND pg.company_id = 1 AND pg.location_id = 1";
                    $qry_list = mysqli_query($dbCon, $sql_list);
                    $num_list = mysqli_num_rows($qry_list);

                    $countShow = "SELECT count(*) FROM `" . ERP_CREDIT_TERMS . "` WHERE 1 " . $cond . " AND company_id='" . $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . " ";
                    $countQry = mysqli_query($dbCon, $countShow);
                    $rowCount = mysqli_fetch_array($countQry);
                    $count = $rowCount[0];
                    $cnt = $GLOBALS['start'] + 1;
                    $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_CREDIT_TERMS", $_SESSION["logedCompanyAdminInfo"]["adminId"]);
                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                    $settingsCheckbox = unserialize($settingsCh);
                    if ($num_list > 0) {
                    ?>
                      <table id="mytable" class="table table-striped table-hover text-nowrap">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Bank ID</th>
                            <th>Gateway Type</th>
                            <th>Access Token</th>
                            <th>Access Key</th>
                            <th>URL Type</th>
                            <th>Environment</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Updated By</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody id="paymentTable">


                        </tbody>

                        <tfoot>
                          <tr>
                            <td colspan="9">
                              <!-- Start .pagination -->


                              <!-- End .pagination -->
                            </td>
                          </tr>
                        </tfoot>

                      </table>
                      <?php
                      if ($count > 0 && $count > $GLOBALS['show']) {
                      ?>
                        <div class="pagination align-right">
                          <?php pagination($count, "frm_opts"); ?>
                        </div>

                        <!-- End .pagination -->

                      <?php  } ?>
                    <?php } else { ?>
                      <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                        <thead>
                          <tr>
                            <td>

                            </td>
                          </tr>
                        </thead>
                      </table>
                  </div>
                <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <!-- /.row -->
    </div>
    </section>
    <!-- /.content -->
    </div>
    <!-- /.Content Wrapper. Contains page content -->
    <!-- For Pegination------->
    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
      <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                  echo  $_REQUEST['pageNo'];
                                                } ?>">
    </form>
    <!-- End Pegination from------->

  <?php
  }
  include("common/footer.php");
  ?>
  <script>
    $(document).ready(function() {

      function fetchPayment() {
        $.ajax({
          type: "POST",
          url: "ajaxs/paymentgateway/ajax-payment-gateway-list.php",
          data: {
            act: 'fetchPayment'
          },
          success: function(response) {
            // Handle and display the data
            $("#paymentTable").html(response);
          }
        });
      }

      // Fetch and display payment data on page load
      fetchPayment();

      $('#submitPayment').on('click', function(e) {
        e.preventDefault();
        let bankID = $('#bankID').val();
        let gatewaytype = $('#gatewaytype').val();
        let accessToken = $('#accessToken').val();
        let accessKey = $('#accessKey').val();
        let urltype = $('#urlTypeid').val();
        let environmentType = $('#environmentTypeid').val();
        // console.log(environmentType);

        // Adding AJAX
        $.ajax({
          type: "POST",
          url: "ajaxs/paymentgateway/ajax-payment-gateway.php",
          data: {
            act: 'addPayment',
            bankID,
            gatewaytype,
            accessToken,
            accessKey,
            urltype,
            environmentType
          },
          success: function(response) {
            let data = JSON.parse(response);
            console.log(response);
            let Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            });
            if (data.status == 'success') {
              console.log("data added successfully");

              $("#createPaymentGateway").hide();
              Toast.fire({
                icon: 'success',
                title: 'Data fetched successfully'
              });
            } else {
              Toast.fire({
                icon: 'warning',
                title: 'Data not found'
              });
            }
            // Fetch and display payment data after form submission
            fetchPayment();
          }
        });
      });
    });
  </script>

  <script>
    $(document).on("click", ".updatePaymentBtn", function() {

      let payment_gateway_id = $(this).data('id');
      // alert(payment_gateway_id);
      // console.log("button clicked. updatePayment...", payment_gateway_id);

      let bankID = $('#bankID_' + payment_gateway_id).val();
      alert(bankID);
      let gatewaytype = $('#gatewaytype_' + payment_gateway_id).val();
      let accessToken = $('#accessToken_' + payment_gateway_id).val();
      let accessKey = $('#accessKey_' + payment_gateway_id).val();
      let urltype = $('#urlTypeid_' + payment_gateway_id).val();
      let environmentType = $('#environmentTypeid_' + payment_gateway_id).val();
      let fldAdminCompanyId = $('#fldAdminCompanyId').val();

      // AJAX request
      $.ajax({
        type: "POST",
        url: "ajaxs/paymentgateway/ajax-payment-gateway.php",
        data: {
          act: 'updatePayment',
          payment_gateway_id,
          bankID,
          gatewaytype,
          accessToken,
          accessKey,
          urltype,
          environmentType
        },
        success: function(response) {
          let data = JSON.parse(response);
          console.log(response);

          let Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          });

          if (data.status == 'success') {
            console.log("data updated successfully");
            $(".edit-payment-gateway").hide();
            window.location.reload(600);

            Toast.fire({
              icon: 'success',
              title: 'Data updated successfully'
            });

            $('#bankID_' + payment_gateway_id).val(bankID);
            $('#gatewaytype_' + payment_gateway_id).val(gatewaytype);
            $('#accessToken_' + payment_gateway_id).val(accessToken);
            $('#accessKey_' + payment_gateway_id).val(accessKey);
            $('#urlTypeid_' + payment_gateway_id).val(urltype);
            $('#environmentTypeid_' + payment_gateway_id).val(environmentType);

            $('#bankID_' + payment_gateway_id).text(bankID);
            $('#gatewaytype_' + payment_gateway_id).text(gatewaytype);
            $('#accessToken_' + payment_gateway_id).text(accessToken);
            $('#accessKey_' + payment_gateway_id).text(accessKey);
            $('#urlTypeid_' + payment_gateway_id).text(urltype);
            $('#environmentTypeid_' + payment_gateway_id).text(environmentType);
          } else {
            Toast.fire({
              icon: 'warning',
              title: 'Data update failed'
            });
          }

          fetchPayment();
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.error('Error in AJAX request:', textStatus, errorThrown);
          Toast.fire({
            icon: 'error',
            title: 'AJAX request failed'
          });
        }
      });
    });
  </script>