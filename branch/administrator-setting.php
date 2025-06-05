<?php
require_once("../app/v1/connection-branch-admin.php");
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("../app/v1/functions/branch/func-branch.php");

administratorAuth();

//console($_SESSION['logedBranchAdminInfo']['fldAdminBranchId']);
if(isset($_POST["saveAdministratorSettingsFormBtn"])){
    $saveSettingsObj = saveBranchSettings($_POST+$_FILES);
    //console($saveSettingsObj);
    swalToast($saveSettingsObj["status"], $saveSettingsObj["message"]);
  //  redirect(basename($_SERVER['PHP_SELF']));
} 

$branchData = getAllDataBranch($branch_id);
//console($branchData);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
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
                                <input type="hidden" name="id" value="<?= $branchData['data']['branch_id'] ?>">
                                <div class="row m-0 p-0">
                                <div class="col-md-6">
                                        <span class="text-muted">Company Name</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" value="<?= $branchData['data']['company_name'] ?>" placeholder="Enter Title" required readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Company Code</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" value="<?= $branchData['data']['company_code'] ?>" placeholder="Enter Title" required readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Name</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" value="<?= $branchData['data']['branch_name'] ?>" placeholder="Enter Title" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Branch Code</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" value="<?= $branchData['data']['branch_code'] ?>" placeholder="Enter Title" readonly>
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
                                        <span class="text-muted">Branch GST</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="gst" value="<?= $branchData['data']['branch_gstin'] ?>" placeholder="Enter GST" >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">GST Username</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="gstUsername" value="<?= $branchData['data']['branch_gstin_username'] ?>" placeholder="Enter GST Username" >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">E-Invoice Username</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="eInvocieUsername" value="<?= $branchData['data']['branch_einvoice_username'] ?>" placeholder="E-Invoice username" >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">E-Invoice Password</span>
                                        <div class="form-group">
                                            <input type="password" class="form-control" name="eInvociePassword" value="<?= $branchData['data']['branch_einvoice_password'] ?>" placeholder="E-Invoice password" >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Constitution of Business</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="const" value="<?= $branchData['data']['con_business'] ?>" placeholder="Enter constitution of business" >
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <span class="text-muted">Logo</span>
                                        <div class="form-group">
                                            <img src="<?=BASE_URL?>/public/storage/logo/<?= getAdministratorSettings("logo"); ?>" style="max-height: 80px; min-height: 80px;">
                                            <input type="file" class="form-control mt-1" name="logo" placeholder="Icon">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Favicon</span>
                                        <div class="form-group">
                                            <img src="<?=BASE_URL?>/public/storage/logo/<?= getAdministratorSettings("favicon"); ?>" style="max-height: 80px; min-height: 80px;">
                                            <input type="file" class="form-control mt-1" name="favicon" placeholder="Fav Icon">
                                        </div>
                                    </div> -->
                                    <div class="col-md-6">
                                        <span class="text-muted">Building Number</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="build" value="<?= $branchData['data']['build_no'] ?>" placeholder="Enter Building Number" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Flat Number</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="flat" value="<?= $branchData['data']['flat_no'] ?>" placeholder="Enter Flat Number" >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Street</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="street" value="<?= $branchData['data']['street_name'] ?>" placeholder="Enter Street name" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Locality</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="locality" value="<?= $branchData['data']['location'] ?>" placeholder="Enter Locality" >
                                        </div>
                                    </div>




                                    <div class="col-md-6">
                                        <span class="text-muted">City</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="city" value="<?= $branchData['data']['city'] ?>" placeholder="Enter City" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">District</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="district" value="<?= $branchData['data']['district'] ?>" placeholder="Enter District" >
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">State</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="state" value="<?= $branchData['data']['state'] ?>" placeholder="Enter State" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Pin Code</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="pin" value="<?= $branchData['data']['pincode'] ?>" placeholder="Enter Pin Code" >
                                        </div>
                                    </div>


                                   

                                    <div class="col-md-6">
                                        <span class="text-muted">Footer</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="footer" value="<?= getAdministratorSettings("footer"); ?>" placeholder="Copyright © 2022 Start-Project, All rights reserved.">
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
require_once("common/footer.php");
?>

<script>
    $(document).ready(function() {

    });
</script>