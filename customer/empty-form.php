<?php
include("../app/v1/connection-user.php");
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
//userAuth();
// if (isset($_POST["changeStatus"]) && !empty($_POST["id"]) && $_POST["id"] > 0) {
//     $changeStatusResult = sendChangeStatus($_POST, "team_details", "teamKey", "teamStatus");
//     swalToast($changeStatusResult["status"], $changeStatusResult["message"]);
// }

// if (isset($_POST["addTeamFormBtn"])) {
//     $addTeamRequest=addNewTeam($_POST,$_FILES);
//     if($addTeamRequest["status"]=="success"){
//         swalAlert($addTeamRequest["status"], "", $addTeamRequest["message"], "manage-player.php");
//     }else{
//         swalAlert($addTeamRequest["status"], "", $addTeamRequest["message"]);
//     }
// }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= CUSTOMER_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Team</a></li>
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
                    <div class="card card-dark card-tabs">
                        <div class="card-header p-0 pt-1">
                            <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3">
                                    <h3 class="card-title">Team Details</h3>
                                </li>
                                <li class="nav-item ml-auto">
                                    <a class="nav-link active" id="listTab" data-toggle="pill" href="#listTabPan" role="tab" aria-controls="listTabPan" aria-selected="true">Team List</a>
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
                                    $listResult=[];
                                    $listResult["status"] = "success";
                                    $listResult["message"] = "success";
                                    $listResult["data"]=[];
                                    if ($listResult["status"] == "success") {
                                    ?>
                                        <table class="table defaultDataTable table-hover text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Team Logo</th>
                                                    <th>Team Name</th>
                                                    <th>Short Name</th>
                                                    <th>Address</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sl = 0;
                                                foreach ($listResult["data"] as $listRow) {
                                                    $statusClass = ($listRow["teamStatus"] == "active") ? "text-success" : "text-warning";
                                                ?>
                                                    <tr>
                                                        <td><?= $sl += 1; ?></td>
                                                        <td>
                                                            <img src="../public/storage/teams/<?= $listRow["teamLogo"] ?>" style="height: 50px; width:80px;">
                                                        </td>
                                                        <td><?= $listRow["teamName"] ?></td>
                                                        <td><?= $listRow["teamShortName"]; ?></td>
                                                        <td><?= $listRow["teamAddress"] ?></td>
                                                        <td>
                                                            <form action="" method="POST">
                                                                <input type="hidden" name="id" value="<?= $listRow["teamKey"] ?>">
                                                                <input type="hidden" name="changeStatus" value="active_inactive">
                                                                <button type="submit" onclick="return confirm('Are you sure change status?')" class="p-0 m-0 ml-2" style="cursor: pointer; border:none" data-toggle="tooltip" data-placement="top" title="<?= $listRow["teamStatus"] ?>">
                                                                    <?php echo ($listRow["teamStatus"] == "active") ? '<i class="fa fa-toggle-on text-success" ></i>' : '<i class="fa fa-toggle-off text-warning"></i>'; ?>
                                                                </button>
                                                            </form>
                                                        </td>
                                                        <td>
                                                            <div class="row p-0 m-0">
                                                                <a href="<?= basename($_SERVER['PHP_SELF']) . "?view=" . $listRow["teamKey"]; ?>" style="cursor: pointer;" class="ml-2"><i class="fa fa-eye text-primary"></i></a>
                                                                <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $listRow["teamKey"]; ?>" style="cursor: pointer;" class="ml-2"><i class="fa fa-edit text-success"></i></a>
                                                                <form action="" method="POST">
                                                                    <input type="hidden" name="id" value="<?= $listRow["teamKey"] ?>">
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
                                        <div class="col-12 bg-secondary">Team Info</div>
                                        <div class="row m-0 p-0">
                                            <div class="col-12 mt-1">
                                                <label class="text-muted m-0">Team Name*</label>
                                                <input type="text" name="teamName" required placeholder="Team Name" class="form-control">
                                            </div>
                                            <div class="col-12 mt-1">
                                                <label class="text-muted m-0">Team Short Name*</label>
                                                <input type="text" name="teamShortName" required placeholder="Team Short Name" class="form-control">
                                            </div>
                                            <div class="col-12 mt-1">
                                                <label class="text-muted m-0">Team Logo*</label>
                                                <input type="file" name="teamLogo" required class="form-control">
                                            </div>
                                            <div class="col-12 mt-1">
                                                <label class="text-muted m-0">Team Address</label>
                                                <textarea name="teamAddress" placeholder="Team Address" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <input type="submit" value="Add Team" name="addTeamFormBtn" class="form-control btn btn-primary">
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
include("common/footer.php");
?>

<script>
    $(document).ready(function() {

    });
</script>