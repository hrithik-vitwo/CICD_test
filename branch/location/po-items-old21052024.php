<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
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


if (isset($_GET["approve"])) {
    //console(($_GET["approve"]));
    ///exit();
    $po_id = $_GET["approve"];
    $po = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` as po, `" . ERP_VENDOR_DETAILS . "` as vendor WHERE po.vendor_id=vendor.vendor_id  AND `po_id`=$po_id ";
    $poGet = queryGet($po);
    //console($poGet['data']);
    $po_no = $poGet['data']['po_number'];
    $to = $poGet['data']['vendor_authorised_person_email'];
    $sub = 'PO approved';
    $user_name = $poGet['data']['trade_name'];
    $gst = $poGet['data']['vendor_gstin'];
    //   $url=LOCATION_URL;
    //   $user_id=$POST['email'];
    //   $password=$adminPassword;
    $msg = 'Hey <b>' . $user_name . ',</b>(GSTIN:' . $gst . ')<br>
  Your Purchase Order (' . $po_no . ') has been approved.<br>
  ';
    $emailReturn = SendMailByMySMTPmailTemplate($to, $sub, $msg, $tmpId = null);

    if ($emailReturn == true) {
        $status = 9;
        $update = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=$status WHERE `po_id`=$po_id";
        $updatePO = queryUpdate($update);
        swalToast('success', 'email sent');
    } else {
        swalToast('warning', 'something went wrong');
    }



    //swalToast($approve["status"], $approve["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩



?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

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
                                <h3 class="card-title">PO Items</h3>
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
                                                        <a href="po-items.php" class="btn active"><i class="fa fa-list mr-2 active"></i>Item Order List</a>
                                                        <a href="pending-po.php" class="btn "><i class="fa fa-clock mr-2 "></i>Pending PO</a>
                                                        <a href="pending-po.php?open" class="btn"><i class="fa fa-lock-open mr-2"></i>Open PO</a>
                                                        <a href="pending-po.php?closed" class="btn "><i class="fa fa-lock mr-2 "></i>Closed PO</a>
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

                                $sts = " AND po.status!='deleted'";
                                if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                    $sts = ' AND po.status="' . $_REQUEST['status_s'] . '"';
                                }

                                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                    $cond .= " AND po.delivery_date between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                }


                                if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                    $cond .= " AND (po.po_number like '%" . $_REQUEST['keyword2'] . "%' OR po_items.itemCode like '%" . $_REQUEST['keyword2'] . "%' OR po_items.itemName like '%" . $_REQUEST['keyword2'] . "%' OR po_date like '%" . $_REQUEST['keyword2'] . "%')";
                                } else {
                                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                        $cond .= " AND (po.po_number like '%" . $_REQUEST['keyword'] . "%' OR po_items.itemCode like '%" . $_REQUEST['keyword'] . "%' OR po_items.itemName like '%" . $_REQUEST['keyword'] . "%' OR po.po_date like '%" . $_REQUEST['keyword'] . "%')";
                                    }
                                }

                                $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` as po_items LEFT JOIN `erp_branch_purchase_order` as po ON po_items.po_id = po.po_id WHERE 1 " . $cond . "   AND po.branch_id=$branch_id AND po.location_id=$location_id AND po.company_id=$company_id "  . $sts . "  ORDER BY po_items.po_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                $qry_list = queryGet($sql_list, true);
                                $num_list = $qry_list['numRows'];
                                $countShow = "SELECT count(*) FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` as po_items LEFT JOIN `erp_branch_purchase_order` as po ON po_items.po_id = po.po_id WHERE 1 " . $cond . " AND `po_status`=14 AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id " . $sts . " ";
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
                                                    <th>Item Code </th>
                                                <?php }
                                                if (in_array(2, $settingsCheckbox)) { ?>
                                                    <th>Item Name </th>
                                                <?php }
                                                if (in_array(3, $settingsCheckbox)) { ?>
                                                    <th>PO Number</th>
                                                <?php }
                                                if (in_array(4, $settingsCheckbox)) { ?>
                                                    <th>PO Date </th>
                                                <?php }
                                                if (in_array(5, $settingsCheckbox)) { ?>
                                                    <th>Schedule Date </th>
                                                <?php  }
                                                if (in_array(6, $settingsCheckbox)) { ?>
                                                    <th>Quantity </th>
                                                <?php }
                                                if (in_array(7, $settingsCheckbox)) { ?>
                                                    <th> Remaining Quantity</th>
                                                <?php  }
                                                if (in_array(8, $settingsCheckbox)) { ?>
                                                    <th> UOM</th>
                                                <?php  }
                                                if (in_array(9, $settingsCheckbox)) { ?>
                                                    <th> Unit Price</th>
                                                <?php  }
                                                if (in_array(10, $settingsCheckbox)) { ?>
                                                    <th> Value(total)</th>
                                                <?php  }
                                                if (in_array(11, $settingsCheckbox)) { ?>
                                                    <th> Value(remaining)</th>
                                                <?php  }
                                                ?>

                                                <!-- <th>Action</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $poList = $qry_list['data'];
                                            foreach ($poList as $onePoList) {
                                                //console($onePoList);
                                                $check_cur = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`='" . $onePoList['currency'] . "'");
                                            ?>
                                                <tr>
                                                    <td><?= $cnt++ ?></td>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <td><?= $onePoList['itemCode'] ?></td>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <td>
                                                            <p class="pre-normal">
                                                                <?php
                                                                $item_id = $onePoList['inventory_item_id'];
                                                                $item_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `itemId`=$item_id");
                                                                echo $item_name = $item_sql['data']['itemName'];
                                                                ?>
                                                            </p>
                                                        </td>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <td><?= $onePoList['po_number'] ?></td>
                                                    <?php }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <td><?= formatDateORDateTime($onePoList['po_date']) ?></td>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <td><?= formatDateORDateTime($onePoList['delivery_date']) ?></td>
                                                    <?php }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <td><?= $onePoList['qty'] ?></td>
                                                    <?php }
                                                    if (in_array(7, $settingsCheckbox)) { ?>
                                                        <td><?= $onePoList['remainingQty'] ?></td>
                                                    <?php }
                                                    if (in_array(8, $settingsCheckbox)) { ?>
                                                        <td><?= $onePoList['uom'] ?></td>
                                                    <?php }
                                                    if (in_array(9, $settingsCheckbox)) { ?>
                                                        <td><?php echo $check_cur['data']['currency_name'];
                                                            echo $onePoList['unitPrice'] * $onePoList['conversion_rate']; ?></td>
                                                    <?php }
                                                    if (in_array(10, $settingsCheckbox)) { ?>
                                                        <td><?php echo $check_cur['data']['currency_name'];
                                                            echo $onePoList['total_price'] * $onePoList['conversion_rate']; ?></td>
                                                    <?php }
                                                    if (in_array(11, $settingsCheckbox)) { ?>
                                                        <td></td>
                                                    <?php }
                                                    ?>
                                                    <!-- <td>
                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePoList['po_number'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                        </td> -->
                                                </tr>




                                                <!-- right modal start here  -->


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
                                                        Item Code</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                        Item Name</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                        PO Number</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                        PO Date</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                        Schedule Date</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                        Quantity</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                        Remaining Quantity</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />
                                                        UOM</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox9" value="9" />
                                                        Unit Price</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(10, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="10" />
                                                        Value(total)</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(11, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox11" value="11" />
                                                        Value(Remaining)</td>
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

require_once("../common/footer.php");
?>
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
        $("#dropBtn").on("click", function(e) {
            e.stopPropagation();
            console.log("clickedddd");
            $("#filterDropdown .dropdown-content").addClass("active");
            $("#filterDropdown").addClass("active");
        });

        $(document).on("click", function() {
            $("#filterDropdown .dropdown-content").removeClass("active");
            $("#filterDropdown").removeClass("active");
        });

        $("#filterDropdown .dropdown-content").on("click", function(e) {
            e.stopPropagation();
        });
    });
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