<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");

if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusCustomer($_POST, "customer_id", "customer_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["createdata"])) {
  $addNewObj = createDataCustomer($_POST);
  // console($addNewObj);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}
if (isset($_POST["editData"])) {
  // console($_SESSION);
  $editDataObj = updateDataCustomer($_POST);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_GET['delete'])) {
  // echo 1;
  $CustomerId = base64_decode($_GET['delete']);
  $del = queryUpdate("UPDATE `erp_customer` SET `customer_status`='deleted' WHERE `customer_id`=$CustomerId");
  //console($del);

  if ($del['status'] == "success") {
    swalToast("success", "Deleted Successfully", "manage-customers.php");
  } else {
    swalToast("warning", "Something Went Wrong", "manage-customers.php");
  }
}
?>
<style>
  .phone-alt-number,
  .email-alt {
    display: flex;
    justify-content: end;
    gap: 3px;
    align-items: center;
  }

  .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
    font-size: 12px;
  }

  .customer-modal .modal-header {
    height: 300px !important;
  }

  .accordion {
    background-color: transparent !important;
  }

  .display-flex-space-between p {
    width: 77%;
    text-align: left !important;
  }

  .row .col.col-head {
    font-size: 10px;
    color: #777;
    font-weight: 600;
    padding: 10px 7px;
    border-bottom: 2px solid #fff;
    text-align: left;
    max-width: 145px;
  }


  .row .col.col-body {
    font-size: 10px;
    color: #003060;
    font-weight: 600;
    padding: 10px 7px;
    border-bottom: 2px solid #fff;
    text-align: left;
    max-width: 145px;
  }

  .row .col.col-head:nth-child(1),
  .row .col.col-body:nth-child(1) {
    max-width: 80px;
  }

  .status-custom {
    font-size: 10px;
  }

  div#experienceTabContent {
    padding-left: 2em;
  }

  #experienceTab.nav-pills .nav-link {
    border-radius: 20px;
    background: #dbe5ee;
    border: 2px solid #dbe5ee;
    color: #003060;
    font-size: 10px;
    font-weight: 600;
    text-align: center;
    margin: 10px 0px;
    white-space: nowrap;
  }

  #experienceTab.nav-pills .nav-item {
    border-bottom: 0;
  }

  #experienceTab.nav-pills .nav-link.active {
    background: #003060;
    border: 2px solid #003060;
    color: #fff;
  }


  /* details.callout-header p:nth-child(1) {
    display: inline-flex;
  }

  details.callout-header.info {
    background-color: #f2f7ff;
    border-color: #73aaff;
    border-left-color: #0065ff;
  }

  details.callout-header.info summary {
    background-color: #d1e3ff;
    color: #004cbf;
  }

  details.callout-header.info[open] summary {
    border-bottom-color: #73aaff;
  } */





  @media (max-width: 575px) {
    .customer-modal .modal-header {
      height: 310px !important;
    }
  }
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<!-- Resources CHART_ONLY -->
<script src="<?= BASE_URL ?>public/assets/core.js"></script>
<script src="<?= BASE_URL ?>public/assets/charts.js"></script>
<script src="<?= BASE_URL ?>public/assets/animated.js"></script>
<script src="<?= BASE_URL ?>public/assets/forceDirected.js"></script>
<script src="<?= BASE_URL ?>public/assets/sunburst.js"></script>

<!-- Styles CHART_ONLY -->
<style>
  .chartContainer {
    width: 100%;
    height: 400px;
    font-size: 10px;
  }

  .pieChartContainer {
    width: 100%;
    height: 400px;
    font-size: 10px;
  }

  .card.flex-fill h5 {
    color: #fff;
    font-size: 15px;
  }

  .card.flex-fill .card-header {
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .card.flex-fill .card-header input,
  .card.flex-fill .card-header select {
    max-width: 155px;
  }

  .head-title,
  .head-input {
    display: flex;
    gap: 10px;
    align-items: center;
  }


  .card-body::after,
  .card-footer::after,
  .card-header::after {
    display: none;
  }

  .pin-tab {
    cursor: pointer;
    text-decoration: none;
  }

  .robo-element {
    height: 30vh;
    /*50vh*/
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 25px;
  }

  .robo-element img {
    width: 200px;
    height: 200px;
    object-fit: contain;
  }
</style>

<?php
if (isset($_GET['create'])) {
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
          <div class="modal-header card-header py-2 px-3">
            <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info text-sm"></i>&nbsp;Notes</h4>
            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
          </div>
          <div id="notesModalBody" class="modal-body card-body">
          </div>
        </div>
      </div>
    </div>

    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0 border-bottom">
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Customer List</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Customer</a></li>
          <li class="back-button">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
              <i class="fa fa-reply po-list-icon"></i>
            </a>
          </li>
        </ol>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- /.row -->
        <div class="row">
          <div class="col-md-12">
            <div class="card2 card-primary">
              <div class="card-header2 pb-5">
                <!-- <h3 class="card-title">Create New Customer</h3>-->
              </div>
              <div class="card-body p-0 gstfield" id="gstform">
                <div class="row p-0 m-0">
                  <?php

                  ?>
                </div>
                <div class="vendor-gstin" id="VerifyGstinBtnDiv">
                  <div class="card">
                    <div class="card-header">
                      <div class="head">
                        <i class="fa fa-user"></i>
                        <h4>Customer GSTIN</h4>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="info-vendor-gstin"><span>Put your GSTIN and click on below verify button to get your Bussiness details!</span></div>
                      <div class="form-inline">
                        <label for="">Enter your GSTIN number</label>
                        <input type="text" class="form-control vendor-gstin-input w-75" name="customerGstNoInput" id="customerGstNoInput">
                        <button class="btn btn-primary verify-btn checkAndVerifyGstinBtn">
                          <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </button>
                      </div>

                      <div class="d-flex mt-2">
                        <span class="text-xs font-bold">Don't have GSTIN? Check me </span>
                        <div class="d-inline ml-0 pl-2">
                          <input type="checkbox" id="isGstRegisteredCheckBoxBtn">
                          <label for="isGstRegisteredCheckBoxBtn">
                          </label>
                        </div>

                      </div>


                    </div>
                  </div>
                </div>

              </div>
              <!-- <div class="card-body p-0 gstfield" id="gstform">
                <div class="row p-0 m-0">
                  <?php

                  ?>
                </div>
                <div class="row m-0 p-0 mt-3" id="VerifyGstinBtnDiv">
                  <div class="card gst-card ml-auto mr-auto">
                    <div class="card-header text-center h4 text-bold">Verify GSTIN</div>
                    <div class="card-body pt-4 pb-5">
                      <h6 class="mt-2 mb-3 text-muted text-center">Put your GSTIN and click on below verify button<br> to get your Bussiness details!</h6>
                      <div class="material-textfield">
                        <input type="text" name="customerGstNoInput" id="customerGstNoInput">
                        <label>Enter your GSTIN number</label>
                        <span class="btn-block2 send-btn checkAndVerifyGstinBtn">
                          <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div> -->

              <!--multisteps-form-->
              <div class="multisteps-form" id="multistepform" style="display:none;">
                <!--<div id="customerCreateMainForm"></div>-->

              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div>
    </section>

    <!-- /.content -->
  </div>
<?php } else if (isset($_GET['edit'])) { ?>
  <!-- 
  #############################################  
  #############################################  
  edit / update page -->
  <!-- ########################################  
  #############################################   -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0 border-bottom">
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Customer</a></li>
          <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-plus po-list-icon"></i>
              Create Customer</a></li>
          <li class="back-button">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
              <i class="fa fa-reply po-list-icon"></i>
            </a>
          </li>
        </ol>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <!--progress bar-->
        <div class="row">
          <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
            <div class="multisteps-form__progress">
              <button class="multisteps-form__progress-btn js-active text-xs" type="button" title="User Info">Basic Details</button>
              <button class="multisteps-form__progress-btn text-xs" type="button" title="Comments" id="poc_btn" disabled>POC Details</button>
            </div>
          </div>
        </div>
        <!--form panels-->
        <div class="row">
          <div class="col-12 col-lg-8 m-auto">
            <form class="multisteps-form__form" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="edit_frm" name="edit_frm">
              <input type="hidden" name="editData" id="editData" value="">
              <input type="hidden" name="company_id" id="company_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]; ?>">
              <input type="hidden" name="company_branch_id" id="company_branch_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]; ?>">

              <!--single form panel-->
              <div class="multisteps-form__panel shadow bg-white js-active" data-animation="scaleIn">
                <h4 class="multisteps-form__title">Basic Details</h4>
                <div class="multisteps-form__content">
                  <?php
                  $editCustomerId = base64_decode($_GET['edit']);
                  $sql = "SELECT " . ERP_CUSTOMER . ".*, " . ERP_CUSTOMER_ADDRESS . ".* FROM `" . ERP_CUSTOMER . "`,`" . ERP_CUSTOMER_ADDRESS . "` WHERE `" . ERP_CUSTOMER . "`.`customer_id`=`" . ERP_CUSTOMER_ADDRESS . "`.`customer_id` AND `" . ERP_CUSTOMER_ADDRESS . "`.`customer_address_primary_flag`=1 AND `" . ERP_CUSTOMER . "`.`customer_id`=$editCustomerId";
                  //echo  $sql = "SELECT * FROM `".ERP_CUSTOMER."` WHERE `customer_id`=$editCustomerId";
                  $res = $dbCon->query($sql);
                  $row = $res->fetch_assoc();
                  // console($row);
                  // echo "<pre>";
                  // print_r($row);
                  // echo "</pre>";
                  ?>
                  <input type="hidden" name="customer_id" value="<?= $row['customer_id'] ?>" id="">
                  <input type="hidden" name="customer_code" value="<?= $row['customer_code'] ?>" id="">
                  <div class="row">
                    <!-- <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="customer_code" id="customer_code" value="<?= $row['customer_code'] ?>" readonly>
                        <label>Customer Code</label>
                      </div>
                    </div> -->

                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="customer_gstin" id="customer_gstin" value="<?= $row['customer_gstin'] ?>" readonly>
                        <label>GSTIN</label>
                      </div>

                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="customer_pan" id="customer_pan" value="<?= $row['customer_pan'] ?>">
                        <label>Pan *</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="trade_name" id="trade_name" value="<?= $row['trade_name'] ?>">
                        <label>Customer Name</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="constitution_of_business" id="constitution_of_business" value="<?= $row['constitution_of_business'] ?>">
                        <label>Constitution of Business</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="state" id="state" value="<?= $row['customer_address_state'] ?>">
                        <label>State</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="city" id="city" value="<?= $row['customer_address_city'] ?>">
                        <label>City</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="district" id="district" value="<?= $row['customer_address_district'] ?>">
                        <label>District</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="location" id="location" value="<?= $row['customer_address_location'] ?>">
                        <label>Location</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="build_no" id="build_no" value="<?= $row['customer_address_building_no'] ?>">
                        <label>Building Number</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="flat_no" id="flat_no" value="<?= $row['customer_address_flat_no'] ?>">
                        <label>Flat Number</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="street_name" id="street_name" value="<?= $row['customer_address_street_name'] ?>">
                        <label>Street Name</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="number" name="pincode" id="pincode" value="<?= $row['customer_address_pin_code'] ?>">
                        <label>Pin Code</label>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="hidden" name="opening_balance" id="customer_opening_balance" value="<?php echo $row['customer_opening_balance'] ?>" id="customer_opening_balance">
                        <!-- <label style="display:hidden;">Opening Blance</label> -->
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <select id="customer_currency" name="currency" class="form-control form-control-border borderColor">
                          <!--<option value="">Select Currency</option>-->
                          <?php
                          $listResult = getAllCurrencyType();
                          if ($listResult["status"] == "success") {
                            foreach ($listResult["data"] as $listRow) {
                          ?>
                              <option <?php if ($row['customer_currency'] == $listRow['currency_id']) {
                                        echo "selected";
                                      } ?> value="<?php echo $listRow['currency_id']; ?>"><?php echo $listRow['currency_name']; ?></option>
                          <?php }
                          } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="credit_period" id="customer_credit_period" value="<?= $row['customer_credit_period'] ?>" id="customer_credit_period">
                        <label>Credit Period(In Days)</label>
                      </div>
                    </div>
                  </div>
                  <div class="button-row d-flex mt-4">
                    <!-- <div>
                              <span>Back </span>
                              <div class="icheck-primary d-inline ml-2">
                                <input type="checkbox" id="checkbox2" class="checkbox2">
                                <label for="checkbox2">
                                </label>
                              </div>
                            </div>-->
                    <button class="btn btn-primary ml-auto js-btn-next" type="button" title="Next">Next</button>
                  </div>
                </div>
              </div>
              <!--single form panel-->
              <div class="multisteps-form__panel shadow p-4 rounded bg-white" data-animation="scaleIn">
                <h4 class="multisteps-form__title">POC Details</h4>
                <div class="multisteps-form__content">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="customer_authorised_person_name" value="<?= $row['customer_authorised_person_name'] ?>" id="adminName">
                        <label>Name of Person*</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="customer_authorised_person_designation" value="<?= $row['customer_authorised_person_designation'] ?>" id="customer_authorised_person_designation">
                        <label>Designation</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="customer_authorised_person_phone" value="<?= $row['customer_authorised_person_phone'] ?>" id="adminPhone">
                        <label>Phone Number*</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="customer_authorised_alt_phone" value="<?= $row['customer_authorised_alt_phone'] ?>" id="customer_authorised_person_phone">
                        <label>Alternative Phone </label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="email" name="customer_authorised_person_email" value="<?= $row['customer_authorised_person_email'] ?>" id="adminEmail">
                        <label>Email*</label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="email" name="customer_authorised_alt_email" value="<?= $row['customer_authorised_alt_email'] ?>" id="customer_authorised_person_email">
                        <label>Alternative Email</label>
                      </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="adminPassword" id="adminPassword" value="<?php echo rand(00000, 999999) ?>">
                          <label>Login Password [Will be send to the POC email]</label>
                        </div>
                      </div> -->
                    <!-- <div class="col-md-3">
                        <div class="material-textfield">
                          <input type="file" name="customer_picture" id="customer_picture">
                        </div>
                      </div> -->
                    <div class="col-md-3">
                      <div class="material-textfield">
                        <select id="customer_visible_to_all" name="customer_visible_to_all" class="select2 form-control form-control-border borderColor">
                          <option value="" selected>Visible For All</option>
                          <option <?php if ($row['customer_visible_to_all'] == 'No') {
                                    echo "selected";
                                  } ?> value="No">No</option>
                          <option <?php if ($row['customer_visible_to_all'] == 'Yes') {
                                    echo "selected";
                                  } ?> value="Yes">Yes</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="button-row d-flex mt-4">
                    <button class="btn btn-outline-secondary js-btn-prev" type="button" title="Prev">Prev</button>
                    <!-- <button class="btn ml-auto btn-danger add_data" type="button" title="Save As Draft" value="add_draft">Save As Draft</button> -->
                    <button id="customerCreateBtn" class="btn btn-primary ml-auto edit_data" type="submit" title="update" name="customerUpdateBtn">Update</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>



    <!-- /.content -->
  </div>
  <script>
    // *** multi step form *** //
    //DOM elements
    const DOMstrings = {
      stepsBtnClass: 'multisteps-form__progress-btn',
      stepsBtns: document.querySelectorAll(`.multisteps-form__progress-btn`),
      stepsBar: document.querySelector('.multisteps-form__progress'),
      stepsForm: document.querySelector('.multisteps-form__form'),
      stepsFormTextareas: document.querySelectorAll('.multisteps-form__textarea'),
      stepFormPanelClass: 'multisteps-form__panel',
      stepFormPanels: document.querySelectorAll('.multisteps-form__panel'),
      stepPrevBtnClass: 'js-btn-prev',
      stepNextBtnClass: 'js-btn-next'
    };
    //remove class from a set of items
    const removeClasses = (elemSet, className) => {

      elemSet.forEach(elem => {

        elem.classList.remove(className);

      });

    };

    //return exect parent node of the element
    const findParent = (elem, parentClass) => {

      let currentNode = elem;

      while (!currentNode.classList.contains(parentClass)) {
        currentNode = currentNode.parentNode;
      }

      return currentNode;

    };

    //get active button step number
    const getActiveStep = elem => {
      return Array.from(DOMstrings.stepsBtns).indexOf(elem);
    };

    //set all steps before clicked (and clicked too) to active
    const setActiveStep = activeStepNum => {

      //remove active state from all the state
      removeClasses(DOMstrings.stepsBtns, 'js-active');

      //set picked items to active
      DOMstrings.stepsBtns.forEach((elem, index) => {

        if (index <= activeStepNum) {
          elem.classList.add('js-active');
        }

      });
    };

    //get active panel
    const getActivePanel = () => {

      let activePanel;

      DOMstrings.stepFormPanels.forEach(elem => {

        if (elem.classList.contains('js-active')) {

          activePanel = elem;

        }

      });

      return activePanel;

    };

    //open active panel (and close unactive panels)
    const setActivePanel = activePanelNum => {

      //remove active class from all the panels
      removeClasses(DOMstrings.stepFormPanels, 'js-active');

      //show active panel
      DOMstrings.stepFormPanels.forEach((elem, index) => {
        if (index === activePanelNum) {

          elem.classList.add('js-active');

          setFormHeight(elem);

        }
      });

    };

    //set form height equal to current panel height
    const formHeight = activePanel => {

      const activePanelHeight = activePanel.offsetHeight;

      DOMstrings.stepsForm.style.height = `${activePanelHeight}px`;

    };

    const setFormHeight = () => {
      const activePanel = getActivePanel();

      formHeight(activePanel);
    };

    //STEPS BAR CLICK FUNCTION
    DOMstrings.stepsBar.addEventListener('click', e => {

      //check if click target is a step button
      const eventTarget = e.target;

      if (!eventTarget.classList.contains(`${DOMstrings.stepsBtnClass}`)) {
        return;
      }

      //get active button step number
      const activeStep = getActiveStep(eventTarget);

      //set all steps before clicked (and clicked too) to active
      setActiveStep(activeStep);

      //open active panel
      setActivePanel(activeStep);
    });

    //PREV/NEXT BTNS CLICK
    DOMstrings.stepsForm.addEventListener('click', e => {

      const eventTarget = e.target;

      //check if we clicked on `PREV` or NEXT` buttons
      if (!(eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`) || eventTarget.classList.contains(`${DOMstrings.stepNextBtnClass}`))) {
        return;
      }

      //find active panel
      const activePanel = findParent(eventTarget, `${DOMstrings.stepFormPanelClass}`);

      let activePanelNum = Array.from(DOMstrings.stepFormPanels).indexOf(activePanel);

      //set active step and active panel onclick
      if (eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`)) {
        activePanelNum--;

      } else {

        activePanelNum++;

      }

      setActiveStep(activePanelNum);
      setActivePanel(activePanelNum);

    });

    //SETTING PROPER FORM HEIGHT ONLOAD
    window.addEventListener('load', setFormHeight, false);

    //SETTING PROPER FORM HEIGHT ONRESIZE
    window.addEventListener('resize', setFormHeight, false);

    //changing animation via animation select !!!YOU DON'T NEED THIS CODE (if you want to change animation type, just change form panels data-attr)

    const setAnimationType = newType => {
      DOMstrings.stepFormPanels.forEach(elem => {
        elem.dataset.animation = newType;
      });
    };

    //selector onchange - changing animation
    const animationSelect = document.querySelector('.pick-animation__select');

    animationSelect.addEventListener('change', () => {
      const newAnimationType = animationSelect.value;

      setAnimationType(newAnimationType);
    });
  </script>
<?php } else {
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
            <div class="p-0 pt-1 my-2">
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage Customer</h3>
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
                </li>
              </ul>
            </div>
            <?php
            $keywd = '';
            if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
              $keywd = $_REQUEST['keyword'];
            } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
              $keywd = $_REQUEST['keyword2'];
            } ?>
            <div class="card card-tabs" style="border-radius: 20px;">
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-2 col-md-2 col-sm-12">
                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-12">
                      <div class="row table-header-item">
                        <div class="col-lg-11 col-md-11 col-sm-11">
                          <div class="section serach-input-section">
                            <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="<?php echo $keywd; ?>">
                            <div class="icons-container">
                              <div class="icon-search">
                                <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                              </div>
                              <div class="icon-close">
                                <i class="fa fa-search po-list-icon" id="myBtn"></i>

                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1">
                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                        </div>
                      </div>

                    </div>

                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Request</h5>

                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php /*if (isset($_REQUEST['keyword2'])) {
                                                                                                                                                      echo $_REQUEST['keyword2'];
                                                                                                                                                    } */ ?>">
                              </div>
                              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <select name="status_s" id="status_s" class="fld form-control" style="appearance: auto;">
                                  <option value=""> Status </option>
                                  <option value="active" <?php if (isset($_REQUEST['status_s']) && 'active' == $_REQUEST['status_s']) {
                                                            echo 'selected';
                                                          } ?>>Active
                                  </option>
                                  <option value="inactive" <?php if (isset($_REQUEST['status_s']) && 'inactive' == $_REQUEST['status_s']) {
                                                              echo 'selected';
                                                            } ?>>Inactive
                                  </option>
                                  <option value="draft" <?php if (isset($_REQUEST['status_s']) && 'draft' == $_REQUEST['status_s']) {
                                                          echo 'selected';
                                                        } ?>>Draft</option>
                                </select>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                          echo $_REQUEST['form_date_s'];
                                                                                                                        } ?>" />
                              </div>
                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <input class="fld form-control" type="date" name="to_date_s" id="to_date_s" value="<?php if (isset($_REQUEST['to_date_s'])) {
                                                                                                                      echo $_REQUEST['to_date_s'];
                                                                                                                    } ?>" />
                              </div>
                            </div>

                          </div>
                          <div class="modal-footer">
                            <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync "></i>Reset</a>-->
                            <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                              Search</button>
                          </div>
                        </div>
                      </div>
                    </div>

              </form>
              <script>
                var input = document.getElementById("myInput");
                input.addEventListener("keypress", function(event) {
                  if (event.key === "Enter") {
                    event.preventDefault();
                    document.getElementById("myBtn").click();
                  }
                });
                var form = document.getElementById("search");

                document.getElementById("myBtn").addEventListener("click", function() {
                  form.submit();
                });
              </script>
              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">

                  <?php
                  $cond = '';

                  $sts = " AND `customer_status` !='deleted'";
                  if (isset($_REQUEST['customer_status_s']) && $_REQUEST['status_s'] != '') {
                    $sts = ' AND customer_status="' . $_REQUEST['status_s'] . '"';
                  }

                  if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                    $cond .= " AND customer_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                  }

                  if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                    $cond .= " AND (`customer_code` like '%" . $_REQUEST['keyword'] . "%' OR `trade_name` like '%" . $_REQUEST['keyword'] . "%' OR `customer_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                  }


                  if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                    $cond .= " AND (`customer_code` like '%" . $_REQUEST['keyword2'] . "%' OR `trade_name` like '%" . $_REQUEST['keyword2'] . "%' OR `customer_gstin` like '%" . $_REQUEST['keyword2'] . "%')";
                  } else {
                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                      $cond .= " AND (`customer_code` like '%" . $_REQUEST['keyword'] . "%' OR `trade_name` like '%" . $_REQUEST['keyword'] . "%' OR `customer_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                    }
                  }


                  $sql_list = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE 1 " . $cond . "  AND company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . "  ORDER BY customer_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);


                  $countShow = "SELECT count(*) FROM `" . ERP_CUSTOMER . "` WHERE 1 " . $cond . " AND company_id='" . $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_CUSTOMER", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($num_list > 0) {
                  ?>
                    <table class="table defaultDataTable table-hover text-nowrap">
                      <thead>
                        <tr class="alert-light">
                          <th>#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th>Customer Code</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th>Customer Icon</th>
                          <?php }

                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th>Customer Name</th>
                          <?php }

                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th>Constitution of Business</th>
                          <?php }
                          if (in_array(5, $settingsCheckbox)) { ?>
                            <th>GSTIN</th>
                          <?php  }
                          if (in_array(6, $settingsCheckbox)) { ?>
                            <th>Email</th>
                          <?php }
                          if (in_array(7, $settingsCheckbox)) { ?>
                            <th>Phone</th>
                          <?php  }
                          if (in_array(8, $settingsCheckbox)) { ?>

                            <th>Order Volume</th>
                          <?php  }
                          if (in_array(9, $settingsCheckbox)) {
                          ?>
                            <th>Receipt Amount</th>
                          <?php } ?>
                          <th>Status</th>

                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $customerModalHtml = "";
                        while ($row = mysqli_fetch_assoc($qry_list)) {
                          $customerId = $row['customer_id'];
                          $customer_authorised_person_name = $row['customer_authorised_person_name'];
                          $customer_authorised_person_designation = $row['customer_authorised_person_designation'];
                          $customer_authorised_person_phone = $row['customer_authorised_person_phone'];
                          $customer_authorised_alt_phone = $row['customer_authorised_alt_phone'];
                          $customer_authorised_person_email = $row['customer_authorised_person_email'];
                          $customer_authorised_alt_email = $row['customer_authorised_alt_email'];
                          $trade_name = $row['trade_name'];
                          $ordercustomer = "SELECT SUM( IF( invoiceStatus = '4', all_total_amt, 0 ) ) AS sentInvoiceAmount FROM erp_branch_sales_order_invoices WHERE `customer_id`=$customerId";
                          $getorder = queryGet($ordercustomer, true);
                          // console($getorder['data'][0]['sentInvoiceAmount']);
                          $ordervol = "SELECT * FROM erp_branch_sales_order_invoices WHERE `customer_id`=$customerId";
                          $getvol = queryGet($ordervol);
                          // console($getvol['numRows']);
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $row['customer_code'] ?></td>
                              <?php }

                            if (in_array(2, $settingsCheckbox)) {
                              if ($row['customer_picture'] != "") { ?>
                                <td><?= $row['customer_picture'] ?></td>
                              <?php
                              } else {
                              ?>
                                <td>
                                  <div class="flex-display">

                                    <div id="profileImage"> <?php echo ucfirst(substr($row['trade_name'], 0, 1)) ?></div>
                                  </div>
                                </td>
                              <?php }
                            }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $row['trade_name'] ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <td><?= $row['constitution_of_business'] ?></td>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td><?= $row['customer_gstin'] ?></td>
                            <?php }
                            if (in_array(6, $settingsCheckbox)) { ?>
                              <td><?= $row['customer_authorised_person_email'] ?></td>
                            <?php }
                            if (in_array(7, $settingsCheckbox)) { ?>
                              <td><?= $row['customer_authorised_person_phone'] ?></td>
                            <?php }
                            if (in_array(8, $settingsCheckbox)) { ?>
                              <td><?= $getorder['data'][0]['sentInvoiceAmount'] ?></td>
                            <?php }
                            if (in_array(9, $settingsCheckbox)) { ?>
                              <td><?= $getvol['numRows'] ?></td>
                            <?php } ?>
                            <td>
                              <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['customer_id'] ?>">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button <?php if ($row['customer_status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change customer_status?')" <?php } ?> class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="<?php echo $row['customer_status'] ?>">
                                  <?php if ($row['customer_status'] == "active") { ?>
                                    <div class="status"><?php echo ucfirst($row['customer_status']); ?></div>
                                  <?php } else if ($row['customer_status'] == "inactive") { ?>
                                    <p class="status-danger"><?php echo ucfirst($row['customer_status']); ?></p>
                                  <?php } else if ($row['customer_status'] == "draft") { ?>
                                    <p class="status-warning"><?php echo ucfirst($row['customer_status']); ?></p>
                                  <?php } ?>

                                </button>
                              </form>
                            </td>
                            <td>
                              <a style="cursor: pointer;" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['customer_id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>


                            </td>


                          </tr>

                          <!-- right modal start here  -->
                          <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $row['customer_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-right modal-notify modal-success" role="document" style="width: 100%; max-width: 70%;">
                              <!--Content-->
                              <div class="modal-content">
                                <!--Header-->
                                <div class="modal-header pt-3">
                                  <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                      <p class="heading lead text-lg text-uppercase font-bold mb-2 mt-3"><?= $trade_name ?></p>
                                      <span class="text-sm font-bold mb-2"></span>
                                      <p class="text-sm text-uppercase font-bold mb-2 mt-3">Code : <?= $row['customer_code'] ?></p>
                                      <p class="text-sm text-uppercase font-bold mb-2 mt-3">GSTIN : <?= $row['customer_gstin'] ?></p>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                      <p class="text-sm text-right font-bold mb-2"><?= $customer_authorised_person_name ?></p>
                                      <p class="text-sm text-right font-italic mb-2">(<?= $customer_authorised_person_designation ?>)</p>
                                      <div class="d-flex phone-alt-number text-right mb-2">
                                        <p class="text-sm"><?= $customer_authorised_person_phone ?></p>/ <p class="text-xs text-right"><?= $customer_authorised_alt_phone ?></p>
                                      </div>
                                      <div class="d-flex email-alt text-right mb-2">
                                        <p class="text-sm"><?= $customer_authorised_person_email ?></p>/ <p class="text-xs text-right"><?= $customer_authorised_alt_email ?></p>
                                      </div>
                                    </div>
                                  </div>


                                  <div class="display-flex-space-between mt-4 mb-3">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item">
                                        <a class="nav-link active" id="home-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#home<?= $row['customer_id'] ?>" role="tab" aria-controls="home<?= $row['customer_id'] ?>" aria-selected="true">Overview</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link" id="transaction-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#transaction<?= $row['customer_id'] ?>" role="tab" aria-controls="transaction<?= $row['customer_id'] ?>" aria-selected="true">Transactions</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link" id="mail-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#mail<?= $row['customer_id'] ?>" role="tab" aria-controls="mail<?= $row['customer_id'] ?>" aria-selected="true">Mails</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link" id="statement-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#statement<?= $row['customer_id'] ?>" role="tab" aria-controls="statement<?= $row['customer_id'] ?>" aria-selected="true">Statement</a>
                                      </li>
                                      <li class="nav-item">
                                        <a class="nav-link" id="compliance-tab<?= $row['customer_id'] ?>" data-toggle="tab" href="#compliance<?= $row['customer_id'] ?>" role="tab" aria-controls="compliance<?= $row['customer_id'] ?>" aria-selected="true">Compliance Status</a>
                                      </li>

                                      <!-- -------------------Audit History Button Start------------------------- -->
                                      <li class="nav-item">
                                        <a class="nav-link auditTrail" id="history-tab<?= $row['customer_id'] ?>" data-toggle="tab" data-ccode="<?= $row['customer_code'] ?>" href="#history<?= $row['customer_id'] ?>" role="tab" aria-controls="history<?= $row['customer_id'] ?>" aria-selected="false"><i class="fa fa-history mr-2"></i>Trail</a>
                                      </li>
                                      <!-- -------------------Audit History Button End------------------------- -->
                                    </ul>
                                    <div class="action-btns display-flex-gap" id="action-navbar">
                                      <?php $customer_id = base64_encode($row['customer_id']) ?>
                                      <form action="" method="POST">
                                        <a href="manage-customers.php?edit=<?= $customer_id ?>" name="customerEditBtn">
                                          <i title="Edit" style="font-size: 1.2em" class="fa fa-edit po-list-icon"></i>
                                        </a>
                                        <a href="manage-customers.php?delete=<?= $customer_id ?>">
                                          <i title="Delete" style="font-size: 1.2em" class="fa fa-trash po-list-icon"></i>
                                        </a>
                                        <a href="">
                                          <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on po-list-icon"></i>
                                        </a>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <!--Body-->
                                <div class="modal-body p-3" style="width: 100%;">

                                  <div class="tab-content pt-0" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="home-tab">

                                      <div class="row px-3 p-0 m-0">

                                        <div class="col-lg-4 col-md-4 col-xs-12">

                                          <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                            <div class="accordion-item">
                                              <h2 class="accordion-header" id="flush-headingOne">
                                                <button class="accordion-button btn btn-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#addressDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                  Address Details
                                                </button>
                                              </h2>
                                              <div id="addressDetails" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body p-0">
                                                  <?php
                                                  $sql_addrress = "SELECT * FROM " . ERP_CUSTOMER_ADDRESS . " WHERE customer_id='" . $row['customer_id'] . "' AND customer_address_primary_flag=1";
                                                  $res_addrress = queryGet($sql_addrress);
                                                  if ($res_addrress['status'] == 'success  ') {
                                                    foreach ($res_addrress as $rowAddress) {
                                                  ?>
                                                      <div class="card h-100">
                                                        <div class="card-body p-3">
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">State :</p>
                                                            <p class="font-bold text-xs"><?= $rowAddress['customer_address_state'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">City :</p>
                                                            <p class="font-bold text-xs"><?= $rowAddress['customer_address_city'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">District :</p>
                                                            <p class="font-bold text-xs"><?= $rowAddress['customer_address_district'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Location :</p>
                                                            <p class="font-bold text-xs"><?= $rowAddress['customer_address_location'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Building Number :</p>
                                                            <p class="font-bold text-xs w-75"><?= $rowAddress['customer_address_building_no'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Flat Number :</p>
                                                            <p class="font-bold text-xs w-75"><?= $rowAddress['customer_address_flat_no'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Street Name :</p>
                                                            <p class="font-bold text-xs w-75"><?= $rowAddress['customer_address_street_name'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">PIN Code :</p>
                                                            <p class="font-bold text-xs w-75"><?= $rowAddress['customer_address_pin_code'] ?></p>
                                                          </div>
                                                        </div>
                                                      </div>
                                                  <?php
                                                    }
                                                  } else {
                                                    echo "Data not found";
                                                  }
                                                  ?>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>


                                        <!---------CHART_ONLY------->

                                        <div class="col-lg-8 col-md-8 col-xs-12 d-flex">
                                          <div class="card flex-fill">
                                            <div class="card-header">
                                              <div class="head-title">
                                                <h5 class="card-title chartDivReceivableAgeing_<?= $row['customer_id'] ?>"></h5>
                                              </div>

                                              <div id="containerThreeDot">

                                                <div id="menu-wrap">
                                                  <input type="checkbox" class="toggler bg-transparent" />
                                                  <div class="dots">
                                                    <div></div>
                                                  </div>
                                                  <?php
                                                  $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);
                                                  ?>
                                                  <div class="menu ">
                                                    <div>
                                                      <ul>
                                                        <li>
                                                          <select name="piefYDropdown" id="piefYDropdown_<?= $row['customer_id'] ?>" data-attr="<?= $row['customer_id'] ?> " class="form-control piefYDropdown">
                                                            <?php
                                                            foreach ($variant_sql['data'] as $key => $data) {
                                                            ?>
                                                              <option value="<?= $data['year_variant_id'] ?>"><?= $data['year_variant_name'] ?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                          </select>
                                                        </li>
                                                      </ul>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>

                                            </div>
                                            <div class="card-body">
                                              <div class="load-wrapp">
                                                <div class="load-1">
                                                  <div class="line"></div>
                                                  <div class="line"></div>
                                                  <div class="line"></div>
                                                </div>
                                              </div>
                                              <div id="chartDivReceivableAgeing_<?= $row['customer_id'] ?>" class="pieChartContainer"></div>
                                            </div>
                                          </div>
                                        </div>

                                        <!---------CHART_ONLY------->

                                        <!---------CHART_ONLY------->

                                        <div class="col-lg-12 col-md-12 col-xs-12 d-flex">
                                          <div class="card flex-fill">
                                            <div class="card-header">
                                              <div class="head-title">
                                                <h5 class="card-title chartDivSalesVsCollection_<?= $row['customer_id'] ?>"></h5>
                                              </div>

                                              <div id="containerThreeDot">

                                                <div id="menu-wrap">
                                                  <input type="checkbox" class="toggler bg-transparent" />
                                                  <div class="dots">
                                                    <div></div>
                                                  </div>
                                                  <?php
                                                  $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);
                                                  ?>
                                                  <div class="menu ">
                                                    <div>
                                                      <ul>
                                                        <li>
                                                          <select name="fYDropdown" id="fYDropdown_<?= $row['customer_id'] ?>" data-attr="<?= $row['customer_id'] ?> " class="form-control fYDropdown">
                                                            <?php
                                                            foreach ($variant_sql['data'] as $key => $data) {
                                                            ?>
                                                              <option value="<?= $data['year_variant_id'] ?>"><?= $data['year_variant_name'] ?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                          </select>
                                                        </li>
                                                        <li><label class="mb-0" for="">OR</label></li>
                                                        <li>
                                                          <input type="month" name="monthRange" id="monthRange_<?= $row['customer_id'] ?>" data-attr="<?= $row['customer_id'] ?> " class="form-control monthRange" style="max-width: 100%;" />
                                                        </li>
                                                      </ul>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>

                                            </div>
                                            <div class="card-body">
                                              <div class="load-wrapp">
                                                <div class="load-1">
                                                  <div class="line"></div>
                                                  <div class="line"></div>
                                                  <div class="line"></div>
                                                </div>
                                              </div>
                                              <div id="chartDivSalesVsCollection_<?= $row['customer_id'] ?>" class="chartContainer"></div>
                                            </div>
                                          </div>
                                        </div>

                                        <!---------CHART_ONLY------->



                                      </div>
                                    </div>
                                    <div class="tab-pane fade" id="transaction<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="transaction-tab">


                                      <div class="row p-3">
                                        <div class="col-md-2 mb-3">
                                          <ul class="nav nav-pills flex-column" id="experienceTab" role="tablist">
                                            <li class="nav-item">
                                              <a class="nav-link active" id="invoices-tab" data-toggle="tab" href="#invoices" role="tab" aria-controls="invoices" aria-selected="true">Invoices</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link" id="customerPayments-tab" data-toggle="tab" href="#customerPayments" role="tab" aria-controls="customerPayments" aria-selected="false">Customer Payments</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link" id="estimate-tab" data-toggle="tab" href="#estimate" role="tab" aria-controls="estimate" aria-selected="false">Estimates</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link" id="salesOrder-tab" data-toggle="tab" href="#salesOrder" role="tab" aria-controls="salesOrder" aria-selected="false">Sales Orders</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link" id="expenses-tab" data-toggle="tab" href="#expenses" role="tab" aria-controls="expenses" aria-selected="false">Expenses</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link" id="recurringExpenses-tab" data-toggle="tab" href="#recurringExpenses" role="tab" aria-controls="recurringExpenses" aria-selected="false">Recurring Expenses</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link" id="journals-tab" data-toggle="tab" href="#journals" role="tab" aria-controls="journals" aria-selected="false">Journals</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link" id="bills-tab" data-toggle="tab" href="#bills" role="tab" aria-controls="bills" aria-selected="false">Bills</a>
                                            </li>
                                            <li class="nav-item">
                                              <a class="nav-link" id="creditNote-tab" data-toggle="tab" href="#creditNote" role="tab" aria-controls="creditNote" aria-selected="false">Credit Notes</a>
                                            </li>
                                          </ul>
                                        </div>
                                        <div class="col-md-10">
                                          <div class="tab-content" id="experienceTabContent">

                                            <div class="tab-pane fade show active text-left text-light" id="invoices" role="tabpanel" aria-labelledby="hoinvoicesme-tab">
                                              <h3 class="d-flex gap-2 text-sm font-bold mb-3">Invoices
                                                <a href="#" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                              </h3>
                                              <div class="card">
                                                <div class="card-body p-3" style="overflow-x: scroll;">
                                                  <div class="row">
                                                    <div class="col col-head">Icon</div>
                                                    <div class="col col-head">Invoice Number</div>
                                                    <div class="col col-head">Amount</div>
                                                    <div class="col col-head">Date</div>
                                                    <div class="col col-head">Due in (day/s)</div>
                                                    <div class="col col-head">Status</div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>

                                            <div class="tab-pane fade text-left text-light" id="customerPayments" role="tabpanel" aria-labelledby="customerPayments-tab">
                                              <h3 class="d-flex gap-2 text-sm font-bold mb-3">Customer Payments
                                                <a href="#" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                              </h3>
                                              <div class="card">
                                                <div class="card-body p-3" style="overflow-x: scroll;">
                                                  <div class="row">
                                                    <div class="col col-head">Icon</div>
                                                    <div class="col col-head">Invoice Number</div>
                                                    <div class="col col-head">Amount</div>
                                                    <div class="col col-head">Date</div>
                                                    <div class="col col-head">Due in (day/s)</div>
                                                    <div class="col col-head">Status</div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col col-body">
                                                      <p class="company-name mt-1">TCS</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                    </div>
                                                    <div class="col col-body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col col-body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col col-body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col col-body">
                                                      <div class="status-custom w-75 text-secondary">
                                                        SENT
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>

                                            <div class="tab-pane fade text-left text-light" id="estimate" role="tabpanel" aria-labelledby="estimate-tab">
                                              <h3>Estimates</h3>
                                              <span class="date-range code-font">Other Details</span>
                                              <ul class="pt-2">
                                                <li>The volcano is eruting.</li>
                                                <li>Everything is on fire.</li>
                                              </ul>
                                            </div>

                                            <div class="tab-pane fade text-left text-light" id="salesOrder" role="tabpanel" aria-labelledby="salesOrder-tab">
                                              <h3>Sales Orders</h3>
                                              <span class="date-range code-font">Other Details</span>
                                              <ul class="pt-2">
                                                <li>The volcano is eruting.</li>
                                                <li>Everything is on fire.</li>
                                              </ul>
                                            </div>

                                            <div class="tab-pane fade text-left text-light" id="expenses" role="tabpanel" aria-labelledby="expenses-tab">
                                              <h3>Expenses</h3>
                                              <span class="date-range code-font">Other Details</span>
                                              <ul class="pt-2">
                                                <li>The volcano is eruting.</li>
                                                <li>Everything is on fire.</li>
                                              </ul>
                                            </div>

                                            <div class="tab-pane fade text-left text-light" id="recurringExpenses" role="tabpanel" aria-labelledby="recurringExpenses-tab">
                                              <h3>Recurring Expenses</h3>
                                              <span class="date-range code-font">Other Details</span>
                                              <ul class="pt-2">
                                                <li>The volcano is eruting.</li>
                                                <li>Everything is on fire.</li>
                                              </ul>
                                            </div>

                                            <div class="tab-pane fade text-left text-light" id="journals" role="tabpanel" aria-labelledby="journals-tab">
                                              <h3>Journals</h3>
                                              <span class="date-range code-font">Other Details</span>
                                              <ul class="pt-2">
                                                <li>The volcano is eruting.</li>
                                                <li>Everything is on fire.</li>
                                              </ul>
                                            </div>

                                            <div class="tab-pane fade text-left text-light" id="bills" role="tabpanel" aria-labelledby="bills-tab">
                                              <h3>Bills</h3>
                                              <span class="date-range code-font">Other Details</span>
                                              <ul class="pt-2">
                                                <li>The volcano is eruting.</li>
                                                <li>Everything is on fire.</li>
                                              </ul>
                                            </div>

                                            <div class="tab-pane fade text-left text-light" id="creditNote" role="tabpanel" aria-labelledby="creditNote-tab">
                                              <h3>Credit Notes</h3>
                                              <span class="date-range code-font">Other Details</span>
                                              <ul class="pt-2">
                                                <li>The volcano is eruting.</li>
                                                <li>Everything is on fire.</li>
                                              </ul>
                                            </div>

                                          </div>
                                          <!--tab content end-->
                                        </div>
                                      </div>


                                      <!---transaction invoice Details---->
                                      <!-- <div class="accordion accordion-flush matrix-accordion invoice-accordion p-0" id="accordionFlushExample">
                                        <div class="accordion-item">
                                          <h2 class="accordion-header" id="flush-headingOne">
                                            <button class="accordion-button btn btn-primary collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#addressDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                              Invoice
                                            </button>
                                          </h2>
                                          <div id="addressDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body p-0">
                                              <div class="card">
                                                <div class="card-body">
                                                  <div class="row">
                                                    <div class="col col-head">Icon</div>
                                                    <div class="col col-head">Invoice Number</div>
                                                    <div class="col col-head">Amount</div>
                                                    <div class="col col-head">Date</div>
                                                    <div class="col col-head">Due in (day/s)</div>
                                                    <div class="col col-head">Status</div>
                                                  </div>
                                                  <div class="row">
                                                    <div class="col body">
                                                      <div class="img-fluid avatar rounded-circle d-flex justify-content-center align-items-center" style="border: 1px solid grey;">T</div>
                                                      <p class="company-name mt-1">Tata Consultancy Services Limited</p>
                                                    </div>
                                                    <div class="col body">
                                                      INV/06/100000042/2023/FY-2022/23
                                                      [1 Item]
                                                    </div>
                                                    <div class="col body">
                                                      ₹ 35400.00
                                                    </div>
                                                    <div class="col body">
                                                      2023-06-15
                                                    </div>
                                                    <div class="col body">
                                                      <p class="status text-xs text-center">Due in 10 days</p>
                                                    </div>
                                                    <div class="col body">
                                                      <div class="status-custom text-xs w-75 text-secondary">
                                                        SENT
                                                        <div class="round">
                                                          <ion-icon name="checkmark-sharp" role="img" class="md hydrated" aria-label="checkmark sharp"></ion-icon>
                                                        </div>
                                                      </div>
                                                      <p class="status-date">2023-06-15 13:14:48</p>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div> -->

                                    </div>
                                    <div class="tab-pane fade" id="mail<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="mail-tab">
                                      mails
                                    </div>
                                    <div class="tab-pane fade" id="statement<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="statement-tab">
                                      Statements
                                    </div>
                                    <div class="tab-pane fade" id="compliance<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="compliance-tab">
                                      Compliance
                                    </div>

                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                    <div class="tab-pane fade" id="history<?= $row['customer_id'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                      <div class="audit-head-section mb-3 mt-3 ">
                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['customer_created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['customer_created_at']) ?></p>
                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['customer_updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['customer_updated_at']) ?></p>
                                      </div>
                                      <hr>
                                      <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $row['customer_code'] ?>">

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



                        <?php } ?>



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
                    <table class="table defaultDataTable table-hover text-nowrap">
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
              <?= $customerModalHtml ?>






              <!---------------------------------Table settings Model Start--------------------------------->
              <div class="modal" id="myModal2">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Table Column Settings</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                      <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                      <input type="hidden" name="pageTableName" value="ERP_CUSTOMER" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                Customer Code</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                Customer Icon</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                Customer Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                Customer TAN</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                GSTIN</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                Email</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="7" />
                                Phone</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="8" />
                                Order Volume</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="9" />
                                Receipt Amount</td>
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
    </section>
  </div>

  <!-- For Pegination------->
  <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                echo  $_REQUEST['pageNo'];
                                              } ?>">
  </form>
  <!-- End Pegination from------->

<?php
}
require_once("../common/footer.php");
?>

<script>
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
  //********************************************************************************************************** */

  var BASE_URL = `<?= BASE_URL ?>`;
  var BRANCH_URL = `<?= BRANCH_URL ?>`;
  var LOCATION_URL = `<?= LOCATION_URL ?>`;
  $(document).ready(function() {
    $(document).on("change", "#isGstRegisteredCheckBoxBtn", function() {
      let isChecked = $(this).is(':checked');
      if (isChecked) {
        $("#customerGstNoInput").attr("readonly", "readonly");
        $("#customerPanNo").removeAttr("readonly");

        $.ajax({
          type: "GET",
          url: `${LOCATION_URL}ajaxs/ajax-customer-with-out-verify-gstin.php`,
          beforeSend: function() {
            $('.checkAndVerifyGstinBtn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Loading...');
            $(".checkAndVerifyGstinBtn").toggleClass("disabled");
          },
          success: function(response) {
            $(".checkAndVerifyGstinBtn").toggleClass("disabled");
            // $('.checkAndVerifyGstinBtn').html("Re-Verify");
            responseObj = (response);
            //  $('.checkAndVerifyGstinBtn').html("Re-Verify");
            responseObj = (response);
            //responseObj = JSON.parse(responseObj);
            $("#VerifyGstinBtnDiv").hide();
            $("#multistepform").show();
            $("#multistepform").html(responseObj);
            // console.log(responseObj);
          }
        });

      } else {
        $("#customerCreateMainForm").html("");
        $("#customerGstNoInput").removeAttr("readonly");
        $("#customerPanNo").attr("readonly", "readonly");
      }
      $(".checkAndVerifyGstinBtn").toggleClass("disabled");
    });

    $(".checkAndVerifyGstinBtn").click(function() {
      let customerGstNo = $("#customerGstNoInput").val();
      if (customerGstNo != "") {
        $.ajax({
          type: "GET",
          url: `${LOCATION_URL}ajaxs/ajax-customer-verify-gstin.php?gstin=${customerGstNo}`,
          beforeSend: function() {
            $('.checkAndVerifyGstinBtn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
            $(".checkAndVerifyGstinBtn").toggleClass("disabled");
          },
          success: function(response) {
            $(".checkAndVerifyGstinBtn").toggleClass("disabled");
            //  $('.checkAndVerifyGstinBtn').html("Re-Verify");
            responseObj = (response);
            //responseObj = JSON.parse(responseObj);
            $("#VerifyGstinBtnDiv").hide();
            $("#multistepform").show();
            $("#multistepform").html(responseObj);
            //console.log(responseObj);
            load_js();
          }
        });
      } else {
        let Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        });
        Toast.fire({
          icon: `warning`,
          title: `&nbsp;Please provide GSTIN No!`
        });
      }
    });

  });

  $(document).ready(function() {
    $(document).on('change', '.customer_bank_cancelled_cheque', function() {
      var file_data = $('.customer_bank_cancelled_cheque').prop('files')[0];
      var form_data = new FormData();
      form_data.append('file', file_data);
      // alert(form_data);
      $.ajax({
        url: 'ajaxs/ajax_cancelled_cheque_upload.php', // <-- point to server-side PHP script 
        dataType: 'text', // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() {
          $('.Ckecked_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".Ckecked_loder").toggleClass("disabled");
        },
        success: function(responseData) {
          $('.Ckecked_loder').html('<i class="fa fa-upload"></i>');
          $(".Ckecked_loder").toggleClass("enabled");
          responseObj = JSON.parse(responseData);
          console.log(responseObj);
          $("#customer_bank_ifsc").val(responseObj["payload"]["cheque_details"]["ifsc"]["value"]);
          $("#account_number").val(responseObj["payload"]["cheque_details"]["acc no"]["value"]);
          $("#account_holder").val(responseObj["payload"]["cheque_details"]["acc holder"]["value"]);

          $("#customer_bank_address").val(responseObj["payload"]["bank_details"]["ADDRESS"]);
          $("#customer_bank_name").val(responseObj["payload"]["bank_details"]["BANK"]);
          $("#customer_bank_branch").val(responseObj["payload"]["bank_details"]["BRANCH"]);
        }
      });
    });


    $(document).on('click', '.visiting_card_btn', function() {
      var file_data = $('#visitingFileInput').prop('files')[0];
      var form_data = new FormData();
      form_data.append('file', file_data);
      // alert(form_data);
      $.ajax({
        url: 'ajaxs/ajax_visiting_card.php', // <-- point to server-side PHP script 
        dataType: 'text', // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() {
          $('.visiting_card_btn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".visiting_card_btn").toggleClass("disabled");
        },
        success: function(responseData) {
          $('.visiting_card_btn').html('Submit');
          $(".visiting_card_btn").toggleClass("enabled");
          responseObj = JSON.parse(responseData);
          console.log(responseObj);
          $("#adminName").val(responseObj["payload"]["ContactNames"]["value"]['0']["content"]);
          $("#vendor_authorised_person_designation").val('');

          $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"]['0']['value']);
          $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"]['1']['value']);

          $("#adminEmail").val(responseObj["payload"]["Emails"]["value"]['0']["content"]);
          $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"]['1']["content"]);

        }
      });
    });

    $(document).on('change', '.visiting_card', function() {
      var file_data = $('.visiting_card').prop('files')[0];
      var form_data = new FormData();
      form_data.append('file', file_data);
      // alert(form_data);
      $.ajax({
        url: 'ajaxs/ajax_visiting_card.php', // <-- point to server-side PHP script 
        dataType: 'text', // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() {
          $('.visiting_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".visiting_loder").toggleClass("disabled");
        },
        success: function(responseData) {
          $('.visiting_loder').html('<i class="fa fa-upload"></i>');
          $(".visiting_loder").toggleClass("enabled");
          responseObj = JSON.parse(responseData);
          console.log(responseObj);
          $("#adminName").val(responseObj["payload"]["ContactNames"]["value"]['0']["content"]);
          $("#vendor_authorised_person_designation").val('');

          $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"]['0']['value']);
          $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"]['1']['value']);

          $("#adminEmail").val(responseObj["payload"]["Emails"]["value"]['0']["content"]);
          $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"]['1']["content"]);

        }
      });
    });


    $(document).on("click", ".add_data", function() {
      var data = this.value;
      $("#createdata").val(data);
      // confirm('Are you sure to Submit?')
      $("#add_frm").submit();
    });

    // $(document).on("click", ".edit_data", function() {
    //   var data = this.value;
    //   $("#editData").val(data);
    //   alert(data);
    //   $("#edit_frm").submit();
    // });

    $(".edit_data").click(function() {
      var data = this.value;
      $("#editData").val(data);
      //confirm('Are you sure to Submit?')
      $("#edit_frm").submit();
    });

    // $(document).on("click", ".js-btn-next", function() {
    //   console.log("hi there!!!!!");
    // });

  });


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


  // datatable
  // $('#mytable2').DataTable({
  //   "paging": false,
  //   "searching": false,
  //   "ordering": true,
  // });

  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };

  window.onscroll = function() {
    myFunction()
  };

  var navbar = document.getElementById("action-navbar");
  var sticky = action - navbar.offsetTop;

  function myFunction() {
    if (window.pageYOffset >= sticky) {
      action - navbar.classList.add("sticky")
    } else {
      action - navbar.classList.remove("sticky");
    }
  };
</script>

<script src="<?= BASE_URL; ?>public/validations/customerValidation.js"></script>

<!-- CHART_ONLY -->
<script>
  $(document).ready(function() {
    for (elem of $(".chartContainer")) {
      let dataAttrValue = elem.getAttribute("id").split("_")[1];
      let id = $(`#fYDropdown_${dataAttrValue}`).val();

      $.ajax({
        type: "GET",
        url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&cust_id=${dataAttrValue}`,
        beforeSend: function() {
          $(".load-wrapp").show();
          $(".load-wrapp").css('opacity', 1);
        },
        success: function(result) {
          $(".load-wrapp").hide();
          $(".load-wrapp").css('opacity', 0);

          let res = jQuery.parseJSON(result);

          salesVsCollection(res, "chartDivSalesVsCollection", dataAttrValue);
        }
      });
    };
  });

  $(document).on("change", '.fYDropdown', function() {

    // function monthWiseChart() {
    var dataAttrValue = $(this).data('attr');
    var id = $(`#fYDropdown_${dataAttrValue}`).val();

    $.ajax({
      type: "GET",
      url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&cust_id=${dataAttrValue}`,
      beforeSend: function() {
        $(".load-wrapp").show();
        $(".load-wrapp").css('opacity', 1);
      },
      success: function(result) {
        $(".load-wrapp").hide();
        $(".load-wrapp").css('opacity', 0);

        let res = jQuery.parseJSON(result);

        salesVsCollection(res, "chartDivSalesVsCollection", dataAttrValue);
      }
    });
    // };

    // monthWiseChart();
  });

  $(document).on("change", '.monthRange', function() {

    // function dayWiseChart() {
    var dataAttrValue = $(this).data('attr');
    var month = $(`#monthRange_${dataAttrValue}`).val();

    $.ajax({
      type: "GET",
      url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?month=${month}&cust_id=${dataAttrValue}`,
      beforeSend: function() {
        $(".load-wrapp").show();
        $(".load-wrapp").css('opacity', 1);
      },
      success: function(result) {
        $(".load-wrapp").hide();
        $(".load-wrapp").css('opacity', 0);

        let res = jQuery.parseJSON(result);

        salesVsCollection(res, "chartDivSalesVsCollection", dataAttrValue);
      }
    });
    // };

    // dayWiseChart();
  });


  // ====================================== Combined bullet/column and line graphs with multiple value axes ======================================

  function salesVsCollection(chartData, chartTitle, custId) {


    $(`.${chartTitle}_${custId.trim()}`).text(`Recievable Vs Recieved`);

    if (chartData.sql_list_all_cust.length == 0 && chartData.sql_list_specific_cust.length == 0) {
      const currentDate = new Date();
      const year = currentDate.getFullYear();
      const month = String(currentDate.getMonth() + 1).padStart(2, '0');
      const day = String(currentDate.getDate()).padStart(2, '0');

      const formattedDate = `${year}-${month}-${day}`;

      chartData = {
        "sql_list_all_cust": [{
          date_: formattedDate,
          total_receivable_all: 0,
          total_received_all: 0
        }],
        "sql_list_specific_cust": [{
          date_: formattedDate,
          total_receivable: 0,
          total_received: 0
        }]
      };
    };

    am4core.ready(function() {

      // Themes begin
      am4core.useTheme(am4themes_animated);
      // Themes end

      // Create chart instance
      var chart = am4core.create(`${chartTitle}_${custId.trim()}`, am4charts.XYChart);
      chart.logo.disabled = true;

      let finalData = [];
      let outerIndex = 0;

      for (obj of chartData.sql_list_all_cust) {
        obj.total_receivable_all = Number(obj.total_receivable);
        obj.total_received_all = Number(obj.total_received);
        obj.total_receivable = 0;
        obj.total_received = 0;
        finalData.push(obj);
      };

      for (obj of chartData.sql_list_specific_cust) {

        const outerObj = finalData.map(obj => {
          return obj.date_
        })
        outerIndex = outerObj.indexOf(obj.date_)

        if (outerIndex !== -1) {
          finalData[outerIndex].total_receivable = Number(obj.total_receivable);
          finalData[outerIndex].total_received = Number(obj.total_received);
        } else {
          obj.total_receivable = Number(obj.total_receivable);
          obj.total_received = Number(obj.total_received);
          obj.total_receivable_all = 0;
          obj.total_received_all = 0;
          finalData.push(obj);
        }
      }

      finalData.sort((a, b) => (a.date_ > b.date_) ? 1 : ((b.date_ > a.date_) ? -1 : 0))

      // Add data
      chart.data = finalData;

      // Create axes
      var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
      //dateAxis.renderer.grid.template.location = 0;
      //dateAxis.renderer.minGridDistance = 30;

      var valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());
      valueAxis1.title.text = "This Customer";

      var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
      valueAxis2.title.text = "All Customers";
      valueAxis2.renderer.opposite = true;
      valueAxis2.renderer.grid.template.disabled = true;

      // Create series
      var series1 = chart.series.push(new am4charts.ColumnSeries());
      series1.dataFields.valueY = "total_receivable";
      series1.dataFields.dateX = "date_";
      series1.yAxis = valueAxis1;
      series1.name = "Receivable";
      series1.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
      series1.fill = chart.colors.getIndex(0);
      series1.strokeWidth = 0;
      series1.clustered = false;
      series1.columns.template.width = am4core.percent(40);

      var series2 = chart.series.push(new am4charts.ColumnSeries());
      series2.dataFields.valueY = "total_received";
      series2.dataFields.dateX = "date_";
      series2.yAxis = valueAxis1;
      series2.name = "Recieved";
      series2.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
      series2.fill = chart.colors.getIndex(0).lighten(0.5);
      series2.strokeWidth = 0;
      series2.clustered = false;
      series2.toBack();

      var series3 = chart.series.push(new am4charts.LineSeries());
      series3.dataFields.valueY = "total_received_all";
      series3.dataFields.dateX = "date_";
      series3.name = "Recieved (all customers)";
      series3.strokeWidth = 2;
      series3.tensionX = 0.7;
      series3.yAxis = valueAxis2;
      series3.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";

      var bullet3 = series3.bullets.push(new am4charts.CircleBullet());
      bullet3.circle.radius = 3;
      bullet3.circle.strokeWidth = 2;
      bullet3.circle.fill = am4core.color("#fff");

      var series4 = chart.series.push(new am4charts.LineSeries());
      series4.dataFields.valueY = "total_receivable_all";
      series4.dataFields.dateX = "date_";
      series4.name = "Recievable (all customers)";
      series4.strokeWidth = 2;
      series4.tensionX = 0.7;
      series4.yAxis = valueAxis2;
      series4.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
      series4.stroke = chart.colors.getIndex(0).lighten(0.5);
      series4.strokeDasharray = "3,3";

      var bullet4 = series4.bullets.push(new am4charts.CircleBullet());
      bullet4.circle.radius = 3;
      bullet4.circle.strokeWidth = 2;
      bullet4.circle.fill = am4core.color("#fff");

      // Add cursor
      chart.cursor = new am4charts.XYCursor();

      // Add legend
      chart.legend = new am4charts.Legend();
      chart.legend.position = "top";

      // Add scrollbar
      chart.scrollbarX = new am4charts.XYChartScrollbar();
      chart.scrollbarX.series.push(series1);
      chart.scrollbarX.series.push(series3);
      chart.scrollbarX.parent = chart.bottomAxesContainer;

    });
  };
  // ++++++++++++++++++++++++++++++++++++++ Combined bullet/column and line graphs with multiple value axes ++++++++++++++++++++++++++++++++++++++
</script>


<script>
  $(document).ready(function() {
    for (elem of $(".pieChartContainer")) {
      let dataAttrValue = elem.getAttribute("id").split("_")[1];
      let id = $(`#piefYDropdown_${dataAttrValue}`).val();

      $.ajax({
        type: "GET",
        url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&customer_id=${dataAttrValue}`,
        beforeSend: function() {
          $(".load-wrapp").show();
          $(".load-wrapp").css('opacity', 1);
        },
        success: function(result) {
          $(".load-wrapp").hide();
          $(".load-wrapp").css('opacity', 0);

          let res = jQuery.parseJSON(result);

          pieChart(res, "chartDivReceivableAgeing", dataAttrValue);
        }
      });
    };
  });

  $(document).on("change", '.piefYDropdown', function() {

    var dataAttrValue = $(this).data('attr');
    var id = $(`#piefYDropdown_${dataAttrValue}`).val();

    $.ajax({
      type: "GET",
      url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&customer_id=${dataAttrValue}`,
      beforeSend: function() {
        $(".load-wrapp").show();
        $(".load-wrapp").css('opacity', 1);
      },
      success: function(result) {
        $(".load-wrapp").hide();
        $(".load-wrapp").css('opacity', 0);

        let res = jQuery.parseJSON(result);

        pieChart(res, "chartDivReceivableAgeing", dataAttrValue);
      }
    });
  });

  function pieChart(chartData, chartTitle, custId) {

    am4core.ready(function() {

      // Themes
      am4core.useTheme(am4themes_animated);

      var chart = am4core.create(`${chartTitle}_${custId.trim()}`, am4charts.PieChart3D);
      chart.logo.disabled = true;
      chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

      chart.legend = new am4charts.Legend();

      let finalData = [{
          "category": "0-30 days",
          "value": 0
        },
        {
          "category": "31-60 days",
          "value": 0
        },
        {
          "category": "61-90 days",
          "value": 0
        },
        {
          "category": "91-180 days",
          "value": 0
        },
        {
          "category": "181-365 days",
          "value": 0
        },
        {
          "category": "More than 365 days",
          "value": 0
        },
      ];

      for (elem of chartData.data) {

        let due_days = parseInt(elem.due_days);

        if (due_days >= 0 && due_days <= 30) {
          finalData[0].value += Number(elem.total_due_amount);
        } else if (due_days >= 31 && due_days <= 60) {
          finalData[1].value += Number(elem.total_due_amount);
        } else if (due_days >= 61 && due_days <= 90) {
          finalData[2].value += Number(elem.total_due_amount);
        } else if (due_days >= 91 && due_days <= 180) {
          finalData[3].value += Number(elem.total_due_amount);
        } else if (due_days >= 181 && due_days <= 365) {
          finalData[4].value += Number(elem.total_due_amount);
        } else {
          finalData[5].value += Number(elem.total_due_amount);
        };
      };

      chart.paddingLeft = 50;
      chart.paddingRight = 40;
      chart.paddingTop = 20;
      chart.paddingBottom = 20;

      // Data 
      chart.data = finalData;

      chart.innerRadius = 40;

      var series = chart.series.push(new am4charts.PieSeries3D());
      series.dataFields.value = "value";
      series.dataFields.category = "category";

      chart.responsive.enabled = true;
      chart.responsive.rules.push({
        relevant: am4core.ResponsiveBreakpoints.widthLargerThan(768),
        state: function(target, stateId) {
          if (stateId !== "mobile") {
            return;
          }

          var state = target.states.create(stateId);
          state.properties.y = 100; // Change the y position for mobile
          return state;
        }
      });

    });
  };
</script>