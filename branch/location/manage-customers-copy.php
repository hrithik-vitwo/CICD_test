<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-customers.php");


if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusCustomer($_POST, "customer_id", "customer_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["createdata"])) {
  $addNewObj = createDataCustomer($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}

if (isset($_POST["editdata"])) {
  $editDataObj = updateDataCustomer($_POST);

  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>
<link rel="stylesheet" href="../../public/assets/listing.css">


<?php
if (isset($_GET['create'])) {
?>
  <style>
    body {
      background-color: #eee;
    }

    .sticky .nav-pills .nav-item a {
      color: #424242;
    }

    .doc-title {
      text-align: center;
    }

    ol li {
      font-weight: 600;
    }

    ol li a,
    ol li p {
      font-weight: 400;
    }

    @media (max-width: 374px) {
      .modal-footer.display-footer {
        display: block;
        text-align: center !important;
      }
    }

    @media (min-width: 375px) and (max-width: 1024px) {
      .modal-dialog.cascading-modal .modal-footer.display-footer {
        padding-right: 0.5rem;
        padding-left: 0.8rem;
      }
    }

    @media (max-width: 374px) {

      .btn.btn-primary-modal,
      .btn.btn-outline-secondary-modal {
        padding-left: 0.9rem;
        padding-right: 0.9rem;
      }
    }

    @media (max-width: 374px) {
      .btn.btn-rounded {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
      }
    }

    @media (min-width: 375px) and (max-width: 768px) {

      .btn.btn-primary-modal,
      .btn.btn-outline-secondary-modal {
        padding-left: 1.85rem;
        padding-right: 1.85rem;
      }
    }

    @media (max-width: 375px) {
      .modal-dialog.cascading-modal .modal-content .close {
        top: -25px;
        right: -13px;
      }
    }

    .btn-primary {
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card-primary:not(.card-outline)>.card-header {
      background-color: #003060 !important;
    }

    .btn-primary,
    .page-item.active .page-link {
      background-color: #003060 !important;
      border-color: #003060 !important;
    }

    .btn-primary .fa-plus {
      margin-right: 5px;
    }

    .menu-btn img,
    .rounded .nav-icon {
      display: none;
    }

    .menu-btn .fa-edit {
      margin-left: 10px;
    }

    .btn-outline-primary {
      color: #003060;
      border-color: #003060;
    }

    .menu-btn {
      margin-bottom: 10px;
    }

    .btn-outline-primary:hover {
      background-color: #003060 !important;
      border-color: #003060;
      color: #fff;
    }

    .btn-outline-primary:hover label {
      color: #fff;
    }

    .form-table {
      width: 100%;
    }

    #customFields .btnstyle {
      position: absolute;
      right: 20px;
      top: 20px;
    }

    .step2 {
      position: relative;
    }

    .defaultDataTable .pagination {
      display: inline-block;
      float: right;
    }

    .defaultDataTable .pagination a {
      font-size: 14px;
      color: black;
      float: left;
      padding: 8px 10px;
      text-decoration: none;
      border: 1px solid rgba(0, 0, 0, 0.5);
    }

    .defaultDataTable .pagination a.active {
      background-color: #000;
      color: #fff;
    }

    .defaultDataTable .pagination a:hover:not(.active) {
      background-color: #003060;
      color: #fff;
    }

    #mytable_paginate,
    #mytable_info {
      display: none;
    }

    .filter-col {
      position: absolute;
      right: 20px;
      width: 70%;
      background: #fff;
      z-index: 9;
      margin-top: -10px;
    }



    /* text input  */
    .material-textfield {
      position: relative;
    }

    label {
      position: absolute;
      font-size: 1rem;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      background-color: white;
      color: gray;
      padding: 0 0.3rem;
      margin: 0 0.5rem;
      transition: .1s ease-out;
      transform-origin: left top;
      pointer-events: none;
      border: none
    }

    input {
      font-size: 1rem;
      outline: none;
      border: none;
      border-bottom: 1px solid gray;
      padding: 1rem 0.7rem;
      padding-bottom: 0;
      color: rgb(10, 10, 10);
      transition: 0.1s ease-out;
      width: 100%;
      margin-bottom: 10px;
    }

    input:focus {
      border-color: #6200EE;
    }

    input:focus+label {
      color: #6200EE;
      top: 50;
      transform: translateY(-50%) scale(.9);
    }

    input:not(:placeholder-shown)+label {
      top: 0;
      transform: translateY(-50%) scale(.9);
    }

    label:not(.form-check-label):not(.custom-file-label) {
      font-weight: 400;
      top: 20%;
      color: rgb(80, 80, 80);
      background: none;
      font-style: italic
    }
  </style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0 border-bottom">
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Customer</a></li>
          <li class="breadcrumb-item active">Create New Customer</li>
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
                <div class="row m-0 p-0 mt-3" id="VerifyGstinBtnDiv">
                  <div class="card gst-card ml-auto mr-auto">
                    <div class="card-header text-center h4 text-bold">Verify GSTIN</div>
                    <div class="card-body pt-4 pb-5">
                      <h6 class="mt-2 mb-3 text-muted text-center">Put your GSTIN and click on below verify button<br> to get your Bussiness details!</h6>
                      <div class="material-textfield">
                        <input type="text" name="customerGstNoInput" id="customerGstNoInput">
                        <label>Enter your GSTIN number</label>
                        <!-- <span class="btn-block2 send-btn" id="checkAndVerifyGstinBtn"> -->
                        <span class="btn-block2 send-btn checkAndVerifyGstinBtn">
                          <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </span>
                      </div>


                      <!--<div class="row mt-2 ml-auto mr-auto">
                        <div>
                          <span>Don't have GSTIN? Check me </span>
                          <div class="icheck-primary d-inline ml-2">
                            <input type="checkbox" id="isGstRegisteredCheckBoxBtn" class="checkbox">
                            <label for="isGstRegisteredCheckBoxBtn">
                            </label>
                          </div>
                        </div>
                      </div>-->
                    </div>
                  </div>
                </div>
                <!-- <div class="row m-2" id="customerCreateMainForm"></div> -->
              </div>

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
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Customer</a></li>
          <li class="breadcrumb-item active">Edit Customer</li>
        </ol>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <!--progress bar-->
        <div class="row">
          <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
            <div class="multisteps-form__progress">
              <button class="multisteps-form__progress-btn js-active" type="button" title="User Info">Basic Details</button>
              <button class="multisteps-form__progress-btn" type="button" title="Comments">POC Details</button>
            </div>
          </div>
        </div>
        <!--form panels-->
        <div class="row">
          <div class="col-12 col-lg-8 m-auto">
            <form class="multisteps-form__form" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="update_frm" name="update_frm">
              <input type="hidden" name="updateData" id="updateData" value="">
              <input type="hidden" name="company_id" id="company_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]; ?>">
              <input type="hidden" name="company_branch_id" id="company_branch_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]; ?>">

              <!--single form panel-->
              <div class="multisteps-form__panel shadow p-4 bg-white js-active" data-animation="scaleIn">
                <h4 class="multisteps-form__title">Basic Details</h4>
                <div class="multisteps-form__content">
                  <?php
                  $editCustomerId = base64_decode($_GET['edit']);
                  $sql = "SELECT " . ERP_CUSTOMER . ".*, " . ERP_CUSTOMER_ADDRESS . ".* FROM `" . ERP_CUSTOMER . "`,`" . ERP_CUSTOMER_ADDRESS . "` WHERE `" . ERP_CUSTOMER . "`.`customer_id`=`" . ERP_CUSTOMER_ADDRESS . "`.`customer_id` AND `" . ERP_CUSTOMER_ADDRESS . "`.`customer_address_primary_flag`=1 AND `" . ERP_CUSTOMER . "`.`customer_id`=$editCustomerId";
                  //echo  $sql = "SELECT * FROM `".ERP_CUSTOMER."` WHERE `customer_id`=$editCustomerId";
                  $res = $dbCon->query($sql);
                  $row = $res->fetch_assoc();
                  // echo "<pre>";
                  // print_r($row);
                  // echo "</pre>";
                  ?>
                  <input type="hidden" name="customer_id" value="<?= $row['customer_id'] ?>" id="">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="customer_code" id="customer_code" value="<?= $row['customer_code'] ?>" readonly>
                        <label>Customer Code</label>
                      </div>
                    </div>

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
                        <input type="number" name="opening_balance" value="<?php echo $row['customer_opening_balance'] ?>" id="customer_opening_balance">
                        <label>Opening Blance</label>
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
                                      } ?> value="<?php echo $fetchAccounting['currency']; ?>"><?php echo $listRow['currency_name']; ?></option>
                          <?php }
                          } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="credit_period" value="<?= $row['customer_credit_period'] ?>" id="customer_credit_period">
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
                        <input type="number" name="customer_authorised_alt_phone" value="<?= $row['customer_authorised_alt_phone'] ?>" id="customer_authorised_person_phone">
                        <label>Alternative Phone </label>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="material-textfield">
                        <input type="text" name="customer_authorised_person_email" value="<?= $row['customer_authorised_person_email'] ?>" id="adminEmail">
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
                    <button class="btn btn-primary ml-auto add_data" type="submit" title="update" name="customerUpdateBtn">Update</button>
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
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary btnstyle m-2"><i class="fa fa-plus"></i> Add New</a>
                </li>
              </ul>
            </div>
            <div class="card card-tabs" style="border-radius: 20px;">
              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
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
                                  <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
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

              </form>
              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                  <?php
                  $cond = '';

                  $sts = " AND `customer_status` !='deleted'";
                  if (isset($_REQUEST['customer_status_s']) && $_REQUEST['customer_status_s'] != '') {
                    $sts = ' AND customer_status="' . $_REQUEST['customer_status_s'] . '"';
                  }

                  if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                    $cond .= " AND branch_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                  }

                  if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                    $cond .= " AND (`customer_code` like '%" . $_REQUEST['keyword'] . "%' OR `customer_name` like '%" . $_REQUEST['keyword'] . "%' OR `customer_gstin` like '%" . $_REQUEST['keyword'] . "%')";
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
                    <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                      <thead>
                        <tr class="alert-light">
                          <th class="borderNone">#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th class="borderNone">Customer Code</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th class="borderNone">Customer Name</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th class="borderNone">Customer PAN</th>
                          <?php  }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th class="borderNone">Constitution of Business</th>
                          <?php }
                          if (in_array(5, $settingsCheckbox)) { ?>
                            <th class="borderNone">GSTIN</th>
                          <?php  }
                          if (in_array(6, $settingsCheckbox)) { ?>
                            <th class="borderNone">Email</th>
                          <?php }
                          if (in_array(7, $settingsCheckbox)) { ?>
                            <th class="borderNone">Phone</th>
                          <?php  } ?>
                          <th class="borderNone">Status</th>

                          <th class="borderNone">Action</th>
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
                        ?>
                          <tr style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['customer_id'] ?>">
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $row['customer_code'] ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= $row['trade_name'] ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td><?= $row['customer_pan'] ?></td>
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
                            <?php } ?>
                            <td>
                              <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['customer_id'] ?>">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button <?php if ($row['customer_status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change customer_status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['customer_status'] ?>">
                                  <?php if ($row['customer_status'] == "active") { ?>
                                    <span class="badge badge-success"><?php echo ucfirst($row['customer_status']); ?></span>
                                  <?php } else if ($row['customer_status'] == "inactive") { ?>
                                    <span class="badge badge-danger"><?php echo ucfirst($row['customer_status']); ?></span>
                                  <?php } else if ($row['customer_status'] == "draft") { ?>
                                    <span class="badge badge-warning"><?php echo ucfirst($row['customer_status']); ?></span>
                                  <?php } ?>

                                </button>
                              </form>
                            </td>
                            <td>
                              <a style="cursor: pointer;" class="btn btn-sm"><i class="fa fa-eye"></i></a>
                            </td>
                          </tr>
                          <!-- right modal start here  -->
                          <div class="modal fade right" id="fluidModalRightSuccessDemo_<?= $row['customer_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                            <div style="max-width: 50%; min-width:50%" class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                              <!--Content-->
                              <div class="modal-content">
                                <!--Header-->
                                <div class="modal-header " style="background: none; border:none; color:#424242">
                                  <p class="heading lead"><?= $trade_name ?></p>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" class="white-text">×</span>
                                  </button>
                                </div>
                                <!--Body-->
                                <div class="modal-body" style="padding: 0;">
                                  <ul class="nav nav-tabs" style="padding-left: 16px;" id="myTab" role="tablist">
                                    <li class="nav-item">
                                      <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Info</a>
                                    </li>
                                    <li class="nav-item">
                                      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Activity</a>
                                    </li>
                                  </ul>
                                  <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                      <div class="col-md-12">
                                        <div class="shadow-sm bg-light py-2 mx-2 my-2" id="action-navbar" style="text-align:right">
                                          <?php $customer_id = base64_encode($row['customer_id']) ?>
                                          <form action="" method="POST">
                                            <a href="manage-customers.php?edit=<?= $customer_id ?>" name="customerEditBtn">
                                              <i title="Edit" style="font-size: 1.2em" class="fa fa-edit text-success mx-3"></i>
                                            </a>
                                            <i title="Delete" style="font-size: 1.2em" class="fa fa-trash text-danger mx-3"></i>
                                            <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on text-primary mx-3"></i>
                                          </form>
                                        </div>
                                      </div>
                                      <div class="row px-3 p-0 m-0" style="place-items: self-start;">


                                        <div class="col-md-12">
                                          <div class="shadow-sm py-2 px-2" style="background: #dfdfdf; margin-bottom: 15px;">
                                            POC Details
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Name of Person: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $customer_authorised_person_name ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Designation: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $customer_authorised_person_designation ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Phone: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $customer_authorised_person_phone ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Alt Phone: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $customer_authorised_alt_phone ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Email: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $customer_authorised_person_email ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Alt Email: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $customer_authorised_alt_email ?></span>
                                            </div>
                                          </div>
                                        </div>

                                        <div class="col-md-12">
                                          <div class="shadow-sm py-2 px-2" style="background: #dfdfdf; margin-top: 20px; margin-bottom: 15px;">
                                            Basic Info
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Customer Code: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $row['customer_code'] ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">GSTIN: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $row['customer_gstin'] ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Pan: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $row['customer_pan'] ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Customer Name: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $row['trade_name'] ?> </span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Constitution of Business: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $row['constitution_of_business'] ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Opening Blance: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $row['customer_opening_balance'] ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Credit Period(In Days): </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $row['customer_credit_period'] ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <?php
                                        $sql = "SELECT * FROM " . ERP_CUSTOMER_ADDRESS . " WHERE customer_address_primary_flag=1";
                                        if ($res = $dbCon->query($sql)) {
                                          if ($res->num_rows > 0) {
                                            while ($row = $res->fetch_assoc()) {
                                        ?>

                                              <div class="col-md-6">
                                                <div class="row m-2 py-2 shadow-sm bg-light">
                                                  <div class="col-md-6">
                                                    <span class="font-weight-bold text-secondary">State: </span>
                                                  </div>
                                                  <div class="col-md-6">
                                                    <span><?= $row['customer_address_state'] ?></span>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="col-md-6">
                                                <div class="row m-2 py-2 shadow-sm bg-light">
                                                  <div class="col-md-6">
                                                    <span class="font-weight-bold text-secondary">City: </span>
                                                  </div>
                                                  <div class="col-md-6">
                                                    <span><?= $row['customer_address_city'] ?> </span>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="col-md-6">
                                                <div class="row m-2 py-2 shadow-sm bg-light">
                                                  <div class="col-md-6">
                                                    <span class="font-weight-bold text-secondary">District: </span>
                                                  </div>
                                                  <div class="col-md-6">
                                                    <span><?= $row['customer_address_district'] ?></span>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="col-md-6">
                                                <div class="row m-2 py-2 shadow-sm bg-light">
                                                  <div class="col-md-6">
                                                    <span class="font-weight-bold text-secondary">Location: </span>
                                                  </div>
                                                  <div class="col-md-6">
                                                    <span><?= $row['customer_address_location'] ?> </span>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="col-md-6">
                                                <div class="row m-2 py-2 shadow-sm bg-light">
                                                  <div class="col-md-6">
                                                    <span class="font-weight-bold text-secondary">Building Number: </span>
                                                  </div>
                                                  <div class="col-md-6">
                                                    <span><?= $row['customer_address_building_no'] ?> </span>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="col-md-6">
                                                <div class="row m-2 py-2 shadow-sm bg-light">
                                                  <div class="col-md-6">
                                                    <span class="font-weight-bold text-secondary">Flat Number: </span>
                                                  </div>
                                                  <div class="col-md-6">
                                                    <span><?= $row['customer_address_flat_no'] ?> </span>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="col-md-6">
                                                <div class="row m-2 py-2 shadow-sm bg-light">
                                                  <div class="col-md-6">
                                                    <span class="font-weight-bold text-secondary">Street Name: </span>
                                                  </div>
                                                  <div class="col-md-6">
                                                    <span><?= $row['customer_address_street_name'] ?> </span>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="col-md-6">
                                                <div class="row m-2 py-2 shadow-sm bg-light">
                                                  <div class="col-md-6">
                                                    <span class="font-weight-bold text-secondary">Pin Code: </span>
                                                  </div>
                                                  <div class="col-md-6">
                                                    <span><?= $row['customer_address_pin_code'] ?></span>
                                                  </div>
                                                </div>
                                              </div>

                                        <?php
                                            }
                                          } else {
                                            echo "Data not found";
                                          }
                                        } else {
                                          echo "Somthing went wrong";
                                        }
                                        ?>


                                      </div>
                                    </div>
                                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>
                                  </div>
                                </div>
                              </div>
                              <!--/.Content-->
                            </div>
                          </div>
                          <!-- right modal end here  -->
                        <?php } ?>
                      <tfoot>
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
                      </tfoot>
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
                                Customer Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                Customer PAN</td>
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
  $(document).ready(function() {
    $(document).on("change", "#isGstRegisteredCheckBoxBtn", function() {
      let isChecked = $(this).is(':checked');
      if (isChecked) {
        $("#customerGstNoInput").attr("readonly", "readonly");
        $("#customerPanNo").removeAttr("readonly");

        $.ajax({
          type: "GET",
          url: `${BRANCH_URL}ajaxs/ajax-customer-with-out-verify-gstin.php`,
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
          url: `${BRANCH_URL}ajaxs/ajax-customer-verify-gstin.php?gstin=${customerGstNo}`,
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




    $(document).on("click", ".add_data", function() {
      var data = this.value;
      $("#createdata").val(data);
      // confirm('Are you sure to Submit?')
      $("#add_frm").submit();
    });

    $(document).on("click", ".edit_data", function() {
      var data = this.value;
      $("#editdata").val(data);
      alert(data);
      //$( "#edit_frm" ).submit();
    });



    $(document).on("click", ".js-btn-next", function() {
      console.log("hi there!");
    });

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