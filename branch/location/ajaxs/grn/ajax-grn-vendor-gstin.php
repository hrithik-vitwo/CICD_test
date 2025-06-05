<?php
include_once("../../../../app/v1/connection-branch-admin.php");


function isVendorExist($GSTIN = null)
{
    global $company_id;
    $check = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "`  WHERE company_id=$company_id AND `vendor_gstin`='" . $GSTIN . "'");
    if ($check['numRows'] >= 1) {
        return true;
    } else {
        return false;
        //exit(); 
    }
}
$grnId = $_GET['grnId'];


if (isVendorExist($_GET["gstin"])) {
    // echo "Customer already exists!";
    //console($check);
    swalAlert("warning", "Opps!", "Vendor already exists!", LOCATION_URL . "manage-vendors.php?create");
} else {

    $vendor_code = getRandCodeNotInTable(ERP_VENDOR_DETAILS, 'vendor_code');
    if ($vendor_code['status'] == 'success') {
        $vendor_code = $vendor_code['data'];
    } else {
        $vendor_code = '';
    }

    if (isset($_GET["gstin"]) && !empty($_GET["gstin"])) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.mastergst.com/public/search?email=developer@vitwo.in&gstin=' . $_GET["gstin"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
                'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
                'Accept: application/json'
            ),
        ));

        $resultGst = curl_exec($curl);
        $resultGstData = json_decode($resultGst, true);

        if (isset($resultGstData["data"]) && count($resultGstData["data"]) > 0) {

            $gstDetails = $resultGstData["data"];
            $gstStatus = $resultGstData["data"]["sts"];
            $gstRegDate = $resultGstData["data"]["rgdt"];
            $legal_name = $resultGstData['data']['lgnm'];
            $gstLastUpdate = $resultGstData["data"]["lstupdt"];
            // console($gstDetails);
            $vendorPan = substr($_GET["gstin"], 2, 10);
            $othersaddress_count = count($resultGstData['data']['adadr']);
            if (empty($gstDetails['pradr']['addr']['city'])) {
                $city =  $gstDetails['pradr']['addr']['loc'];
            } else {
                $city = $gstDetails['pradr']['addr']['city'];
            }

?>

            <div class="card-body">
                <div class="multisteps-form__content">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">GST</label>
                                <input type="hidden" name="pendingGrnId" value="<?= $grnId ?>">
                                <input type="text" class="form-control" name="vendorGstin" id="vendor_gstin" value="<?php echo $_GET["gstin"]; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">Pan *</label>
                                <input type="text" class="form-control" name="vendor_pan" id="vendor_pan" value="<?php echo $vendorPan; ?>" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">Trade Name</label>
                                <input type="text" class="form-control" name="trade_name" id="trade_name" value="<?php echo $gstDetails['tradeNam']; ?>" required>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">Legal Name</label>
                                <input type="text" class="form-control" name="legal_name" id="legal_name" value="<?php echo $legal_name; ?>" required>
                            </div>
                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">Constitution of Business</label>
                                <input type="text" class="form-control" name="con_business" id="con_business" value="<?php echo $gstDetails['ctb']; ?>" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">Flat Number</label>
                                <input type="text" class="form-control" name="flat_no" id="flat_no" value="<?php echo $gstDetails['pradr']['addr']['flno']; ?>" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">Building Number</label>
                                <input type="text" class="form-control" name="build_no" id="build_no" value="<?php echo $gstDetails['pradr']['addr']['bno']; ?>" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">Street Name</label>
                                <input type="text" class="form-control" name="street_name" id="street_name" value="<?php echo $gstDetails['pradr']['addr']['st']; ?>" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">Location</label>
                                <input type="text" class="form-control" name="location" id="location" value="<?php echo $gstDetails['pradr']['addr']['loc']; ?>" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">City</label>
                                <input type="text" class="form-control" name="city" id="city" value="<?php echo $city; ?>" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">Pin Code</label>
                                <input type="number" class="form-control" name="pincode" id="pincode" value="<?php echo $gstDetails['pradr']['addr']['pncd']; ?>" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">Country</label>
                                <select id="country" name="country" class="form-control stateDropDown" required>
                                    <?php
                                    $country_sql = queryGet("SELECT * FROM `erp_countries`", true);
                                    $country_data = $country_sql['data'];
                                    foreach ($country_data as $con_data) {

                                    ?>
                                        <option value="<?= $con_data['name'] ?>" <?php if ($con_data['name'] == "India") {
                                                                                        echo "selected";
                                                                                    } ?>><?= $con_data['name'] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <!-- <input type="text" class="form-control" name="state" id="state" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>"> -->
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">State</label>
                                <select id="state" name="state" class="form-control stateDropDown" required>
                                    <?php
                                    $state_sql = queryGet("SELECT * FROM `erp_gst_state_code`", true);
                                    $state_data = $state_sql['data'];
                                    foreach ($state_data as $data) {

                                    ?>
                                        <option value="<?= $data['gstStateCode'] . "|" . $data['gstStateName'] ?>" <?php if ($data['gstStateCode'] == $gstDetails['pradr']['addr']['stcd'] || $data['gstStateName'] == $gstDetails['pradr']['addr']['stcd']) {
                                                                                                                    echo "selected";
                                                                                                                } ?>><?= $data['gstStateName'] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <!-- <input type="text" class="form-control" name="state" id="state" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>"> -->
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">District</label>
                                <input type="text" class="form-control" name="district" id="district" value="<?php echo $gstDetails['pradr']['addr']['dst']; ?>" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="forform-control">
                                <label for="">Credit Period (days)</label>
                                <input type="number" name="creditPeriod" placeholder="E.g 30" class="form-control" required />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="col-md-12">
                <div class="input-group btn-col">
                    <button type="submit" class="btn btn-primary btnstyle" id="vendorQuickAddFormSubmitBtn">Add Vendor</button>
                </div>
            </div>


        <?php

        }
    } else {
        ?>

        <div class="card-body">
            <div class="multisteps-form__content">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">GST</label>
                            <input type="hidden" name="pendingGrnId" value="<?= $grnId ?>">
                            <input type="text" class="form-control" name="vendor_gstin" id="vendor_gstin" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">Pan *</label>
                            <input type="text" class="form-control" name="vendor_pan" id="vendor_pan" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">Trade Name</label>
                            <input type="text" class="form-control" name="trade_name" id="trade_name" value="" required>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">Legal Name</label>
                            <input type="text" class="form-control" name="legal_name" id="legal_name" value="" required>
                        </div>
                    </div>


                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">Constitution of Business</label>
                            <input type="text" class="form-control" name="con_business" id="con_business" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">Flat Number</label>
                            <input type="text" class="form-control" name="flat_no" id="flat_no" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">Building Number</label>
                            <input type="text" class="form-control" name="build_no" id="build_no" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">Street Name</label>
                            <input type="text" class="form-control" name="street_name" id="street_name" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">Location</label>
                            <input type="text" class="form-control" name="location" id="location" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">City</label>
                            <input type="text" class="form-control" name="city" id="city" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">Pin Code</label>
                            <input type="number" class="form-control" name="pincode" id="pincode" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">Country</label>
                            <select id="country" name="country" class="form-control stateDropDown" required>
                                <?php
                                $country_sql = queryGet("SELECT * FROM `erp_countries`", true);
                                $country_data = $country_sql['data'];
                                foreach ($country_data as $con_data) {

                                ?>
                                    <option value="<?= $con_data['name'] ?>" <?php if ($con_data['name'] == "India") {
                                                                                    echo "selected";
                                                                                } ?>><?= $con_data['name'] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <!-- <input type="text" class="form-control" name="state" id="state" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>"> -->
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">State</label>
                            <select id="state" name="state" class="form-control stateDropDown" required>
                                <?php
                                $state_sql = queryGet("SELECT * FROM `erp_gst_state_code`", true);
                                $state_data = $state_sql['data'];
                                foreach ($state_data as $data) {

                                ?>
                                    <option value="<?= $data['gstStateCode'] ?>"><?= $data['gstStateName'] ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <!-- <input type="text" class="form-control" name="state" id="state" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>"> -->
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">District</label>
                            <input type="text" class="form-control" name="district" id="district" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="forform-control">
                            <label for="">Credit Period (days)</label>
                            <input type="number" name="creditPeriod" placeholder="E.g 30" class="form-control" required />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <div class="col-md-12">
            <div class="input-group btn-col">
                <button type="submit" class="btn btn-primary btnstyle" id="vendorQuickAddFormSubmitBtn">Add Vendor</button>
            </div>
        </div>

<?php
    }
}
