<?php
include("../app/v1/connection-vendor-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/company/func-branches.php");
include("../app/v1/functions/branch/func-brunch-po-controller.php");

//console($_SESSION);
$vendor_id = $_SESSION['logedVendorAdminInfo']['fldAdminVendorId'];
if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"],);
}

$BranchPoObj = new BranchPo();

include("../app/v1/functions/branch/func-items-controller.php");

$ItemsObj = new ItemsController();
if (isset($_POST['uploadInvoice'])) {
   // console($_SESSION);
    $addBranchPo = $BranchPoObj->uploadInvoice($_POST+$_FILES);


    swalToast($addBranchPo["status"], $addBranchPo["message"]);
  
}

if (isset($_POST["visit"])) {
    $newStatusObj = VisitBranches($_POST);
    redirect(BRANCH_URL);
}


if (isset($_POST["editdata"])) {
    $editDataObj = updateDataBranches($_POST);

    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedVendorAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩




?>
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <!-- <div class="p-0 pt-1 my-2">
                        <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Manage Purchase order</h3>
                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?po-creation" class="btn btn-sm btn-primary btnstyle m-2 float-add-btn" style="line-height: 32px;"><i class="fa fa-plus"></i></a>
                            </li>
                        </ul>
                    </div> -->
                    <div class="card card-tabs" style="border-radius: 20px;">
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                            <div class="card-body">
                                <div class="row filter-serach-row">
                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-lg-10 col-md-10 col-sm-12">
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

                                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Filter
                                                        Vendors</h5>

                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                            <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                                                    echo $_REQUEST['keyword'];
                                                                                                                                                                                } ?>">
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                            <select name="vendor_status_s" id="vendor_status_s" class="fld form-control" style="appearance: auto;">
                                                                <option value=""> Status </option>
                                                                <option value="active" <?php if (isset($_REQUEST['vendor_status_s']) && 'active' == $_REQUEST['vendor_status_s']) {
                                                                                            echo 'selected';
                                                                                        } ?>>Active
                                                                </option>
                                                                <option value="inactive" <?php if (isset($_REQUEST['vendor_status_s']) && 'inactive' == $_REQUEST['vendor_status_s']) {
                                                                                                echo 'selected';
                                                                                            } ?>>Inactive
                                                                </option>
                                                                <option value="draft" <?php if (isset($_REQUEST['vendor_status_s']) && 'draft' == $_REQUEST['vendor_status_s']) {
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
                                                            <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                        echo $_REQUEST['form_date_s'];
                                                                                                                                                    } ?>" />
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync fa-spin"></i>Reset</a>
                                                    <a type="button" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                                                        Search</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        </form>
                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                <?php
                                $cond = '';

                                $sts = " AND `vendor_status` !='deleted'";
                                if (isset($_REQUEST['vendor_status_s']) && $_REQUEST['vendor_status_s'] != '') {
                                    $sts = ' AND vendor_status="' . $_REQUEST['vendor_status_s'] . '"';
                                }

                                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                    $cond .= " AND branch_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                }

                                if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                    $cond .= " AND (`vendor_code` like '%" . $_REQUEST['keyword'] . "%' OR `vendor_name` like '%" . $_REQUEST['keyword'] . "%' OR `vendor_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                                }

                                $sql_list = "SELECT * FROM `" .ERP_BRANCH_PURCHASE_ORDER. "` WHERE 1 " . $cond . "  AND vendor_id='" . $_SESSION["logedVendorAdminInfo"]["fldAdminVendorId"] . "'   ORDER BY po_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                               $qry_list = queryGet($sql_list, true);
                               $num_list = $qry_list['numRows'];
                               $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . " AND vendor_id='" . $_SESSION["logedVendorAdminInfo"]["fldAdminVendorId"] . "' ";
                               $countQry = mysqli_query($dbCon, $countShow);
                               $rowCount = mysqli_fetch_array($countQry);
                               $count = $rowCount[0];
                               $cnt = $GLOBALS['start'] + 1;
                               $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_PURCHASE_ORDER", $_SESSION["logedVendorAdminInfo"]["fldAdminVendorId"]);
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
                                                    <th>Delivery Date</th>
                                                <?php  }
                                                if (in_array(4, $settingsCheckbox)) { ?>
                                                    <th>Company Name</th>
                                                <?php }
                                                if (in_array(5, $settingsCheckbox)) { ?>
                                                    <th>Total Amount</th>
                                                <?php  }
                                                if (in_array(6, $settingsCheckbox)) { ?>
                                                    <th>Total Items</th>
                                                <?php } ?>

                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <?php 
                                                $poList = $qry_list['data'];

                                                    foreach ($poList as $onePoList) {
 
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
                                                    <td><?= $onePoList['delivery_date'] ?></td>
                                                <?php }
                                                if (in_array(4, $settingsCheckbox)) { ?>
                                                    <td><?php 
                                                    $company_id= $onePoList['company_id'];
                                                    $company= $BranchPoObj->fetchCompanyDetails($company_id);
                                                    $company_name = $company['data'][0]['company_name'];
                                                    echo $company_name; ?></td>
                                                <?php }
                                                if (in_array(5, $settingsCheckbox)) { ?>
                                                    <td><?= $onePoList['totalAmount'] ?></td>
                                                <?php }
                                                if (in_array(6, $settingsCheckbox)) { ?>
                                                    <td><?= $onePoList['totalItems'] ?></td>
                                                <?php } ?>
                                                <td>
                                                    <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                </td>
                                            </tr>



                                            <!-- right modal start here  -->

                                  
                                            <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                    <!--Content-->
                                                    <div class="modal-content">
                                                        <!--Header-->
                                                        <div class="modal-header">

                                                            <div class="customer-head-info">
                                                                <div class="customer-name-code">
                                                                    <h2><?= $onePoList['totalAmount'] ?></h2>
                                                                    <p class="heading lead"><?= $onePoList['po_number'] ?></p>
                                                                    <p>REF :&nbsp;<?= $onePoList['po_number'] ?></p>
                                                                </div>
                                                                
                                                                <div class="customer-image">
                                                                    <div class="name-item-count">
                                                                        <h5><?= $company_name ?></h5>
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
                                                                <?php
                                                                        if($onePoList['invoice_status'] != "invoice uploaded")
                                                                        {
                                                                ?>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile<?= $onePoList['po_number'] ?>" role="tab" aria-controls="profile" aria-selected="false">Upload</a>
                                                                </li>
                                                                <?php
                                                                        }
                                                                        ?>
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
                                                                            <div class="nav-action" id="settings">
                                                                                <a title="Delivery Creation" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($onePoList['po_number']) ?>" name="vendorEditBtn">
                                                                                    <i class="fa fa-box"></i>
                                                                                </a>
                                                                            </div>
                                                                            <div class="nav-action" id="thumb">
                                                                                <a title="Notify Me" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($onePoList['po_number']) ?>" name="vendorEditBtn">
                                                                                    <i class="fa fa-bell"></i>
                                                                                </a>
                                                                            </div>
                                                                            <div class="nav-action" id="create">
                                                                                <a title="Edit" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($onePoList['po_number']) ?>" name="vendorEditBtn">
                                                                                    <i class="fa fa-edit"></i>
                                                                                </a>
                                                                            </div>
                                                                            <div class="nav-action trash" id="share">
                                                                                <a title="Delete" href="manage-sales-orders-delivery.php?create-sales-order-delivery=<?= base64_encode($onePoList['po_number']) ?>" name="vendorEditBtn">
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
                                                                </div>
                                                                <div class="tab-pane fade" id="profile<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="accordion accordion-flush customer-details-sells-order" id="accordionFlushCustDetails">
                                                                                <div class="accordion-item customer-details">
                                                                                    <h2 class="accordion-header" id="flush-headingOne">
                                                                                    <form name="upload" action="" method="post" id="uploadForm" name="uploadForm" enctype="multipart/form-data">
                                                                                        <input type="hidden" name="id" value="<?= $onePoList['po_id'] ?>">
                                                                                        <input type="hidden" id="uploadInvoice" name="uploadInvoice">
                                                                                    <input type="file" class="form-control" name ="invoice">
                                                                                    <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_data">Save changes</button>
                                                                                    </h2>
                                                                                    <div id="flush-collapseOnePo" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                        <div class="accordion-body cust-detsils-body">
                                                                                    </form>

                                                                                          
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
                                                <!--/.Content-->
                                            </div>

                                </div>

                        <?php }
                                        //  console($onePoList['po_number']); 
                        ?>
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
                                                        Total Amount</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />
                                                        Total Items</td>
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




<?php
include("common/footer.php");
?>
<script>
    $(document).on("click", ".add-btn-minus", function() {
        $(this).parent().parent().remove();
    })

    function addMultiQty(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row_${id}`).append(` <div class='row othe-cost-infor'>
        <div class="col-lg-2 col-md-12 col-sm-12">
                            <div class="form-input">
                              <label for="">Vendor Select</label>
                              <select class="form-control">
                                <option value="">Tata Consultancy Limited</option>
                                <option value="">ITC Limited</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-12 col-sm-12">
                            <div class="form-input">
                              <label for="">Amount</label>
                              <input type="number" class="form-control" placeholder="placeholder">
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-12 col-sm-12">
                            <div class="form-input">
                              <label for="">Service Description</label>
                              <input type="text" class="form-control" placeholder="placeholder">
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-12 col-sm-12">
                            <div class="form-input">
                              <label for="">GST</label>
                              <input type="text" class="form-control" placeholder="placeholder">
                            </div>
                          </div>
                          <div class="col-lg-2 col-md-12 col-sm-12">
                            <div class="form-input">
                              <label for="">Total Amount</label>
                              <input type="text" class="form-control" placeholder="placeholder">
                            </div>
                          </div>
                          <div class="col-lg col-md-6 col-sm-6">
                            <div class="form-check-rcm">
                              <input type="checkbox" name="" id="">
                              <label for="">RCM</label>
                            </div>
                          </div>
                                          <div class="col-lg-1 col-md-1 col-sm-1">
                                          <div class="add-btn-minus">
                                            <a style="cursor: pointer" class="btn btn-danger" onclick="rm(538)">
                                              <i class="fa fa-minus"></i>
                                            </a>
                                            </div>
                                          </div>
                                          </div>`);
    }

    function addDeliveryQty(id) {
        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.modal-add-row-delivery_${id}`).append(`
                                          <div class="row">
                                        <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Delivery date</label>
                                            <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                        <div class="form-input">
                                            <label>Quantity</label>
                                            <input type="text" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="">
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
</script>
<script>
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
        // customers ********************************
        function loadCustomers() {
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
        loadCustomers();
        // get customer details by id
        $("#vendorDropdown").on("change", function() {
            let itemId = $(this).val();

            $.ajax({
                type: "GET",
                url: `ajaxs/po/ajax-vendors-list.php`,
                data: {
                    act: "listItem",
                    itemId
                },
                beforeSend: function() {
                    $("#vendorInfo").html(`<option value="">Loding...</option>`);
                },
                success: function(response) {
                    console.log(response);
                    $("#vendorInfo").html(response);
                }
            });
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
                    console.log(response);
                    $("#itemsTable").append(response);
                }
            });
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

    $(".add_data").click(function() {
      var data = this.value;
      $("#uploadInvoice").val(data);
      //confirm('Are you sure to Submit?')
      $("#uploadForm").submit();
    });


</script>