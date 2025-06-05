<?php
include("../app/v1/connection-admin.php");
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");

administratorAuth();

if (isset($_POST["addNewAdminRoleFormBtn"])) {
    $addNewAdminRoleObj = addNewAdministratorRole($_POST);
    swalToast($addNewAdminRoleObj["status"], $addNewAdminRoleObj["message"]);
}

if (isset($_POST["updateAdminRoleFormBtn"])) {
    $updateMenuObj = updateAdministratorRole($_POST);
    swalToast($updateMenuObj["status"], $updateMenuObj["message"]);
}

if (isset($_POST["changeStatus"])) {
    $newStatusObj = administratorFuncChangeStatus($_POST, "tbl_admin_roles", "fldRoleKey", "fldRoleStatus");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}



if (isset($_GET["view"]) && $_GET["view"] > 0) {
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header mb-2 p-0  border-bottom">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Roles</a></li>
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
                                <h3 class="card-title">Administrator Roles Details <small class="text-muted">View</small></h3>
                            </div>
                            <div class="card-body">
                                <?php
                                $viewResult = getAdministratorRoleDetails($_GET["view"]);

                                if ($viewResult["status"] == "success") {
                                    $viewData = $viewResult["data"];
                                    ?>
                                    <table class="table">
                                        <tr>
                                            <td class="text-muted">Role Name</td>
                                            <td><?= $viewData["fldRoleName"] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Role Accesses</td>
                                            <td><b><?= (getAdministratorAccessesNames($viewData["fldRoleAccesses"])["data"]); ?></b></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Role Description</td>
                                            <td><?= $viewData["fldRoleDescription"] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Role Create Date</td>
                                            <td><?= $viewData["fldRoleCreatedAt"] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Role Update Date</td>
                                            <td><?= $viewData["fldRoleUpdatedAt"] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Role Status</td>
                                            <td><b><?= ucfirst($viewData["fldRoleStatus"]) ?></b></td>
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

                                <div class="col-12">
                                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-warning btnstyle text-light">Cancel</a>
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

} else if (isset($_GET["edit"]) && $_GET["edit"] > 0) {

?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header mb-2 p-0  border-bottom">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Roles</a></li>
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
                                <h3 class="card-title">Administrator Role Details <small class="text-muted">Edit</small></h3>
                            </div>
                            <div class="card-body">
                                <?php
                                $editResult = getAdministratorRoleDetails($_GET["edit"]);
                                if ($editResult["status"] == "success") {
                                    $editData = $editResult["data"];
                                    $roleAccessesArray = explode(",", $editData["fldRoleAccesses"]);
                                ?>
                                    <form action="" method="POST">
                                        <input type="hidden" name="editKey" value="<?= $editData["fldRoleKey"] ?>">
                                        <div class="form-group">
                                            <span class="text-muted">Role Name:</span>
                                            <input type="text" class="form-control" name="roleName" value="<?= $editData["fldRoleName"] ?>" placeholder="Enter Role Name" required>
                                        </div>
                                        <div class="form-group">
                                            <span class="text-muted">Role Description:</span>
                                            <textarea class="form-control" rows="3" name="roleDescription" placeholder="Enter Role Description (Optional)"><?= $editData["fldRoleDescription"] ?></textarea>
                                        </div>
                                        <?php
                                        if ($editData["fldRoleAccesses"] == 0) {
                                            ?>
                                            <div class="form-group">
                                                <span class="text-muted">Select Menu & Sub-Menu:</span>
                                                <input type="hidden" name="menuFiles[]" value="0">
                                                <p class="bg-dark text-light p-2">This is super user role, Only <i>"Role Name"</i> and <i>"Role Description"</i> can be change!</p>
                                            </div>
                                            <?php
                                        } else {
                                        ?>
                                            <div class="form-group">
                                                <span class="text-muted">Select Menu & Sub-Menu:</span>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th class="w-25">Menu</th>
                                                        <th>Sub Menus</th>
                                                    </tr>
                                                    <?php foreach (getAdministratorMenuList()['data'] as $menuDetails) { ?>
                                                        <tr>
                                                            <td><label class="text-muted"><?= $menuDetails['fldMenuLabel'] ?></label></td>
                                                            <td>
                                                                <?php foreach (getAdministratorMenuList($menuDetails['fldMenuKey'])['data'] as $subMenuDetails) {
                                                                    if (in_array($subMenuDetails["fldMenuKey"], $roleAccessesArray)) {
                                                                ?>
                                                                        <span class="form-group mr-2">
                                                                            <input type="checkbox" checked name="menuFiles[]" class="form-control-default" value="<?= $subMenuDetails['fldMenuKey'] ?>">
                                                                            <label class="text-muted"><?= $subMenuDetails['fldMenuLabel'] ?></label>
                                                                        </span>
                                                                    <?php
                                                                    } else {
                                                                    ?>
                                                                        <span class="form-group mr-2">
                                                                            <input type="checkbox" name="menuFiles[]" class="form-control-default" value="<?= $subMenuDetails['fldMenuKey'] ?>">
                                                                            <label class="text-muted"><?= $subMenuDetails['fldMenuLabel'] ?></label>
                                                                        </span>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                        <div class="col-12">
                                            <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-warning btnstyle text-light">Cancel</a>
                                            <button type="submit" name="updateAdminRoleFormBtn" class="btn btn-dark btnstyle">Modify Role</button>
                                        </div>
                                    </form>
                                <?php
                                } else {
                                ?>
                                    <div class="col-12 my-2">
                                        <?= $editResult["message"] ?>
                                    </div>
                                    <div class="col-12">
                                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-warning btnstyle text-light">Cancel</a>
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
                    <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Roles</a></li>
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
                                        <h3 class="card-title">Administrator Roles Details</h3>
                                    </li>
                                    <li class="nav-item ml-auto">
                                        <a class="nav-link active" id="listTab" data-toggle="pill" href="#listTabPan" role="tab" aria-controls="listTabPan" aria-selected="true">Role List</a>
                                    </li>
                                    <li class="nav-item mr-1">
                                        <a class="nav-link" id="addNewTab" data-toggle="pill" href="#addNewTabPan" role="tab" aria-controls="addNewTabPan" aria-selected="false">Add New Role</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                        <?php
                                        $listResult = getAllAdministratorRoles();
                                        if ($listResult["status"] == "success") {
                                        ?>
                                            <table class="table defaultDataTable table-hover text-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Role Name</th>
                                                        <!-- <th>Descriptions</th> -->
                                                        <!-- <th>Accesses</th> -->
                                                        <th>Last Modify</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sl = 0;
                                                    foreach ($listResult["data"] as $listRow) {
                                                        $listRowKey = $listRow["fldRoleKey"];
                                                        $listRowStatus = $listRow["fldRoleStatus"];
                                                        $statusClass = ($listRowStatus == "active") ? "text-success" : "text-warning";
                                                    ?>
                                                        <tr>
                                                            <td><?= $sl += 1; ?></td>
                                                            <td><?= $listRow["fldRoleName"] ?></td>
                                                            <!-- <td><?= $listRow["fldRoleDescription"] ?></td> -->
                                                            <!-- <td><?= (getAdministratorAccessesNames($roleAccesses = $listRow["fldRoleAccesses"])["data"]); ?></td> -->
                                                            <td><?= explode(" ", $listRow["fldRoleUpdatedAt"])[0]; ?></td>
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
                                                                <div class="row p-0 m-0">
                                                                    <a href="<?= basename($_SERVER['PHP_SELF']) . "?view=" . $listRowKey; ?>" style="cursor: pointer;" class="ml-2"><i class="fa fa-eye text-primary"></i></a>
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
                                            ?>
                                            <div class="col-12 my-2">
                                                <?= $listResult["message"] ?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="tab-pane fade" id="addNewTabPan" role="tabpanel" aria-labelledby="addNewTab">
                                        <form action="" method="POST">
                                            <div class="form-group">
                                                <span class="text-muted">Role Name:</span>
                                                <input type="text" class="form-control" name="roleName" placeholder="Enter Role Name" required>
                                            </div>
                                            <div class="form-group">
                                                <span class="text-muted">Role Description:</span>
                                                <textarea class="form-control" rows="3" name="roleDescription" placeholder="Enter Role Description (Optional)"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <span class="text-muted">Select Menu & Sub-Menu:</span>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th class="w-25">Menu</th>
                                                        <th>Sub Menus</th>
                                                    </tr>
                                                    <?php foreach (getAdministratorMenuList()['data'] as $menuDetails) { ?>
                                                        <tr>
                                                            <td><label class="text-muted"><?= $menuDetails['fldMenuLabel'] ?></label></td>
                                                            <td>
                                                                <?php foreach (getAdministratorMenuList($menuDetails['fldMenuKey'])['data'] as $subMenuDetails) { ?>
                                                                    <span class="form-group mr-2">
                                                                        <input type="checkbox" name="menuFiles[]" class="form-control-default" value="<?= $subMenuDetails['fldMenuKey'] ?>">
                                                                        <label class="text-muted"><?= $subMenuDetails['fldMenuLabel'] ?></label>
                                                                    </span>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            </div>

                                            <div class="col-12">
                                                <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-warning btnstyle text-light">Cancel</a>
                                                <button type="submit" name="addNewAdminRoleFormBtn" class="btn btn-dark btnstyle">Add New Role</button>
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
include("common/footer.php");
?>

<script>
    $(document).ready(function() {

    });
</script>