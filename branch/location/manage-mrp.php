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
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");


$ItemsObj = new ItemsController();

$BranchPoObj = new BranchPo();


// $variant = $_SESSION['visitBranchAdminInfo']['flAdminVariant'];
$check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
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


if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchPrObj = new BranchPr();

if (isset($_POST['addNewPRFormSubmitBtn'])) {

    //  console($_POST);

    $addBranchPr = $BranchPrObj->addBranchPr($_POST);

    if ($addBranchPr["status"] == "success") {
        swalAlert($addBranchPr["status"], ucfirst($addBranchPr["status"]), $addBranchPr["message"], BASE_URL . "branch/location/manage-pr.php");
    } else {
        swalToast($addBranchPr["status"], $addBranchPr["message"]);
    }
}

if (isset($_POST["editNewPRFormSubmitBtn"])) {
    // console($_POST);
    // exit();
    $editBranchPr = $BranchPrObj->updatePR($_POST);
    // $branchId = base64_encode($addNewObj['branchId']);
    // redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
    swalAlert($editBranchPr["status"], ucfirst($editBranchPr["status"]), $editBranchPr["message"], BASE_URL . "branch/location/manage-pr.php");
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


    .pr-modal .modal-header.pt-3 {

        height: 315px;

    }

    .tab-content>.tab-pane li {
        margin-left: 0;
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


    .printable-view .h3-title {
        visibility: hidden;
    }

    @media print {
        body {
            visibility: hidden;
        }


        .printable-view {
            visibility: visible !important;
        }

        .printable-view .h3-title {
            visibility: visible;
        }

        .classic-view-modal .modal-dialog {
            max-width: 100% !important;
        }

        .classic-view-modal .modal-dialog .modal-header {
            height: 0 !important;
        }

        .classic-view-modal table.classic-view th {
            font-size: 12px !important;
            padding: 5px 10px !important;
        }

        table.classic-view td p {
            font-size: 12px !important;
        }

    }
</style>

<?php

if (isset($_GET['detail'])) {

?>
    <div class="content-wrapper is-pr">
        <!-- Content Header (Page header) -->
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content card">
                    <div class="modal-header card-header py-2 px-3">
                        <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div id="notesModalBody" class="modal-body card-body">
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="p-0 pt-1 my-2">

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
                                                            <a href="manage-mrp.php" class="btn"><i class="fa fa-stream mr-2"></i>Concised View</a>
                                                            <a href="manage-mrp.php?detail" class="btn active"><i class="fa fa-list mr-2 active"></i>Detailed View</a>
                                                        </div>

                                                        <div class="dropdown filter-dropdown" id="filterDropdown">

                                                            <button type="button" class="dropbtn" id="dropBtn">
                                                                <i class="fas fa-filter po-list-icon"></i>
                                                            </button>

                                                            <div class="dropdown-content">
                                                                <!-- <a href="manage-pr.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                <a href="pr-list.php?item" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                <a href="pr-list.php" class="btn "><i class="fa fa-lock-open mr-2 "></i>Open PR</a>
                                <a href="pr-list.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PR</a> -->
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

                                            </div>

                                        </div>

                                        <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Filter MRP</h5>

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
                                        $cond .= " AND created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }


                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND (`mrp_code` like '%" . $_REQUEST['keyword2'] . "%' OR `created_at` like '%" . $_REQUEST['keyword2'] . "%' OR `created_by` like '%" . $_REQUEST['keyword2'] . "%')";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND (`mrp_code` like '%" . $_REQUEST['keyword'] . "%'  OR `created_at` like '%" . $_REQUEST['keyword'] . "%' OR `created_by` like '%" . $_REQUEST['keyword'] . "%')";
                                        }
                                    }
                                    $sql_list = "SELECT items.itemName AS itemName, pr.refNo AS mrpCode, pr.prCode AS code, items.itemQuantity AS qty,items.remainingQty AS remainQty,pr.expectedDate AS expectedDate, pr.created_at AS created_at, uom.uomName AS uomName, pr.created_by AS created_by FROM `erp_mrp` AS mrp LEFT JOIN `erp_branch_purchase_request` AS pr  ON mrp.mrp_code = pr.refNo LEFT JOIN `erp_branch_purchase_request_items` AS items ON pr.purchaseRequestId = items.prId LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.uomId = items.uom WHERE pr.refNo IS NOT NULL  AND pr.location_id = $location_id
                                    UNION
                                    SELECT items.itemName AS itemName, sub.mrp_code AS mrpCode, sub.subProdCode AS code, sub.prodQty AS qty, sub.remainQty AS remainQty, sub.expectedDate AS expectedDate, sub.created_at AS created_at , uom.uomName AS uomName, sub.created_by AS created_by FROM `erp_mrp` AS mrp LEFT JOIN `erp_production_order_sub` AS sub  ON mrp.mrp_code = sub.mrp_code LEFT JOIN `erp_inventory_items` AS items ON sub.itemId = items.itemId LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.uomId = items.baseUnitMeasure WHERE  sub.mrp_code IS NOT NULL AND sub.location_id = $location_id
                                    ORDER BY created_at DESC";
                                    // exit();
                                    $qry_list = queryGet($sql_list, true);
                                    $num_list = $qry_list['numRows'];


                                    $countShow = "SELECT count(*) FROM (SELECT items.itemName AS itemName, pr.refNo AS mrpCode, pr.prCode AS code, items.itemQuantity AS qty,items.remainingQty AS remainQty,pr.expectedDate AS expectedDate, pr.created_at AS created_at, uom.uomName AS uomName, pr.created_by AS created_by FROM `erp_mrp` AS mrp LEFT JOIN `erp_branch_purchase_request` AS pr  ON mrp.mrp_code = pr.refNo LEFT JOIN `erp_branch_purchase_request_items` AS items ON pr.purchaseRequestId = items.prId LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.uomId = items.uom WHERE  pr.refNo IS NOT NULL  AND pr.location_id = $location_id
                                    UNION
                                    SELECT items.itemName AS itemName, sub.mrp_code AS mrpCode, sub.subProdCode AS code, sub.prodQty AS qty, sub.remainQty AS remainQty, sub.expectedDate AS expectedDate, sub.created_at AS created_at , uom.uomName AS uomName, sub.created_by AS created_by FROM `erp_mrp` AS mrp LEFT JOIN `erp_production_order_sub` AS sub  ON mrp.mrp_code = sub.mrp_code LEFT JOIN `erp_inventory_items` AS items ON sub.itemId = items.itemId LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.uomId = items.baseUnitMeasure WHERE  sub.mrp_code IS NOT NULL AND sub.location_id = $location_id) as count
                                    ORDER BY created_at DESC ";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];


                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_MRP_DETAIL", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>#</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>MRP Number</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th>Production/PR Code</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th> Item Name</th>
                                                    <?php }
                                                   
                                                     if (in_array(4, $settingsCheckbox)) { ?>
                                                        <th>Qty</th>
                                                    <?php }
                                                     if (in_array(5, $settingsCheckbox)) { ?>
                                                        <th>Remaining Qty</th>
                                                    <?php }
                                                     if (in_array(6, $settingsCheckbox)) { ?>
                                                        <th> UOM </th>
                                                    <?php }
                                                     if (in_array(7, $settingsCheckbox)) { ?>
                                                        <th> Expected Date</th>
                                                    <?php }
                                                     if (in_array(8, $settingsCheckbox)) { ?>
                                                        <th> Created At</th>
                                                    <?php } 
                                                      if (in_array(9, $settingsCheckbox)) { ?>
                                                        <th>Created By</th>
                                                    <?php }?>

                                                 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $soList = $qry_list['data'];

                                                //   console($soList);

                                                foreach ($soList as $onePrList) {
                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $onePrList['mrpCode'] ?></td>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?=$onePrList['code'] ?></td>

                                                        <?php }


                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td>
                                                                <?=
                                                                $onePrList['itemName']
                                                                // echo $onePrList['created_by'];
                                                                ?>
                                                            </td>
                                                        <?php }
                                                         if (in_array(4, $settingsCheckbox)) { ?>
                                                            <td><?= $onePrList['qty'] ?></td>

                                                        <?php }
                                                         if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td><?= $onePrList['remainQty'] ?></td>

                                                        <?php }
                                                         if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td><?=$onePrList['uomName'] ?></td>

                                                        <?php }
                                                         if (in_array(7, $settingsCheckbox)) { ?>
                                                            <td><?= formatDateORDateTime($onePrList['expectedDate']) ?></td>

                                                        <?php }
                                                         if (in_array(8, $settingsCheckbox)) { ?>
                                                            <td><?= formatDateORDateTime($onePrList['created_at']) ?></td>

                                                        <?php }
                                                         if (in_array(9, $settingsCheckbox)) { ?>
                                                            <td><?= getCreatedByUser($onePrList['created_by']) ?></td>

                                                        <?php } ?>
                                                       
                                                    </tr>

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
                                            <input type="hidden" name="pageTableName" value="ERP_MRP_DETAIL" />
                                            <div class="modal-body">
                                                <div id="dropdownframe"></div>
                                                <div id="main2">
                                                    <table>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                                MRP Number</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                               Production / Pr Code </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                Item Name</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                                Qty</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                                 Remaining Qty</td>
                                                        </tr>

                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                               UOM </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                                Expected Date</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />
                                                                Created At</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(9, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox9" value="9" />
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
} else {

?>

    <div class="content-wrapper is-pr">
        <!-- Content Header (Page header) -->
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content card">
                    <div class="modal-header card-header py-2 px-3">
                        <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div id="notesModalBody" class="modal-body card-body">
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="p-0 pt-1 my-2">

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
                                        <!-- <div class="col-lg-1 col-md-1 col-sm-12">
                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                        </div> -->
                                        <div class="col-lg-11 col-md-11 col-sm-12">
                                            <div class="row table-header-item">
                                                <div class="col-lg-11 col-md-11 col-sm-11">
                                                    <div class="filter-search">
                                                        <div class="filter-list">
                                                            <a href="manage-mrp.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>Concised View</a>
                                                            <a href="manage-mrp.php?detail" class="btn"><i class="fa fa-list mr-2"></i>Detailed View</a>
                                                        </div>

                                                        <div class="dropdown filter-dropdown" id="filterDropdown">

                                                            <button type="button" class="dropbtn" id="dropBtn">
                                                                <i class="fas fa-filter po-list-icon"></i>
                                                            </button>

                                                            <div class="dropdown-content">
                                                                <!-- <a href="manage-pr.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                <a href="pr-list.php?item" class="btn"><i class="fa fa-list mr-2"></i>Item Order List</a>
                                <a href="pr-list.php" class="btn "><i class="fa fa-lock-open mr-2 "></i>Open PR</a>
                                <a href="pr-list.php?closed" class="btn"><i class="fa fa-lock mr-2"></i>Closed PR</a> -->
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

                                            </div>

                                        </div>

                                        <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Filter MRP</h5>

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
                                        $cond .= " AND created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }


                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND (`mrp_code` like '%" . $_REQUEST['keyword2'] . "%' OR `created_at` like '%" . $_REQUEST['keyword2'] . "%' OR `created_by` like '%" . $_REQUEST['keyword2'] . "%')";
                                    } else {
                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                            $cond .= " AND (`mrp_code` like '%" . $_REQUEST['keyword'] . "%'  OR `created_at` like '%" . $_REQUEST['keyword'] . "%' OR `created_by` like '%" . $_REQUEST['keyword'] . "%')";
                                        }
                                    }
                                    $sql_list = "SELECT * FROM `erp_mrp` WHERE 1 " . $cond . "  AND company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' " . $sts . "  ORDER BY mrp_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                    // exit();
                                    $qry_list = queryGet($sql_list, true);
                                    $num_list = $qry_list['numRows'];


                                    $countShow = "SELECT count(*) FROM `erp_mrp` WHERE 1 " . $cond . " AND company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' " . $sts . " ";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];


                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_MRP", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>
                                        <table class="table defaultDataTable table-hover">
                                            <thead>
                                                <tr class="alert-light">
                                                    <th>#</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>MRP Number</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th> Date</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th> Created By</th>
                                                    <?php } ?>

                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $soList = $qry_list['data'];

                                                //   console($soList);

                                                foreach ($soList as $onePrList) {
                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $onePrList['mrp_code'] ?></td>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= formatDateORDateTime($onePrList['created_at']) ?></td>

                                                        <?php }


                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td>
                                                                <?php
                                                                echo getCreatedByUser($onePrList['created_by']);
                                                                // echo $onePrList['created_by'];
                                                                ?>
                                                            </td>
                                                        <?php } ?>
                                                        <td>

                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $onePrList['mrp_id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                            <!-- right modal start here  -->
                                                            <div class="modal fade right customer-modal pr-modal classic-view-modal" id="fluidModalRightSuccessDemo_<?= $onePrList['mrp_id'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                                    <!--Content-->
                                                                    <div class="modal-content">
                                                                        <!--Header-->
                                                                        <div class="modal-header pt-3">
                                                                            <p class="heading lead  mt-2 mb-4">MRP Code : <?= $onePrList['mrp_code'] ?></p>
                                                                            <p class="text-sm  mt-2 mb-2"> Date : <?= $onePrList['created_at'] ?></p>
                                                                            <p class="text-sm  mt-2 mb-2">status: <span class="status status-modal ml-2"><?php if ($onePrList['status'] != null) {
                                                                                                                                                                echo $onePrList['status'];
                                                                                                                                                            } else {
                                                                                                                                                                echo "PENDING";
                                                                                                                                                            }  ?></span></p>
                                                                            <!-- <p class="text-xs mt-2 mb-2">Note : <?= $onePrList['description'] ?></p> -->

                                                                            <ul class="nav nav-tabs" id="myTab" role="tablist">

                                                                                <li class="nav-item">
                                                                                    <a class="nav-link active" id="home-tab<?= $onePrList['mrp_code'] ?>" data-toggle="tab" href="#home<?= $onePrList['mrp_code'] ?>" role="tab" aria-controls="home<?= $onePrList['mrp_code'] ?>" aria-selected="true">Info</a>
                                                                                </li>

                                                                                <!-- <li class="nav-item">
                                          <a class="nav-link" id="classic-view-tab" data-toggle="tab" href="#classic-view<?= $onePrList['prCode'] ?>" role="tab" aria-controls="classic-view" aria-selected="false"><ion-icon name="apps-outline" class="mr-2"></ion-icon> Classic View</a>
                                        </li> -->

                                                                                <!-- -------------------Audit History Button Start------------------------- -->
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link auditTrail" id="history-tab<?= $onePrList['mrp_code'] ?>" data-toggle="tab" data-ccode="<?= $onePrList['mrp_code'] ?>" href="#history<?= $onePrList['mrp_code'] ?>" role="tab" aria-controls="history<?= $onePrList['mrp_code'] ?>" aria-selected="false">Trail</a>
                                                                                </li>
                                                                                <!-- -------------------Audit History Button End------------------------- -->
                                                                            </ul>


                                                                        </div>
                                                                        <!--Body-->
                                                                        <div class="modal-body px-4">
                                                                            <div class="tab-content pt-1" id="myTabContent">
                                                                                <div class="tab-pane pr-info-tab fade show active" id="home<?= $onePrList['mrp_code'] ?>" role="tabpanel" aria-labelledby="home-tab">
                                                                                    <h3>Production Order</h3>
                                                                                    <table>
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Item Code</th>
                                                                                                <th>Item Name</th>
                                                                                                <th>Item UOM</th>
                                                                                                <th>Qty</th>
                                                                                                <th>Remaining Qty</th>
                                                                                                <th> Expected Date</th>
                                                                                                <th>Created By</th>
                                                                                                <th>Created At</th>
                                                                                            </tr>
                                                                                        </thead>

                                                                                        <tbody>
                                                                                            <?php
                                                                                            $mrp_code = $onePrList['mrp_code'];
                                                                                            $prods = queryGet("SELECT * FROM `erp_production_order_sub` AS prod LEFT JOIN `erp_inventory_items` AS items ON prod.`itemId` = items.`itemId` LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.uomId = items.baseUnitMeasure  WHERE prod.`mrp_code` ='" . $mrp_code . "'", true);

                                                                                            foreach ($prods['data'] as $prod) {
                                                                                                //console($prod)
                                                                                            ?>
                                                                                                <tr>
                                                                                                    <td><?= $prod['itemCode'] ?></td>
                                                                                                    <td><?= $prod['itemName'] ?></td>
                                                                                                    <td><?= $prod['uomName'] ?></td>
                                                                                                    <td><?= $prod['prodQty'] ?></td>
                                                                                                    <td><?= $prod['remainQty'] ?></td>
                                                                                                    <td><?= formatDateORDateTime($prod['expectedDate']) ?></td>
                                                                                                    <td><?= getCreatedByUser($prod['created_by']) ?></td>
                                                                                                    <td><?= formatDateORDateTime($prod['created_at']) ?></td>

                                                                                                </tr>


                                                                                            <?php
                                                                                            }
                                                                                            ?>

                                                                                        </tbody>
                                                                                    </table>

                                                                                    <h3>Purchase Request</h3>
                                                                                    <table>
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Item Code</th>
                                                                                                <th>Item Name</th>
                                                                                                <th>Item UOM</th>
                                                                                                <th>Qty</th>
                                                                                                <th>Remaining Qty</th>
                                                                                                <th> Expected Date</th>
                                                                                                <th>Created By</th>
                                                                                                <th>Created At</th>
                                                                                            </tr>
                                                                                        </thead>

                                                                                        <?php
                                                                                        $mrp_code = $onePrList['mrp_code'];
                                                                                        $prods = queryGet("SELECT * FROM `erp_branch_purchase_request_items` AS items LEFT JOIN `erp_branch_purchase_request` AS pr ON pr.purchaseRequestId = items.prId LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.uomId = items.uom WHERE pr.refNo = '" . $mrp_code . "' ", true);

                                                                                        foreach ($prods['data'] as $prod) {
                                                                                            //console($prod)
                                                                                        ?>
                                                                                            <tr>
                                                                                                <td><?= $prod['itemCode'] ?></td>
                                                                                                <td><?= $prod['itemName'] ?></td>
                                                                                                <td><?= $prod['uomName'] ?></td>
                                                                                                <td><?= $prod['itemQuantity'] ?></td>
                                                                                                <td><?= $prod['remainingQty'] ?></td>
                                                                                                <td><?= formatDateORDateTime($prod['expectedDate']) ?></td>
                                                                                                <td><?= getCreatedByUser($prod['created_by']) ?></td>
                                                                                                <td><?= formatDateORDateTime($prod['created_at']) ?></td>

                                                                                            </tr>


                                                                                        <?php
                                                                                        }
                                                                                        ?>

                                                                                        <tbody>

                                                                                        </tbody>
                                                                                    </table>




                                                                                </div>





                                                                                <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                                <div class="tab-pane fade" id="history<?= $onePrList['mrp_code'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                    <div class="audit-head-section mb-3 mt-3 ">
                                                                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($onePrList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePrList['created_at']) ?></p>
                                                                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($onePrList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePrList['updated_at']) ?></p>
                                                                                    </div>
                                                                                    <hr>
                                                                                    <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $onePrList['mrp_code'] ?>">

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
                                                        </td>
                                                    </tr>

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
                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />
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
    $(document).on("click", ".add-btn-minus", function() {
        $(this).parent().parent().remove();
    });


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
            e.stopPropagation(); // Prevent the event from reaching the document
        });

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
            alert("Please Check Atleast 5");
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
                                            <input type="text" data-attr="${randCode}" name="listItem[${randCode}][deliverySchedule][${addressRandNo}][quantity]" class="form-control multiQuantity multiQty_${randCode}" id="multiQuantity_${addressRandNo}" placeholder="quantity" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                    <div class="add-btn-minus">
                                            <a style="cursor: pointer" class="btn btn-danger qty_minus" data-attr="${randCode}">
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
        $('#customerDropDown')
            .select2()
            .on('select2:open', () => {
                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
            });


        $("#usetypesDropdown").on("change", function() {
            // alert(1);
            let type = $(this).val();
            console.log(type);
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
                //   alert("item already exists!");
                Swal.fire({
                    title: 'item already exists!Do you want to add again?',

                    showCancelButton: true,
                    confirmButtonText: 'Save',

                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
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
                        Swal.fire('Saved!', '', 'success')
                    } else if (result.isDenied) {
                        Swal.fire('Changes are not saved', '', 'info')
                    }
                })
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
</script>

<script src="<?= BASE_URL; ?>public/validations/prValidation.js"></script>