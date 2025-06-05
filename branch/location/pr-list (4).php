<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-branch-pr-controller.php");
require_once("../../app/v1/functions/branch/func-items-controller.php");
$ItemsObj = new ItemsController();
$variant = $_SESSION['visitBranchAdminInfo']['flAdminVariant'];
$check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$variant");
$check_var_data = $check_var_sql['data'];
// console($_SESSION);
// // console($check_var_sql);
// console($check_var_sql);
$max = $check_var_data['month_end'];
$min = $check_var_data['month_start'];
// console($_SESSION); 
$today = date("Y-m-d");
if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"],);
}

if (isset($_POST["visit"])) {
    $newStatusObj = VisitBranches($_POST);
    redirect(BRANCH_URL);
}

// if (isset($_POST["createdata"])) {
//   $addNewObj = createDataBranches($_POST);
//   if ($addNewObj["status"] == "success") {
//     $branchId = base64_encode($addNewObj['branchId']);
//     redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
//     swalToast($addNewObj["status"], $addNewObj["message"]);
//     // console($addNewObj);
//   } else {
//     swalToast($addNewObj["status"], $addNewObj["message"]);
//   }
// }


if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

// if (isset($_POST["posubmit"])) {
//   console($_POST);

// }

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchPrObj = new BranchPr();

if (isset($_POST['addNewPRFormSubmitBtn'])) {

    $addBranchPr = $BranchPrObj->addBranchPr($_POST);

    if ($addBranchPr["status"] == "success") {
        swalToast($addBranchPr["status"], $addBranchPr["message"], $_SERVER['PHP_SELF']);
    } else {
        swalToast($addBranchPr["status"], $addBranchPr["message"]);
    }
}

if (isset($_POST["editNewPRFormSubmitBtn"])) {
    //console($_SESSION);
    $editBranchPr = $BranchPrObj->updatePR($_POST);
    // $branchId = base64_encode($addNewObj['branchId']);
    // redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
    swalToast($editBranchPr["status"], $editBranchPr["message"]);
}



if (isset($_POST['addNewRFQFormSubmitBtn'])) {

    $addBranchRfq = $BranchPrObj->addBranchRFQ($_POST);

    swalToast($addBranchRfq["status"], $addBranchRfq["message"]);
}
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
    .matrix-card .row:nth-child(1):hover {

        pointer-events: none;

    }

    .matrix-card .row:hover {

        border-radius: 0 0 10px 10px;

    }

    .matrix-card .row:nth-child(1) {

        background: #fff;

    }

    .matrix-card .row .col {

        display: flex;

        align-items: center;

    }

    .matrix-accordion button {

        color: #fff;

        border-radius: 15px !important;

        margin: 20px 0;

    }

    .accordion-button:not(.collapsed) {

        color: #fff;

    }

    .accordion-button::after {

        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

    }

    .accordion-button:not(.collapsed)::after {

        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

    }

    .accordion-item {

        border-radius: 15px !important;

        margin-bottom: 2em;

    }

    .info-h4 {

        font-size: 20px;

        font-weight: 600;

        color: #003060;

        padding: 0px 10px;

    }

    .tab-content li a span,
    .tab-content li a i {

        font-weight: 600;

    }


    .float-add-btn {

        display: flex !important;

    }

    @media (max-width: 575px) {

        .rfq-modal .modal-body {

            padding: 20px !important;

        }

    }

    @media(max-width: 390px) {

        .display-flex-space-between .matrix-btn {

            position: relative;

            top: 10px;

        }

    }
</style>

<?php
if (isset($_GET['closed'])) {

?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="p-0 pt-1 my-2">
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                    <h3 class="card-title">Manage Purchase Request</h3>
                                    <a href="manage-pr.php?pr-creation" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a>
                                </li>
                            </ul>
                        </div>
                        <div class="filter-list">
                            <a href="manage-pr.php" class="btn"><i class="fa fa-stream mr-2"></i>All</a>
                            <a href="pr-list.php?item" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                            <a href="pr-list.php" class="btn "><i class="fa fa-lock-open mr-2 "></i>Open PR</a>
                            <a href="pr-list.php?closed" class="btn active"><i class="fa fa-lock mr-2 active"></i>Closed PR</a>
                        </div>
                        <?php
                        $keywd = '';
                        if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
                            $keywd = $_REQUEST['keyword'];
                        } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
                            $keywd = $_REQUEST['keyword2'];
                        } ?>
                        <div class="card card-tabs" style="border-radius: 20px;">
                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                                <div class="card-body">
                                    <div class="row filter-serach-row">
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-10 col-md-10 col-sm-12">
                                            <div class="section serach-input-section">
                                                <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="<?php echo $keywd; ?>">
                                                <div class="icons-container">
                                                    <div class="icon-search">
                                                        <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                    </div>
                                                    <div class="icon-close">
                                                        <i class="fa fa-search po-list-icon" id="myBtn"></i>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Request</h5>

                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php /*if (isset($_REQUEST['keyword2'])) {
                                                                                                                                                      echo $_REQUEST['keyword2'];
                                                                                                                                                    } */ ?>">
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                <select name="status_s" id="status_s" class="fld form-control" style="appearance: auto;">
                                                                    <option value=""> Status </option>
                                                                    <option value="active" <?php if (isset($_REQUEST['status_s']) && 'active' == $_REQUEST['status_s']) {
                                                                                                echo 'selected';
                                                                                            } ?>>Active
                                                                    </option>
                                                                    <option value="inactive" <?php if (isset($_REQUEST['status_s']) && 'inactive' == $_REQUEST['status_s']) {
                                                                                                    echo 'selected';
                                                                                                } ?>>Inactive
                                                                    </option>
                                                                    <option value="draft" <?php if (isset($_REQUEST['status_s']) && 'draft' == $_REQUEST['status_s']) {
                                                                                                echo 'selected';
                                                                                            } ?>>Draft</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                            echo $_REQUEST['form_date_s'];
                                                                                                                                                        } ?>" />
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                <input class="fld form-control" type="date" name="to_date_s" id="to_date_s" value="<?php if (isset($_REQUEST['to_date_s'])) {
                                                                                                                                                        echo $_REQUEST['to_date_s'];
                                                                                                                                                    } ?>" />
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync "></i>Reset</a>-->
                                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                                                            Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                            </form>
                            <script>
                                var input = document.getElementById("myInput");
                                input.addEventListener("keypress", function(event) {
                                    if (event.key === "Enter") {
                                        event.preventDefault();
                                        document.getElementById("myBtn").click();
                                    }
                                });
                                var form = document.getElementById("search");

                                document.getElementById("myBtn").addEventListener("click", function() {
                                    form.submit();
                                });
                            </script>
                            <!-- <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog" aria-hidden="true"></i></a> -->
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                    <?php
                                    // console($_POST);
                                    $cond = '';

                                    $sts = " AND `status`!='deleted'";
                                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                        $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                    }

                                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                        $cond .= " AND expectedDate between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }


                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND (`prCode` like '%" . $_REQUEST['keyword2'] . "%' OR `refNo` like '%" . $_REQUEST['keyword2'] . "%' OR `description` like '%" . $_REQUEST['keyword2'] . "%')";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND (`prCode` like '%" . $_REQUEST['keyword'] . "%'  OR `refNo` like '%" . $_REQUEST['keyword'] . "%' OR `description` like '%" . $_REQUEST['keyword'] . "%')";
                                        }
                                    }
                                    $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE 1 " . $cond . "  AND pr_status=10 AND company_id='" . $company_id . "' " . $sts . "  ORDER BY purchaseRequestId desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                    $qry_list = queryGet($sql_list, true);
                                    $num_list = $qry_list['numRows'];


                                    $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE 1 " . $cond . " AND pr_status=10 AND company_id='" . $company_id . "' " . $sts . " ";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];


                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_PURCHASE_REQUEST", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>#</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>PR Number</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th>Required Date</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th>Reference Number</th>
                                                    <?php  }

                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <th>Status</th>
                                                    <?php }


                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <th>Validity Period</th>

                                                    <?php }

                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <th>Created By</th>

                                                    <?php } ?>

                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $soList = $qry_list['data'];

                                                foreach ($soList as $onePrList) {
                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $onePrList['prCode'] ?></td>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= formatDateORDateTime($onePrList['expectedDate']) ?></td>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td><?= $onePrList['refNo'] ?></td>
                                                        <?php }

                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <td><?php
                                                                echo "closed";
                                                                ?></td>
                                                        <?php }




                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td>
                                                                <?php

                                                                if ($onePrList['validityperiod'] != '') {
                                                                    $date1 = new DateTime($onePrList['validityperiod']);
                                                                    $date2 = new DateTime(date('Y-m-d'));

                                                                    $interval = $date1->diff($date2);
                                                                    $countdays = $interval->format('%a');
                                                                    $day = "";
                                                                    if ($countdays > 1) {
                                                                        $day = "days";
                                                                    } else {
                                                                        $day = "day";
                                                                    }


                                                                    if ($onePrList['validityperiod'] < date('Y-m-d')) {
                                                                        echo "expired";
                                                                    } else {
                                                                        echo $countdays . " " . $day." Remaining";
                                                                    
                                                                    }
                                                                } else {
                                                                    echo '-';
                                                                }

                                                                ?>
                                                            </td>
                                                        <?php }




                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td><?= getCreatedByUser($onePrList['created_by']) ?></td>
                                                        <?php } ?>
                                                        <td>

                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePrList['purchaseRequestId'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                        </td>
                                                    </tr>
                                                    <!-- right modal start here  -->
                                                    <div class="modal fade right customer-modal pr-modal" id="fluidModalRightSuccessDemo_<?= $onePrList['purchaseRequestId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                            <!--Content-->
                                                            <div class="modal-content">
                                                                <!--Header-->
                                                                <div class="modal-header pt-3">
                                                                    <p class="heading lead  mt-2 mb-4">PR Code : <?= $onePrList['prCode'] ?></p>
                                                                    <p class="text-sm  mt-2 mb-2">Ref Number : <?= $onePrList['refNo'] ?></p>
                                                                    <p class="text-sm  mt-2 mb-2">Required Date : <?= $onePrList['expectedDate'] ?></p>
                                                                    <p class="text-sm  mt-2 mb-2">status: <span class="status status-modal ml-2"><?php if ($onePrList['status'] != null) {
                                                                                                                                                        echo $onePrList['status'];
                                                                                                                                                    } else {
                                                                                                                                                        echo "PENDING";
                                                                                                                                                    }  ?></span></p>
                                                                    <p class="text-xs mt-2 mb-2">Note : <?= $onePrList['description'] ?></p>


                                                                </div>
                                                                <!--Body-->
                                                                <div class="modal-body px-4">
                                                                    <div class="tab-content pt-1" id="myTabContent">
                                                                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                                                            <div class="col-md-12">

                                                                                <div class="purchase-create-section mt-2 mb-4" id="action-navbar">
                                                                                    <form action="" method="POST">
                                                                                        <input type="hidden" value="<?= $onePrList['prCode'] ?>" name="prCode">
                                                                                        <input type="hidden" value="<?= $onePrList['purchaseRequestId'] ?>" name="prid">
                                                                                        <?php if ($onePrList['status'] == "") { ?>
                                                                                            <i title="Delete" style="font-size: 1.2em" class="fa fa-trash text-danger mx-3"></i>
                                                                                        <?php } ?>
                                                                                        <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on text-primary mx-3"></i>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card">

                                                                                <div class="card-body p-3">
                                                                                    <div class="display-flex rfq-item-title mt-2 mb-2">
                                                                                        <h4 class="info-h4 mb-0">
                                                                                            Item
                                                                                        </h4>
                                                                                        <div class="action-btn-flex">
                                                                                            <a href="<?= LOCATION_URL ?>manage-rfq.php?prid=<?= $onePrList['purchaseRequestId'] ?>" class="btn btn-primary"><i class="fa fa-list pr-2"></i> RFQ LIST</a>
                                                                                            <button type="submit" name="addNewRFQFormSubmitBtn" class="btn btn-primary float-right"><i class="fa fa-plus pr-2"></i> Add To RFQ</button>
                                                                                            <a href="manage-pr.php?edit=<?= $onePrList['purchaseRequestId'] ?>" type="submit" name="editNewRFQFormSubmitBtn" class="btn btn-primary float-right"><i class="fa fa-edit pr-2"></i>Edit</a>
                                                                                        </div>
                                                                                    </div>
                                                                                    <hr class="mt-1 mb-1">


                                                                                    <div class="row px-3 p-0 m-0 mb-2">


                                                                                        <?php
                                                                                        $itemDetails = $BranchPrObj->fetchBranchPrItems($onePrList['purchaseRequestId'])['data'];
                                                                                        // console($itemDetails);
                                                                                        // exit();
                                                                                        // console($_POST);
                                                                                        foreach ($itemDetails as $oneItem) {

                                                                                        ?>



                                                                                            <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                                <div class="accordion-item">
                                                                                                    <h2 class="accordion-header" id="flush-headingOne">
                                                                                                        <input type="checkbox" class="rfq-item-checkbox" name="itemId[]" value="<?= $oneItem['itemId'] ?>" />
                                                                                                        <button class="accordion-button btn btn-primary collapsed mb-1 pl-5" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                            <?= $oneItem['itemName'] ?>
                                                                                                            &nbsp;
                                                                                                            <p class="font-bold text-xs">Remaining Quantity :</p>
                                                                                                            <p class="font-bold text-xs"><?= $oneItem['remainingQty'] ?></p>

                                                                                                            <?php
                                                                                                            $itemId = $oneItem['itemId'];
                                                                                                            $prId = $onePrList['purchaseRequestId'];
                                                                                                            $query = "SELECT * FROM erp_rfq_items WHERE prId = '$prId' AND ItemId = '$itemId'";
                                                                                                            $qry = queryGet($query, true);
                                                                                                            $num = $qry['numRows'];

                                                                                                            if ($num != 0) {
                                                                                                            ?>
                                                                                                                <span class="badge badge-primary ml-2">Added</span>
                                                                                                            <?php
                                                                                                            }
                                                                                                            ?>
                                                                                                        </button>
                                                                                                    </h2>
                                                                                                    <div id="basicDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                        <div class="accordion-body p-0">
                                                                                                            <div class="card bg-white">

                                                                                                                <div class="card-body p-3">

                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs">Item Code :</p>
                                                                                                                        <p class="font-bold text-xs"><?= $oneItem['itemCode'] ?></p>
                                                                                                                    </div>
                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs">Item Name :</p>
                                                                                                                        <p class="font-bold text-xs"><?= $oneItem['itemName'] ?></p>
                                                                                                                    </div>
                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs">Item Quantity :</p>
                                                                                                                        <p class="font-bold text-xs"><?= $oneItem['itemQuantity'] ?></p>
                                                                                                                    </div>
                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs">UOM :</p>
                                                                                                                        <p class="font-bold text-xs"><?= $oneItem['uomName'] ?></p>
                                                                                                                    </div>

                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs">Note :</p>
                                                                                                                        <p class="font-bold text-xs"><?= $oneItem['itemNote'] ?></p>
                                                                                                                    </div>

                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>







                                                                                        <?php } ?>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>



                                                                            </div>


                                                                        </div>
                                                                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--/.Content-->
                                                        </div>
                                                    </div>
                                                    <!-- right modal end here  -->
                                                <?php } ?>
                                            </tbody>
                                            <tbody>
                                                <tr>
                                                    <td colspan="9">
                                                        <!-- Start .pagination -->

                                                        <?php
                                                        if ($count > 0 && $count > $GLOBALS['show']) {
                                                        ?>
                                                            <div class="pagination align-right">
                                                                <?php pagination($count, "frm_opts"); ?>
                                                            </div>

                                                            <!-- End .pagination -->

                                                        <?php  } ?>

                                                        <!-- End .pagination -->
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } else { ?>
                                        <table class="table defaultDataTable table-hover text-nowrap">
                                            <thead>
                                                <tr>
                                                    <td>

                                                    </td>
                                                </tr>
                                            </thead>
                                        </table>
                                </div>
                            <?php } ?>
                            </div>
                            <!---------------------------------Table settings Model Start--------------------------------->

                            <div class="modal" id="myModal2">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Table Column Settings</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                            <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                            <input type="hidden" name="pageTableName" value="ERP_VENDOR_DETAILS" />
                                            <div class="modal-body">
                                                <div id="dropdownframe"></div>
                                                <div id="main2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                PR Number</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                Required Date </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                Reference Number</td>
                                                        </tr>

                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                                                Status</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                                                validity period</td>
                                                        </tr>


                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                                                Created By</td>
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
    </div>
    </section>
    </div>
    <!-- For Pegination------->
    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>
    <!-- End Pegination from------->



<?php
} else if (isset($_GET['item'])) {
?>

    <div class="content-wrapper">
        <!-- Modal -->
        <div class="modal fade" id="itemModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="itemModalContent modal-content card">
                    <div class="modal-header card-header py-2 px-3">
                        <h4 class="modal-title font-monospace text-md text-white" id="itemModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div id="itemModalBody" class="modal-body card-body">
                    </div>
                </div>
            </div>
        </div>
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="p-0 pt-1 my-2">
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                    <h3 class="card-title">Manage Purchase Request</h3>
                                    <a href="manage-pr.php?pr-creation" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a>
                                </li>
                            </ul>
                        </div>

                        <div class="filter-list">
                            <a href="manage-pr.php" class="btn"><i class="fa fa-stream mr-2"></i>All</a>
                            <a href="pr-list.php?item" class="btn active"><i class="fa fa-list mr-2 active"></i>Item Order List</a>
                            <a href="pr-list.php" class="btn "><i class="fa fa-lock-open mr-2 "></i>Open PR</a>
                            <a href="pr-list.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PR</a>
                        </div>


                        <?php
                        $keywd = '';
                        if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
                            $keywd = $_REQUEST['keyword'];
                        } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
                            $keywd = $_REQUEST['keyword2'];
                        } ?>
                        <div class="card card-tabs" style="border-radius: 20px;">
                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                                <div class="card-body">
                                    <div class="row filter-serach-row">
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-10 col-md-10 col-sm-12">
                                            <div class="section serach-input-section">
                                                <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="<?php echo $keywd; ?>">
                                                <div class="icons-container">
                                                    <div class="icon-search">
                                                        <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                    </div>
                                                    <div class="icon-close">
                                                        <i class="fa fa-search po-list-icon" id="myBtn"></i>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Request</h5>

                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php /*if (isset($_REQUEST['keyword2'])) {
                                                                                                                                                      echo $_REQUEST['keyword2'];
                                                                                                                                                    } */ ?>">
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                <select name="status_s" id="status_s" class="fld form-control" style="appearance: auto;">
                                                                    <option value=""> Status </option>
                                                                    <option value="active" <?php if (isset($_REQUEST['status_s']) && 'active' == $_REQUEST['status_s']) {
                                                                                                echo 'selected';
                                                                                            } ?>>Active
                                                                    </option>
                                                                    <option value="inactive" <?php if (isset($_REQUEST['status_s']) && 'inactive' == $_REQUEST['status_s']) {
                                                                                                    echo 'selected';
                                                                                                } ?>>Inactive
                                                                    </option>
                                                                    <option value="draft" <?php if (isset($_REQUEST['status_s']) && 'draft' == $_REQUEST['status_s']) {
                                                                                                echo 'selected';
                                                                                            } ?>>Draft</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                            echo $_REQUEST['form_date_s'];
                                                                                                                                                        } ?>" />
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                <input class="fld form-control" type="date" name="to_date_s" id="to_date_s" value="<?php if (isset($_REQUEST['to_date_s'])) {
                                                                                                                                                        echo $_REQUEST['to_date_s'];
                                                                                                                                                    } ?>" />
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync "></i>Reset</a>-->
                                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                                                            Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                            </form>

                            <script>
                                var input = document.getElementById("myInput");
                                input.addEventListener("keypress", function(event) {
                                    if (event.key === "Enter") {
                                        event.preventDefault();
                                        document.getElementById("myBtn").click();
                                    }
                                });
                                var form = document.getElementById("search");

                                document.getElementById("myBtn").addEventListener("click", function() {
                                    form.submit();
                                });
                            </script>

                            <!-- <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog" aria-hidden="true"></i></a> -->
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                    <?php
                                    // console($_POST);
                                    $cond = '';

                                    $sts = " AND pr.status!='deleted'";
                                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                        $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                    }

                                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                        $cond .= " AND pr.expectedDate between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }


                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND (pr.prCode like '%" . $_REQUEST['keyword2'] . "%' OR item.itemCode like '%" . $_REQUEST['keyword2'] . "%' OR item.itemName like '%" . $_REQUEST['keyword2'] . "%' OR pr.refNo like '%" . $_REQUEST['keyword2'] . "%' OR pr.description like '%" . $_REQUEST['keyword2'] . "%')";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND (pr.prCode like '%" . $_REQUEST['keyword'] . "%' OR item.itemCode like '%" . $_REQUEST['keyword'] . "%' OR item.itemName like '%" . $_REQUEST['keyword'] . "%' OR pr.refNo like '%" . $_REQUEST['keyword'] . "%' OR pr.description like '%" . $_REQUEST['keyword'] . "%')";
                                        }
                                    }
                                    $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` as item LEFT JOIN `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr ON item.prId = pr.purchaseRequestId WHERE 1 " . $cond . "  AND pr.company_id='" . $company_id . "' " . $sts . "  ORDER BY purchaseRequestId desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                    $qry_list = queryGet($sql_list, true);
                                    $num_list = $qry_list['numRows'];


                                    $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_PURCHASE_REQUEST_ITEMS . "` as item LEFT JOIN `" . ERP_BRANCH_PURCHASE_REQUEST . "` as pr ON item.prId = pr.purchaseRequestId  WHERE 1 " . $cond . " AND pr.company_id='" . $company_id . "' " . $sts . " ";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];


                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_PURCHASE_REQUEST_ITEM", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>


                                        <form action="manage-purchases-orders.php" method="GET" name="">
                                            <table class="table defaultDataTable table-hover">
                                                <thead>
                                                    <tr class="alert-light">
                                                        <th></th>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <th>Item Code</th>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <th>Item Name</th>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <th>Item Type</th>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <th>PR Number</th>
                                                        <?php  }

                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <th>Posting Date</th>

                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <th>Required Date</th>

                                                        <?php }

                                                        if (in_array(7, $settingsCheckbox)) { ?>
                                                            <th>Quantity</th>

                                                        <?php }
                                                        if (in_array(8, $settingsCheckbox)) { ?>
                                                            <th>Remaining Quantity</th>

                                                        <?php } ?>


                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <input type="hidden" name="form_submit" value="1">

                                                    <button type="submit" class="btn btn-primary ">Create Purchase Order</button>

                                                    <?php
                                                    $soList = $qry_list['data'];

                                                    foreach ($soList as $onePrList) {
                                                        //  console($onePrList);
                                                        $item_id = $onePrList['itemId'];
                                                        $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `itemId`=$item_id");
                                                        // console($type_sql);
                                                        $type_id = $type_sql['data']['goodsType'];
                                                        $type = queryGet("SELECT * FROM `erp_inventory_mstr_good_types` WHERE `goodTypeId`=$type_id");
                                                        $type_name = $type['data']['goodTypeName'];
                                                        $rand = rand(100, 1000);
                                                        $remainingQty = $onePrList['remainingQty'];



                                                    ?>
                                                        <tr>
                                                            <!-- <td><?= $cnt++ ?></td> -->


                                                            <td><input type="checkbox" name="selectItemPr[]" class="selectItemPr selectItemPr_<?= $rand ?>" id="selectItemPr" data-attr="<?= $type_name ?>" value="<?= $onePrList['prItemId'] ?> <?php if ($remainingQty <= 0) {
                                                                                                                                                                                                                                                        echo "disabled";
                                                                                                                                                                                                                                                    } ?>"></td>

                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                                <td><?= $onePrList['itemCode'] ?></td>
                                                            <?php }
                                                            if (in_array(2, $settingsCheckbox)) { ?>
                                                                <td><?= $onePrList['itemName'] ?></td>
                                                            <?php }
                                                            if (in_array(3, $settingsCheckbox)) { ?>
                                                                <td><?= $type_name ?></td>
                                                            <?php }
                                                            if (in_array(4, $settingsCheckbox)) { ?>
                                                                <td><?= $onePrList['prCode'] ?></td>
                                                            <?php }
                                                            if (in_array(5, $settingsCheckbox)) { ?>
                                                                <td><?= formatDateORDateTime($onePrList['pr_date']) ?></td>
                                                            <?php }
                                                            if (in_array(6, $settingsCheckbox)) { ?>
                                                                <td><?= formatDateORDateTime($onePrList['expectedDate']) ?></td>
                                                            <?php }
                                                            if (in_array(7, $settingsCheckbox)) { ?>
                                                                <td><?= $onePrList['itemQuantity'] ?></td>
                                                            <?php }
                                                            if (in_array(8, $settingsCheckbox)) { ?>
                                                                <td><?= $onePrList['remainingQty'] ?></td>
                                                            <?php } ?>

                                                        </tr>
                                                        <!-- right modal start here  -->
                                                        <div class="modal fade right customer-modal pr-modal" id="fluidModalRightSuccessDemo_<?= $onePrList['purchaseRequestId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                                <!--Content-->
                                                                <div class="modal-content">
                                                                    <!--Header-->
                                                                    <div class="modal-header pt-3">
                                                                        <p class="heading lead  mt-2 mb-4">PR Code : <?= $onePrList['prCode'] ?></p>
                                                                        <p class="text-sm  mt-2 mb-2">Ref Number : <?= $onePrList['refNo'] ?></p>
                                                                        <p class="text-sm  mt-2 mb-2">Required Date : <?= $onePrList['expectedDate'] ?></p>
                                                                        <p class="text-sm  mt-2 mb-2">status: <span class="status status-modal ml-2"><?php if ($onePrList['status'] != null) {
                                                                                                                                                            echo $onePrList['status'];
                                                                                                                                                        } else {
                                                                                                                                                            echo "PENDING";
                                                                                                                                                        }  ?></span></p>
                                                                        <p class="text-xs mt-2 mb-2">Note : <?= $onePrList['description'] ?></p>


                                                                    </div>
                                                                    <!--Body-->

                                                                </div>
                                                                <!--/.Content-->
                                                            </div>
                                                        </div>
                                                        <!-- right modal end here  -->
                                                    <?php } ?>


                                                </tbody>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="9">
                                                            <!-- Start .pagination -->

                                                            <?php
                                                            if ($count > 0 && $count > $GLOBALS['show']) {
                                                            ?>
                                                                <div class="pagination align-right">
                                                                    <?php pagination($count, "frm_opts"); ?>
                                                                </div>

                                                                <!-- End .pagination -->

                                                            <?php  } ?>

                                                            <!-- End .pagination -->
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </form>

                                    <?php } else { ?>
                                        <table class="table defaultDataTable table-hover text-nowrap">
                                            <thead>
                                                <tr>
                                                    <td>

                                                    </td>
                                                </tr>
                                            </thead>
                                        </table>
                                </div>
                            <?php } ?>
                            </div>

                            <!---------------------------------Table settings Model Start--------------------------------->

                            <div class="modal" id="myModal2">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Table Column Settings</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                            <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                            <input type="hidden" name="pageTableName" value="ERP_BRANCH_PURCHASE_REQUEST_ITEM" />
                                            <div class="modal-body">
                                                <div id="dropdownframe"></div>
                                                <div id="main2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                Item Code </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                Item Name </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                Item Type </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                                PR Number </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                                PR Date </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                                Required Date Remaining Quantity</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                                Item Quantity</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 166px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />
                                                                Remaining Quantity</td>
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
    </div>
    </section>
    </div>
    <!-- For Pegination------->
    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>
    <!-- End Pegination from------->


<?php
} else {
?>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="p-0 pt-1 my-2">
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                    <h3 class="card-title">Manage Purchase Request</h3>
                                    <a href="manage-pr.php?pr-creation" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></a>
                                </li>
                            </ul>
                        </div>
                        <div class="filter-list">
                            <a href="manage-pr.php" class="btn"><i class="fa fa-stream mr-2"></i>All</a>
                            <a href="pr-list.php?item" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                            <a href="pr-list.php" class="btn active"><i class="fa fa-lock-open mr-2 active"></i>Open PR</a>
                            <a href="pr-list.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PR</a>
                        </div>
                        <?php
                        $keywd = '';
                        if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
                            $keywd = $_REQUEST['keyword'];
                        } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
                            $keywd = $_REQUEST['keyword2'];
                        } ?>
                        <div class="card card-tabs" style="border-radius: 20px;">
                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                                <div class="card-body">
                                    <div class="row filter-serach-row">
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-10 col-md-10 col-sm-12">
                                            <div class="section serach-input-section">
                                                <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="<?php echo $keywd; ?>">
                                                <div class="icons-container">
                                                    <div class="icon-search">
                                                        <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                    </div>
                                                    <div class="icon-close">
                                                        <i class="fa fa-search po-list-icon" id="myBtn"></i>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Request</h5>

                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php /*if (isset($_REQUEST['keyword2'])) {
                                                                                                                                                      echo $_REQUEST['keyword2'];
                                                                                                                                                    } */ ?>">
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                <select name="status_s" id="status_s" class="fld form-control" style="appearance: auto;">
                                                                    <option value=""> Status </option>
                                                                    <option value="active" <?php if (isset($_REQUEST['status_s']) && 'active' == $_REQUEST['status_s']) {
                                                                                                echo 'selected';
                                                                                            } ?>>Active
                                                                    </option>
                                                                    <option value="inactive" <?php if (isset($_REQUEST['status_s']) && 'inactive' == $_REQUEST['status_s']) {
                                                                                                    echo 'selected';
                                                                                                } ?>>Inactive
                                                                    </option>
                                                                    <option value="draft" <?php if (isset($_REQUEST['status_s']) && 'draft' == $_REQUEST['status_s']) {
                                                                                                echo 'selected';
                                                                                            } ?>>Draft</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                            echo $_REQUEST['form_date_s'];
                                                                                                                                                        } ?>" />
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                <input class="fld form-control" type="date" name="to_date_s" id="to_date_s" value="<?php if (isset($_REQUEST['to_date_s'])) {
                                                                                                                                                        echo $_REQUEST['to_date_s'];
                                                                                                                                                    } ?>" />
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync "></i>Reset</a>-->
                                                        <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                                                            Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                            </form>
                            <script>
                                var input = document.getElementById("myInput");
                                input.addEventListener("keypress", function(event) {
                                    if (event.key === "Enter") {
                                        event.preventDefault();
                                        document.getElementById("myBtn").click();
                                    }
                                });
                                var form = document.getElementById("search");

                                document.getElementById("myBtn").addEventListener("click", function() {
                                    form.submit();
                                });
                            </script>
                            <!-- <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog" aria-hidden="true"></i></a> -->
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                    <?php
                                    // console($_POST);
                                    $cond = '';

                                    $sts = " AND `status`!='deleted'";
                                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                        $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                    }

                                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                        $cond .= " AND expectedDate between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }


                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND (`prCode` like '%" . $_REQUEST['keyword2'] . "%' OR `refNo` like '%" . $_REQUEST['keyword2'] . "%' OR `description` like '%" . $_REQUEST['keyword2'] . "%')";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND (`prCode` like '%" . $_REQUEST['keyword'] . "%'  OR `refNo` like '%" . $_REQUEST['keyword'] . "%' OR `description` like '%" . $_REQUEST['keyword'] . "%')";
                                        }
                                    }
                                    $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE 1 " . $cond . "  AND pr_status=9 AND company_id='" . $company_id . "' " . $sts . "  ORDER BY purchaseRequestId desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                    $qry_list = queryGet($sql_list, true);
                                    $num_list = $qry_list['numRows'];


                                    $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE 1 " . $cond . " AND pr_status=9 AND company_id='" . $company_id . "' " . $sts . " ";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];


                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_PURCHASE_REQUEST", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>#</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>PR Number</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th>Required Date</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th>Reference Number</th>
                                                    <?php  }

                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <th>Status</th>
                                                    <?php }

                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <th>Validity Period</th>

                                                    <?php }


                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <th>Created By</th>

                                                    <?php } ?>

                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $soList = $qry_list['data'];

                                                foreach ($soList as $onePrList) {
                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $onePrList['prCode'] ?></td>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= formatDateORDateTime($onePrList['expectedDate']) ?></td>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td><?= $onePrList['refNo'] ?></td>
                                                        <?php }

                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <td><?php
                                                                echo "open";
                                                                ?></td>
                                                        <?php }

                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td><?php

                                                                if ($onePrList['validityperiod'] != '') {
                                                                    $date1 = new DateTime($onePrList['validityperiod']);
                                                                    $date2 = new DateTime(date('Y-m-d'));

                                                                    $interval = $date1->diff($date2);
                                                                    $countdays = $interval->format('%a');
                                                                    $day = "";
                                                                    if ($countdays > 1) {
                                                                        $day = "days";
                                                                    } else {
                                                                        $day = "day";
                                                                    }


                                                                    if ($onePrList['validityperiod'] < date('Y-m-d')) {
                                                                        echo "expired";
                                                                    } else {
                                                                    
                                                                    echo $countdays . " " . $day." Remaining";

                                                                    }
                                                                } else {
                                                                    echo '-';
                                                                }


                                                                ?>
                                                            </td>
                                                        <?php }

                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td><?= getCreatedByUser($onePrList['created_by']) ?></td>
                                                        <?php } ?>
                                                        <td>

                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePrList['purchaseRequestId'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                        </td>
                                                    </tr>
                                                    <!-- right modal start here  -->
                                                    <div class="modal fade right customer-modal pr-modal" id="fluidModalRightSuccessDemo_<?= $onePrList['purchaseRequestId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                            <!--Content-->
                                                            <div class="modal-content">
                                                                <!--Header-->
                                                                <div class="modal-header pt-3">
                                                                    <p class="heading lead  mt-2 mb-4">PR Code : <?= $onePrList['prCode'] ?></p>
                                                                    <p class="text-sm  mt-2 mb-2">Ref Number : <?= $onePrList['refNo'] ?></p>
                                                                    <p class="text-sm  mt-2 mb-2">Required Date : <?= $onePrList['expectedDate'] ?></p>
                                                                    <p class="text-sm  mt-2 mb-2">status: <span class="status status-modal ml-2"><?php if ($onePrList['status'] != null) {
                                                                                                                                                        echo $onePrList['status'];
                                                                                                                                                    } else {
                                                                                                                                                        echo "PENDING";
                                                                                                                                                    }  ?></span></p>
                                                                    <p class="text-xs mt-2 mb-2">Note : <?= $onePrList['description'] ?></p>


                                                                </div>
                                                                <!--Body-->
                                                                <div class="modal-body px-4">
                                                                    <div class="tab-content pt-1" id="myTabContent">
                                                                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                                                            <div class="col-md-12">

                                                                                <div class="purchase-create-section mt-2 mb-4" id="action-navbar">
                                                                                    <form action="" method="POST">
                                                                                        <?php if ($onePrList['pr_status'] == 10) {
                                                                                        } else {
                                                                                        ?>
                                                                                            <a class="btn btn-primary create-purchase float-right" href="manage-purchases-orders.php?pr-po-creation=<?= $onePrList['purchaseRequestId'] ?>">Create Purchase Order</a>

                                                                                            <a class="btn btn-primary create-purchase float-right" href="manage-pr.php?close-pr=<?= $onePrList['purchaseRequestId'] ?>"> Close PR</a>
                                                                                        <?php
                                                                                        }
                                                                                        ?>
                                                                                        <input type="hidden" value="<?= $onePrList['prCode'] ?>" name="prCode">
                                                                                        <input type="hidden" value="<?= $onePrList['purchaseRequestId'] ?>" name="prid">
                                                                                        <?php if ($onePrList['status'] == "") { ?>
                                                                                            <i title="Delete" style="font-size: 1.2em" class="fa fa-trash text-danger mx-3"></i>
                                                                                        <?php } ?>
                                                                                        <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on text-primary mx-3"></i>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card">

                                                                                <div class="card-body p-3">
                                                                                    <div class="display-flex rfq-item-title mt-2 mb-2">
                                                                                        <h4 class="info-h4 mb-0">
                                                                                            Item
                                                                                        </h4>
                                                                                        <div class="action-btn-flex">
                                                                                            <a href="<?= LOCATION_URL ?>manage-rfq.php?prid=<?= $onePrList['purchaseRequestId'] ?>" class="btn btn-primary"><i class="fa fa-list pr-2"></i> RFQ LIST</a>
                                                                                            <button type="submit" name="addNewRFQFormSubmitBtn" class="btn btn-primary float-right"><i class="fa fa-plus pr-2"></i> Add To RFQ</button>
                                                                                            <a href="manage-pr.php?edit=<?= $onePrList['purchaseRequestId'] ?>" type="submit" name="editNewRFQFormSubmitBtn" class="btn btn-primary float-right"><i class="fa fa-edit pr-2"></i>Edit</a>
                                                                                        </div>
                                                                                    </div>
                                                                                    <hr class="mt-1 mb-1">


                                                                                    <div class="row px-3 p-0 m-0 mb-2">


                                                                                        <?php
                                                                                        $itemDetails = $BranchPrObj->fetchBranchPrItems($onePrList['purchaseRequestId'])['data'];
                                                                                        // console($itemDetails);
                                                                                        // exit();
                                                                                        // console($_POST);
                                                                                        foreach ($itemDetails as $oneItem) {

                                                                                        ?>



                                                                                            <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                                <div class="accordion-item">
                                                                                                    <h2 class="accordion-header" id="flush-headingOne">
                                                                                                        <input type="checkbox" class="rfq-item-checkbox" name="itemId[]" value="<?= $oneItem['itemId'] ?>" />
                                                                                                        <button class="accordion-button btn btn-primary collapsed mb-1 pl-5" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                            <?= $oneItem['itemName'] ?>
                                                                                                            &nbsp;
                                                                                                            <p class="font-bold text-xs">Remaining Quantity :</p>
                                                                                                            <p class="font-bold text-xs"><?= $oneItem['remainingQty'] ?></p>

                                                                                                            <?php
                                                                                                            $itemId = $oneItem['itemId'];
                                                                                                            $prId = $onePrList['purchaseRequestId'];
                                                                                                            $query = "SELECT * FROM erp_rfq_items WHERE prId = '$prId' AND ItemId = '$itemId'";
                                                                                                            $qry = queryGet($query, true);
                                                                                                            $num = $qry['numRows'];

                                                                                                            if ($num != 0) {
                                                                                                            ?>
                                                                                                                <span class="badge badge-primary ml-2">Added</span>
                                                                                                            <?php
                                                                                                            }
                                                                                                            ?>
                                                                                                        </button>
                                                                                                    </h2>
                                                                                                    <div id="basicDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                        <div class="accordion-body p-0">
                                                                                                            <div class="card bg-white">

                                                                                                                <div class="card-body p-3">

                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs">Item Code :</p>
                                                                                                                        <p class="font-bold text-xs"><?= $oneItem['itemCode'] ?></p>
                                                                                                                    </div>
                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs">Item Name :</p>
                                                                                                                        <p class="font-bold text-xs"><?= $oneItem['itemName'] ?></p>
                                                                                                                    </div>
                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs">Item Quantity :</p>
                                                                                                                        <p class="font-bold text-xs"><?= $oneItem['itemQuantity'] ?></p>
                                                                                                                    </div>
                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs">UOM :</p>
                                                                                                                        <p class="font-bold text-xs"><?= $oneItem['uomName'] ?></p>
                                                                                                                    </div>

                                                                                                                    <div class="display-flex-space-between">
                                                                                                                        <p class="font-bold text-xs">Note :</p>
                                                                                                                        <p class="font-bold text-xs"><?= $oneItem['itemNote'] ?></p>
                                                                                                                    </div>

                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>







                                                                                        <?php } ?>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>



                                                                            </div>


                                                                        </div>
                                                                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--/.Content-->
                                                        </div>
                                                    </div>
                                                    <!-- right modal end here  -->
                                                <?php } ?>
                                            </tbody>
                                            <tbody>
                                                <tr>
                                                    <td colspan="9">
                                                        <!-- Start .pagination -->

                                                        <?php
                                                        if ($count > 0 && $count > $GLOBALS['show']) {
                                                        ?>
                                                            <div class="pagination align-right">
                                                                <?php pagination($count, "frm_opts"); ?>
                                                            </div>

                                                            <!-- End .pagination -->

                                                        <?php  } ?>

                                                        <!-- End .pagination -->
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } else { ?>
                                        <table class="table defaultDataTable table-hover text-nowrap">
                                            <thead>
                                                <tr>
                                                    <td>

                                                    </td>
                                                </tr>
                                            </thead>
                                        </table>
                                </div>
                            <?php } ?>
                            </div>
                            <!---------------------------------Table settings Model Start--------------------------------->

                            <div class="modal" id="myModal2">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Table Column Settings</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                            <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                            <input type="hidden" name="pageTableName" value="ERP_VENDOR_DETAILS" />
                                            <div class="modal-body">
                                                <div id="dropdownframe"></div>
                                                <div id="main2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                PR Number</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                Required Date </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                Reference Number</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                                                Status</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                                                Validity Period</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                                                Created By</td>
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
    </div>
    </section>
    </div>
    <!-- For Pegination------->
    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>
    <!-- End Pegination from------->


<?php
}
require_once("../common/footer.php");
?>
<script>
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


    function rm() {
        $(event.target).closest("tr").remove();
    }

    function addMultiQty(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row_${id}`).append(`<tr><td><span class='has-float-label'><input type='date' name='listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]' class='form-control' placeholder='delivery date'><label>Delivery date</label></span></td><td><span class='has-float-label'><input type='text' name='listItem[${id}][deliverySchedule][${addressRandNo}][quantity]' class='form-control' placeholder='quantity'><label>quantity</label></span></td><td><a class='btn btn-danger' onclick='rm()'><i class='fa fa-minus'></i></a></td></tr>`);
    }
</script>
<script>
    $(document).ready(function() {
        $('#itemsDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });
        $('#customerDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });


        $("#usetypesDropdown").on("change", function() {
            let type = $(this).val();
            //  console.log(type);
            if (type != "") {
                $.ajax({
                    type: "GET",
                    url: `ajaxs/pr/ajax-items.php`,
                    data: {

                        type
                    },
                    beforeSend: function() {
                        $("#itemsDropDown").html(`<option value="">Loding...</option>`);
                    },
                    success: function(response) {
                        console.log(response);
                        $("#itemsDropDown").html(response);
                    }
                });
            } else {
                $("#itemsDropDown").html('');
            }
        });



        // function loadItems() {
        //   $.ajax({
        //     type: "GET",
        //     url: `ajaxs/pr/ajax-items.php`,
        //     beforeSend: function() {
        //       $("#itemsDropDown").html(`<option value="">Loding...</option>`);
        //     },
        //     success: function(response) {
        //       $("#itemsDropDown").html(response);
        //     }
        //   });
        // }
        // loadItems();

        // get item details by id
        $("#itemsDropDown").on("change", function() {
            let itemId = $(this).val();
            const searchValue = itemId;
            let flag = 0;
            $('.pr_item_list').each(function(index) {
                if ($(this).val().includes(searchValue)) {
                    console.log(`Search value ${searchValue} found in field ${index + 1}.`);
                    flag++;
                } else {
                    console.log(`Search value ${searchValue} not found`);
                }
            });
            if (flag == 0) {

                $.ajax({
                    type: "GET",
                    url: `ajaxs/pr/ajax-items-list.php`,
                    data: {
                        act: "listItem",
                        itemId
                    },
                    beforeSend: function() {
                        //  $("#itemsTable").html(`<option value="">Loding...</option>`);
                    },
                    success: function(response) {
                        // console.log(response);
                        $("#itemsTable").append(response);
                    }
                });
            } else {
                alert("item already exists!");
            }
        });
        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
        })

        $(document).on('submit', '#addNewItemForm', function(event) {
            event.preventDefault();
            let formData = $("#addNewItemsForm").serialize();
            $.ajax({
                type: "POST",
                url: `ajaxs/so/ajax-items.php`,
                data: formData,
                beforeSend: function() {
                    $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
                    $("#addNewItemsFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');
                },
                success: function(response) {
                    $("#goodTypeDropDown").html(response);
                    $('#addNewItemsForm').trigger("reset");
                    $("#addNewItemsFormModal").modal('toggle');
                    $("#addNewItemsFormSubmitBtn").html("Submit");
                    $("#addNewItemsFormSubmitBtn").toggleClass("disabled");
                }
            });
        });

        $(document).on("keyup change", ".qty", function() {
            let id = $(this).val();
            var sls = $(this).attr("sls");
            alert(sls);
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-items-list.php`,
                data: {
                    act: "totalPrice",
                    itemId: "ss",
                    id
                },
                beforeSend: function() {
                    $(".totalPrice").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $(".totalPrice").html(response);
                }
            });
        })

    })
    $('.hamburger').click(function() {
        $('.hamburger').toggleClass('show');
        $('#overlay').toggleClass('show');
        $('.nav-action').toggleClass('show');
    });


    function check_date() {

        let date = $("#prDate").val();

        let max = '<?php echo $max; ?>';
        let min = '<?php echo $min; ?>';


        if (date < min) {


            $("#prdatelabel").html(`<p class="text-danger text-xs" id="prdatelabel">Invalid PR creation Date</p>`);
            document.getElementById("prbtn").disabled = true;
        } else if (date > max) {
            $("#prdatelabel").html(`<p class="text-danger text-xs" id="prdatelabel">Invalid PR creation Date</p>`);
            document.getElementById("prbtn").disabled = true;
        } else {
            $("#prdatelabel").html("");
            document.getElementById("prbtn").disabled = false;

        }



    }

    function compare_date() {
        let prDate = $("#prDate").val();
        let expDate = $("#expDate").val();
        if (expDate < prDate) {
            console.log("error");
            $("#prdatelabel").html(`<p class="text-danger text-xs" id="prdatelabel">Can not be greater than Required Date</p>`);
            document.getElementById("prbtn").disabled = true;

        } else {
            $("#prdatelabel").html("");
            document.getElementById("prbtn").disabled = false;
        }
    }

    $("#prDate ").keyup(function() {

        check_date();
        compare_date();


    });
    $("#expDate").change(function() {
        compare_date();
    });

    $("#prDate").change(function() {
        compare_date();
    });

    let checkedItem;
    var typeCheck = [];

    $(document).on("click", ".selectItemPr", function() {
        atr = $(this).data('attr');
        typeCheck.push(atr);
        checkedItem = $(this);
        if ($(this).prop("checked")) {
            if (typeCheck.length > 1) {
                if (typeCheck[typeCheck.length - 1] !== typeCheck[typeCheck.length - 2]) {
                    $(".notesTypeCheck").remove();
                    $("#itemModalBody").append(
                        '<p class="notesTypeCheck font-monospace text-danger">You have selected items of different item types. Do you want to proceed with the new item type?</p>'
                    );
                    $(".modal-footer").remove();
                    $(".itemModalContent").append(
                        '<div class="modal-footer pt-0"><button type="button" class="yesType btn btn-secondary" data-bs-dismiss="modal">Yes</button><button type="button" class="noType btn btn-primary" data-bs-dismiss="modal">No</button></div>'
                    );
                    $("#itemModal").modal("show");
                } else {
                    $(".notesTypeCheck").remove();
                };
            }
        }
    });

    $(document).on("click", ".noType", function(e) {
        $(checkedItem).prop("checked", false);
        typeCheck.pop();
    });

    $(document).on("click", ".yesType", function(e) {
        for (elem of $(".selectItemPr")) {
            $(elem).prop("checked", false);
        };
        $(checkedItem).prop("checked", true);
    });
</script>

<!-- <script>
  $(".add_data").click(function() {

    console.log("form submiting...");
    $("#posubmit").submit();

  });
</script> -->