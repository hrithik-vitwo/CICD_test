<?php
require_once("../app/v1/connection-branch-admin.php");
administratorAuth();
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("../app/v1/functions/branch/func-others-location.php");

if (isset($_POST["changeStatus"])) {
    $newStatusObj = administratorFuncChangeStatus($_POST, "tbl_admin_details", "fldAdminKey", "fldAdminStatus");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["addNewAdministratorFormBtn"])) {
    $addNewObj = addNewAdministratorUser($_POST + $_FILES);
    swalToast($addNewObj["status"], $addNewObj["message"]);
}

if (isset($_POST["editAdministratorFormBtn"])) {
    $editDataObj = updateAdministratorUserDetails($_POST);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}



if (isset($_GET["view"]) && $_GET["view"] > 0) {
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header mb-2 p-0  border-bottom">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Administrators</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">View</a></li>
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
                                <h3 class="card-title">Administrator Details <small class="text-muted">View</small></h3>
                            </div>
                            <div class="card-body">
                                <?php
                                $viewResult = getAdministratorUserDetails($_GET["view"]);
                                if ($viewResult["status"] == "success") {
                                    $viewData = $viewResult["data"];
                                    //console($viewData);
                                ?>
                                    <table class="table">
                                        <tr>
                                            <td class="text-muted">Name</td>
                                            <td><?= $viewData["fldAdminName"] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Phone</td>
                                            <td><?= $viewData["fldAdminPhone"] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Email</td>
                                            <td><?= $viewData["fldAdminEmail"] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Role</td>
                                            <td><b><?= $viewData["fldRoleName"] ?></b></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Avatar</td>
                                            <td><img src="<?= BASE_URL ?>/public/storage/avatar/<?= $viewData["fldAdminAvatar"] ?>" alt="Avatar" style="max-height: 50px;"></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Password</td>
                                            <td><?= $viewData["fldAdminPassword"] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Create Date</td>
                                            <td><?= $viewData["fldAdminCreatedAt"] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Update Date</td>
                                            <td><?= $viewData["fldAdminUpdatedAt"] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Note</td>
                                            <td><?= $viewData["fldAdminNotes"] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Status</td>
                                            <td><b><?= ucfirst($viewData["fldAdminStatus"]) ?></b></td>
                                        </tr>
                                    </table>
                                <?php
                                    //console($viewData);
                                } else {
                                ?>
                                    <div class="col-12 my-2">
                                        <?= $viewResult["message"] ?>
                                    </div>
                                <?php
                                }
                                ?>

                                <div class="col-12 m-0 p-0">
                                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-warning text-light">Cancel</a>
                                </div>
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
} elseif (isset($_GET["edit"]) && $_GET["edit"] > 0) {
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header mb-2 p-0  border-bottom">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Administrators</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Edit</a></li>
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
                                <h3 class="card-title">Administrator Details <small class="text-muted">Edit</small></h3>
                            </div>
                            <div class="card-body">
                                <?php
                                $editResult = getAdministratorUserDetails($_GET["edit"]);
                                if ($editResult["status"] == "success") {
                                    $editData = $editResult["data"];
                                ?>
                                    <form action="" method="POST">

                                        <input type="hidden" name="adminKey" value="<?= $editData["fldAdminKey"] ?>">
                                        <div class="row m-0 p-0">
                                            <div class="col-md-6">
                                                <span class="text-muted">Full Name:</span>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="adminName" value="<?= $editData["fldAdminName"] ?>" placeholder="Enter Full Name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Select Role:</span>
                                                <div class="form-group">
                                                    <select name="adminRole" class="form-control" required>
                                                        <option value="">Select One Role</option>
                                                        <?php
                                                        $listResult = getAllAdministratorRoles();
                                                        if ($listResult["status"] == "success") {
                                                            foreach ($listResult["data"] as $listRow) {
                                                                if ($listRow["fldRoleStatus"] == "active") {
                                                                    if ($listRow["fldRoleKey"] == $editData["fldAdminRole"]) {
                                                                        echo '<option value="' . $listRow["fldRoleKey"] . '" selected>' . $listRow["fldRoleName"] . '</option>';
                                                                    } else {
                                                                        echo '<option value="' . $listRow["fldRoleKey"] . '">' . $listRow["fldRoleName"] . '</option>';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Email:</span>
                                                <div class="form-group">
                                                    <input type="email" class="form-control" name="adminEmail" value="<?= $editData["fldAdminEmail"] ?>" placeholder="Enter email" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Phone:</span>
                                                <div class="form-group">
                                                    <input type="number" class="form-control" name="adminPhone" value="<?= $editData["fldAdminPhone"] ?>" placeholder="Enter phone no" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Password:</span>
                                                <div class="form-group">
                                                    <input type="password" class="form-control" name="adminPassword" value="<?= $editData["fldAdminPassword"] ?>" placeholder="Enter password ****">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Profile Photo <small>(Optional)</small>:</span><img src="<?= $editData["fldAdminAvatar"] ?>" style="height: 50px;">
                                                <div class="form-group">
                                                    <input type="file" class="form-control" name="adminAvatar" placeholder="Profile photo">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-warning text-light">Cancel</a>
                                                <button type="submit" name="editAdministratorFormBtn" class="btn btn-dark">Modify Admin</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php
                                } else {
                                ?>
                                    <p><?= $editResult["message"] ?></p>
                                    <div class="col-12 m-0 p-0">
                                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-warning text-light">Cancel</a>
                                    </div>
                                <?php
                                }
                                ?>
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
} else {
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header mb-2 p-0  border-bottom">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Administrators</a></li>
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
                            <div class="card-header p-0 pt-1">
                                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-2 px-3">
                                        <h3 class="card-title">Administrator List</h3>
                                    </li>
                                    <li class="nav-item ml-auto">
                                        <a class="nav-link active" id="listTab" data-toggle="pill" href="#listTabPan" role="tab" aria-controls="listTabPan" aria-selected="true">List</a>
                                    </li>
                                    <li class="nav-item mr-1">
                                        <a class="nav-link" id="addNewTab" data-toggle="pill" href="#addNewTabPan" role="tab" aria-controls="addNewTabPan" aria-selected="false">Add New</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                        <?php
                                        $listResult = getAllAdministratorUsers();
                                        if ($listResult["status"] == "success") {
                                        ?>
                                            <table class="table defaultDataTable table-hover text-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Role For</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sl = 0;
                                                    foreach ($listResult["data"] as $listRow) {
                                                        $listRowKey = $listRow["fldAdminKey"];
                                                        $listRowStatus = $listRow["fldAdminStatus"];
                                                        $statusClass = ($listRowStatus == "active") ? "text-success" : "text-warning";
                                                        if($listRow["fldAdminBranchLocationId"]!=0){ 
                                                            $getDataDetails=getDataDetails($listRow["fldAdminBranchLocationId"]); 
                                                            $roleforrr= $getDataDetails['data']['othersLocation_name'].' ['.$getDataDetails['data']['othersLocation_code'].']'; 
                                                        }else{
                                                            $roleforrr= 'Self';
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td><?= $sl += 1; ?></td>
                                                            <td><?= $listRow["fldAdminName"] ?></td>
                                                            <td><?= $listRow["fldAdminEmail"]; ?></td>
                                                            <td><b><?= $listRow["fldRoleName"] ?></b></td>
                                                            <td><b><?php echo $roleforrr;  ?></b></td>
                                                            <td>
                                                                <form action="" method="POST">
                                                                    <input type="hidden" name="id" value="<?= $listRowKey ?>">
                                                                    <input type="hidden" name="changeStatus" value="active_inactive">
                                                                    <button type="submit" onclick="return confirm('Are you sure change status?')" class="p-0 m-0 ml-2" style="cursor: pointer; border:none" data-toggle="tooltip" data-placement="top" title="<?= $listRowStatus ?>">
                                                                        <?php echo ($listRowStatus == "active") ? '<i class="fa fa-toggle-on text-success" ></i>' : '<i class="fa fa-toggle-off text-warning"></i>'; ?>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <div class="action-btn p-0 m-0">
                                                                    <a href="<?= basename($_SERVER['PHP_SELF']) . "?view=" . $listRowKey; ?>" style="cursor: pointer;" class="ml-2"><i class="fa fa-eye text-primary">hahahah</i></a>
                                                                    <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $listRowKey; ?>" style="cursor: pointer;" class="ml-2"><i class="fa fa-edit text-success"></i></a>
                                                                    <form action="" method="POST">
                                                                        <input type="hidden" name="id" value="<?= $listRowKey ?>">
                                                                        <input type="hidden" name="changeStatus" value="delete">
                                                                        <button type="submit" onclick="return confirm('Are you sure to delete?')" class="p-0 m-0 ml-2" style="cursor: pointer; border:none"><i class='fa fa-trash text-danger'></i></button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        <?php
                                        } else {
                                            echo "<p class='ml-3'>" . $listResult["message"] . "</p>";
                                        }
                                        ?>
                                    </div>
                                    <div class="tab-pane fade" id="addNewTabPan" role="tabpanel" aria-labelledby="addNewTab">
                                        <form action="" method="POST" enctype="multipart/form-data">
                                            <div class="row m-0 p-0">
                                                <div class="col-md-6">
                                                    <span class="text-muted">Select Role:</span>
                                                    <div class="form-group">
                                                        <select name="adminRole" id="adminRole" class="form-control" required>
                                                            <option value="">Select One Role</option>
                                                            <?php
                                                            $listResult = getAllAdministratorRoles();
                                                            if ($listResult["status"] == "success") {
                                                                foreach ($listResult["data"] as $listRow) {
                                                                    if ($listRow["fldRoleStatus"] == "active") {
                                                                        echo '<option value="' . $listRow["fldRoleKey"] . '">' . $listRow["fldRoleName"] . '</option>';
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 role_for_n" style="display: none;">
                                                    <span class="text-muted">Select Role For:</span>
                                                    <div class="form-group fldAdminBranchLocationId">

                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <span class="text-muted">Full Name:</span>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="adminName" placeholder="Enter Full Name" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <span class="text-muted">Email</span>
                                                    <div class="form-group">
                                                        <input type="email" class="form-control" name="adminEmail" placeholder="Enter email" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">Phone</span>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" name="adminPhone" placeholder="Enter phone no" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">Password</span>
                                                    <div class="form-group">
                                                        <input type="password" class="form-control" name="adminPassword" placeholder="Enter password ****">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">Profile Photo <small>(Optional)</small></span>
                                                    <div class="form-group">
                                                        <input type="file" class="form-control" name="adminAvatar" placeholder="Profile photo" accept=".jpg, .jpeg, .png">
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-warning text-light">Cancel</a>
                                                    <button type="submit" name="addNewAdministratorFormBtn" class="btn btn-dark">Add New Admin</button>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
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
}

require_once("common/footer.php");
?>

<script>
    $(document).ready(function() {

        $(document).on("change", '#adminRole', function() {
            let menufor = $(this).val();
            if (menufor != '') {
                $.ajax({
                    url: 'ajaxs/ajax_get_role_user_type.php',
                    data: {
                        menufor
                    },
                    type: 'POST',
                    beforeSend: function() {
                        $('.role_for_n').show();
                        $('.fldAdminBranchLocationId').show();
                        $('.fldAdminBranchLocationId').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    },
                    success: function(responseData) {
                        $('.fldAdminBranchLocationId').show();
                        $('.fldAdminBranchLocationId').html('');
                        $(".fldAdminBranchLocationId").html(responseData);
                    }
                });
            } else {
                $('.role_for_n').hide();

            }
        });
    });
</script>