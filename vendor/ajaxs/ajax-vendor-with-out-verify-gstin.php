<!--progress bar-->
<div class="row">
                  <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
                    <div class="multisteps-form__progress">
                      <button class="multisteps-form__progress-btn js-active" type="button" title="User Info">Basic Details</button>
                      <button class="multisteps-form__progress-btn" type="button" title="Address">Other Business Address</button>
                      <button class="multisteps-form__progress-btn" type="button" title="Order Info">Bank Details</button>
                      <button class="multisteps-form__progress-btn" type="button" title="Comments">Other Details</button>
                    </div>
                  </div>
                </div>
                <!--form panels-->
                <div class="row">
                  <div class="col-12 col-lg-8 m-auto">
                    <form class="multisteps-form__form">
                      <!--single form panel-->
                      <div class="multisteps-form__panel shadow p-4 bg-white js-active" data-animation="scaleIn">
                        <h4 class="multisteps-form__title">Basic Details</h4>
                        <div class="multisteps-form__content">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="vid" class="m-input" id="vid">

                                <label>Vendor ID</label>
                              </div>

                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="number" name="op_blance" class="m-input" id="op_blance">

                                <label>Opening Blance</label>
                              </div>

                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="pan" class="m-input" id="pan">

                                <label>Pan *</label>
                              </div>

                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="tan" class="m-input" id="tan">

                                <label>TAN</label>
                              </div>

                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="name" class="m-input" id="name">

                                <label>Name</label>
                              </div>

                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="email" class="m-input" id="email">
                                <label>Email</label>
                              </div>
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
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="location" class="m-input" id="location">
                                <label>Location</label>
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
                                <input type="text" name="state" class="m-input" id="state">
                                <label>State</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="al_email" class="m-input" id="al_email">
                                <label>Alternate Email</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="status" class="m-input" id="status">
                                <label>Status</label>
                              </div>
                            </div>
                          </div>
                          <div class="button-row d-flex mt-4">
                            <div>
                              <span>Back </span>
                              <div class="icheck-primary d-inline ml-2">
                                <input type="checkbox" id="checkbox2" class="checkbox2">
                                <label for="checkbox2">
                                </label>
                              </div>
                            </div>
                            <button class="btn btn-primary ml-auto js-btn-next" type="button" title="Next">Next</button>
                          </div>
                        </div>
                      </div>
                      <!--single form panel-->
                      <div class="multisteps-form__panel shadow p-4 rounded bg-white step2" data-animation="scaleIn">
                        <h4 class="multisteps-form__title">Other Business Address</h4>
                        <div class="multisteps-form__content">
                          <div class="form-table" id="customFields">
                            <a href="javascript:void(0);" class="addCF btn btn-primary btnstyle mb-4">Add</a>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="input-group">
                                  <input type="text" name="legal_name" class="m-input" id="legal_name">
                                  <label>GST Legal Name</label>
                                </div>
                                <div class="input-group">
                                  <input type="text" name="ct_business" class="m-input" id="ct_business">
                                  <label>Constitution of Business</label>
                                </div>
                                <div class="input-group">
                                  <input type="text" name="flat_number" class="m-input" id="flat_number">
                                  <label>Flat Number</label>
                                </div>
                                <div class="input-group">
                                  <input type="text" name="pin_code" class="m-input" id="pin_code">
                                  <label>Pin Code</label>
                                </div>
                                <div class="input-group">
                                  <input type="text" name="district" class="m-input" id="district">
                                  <label>District</label>
                                </div>
                                <div class="input-group">
                                  <input type="text" name="al_email" class="m-input" id="al_email">
                                  <label>Alternate Email</label>
                                </div>
                              </div>
                              <div class="col-md-6">

                                <div class="input-group">
                                  <input type="number" name="trade_name" class="m-input" id="op_blance">
                                  <label>GST Trade Name</label>
                                </div>

                                <div class="input-group">
                                  <input type="text" name="bd_no" class="m-input" id="tan">
                                  <label>Building Number</label>
                                </div>

                                <div class="input-group">
                                  <input type="text" name="st_name" class="m-input" id="email">
                                  <label>Street Name</label>
                                </div>

                                <div class="input-group">
                                  <input type="text" name="city" class="m-input" id="city">
                                  <label>City</label>
                                </div>

                                <div class="input-group">
                                  <input type="text" name="state" class="m-input" id="state">
                                  <label>State</label>
                                </div>

                                <div class="input-group">
                                  <input type="text" name="status" class="m-input" id="status">
                                  <label>Status</label>
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
                                <input type="text" name="ifsc" class="m-input" id="ifsc">
                                <label>IFSC</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="bank_name" class="m-input" id="bank_name">
                                <label>Bank Name</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="branch_name" class="m-input" id="branch_name">
                                <label>Bank Branch Name</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="bank_address" class="m-input" id="bank_address">
                                <label>Bank Address</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="account_number" class="m-input" id="account_number">
                                <label>Bank Account Number</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="file" name="chaque" class="m-input" id="chaque" placeholder="Upload Cancelled Chaque">
                                <label></label>
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
                                <input type="text" name="fssai" class="m-input" id="fssai">
                                <label>FSSAI</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="creadit" class="m-input" id="creadit">
                                <label>Creadit Period</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="auth_person" class="m-input" id="auth_person">
                                <label>Name of Authorised Person</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="al_email" class="m-input" id="al_email">
                                <label>Alternate Email</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="phone_no" class="m-input" id="phone_no">
                                <label>Phone Number</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="website" class="m-input" id="website">
                                <label>Website</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="picture" class="m-input" id="picture">
                                <label>Picture</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="designation" class="m-input" id="designation">
                                <label>Designation</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <select id="" name="goodsType" class="select2 form-control form-control-border borderColor">
                                  <option value="">Enabled</option>
                                  <option value="A">Yes</option>
                                  <option value="B">No</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="button-row d-flex mt-4">
                            <button class="btn btn-outline-secondary js-btn-prev" type="button" title="Prev">Prev</button>
                            <button class="btn btn-primary ml-auto" type="button" title="Send">Submit</button>
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
stepNextBtnClass: 'js-btn-next' };


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
if (!(eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`) || eventTarget.classList.contains(`${DOMstrings.stepNextBtnClass}`)))
{
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