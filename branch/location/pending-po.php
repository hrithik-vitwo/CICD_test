<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");

$today = date("Y-m-d");
if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"],);
}

$BranchPoObj = new BranchPo();

require_once("../../app/v1/functions/branch/func-items-controller.php");
$ItemsObj = new ItemsController();
if (isset($_POST['createData'])) {
    //console($POST);
    $addBranchPo = $BranchPoObj->addBranchPo($_POST, $branch_id, $company_id, $location_id);
    //console($addBranchPo);

    swalToast($addBranchPo["status"], $addBranchPo["message"], $_SERVER['PHP_SELF']);
}

if (isset($_POST["visit"])) {
    $newStatusObj = VisitBranches($_POST);
    redirect(BRANCH_URL);
}


//$sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE company_branch_id=".$branch_id." AND company_id=".$company_id." `vendor_status`!='deleted'";
$sql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `vendor_status`!='deleted'";
$get = queryGet($sql, true);
$datas = $get['data'];
$vendrSelect = '';
foreach ($datas as $data) {
    $vendrSelect .= '<option value="' . $data['vendor_id'] . '">' . $data['trade_name'] . '</option>';
}

// if (isset($_POST["createdata"])) {
//     $addNewObj = createDataBranches($_POST);
//     if ($addNewObj["status"] == "success") {
//         $branchId = base64_encode($addNewObj['branchId']);
//         redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
//         swalToast($addNewObj["status"], $addNewObj["message"]);
//         // console($addNewObj);
//     } else {
//         swalToast($addNewObj["status"], $addNewObj["message"]);
//     }
// }

if (isset($_POST["editdata"])) {
    $editDataObj = updateDataBranches($_POST);

    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}
if (isset($_GET["reject"])) {
    //console(($_GET["reject"]));
    ///exit();
    $po_id = $_GET["reject"];
    $update = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`= 17 WHERE `po_id` = $po_id");
    if($update['status'] == 'success'){
        swalToast('success', 'PO Rejected');
    } else {
        swalToast('warning', 'PO Rejection Failed');
    
    }

}

if (isset($_GET["approve"])) {
    //console(($_GET["approve"]));
    ///exit();
    $po_id = $_GET["approve"];
    $po = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` as po, `" . ERP_VENDOR_DETAILS . "` as vendor WHERE po.vendor_id=vendor.vendor_id  AND `po_id`=$po_id ";
    $poGet = queryGet($po);
    $status = 9;
    $update = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=$status WHERE `po_id`=$po_id";
    //console($poGet['data']);

    $updatePO = queryUpdate($update);
    $check_service = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `ref_no` = '" . $po_no . "'", true);
    foreach ($check_service['data'] as $data) {
        $s_po_id = $data['po_id'];
        $update_service = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=$status WHERE `po_id`=$s_po_id");
    }


    $encodePo_id = base64_encode($po_id);
    $ref_no = $poGet['data']['ref_no'];
    $del_date = $poGet['data']['delivery_date'];
    $total_amount = $poGet['data']['totalAmount'];
    $po_no = $poGet['data']['po_number'];
    $to = $poGet['data']['vendor_authorised_person_email'];
    $sub = 'PO approved';
    $user_name = $poGet['data']['vendor_authorised_person_name'];
    $trade_name = $poGet['data']['trade_name'];
    $gst = $poGet['data']['vendor_gstin'];
    //   $url=LOCATION_URL;
    //   $user_id=$POST['email'];
    //   $password=$adminPassword;
    $msg = '
 
                <div>
                <div><strong>Dear ' . $user_name . ',</strong>(GSTIN:' . $gst . ')</div>
                <p>
                Your Purchase Order (' . $po_no . ') has been approved.
                </p>
                <strong>
                    PO details:
                </strong>
                <div style="display:grid">
                    <span>
                        Refernce Number: ' . $ref_no . '
                    </span>
                    <span>
                       Total Amount: ' . $total_amount . '
                    </span>
                    <span>
                        Delivery Date: <strong>' . $del_date . '</strong>
                    </span>
                </div>
               
                <div style="display:grid">
                    Best regards for, <span><b>' . $trade_name . '</b></span>
                </div>
                
                <p>
                <a href="' . BASE_URL . 'branch/location/branch-po-view.php?po_id=' . $encodePo_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View PO</a>
                
                </p>
                </div>
  ';





    $emailReturn = SendMailByMySMTPmailTemplate($to, $sub, $msg, $tmpId = null);


    if ($emailReturn == true) {
        //     $status = 9;
        //    echo $update = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=$status WHERE `po_id`=$po_id";
        // exit();




        swalToast('success', 'email sent');
    } else {
        swalToast('warning', 'mail not sent');
    }
}

if (isset($_GET["close-po"])) {
    // exit();
    $po_id = $_GET['close-po'];
    $update = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=10 WHERE `po_id`=$po_id");
}

//swalToast($approve["status"], $approve["message"]);

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩



?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<?php
if (isset($_GET['open'])) {
?>

    <div class="content-wrapper is-purchase-order">
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
                                    <h3 class="card-title">Open Purchase Order</h3>
                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-sm btn-primary btnstyle m-2 float-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
                                </li>
                            </ul>
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
                                        <div class="col-lg-1 col-md-1 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-11 col-md-11 col-sm-12">
                                            <div class="row table-header-item">
                                                <div class="col-lg-11 col-md-11 col-sm-11">
                                                    <div class="filter-search">
                                                        <div class="filter-list">
                                                            <a href="manage-purchases-orders.php" class="btn "><i class="fa fa-stream mr-2 "></i>All</a>
                                                            <a href="po-items.php" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                                            <a href="pending-po.php" class="btn "><i class="fa fa-clock mr-2 "></i>Pending PO</a>
                                                            <a href="pending-po.php?open" class="btn active"><i class="fa fa-lock-open mr-2 active"></i>Open PO</a>
                                                            <a href="pending-po.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PO</a>
                                                            <a href="pending-po.php?service" class="btn "><i class="fa fa-male mr-2 "></i>Service PO</a>
                                                        </div>
                                                        <div class="dropdown filter-dropdown" id="filterDropdown">

                                                            <button type="button" class="dropbtn" id="dropBtn">
                                                                <i class="fas fa-filter po-list-icon"></i>
                                                            </button>

                                                            <div class="dropdown-content">
                                                                <a href="manage-purchases-orders.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                                                <a href="po-items.php" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                                                <a href="pending-po.php" class="btn"><i class="fa fa-clock mr-2"></i>Pending PO</a>
                                                                <a href="pending-po.php?open" class="btn"><i class="fa fa-lock-open mr-2"></i>Open PO</a>
                                                                <a href="pending-po.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PO</a>
                                                                <a href="pending-po.php?service" class="btn"><i class="fa fa-male mr-2"></i>Service PO</a>
                                                            </div>
                                                        </div>
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
                                                <div class="col-lg-1 col-md-1 col-sm-12">
                                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-sm btn-primary btnstyle m-2 relative-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
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
                                                                    <option value="6" <?php if (isset($_REQUEST['status_s']) && '6' == $_REQUEST['status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Active
                                                                    </option>
                                                                    <option value="7" <?php if (isset($_REQUEST['status_s']) && '7' == $_REQUEST['status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Inactive
                                                                    </option>
                                                                    <option value="8" <?php if (isset($_REQUEST['status_s']) && '8' == $_REQUEST['status_s']) {
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
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                    <?php
                                    $cond = '';

                                    $sts = " AND `status`!='deleted'";
                                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                        $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                    }

                                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                        $cond .= " AND delivery_date between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }


                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND (`po_number` like '%" . $_REQUEST['keyword2'] . "%' OR `po_date` like '%" . $_REQUEST['keyword2'] . "%')";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND (`po_number` like '%" . $_REQUEST['keyword'] . "%'  OR `po_date` like '%" . $_REQUEST['keyword'] . "%')";
                                        }
                                    }

                                    $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . "  AND `po_status`=9 AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id "  . $sts . "  ORDER BY po_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                    $qry_list = queryGet($sql_list, true);
                                    $num_list = $qry_list['numRows'];
                                    $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . " AND `po_status`=9  AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id " . $sts . " ";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];
                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_PURCHASE_ORDER", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>#</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>PO Number</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th>Reference Number</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th>PO Date</th>
                                                    <?php  }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <th>Vendor Name</th>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <th>Total Item</th>
                                                    <?php  }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <th>Total Amount</th>
                                                    <?php } ?>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>



                                            <tbody>
                                                <?php
                                                $poList = $qry_list['data'];

                                                foreach ($poList as $onePoList) {

                                                    $check_cur = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`='" . $onePoList['currency'] . "'");
                                                    
                                                    // console($onePoList['po_number']);
                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['po_number'] ?></td>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['ref_no'] ?></td>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td><?= formatDateORDateTime($onePoList['po_date']) ?></td>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <td><?= $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0]['trade_name'] ?></td>
                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['totalItems'] ?></td>
                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td>  <?php echo $check_cur['data']['currency_name'];
                                                                echo $onePoList['totalAmount'] * $onePoList['conversion_rate'] ; ?> </td>
                                                        <?php } ?>
                                                        <td>
                                                            <?php
                                                            if ($onePoList['po_status'] == 14) {
                                                                echo "Pending";
                                                            } else if ($onePoList['po_status'] == 9) {
                                                                echo "open";
                                                            } else if ($onePoList['po_status'] == 10) {
                                                                echo "closed";
                                                            } else {
                                                            }
                                                            ?>


                                                        </td>
                                                        <td>
                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                        </td>

                                                    </tr>




                                                    <!-- right modal start here  -->

                                                    <div class="modal fade right customer-modal pending-po-open-modal" id="fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                            <!--Content-->
                                                            <div class="modal-content">
                                                                <!--Header-->
                                                                <div class="modal-header">

                                                                    <div class="customer-head-info">
                                                                        <div class="customer-name-code">
                                                                            <h2>  <?php echo $check_cur['data']['currency_name'];
                                                                echo $onePoList['totalAmount']  * $onePoList['conversion_rate'] ; ?></h2>
                                                                            <p class="heading lead"><?= $onePoList['po_number'] ?></p>
                                                                            <p>REF :&nbsp;<?= $onePoList['ref_no'] ?></p>
                                                                        </div>
                                                                        <?php
                                                                        $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                        ?>
                                                                        <div class="customer-image">
                                                                            <div class="name-item-count">
                                                                                <h5><?= $vendorDetails['trade_name'] ?></h5>
                                                                                <span>
                                                                                    <div class="round-item-count"><?= $onePoList['totalItems'] ?></div> Items
                                                                                </span>
                                                                            </div>
                                                                            <i class="fa fa-user"></i>
                                                                        </div>
                                                                    </div>

                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                        <li class="nav-item">
                                                                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $onePoList['po_number'] ?>" role="tab" aria-controls="home" aria-selected="true">Info</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= $onePoList['po_number'] ?>" role="tab" aria-controls="profile" aria-selected="false">Vendor Details</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link" id="" data-toggle="" href="pending-po.php?close-po=<?= $onePoList['po_id'] ?>"> Close PO</a>
                                                                        </li>
                                                                        <?php
                                                                        // if ($onePoList['use_type'] == "service" || $onePoList['use_type'] == "servicep") {
                                                                        ?>
                                                                            <!-- <li class="nav-item">
                                                                                <a class="nav-link" id="" data-toggle="" href="manage-manual-grn.php?view=<?= $onePoList['po_number'] ?>&type=srn"> SRN</a>
                                                                            </li> -->
                                                                        <?php
                                                                        // } else {
                                                                        ?>
                                                                            <li class="nav-item">
                                                                                <a class="nav-link" id="" data-toggle="" href="manage-manual-grn.php?view=<?= $onePoList['po_number'] ?>&type=grn"> GRN</a>
                                                                            </li>
                                                                        <?php
                                                                        // }
                                                                        ?>
                                                                        <!-- -------------------Audit History Button Start------------------------- -->
                                                                        <li class="nav-item">
                                                                            <a class="nav-link auditTrail" id="history-tab<?= $onePoList['po_number'] ?>" data-toggle="tab" data-ccode="<?= $onePoList['po_number'] ?>" href="#history<?= $onePoList['po_number'] ?>" role="tab" aria-controls="history<?= $onePoList['po_number'] ?>" aria-selected="false"><i class="fa fa-history mr-2"></i>Trail</a>
                                                                        </li>
                                                                        <!-- -------------------Audit History Button End------------------------- -->
                                                                    </ul>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="tab-content" id="myTabContent">
                                                                        <div class="tab-pane fade show active" id="home<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                            <?php
                                                                            $itemDetails = $BranchPoObj->fetchBranchPoItems($onePoList['po_id'])['data'];
                                                                            foreach ($itemDetails as $oneItem) {
                                                                            ?>
                                                                                <form action="" method="POST">

                                                                                    <div class="hamburger">
                                                                                        <div class="wrapper-action">
                                                                                            <i class="fa fa-cog fa-2x"></i>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="nav-action" id="thumb">
                                                                                        <a title="Notify Me" href="" name="vendorEditBtn">
                                                                                            <i class="fa fa-bell"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="nav-action" id="create">
                                                                                        <a title="Edit" href="" name="vendorEditBtn">
                                                                                            <i class="fa fa-edit"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="nav-action trash" id="share">
                                                                                        <a title="Delete" href="" name="vendorEditBtn">
                                                                                            <i class="fa fa-trash"></i>
                                                                                        </a>
                                                                                    </div>

                                                                                </form>


                                                                                <div class="item-detail-section">
                                                                                    <h6>Items Details</h6>

                                                                                    <div class="card">
                                                                                        <div class="card-body">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                                    <div class="left-section">
                                                                                                        <div class="icon-img">
                                                                                                            <i class="fa fa-box"></i>
                                                                                                        </div>
                                                                                                        <div class="code-des">
                                                                                                            <h4><?= $oneItem['itemCode'] ?></h4>
                                                                                                            <p><?= $oneItem['itemName'] ?></p>
                                                                                                            <p>  <?php echo $check_cur['data']['currency_name'];
                                                                                                                                    echo $oneItem['total_price']  * $onePoList['conversion_rate'] ; ?></p>
                                                                                                            <p>
                                                                                                                <h10>Quantity- <?= $oneItem['qty'] . "  " . $oneItem['uom'] ?></h10>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <h10>Remaining Quantity- <?php if ($oneItem['remainingQty'] != "") {
                                                                                                                                                echo $oneItem['remainingQty'] . "  " . $oneItem['uom'];
                                                                                                                                            } else {
                                                                                                                                                echo 0 . "  " . $oneItem['uom'];
                                                                                                                                            }
                                                                                                                                            ?></h10>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <h10>Total Price-  <?php echo $check_cur['data']['currency_name'];
                                                                                                                                    echo $oneItem['total_price']  * $onePoList['conversion_rate'] ; ?></h10>
                                                                                                            </p>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <hr>
                                                                                            <?php
                                                                                            $deliverySchedule = $BranchPoObj->fetchBranchPoItemsDeliverySchedule($oneItem['po_item_id'])['data'];
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
                                                                                                                    <?= $dSchedule['qty'] ?> <?= $oneItem['uom'] ?>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                    </div>

                                                                                </div>
                                                                            <?php } ?>
                                                                            <!-- <a href="pending-po.php?approve=<?= $onePoList['po_id'] ?>" class="btn btn-primary">Approve PO</a> -->
                                                                        </div>



                                                                        <div class="tab-pane fade" id="profile<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="accordion accordion-flush customer-details-sells-order" id="accordionFlushCustDetails">
                                                                                        <div class="accordion-item customer-details">
                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnePo" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                    <span>Vendor Details</span>
                                                                                                </button>
                                                                                            </h2>
                                                                                            <div id="flush-collapseOnePo" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                <div class="accordion-body cust-detsils-body">

                                                                                                    <div class="card">
                                                                                                        <div class="card-body">
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <?php
                                                                                                                    $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                                                                    ?>
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-hashtag"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>Vendor Code</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['vendor_code'] ?>
                                                                                                                    </p>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <hr>
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-user"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>Vendor Name</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['trade_name'] ?>
                                                                                                                    </p>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <hr>
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-file"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>GST</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['vendor_gstin'] ?>
                                                                                                                    </p>
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
                                                                        </div>


                                                                        <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                        <div class="tab-pane fade" id="history<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                            <div class="audit-head-section mb-3 mt-3 ">
                                                                                <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($onePoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['created_at']) ?></p>
                                                                                <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($onePoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['updated_at']) ?></p>
                                                                            </div>
                                                                            <hr>
                                                                            <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $onePoList['po_number'] ?>">

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
                                                        <!--/.Content-->
                                                    </div>
                                </div>
                            <?php }
                                                //  console($onePoList['po_number']); 
                            ?>
                            <!-- right modal end here  -->

                            </tbody>
                            <tfoot>
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

                                <!-- For Pegination------->
                                <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
                                    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                                                    echo  $_REQUEST['pageNo'];
                                                                                } ?>">
                                </form>
                                <!-- End Pegination from------->
                            </tfoot>
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
                                        <input type="hidden" name="pageTableName" value="ERP_BRANCH_PURCHASE_ORDER" />
                                        <div class="modal-body">
                                            <div id="dropdownframe"></div>
                                            <div id="main2">
                                                <table>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                            PO Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                            Reference Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                            PO Date</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                                            Vendor Name</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                                            Total Items</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                                            Total Amount</td>
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
    <!-- End Pegination from------->



<?php
} elseif (isset($_GET['service'])) {
?>

    <div class="content-wrapper is-purchase-order">
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
                                    <h3 class="card-title">Open Purchase Order</h3>
                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-sm btn-primary btnstyle m-2 float-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
                                </li>
                            </ul>
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
                                        <div class="col-lg-1 col-md-1 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-11 col-md-11 col-sm-12">
                                            <div class="row table-header-item">
                                                <div class="col-lg-11 col-md-11 col-sm-11">
                                                    <div class="filter-search">
                                                        <div class="filter-list">
                                                            <a href="manage-purchases-orders.php" class="btn "><i class="fa fa-stream mr-2 "></i>All</a>
                                                            <a href="po-items.php" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                                            <a href="pending-po.php" class="btn "><i class="fa fa-clock mr-2 "></i>Pending PO</a>
                                                            <a href="pending-po.php?open" class="btn "><i class="fa fa-lock-open mr-2 "></i>Open PO</a>
                                                            <a href="pending-po.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PO</a>
                                                            <a href="pending-po.php?service" class="btn active"><i class="fa fa-male mr-2 active"></i>Service PO</a>
                                                        </div>
                                                        <div class="dropdown filter-dropdown" id="filterDropdown">

                                                            <button type="button" class="dropbtn" id="dropBtn">
                                                                <i class="fas fa-filter po-list-icon"></i>
                                                            </button>

                                                            <div class="dropdown-content">
                                                                <a href="manage-purchases-orders.php" class="btn "><i class="fa fa-stream mr-2 "></i>All</a>
                                                                <a href="po-items.php" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                                                <a href="pending-po.php" class="btn "><i class="fa fa-clock mr-2 "></i>Pending PO</a>
                                                                <a href="pending-po.php?open" class="btn "><i class="fa fa-lock-open mr-2 "></i>Open PO</a>
                                                                <a href="pending-po.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PO</a>
                                                                <a href="pending-po.php?service" class="btn active"><i class="fa fa-male mr-2 active"></i>Service PO</a>
                                                            </div>
                                                        </div>
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
                                                <div class="col-lg-1 col-md-1 col-sm-12">
                                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-sm btn-primary btnstyle m-2 relative-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
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
                                                                    <option value="6" <?php if (isset($_REQUEST['status_s']) && '6' == $_REQUEST['status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Active
                                                                    </option>
                                                                    <option value="7" <?php if (isset($_REQUEST['status_s']) && '7' == $_REQUEST['status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Inactive
                                                                    </option>
                                                                    <option value="8" <?php if (isset($_REQUEST['status_s']) && '8' == $_REQUEST['status_s']) {
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
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                    <?php
                                    $cond = '';

                                    $sts = " AND `status`!='deleted'";
                                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                        $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                    }

                                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                        $cond .= " AND delivery_date between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }


                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND (`po_number` like '%" . $_REQUEST['keyword2'] . "%' OR `po_date` like '%" . $_REQUEST['keyword2'] . "%')";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND (`po_number` like '%" . $_REQUEST['keyword'] . "%'  OR `po_date` like '%" . $_REQUEST['keyword'] . "%')";
                                        }
                                    }

                                    $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . "  AND  `service_po`='yes'  AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id "  . $sts . "  ORDER BY po_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                    //console($sql_list);

                                    $qry_list = queryGet($sql_list, true);
                                    $num_list = $qry_list['numRows'];
                                    $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . " AND  `service_po`='yes'  AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id " . $sts . " ";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];
                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_PURCHASE_ORDER", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>#</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>PO Number</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th>Reference Number</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th>PO Date</th>
                                                    <?php  }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <th>Vendor Name</th>
                                                    <?php }

                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <th>Service Type</th>
                                                    <?php }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <th>Amount</th>
                                                    <?php  }
                                                    if (in_array(7, $settingsCheckbox)) { ?>
                                                        <th>Service GST</th>
                                                    <?php }
                                                    if (in_array(8, $settingsCheckbox)) { ?>
                                                        <th>Total Amount</th>
                                                    <?php  }
                                                    if (in_array(9, $settingsCheckbox)) { ?>
                                                        <th>Service Description</th>
                                                    <?php } ?>
                                                    <!-- <th>Status</th>
                                                       <th>Action</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $poList = $qry_list['data'];

                                                foreach ($poList as $onePoList) {
                                                    // console($onePoList);
                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['po_number'] ?></td>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['ref_no'] ?></td>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td><?= formatDateORDateTime($onePoList['po_date']) ?></td>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <td><?php
                                                                if ($onePoList['vendor_id'] != "") {
                                                                    echo $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0]['trade_name'];
                                                                } else {
                                                                    if ($BranchPoObj->fetchVendorDetails($onePoList['service_name'])['data'][0]['trade_name'] != "") {
                                                                        echo $BranchPoObj->fetchVendorDetails($onePoList['service_name'])['data'][0]['trade_name'];
                                                                    } else {
                                                                        echo $onePoList['service_name'];
                                                                    }
                                                                }
                                                                ?></td>
                                                        <?php }

                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td><?php if ($onePoList['parent_id'] != null) {
                                                                    echo $onePoList['service_type'];
                                                                    echo "  (Associate PO)";
                                                                } else {
                                                                    echo "Direct Service PO";
                                                                } ?></td>
                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td><?php if ($onePoList['parent_id'] != null) {
                                                                    echo $onePoList['service_amount'];
                                                                } else { echo $check_cur['data']['currency_name'];
                                                                    echo $onePoList['totalAmount'];
                                                                } ?></td>
                                                        <?php }
                                                        if (in_array(7, $settingsCheckbox)) { ?>
                                                            <td><?php if ($onePoList['parent_id'] != null) {
                                                                    echo  $onePoList['service_gst'];
                                                                } else {
                                                                    echo "N/A";
                                                                } ?></td>
                                                        <?php }
                                                        if (in_array(8, $settingsCheckbox)) { ?>
                                                            <td><?php if ($onePoList['parent_id'] != null) {
                                                                    echo $onePoList['service_total'];
                                                                } else {
                                                                    echo $onePoList['totalAmount'];
                                                                } ?></td>
                                                        <?php }
                                                        if (in_array(9, $settingsCheckbox)) { ?>
                                                            <td><?php if ($onePoList['parent_id'] != null) {
                                                                    echo  $onePoList['service_description'];
                                                                } else {
                                                                    echo "N/A";
                                                                } ?></td>

                                                        <?php } ?>
                                                        <!-- <td> 
                                                                  <?php
                                                                    if ($onePoList['po_status'] == 14) {
                                                                        echo "Pending";
                                                                    } else if ($onePoList['po_status'] == 9) {
                                                                        echo "open";
                                                                    } else if ($onePoList['po_status'] == 10) {
                                                                        echo "closed";
                                                                    } else {
                                                                    }
                                                                    ?>
   
   
                                                               </td>
                                                           <td>
                                                               <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                           </td> -->

                                                    </tr>




                                                    <!-- right modal start here  -->

                                                    <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                            <!--Content-->
                                                            <div class="modal-content">
                                                                <!--Header-->
                                                                <div class="modal-header">

                                                                    <div class="customer-head-info">
                                                                        <div class="customer-name-code">
                                                                            <h2><?php echo $check_cur['data']['currency_name']; echo $onePoList['totalAmount'] ?></h2>
                                                                            <p class="heading lead"><?= $onePoList['po_number']; ?></p>
                                                                            <p>REF :&nbsp;<?= $onePoList['ref_no'] ?></p>
                                                                        </div>
                                                                        <?php
                                                                        $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                        ?>
                                                                        <div class="customer-image">
                                                                            <div class="name-item-count">
                                                                                <h5><?= $vendorDetails['trade_name'] ?></h5>
                                                                                <span>
                                                                                    <div class="round-item-count"><?= $onePoList['totalItems'] ?></div> Items
                                                                                </span>
                                                                            </div>
                                                                            <i class="fa fa-user"></i>
                                                                        </div>
                                                                    </div>

                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                        <li class="nav-item">
                                                                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $onePoList['po_number'] ?>" role="tab" aria-controls="home" aria-selected="true">Info</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= $onePoList['po_number'] ?>" role="tab" aria-controls="profile" aria-selected="false">Vendor Details</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link" id="" data-toggle="" href="pending-po.php?close-po=<?= $onePoList['po_id'] ?>"> Close PO</a>
                                                                        </li>
                                                                        <!-- -------------------Audit History Button Start------------------------- -->
                                                                        <li class="nav-item">
                                                                            <a class="nav-link auditTrail" id="history-tab<?= $onePoList['po_number'] ?>" data-toggle="tab" data-ccode="<?= $onePoList['po_number'] ?>" href="#history<?= $onePoList['po_number'] ?>" role="tab" aria-controls="history<?= $onePoList['po_number'] ?>" aria-selected="false"><i class="fa fa-history mr-2"></i>Trail</a>
                                                                        </li>
                                                                        <!-- -------------------Audit History Button End------------------------- -->
                                                                    </ul>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="tab-content" id="myTabContent">
                                                                        <div class="tab-pane fade show active" id="home<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                            <?php
                                                                            $itemDetails = $BranchPoObj->fetchBranchPoItems($onePoList['po_id'])['data'];
                                                                            foreach ($itemDetails as $oneItem) {
                                                                            ?>
                                                                                <form action="" method="POST">

                                                                                    <div class="hamburger">
                                                                                        <div class="wrapper-action">
                                                                                            <i class="fa fa-cog fa-2x"></i>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="nav-action" id="thumb">
                                                                                        <a title="Notify Me" href="" name="vendorEditBtn">
                                                                                            <i class="fa fa-bell"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="nav-action" id="create">
                                                                                        <a title="Edit" href="" name="vendorEditBtn">
                                                                                            <i class="fa fa-edit"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="nav-action trash" id="share">
                                                                                        <a title="Delete" href="" name="vendorEditBtn">
                                                                                            <i class="fa fa-trash"></i>
                                                                                        </a>
                                                                                    </div>

                                                                                </form>


                                                                                <div class="item-detail-section">
                                                                                    <h6>Items Details</h6>

                                                                                    <div class="card">
                                                                                        <div class="card-body">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                                    <div class="left-section">
                                                                                                        <div class="icon-img">
                                                                                                            <i class="fa fa-box"></i>
                                                                                                        </div>
                                                                                                        <div class="code-des">
                                                                                                            <h4><?= $oneItem['itemCode'] ?></h4>
                                                                                                            <p><?= $oneItem['itemName'] ?></p>
                                                                                                            <p><?= $oneItem['unitPrice'] ?></p>
                                                                                                            <p>
                                                                                                                <h10>Quantity- <?= $oneItem['qty'] . "  " . $oneItem['uom'] ?></h10>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <h10>Remaining Quantity- <?php if ($oneItem['remainingQty'] != "") {
                                                                                                                                                echo $oneItem['remainingQty'] . "  " . $oneItem['uom'];
                                                                                                                                            } else {
                                                                                                                                                echo 0 . "  " . $oneItem['uom'];
                                                                                                                                            }
                                                                                                                                            ?></h10>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <h10>Total Price- <?php echo $check_cur['data']['currency_name']; echo $oneItem['total_price'] ; ?></h10>
                                                                                                            </p>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <hr>
                                                                                            <?php
                                                                                            $deliverySchedule = $BranchPoObj->fetchBranchPoItemsDeliverySchedule($oneItem['po_item_id'])['data'];
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
                                                                                                                    <?= $dSchedule['qty'] ?> <?= $oneItem['uom'] ?>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                    </div>

                                                                                </div>
                                                                            <?php } ?>
                                                                            <!-- <a href="pending-po.php?approve=<?= $onePoList['po_id'] ?>" class="btn btn-primary">Approve PO</a> -->
                                                                        </div>



                                                                        <div class="tab-pane fade" id="profile<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="accordion accordion-flush customer-details-sells-order" id="accordionFlushCustDetails">
                                                                                        <div class="accordion-item customer-details">
                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnePo" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                    <span>Vendor Details</span>
                                                                                                </button>
                                                                                            </h2>
                                                                                            <div id="flush-collapseOnePo" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                <div class="accordion-body cust-detsils-body">

                                                                                                    <div class="card">
                                                                                                        <div class="card-body">
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <?php
                                                                                                                    $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                                                                    ?>
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-hashtag"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>Vendor Code</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['vendor_code'] ?>
                                                                                                                    </p>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <hr>
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-user"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>Vendor Name</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['trade_name'] ?>
                                                                                                                    </p>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <hr>
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-file"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>GST</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['vendor_gstin'] ?>
                                                                                                                    </p>
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
                                                                        </div>

                                                                        <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                        <div class="tab-pane fade" id="history<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                            <div class="audit-head-section mb-3 mt-3 ">
                                                                                <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($onePoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['created_at']) ?></p>
                                                                                <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($onePoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['updated_at']) ?></p>
                                                                            </div>
                                                                            <hr>
                                                                            <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $onePoList['po_number'] ?>">

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
                                                        <!--/.Content-->
                                                    </div>
                                </div>
                            <?php }
                                                //  console($onePoList['po_number']); 
                            ?>
                            <!-- right modal end here  -->

                            </tbody>
                            <tfoot>
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

                                <!-- For Pegination------->
                                <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
                                    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                                                    echo  $_REQUEST['pageNo'];
                                                                                } ?>">
                                </form>
                                <!-- End Pegination from------->
                            </tfoot>
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
                                        <input type="hidden" name="pageTableName" value="ERP_BRANCH_PURCHASE_ORDER" />
                                        <div class="modal-body">
                                            <div id="dropdownframe"></div>
                                            <div id="main2">
                                                <table>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                            PO Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                            Reference Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                            PO Date</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                                            Vendor Name</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                                            Service Type</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                                            Amount</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="7" />
                                                            GST</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="8" />
                                                            Total Amount</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="9" />
                                                            Service Description</td>
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
    <!-- End Pegination from------->



<?php
} else if (isset($_GET['closed'])) {
?>

    <div class="content-wrapper is-purchase-order">
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
                                    <h3 class="card-title">Closed Purchase Order</h3>
                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-sm btn-primary btnstyle m-2 float-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
                                </li>
                            </ul>
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
                                        <div class="col-lg-1 col-md-1 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-11 col-md-11 col-sm-12">
                                            <div class="row table-header-item">
                                                <div class="col-lg-11 col-md-11 col-sm-11">
                                                    <div class="filter-search">
                                                        <div class="filter-list">
                                                            <a href="manage-purchases-orders.php" class="btn "><i class="fa fa-stream mr-2 "></i>All</a>
                                                            <a href="po-items.php" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                                            <a href="pending-po.php" class="btn "><i class="fa fa-clock mr-2 "></i>Pending PO</a>
                                                            <a href="pending-po.php?open" class="btn "><i class="fa fa-lock-open mr-2 "></i>Open PO</a>
                                                            <a href="pending-po.php?closed" class="btn active"><i class="fa fa-lock mr-2 active"></i>Closed PO</a>
                                                            <a href="pending-po.php?service" class="btn "><i class="fa fa-male mr-2 "></i>Service PO</a>
                                                        </div>
                                                        <div class="dropdown filter-dropdown" id="filterDropdown">

                                                            <button type="button" class="dropbtn" id="dropBtn">
                                                                <i class="fas fa-filter po-list-icon"></i>
                                                            </button>

                                                            <div class="dropdown-content">
                                                                <a href="manage-purchases-orders.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                                                <a href="po-items.php" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                                                <a href="pending-po.php" class="btn"><i class="fa fa-clock mr-2"></i>Pending PO</a>
                                                                <a href="pending-po.php?open" class="btn"><i class="fa fa-lock-open mr-2"></i>Open PO</a>
                                                                <a href="pending-po.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PO</a>
                                                                <a href="pending-po.php?service" class="btn"><i class="fa fa-male mr-2"></i>Service PO</a>
                                                            </div>
                                                        </div>
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
                                                <div class="col-lg-1 col-md-1 col-sm-12">
                                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-sm btn-primary btnstyle m-2 relative-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
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
                                                                    <option value="6" <?php if (isset($_REQUEST['status_s']) && '6' == $_REQUEST['status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Active
                                                                    </option>
                                                                    <option value="7" <?php if (isset($_REQUEST['status_s']) && '7' == $_REQUEST['status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Inactive
                                                                    </option>
                                                                    <option value="8" <?php if (isset($_REQUEST['status_s']) && '8' == $_REQUEST['status_s']) {
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
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                    <?php
                                    $cond = '';

                                    $sts = " AND `status`!='deleted'";
                                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                        $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                    }

                                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                        $cond .= " AND delivery_date between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }


                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND (`po_number` like '%" . $_REQUEST['keyword2'] . "%' OR `po_date` like '%" . $_REQUEST['keyword2'] . "%')";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND (`po_number` like '%" . $_REQUEST['keyword'] . "%'  OR `po_date` like '%" . $_REQUEST['keyword'] . "%')";
                                        }
                                    }

                                    $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . "  AND `po_status`=10  AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id "  . $sts . "  ORDER BY po_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                    $qry_list = queryGet($sql_list, true);
                                    $num_list = $qry_list['numRows'];
                                    $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . " AND `po_status`=10 AND  AND `service_po`='no' `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id " . $sts . " ";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];
                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_PURCHASE_ORDER", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>#</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>PO Number</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th>Reference Number</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th>PO Date</th>
                                                    <?php  }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <th>Vendor Name</th>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <th>Total Items</th>
                                                    <?php  }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <th>Total Amount</th>
                                                    <?php } ?>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>



                                            <tbody>
                                                <?php
                                                $poList = $qry_list['data'];

                                                foreach ($poList as $onePoList) {
                                                    $check_cur = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`='" . $onePoList['currency'] . "'");
                                                    // console($onePoList['po_number']);
                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['po_number'] ?></td>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['ref_no'] ?></td>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td><?= formatDateORDateTime($onePoList['po_date']) ?></td>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <td><?= $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0]['trade_name'] ?></td>
                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['totalItems'] ?></td>
                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td><?php echo $check_cur['data']['currency_name']; echo $onePoList['totalAmount']  * $onePoList['conversion_rate']; ?></td>
                                                        <?php } ?>
                                                        <td>
                                                            <?php
                                                            if ($onePoList['po_status'] == 14) {
                                                                echo "Pending";
                                                            } else if ($onePoList['po_status'] == 9) {
                                                                echo "open";
                                                            } else if ($onePoList['po_status'] == 10) {
                                                                echo "closed";
                                                            } else {
                                                            }
                                                            ?>


                                                        </td>
                                                        <td>
                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                        </td>
                                                    </tr>




                                                    <!-- right modal start here  -->

                                                    <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                            <!--Content-->
                                                            <div class="modal-content">
                                                                <!--Header-->
                                                                <div class="modal-header">

                                                                    <div class="customer-head-info">
                                                                        <div class="customer-name-code">
                                                                            <h2><?php echo $check_cur['data']['currency_name']; echo $onePoList['totalAmount']  * $onePoList['conversion_rate']; ?></h2>
                                                                            <p class="heading lead"><?= $onePoList['po_number'] ?></p>
                                                                            <p>REF :&nbsp;<?= $onePoList['ref_no'] ?></p>
                                                                        </div>
                                                                        <?php
                                                                        $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                        ?>
                                                                        <div class="customer-image">
                                                                            <div class="name-item-count">
                                                                                <h5><?= $vendorDetails['trade_name'] ?></h5>
                                                                                <span>
                                                                                    <div class="round-item-count"><?= $onePoList['totalItems'] ?></div> Items
                                                                                </span>
                                                                            </div>
                                                                            <i class="fa fa-user"></i>
                                                                        </div>
                                                                    </div>

                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                        <li class="nav-item">
                                                                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $onePoList['po_number'] ?>" role="tab" aria-controls="home" aria-selected="true">Info</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= $onePoList['po_number'] ?>" role="tab" aria-controls="profile" aria-selected="false">Vendor Details</a>
                                                                        </li>
                                                                        <!-- -------------------Audit History Button Start------------------------- -->
                                                                        <li class="nav-item">
                                                                            <a class="nav-link auditTrail" id="history-tab<?= $onePoList['po_number'] ?>" data-toggle="tab" data-ccode="<?= $onePoList['po_number'] ?>" href="#history<?= $onePoList['po_number'] ?>" role="tab" aria-controls="history<?= $onePoList['po_number'] ?>" aria-selected="false"><i class="fa fa-history mr-2"></i>Trail</a>
                                                                        </li>
                                                                        <!-- -------------------Audit History Button End------------------------- -->
                                                                    </ul>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="tab-content" id="myTabContent">
                                                                        <div class="tab-pane fade show active" id="home<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                            <?php
                                                                            $itemDetails = $BranchPoObj->fetchBranchPoItems($onePoList['po_id'])['data'];
                                                                            foreach ($itemDetails as $oneItem) {
                                                                            ?>
                                                                                <form action="" method="POST">

                                                                                    <div class="hamburger">
                                                                                        <div class="wrapper-action">
                                                                                            <i class="fa fa-cog fa-2x"></i>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="nav-action" id="thumb">
                                                                                        <a title="Notify Me" href="" name="vendorEditBtn">
                                                                                            <i class="fa fa-bell"></i>
                                                                                        </a>
                                                                                    </div>

                                                                                    <div class="nav-action trash" id="share">
                                                                                        <a title="Delete" href="" name="vendorEditBtn">
                                                                                            <i class="fa fa-trash"></i>
                                                                                        </a>
                                                                                    </div>

                                                                                </form>


                                                                                <div class="item-detail-section">
                                                                                    <h6>Items Details</h6>

                                                                                    <div class="card">
                                                                                        <div class="card-body">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                                    <div class="left-section">
                                                                                                        <div class="icon-img">
                                                                                                            <i class="fa fa-box"></i>
                                                                                                        </div>
                                                                                                        <div class="code-des">
                                                                                                            <h4><?= $oneItem['itemCode'] ?></h4>
                                                                                                            <p><?= $oneItem['itemName'] ?></p>
                                                                                                            <p><?php echo $check_cur['data']['currency_name']; echo $oneItem['unitPrice']  * $onePoList['conversion_rate']; ?></p>
                                                                                                            <p>
                                                                                                                <h10>Quantity- <?= $oneItem['qty'] . "  " . $oneItem['uom'] ?></h10>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <h10>Remaining Quantity- <?php if ($oneItem['remainingQty'] != "") {
                                                                                                                                                echo $oneItem['remainingQty'] . "  " . $oneItem['uom'];
                                                                                                                                            } else {
                                                                                                                                                echo 0 . "  " . $oneItem['uom'];
                                                                                                                                            }
                                                                                                                                            ?></h10>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <h10>Total Price- <?php echo $check_cur['data']['currency_name']; echo $oneItem['total_price']  * $onePoList['conversion_rate'];?></h10>
                                                                                                            </p>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <hr>
                                                                                            <?php
                                                                                            $deliverySchedule = $BranchPoObj->fetchBranchPoItemsDeliverySchedule($oneItem['po_item_id'])['data'];
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
                                                                                                                    <?= $dSchedule['qty'] ?> <?= $oneItem['uom'] ?>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                    </div>

                                                                                </div>
                                                                            <?php } ?>
                                                                            <!-- <a href="pending-po.php?approve=<?= $onePoList['po_id'] ?>" class="btn btn-primary">Approve PO</a> -->
                                                                        </div>



                                                                        <div class="tab-pane fade" id="profile<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="accordion accordion-flush customer-details-sells-order" id="accordionFlushCustDetails">
                                                                                        <div class="accordion-item customer-details">
                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnePo" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                    <span>Vendor Details</span>
                                                                                                </button>
                                                                                            </h2>
                                                                                            <div id="flush-collapseOnePo" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                <div class="accordion-body cust-detsils-body">

                                                                                                    <div class="card">
                                                                                                        <div class="card-body">
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <?php
                                                                                                                    $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                                                                    ?>
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-hashtag"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>Vendor Code</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['vendor_code'] ?>
                                                                                                                    </p>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <hr>
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-user"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>Vendor Name</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['trade_name'] ?>
                                                                                                                    </p>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <hr>
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-file"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>GST</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['vendor_gstin'] ?>
                                                                                                                    </p>
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
                                                                        </div>


                                                                        <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                        <div class="tab-pane fade" id="history<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                            <div class="audit-head-section mb-3 mt-3 ">
                                                                                <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($onePoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['created_at']) ?></p>
                                                                                <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($onePoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['updated_at']) ?></p>
                                                                            </div>
                                                                            <hr>
                                                                            <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $onePoList['po_number'] ?>">

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
                                                        <!--/.Content-->
                                                    </div>
                                </div>
                            <?php }
                                                //  console($onePoList['po_number']); 
                            ?>
                            <!-- right modal end here  -->

                            </tbody>
                            <tfoot>
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

                                <!-- For Pegination------->
                                <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
                                    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                                                    echo  $_REQUEST['pageNo'];
                                                                                } ?>">
                                </form>
                                <!-- End Pegination from------->
                            </tfoot>
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
                                        <input type="hidden" name="pageTableName" value="ERP_BRANCH_PURCHASE_ORDER" />
                                        <div class="modal-body">
                                            <div id="dropdownframe"></div>
                                            <div id="main2">
                                                <table>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                            PO Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                            Reference Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                            PO Date</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                                            Vendor Name</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                                            Total Items</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                                            Total Amount</td>
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
    <!-- End Pegination from------->


<?php
} else {
?>

    <div class="content-wrapper is-purchase-order">
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
                                    <h3 class="card-title">Pending Purchase Order</h3>
                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-sm btn-primary btnstyle m-2 float-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
                                </li>
                            </ul>
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
                                        <div class="col-lg-1 col-md-1 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-11 col-md-11 col-sm-12">
                                            <div class="row table-header-item">
                                                <div class="col-lg-11 col-md-11 col-sm-12">
                                                    <div class="filter-search">
                                                        <div class="filter-list">
                                                            <a href="manage-purchases-orders.php" class="btn "><i class="fa fa-stream mr-2 "></i>All</a>
                                                            <a href="po-items.php" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                                            <a href="pending-po.php" class="btn active"><i class="fa fa-clock mr-2 active"></i>Pending PO</a>
                                                            <a href="pending-po.php?open" class="btn"><i class="fa fa-lock-open mr-2"></i>Open PO</a>
                                                            <a href="pending-po.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PO</a>
                                                            <a href="pending-po.php?service" class="btn"><i class="fa fa-male mr-2"></i>Service PO</a>
                                                        </div>
                                                        <div class="dropdown filter-dropdown" id="filterDropdown">

                                                            <button type="button" class="dropbtn" id="dropBtn">
                                                                <i class="fas fa-filter po-list-icon"></i>
                                                            </button>

                                                            <div class="dropdown-content">
                                                                <a href="manage-purchases-orders.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                                                <a href="po-items.php" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                                                <a href="pending-po.php" class="btn"><i class="fa fa-clock mr-2"></i>Pending PO</a>
                                                                <a href="pending-po.php?open" class="btn"><i class="fa fa-lock-open mr-2"></i>Open PO</a>
                                                                <a href="pending-po.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PO</a>
                                                                <a href="pending-po.php?service" class="btn"><i class="fa fa-male mr-2"></i>Service PO</a>
                                                            </div>
                                                        </div>
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
                                                <div class="col-lg-1 col-md-1 col-sm-12">
                                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-sm btn-primary btnstyle m-2 relative-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
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
                                                                    <option value="6" <?php if (isset($_REQUEST['status_s']) && '6' == $_REQUEST['status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Active
                                                                    </option>
                                                                    <option value="7" <?php if (isset($_REQUEST['status_s']) && '7' == $_REQUEST['status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Inactive
                                                                    </option>
                                                                    <option value="8" <?php if (isset($_REQUEST['status_s']) && '8' == $_REQUEST['status_s']) {
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
                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                    <?php
                                    $cond = '';

                                    $sts = " AND `status`!='deleted'";
                                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                        $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                    }

                                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                        $cond .= " AND delivery_date between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }


                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND (`po_number` like '%" . $_REQUEST['keyword2'] . "%' OR `po_date` like '%" . $_REQUEST['keyword2'] . "%')";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND (`po_number` like '%" . $_REQUEST['keyword'] . "%'  OR `po_date` like '%" . $_REQUEST['keyword'] . "%')";
                                        }
                                    }

                                    $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . "  AND `po_status`=14  AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id "  . $sts . "  ORDER BY po_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                    $qry_list = queryGet($sql_list, true);
                                    $num_list = $qry_list['numRows'];
                                    $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . " AND `po_status`=14  AND  `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id " . $sts . " ";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];
                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_PURCHASE_ORDER", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>#</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>PO Number</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th>Reference Number</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th>PO Date</th>
                                                    <?php  }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <th>Vendor Name</th>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <th>Total Items</th>
                                                    <?php  }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <th>Total Amount</th>
                                                    <?php } ?>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>



                                            <tbody>
                                                <?php
                                                $poList = $qry_list['data'];

                                                foreach ($poList as $onePoList) {
                                                    $check_cur = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`='" . $onePoList['currency'] . "'");
                                                    // console($onePoList['po_number']);
                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['po_number'] ?></td>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['ref_no'] ?></td>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['po_date'] ?></td>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <td><?= $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0]['trade_name'] ?></td>
                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td><?= $onePoList['totalItems'] ?></td>
                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td><?php  echo $check_cur['data']['currency_name']; echo $onePoList['totalAmount']  * $onePoList['conversion_rate']; ?></td>
                                                        <?php } ?>
                                                        <td>
                                                            <?php
                                                            if ($onePoList['po_status'] == 14) {
                                                                echo "Pending";
                                                            } else if ($onePoList['po_status'] == 9) {
                                                                echo "open";
                                                            } else if ($onePoList['po_status'] == 10) {
                                                                echo "closed";
                                                            } else {
                                                            }
                                                            ?>


                                                        </td>
                                                        <td>
                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                        </td>
                                                    </tr>




                                                    <!-- right modal start here  -->

                                                    <div class="modal fade right customer-modal pending-po-modal" id="fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                            <!--Content-->
                                                            <div class="modal-content">
                                                                <!--Header-->
                                                                <div class="modal-header">

                                                                    <div class="customer-head-info">
                                                                        <div class="customer-name-code">
                                                                            <h2><?php echo $check_cur['data']['currency_name']; echo $onePoList['totalAmount'] * $onePoList['conversion_rate']; ?></h2>
                                                                            <p class="heading lead"><?= $onePoList['po_number'] ?></p>
                                                                            <p>REF :&nbsp;<?= $onePoList['ref_no'] ?></p>

                                                                        </div>
                                                                        <?php
                                                                        $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                        ?>
                                                                        <div class="customer-image">
                                                                            <div class="name-item-count">
                                                                                <h5><?= $vendorDetails['trade_name'] ?></h5>
                                                                                <span>
                                                                                    <div class="round-item-count"><?= $onePoList['totalItems'] ?></div> Items
                                                                                </span>
                                                                            </div>
                                                                            <i class="fa fa-user"></i>
                                                                        </div>
                                                                    </div>

                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                        <li class="nav-item">
                                                                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $onePoList['po_number'] ?>" role="tab" aria-controls="home" aria-selected="true">Info</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= $onePoList['po_number'] ?>" role="tab" aria-controls="profile" aria-selected="false">Vendor Details</a>
                                                                        </li>
                                                                        <!-- <li class="nav-item">
                                                                            <a class="nav-link" id="" href="pending-po.php?approve=<?= $onePoList['po_id'] ?>" role="" aria-controls="profile" aria-selected="false">Approve PO</a>
                                                                        </li> -->
                                                                        <!-- -------------------Audit History Button Start------------------------- -->
                                                                        <li class="nav-item">
                                                                            <a class="nav-link auditTrail" id="history-tab<?= $onePoList['po_number'] ?>" data-toggle="tab" data-ccode="<?= $onePoList['po_number'] ?>" href="#history<?= $onePoList['po_number'] ?>" role="tab" aria-controls="history<?= $onePoList['po_number'] ?>" aria-selected="false"><i class="fa fa-history mr-2"></i>Trail</a>
                                                                        </li>
                                                                        <!-- -------------------Audit History Button End------------------------- -->
                                                                    </ul>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <a class="nav-link approve-po btn btn-success text-white float-right p-2" id="" href="pending-po.php?approve=<?= $onePoList['po_id'] ?>" role="" aria-controls="profile" aria-selected="false">Approve PO</a>
                                                                    <a class="nav-link approve-po btn btn-danger text-white float-right p-2" id="" href="pending-po.php?reject=<?= $onePoList['po_id'] ?>" role="" aria-controls="profile" aria-selected="false">Reject PO</a>

                                                                    <div class="tab-content" id="myTabContent">
                                                                        <div class="tab-pane fade show active" id="home<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                            <?php
                                                                            $itemDetails = $BranchPoObj->fetchBranchPoItems($onePoList['po_id'])['data'];
                                                                            foreach ($itemDetails as $oneItem) {
                                                                            ?>
                                                                                <form action="" method="POST">

                                                                                    <div class="hamburger">
                                                                                        <div class="wrapper-action">
                                                                                            <i class="fa fa-cog fa-2x"></i>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="nav-action" id="thumb">
                                                                                        <a title="Notify Me" href="" name="vendorEditBtn">
                                                                                            <i class="fa fa-bell"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="nav-action" id="create">
                                                                                        <a title="Edit" href="manage-purchases-orders.php?edit=<?= $onePoList['po_id'] ?>" name="vendorEditBtn">
                                                                                            <i class="fa fa-edit"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div class="nav-action trash" id="share">
                                                                                        <a title="Delete" href="" name="vendorEditBtn">
                                                                                            <i class="fa fa-trash"></i>
                                                                                        </a>
                                                                                    </div>

                                                                                </form>


                                                                                <div class="item-detail-section">
                                                                                    <h6>Items Details</h6>

                                                                                    <div class="card">
                                                                                        <div class="card-body">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                                    <div class="left-section">
                                                                                                        <div class="icon-img">
                                                                                                            <i class="fa fa-box"></i>
                                                                                                        </div>
                                                                                                        <div class="code-des">
                                                                                                            <h4><?= $oneItem['itemCode'] ?></h4>
                                                                                                            <p><?= $oneItem['itemName'] ?></p>
                                                                                                            <p><?= $oneItem['unitPrice'] ?></p>
                                                                                                            <p>
                                                                                                                <h10>Quantity- <?= $oneItem['qty'] . "  " . $oneItem['uom'] ?></h10>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <h10>Remaining Quantity- <?php if ($oneItem['remainingQty'] != "") {
                                                                                                                                                echo $oneItem['remainingQty'] . "  " . $oneItem['uom'];
                                                                                                                                            } else {
                                                                                                                                                echo 0 . "  " . $oneItem['uom'];
                                                                                                                                            }
                                                                                                                                            ?></h10>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <h10>Total Price- <?php echo $check_cur['data']['currency_name']; echo $oneItem['total_price']  * $onePoList['conversion_rate']; ?></h10>
                                                                                                            </p>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <hr>
                                                                                            <?php
                                                                                            $deliverySchedule = $BranchPoObj->fetchBranchPoItemsDeliverySchedule($oneItem['po_item_id'])['data'];
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
                                                                                                                    <?= $dSchedule['qty'] ?> <?= $oneItem['uom'] ?>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                    </div>

                                                                                </div>
                                                                            <?php } ?>
                                                                            <!-- <a href="pending-po.php?approve=<?= $onePoList['po_id'] ?>" class="btn btn-primary">Approve PO</a> -->
                                                                        </div>



                                                                        <div class="tab-pane fade" id="profile<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="accordion accordion-flush customer-details-sells-order" id="accordionFlushCustDetails">
                                                                                        <div class="accordion-item customer-details">
                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnePo" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                    <span>Vendor Details</span>
                                                                                                </button>
                                                                                            </h2>
                                                                                            <div id="flush-collapseOnePo" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                <div class="accordion-body cust-detsils-body">

                                                                                                    <div class="card">
                                                                                                        <div class="card-body">
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <?php
                                                                                                                    $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                                                                    ?>
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-hashtag"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>Vendor Code</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['vendor_code'] ?>
                                                                                                                    </p>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <hr>
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-user"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>Vendor Name</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['trade_name'] ?>
                                                                                                                    </p>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <hr>
                                                                                                            <div class="row">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                    <div class="icon">
                                                                                                                        <i class="fa fa-file"></i>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                    <span>GST</span>
                                                                                                                </div>
                                                                                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                    <p>
                                                                                                                        <?= $vendorDetails['vendor_gstin'] ?>
                                                                                                                    </p>
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
                                                                        </div>


                                                                        <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                        <div class="tab-pane fade" id="history<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                            <div class="audit-head-section mb-3 mt-3 ">
                                                                                <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($onePoList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['created_at']) ?></p>
                                                                                <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($onePoList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePoList['updated_at']) ?></p>
                                                                            </div>
                                                                            <hr>
                                                                            <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $onePoList['po_number'] ?>">

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
                                                        <!--/.Content-->
                                                    </div>
                                </div>
                            <?php }
                                                //  console($onePoList['po_number']); 
                            ?>
                            <!-- right modal end here  -->

                            </tbody>
                            <tfoot>
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

                                <!-- For Pegination------->
                                <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
                                    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                                                    echo  $_REQUEST['pageNo'];
                                                                                } ?>">
                                </form>
                                <!-- End Pegination from------->
                            </tfoot>
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
                                        <input type="hidden" name="pageTableName" value="ERP_BRANCH_PURCHASE_ORDER" />
                                        <div class="modal-body">
                                            <div id="dropdownframe"></div>
                                            <div id="main2">
                                                <table>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                            PO Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                            Reference Number</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                            PO Date</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                                            Vendor Name</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
                                                            Total Items</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                                            Total Amount</td>
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
    <!-- End Pegination from------->


<?php
}
require_once("../common/footer.php");
?>
<script>
    $(document).ready(function() {
        $("#dropBtn").on("click", function(e) {
            e.stopPropagation(); // Stop the event from propagating to the document
            console.log("clickedddd");
            $("#filterDropdown .dropdown-content").addClass("active");
            $("#filterDropdown").addClass("active");
        });

        $(document).on("click", function() {
            $("#filterDropdown .dropdown-content").removeClass("active");
            $("#filterDropdown").removeClass("active");
        });

        // Close the dropdown when clicking inside it
        $("#filterDropdown .dropdown-content").on("click", function(e) {
            e.stopPropagation(); // Prevent the event from reaching the document
        });

        // $(window).resize(function() {
        //     if ($(window).width() > 768) {
        //         $("#filterDropdown .dropdown-content").hide();
        //     }
        // });
    });
</script>
<script>
    $(document).on("click", ".add-btn-minus", function() {
        $(this).parent().parent().remove();
    });

    function addMultiQtyf(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row_${id}`).append(`  <div class="row othe-cost-infor">
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Select</label>
                                                                        <select class="form-control" name="FreightCost[${addressRandNo}][txt]">
                                                                        <option value="">Select Vendor</option>
                                                                           <?php echo $vendrSelect; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input step="0.01" type="number" class="form-control" placeholder="amount" name="FreightCost[${addressRandNo}][amount]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="service description" name="FreightCost[${addressRandNo}][service]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">GST</label>
                                                                        <input type="text" class="form-control" placeholder="gst" name="FreightCost[${addressRandNo}][gst]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Total Amount</label>
                                                                        <input type="text" class="form-control" placeholder="total amount" name="FreightCost[${addressRandNo}][total]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="form-check-rcm">
                                                                        <input type="checkbox" name="FreightCost[${addressRandNo}][rcm]" id="">
                                                                        <label for="">RCM</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="add-btn-minus">
                                                                        <a style="cursor: pointer" class="btn btn-danger">
                                                                            <i class="fa fa-minus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>`);
    }


    function addMultiQty(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row_${id}`).append(`  <div class="row othe-cost-infor">
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Vendor Name</label>
                                                                        <input type="text" class="form-control" placeholder="vendor name" name="OthersCost[${addressRandNo}][name]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Amount</label>
                                                                        <input step="0.01" type="number" class="form-control" placeholder="amount" name="OthersCost[${addressRandNo}][amount]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Service Description</label>
                                                                        <input type="text" class="form-control" placeholder="description" name="OthersCost[${addressRandNo}][service]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">GST</label>
                                                                        <input type="text" class="form-control" placeholder="gst" name="OthersCost[${addressRandNo}][gst]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-12 col-sm-12">
                                                                    <div class="form-input">
                                                                        <label for="">Total Amount</label>
                                                                        <input type="text" class="form-control" placeholder="total amount" name="OthersCost[${addressRandNo}][total]">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="form-check-rcm">
                                                                        <input type="checkbox" name="OthersCost[${addressRandNo}][rcm]" id="" value="1">
                                                                        <label for="">RCM</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="add-btn-minus">
                                                                        <a style="cursor: pointer" class="btn btn-danger">
                                                                            <i class="fa fa-minus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            </div>`);
    }

    // function addDeliveryQty(id) {
    //     let addressRandNo = Math.ceil(Math.random() * 100000);
    //     $(`.modal-add-row-delivery_${id}`).append(`
    //                                       <div class="row">
    //                                     <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
    //                                     <div class="form-input">
    //                                         <label>Delivery date</label>
    //                                         <input type="date" name="listItem[${addressRandNo}][deliverySchedule][${addressRandNo}][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
    //                                     </div>
    //                                 </div>
    //                                 <div class="col-lg-5 col-md-5 col-sm-5 col-12">
    //                                     <div class="form-input">
    //                                         <label>Quantity</label>
    //                                         <input type="text" name="listItem[${addressRandNo}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity" id="multiQuantity_${addressRandNo}" placeholder="quantity" value="">
    //                                     </div>
    //                                 </div>
    //                                 <div class="col-lg-2 col-md-2 col-sm-2 col-12">
    //                                 <div class="add-btn-minus">
    //                                         <a style="cursor: pointer" class="btn btn-danger" onclick="rm(538)">
    //                                           <i class="fa fa-minus"></i>
    //                                         </a>
    //                                         </div>
    //                                 </div>
    //                             </div>`);
    // }

    function addDeliveryQty(randCode) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row-delivery_${randCode}`).append(`
                                          <div class="row">
                                        <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Delivery date</label>
                                            <input type="date" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Quantity</label>
                                            <input type="text" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity" id="multiQuantity_${addressRandNo}" placeholder="quantity" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                    <div class="add-btn-minus">
                                            <a style="cursor: pointer" class="btn btn-danger" onclick="rm(538)">
                                              <i class="fa fa-minus"></i>
                                            </a>
                                            </div>
                                    </div>
                                </div>`);
    }


    function loadItems() {
        $.ajax({
            type: "GET",
            url: `ajaxs/po/ajax-items.php`,
            beforeSend: function() {
                $("#itemsDropDown").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                $("#itemsDropDown").html(response);
            }
        });
    }
    loadItems();
    // vendors ********************************
    function loadVendors() {
        $.ajax({
            type: "GET",
            url: `ajaxs/po/ajax-vendors.php`,
            beforeSend: function() {
                $("#vendorDropdown").html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                $("#vendorDropdown").html(response);
            }
        });
    }
    loadVendors();
</script>
<script>
    $(document).ready(function() {

        $(".add_data").click(function() {
            var data = this.value;
            $("#creatData").val(data);
            //confirm('Are you sure to Submit?')
            $("#submitPoForm").submit();
        });
    });
    $(document).ready(function() {
        $('#itemsDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });
        $('#vendorDropdown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });

        // get vendor details by id
        $("#vendorDropdown").on("change", function() {
            let vendorId = $(this).val();
            if (vendorId != "") {
                $.ajax({
                    type: "GET",
                    url: `ajaxs/po/ajax-vendors-list.php`,
                    data: {
                        act: "vendorlist",
                        vendorId
                    },
                    beforeSend: function() {
                        $("#vendorInfo").html(`<option value="">Loding...</option>`);
                    },
                    success: function(response) {
                        console.log(response);
                        $("#vendorInfo").html(response);
                    }
                });
            } else {
                $("#vendorInfo").html('');
            }
        });

        $(document).ready(function() {
            $('input[type="radio"]').click(function() {
                var inputValue = $(this).attr("value");
                var targetBox = $("." + inputValue);
                $(".box").not(targetBox).hide();
                $(targetBox).show();
            });
        });
        // **************************************

        // get item details by id
        $("#itemsDropDown").on("change", function() {
            let itemId = $(this).val();

            $.ajax({
                type: "GET",
                url: `ajaxs/po/ajax-items-list.php`,
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
                    calculateAllItemsGrandAmount();
                }
            });
        });
        $(document).on("click", ".delItemBtn", function() {
            // let id = ($(this).attr("id")).split("_")[1];
            // $(`#delItemRowBtn_${id}`).remove();
            $(this).parent().parent().remove();
            calculateAllItemsGrandAmount();
        });

        $(document).on('submit', '#addNewItemForm', function(event) {
            event.preventDefault();
            let formData = $("#addNewItemsForm").serialize();
            $.ajax({
                type: "POST",
                url: `ajaxs/po/ajax-items.php`,
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
                url: `ajaxs/po/ajax-items-list.php`,
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
</script>


<script>
    var potypes = jQuery('#potypes');
    var select = this.value;
    potypes.change(function() {
        if ($(this).val() == 'international') {
            $('.radio-types-fob-cif').show();
        } else $('.radio-types-fob-cif').hide();
    });
</script>
<script>
    var potypes = jQuery('#potypes');
    var select = this.value;
    potypes.change(function() {
        if ($(this).val() == 'domestic') {
            $('.radio-types-ex-for').show();
        } else $('.radio-types-ex-for').hide();
    });
</script>

<script>
    var usetypesDropdown = jQuery('#usetypesDropdown');
    var select = this.value;
    usetypesDropdown.change(function() {
        if ($(this).val() == 'consumable') {
            $('.cost-center').show();
        } else $('.cost-center').hide();
    });

    /********************************************** */
    function calculateAllItemsGrandAmount() {
        let grandTotal = 0;
        $(".itemTotalPrice").each(function() {
            let itemTotalPrice = parseFloat($(this).val());
            grandTotal += itemTotalPrice > 0 ? itemTotalPrice : 0;
        });
        $("#grandTotalAmount").html(grandTotal.toFixed(2));
        $("#grandTotalAmountInput").val(grandTotal.toFixed(2));
    }

    function calculateOneItemRowAmount(rowNum) {
        let qty = parseFloat($(`#itemQty_${rowNum}`).val());
        qty = qty > 0 ? qty : 0;
        let unitPrice = parseFloat($(`#itemUnitPrice_${rowNum}`).val());
        unitPrice = unitPrice > 0 ? unitPrice : 0;
        let totalPrice = unitPrice * qty;
        $(`#itemTotalPrice_${rowNum}`).val(totalPrice.toFixed(2));
        calculateAllItemsGrandAmount();
    }

    $(document).on("keyup", ".itemQty", function() {
        let rowNum = ($(this).attr("id")).split("_")[1];
        calculateOneItemRowAmount(rowNum);
    });
    $(document).on("keyup", ".itemUnitPrice", function() {
        let rowNum = ($(this).attr("id")).split("_")[1];
        calculateOneItemRowAmount(rowNum);
    });
</script>