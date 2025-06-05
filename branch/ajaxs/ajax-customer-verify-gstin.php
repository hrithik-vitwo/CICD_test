<?php
require_once("../../app/v1/connection-branch-admin.php");

$customer_code = getRandCodeNotInTable(ERP_CUSTOMER, 'customer_code');
if ($customer_code['status'] == 'success') {
  $customer_code = $customer_code['data'];
} else {
  $customer_code = '';
}
$headerData = array('Content-Type: application/json');
$postData = array(
  "username" => "rbajoria@vitwo.in",
  "password" => "Vitwo@123",
  "client_id" => "ifYTepjBvEWpzUCKji",
  "client_secret" => "0Z6ebVPQ5NplrfZ98BI1mF56",
  "grant_type" => "password"
);

$url_name = "https://commonapi.mastersindia.co/oauth/access_token";
$curl = curl_init();
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_URL, $url_name);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($curl, CURLOPT_HTTPHEADER, $headerData);

$result = curl_exec($curl);

try {
  $resultData = json_decode($result, true);
  if (isset($resultData["access_token"]) && !empty($resultData["access_token"])) {

    $curlGstHeaderData = array('Content-Type: application/json', 'Authorization: Bearer ' . $resultData["access_token"], 'client_id: ifYTepjBvEWpzUCKji');

    if (isset($_GET["gstin"]) && !empty($_GET["gstin"])) {
      $url_name = "https://commonapi.mastersindia.co/commonapis/searchgstin?gstin=" . $_GET["gstin"];
      $curlGst = curl_init();
      curl_setopt($curlGst, CURLOPT_URL, $url_name);
      curl_setopt($curlGst, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curlGst, CURLOPT_HTTPHEADER, $curlGstHeaderData);

      $resultGst = curl_exec($curlGst);

      $resultGstData = json_decode($resultGst, true);
      /*echo '<pre>';
            print_r($resultGstData);
            exit;*/
      try {
        $resultGstData = json_decode($resultGst, true);
        if (isset($resultGstData["error"]) && $resultGstData["error"] != "false") {
          $gstDetails = $resultGstData["data"];
          $customerPan = substr($_GET["gstin"], 2, 10);
          $othersaddress_count = count($resultGstData['data']['adadr']);
          if (empty($gstDetails['pradr']['addr']['city'])) {
            $city =  $gstDetails['pradr']['addr']['loc'];
          } else {
            $city = $gstDetails['pradr']['addr']['city'];
          }

?>
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
                        <div class="material-textfield">
                          <input type="text" name="customer_code" id="customer_code" value="<?php echo $customer_code; ?>" readonly>
                          <label>Customer Code</label>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="customer_gstin" id="customer_gstin" value="<?php echo $_GET["gstin"]; ?>" readonly>

                          <label>GSTIN</label>
                        </div>

                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="customer_pan" id="customer_pan" value="<?php echo $customerPan; ?>">
                          <label>Pan *</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="trade_name" id="trade_name" value="<?php echo $gstDetails['tradeNam']; ?>">
                          <label>Customer Name</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="con_business" id="con_business" value="<?php echo $gstDetails['ctb']; ?>">
                          <label>Constitution of Business</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="state" id="state" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>">
                          <label>State</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="city" id="city" value="<?php echo $city; ?>">
                          <label>City</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="district" id="district" value="<?php echo $gstDetails['pradr']['addr']['dst']; ?>">
                          <label>District</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="location" id="location" value="<?php echo $gstDetails['pradr']['addr']['loc']; ?>">
                          <label>Location</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="build_no" id="build_no" value="<?php echo $gstDetails['pradr']['addr']['bno']; ?>">
                          <label>Building Number</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="flat_no" id="flat_no" value="<?php echo $gstDetails['pradr']['addr']['flno']; ?>">
                          <label>Flat Number</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="street_name" id="street_name" value="<?php echo $gstDetails['pradr']['addr']['st']; ?>">
                          <label>Street Name</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="number" name="pincode" id="pincode" value="<?php echo $gstDetails['pradr']['addr']['pncd']; ?>">
                          <label>Pin Code</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="number" name="opening_balance" id="customer_opening_balance" value="">
                          <label>Opening Blance</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <select id="company_currency" name="currency" class="form-control form-control-border borderColor">
                            <!--<option value="">Select Currency</option>-->
                            <?php
                            $listResult = getAllCurrencyType();
                            if ($listResult["status"] == "success") {
                              foreach ($listResult["data"] as $listRow) {
                            ?>
                                <option value="<?php echo $listRow['currency_id']; ?>"><?php echo $listRow['currency_name']; ?></option>
                            <?php }
                            } ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="credit_period" id="customer_credit_period" value="">
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
                          <input type="text" name="customer_authorised_person_name" id="adminName" value="">
                          <label>Name of Person*</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="customer_authorised_person_designation" id="customer_authorised_person_designation" value="">
                          <label>Designation</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="customer_authorised_person_phone" id="adminPhone" value="">
                          <label>Phone Number*</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="number" name="customer_authorised_alt_phone" id="customer_authorised_person_phone" value="">
                          <label>Alternative Phone </label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="customer_authorised_person_email" id="adminEmail" value="">
                          <label>Email*</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="email" name="customer_authorised_alt_email" id="customer_authorised_person_email" value="">
                          <label>Alternative Email</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="material-textfield">
                          <input type="text" name="adminPassword" id="adminPassword" value="<?php echo rand(00000, 999999) ?>">
                          <label>Login Password [Will be send to the POC email]</label>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="material-textfield">
                          <input type="file" name="customer_picture" id="customer_picture">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="material-textfield">
                          <select id="customer_visible_to_all" name="customer_visible_to_all" class="select2 form-control form-control-border borderColor">
                            <option value="" selected>Visible For All</option>
                            <option value="No">No</option>
                            <option value="Yes" selected>Yes</option>
                          </select>
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
<?php
        } else {
          swalToast("warning", "Something went wrong try again!");
        }
      } catch (Exception $ee) {
        swalToast("warning", "Something went wrong try again!");
      }
    } else {
      swalToast("warning", "Please provide valid gstin number!");
    }
  } else {
    swalToast("warning", "Something went wrong try again with valid credentials!");
  }
} catch (Exception $e) {
  swalToast("warning", "Something went wrong try again to auth!");
}
curl_close($curl);
