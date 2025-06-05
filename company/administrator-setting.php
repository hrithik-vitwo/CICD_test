<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
include("../app/v1/functions/admin/func-company.php");
require_once("./../app/v1/functions/common/func-common.php");
//console($_SESSION['logedCompanyAdminInfo']['fldAdminCompanyId']);
global $companyCountry;
$company_id = $_SESSION['logedCompanyAdminInfo']['fldAdminCompanyId'];
$components = getLebels($companyCountry)['data'];
$componentsjsn = json_decode($components, true);

// console($componentsjsn['constitution_of_business']['options']);


if (isset($_POST["saveAdministratorSettingsFormBtn"])) {

    $saveSettingsObj = saveCompanyDetails($_POST + $_FILES);
    // console($saveSettingsObj);

    swalToast($saveSettingsObj["status"], $saveSettingsObj["message"], COMPANY_URL);
    // redirect(basename($_SERVER['PHP_SELF']));
}



$companyData = getAllDataCompany($company_id);
//console($companyData);

$data = $companyData['data'][0];
//console($data);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Settings</a></li>
            </ol>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">

                <div class="col-12 m-0 p-0">
                    <div class="card card-primary card-tabs">
                        <div class="card-header">
                            <h3 class="card-title">Mange Settings</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" enctype="multipart/form-data" id="companyUpdate">
                                <input type="hidden" name="company_id" value="<?= $data['company_id'] ?>">
                                <div class="row m-0 p-0">
                                    <div class="col-md-4">
                                        <span class="text-muted">Name</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" value="<?= $data['company_name'] ?>" placeholder="Enter Name" required readonly>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <span class="text-muted">Select Time Zone</span>
                                        <div class="form-group">
                                            <select name="timeZone" class="form-control" required>
                                                <?php
                                                $allZones = ["Asia/Kolkata", "Asia/Dhaka", "Asia/Dubai", "Asia/Singapore"];
                                                $timeZone = getAdministratorSettings("timeZone");
                                                foreach ($allZones as $oneZone) {
                                                    if ($oneZone == $timeZone) {
                                                ?>
                                                            <option selected value="<?= $oneZone ?>"><?= $oneZone ?></option>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <option value="<?= $oneZone ?>"><?= $oneZone ?></option>
                                                            <?php
                                                        }
                                                    }

                                                            ?>
                                            </select>
                                        </div>
                                    </div> -->

                                    <div class="col-md-4">
                                        <span class="text-muted">Email</span>
                                        <div class="form-group">
                                            <input type="email" class="form-control" name="email" value="<?= $data['fldAdminEmail'] ?>" placeholder="Enter email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted">Phone</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="phone" value="<?= $data['fldAdminPhone'] ?>" placeholder="Enter phone no" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted">Code</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="code" value="<?= $data['company_code'] ?>" placeholder="Enter company code" required readonly>
                                        </div>
                                    </div>
                                    <?php if ($componentsjsn['fields']['taxNumber']) { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted"><?= $componentsjsn['fields']['taxNumber'] ?></span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="company_pan" value="<?= $data['company_pan'] ?>" placeholder="Enter <?= $componentsjsn['fields']['taxNumber'] ?>" required readonly>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($componentsjsn['fields']['company_if_num']) { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted"><?= $componentsjsn['fields']['company_if_num'] ?></span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="cin" value="<?= $data['company_cin'] ?>" placeholder="Enter <?= $componentsjsn['fields']['company_if_num'] ?>" <?php if (!empty($data['company_cin'])) { ?> readonly <?php } ?>>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($componentsjsn['fields']['businessID']) { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted"><?= $componentsjsn['fields']['businessID'] ?></span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="llpin" value="<?= $data['company_llpin'] ?>" placeholder="Enter <?= $componentsjsn['fields']['businessID'] ?>" <?php if (!empty($data['company_llpin'])) { ?> readonly <?php } ?>>
                                            </div>
                                        </div>
                                    <?php } ?>



                                    <div class="col-md-4">
                                        <div class="form-input">
                                            <label for="">Select MRP Priority</label>
                                            <select id="company_mrp_priority" name="company_mrp_priority" class="form-control">
                                                <option value="">Select MRP Priority</option>
                                                <option value="customer" <?php echo ($data['mrpPriority'] == "customer") ? 'selected' : ''; ?>>Customer</option>
                                                <option value="territory" <?php echo ($data['mrpPriority'] == "territory") ? 'selected' : ''; ?>>Territory</option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if ($componentsjsn['fields']['taxidNumber']) { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted"><?= $componentsjsn['fields']['taxidNumber'] ?></span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="tan" value="<?= $data['company_tan'] ?>" placeholder="Enter <?= $componentsjsn['fields']['taxidNumber'] ?>" <?php if (!empty($data['company_tan'])) { ?> readonly <?php } ?>>
                                            </div>
                                        </div>
                                    <?php } ?>



                                    <div class="col-md-4">
                                        <span class="text-muted">Constitution of Bussiness</span>
                                        <div class="form-group">
                                            <?php if ($componentsjsn['constitution_of_business']['type'] == 'dropdown') { ?>

                                                <select name="const" class="form-control" required >

                                                    <option selected value="">Enter Constitution of Bussiness</option>
                                                    <?php
                                                    foreach ($componentsjsn['constitution_of_business']['options'] as $const) {
                                                    ?>
                                                        <option value="<?= $const['key'] ?>" <?php if ($const['key'] == $data['company_const_of_business']) {echo "selected";} ?>><?= $const['label'] ?></option>
                                                    <?php
                                                    }

                                                    ?>
                                                </select>

                                            <?php } else { ?>
                                                <input type="text" class="form-control" name="const" value="<?= $data['company_const_of_business'] ?>" placeholder="Enter Constitution of Bussiness" required readonly>
                                            <?php } ?>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted">gl account length</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="gl_length" value="<?= $data['gl_account_length'] ?>" placeholder="Enter GL length" required readonly>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <span class="text-muted">gl lenghth breakup</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="gl_brkup" value="<?= $data['gl_length_bkup'] ?>" placeholder="Enter Length breakup" required readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted">Quantity Decimal</span>
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="decimal_quantity" value="<?= $data['decimal_quantity'] ?>" placeholder="Enter Quantity Decimal" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted">Amount Decimal</span>
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="decimal_value" value="<?= $data['decimal_value'] ?>" placeholder="Enter Amount Decimal" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="text-muted">PO Enable</span>
                                        <div class="form-group">

                                            <input type="checkbox" name="po_enable" value="1" <?php if ($data['isPoEnabled'] == 1) {
                                                                                                    echo "checked";
                                                                                                } ?>>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <span class="text-muted">QA Enable</span>
                                        <div class="form-group">

                                            <input type="checkbox" name="qa_enable" value="1" <?php if ($data['isQaEnabled'] == 1) {
                                                                                                    echo "checked";
                                                                                                } ?>>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="text-muted">Email Notification Enable</span>
                                        <div class="form-group">

                                            <input type="checkbox" name="isEmailActive" value="yes" <?php if ($data['isEmailActive'] == 'yes') {
                                                                                                        echo "checked";
                                                                                                    } ?>>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <span class="text-muted">Whatsapp Notification Enable</span>
                                        <div class="form-group">

                                            <input type="checkbox" name="isWhatsappActive" value="yes" <?php if ($data['isWhatsappActive'] == 'yes') {
                                                                                                            echo "checked";
                                                                                                        } ?>>
                                        </div>
                                    </div>



                                    <div class="col-md-6">
                                        <span class="text-muted">Select Language</span>
                                        <div class="form-group">
                                            <?php
                                            $languagesget = getAllLanguages();
                                            //console($languagesget);
                                            $languages = $languagesget['data'];


                                            ?>
                                            <select name="lang" class="form-control" required disabled>

                                                <option selected value="">Select language</option>
                                                <?php
                                                foreach ($languages as $language) {
                                                ?>
                                                    <option value="<?= $language['language_id'] ?>" <?php if ($language['language_id'] == $data['company_language']) {
                                                                                                        echo "selected";
                                                                                                    } ?>><?= $language['language_name'] ?></option>
                                                <?php
                                                }

                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Currency</span>
                                        <div class="form-group">
                                            <?php
                                            $currencyget = getAllCurrency();
                                            //      echo $data['company_currency'];
                                            //    console($currencyget);
                                            $currency = $currencyget['data'];


                                            ?>
                                            <select name="currency" class="form-control" required>

                                                <option selected value="">Select Currency</option>
                                                <?php
                                                foreach ($currency as $currency) {
                                                ?>
                                                    <option value="<?= $currency['currency_id'] ?>" <?php if ($currency['currency_id'] == $data['company_currency']) {
                                                                                                        echo "selected";
                                                                                                    } ?>><?= $currency['currency_name'] ?></option>
                                                <?php

                                                }

                                                ?>
                                            </select>
                                        </div>
                                    </div>





                                    <div class="col-md-6">
                                        <span class="text-muted">Signature</span>
                                        <div class="form-group">
                                            <?php
                                            if ($data['signature'] != "") {
                                            ?>
                                                <img src="<?= COMP_STORAGE_URL ?>/profile/<?= $data['signature'] ?>" alt="signature" style="height: 80px; width:auto;">
                                            <?php
                                            }
                                            ?>
                                            <input type="file" class="form-control mt-1" name="signature" placeholder="Signature">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Logo</span>
                                        <div class="form-group">
                                            <?php
                                            if ($data['company_logo'] != "") {
                                            ?>
                                                <img src="<?= COMP_STORAGE_URL ?>/profile/<?= $data['company_logo'] ?>" style="height: 80px; width:auto;">
                                            <?php
                                            }
                                            ?>
                                            <input type="file" class="form-control mt-1" name="logo" placeholder="Icon">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Favicon</span>
                                        <div class="form-group">
                                            <?php
                                            if ($data['company_favicon'] != "") {
                                            ?>
                                                <img src="<?= COMP_STORAGE_URL ?>/profile/<?= $data['company_favicon'] ?>" style="height: 80px; width:auto;">
                                            <?php
                                            }
                                            ?>
                                            <input type="file" class="form-control mt-1" name="favicon" placeholder="Fav Icon">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <span class="text-muted">Remarks</span>
                                        <div class="form-group">
                                            <textarea class="form-control" name="address" placeholder="remarks" rows="3"><?= $data['company_address'] ?></textarea>
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <span class="text-muted">Sales Invoice Declaration</span>

                                        <div class="form-group">
                                            <textarea class="form-control" id="salesInvDeclaration" rows="3" name="sales_invoice_declaration" placeholder="Sales Invoice Declaration"><?= $data['sales_invoice_declaration'] ?></textarea>

                                        </div>
                                    </div>


                                    <?php if ($companyCountry == 103) { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted">Building</span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="building" value="<?= $data['company_building'] ?>" placeholder="Enter Building">
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted">Building Name / Number</span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="building" value="<?= $data['company_building'] ?>" placeholder="Enter Building">
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($companyCountry == 103) { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted">Flat Number</span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="flat" value="<?= $data['company_flat_no'] ?>" placeholder="Enter Flat Number">
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted">Unit No.</span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="flat" value="<?= $data['company_flat_no'] ?>" placeholder="Enter Flat Number">
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($companyCountr == 103) { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted">State</span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="state" value="<?= $data['company_state'] ?>" placeholder="Enter State">
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted">State/Territory</span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="state" value="<?= $data['company_state'] ?>" placeholder="Enter State">
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class=" col-md-4 form-input">
                                        <label for="">Region</label>
                                        <?php
                                        $state_sql = queryGet("SELECT * FROM `erp_state_region` WHERE region_status='active'", true);
                                        $state_data = $state_sql['data']; ?>
                                        <select id="region" name="region" class="form-control regionDropDown" required>

                                            <option value="">Select Region</option>
                                            <?php foreach ($state_data as $regdata) {

                                            ?>
                                                <option value="<?= $regdata['region_id'] ?>" <?php if ($regdata['region_id'] == $data['region']) {
                                                                                                    echo "selected";
                                                                                                } ?>><?= $regdata['region_name'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        <span class="error region"></span>

                                    </div>
                                    <?php if ($companyCountry == 103) { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted">District</span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="district" value="<?= $data['company_district'] ?>" placeholder="Enter District">
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($companyCountry == 103) { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted">Location</span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="location" value="<?= $data['company_location'] ?>" placeholder="Enter Location">
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="col-md-4">
                                        <span class="text-muted">Postal Code</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="pin" value="<?= $data['company_pin'] ?>" placeholder="Enter Pin">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted">Street</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="street" value="<?= $data['company_street'] ?>" placeholder="Enter Street">
                                        </div>
                                    </div>
                                    <?php if ($companyCountry == 103) { ?>
                                        <div class="col-md-4">
                                            <span class="text-muted">City</span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="city" value="<?= $data['company_city'] ?>" placeholder="Enter City">
                                            </div>
                                        </div>
                                    <?php  } ?>
                                    <div class="col-md-4">
                                        <span class="text-muted">Country</span>
                                        <div class="form-group">
                                            <?php
                                            $countryget = getAllCountry();
                                            // console($countryget);
                                            $countries = $countryget['data'];
                                            ?>
                                            <select name="country" class="form-control" required disabled>

                                                <option selected value="">Country</option>
                                                <?php
                                                foreach ($countries as $country) {
                                                ?>
                                                    <option value="<?= $country['id'] ?>" <?php if ($country['id'] == $companyCountry) {
                                                                                                echo "selected";
                                                                                            } ?>><?= $country['name'] ?></option>
                                                <?php
                                                }

                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted">Opening Date</span>
                                        <div class="form-group">
                                            <input type="date" class="form-control" name="date" value="<?= $data['opening_date'] ?>" placeholder="Enter City">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <span class="text-muted">Footer</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="footer" value="<?= $data['company_footer'] ?>" placeholder="Copyright © 2022 Start-Project, All rights reserved.">
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex">
                                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btnstyle btn-outline-secondary mr-2">Cancel</a>
                                        <button type="submit" name="saveAdministratorSettingsFormBtn" class="btn btn-primary btnstyle">Save Settings</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->

<?php
include("common/footer.php");
?>

<script>
    $(document).ready(function() {

    });



    $(".add_data").click(function() {
        var data = this.value;
        $("#saveAdministratorSettingsFormBtn").val(data);
        //confirm('Are you sure to Submit?')
        $("#companyUpdate").submit();
    });
</script>