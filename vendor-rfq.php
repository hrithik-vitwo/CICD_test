<?php
include("app/v1/config.php");

include("app/v1/functions/branch/func-vendor-validation.php");

if (isset($_GET['inf'])) {
    $validVendor = validation($_GET['inf']);
    if ($validVendor['status'] == 'success') {
        // print_r(item_details($_GET['inf']));
        $detail = get_vendor_details($_GET['inf']);
        $items = item_details($_GET['inf']);
               
        $rfqId = $detail["rfqItemListId"];
        $closing_query = "SELECT * FROM erp_rfq_list WHERE `rfqId`='" . $rfqId . "'";
        $closing_execute = queryGet($closing_query, false);
        $closing_date = $closing_execute["data"]["closing_date"];
        $company_id = $closing_execute["data"]["company_id"];



        $company_query = queryGet("SELECT * FROM erp_companies WHERE `company_id`='" . $company_id . "'", false);
        $company_name = $company_query["data"]["company_name"];
        $company_country = $company_query["data"]["company_country"];
        $countryname = queryGet("SELECT `name` FROM erp_countries WHERE `id`='" . $company_country . "'", false)['data'];
        $companyCountry = $company_country;
        $lables = getLebels($company_country)['data'];
        $lable = json_decode($lables, true);

        $abn = $lable['fields']['taxidNumber'];
        $tfn = $lable['fields']['taxNumber'];
        $tdslable = ($lable['source_taxation']);
        $tcslable = $lable['transaction_taxation'];
        $taxname = $lable['fields']['taxStatus'];





?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="icon" type="image/x-icon" href="public/storage/logo/165985132599981.ico">
            <title>Vitwo.ai | Dashboard</title>
            <!-- Google Font: Source Sans Pro -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous"> <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.0/css/bootstrap.min.css" integrity="sha512-XWTTruHZEYJsxV3W/lSXG1n3Q39YIWOstqvmFsdNEEQfHoZ6vm6E9GK2OrF6DSJSpIbRbi+Nn0WDPID9O7xB2Q==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <link rel="stylesheet" href="public/assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
            <link rel="stylesheet" href="./public/assets/sales-order.css">
            <link rel="stylesheet" href="./public/assets/listing.css">
            <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

            <style>
                .error {
                    color: red;
                    font-size: 10px;
                    /* display: none; */
                    position: relative;
                }

                .font-small,
                .font-small strong {
                    font-size: 13px;
                }

                .content-wrapper {
                    padding-top: 4rem !important;
                }

                .gst-check {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                }



                .gst-check-section {
                    gap: 13px;
                }

                .btn-primary {
                    background-color: #003060 !important;
                    border-color: #003060 !important;
                    color: #fff;
                }

                a.btn.btn-primary.vendor-rfq-modal-btn {
                    color: #fff;
                }

                nav.navbar.vendor-rfq-quotation {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    position: fixed;
                    width: 100%;
                    z-index: 99999;
                }

                .logo-section img {
                    width: 120px;
                    height: 30px;
                    object-fit: contain;
                }

                .company-section a.dropdown-toggle {
                    display: flex;
                    align-items: center;
                }

                .company-section a p {
                    font-size: 13px;
                }

                .basic-details .form-input.d-flex {
                    font-size: 10px;
                    align-items: center;
                    gap: 10px;
                    margin-top: 10px;
                }

                .text-xs {
                    font-size: 10px !important;
                }

                .basic-details .form-input.d-flex input {
                    max-width: 200px;
                }

                .terms-condition-text .d-flex {
                    align-items: center;
                    gap: 5px;
                }

                .item-name-rfq-mail {
                    width: 500px;
                }

                button.btn.btn-primary.checkAndVerifyGstinBtn.disabled {
                    font-size: 0.75rem;
                    border: 0;
                }

                button.btn.btn-primary.checkAndVerifyGstinBtn .spinner-border {
                    width: 1rem;
                    height: 1rem;
                }
            </style>
        </head>

        <body>
            <!-- Modal -->
            <div class="modal fade" id="exampleQuotationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleQuotationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content card">
                        <div class="modal-header card-header py-2 px-3">
                            <h4 class="modal-title font-monospace text-md text-white" id="exampleQuotationModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                            <button type="button" class="close text-white" id="rfqmodal" data-bs-dismiss="modal" aria-label="Close">x</button>
                        </div>
                        <div id="notesModalBody" class="modal-body card-body">
                        </div>
                    </div>
                </div>
            </div>
            <nav class="navbar vendor-rfq-quotation navbar-fixed navbar-expand-lg navbar-light bg-light">
                <div class="logo-section">
                    <img src="public/assets/img/logo/vitwo-logo.png" alt="company-logo">
                </div>
                <div class="company-section">
                    <div class="dropdown">
                        <a type="button" class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown">
                            <img src="public/assets/img/header-icon/company.png" alt="" width="30px">
                            <p class="text-xs font-bold ml-2">
                                <?= $company_name ?>
                            </p>

                        </a>
                    </div>
                </div>
            </nav>
            <div class="content-wrapper p-3">



                <div class="container-fluid">
                    <section class="vendor-rfq-section">
                        <div class="head mb-3 ">
                            <h6>Quotation Details Valid till <?php
                                                                echo ($closing_date);
                                                                $date1 = date_create($closing_date);
                                                                $date2 = date_create(date());
                                                                $diff = date_diff($date1, $date2);
                                                                $days = $diff->format("%a days left");
                                                                echo (" (" . $days . ")");
                                                                ?></h6>
                        </div>


                        <div class="card vendor-details">
                            <div class="card-header">
                                <div class="head">
                                    <i class="fa fa-info"></i>
                                    <h4>Vendor Details</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="" id="v_id" class="form-control" value="<?= $detail['rfqVendorId'] ?>" />
                                <input type="hidden" name="" id="rfq_code" class="form-control" value="<?= $detail['rfqCode'] ?>" />
                                <input type="hidden" name="" id="rfqId" class="form-control" value="<?= $detail['rfqItemListId'] ?>" />
                                <input type="hidden" name="" id="location_url" class="form-control" value="<?= LOCATION_URL ?>" />

                                <?php if ($detail['vendor_type'] != "existing") { ?>


                                    <div class="row gst-have-section mb-3">
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-inline gst-check-section mb-2" <?php if ($companyCountry != 103) {
                                                                                                        echo 'style="display:none;"';
                                                                                                    } ?>>
                                                        <div class="gst-check">
                                                            <input type="radio" name="chose_gst" class="gstAlert" id="gst" value="check_true" <?php if ($companyCountry == 103) {
                                                                                                                                                    echo "checked";
                                                                                                                                                } ?> /><label class="mb-0 font-bold" for=""> Have GST Number</label>
                                                        </div>
                                                        <div class="gst-check">
                                                            <input type="radio" name="chose_gst" class="gstAlert" id="no_gst" <?php if ($companyCountry != 103) {
                                                                                                                                    echo "checked";
                                                                                                                                } ?> value="check_false" /><label class="mb-0 font-bold" for="">Have No GST Number</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if ($companyCountry == 103) { ?>
                                                    <div class="col-lg-12 col-md-12 col-sm-12" id="show_gst">
                                                        <div class="form-input">
                                                            <!-- <label for="">GST</label> -->
                                                            <div class="d-flex">
                                                                <input type="text" class="form-control vendor-gstin-input" name="vendorGstNoInput" id="v_gst" placeholder="enter GST number" oninput="this.value = this.value.toUpperCase();" />
                                                                <button class="btn btn-primary checkAndVerifyGstinBtn ml-2">
                                                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="col-lg-12 col-md-12 col-sm-12" id="show_gst">
                                                        <div class="form-input">
                                                            <label for=""><?= $abn ?></label>
                                                            <div class="d-flex">
                                                                <input type="text" class="form-control vendor-gstin-input" name="vendorGstNoInput" id="v_gst" placeholder="Enter <?= $abn ?> Number" oninput="this.value = this.value.toUpperCase();" />

                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <div class="col-lg-12 col-md-12 col-sm-12 basic-details" id="show_gst">
                                                    <div class="form-input d-flex">
                                                        <label for="">Name :</label>
                                                        <p class="font-bold"><?= $detail['vendor_name'] ?></p>
                                                        <input type="hidden" name="deliveryDate1" id="v_name" class="form-control" placeholder="Name" value="<?= $detail['vendor_name'] ?>" readonly />
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 basic-details" id="show_gst">
                                                    <div class="form-input d-flex">
                                                        <label for="">Email :</label>
                                                        <p class="font-bold"><?= $detail['vendor_email'] ?></p>
                                                        <input type="hidden" name="deliveryDate1" id="v_email" class="form-control" placeholder="Email" value="<?= $detail['vendor_email'] ?>" readonly />

                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 basic-details" id="show_gst">
                                                    <div class="form-input d-flex">
                                                        <label for="">Phone :</label>
                                                        <input id="v_phone" type="number" class="form-control" name="ph_number">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-12">
                                            <div id="show_details" style="display: <?php if ($companyCountry == 103) {
                                                                                        echo "none";
                                                                                    } else {
                                                                                        echo "block";
                                                                                    } ?>;">
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-input">
                                                            <label for=""><?= $tfn ?></label>
                                                            <input type="text" name="deliveryDate1" id="v_pan" class="form-control" placeholder="PAN" />

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Trade Name</label>
                                                            <input type="text" name="deliveryDate1" id="v_trade_name" class="form-control" placeholder="Trade Name" />

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Constitution Of Business</label>
                                                            <input type="text" name="deliveryDate1" id="v_co_busi" class="form-control" placeholder="Constitution Of Business" />

                                                        </div>

                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="form-input">
                                                            <?php if ($companyCountry == 103) { ?>
                                                                <label for="">Flat No.</label>
                                                            <?php } else { ?>
                                                                <label for="">Unit No.</label>
                                                            <?php } ?>
                                                            <input type="text" name="deliveryDate1" id="v_flat_no" class="form-control" placeholder="Flat Number" />

                                                        </div>

                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Building Number</label>
                                                            <input type="text" name="deliveryDate1" id="v_build_num" class="form-control" placeholder="Building Number" />

                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Street Name</label>
                                                            <input type="text" name="deliveryDate1" id="v_street_no" class="form-control" placeholder="Street Name" />

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">

                                                    <?php if ($companyCountry == 103) { ?>
                                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                                            <div class="form-input">
                                                                <label for="">Location</label>
                                                                <input type="text" name="deliveryDate1" id="v_location" class="form-control" placeholder="Location" />

                                                            </div>

                                                        </div>
                                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                                            <div class="form-input">
                                                                <label for="">City</label>
                                                                <input type="text" name="deliveryDate1" id="v_city" class="form-control" placeholder="City" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                                            <div class="form-input">
                                                                <label for="">District</label>
                                                                <input type="text" name="deliveryDate1" id="v_district" class="form-control" placeholder="District" value="<?php if ($detail['vendor_type'] == "existing") echo $detail['vendor_business_district'] ?>" />

                                                            </div>

                                                        </div>
                                                    <?php } else { ?>

                                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                                            <div class="form-input">
                                                                <label for="">Region</label>
                                                                <!-- <input type="text" name="deliveryDate1" id="v_district" class="form-control" placeholder="District" value="<?php if ($detail['vendor_type'] == "existing") echo $detail['vendor_business_district'] ?>" /> -->
                                                                <select id="region" name="deliveryDate1" id="v_district" class="form-control">

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
                                                            </div>

                                                        </div>
                                                    <?php } ?>
                                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Postal Code</label>
                                                            <?php


                                                            $maxlength = ($companyCounty == 103) ? 6 : 4;
                                                            ?>
                                                            <input type="text" name="deliveryDate1" id="v_pin" class="form-control" placeholder="Postal Code" maxlength="<?= $maxlength ?>" />

                                                        </div>

                                                    </div>

                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="form-input">

                                                            <?php if ($companyCountry == 103) { ?>
                                                                <label for="">State</label>

                                                            <?php } else { ?>

                                                                <label for="">Territory</label>
                                                                <select id="state" name="deliveryDate1" id="v_state" class="form-control stateDropDown">
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

                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                                        <div class="form-input">
                                                            <label for="">Country</label>
                                                            <input type="text" name="deliveryDate1" id="v_country" class="form-control" placeholder="Country" readonly value="<?= $countryname['name'] ?>" />

                                                        </div>

                                                    </div>


                                                </div>
                                            </div>
                                        </div>


                                    </div>





                                <?php
                                } else {
                                ?>
                                    <div id="show_details">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-input">
                                                    <label for=""><?= $abn; ?></label>
                                                    <input type="text" class="form-control vendor-gstin-input w-75" name="vendorGstNoInput" id="v_gst" value="<?= $detail['vendor_gstin'] ?>" />
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-input">
                                                    <label for="">Code</label>
                                                    <input type="text" name="" id="v_code" class="form-control" placeholder="Code" value="<?php if ($detail['vendor_type'] == "existing") echo $detail['vendor_code'] ?>" <?php if ($detail['vendor_type'] == "existing") echo "readonly" ?> />
                                                </div>
                                            </div>
                                            <input type="hidden" name="" id="vendor_primary_id" class="form-control" placeholder="Code" value="<?php if ($detail['vendor_type'] == "existing") echo $detail['vendorId'] ?>" />
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-input">
                                                    <label for="">Name</label>
                                                    <input type="text" name="deliveryDate1" id="v_name" class="form-control" placeholder="Name" value="<?= $detail['vendor_name'] ?>" readonly />

                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-input">
                                                    <label for="">Email</label>
                                                    <input type="text" name="deliveryDate1" id="v_email" class="form-control" placeholder="Email" value="<?= $detail['vendor_email'] ?>" readonly />

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-input">
                                                    <label for=""><?= $tfn; ?></label>
                                                    <input type="text" name="deliveryDate1" id="v_pan" class="form-control" placeholder="<?= $tfn; ?>" value="<?= $detail['vendor_pan'] ?>" />

                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-input">
                                                    <label for="">Trade Name</label>
                                                    <input type="text" name="deliveryDate1" id="v_trade_name" class="form-control" placeholder="Trade Name" value="<?= $detail['trade_name'] ?>" />

                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-input">
                                                    <label for="">Constitution Of Business</label>
                                                    <input type="text" name="deliveryDate1" id="v_co_busi" class="form-control" placeholder="Constitution Of Business" value="<?= $detail['constitution_of_business'] ?>" />

                                                </div>

                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="form-input">
                                                    <?php if ($companyCountry == 103) { ?>
                                                        <label for="">Flat No.</label>
                                                    <?php } else { ?>
                                                        <label for="">Unit No.</label>
                                                    <?php } ?>
                                                    <input type="text" name="deliveryDate1" id="v_flat_no" class="form-control" placeholder="Flat Number" />

                                                </div>

                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="form-input">
                                                    <label for="">Building Number</label>
                                                    <input type="text" name="deliveryDate1" id="v_build_num" class="form-control" placeholder="Building Number" />

                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-input">
                                                    <label for="">Street Name</label>
                                                    <input type="text" name="deliveryDate1" id="v_street_no" class="form-control" placeholder="Street Name" />

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php if ($companyCountry == 103) { ?>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="">Location</label>
                                                        <input type="text" name="deliveryDate1" id="v_location" class="form-control" placeholder="Location" value="<?= $detail['vendor_business_city'] ?>" />

                                                    </div>

                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="">City</label>
                                                        <input type="text" name="deliveryDate1" id="v_city" class="form-control" placeholder="City" value="<?= $detail['vendor_business_city'] ?>" />

                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="">District</label>
                                                        <input type="text" name="deliveryDate1" id="v_district" class="form-control" placeholder="District" value="<?php if ($detail['vendor_type'] == "existing") echo $detail['vendor_business_district'] ?>" />

                                                    </div>

                                                </div>
                                            <?php } else { ?>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-input">
                                                        <label for="">Region</label>
                                                        <!-- <input type="text" name="deliveryDate1" id="v_district" class="form-control" placeholder="District" value="<?php if ($detail['vendor_type'] == "existing") echo $detail['vendor_business_district'] ?>" /> -->
                                                        <select id="region" name="deliveryDate1" id="v_district" class="form-control">

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
                                                    </div>

                                                </div>
                                            <?php } ?>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-input">
                                                    <label for="">Postal Code</label>
                                                    <?php


                                                    $maxlength = ($companyCounty == 103) ? 6 : 4;
                                                    ?>
                                                    <input type="text" name="deliveryDate1" id="v_pin" class="form-control" placeholder="Postal Code" maxlength="<?= $maxlength ?>" />

                                                </div>

                                            </div>

                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="form-input">
                                                    <?php if ($companyCountry == 103) { ?>
                                                        <label for="">State</label>
                                                        <input type="text" name="deliveryDate1" id="v_state" class="form-control" placeholder="State" value="<?= $detail['vendor_business_state'] ?>" />

                                                    <?php } else { ?>

                                                        <label for="">Territory</label>
                                                        <select id="state" name="deliveryDate1" id="v_state" class="form-control stateDropDown">
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
                                                    <?php } ?>


                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="form-input">
                                                    <label for="">Phone</label>
                                                    <input type="text" name="deliveryDate1" id="v_phone" class="form-control" placeholder="Phone" value="<?php if ($detail['vendor_type'] == "existing") echo $detail['vendor_authorised_person_phone'] ?>" />

                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="form-input">
                                                    <label for="">Country</label>
                                                    <input type="text" name="deliveryDate1" id="v_country" class="form-control" placeholder="Country" readonly value="<?= $countryname['name'] ?>" />

                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                <?php
                                }
                                ?>

                            </div>
                        </div>

                        <div class="head mb-3 mt-5">
                            <h6>Items Details of <?= $detail['rfqCode'] ?></h6>
                        </div>

                        <div class="card items-details-table">
                            <div class="card-body p-0" style="overflow-x: auto;">
                                <table class="table defaultDataTable table-hover vendor-rfq-table">
                                    <!-- Responsive Table Header Section -->
                                    <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Other Details</th>
                                            <th>Required Quantity</th>
                                            <th>Unit of Measurement</th>
                                            <th>Minimum Order Quantity</th>
                                            <th>Item Price</th>
                                            <th>Discount (%)</th>
                                            <th>GST (%)</th>
                                            <th>Total Amount</th>
                                            <th>Delivery Mode</th>
                                            <th>Lead Time</th>
                                        </tr>
                                    </thead>
                                    <!-- Responsive Table Body Section -->
                                    <tbody>

                                        <?php
                                        foreach ($items as $item) {
                                        ?>
                                            <tr>
                                                <td><?= $item['itemCode'] ?></td>
                                                <td>
                                                    <p class="pre-normal item-name-rfq-mail"><?= $item['itemName'] ?></p>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-primary vendor-rfq-modal-btn rfq_modal m-auto" data-value="<?= $item['itemId'] ?>" data-toggle="modal" data-target="#vendorRfqModal">
                                                        <i class="fa fa-info"></i>
                                                    </button>
                                                </td>
                                                <td><?= $item['qty'] ?></td>
                                                <td><?= $item['uomName'] ?></td>
                                                <td>
                                                    <div class="form-input rfq-vendor-item">
                                                        <label for="">Quantity</label>
                                                        <input type="number" name="deliveryDate1" class="form-control each_quantity w-75" id="itemQty_<?= $item['rfqItemId'] ?>" placeholder="Quantity" value="<?= $item['itemQuantity'] ?>" />

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-input rfq-vendor-item">
                                                        <label for="">Price</label>
                                                        <input type="number" name="deliveryDate1" class="form-control each_price w-75" placeholder="Price" id="itemUnitPrice_<?= $item['rfqItemId'] ?>" value="0" />

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-input rfq-vendor-item">
                                                        <label for="">Discount</label>
                                                        <input type="number" name="deliveryDate1" class="form-control each_discount w-75" placeholder="Discount" id="itemDiscount_<?= $item['rfqItemId'] ?>" value="0" />

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-input rfq-vendor-item">
                                                        <label for="">GST</label>
                                                        <input type="number" name="deliveryDate1" class="form-control each_gst w-75" placeholder="GST" id="itemGST_<?= $item['rfqItemId'] ?>" value="0" />

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-input rfq-vendor-item">
                                                        <label for="">Total</label>
                                                        <input type="number" name="deliveryDate1" id="itemTotalPrice_<?= $item['rfqItemId'] ?>" class="form-control each_total w-75" placeholder="Total" readonly value="0" />

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-input rfq-vendor-item">
                                                        <label for="">Delivery Mode</label>
                                                        <select id="itemIncoterms_<?= $item['itemId'] ?>" name="" class="form-control each_incoterms w-75">
                                                            <option value="1" disabled>Ex-works</option>
                                                            <option value="2">FOR</option>
                                                            <option value="3" disabled>FOB</option>
                                                            <option value="4" disabled>CIF</option>
                                                        </select>

                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-input rfq-vendor-item">
                                                        <label for="">Lead Time</label>
                                                        <input type="number" name="deliveryDate1" class="form-control each_lead_time w-75" placeholder="LeadTime" id="itemLead_<?= $item['itemId'] ?>" value="0" />

                                                    </div>
                                                </td>

                                                <input type="hidden" id="" class="form-control each_detail" value="<?= $item['itemId'] . "|" . $item['itemCode'] . "|" . $item['itemName'] . "|" . $item['itemDesc'] . "|" . $item['qty'] . "|" . $item['netWeight'] . "|" . $item['grossWeight'] . "|" . $item['baseUnitMeasure'] . "|" . $item['volume'] . "|" . $item['volumeCubeCm'] . "|" . $item['height'] . "|" . $item['width'] . "|" . $item['length'] . "|" . $item['goodsType'] . "|" . $item['goodsGroup'] . "|" . $item['purchaseGroup'] . "|" . $item['branch'] . "|" . $item['availabilityCheck'] . "|" . $item['issueUnitMeasure'] . "|" . $item['uomRel'] . "|" . $item['storageBin'] . "|" . $item['pickingArea'] . "|" . $item['tempControl'] . "|" . $item['storageControl'] . "|" . $item['maxStoragePeriod'] . "|" . $item['maxStoragePeriodTimeUnit'] . "|" . $item['minRemainSelfLife'] . "|" . $item['minRemainSelfLifeTimeUnit'] . "|" . $item['purchasingValueKey'] ?>">



                                            </tr>

                                        <?php } ?>
                                    </tbody>
                                </table>
                                <!-- Modal -->
                                <div class="modal fade vendor-rfq-modal rfq-item-detail" id="vendorRfqModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content p-0" style="border-radius: 15px;">
                                            <div class="modal-header">
                                                <div class="head d-flex">
                                                    <i class="fas fa-info-circle"></i>
                                                    <h4 class="font-bold mb-0">Other Details for item</h4>
                                                </div>
                                            </div>
                                            <div class="modal-body" id="item_data">

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="card mt-5">
                            <div class="card-header">
                                <div class="head">
                                    <i class="fa fa-info"></i>
                                    <h4>Remarks</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-input">
                                            <label for="">I would like to say something, which is not mention in quotation</label>
                                            <textarea name="" id="v_description" cols="196" rows="7" class="form-control" placeholder="notes...."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-input terms-condition-text">
                                            <div class="d-flex">
                                                <input type="checkbox" id="TandCckbox">
                                                <label for="" class="font-bold mb-0">Terms and Condition :</label>
                                            </div>
                                            <hr class="mt-1 mb-2">
                                            <!-- <p class="text-xs font-bold">
                                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Neque corporis veritatis hic placeat? Ipsam iste dolorem deserunt, rerum hic eligendi recusandae, doloremque nulla obcaecati sit, placeat tempora deleniti optio incidunt.
                                            </p> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <!-- <button type="button" id="finalsubmit" class="btn btn-danger float-right mt-4 mb-2 ml-3">Cancel</button> -->

                                <button type="button" id="finalsubmit" class="btn btn-primary float-right mt-4 mb-2">Send Quotation</button>

                            </div>
                        </div>



                    </section>
                </div>
            </div>

            <script src="<?= BASE_URL; ?>public/assets/plugins/jquery/jquery.min.js"></script>
            <script src="<?= BASE_URL; ?>public/validations/quotationValidation.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.6/dist/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
            <!-- sweetalert2 -->
            <script src="public/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
            <script>
                $(document).on("keyup", ".each_quantity", function() {
                    let rowNo = ($(this).attr("id")).split("_")[1];
                    calculateOneItemAmounts(rowNo);
                    // alert(rowNo);
                });

                $(document).on("keyup", ".each_price", function() {
                    let rowNo = ($(this).attr("id")).split("_")[1];
                    calculateOneItemAmounts(rowNo);

                });

                $(document).on("keyup", ".each_discount", function() {
                    let rowNo = ($(this).attr("id")).split("_")[1];
                    calculateOneItemAmounts(rowNo);
                });

                $(document).on("keyup", ".each_gst", function() {
                    let rowNo = ($(this).attr("id")).split("_")[1];
                    calculateOneItemAmounts(rowNo);
                });



                function calculateOneItemAmounts(rowNo) {
                    let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
                    let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;
                    let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;
                    let itemTax = (parseFloat($(`#itemGST_${rowNo}`).val())) ? parseFloat($(`#itemGST_${rowNo}`).val()) : 0;


                    // $(`#multiQuantity_${rowNo}`).val(itemQty);

                    let basicPrice = itemUnitPrice * itemQty;
                    let totalDiscount = basicPrice * itemDiscount / 100;
                    let priceWithDiscount = basicPrice - totalDiscount;
                    let totalTax = priceWithDiscount * itemTax / 100;
                    let totalItemPrice = priceWithDiscount + totalTax;

                    console.log(itemQty, itemUnitPrice, totalItemPrice);

                    $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice);
                    // $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount);
                    // $(`#itemTotalTax_${rowNo}`).html(totalTax);
                    // $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice);
                    // $(`#mainQty_${rowNo}`).html(itemQty);
                    // calculateGrandTotalAmount();
                }
            </script>


            <script>
                // $(document).ready(function() {
                //     $("#finalsubmit").click(function() {

                //         var quantity_array = new Array();
                //         var price_array = new Array();
                //         var discount_array = new Array();
                //         var gst_array = new Array();
                //         var delivery_array = new Array();
                //         var lead_array = new Array();
                //         var total_array = new Array();
                //         var detail_array = new Array();

                //         var arr3 = new Array();
                //         $.each($('.each_quantity'), function(i, value) {
                //             quantity_array.push($(this).val());
                //         });
                //         $.each($('.each_price'), function(j, values) {
                //             price_array.push($(this).val());
                //         });
                //         $.each($('.each_discount'), function(j, values) {
                //             discount_array.push($(this).val());
                //         });
                //         $.each($('.each_gst'), function(j, values) {
                //             gst_array.push($(this).val());
                //         });
                //         $.each($('.each_total'), function(j, values) {
                //             total_array.push($(this).val());
                //         });
                //         $.each($('.each_incoterms'), function(j, values) {
                //             delivery_array.push($(this).val());
                //         });
                //         $.each($('.each_lead_time'), function(j, values) {
                //             lead_array.push($(this).val());
                //         });
                //         $.each($('.each_detail'), function(j, values) {
                //             detail_array.push($(this).val());
                //         });

                //         console.log(detail_array);
                //         console.log(quantity_array);
                //         let i = 0,
                //             j = 0,
                //             k = 0,
                //             l = 0,
                //             m = 0,
                //             n = 0,
                //             o = 0,
                //             p = 0,
                //             q = 0;

                //         while (i < quantity_array.length && j < price_array.length && k < discount_array.length && l < total_array.length && m < detail_array.length && n < delivery_array.length && o < lead_array.length && p < gst_array.length) {
                //             if (quantity_array[i] == "") {
                //                 i++;
                //                 j++;
                //                 k++;
                //                 l++;
                //                 m++;
                //                 n++;
                //                 o++;
                //                 p++;
                //                 continue;
                //             } else {
                //                 arr3[q++] = detail_array[m++] + "|" + quantity_array[i++] + "|" + price_array[j++] + "|" + discount_array[k++] + "|" + total_array[l++] + "|" + delivery_array[n++] + "|" + lead_array[o++] + "|" + gst_array[p++];
                //             }
                //         }


                //         console.log(arr3);

                //         $.ajax({
                //             type: "POST",
                //             url: `<?= LOCATION_URL ?>ajaxs/pr/ajax-vendor-submit.php`,
                //             data: {
                //                 v_id: $("#v_id").val(),
                //                 vendor_primary_id: $("#vendor_primary_id").val(),
                //                 v_code: $("#v_code").val(),
                //                 rfq_code: $("#rfq_code").val(),
                //                 rfqId: $("#rfqId").val(),
                //                 vendor_gst: $("#v_gst").val(),
                //                 vendor_pan: $("#v_pan").val(),
                //                 vendor_tradename: $("#v_trade_name").val(),
                //                 vendor_constofbusiness: $("#v_co_busi").val(),
                //                 vendor_flatno: $("#v_flat_no").val(),
                //                 vendor_buildno: $("#v_build_num").val(),
                //                 vendor_streetname: $("#v_street_no").val(),
                //                 vendor_location: $("#v_location").val(),
                //                 v_name: $("#v_name").val(),
                //                 v_email: $("#v_email").val(),
                //                 v_phone: $("#v_phone").val(),
                //                 v_city: $("#v_city").val(),
                //                 v_district: $("#v_district").val(),
                //                 v_state: $("#v_state").val(),
                //                 v_pin: $("#v_pin").val(),
                //                 v_description: $("#v_description").val(),
                //                 v_detail: arr3

                //             },
                //             beforeSend: function() {
                //                 $("#finalsubmit").prop('disabled', true);
                //                 $("#finalsubmit").html(`<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...`);
                //             },
                //             success: function(response) {

                //                 //console.log(response);
                //                 var proper_response = JSON.parse(response);
                //                 var status = proper_response["status"];
                //                 var message = proper_response["message"];
                //                 //alert(proper_response);

                //                 if (status == "success") {
                //                     //alert(message);
                //                         $(document).ready(function() {
                //                             Swal.fire({
                //                                 icon: status,
                //                                 title: `Thank You`,
                //                                 text: message,
                //                             }).then(function() {
                //                                 window.location.href = `success-page.php`;
                //                             });
                //                         });
                //                     $("#finalsubmit").html(`Submitted`);
                //                 } else {
                //                         $(document).ready(function() {
                //                             Swal.fire({
                //                                 icon: status,
                //                                 title: `Opps...!`,
                //                                 text: message,
                //                             }).then(function() {
                //                                 window.location.href = `success-page.php`;
                //                             });
                //                         });
                //                     $("#finalsubmit").html(`Submit`);
                //                 }

                //             }
                //         });



                //     });
                // });

                // $(function() {
                //     $(".defaultDataTable").DataTable({
                //         "responsive": true,
                //         "lengthChange": false,
                //         "autoWidth": false,
                //         "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                //     }).buttons().container().appendTo('#defaultDataTable_wrapper .col-md-6:eq(0)');

                //     /*$('#defaultDataTable').DataTable({
                //       "paging": true,
                //       "lengthChange": false,
                //       "searching": false,
                //       "ordering": true,
                //       "info": true,
                //       "autoWidth": false,
                //       "responsive": true,
                //     });*/
                // });
            </script>

            <script>
                $(document).ready(function() {
                    $(document).on("click", "#rfqmodal", function() {
                        const $modal = $("#exampleQuotationModal");
                        $("#exampleQuotationModal").modal("hide");
                    });
                    $(".rfq_modal").click(function() {

                        $.ajax({
                            type: "POST",
                            url: `<?= LOCATION_URL ?>ajaxs/pr/ajax-vendor-itemdetail.php`,
                            data: {
                                id: $(this).data('value')
                            },
                            beforeSend: function() {
                                var html_data = `
                           
                           <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Gross Weight </strong>:&nbsp;
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Net Weight </strong>:&nbsp; 
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Length </strong>:&nbsp; 
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Height </strong>:&nbsp; 
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Width </strong>:&nbsp; 
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Volume </strong>:&nbsp;
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>VolumeCubeCm </strong>:&nbsp; 
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>HSN Code </strong>:&nbsp; 
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Max Storage Period </strong>:&nbsp;
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Item Description </strong>:&nbsp; 
                                    </p>
                                </div>
                           </div>
                           `
                                //append
                                $("#item_data").html(html_data);

                            },
                            success: function(response) {
                                console.log(JSON.parse(response));
                                var total = JSON.parse(response);
                                var html_data = `
                           
                           <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Gross Weight </strong>:&nbsp; ` + total.grossWeight + `
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Net Weight </strong>:&nbsp; ` + total.netWeight + `
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Length </strong>:&nbsp; ` + total.length + `
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Height </strong>:&nbsp; ` + total.height + `
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Width </strong>:&nbsp; ` + total.width + `
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Volume </strong>:&nbsp; ` + total.volume + `
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>VolumeCubeCm </strong>:&nbsp; ` + total.volumeCubeCm + `
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>HSN Code </strong>:&nbsp; ` + total.hsnCode + `
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Max Storage Period </strong>:&nbsp; ` + total.maxStoragePeriod + ` ` + total.maxStoragePeriodTimeUnit + `
                                    </p>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-2">
                                    <p class="text-xs my-2">
                                        <strong>Item Description </strong>:&nbsp; ` + total.itemDesc + `
                                    </p>
                                </div>
                           </div>
                           `
                                //append
                                $("#item_data").html(html_data);
                                //    console.log(response);
                            }
                        });
                    });
                });
            </script>

            <script>
                $(document).on("click", ".gstAlert", function() {
                    let checked = $('input[name=chose_gst]:radio:checked').val();
                    // alert(checked);

                    if (checked == 'check_true') {
                        $("#show_gst").show();
                        $("#show_details").hide();
                    } else {
                        $("#show_gst").hide();
                        $("#v_pan").val("");
                        $("#v_trade_name").val("");
                        $("#v_co_busi").val("");
                        $("#v_flat_no").val("");
                        $("#v_build_num").val("");
                        $("#v_street_no").val("");
                        $("#v_location").val("");
                        $("#v_city").val("");
                        $("#v_pin").val("");
                        $("#v_state").val("");
                        $("#v_district").val("");
                    }

                });

                $(document).on("click", "#no_gst", function() {
                    let checked = $('input[name=chose_gst]:radio:checked').val();

                    if (checked == 'check_false') {
                        $("#show_details").show();
                    } else {
                        $("#show_details").hide();
                    }
                })

                $(document).on("click", "#gst", function() {
                    let checked = $('input[name=chose_gst]:radio:checked').val();
                    $(".checkAndVerifyGstinBtn").removeClass("disabled").prop("disabled", false);


                })

                // alert();
            </script>

            <script>
                $(".checkAndVerifyGstinBtn").click(function() {
                    let vendorGstNo = $("#v_gst").val();
                    if (vendorGstNo != "") {
                        $.ajax({
                            type: "GET",
                            url: `branch/location/ajaxs/ajax-vendor-gst.php?gstin=${vendorGstNo}`,
                            beforeSend: function() {
                                $('.checkAndVerifyGstinBtn').html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
                                // $('.checkAndVerifyGstinBtn').toggleClass("disabled");
                                $(".checkAndVerifyGstinBtn").removeClass("disabled").prop("disabled", false);
                            },
                            success: function(response) {
                                console.log(response)
                                $("#show_details").show();
                                // $(".checkAndVerifyGstinBtn").toggleClass("disabled");
                                $('.checkAndVerifyGstinBtn').toggleClass("disabled").prop("disabled", true);
                                $('.checkAndVerifyGstinBtn').html('<i class="fa fa-arrow-right" aria-hidden="true"></i>');


                                //  $('.ch  eckAndVerifyGstinBtn').html("Re-Verify");
                                responseObj = (JSON.parse(response));
                                $("#v_pan").val(responseObj["pan"]);
                                $("#v_trade_name").val(responseObj["data"]["tradeNam"]);
                                $("#v_co_busi").val(responseObj["data"]["ctb"]);
                                $("#v_flat_no").val(responseObj["data"]['pradr']['addr']['flno']);
                                $("#v_build_num").val(responseObj["data"]['pradr']['addr']['bno']);
                                $("#v_street_no").val(responseObj["data"]['pradr']['addr']['st']);
                                $("#v_location").val(responseObj["data"]['pradr']['addr']['loc']);
                                $("#v_city").val(responseObj["data"]['pradr']['addr']['city']);
                                $("#v_pin").val(responseObj["data"]['pradr']['addr']['pncd']);
                                $("#v_state").val(responseObj["data"]['pradr']['addr']['stcd']);
                                $("#v_district").val(responseObj["data"]['pradr']['addr']['dst']);

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
            </script>

        <?php
    } else {
        // console($validVendor);
        echo $validVendor['message'];
    }
} else {
        ?>
        <h5>Access Denied</h5>
    <?php
}
    ?>



        </body>

        </html>