<?php
require_once("../app/v1/connection-company-admin.php");
administratorAuth();
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("../app/v1/functions/branch/func-others-location.php");

if (isset($_POST["changeStatus"])) {
    $newStatusObj = administratorFuncChangeStatus($_POST, "tbl_company_admin_details", "fldAdminKey", "fldAdminStatus");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["addNewAdministratorFormBtn"])) {
    // console($_POST);
    $addNewObj = addNewLocationAdministratorUser($_POST + $_FILES);
    // console($addNewObj);
    // exit();

    swalToast($addNewObj["status"], $addNewObj["message"]);
}

if (isset($_POST["editAdministratorFormBtn"])) {
    $editDataObj = updateAdministratorUserDetails($_POST);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>

<style>
    .card.creator-priviladge-card .row,
    .card.approval-priviladge-card .row {
        padding: 10px 0;
        border-bottom: 1px solid #0030601f;
    }

    .card.creator-priviladge-card .row .col,
    .card.approval-priviladge-card .row .col {
        padding-left: 10px;
    }

    .card.creator-priviladge-card .row:nth-child(1) .col,
    .card.approval-priviladge-card .row:nth-child(1) .col {
        padding-left: 0px;
    }

    .bgColorModule {
        background-color: #b2d1ed;
    }

    .company-user-modal .modal-header {
        height: 200px !important;
    }

    .branch-user-modal .modal-header {
        height: 200px !important;
    }

    .location-user-modal .modal-header {
        height: 200px !important;
    }
</style>

<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">

<?php
if (isset($_GET["view"]) && $_GET["view"] > 0) {
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header mb-2 p-0  border-bottom">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
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
                    <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
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
                    <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
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
                                        <h3 class="card-title  text-white">Administrator List</h3>
                                    </li>
                                    <li class="nav-item ml-auto">
                                        <a class="nav-link active" id="listCompany" data-toggle="pill" href="#listCompanyPan" role="tab" aria-controls="listCompanyPan" aria-selected="true">Company Users</a>
                                    </li>
                                    <li class="nav-item  mr-0">
                                        <a class="nav-link " id="listBranch" data-toggle="pill" href="#listBranchPan" role="tab" aria-controls="listBranchPan" aria-selected="true">Branch Users</a>
                                    </li>
                                    <li class="nav-item mr-0">
                                        <a class="nav-link " id="listLocation" data-toggle="pill" href="#listLocationPan" role="tab" aria-controls="listLocationPan" aria-selected="true">Location Users</a>
                                    </li>
                                    <li class="nav-item mr-0">
                                        <a class="nav-link" id="addNewTab" data-toggle="pill" href="#addNewTabPan" role="tab" aria-controls="addNewTabPan" aria-selected="false">Add Location User</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <!-- Company User -->
                                    <div class="tab-pane fade show active" id="listCompanyPan" role="tabpanel" aria-labelledby="listCompany">
                                        <?php
                                        $listResult1 = getAllAdministratorUsers();
                                        // console($listResult1);

                                        if ($listResult1["status"] == "success") {
                                        ?>
                                            <table class="table defaultDataTable table-hover text-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Company Code</th>
                                                        <th>Company Name</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Role For</th>
                                                        <!-- <th>Status</th>-->
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sl = 0;
                                                    foreach ($listResult1["data"] as $listRow1) {
                                                        $listRow1Key = $listRow1["fldAdminKey"];
                                                        $listRow1Status = $listRow1["fldAdminStatus"];
                                                        $statusClass = ($listRow1Status == "active") ? "text-success" : "text-warning";
                                                        if ($listRow1["fldAdminBranchLocationId"] != 0) {
                                                            $getDataDetails = getDataDetails($listRow1["fldAdminBranchLocationId"]);
                                                            $roleforrr = $getDataDetails['data']['othersLocation_name'] . ' [' . $getDataDetails['data']['othersLocation_code'] . ']';
                                                        } else {
                                                            $roleforrr = 'Self';
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td><?= $sl += 1; ?></td>
                                                            <td><?= $listRow1["company_code"] ?></td>
                                                            <td><?= $listRow1["company_name"]; ?></td>
                                                            <td><?= $listRow1["fldAdminName"] ?></td>
                                                            <td><?= $listRow1["fldAdminEmail"]; ?></td>
                                                            <td><b><?= $listRow1["fldRoleName"] ?></b></td>
                                                            <td><b><?php echo $roleforrr;  ?></b></td>
                                                            <td>
                                                                <a style="cursor: pointer;" class="btn btn-sm" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_CompanyUser"><i class="fa fa-eye po-list-icon"></i></a>
                                                            </td>
                                                            <!-- <td>
                                                                <form action="" method="POST">
                                                                    <input type="hidden" name="id" value="<?= $listRow1Key ?>">
                                                                    <input type="hidden" name="changeStatus" value="active_inactive">
                                                                    <button type="submit" onclick="return confirm('Are you sure change status?')" class="p-0 m-0 ml-2" style="cursor: pointer; border:none" data-toggle="tooltip" data-placement="top" title="<?= $listRow1Status ?>">
                                                                        <?php echo ($listRow1Status == "active") ? '<i class="fa fa-toggle-on text-success" ></i>' : '<i class="fa fa-toggle-off text-warning"></i>'; ?>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <div class="action-btn p-0 m-0">
                                                                    <a href="<?= basename($_SERVER['PHP_SELF']) . "?view=" . $listRow1Key; ?>" style="cursor: pointer;" class="ml-2"><i class="fa fa-eye text-primary"></i></a>
                                                                    <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $listRow1Key; ?>" style="cursor: pointer;" class="ml-2"><i class="fa fa-edit text-success"></i></a>
                                                                    <form action="" method="POST">
                                                                        <input type="hidden" name="id" value="<?= $listRow1Key ?>">
                                                                        <input type="hidden" name="changeStatus" value="delete">
                                                                        <button type="submit" onclick="return confirm('Are you sure to delete?')" class="p-0 m-0 ml-2" style="cursor: pointer; border:none"><i class='fa fa-trash text-danger'></i></button>
                                                                    </form>
                                                                </div>
                                                            </td> -->
                                                        </tr>


                                                        <!-------------company-user-modal-start------------>


                                                        <div class="modal fade right company-user-modal customer-modal" id="fluidModalRightSuccessDemo_CompanyUser" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <div class="display-flex-space-between mt-4 mb-3">
                                                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                <!-- <li class="nav-item">
                                                                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= str_replace('/', '-', $listRow1["fldAdminKey"]) ?>">Info</a>
                                                                                </li>
                                                                               -->
                                                                                <!-- -------------------Audit History Button Start------------------------- -->
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $listRow1["fldAdminKey"]) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $listRow1["fldAdminKey"]) ?>" href="#history<?= str_replace('/', '-', $listRow1["fldAdminKey"]) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $listRow1["fldAdminKey"]) ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                                                                </li>
                                                                                <!-- -------------------Audit History Button End------------------------- -->
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="tab-content" id="myTabContent">
                                                                            <!-- <div class="tab-pane fade show active" id="home<?= $onePrList['rfqId'] ?>" role="tabpanel" aria-labelledby="home-tab"></div> -->
                                                                            <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                            <div class="tab-pane fade show active" id="history<?= str_replace('/', '-', $listRow1["fldAdminKey"]) ?>" role="tabpanel" aria-labelledby="history-tab">

                                                                                <div class="audit-head-section mb-3 mt-3 ">
                                                                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($listRow1['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($listRow1['created_at']) ?></p>
                                                                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($listRow1['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($listRow1['updated_at']) ?></p>
                                                                                </div>
                                                                                <hr>
                                                                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $listRow1["fldAdminKey"]) ?>">

                                                                                    <ol class="timeline">

                                                                                        <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                            <div class="new-comment font-bold">
                                                                                                <p>Loading...
                                                                                                <ul class="ml-3 pl-0">
                                                                                                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                </ul>
                                                                                                </p>
                                                                                            </div>
                                                                                        </li>
                                                                                        <p class="mt-0 mb-5 ml-5">Loading...</p>

                                                                                        <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                            <div class="new-comment font-bold">
                                                                                                <p>Loading...
                                                                                                <ul class="ml-3 pl-0">
                                                                                                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                </ul>
                                                                                                </p>
                                                                                            </div>
                                                                                        </li>
                                                                                        <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                                                    </ol>
                                                                                </div>
                                                                            </div>
                                                                            <!-- -------------------Audit History Tab Body End------------------------- -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!-------------company-user-modal-finish------------>


                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        <?php
                                        } else {
                                            echo "<p class='ml-3'>" . $listResult1["message"] . "</p>";
                                        }
                                        ?>
                                    </div>
                                    <!-- Branch User -->
                                    <div class="tab-pane fade" id="listBranchPan" role="tabpanel" aria-labelledby="listBranch">
                                        <?php
                                        $listResult2 = getBranchAllAdministratorUsers();
                                        // console($listResult2);
                                        if ($listResult2["status"] == "success") {
                                        ?>
                                            <table class="table defaultDataTable table-hover text-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Company Code</th>
                                                        <th>Company Name</th>
                                                        <th>Branch Code</th>
                                                        <th>Branch Name</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sl = 0;
                                                    foreach ($listResult2["data"] as $listRow2) {
                                                        $listRow2Key = $listRow2["fldAdminKey"];
                                                        $listRow2Status = $listRow2["fldAdminStatus"];
                                                        $statusClass = ($listRow2Status == "active") ? "text-success" : "text-warning";

                                                    ?>
                                                        <tr>
                                                            <td><?= $sl += 1; ?></td>
                                                            <td><?= $listRow2["company_code"] ?></td>
                                                            <td><?= $listRow2["company_name"]; ?></td>
                                                            <td><?= $listRow2["branch_code"] ?></td>
                                                            <td><?= $listRow2["state"] ?></td>
                                                            <td><?= $listRow2["fldAdminName"] ?></td>
                                                            <td><?= $listRow2["fldAdminEmail"]; ?></td>
                                                            <td><b><?= $listRow2["fldRoleName"] ?></b></td>
                                                            <td>
                                                                <form action="" method="POST">
                                                                    <input type="hidden" name="id" value="<?= $listRow2Key ?>">
                                                                    <input type="hidden" name="changeStatus" value="active_inactive">
                                                                    <button type="submit" onclick="return confirm('Are you sure change status?')" class="btn btn-sm" style="cursor: pointer; border:none" data-toggle="tooltip" data-placement="top" title="<?= $listRow2Status ?>">
                                                                        <?php echo ($listRow2Status == "active") ? '<span class="status">active</span>' : '<span class="status-warning"></span>'; ?>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <div class="action-btn p-0 m-0">
                                                                    <a style="cursor: pointer;" class="btn btn-sm" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_BranchUser"><i class="fa fa-eye po-list-icon"></i></a>
                                                                    <a style="cursor: pointer;" class="btn btn-sm"><i class="fa fa-edit po-list-icon"></i></a>
                                                                    <form action="" method="POST">
                                                                        <input type="hidden" name="id" value="<?= $listRow2Key ?>">
                                                                        <input type="hidden" name="changeStatus" value="delete">
                                                                        <button type="submit" onclick="return confirm('Are you sure to delete?')" class="btn btn-sm" style="cursor: pointer;"><i class='fa fa-trash po-list-icon'></i></button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>



                                                        <!-------------branch-user-modal-start------------>


                                                        <div class="modal fade right branch-user-modal customer-modal" id="fluidModalRightSuccessDemo_BranchUser" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <div class="display-flex-space-between mt-4 mb-3">
                                                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                <!-- <li class="nav-item">
                                                                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= str_replace('/', '-', $listRow2["fldAdminKey"]) ?>">Info</a>
                                                                                </li>
                                                                               -->
                                                                                <!-- -------------------Audit History Button Start------------------------- -->
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $listRow2["fldAdminKey"]) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $listRow2["fldAdminKey"]) ?>" href="#history<?= str_replace('/', '-', $listRow2["fldAdminKey"]) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $listRow2["fldAdminKey"]) ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                                                                </li>
                                                                                <!-- -------------------Audit History Button End------------------------- -->
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="tab-content" id="myTabContent">
                                                                            <!-- <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $listRow2["fldAdminKey"]) ?>" role="tabpanel" aria-labelledby="home-tab"></div> -->
                                                                            <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                            <div class="tab-pane fade show active" id="history<?= str_replace('/', '-', $listRow2["fldAdminKey"]) ?>" role="tabpanel" aria-labelledby="history-tab">

                                                                                <div class="audit-head-section mb-3 mt-3 ">
                                                                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($listRow2['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($listRow2['created_at']) ?></p>
                                                                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($listRow2['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($listRow2['updated_at']) ?></p>
                                                                                </div>
                                                                                <hr>
                                                                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $listRow2["fldAdminKey"]) ?>">

                                                                                    <ol class="timeline">

                                                                                        <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                            <div class="new-comment font-bold">
                                                                                                <p>Loading...
                                                                                                <ul class="ml-3 pl-0">
                                                                                                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                </ul>
                                                                                                </p>
                                                                                            </div>
                                                                                        </li>
                                                                                        <p class="mt-0 mb-5 ml-5">Loading...</p>

                                                                                        <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                            <div class="new-comment font-bold">
                                                                                                <p>Loading...
                                                                                                <ul class="ml-3 pl-0">
                                                                                                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                </ul>
                                                                                                </p>
                                                                                            </div>
                                                                                        </li>
                                                                                        <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                                                    </ol>
                                                                                </div>
                                                                            </div>
                                                                            <!-- -------------------Audit History Tab Body End------------------------- -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!-------------branch-user-modal-finish------------>


                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        <?php
                                        } else {
                                            echo "<p class='ml-3'>" . $listResult2["message"] . "</p>";
                                        }
                                        ?>
                                    </div>
                                    <!-- Locations User -->
                                    <div class="tab-pane fade" id="listLocationPan" role="tabpanel" aria-labelledby="listLocation">
                                        <?php
                                        $listResult3 = getLocationAllAdministratorUsers();

                                        if ($listResult3["status"] == "success") {
                                        ?>
                                            <table class="table  table-hover text-nowrap">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Company Code</th>
                                                        <th>Branch Code</th>
                                                        <th>Branch Name</th>
                                                        <th>Location Code</th>
                                                        <th>Location Name</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Status</th>
                                                        <th>Map Licence</th>
                                                        <th>Licence Expire date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sl = 0;
                                                    // console($listResult3);
                                                    foreach ($listResult3["data"] as $listRow3) {
                                                        // console($listRow3);
                                                        $listRow3Key = $listRow3["fldAdminKey"];
                                                        $listRow3Status = $listRow3["fldAdminStatus"];
                                                        $statusClass = ($listRow3Status == "active") ? "text-success" : "text-warning";
                                                        $rand = rand(100, 1000);

                                                    ?>
                                                        <tr>
                                                            <td><?= $sl += 1; ?></td>
                                                            <td><?= $listRow3["company_code"] ?></td>
                                                            <td><?= $listRow3["branch_code"] ?></td>
                                                            <td><?= $listRow3["state"] ?></td>
                                                            <td><?= $listRow3["othersLocation_code"] ?></td>
                                                            <td><?= $listRow3["othersLocation_location"] ?></td>
                                                            <td><?= $listRow3["fldAdminName"] ?></td>
                                                            <td><?= $listRow3["fldAdminEmail"]; ?></td>
                                                            <td><b><?= $listRow3["fldRoleName"] ?></b></td>
                                                            <td>
                                                                <form action="" method="POST">
                                                                    <input type="hidden" name="id" value="<?= $listRow3Key ?>">
                                                                    <input type="hidden" name="changeStatus" value="active_inactive">
                                                                    <button type="submit" onclick="return confirm('Are you sure change status?')" class="btn btn-sm" style="cursor: pointer; border:none" data-toggle="tooltip" data-placement="top" title="<?= $listRow3Status ?>">
                                                                        <?php echo ($listRow3Status == "active") ? '<span class="status">active</span>' : '<span class="status-warning"></span>'; ?>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                            <td><?php

                                                                if ($listRow3['licence_id'] != 0) {
                                                                    $licence_id = $listRow3['licence_id'];
                                                                    $sql = queryGet("SELECT * FROM `erp_company_licence` WHERE `licence_id`=$licence_id");
                                                                    echo $sql['data']['licence_code'];
                                                                } else {
                                                                ?>
                                                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addToLocation_<?= $listRow3Key ?>">Map</button>
                                                                <?php
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><?php

                                                                if ($listRow3['licence_id'] != 0) {
                                                                    $licence_id = $listRow3['licence_id'];
                                                                    $sql = queryGet("SELECT * FROM `erp_company_licence` WHERE `licence_id`=$licence_id");
                                                                    echo formatDateORDateTime($sql['data']['enddate']);
                                                                } else {
                                                                    echo "-";
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <div class="action-btn p-0 m-0">
                                                                    <a style="cursor: pointer;" class="btn btn-sm" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_LocationUser"><i class="fa fa-eye po-list-icon"></i></a>
                                                                    <a style="cursor: pointer;" class="btn btn-sm" href="adminstrator-user-edit.php?locUserId=<?= base64_encode($listRow3Key) ?>"><i class="fa fa-edit po-list-icon"></i></a>

                                                                    <?php if ($listRow3['fldAdminRole'] != 2) { ?>

                                                                        <form action="" method="POST">
                                                                            <input type="hidden" name="id" value="<?= $listRow3Key ?>">
                                                                            <input type="hidden" name="changeStatus" value="delete">
                                                                            <button type="submit" onclick="return confirm('Are you sure to delete?')" class="btn btn-sm" style="cursor: pointer; border:none"><i class='fa fa-trash po-list-icon'></i></button>
                                                                        </form>
                                                                    <?php } ?>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                        <!-------------location-user-modal-start------------>


                                                        <div class="modal fade right location-user-modal customer-modal" id="fluidModalRightSuccessDemo_LocationUser" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <div class="display-flex-space-between mt-4 mb-3">
                                                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                <!-- <li class="nav-item">
                                                                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= str_replace('/', '-', $listRow3["fldAdminKey"]) ?>">Info</a>
                                                                                </li>
                                                                               -->
                                                                                <!-- -------------------Audit History Button Start------------------------- -->
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $listRow3["fldAdminKey"]) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $listRow3["fldAdminKey"]) ?>" href="#history<?= str_replace('/', '-', $listRow3["fldAdminKey"]) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $listRow3["fldAdminKey"]) ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                                                                </li>
                                                                                <!-- -------------------Audit History Button End------------------------- -->
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="tab-content" id="myTabContent">
                                                                            <!-- <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $listRow3["fldAdminKey"]) ?>" role="tabpanel" aria-labelledby="home-tab"></div> -->
                                                                            <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                            <div class="tab-pane fade show active" id="history<?= str_replace('/', '-', $listRow3["fldAdminKey"]) ?>" role="tabpanel" aria-labelledby="history-tab">

                                                                                <div class="audit-head-section mb-3 mt-3 ">
                                                                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($listRow3['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($listRow3['created_at']) ?></p>
                                                                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($listRow3['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($listRow3['updated_at']) ?></p>
                                                                                </div>
                                                                                <hr>
                                                                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $listRow3["fldAdminKey"]) ?>">

                                                                                    <ol class="timeline">

                                                                                        <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                            <div class="new-comment font-bold">
                                                                                                <p>Loading...
                                                                                                <ul class="ml-3 pl-0">
                                                                                                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                </ul>
                                                                                                </p>
                                                                                            </div>
                                                                                        </li>
                                                                                        <p class="mt-0 mb-5 ml-5">Loading...</p>

                                                                                        <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                            <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                            <div class="new-comment font-bold">
                                                                                                <p>Loading...
                                                                                                <ul class="ml-3 pl-0">
                                                                                                    <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                                </ul>
                                                                                                </p>
                                                                                            </div>
                                                                                        </li>
                                                                                        <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                                                    </ol>
                                                                                </div>
                                                                            </div>
                                                                            <!-- -------------------Audit History Tab Body End------------------------- -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!-------------location-user-modal-finish------------>

                                                        <!-----add form modal start --->
                                                        <div class="modal fade hsn-dropdown-modal" id="addToLocation_<?= $listRow3Key ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                                                            <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                                                <div class="modal-content card">
                                                                    <div class="modal-header card-header p-3">
                                                                        <h4>Map <?php
                                                                                echo $listRow3['user_type'];

                                                                                ?>
                                                                            Licence</h4>
                                                                    </div>
                                                                    <div class="modal-body card-body p-3">
                                                                        <form id="addLicenceForm" class="addLicenceForm addLicenceForm_<?= $rand ?>">
                                                                            <!-- <input type="hidden" name="createLocationItem" id="createLocationItem" value=""> -->
                                                                            <input type="hidden" name="user_id" class="select2 form-control user_id_<?= $rand ?> " value="<?= $listRow3Key  ?>">
                                                                            <div class="form-input">
                                                                                <label class="label" for="">Select Licence</label>
                                                                                <select id="licence" name="licence" class="select2 form-control licence_<?= $rand ?>">
                                                                                    <option>Select Licence</option>
                                                                                    <?php
                                                                                    if ($listRow3['user_type'] == "Creator") {
                                                                                        $sql = queryGet("SELECT * FROM `erp_company_licence` WHERE `company_id`=$company_id AND `licence_type`= 'Creator' AND `user_id`=0 ", true);
                                                                                        $sql_data = $sql['data'];
                                                                                        //console($sql_data);
                                                                                        foreach ($sql_data as $data) {
                                                                                    ?>
                                                                                            <option value="<?= $data['licence_id'] ?>"><?= $data['licence_code'] . "(" . $data['licence_title'] . ")" ?></option>
                                                                                        <?php
                                                                                        }
                                                                                    } elseif ($listRow3['user_type'] == "Approver") {
                                                                                        $sql = queryGet("SELECT * FROM `erp_company_licence` WHERE `company_id`=$company_id AND `licence_type`= 'Approver'AND `licence_type`= 'Approver' AND `user_id`=0 ", true);
                                                                                        $sql_data = $sql['data'];
                                                                                        //console($sql_data);
                                                                                        foreach ($sql_data as $data) {
                                                                                        ?>
                                                                                            <option value="<?= $data['licence_id'] ?>"><?= $data['licence_code'] . "(" . $data['licence_title'] . ")"  ?></option>
                                                                                    <?php
                                                                                        }
                                                                                    }

                                                                                    ?>

                                                                                </select>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button class="btn btn-primary save-close-btn float-right addLicence" value="<?= $rand ?>">Submit</button>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                    </div>


                                    <!---end modal --->
                                <?php
                                                    }
                                ?>
                                </tbody>
                                </table>
                            <?php
                                        } else {
                                            echo "<p class='ml-3'>" . $listResult3["message"] . "</p>";
                                        }
                            ?>
                                </div>
                                <!-- Add TAB -->
                                <div class="tab-pane fade" id="addNewTabPan" role="tabpanel" aria-labelledby="addNewTab">
                                    <form action="" method="POST" enctype="multipart/form-data">
                                        <div class="row m-0 p-0">
                                            <div class="col-md-3">
                                                <span class="text-muted">Select User Type:</span>
                                                <div class="form-group">
                                                    <select name="fldRoleAccesses" id="fldRoleAccesses" class="form-control required">
                                                        <option value="">Select Type</option>
                                                        <option value="Creator">Creator</option>
                                                        <option value="Approver">Approver</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3 role_for_n">
                                                <span class="text-muted">Select Location:</span>
                                                <div class="form-group ">
                                                    <select name="fldAdminBranchLocationId" id="fldAdminBranchLocationId" class="form-control" required>
                                                        <option value="">---- Select One ----</option>
                                                        <?php
                                                        $sql = "SELECT * FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `company_id`='" . $company_id . "' AND `othersLocation_status`='active'";
                                                        $listResult = queryGet($sql, true);
                                                        if ($listResult["status"] == "success") {
                                                            foreach ($listResult["data"] as $listRow) { ?>
                                                                <option value="<?= $listRow["othersLocation_id"] . '|' . $listRow["branch_id"] . '|' . $listRow["othersLocation_location"]; ?>"><?= $listRow["othersLocation_code"]; ?> | <?= $listRow["othersLocation_location"]; ?> </option>
                                                        <?php   }
                                                        }
                                                        ?>
                                                    </select>
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
                                            <div class="col-md-3">
                                                <span class="text-muted">User Name</span>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="userName" placeholder="Enter UserName" required>
                                                    <!-- <input type="text" class="form-control is-invalid" id="inputSuccess" placeholder="Enter ..."> -->
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="text-muted">Password</span>
                                                <div class="form-group">
                                                    <input type="password" class="form-control" name="adminPassword" placeholder="Enter password ****" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-muted">Profile Photo <small>(Optional)</small></span>
                                                <div class="form-group">
                                                    <input type="file" class="form-control" name="adminAvatar" placeholder="Profile photo" accept=".jpg, .jpeg, .png">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="card creator-priviladge-card">
                                                    <div class="card-header p-3">
                                                        <div class="head">
                                                            <h4 class="mb-0 text-white">Previladge</h4>
                                                        </div>
                                                    </div>
                                                    <div class="card-body menu_access">


                                                    </div>
                                                </div>


                                            </div>

                                            <div class="col-12">
                                                <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-warning text-light">Cancel</a>
                                                <button type="submit" name="addNewAdministratorFormBtn" class="btn btn-dark">Add User</button>
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

<script>
    $(document).ready(function() {


        $(document).on("change", '.menuCBX', function() {
            let valllAc = $(this).val();
            if (this.checked) {
                $("." + valllAc).each(function() {
                    if (!$(this).prop('disabled')) {
                        this.checked = true;
                    }
                });
            } else {
                $("." + valllAc).each(function() {
                    this.checked = false;
                });
            }
        });

        $(document).on("change", '.menuGrandParentSub', function() {
            let valllAc = $(this).val();
            if (this.checked) {
                $(".menuGrandParentSubAccess" + valllAc).each(function() {
                    if (!$(this).prop('disabled')) {
                        this.checked = true;
                    }
                });
            } else {
                $(".menuGrandParentSubAccess" + valllAc).each(function() {
                    this.checked = false;
                });
            }
        });

        $(document).on("change", '.menuGrandParent', function() {
            let valllAc = $(this).val();
            if (this.checked) {
                $(".menuGrandParentSub" + valllAc).each(function() {
                    if (!$(this).prop('disabled')) {
                        this.checked = true;
                    }
                });
            } else {
                $(".menuGrandParentSub" + valllAc).each(function() {
                    this.checked = false;
                });
            }
            if (this.checked) {
                $(".SubAccess" + valllAc).each(function() {
                    if (!$(this).prop('disabled')) {
                        this.checked = true;
                    }
                });
            } else {
                $(".SubAccess" + valllAc).each(function() {
                    this.checked = false;
                });
            }
        });

        $(document).on("change", '.menuGrand', function() {
            let grand = $(this).val();
            if (this.checked) {
                $(".menuGrandParent" + grand).each(function() {
                    if (!$(this).prop('disabled')) {
                        this.checked = true;
                    }
                });
            } else {
                $(".menuGrandParent" + grand).each(function() {
                    this.checked = false;
                });
            }
            if (this.checked) {
                $(".GrandParentSubAccess" + grand).each(function() {
                    if (!$(this).prop('disabled')) {
                        this.checked = true;
                    }
                });
            } else {
                $(".GrandParentSubAccess" + grand).each(function() {
                    this.checked = false;
                });
            }
            if (this.checked) {
                $(".GrandParentSub" + grand).each(function() {
                    if (!$(this).prop('disabled')) {
                        this.checked = true;
                    }
                });
            } else {
                $(".GrandParentSub" + grand).each(function() {
                    this.checked = false;
                });
            }

        });



        $(document).on("change", '#fldRoleAccesses', function() {
            let menuType = $(this).val();
            let menufor = 'Location';

            $.ajax({
                url: 'ajaxs/ajax_get_menu_access.php',
                data: {
                    menuType,
                    menufor
                },
                type: 'POST',
                beforeSend: function() {
                    $('.lddrttts').show();
                    $(".menu_access").html('');
                    $('.lddrttts').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                },
                success: function(responseData) {
                    $('.lddrttts').hide();
                    $('.lddrttts').html('');
                    $(".menu_access").html(responseData);
                }
            });
        });

    });

    $(document).ready(function() {

        /*@ Registration start */
        $('.addLicence').click(function(event) {
            var attr = $(this).val();
            //alert(attr);
            // alert(1);
            // $(document).on('submit', '#addLicence', function(event) {
            //     alert(1); 

            event.preventDefault();

            let formData = $(".addLicenceForm_" + (attr)).serialize();
            //    var user_id = $(".user_id_"+attr).val();
            //    alert(user_id)

            console.log(formData);
            $.ajax({

                type: "POST",

                url: `ajaxs/ajax-licence.php`,

                data: formData,

                beforeSend: function() {

                    $(".addLicence").toggleClass("disabled");

                    $(".addLicence").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

                },

                success: function(response) {

                    console.log(response);

                    $('.addLicenceForm').trigger("reset");

                    $(".addNewPurchaseGroupFormModal").modal('toggle');

                    $(".addLicence").html("Submit");

                    $(".addLicence").toggleClass("disabled");
                    location.reload();

                }

            });

        });
    });
</script>
<style>
    .dataTable thead {
        top: 0px !important;
    }
</style>