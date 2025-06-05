<?php
require_once("../../../../../app/v1/connection-branch-admin.php");

?>

<!-- <script src="../../../validation/add-customer-modal.js"></script> -->
<script src="../../api/v2/proccess/validation/add-customer-modal.js"></script>




<div class="modal-dialog">
    <div class="modal-content card">
        <div class="modal-header card-header py-2 px-3">
            <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Customer</h4>
            <button type="button" class="close text-white" data-dismiss="modal" id="addCustomerCloseBtn" aria-label="Close">x</button>
        </div>
        <div id="notesModalBody" class="modal-body card-body">
            <div class="row">
                <div class="col-12 col-lg-8 ml-auto mr-auto mb-4">
                    <div class="multisteps-form__progress">
                        <button class="multisteps-form__progress-btn js-active" type="button" title="User Info">Basic Details</button>
                        <button class="multisteps-form__progress-btn" type="button" title="Comments" id="poc_btn" disabled="">POC Details</button>
                    </div>
                </div>
            </div>
            <!--form panels-->
            <div class="row">
                <div class="col-12 col-lg-8 m-auto">
                    <form class="multisteps-form__form" action="" method="POST" id="add_frm" name="add_frm" style="height: 0px;">
                        <input type="hidden" name="createdatamultiform" id="createdatamultiform" value="">
                        <input type="hidden" name="company_id" id="company_id" value="1">
                        <input type="hidden" name="company_branch_id" id="company_branch_id" value="1">

                        <!--single form panel-->
                        <div class="multisteps-form__panel js-active" data-animation="scaleIn">
                            <div class="card vendor-details-card mb-0">
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

                                            <div class="col-md-6">
                                                <div class="form-input">
                                                    <label>GSTIN</label>
                                                    <input type="text" class="form-control" name="customer_gstin" id="customer_gstin" value="">
                                                </div>

                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-input">
                                                    <label>Pan *</label>
                                                    <input type="text" class="form-control" name="customer_pan" id="customer_pan" value="">

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-input">
                                                    <label>Customer Name</label>
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
                                                <div class="form-input selDiv">
                                                    <label>State</label>
                                                    <select id="state" name="state" class="form-control stateDropDown">
                                                        <?php
                                                        $stateNameList = fetchStateName()['data'];

                                                        usort($stateNameList, 'compareByStateCode');
                                                        foreach ($stateNameList as $one) {
                                                        ?>
                                                            <option value="<?= ($one['gstStateName']) ?>"><?= $one['gstStateCode'] ?> - <?= $one['gstStateName'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-input">
                                                    <label>City</label>
                                                    <input type="text" class="form-control" name="city" id="city" value="">

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-input">
                                                    <label>District</label>
                                                    <input type="text" class="form-control" name="district" id="district" value="">

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-input">
                                                    <label>Location</label>
                                                    <input type="text" class="form-control" name="location" id="location" value="">

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-input">
                                                    <label>Building Number</label>
                                                    <input type="text" class="form-control" name="build_no" id="build_no" value="">

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-input">
                                                    <label>Flat Number</label>
                                                    <input type="text" class="form-control" name="flat_no" id="flat_no" value="">

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-input">
                                                    <label>Street Name</label>
                                                    <input type="text" class="form-control" name="street_name" id="street_name" value="">

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-input">
                                                    <label>Pin Code</label>
                                                    <input type="number" class="form-control" name="pincode" id="pincode" value="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-input">
                                                    <label for="">Company currency</label>
                                                    <select id="company_currency" name="company_currency" class="form-control mt-0 form-control-border borderColor">
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
                                            <div class="col-md-6" style="display:none;">
                                                <div class="form-input">
                                                    <label>Opening Blance</label>
                                                    <input type="hidden" class="form-control" name="opening_balance" id="customer_opening_balance" value="0">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-input">
                                                    <label>Credit Period(In Days)</label>
                                                    <input type="text" class="form-control" name="credit_period" id="customer_credit_period" value="">

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="button-row d-flex">
                                        <button class="btn btn-primary ml-auto js-btn-next waves-effect waves-light" id="customerRegFrmNextBtn" type="button" data-toggle="modal" data-target="#visitingCard" title="Next">Next</button>
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
                                                            <input type="file" class="form-control" id="visitingFileInput" name="" placeholder="Visiting Card Upload" required="">
                                                        </div>
                                                        <div class="file-block">
                                                            <div class="progress-bar"> </div>
                                                        </div>
                                                        <button type="button" class="upload-button btn btn-primary visiting_card_btn waves-effect waves-light" name="" id="visiting_card_btn"> Upload </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="multisteps-form__panel" data-animation="scaleIn">

                            <div class="card vendor-details-card mb-0">
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
                                                    <label>Designation</label>
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
                                                    <label class="active">Login Password [Will be send to the POC email]</label>
                                                    <input type="text" class="form-control" name="adminPassword" id="adminPassword" value="132069">

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
                                                        <option value="Yes" selected="">Visible For All</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="button-row d-flex">
                                        <button class="btn btn-primary js-btn-prev waves-effect waves-light" type="button" title="Prev">Prev</button>
                                        <button class="btn ml-auto btn-danger add_data waves-effect waves-light" type="button" title="Save As Draft" value="add_draft">Save As Draft</button>
                                        <button id="customerCreateBtn" class="btn btn-primary ml-auto add_data waves-effect waves-light" type="button" title="Final Submit" value="add_post">Final Submit</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>


