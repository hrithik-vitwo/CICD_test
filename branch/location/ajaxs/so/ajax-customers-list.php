<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-customers-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$CustomersObj = new CustomersController();
if ($_GET['act'] === "listItem") {
    $customerId = $_GET['customerId'];
    $getCustomerObj = $CustomersObj->getDataCustomerDetails($customerId);
    $data = $getCustomerObj['data'][0];
    $customerDetails = $CustomersObj->getDataCustomerAddressDetails($customerId)['data'];
?>
    <input type="hidden" name="customerGstin" value="<?= $data['customer_gstin'] ?>">
    <input type="hidden" name="customerName" value="<?= $data['trade_name'] ?>">
    <input type="hidden" name="customerId" value="<?= $data['customer_id'] ?>">

    <div class="card po-vendor-details-view pt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-code"><i class="fa fa-check"></i>&nbsp;<p>Code :&nbsp;</p>
                    <p> <?= $data['customer_code'] ?></p>
                    <span style="display: none;" id="spanCreditPeriod"><?= $data['customer_credit_period'] ?></span>
                    <div class="divider"></div>
                </div>
                <?php if($companyCountry==103){?>
                <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-gstin"><i class="fa fa-check"></i>&nbsp;<p>GSTIN :&nbsp;</p>
                    <p class="customerGstin"> <?= $data['customer_gstin'] ?></p>
                    <input type="hidden" class="customerGstinCode" id="customerGstinCode" name="customerGstinCode" value="<?= substr($data['customer_gstin'], 0, 2) ?>">
                    <div class="divider"></div>
                </div>
                <?php }?>

                <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-status"><i class="fa fa-check"></i>&nbsp;<p>Status :&nbsp;</p>
                    <p class="status"> <?= $data['customer_status'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="row address-section">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="address-to bill-to">
                        <h5>Bill to</h5>
                        <hr class="mt-0 mb-2">
                        <?php
                        foreach ($customerDetails as $primaryCustomer) {
                            // console($primaryCustomer); 
                        ?>
                            <?php if ($primaryCustomer['customer_address_primary_flag'] == 1) { ?>
                                <p><?= $primaryCustomer['customer_address_building_no'] ?? null ?>,
                                    <?= $primaryCustomer['customer_address_flat_no'] ?? null ?>,
                                    <?= $primaryCustomer['customer_address_street_name'] ?? null ?>,
                                    <?= $primaryCustomer['customer_address_pin_code'] ?? null ?>,
                                    <?= $primaryCustomer['customer_address_location'] ?? null ?>,
                                    <?= $primaryCustomer['customer_address_city'] ?? null ?>,
                                    <?= $primaryCustomer['customer_address_district'] ?? null ?>,
                                    <?= $primaryCustomer['customer_address_state'] ?? null ?></p>
                                <!-- <div class="billAddressEdit" id="billAddressEdit_<?= $primaryCustomer['customer_address_id'] ?>"><i class="fa fa-edit" style="font-size: 0.8em;"></i></div> -->
                                <?php
                                $data1 = '' . $primaryCustomer['customer_address_building_no'] . ', ' . $primaryCustomer['customer_address_flat_no'] . ', ' . $primaryCustomer['customer_address_street_name'] . ', ' . $primaryCustomer['customer_address_pin_code'] . ', ' . $primaryCustomer['customer_address_location'] . ', ' . $primaryCustomer['customer_address_city'] . ', ' . $primaryCustomer['customer_address_district'] . ', ' . $primaryCustomer['customer_address_state'] . '' ?? "";
                                ?>
                                <input type="hidden" name="billingAddress" id="billingAddressInp" value="<?= $data1 ?>">
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="address-to ship-to">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <h5>Ship to</h5>
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <h5 class="display-inline">
                                    <div class="checkbox-label">
                                        <input type="checkbox" id="billToCheckbox" class="billToCheckbox" name="billToCheckbox" title="checked here for same as Bill To adress" checked>
                                        <label for="billToCheckbox" class="mb-0">Same as Bill to</label>
                                    </div>
                                    <button type="button" class="btn btn-primary change-address-modal text-xs btn-sm changeAddress" data-id="<?=$data['customer_id']?>" data-toggle="modal" data-target="#address-change<?= $customerId ?>">Change</button>
                                </h5>
                            </div>
                        </div>
                        <hr class="mt-0 mb-2">
                        <?php
                        foreach ($customerDetails as $shipToAddr) {
                            $recipientName = '';
                            if ($shipToAddr['customer_address_recipient_name'] != "") {
                                $recipientName = $shipToAddr['customer_address_recipient_name'] . ',';
                            }

                            if ($shipToAddr['customer_address_state_code'] != "") {
                            }
                            if ($shipToAddr['customer_address_primary_flag'] == 1) { ?>
                                <input type="hidden" id="billing_address_id" name="billing_address_id" value="<?= $shipToAddr['customer_address_id'] ?>">
                                <input type="hidden" id="shipping_address_id" name="shipping_address_id" value="<?= $shipToAddr['customer_address_id'] ?>">
                                <p id="shipTo">
                                    <?= $recipientName ?>
                                    <?= $shipToAddr['customer_address_building_no'] ?? null ?>,
                                    <?= $shipToAddr['customer_address_flat_no'] ?? null ?>,
                                    <?= $shipToAddr['customer_address_street_name'] ?? null ?>,
                                    <?= $shipToAddr['customer_address_pin_code'] ?? null ?>,
                                    <?= $shipToAddr['customer_address_location'] ?? null ?>,
                                    <?= $shipToAddr['customer_address_city'] ?? null ?>,
                                    <?= $shipToAddr['customer_address_district'] ?? null ?>,
                                    <?= $shipToAddr['customer_address_state'] ?? null ?></p>
                                </p>
                                
                                <div class="stateCodeSpan text-danger">
                                    <?php  if($companyCountry == 103){
                                    echo $shipToAddr['customer_address_state_code'] ;
                                    }
                                    else{} ?>
                                </div>
                                <!-- <div class="shipAddressEdit" id="shipAddressEdit_<?= $shipToAddr['customer_address_id'] ?>"><i class="fa fa-edit" style="font-size: 0.8em;"></i></div> -->
                                <?php
                                $data2 = '' . $recipientName . '' . $shipToAddr['customer_address_building_no'] . ', ' . $shipToAddr['customer_address_flat_no'] . ', ' . $shipToAddr['customer_address_street_name'] . ', ' . $shipToAddr['customer_address_pin_code'] . ', ' . $shipToAddr['customer_address_location'] . ', ' . $shipToAddr['customer_address_city'] . ', ' . $shipToAddr['customer_address_district'] . ', ' . $shipToAddr['customer_address_state'];
                                ?>
                                <input type="hidden" name="shippingAddress" id="shippingAddressInp" value="<?= $data2 ?>">
                        <?php
                            }
                        }
                        ?>
                    </div>
                    <!----------Address modal-------->

                    <div class="modal fade address-change-modal" id="address-change<?= $customerId ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header card-header">
                                    <div class="head">
                                        <i class="fa fa-map-marker-alt"></i>
                                        <h4>Change Address</h4>
                                    </div>
                                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="btn btn-primary address-btn otheraddressbtn nav-link active" id="pills-home-tab" data-id="<?=$data['customer_id']?>" data-bs-toggle="pill" data-bs-target="#savedAddress" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Other Address</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="btn btn-primary address-btn newaddress nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#newAddress" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">New Address</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="modal-body" style="height:15rem;">
                                    <div class="tab-content " id="pills-tabContent">
                                        <div class="tab-pane otherAddress-tab-pen fade show active" id="savedAddress" role="tabpanel" aria-labelledby="pills-home-tab">
                                            
                                        </div>
                                        <div class="tab-pane newAddress-tab-pen fade" id="newAddress" role="tabpanel" aria-labelledby="pills-profile-tab">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <label for="">Recipient Name</label>
                                                    <input type="text" class="form-control" id="recipientName">
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <label for="">Building Number</label>
                                                    <input type="text" class="form-control" id="billingNo">
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <label for="">Flat Number</label>
                                                    <input type="text" class="form-control" id="flatNo">
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <label for="">Street Name</label>
                                                    <input type="text" class="form-control" id="streetName">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-12 px-0">
                                                    <label for="">Location</label>
                                                    <input type="text" class="form-control" id="location">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <label for="">City</label>
                                                    <input type="text" class="form-control" id="city">
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <label for="">Pin Code</label>
                                                    <input type="text" class="form-control" id="pinCode">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <label for="">District</label>
                                                    <input type="text" class="form-control" id="district">
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <label for="">State</label>
                                                    <input type="text" class="form-control" id="state">
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                    <label for="">State Code</label>
                                                    <input type="number" class="form-control" id="stateCode">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12"></div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-12"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary closeButton" data-dismiss="modal">Close</button>
                                    <div id="saveChanges">
                                        <button type="button" class="btn btn-primary go">Go</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

<?php
} else {
    echo "Something wrong, try again!";
}
?>