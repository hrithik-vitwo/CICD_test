<?php
require_once("../../../app/v1/connection-branch-admin.php");
global $companyCountry;
$componentsjsn = json_decode(getLebels($companyCountry)['data'], true);
// console($componentsjsn);
?>
<!--progress bar-->
<div class="row">
  <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
    <div class="multisteps-form__progress">
      <button class="multisteps-form__progress-btn js-active" type="button" title="User Info">Basic Details</button>
      <button class="multisteps-form__progress-btn" type="button" title="Comments" id="poc_btn" disabled>POC Details</button>
    </div>
  </div>
</div>
<!--form panels-->
<div class="row">
  <div class="col-12 col-lg-8 m-auto">
    <form class="multisteps-form__form" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
      <input type="hidden" name="createdata" id="createdata" value="">
      <input type="hidden" name="company_id" id="company_id" value="<?= $company_id; ?>">
      <input type="hidden" name="company_branch_id" id="company_branch_id" value="<?= $branch_id; ?>">

      <!--single form panel-->
      <div class="multisteps-form__panel js-active" data-animation="scaleIn">
        <div class="card vendor-details-card withOutGST-card mb-0">
          <div class="card-header p-3">
            <div class="display-flex">
              <div class="head">
                <i class="fa fa-user"></i>
                <h4>Basic Details</h4>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="multisteps-form__content">
              <div class="row">

                <!-- <div class="col-md-6">
                            <div class="form-input">
                              <label>GSTIN</label>
                              <input type="text" class="form-control" name="customer_gstin" id="customer_gstin" value="" readonly>

                            </div>

                          </div> -->
                <div class="col-md-6">
                  <div class="form-input">
                    <label><?=$componentsjsn['fields']['taxNumber']?> *</label>
                    <input type="text" class="form-control customer_pan customer_pan_without_gst" name="customer_pan" id="customer_pan" value="">
                    <p id="pan_error"></p>

                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-input">
                    <label><?=$componentsjsn['fields']['businessTaxID']?> </label>
                    <input type="text" class="form-control customer_gst customer_gst_without_gst" name="customer_gst" id="customer_gst" value="">
                    <p id="gst_error"></p>

                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-input">
                    <label>Customer Name*</label>
                    <input type="text" class="form-control" name="trade_name" id="trade_name" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Constitution of Business</label>
                    <input type="text" class="form-control" name="con_business" id="con_business" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Building Number*</label>
                    <input type="text" class="form-control" name="build_no" id="build_no" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Flat Number</label>
                    <input type="text" class="form-control" name="flat_no" id="flat_no" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Street Name*</label>
                    <input type="text" class="form-control" name="street_name" id="street_name" value="">

                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-input">
                    <label>City*</label>
                    <input type="text" class="form-control" name="city" id="city" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>District*</label>
                    <input type="text" class="form-control" name="district" id="district" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Location*</label>
                    <input type="text" class="form-control" name="location" id="location" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Pin Code*</label>
                    <input type="number" class="form-control" name="pincode" id="pincode" value="">
                    <small id="pincodeError" style="color: red; display: none;">Please enter a valid pincode.</small>
                    <small id="pincodeError1" style="color: red; display: none;">Pincode not matched with any .</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="forform-control">
                    <label for="">Country</label>
                    <select id="countries" name="countries" class="form-control countriesDropDown">
                      <?php
                      $countries_sql = queryGet("SELECT * FROM `erp_countries`", true);
                      $countries_data = $countries_sql['data'];
                      foreach ($countries_data as $data) {

                      ?>

                        <option value="<?= $data['name'] ?>" <?php if ($data['id'] == $companyCountry ) {
                                                                echo "selected";
                                                              } ?> data-attr="<?= $data['id'] ?>"><?= $data['name'] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                    <!-- <input type="text" class="form-control" name="countries" id="countries" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>"> -->
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="forform-control stateDivDropDown">
                    <label for="">State</label>
                    <select id="state" name="state" class="form-control secect2 stateDropDown">
                    <option value="" selected disabled>Select State</option>
                      <?php
                      $state_sql = queryGet("SELECT * FROM `erp_gst_state_code`  WHERE `country_id` = $companyCountry ", true);
                      $state_data = $state_sql['data'];
                      foreach ($state_data as $data) {

                      ?>

                        <option value="<?= $data['gstStateName'] ?>" <?php if ($data['gstStateName'] == $gstDetails['pradr']['addr']['stcd']) {
                                                                        // echo "selected";
                                                                      } ?>><?= $data['gstStateName'] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                    <!-- <input type="text" class="form-control" name="state" id="state" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>"> -->
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label for="">Company currency</label>
                    <select id="company_currency" name="currency" class="form-control mt-0 form-control-border borderColor">
                      <!--<option value="">Select Currency</option>-->
                      <?php
                      $listResult = getAllCurrencyType();
                      if ($listResult["status"] == "success") {
                        foreach ($listResult["data"] as $listRow) {
                      ?>
                          <option value="<?php echo $listRow['currency_id']; ?>" <?php if($listRow['currency_id'] == $company_currency) { echo 'selected';} ?>><?php echo $listRow['currency_name']; ?></option>
                      <?php }
                      } ?>
                    </select>
                  </div>
                </div>
                <input step="0.01" type="hidden" class="form-control" name="opening_balance" id="customer_opening_balance" value="0">


                <div class="col-md-6">
                  <div class="form-input">
                    <label>Credit Period(In Days)*</label>
                    <input type="text" class="form-control" name="credit_period" id="customer_credit_period" value="">

                  </div>
                </div>

                <div class="col-md-6" >
                            <div class="form-input">
                            <label for="">Discount Group</label>
                            <select id="discount_group" name="discount_group" class="form-control mt-0 form-control-border borderColor">
                                <option value="">Select Customer Discount Group</option>
                                <?php
                                $discountGroups = queryGet("SELECT * FROM `erp_customer_discount_group` WHERE company_id = $company_id",true);
                                if ($discountGroups["status"] == "success") {
                                  foreach ($discountGroups["data"] as $discountGroup) {
                                ?>
                                    <option value="<?php echo $discountGroup['customer_discount_group_id']; ?>"><?php echo $discountGroup['customer_discount_group']; ?></option>
                                <?php }
                                } ?>
                              </select>
                            </div>
                          </div>

                

              	<div class="col-md-6" >
                            <div class="form-input">
                            <label for="">Customer Mrp Group</label>
                            <select id="customer_mrp_group" name="customer_mrp_group" class="form-control mt-0 form-control-border borderColor">
                                <option value="">Select Customer Mrp Group</option>
                                <?php
                                $customerMrpGroup = queryGet("SELECT customer_mrp_group_id,customer_mrp_group FROM `erp_customer_mrp_group` WHERE company_id=$company_id",true);
                                if ($customerMrpGroup["status"] == "success") {
                                  foreach ($customerMrpGroup["data"] as $data) {
                                ?>
                                    <option value="<?php echo $data['customer_mrp_group_id']; ?>"><?php echo $data['customer_mrp_group']; ?></option>
                                <?php }
                                } ?>
                              </select>
                            </div>
                          </div>
                        
              </div>


              

                

                        

            </div>
          </div>
          <div class="card-footer">
            <div class="button-row d-flex">
              <button class="btn btn-primary ml-auto js-btn-next" id="customerRegFrmNextBtn" type="button" data-toggle="modal" data-target="#visitingCard" title="Next">Next</button>
            </div>
          </div>
        </div>
      </div>
      <!--single form panel-->
      <div class="modal fade" id="visitingCard" style="z-index: 999999;" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content m-auto" style="max-width: 375px; border-radius: 20px;">

            <div class="modal-body p-0" style="overflow: hidden; border-radius: 20px;">
              <div id="uploadGrnInvoiceDiv" class="create-grn">
                <div class="upload-files-container">
                  <div class="card visiting-card-upload">
                    <div class="card-header">
                      <div class="head">
                        <h4>Upload Visiting Card</h4>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="drag-file-area">
                        <i class="fa fa-arrow-up po-list-icon text-center m-auto"></i>
                        <br>
                        <input type="file" class="form-control" id="visitingFileInput" name="" placeholder="Visiting Card Upload" required />
                      </div>
                      <div class="file-block">
                        <div class="progress-bar"> </div>
                      </div>
                      <button type="button" class="upload-button btn btn-primary visiting_card_btn" name="" id="visiting_card_btn"> Upload </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="multisteps-form__panel" data-animation="scaleIn">

        <div class="card vendor-details-card withOutGST-card mb-0">
          <div class="card-header">
            <div class="head">
              <h4>POC Details</h4>
            </div>
          </div>
          <div class="card-body">
            <div class="multisteps-form__content">
              <div class="row">
                <div class="col-md-12">
                  <label for="">Upload Visiting Card<span class="visiting_loder"></span></label>
                  <input class="visiting_card form-control" type="file" name="visiting_card" id="visiting_card">
                </div>

                <div class="col-md-6">
                  <div class="form-input">
                    <label>Name of Person*</label>
                    <input type="text" class="form-control" name="customer_authorised_person_name" id="adminName" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Designation*</label>
                    <input type="text" class="form-control" name="customer_authorised_person_designation" id="customer_authorised_person_designation" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Phone Number*</label>
                    <input type="text" class="form-control" name="customer_authorised_person_phone" id="adminPhone" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Alternative Phone </label>
                    <input type="text" class="form-control" name="customer_authorised_alt_phone" id="customer_authorised_person_phone" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Email*</label>
                    <input type="email" class="form-control" name="customer_authorised_person_email" id="adminEmail" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Alternative Email</label>
                    <input type="email" class="form-control" name="customer_authorised_alt_email" id="customer_authorised_person_email" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Login Password [Will be send to the POC email]</label>
                    <input type="text" class="form-control" name="adminPassword" id="adminPassword" value="<?php echo rand(00000, 999999) ?>">

                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-input">
                    <label for="">Choose Image</label>
                    <input type="file" class="form-control" name="customer_picture" id="customer_picture">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-input">
                    <label for="" style="visibility: hidden;">Visible for all</label>
                    <select id="customer_visible_to_all" name="customer_visible_to_all" class="select2 form-control mt-0 borderColor">
                      <option value="No"> Only for this location</option>
                      <option value="Yes" selected>Visible For All</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="button-row d-flex">
              <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
              <button class="btn ml-auto btn-danger add_data" type="button" title="Save As Draft" value="add_draft">Save As Draft</button>
              <button id="customerCreateBtn" class="btn btn-primary ml-auto add_data" type="button" title="Final Submit" value="add_post">Final Submit</button>
            </div>
          </div>
        </div>

      </div>
    </form>
  </div>
</div>
<script src="https://code.getmdl.io/1.2.0/material.min.js"></script>
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