<?php
include("../app/v1/connection-vendor-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");



if (isset($_POST['addMenuFormBtn'])) {
    $addAdministratorMenuObj = addAdministratorMenu($_POST);
    swalToast($addAdministratorMenuObj["status"], $addAdministratorMenuObj["message"]);
}

if (isset($_POST['addSubMenuFormBtn'])) {
    $addSubMenuObj = addAdministratorSubMenu($_POST);
    swalToast($addSubMenuObj["status"], $addSubMenuObj["message"]);
}

if (isset($_POST["updateMenuFormBtn"])) {
    $updateMenuObj = updateAdministratorMenu($_POST);
    swalToast($updateMenuObj["status"], $updateMenuObj["message"]);
}
if (isset($_POST["updateSubMenuFormBtn"])) {
    $updateSubMenuObj = updateAdministratorMenu($_POST);
    swalToast($updateSubMenuObj["status"], $updateSubMenuObj["message"]);
}



if (isset($_GET["menu-edit"]) && $_GET["menu-edit"] > 0) {
    $menuDetailsObj = getAdministratorMenuDetails($_GET["menu-edit"]);
    ?>
    
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header mb-2 p-0  border-bottom">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= VENDOR_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Sidebar Menu</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Menu Edit</a></li>
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
                                <h3 class="card-title">Manage Sidebar Menu <small class="text-muted">Edit</small></h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if ($menuDetailsObj["status"] == "success") {
                                    $menuDetails = $menuDetailsObj["data"];
                                    $statuses = ["active" => "Active", "inactive" => "Inactive", "deleted" => "Delete"];
                                ?>
                                    <form action="" method="POST">
                                        <div class="row m-0 p-0">
                                            <input type="hidden" name="menuKey" value="<?= $menuDetails["fldMenuKey"] ?>">
                                            <input type="hidden" name="parentMenuKey" value="<?= $menuDetails["fldParentMenuKey"] ?>">
                                            <div class="col-md-6">
                                                <span class="text-muted">Menu Name/Label:</span>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="menuLabel" value="<?= $menuDetails["fldMenuLabel"] ?>" placeholder="Enter label" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Menu File Name<small>(manage-user.php)</small>:</span>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="menuFile" value="<?= $menuDetails["fldMenuFile"] ?>" placeholder="Enter file name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Menu Icon<small>(HTML Class Name)</small>:</span>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="menuIcon" value="<?= htmlspecialchars($menuDetails["fldMenuIcon"]) ?>" placeholder="Enter icon" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Menu Order By:</span>
                                                <div class="form-group">
                                                    <input type="number" class="form-control" name="menuOrderBy" min="0" value="<?= $menuDetails["fldMenuOrderBy"] ?>" placeholder="Enter order no" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Menu Status:</span>
                                                <div class="form-group">
                                                    <select class="form-control" name="menuStatus" required>
                                                        <option value="">Select one option</option>
                                                        <?php
                                                        foreach ($statuses as $statusVal => $statusLabel) {
                                                            if ($statusVal == $menuDetails["fldMenuStatus"]) {
                                                                echo '<option value="' . $statusVal . '" selected >' . $statusLabel . '</option>';
                                                            } else {
                                                                echo '<option value="' . $statusVal . '">' . $statusLabel . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-outline-secondary mr-2">Cancel</a>
                                                <button type="submit" name="updateMenuFormBtn" class="btn btn-primary">Modify Menu Details</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php
                                } else {
                                    echo $menuDetailsObj["message"];
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
} elseif (isset($_GET["submenu-edit"]) && $_GET["submenu-edit"] > 0) {
    $menuDetailsObj = getAdministratorMenuDetails($_GET["submenu-edit"]);
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header mb-2 p-0  border-bottom">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= VENDOR_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Sidebar Menu</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Sub Menu Edit</a></li>
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
                                <h3 class="card-title">Manage Sidebar Sub Menu <small class="text-muted">Edit</small></h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if ($menuDetailsObj["status"] == "success") {
                                    $menuDetails = $menuDetailsObj["data"];
                                    $statuses = ["active" => "Active", "inactive" => "Inactive", "deleted" => "Delete"];
                                ?>
                                    <form action="" method="POST">
                                        <div class="row m-0 p-0">
                                            <input type="hidden" name="menuKey" value="<?= $menuDetails["fldMenuKey"] ?>">
                                            <div class="col-md-6">
                                                <span class="text-muted">Select Menu:</span>
                                                <div class="form-group">
                                                    <select name="parentMenuKey" class="form-control" required>
                                                        <option value="">Select One Menu</option>
                                                        <?php
                                                        $sql = "SELECT * FROM `tbl_vendor_admin_menu` WHERE `fldParentMenuKey`=0 AND `fldMenuStatus`='active'";
                                                        if ($res = mysqli_query($dbCon, $sql)) {
                                                            while ($row = mysqli_fetch_assoc($res)) {
                                                                if ($menuDetails["fldParentMenuKey"] == $row["fldMenuKey"]) {
                                                                    echo '<option value="' . $row['fldMenuKey'] . '" selected>' . $row['fldMenuLabel'] . '</option>';
                                                                } else {
                                                                    echo '<option value="' . $row['fldMenuKey'] . '">' . $row['fldMenuLabel'] . '</option>';
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Menu Name/Label:</span>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="menuLabel" value="<?= $menuDetails["fldMenuLabel"] ?>" placeholder="Enter label" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Menu File Name<small>(manage-user.php)</small>:</span>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="menuFile" value="<?= $menuDetails["fldMenuFile"] ?>" placeholder="Enter file name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Menu Icon<small>(HTML Class Name)</small>:</span>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="menuIcon" value="<?= htmlspecialchars($menuDetails["fldMenuIcon"]) ?>" placeholder="Enter icon" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Menu Order By:</span>
                                                <div class="form-group">
                                                    <input type="number" class="form-control" name="menuOrderBy" min="0" value="<?= $menuDetails["fldMenuOrderBy"] ?>" placeholder="Enter order no" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Menu Status:</span>
                                                <div class="form-group">
                                                    <select class="form-control" name="menuStatus" required>
                                                        <option value="">Select one option</option>
                                                        <?php
                                                        foreach ($statuses as $statusVal => $statusLabel) {
                                                            if ($statusVal == $menuDetails["fldMenuStatus"]) {
                                                                echo '<option value="' . $statusVal . '" selected >' . $statusLabel . '</option>';
                                                            } else {
                                                                echo '<option value="' . $statusVal . '">' . $statusLabel . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-outline-secondary mr-2">Cancel</a>
                                                <button type="submit" name="updateSubMenuFormBtn" class="btn btn-primary">Modify Sub Menu Details</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php
                                } else {
                                    echo $menuDetailsObj["message"];
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
                    <li class="breadcrumb-item"><a href="<?= VENDOR_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                    <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Sidebar Menu</a></li>
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
                                        <h3 class="card-title">Manage Sidebar Menu</h3>
                                    </li>
                                    <li class="nav-item ml-auto">
                                        <a class="nav-link active" id="listTab" data-toggle="pill" href="#listTabPan" role="tab" aria-controls="listTabPan" aria-selected="true">Menu List</a>
                                    </li>
                                    <li class="nav-item mr-1">
                                        <a class="nav-link" id="addNewTab" data-toggle="pill" href="#addNewTabPan" role="tab" aria-controls="addNewTabPan" aria-selected="false">Add Menu</a>
                                    </li>
                                    <li class="nav-item mr-1">
                                        <a class="nav-link" id="addSubNewTab" data-toggle="pill" href="#addSubNewTabPan" role="tab" aria-controls="addSubNewTabPan" aria-selected="false">Add Sub Menu</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th class="w-25">Menu</th>
                                                <th>Sub Menus</th>
                                            </tr>
                                            <?php foreach (getAdministratorMenuList()['data'] as $menuDetails) { ?>
                                                <tr>
                                                    <td> <a class="btn btn-outline-primary btnstyle menu-btn" href="?menu-edit=<?= $menuDetails["fldMenuKey"] ?>"><label class="rounded"><?= $menuDetails["fldMenuIcon"] ?><?= $menuDetails['fldMenuLabel'] ?></label><i class="nav-icon fas fa-edit" style="cursor: pointer;"></i></a></td>
                                                    <td>
                                                        <?php foreach (getAdministratorMenuList($menuDetails['fldMenuKey'])['data'] as $subMenuDetails) { ?>
                                                            <span class="form-group mr-2">
                                                                 <a class="btn btn-outline-primary btnstyle menu-btn" href="?submenu-edit=<?= $subMenuDetails["fldMenuKey"] ?>"><label class="rounded"><?= $subMenuDetails["fldMenuIcon"] ?> <?= $subMenuDetails['fldMenuLabel'] ?></label><i class="nav-icon fas fa-edit" style="cursor: pointer;"></i></a>
                                                            </span>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="addNewTabPan" role="tabpanel" aria-labelledby="addNewTab">
                                        <form action="" method="POST">
                                            <div class="row m-0 p-0">
                                                <div class="col-md-6">
                                                    <span class="text-muted">Menu Name/Label:</span>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="menuLabel" placeholder="Enter label" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">Menu File Name<small>(manage-user.php)</small>:</span>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="menuFile" placeholder="Enter file name">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">Menu Icon<small>(HTML Class Name)</small>:</span>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="menuIcon" placeholder="Enter icon" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">Menu Order By:</span>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" name="menuOrderBy" min="0" placeholder="Enter order no" required>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btnstyle btn-outline-secondary mr-2">Cancel</a>
                                                    <button type="submit" name="addMenuFormBtn" class="btn btn-primary btnstyle">Add New Menu</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane fade" id="addSubNewTabPan" role="tabpanel" aria-labelledby="addSubNewTab">
                                        <form action="" method="POST">
                                            <div class="row m-0 p-0">
                                                <div class="col-md-6">
                                                    <span class="text-muted">Select Menu:</span>
                                                    <div class="form-group">
                                                        <select name="menuKey" class="form-control" required>
                                                            <option value="">Select One Menu</option>
                                                            <?php
                                                            $sql = "SELECT * FROM `tbl_vendor_admin_menu` WHERE `fldParentMenuKey`=0 AND `fldMenuStatus`='active'";
                                                            if ($res = mysqli_query($dbCon, $sql)) {
                                                                while ($row = mysqli_fetch_assoc($res)) {
                                                                    echo '<option value="' . $row['fldMenuKey'] . '">' . $row['fldMenuLabel'] . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">Menu Name/Label:</span>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="subMenuLabel" placeholder="Enter label" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">Menu File Name<small>(manage-user.php)</small>:</span>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="subMenuFile" placeholder="Enter file" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">Menu Icon<small>(HTML Class Name)</small>:</span>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="subMenuIcon" placeholder="Enter icon" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="text-muted">Menu Order By:</span>
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="subMenuOrderBy" placeholder="Enter order no" required>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btnstyle btn-outline-secondary mr-2">Cancel</a>
                                                    <button type="submit" name="addSubMenuFormBtn" class="btn btn-primary btnstyle">Add New Sub Menu</button>
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
include("common/footer.php");
?>

<script>
    $(document).ready(function() {

    });
</script>