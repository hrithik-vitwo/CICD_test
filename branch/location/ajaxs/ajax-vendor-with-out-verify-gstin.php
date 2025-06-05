<?php
require_once("../../../app/v1/connection-branch-admin.php");
// echo $companyCountry;
?>

<style>
  .cancelled-check-modal .modal-dialog {
    max-width: 800px;
  }

  .cancelled-check-modal .head {
    justify-content: center;
    padding: 7px 0 15px;
    border-bottom: 1px solid #d6d6d6;
  }

  /* 
          .modal-open .modal.cancelled-check-modal {
            backdrop-filter: blur(0px);
          } */

  .drag-file-area i {
    font-size: 20px !important;
    padding: 20px;
    box-shadow: 1px 8px 7px 2px #00000030;
  }

  /* .drag-file-area input {
            opacity: 0;
            height: 53vh !important;
            position: absolute;
            width: 56% !important;
            left: 22%;
            top: 98px;
          } */

  .drag-drop-text p {
    opacity: 33%;
    font-weight: 600;
  }

  .card.check-upload {
    background: #fff;
  }

  .grn-notes {
    padding-left: 0;
    font-weight: 600;
    margin: 20px 40px 15px;
  }

  .grn-notes h4 {
    max-width: 400px !important;
    text-align: left;
  }

  .grn-notes ul li {
    list-style-type: disc;
    margin-left: 1em;
  }

  .grn-notes p {
    font-size: 10px !important;
    line-height: 1.5rem;
    max-width: 400px !important;
    font-size: 12px;
    color: #003060;
    font-weight: 600;
    text-align: left;
  }

  img.check-img {
    max-width: 68%;
    box-shadow: 1px 1px 4px 0px #0000008f;
  }

  .check-sample-section p,
  .check-sample-section hr {
    width: 68%;
    margin: 0 auto;
    font-size: 10px !important;
    font-weight: 600;
  }
</style>


<!--progress bar-->
<div class="row">
  <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
    <div class="multisteps-form__progress">
      <button class="multisteps-form__progress-btn js-active" type="button" title="User Info">Basic Details</button>
      <button class="multisteps-form__progress-btn" type="button" title="Address" disabled>Others Address</button>
      <button class="multisteps-form__progress-btn" type="button" title="Order Info" disabled>Accounting</button>
      <button class="multisteps-form__progress-btn" type="button" title="Comments" disabled>POC Details</button>
    </div>
  </div>
</div>
<!--form panels-->
<div class="row">
  <div class="col-12 col-lg-8 m-auto">
    <form class="multisteps-form__form" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
      <input type="hidden" name="createdata" id="createdata" value="">
      <input type="hidden" name="createtype" id="createtype" value="withoutgst">
      <input type="hidden" name="company_id" id="company_id" value="<?= $company_id; ?>">
      <input type="hidden" name="company_branch_id" id="company_branch_id" value="<?= $branch_id; ?>">
      <input type="hidden" name="company_location_id" id="company_location_id" value="<?= $location_id; ?>">

      <!--single form panel-->
      <div class="multisteps-form__panel js-active" data-animation="scaleIn">
        <div class="card vendor-details-card withOutGST-card">
          <div class="card-header">
            <div class="display-flex">
              <div class="head">
                <i class="fa fa-info"></i>
                <h4>Basic Details</h4>
              </div>
              <!-- <div class="head">
                        <button class="btn btn-primary" id="getGstinReturnFiledStatusBtn" data-gstin="<?= $_GET["gstin"] ?>" style="" data-toggle="modal" data-target="#fluidModalRight"><i class="fa fa-file"></i>&nbsp;&nbsp;GST Filed Status</button>
                      </div> -->
            </div>
          </div>
          <div class="card-body">
            <div class="multisteps-form__content">
              <div class="row">
                <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                          <div class="forform-control">
                            <label for="">GST</label>
                            <input type="text" class="form-control" name="vendor_gstin" id="vendor_gstin" value="<?php echo $_GET["gstin"]; ?>" readonly>
                          </div>
                        </div> -->
                <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="forform-control">
                    <label for="">Pan / TFN*</label>
                    <input type="text" class="form-control vendor_pan" name="vendor_pan" id="vendor_pan" value="">
                    <p id="pan_error"></p>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="forform-control">
                    <label for="">Trade Name *</label>
                    <input type="text" class="form-control" name="trade_name" id="trade_name" value="">
                  </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6" style="display: none;">
                          <div class="forform-control">
                            <label for="">Legal Name</label>
                            <input type="text" class="form-control" name="legal_name" id="legal_name" value="N/A" >
                          </div>
                        </div>

                <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="forform-control">
                    <label for="">Constitution of Business *</label>
                    <input type="text" class="form-control" name="con_business" id="con_business" value="">
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                  <div class="forform-control">
                    <label for="">Flat Number</label>
                    <input type="text" class="form-control" name="flat_no" id="flat_no" value="">
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                  <div class="forform-control">
                    <label for="">Building Number *</label>
                    <input type="text" class="form-control" name="build_no" id="build_no" value="">
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                  <div class="forform-control">
                    <label for="">Street Name *</label>
                    <input type="text" class="form-control" name="street_name" id="street_name" value="">
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                  <div class="forform-control">
                    <label for="">Location *</label>
                    <input type="text" class="form-control" name="location" id="location" value="">
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                  <div class="forform-control">
                    <label for="">City *</label>
                    <input type="text" class="form-control" name="city" id="city" value="">
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                  <div class="forform-control">
                    <label for="">District *</label>
                    <input type="text" class="form-control" name="district" id="district" value="">
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4">
                  <div class="forform-control">
                    <label for="">Pin Code *</label>
                    <input type="number" class="form-control" name="pincode" id="pincode" value="">
                    <small id="pincodeError" style="color: red; display: none;">Please enter a valid pincode.</small>
                    <small id="pincodeError1" style="color: red; display: none;">Pincode not matched with any .</small>
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="forform-control">
                    <label for="">Country</label>
                    <select id="countries" name="countries" class="form-control countriesDropDown">
                      <?php
                      $countries_sql = queryGet("SELECT * FROM `erp_countries`", true);
                      $countries_data = $countries_sql['data'];
                      foreach ($countries_data as $data) {

                      ?>

                        <option value="<?= $data['name'] ?>" <?php if ($data['id'] == $companyCountry) {
                                                                echo "selected";
                                                              } ?> data-attr="<?= $data['id'] ?>"><?= $data['name'] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                    <!-- <input type="text" class="form-control" name="countries" id="countries" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>"> -->
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                  <div class="forform-control stateDivDropDown">
                    <label for="">State</label>
                    <select id="state" name="state" class="form-control secect2 stateDropDown">
                    <option value="" selected disabled>Select State</option>
                      <?php
                      $state_sql = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `country_id` = $companyCountry ", true);
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
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="button-row d-flex mt-2 mb-2">
              <button class="btn btn-primary ml-auto js-btn-next" id="next_first" type="button" title="Next">Next</button>
            </div>
          </div>
        </div>
        <!-- <h4 class="multisteps-form__title">Basic Details</h4> -->
        <!-- <div class="btn btn-primary" id="getGstinReturnFiledStatusBtn" data-gstin="<?= $_GET["gstin"] ?>" style="" data-toggle="modal" data-target="#fluidModalRight">GST Filed Status</div> -->

      </div>
      <!--single form panel-->
      <div class="multisteps-form__panel step2" data-animation="scaleIn">
        <div class="card">
          <div class="card-header">
            <div class="head">
              <h4>Other Address</h4>
            </div>
          </div>
          <div class="card-body">
            <div class="multisteps-form__content">
              <div class="form-table" id="customFields">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-6"></div>
                  <div class="col-lg-6 col-md-6 col-sm-6">
                    <button href="javascript:void(0);" class="addCF btn btn-primary float-right"><i class="fa fa-plus" style="margin-right: 0;"></i></button>
                  </div>
                  <div class="col-md-6">
                    <div class="form-input">
                      <label>Flat Number</label>
                      <input type="text" name="vendorOtherAddress[0][vendor_business_flat_no]" class="form-control" id="vendor_business_flat_no">

                    </div>
                    <div class="form-input">
                      <label>Pin Code</label>

                      <input type="text" name="vendorOtherAddress[0][vendor_business_pin_code]" class="form-control" id="vendor_business_pin_code">
                      <small id="pincodeError2" style="color: red; display: none;">Please enter a valid pincode.</small>
                      <small id="pincodeError3" style="color: red; display: none;">Pincode not matched with any </small>
                    </div>
                    <div class="form-input">
                      <label>District</label>

                      <input type="text" name="vendorOtherAddress[0][vendor_business_district]" class="form-control" id="vendor_business_district">
                    </div>
                    <div class="form-input">
                      <label>Location</label>

                      <input type="text" name="vendorOtherAddress[0][vendor_business_location]" class="form-control" id="vendor_business_location">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-input">
                      <label>Building Number</label>

                      <input type="text" name="vendorOtherAddress[0][vendor_business_building_no]" class="form-control" id="vendor_business_building_no">
                    </div>

                    <div class="form-input">
                      <label>Street Name</label>

                      <input type="text" name="vendorOtherAddress[0][vendor_business_street_name]" class="form-control" id="vendor_business_street_name">
                    </div>

                    <div class="form-input">
                      <label>City</label>

                      <input type="text" name="vendorOtherAddress[0][vendor_business_city]" class="form-control" id="vendor_business_city">
                    </div>
                    <div class="loop-div">
                      <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                          <div class="form-input">
                            <label>Country</label>
                            <select name="vendorOtherAddress[0][vendor_business_country]" class="form-control countriesDropDownloop">
                              <?php
                              foreach ($countries_data as $data) {
                              ?>
                                <option value="<?= $data['name'] ?>" <?php if ($data['id'] == $companyCountry) {
                                                                        echo "selected";
                                                                      } ?> data-attr="<?= $data['id'] ?>"><?= $data['name'] ?></option>
                              <?php
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                          <div class="form-input stateDropDownloop">
                            <label>State</label>
                            <select name="vendorOtherAddress[0][vendor_business_state]" class="form-control secect2"  id="otherstate" >
                              <option value="" selected disabled>Select State</option>
                              <?php
                              foreach ($state_data as $data) {
                              ?>
                                <option value="<?= $data['gstStateName'] ?>" <?php if ($data['gstStateName'] == $gstDetails['pradr']['addr']['stcd']) {
                                                                                echo "selected";
                                                                              } ?> data-attr="<?= $data['id'] ?>"><?= $data['gstStateName'] ?></option>
                              <?php
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>


                  </div>
                </div>
              </div>


            </div>
          </div>
          <div class="card-footer">
            <div class="button-row d-flex mt-2 mb-2">
              <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
              <button class="btn btn-primary ml-auto js-btn-next" type="button" data-toggle="modal" data-target="#checkUpload" title="Next">Next</button>
            </div>
          </div>
        </div>
      </div>
      <!--single form panel-->
      <div class="modal fade cancelled-check-modal" id="checkUpload" style="z-index: 999999;" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content m-auto" style="border-radius: 20px;">
            <div class="modal-body p-0" style="overflow: hidden; border-radius: 20px;">
              <div id="uploadGrnInvoiceDiv" class="create-grn">
                <div class="upload-files-container">
                  <div class="card check-upload">
                    <div class="card-body">
                      <div class="head text-center">
                        <h4 class="mb-0">Upload Cancel Check</h4>
                      </div>
                      <div class="drag-file-area">
                        <i class="fa fa-file-upload po-list-icon text-center m-auto"></i>
                        <br>

                        <div class="drag-drop-text mb-5 mt-4">
                          <!-- <p class="text-sm"> Drag & Drop Cancelled Check here</p> -->
                          <div class="check-sample-section mt-4">
                            <p class="text-xs text-left">Check Sample :</p>
                            <hr class="mt-1 mb-2">
                            <img class="check-img" src="../../public/assets/img/cheque-book.jpg" alt="check-sample">
                          </div>
                        </div>





                        <!-- <div class="notes">
                                  <ul>
                                    <p class="font-bold text-sm">Notes:</p>
                                    <li>
                                        <p class="text-xs">Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
                                    </li>
                                    <li>
                                        <p class="text-xs">Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
                                    </li>
                                    <li>
                                        <p class="text-xs">Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
                                    </li>
                                  </ul>
                                </div> -->
                        <!-- <div class="upload-btn m-auto mt-3 mb-3">
                                  <button class="btn btn-primary upload" id="invoiceFileInput">Upload</button>
                                </div> -->
                        <input type="file" class="form-control" id="invoiceFileInput" name="" placeholder="Invoice Upload" required />

                        <div class="file-block">
                          <div class="progress-bar"> </div>
                        </div>
                        <button type="button" class="upload-button btn btn-primary vendor_bank_cancelled_cheque_btn mt-4 mb-2" name="" id="vendor_bank_cancelled_cheque_btn" disabled> Upload </button>

                      </div>

                      <div class="grn-notes">
                        <h4 class="text-xs">Note:</h4>
                        <hr>
                        <ul class="pl-0 mb-0">
                          <li>
                            <p class="text-xs">You can upload Cancelled Check here</p>
                          </li>
                          <li>
                            <p class="text-xs">Your maximum file size should be <span class="font-bold text-xs">2 mb/file</span></p>
                          </li>

                        </ul>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="multisteps-form__panel" data-animation="scaleIn">
        <div class="card">
          <div class="card-header">
            <div class="head">
              <h4>
                Accounting
              </h4>
            </div>
          </div>
          <div class="card-body">
            <div class="multisteps-form__content">
              <div class="row">
                <input step="0.01" type="hidden" class="form-control" name="opening_balance" id="vendor_opening_balance" value="0">

                <div class="col-md-6">
                  <div class="form-input">
                    <label for="">Company Currency</label>
                    <select id="company_currency" name="currency" class="form-control mt-0">
                      <?php
                      $listResult = getAllCurrencyType();
                      if ($listResult["status"] == "success") {
                        foreach ($listResult["data"] as $listRow) {
                      ?>
                          <option value="<?php echo $listRow['currency_id']; ?>"  <?php if($listRow['currency_id'] == $company_currency) { echo 'selected'; } ?>><?php echo $listRow['currency_name']; ?></option>
                      <?php }
                      } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Credit Period(In Days)</label>
                    <input type="text" class="form-control" name="credit_period" id="vendor_credit_period" value="">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label for="vendor_bank_cancelled_cheque"> Upload Cancled Cheque <span class="Ckecked_loder"></span> </label>
                    <input class="vendor_bank_cancelled_cheque form-control" type="file" name="vendor_bank_cancelled_cheque" id="vendor_bank_cancelled_cheque">
                    <small id="file_name_display" class="text-muted mt-1 d-block"></small>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-input">
                    <label>IFSC/BSB</label>
                    <input type="text" class="form-control IFSClass" name="vendor_bank_ifsc" id="vendor_bank_ifsc" value="">
                    <div>
                      <span style="font-size: 0.7em; " class="tick-icon"></span>
                      <span class="text-xs" id="ifscCodeMsg"></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Bank Name</label>
                    <input type="text" class="form-control" name="vendor_bank_name" id="vendor_bank_name" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Bank Branch Name</label>
                    <input type="text" class="form-control" name="vendor_bank_branch" id="vendor_bank_branch" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Bank Address</label>
                    <input type="text" class="form-control" name="vendor_bank_address" id="vendor_bank_address" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Bank Account Number</label>
                    <input type="text" class="form-control account_number" name="vendor_bank_account_no" id="account_number" value="">

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Bank Account Holder *</label>
                    <input type="text" class="form-control" name="account_holder" id="account_holder" value="">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-input">
                    <label id="bank_detail_error"></label>
                    
                  </div>
                </div>


              </div>

            </div>
          </div>
          <div class="card-footer">
            <div class="row">
              <div class="button-row d-flex mt-2 mb-2">
                <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
                <button class="btn btn-primary ml-auto js-btn-next" id="next_last" type="button" data-toggle="modal" data-target="#visitingCard" title="Next">Next</button>
              </div>
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
                      <button type="button" class="upload-button btn btn-primary visiting_card_btn" name="" id="visiting_card_btn" disabled> Upload </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="multisteps-form__panel" data-animation="scaleIn">

        <div class="card">
          <div class="card-header">
            <div class="head">
              <h4>POC Details</h4>
            </div>
          </div>
          <div class="card-body">
            <div class="multisteps-form__content">
              <div class="modal" id="checkupload"></div>
              <div class="row">

                <div class="col-md-12">
                  <div class="form-input">
                    <label for="visiting_card"> Upload Visiting Card <span class="visiting_loder"></span></label>
                    <input class="visiting_card form-control" type="file" name="visiting_card" id="visiting_card">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-input">
                    <label>Name of Person*</label>
                    <input type="text" class="form-control" name="vendor_authorised_person_name" id="adminName" value="">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Designation *</label>
                    <input type="text" class="form-control" name="vendor_authorised_person_designation" id="vendor_authorised_person_designation" value="">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Phone Number*</label>
                    <input type="text" class="form-control" name="vendor_authorised_person_phone" id="adminPhone" value="">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Alternative Phone </label>
                    <input type="number" class="form-control" name="vendor_authorised_alt_phone" id="vendor_authorised_person_phone" value="">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Email*</label>
                    <input type="text" class="form-control" name="vendor_authorised_person_email" id="adminEmail" value="">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-input">
                    <label>Alternative Email</label>
                    <input type="email" class="form-control" name="vendor_authorised_alt_email" id="vendor_authorised_person_email" value="">
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
                    <label for="">Vendor Picture</label>
                    <input type="file" class="form-control" name="vendor_picture" id="vendor_picture">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-input">
                    <label for="">Visible For All</label>
                    <select id="vendor_visible_to_all" name="vendor_visible_to_all" class="select2 form-control mt-0 form-control-border borderColor">
                     
                      <option value="No">No</option>
                      <option value="Yes" selected>Yes</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <div class="button-row d-flex mt-2 mb-2">
              <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
              <button class="btn ml-auto btn-danger add_data" type="button" title="Save As Draft" value="add_draft">Save As Draft</button>
              <button class="btn btn-primary ml-auto add_data" type="button" title="Final Submit" value="add_post" id="vendorCreateBtn">Final Submit</button>
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