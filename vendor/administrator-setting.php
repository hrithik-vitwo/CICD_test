<?php
include("../app/v1/connection-vendor-admin.php");
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
include("../app/v1/functions/vendor/func-vendor.php");

administratorAuth();

// if(isset($_POST["saveAdministratorSettingsFormBtn"])){
//     $saveSettingsObj = saveAdministratorSettings($_POST+$_FILES);
//     //console($saveSettingsObj);
//     swalToast($saveSettingsObj["status"], $saveSettingsObj["message"]);
//     redirect(basename($_SERVER['PHP_SELF']));
// }


$vendor_id = $_SESSION['logedVendorAdminInfo']['fldAdminVendorId'];
if(isset($_POST["saveAdministratorSettingsFormBtn"])){ 
    $saveSettingsObj = saveCustomerSettings($_POST+$_FILES);
    //console($saveSettingsObj);
    swalToast($saveSettingsObj["status"], $saveSettingsObj["message"]);
   // redirect(basename($_SERVER['PHP_SELF']));
}
$customerData =getAllDataVendor($vendor_id);
//\console($customerData);

$data = $customerData['data']; 

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= VENDOR_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
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
                            <form action="" method="POST" enctype="multipart/form-data">
                                <input type="hidden" value = "<?= $vendor_id ?>" name ="vendor_id">
                                <div class="row m-0 p-0">
                                    <div class="col-md-6">
                                        <span class="text-muted">Trade Name</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="trade_name" value="<?= $data[0]['trade_name'] ?>" placeholder="Enter Trade Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Vendor Code</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="code" value="<?= $data[0]['vendor_code'] ?>" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Vendor Pan</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="pan" value="<?= $data[0]['vendor_pan'] ?>" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Vendor GSTIN</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="gst" value="<?= $data[0]['vendor_gstin'] ?>" placeholder="" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Constitution of Bussiness</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="const" value="<?= $data[0]['constitution_of_business'] ?>" placeholder="" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Vendor Website</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="website" value="<?= $data[0]['vendor_website'] ?>" placeholder="Enter website" >
                                        </div>
                                    </div>

                                    

                                    <!-- <div class="col-md-6">
                                        <span class="text-muted">Select Time Zone</span>
                                        <div class="form-group">
                                            <select name="timeZone" class="form-control" required>
                                                <?php 
                                                    $allZones=["Asia/Kolkata","Asia/Dhaka","Asia/Dubai","Asia/Singapore"];
                                                    $timeZone = getAdministratorSettings("timeZone");
                                                    foreach($allZones as $oneZone){
                                                        if($oneZone == $timeZone){
                                                            ?>
                                                            <option selected value="<?= $oneZone ?>"><?= $oneZone ?></option>
                                                            <?php
                                                        }else{
                                                            ?>
                                                            <option value="<?= $oneZone ?>"><?= $oneZone ?></option>
                                                            <?php
                                                        }
                                                    }
                                                
                                                ?>
                                            </select>
                                        </div>
                                    </div> -->

                                    <div class="col-md-6">
                                        <span class="text-muted">Name</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" value="<?= $data[0]['vendor_authorised_person_name'] ?>"  required>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <span class="text-muted">Designation</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="desg" value="<?= $data[0]['vendor_authorised_person_email'] ?>"  required>
                                        </div>
                                    </div> -->

                                    <div class="col-md-6">
                                        <span class="text-muted">Email</span>
                                        <div class="form-group">
                                            <input type="email" class="form-control" name="email" value="<?= $data[0]['vendor_authorised_person_email'] ?>"  required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Phone</span>
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="phone" value="<?= $data[0]['vendor_authorised_person_phone'] ?>" placeholder="" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Opening balance</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="opening_balance" value="<?= $data[0]['vendor_opening_balance'] ?>"  >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Currency</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="currency" value="<?= $data[0]['vendor_currency'] ?>"  >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Credit Period</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="credit" value="<?= $data[0]['vendor_credit_period'] ?>"  >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Profile Photo</span>
                                        <div class="form-group">
                                           <?php  
                                            if($data[0]['vendor_picture'] != ""){
                                                ?>
                                            <img src="<?=BASE_URL?>public/storage/picture/<?= $data[0]['vendor_picture'] ?>" style="max-height: 80px; min-height: 80px;">
                                            <?php } ?>
                                            <input type="file" class="form-control mt-1" name="profile_photo" placeholder="Icon">
                                        </div>
                                    </div>
                                  
                                    <!-- <div class="col-md-12">
                                        <span class="text-muted">Address</span>
                                        <div class="form-group">
                                            <textarea class="form-control" name="address" placeholder="Address" rows="3"><?= getAdministratorSettings("address"); ?></textarea>
                                        </div>
                                    </div> -->
                                    <!-- <div class="col-md-12">
                                        <span class="text-muted">Footer</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="footer" value="<?= getAdministratorSettings("footer"); ?>" placeholder="Copyright © 2022 Start-Project, All rights reserved.">
                                        </div>
                                    </div> -->

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
</script>