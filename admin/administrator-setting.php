<?php
include("../app/v1/connection-admin.php");
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");

administratorAuth();

if(isset($_POST["saveAdministratorSettingsFormBtn"])){
    $saveSettingsObj = saveAdministratorSettings($_POST+$_FILES);
    //console($saveSettingsObj);
    swalToast($saveSettingsObj["status"], $saveSettingsObj["message"]);
    redirect(basename($_SERVER['PHP_SELF']));
}


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
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
                                <div class="row m-0 p-0">
                                    <div class="col-md-6">
                                        <span class="text-muted">Title</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="title" value="<?= getAdministratorSettings("title"); ?>" placeholder="Enter Title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Email</span>
                                        <div class="form-group">
                                            <input type="email" class="form-control" name="email" value="<?= getAdministratorSettings("email"); ?>" placeholder="Enter email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Phone</span>
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="phone" value="<?= getAdministratorSettings("phone"); ?>" placeholder="Enter phone no" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                    </div>
                                    <div class="col-md-12">
                                        <span class="text-muted">Address</span>
                                        <div class="form-group">
                                            <textarea class="form-control" name="address" placeholder="Address" rows="3"><?= getAdministratorSettings("address"); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
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
include("common/footer.php");
?>

<script>
    $(document).ready(function() {

    });
</script>