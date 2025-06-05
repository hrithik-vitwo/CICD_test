<?php
include("../app/v1/connection-company-admin.php");
//administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/company/func-branches.php");


if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"],);
}
if (isset($_POST["visit"])) {
    $newStatusObj = VisitBranches($_POST);
    if ($newStatusObj["status"] == "success") {
        redirect(BRANCH_URL);
        swalToast($newStatusObj["status"], $newStatusObj["message"]);
    } else {
        swalToast($newStatusObj["status"], $newStatusObj["message"]);
    }
}


if (isset($_POST["createdata"])) {
    // console($_POST);
    $addNewObj = createDataBranches($_POST);
    if ($addNewObj["status"] == "success") {
        redirect($_SERVER['PHP_SELF']);
        swalToast($addNewObj["status"], $addNewObj["message"]);
    } else {
        swalToast($addNewObj["status"], $addNewObj["message"]);
    }
}

if (isset($_POST["editdata"])) {
    //console($_POST);
    $editNewObj = updateDataBranches($_POST);
    if ($editNewObj["status"] == "success") {
        redirect($_SERVER['PHP_SELF']);
        swalToast($editNewObj["status"], $editNewObj["message"]);
    } else {
        swalToast($editNewObj["status"], $editNewObj["message"]);
    }
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>
<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">




<?php
if (isset($_GET['create'])) {
?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <?php if (isset($msg)) { ?>
                <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
                    <?= $msg ?>
                </div>
            <?php } ?>
            <div class="container-fluid">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

                    <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Branches</a></li>

                    <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add Branch</a></li>

                    <li class="back-button">

                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

                            <i class="fa fa-reply po-list-icon"></i>

                        </a>

                    </li>

                </ol>

                <!-- <div class="row pt-2 pb-2">
          <div class="col-md-6">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Branches</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Add Branches</a></li>
            </ol>
          </div>
          <div class="col-md-6" style="display: flex;">
            <button class="btn btn-danger btnstyle ml-2 add_data" value="add_draft">Save As Draft</button>
            <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_post"><i class="fa fa-plus fontSize"></i> Final Submit</button>
          </div>
        </div> -->
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                    <input type="hidden" name="createdata" id="createdata" value="">
                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">



                    <div class="row">

                        <div class="col-lg-8 col-md-8 col-sm-8">

                            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                <div class="card-header">

                                    <h4>Branch Basic Details</h4>

                                </div>

                                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                        <label for="">GSTIN</label>

                                                        <input type="text" class="form-control" id="branch_gstin" name="branch_gstin">

                                                        <span class="error branch_gstin"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                        <label for="">Trade Name</label>

                                                        <input type="text" name="branch_name" class="form-control" id="branch_name" value="">

                                                        <span class="error branch_name"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                        <label for="">Constitution of Business</label>

                                                        <input type="text" name="con_business" class="form-control" id="con_business" value="">

                                                        <span class="error con_business"></span>

                                                    </div>

                                                </div>




                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">Building Number</label>

                                                        <input type="text" name="build_no" class="form-control" id="build_no" value="">

                                                        <span class="error build_no"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">Flat Number</label>

                                                        <input type="text" name="flat_no" class="form-control" id="flat_no" value="">

                                                        <span class="error flat_no"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">Sreet Name</label>

                                                        <input type="text" name="street_name" class="form-control" id="street_name" value="">

                                                        <span class="error street_name"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">PIN Code</label>

                                                        <input type="number" name="pincode" class="form-control" id="pincode" value="">

                                                        <span class="error pincode"></span>

                                                    </div>

                                                </div>




                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">Location</label>

                                                        <input type="text" name="location" class="form-control" id="location" value="">

                                                        <span class="error location"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">City</label>

                                                        <input type="text" name="city" class="form-control" id="city" value="">

                                                        <span class="error city"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">District</label>

                                                        <input type="text" name="district" class="form-control" id="district" value="">

                                                        <span class="error district"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">State</label>

                                                        <input type="text" name="state" class="form-control" id="state" value="">

                                                        <span class="error state"></span>

                                                    </div>

                                                </div>





                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4">

                            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                <div class="card-header">

                                    <h4>Branch Admin Details</h4>

                                </div>

                                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                    <div class="form-input">

                                                        <label for="">User Name</label>

                                                        <input type="email" name="adminName" class="form-control" id="adminEmail">

                                                        <span class="error adminName"></span>

                                                    </div>

                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">User Email</label>

                                                        <input type="email" name="adminEmail" class="form-control" id="adminEmail">

                                                        <span class="error adminEmail"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">User Phone</label>

                                                        <input type="text" name="adminPhone" class="form-control" id="adminName">

                                                        <span class="error adminPhone"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                    <div class="form-input">

                                                        <label for="">Password</label>

                                                        <input type="text" name="adminPassword" class="form-control" id="adminPassword" value="<?php echo rand(1111, 9999); ?>">

                                                        <span class="error adminPassword"></span>

                                                    </div>

                                                </div>




                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="btn-section mt-2 mb-2 ml-auto">

                            <button class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" value="add_post">Submit</button>

                            <!-- <button class="btn btn-danger save-close-btn float-right add_data waves-effect waves-light" value="add_draft">Save as Draft</button> -->

                        </div>


                    </div>


                </form>

                <!-- modal -->
                <div class="modal" id="myModal3">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Heading</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="col-md-12 mb-3">
                                    <div class="input-group">
                                        <select name="goodsGroup" class="form-control form-control-border borderColor">
                                            <option value="">Branches Group</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                                        <label>Item Code</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group btn-col">
                                        <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
                        </div>
                    </div>
                </div>
                <!-- modal end -->
                <!-- modal -->
                <div class="modal" id="myModal4">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Heading4</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="col-md-12 mb-3">
                                    <div class="input-group">
                                        <select name="goodsGroup" class="form-control form-control-border borderColor">
                                            <option value="">Branches Group</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                                        <label>Item Code</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group btn-col">
                                        <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
                        </div>
                    </div>
                </div>
                <!-- modal end -->
            </div>
        </section>
        <!-- /.content -->
    </div>
<?php
} else if (isset($_GET['edit']) && $_GET["edit"] > 0) {

    $id = $_GET['edit'];
    $branch_sql = queryGet("SELECT * FROM `" . ERP_BRANCHES . "` WHERE `branch_id`=$id");
    $branch_data = $branch_sql['data'];
    //console($branch_data);
    $admin_sql = queryGet("SELECT * FROM `tbl_branch_admin_details` WHERE `fldAdminBranchId`=$id AND `fldAdminRole`=1 ORDER BY `fldAdminKey` ASC");
    $admin_data = $admin_sql['data'];
    //console($admin_data);
?>



    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <?php if (isset($msg)) { ?>
                <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
                    <?= $msg ?>
                </div>
            <?php } ?>
            <div class="container-fluid">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

                    <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Branches</a></li>

                    <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Edit Branch</a></li>

                    <li class="back-button">

                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

                            <i class="fa fa-reply po-list-icon"></i>

                        </a>

                    </li>

                </ol>

            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                    <input type="hidden" name="editdata" id="editdata" value="">
                    <input type="hidden" name="branch_id" id="branch_id" value="<?= $id ?>">
                    <input type="hidden" name="admin_id" id="admin_id" value="<?= $admin_data['fldAdminKey'] ?>">
                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">



                    <div class="row">

                        <div class="col-lg-8 col-md-8 col-sm-8">

                            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                <div class="card-header">

                                    <h4>Branch Basic Details</h4>

                                </div>

                                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                        <label for="">GSTIN</label>

                                                        <input type="text" class="form-control" id="branch_gstin" name="branch_gstin" value="<?= $branch_data['branch_gstin'] ?>" readonly>

                                                        <span class="error branch_gstin"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                        <label for="">Trade Name</label>

                                                        <input type="text" name="branch_name" class="form-control" id="branch_name" value="<?= $branch_data['branch_name'] ?>" readonly>

                                                        <span class="error branch_name"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                        <label for="">Constitution of Business</label>

                                                        <input type="text" name="con_business" class="form-control" id="con_business" value="<?= $branch_data['con_business'] ?>">

                                                        <span class="error con_business"></span>

                                                    </div>

                                                </div>




                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">Building Number</label>

                                                        <input type="text" name="build_no" class="form-control" id="build_no" value="<?= $branch_data['build_no'] ?>">

                                                        <span class="error build_no"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">Flat Number</label>

                                                        <input type="text" name="flat_no" class="form-control" id="flat_no" value="<?= $branch_data['flat_no'] ?>">

                                                        <span class="error flat_no"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">Sreet Name</label>

                                                        <input type="text" name="street_name" class="form-control" id="street_name" value="<?= $branch_data['street_name'] ?>">

                                                        <span class="error street_name"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">PIN Code</label>

                                                        <input type="number" name="pincode" class="form-control" id="pincode" value="<?= $branch_data['pincode'] ?>" readonly>

                                                        <span class="error pincode"></span>

                                                    </div>

                                                </div>




                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">Location</label>

                                                        <input type="text" name="location" class="form-control" id="location" value="<?= $branch_data['location'] ?>">

                                                        <span class="error location"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">City</label>

                                                        <input type="text" name="city" class="form-control" id="city" value="<?= $branch_data['city'] ?>">

                                                        <span class="error city"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">District</label>

                                                        <input type="text" name="district" class="form-control" id="district" value="<?= $branch_data['district'] ?>">

                                                        <span class="error district"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-3">

                                                    <div class="form-input">

                                                        <label for="">State</label>

                                                        <input type="text" name="state" class="form-control" id="state" value="<?= $branch_data['state'] ?>">

                                                        <span class="error state"></span>

                                                    </div>

                                                </div>





                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4">

                            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                <div class="card-header">

                                    <h4>Branch Admin Details</h4>

                                </div>

                                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                    <div class="form-input">

                                                        <label for="">User Name</label>

                                                        <input type="text" name="adminName" class="form-control" id="adminEmail" value="<?= $admin_data['fldAdminName'] ?>">

                                                        <span class="error adminName"></span>

                                                    </div>

                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">User Email</label>

                                                        <input type="email" name="adminEmail" class="form-control" id="adminEmail" value="<?= $admin_data['fldAdminEmail'] ?>" readonly>

                                                        <span class="error adminEmail"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">User Phone</label>

                                                        <input type="text" name="adminPhone" class="form-control" id="adminName" value="<?= $admin_data['fldAdminPhone'] ?>">

                                                        <span class="error adminPhone"></span>

                                                    </div>

                                                </div>

                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                    <div class="form-input">

                                                        <label for="">Password</label>

                                                        <input type="password" name="adminPassword" class="form-control" id="adminPassword" value="<?= $admin_data['fldAdminPassword'] ?>">

                                                        <span class="error adminPassword"></span>

                                                    </div>

                                                </div>




                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="btn-section mt-2 mb-2 ml-auto">

                            <!-- <button class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" value="add_post">Update</button> -->

                            <button class="btn btn-danger save-close-btn float-right add_data waves-effect waves-light" value="add_draft">Update</button>

                        </div>


                    </div>



                    <!-- <div class="row">
            <div class="col-md-8">
              <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog" aria-hidden="true"></i></button>
              <div id="accordion">
                <div class="card card-primary">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseOne"> Branch Basic Details </a> </h4>
                  </div>
                  <div id="collapseOne" class="collapse show" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" class="form-control" id="branch_gstin" name="branch_gstin">
                            <label>GSTIN </label>
                            <span class="error branch_gstin"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="branch_name" class="form-control" id="branch_name" value="">
                            <label>Trade Name</label>
                            <span class="error branch_name"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="con_business" class="form-control" id="con_business" value="">
                            <label>Constitution of Business</label>
                            <span class="error con_business"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="build_no" class="form-control" id="build_no" value="">
                            <label>Building Number</label>
                            <span class="error build_no"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="flat_no" class="form-control" id="flat_no" value="">
                            <label>Flat Number</label>
                            <span class="error flat_no"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="street_name" class="form-control" id="street_name" value="">
                            <label>Street Name</label>
                            <span class="error street_name"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="number" name="pincode" class="form-control" id="pincode" value="">
                            <label>Pin Code</label>
                            <span class="error pincode"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="location" class="form-control" id="location" value="">
                            <label>Location</label>
                            <span class="error location"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="city" class="form-control" id="city" value="">
                            <label>City</label>
                            <span class="error city"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="district" class="form-control" id="district" value="">
                            <label>District</label>
                            <span class="error district"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="state" class="form-control" id="state" value="">
                            <label>State</label>
                            <span class="error state"></span>
                          </div>
                        </div>



                      </div>
                    </div>
                  </div>
                </div>
                <div class="card card-primary">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseTwo"> Branch Admin Details </a> </h4>
                  </div>
                  <div id="collapseTwo" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="adminName" class="form-control" id="adminName">
                            <label>User Name</label>
                            <span class="error adminName"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="email" name="adminEmail" class="form-control" id="adminEmail">
                            <label>User Email</label>
                            <span class="error adminEmail"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="adminPhone" class="form-control" id="adminPhone">
                            <label>User Phone</label>
                            <span class="error adminPhone"></span>

                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="adminPassword" class="form-control" id="adminPassword" value="<?php echo rand(1111, 9999); ?>">
                            <label>Password</label>
                            <span class="error adminPassword"></span>
                          </div>
                        </div>



                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                  <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item"> <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">TAB1</a> </li>
                    <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">TAB2</a> </li>
                    <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-messages-tab" data-toggle="pill" href="#custom-tabs-three-messages" role="tab" aria-controls="custom-tabs-three-messages" aria-selected="false">TAB3</a> </li>
                    <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-settings-tab" data-toggle="pill" href="#custom-tabs-three-settings" role="tab" aria-controls="custom-tabs-three-settings" aria-selected="false">TAB4</a> </li>
                  </ul>
                </div>
                <div class="card-body fontSize">
                  <div class="tab-content" id="custom-tabs-three-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab"> 90 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin malesuada lacus ullamcorper
                      dui
                      molestie, sit amet congue quam finibus. Etiam ultricies nunc non magna feugiat commodo. Etiam
                      odio
                      magna, mollis auctor felis vitae, ullamcorper ornare ligula. Proin pellentesque tincidunt nisi,
                      vitae ullamcorper felis aliquam id. Pellentesque habitant morbi tristique senectus et netus et
                      malesuada fames ac turpis egestas. Proin id orci eu lectus blandit suscipit. Phasellus porta,
                      ante
                      et varius ornare, sem enim sollicitudin eros, at commodo leo est vitae lacus. Etiam ut porta
                      sem.
                      Proin porttitor porta nisl, id tempor risus rhoncus quis. In in quam a nibh cursus pulvinar non
                      consequat neque. Mauris lacus elit, condimentum ac condimentum at, semper vitae lectus. Cras
                      lacinia erat eget sapien porta consectetur. </div>
                    <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab"> Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut
                      ligula
                      tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                      Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas
                      sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu
                      lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod
                      pellentesque diam. </div>
                    <div class="tab-pane fade" id="custom-tabs-three-messages" role="tabpanel" aria-labelledby="custom-tabs-three-messages-tab"> Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue
                      id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac
                      tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit
                      condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus
                      tristique.
                      Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est
                      libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id
                      fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna. </div>
                    <div class="tab-pane fade" id="custom-tabs-three-settings" role="tabpanel" aria-labelledby="custom-tabs-three-settings-tab"> Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis
                      ac,
                      ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi
                      euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum
                      placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam.
                      Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet
                      accumsan ex sit amet facilisis. </div>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
                </form>

                <!-- modal -->
                <div class="modal" id="myModal3">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Heading</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="col-md-12 mb-3">
                                    <div class="input-group">
                                        <select name="goodsGroup" class="form-control form-control-border borderColor">
                                            <option value="">Branches Group</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                                        <label>Item Code</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group btn-col">
                                        <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
                        </div>
                    </div>
                </div>
                <!-- modal end -->
                <!-- modal -->
                <div class="modal" id="myModal4">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Heading4</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="col-md-12 mb-3">
                                    <div class="input-group">
                                        <select name="goodsGroup" class="form-control form-control-border borderColor">
                                            <option value="">Branches Group</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                                        <label>Item Code</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group btn-col">
                                        <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
                        </div>
                    </div>
                </div>
                <!-- modal end -->
            </div>
        </section>
        <!-- /.content -->
    </div>
<?php
} else if (isset($_GET['view']) && $_GET["view"] > 0) {
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
                            <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Branches</a></li>
                            <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">View Branches</a></li>
                        </ol>
                    </div>
                    <div class="col-md-6" style="display: flex;">
                        <a href="<?= basename($_SERVER['PHP_SELF']); ?>"><button class="btn btn-danger btnstyle ml-2">Back</button></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-8">
                            <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog" aria-hidden="true"></i></button>
                            <div id="accordion">
                                <div class="card card-primary">
                                    <div class="card-header cardHeader">
                                        <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseOne"> Classification </a> </h4>
                                    </div>
                                    <div id="collapseOne" class="collapse show" data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="input-group">
                                                        <select id="" name="goodsType" class="select2 form-control form-control-border borderColor">
                                                            <option value="">Branches Type</option>
                                                            <option value="A">A</option>
                                                            <option value="B">B</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="input-group">
                                                        <select name="goodsGroup" class="select4 form-control form-control-border borderColor">
                                                            <option value="">Branches Group</option>
                                                            <option value="A">A</option>
                                                            <option value="B">B</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <select name="purchaseGroup" class="select2 form-control form-control-border borderColor">
                                                            <option value="">Purchase Group</option>
                                                            <option value="">A</option>
                                                            <option value="">B</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <input type="text" name="branh" class="form-control" id="exampleInputBorderWidth2">
                                                        <label>Branches</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <select name="availabilityCheck" class="select2 form-control form-control-border borderColor">
                                                            <option value="">Availability Check</option>
                                                            <option value="Daily">Daily</option>
                                                            <option value="Weekly">Weekly</option>
                                                            <option value="By Weekly">By Weekly</option>
                                                            <option value="Monthly">Monthly</option>
                                                            <option value="Qtr">Qtr</option>
                                                            <option value="Half Y">Half Y</option>
                                                            <option value="Year">Year</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-danger">
                                    <div class="card-header cardHeader">
                                        <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseTwo"> Basic Details </a> </h4>
                                    </div>
                                    <div id="collapseTwo" class="collapse" data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                                                        <label>Item Code</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <input type="text" name="itemName" class="form-control" id="exampleInputBorderWidth2">
                                                        <label>Item Name</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <input type="text" name="netWeight" class="form-control" id="exampleInputBorderWidth2">
                                                        <label>Net Weight</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <input type="text" name="grossWeight" class="form-control" id="exampleInputBorderWidth2">
                                                        <label>Gross Weight</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">Volume :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="volume" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="volume">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">height :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="height" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="height">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">width :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="width" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="width">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">length :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="length" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="length">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">Base Unit Of Measure :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="baseUnitMeasure" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="baseUnitOfMeasure">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">Issue Unit :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="issueUnit" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="issueUnit">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <textarea type="text" name="itemDesc" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Item Description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-success">
                                    <div class="card-header cardHeader">
                                        <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseThree"> Storage Details </a> </h4>
                                    </div>
                                    <div id="collapseThree" class="collapse" data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">Storage Bin :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="storageBin" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Storage Bin">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">Picking Area :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="pickingArea" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Picking Area">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">Temp Control :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="tempControl" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Temp Control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">Storage Control :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="storageControl" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Storage Control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">Max Storage Period :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="maxStoragePeriod" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Max Storage Period">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">Time Unit :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="timeUnit" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Time Unit">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">Min Remain Self Life :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="minRemainSelfLife" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Min Remain Self Life">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-success">
                                    <div class="card-header cardHeader">
                                        <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseFour"> Purchase Details </a> </h4>
                                    </div>
                                    <div id="collapseFour" class="collapse" data-parent="#accordion">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="" class="form-control borderNone">Purchasing Value Key :</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" name="purchasingValueKey" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Purchasing Value Key">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <!-- /.content -->
    </div>
<?php
} else {
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">

                        <ul class="nav nav-tabs border-0 mb-3" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Manage Profit & Lose</h3>
                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
                            </li>
                        </ul>


                        <div class="card card-tabs">

                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

                                <div class="card-body">

                                    <div class="row filter-serach-row">

                                        <div class="col-lg-2 col-md-2 col-sm-12">

                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                        </div>

                                        <div class="col-lg-10 col-md-10 col-sm-12">

                                            <div class="row table-header-item">

                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                    <div class="section serach-input-section">



                                                        <input type="text" id="myInput" placeholder="" class="field form-control" />

                                                        <div class="icons-container">

                                                            <div class="icon-search">

                                                                <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>

                                                            </div>

                                                            <div class="icon-close">

                                                                <i class="fa fa-search po-list-icon" onclick="javascript:alert('Hello World!')" id="myBtn"></i>

                                                                <script>
                                                                    var input = document.getElementById("myInput");

                                                                    input.addEventListener("keypress", function(event) {

                                                                        if (event.key === "Enter") {

                                                                            event.preventDefault();

                                                                            document.getElementById("myBtn").click();

                                                                        }

                                                                    });
                                                                </script>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                                <!-- <div class="col-lg-1 col-md-1 col-sm-1">

                                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

                                                </div> -->

                                            </div>



                                        </div>

                                    </div>

                                </div>



                            </form>
                            <!-- <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                <div class="card-body">
                  <div class="row filter-serach-row">
                    <div class="col-lg-2 col-md-2 col-sm-12">
                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog" aria-hidden="true"></i></a>
                    </div>
                    <div class="col-lg-10 col-md-10 col-sm-12">
                      <div class="section serach-input-section">

                        <div class="collapsible-content">
                          <div class="filter-col">

                            <div class="row">
                              <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group-manage-vendor">
                                  <select name="vendor_status_s" id="vendor_status_s" class="form-control">
                                    <option value="">--- Status --</option>
                                    <option value="active" <?php if (isset($_REQUEST['vendor_status_s']) && 'active' == $_REQUEST['vendor_status_s']) {
                                                                echo 'selected';
                                                            } ?>>Active</option>
                                    <option value="inactive" <?php if (isset($_REQUEST['vendor_status_s']) && 'inactive' == $_REQUEST['vendor_status_s']) {
                                                                    echo 'selected';
                                                                } ?>>Inactive</option>
                                    <option value="draft" <?php if (isset($_REQUEST['vendor_status_s']) && 'draft' == $_REQUEST['vendor_status_s']) {
                                                                echo 'selected';
                                                            } ?>>Draft</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group-manage-vendor"> <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                    echo $_REQUEST['form_date_s'];
                                                                                                                                                                } ?>" />
                                </div>
                              </div>
                             <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group-manage-vendor"> <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                    echo $_REQUEST['form_date_s'];
                                                                                                                                                                } ?>" />
                                </div>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-2">
                                <div class="input-group-manage-vendor"> 
                              <input type="text" name="keyword" class="fld form-control form-control" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                            echo $_REQUEST['keyword'];
                                                                                                                                                        } ?>">
                              </div>
                              </div>


                              <div class="col-lg-2 col-md-2 col-sm-2">
                                <button type="submit" class="btn btn-primary btnstyle">Search</button>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-2">
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btnstyle">Reset</a>
                              </div>
                            </div>






                          </div>
                        </div>
                        <button type="button" class="collapsible btn-search-collpase" id="btnSearchCollpase">
                          <i class="fa fa-search"></i>
                        </button>
                      </div>

                    </div>
                  </div>

              </form> -->
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="overflow: auto;">
                                    <table id="mytable" class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Group</th>
                                                <th>2020-21</th>
                                                <th>April</th>
                                                <th>May</th>
                                                <th>June</th>
                                                <th>July</th>
                                                <th>August</th>
                                                <th>Sept</th>
                                                <th>Oct</th>
                                                <th>Nov</th>
                                                <th>Dec</th>
                                                <th>Jan</th>
                                                <th>Feb</th>
                                                <th>YTD Feb 2022</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="font-bold">Revenue</td>
                                                <td>1301.29</td>
                                                <td>34.14</td>
                                                <td>76.11</td>
                                                <td>52.24</td>
                                                <td>119.92</td>
                                                <td>165.53</td>
                                                <td>35.69</td>
                                                <td>126.87</td>
                                                <td>36.82</td>
                                                <td>70.13</td>
                                                <td>35.85</td>
                                                <td>37.43</td>
                                                <td>790.93</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold">Direct Expenses and Cost</td>
                                                <td>1301.29</td>
                                                <td>34.14</td>
                                                <td>76.11</td>
                                                <td>52.24</td>
                                                <td class="text-danger">119.92</td>
                                                <td>165.53</td>
                                                <td>35.69</td>
                                                <td>126.87</td>
                                                <td class="text-danger">36.82</td>
                                                <td>70.13</td>
                                                <td class="text-danger">35.85</td>
                                                <td>37.43</td>
                                                <td>790.93</td>
                                            </tr>
                                            <tr>
                                                <td class="font-bold">% age of Revenue</td>
                                                <td class="font-bold">20%</td>
                                                <td class="font-bold">13%</td>
                                                <td class="font-bold">5%</td>
                                                <td class="font-bold">8%</td>
                                                <td class="font-bold">4%</td>
                                                <td class="font-bold">5%</td>
                                                <td class="font-bold">8%</td>
                                                <td class="font-bold">2%</td>
                                                <td class="font-bold">11%</td>
                                                <td class="font-bold">4%</td>
                                                <td class="font-bold">9%</td>
                                                <td class="font-bold">11%</td>
                                                <td class="font-bold">6%</td>
                                            </tr>
                                        </tbody>
                                    </table>


                                  
                            </div>
                            <!--  -->


                            <!---------------------------------Table settings Model Start--------------------------------->

                            <div class="modal" id="myModal2">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Table Column Settings</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                            <input type="hidden" name="tablename" value="<?= TBL_COMPANY_ADMIN_TABLESETTINGS; ?>" />
                                            <input type="hidden" name="pageTableName" value="ERP_BRANCHES" />
                                            <div class="modal-body">
                                                <div id="dropdownframe"></div>
                                                <div id="main2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                Branches Code</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                Branches Name</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                GSTIN</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="4" />
                                                                Address</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" name="add-table-settings" class="btn btn-success">Save</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!---------------------------------Table Model End--------------------------------->

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
    $('.form-control').on('keyup', function() {
        $(this).parent().children('.error').hide()
    });
    $(".add_data").click(function() {
        var data = this.value;
        $("#createdata").val(data);
        let flag = 1;
        if (data == 'add_post') {
            if ($("#branch_gstin").val() == "") {
                $(".branch_gstin").show();
                $(".branch_gstin").html("GSTIN  is requried.");
                flag++;
            } else {
                $(".branch_gstin").hide();
                $(".branch_gstin").html("");
            }
            if ($("#branch_name").val() == "") {
                $(".branch_name").show();
                $(".branch_name").html(" Trade name is requried.");
                flag++;
            } else {
                $(".branch_name").hide();
                $(".branch_name").html("");
            }
            if ($("#con_business").val() == "") {
                $(".con_business").show();
                $(".con_business").html("Constitution of Business is requried.");
                flag++;
            } else {
                $(".con_business").hide();
                $(".con_business").html("");
            }
            if ($("#build_no").val() == "") {
                $(".build_no").show();
                $(".build_no").html("Build number is requried.");
                flag++;
            } else {
                $(".build_no").hide();
                $(".build_no").html("");
            }
            if ($("#flat_no").val() == "") {
                $(".flat_no").show();
                $(".flat_no").html("Flat number is requried.");
                flag++;
            } else {
                $(".flat_no").hide();
                $(".flat_no").html("");
            }
            if ($("#street_name").val() == "") {
                $(".street_name").show();
                $(".street_name").html(" is requried.");
                flag++;
            } else {
                $(".street_name").hide();
                $(".street_name").html("");
            }
            if ($("#pincode").val() == "") {
                $(".pincode").show();
                $(".pincode").html("pincode is requried.");
                flag++;
            } else {
                $(".pincode").hide();
                $(".pincode").html("");
            }
            if ($("#location").val() == "") {
                $(".location").show();
                $(".location").html("location is requried.");
                flag++;
            } else {
                $(".location").hide();
                $(".location").html("");
            }
            if ($("#city").val() == "") {
                $(".city").show();
                $(".city").html("city is requried.");
                flag++;
            } else {
                $(".city").hide();
                $(".city").html("");
            }
            if ($("#district").val() == "") {
                $(".district").show();
                $(".district").html("district is requried.");
                flag++;
            } else {
                $(".district").hide();
                $(".district").html("");
            }
            if ($("#state").val() == "") {
                $(".state").show();
                $(".state").html("state is requried.");
                flag++;
            } else {
                $(".state").hide();
                $(".state").html("");
            }
            if ($("#adminName").val() == "") {
                $(".adminName").show();
                $(".adminName").html("username is requried.");
                flag++;
            } else {
                $(".adminName").hide();
                $(".adminName").html("");
            }
            var Regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            if ($("#adminEmail").val() == "") {
                $(".adminEmail").show();
                $(".adminEmail").html("Email is requried.");
                flag++;
            } else {
                if ($("#adminEmail").val().match(Regex)) {
                    console.log($("#adminEmail").val())
                    $(".adminEmail").show();
                    $(".adminEmail").html("");
                    flag++;
                } else {
                    console.log("1")
                    $(".adminEmail").show();
                    $(".adminEmail").html("Enter a valid email.");
                }
            }
            if ($("#adminPhone").val() == "") {
                $(".adminPhone").show();
                $(".adminPhone").html("Phone number is requried.");
                flag++;
            } else {
                $(".adminPhone").hide();
                $(".adminPhone").html("");
            }
            if ($("#adminPassword").val() == "") {
                $(".adminPassword").show();
                $(".adminPassword").html("Password is requried.");
                flag++;
            } else {
                $(".adminPassword").hide();
                $(".adminPassword").html("");
            }
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
        alert(data);
        //$( "#edit_frm" ).submit();
    });


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

    var inputs = document.getElementsByClassName("form-control");
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