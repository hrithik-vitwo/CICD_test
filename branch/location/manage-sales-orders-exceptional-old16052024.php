<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");


// console($_SESSION);

if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}

if (isset($_POST["visit"])) {
    $newStatusObj = VisitBranches($_POST);
    redirect(BRANCH_URL);
}

if (isset($_POST["createdata"])) {
    $addNewObj = createDataBranches($_POST);
    if ($addNewObj["status"] == "success") {
        $branchId = base64_encode($addNewObj['branchId']);
        redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
        swalToast($addNewObj["status"], $addNewObj["message"]);
        // console($addNewObj);
    } else {
        swalToast($addNewObj["status"], $addNewObj["message"]);
    }
}

if (isset($_POST["editdata"])) {
    $editDataObj = updateDataBranches($_POST);

    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();

if (isset($_POST['addNewSOFormSubmitBtn'])) {
    // console($_POST);
    // exit;
    $addBranchSo = $BranchSoObj->addBranchSo($_POST);
    //console($addBranchSo);
    if ($addBranchSo['status'] == "success") {
        $addBranchSoItems = $BranchSoObj->addBranchSoItems($_POST, $addBranchSo['lastID']);
        //console($addBranchSoItems);
        if ($addBranchSoItems['status'] == "success") {
            // swalToast($addBranchSoItems["status"], $addBranchSoItems["message"]);
            swalToast($addBranchSoItems["status"], $addBranchSoItems["message"], $_SERVER['PHP_SELF']);
        } else {
            swalToast($addBranchSoItems["status"], $addBranchSoItems["message"]);
        }
    } else {
        swalToast($addBranchSo["status"], $addBranchSo["message"]);
    }
}

// $branchSoPendingList = $BranchSoObj->fetchBranchSoExceptionalListing()['data'];
// console($branchSoPendingList);
?>
<style>
    .filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .so_number-item {
        align-items: center;
        justify-content: space-between;
    }

    .item-count {
        display: flex;
        align-items: center;
    }

    .customer-modal .modal-header {
        height: 285px !important;
    }

    .icon-user-text {
        width: 100%;
    }

    .icon-user-img i {
        border: 1px solid #fff;
        padding: 15px 10px;
        border-radius: 7px;
    }

    .so-header {
        gap: 10px;
        display: flex;
        align-items: center;
    }
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<?php
if (isset($_GET['customer-so-creation'])) { ?>
    ...
<?php } else { ?>
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
                                    <h3 class="card-title">Manage SO Exceptional List</h3>
                                    <!-- <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?customer-so-creation" class="btn btn-sm btn-primary btnstyle m-2 float-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a> -->
                                </li>
                            </ul>
                        </div>
                        <div class="card card-tabs" style="border-radius: 20px;">
                            <?php
                            $keywd = '';
                            if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
                                $keywd = $_REQUEST['keyword'];
                            } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
                                $keywd = $_REQUEST['keyword2'];
                            } ?>
                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                                <div class="card-body">
                                    <div class="row filter-serach-row">
                                        <div class="col-lg-1 col-md-1 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-11 col-md-11 col-sm-12">
                                            <div class="row table-header-item">
                                                <div class="col-lg-11 col-md-11 col-sm-11">
                                                    <div class="filter-search">
                                                        <?php require_once('salesorder-filter-list.php'); ?>

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

                                                </div>
                                                <!-- <div class="col-lg-1 col-md-1 col-sm-1">
                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?customer-so-creation" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                        </div> -->
                                                <div class="col-lg-1 col-md-1 col-sm-1">
                                                    <a href="direct-create-invoice.php?sales_order_creation" class="btn btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Order</h5>

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

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                        <?php
                                        $cond = '';

                                        $sts = " AND `status` !='deleted'";
                                        if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                            $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                        }

                                        if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                            $cond .= " AND created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                        }

                                        if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                            $cond .= " AND `so_number` like '%" . $_REQUEST['keyword2'] . "%' OR `so_date` like '%" . $_REQUEST['keyword2'] . "%'";
                                        } else {
                                            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                                $cond .= " AND `so_number` like '%" . $_REQUEST['keyword'] . "%'  OR `so_date` like '%" . $_REQUEST['keyword'] . "%'";
                                            }
                                        }

                                        $sql_list = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE 1 " . $cond . "  AND approvalStatus=12 AND company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "' " . $sts . "  ORDER BY so_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                        $qry_list = mysqli_query($dbCon, $sql_list);
                                        $num_list = mysqli_num_rows($qry_list);

                                        $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "'  AND branch_id='" . $branch_id . "'  AND location_id='" . $location_id . "' AND approvalStatus=12 " . $sts . " ";
                                        $countQry = mysqli_query($dbCon, $countShow);
                                        $rowCount = mysqli_fetch_array($countQry);

                                        $ins = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE approvalStatus=14 AND status='active' ORDER BY so_id DESC";
                                        $soListing = queryGet($ins);

                                        $count = $rowCount[0];
                                        $cnt = $GLOBALS['start'] + 1;
                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_SALES_ORDER-EXCEPTIONAL", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                        $settingsCheckbox = unserialize($settingsCh);
                                        if ($num_list > 0) {
                                        ?>
                                            <table class="table defaultDataTable table-hover tableDataBody">
                                                <thead>
                                                    <tr class="alert-light">
                                                        <th>#</th>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <th>SO Number</th>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <th>Customer PO</th>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <th>Delivery Date</th>
                                                        <?php  }
                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <th>Customer Name</th>
                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <th>Customer Name</th>
                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <th>Status</th>
                                                            <?php
                                                            // }
                                                            // if (in_array(6, $settingsCheckbox)) { 
                                                            ?>
                                                            <!--   <th>Status</th> -->
                                                        <?php  }
                                                        if (in_array(7, $settingsCheckbox)) { ?>
                                                            <th>Total Items</th>
                                                        <?php }

                                                        if (in_array(8, $settingsCheckbox)) { ?>
                                                            <th>Validity Period</th>
                                                        <?php }


                                                        ?>

                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tableBody">
                                                    <?php
                                                    // console($BranchSoObj->fetchBranchSoListing()['data']);
                                                    foreach ($qry_list as $oneSoList) {
                                                        $goodsType = "";
                                                        if ($oneSoList['goodsType'] == "material") {
                                                            $goodsType = '<span style="border-radius: 10px 3px;padding: 0px 5px;background: antiquewhite;box-shadow: 0 0 5px #b9b9b9;font-style: italic;">GOODS</span>';
                                                        } elseif ($oneSoList['goodsType'] == "service") {
                                                            $goodsType = '<span style="border-radius: 10px 3px;padding: 0px 5px;background: #d7f8fa;box-shadow: 0 0 5px #b9b9b9;font-style: italic;">SERVICE</span>';
                                                        } elseif ($oneSoList['goodsType'] == "both") {
                                                            $goodsType = '<span style="border-radius: 10px 3px;padding: 0px 5px;background: #d7d7fa;box-shadow: 0 0 5px #b9b9b9;font-style: italic;">BOTH</span>';
                                                        } elseif ($oneSoList['goodsType'] == "project") {
                                                            $goodsType = '<span style="border-radius: 10px 3px;padding: 0px 5px;background: #d7fad9;box-shadow: 0 0 5px #b9b9b9;font-style: italic;">PROJECT</span>';
                                                        }

                                                        if (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "open") {
                                                            $approvalStatus = '<strong class="text-success">OPEN</strong>';
                                                        } elseif (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "pending") {
                                                            $approvalStatus = '<strong class="text-warning">PENDING</strong>';
                                                        } elseif (fetchStatusMasterByCode($oneSoList['approvalStatus'])['data']['label'] == "exceptional") {
                                                            $approvalStatus = '<strong class="text-dark">EXCEPTIONAL</strong>';
                                                        }
                                                    ?>
                                                        <tr class="tableOneRow">
                                                            <td><?= $cnt++ ?></td>
                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                                <td><?= $oneSoList['so_number'] ?></td>
                                                            <?php }
                                                            if (in_array(2, $settingsCheckbox)) { ?>
                                                                <td><?= $oneSoList['customer_po_no'] ?></td>
                                                            <?php }
                                                            if (in_array(3, $settingsCheckbox)) { ?>
                                                                <td><?= $oneSoList['delivery_date'] ?></td>
                                                            <?php }
                                                            if (in_array(4, $settingsCheckbox)) { ?>
                                                                <td><?= $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0]['trade_name'] ?></td>
                                                            <?php }
                                                            if (in_array(5, $settingsCheckbox)) { ?>
                                                                <td><?= $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0]['trade_name'] ?></td>
                                                            <?php }
                                                            if (in_array(6, $settingsCheckbox)) { ?>
                                                                <td><?= $goodsType ?> </td>
                                                                <?php
                                                                // }
                                                                // if (in_array(5, $settingsCheckbox)) { 
                                                                ?>
                                                                <!-- <td><?= $oneSoList['soStatus'] ?></td> -->
                                                            <?php }

                                                            if (in_array(7, $settingsCheckbox)) { ?>
                                                                <td><?= $oneSoList['totalItems'] ?></td>
                                                            <?php }
                                                            if (in_array(8, $settingsCheckbox)) { ?>
                                                                <td>
                                                                    <?php

                                                                    if ($oneSoList['validityperiod'] != '') {
                                                                        $date1 = new DateTime($oneSoList['validityperiod']);
                                                                        $date2 = new DateTime(date('Y-m-d'));

                                                                        $interval = $date1->diff($date2);
                                                                        $countdays = $interval->format('%a');
                                                                        $day = "";
                                                                        if ($countdays > 1) {
                                                                            $day = "days";
                                                                        } else {
                                                                            $day = "day";
                                                                        }


                                                                        if ($oneSoList['validityperiod'] < date('Y-m-d')) {
                                                                            echo "expired";
                                                                        } else {
                                                                            echo $countdays . " " . $day . " Remaining";
                                                                        }
                                                                    } else {
                                                                        echo '-';
                                                                    }
                                                                    ?>
                                                                </td>
                                                            <?php }



                                                            ?>
                                                            <td>
                                                                <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $oneSoList['so_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                            </td>
                                                        </tr>
                                                        <?php $customerDetails =  $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0] ?>
                                                        <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $oneSoList['so_number'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                                <!--Content-->
                                                                <div class="modal-content">
                                                                    <!--Header-->
                                                                    <div class="modal-header">

                                                                        <div class="so-header">

                                                                            <div class="icon-user-img">

                                                                                <i class="fa fa-user"></i>

                                                                            </div>

                                                                            <div class="icon-user-text">

                                                                                <p class="text-sm text-white mt-3"><?= $customerDetails['trade_name'] ?></p>

                                                                                <div class="d-flex so_number-item mt-1 mb-2">

                                                                                    <p class="heading lead text-xs"><?= $oneSoList['so_number'] ?></p>

                                                                                    <div class="item-count text-xs">
                                                                                        <p class="round-item-count text-xs"><?= $oneSoList['totalItems'] ?></p>
                                                                                        <p>Items</p>
                                                                                    </div>

                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                        <div class="customer-head-info mb-0 mt-2">
                                                                            <div class="customer-name-code">
                                                                                <h2 class="text-lg mb-0"><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneSoList['totalAmount'] ?></h2>
                                                                                <p class="text-xs">(Fourteen Thousands One Hundred Ten rupees)</p>
                                                                                <!-- <p class="heading lead"><?= $oneSoList['so_number'] ?></p>
                                      <p>Cust CO/REF :&nbsp;<?= $oneSoList['customer_po_no'] ?></p> -->
                                                                            </div>
                                                                            <!-- <div class="customer-image">
                                      <div class="name-item-count">
                                        <h5 style="font-size: .8rem;"><?= $customerDetails['trade_name'] ?></h5>
                                        <span>
                                          <div class="round-item-count"><?= $oneSoList['totalItems'] ?></div> Items
                                        </span>
                                      </div>
                                      <i class="fa fa-user"></i>
                                    </div> -->
                                                                        </div>



                                                                        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true" class="white-text">×</span>
                                </button> -->

                                                                        <!-- action btn  -->
                                                                        <!-- <?php if ($oneSoList['approvalStatus'] == 11) { ?>
                                    <a href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" class="btn btn-sm" title="Create Delivery"><i class="fa fa-plus po-list-icon"></i></a>
                                  <?php } else { ?>
                                    <a href="#" class="btn btn-sm" title="Create Delivery"><i class="fa fa-box po-list-icon"></i></a>
                                  <?php } ?> -->
                                                                        <!-- <a href="#" class="btn btn-sm" title="Edit SO"><i class="fa fa-edit po-list-icon"></i></a>
                                  <a href="#" class="btn btn-sm" title="Delete SO"><i class="fa fa-trash po-list-icon"></i></a> -->
                                                                        <!-- action btn  -->

                                                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                            <li class="nav-item">
                                                                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $oneSoList['so_number'] ?>" role="tab" aria-controls="home" aria-selected="true">Item Info</a>
                                                                            </li>
                                                                            <!-- <li class="nav-item">
                                      <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= $oneSoList['so_number'] ?>" role="tab" aria-controls="profile" aria-selected="false">Customer Info</a>
                                    </li> -->
                                                                            <!-- -------------------Audit History Button Start------------------------- -->
                                                                            <li class="nav-item">
                                                                                <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $oneSoList['so_number']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $oneSoList['so_number']) ?>" href="#history<?= str_replace('/', '-', $oneSoList['so_number']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $oneSoList['so_number']) ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                                                            </li>
                                                                            <!-- -------------------Audit History Button End------------------------- -->
                                                                            <li class="nav-item">
                                                                                <a class="nav-link bg-success approvalTab" style="cursor: pointer;" id="approvalTab_<?= $oneSoList['so_id'] ?>">Approve</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>

                                                                    <div class="modal-body">
                                                                        <div class="tab-content" id="myTabContent">
                                                                            <div class="tab-pane fade show active" id="home<?= $oneSoList['so_number'] ?>" role="tabpanel" aria-labelledby="home-tab">

                                                                                <form action="" method="POST">
                                                                                    <div class="hamburger">
                                                                                        <div class="wrapper-action">
                                                                                            <i class="fa fa-bell fa-2x"></i>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="nav-action" id="settings">

                                                                                        <a title="Mail the customer" href="#" name="vendorEditBtn">
                                                                                            <i class="fa fa-envelope"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="nav-action" id="thumb">
                                                                                        <a title="Chat the customer" href="#" name="vendorEditBtn">
                                                                                            <i class="fab fa-whatsapp" aria-hidden="true"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="nav-action" id="create">
                                                                                        <a title="Call the customer" href="#" name="vendorEditBtn">
                                                                                            <i class="fa fa-phone"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                </form>

                                                                                <!-- <form action="" method="POST">
                                        <div class="hamburger">
                                          <div class="wrapper-action">
                                            <i class="fa fa-cog fa-2x"></i>
                                          </div>
                                        </div>
                                        <div class="nav-action" id="settings">
                                          <?php if ($oneSoList['approvalStatus'] == 11) { ?>
                                            <a title="Delivery Creation" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                              <i class="fa fa-box"></i>
                                            </a>
                                          <?php } else { ?>
                                            <a title="Can't access 'Delivery Creation'" href="#" name="vendorEditBtn">
                                              <i class="fa fa-box"></i>
                                            </a>
                                          <?php } ?>
                                        </div>

                                        <div class="nav-action" id="thumb">
                                          <a title="Notify Me" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                            <i class="fa fa-bell"></i>
                                          </a>
                                        </div>
                                        <div class="nav-action" id="create">
                                          <a title="Edit" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                            <i class="fa fa-edit"></i>
                                          </a>
                                        </div>
                                        <div class="nav-action trash" id="share">
                                          <a title="Delete" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($oneSoList['so_number']) ?>" name="vendorEditBtn">
                                            <i class="fa fa-trash"></i>
                                          </a>
                                        </div>
                                      </form> -->

                                                                                <?php
                                                                                $customerDetails = $BranchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0];
                                                                                // console($customerDetails);
                                                                                $customerAddressDetails = $BranchSoObj->fetchCustomerAddressDetails($customerDetails['customer_id'])['data'][0];
                                                                                ?>
                                                                                <div class="item-detail-section">
                                                                                    <!-- <h6>Items Details</h6> -->
                                                                                    <?php
                                                                                    $itemDetails = $BranchSoObj->fetchBranchSoItems($oneSoList['so_id'])['data'];
                                                                                    // console($itemDetails);
                                                                                    foreach ($itemDetails as $oneItem) {
                                                                                        $uomName = getUomDetail($oneItem['uom'])['data']['uomName'];
                                                                                    ?>
                                                                                        <div class="card">
                                                                                            <div class="card-body">
                                                                                                <div class="row">
                                                                                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                                                                                        <div class="left-section">
                                                                                                            <div class="icon-img">
                                                                                                                <i class="fa fa-box"></i>
                                                                                                            </div>
                                                                                                            <div class="code-des">
                                                                                                                <h4><?= $oneItem['itemCode'] ?></h4>
                                                                                                                <p><?= $oneItem['itemName'] ?></p>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                        <div class="right-section">
                                                                                                            <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneItem['unitPrice'] ?> * <?= $oneItem['qty'] ?> <?= $uomName ?></p>
                                                                                                            <!-- <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneItem['unitPrice'] * $oneItem['qty'] ?></p> -->
                                                                                                            <div class="discount">
                                                                                                                <p><span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span><?= $oneItem['unitPrice'] * $oneItem['qty'] ?></p>
                                                                                                                (-<?= $oneItem['totalDiscount'] ?>%)
                                                                                                            </div>
                                                                                                            <p>(GST: <?= $oneItem['tax'] ?>%)</p>
                                                                                                            <div class="font-weight-bold">
                                                                                                                <span style="font-family: 'Font Awesome 5 Free';">&#x20B9;</span> <?= $oneItem['totalPrice'] ?>
                                                                                                            </div>
                                                                                                            <!-- <div class="discount">
                                                    <p><?= $oneItem['itemTotalDiscount'] ?></p>
                                                    (-<?= $oneItem['totalDiscount'] ?>%)
                                                  </div> -->
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <hr>
                                                                                                <?php
                                                                                                $deliverySchedule = $BranchSoObj->fetchBranchSoItemsDeliverySchedule2($oneItem['so_item_id'])['data'];
                                                                                                // console($deliverySchedule);
                                                                                                foreach ($deliverySchedule as $dSchedule) {
                                                                                                ?>
                                                                                                    <div class="row">
                                                                                                        <div class="col-lg-8 col-md-8 col-sm-8">
                                                                                                            <div class="left-section">
                                                                                                                <div class="icon-img">
                                                                                                                    <i class="fa fa-clock"></i>
                                                                                                                </div>
                                                                                                                <div class="date-time-parent">
                                                                                                                    <div class="date-time">
                                                                                                                        <div class="code-des">
                                                                                                                            <h4>
                                                                                                                                <?php
                                                                                                                                // $timestamp = $dSchedule['delivery_date'];
                                                                                                                                // $dt1 = date_format($timestamp, "d");
                                                                                                                                echo $dSchedule['delivery_date'];
                                                                                                                                ?>
                                                                                                                                <small class="text-secondary text-capitalize">(<?= $dSchedule['deliveryStatus'] ?>)</small>
                                                                                                                                <?php
                                                                                                                                // $date=date_create($dSchedule['delivery_date']);
                                                                                                                                // echo date_format($date,"Y/F/d");
                                                                                                                                ?>
                                                                                                                            </h4>
                                                                                                                        </div>
                                                                                                                        <p>
                                                                                                                            <?php
                                                                                                                            // echo $timestamp = $dSchedule['delivery_date'];
                                                                                                                            // $dt2 = date("Y", strtotime($timestamp));
                                                                                                                            // echo $dt2;
                                                                                                                            ?>
                                                                                                                        </p>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                            <div class="right-section unit">
                                                                                                                <div class="dropdown">
                                                                                                                    <button class="btn btn-secondary dropdown-toggle date-time-item" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                                                                        <?= $dSchedule['qty'] ?> <?= $uomName ?>
                                                                                                                    </button>
                                                                                                                </div>
                                                                                                            </div>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                <?php } ?>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php } ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade" id="profile<?= $oneSoList['so_number'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                                                                <div class="card p-2">
                                                                                    <div class="card-body">
                                                                                        <div class="row">
                                                                                            <!-- <div class="col-lg-2 col-md-2 col-sm-2">
                                            <div class="icon">
                                              <i class="fa fa-hashtag"></i>
                                            </div>
                                          </div> -->
                                                                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                <span>Code: </span>
                                                                                            </div>
                                                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                <p>
                                                                                                    <?= $customerDetails['customer_code'] ?>
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <hr>
                                                                                        <div class="row">
                                                                                            <!-- <div class="col-lg-2 col-md-2 col-sm-2">
                                            <div class="icon">
                                              <i class="fa fa-hashtag"></i>
                                            </div>
                                          </div> -->
                                                                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                <span>Pan: </span>
                                                                                            </div>
                                                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                <p>
                                                                                                    <?= $customerDetails['customer_pan'] ?>
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <hr>
                                                                                        <div class="row">
                                                                                            <!-- <div class="col-lg-2 col-md-2 col-sm-2">
                                            <div class="icon">
                                              <i class="fa fa-hashtag"></i>
                                            </div>
                                          </div> -->
                                                                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                <span>GST: </span>
                                                                                            </div>
                                                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                <p>
                                                                                                    <?= $customerDetails['customer_gstin'] ?>
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <hr>
                                                                                        <div class="row">
                                                                                            <!-- <div class="col-lg-2 col-md-2 col-sm-2">
                                            <div class="icon">
                                              <i class="fa fa-hashtag"></i>
                                            </div>
                                          </div> -->
                                                                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                <span>Address: </span>
                                                                                            </div>
                                                                                            <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                <p>
                                                                                                    <?= $customerAddressDetails['customer_address_building_no'] . ', ' . $customerAddressDetails['customer_address_flat_no'] . ', ' . $customerAddressDetails['customer_address_street_name'] . ', ' . $customerAddressDetails['customer_address_pin_code'] . ', ' . $customerAddressDetails['customer_address_location'] . ', ' . $customerAddressDetails['customer_address_city'] . ', ' . $customerAddressDetails['customer_address_district'] . ', ' . $customerAddressDetails['customer_address_state'] ?>
                                                                                                </p>
                                                                                            </div>
                                                                                        </div>
                                                                                        <hr>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                            <div class="tab-pane fade" id="history<?= str_replace('/', '-', $oneSoList['so_number']) ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                <div class="audit-head-section mb-3 mt-3 ">
                                                                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($oneSoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['created_at']) ?></p>
                                                                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($oneSoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($oneSoList['updated_at']) ?></p>
                                                                                </div>
                                                                                <hr>
                                                                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $oneSoList['so_number'] ?>">

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
                                            <input type="hidden" name="pageTableName" value="ERP_BRANCH_SALES_ORDER-EXCEPTIONAL" />
                                            <div class="modal-body">
                                                <div id="dropdownframe"></div>
                                                <div id="main2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                SO Number</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                                Customer PO Number</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                Delivery Date</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                                                Customer Name</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                                                Type</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                                                Status</td>
                                                        </tr>
                                                        <!-- <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="7" />
                                Status</td>
                            </tr> -->
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="7" />
                                                                Total Items</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="8" />
                                                                Validity Period</td>
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
    </div> <!-- For Pegination------->
    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>
    <!-- End Pegination from------->
<?php } ?>

<?php
require_once("../common/footer.php");
?>

<script>
    $(document).on("click", ".dlt-popup", function() {
        $(this).parent().parent().remove();
    });

    function rm() {
        // $(event.target).closest("tr").remove();
        $(this).parent().parent().parent().remove();
    }

    function addMultiQty(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        //$(`.modal-add-row_${id}`).append(`<tr><td><span class='has-float-label'><input type='date' name='listItem[${id}][deliverySchedule][${addressRandNo}][multiDeliveryDate]' class='form-control' placeholder='delivery date' required><label>Delivery date</label></span></td><td><span class='has-float-label'><input type='text' name='listItem[${id}][deliverySchedule][${addressRandNo}][quantity]' class='form-control multiQuantity' data-itemid="${id}" id='multiQuantity_${addressRandNo}' placeholder='quantity' required><label>quantity</label></span></td><td><a class='btn btn-danger' onclick='rm()'><i class='fa fa-minus'></i></a></td></tr>`);
        $(`.modal-add-row_${id}`).append(`
      <div class="modal-add-row">
        <div class="row modal-cog-right">
          <div class="col-lg-5 col-md-5 col-sm-5">
              <div class="form-input">
                  <label>Delivery date</label>
                  <input type="date" name="listItem[${id}][deliverySchedule][${id}][multiDeliveryDate]" class="form-control multiDeliveryDate" id="multiDeliveryDate_${id}" placeholder="delivery date" value="<?= $_GET['deliveryDate'] ?>">

              </div>
          </div>
          <div class="col-lg-5 col-md-5 col-sm-5">
              <div class="form-input">
                  <label>Quantity</label>
                  <input type="text" name="listItem[${id}][deliverySchedule][${id}][quantity]" class="form-control multiQuantity" data-itemid="${id}" id="multiQuantity_${id}" placeholder="quantity" value="1">

              </div>
          </div>
          <div class="col-lg-2 col-md-2 col-sm-2 dlt-popup">
              <a style="cursor: pointer" class="btn btn-danger">
                  <i class="fa fa-minus"></i>
              </a>
          </div>
        </div>
      </div>`);
    }
</script>



<script>
    $(document).ready(function() {
        loadItems();

        loadCustomers();


        // **************************************
        function loadItems() {
            // alert();
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-items.php`,
                beforeSend: function() {
                    $("#itemsDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $("#itemsDropDown").html(response);
                }
            });
        }

        // customers ********************************
        function loadCustomers() {
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-customers.php`,
                beforeSend: function() {
                    $("#customerDropDown").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    $("#customerDropDown").html(response);
                }
            });
        }
        // get customer details by id
        $("#customerDropDown").on("change", function() {
            let customerId = $(this).val();

            if (customerId > 0) {
                $(document).on("click", ".billToCheckbox", function() {
                    if ($('input.billToCheckbox').is(':checked')) {
                        // $(".shipTo").html(`checked ${customerId}`);
                        $.ajax({
                            type: "GET",
                            url: `ajaxs/so/ajax-customers-address.php`,
                            data: {
                                act: "customerAddress",
                                customerId
                            },
                            beforeSend: function() {
                                $("#shipTo").html(`Loding...`);
                            },
                            success: function(response) {
                                console.log(response);
                                $("#shipTo").html(response);
                            }
                        });
                    } else {
                        $(".changeAddress").click();
                        // $("#shipTo").html(`unchecked ${customerId}`);
                    }
                });

                $(".customerIdInp").val(customerId);
                $.ajax({
                    type: "GET",
                    url: `ajaxs/so/ajax-customers-list.php`,
                    data: {
                        act: "listItem",
                        customerId
                    },
                    beforeSend: function() {
                        $("#customerInfo").html(`<option value="">Loding...</option>`);
                    },
                    success: function(response) {
                        console.log(response);
                        $("#customerInfo").html(response);
                        let creditPeriod = $("#spanCreditPeriod").text();
                        $("#inputCreditPeriod").val(creditPeriod);
                    }
                });
            }
        });

        $(document).on("click", "#pills-home-tab", function() {
            $("#saveChanges").html('<button type="button" class="btn btn-primary go">Go</button>');
        });
        $(document).on("click", "#pills-profile-tab", function() {
            $("#saveChanges").html('<button type="button" class="btn btn-primary" id="save">Save</button>');
        });

        // 👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀
        $(document).on('click', '.go', function() {
            let the_value = $('input[name=radioBtn]:radio:checked').val();

            console.log(the_value);
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-customers-address.php`,
                data: {
                    act: "shipAddressRadio",
                    addressKey: the_value
                },
                beforeSend: function() {
                    $(`.go`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                },
                success: function(response) {
                    console.log(response);
                    $(".address-change-modal").hide();
                    $(".modal-backdrop").hide();
                    $("#shipTo").html(response);
                    $("#shippingAddressInp").val(response);
                    $('input.billToCheckbox').prop('checked', false);
                    $(".go").html('<button type="button" class="btn btn-primary go">Go</button>');
                }
            });
        });

        // submit address form
        $(document).on('click', '#save', function() {
            let customerId = $('.customerIdInp').val();
            let billingNo = $("#billingNo").val();
            let flatNo = $("#flatNo").val();
            let streetName = $("#streetName").val();
            let location = $("#location").val();
            let city = $("#city").val();
            let pinCode = $("#pinCode").val();
            let district = $("#district").val();
            let state = $("#state").val();

            if (billingNo != '' || flatNo != '' || streetName != '' || location != '' || city != '' || pinCode != '' || district != '' || state != '') {
                $.ajax({
                    type: "GET",
                    url: `ajaxs/so/ajax-customers-address.php`,
                    data: {
                        act: "shipAddressSave",
                        customerId,
                        billingNo,
                        flatNo,
                        streetName,
                        location,
                        city,
                        pinCode,
                        district,
                        state
                    },
                    beforeSend: function() {
                        $(`#save`).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {
                        // console.log(response);
                        $(".address-change-modal").hide();
                        $(".modal-backdrop").hide();
                        $("#shipTo").html(response);
                        $('input.billToCheckbox').prop('checked', false);
                    }
                });
            } else {
                alert(`All field are required`);
            }
        });
        // 👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀👀

        // get item details by id
        $("#itemsDropDown").on("change", function() {
            let itemId = $(this).val();
            if (itemId > 0) {
                let deliveryDate = $('#deliveryDate').val();
                $.ajax({
                    type: "GET",
                    url: `ajaxs/so/ajax-items-list.php`,
                    data: {
                        act: "listItem",
                        itemId,
                        deliveryDate
                    },
                    beforeSend: function() {
                        //  $(`#spanItemsTable`).html(`Loding...`);
                    },
                    success: function(response) {
                        console.log(response);
                        $("#itemsTable").append(response);
                        calculateGrandTotalAmount();
                    }
                });
            }
        });
        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
            calculateGrandTotalAmount();
        });

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
        });

        $(".approvalTab").on("click", function() {
            let soId = ($(this).attr("id")).split("_")[1];

            if (confirm("Are you sure?")) {
                $.ajax({
                    type: "GET",
                    url: `ajaxs/so/ajax-items-list.php`,
                    data: {
                        act: "approvalTab",
                        soId
                    },
                    beforeSend: function() {
                        $(".approvalTab").html(`<option value="">Processing...</option>`);
                    },
                    success: function(response) {
                        console.log(response);
                        if (response === 'success') {
                            window.location.href = "";
                        } else {
                            $(".approvalTab").html(response);
                        }
                    }
                });
            }
        })

        // 🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴🌴
        // auto calculation 
        function calculateGrandTotalAmount() {
            let totalAmount = 0;
            let totalTaxAmount = 0;
            let totalDiscountAmount = 0;
            $(".itemTotalPrice").each(function() {
                totalAmount += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            $(".itemTotalTax").each(function() {
                totalTaxAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
            });
            $(".itemTotalDiscount").each(function() {
                totalDiscountAmount += (parseFloat($(this).html()) > 0) ? parseFloat($(this).html()) : 0;
            });
            console.log("Grand = ", totalAmount, totalTaxAmount, totalDiscountAmount);
            let grandSubTotalAmt = totalAmount - totalTaxAmount - totalDiscountAmount;
            $("#grandSubTotalAmt").html(grandSubTotalAmt.toFixed(2));
            $("#grandTotalDiscount").html(totalDiscountAmount.toFixed(2));
            $("#grandTaxAmt").html(totalTaxAmount.toFixed(2));
            $("#grandTotalAmt").html(totalAmount.toFixed(2));
        }

        function calculateOneItemAmounts(rowNo) {
            let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;
            let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;
            let itemTax = (parseFloat($(`#itemTax_${rowNo}`).val())) ? parseFloat($(`#itemTax_${rowNo}`).val()) : 0;

            $(`#multiQuantity_${rowNo}`).val(itemQty);

            let basicPrice = itemUnitPrice * itemQty;
            let totalDiscount = basicPrice * itemDiscount / 100;
            let priceWithDiscount = basicPrice - totalDiscount;
            let totalTax = priceWithDiscount * itemTax / 100;
            let totalItemPrice = priceWithDiscount + totalTax;

            console.log(itemQty, itemUnitPrice, itemDiscount, itemTax);

            $(`#itemTotalDiscount_${rowNo}`).html(totalDiscount.toFixed(2));
            $(`#itemTotalDiscount1_${rowNo}`).val(totalDiscount.toFixed(0));
            $(`#itemTotalTax_${rowNo}`).html(totalTax.toFixed(2));
            $(`#itemTotalTax1_${rowNo}`).val(totalTax.toFixed(2));
            $(`#itemTotalPrice_${rowNo}`).val(totalItemPrice.toFixed(2));
            $(`#itemTotalPrice1_${rowNo}`).html(totalItemPrice.toFixed(2));
            $(`#mainQty_${rowNo}`).html(itemQty);
            calculateGrandTotalAmount();
        }

        // #######################################################
        function calculateQuantity(rowNo, itemId, thisVal) {
            // console.log("code", rowNo);
            let itemQty = (parseFloat($(`#itemQty_${itemId}`).val()) > 0) ? parseFloat($(`#itemQty_${itemId}`).val()) : 0;
            let totalQty = 0;
            // console.log("calculateQuantity() ========== Row:", rowNo);
            // console.log("Total qty", itemQty);
            $(".multiQuantity").each(function() {
                if ($(this).data("itemid") == itemId) {
                    totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                    // console.log('Qtys":', $(this).val());
                }
            });

            let avlQty = itemQty - totalQty;

            // console.log("Avl qty:", avlQty);

            if (avlQty < 0) {
                let totalQty = 0;
                $(`#multiQuantity_${rowNo}`).val('');
                $(".multiQuantity").each(function() {
                    if ($(this).data("itemid") == itemId) {
                        totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                        // console.log('Qtys":', $(this).val());
                    }
                });
                let avlQty = itemQty - totalQty;

                $(`#mainQtymsg_${itemId}`).show();
                $(`#mainQtymsg_${itemId}`).html("[Error! Delivery QTY should equal to order QTY.]");
                $(`#mainQty_${itemId}`).html(avlQty);
            } else {
                let totalQty = 0;
                $(".multiQuantity").each(function() {
                    if ($(this).data("itemid") == itemId) {
                        totalQty += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                        // console.log('Qtys":', $(this).val());
                    }
                });

                let avlQty = itemQty - totalQty;

                $(`#mainQtymsg_${itemId}`).hide();
                $(`#mainQty_${itemId}`).html(avlQty);
            }
            if (avlQty == 0) {
                $(`#saveClose_${itemId}`).show();
                $(`#saveCloseLoading_${itemId}`).hide();
            } else {
                $(`#saveClose_${itemId}`).hide();
                $(`#saveCloseLoading_${itemId}`).show();
                $(`#setAvlQty_${itemId}`).html(avlQty);
            }
        }

        // function itemMaxDiscount(rowNo, keyValue = 0) {
        //   let itemMaxDis = $(`#itemMaxDiscount_${rowNo}`).html();
        //   console.log('this is max discount', itemMaxDis);
        //   console.log('this is key value', keyValue);
        //   if (parseFloat(keyValue) > parseFloat(itemMaxDis)) {
        //     console.log('max discount is over');
        //     $(`#itemSpecialDiscount_${rowNo}`).text(`Special Discount`);
        //     $(`#itemSpecialDiscount_${rowNo}`).show();
        //     // $(`#specialDiscount`).show();
        //   } else {
        //     $(`#itemSpecialDiscount_${rowNo}`).hide();
        //     // $(`#specialDiscount`).hide();
        //   }
        // }

        $(document).on("keyup blur click", ".itemQty", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });

        function checkSpecialDiscount() {
            let isSpecialDiscountApplied = false;

            $(".itemDiscount").each(function() {
                let rowNum = ($(this).attr("id")).split("_")[1];
                let discountPercentage = parseFloat($(this).val());
                discountPercentage = discountPercentage > 0 ? discountPercentage : 0;
                let maxDiscountPercentage = parseFloat($(`#itemMaxDiscount_${rowNum}`).html());
                maxDiscountPercentage = maxDiscountPercentage > 0 ? maxDiscountPercentage : 0;
                if (discountPercentage > maxDiscountPercentage) {
                    isSpecialDiscountApplied = true;
                }
            });

            if (isSpecialDiscountApplied) {
                $(`#approvalStatus`).val(`12`);
                console.log('max');
            } else {
                $(`#approvalStatus`).val(`14`);
                console.log('ok');
            }
        }


        $(document).on("keyup", ".itemDiscount", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let keyValue = $(this).val();
            calculateOneItemAmounts(rowNo);
            // itemMaxDiscount(rowNo, keyValue);
            checkSpecialDiscount();
            // $(`#itemTotalDiscount1_${rowNo}`).attr('disabled', 'disabled');
        });

        // #######################################################
        $(document).on("keyup blur click change", ".multiQuantity", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let itemid = ($(this).data("itemid"));
            let thisVal = ($(this).val());
            calculateQuantity(rowNo, itemid, thisVal);
        });

        // #######################################################
        $(document).on("keyup", ".itemTotalDiscount1", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            let itemDiscountAmt = ($(this).val());

            let itemQty = (parseFloat($(`#itemQty_${rowNo}`).val()) > 0) ? parseFloat($(`#itemQty_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#itemUnitPrice_${rowNo}`).val()) > 0) ? parseFloat($(`#itemUnitPrice_${rowNo}`).val()) : 0;

            let totalAmt = itemQty * itemUnitPrice;
            let discountPercentage = itemDiscountAmt * 100 / totalAmt;

            $(`#itemDiscount_${rowNo}`).val(discountPercentage.toFixed(0));

            // let itemDiscount = (parseFloat($(`#itemDiscount_${rowNo}`).val())) ? parseFloat($(`#itemDiscount_${rowNo}`).val()) : 0;

            console.log('total', itemQty, itemUnitPrice, discountPercentage);
            calculateOneItemAmounts(rowNo);

            // $(`#itemDiscount_${rowNo}`).attr('disabled', 'disabled');
            // discountCalculate(rowNo, thisVal);
        });

        // allItemsBtn
        $("#allItemsBtn").on('click', function() {
            window.location.href = "";
        })

        // itemWiseSearch
        $("#itemWiseSearch").on('click', function() {
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-so-list.php`,
                data: {
                    act: "itemWiseSearch"
                },
                beforeSend: function() {
                    $(".tableDataBody").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $(".tableDataBody").html(response);
                }
            });
        })

        $(function() {
            $("#datepicker").datepicker({
                autoclose: true,
                todayHighlight: true
            }).datepicker('update', new Date());
        });

    });

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


    $('.hamburger').click(function() {
        $('.hamburger').toggleClass('show');
        $('#overlay').toggleClass('show');
        $('.nav-action').toggleClass('show');
    });



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
    $('#profitCenterDropDown')
        .select2()
        .on('select2:open', () => {
            // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
        });
    $('#kamDropDown')
        .select2()
        .on('select2:open', () => {
            // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
        });
</script>