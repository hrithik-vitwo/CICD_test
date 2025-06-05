<?php
require_once("../../app/v1/connection-branch-admin.php");

$vendor_code = getRandCodeNotInTable(ERP_VENDOR_DETAILS, 'vendor_code');
if ($vendor_code['status'] == 'success') {
  $vendor_code = $vendor_code['data'];
} else {
  $vendor_code = '';
}
?>

<!--progress bar-->
<div class="row">
  <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
    <div class="multisteps-form__progress">
      <button class="multisteps-form__progress-btn js-active" type="button" title="User Info">Basic Details</button>
      <button class="multisteps-form__progress-btn" type="button" title="Address">Other Business Location</button>
      <button class="multisteps-form__progress-btn" type="button" title="Order Info">Bank Details</button>
      <button class="multisteps-form__progress-btn" type="button" title="Comments">Other Details</button>
    </div>
  </div>
</div>
<!--form panels-->
<div class="row">
  <div class="col-12 col-lg-8 m-auto">
    <form class="multisteps-form__form" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
      <input type="hidden" name="createdata" id="createdata" value="">
      <input type="hidden" name="company_id" id="company_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]; ?>">
      <input type="hidden" name="company_branch_id" id="company_branch_id" value="<?= $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]; ?>">

      <!--single form panel-->
      <div class="multisteps-form__panel shadow p-4 bg-white js-active" data-animation="scaleIn">
        <h4 class="multisteps-form__title">Basic Details</h4>
        <div class="multisteps-form__content">
          <div class="row">
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_code" class="m-input" id="vendor_code" value="<?php echo $vendor_code; ?>" readonly>

                <label>Vendor ID</label>
              </div>

            </div>
            <!-- ************************ -->
            <!-- <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label mr">
              <input class="mdl-textfield__input" value="imran" type="text" id="sample3">
              <label class="mdl-textfield__label" for="sample3">First Name</label>
            </div>
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
              <input class="mdl-textfield__input" type="text" id="sample3">
              <label class="mdl-textfield__label" for="sample3">Last Name</label>
            </div> -->
            <!-- ************************ -->
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_pan" class="m-input" id="vendor_pan">

                <label>Pan *</label>
              </div>

            </div>
            <!-- <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_tan" class="m-input" id="vendor_tan">

                <label>TAN</label>
              </div> -->

            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="trade_name" class="m-input" id="trade_name">
                <label>Trade Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="con_business" class="m-input" id="con_business">
                <label>Constitution of Business</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="state" class="m-input" id="state">
                <label>State</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="city" class="m-input" id="city">
                <label>City</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="district" class="m-input" id="district">
                <label>District</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="location" class="m-input" id="location">
                <label>Location</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="build_no" class="m-input" id="build_no">
                <label>Building Number</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="flat_no" class="m-input" id="flat_no">
                <label>Flat Number</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="street_name" class="m-input" id="street_name">
                <label>Street Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="number" name="pincode" class="m-input" id="pincode">
                <label>Pin Code</label>
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
      <div class="multisteps-form__panel shadow p-4 rounded bg-white step2" data-animation="scaleIn">
        <h4 class="multisteps-form__title">Other Business Location</h4>
        <div class="multisteps-form__content">
          <div class="form-table" id="customFields">
            <a href="javascript:void(0);" class="addCF btn btn-primary btnstyle mb-4">Add</a>
            <div class="row">
              <div class="col-md-6">
                <div class="input-group">
                  <input type="text" name="vendor_business_legal_name[]" class="m-input" id="vendor_business_legal_name">
                  <label>GST Legal Name</label>
                </div>
                <div class="input-group">
                  <input type="text" name="vendor_business_constitution[]" class="m-input" id="vendor_business_constitution">
                  <label>Constitution of Business</label>
                </div>
                <div class="input-group">
                  <input type="text" name="vendor_business_flat_no[]" class="m-input" id="vendor_business_flat_no">
                  <label>Flat Number</label>
                </div>
                <div class="input-group">
                  <input type="text" name="vendor_business_pin_code[]" class="m-input" id="vendor_business_pin_code">
                  <label>Pin Code</label>
                </div>
                <div class="input-group">
                  <input type="text" name="vendor_business_district[]" class="m-input" id="vendor_business_district">
                  <label>District</label>
                </div>
                <div class="input-group">
                  <input type="text" name="vendor_business_location[]" class="m-input" id="vendor_business_location">
                  <label>Location</label>
                </div>
              </div>
              <div class="col-md-6">

                <div class="input-group">
                  <input type="number" name="vendor_business_trade_name[]" class="m-input" id="vendor_business_trade_name">
                  <label>GST Trade Name</label>
                </div>

                <div class="input-group">
                  <input type="text" name="vendor_business_building_no[]" class="m-input" id="vendor_business_building_no">
                  <label>Building Number</label>
                </div>

                <div class="input-group">
                  <input type="text" name="vendor_business_street_name[]" class="m-input" id="vendor_business_street_name">
                  <label>Street Name</label>
                </div>

                <div class="input-group">
                  <input type="text" name="vendor_business_city[]" class="m-input" id="vendor_business_city">
                  <label>City</label>
                </div>

                <div class="input-group">
                  <input type="text" name="vendor_business_state[]" class="m-input" id="vendor_business_state">
                  <label>State</label>
                </div>
              </div>

            </div>
          </div>

          <div class="button-row d-flex mt-4">
            <button class="btn btn-outline-secondary btnstyle js-btn-prev" type="button" title="Prev">Prev</button>
            <button class="btn btn-primary btnstyle ml-auto js-btn-next" type="button" title="Next">Next</button>
          </div>
        </div>
      </div>
      <!--single form panel-->
      <div class="multisteps-form__panel shadow p-4 rounded bg-white" data-animation="scaleIn">
        <h4 class="multisteps-form__title"> Bank Details</h4>
        <div class="multisteps-form__content">
          <div class="row">
            <div class="col-md-6">
              <div class="input-group">
              <input style="display: none;" type="file" name="vendor_bank_cancelled_cheque" class="m-input" id="vendor_bank_cancelled_cheque" placeholder="Upload Cancelled Chaque">
                <label for="vendor_bank_cancelled_cheque" class="btn btn-light text-dark btn-sm"><img width="40" src="../public/assets/img/32173261.jpg" alt=""> Upload Cancled Ckecked <i class="fa fa-upload"></i> </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_bank_ifsc" class="m-input" id="vendor_bank_ifsc">
                <label>IFSC</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_bank_name" class="m-input" id="vendor_bank_name">
                <label>Bank Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_bank_branch" class="m-input" id="vendor_bank_branch">
                <label>Bank Branch Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_bank_address" class="m-input" id="vendor_bank_address">
                <label>Bank Address</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="account_number" class="m-input" id="account_number">
                <label>Bank Account Number</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="button-row d-flex mt-4 col-12">
              <button class="btn btn-outline-secondary js-btn-prev" type="button" title="Prev">Prev</button>
              <button class="btn btn-primary ml-auto js-btn-next" type="button" title="Next">Next</button>
            </div>
          </div>
        </div>
      </div>
      <!--single form panel-->
      <div class="multisteps-form__panel shadow p-4 rounded bg-white" data-animation="scaleIn">
        <h4 class="multisteps-form__title">Other Details</h4>
        <div class="multisteps-form__content">
          <div class="row">
            <div class="col-md-6">
              <div class="input-group">
                <input type="number" name="vendor_opening_balance" class="m-input" id="vendor_opening_balance">
                <label>Opening Blance</label>
              </div>

            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_credit_period" class="m-input" id="vendor_credit_period">
                <label>Creadit Period</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_fssai" class="m-input" id="vendor_fssai">
                <label>FSSAI</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_website" class="m-input" id="vendor_website">
                <label>Website</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_authorised_person_name" class="m-input" id="vendor_authorised_person_name">
                <label>Name of Authorised Person</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="vendor_authorised_person_designation" class="m-input" id="vendor_authorised_person_designation">
                <label>Designation</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="number" name="vendor_authorised_person_phone" class="m-input" id="vendor_authorised_person_phone">
                <label>Phone Number</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="email" name="vendor_authorised_person_email" class="m-input" id="vendor_authorised_person_email">
                <label>Email</label>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <input type="file" name="vendor_picture" class="m-input" id="vendor_picture">
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group">
                <select id="vendor_visible_to_all" name="vendor_visible_to_all" class="select2 form-control form-control-border borderColor">
                  <option value="" selected>Visible For All</option>
                  <option value="No">No</option>
                  <option value="Yes">Yes</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <h4 class="multisteps-form__title">Admin Details</h4>
        <div class="multisteps-form__content">
          <div class="row">

            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="adminName" class="m-input" id="adminName">
                <label>Admin Name</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="adminPhone" class="m-input" id="adminPhone">
                <label>Phone Number</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="adminEmail" class="m-input" id="adminEmail">
                <label>Email</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <input type="text" name="adminPassword" class="m-input" id="adminPassword" value="<?php echo rand(00000, 999999) ?>">
                <label>Login Password</label>
              </div>
            </div>
          </div>
          <div class="button-row d-flex mt-4">
            <button class="btn btn-outline-secondary js-btn-prev" type="button" title="Prev">Prev</button>
            <button class="btn ml-auto btn-danger add_data" type="button" title="Save As Draft" value="add_draft">Save As Draft</button>
            <button class="btn btn-primary ml-auto add_data" type="button" title="Final Submit" value="add_post">Final Submit</button>
          </div>
        </div>
      </div>
    </form>
  </div>
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
