<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/branch/func-cost-center.php");

$company_id = $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"];
if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusCostCenter($_POST, "CostCenter_id", "CostCenter_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["createdata"])) {
    $addNewObj = createDataCostCenter($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);

    if ($addNewObj["status"] == "success") {
        swalToast($addNewObj["status"], $addNewObj["message"]);
    } else {
        swalToast($addNewObj["status"], $addNewObj["message"]);
    }
}

if (isset($_POST["editdata"])) {
    $editDataObj = updateDataCostCenter($_POST);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}
$sqqql = "SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE `company_id`='" . $company_id . "' AND `CostCenter_status`!='deleted' ORDER BY CostCenter_id DESC LIMIT 1";
$CostCenter_code = queryGet($sqqql);
if (isset($CostCenter_code['data'])) {
    $CostCenter_Lastcode = $CostCenter_code['data']['CostCenter_code'];
} else {
    $CostCenter_Lastcode = '';
}
?>

<link rel="stylesheet" href="../public/assets/listing.css">

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
</style>

<?php
if (isset($_GET['create'])) {
?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header mb-2 p-0  border-bottom">
            <?php if (isset($msg)) { ?>
                <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
                    <?= $msg ?>
                </div>
            <?php } ?>
            <div class="container-fluid">
                <div class="row pt-2 pb-2">
                    <div class="col-md-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage CostCenter</a></li>
                            <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Add CostCenter</a></li>
                        </ol>
                    </div>
                    <div class="col-md-6" style="display: flex;">
                        <button class="btn btn-danger btnstyle ml-2 add_data" value="add_draft">Save As Draft</button>
                        <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_post"><i class="fa fa-plus fontSize"></i> Final Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                ffhdfdfhhfk
            </div>
        </section>
        <!-- /.content -->
    </div>
<?php
} else if (isset($_GET['edit']) && $_GET["edit"] > 0) {
    $sqqql = "SELECT * FROM `" . ERP_COST_CENTER . "` WHERE `CostCenter_id`='" . $_GET["edit"] . "' AND `CostCenter_status`!='deleted'";
    $CostCenter_code = queryGet($sqqql);
    if (isset($CostCenter_code['data'])) {
        $CostCenter_code = $CostCenter_code['data'];
    } else {
        redirect(basename($_SERVER['PHP_SELF']));
        exit;
    }
?>

    <!-- Content Wrapper. Contains page content -->

<?php
} else if (isset($_GET['view']) && $_GET["view"] > 0) {
?>

    <!-- Content Wrapper. Contains page content -->

<?php
} else {
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper user-priviladge">
        <div class="container-fluid">

            <div class="card creator-priviladge-card">
                <div class="card-header p-3">
                    <div class="head">
                        <h4 class="mb-0 text-white">Creator Previladge</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col"></div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Read</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Write</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Update</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Delete</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Approve</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-1</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-2</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-3</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-4</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-5</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-6</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-7</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-8</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-9</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-10</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-11</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                </div>
            </div>


            <div class="card approval-priviladge-card">
                <div class="card-header p-3">
                    <div class="head">
                        <h4 class="mb-0 text-white">Approver Previladge</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col"></div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Read</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Write</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Update</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Delete</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Approve</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-1</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-2</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-3</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-4</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-5</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-6</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-7</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-8</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-9</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-10</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <p class="text-xs font-bold">Function-11</p>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col">
                            <input type="checkbox">
                        </div>
                    </div>
                </div>
            </div>




        </div><!-- row -->

    </div>
    </div>
    <!-- /.row -->
    </div>
    </section>
    <!-- /.content -->
    </div>
    <!-- /.Content Wrapper. Contains page content -->
    <!-- For Pegination------->
    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>
    <!-- End Pegination from------->

<?php
}
include("common/footer.php");
?>
<script>
    $('.m-input').on('keyup', function() {
        $(this).parent().children('.error').hide()
    });
    /*
      $(".update_data").click(function() {
        var data = this.value;
        $("#createdata").val(data);
        let flag = 1;
        var Ragex = "/[0-9]{4}/";
        if ($("#CostCenter_code").val() == "") {
          $(".CostCenter_code").show();
          $(".CostCenter_code").html("Credit Period is requried.");
          flag++;
        } else {
          $(".CostCenter_code").hide();
          $(".CostCenter_code").html("");
        }
        if ($("#CostCenter_desc").val() == "") {
          $(".CostCenter_desc").show();
          $(".CostCenter_desc").html("Description is requried.");
          flag++;
        } else {
          $(".CostCenter_desc").hide();
          $(".CostCenter_desc").html("");
        }

        if (flag != 1) {
          return false;
        } else {
          $("#add_frm").submit();
        }


      });
      $(".edit_data").click(function() {
        var data = this.value;
        $("#editdata").val(data);
        let flag = 1;
        var Ragex = "/[0-9]{4}/";
        if ($("#CostCenter_code").val() == "") {
          $(".CostCenter_code").show();
          $(".CostCenter_code").html("Credit Period is requried.");
          flag++;
        } else {
          $(".CostCenter_code").hide();
          $(".CostCenter_code").html("");
        }
        if ($("#CostCenter_desc").val() == "") {
          $(".CostCenter_desc").show();
          $(".CostCenter_desc").html("Description is requried.");
          flag++;
        } else {
          $(".CostCenter_desc").hide();
          $(".CostCenter_desc").html("");
        }

        if (flag != 1) {
          return false;
        } else {
          $("#edit_frm").submit();
        }

      });
    */

    function srch_frm() {
        if ($('#form_date_s').val().trim() != '' && $('#to_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
            //$("#phone_r_err").html("Your Phone Number");
            alert("Enter To Date");
            $('#to_date_s').focus();
            return false;
        }
        if ($('#to_date_s').val().trim() != '' && $('#form_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
            //$("#phone_r_err").html("Your Phone Number");
            alert("Enter From Date");
            $('#form_date_s').focus();
            return false;
        }

    }

    function table_settings() {
        var favorite = [];
        $.each($("input[name='settingsCheckbox[]']:checked"), function() {
            favorite.push($(this).val());
        });
        var check = favorite.length;
        if (check < 5) {
            alert("Please Check Atlast 5");
            return false;
        }

    }


    $(document).on("click", "#btnSearchCollpase", function() {
        sec = document.getElementById("btnSearchCollpase").parentElement;
        coll = sec.getElementsByClassName("collapsible-content")[0];

        if (sec.style.width != '100%') {
            sec.style.width = '100%';
        } else {
            sec.style.width = 'auto';
        }

        if (coll.style.height != 'auto') {
            coll.style.height = 'auto';
        } else {
            coll.style.height = '0px';
        }

        $(this).children().toggleClass("fa-search fa-times");

    });



    $(document).ready(function() {


        $(document).on("keyup paste keydown", "#branch_gstin", function() {
            var branch_gstin = $("#branch_gstin").val();
            var leng_gstin = branch_gstin.length;
            if (leng_gstin > 14) {
                $("#vendorPanNo").val(branch_gstin.substr(2, 10));

                $.ajax({
                    type: "GET",
                    url: `ajaxs/ajax-gst-details.php?gstin=${branch_gstin}`,
                    beforeSend: function() {
                        $('#gstinloder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {

                        $('#gstinloder').html("");
                        responseObj = JSON.parse(response);
                        if (responseObj["status"] == "success") {
                            responseData = responseObj["data"];

                            console.log(responseData);

                            $("#branch_name").val(responseData["tradeNam"]);
                            $("#con_business").val(responseData["ctb"]);
                            $("#build_no").val(responseData['pradr']['addr']['bno']);
                            $("#flat_no").val(responseData['pradr']['addr']['flno']);
                            $("#street_name").val(responseData['pradr']['addr']['st']);
                            $("#pincode").val(responseData['pradr']['addr']['pncd']);
                            $("#location").val(responseData['pradr']['addr']['loc']);
                            $("#city").val(responseData['pradr']['addr']['city']);
                            $("#district").val(responseData['pradr']['addr']['dst']);
                            $("#state").val(responseData['pradr']['addr']['stcd']);

                            //$("#status").val(responseData["sts"]);

                        } else {
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            Toast.fire({
                                icon: `warning`,
                                title: `&nbsp;Invalid GSTIN No!`
                            });
                        }
                    }
                });
            }

        });


        $('.select2')
            .select2()
            .on('select2:open', () => {
                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal3">
    Add New
  </a></div>`);
            });
        //**************************************************************
        $('.select4')
            .select4()
            .on('select4:open', () => {
                $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
    Add New
  </a></div>`);
            });
    });
</script>
<script>
    function leaveInput(el) {
        if (el.value.length > 0) {
            if (!el.classList.contains('active')) {
                el.classList.add('active');
            }
        } else {
            if (el.classList.contains('active')) {
                el.classList.remove('active');
            }
        }
    }

    var inputs = document.getElementsByClassName("m-input");
    for (var i = 0; i < inputs.length; i++) {
        var el = inputs[i];
        el.addEventListener("blur", function() {
            leaveInput(this);
        });
    }

    // *** autocomplite select *** //
    wow = new WOW({
        boxClass: 'wow', // default
        animateClass: 'animated', // default
        offset: 0, // default
        mobile: true, // default
        live: true // default
    })
    wow.init();
</script>