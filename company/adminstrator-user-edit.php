<?php
require_once("../app/v1/connection-company-admin.php");
// administratorAuth();
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("../app/v1/functions/branch/func-others-location.php");

if (isset($_POST["changeStatus"])) {
    $newStatusObj = administratorFuncChangeStatus($_POST, "tbl_company_admin_details", "fldAdminKey", "fldAdminStatus");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["editNewAdministratorFormBtn"])) {
    $addNewObj = editNewLocationAdministratorUser($_POST + $_FILES);
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
$locUserId = base64_decode($_GET['locUserId']);
$dbObj = new Database();
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= COMPANY_URL ?>administrator-user.php" class="text-dark">Manage Administrators</a></li>
                <li class="breadcrumb-item active"><a href="" class="text-dark">Manage Administrators Edit</a></li>

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
                                <li class="nav-item mr-0">
                                    <a class="nav-link" id="addNewTab" data-toggle="pill" href="#addNewTabPan" role="tab" aria-controls="addNewTabPan" aria-selected="false">Edit Location User</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">

                            <?php

                            $adminSql = "SELECT * FROM `tbl_branch_admin_details` WHERE fldAdminKey=$locUserId;";
                            $resAdminDetail = $dbObj->queryGet($adminSql);
                            $data = $resAdminDetail['data'];
                            $roleSql = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE fldRoleKey='" . $data['fldAdminRole'] . "' ";
                            $resRoleSql = $dbObj->queryGet($roleSql);
                            $roleData = $resRoleSql['data'];



                            ?>

                            <!-- Add TAB -->
                            <div class="tab-pane " id="addNewTabPan" role="tabpanel" aria-labelledby="addNewTab">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="row m-0 p-0">
                                        <div class="col-md-3">
                                            <span class="text-muted">Select User Type:</span>
                                            <div class="form-group">
                                                <select name="fldRoleAccesses" id="fldRoleAccesses" class="form-control required" <?php if ($roleData['fldRoleKey'] == 2) {
                                                                                                                                        echo "disabled";
                                                                                                                                    } ?>>
                                                    <option value="">Select Type</option>
                                                    <option value="Creator" <?php if ($data['user_type'] == 'Creator') {
                                                                                echo "selected";
                                                                            } ?>>Creator</option>
                                                    <option value="Approver" <?php if ($data['user_type'] == 'Approver') {
                                                                                    echo "selected";
                                                                                } ?>>Approver</option>
                                                </select>


                                                <input type="hidden" name="fldRoleAccessesName" value="<?= $data['user_type'] ?>">

                                            </div>
                                        </div>
                                        <input type="hidden" name="locAdminId" value="<?= $locUserId ?>">
                                        <input type="hidden" name="roleAdminId" value="<?= $data['fldAdminRole'] ?>">

                                        <div class="col-md-3 role_for_n">
                                            <span class="text-muted">Select Location:</span>
                                            <div class="form-group ">
                                                <select name="fldAdminBranchLocationId" id="fldAdminBranchLocationId" class="form-control" <?php if ($roleData['fldRoleKey'] == 2) {
                                                                                                                                                echo "disabled";
                                                                                                                                            } ?> required>
                                                    <option value="">---- Select One ----</option>
                                                    <?php
                                                    $sql = "SELECT * FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `company_id`='" . $company_id . "' AND `othersLocation_status`='active'";
                                                    $listResult = queryGet($sql, true);
                                                    $setValue = '';
                                                    if ($listResult["status"] == "success") {
                                                        // Concatenate values with '|' separator

                                                        foreach ($listResult["data"] as $listRow) {
                                                            $opValue = $listRow["othersLocation_id"] . '|' . $listRow["branch_id"] . '|' . $listRow["othersLocation_location"];

                                                    ?>
                                                            <option value="<?= $opValue ?>" <?php
                                                                                            if ($data['fldAdminBranchLocationId'] == $listRow["othersLocation_id"]) {
                                                                                                $setValue = $opValue;
                                                                                                echo "selected";
                                                                                            } ?>><?= $listRow["othersLocation_code"]; ?> | <?= $listRow["othersLocation_location"]; ?> </option>
                                                    <?php   }
                                                    }
                                                    ?>
                                                </select>
                                                <input type="hidden" name="fldAdminBranchLocationIdName" value="<?= $setValue ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <span class="text-muted">Full Name:</span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="adminName" placeholder="Enter Full Name" value="<?= $data['fldAdminName'] ?>" required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <span class="text-muted">Email</span>
                                            <div class="form-group">
                                                <input type="email" class="form-control" name="adminEmail" placeholder="Enter email" value="<?= $data['fldAdminEmail'] ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="text-muted">Phone</span>
                                            <div class="form-group">
                                                <input type="number" class="form-control" name="adminPhone" placeholder="Enter phone no" value="<?= $data['fldAdminPhone'] ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="text-muted">User Name</span>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="userName" placeholder="Enter UserName" value="<?= $data['fldAdminUserName'] ?>" required>
                                                <!-- <input type="text" class="form-control is-invalid" id="inputSuccess" placeholder="Enter ..."> -->
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="text-muted">Password</span>
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="adminPassword" placeholder="Enter password ****" value="<?= $data['fldAdminPassword'] ?>" required>
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
                                                    <div class="lddrttts"></div>
                                                </div>

                                                <?php
                                                if ($roleData['fldRoleKey'] != 2) {
                                                ?>
                                                    <input type="hidden" name="supAdmin" value="0">

                                                <?php
                                                } else {
                                                ?>
                                                    <input type="hidden" name="supAdmin" value="1">
                                                    <span class="text-muted">This is location super user all permission granted</span>
                                                <?php
                                                }
                                                ?>

                                            </div>


                                        </div>

                                        <div class="col-12">
                                            <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="btn btn-warning text-light">Cancel</a>
                                            <button type="submit" name="editNewAdministratorFormBtn" class="btn btn-dark">Update User</button>
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

require_once("common/footer.php");
?>

<script>
    $(document).ready(function() {
        $("#addNewTabPan").show();

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


        function loadRoles() {
            let menuType = $('#fldRoleAccesses').val();
            let menufor = 'Location';
            let roleId = <?= $roleData['fldRoleKey'] ?>;
            $.ajax({
                url: 'ajaxs/ajax-get-menu-access-update.php',
                data: {
                    fldRoleKey: roleId,
                    menuType,
                    menufor
                },
                type: 'POST',
                beforeSend: function() {
                    $('.lddrttts').show();
                    $(".menu_access").html('<div class="lddrttts"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span></div>');
                },
                success: function(responseData) {
                    console.log(responseData);
                    $('.lddrttts').hide();
                    $('.lddrttts').html('');
                    $(".menu_access").html(responseData);
                }
            });

        }

        <?php if ($roleData['fldRoleKey'] != 2) { ?>
            loadRoles();
        <?php } ?>

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