<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/company/func-branches.php");

$companyCountry = ($_SESSION['logedCompanyAdminInfo']['companyCountry']);
$lables = json_decode(getLebels($companyCountry)['data'], true);
$const = ($lables['constitution_of_business']);
$abn = $lables['fields']['taxidNumber'];
$tfn = $lables['fields']['taxNumber'];






if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"],);
}
if (isset($_POST["visit"])) {
  $newStatusObj = VisitBranches($_POST);
  if ($newStatusObj["status"] == "success") {
    redirect(BRANCH_URL);
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
  } else {
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
  }
}


if (isset($_POST["createdata"])) {
  $addNewObj = createDataBranches($_POST);
  // console($addNewObj);
  if ($addNewObj["status"] == "success") {
    swalAlert($addNewObj["status"], 'Great!', $addNewObj["message"], $_SERVER['PHP_SELF']);
  } else {
    swalToast($addNewObj["status"], $addNewObj["message"]);
  }
}

if (isset($_POST["editdata"])) {
  //console($_POST);
  $editNewObj = updateDataBranches($_POST);
  if ($editNewObj["status"] == "success") {
    redirect($_SERVER['PHP_SELF']);
    swalToast($editNewObj["status"], $editNewObj["message"]);
  } else {
    swalToast($editNewObj["status"], $editNewObj["message"]);
  }
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>
<style>
  .legal-details .form-input {
    margin: 15px 0;
    justify-content: space-between;
  }

  .legal-details .form-input .d-flex {
    align-items: cEnter;
    gap: 7px;
  }

  .blur {
    filter: blur(1.5px);
  }

  .row.brances-create .card {
    min-height: 100%;
  }

  .row.brances-create .col .card .card-body .form-input {
    margin: 10px 0;
  }

  span.error.branch_gstin {
    top: 112px;
  }

  .branch-modal .modal-header {
    height: 320px !important;
  }
</style>
<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">




<?php
if (isset($_GET['create'])) {
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="container-fluid">

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

          <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Branches</a></li>

          <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add Branch</a></li>

          <li class="back-button">

            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

              <i class="fa fa-reply po-list-icon"></i>

            </a>

          </li>

        </ol>

      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
          <input type="hidden" name="createdata" id="createdata" value="">
          <div class="row brances-create">
            <div class="col-lg-4 col-md-12 col-sm-12">
              <div class="card legal-details">
                <div class="card-header p-3">
                  <h4>Legal Details</h4>
                </div>
                <div class="card-body pt-2">
                  <?php if ($companyCountry == 103) { ?>
                    <div class="form-input text-right d-flex">
                      <div class="d-flex">
                        <input type="checkbox" class="haveGSTregg" name="haveGSTregg" id="haveGSTregg" checked>
                        <label for="" class="mb-0">I am GSTIN Registered. </label>
                      </div>
                      <div class="d-flex Branch_nameclass">
                        <label for="" class="mb-0">Branch Name :</label>
                        <label for="" class="text-xs font-bold font-italic mb-0 Branch_nameclasstxt">...</label>
                      </div>
                    </div>

                    <div class="form-input">
                      <label for="">GSTIN <span id="gstinStatus"></span></label>
                      <div class="d-flex">
                        <input type="text" class="form-control" name="branch_gstin" id="branch_gstin" placeholder="Enter GST" oninput="this.value = this.value.toUpperCase();">
                        <span class="error branch_gstin"></span>
                        <span class="rupee-symbol gst-submit-symbol pr-2" id="checkAndVerifyGstinBtn"><i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                      </div>
                    </div>
                    <div class="form-input lgnmtradeNam" style="display: none;">
                      <label class=" font-italic" for="">Legal name: <span id="lgnm"></span></label>
                      <label class=" font-italic" for="">Trade name: <span id="tradeNam"></span> </label>
                      <input type="hidden" class="form-control" name="legal_name" id="legal_name_input" value="" readonly>
                    </div>
                  <?php } else {


                  ?>
                    <div class="form-input">
                      <label for=""><?= $abn ?> <span id="gstinStatus"></span></label>
                      <div class="d-flex">
                        <input type="text" class="form-control" name="branch_gstin" id="branch_gstin" placeholder="Enter <?= $abn ?>" oninput="this.value = this.value.toUpperCase();">
                        <span class="error branch_gstin"></span>
                        <!-- <span class="rupee-symbol gst-submit-symbol pr-2" id="checkAndVerifyGstinBtn"><i class="fa fa-arrow-right" aria-hidden="true"></i></span> -->
                      </div>
                    </div>
                  <?php } ?>

                  <?php if ($companyCountry != 103) { ?>
                    <div class="form-input">

                      <label for="">Trade Name:</label>
                      <input type="text" class="form-control" name="legal_name" id="legal_name_input" value="">
                    </div>

                  <?php } ?>
                  <div class="form-input">

                    <label for=""><?= $tfn ?></label>
                    <input type="text" class="form-control" name="pan-number" id="panNumber" placeholder="Enter <?= $tfn ?> number" value="<?= $companyPAN; ?>" readonly>
                  </div>
                  <div class="form-input">
                    <label for="">Company Name</label>
                    <input type="text" class="form-control" name="company-name" id="companyName" placeholder="Enter company name" value="<?= $companyNameNav; ?>" readonly>
                  </div>
                  <div class="form-input">
                    <label for="">Constitution of Business</label>
                    <input type="text" class="form-control" name="con_business" id="con_business" placeholder="Enter cob" value="<?= $companyCOB; ?>" readonly>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col">
              <div class="card branch-address fullUnblur <?php if ($companyCountry == 103) {
                                                            echo "blur";
                                                          } ?>" id="afullUnblur">
                <div class="card-header p-3">
                  <h4>Address</h4>
                </div>
                <div class="card-body pt-2">
                  <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 form-input">
                      <label for="">Building Number*</label>
                      <input type="text" class="form-control" name="build_no" id="build_no" placeholder="Enter building Number">
                      <span class="error build_no"></span>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 form-input">
                      <?php if ($companyCountry == 103) { ?>
                        <label for="">Flat Number</label>
                      <?php } else { ?>
                        <label for="">Unit No.</label>
                      <?php } ?>
                      <input type="text" class="form-control" name="flat_no" id="flat_no" placeholder="Enter <?php if ($companyCountry == 103) {
                                                                                                                echo "Flat Number"; ?>
                              
                            <?php } else {
                                                                                                                echo "Unit Number"; ?>
                              
                            <?php } ?> ">
                      <span class="error flat_no"></span>
                    </div>
                    <div class="form-input">
                      <label for="">Street Name*</label>
                      <input type="text" class="form-control" name="street_name" id="street_name" placeholder="Enter street name">
                      <span class="error street_name"></span>
                    </div>
                    <?php if ($companyCountry == 103) { ?>
                      <div class="form-input">
                        <label for="">Location*</label>
                        <input type="text" class="form-control" name="location" id="location" placeholder="Enter location">
                        <span class="error location"></span>
                      </div>
                      <div class="col-lg-6 col-md-12 col-sm-12 form-input">
                        <label for="">City*</label>
                        <input type="text" class="form-control" name="city" id="city" placeholder="Enter city">
                        <span class="error city"></span>
                      </div>
                      <div class="col-lg-6 col-md-12 col-sm-12 form-input">
                        <label for="">District*</label>
                        <input type="text" class="form-control" name="district" id="district" placeholder="Enter district">
                        <span class="error district"></span>
                      </div>
                    <?php } ?>


                    <div class="form-input">
                      <label for="">Region*</label>

                      <select id="region" name="region" class="form-control regionDropDown" required>

                        <option value="">Select Region</option>
                        <?php
                        $state_sql = queryGet("SELECT * FROM `erp_state_region` WHERE region_status='active'", true);
                        $state_data = $state_sql['data'];
                        foreach ($state_data as $data) {

                        ?>

                          <option value="<?= $data['region_id'] ?>"><?= $data['region_name'] ?></option>
                        <?php
                        }
                        ?>
                      </select>
                      <span class="error region"></span>

                    </div>
                    <div class="form-input">
                      <?php if ($companyCountry == 103) { ?>
                        <label for="">State*</label>
                      <?php } else { ?>

                        <label for="">Territory*</label>
                      <?php } ?>

                      <select id="state" name="state" class="form-control stateDropDown">
                        <option value="">Select <?php if ($companyCountry == 103) {
                                                  echo "State";
                                                } else {
                                                  echo "Territory";
                                                } ?></option>
                        <?php
                        $state_sql = queryGet("SELECT * FROM `erp_gst_state_code` WHERE country_id = $companyCountry", true);
                        $state_data = $state_sql['data'];

                        foreach ($state_data as $data) {

                        ?>

                          <option value="<?= $data['gstStateName'] ?>"><?= $data['gstStateName'] ?></option>
                        <?php
                        }
                        ?>
                      </select>
                      <span class="error state"></span>

                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 form-input">
                      <label for="">Postal Code*</label>
                      <input type="text" class="form-control" name="pincode" id="pincode" placeholder="Enter pin">
                      <span class="error pincode"></span>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 form-input">
                      <label for="">Country*</label>
                      <?php
                      // $country = queryGet('SELECT * FROM `erp_countries`', true);
                      $country = queryGet("SELECT `name` FROM `erp_countries` where `id`='$companyCountry'")['data'];

                      ?>
                      <input type="text" id="country" name="country" class="form-control" value="<?= $country['name'] ?>" readonly>

                      <span class="error country"></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 col">
              <div class="card admin-login fullUnblur <?php if ($companyCountry == 103) {
                                                        echo "blur";
                                                      } ?>" id="ufullUnblur">
                <div class="card-header p-3">
                  <h4>POC Details</h4>
                </div>
                <div class="card-body pt-2">
                  <div class="form-input">
                    <label for="">Full Name*</label>
                    <input type="text" name="adminName" class="form-control" id="adminName">
                    <span class="error adminName"></span>
                  </div>
                  <div class="form-input">
                    <label for="">User Email*</label>
                    <input type="email" name="adminEmail" class="form-control" id="adminEmail">
                    <span class="error adminEmail"></span>
                  </div>
                  <div class="form-input">
                    <label for="">User Phone*</label>
                    <input type="text" name="adminPhone" class="form-control" id="adminPhone">
                    <span class="error adminPhone"></span>
                  </div>
                  <div class="form-input">
                    <label for="">Password*</label>
                    <input type="text" name="adminPassword" class="form-control" id="adminPassword" value="<?php echo rand(1111, 9999); ?>">
                    <span class="error adminPassword"></span>
                  </div>
                </div>
              </div>
            </div>
            <div class="btn-section mt-2 mb-2 ml-auto">

              <button class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" value="add_post">Submit</button>

            </div>
          </div>
        </form>
      </div>
    </section>
    <!-- /.content -->
  </div>
<?php
} else if (isset($_GET['edit']) && $_GET["edit"] > 0) {

  $id = $_GET['edit'];
  $branch_sql = queryGet("SELECT * FROM `" . ERP_BRANCHES . "` WHERE `branch_id`=$id");
  $branch_data = $branch_sql['data'];
  //console($branch_data);
  $admin_sql = queryGet("SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminBranchId`=$id AND `fldAdminRole`=1 ORDER BY `fldAdminKey` ASC");
  $admin_data = $admin_sql['data'];
  //console($admin_data);
?>



  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="container-fluid">

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

          <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Branches</a></li>

          <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Edit Branch</a></li>

          <li class="back-button">

            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

              <i class="fa fa-reply po-list-icon"></i>

            </a>

          </li>

        </ol>

      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="edit_frm" name="edit_frm">
          <input type="hidden" name="editdata" id="editdata" value="">
          <input type="hidden" name="branch_id" id="branch_id" value="<?= $id ?>">
          <input type="hidden" name="admin_id" id="admin_id" value="<?= $admin_data['fldAdminKey'] ?>">
          <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">



          <div class="row">

            <div class="col-lg-8 col-md-8 col-sm-8">

              <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                <div class="card-header">

                  <h4>Branch Basic Details</h4>

                </div>

                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                  <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12">

                      <div class="row goods-info-form-view customer-info-form-view">

                        <div class="col-lg-4 col-md-4 col-sm-4">

                          <div class="form-input">

                            <?php if ($companyCountry == 103) { ?>
                              <label for="">GSTIN</label>
                            <?php } else { ?>
                              <label for=""><?= $abn ?></label>
                            <?php } ?>

                            <input type="text" class="form-control" id="branch_gstin" name="branch_gstin" value="<?= $branch_data['branch_gstin'] ?>" readonly>

                            <span class="error branch_gstin"></span>

                          </div>

                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4">

                          <div class="form-input">

                            <label for="">Trade Name</label>

                            <input type="text" name="branch_name" class="form-control" id="branch_name" value="<?= $branch_data['branch_name'] ?>" readonly>

                            <span class="error branch_name"></span>

                          </div>

                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4">

                          <div class="form-input">

                            <label for="">Constitution of Business</label>

                            <?php $selected_value = $branch_data['con_business'] ?? ''; if ($const['type'] == "dropdown") { ?>
                              <select name="con_business" class="form-control" id="con_business">
                                <?php foreach ($const['options'] as $option): ?>
                                  <option value="<?= htmlspecialchars($option['key']) ?>" <?= ($selected_value == $option['key']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($option['label']) ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            <?php } else { ?>
                              <input type="text" name="con_business" class="form-control" id="con_business" value="<?= $branch_data['con_business'] ?>">

                            <?php } ?>
                            


                            <span class="error con_business"></span>

                          </div>

                        </div>




                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Building Number</label>

                            <input type="text" name="build_no" class="form-control" id="build_no" value="<?= $branch_data['build_no'] ?>">

                            <span class="error build_no"></span>

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <?php if ($companyCountry == 103) { ?>
                              <label for="">Flat No.</label>
                            <?php } else { ?>
                              <label for="">Unit No.</label>
                            <?php } ?>

                            <input type="text" name="flat_no" class="form-control" id="flat_no" value="<?= $branch_data['flat_no'] ?>">

                            <span class="error flat_no"></span>

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Street Name</label>

                            <input type="text" name="street_name" class="form-control" id="street_name" value="<?= $branch_data['street_name'] ?>">

                            <span class="error street_name"></span>

                          </div>

                        </div>





                        <?php if ($companyCountry == 103) { ?>
                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">Location</label>

                              <input type="text" name="location" class="form-control" id="location" value="<?= $branch_data['location'] ?>">

                              <span class="error location"></span>

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">City</label>

                              <input type="text" name="city" class="form-control" id="city" value="<?= $branch_data['city'] ?>">

                              <span class="error city"></span>

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">District</label>

                              <input type="text" name="district" class="form-control" id="district" value="<?= $branch_data['district'] ?>">

                              <span class="error district"></span>

                            </div>

                          </div>
                        <?php }  ?>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">
                            <label for="">Region*</label>

                            <select id="region" name="region" class="form-control regionDropDown" required>
                              <option value="">Select Region</option>
                              <?php
                              $state_sql = queryGet("SELECT * FROM `erp_state_region` WHERE region_status='active'", true);
                              $state_data = $state_sql['data'];
                              foreach ($state_data as $data) {

                              ?>

                                <option value="<?= $data['region_id'] ?>" <?php if ($data['region_id'] == $branch_data['region']) {
                                                                            echo 'selected';
                                                                          } ?>><?= $data['region_name'] ?></option>
                              <?php
                              }
                              ?>
                            </select>
                            <span class="error region"></span>

                          </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <?php if ($companyCountry == 103) { ?>
                              <label for="">State</label>
                            <?php } else { ?>
                              <label for="">Territory</label>
                            <?php } ?>

                            <input type="text" name="state" class="form-control" id="state" value="<?= $branch_data['state'] ?>">

                            <span class="error state"></span>

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Postal Code</label>

                            <input type="number" name="pincode" class="form-control" id="pincode" value="<?= $branch_data['pincode'] ?>" readonly>

                            <span class="error pincode"></span>

                          </div>

                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Country</label>

                            <input type="text" name="country" class="form-control" id="country" value="<?= $branch_data['country'] ?>" readonly>

                            <span class="error country"></span>

                          </div>

                        </div>





                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </div>

            <div class="col-lg-4 col-md-4 col-sm-4">

              <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                <div class="card-header">

                  <h4>Branch Admin Details</h4>

                </div>

                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                  <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12">

                      <div class="row goods-info-form-view customer-info-form-view">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="form-input">

                            <label for="">User Name</label>

                            <input type="text" name="adminName" class="form-control" id="adminEmail" value="<?= $admin_data['fldAdminName'] ?>">

                            <span class="error adminName"></span>

                          </div>

                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">

                          <div class="form-input">

                            <label for="">User Email</label>

                            <input type="email" name="adminEmail" class="form-control" id="adminEmail" value="<?= $admin_data['fldAdminEmail'] ?>" readonly>

                            <span class="error adminEmail"></span>

                          </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">

                          <div class="form-input">

                            <label for="">User Phone</label>

                            <input type="text" name="adminPhone" class="form-control" id="adminName" value="<?= $admin_data['fldAdminPhone'] ?>">

                            <span class="error adminPhone"></span>

                          </div>

                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="form-input">

                            <label for="">Password</label>

                            <input type="password" name="adminPassword" class="form-control" id="adminPassword" value="<?= $admin_data['fldAdminPassword'] ?>">

                            <span class="error adminPassword"></span>

                          </div>

                        </div>




                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </div>


            <div class="btn-section mt-2 mb-2 ml-auto">

              <!-- <button class="btn btn-primary save-close-btn float-right edit_data waves-effect waves-light" value="add_post">Update</button> -->

              <button class="btn btn-danger save-close-btn float-right edit_data waves-effect waves-light" value="add_draft">Update</button>

            </div>


          </div>

        </form>
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

            <ul class="nav nav-tabs border-0 mb-3" id="custom-tabs-two-tab" role="tablist">
              <li class="pt-2 px-3 d-flex justify-content-between align-items-cEnter" style="width:100%">
                <h3 class="card-title">Manage Branches</h3>
                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
              </li>
            </ul>


            <div class="card card-tabs">

              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

                <div class="card-body">

                  <div class="row filter-serach-row">

                    <div class="col-lg-2 col-md-2 col-sm-12">

                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                    </div>

                    <div class="col-lg-10 col-md-10 col-sm-12">

                      <div class="row table-header-item">

                        <div class="col-lg-11 col-md-11 col-sm-11">

                          <div class="section serach-input-section">



                            <input type="text" id="myInput" placeholder="" class="field form-control" />

                            <div class="icons-container">

                              <div class="icon-search">

                                <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>

                              </div>

                              <div class="icon-close">

                                <i class="fa fa-search po-list-icon" id="myBtn"></i>

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

                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

                        </div>

                      </div>



                    </div>

                  </div>

                </div>



              </form>
              <!-- <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-2 col-md-2 col-sm-12">
                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-12">
                      <div class="section serach-input-section">

                        <div class="collapsible-content">
                          <div class="filter-col">

                            <div class="row">
                              <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group-manage-vendor">
                                  <select name="vendor_status_s" id="vendor_status_s" class="form-control">
                                    <option value="">--- Status --</option>
                                    <option value="active" <?php if (isset($_REQUEST['vendor_status_s']) && 'active' == $_REQUEST['vendor_status_s']) {
                                                              echo 'selected';
                                                            } ?>>Active</option>
                                    <option value="inactive" <?php if (isset($_REQUEST['vendor_status_s']) && 'inactive' == $_REQUEST['vendor_status_s']) {
                                                                echo 'selected';
                                                              } ?>>Inactive</option>
                                    <option value="draft" <?php if (isset($_REQUEST['vendor_status_s']) && 'draft' == $_REQUEST['vendor_status_s']) {
                                                            echo 'selected';
                                                          } ?>>Draft</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group-manage-vendor"> <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                  echo $_REQUEST['form_date_s'];
                                                                                                                                                                } ?>" />
                                </div>
                              </div>
                             <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group-manage-vendor"> <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                  echo $_REQUEST['form_date_s'];
                                                                                                                                                                } ?>" />
                                </div>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group-manage-vendor"> 
                              <input type="text" name="keyword" class="fld form-control form-control" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                        echo $_REQUEST['keyword'];
                                                                                                                                                      } ?>">
                              </div>
                              </div>


                              <div class="col-lg-2 col-md-2 col-sm-2">
                                <button type="submit" class="btn btn-primary btnstyle">Search</button>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-2">
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btnstyle">Reset</a>
                              </div>
                            </div>






                          </div>
                        </div>
                        <button type="button" class="collapsible btn-search-collpase" id="btnSearchCollpase">
                          <i class="fa fa-search"></i>
                        </button>
                      </div>

                    </div>
                  </div>

              </form> -->
              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                  <?php
                  $cond = '';

                  $sts = " AND `branch_status` !='deleted'";
                  if (isset($_REQUEST['branch_status_s']) && $_REQUEST['branch_status_s'] != '') {
                    $sts = ' AND branch_status="' . $_REQUEST['branch_status_s'] . '"';
                  }

                  if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                    $cond .= " AND branch_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                  }

                  if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                    $cond .= " AND (`branch_cin` like '%" . $_REQUEST['keyword'] . "%' OR `branch_name` like '%" . $_REQUEST['keyword'] . "%' OR `branch_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                  }

                  $sql_list = "SELECT * FROM `" . ERP_BRANCHES . "` WHERE 1 " . $cond . " " . $sts . " AND company_id='" . $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"] . "' ORDER BY branch_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);


                  $countShow = "SELECT count(*) FROM `" . ERP_BRANCHES . "` WHERE 1 " . $cond . " AND company_id='" . $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_BRANCHES", $_SESSION["logedCompanyAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($num_list > 0) {
                  ?>
                    <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                      <thead>
                        <tr>
                          <th>#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th>Branches Code</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th>Branches Name</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th>GSTIN Number</th>
                          <?php  }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th>Address</th>
                          <?php } ?>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($qry_list)) {
                          // console($row);
                          $id = $row['branch_id'];
                          $company_id = $row['company_id'];
                          $admin_sql = queryGet("SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminBranchId`=$id AND `fldAdminRole`=1 ORDER BY `fldAdminKey` ASC");
                          $admin_data = $admin_sql['data'];
                          //console($admin_data);
                          $company_sql = queryGet("SELECT * FROM `" . ERP_COMPANIES . "` WHERE `company_id`=$company_id ");
                          $company_data = $company_sql['data'];
                          // console($company_data);
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $row['branch_code'] ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= $row['branch_name'] ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $row['branch_gstin'] ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <td><?= $row['street_name'] ?></td>
                            <?php } ?>
                            <td>
                              <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['branch_id'] ?>">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button <?php if ($row['branch_status'] == "draft") { ?> type="button" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change branch_status?')" style="cursor: pointer; border:none" <?php } ?> class="btn" data-toggle="tooltip" data-placement="top" title="<?php echo $row['branch_status'] ?>">
                                  <?php if ($row['branch_status'] == "active") { ?>
                                    <p class="status"><?php echo ucfirst($row['branch_status']); ?></p>
                                  <?php } else if ($row['branch_status'] == "inactive") { ?>
                                    <p class="status-danger"><?php echo ucfirst($row['branch_status']); ?></p>
                                  <?php } else if ($row['branch_status'] == "draft") { ?>
                                    <p class="status-warning"><?php echo ucfirst($row['branch_status']); ?></p>

                                  <?php } ?>

                                </button>
                              </form>
                            </td>
                            <td>

                              <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemoBranch<?= $row['branch_code'] ?>" class="btn btn-sm">

                                <i class="fa fa-eye po-list-icon"></i>

                              </a>

                              <!-- <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $row['branch_id']; ?>" style="cursor: pointer;" class="btn btn-sm" title="Edit Branch"><i class="fa fa-edit po-list-icon"></i></a> -->
                              <?php
                              if (isset($_SESSION["logedCompanyAdminInfo"]["adminRole"]) && $_SESSION["logedCompanyAdminInfo"]["adminRole"] == 3) { ?>
                                <form action="" method="POST" class="btn btn-sm">
                                  <input type="hidden" name="fldAdminBranchId" value="<?php echo $row['branch_id'] ?>">
                                  <input type="hidden" name="fldAdminCompanyId" value="<?php echo $row['company_id'] ?>">
                                  <input type="hidden" name="visit" value="visit">
                                  <button title="Visit Branch" type="submit" onclick="return confirm('Are you sure to Visit?')" class="btn btn-sm" style="cursor: pointer; border:none"><i class="fa fa-share po-list-icon" aria-hidden="true"></i></button>
                                </form>
                              <?php } ?>
                              <!-- <form action="" method="POST" class="btn btn-sm">
                                <input type="hidden" name="id" value="<?php echo $row['branch_id'] ?>">
                                <input type="hidden" name="changeStatus" value="delete">
                                <a title="Delete Branch" type="submit" onclick="return confirm('Are you sure to delete?')" style="cursor: pointer; border:none"><i class="fa fa-trash po-list-icon"></i></a>
                              </form> -->
                            </td>
                          </tr>

                          <!-- right modal start here  -->

                          <div class="modal fade right branch-modal customer-modal" id="fluidModalRightSuccessDemoBranch<?= $row['branch_code'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                              <!--Content-->

                              <div class="modal-content">

                                <!--Header-->

                                <div class="modal-header pt-4">

                                  <div class="row branch-detail-header mt-3">

                                    <div class="col-lg-6 col-md-6 col-sm-6 col">

                                      <p class="heading lead text-sm mt-2 mb-2"><?= $company_data['company_name']  ?></p>

                                      <p class="text-xs mt-2 mb-2"><?= $company_data['company_gstin']  ?></p>

                                      <p class="text-xs mt-2 mb-2"><?= $company_data['company_const_of_business']  ?></p>

                                    </div>

                                    <!-- <hr class="divider-vertical"> -->

                                    <div class="col-lg-6 col-md-6 col-sm-6 col">

                                      <p class="heading lead text-sm mt-2 mb-2"><?= $row['branch_name'] ?></p>

                                      <p class="text-xs mt-2 mb-2"><?= $row['branch_gstin'] ?></p>

                                      <p class="text-xs mt-2 mb-2"><?= $row['con_business'] ?></p>

                                    </div>

                                  </div>
                                  <div class="display-flex-space-between mt-4 mb-3">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item">
                                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= str_replace('/', '-', $row['branch_code']) ?>">Info</a>
                                      </li>

                                      <!-- -------------------Audit History Button Start------------------------- -->
                                      <li class="nav-item">
                                        <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $row['branch_code']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $row['branch_code']) ?>" href="#history<?= str_replace('/', '-', $row['branch_code']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $row['branch_code']) ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                      </li>
                                      <!-- -------------------Audit History Button End------------------------- -->
                                    </ul>
                                    <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">
                                      <!-- <a href="" class="btn btn-sm">
                                        <i title="Toggle" class="fa fa-toggle-on po-list-icon"></i>
                                      </a> -->
                                      <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $row['branch_id']; ?>" style="cursor: pointer;" class="btn btn-sm" title="Edit Branch"><i class="fa fa-edit po-list-icon"></i></a>
                                      <form action="" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $row['branch_id'] ?>">
                                        <input type="hidden" name="changeStatus" value="delete">
                                        <button title="Delete Branch" type="submit" onclick="return confirm('Are you sure to delete?')" class="btn btn-sm"><i class="fa fa-trash po-list-icon" style="color: red;"></i></a>
                                      </form>
                                    </div>
                                  </div>


                                </div>



                                <!--Body-->

                                <div class="modal-body" style="padding: 0;">

                                  <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $row['branch_code']) ?>" role="tabpanel" aria-labelledby="home-tab">
                                      <div class="row px-3">

                                        <div class="col-lg-12 col-md-12 col-sm-12">


                                          <!-------Address------>
                                          <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                            <div class="accordion-item">
                                              <h2 class="accordion-header" id="flush-headingOne">
                                                <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" style="color: white !important;" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                  Address
                                                </button>
                                              </h2>
                                              <div id="classifications" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body p-0">

                                                  <div class="card">

                                                    <div class="card-body p-3">

                                                      <div class="display-flex-space-between">
                                                        <p class="font-bold text-xs">Building Number :</p>
                                                        <p class="font-bold text-xs"><?= $row['build_no'] ?></p>
                                                      </div>

                                                      <div class="display-flex-space-between">
                                                        <p class="font-bold text-xs"><?php if ($companyCountry == 103) {
                                                                                        echo "Flat";
                                                                                      } else {
                                                                                        echo "Unit";
                                                                                      } ?> No. :</p>
                                                        <p class="font-bold text-xs"><?= $row['flat_no'] ?></p>
                                                      </div>

                                                      <div class="display-flex-space-between">
                                                        <p class="font-bold text-xs">Street Name :</p>
                                                        <p class="font-bold text-xs"><?= $row['street_name'] ?></p>
                                                      </div>


                                                      <?php if ($companyCountry == 103) { ?>
                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs">Location :</p>
                                                          <p class="font-bold text-xs"><?= $row['location'] ?></p>
                                                        </div>

                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs">City :</p>
                                                          <p class="font-bold text-xs"><?= $row['city'] ?></p>
                                                        </div>

                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs">District :</p>
                                                          <p class="font-bold text-xs"><?= $row['district'] ?></p>
                                                        </div>
                                                      <?php } ?>
                                                      <div class="display-flex-space-between">
                                                        <p class="font-bold text-xs">Region :</p>
                                                        <?php
                                                        $region_id = $row['region'];
                                                        $region_name = queryGet("SELECT `region_name` FROM `erp_state_region` WHERE `region_id`='$region_id'")['data'];
                                                        // console($region_name);
                                                        ?>
                                                        <p class="font-bold text-xs"><?= $region_name['region_name'] ?></p>
                                                      </div>
                                                      <div class="display-flex-space-between">
                                                        <p class="font-bold text-xs">State :</p>
                                                        <p class="font-bold text-xs"><?= $row['state'] ?></p>
                                                      </div>
                                                      <div class="display-flex-space-between">
                                                        <p class="font-bold text-xs">Postal Code :</p>
                                                        <p class="font-bold text-xs"><?= $row['pincode'] ?></p>
                                                      </div>
                                                      <div class="display-flex-space-between">
                                                        <p class="font-bold text-xs">Country :</p>
                                                        <p class="font-bold text-xs"><?= $row['country'] ?></p>
                                                      </div>

                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          </div>

                                          <!-------POC------>
                                          <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                            <div class="accordion-item">
                                              <h2 class="accordion-header" id="flush-headingOne">
                                                <button class="accordion-button btn btn-primary mt-3 mb-2" style="color: white !important;" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                  POC Details
                                                </button>
                                              </h2>
                                              <div id="classifications" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body p-0">

                                                  <div class="card">

                                                    <div class="card-body p-3">

                                                      <div class="display-flex-space-between">
                                                        <p class="font-bold text-xs">User Name :</p>
                                                        <p class="font-bold text-xs"><?= $admin_data['fldAdminName'] ?></p>
                                                      </div>

                                                      <div class="display-flex-space-between">
                                                        <p class="font-bold text-xs">User Email :</p>
                                                        <p class="font-bold text-xs"><?= $admin_data['fldAdminEmail'] ?></p>
                                                      </div>

                                                      <div class="display-flex-space-between">
                                                        <p class="font-bold text-xs">User Phone :</p>
                                                        <p class="font-bold text-xs"><?= $admin_data['fldAdminPhone'] ?></p>
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

                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                    <div class="tab-pane fade" id="history<?= str_replace('/', '-', $row['branch_code']) ?>" role="tabpanel" aria-labelledby="history-tab">

                                      <div class="audit-head-section mb-3 mt-3 ">
                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['branch_created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['branch_created_by']) ?></p>
                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['branch_updated_at']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['branch_updated_at']) ?></p>
                                      </div>
                                      <hr>
                                      <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $row['branch_code']) ?>">

                                        <ol class="timeline">

                                          <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                            <div class="new-comment font-bold">
                                              <p>Loading...
                                              <ul class="ml-3 pl-0">
                                                <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                              </ul>
                                              </p>
                                            </div>
                                          </li>
                                          <p class="mt-0 mb-5 ml-5">Loading...</p>

                                          <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                            <div class="new-comment font-bold">
                                              <p>Loading...
                                              <ul class="ml-3 pl-0">
                                                <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                              </ul>
                                              </p>
                                            </div>
                                          </li>
                                          <p class="mt-0 mb-5 ml-5">Loading...</p>
                                        </ol>
                                      </div>
                                    </div>
                                    <!-- -------------------Audit History Tab Body End------------------------- -->

                                  </div>

                                </div>

                              </div>

                              <!--/.Content-->

                            </div>

                          </div>

                          <!-- right modal end here  -->

                        <?php  } ?>
                      </tbody>
                      <tbody>
                        <tr>
                          <td colspan="8">
                            <!-- Start .pagination -->

                            <?php
                            if ($count > 0 && $count > $GLOBALS['show']) {
                            ?>
                              <div class="pagination align-right">
                                <?php pagination($count, "frm_opts"); ?>
                              </div>

                              <!-- End .pagination -->

                            <?php  } ?>

                            <!-- End .pagination -->
                          </td>
                        </tr>
                      </tbody>

                    </table>
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
              <!--  -->


              <!---------------------------------Table settings Model Start--------------------------------->

              <div class="modal" id="myModal2">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Table Column Settings</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                      <input type="hidden" name="tablename" value="<?= TBL_COMPANY_ADMIN_TABLESETTINGS; ?>" />
                      <input type="hidden" name="pageTableName" value="ERP_BRANCHES" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                Branches Code</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                Branches Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                GSTIN</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="4" />
                                Address</td>
                            </tr>
                          </table>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button type="submit" name="add-table-settings" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!---------------------------------Table Model End--------------------------------->

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
  $('.form-control').on('keyup', function() {
    $(this).parent().children('.error').hide()
  });
  $(".add_data").click(function() {
    var data = this.value;
    $("#createdata").val(data);
    let flag = 1;
    if (data == 'add_post') {
      // if ($("#branch_gstin").val() == "") {
      //   $(".branch_gstin").show();
      //   $(".branch_gstin").html("GSTIN  is requried.");
      //   flag++;
      // } else {
      //   $(".branch_gstin").hide();
      //   $(".branch_gstin").html("");
      // }
      if ($("#build_no").val() == "") {
        $(".build_no").show();
        $(".build_no").html("Build number is requried.");
        flag++;
      } else {
        $(".build_no").hide();
        $(".build_no").html("");
      }
      if ($("#flat_no").val() == "") {
        $(".flat_no").show();
        $(".flat_no").html("Flat number is requried.");
        flag++;
      } else {
        $(".flat_no").hide();
        $(".flat_no").html("");
      }
      if ($("#street_name").val() == "") {
        $(".street_name").show();
        $(".street_name").html(" is requried.");
        flag++;
      } else {
        $(".street_name").hide();
        $(".street_name").html("");
      }
      if ($("#pincode").val() == "") {
        $(".pincode").show();
        $(".pincode").html("pincode is requried.");
        flag++;
      } else {
        $(".pincode").hide();
        $(".pincode").html("");
      }
      if ($("#location").val() == "") {
        $(".location").show();
        $(".location").html("location is requried.");
        flag++;
      } else {
        $(".location").hide();
        $(".location").html("");
      }
      if ($("#city").val() == "") {
        $(".city").show();
        $(".city").html("city is requried.");
        flag++;
      } else {
        $(".city").hide();
        $(".city").html("");
      }
      if ($("#district").val() == "") {
        $(".district").show();
        $(".district").html("district is requried.");
        flag++;
      } else {
        $(".district").hide();
        $(".district").html("");
      }
      if ($("#region").val() == "") {
        $(".region").show();
        $(".region").html("Region is requried.");
        flag++;
      } else {
        $(".region").hide();
        $(".region").html("");
      }
      if ($("#state").val() == "") {
        $(".state").show();
        $(".state").html("state is requried.");
        flag++;
      } else {
        $(".state").hide();
        $(".state").html("");
      }
      if ($("#adminName").val() == "") {
        $(".adminName").show();
        $(".adminName").html("username is requried.");
        flag++;
      } else {
        $(".adminName").hide();
        $(".adminName").html("");
      }
      var Regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
      if ($("#adminEmail").val() == "") {
        $(".adminEmail").show();
        $(".adminEmail").html("Email is requried.");
        flag++;
      } else {
        if ($("#adminEmail").val().match(Regex)) {
          console.log($("#adminEmail").val())
          $(".adminEmail").hide();
          $(".adminEmail").html("");
        } else {
          console.log("1")
          $(".adminEmail").show();
          $(".adminEmail").html("Enter a valid email.");
          flag++;
        }
      }
      if ($("#adminPhone").val() == "") {
        $(".adminPhone").show();
        $(".adminPhone").html("Phone number is requried.");
        flag++;
      } else {
        $(".adminPhone").hide();
        $(".adminPhone").html("");
      }
      if ($("#adminPassword").val() == "") {
        $(".adminPassword").show();
        $(".adminPassword").html("Password is requried.");
        flag++;
      } else {
        $(".adminPassword").hide();
        $(".adminPassword").html("");
      }
    }
    if (flag != 1) {
      return false;
    } else {
      $(".add_data").prop('disabled', true);
      $('.add_data').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
      $("#add_frm").submit();
    }

  });
  $(".edit_data").click(function() {
    var data = this.value;
    $("#editdata").val(data);
    alert(data);
    //$( "#edit_frm" ).submit();
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


  $(document).ready(function() {


    checkboxcheckdornot();

    function checkboxcheckdornot() {
      if ($('input.haveGSTregg').is(':checked')) {
        $(".Branch_nameclasstxt").hide();
        $("#branch_gstin").removeAttr("readonly");
        $("#panNumber").attr('readonly', 'readonly');
        $("#companyName").attr('readonly', 'readonly');
        $("#cob").attr('readonly', 'readonly');
        $("#branch_name").attr('readonly', 'readonly');
        $("#con_business").attr('readonly', 'readonly');
        // $("#buildingName").attr('readonly', 'readonly');
        // $("#build_no").attr('readonly', 'readonly');
        // $("#flat_no").attr('readonly', 'readonly');
        // $("#street_name").attr('readonly', 'readonly');
        // $("#pincode").attr('readonly', 'readonly');
        // $("#location").attr('readonly', 'readonly');
        // $("#city").attr('readonly', 'readonly');
        // $("#district").attr('readonly', 'readonly');
        // $("#state").attr('readonly', 'readonly');
      }
    }

    $('[name="haveGSTregg"]').change(function() {
      if ($(this).is(':checked')) {
        $("#branch_gstin").removeAttr("readonly");
        $(".Branch_nameclasstxt").hide();
        // $("#panNumber").attr('readonly', 'readonly');
        // $("#companyName").attr('readonly', 'readonly');
        //$("#cob").attr('readonly', 'readonly');
        $("#branch_name").attr('readonly', 'readonly');
        // $("#con_business").attr('readonly', 'readonly');
        // $("#buildingName").attr('readonly', 'readonly');
        // $("#build_no").attr('readonly', 'readonly');
        // $("#flat_no").attr('readonly', 'readonly');
        // $("#street_name").attr('readonly', 'readonly');
        // $("#pincode").attr('readonly', 'readonly');
        // $("#location").attr('readonly', 'readonly');
        // $("#city").attr('readonly', 'readonly');
        // $("#district").attr('readonly', 'readonly');
        // $("#state").attr('readonly', 'readonly');
        $("#ufullUnblur").addClass("blur");
        $("#afullUnblur").addClass("blur");
      } else {
        $("#branch_gstin").val('');
        $(".Branch_nameclasstxt").hide();
        $("#branch_gstin").attr('readonly', 'readonly');
        // $("#panNumber").removeAttr("readonly");
        // $("#companyName").removeAttr("readonly");
        // $("#cob").removeAttr("readonly");
        $("#branch_name").removeAttr("readonly");
        //$("#con_business").removeAttr("readonly");
        // $("#buildingName").removeAttr("readonly");
        // $("#build_no").removeAttr("readonly");
        // $("#flat_no").removeAttr("readonly");
        // $("#street_name").removeAttr("readonly");
        // $("#pincode").removeAttr("readonly");
        // $("#location").removeAttr("readonly");
        // $("#city").removeAttr("readonly");
        // $("#district").removeAttr("readonly");
        // $("#state").removeAttr("readonly");
        $("#ufullUnblur").removeClass("blur");
        $("#afullUnblur").removeClass("blur");
        fldBlnak();
      }
    });

    $(document).on("keydown", "#branch_gstin", function() {
      $('#checkAndVerifyGstinBtn').html('<i class="fa fa-arrow-right" aria-hidden="true"></i>');

      fldBlnak();
    });

    $(document).on("keyup keydown paste", "#state", function() {
      $(".Branch_nameclasstxt").show();
      $(".Branch_nameclasstxt").html($(this).val());
    });

    function fldBlnak() {

      $("#branch_name").val('');
      $("#build_no").val('');
      $("#flat_no").val('');
      $("#street_name").val('');
      $("#pincode").val('');
      $("#location").val('');
      $("#city").val('');
      $("#district").val('');
      $("#state").val('');
    }

    $(document).on("click", "#checkAndVerifyGstinBtn", function() {
      var branch_gstin = $("#branch_gstin").val();
      var leng_gstin = branch_gstin.length;
      if (leng_gstin > 14) {
        var branchpan = branch_gstin.substr(2, 10).trim();
        var companyPan = '<?= $companyPAN; ?>'.trim();
        if (branchpan != companyPan) {
          let Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          });
          Toast.fire({
            icon: `warning`,
            title: `&nbsp;This not your GSTIN please check!`
          });
        } else {
          $.ajax({
            type: "GET",
            url: `ajaxs/ajax-gst-details.php?gstin=${branch_gstin}`,
            beforeSend: function() {
              $('#checkAndVerifyGstinBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            },
            success: function(response) {

              $('#gstinloder').html("");
              responseObj = JSON.parse(response);
              if (responseObj["status"] == "success") {
                responseData = responseObj["data"];

                if (responseData["sts"] == "Active") {
                  $("#branch_gstin").attr('readonly', 'readonly');
                  // $("#companyName").val(responseData["tradeNam"]);
                  // $("#con_business").val(responseData["ctb"]);
                  $("#build_no").val(responseData['pradr']['addr']['bno']);
                  $("#flat_no").val(responseData['pradr']['addr']['flno']);
                  $("#street_name").val(responseData['pradr']['addr']['st']);
                  $("#pincode").val(responseData['pradr']['addr']['pncd']);
                  $("#location").val(responseData['pradr']['addr']['loc']);
                  if (responseData['pradr']['addr']['city'] != '') {
                    $("#city").val(responseData['pradr']['addr']['city']);
                  } else {
                    $("#city").val(responseData['pradr']['addr']['dst']);
                  }
                  $("#district").val(responseData['pradr']['addr']['dst']);
                  $("#state").val(responseData['pradr']['addr']['stcd']);
                  $(".Branch_nameclasstxt").show();
                  $(".lgnmtradeNam").show();
                  $("#lgnm").html(responseData['lgnm']);
                  $("#tradeNam").html(responseData['tradeNam']);
                  $(".Branch_nameclasstxt").html(responseData['pradr']['addr']['stcd']);
                  $("#legal_name_input").val(responseData['tradeNam']);
                  //$("#status").val(responseData["sts"]);

                  $("#ufullUnblur").removeClass("blur");
                  $("#afullUnblur").removeClass("blur");
                  $('#checkAndVerifyGstinBtn').html('<i class="fa fa-check" aria-hidden="true"></i>');
                } else {
                  let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                  });
                  Toast.fire({
                    icon: `warning`,
                    title: `&nbsp;GSTIN is ${responseData["sts"]}!`
                  });
                  $("#branch_gstin").attr('readonly', 'readonly');
                  // $("#panNumber").removeAttr("readonly");
                  // $("#companyName").removeAttr("readonly");
                  // $("#cob").removeAttr("readonly");
                  // $("#branch_name").removeAttr("readonly");
                  //$("#con_business").removeAttr("readonly");
                  // $("#buildingName").removeAttr("readonly");
                  // $("#build_no").removeAttr("readonly");
                  // $("#flat_no").removeAttr("readonly");
                  // $("#street_name").removeAttr("readonly");
                  // $("#pincode").removeAttr("readonly");
                  // $("#location").removeAttr("readonly");
                  // $("#city").removeAttr("readonly");
                  // $("#district").removeAttr("readonly");
                  // $("#state").removeAttr("readonly");

                  $("#gstinStatus").html(`${responseData["sts"]}`);
                  $("#ufullUnblur").removeClass("blur");
                  $("#afullUnblur").removeClass("blur");
                  $(".branch_gstin").show();
                  $(".branch_gstin").html(`GSTIN status is ${responseData["sts"]}`);
                  $('#checkAndVerifyGstinBtn').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>');
                }
              } else {
                let Toast = Swal.mixin({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000
                });
                Toast.fire({
                  icon: `warning`,
                  title: `&nbsp;Invalid GSTIN No!`
                });

                $("#branch_gstin").removeAttr('readonly', 'readonly');
                $('#checkAndVerifyGstinBtn').html('<i class="fa fa-arrow-right" aria-hidden="true"></i>');
              }
            }
          });
        }
      }

    });


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

  var inputs = document.getElementsByClassName("form-control");
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
<style>
  .dataTable thead {
    top: 0px !important;
  }
</style>