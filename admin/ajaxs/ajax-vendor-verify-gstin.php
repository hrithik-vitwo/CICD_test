<?php
include_once("../../app/v1/connection-admin.php");

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
            try {
                $resultGstData = json_decode($resultGst, true);
                if (isset($resultGstData["error"]) && $resultGstData["error"] != "false") {
                    $gstDetails = $resultGstData["data"];
                    $vendorPan = substr($_GET["gstin"], 2,10);
                    ?>
                    <div class="col-12" id="accordion">
                        <div class="card card-primary card-outline">
                            <a class="d-block w-100" data-toggle="collapse" href="#collapseOne">
                                <div class="card-header p-0">
                                    <h4 class="card-title bg-primary p-2" style="clip-path: polygon(0 0, 100% 0, 80% 100%, 0 100%);">
                                        <span class="pr-5">Basic Details</span>
                                    </h4>
                                </div>
                            </a>
                            <div id="collapseOne" class="collapse show" data-parent="#accordion">
                                <div class="card-body">
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Vendor ID</label>
                                                <input type="text" class="form-control" value="VEN-<?= rand(111111, 999999) ?>" name="vendorId" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Opening Balance</label>
                                                <input type="number" class="form-control" placeholder="00.00" name="vendorOpeningBalance">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">PAN *</label>
                                                <input type="text" class="form-control" id="vendorPanNo" placeholder="PAN" name="vendorPan" value="<?= $vendorPan ?>" required>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">TAN</label>
                                                <input type="text" class="form-control" placeholder="TAN" name="vendorTan">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Name</label>
                                                <input type="text" class="form-control" placeholder="Name" name="vendorName" value="<?= isset($gstDetails["lgnm"]) ? $gstDetails["lgnm"] : "" ?>" required>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Email</label>
                                                <input type="email" class="form-control" placeholder="Email" name="vendorEmail">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Trade Name</label>
                                                <input type="text" class="form-control disable" placeholder="Trade Name" value="<?= isset($gstDetails["tradeNam"]) ? $gstDetails["tradeNam"] : "" ?>" name="vendorTrandeName" required>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Constitution of Business</label>
                                                <input type="text" class="form-control" placeholder="Constitution of Business" value="<?= isset($gstDetails["ctb"]) ? $gstDetails["ctb"] : "" ?>" name="vendorConstitutionBusiness">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Building Number</label>
                                                <input type="text" class="form-control disable" placeholder="Building Number" value="<?= isset($gstDetails["ctb"]) ? $gstDetails["ctb"] : "" ?>" name="vendorBuildingNumber" required>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Flat Number</label>
                                                <input type="text" class="form-control" placeholder="Flat Number" name="vendorFlatNumber">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Street Name</label>
                                                <input type="text" class="form-control disable" placeholder="Street Name" name="vendorStreetName" value="<?= isset($gstDetails["pradr"]["addr"]["st"]) ? $gstDetails["pradr"]["addr"]["st"] : "" ?>">
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Pin Code</label>
                                                <input type="text" class="form-control" placeholder="Pin Code" value="<?= isset($gstDetails["pradr"]["addr"]["pncd"]) ? $gstDetails["pradr"]["addr"]["pncd"] : "" ?>" name="vendorPinCode">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Location</label>
                                                <input type="text" class="form-control disable" placeholder="Location" name="vendorLocation" value="<?= isset($gstDetails["pradr"]["addr"]["loc"]) ? $gstDetails["pradr"]["addr"]["loc"] : "" ?>">
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">City</label>
                                                <input type="text" class="form-control" placeholder="City" name="vendorCity" value="<?= isset($gstDetails["pradr"]["addr"]["city"]) ? $gstDetails["pradr"]["addr"]["city"] : "" ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">District</label>
                                                <input type="text" class="form-control" placeholder="District" name="vendorDistrict" required value="<?= isset($gstDetails["pradr"]["addr"]["dst"]) ? $gstDetails["pradr"]["addr"]["dst"] : "" ?>">
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">State</label>
                                                <input type="text" class="form-control" placeholder="State" name="vendorState" value="<?= isset($gstDetails["pradr"]["addr"]["stcd"]) ? $gstDetails["pradr"]["addr"]["stcd"] : "" ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Alternate Eamil</label>
                                                <input type="email" class="form-control" placeholder="Alternate Eamil" name="vendorAltEmail">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Status</label>
                                                <input type="text" class="form-control" placeholder="Status" value="<?= isset($gstDetails["sts"]) ? $gstDetails["sts"] : "" ?>" id="vendorStatus" name="vendorStatus">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-primary card-outline">
                            <a class="d-block w-100" data-toggle="collapse" href="#collapseTwo">
                                <div class="card-header p-0">
                                    <h4 class="card-title bg-primary p-2" style="clip-path: polygon(0 0, 100% 0, 90% 100%, 0 100%);">
                                        <span class="pr-5">Other Business Addresses</span>
                                    </h4>
                                </div>
                            </a>
                            <div id="collapseTwo" class="collapse" data-parent="#accordion">
                                <div class="card-body">
                                    <div class="row m-0 p-2">
                                        <!-- <div class="h5 text-bold ml-1">1. Address</div> -->
                                        <div class="ml-auto mr-2">
                                            <span class="btn btn-warning btn-sm text-light deleteOtherAddressBtns" id="deleteOtherAddressBtn_1">Don't Have</span>
                                            <span class="btn btn-success btn-sm addNewOtherAddress">Add New</span>
                                        </div>
                                    </div>
                                    <div id="otherAddressesListDiv">
                                        <div id="otherAddressItem_<?= $listItemKey ?>">
                                            <p><?= $listItemKey ?></p>.
                                            <div class="ml-auto mr-2">
                                                <span class="btn btn-warning btn-sm text-light deleteOtherAddressBtns" id="deleteOtherAddressBtn_<?= $listItemKey ?>">Delete</span>
                                                <span class="btn btn-success btn-sm addNewOtherAddress">Add New</span>
                                            </div>
                                            <div class="row m-0 p-0">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">GST Legal Name</label>
                                                        <input type="text" class="form-control" placeholder="GST Legal Name" name="vendorBranchGstLegalName[]" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">GST Trade Name</label>
                                                        <input type="text" class="form-control" placeholder="GST Trade Name" name="vendorBranchGstTradeName[]" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row m-0 p-0">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">Constitution of Business</label>
                                                        <input type="text" class="form-control" placeholder="GST Legal Name" name="vendorBranchConstitutionBusiness[]" required>
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">Building Number</label>
                                                        <input type="text" class="form-control" placeholder="Building Number" name="vendorBranchBuildingNumber[]" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row m-0 p-0">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">Flat Number</label>
                                                        <input type="text" class="form-control" placeholder="Flat Number" name="vendorBranchFlatNumber[]" required>
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">Street Name</label>
                                                        <input type="text" class="form-control" placeholder="Street Name" name="vendorBranchStreetName[]" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row m-0 p-0">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">Pin Code</label>
                                                        <input type="text" class="form-control" placeholder="Pin Code" name="vendorBranchPinCode[]" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">Location</label>
                                                        <input type="text" class="form-control" placeholder="Location" name="vendorBranchLocation[]" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row m-0 p-0">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">City</label>
                                                        <input type="text" class="form-control" placeholder="City" name="vendorBranchCity[]" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">District</label>
                                                        <input type="text" class="form-control" placeholder="District" name="vendorBranchDistrict[]" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row m-0 p-0">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">State</label>
                                                        <input type="text" class="form-control" placeholder="State" name="vendorBranchState[]" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="text-muted">Status</label>
                                                        <input type="text" class="form-control" placeholder="Status" name="vendorBranchStatus[]" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-primary card-outline">
                            <a class="d-block w-100" data-toggle="collapse" href="#collapseThree">
                                <div class="card-header p-0">
                                    <h4 class="card-title bg-primary p-2" style="clip-path: polygon(0 0, 100% 0, 80% 100%, 0 100%);">
                                        <span class="pr-5">Bank Details</span>
                                    </h4>
                                </div>
                            </a>
                            <div id="collapseThree" class="collapse" data-parent="#accordion">
                                <div class="card-body">

                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">IFSC</label>
                                                <input type="text" class="form-control" placeholder="IFSC" name="vendorIfsc[]" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Bank Name</label>
                                                <input type="text" class="form-control" placeholder="Bank Name" name="vendorBankName[]" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Bank Branch Name</label>
                                                <input type="text" class="form-control" placeholder="Bank Branch Name" name="vendorBankBranchName[]" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Bank Address</label>
                                                <input type="text" class="form-control" placeholder="Bank Address" name="vendorBankAddress[]" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Bank Account Number</label>
                                                <input type="text" class="form-control" placeholder="Bank Account Number" name="vendorBankAccountNumber[]" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Upload Cancelled Cheque</label>
                                                <input type="text" class="form-control" placeholder="Upload Cancelled Cheque" name="vendorBankCancelledCheque[]" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-primary card-outline">
                            <a class="d-block w-100" data-toggle="collapse" href="#collapseFour">
                                <div class="card-header p-0">
                                    <h4 class="card-title bg-primary p-2" style="clip-path: polygon(0 0, 100% 0, 80% 100%, 0 100%);">
                                        <span class="pr-5">Other Details</span>
                                    </h4>
                                </div>
                            </a>
                            <div id="collapseFour" class="collapse" data-parent="#accordion">
                                <div class="card-body">
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">FSSAI</label>
                                                <input type="text" class="form-control" placeholder="FSSAI" name="vendorFSSAI" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Credit Period</label>
                                                <input type="text" class="form-control" placeholder="Credit Period" name="vendorCreditPeriod" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Name of Authorised Person</label>
                                                <input type="text" class="form-control" placeholder="Name of Authorised Person" name="vendorAuthorisedPersonName" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Phone</label>
                                                <input type="text" class="form-control" placeholder="Phone" name="vendorPhone" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Website</label>
                                                <input type="text" class="form-control" placeholder="Website" name="vendorWebsite" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Picture</label>
                                                <input type="text" class="form-control" placeholder="Picture" name="vendorPicture" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row m-0 p-0">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Designation</label>
                                                <input type="text" class="form-control" placeholder="Designation" name="vendorDesignation" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-muted">Enabled</label>
                                                <select class="form-control" name="vendorEnabled">
                                                    <option value="yes" selected>Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success px-5 mt-2">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    swalToast("warning","Something went wrong try again!");
                }
            } catch (Exception $ee) {
                swalToast("warning","Something went wrong try again!");
            }
        } else {
            swalToast("warning","Please provide valid gstin number!");
        }
    } else {
        swalToast("warning","Something went wrong try again with valid credentials!");
    }
} catch (Exception $e) {
    swalToast("warning","Something went wrong try again to auth!");
}
curl_close($curl);
