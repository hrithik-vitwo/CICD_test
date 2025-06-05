<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");


if(isset($_POST["saveChangePassFormBtn"])){
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
                            <h3 class="card-title">Change Password</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="row m-0 p-0">
                                    <div class="col-md-6">
                                        <span class="text-muted">Old Pass</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="oldpass" value="oldpass" placeholder="Old Password" required>
                                        </div>
                                    </div> 
                                    <div class="col-md-6">
                                        <span class="text-muted">New Password</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="newpass" value="newpass" placeholder="New Password" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">Confirm Password</span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="confirmpass" value="confirmpass" placeholder="Confirm Password" required>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex">
                                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btnstyle btn-outline-secondary mr-2">Cancel</a>
                                        <button type="submit" name="saveChangePassFormBtn" class="btn btn-primary btnstyle">Change Now</button>
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