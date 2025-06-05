<?php
include("../app/v1/connection-customer-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
include("../app/v1/functions/customer/func-customer.php");


$customer_id = $_SESSION['logedCustomerAdminInfo']['customer_id'];
if (isset($_POST["saveAdministratorSettingsFormBtn"])) {
    $saveSettingsObj = saveCustomerSettings($_POST + $_FILES);
    //console($saveSettingsObj);
    swalToast($saveSettingsObj["status"], $saveSettingsObj["message"]);
    redirect(basename($_SERVER['PHP_SELF']));
}
$customerData = getAllDataCustomer($customer_id);
// console($customerData);

$data = $customerData['data'];
//console($data);
?>

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content">
        <div class="container-fluid">
            <!-- <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= CUSTOMER_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Settings</a></li>
            </ol> -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= CUSTOMER_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-cog po-list-icon"></i>Profile Setting</a></li>
                <li class="back-button">
                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
                        <i class="fa fa-reply po-list-icon"></i>
                    </a>
                </li>
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
                            <h3 class="card-title">Profile Setting</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" enctype="multipart/form-data">
                                <input type="hidden" value="<?= $customer_id ?>" name="customer_id">
                                <div class="row m-0 p-0">
                                    <div class="col-md-6">
                                        <div class="form-input">
                                            <label for="">Trade Name</label>
                                            <input type="text" class="form-control" name="trade_name" value="<?= $data[0]['trade_name'] ?>" placeholder="Enter Trade Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-input">
                                            <label for="">Customer Code</label>
                                            <input type="text" class="form-control" name="code" value="<?= $data[0]['customer_code'] ?>" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-input">
                                            <label for="">Customen Pan</label>
                                            <input type="text" class="form-control" name="pan" value="<?= $data[0]['customer_pan'] ?>" placeholder="" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-input">
                                            <label for="">Customer GSTIN</label>
                                            <input type="text" class="form-control" name="gst" value="<?= $data[0]['customer_gstin'] ?>" placeholder="" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-input">
                                            <label for="">Constitution of Bussiness</label>
                                            <input type="text" class="form-control" name="const" value="<?= $data[0]['constitution_of_business'] ?>" placeholder="" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-input">
                                            <label for="">Constitution of Bussiness</label>
                                            <input type="text" class="form-control" name="website" value="<?= $data[0]['customer_website'] ?>" placeholder="Enter website">
                                        </div>
                                    </div>



                                    <!-- <div class="col-md-6">
                                        <span class="text-muted">Select Time Zone</span>
                                        <div class="form-input">
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

                                    <div class="col-md-6">
                                        <div class="form-input">
                                            <label for="">Name</label>
                                            <input type="text" class="form-control" name="name" value="<?= $data[0]['customer_authorised_person_name'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-input">
                                            <label for="">Designation</label>
                                            <input type="text" class="form-control" name="desg" value="<?= $data[0]['customer_authorised_person_designation'] ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-input">
                                            <label for="">Email</label>
                                            <input type="email" class="form-control" name="email" value="<?= $data[0]['customer_authorised_person_email'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-input">
                                            <label for="">Email</label>
                                            <input type="number" class="form-control" name="phone" value="<?= $data[0]['customer_authorised_person_phone'] ?>" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-input">
                                            <label for="">Profile Photo</label>
                                            <?php
                                            if ($data[0]['customer_picture'] != "") {
                                            ?>
                                                <img src="<?= BASE_URL ?>public/storage/picture/<?= $data[0]['customer_picture'] ?>" style="max-height: 80px; min-height: 80px;">
                                            <?php } ?>
                                            <input type="file" class="form-control mt-1" name="profile_photo" placeholder="Icon">
                                        </div>
                                    </div>
                                </div>
                        </div>

                        </form>

                    </div>
                    
                    <div class="action-flex-btn">
                            <button href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-danger m-3 float-right">Cancel</button>
                            <button type="submit" name="saveAdministratorSettingsFormBtn" class="btn btn-primary m-3 float-right">Save Settings</button>
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